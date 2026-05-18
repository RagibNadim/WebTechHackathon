<?php

session_start();

session_destroy();

header("Location: ../../Noshin/View/login.php");

?>