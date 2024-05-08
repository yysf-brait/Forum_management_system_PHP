<?php
session_start();  
include 'config.php';  global $conn;  
function GetOs()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $OS = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $OS)) {
            $OS = 'Windows';
        } elseif (preg_match('/mac/i', $OS)) {
            $OS = 'MAC';
        } elseif (preg_match('/linux/i', $OS)) {
            $OS = 'Linux';
        } elseif (preg_match('/unix/i', $OS)) {
            $OS = 'Unix';
        } elseif (preg_match('/bsd/i', $OS)) {
            $OS = 'BSD';
        } else {
            $OS = 'Other';
        }
        return $OS;
    } else {
        return "failed";
    }
}

function GetBrowser()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $br = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $br)) {
            $br = 'MSIE';
        } elseif (preg_match('/Firefox/i', $br)) {
            $br = 'Firefox';
        } elseif (preg_match('/Chrome/i', $br)) {
            $br = 'Chrome';
        } elseif (preg_match('/Safari/i', $br)) {
            $br = 'Safari';
        } elseif (preg_match('/Opera/i', $br)) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    } else {
        return "failed";
    }
}


$username = $_POST['username'];
$password = $_POST['password'];
$user_captcha = $_POST['captcha'];

$_SESSION['loggedin'] = false;  $_SESSION['username'] = $username;  

$sql = "SELECT user_id, username, password, email, is_admin FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($id, $username, $hashed_password, $email, $is_admin);
$stmt->fetch();

if (isset($_SESSION['captcha']) && $user_captcha == $_SESSION['captcha']) {
    unset($_SESSION['captcha']);
        if (password_verify($password, $hashed_password)) {
        $_SESSION = array();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['is_admin'] = $is_admin;  
        setcookie('username', $username, time() + 3600, '/');  
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $os = GetOs();
        $brower = GetBrowser();

        $host = gethostbyaddr($ip_address) ?? $ip_address;
                date_default_timezone_set('Asia/Shanghai');
        $time_stamp = date("Y-m-d H:i:s");
        $log_message = "User:$id Action:Login IP:$ip_address Host:$host OS:$os Browser:$brower AT:$time_stamp\n";
        file_put_contents("../logs/user.txt", $log_message, FILE_APPEND);

                if ($is_admin) {
            header("Location: ../public/index.php");
        } else {
            header("Location: ../public/index.php");
        }
    } else {
        header("Location: ../public/user/login.php?error=invalid");
    }

} else {
    header("Location: ../public/user/login.php?error=captcha");
}


$stmt->close();
$conn->close();
