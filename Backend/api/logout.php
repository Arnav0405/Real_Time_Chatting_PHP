<?php
session_start(); // Start the session

// Destroy the session to log the user out
session_unset();    // Unset all session variables
session_destroy();  // Destroy the session itself

// Redirect to the login page (or homepage)
// header("Location: login.php");   NEED TO SET THIS
exit(); // Ensure no further code is executed
?>
