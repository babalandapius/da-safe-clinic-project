
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Da Safe Community Clinic - Patient Records System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #22a81bff;
            --primary-dark: #0a7841ff;
            --accent: #00a873ff;
            --bg-light: #f0f4f8;
            --card-bg: #ffffff;
            --text-main: #2d4842ff;
            --text-muted: #718096;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--bg-light);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .login-image {
            flex: 1.1;
            background: linear-gradient(135deg, #096120ff 0%, #168c45ff 50%, #1ea855ff 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 50px;
            color: white;
            text-align: center;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 10%, transparent 20%);
            background-size: 30px 30px;
            animation: moveGrid 30s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .image-overlay {
            position: relative;
            z-index: 3;
            max-width: 520px;
        }

        .image-overlay .clinic-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .image-overlay h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .image-overlay h2 {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 20px;
            opacity: 0.9;
            color: #d1ecf1;
        }

        .image-overlay p {
            font-size: 0.98rem;
            line-height: 1.6;
            opacity: 0.85;
            margin-bottom: 25px;
        }

        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .feature-pill {
            background: rgba(255, 255, 255, 0.12);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .login-form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f8fafc;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 440px;
            border: 1px solid #e2e8f0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #00a896 0%, #00a896 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin: 0 auto 15px;
            box-shadow: 0 8px 20px rgba(27, 108, 168, 0.3);
        }

        .login-header h2 {
            font-size: 1.5rem;
            color: #1a202c;
            font-weight: 700;
        }

        .login-header p {
            font-size: 0.88rem;
            color: #718096;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #4a5568;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #cbd5e0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            background: #ffffff;
            color: #2d3748;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #1ba835ff;
            box-shadow: 0 0 0 3px rgba(27, 168, 114, 0.15);
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background-color: #2ea244ff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(27, 168, 83, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(14, 105, 11, 1);
        }

        .demo-credentials {
            background: #edf2f7;
            border-radius: 10px;
            padding: 12px;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #4a5568;
        }

        .demo-credentials strong {
            display: block;
            margin-bottom: 6px;
            color: #2d3748;
        }

        .demo-roles {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .demo-chip {
            background: #ffffff;
            border: 1px solid #cbd5e0;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 500;
        }

        .demo-chip:hover {
            background: #e2e8f0;
            border-color: #a0aec0;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #718096;
        }

        .login-footer a {
            color: #1b6ca8;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fff5f5;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .alert-success {
            background: #f0fff4;
            color: #276749;
            border: 1px solid #9ae6b4;
        }

        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
            }
            .login-image {
                display: none;
            }
            .login-form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Banner: Clinic Project Overview -->
        <div class="login-image">
            <div class="image-overlay">
                <span class="clinic-badge"><i class="fas fa-map-marker-alt"></i> Nagongera, Tororo District</span>
                <h1>Da Safe Community Clinic</h1>
                <h2>Welcome please at Da safe hospital where you will find waulity treatment</h2>
                <p>A centralized electronic health records platform digitizing patient registration, vital signs, clinical encounters, and diagnostic laboratory workflows.</p>
                
                <div class="feature-pills">
                    <div class="feature-pill"><i class="fas fa-user-check"></i> Role-Based Access</div>
                    <div class="feature-pill"><i class="fas fa-bolt"></i> Real-time Lab Sync</div>
                    <div class="feature-pill"><i class="fas fa-notes-medical"></i> Clinical Vitals</div>
                    <div class="feature-pill"><i class="fas fa-file-medical-alt"></i> HMIS Reports</div>
                </div>
                
                <div style="margin-top: 35px; font-size: 0.82rem; opacity: 0.8; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 15px;">
                    Busitema University • Faculty of Science & Education<br>
                    Project by <strong>Mwabini Peter Moses</strong> (BU/UP/2023/1755)
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="login-form-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h2>Da Safe Clinic</h2>
                    <p>Sign in to access patient records</p>
                </div>

                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i> Invalid credentials or incorrect password!
                </div>
                <?php endif; ?>

                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registration_success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Account registered successfully! Please sign in.
                </div>
                <?php endif; ?>

                <form action="process_lab.php?action=login" method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Select Role</label>
                        <select name="role" id="roleSelect" required>
                            <option value="Admin">Administrator</option>
                            <option value="Doctor" selected>Doctor / Clinician</option>
                            <option value="Nurse">Nurse (Triage)</option>
                            <option value="Lab Technician">Laboratory Technician</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" id="emailInput" required placeholder="doctor@dasafe.com" value="doctor@dasafe.com">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" id="passwordInput" required placeholder="Enter password" value="doctor123">
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Sign In to Portal
                    </button>
                </form>

                <!-- Quick login shortcuts for demonstration -->
                <div class="demo-credentials">
                    <strong><i class="fas fa-key"></i> Quick Demo Logins:</strong>
                    <div class="demo-roles">
                        <span class="demo-chip" onclick="setDemo('Admin', 'admin@dasafe.com', 'admin123')">Admin</span>
                        <span class="demo-chip" onclick="setDemo('Doctor', 'doctor@dasafe.com', 'doctor123')">Doctor</span>
                        <span class="demo-chip" onclick="setDemo('Nurse', 'nurse@dasafe.com', 'nurse123')">Nurse</span>
                        <span class="demo-chip" onclick="setDemo('Lab Technician', 'lab@dasafe.com', 'lab123')">Lab Tech</span>
                    </div>
                </div>

                <div class="login-footer">
                    <p>New staff member? <a href="register.php"><i class="fas fa-user-plus"></i> Register Here</a></p>
                    <p style="font-size: 11px; margin-top: 10px; color: #a0aec0;">© 2026 Da Safe Community Clinic, Nagongera</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDemo(role, email, pass) {
            document.getElementById('roleSelect').value = role;
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = pass;
        }
    </script>
</body>
</html>