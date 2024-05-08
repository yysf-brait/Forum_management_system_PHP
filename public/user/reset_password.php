<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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
            width: 300px; /* Fixed form width */
            transition: box-shadow 0.3s;
        }
        form:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px; /* Increase spacing from form elements */
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
            width: 100%; /* Width adjusted to 100% to fill parent container */
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
    <h2>Reset Password</h2>
    <?php
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'password_not_strong':
                echo '<p class="error">Password must be at least 8 characters long<br>and must include letters and numbers.</p>';
                break;
            case 'username_not_found':
                echo '<p class="error">Username not found, please try again.</p>';
                break;
            case 'reset_failed':
                echo '<p class="error">Password reset failed, please try again.</p>';
                break;
            case 'sql_error':
                echo '<p class="error">Database error, please contact the administrator.</p>';
                break;
        }
    }
    ?>
    <label>
        Username:
        <input type="text" name="username" required>
    </label>
    <label>
        New Password:
        <input type="password" name="new_password" required>
    </label>
    <button type="submit">Reset Password</button>
</form>
</body>
</html>
