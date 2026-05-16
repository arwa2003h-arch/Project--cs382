<?php

session_start();

// Remove all session data
$_SESSION = [];

// Destroy session
session_destroy();

// Return user to login page
header("Location: login.html");

exit();

?>