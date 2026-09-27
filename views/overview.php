<?php
// Ensure the database connection is available
global $conn;
$role = $_SESSION['user_role'] ?? 'Doctor';
$user_name = $_SESSION['user_name'] ?? 'Medical Staff';

// Test mapping for display
$test_names = [
    1 => 'Complete Blood Count (CBC)',
    2 => 'Malaria Parasites (MP)',
    3 => 'Urinalysis',
    4 => 'Blood Glucose (FBG/RBG)',
    5 => 'Chest X-Ray (CXR)',
    6 => 'Typhoid Test (Widal)',
    7 => 'HIV Rapid Diagnostic Test'
];

// 1. Fetch Total Patients
$patient_res = $conn->query("SELECT COUNT(*) as total FROM patients");
$total_patients = ($patient_res) ? $patient_res->fetch_assoc()['total'] : 0;

// 2. Fetch Total Lab Requests
$total_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests");
$total_count = ($total_res) ? $total_res->fetch_assoc()['total'] : 0;

// 3. Fetch Pending Analysis
$pending_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status != 'Completed' AND status != 'Flagged'");
$pending_count = ($pending_res) ? $pending_res->fetch_assoc()['total'] : 0;

$urgent_count = 0;
$emergency_count = 0;

$urgent_count_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status != 'Completed' AND priority = 'urgent'");
if ($urgent_count_res) {
    $urgent_count = $urgent_count_res->fetch_assoc()['total'];
}
$emergency_count_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status != 'Completed' AND priority = 'emergency'");
if ($emergency_count_res) {
    $emergency_count = $emergency_count_res->fetch_assoc()['total'];
}

// 4. Fetch Results Ready
$ready_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status = 'Completed' OR status = 'Flagged'");
$ready_count = ($ready_res) ? $ready_res->fetch_assoc()['total'] : 0;

// 5. Fetch Staff count
$staff_res = $conn->query("SELECT COUNT(*) as total FROM users");
$total_staff = ($staff_res) ? $staff_res->fetch_assoc()['total'] : 0;

// Recent Lab Requests
$recent_requests = $conn->query("SELECT lr.*, p.full_name, p.patient_code, p.residence FROM lab_requests lr LEFT JOIN patients p ON lr.patient_id = p.patient_id ORDER BY lr.request_date DESC LIMIT 5");

// Recent Registered Patients
$recent_patients = $conn->query("SELECT * FROM patients ORDER BY created_at DESC LIMIT 5");
?>

<div class="view-header" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="font-size: 1.6rem; color: #1a202c; font-weight: 700; margin-bottom: 4px;">
                <i class="fas fa-chart-line" style="color: #1b6ca8;"></i> Clinical & Diagnostic Overview
            </h2>
            <p style="color: #718096; margin: 0; font-size: 0.95rem;">
                Da Safe Community Clinic • Nagongera Town Council • Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong> (<?php echo htmlspecialchars($role); ?>)
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <?php if ($role === 'Doctor' || $role === 'Nurse' || $role === 'Admin'): ?>
                <a href="dashboard.php?view=register_patient" class="btn-primary" style="padding: 10px 16px; font-size: 0.88rem; text-decoration: none; border-radius: 8px;">
                    <i class="fas fa-user-plus"></i> Register Patient
                </a>
                <a href="dashboard.php?view=request_test" class="btn-primary" style="padding: 10px 16px; font-size: 0.88rem; text-decoration: none; border-radius: 8px; background: #00a896;">
                    <i class="fas fa-file-medical"></i> Order Lab Test
                </a>
            <?php endif; ?>
            <?php if ($role === 'Lab Technician'): ?>
                <a href="dashboard.php?view=pending_tests" class="btn-primary" style="padding: 10px 16px; font-size: 0.88rem; text-decoration: none; border-radius: 8px;">
                    <i class="fas fa-microscope"></i> Open Lab Workspace
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Priority Alert Banner -->
<?php if ($emergency_count > 0 || $urgent_count > 0): ?>
<div style="background: #fffaf0; border-left: 5px solid #dd6b20; padding: 14px 20px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-exclamation-circle fa-2x" style="color: #dd6b20;"></i>
        <div>
            <h4 style="margin: 0; color: #9c4221; font-weight: 700;">Diagnostic Priority Alert</h4>
            <p style="margin: 2px 0 0; color: #7b341e; font-size: 0.9rem;">
                There <?php echo ($emergency_count == 1) ? 'is' : 'are'; ?> <strong><?php echo $emergency_count; ?> STAT/Emergency</strong> and <strong><?php echo $urgent_count; ?> Urgent</strong> patient test(s) awaiting processing.
            </p>
        </div>
    </div>
    <a href="dashboard.php?view=pending_tests" style="background: #dd6b20; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
        View Queue <i class="fas fa-arrow-right"></i>
    </a>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Total Registered Patients -->
    <div class="card" style="background: white; padding: 22px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; border-left: 4px solid #1b6ca8;">
        <div style="background: #ebf8ff; color: #2b6cb0; width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
            <i class="fas fa-users fa-2x"></i>
        </div>
        <div>
            <h3 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">Registered Patients</h3>
            <p style="margin: 4px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $total_patients; ?></p>
        </div>
    </div>

    <!-- Total Diagnostic Requests -->
    <div class="card" style="background: white; padding: 22px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; border-left: 4px solid #4299e1;">
        <div style="background: #e3f2fd; color: #1976d2; width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
            <i class="fas fa-file-medical-alt fa-2x"></i>
        </div>
        <div>
            <h3 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">Total Lab Requests</h3>
            <p style="margin: 4px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $total_count; ?></p>
        </div>
    </div>

    <!-- Pending Analysis -->
    <div class="card" style="background: white; padding: 22px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; border-left: 4px solid #ecc94b;">
        <div style="background: #fffaf0; color: #d69e2e; width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
            <i class="fas fa-clock fa-2x"></i>
        </div>
        <div>
            <h3 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">Pending Tests</h3>
            <p style="margin: 4px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $pending_count; ?></p>
        </div>
    </div>

    <!-- Results Ready -->
    <div class="card" style="background: white; padding: 22px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; align-items: center; border-left: 4px solid #48bb78;">
        <div style="background: #f0fff4; color: #38a169; width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
            <i class="fas fa-check-circle fa-2x"></i>
        </div>
        <div>
            <h3 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">Results Verified</h3>
            <p style="margin: 4px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $ready_count; ?></p>
        </div>
    </div>
