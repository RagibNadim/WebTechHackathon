<?php

$theme = $_POST["theme"] ?? "Default";

setcookie("theme", $theme, time() + 3600, "/");

Header("Location: ../View/dashboard.php");
exit();

?>