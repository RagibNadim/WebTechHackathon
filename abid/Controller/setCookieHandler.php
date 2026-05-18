<?php
// Simple script to save user preferences (like a theme or last viewed project)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cookie_name = $_POST['cookie_name'] ?? 'user_pref';
    $cookie_value = $_POST['cookie_value'] ?? '';
    // Set cookie for 30 days
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); 
    echo json_encode(["success" => true, "message" => "Cookie set successfully"]);
}
?>