</div>

<!-- Two-Column Section: Recent Patients & Recent Lab Tests -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 25px; margin-bottom: 30px;">
    <!-- Recent Patients -->
    <div class="card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="margin: 0; font-size: 1.15rem; color: #2d3748;">
                <i class="fas fa-user-friends" style="color: #1b6ca8;"></i> Recently Registered Patients
            </h3>
            <a href="dashboard.php?view=patient_records" style="font-size: 0.85rem; color: #1b6ca8; text-decoration: none; font-weight: 600;">
                View All <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #edf2f7; color: #718096;">
                    <th style="padding: 10px 8px;">Patient ID</th>
                    <th style="padding: 10px 8px;">Name</th>
                    <th style="padding: 10px 8px;">Village / Area</th>
                    <th style="padding: 10px 8px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_patients && $recent_patients->num_rows > 0): ?>
                    <?php while($pt = $recent_patients->fetch_assoc()): ?>
                        <?php 
                            $pcode = !empty($pt['patient_code']) ? $pt['patient_code'] : 'DSC/' . str_pad($pt['patient_id'], 3, '0', STR_PAD_LEFT); 
                        ?>
                        <tr style="border-bottom: 1px solid #f7fafc;">
                            <td style="padding: 12px 8px;"><span style="background: #edf2f7; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.82rem; color: #2d3748;"><?php echo htmlspecialchars($pcode); ?></span></td>
                            <td style="padding: 12px 8px; font-weight: 600; color: #2d3748;"><?php echo htmlspecialchars($pt['full_name']); ?></td>
                            <td style="padding: 12px 8px; color: #718096;"><?php echo htmlspecialchars($pt['residence'] ?: 'Nagongera'); ?></td>
                            <td style="padding: 12px 8px; text-align: right;">
                                <a href="dashboard.php?view=patient_records&pid=<?php echo $pt['patient_id']; ?>" style="color: #1b6ca8; text-decoration: none; font-weight: 600; font-size: 0.82rem; background: #ebf8ff; padding: 4px 10px; border-radius: 6px;">
                                    <i class="fas fa-notes-medical"></i> Records
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #a0aec0;">No patients registered yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Lab Requests -->
    <div class="card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="margin: 0; font-size: 1.15rem; color: #2d3748;">
                <i class="fas fa-microscope" style="color: #00a896;"></i> Recent Diagnostic Orders
            </h3>
            <a href="dashboard.php?view=pending_tests" style="font-size: 0.85rem; color: #00a896; text-decoration: none; font-weight: 600;">
                Lab Workspace <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #edf2f7; color: #718096;">
                    <th style="padding: 10px 8px;">Patient</th>
                    <th style="padding: 10px 8px;">Test Type</th>
                    <th style="padding: 10px 8px;">Priority</th>
                    <th style="padding: 10px 8px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_requests && $recent_requests->num_rows > 0): ?>
                    <?php while($req = $recent_requests->fetch_assoc()): ?>
                        <?php 
                            $reqPcode = !empty($req['patient_code']) ? $req['patient_code'] : 'DSC/' . str_pad($req['patient_id'], 3, '0', STR_PAD_LEFT);
                            $priority = strtolower($req['priority'] ?? 'normal');
                            $prioBadge = 'background: #edf2f7; color: #4a5568;';
                            if ($priority === 'urgent') $prioBadge = 'background: #feebc8; color: #c05621; font-weight: bold;';
                            if ($priority === 'emergency') $prioBadge = 'background: #fed7d7; color: #9b2c2c; font-weight: bold;';

                            $status = $req['status'];
                            $statBadge = 'background: #fffaf0; color: #dd6b20;';
                            if ($status === 'Completed') $statBadge = 'background: #f0fff4; color: #38a169;';
                            if ($status === 'Flagged') $statBadge = 'background: #fff5f5; color: #e53e3e;';
                        ?>
                        <tr style="border-bottom: 1px solid #f7fafc;">
                            <td style="padding: 12px 8px;">
                                <strong><?php echo htmlspecialchars($req['full_name'] ?? 'Patient #'.$req['patient_id']); ?></strong>
                                <small style="display:block; color: #718096;"><?php echo htmlspecialchars($reqPcode); ?></small>
                            </td>
                            <td style="padding: 12px 8px;"><?php echo htmlspecialchars($test_names[$req['test_id']] ?? 'Lab Test'); ?></td>
                            <td style="padding: 12px 8px;">
                                <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; <?php echo $prioBadge; ?>">
                                    <?php echo strtoupper($priority); ?>
                                </span>
                            </td>
                            <td style="padding: 12px 8px;">
                                <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; <?php echo $statBadge; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #a0aec0;">No diagnostic tests requested yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>