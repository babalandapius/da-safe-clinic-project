<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

$test_names = [
    1 => 'Complete Blood Count (CBC)',
    2 => 'Malaria Parasites (MP)',
    3 => 'Urinalysis',
    4 => 'Blood Glucose (FBG/RBG)',
    5 => 'Chest X-Ray (CXR)',
    6 => 'Typhoid Test (Widal)',
    7 => 'HIV Rapid Diagnostic Test'
];

$selected_pid = intval($_GET['pid'] ?? 0);
$search_query = trim($_GET['search'] ?? '');

// If search query is provided
$patients_list = [];
if (!empty($search_query)) {
    $search_param = "%$search_query%";
    $stmt = $conn->prepare("SELECT * FROM patients WHERE full_name LIKE ? OR contact LIKE ? OR patient_code LIKE ? OR patient_id = ? ORDER BY created_at DESC");
    $search_int = intval($search_query);
    $stmt->bind_param("sssi", $search_param, $search_param, $search_param, $search_int);
    $stmt->execute();
    $patients_list = $stmt->get_result();
} else {
    $patients_list = $conn->query("SELECT * FROM patients ORDER BY created_at DESC LIMIT 20");
}

// Fetch selected patient details if $selected_pid > 0
$selected_patient = null;
$patient_vitals = [];
$patient_lab_history = [];

