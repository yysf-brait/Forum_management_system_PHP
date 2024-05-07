<?php
session_start();
include '../../src/config.php';  // 引入数据库配置
global $conn;  // 使用全局变量

if (!isset($_SESSION['username'])) {
    // 如果没有登录，重定向到登录页面
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$current_username = '';
$current_email = '';

// 准备SQL语句查询当前用户信息
$query = "SELECT username, email FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
if (!$stmt->fetch()) {
    echo "<p>没有找到用户信息。</p>"; // 适当处理用户不存在的情况
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>编辑用户资料</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .error {
            color: red;
            font-size: 0.9em;
            text-align: center;
            margin: 10px 0;
        }
        label {
            margin-bottom: 10px;
            display: block;
            color: #666;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #5c67f2;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #4a54e1;
        }
    </style>
</head>
<body>
    <form action="../../src/update_profile.php" method="POST">
        <h2>编辑用户资料</h2>
        <?php
        if (isset($_GET['error'])) {
            $errorMessages = [
                'username_too_short' => '用户名长度至少需要5个字符。',
                'username_taken' => '该用户名已被其他用户使用，请选择其他用户名。',
                'invalid_email' => '邮箱格式不正确。'
            ];
            if (array_key_exists($_GET['error'], $errorMessages)) {
                echo '<p class="error">' . $errorMessages[$_GET['error']] . '</p>';
            }
        }
        ?>
        <label>
            用户名（如需修改）:
            <input type="text" name="new_username" value="<?php echo htmlspecialchars($current_username); ?>" required>
        </label>
        <label>
            邮箱（如需修改）:
            <input type="email" name="new_email" value="<?php echo htmlspecialchars($current_email); ?>" required>
        </label>
        <button type="submit">更新资料</button>
    </form>
</body>
</html>
