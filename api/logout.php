<?php
session_start();
session_destroy();
header("Location: ../../SignUps/ALogin.php");
exit();
?>
