<?php
session_start();
$role = $_SESSION["role"] ?? null;
session_unset();
session_destroy();

if ($role === "admin") {
    header("Location: SignUps/Alogin.php");
} elseif ($role === "company") {
    header("Location: SignUps/Clogin.php");
} elseif ($role === "student") {
    header("Location: SignUps/Slogin.php");
} else {
    header("Location: index.html");
}
exit();
?>
