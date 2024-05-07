<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>注册</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* 固定表单宽度 */
        }

        .form-header, .error {
            text-align: center;
            color: #333;
            margin-bottom: 20px; /* 增加与表单元素的间距 */
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        label {
            margin-bottom: 10px;
            display: block;
            color: #666;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%; /* 宽度调整为100%填充父容器 */
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* 确保padding不会影响输入框大小 */
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
<form action="../../src/register.php" method="POST">
    <div class="form-header">
        <h2>用户注册</h2>
        <?php
        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case 'username_too_short':
                    echo '<p class="error">用户名长度至少5个字符。</p>';
                    break;
                case 'password_not_strong':
                    echo '<p class="error">密码长度至少8个字符<br>且必须包含字母和数字</p>';
                    break;
                case 'invalid_email':
                    echo '<p class="error">邮箱格式无效。</p>';
                    break;
                case 'username_exists':
                    echo '<p class="error">用户名已存在。</p>';
                    break;
                case 'unknown_error':
                    echo '<p class="error">未知错误。</p>';
                    break;

            }
        }
        ?>    </div>
    <label>
        用户名:
        <input type="text" name="username" required>
    </label>
    <label>
        密码:
        <input type="password" name="password" required>
    </label>
    <label>
        邮箱:
        <input type="email" name="email" required>
    </label>
    <button type="submit">注册</button>
</form>
</body>
</html>
