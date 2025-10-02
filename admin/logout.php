<?php
// logout.php
session_start();
session_unset(); // remove all session variables
session_destroy(); // destroy the session

// Redirect back to login
header('Location: login.php');
exit;
