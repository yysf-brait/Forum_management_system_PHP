<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit;
}

include '../../src/config.php';  // Include database configuration
global $conn;  // Use global variable

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];  // Assume username is already saved in the session

// Prepare SQL statement
$sql = "SELECT username, email, is_admin, created_at FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($username, $email, $is_admin, $created_at);
$stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
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
    <p class="welcome">Welcome back, <?php echo htmlspecialchars($username); ?></p>
    <h1>User Profile</h1>
    <p>Username: <?php echo htmlspecialchars($username); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Registration Time: <?php echo $created_at; ?></p>
    <p>Role: <?php echo $is_admin ? 'Administrator' : 'Regular User'; ?></p>
    <a href="edit_profile.php">Edit Profile</a>
    <a href="../../src/logout.php">Log Out</a>
    <?php if ($is_admin): ?>
        <a href="../../logs/view.php">View Logs</a>
    <?php endif; ?>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
