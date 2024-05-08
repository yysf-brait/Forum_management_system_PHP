<?php
session_start();
include '../config.php';  global $conn;

$response = ['success' => false];
$user = $_SESSION['user_id'] ?? null;  $article_id = $_GET['id'] ?? 0;  
if ($article_id == 0) {
    $response['message'] = 'article_id is required';
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM article_detail_view WHERE article_id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    $response['message'] = 'article not found';
    echo json_encode($response);
    exit;
}
$response['success'] = true;
$response['article'] = $article;
if (!isset($_SESSION['last_read_article_id']) || ($_SESSION['last_read_article_id'] != $article_id) || (time() - $_SESSION['last_read_article_time'] > 60)) {
    date_default_timezone_set('Asia/Shanghai');
    $time_stamp = date("Y-m-d H:i:s");
    $log_message = "User:$user Article:$article_id AT:$time_stamp Action:Read\n";
    file_put_contents("../../logs/article_logs.txt", $log_message, FILE_APPEND);
}


$_SESSION['last_read_article_id'] = $article_id;  $_SESSION['last_read_article_time'] = time();  echo json_encode($response);

$stmt->close();
$conn->close();
?>
