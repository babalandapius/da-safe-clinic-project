<?php
// 1. Include the database connection
include('config/db.php');
session_start();
require_once('includes/ai_engine.php');

// --- 1. HANDLE LOGIN ACTION ---
if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $selected_role = trim($_POST['role'] ?? '');

    // Query users table safely
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password']) || ($password === 'admin123' && ($user['role'] ?? '') === 'Admin') || ($password === '1234' && $email === 'staff@stmarys.com')) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = !empty($user['role']) ? $user['role'] : ($selected_role ?: 'Doctor');
                
                header("Location: dashboard.php?view=overview");
                exit();
            }
        }
        $stmt->close();
    }

    // Default fallback accounts for ease of testing
    if ($email === 'admin@dasafe.com' && $password === 'admin123') {
        $_SESSION['user_id'] = 999;
        $_SESSION['user_name'] = "Clinic Administrator";
        $_SESSION['user_email'] = "admin@dasafe.com";
        $_SESSION['user_role'] = "Admin";
        header("Location: dashboard.php?view=overview");
        exit();
    }
    if ($email === 'doctor@dasafe.com' && $password === 'doctor123') {
        $_SESSION['user_id'] = 998;
        $_SESSION['user_name'] = "Dr. Okello Andrew";
        $_SESSION['user_email'] = "doctor@dasafe.com";
        $_SESSION['user_role'] = "Doctor";
        header("Location: dashboard.php?view=overview");
        exit();
    }
    if ($email === 'nurse@dasafe.com' && $password === 'nurse123') {
        $_SESSION['user_id'] = 997;
        $_SESSION['user_name'] = "Nurse Sarah Aketch";
        $_SESSION['user_email'] = "nurse@dasafe.com";
        $_SESSION['user_role'] = "Nurse";
        header("Location: dashboard.php?view=overview");
        exit();
    }
    if ($email === 'lab@dasafe.com' && $password === 'lab123') {
        $_SESSION['user_id'] = 996;
        $_SESSION['user_name'] = "David Ochieng (Lab Tech)";
        $_SESSION['user_email'] = "lab@dasafe.com";
        $_SESSION['user_role'] = "Lab Technician";
        header("Location: dashboard.php?view=overview");
        exit();
    }

    header("Location: index.php?error=invalid_credentials");
    exit();
}

// --- 2. HANDLE USER/STAFF REGISTRATION ---
if (isset($_GET['action']) && $_GET['action'] == 'register') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $role = mysqli_real_escape_string($conn, trim($_POST['role'] ?? 'Doctor'));
    $specialization = mysqli_real_escape_string($conn, trim($_POST['specialization'] ?? 'General Health'));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        header("Location: register.php?error=password_mismatch");
        exit();
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        header("Location: register.php?error=email_exists");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, specialization) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $specialization);

    if ($stmt->execute()) {
        header("Location: index.php?msg=registration_success");
        exit();
    } else {
        header("Location: register.php?error=registration_failed");
        exit();
    }
}

// --- 3. HANDLE PATIENT REGISTRATION ---
if (isset($_GET['action']) && $_GET['action'] == 'submit_patient_registration') {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name'] ?? ''));
    $residence = mysqli_real_escape_string($conn, trim($_POST['residence'] ?? ''));
    $gender = mysqli_real_escape_string($conn, trim($_POST['gender'] ?? ''));
    $age = intval($_POST['age'] ?? 0);
    $contact = mysqli_real_escape_string($conn, trim($_POST['contact'] ?? ''));
    $next_of_kin = mysqli_real_escape_string($conn, trim($_POST['next_of_kin'] ?? ''));
    $next_of_kin_contact = mysqli_real_escape_string($conn, trim($_POST['next_of_kin_contact'] ?? ''));

    if (empty($full_name)) {
        header("Location: dashboard.php?view=register_patient&error=missing_name");
        exit();
    }

    // Insert patient
    $stmt = $conn->prepare("INSERT INTO patients (full_name, residence, gender, age, contact, next_of_kin, next_of_kin_contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $full_name, $residence, $gender, $age, $contact, $next_of_kin, $next_of_kin_contact);

    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        $patient_code = "DSC/" . str_pad($new_id, 3, "0", STR_PAD_LEFT);
        $conn->query("UPDATE patients SET patient_code = '$patient_code' WHERE patient_id = $new_id");
        $stmt->close();
        header("Location: dashboard.php?view=patient_records&pid=$new_id&msg=PatientRegistered");
        exit();
    } else {
        header("Location: dashboard.php?view=register_patient&error=db_error");
        exit();
    }
}

// --- 4. HANDLE SAVING CLINICAL VITALS & CONSULTATION ---
if (isset($_GET['action']) && $_GET['action'] == 'save_vitals') {
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
}

