<?php
session_start();
include 'config.php';
global $conn;

// 从会话中获取用户ID
$user_id = $_SESSION['user_id'];

// 从表单接收数据
$new_username = $_POST['new_username'];
$new_email = $_POST['new_email'];
$old_username = $_SESSION['username'];

// 检查用户名长度
$minUsernameLength = 5;
if (strlen($new_username) < $minUsernameLength) {
    header("Location: ../public/user/edit_profile.php?error=username_too_short");
    exit;
}
// 检查邮箱格式
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../public/user/edit_profile.php?error=invalid_email");
    exit;
}

// 验证新用户名是否已被占用（除当前用户外）
$query = "SELECT user_id FROM users WHERE username = ? AND username != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $new_username, $old_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // 如果用户名已存在，重定向回编辑页面并附带错误信息
    header("Location: ../public/user/edit_profile.php?error=username_taken");
    $stmt->close();
    $conn->close();
    exit;
}

// 更新用户名和邮箱
$sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $new_username, $new_email, $user_id);
$result = $stmt->execute();

// 检查执行结果
if ($result) {
    // 更新Session中的用户名
    $_SESSION['username'] = $new_username;
    // 更新Cookie中的用户名
    setcookie('username', $new_username, time() + 3600, '/');
    // 日志
    date_default_timezone_set('Asia/Shanghai');
    $time_stamp = date("Y-m-d H:i:s");
    $log_message = "User:$user_id Action:UpdateProfile AT:$time_stamp\n";
    file_put_contents("../logs/user.txt", $log_message, FILE_APPEND);
    echo "<script>alert('用户资料已成功更新！'); window.location.href='../public/user/profile.php';</script>";
} else {
    echo "<script>alert('错误：无法更新资料。'); window.history.back();</script>";
}

// 关闭语句和连接
$stmt->close();
$conn->close();
?>
