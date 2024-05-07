<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // 用户未登录，跳转到登录页面
    header("Location: login.php");
    exit;
}


include '../../src/config.php';  // 引入数据库配置
global $conn;  // 使用全局变量

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$username = $_SESSION['username'];  // 假设用户名已经保存在Session中

// 准备SQL语句
$sql = "SELECT username, email, is_admin, created_at FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($username, $email, $is_admin, $created_at);
$stmt->fetch();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>用户资料</title>
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
            text-align: center;
        }
        .profile {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h1, .welcome {
            color: #333;
        }
        .welcome {
            font-size: 18px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 16px;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #5c67f2;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="profile">
    <p class="welcome">欢迎回来，<?php echo htmlspecialchars($username); ?></p>
    <h1>用户资料</h1>
    <p>用户名: <?php echo htmlspecialchars($username); ?></p>
    <p>邮箱: <?php echo htmlspecialchars($email); ?></p>
    <p>注册时间: <?php echo $created_at; ?></p>
    <p>身份: <?php echo $is_admin ? '管理员' : '普通用户'; ?></p>
    <a href="edit_profile.php">编辑资料</a>
    <a href="../../src/logout.php">登出</a>
    <?php if ($is_admin): ?>
        <a href="../../logs/view.php">查看日志</a>
    <?php endif; ?>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
