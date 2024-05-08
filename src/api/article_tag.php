<?php
session_start();
include '../config.php';  // 引入数据库配置文件
global $conn;

// 检查用户是否登录
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['message' => '未登录，无法执行操作']);
    exit;
}

$userId = $_SESSION['user_id'];

// 检查是否为POST请求
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Request method must be POST']);
    exit;
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['message' => 'Access Denied: You are not an admin']);
    exit;
}

$article_id = $_POST['article_id'] ?? 0;
$add_tags = $_POST['add_tags'] ?? '';
$remove_tags = $_POST['remove_tags'] ?? '';

if ($article_id == 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'article_id is required']);
    exit;
}

// 调用存储过程更新文章标签
$stmt = $conn->prepare("CALL UpdateArticleTags(?, ?, ?)");
$stmt->bind_param("iss", $article_id, $add_tags, $remove_tags);
$result = $stmt->execute();

if ($result) {
    // 日志
    // 日志
    date_default_timezone_set('Asia/Shanghai');
    $time_stamp = date("Y-m-d H:i:s");
    $log_message = "User:$userId Article:$article_id AT:$time_stamp Action:UpdateTags\n";
    file_put_contents("../../logs/article_logs.txt", $log_message, FILE_APPEND);
    echo json_encode(['message' => 'tags updated successfully']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'tags updated failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
