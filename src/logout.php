<?php
session_start();  
$_SESSION = array();

setcookie("username", "", time()-3600, "/");  
session_destroy();

header("Location: ../public/user/login.php");
exit;
?>
