<?php
// Start the session to gain access to it
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the actual session
session_destroy();

// Redirect the user back to the login page (index.php)
header("Location: index.php");
exit();
?>