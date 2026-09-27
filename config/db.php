<?php
if (isset($GLOBALS['conn']) && ($GLOBALS['conn'] instanceof mysqli) && @$GLOBALS['conn']->ping()) {
    $conn = $GLOBALS['conn'];
    return;
}

$host = "localhost";
$user = "root";
$pass = "pius1234.";
$dbname = "st_marys_hmS";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
$GLOBALS['conn'] = $conn;

// Helper function to safely add a column if it doesn't exist (compatible with all MySQL / MariaDB versions)
if (!function_exists('ensureColumnExists')) {
    function ensureColumnExists($conn, $table, $column, $definition) {
        $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check && $check->num_rows == 0) {
            $conn->query("ALTER TABLE `$table` ADD `$column` $definition");
        }
    }
}

// 1. Users Table (Role-based access)
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Doctor',
    specialization VARCHAR(255) DEFAULT 'General Medicine',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

ensureColumnExists($conn, 'users', 'role', "VARCHAR(50) DEFAULT 'Doctor'");
ensureColumnExists($conn, 'users', 'specialization', "VARCHAR(255) DEFAULT 'General Medicine'");

// Seed default users if users table has no accounts or default accounts
$adminExists = $conn->query("SELECT id FROM users WHERE email = 'admin@dasafe.com' LIMIT 1");
if ($adminExists && $adminExists->num_rows == 0) {
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $doctorPass = password_hash('doctor123', PASSWORD_DEFAULT);
    $nursePass = password_hash('nurse123', PASSWORD_DEFAULT);
    $labPass = password_hash('lab123', PASSWORD_DEFAULT);
    
    $conn->query("INSERT INTO users (name, email, password, role, specialization) VALUES 
        ('Clinic Administrator', 'admin@dasafe.com', '$adminPass', 'Admin', 'System Administration'),
        ('Dr. Okello Andrew', 'doctor@dasafe.com', '$doctorPass', 'Doctor', 'General Clinician'),
        ('Nurse Sarah Aketch', 'nurse@dasafe.com', '$nursePass', 'Nurse', 'Triage & Inpatient Care'),
        ('David Ochieng (Lab Tech)', 'lab@dasafe.com', '$labPass', 'Lab Technician', 'Diagnostic Pathology')");
}

// 2. Patients Table
$conn->query("CREATE TABLE IF NOT EXISTS patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_code VARCHAR(50) DEFAULT NULL,
    full_name VARCHAR(255) NOT NULL,
    residence VARCHAR(255),
    gender VARCHAR(50),
    age INT,
    contact VARCHAR(100),
    next_of_kin VARCHAR(255) DEFAULT NULL,
    next_of_kin_contact VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

ensureColumnExists($conn, 'patients', 'patient_code', "VARCHAR(50) DEFAULT NULL");
ensureColumnExists($conn, 'patients', 'next_of_kin', "VARCHAR(255) DEFAULT NULL");
ensureColumnExists($conn, 'patients', 'next_of_kin_contact', "VARCHAR(100) DEFAULT NULL");

// 3. Clinical Visits & Vitals Table
$conn->query("CREATE TABLE IF NOT EXISTS patient_vitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT DEFAULT 1,
    blood_pressure VARCHAR(50),
    temperature VARCHAR(50),
    weight VARCHAR(50),
    height VARCHAR(50),
    symptoms TEXT,
    diagnosis TEXT,
    prescription TEXT,
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 4. Lab Requests Table
$conn->query("CREATE TABLE IF NOT EXISTS lab_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL DEFAULT 0,
    test_id INT NOT NULL DEFAULT 0,
    doctor_id INT DEFAULT 1,
    status VARCHAR(50) DEFAULT 'Requested',
    priority VARCHAR(20) DEFAULT 'normal',
    observations TEXT DEFAULT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

ensureColumnExists($conn, 'lab_requests', 'priority', "VARCHAR(20) DEFAULT 'normal'");
ensureColumnExists($conn, 'lab_requests', 'observations', "TEXT DEFAULT NULL");

// 5. Test Results Table
$conn->query("CREATE TABLE IF NOT EXISTS test_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    patient_id INT NOT NULL DEFAULT 0,
    parameter_name VARCHAR(255) NOT NULL,
    result_value VARCHAR(255) NOT NULL,
    normal_range VARCHAR(255),
    technician_comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

ensureColumnExists($conn, 'test_results', 'patient_id', "INT NOT NULL DEFAULT 0");

// 6. Legacy Lab Results Table (for compatibility)
$conn->query("CREATE TABLE IF NOT EXISTS lab_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    finding_summary TEXT NOT NULL,
    result_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 7. Inventory Table
$conn->query("CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(255),
    quantity INT NOT NULL,
    expiry_date DATE,
    status VARCHAR(50)
)");
?>