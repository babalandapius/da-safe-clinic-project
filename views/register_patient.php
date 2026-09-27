<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

// Compute next patient ID preview
$maxRes = $conn->query("SELECT MAX(patient_id) as max_id FROM patients");
$next_id = 1;
if ($maxRes && $row = $maxRes->fetch_assoc()) {
    $next_id = intval($row['max_id']) + 1;
}
$preview_code = "DSC/" . str_pad($next_id, 3, "0", STR_PAD_LEFT);
?>

<div class="centered-form-wrapper" style="max-width: 750px; margin: 0 auto; padding: 20px 0;">
    <div class="card request-card" style="background: white; padding: 35px 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h2 style="font-size: 1.5rem; color: #1a202c; margin: 0;">
                    <i class="fas fa-user-plus" style="color: #1b6ca8;"></i> Register New Patient
                </h2>
                <p class="form-subtitle" style="color: #718096; margin: 5px 0 0; font-size: 0.9rem;">
                    Capture demographics and bio details for Da Safe Community Clinic records.
                </p>
            </div>
            <div style="background: #ebf8ff; color: #2b6cb0; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 0.88rem; border: 1px solid #bee3f8;">
                <i class="fas fa-id-card"></i> Auto ID: <?php echo $preview_code; ?>
            </div>
        </div>

        <form action="dashboard.php" method="POST" class="modern-form">
            <input type="hidden" name="action" value="submit_patient_registration">

            <div class="form-group" style="margin-bottom: 18px;">
                <label for="full_name" style="font-weight: 600; color: #4a5568;"><i class="fas fa-user"></i> Full Name <span style="color:red;">*</span></label>
                <input type="text" id="full_name" name="full_name" placeholder="e.g. John Baptist Okoth" required style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label for="age" style="font-weight: 600; color: #4a5568;"><i class="fas fa-birthday-cake"></i> Age (Years)</label>
                    <input type="number" id="age" name="age" min="0" max="130" placeholder="e.g. 34" style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label for="gender" style="font-weight: 600; color: #4a5568;"><i class="fas fa-venus-mars"></i> Gender <span style="color:red;">*</span></label>
                    <select id="gender" name="gender" required style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        <option value="" disabled selected>-- Select gender --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label for="contact" style="font-weight: 600; color: #4a5568;"><i class="fas fa-phone"></i> Patient Phone Number</label>
                    <input type="tel" id="contact" name="contact" placeholder="e.g. 0772 000 000" style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label for="residence" style="font-weight: 600; color: #4a5568;"><i class="fas fa-map-marker-alt"></i> Village / Residence (Tororo)</label>
                    <input type="text" id="residence" name="residence" placeholder="e.g. Nagongera Central" style="width: 100%; padding: 12px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>
            </div>

            <div style="background: #f7fafc; padding: 16px 18px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #edf2f7;">
                <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 0.95rem;"><i class="fas fa-user-shield"></i> Next of Kin Details (Emergency Contact)</h4>
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="next_of_kin" style="font-weight: 600; color: #4a5568; font-size: 0.85rem;">Next of Kin Full Name</label>
                        <input type="text" id="next_of_kin" name="next_of_kin" placeholder="e.g. Mary Aketch" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="next_of_kin_contact" style="font-weight: 600; color: #4a5568; font-size: 0.85rem;">Next of Kin Phone</label>
                        <input type="tel" id="next_of_kin_contact" name="next_of_kin_contact" placeholder="e.g. 0782 000 000" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                    </div>
                </div>
            </div>

            <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
                <a href="dashboard.php?view=overview" class="btn-form secondary" style="padding: 12px 20px; background: #edf2f7; color: #4a5568; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn-form primary" style="padding: 12px 26px; background: #1b6ca8; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Save & Open Patient File
                </button>
            </div>
        </form>
    </div>
</div>