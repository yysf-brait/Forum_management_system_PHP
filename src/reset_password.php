<?php
session_start();
include 'config.php';  // 引入数据库配置
global $conn;  // 使用全局变量

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

    // 检查密码强度
    $minPasswordLength = 8;
    if (strlen($new_password) < $minPasswordLength || !preg_match("#[0-9]+#", $new_password) || !preg_match("#[a-zA-Z]+#", $new_password)) {
        header("Location: ../public/user/reset_password.php?error=password_not_strong");
        exit;
    }

    // 检查用户名是否存在
    if ($stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // 如果用户名不存在
            header("Location: ../public/user/reset_password.php?error=username_not_found");
            exit;
        } else {
            // 获取用户ID
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // 用户名存在，尝试更新密码
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if ($update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?")) {
                $update_stmt->bind_param("ss", $hashed_password, $user_id);
                if ($update_stmt->execute()) {
                    // 日志
                    date_default_timezone_set('Asia/Shanghai');
                    $time_stamp = date("Y-m-d H:i:s");
                    $log_message = "User:$user_id Action:ResetPassword AT:$time_stamp\n";
                    file_put_contents("../logs/user.txt", $log_message, FILE_APPEND);
                    // 密码重置成功，跳转到登录页面
                    echo "<!DOCTYPE html>
<html lang='zh'>
<head>
    <meta charset='UTF-8'>
    <title>密码重置成功</title>
    <meta http-equiv='refresh' content='3;url=../public/user/login.php'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding-top: 50px; }
        .message { color: #4CAF50; font-size: 20px; }
    </style>
</head>
<body>
    <p class='message'>您的密码已成功重置。您将在3秒后跳转到登录页面...</p>
</body>
</html>";
                    exit;
                } else {
                    // 重置密码失败，可能是SQL执行错误
                    header("Location: ../public/user/reset_password.php?error=reset_failed");
                }
                $update_stmt->close();
            } else {
                // SQL语句准备失败
                header("Location: ../public/user/reset_password.php?error=sql_error");
            }
        }
        $stmt->close();
    } else {
        // SQL语句准备失败
        header("Location: ../public/user/reset_password.php?error=sql_error");
    }
    $conn->close();
}
?>
