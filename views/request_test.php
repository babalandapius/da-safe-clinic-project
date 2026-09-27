
<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

$pre_pid = intval($_GET['pid'] ?? 0);
$patients = $conn->query("SELECT patient_id, patient_code, full_name, residence FROM patients ORDER BY full_name ASC");
?>

<div class="centered-form-wrapper" style="max-width: 750px; margin: 0 auto; padding: 20px 0;">
    <div class="card request-card" style="background: white; padding: 35px 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        
        <div style="margin-bottom: 22px;">
            <h2 style="font-size: 1.5rem; color: #1a202c; margin: 0;">
                <i class="fas fa-file-medical" style="color: #1b6ca8;"></i> Order Diagnostic Laboratory Test
            </h2>
            <p class="form-subtitle" style="color: #718096; margin: 5px 0 0; font-size: 0.9rem;">
                Select registered patient, diagnostic investigation, and clinical urgency.
            </p>
        </div>

        <form action="dashboard.php" method="POST" class="modern-form">
            <input type="hidden" name="action" value="submit_test_request">

            <div class="form-group" style="margin-bottom: 18px;">
                <label for="patient_id" style="font-weight: 600; color: #4a5568;"><i class="fas fa-id-badge"></i> Select Registered Patient <span style="color:red;">*</span></label>
                <?php if ($patients && mysqli_num_rows($patients) > 0): ?>
                    <select id="patient_id" name="patient_id" required style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        <option value="" disabled <?php echo ($pre_pid == 0) ? 'selected' : ''; ?>>-- Select registered patient --</option>
                        <?php while ($patient = mysqli_fetch_assoc($patients)): ?>
                            <?php 
                                $code = !empty($patient['patient_code']) ? $patient['patient_code'] : 'DSC/' . str_pad($patient['patient_id'], 3, '0', STR_PAD_LEFT);
                                $isSelected = ($pre_pid == $patient['patient_id']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo htmlspecialchars($patient['patient_id']); ?>" <?php echo $isSelected; ?>>
                                <?php echo htmlspecialchars($code); ?> - <?php echo htmlspecialchars($patient['full_name']); ?> (<?php echo htmlspecialchars($patient['residence'] ?: 'Nagongera'); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <small style="color: #718096; font-size: 0.82rem; margin-top: 4px; display: block;">
                        Not registered? <a href="dashboard.php?view=register_patient" style="color: #1b6ca8; font-weight: 600;">Register new patient</a> first.
                    </small>
                <?php else: ?>
                    <p style="margin: 0 0 10px; color: #e53e3e;">No registered patients found in the clinic database.</p>
                    <a href="dashboard.php?view=register_patient" class="btn-primary" style="display: inline-block; padding: 10px 16px; text-decoration: none; border-radius: 8px;">Register New Patient First</a>
                <?php endif; ?>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label for="test_type" style="font-weight: 600; color: #4a5568;"><i class="fas fa-vial"></i> Diagnostic Test Type <span style="color:red;">*</span></label>
                    <select id="test_type" name="test_type" required style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        <option value="" disabled selected>-- Select a test... --</option>
                        <option value="malaria">Malaria Parasites (MP / Rapid Test)</option>
                        <option value="typhoid">Typhoid Test (Widal / Ag)</option>
                        <option value="cbc">Complete Blood Count (CBC)</option>
                        <option value="urinalysis">Urinalysis (Routine)</option>
                        <option value="glucose">Blood Glucose (FBG/RBG)</option>
                        <option value="hiv">HIV Rapid Diagnostic Test (RDT)</option>
                        <option value="xray">Chest X-Ray (CXR)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority_level" style="font-weight: 600; color: #4a5568;"><i class="fas fa-exclamation-triangle"></i> Priority Urgency</label>
                    <select id="priority_level" name="priority_level" style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        <option value="normal" selected>Normal (Standard Queue)</option>
                        <option value="urgent">Urgent (High Priority)</option>
                        <option value="emergency">Emergency / STAT (Immediate Attention)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 22px;">
                <label for="observations" style="font-weight: 600; color: #4a5568;"><i class="fas fa-comment-medical"></i> Clinical Observations & Reason for Order</label>
                <textarea id="observations" name="observations" rows="3" placeholder="Describe symptoms, suspected condition, or clinical suspicion for lab technician..." style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-family: inherit;"></textarea>
            </div>

            <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center;">
                <a href="dashboard.php?view=overview" class="btn-form secondary" style="padding: 12px 20px; background: #edf2f7; color: #4a5568; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn-form primary" style="padding: 12px 26px; background: #1b6ca8; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Submit Test Request to Lab
                </button>
            </div>
        </form>
    </div>
</div>