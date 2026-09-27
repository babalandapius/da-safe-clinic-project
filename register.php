<?php
session_start();
// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration | Da Safe Community Clinic</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #093b61 0%, #165b8c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-card {
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 480px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .register-header i {
            font-size: 40px;
            color: #1b6ca8;
            margin-bottom: 10px;
        }

        .register-header h2 {
            font-size: 1.5rem;
            color: #1a202c;
            font-weight: 700;
        }

        .register-header p {
            font-size: 0.85rem;
            color: #718096;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #4a5568;
            font-weight: 600;
            font-size: 0.88rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 11px 13px;
            border: 1.5px solid #cbd5e0;
            border-radius: 8px;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: 0.2s;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #1b6ca8;
            box-shadow: 0 0 0 3px rgba(27, 108, 168, 0.15);
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1b6ca8 0%, #165b8c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(27, 108, 168, 0.3);
        }

        .alert {
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fff5f5;
            color: #c53030;
            border: 1px solid #feb2b2;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create Staff Account</h2>
            <p>Da Safe Community Clinic, Nagongera</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> 
            <?php 
            if($_GET['error'] == 'password_mismatch') echo "Passwords do not match!";
            elseif($_GET['error'] == 'email_exists') echo "Email already registered!";
            elseif($_GET['error'] == 'registration_failed') echo "Registration failed. Please try again.";
            ?>
        </div>
        <?php endif; ?>

        <form action="process_lab.php?action=register" method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="name" required placeholder="e.g., Dr. Okello Andrew">
            </div>

            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Work Email Address</label>
                <input type="email" name="email" required placeholder="e.g., doctor@dasafe.com">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user-shield"></i> User Role</label>
                    <select name="role" required>
                        <option value="Doctor">Doctor (Clinician)</option>
                        <option value="Nurse">Nurse (Triage)</option>
                        <option value="Lab Technician">Lab Technician</option>
                        <option value="Admin">Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-stethoscope"></i> Department / Spec</label>
                    <input type="text" name="specialization" placeholder="e.g. OPD, Triage">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required placeholder="Min 6 chars">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="Re-enter password">
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-check-circle"></i> Create Staff Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.88rem;">
            <a href="index.php" style="color: #1b6ca8; text-decoration: none; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back to Login Portal
            </a>
        </div>
    </div>
</body>
</html>