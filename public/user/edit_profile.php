<?php
session_start();
include '../../src/config.php';  // Include database configuration
global $conn;  // Use global variable

if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$current_username = '';
$current_email = '';

// Prepare SQL statement to query current user information
$query = "SELECT username, email FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
if (!$stmt->fetch()) {
    echo "<p>User information not found.</p>"; // Appropriately handle the case where the user does not exist
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User Profile</title>
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
        <h2>Edit User Profile</h2>
        <?php
        if (isset($_GET['error'])) {
            $errorMessages = [
                'username_too_short' => 'Username must be at least 5 characters long.',
                'username_taken' => 'This username is already taken, please choose another one.',
                'invalid_email' => 'Invalid email format.'
            ];
            if (array_key_exists($_GET['error'], $errorMessages)) {
                echo '<p class="error">' . $errorMessages[$_GET['error']] . '</p>';
            }
        }
        ?>
        <label>
            Username (if changing):
            <input type="text" name="new_username" value="<?php echo htmlspecialchars($current_username); ?>" required>
        </label>
        <label>
            Email (if changing):
            <input type="email" name="new_email" value="<?php echo htmlspecialchars($current_email); ?>" required>
        </label>
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
