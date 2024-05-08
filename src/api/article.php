<?php
session_start();
include '../config.php';  // 引入数据库配置文件
global $conn;

$response = ['success' => false];
$user = $_SESSION['user_id'] ?? null;  // 获取当前登录用户ID
$article_id = $_GET['id'] ?? 0;  // 从URL参数获取文章ID

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
// 日志
if (!isset($_SESSION['last_read_article_id']) || ($_SESSION['last_read_article_id'] != $article_id) || (time() - $_SESSION['last_read_article_time'] > 60)) {
    date_default_timezone_set('Asia/Shanghai');
    $time_stamp = date("Y-m-d H:i:s");
    $log_message = "User:$user Article:$article_id AT:$time_stamp Action:Read\n";
    file_put_contents("../../logs/article_logs.txt", $log_message, FILE_APPEND);
}


$_SESSION['last_read_article_id'] = $article_id;  // 记录最后阅读的文章ID
$_SESSION['last_read_article_time'] = time();  // 记录最后阅读的时间戳
echo json_encode($response);

$stmt->close();
$conn->close();
?>
