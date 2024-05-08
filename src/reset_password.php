<?php
session_start();
include 'config.php';  global $conn;  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

        $minPasswordLength = 8;
    if (strlen($new_password) < $minPasswordLength || !preg_match("#[0-9]+#", $new_password) || !preg_match("#[a-zA-Z]+#", $new_password)) {
        header("Location: ../public/user/reset_password.php?error=password_not_strong");
        exit;
    }

        if ($stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
                        header("Location: ../public/user/reset_password.php?error=username_not_found");
            exit;
        } else {
                        $stmt->bind_result($user_id);
            $stmt->fetch();
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if ($update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?")) {
                $update_stmt->bind_param("ss", $hashed_password, $user_id);
                if ($update_stmt->execute()) {
                                        date_default_timezone_set('Asia/Shanghai');
                    $time_stamp = date("Y-m-d H:i:s");
                    $log_message = "User:$user_id Action:ResetPassword AT:$time_stamp\n";
                    file_put_contents("../logs/user.txt", $log_message, FILE_APPEND);
                                        echo "<!DOCTYPE html>
<html lang='zh'>
<head>
    <meta charset='UTF-8'>
    <title>Success</title>
    <meta http-equiv='refresh' content='3;url=../public/user/login.php'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding-top: 50px; }
        .message { color: #4CAF50; font-size: 20px; }
    </style>
</head>
<body>
    <p class='message'>Your password has been reset successfully. Redirecting to login page...</p>
</body>
</html>";
                    exit;
                } else {
                                        header("Location: ../public/user/reset_password.php?error=reset_failed");
                }
                $update_stmt->close();
            } else {
                                header("Location: ../public/user/reset_password.php?error=sql_error");
            }
        }
        $stmt->close();
    } else {
                header("Location: ../public/user/reset_password.php?error=sql_error");
    }
    $conn->close();
}
?>
