<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Remove the "Remember Me" cookie
if (isset($_COOKIE["remember_token"])) {
    setcookie("remember_token", "", time() - 3600, "/", "", false, true); // expire cookie

    // Optional: clear it in the database too
    $db = new mysqli("localhost", "root", "", "mk_watches");
    if (!$db->connect_error) {
        $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
        $stmt->bind_param("s", $_COOKIE["remember_token"]);
        $stmt->execute();
        $stmt->close();
    }
}

// Redirect to homepage with a message (optional)
header("Location: index.php?success=" . urlencode("You have been logged out successfully."));
exit;
?>
