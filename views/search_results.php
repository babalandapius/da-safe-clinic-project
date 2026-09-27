<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
global $conn;
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Search in patients table
$search_term = "%$query%";
$stmt = $conn->prepare("SELECT * FROM patients WHERE full_name LIKE ? OR contact LIKE ? OR patient_code LIKE ? OR residence LIKE ? ORDER BY created_at DESC");
$stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
$stmt->execute();
$patients_found = $stmt->get_result();
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-search" style="color: #1b6ca8;"></i> Search Results for: "<?php echo htmlspecialchars($query); ?>"
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Found <?php echo $patients_found ? $patients_found->num_rows : 0; ?> patient record(s) matching your inquiry (< 5 seconds).
            </p>
        </div>
    </div>

    <div class="card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <table class="modern-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Full Name</th>
                    <th>Age & Gender</th>
                    <th>Contact Phone</th>
                    <th>Village / Residence</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($patients_found && $patients_found->num_rows > 0): ?>
                    <?php while($p = $patients_found->fetch_assoc()): ?>
                        <?php 
                            $pcode = !empty($p['patient_code']) ? $p['patient_code'] : 'DSC/' . str_pad($p['patient_id'], 3, '0', STR_PAD_LEFT);
                        ?>
                    <tr style="border-bottom: 1px solid #f7fafc;">
                        <td>
                            <span style="background: #edf2f7; color: #1b6ca8; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.82rem;">
                                <?php echo htmlspecialchars($pcode); ?>
                            </span>
                        </td>
                        <td><strong style="color: #2d3748;"><?php echo htmlspecialchars($p['full_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['age'] ?: 'N/A'); ?> yrs / <?php echo htmlspecialchars($p['gender'] ?: 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($p['contact'] ?: 'No phone'); ?></td>
                        <td><?php echo htmlspecialchars($p['residence'] ?: 'Nagongera'); ?></td>
                        <td style="text-align: right;">
                            <a href="dashboard.php?view=patient_records&pid=<?php echo $p['patient_id']; ?>" style="background: #1b6ca8; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.82rem; font-weight: 600; margin-right: 6px;">
                                <i class="fas fa-folder-open"></i> View Records
                            </a>
                            <a href="dashboard.php?view=request_test&pid=<?php echo $p['patient_id']; ?>" style="background: #00a896; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.82rem; font-weight: 600;">
                                <i class="fas fa-vial"></i> Order Test
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #a0aec0;">
                            <i class="fas fa-search fa-3x" style="margin-bottom: 12px;"></i>
                            <p>No patients found matching "<?php echo htmlspecialchars($query); ?>".</p>
                            <a href="dashboard.php?view=register_patient" class="btn-primary" style="display: inline-block; padding: 8px 16px; text-decoration: none; border-radius: 6px; margin-top: 10px;">Register New Patient</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>