<?php
session_start();
include 'config.php';
global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $minUsernameLength = 5;
    if (strlen($username) < $minUsernameLength) {
        header("Location: ../public/user/register.php?error=username_too_short");
        exit;
    }

    $minPasswordLength = 8;
    if (strlen($password) < $minPasswordLength || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password)) {
        header("Location: ../public/user/register.php?error=password_not_strong");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../public/user/register.php?error=invalid_email");
        exit;
    }

    $stmt = $conn->prepare("SELECT users.user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: ../public/user/register.php?error=username_exists");
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            date_default_timezone_set('Asia/Shanghai');
            $time_stamp = date("Y-m-d H:i:s");
            $log_message = "User:$new_user_id Action:Register AT:$time_stamp\n";
            file_put_contents("../logs/user.txt", $log_message, FILE_APPEND);
            echo "<!DOCTYPE html>
            <html lang='zh'>
            <head>
                <meta charset='UTF-8'>
                <title>SUCCESS</title>
                <meta http-equiv='refresh' content='3;url=../public/user/login.php'>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding-top: 50px; }
                    .message { color: #4CAF50; font-size: 20px; }
                </style>
            </head>
            <body>
                <p class='message'>Welcome, {$username}! Your account has been created successfully. Redirecting to login page...</p>
            </body>
            </html>";
            exit;
        } else {
            header("Location: ../public/user/register.php?error=unknown_error");
        }
    }
    $stmt->close();
}
$conn->close();
?>

