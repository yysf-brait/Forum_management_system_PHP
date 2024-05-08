<?php
session_start();

header('Content-Type: application/json');

include '../config.php';  // 引入数据库配置文件
global $conn;

$pageNum = isset($_GET['pageNum']) ? (int)$_GET['pageNum'] : 1;
$pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;
$tagIds = $_GET['tagIds']?? '';


if (!empty($tagIds)) {
    $sql = "CALL FetchArticlesByTagsWithPaging(?, ?, ?, @total);";
} else {
    $sql = "CALL FetchArticlesByPage(?, ?);";
}

$stmt = $conn->prepare($sql);

if (!empty($tagIds)) {
    $stmt->bind_param("sii", $tagIds, $pageNum, $pageSize);
} else {
    $stmt->bind_param("ii", $pageNum, $pageSize);
}

if (!$stmt->execute()) {
  echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$result = $stmt->get_result();
$articles = [];



while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

if (!empty($tagIds)) {
    $stmt->next_result(); // 移动到下一个结果集
    $stmt->store_result(); // 存储剩余的结果集以清除额外的数据
    $stmt = $conn->prepare("SELECT @total AS totalArticles;");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalArticles = $row['totalArticles'];
} else {
    $totalArticles = null;
}


$response = [
    'total' => $totalArticles,
    'articles' => $articles
];

echo json_encode($response);

$stmt->close();
$conn->close();
?>
