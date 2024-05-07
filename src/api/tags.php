<?php
// src/api/tags.php
header('Content-Type: application/json');

include '../config.php';  // 引入数据库配置文件
global $conn;

$query = "SELECT * FROM tag_article_count_view;";
$result = $conn->query($query);

$tags = [];

while ($row = $result->fetch_assoc()) {
    $tags[] = $row;
}

echo json_encode($tags);

$conn->close();
?>
