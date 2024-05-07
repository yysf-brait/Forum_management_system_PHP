<?php
session_start();
include '../config.php';  // 引入数据库配置文件
global $conn;

$user = $_SESSION['user_id'] ?? null;  // 获取当前登录用户ID

// 检查用户是否登录以及是否有更新文章的权限
if (!$user) {
    http_response_code(401); // Unauthorized
    echo json_encode(['message' => '未登录，无法执行操作']);
    exit;
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['message' => '非管理员用户无权执行此操作']);
    exit;
}

$article_id = $_GET['article_id'] ?? 0;

if ($article_id == 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => '文章ID不能为空']);
    exit;
}

// 调用存储过程更新文章
$stmt = $conn->prepare("CALL DeleteArticle(?, @message)");
$stmt->bind_param("i", $article_id);
if ($stmt->execute()) {
    // 检索存储过程的输出消息
    $stmt->close();
    $stmt = $conn->prepare("SELECT @message AS message;");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row) {
        echo json_encode(['message' => $row['message']]);
        if (str_starts_with($row['message'], 'Success')) {
            // 日志
            date_default_timezone_set('Asia/Shanghai');
            $time_stamp = date("Y-m-d H:i:s");
            $log_message = "User:$user Article:$article_id AT:$time_stamp Action:Delete\n";
            file_put_contents("../../logs/article_logs.txt", $log_message, FILE_APPEND);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => '删除文章时发生未知错误']);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => '无法执行删除操作：' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>
