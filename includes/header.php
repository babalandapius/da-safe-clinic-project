<?php
$currentUserRole = $_SESSION['user_role'] ?? 'Doctor';
$currentUserName = $_SESSION['user_name'] ?? 'Staff Member';

$roleBadgeClass = 'badge-doctor';
if ($currentUserRole === 'Admin') $roleBadgeClass = 'badge-admin';
elseif ($currentUserRole === 'Nurse') $roleBadgeClass = 'badge-nurse';
elseif ($currentUserRole === 'Lab Technician') $roleBadgeClass = 'badge-lab';
?>
<header class="top-bar">
    <div class="header-left">
        <button id="menu-toggle" class="menu-btn" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="clinic-title-header">
            <span style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem;">
                <i class="fas fa-clinic-medical" style="color: #1b6ca8;"></i> Da Safe Community Clinic
            </span>
            <small style="display: block; color: var(--text-secondary); font-size: 0.75rem;">Nagongera Town Council, Tororo District</small>
        </div>

        <form action="dashboard.php" method="GET" class="search-box" style="margin-left: 20px;">
            <input type="hidden" name="view" value="search_results">
            <i class="fas fa-search"></i>
            <input type="text" name="query" placeholder="Search patient ID (DSC/...), name, or phone..." required>
        </form>
    </div>

    <div class="header-right">
        <button id="theme-toggle" class="theme-toggle-btn" title="Toggle Dark/Light Theme">
            <i class="fas fa-moon"></i>
        </button>

        <div class="user-profile">
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($currentUserName); ?></span>
                <span class="user-role" style="font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="fas fa-id-badge"></i> <?php echo htmlspecialchars($currentUserRole); ?>
                </span>
            </div>
            <img src="assets/images/user-avatar.svg" alt="Profile" onerror="this.src='assets/images/user-avatar.svg'">
        </div>
    </div>
</header>
