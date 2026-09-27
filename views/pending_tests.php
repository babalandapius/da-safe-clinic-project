<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

// Test type mapping
$test_names = [
    1 => 'Complete Blood Count (CBC)',
    2 => 'Malaria Parasites (MP)',
    3 => 'Urinalysis',
    4 => 'Blood Glucose (FBG/RBG)',
    5 => 'Chest X-Ray (CXR)',
    6 => 'Typhoid Test (Widal)',
    7 => 'HIV Rapid Diagnostic Test'
];

$priority_counts = [
    'emergency' => 0,
    'urgent' => 0
];
$priority_query = "SELECT priority, COUNT(*) as total FROM lab_requests WHERE status != 'Completed' AND status != 'Flagged' GROUP BY priority";
$priority_result = mysqli_query($conn, $priority_query);
if ($priority_result) {
    while ($row = mysqli_fetch_assoc($priority_result)) {
        $p = strtolower($row['priority'] ?? '');
        if ($p === 'emergency' || $p === 'urgent') {
            $priority_counts[$p] = intval($row['total']);
        }
    }
}

$query = "SELECT lr.*, p.full_name, p.patient_code, p.gender, p.age, u.name as doctor_name 
          FROM lab_requests lr 
          LEFT JOIN patients p ON lr.patient_id = p.patient_id 
          LEFT JOIN users u ON lr.doctor_id = u.id 
          WHERE lr.status != 'Completed' AND lr.status != 'Flagged' 
          ORDER BY CASE lr.priority 
                WHEN 'emergency' THEN 1 
                WHEN 'urgent' THEN 2 
                ELSE 3 
            END, lr.request_date DESC";
$result = mysqli_query($conn, $query);
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-microscope" style="color: #1b6ca8;"></i> Laboratory Workspace & Pending Queue
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Process diagnostic test orders submitted by clinical staff (Emergency/STAT tests are prioritized).
            </p>
        </div>
        <div class="workspace-actions">
            <button class="btn-refresh" onclick="window.location.reload()" style="padding: 9px 16px; border-radius: 8px; border: 1px solid #cbd5e0; background: white; cursor: pointer;">
                <i class="fas fa-sync-alt"></i> Refresh Queue
            </button>
        </div>
    </div>

    <?php if ($priority_counts['emergency'] > 0 || $priority_counts['urgent'] > 0): ?>
    <div class="alert warning" style="margin-bottom: 20px; padding: 14px 18px; border-radius: 10px; background: #fffaf0; color: #9c4221; border-left: 5px solid #dd6b20;">
        <strong><i class="fas fa-exclamation-triangle"></i> Priority Queue Notice:</strong>
        <?php if ($priority_counts['emergency'] > 0): ?>
            <span style="font-weight: 700; color: #c53030;"><?php echo $priority_counts['emergency']; ?> STAT/Emergency</span> patient(s) waiting. 
        <?php endif; ?>
        <?php if ($priority_counts['urgent'] > 0): ?>
            <span style="font-weight: 700; color: #dd6b20;"><?php echo $priority_counts['urgent']; ?> Urgent</span> request(s) queued next.
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="table-card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <table class="modern-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Investigation Ordered</th>
                    <th>Priority Level</th>
                    <th>Ordering Doctor</th>
                    <th>Received At</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <?php 
                            $pcode = !empty($row['patient_code']) ? $row['patient_code'] : 'DSC/' . str_pad($row['patient_id'], 3, '0', STR_PAD_LEFT);
                            $priority = strtolower($row['priority'] ?? 'normal');
                            $prioBadge = 'background: #edf2f7; color: #4a5568;';
                            if ($priority === 'urgent') $prioBadge = 'background: #feebc8; color: #c05621; font-weight: bold;';
                            if ($priority === 'emergency') $prioBadge = 'background: #fed7d7; color: #9b2c2c; font-weight: bold;';
                        ?>
                    <tr style="border-bottom: 1px solid #f7fafc;">
                        <td>
                            <span style="background: #edf2f7; color: #1b6ca8; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.82rem;">
                                <?php echo htmlspecialchars($pcode); ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color: #2d3748;"><?php echo htmlspecialchars($row['full_name'] ?: 'Patient #'.$row['patient_id']); ?></strong>
                            <small style="display:block; color: #718096;"><?php echo htmlspecialchars($row['gender'] ?? ''); ?>, <?php echo htmlspecialchars($row['age'] ?? ''); ?> yrs</small>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #2d3748;"><?php echo htmlspecialchars($test_names[$row['test_id']] ?? 'Diagnostic Test'); ?></span>
                            <?php if (!empty($row['observations'])): ?>
                                <small style="display:block; color: #718096; font-style: italic; max-width: 250px;">"<?php echo htmlspecialchars($row['observations']); ?>"</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; <?php echo $prioBadge; ?>">
                                <?php echo strtoupper($priority); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['doctor_name'] ?: 'Dr. Okello'); ?></td>
                        <td><?php echo date('M d, h:i A', strtotime($row['request_date'])); ?></td>
                        <td style="text-align: right;">
                            <a href="dashboard.php?view=enter_results&id=<?php echo $row['request_id']; ?>" style="background: #00a896; color: white; padding: 7px 16px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-keyboard"></i> Enter Result
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 50px; color: #a0aec0;">
                            <i class="fas fa-check-double fa-3x" style="margin-bottom: 12px; color: #48bb78;"></i>
                            <h4 style="margin: 0; color: #4a5568;">All Lab Requests Processed!</h4>
                            <p style="margin: 4px 0 0;">There are currently no pending diagnostic tests in the queue.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>