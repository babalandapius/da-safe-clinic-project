<?php
$role = $_SESSION['user_role'] ?? 'Doctor';
$view = $_GET['view'] ?? 'overview';
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <i class="fas fa-heartbeat"></i>
        <span>DA SAFE CLINIC</span>
    </div>
    
    <div class="sidebar-profile">
        <img src="assets/images/user-avatar.svg" onerror="this.src='assets/images/user-avatar.svg'" alt="User Avatar">
        <div class="profile-info">
            <span class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Staff Member'); ?></span>
            <span class="profile-role" style="color: #1100ffff; font-weight: 600;"><?php echo htmlspecialchars($role); ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <!-- Common Dashboard Overview -->
            <li class="<?php echo ($view == 'overview') ? 'active' : ''; ?>">
                <a href="dashboard.php?view=overview">
                    <i class="fas fa-chart-line"></i> <span>Overview Dashboard</span>
                </a>
            </li>

            <?php if ($role === 'Admin'): ?>
                <!-- ADMIN EXCLUSIVE MODULES -->
                <li class="nav-label">Administration</li>
                <li class="<?php echo ($view == 'manage_staff') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=manage_staff">
                        <i class="fas fa-user-md"></i> <span>Manage Staff</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'patient_records') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=patient_records">
                        <i class="fas fa-users"></i> <span>All Patients List</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'reports') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=reports">
                        <i class="fas fa-file-invoice-dollar"></i> <span>HMIS & Clinic Reports</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'backup') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=backup">
                        <i class="fas fa-database"></i> <span>Backup & Security</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'Doctor' || $role === 'Nurse' || $role === 'Admin'): ?>
                <!-- CLINICAL STAFF MODULES -->
                <li class="nav-label">Clinical Care</li>
                <li class="<?php echo ($view == 'register_patient') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=register_patient">
                        <i class="fas fa-user-plus"></i> <span>Register Patient</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'patient_records') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=patient_records">
                        <i class="fas fa-notes-medical"></i> <span>Patient Records & Vitals</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'request_test') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=request_test">
                        <i class="fas fa-file-medical"></i> <span>Order Lab Test</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'test_results' || $view == 'view_results') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=test_results">
                        <i class="fas fa-clipboard-check"></i> <span>Verified Lab Results</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'Lab Technician' || $role === 'Admin'): ?>
                <!-- LAB TECHNICIAN MODULES -->
                <li class="nav-label">Laboratory Diagnostic</li>
                <li class="<?php echo ($view == 'pending_tests') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=pending_tests">
                        <i class="fas fa-microscope"></i> <span>Pending Tests Queue</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'test_results') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=test_results">
                        <i class="fas fa-vials"></i> <span>Completed Lab Reports</span>
                    </a>
                </li>
                <li class="<?php echo ($view == 'inventory') ? 'active' : ''; ?>">
                    <a href="dashboard.php?view=inventory">
                        <i class="fas fa-boxes"></i> <span>Reagent Inventory</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-label">General</li>
            <li class="<?php echo ($view == 'about-us') ? 'active' : ''; ?>">
                <a href="dashboard.php?view=about-us">
                    <i class="fas fa-info-circle"></i> <span>About Project</span>
                </a>
            </li>
            <li class="<?php echo ($view == 'profile') ? 'active' : ''; ?>">
                <a href="dashboard.php?view=profile">
                    <i class="fas fa-user-circle"></i> <span>My Profile</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span class="log-out-link">Sign Out</span></a>
    </div>
</aside>