<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

// Aggregate statistics
$total_pts_res = $conn->query("SELECT COUNT(*) as c FROM patients");
$total_pts = ($total_pts_res) ? $total_pts_res->fetch_assoc()['c'] : 0;

$today_pts_res = $conn->query("SELECT COUNT(*) as c FROM patients WHERE DATE(created_at) = CURDATE()");
$today_pts = ($today_pts_res) ? $today_pts_res->fetch_assoc()['c'] : 0;

$total_lab_res = $conn->query("SELECT COUNT(*) as c FROM lab_requests");
$total_lab = ($total_lab_res) ? $total_lab_res->fetch_assoc()['c'] : 0;

$completed_lab_res = $conn->query("SELECT COUNT(*) as c FROM lab_requests WHERE status = 'Completed'");
$completed_lab = ($completed_lab_res) ? $completed_lab_res->fetch_assoc()['c'] : 0;

$flagged_lab_res = $conn->query("SELECT COUNT(*) as c FROM lab_requests WHERE status = 'Flagged'");
$flagged_lab = ($flagged_lab_res) ? $flagged_lab_res->fetch_assoc()['c'] : 0;

// Test breakdown
$test_breakdown = $conn->query("SELECT test_id, COUNT(*) as count FROM lab_requests GROUP BY test_id");
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
                <i class="fas fa-file-invoice-dollar" style="color: #1b6ca8;"></i> Clinic HMIS & Operational Reports
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Statistical summaries, daily patient attendance, and laboratory utilization for Tororo District reporting.
            </p>
        </div>
        <div class="workspace-actions">
            <button class="btn-refresh" onclick="window.print()" style="padding: 9px 18px; border-radius: 8px; border: 1px solid #cbd5e0; background: white; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-print"></i> Print HMIS Summary
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-top: 4px solid #1b6ca8;">
            <h4 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Total Cumulative Patients</h4>
            <p style="margin: 6px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $total_pts; ?></p>
        </div>
        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-top: 4px solid #00a896;">
            <h4 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Registered Today</h4>
            <p style="margin: 6px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $today_pts; ?></p>
        </div>
        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-top: 4px solid #3182ce;">
            <h4 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Total Lab Orders</h4>
            <p style="margin: 6px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $total_lab; ?></p>
        </div>
        <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-top: 4px solid #e53e3e;">
            <h4 style="margin: 0; color: #718096; font-size: 0.85rem; text-transform: uppercase;">Flagged / Critical Results</h4>
            <p style="margin: 6px 0 0; font-size: 1.8rem; font-weight: 800; color: #2d3748;"><?php echo $flagged_lab; ?></p>
        </div>
    </div>

    <!-- HMIS Summary Tables -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 25px;">
        <!-- Test Distribution -->
        <div class="card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; margin-bottom: 16px; color: #2d3748; font-size: 1.15rem;">
                <i class="fas fa-chart-pie" style="color: #1b6ca8;"></i> Diagnostic Test Volume Breakdown
            </h3>

            <table class="modern-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Investigation Category</th>
                        <th style="text-align: right;">Total Requests</th>
                        <th style="text-align: right;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($test_breakdown && $test_breakdown->num_rows > 0): ?>
                        <?php while($tb = $test_breakdown->fetch_assoc()): ?>
                            <?php 
                                $pct = ($total_lab > 0) ? round(($tb['count'] / $total_lab) * 100, 1) : 0;
                            ?>
                            <tr style="border-bottom: 1px solid #f7fafc;">
                                <td><strong><?php echo htmlspecialchars($test_names[$tb['test_id']] ?? 'Investigation #'.$tb['test_id']); ?></strong></td>
                                <td style="text-align: right; font-weight: 700; color: #2d3748;"><?php echo $tb['count']; ?></td>
                                <td style="text-align: right; color: #718096;"><?php echo $pct; ?>%</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 25px; color: #a0aec0;">No test volume records recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Clinic Operation Info -->
        <div class="card" style="background: white; padding: 25px 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; margin-bottom: 16px; color: #2d3748; font-size: 1.15rem;">
                <i class="fas fa-info-circle" style="color: #00a896;"></i> Tororo District HMIS Compliance
            </h3>
            <p style="color: #4a5568; line-height: 1.6; font-size: 0.92rem;">
                This automated reporting module replaces the manual 3-4 day paper compilation at Da Safe Community Clinic. Monthly attendance and disease morbidity metrics are compiled in real-time from electronic consultations and laboratory findings.
            </p>
            <div style="background: #f7fafc; padding: 14px 16px; border-radius: 10px; border: 1px solid #edf2f7; font-size: 0.88rem; margin-top: 15px;">
                <strong><i class="fas fa-check-circle" style="color:#38a169;"></i> Automated Indicators Tracked:</strong>
                <ul style="margin: 8px 0 0 18px; color: #4a5568;">
                    <li>Outpatient (OPD) New & Return Attendances</li>
                    <li>Malaria Positivity & Treatment Rates</li>
                    <li>Typhoid, Glucose, and Routine Diagnostic Load</li>
                    <li>Critical Value Alerts & Turnaround Times</li>
                </ul>
            </div>
        </div>
    </div>
</div>
