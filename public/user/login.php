<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
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
            width: 300px;
        }

        .form-header, .error {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
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
        input[type="password"] {
            width: 100%; /* 宽度调整为100%填充父容器 */
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .captcha-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .captcha-group label {
            flex-grow: 1;
        }

        .captcha-group input {
            flex-grow: 2;
            margin-left: 10px; /* 调整此值以增加间距 */
        }

        .captcha-group img {
            flex-grow: 1;
            cursor: pointer;
            height: 38px; /* 调整图片大小以适应输入框高度 */
            margin-left: 5px;
        }

        .captcha-hint {
            font-size: 0.7em; /* 减小字体大小 */
            color: #666;
            text-align: center;
            margin-top: 0; /* 减少上边距 */
            margin-bottom: 2px; /* 减少下边距 */
            line-height: 0.6; /* 调整行高 */
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

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #333;
        }
    </style>
</head>
<body>
<form action="../../src/login.php" method="POST">
    <div class="form-header">
        <h2>用户登录</h2>
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'invalid') {
                echo '<p class="error">用户名或密码错误，请重试。</p>';
            } else if ($_GET['error'] == 'captcha') {
                echo '<p class="error">验证码错误，请重试。</p>';
            }
        }
        ?>
    </div>
    <label>用户名:<input type="text" name="username" value="<?php echo htmlspecialchars($_COOKIE['username'] ?? ''); ?>"
                         required></label>
    <label>密码:<input type="password" name="password" required></label>
    <div class="captcha-group">
        <label>验证码:<input type="text" name="captcha" required style="width: 60%;"></label>
        <img src="../../src/captcha.php" alt="点击刷新验证码" id="captcha_image" onclick="refreshCaptcha();"
             title="点击刷新验证码"
             style="width: 30%; height: 38px; margin-left: 5px;">
    </div>
    <p class="captcha-hint">点击图片刷新验证码</p>
    <button type="submit">登录</button>
    <button type="button" onclick="window.location='register.php';">注册新账户</button>
    <a href="reset_password.php">忘记密码？</a>
</form>

<script>
    function refreshCaptcha() {
        var captchaImage = document.getElementById('captcha_image');
        captchaImage.src = '../../src/captcha.php?' + Date.now();
    }
</script>
</body>
</html>
