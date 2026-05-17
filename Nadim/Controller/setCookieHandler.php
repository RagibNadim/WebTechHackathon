<?php

$theme = $_POST["theme"] ?? "Default";

setcookie("theme", $theme, time() + 3600, "/");

header("Location: ../View/projectList.php");

?>