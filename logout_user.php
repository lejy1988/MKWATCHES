<?php
session_start();
session_unset();
session_destroy();

// redirect back to home page
header("Location: index.php");
exit;
?>
