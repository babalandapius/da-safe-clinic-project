<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');
require_once(__DIR__ . '/../includes/ai_engine.php');

$query = "SELECT lr.*, p.full_name, p.patient_code, p.gender, p.age, p.residence, 
                 tr.parameter_name, tr.result_value, tr.normal_range, tr.technician_comment, tr.created_at as result_time,
                 u.name as doctor_name
          FROM lab_requests lr 
          JOIN test_results tr ON lr.request_id = tr.test_id 
          LEFT JOIN patients p ON lr.patient_id = p.patient_id 
          LEFT JOIN users u ON lr.doctor_id = u.id 
          WHERE lr.status = 'Completed' OR lr.status = 'Flagged'
          ORDER BY tr.created_at DESC";
$result = mysqli_query($conn, $query);

$test_names = [
    1 => 'Complete Blood Count (CBC)',
    2 => 'Malaria Parasites (MP)',
    3 => 'Urinalysis',
    4 => 'Blood Glucose (FBG/RBG)',
    5 => 'Chest X-Ray (CXR)',
    6 => 'Typhoid Test (Widal)',
    7 => 'HIV Rapid Diagnostic Test'
];
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-clipboard-check" style="color: #1b6ca8;"></i> Verified Laboratory Reports
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Official diagnostic test outcomes certified by the laboratory for Da Safe Community Clinic.
            </p>
        </div>
        <div class="workspace-actions">
            <button class="btn-refresh" onclick="window.print()" style="padding: 9px 18px; border-radius: 8px; border: 1px solid #cbd5e0; background: white; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-print"></i> Print Report Sheet
            </button>
        </div>
    </div>

    <!-- Printable Official Letterhead (Visible on Print) -->
    <div class="print-only-header" style="display: none;">
        <div style="text-align: center; border-bottom: 2px solid #1b6ca8; padding-bottom: 12px; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #1b6ca8; font-size: 1.6rem; text-transform: uppercase;">Da Safe Community Clinic</h2>
            <p style="margin: 2px 0; color: #4a5568;">Nagongera Town Council, Tororo District, Eastern Uganda</p>
            <p style="margin: 0; font-size: 0.85rem; color: #718096;">Tel: +256 700 000 000 &bull; Email: records@dasafeclinic.ug</p>
            <h3 style="margin: 10px 0 0; font-size: 1.1rem; color: #2d3748; text-decoration: underline;">OFFICIAL MEDICAL LABORATORY REPORT</h3>
        </div>
    </div>

    <div class="table-card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <table class="modern-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Details</th>
                    <th>Investigation</th>
                    <th>Finding / Result</th>
                    <th>Status</th>
                    <th>Date Verified</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <?php 
                            $pcode = !empty($row['patient_code']) ? $row['patient_code'] : 'DSC/' . str_pad($row['patient_id'], 3, '0', STR_PAD_LEFT);
                            $status = $row['status'];
                            $statBadge = ($status === 'Flagged') ? 'background: #fff5f5; color: #e53e3e; font-weight: 700;' : 'background: #f0fff4; color: #38a169; font-weight: 600;';
                            $ai = LabAI::analyzeResult($row['parameter_name'], $row['result_value']);
                        ?>
                    <tr style="border-bottom: 1px solid #f7fafc;">
                        <td>
                            <span style="background: #edf2f7; color: #1b6ca8; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.82rem;">
                                <?php echo htmlspecialchars($pcode); ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color: #2d3748;"><?php echo htmlspecialchars($row['full_name'] ?: 'Patient #'.$row['patient_id']); ?></strong>
                            <small style="display: block; color: #718096;"><?php echo htmlspecialchars($row['gender'] ?? ''); ?>, <?php echo htmlspecialchars($row['age'] ?? ''); ?> yrs &bull; <?php echo htmlspecialchars($row['residence'] ?: 'Nagongera'); ?></small>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #2d3748;"><?php echo htmlspecialchars($test_names[$row['test_id']] ?? $row['parameter_name']); ?></span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1b6ca8; font-size: 0.95rem;">
                                <?php echo htmlspecialchars($row['result_value']); ?>
                            </div>
                            <?php if(!empty($row['normal_range'])): ?>
                                <small style="color: #718096;">Ref: <?php echo htmlspecialchars($row['normal_range']); ?></small>
                            <?php endif; ?>
                            <?php if(!empty($row['technician_comment'])): ?>
                                <small style="display: block; color: #4a5568; margin-top: 3px; font-style: italic;"><?php echo htmlspecialchars($row['technician_comment']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; <?php echo $statBadge; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['result_time'] ?: $row['request_date'])); ?></td>
                        <td style="text-align: right;">
                            <button onclick="window.print()" style="background: #edf2f7; color: #2b6cb0; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 50px; color: #a0aec0;">
                            <i class="fas fa-folder-open fa-3x" style="margin-bottom: 12px;"></i>
                            <p>No verified laboratory test findings in the system yet.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .top-bar, .sidebar, .workspace-actions, footer, .btn-refresh {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .print-only-header {
        display: block !important;
    }
    .table-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>