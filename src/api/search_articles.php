<?php
// PHP 脚本中调用存储过程
$searchQuery = $_GET['query'] ?? '';
$pageNum = $_GET['pageNum'] ?? 1;
$pageSize = $_GET['pageSize'] ?? 5;

// 连接数据库
include '../config.php';  // 引入数据库配置文件
global $conn;

$stmt = $conn->prepare("CALL SearchArticlesWithPaging(?, ?, ?, @total)");
$stmt->bind_param("sii", $searchQuery, $pageNum, $pageSize);
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

$stmt->next_result(); // 移动到下一个结果集
$stmt->store_result(); // 存储剩余的结果集以清除额外的数据
$stmt = $conn->prepare("SELECT @total AS totalArticles;");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalArticles = $row['totalArticles'];

$response = [
    'total' => $totalArticles,
    'articles' => $articles
];

echo json_encode($response);
$stmt->close();
$conn->close();