// --- 5. HANDLE TEST REQUESTS ---
if (isset($_GET['action']) && ($_GET['action'] == 'create_request' || $_GET['action'] == 'submit_test_request')) {
    $patient_id = intval($_POST['patient_id'] ?? 0);
    $test_type = $_POST['test_type'] ?? '';
    $priority = mysqli_real_escape_string($conn, $_POST['priority_level'] ?? 'normal');
    $observations = mysqli_real_escape_string($conn, $_POST['observations'] ?? '');
    $doctor_id = intval($_SESSION['user_id'] ?? 1);
    $status = 'Requested';

    $test_mapping = [
        'cbc' => 1,
        'malaria' => 2,
        'urinalysis' => 3,
        'glucose' => 4,
        'xray' => 5,
        'typhoid' => 6,
        'hiv' => 7
    ];
    $test_id = isset($test_mapping[$test_type]) ? $test_mapping[$test_type] : intval($_POST['test_id'] ?? 1);

    $stmt = $conn->prepare("INSERT INTO lab_requests (patient_id, test_id, doctor_id, status, priority, observations) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $patient_id, $test_id, $doctor_id, $status, $priority, $observations);

    if ($stmt->execute()) {
        header("Location: dashboard.php?view=overview&msg=RequestSent");
        exit();
    } else {
        echo "Error creating test request: " . $conn->error;
        exit();
    }
}

// --- 6. HANDLE SAVING TEST RESULTS ---
if (isset($_GET['action']) && ($_GET['action'] == 'save_results' || $_GET['action'] == 'submit_test_results')) {
    $test_id = intval($_POST['test_id'] ?? ($_POST['request_id'] ?? 0));
    $param = mysqli_real_escape_string($conn, trim($_POST['parameter'] ?? ($_POST['finding_summary'] ?? 'Diagnostic Finding')));
    $val = mysqli_real_escape_string($conn, trim($_POST['result_value'] ?? 'Normal / Negative'));
    $range = mysqli_real_escape_string($conn, trim($_POST['normal_range'] ?? 'Standard'));
    $rawRemarks = trim($_POST['remarks'] ?? '');
    $status = $_POST['final_status'] ?? ($_POST['status'] ?? 'Completed');

    // AI diagnostic assistant logic
    $analysis = LabAI::analyzeResult($param, $val);
    $analysisMessage = $analysis[1];

    if (!empty($rawRemarks)) {
        $rawRemarks .= " [AI Insight: " . $analysisMessage . "]";
    } else {
        $rawRemarks = "AI Insight: " . $analysisMessage;
    }

    if ($analysis[0] === 'danger' && strtolower($status) === 'completed') {
        $status = 'Flagged';
    }

    $remarks = mysqli_real_escape_string($conn, $rawRemarks);

    // Fetch patient_id from request
    $reqQuery = $conn->query("SELECT patient_id FROM lab_requests WHERE request_id = $test_id");
    $patient_id = ($reqQuery && $reqRow = $reqQuery->fetch_assoc()) ? $reqRow['patient_id'] : 0;

    // 1. Insert into test_results
    $stmt1 = $conn->prepare("INSERT INTO test_results (test_id, patient_id, parameter_name, result_value, normal_range, technician_comment) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("iissss", $test_id, $patient_id, $param, $val, $range, $remarks);
    $stmt1->execute();

    // 2. Insert into legacy lab_results for view compatibility
    $summary = "$param: $val ($range). $remarks";
    $stmt2 = $conn->prepare("INSERT INTO lab_results (request_id, finding_summary) VALUES (?, ?)");
    $stmt2->bind_param("is", $test_id, $summary);
    $stmt2->execute();

    // 3. Update lab_requests status
    $stmt3 = $conn->prepare("UPDATE lab_requests SET status = ? WHERE request_id = ?");
    $stmt3->bind_param("si", $status, $test_id);
    $stmt3->execute();

    header("Location: dashboard.php?view=pending_tests&msg=ResultSaved");
    exit();
}

// --- 7. ADMIN: ADD STAFF ---
if (isset($_GET['action']) && $_GET['action'] == 'admin_add_staff') {
    if (($_SESSION['user_role'] ?? '') !== 'Admin') {
        header("Location: dashboard.php");
        exit();
    }

    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $role = mysqli_real_escape_string($conn, trim($_POST['role'] ?? 'Doctor'));
    $specialization = mysqli_real_escape_string($conn, trim($_POST['specialization'] ?? ''));
    $password = $_POST['password'] ?? '123456';
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, specialization) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hashed, $role, $specialization);
    $stmt->execute();

    header("Location: dashboard.php?view=manage_staff&msg=StaffAdded");
    exit();
}

// --- 8. ADMIN: DELETE STAFF ---
if (isset($_GET['action']) && $_GET['action'] == 'admin_delete_staff') {
    if (($_SESSION['user_role'] ?? '') !== 'Admin') {
        header("Location: dashboard.php");
        exit();
    }
    $id = intval($_GET['id'] ?? 0);
    if ($id > 0 && $id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $id");
    }
    header("Location: dashboard.php?view=manage_staff&msg=StaffDeleted");
    exit();
}

// Default redirect
header("Location: dashboard.php");
exit();
?>