if ($selected_pid > 0) {
    $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $selected_pid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $selected_patient = $res->fetch_assoc();
        
        // Fetch past vitals
        $vStmt = $conn->prepare("SELECT pv.*, u.name as doctor_name FROM patient_vitals pv LEFT JOIN users u ON pv.doctor_id = u.id WHERE pv.patient_id = ? ORDER BY pv.visit_date DESC");
        $vStmt->bind_param("i", $selected_pid);
        $vStmt->execute();
        $patient_vitals = $vStmt->get_result();

        // Fetch past lab requests and results
        $lStmt = $conn->prepare("SELECT lr.*, tr.parameter_name, tr.result_value, tr.normal_range, tr.technician_comment 
                                FROM lab_requests lr 
                                LEFT JOIN test_results tr ON lr.request_id = tr.test_id 
                                WHERE lr.patient_id = ? 
                                ORDER BY lr.request_date DESC");
        $lStmt->bind_param("i", $selected_pid);
        $lStmt->execute();
        $patient_lab_history = $lStmt->get_result();
    }
}
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-notes-medical" style="color: #1b6ca8;"></i> Patient Electronic Records & Vitals
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Search patient, record triage vital signs, clinical diagnosis, and view chronological medical history.
            </p>
        </div>
        <div class="workspace-actions">
            <a href="dashboard.php?view=register_patient" class="btn-primary" style="padding: 10px 18px; text-decoration: none; border-radius: 8px; font-size: 0.9rem;">
                <i class="fas fa-user-plus"></i> New Patient
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <form action="dashboard.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="hidden" name="view" value="patient_records">
            <div style="flex: 1; min-width: 280px; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 14px; top: 14px; color: #a0aec0;"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Patient ID (e.g. DSC/001), Full Name, or Phone..." style="width: 100%; padding: 12px 12px 12px 40px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <button type="submit" class="btn-primary" style="padding: 12px 22px; border-radius: 8px; cursor: pointer; border: none;">
                <i class="fas fa-search"></i> Search Record
            </button>
            <?php if (!empty($search_query)): ?>
                <a href="dashboard.php?view=patient_records" style="padding: 12px 18px; background: #edf2f7; color: #4a5568; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center;">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($selected_patient): ?>
        <!-- SELECTED PATIENT VIEW -->
        <?php 
            $pcode = !empty($selected_patient['patient_code']) ? $selected_patient['patient_code'] : 'DSC/' . str_pad($selected_patient['patient_id'], 3, '0', STR_PAD_LEFT); 
        ?>
        
        <!-- Patient Bio Summary Card -->
        <div class="card" style="background: linear-gradient(135deg, #1b6ca8 0%, #0a4b78 100%); color: white; padding: 25px 30px; border-radius: 14px; box-shadow: 0 10px 25px rgba(27, 108, 168, 0.25); margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.5px;">
                            <?php echo htmlspecialchars($pcode); ?>
                        </span>
                        <h2 style="margin: 0; font-size: 1.6rem;"><?php echo htmlspecialchars($selected_patient['full_name']); ?></h2>
                    </div>
                    <p style="margin: 0; opacity: 0.9; font-size: 0.95rem;">
                        <i class="fas fa-venus-mars"></i> <?php echo htmlspecialchars($selected_patient['gender']); ?> &bull; 
                        <i class="fas fa-birthday-cake"></i> <?php echo htmlspecialchars($selected_patient['age']); ?> Years &bull; 
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($selected_patient['residence'] ?: 'Nagongera Town Council'); ?> &bull; 
                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($selected_patient['contact'] ?: 'No Phone'); ?>
                    </p>
                    <?php if (!empty($selected_patient['next_of_kin'])): ?>
                    <p style="margin: 6px 0 0; opacity: 0.8; font-size: 0.85rem;">
                        <strong>Next of Kin:</strong> <?php echo htmlspecialchars($selected_patient['next_of_kin']); ?> (<?php echo htmlspecialchars($selected_patient['next_of_kin_contact'] ?: 'N/A'); ?>)
                    </p>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="dashboard.php?view=request_test&pid=<?php echo $selected_patient['patient_id']; ?>" style="background: #00a896; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.88rem; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <i class="fas fa-vial"></i> Order Diagnostic Test
                    </a>
                </div>
            </div>
        </div>

        <!-- Consultation & Vitals Form + Medical Timeline -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
            <!-- Left Column: Enter New Vitals & Diagnosis -->
            <div class="card" style="background: white; padding: 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <h3 style="margin-top: 0; margin-bottom: 18px; color: #1a202c; font-size: 1.2rem;">
                    <i class="fas fa-stethoscope" style="color: #1b6ca8;"></i> Clinical Triage & Consultation
                </h3>

                <form action="dashboard.php" method="POST">
                    <input type="hidden" name="action" value="save_vitals">
                    <input type="hidden" name="patient_id" value="<?php echo $selected_patient['patient_id']; ?>">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-heart"></i> Blood Pressure (BP)</label>
                            <input type="text" name="blood_pressure" placeholder="e.g. 120/80 mmHg" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-thermometer-half"></i> Temperature (°C)</label>
                            <input type="text" name="temperature" placeholder="e.g. 37.5 °C" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-weight"></i> Weight (kg)</label>
                            <input type="text" name="weight" placeholder="e.g. 68 kg" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-ruler-vertical"></i> Height (cm)</label>
                            <input type="text" name="height" placeholder="e.g. 170 cm" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-comment-medical"></i> Presenting Symptoms / Chief Complaints</label>
                        <textarea name="symptoms" rows="2" placeholder="e.g. High fever, headache for 3 days, chills..." style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-family: inherit;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-diagnoses"></i> Clinical Diagnosis</label>
                        <textarea name="diagnosis" rows="2" placeholder="e.g. Suspected Severe Malaria / Acute UTI..." style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-family: inherit;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 18px;">
                        <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-pills"></i> Treatment / Prescription Plan</label>
                        <textarea name="prescription" rows="2" placeholder="e.g. Artemether-Lumefantrine 20/120mg, Paracetamol 1g TDS..." style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px; font-family: inherit;"></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-save"></i> Save Clinical Consultation
                    </button>
                </form>
            </div>

            <!-- Right Column: Medical History & Lab Findings Timeline -->
            <div class="card" style="background: white; padding: 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <h3 style="margin-top: 0; margin-bottom: 18px; color: #1a202c; font-size: 1.2rem;">
                    <i class="fas fa-history" style="color: #00a896;"></i> Medical & Lab History Timeline
                </h3>

                <!-- Clinical Vitals Timeline -->
                <h4 style="font-size: 0.95rem; color: #4a5568; margin: 15px 0 10px 0; border-bottom: 1px solid #edf2f7; padding-bottom: 6px;">
                    <i class="fas fa-clipboard-list"></i> Past Clinical Consultations
                </h4>
                
                <?php if ($patient_vitals && $patient_vitals->num_rows > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 14px; max-height: 250px; overflow-y: auto; padding-right: 5px;">
                        <?php while($v = $patient_vitals->fetch_assoc()): ?>
                            <div style="background: #f8fafc; border-left: 3px solid #1b6ca8; padding: 12px 16px; border-radius: 8px; font-size: 0.88rem;">
                                <div style="display: flex; justify-content: space-between; color: #718096; font-size: 0.8rem; margin-bottom: 4px;">
                                    <span><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y h:i A', strtotime($v['visit_date'])); ?></span>
                                    <span><i class="fas fa-user-md"></i> <?php echo htmlspecialchars($v['doctor_name'] ?: 'Attending Clinician'); ?></span>
                                </div>
                                <div style="display: flex; gap: 12px; margin-bottom: 6px; font-weight: 600; color: #2d3748;">
                                    <?php if ($v['blood_pressure']) echo "<span>BP: {$v['blood_pressure']}</span>"; ?>
                                    <?php if ($v['temperature']) echo "<span>Temp: {$v['temperature']}</span>"; ?>
                                    <?php if ($v['weight']) echo "<span>Wt: {$v['weight']}</span>"; ?>
                                </div>
                                <?php if ($v['diagnosis']): ?>
                                    <p style="margin: 3px 0; color: #2b6cb0;"><strong>Diagnosis:</strong> <?php echo htmlspecialchars($v['diagnosis']); ?></p>
                                <?php endif; ?>
                                <?php if ($v['prescription']): ?>
                                    <p style="margin: 3px 0; color: #276749;"><strong>Rx:</strong> <?php echo htmlspecialchars($v['prescription']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #a0aec0; font-size: 0.88rem; margin: 10px 0;">No prior clinical vitals recorded.</p>
                <?php endif; ?>

                <!-- Lab History Timeline -->
                <h4 style="font-size: 0.95rem; color: #4a5568; margin: 20px 0 10px 0; border-bottom: 1px solid #edf2f7; padding-bottom: 6px;">
                    <i class="fas fa-microscope"></i> Laboratory Investigations
                </h4>

                <?php if ($patient_lab_history && $patient_lab_history->num_rows > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 14px; max-height: 250px; overflow-y: auto; padding-right: 5px;">
                        <?php while($lh = $patient_lab_history->fetch_assoc()): ?>
                            <?php
                                $status = $lh['status'];
                                $badgeColor = '#d69e2e';
                                if ($status === 'Completed') $badgeColor = '#38a169';
                                if ($status === 'Flagged') $badgeColor = '#e53e3e';
                            ?>
                            <div style="background: #f8fafc; border-left: 3px solid <?php echo $badgeColor; ?>; padding: 12px 16px; border-radius: 8px; font-size: 0.88rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <strong style="color: #2d3748;"><?php echo htmlspecialchars($test_names[$lh['test_id']] ?? 'Lab Test'); ?></strong>
                                    <span style="background: rgba(0,0,0,0.05); color: <?php echo $badgeColor; ?>; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.78rem;">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </div>
                                <div style="font-size: 0.8rem; color: #718096; margin-bottom: 4px;">
                                    Ordered: <?php echo date('M d, Y', strtotime($lh['request_date'])); ?>
                                </div>
                                <?php if (!empty($lh['result_value'])): ?>
                                    <div style="background: white; padding: 8px; border-radius: 6px; border: 1px solid #edf2f7; margin-top: 5px;">
                                        <p style="margin: 0; font-weight: 600; color: #2d3748;">
                                            Finding: <span style="color: #1b6ca8;"><?php echo htmlspecialchars($lh['result_value']); ?></span>
                                            <?php if (!empty($lh['normal_range'])) echo " <small style='color:#718096;'>(Ref: {$lh['normal_range']})</small>"; ?>
                                        </p>
                                        <?php if (!empty($lh['technician_comment'])): ?>
                                            <p style="margin: 4px 0 0; color: #4a5568; font-size: 0.82rem;"><?php echo htmlspecialchars($lh['technician_comment']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #a0aec0; font-size: 0.88rem; margin: 10px 0;">No lab tests recorded for this patient.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- ALL PATIENTS DIRECTORY TABLE -->
        <div class="card" style="background: white; padding: 25px 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                <h3 style="margin: 0; color: #2d3748; font-size: 1.2rem;">
                    <i class="fas fa-users" style="color: #1b6ca8;"></i> Registered Patient Directory (Da Safe Clinic)
                </h3>
                <span style="font-size: 0.88rem; color: #718096;">Click any patient to open their medical chart</span>
            </div>

            <table class="modern-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Full Name</th>
                        <th>Age / Gender</th>
                        <th>Contact Phone</th>
                        <th>Village / Residence</th>
                        <th>Registration Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($patients_list && $patients_list->num_rows > 0): ?>
                        <?php while($p = $patients_list->fetch_assoc()): ?>
                            <?php 
                                $code = !empty($p['patient_code']) ? $p['patient_code'] : 'DSC/' . str_pad($p['patient_id'], 3, '0', STR_PAD_LEFT);
                            ?>
                            <tr>
                                <td>
                                    <span style="background: #edf2f7; color: #1b6ca8; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.82rem;">
                                        <?php echo htmlspecialchars($code); ?>
                                    </span>
                                </td>
                                <td><strong style="color: #2d3748;"><?php echo htmlspecialchars($p['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['age'] ? $p['age'].' yrs' : 'N/A'); ?> / <?php echo htmlspecialchars($p['gender'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($p['contact'] ?: 'None'); ?></td>
                                <td><?php echo htmlspecialchars($p['residence'] ?: 'Nagongera'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                                <td style="text-align: right;">
                                    <a href="dashboard.php?view=patient_records&pid=<?php echo $p['patient_id']; ?>" style="background: #1b6ca8; color: white; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 0.82rem; font-weight: 600;">
                                        <i class="fas fa-folder-open"></i> Open Chart
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #a0aec0;">
                                <i class="fas fa-folder-open fa-3x" style="margin-bottom: 12px;"></i>
                                <p>No matching patient records found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
