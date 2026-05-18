<?php
session_start();
session_unset();
session_destroy();
// Redirect to login page (assuming Student 1 handles this route)
header("Location: ../../index.php"); 
exit;
?>