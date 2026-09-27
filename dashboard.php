<?php
include('config/db.php');
session_start();
require_once('includes/ai_engine.php');

// Session timeout: 30 minutes of inactivity
$timeout_duration = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: index.php?error=session_timeout");
    exit();
}
$_SESSION['last_activity'] = time();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_role = $_SESSION['user_role'] ?? 'Doctor';
$user_name = $_SESSION['user_name'] ?? 'Medical Staff';

// --- POST ACTION HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_inventory') {
        $id = intval($_POST['item_id']);
        $name = mysqli_real_escape_string($conn, $_POST['item_name']);
        $qty = intval($_POST['quantity']);
        $cat = mysqli_real_escape_string($conn, $_POST['category']);
        $expiry = $_POST['expiry_date'];

        $status = 'In Stock';
        if ($qty <= 0) {
            $status = 'Out of Stock';
        } elseif ($qty <= 10) {
            $status = 'Low Stock';
        }

        $updateQuery = "UPDATE inventory SET 
                        item_name='$name', 
                        category='$cat', 
                        quantity='$qty', 
                        expiry_date='$expiry', 
                        status='$status' 
                        WHERE id=$id";

        if (mysqli_query($conn, $updateQuery)) {
            header("Location: dashboard.php?view=inventory&msg=updated");
            exit();
        } else {
            die("Error updating record: " . mysqli_error($conn));
        }
    } elseif ($_POST['action'] == 'submit_test_results') {
        $test_id = intval($_POST['test_id']);
        $param = mysqli_real_escape_string($conn, $_POST['parameter']);
        $val = mysqli_real_escape_string($conn, $_POST['result_value']);
        $range = mysqli_real_escape_string($conn, $_POST['normal_range']);
        $rawRemarks = trim($_POST['remarks']);
        $status = $_POST['final_status'];

        $analysis = LabAI::analyzeResult($param, $val);
        $analysisMessage = $analysis[1];

        if (!empty($rawRemarks)) {
            $rawRemarks .= "\nAI Insight: " . $analysisMessage;
        } else {
            $rawRemarks = $analysisMessage;
        }

        if ($analysis[0] === 'danger' && strtolower($status) === 'completed') {
            $status = 'Flagged';
        }

        $remarks = mysqli_real_escape_string($conn, $rawRemarks);

        $result = mysqli_query($conn, "SELECT patient_id FROM lab_requests WHERE request_id = $test_id");
        $patient_id = ($result && $row = mysqli_fetch_assoc($result)) ? $row['patient_id'] : 0;

        $sql = "INSERT INTO test_results (test_id, patient_id, parameter_name, result_value, normal_range, technician_comment) 
                VALUES ('$test_id', '$patient_id', '$param', '$val', '$range', '$remarks')";

        if (mysqli_query($conn, $sql)) {
            mysqli_query($conn, "UPDATE lab_requests SET status='$status' WHERE request_id=$test_id");
            header("Location: dashboard.php?view=pending_tests&msg=ResultSaved");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($_POST['action'] == 'submit_patient_registration') {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $residence = mysqli_real_escape_string($conn, $_POST['residence']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $age = intval($_POST['age']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $next_of_kin = mysqli_real_escape_string($conn, $_POST['next_of_kin'] ?? '');
        $next_of_kin_contact = mysqli_real_escape_string($conn, $_POST['next_of_kin_contact'] ?? '');

        if (empty($full_name)) {
            die("Please enter the patient's name.");
        }

        $stmt = $conn->prepare("INSERT INTO patients (full_name, residence, gender, age, contact, next_of_kin, next_of_kin_contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssisss", $full_name, $residence, $gender, $age, $contact, $next_of_kin, $next_of_kin_contact);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $code = "DSC/" . str_pad($new_id, 3, "0", STR_PAD_LEFT);
            $conn->query("UPDATE patients SET patient_code = '$code' WHERE patient_id = $new_id");
            $stmt->close();
            header("Location: dashboard.php?view=patient_records&pid=$new_id&msg=PatientRegistered");
            exit();
        } else {
            die("Execute failed: " . $stmt->error);
        }
    } elseif ($_POST['action'] == 'save_vitals') {
        $patient_id = intval($_POST['patient_id'] ?? 0);
        $bp = mysqli_real_escape_string($conn, trim($_POST['blood_pressure'] ?? ''));
        $temp = mysqli_real_escape_string($conn, trim($_POST['temperature'] ?? ''));
        $weight = mysqli_real_escape_string($conn, trim($_POST['weight'] ?? ''));
        $height = mysqli_real_escape_string($conn, trim($_POST['height'] ?? ''));
        $symptoms = mysqli_real_escape_string($conn, trim($_POST['symptoms'] ?? ''));
        $diagnosis = mysqli_real_escape_string($conn, trim($_POST['diagnosis'] ?? ''));
        $prescription = mysqli_real_escape_string($conn, trim($_POST['prescription'] ?? ''));
        $doctor_id = intval($_SESSION['user_id'] ?? 1);

        if ($patient_id > 0) {
            $stmt = $conn->prepare("INSERT INTO patient_vitals (patient_id, doctor_id, blood_pressure, temperature, weight, height, symptoms, diagnosis, prescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisssssss", $patient_id, $doctor_id, $bp, $temp, $weight, $height, $symptoms, $diagnosis, $prescription);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: dashboard.php?view=patient_records&pid=$patient_id&msg=VitalsSaved");
        exit();
    } elseif ($_POST['action'] == 'submit_test_request') {
        $patient_id = intval($_POST['patient_id']);
        if ($patient_id <= 0) {
            die("Please select a valid registered patient.");
        }
        $test_type = $_POST['test_type'];
        if (empty($test_type)) {
            die("Please select a test type.");
        }
        $priority = mysqli_real_escape_string($conn, $_POST['priority_level']);
        $observations = mysqli_real_escape_string($conn, $_POST['observations']);
        $status = 'Requested';
        $doctor_id = intval($_SESSION['user_id'] ?? 1);

        $test_mapping = [
            'cbc' => 1,
            'malaria' => 2,
            'urinalysis' => 3,
            'glucose' => 4,
            'xray' => 5,
            'typhoid' => 6,
            'hiv' => 7
        ];
        $test_id = $test_mapping[$test_type] ?? 1;

        $stmt = $conn->prepare("INSERT INTO lab_requests (patient_id, test_id, doctor_id, status, priority, observations) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iiisss", $patient_id, $test_id, $doctor_id, $status, $priority, $observations);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: dashboard.php?view=overview&msg=RequestSent");
            exit();
        } else {
            die("Execute failed: " . $stmt->error);
        }
    }
}

$view = isset($_GET['view']) ? $_GET['view'] : 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Da Safe Community Clinic | Patient Records System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/theme-toggle.js"></script>
</head>
<body>

    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/header.php'); ?>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert success" style="padding: 12px 18px; background: #e6fffa; color: #234e52; margin: 20px; border-radius: 8px; border: 1px solid #b2f5ea; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="color: #319795; font-size: 1.2rem;"></i>
                <span>
                <?php 
                    if($_GET['msg'] == 'RequestSent') echo "Diagnostic lab request submitted successfully to Laboratory queue!";
                    if($_GET['msg'] == 'ResultSaved') echo "Lab findings and diagnostic remarks verified and saved!";
                    if($_GET['msg'] == 'PatientRegistered') echo "Patient registered successfully and assigned unique Clinic ID!";
                    if($_GET['msg'] == 'VitalsSaved') echo "Clinical consultation and vital signs recorded into patient medical history!";
                    if($_GET['msg'] == 'StaffAdded') echo "New staff member added successfully!";
                    if($_GET['msg'] == 'StaffDeleted') echo "Staff account deleted.";
                    if($_GET['msg'] == 'updated') echo "Record updated successfully!";
                ?>
                </span>
            </div>
        <?php endif; ?>

        <section class="view-container">
            <?php 
                $view = strtolower($view); 
                $file = __DIR__ . "/views/" . $view . ".php";

                if(file_exists($file)) {
                    define('ACCESS_DASHBOARD', true);
                    include($file);
                } else {
                    echo "<div style='padding:25px; color:#c53030; background:#fff5f5; border:1px solid #feb2b2; border-radius:10px; margin: 20px;'>";
                    echo "<h3><i class='fas fa-exclamation-circle'></i> Module View Not Found</h3>";
                    echo "<p>The requested view <code>" . htmlspecialchars($view) . ".php</code> could not be located.</p>";
                    echo "<a href='dashboard.php?view=overview' class='action-btn' style='display:inline-block; margin-top:10px;'>Return to Dashboard Overview</a>";
                    echo "</div>";
                }
            ?>
        </section>
        <?php include('includes/footer.php'); ?>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>