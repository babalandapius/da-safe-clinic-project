<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }

$userName = $_SESSION['user_name'] ?? 'Medical Staff';
$userEmail = $_SESSION['user_email'] ?? 'staff@dasafe.com';
$userRole = $_SESSION['user_role'] ?? 'Doctor';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare('SELECT email, role, specialization FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $data = $result->fetch_assoc();
        $userEmail = $data['email'];
        $userRole = $data['role'] ?: $userRole;
        $userSpec = $data['specialization'] ?? '';
    }
}
?>

<div class="workspace-container">
    <div class="workspace-header" style="margin-bottom: 22px;">
        <div>
            <h2 style="margin: 0; color: #1a202c; font-size: 1.5rem;">
                <i class="fas fa-user-circle" style="color: #1b6ca8;"></i> User Account & Profile
            </h2>
            <p style="color: #718096; margin: 4px 0 0; font-size: 0.9rem;">
                Manage your credentials and duty role at Da Safe Community Clinic.
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 25px;">
        <div class="card" style="background: white; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; color: #2d3748; font-size: 1.15rem; margin-bottom: 20px;">
                <i class="fas fa-id-card-alt" style="color: #1b6ca8;"></i> Account Information
            </h3>

            <div style="display: grid; gap: 16px;">
                <div>
                    <label style="display:block; margin-bottom:4px; color:#718096; font-size: 0.82rem; text-transform: uppercase; font-weight: 600;">Staff Name</label>
                    <p style="margin:0; font-size: 1.05rem; font-weight: 700; color:#1a202c;"><?php echo htmlspecialchars($userName); ?></p>
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px; color:#718096; font-size: 0.82rem; text-transform: uppercase; font-weight: 600;">Work Email</label>
                    <p style="margin:0; font-size: 0.95rem; color:#2d3748;"><?php echo htmlspecialchars($userEmail); ?></p>
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px; color:#718096; font-size: 0.82rem; text-transform: uppercase; font-weight: 600;">Authorized Role</label>
                    <p style="margin:0; font-size: 0.95rem; font-weight: 600; color:#1b6ca8;"><?php echo htmlspecialchars($userRole); ?></p>
                </div>
                <div>
                    <label style="display:block; margin-bottom:4px; color:#718096; font-size: 0.82rem; text-transform: uppercase; font-weight: 600;">Facility Station</label>
                    <p style="margin:0; font-size: 0.95rem; color:#4a5568;">Da Safe Community Clinic, Nagongera Town Council, Tororo</p>
                </div>
            </div>
        </div>

        <div class="card" style="background: white; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; color: #2d3748; font-size: 1.15rem; margin-bottom: 20px;">
                <i class="fas fa-lock" style="color: #00a896;"></i> Session & Security
            </h3>
            <p style="margin: 0 0 18px 0; color: #718096; font-size: 0.9rem; line-height: 1.6;">
                Your session is authenticated via secure session cookies with automatic 30-minute inactivity logouts for clinic data protection.
            </p>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="dashboard.php?view=overview" class="btn-primary" style="padding: 10px 18px; text-decoration: none; border-radius: 8px; font-size: 0.88rem;">
                    <i class="fas fa-th-large"></i> Go to Dashboard
                </a>
                <a href="logout.php" style="background: #e53e3e; color: white; padding: 10px 18px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.88rem;">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>
    </div>
</div>