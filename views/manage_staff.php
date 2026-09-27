<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');

// Restrict to Admin
if (($_SESSION['user_role'] ?? '') !== 'Admin') {
    echo "<div class='alert alert-error' style='padding:20px; margin:20px; background:#fff5f5; color:#c53030; border-radius:10px;'>";
    echo "<h3><i class='fas fa-lock'></i> Access Restricted</h3>";
    echo "<p>Only System Administrators have permission to manage staff accounts.</p>";
    echo "</div>";
    return;
}

// Fetch staff members
$staff_list = $conn->query("SELECT * FROM users ORDER BY role ASC, name ASC");
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-user-md" style="color: #1b6ca8;"></i> Staff Management (Clinical Staff & Technicians)
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Administer user credentials and role-based permissions for Da Safe Community Clinic personnel.
            </p>
        </div>
    </div>

    <!-- Two columns: Staff Table & Add Staff Form -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
        
        <!-- Left: Staff Table -->
        <div class="card" style="background: white; padding: 25px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; margin-bottom: 16px; color: #2d3748; font-size: 1.15rem;">
                <i class="fas fa-users-cog" style="color: #1b6ca8;"></i> Active Clinic Staff
            </h3>

            <table class="modern-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Email / Contact</th>
                        <th>Role & Dept</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($staff_list && $staff_list->num_rows > 0): ?>
                        <?php while($s = $staff_list->fetch_assoc()): ?>
                            <?php
                                $roleClass = 'background: #ebf8ff; color: #2b6cb0;';
                                if ($s['role'] === 'Admin') $roleClass = 'background: #faf5ff; color: #6b46c1;';
                                if ($s['role'] === 'Nurse') $roleClass = 'background: #e6fffa; color: #234e52;';
                                if ($s['role'] === 'Lab Technician') $roleClass = 'background: #f0fff4; color: #22543d;';
                            ?>
                            <tr style="border-bottom: 1px solid #f7fafc;">
                                <td>
                                    <strong style="color: #2d3748;"><?php echo htmlspecialchars($s['name']); ?></strong>
                                    <small style="display: block; color: #718096;">ID: #<?php echo $s['id']; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($s['email']); ?></td>
                                <td>
                                    <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700; <?php echo $roleClass; ?>">
                                        <?php echo htmlspecialchars($s['role'] ?: 'Doctor'); ?>
                                    </span>
                                    <small style="display: block; color: #718096; margin-top: 2px;"><?php echo htmlspecialchars($s['specialization'] ?: 'General'); ?></small>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($s['id'] != $_SESSION['user_id']): ?>
                                        <a href="process_lab.php?action=admin_delete_staff&id=<?php echo $s['id']; ?>" onclick="return confirm('Are you sure you want to remove this staff account?');" style="color: #e53e3e; text-decoration: none; padding: 4px 8px; border-radius: 4px; background: #fff5f5; font-size: 0.8rem; font-weight: 600;">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #a0aec0; font-size: 0.78rem; font-style: italic;">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Right: Add New Staff Form -->
        <div class="card" style="background: white; padding: 25px 30px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; margin-bottom: 16px; color: #2d3748; font-size: 1.15rem;">
                <i class="fas fa-user-plus" style="color: #00a896;"></i> Add New Staff Member
            </h3>

            <form action="process_lab.php?action=admin_add_staff" method="POST">
                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-user"></i> Full Name <span style="color:red;">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Dr. Jane Nabukenya" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-envelope"></i> Work Email Address <span style="color:red;">*</span></label>
                    <input type="email" name="email" required placeholder="e.g. staff@dasafe.com" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-user-tag"></i> Role <span style="color:red;">*</span></label>
                        <select name="role" required style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                            <option value="Doctor">Doctor (Clinician)</option>
                            <option value="Nurse">Nurse (Triage)</option>
                            <option value="Lab Technician">Lab Technician</option>
                            <option value="Admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-stethoscope"></i> Department</label>
                        <input type="text" name="specialization" placeholder="e.g. OPD / Pathology" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #4a5568; font-size: 0.85rem;"><i class="fas fa-lock"></i> Initial Password <span style="color:red;">*</span></label>
                    <input type="password" name="password" required placeholder="Set default password" value="123456" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e0; border-radius: 8px;">
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-check-circle"></i> Create Staff Account
                </button>
            </form>
        </div>
    </div>
</div>
