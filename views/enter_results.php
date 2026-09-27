<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

$test_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$test_names = [
    1 => 'Complete Blood Count (CBC)',
    2 => 'Malaria Parasites (MP)',
    3 => 'Urinalysis',
    4 => 'Blood Glucose (FBG/RBG)',
    5 => 'Chest X-Ray (CXR)',
    6 => 'Typhoid Test (Widal)',
    7 => 'HIV Rapid Diagnostic Test'
];

$req_data = null;
if ($test_id > 0) {
    $stmt = $conn->prepare("SELECT lr.*, p.full_name, p.patient_code, p.gender, p.age, p.residence, u.name as doctor_name 
                            FROM lab_requests lr 
                            LEFT JOIN patients p ON lr.patient_id = p.patient_id 
                            LEFT JOIN users u ON lr.doctor_id = u.id 
                            WHERE lr.request_id = ?");
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $req_data = $res->fetch_assoc();
    }
}

if (!$req_data) {
    echo "<div class='alert alert-error' style='padding:20px; background:#fff5f5; color:#c53030; border-radius:10px;'>";
    echo "<h3><i class='fas fa-exclamation-triangle'></i> Request Not Found</h3>";
    echo "<p>The requested laboratory order #$test_id does not exist or has already been completed.</p>";
    echo "<a href='dashboard.php?view=pending_tests' class='btn-primary' style='display:inline-block; margin-top:10px;'>Back to Workspace</a>";
    echo "</div>";
    return;
}

$pcode = !empty($req_data['patient_code']) ? $req_data['patient_code'] : 'DSC/' . str_pad($req_data['patient_id'], 3, '0', STR_PAD_LEFT);
$test_title = $test_names[$req_data['test_id']] ?? 'Diagnostic Analysis';
?>

<div class="centered-form-wrapper" style="max-width: 800px; margin: 0 auto; padding: 15px 0;">
    <!-- Patient & Order Details Banner -->
    <div class="card" style="background: #f8fafc; border: 1px solid #cbd5e0; padding: 20px 25px; border-radius: 12px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
            <div>
                <span style="background: #1b6ca8; color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                    <?php echo htmlspecialchars($pcode); ?>
                </span>
                <h3 style="margin: 6px 0 2px 0; color: #1a202c; font-size: 1.25rem;"><?php echo htmlspecialchars($req_data['full_name']); ?></h3>
                <p style="margin: 0; color: #718096; font-size: 0.88rem;">
                    <?php echo htmlspecialchars($req_data['gender']); ?>, <?php echo htmlspecialchars($req_data['age']); ?> yrs &bull; 
                    <?php echo htmlspecialchars($req_data['residence'] ?: 'Nagongera'); ?> &bull; 
                    Ordered by <strong><?php echo htmlspecialchars($req_data['doctor_name'] ?: 'Dr. Okello'); ?></strong>
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 700; color: #1b6ca8; font-size: 1.05rem;"><?php echo htmlspecialchars($test_title); ?></div>
                <small style="color: #718096;">Received: <?php echo date('M d, Y h:i A', strtotime($req_data['request_date'])); ?></small>
            </div>
        </div>

        <?php if (!empty($req_data['observations'])): ?>
            <div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 0.88rem; color: #4a5568;">
                <strong>Clinical Notes / Symptoms:</strong> "<?php echo htmlspecialchars($req_data['observations']); ?>"
            </div>
        <?php endif; ?>
    </div>

    <!-- Diagnostic Findings Form -->
    <div class="card request-card" style="background: white; padding: 35px 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <div class="card-header" style="margin-bottom: 20px;">
            <h2 style="font-size: 1.4rem; color: #1a202c; margin: 0;">
                <i class="fas fa-vials" style="color: #00a896;"></i> Enter Laboratory Findings & Verification
            </h2>
            <p class="form-subtitle" style="color: #718096; margin: 4px 0 0; font-size: 0.88rem;">
                Finalize diagnostic results and provide clinical remarks.
            </p>
        </div>

        <form action="dashboard.php" method="POST" class="modern-form">
            <input type="hidden" name="action" value="submit_test_results">
            <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;"><i class="fas fa-microscope"></i> Parameter Analyzed <span style="color:red;">*</span></label>
                    <input type="text" name="parameter" value="<?php echo htmlspecialchars($test_title); ?>" required style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;"><i class="fas fa-poll"></i> Result / Finding Value <span style="color:red;">*</span></label>
                    <input type="text" name="result_value" placeholder="e.g. Positive (+++), Negative, 13.5 g/dL" required style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;"><i class="fas fa-sliders-h"></i> Reference / Normal Range</label>
                    <input type="text" name="normal_range" placeholder="e.g. Negative or 12.0 - 16.0 g/dL" style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #4a5568;"><i class="fas fa-shield-alt"></i> Verification Status</label>
                    <select name="final_status" style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        <option value="Completed" selected>Completed (Normal / Verified)</option>
                        <option value="Flagged">Flagged (Abnormal / Critical Value)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 22px;">
                <label style="font-weight: 600; color: #4a5568;"><i class="fas fa-comment-medical"></i> Technician's Remarks & Microscopy Findings</label>
                <textarea name="remarks" rows="3" placeholder="Add specific microscopy notes, parasite density, or clinical comments..." style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-family: inherit;"></textarea>
            </div>

            <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center;">
                <a href="dashboard.php?view=pending_tests" class="btn-form secondary" style="padding: 12px 20px; background: #edf2f7; color: #4a5568; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn-form primary" style="padding: 12px 26px; background: #00a896; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-check-circle"></i> Verify & Finalize Results
                </button>
            </div>
        </form>
    </div>
</div>