<?php
session_start();
session_unset();
session_destroy();

session_start();
$_SESSION['flash_message'] = "Logout berhasil. Sampai jumpa lagi!";
header('Location: index.php');
exit;
