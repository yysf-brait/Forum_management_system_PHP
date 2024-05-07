<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>重置密码</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px; /* 固定表单宽度 */
            transition: box-shadow 0.3s;
        }
        form:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px; /* 增加与表单元素的间距 */
        }
        .error {
            color: #D8000C;
            background-color: #FFD2D2;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            font-size: 0.9em;
        }
        label {
            margin-bottom: 12px;
            display: block;
            color: #666;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%; /* 宽度调整为100%填充父容器 */
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            border-color: #5c67f2;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #5c67f2;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background-color: #4a54e1;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #333;
        }
    </style>
</head>
<body>
<form action="../../src/reset_password.php" method="POST">
    <h2>重置密码</h2>
    <?php
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'password_not_strong':
                echo '<p class="error">密码长度至少8个字符<br>且必须包含字母和数字</p>';
                break;
            case 'username_not_found':
                echo '<p class="error">用户名不存在，请重试。</p>';
                break;
            case 'reset_failed':
                echo '<p class="error">密码重置失败，请重试。</p>';
                break;
            case 'sql_error':
                echo '<p class="error">数据库错误，请联系管理员。</p>';
                break;
        }
    }
    ?>
    <label>
        用户名:
        <input type="text" name="username" required>
    </label>
    <label>
        新密码:
        <input type="password" name="new_password" required>
    </label>
    <button type="submit">重置密码</button>
</form>
</body>
</html>
