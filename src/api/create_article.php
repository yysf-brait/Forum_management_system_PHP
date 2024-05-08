<?php
session_start();
header('Content-Type: application/json');  
include '../config.php';  global $conn;

$title = $_GET['title'] ?? '';
$content = $_GET['content'] ?? '';

if (empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'title and content are required']);
    exit;
}

$author_id = $_SESSION['user_id'] ?? null;
if (!$author_id) {
    echo json_encode(['success' => false, 'message' => 'login required']);
    exit;
}

$stmt = $conn->prepare("CALL CreateArticle(?, ?, ?, @new_article_id)");
$stmt->bind_param("ssi", $title, $content, $author_id);
$success = $stmt->execute();

if ($success) {
    $stmt->close();
    $stmt = $conn->prepare("SELECT @new_article_id AS new_article_id");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'message' => 'article created successfully', 'new_article_id' => $row['new_article_id']]);

        date_default_timezone_set('Asia/Shanghai');
    $time_stamp = date("Y-m-d H:i:s");
    $log_message = "User:$author_id Article:{$row['new_article_id']} AT:$time_stamp Action:Create\n";
    file_put_contents("../../logs/article_logs.txt", $log_message, FILE_APPEND);
} else {
    echo json_encode(['success' => false, 'message' => "article creation failed: {$stmt->error}"]);
}

$conn->close();
?>
