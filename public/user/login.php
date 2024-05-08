<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            width: 100%;
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
            margin-left: 10px;
        }

        .captcha-group img {
            flex-grow: 1;
            cursor: pointer;
            height: 38px;
            margin-left: 5px;
        }

        .captcha-hint {
            font-size: 0.7em;
            color: #666;
            text-align: center;
            margin-top: 0;
            margin-bottom: 2px;
            line-height: 0.6;
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
        <h2>User Login</h2>
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'invalid') {
                echo '<p class="error">Invalid username or password, please try again.</p>';
            } else if ($_GET['error'] == 'captcha') {
                echo '<p class="error">Incorrect captcha, please try again.</p>';
            }
        }
        ?>
    </div>
    <label>Username:<input type="text" name="username" value="<?php echo htmlspecialchars($_COOKIE['username'] ?? ''); ?>"
                         required></label>
    <label>Password:<input type="password" name="password" required></label>
    <div class="captcha-group">
        <label>Captcha:<input type="text" name="captcha" required style="width: 60%;"></label>
        <img src="../../src/captcha.php" alt="Click to refresh captcha" id="captcha_image" onclick="refreshCaptcha();"
             title="Click to refresh captcha"
             style="width: 30%; height: 38px; margin-left: 5px;">
    </div>
    <p class="captcha-hint">Click the image to refresh captcha</p>
    <button type="submit">Login</button>
    <button type="button" onclick="window.location='register.php';">Register a New Account</button>
    <a href="reset_password.php">Forgot Password?</a>
</form>

<script>
    function refreshCaptcha() {
        var captchaImage = document.getElementById('captcha_image');
        captchaImage.src = '../../src/captcha.php?' + Date.now();
    }
</script>
</body>
</html>
