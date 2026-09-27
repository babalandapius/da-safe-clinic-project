<?php
include('config/db.php');

$urgent_count_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status != 'Completed' AND priority = 'urgent'");
if ($urgent_count_res) {
    $urgent_count = $urgent_count_res->fetch_assoc()['total'];
    echo "Urgent count query successful: $urgent_count\n";
} else {
    echo "Urgent count query failed: " . $conn->error . "\n";
}

$emergency_count_res = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status != 'Completed' AND priority = 'emergency'");
if ($emergency_count_res) {
    $emergency_count = $emergency_count_res->fetch_assoc()['total'];
    echo "Emergency count query successful: $emergency_count\n";
} else {
    echo "Emergency count query failed: " . $conn->error . "\n";
}
?>