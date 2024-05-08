<?php
session_start();
include 'config.php';
global $conn;

$user_id = $_SESSION['user_id'];

$new_username = $_POST['new_username'];
$new_email = $_POST['new_email'];
$old_username = $_SESSION['username'];

$minUsernameLength = 5;
if (strlen($new_username) < $minUsernameLength) {
    header("Location: ../public/user/edit_profile.php?error=username_too_short");
    exit;
}
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../public/user/edit_profile.php?error=invalid_email");
    exit;
}

$query = "SELECT user_id FROM users WHERE username = ? AND username != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $new_username, $old_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
        header("Location: ../public/user/edit_profile.php?error=username_taken");
    $stmt->close();
    $conn->close();
    exit;
}

$sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $new_username, $new_email, $user_id);
$result = $stmt->execute();

if ($result) {
        $_SESSION['username'] = $new_username;
        setcookie('username', $new_username, time() + 3600, '/');
        date_default_timezone_set('Asia/Shanghai');
    $time_stamp = date("Y-m-d H:i:s");
    $log_message = "User:$user_id Action:UpdateProfile AT:$time_stamp\n";
    file_put_contents("../logs/user.txt", $log_message, FILE_APPEND);
    echo "<script>alert('profile updated successfully.'); window.location.href='../public/user/profile.php';</script>";
} else {
    echo "<script>alert('profile update failed.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
