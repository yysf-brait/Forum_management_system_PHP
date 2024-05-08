<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
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
            width: 300px; /* Fixed form width */
        }

        .form-header, .error {
            text-align: center;
            color: #333;
            margin-bottom: 20px; /* Increase spacing from form elements */
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
            width: 100%; /* Width adjusted to 100% to fill parent container */
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensures padding does not affect input size */
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
        <h2>User Registration</h2>
        <?php
        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case 'username_too_short':
                    echo '<p class="error">Username must be at least 5 characters long.</p>';
                    break;
                case 'password_not_strong':
                    echo '<p class="error">Password must be at least 8 characters long<br>and must include letters and numbers.</p>';
                    break;
                case 'invalid_email':
                    echo '<p class="error">Invalid email format.</p>';
                    break;
                case 'username_exists':
                    echo '<p class="error">Username already exists.</p>';
                    break;
                case 'unknown_error':
                    echo '<p class="error">Unknown error.</p>';
                    break;

            }
        }
        ?>    </div>
    <label>
        Username:
        <input type="text" name="username" required>
    </label>
    <label>
        Password:
        <input type="password" name="password" required>
    </label>
    <label>
        Email:
        <input type="email" name="email" required>
    </label>
    <button type="submit">Register</button>
</form>
</body>
</html>
