<?php
session_start();  // 启动会话

// 清除所有会话变量
$_SESSION = array();

setcookie("username", "", time()-3600, "/");  // 删除Cookie

// 最后，销毁会话
session_destroy();

// 重定向到登录页面或首页
header("Location: ../public/user/login.php");
exit;
?>
