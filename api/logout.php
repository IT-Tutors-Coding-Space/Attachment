<?php
session_start();
session_destroy();
header("Location: ../../SignUps/Alogin.php");
exit();
?>
