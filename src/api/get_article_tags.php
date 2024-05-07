<?php
session_start();
header('Content-Type: application/json');

include '../config.php';  // 引入数据库配置
global $conn;  // 使用全局数据库连接

$article_id = $_GET['article_id'] ?? 0;  // 从GET请求中获取文章ID

if (!$article_id) {
    echo json_encode(['error' => 'No article ID provided']);
    exit;
}

// 查询该文章的所有标签
$query = "SELECT t.tag_id, t.tag_name FROM tags t 
          JOIN article_tags at ON t.tag_id = at.tag_id
          WHERE at.article_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

$tags = [];
while ($row = $result->fetch_assoc()) {
    $tags[] = $row;
}

echo json_encode(['tags' => $tags]);

$stmt->close();
$conn->close();
?>
