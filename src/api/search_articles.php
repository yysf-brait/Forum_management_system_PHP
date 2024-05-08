<?php
$searchQuery = $_GET['query'] ?? '';
$pageNum = $_GET['pageNum'] ?? 1;
$pageSize = $_GET['pageSize'] ?? 5;

include '../config.php';  global $conn;

$stmt = $conn->prepare("CALL SearchArticlesWithPaging(?, ?, ?, @total)");
$stmt->bind_param("sii", $searchQuery, $pageNum, $pageSize);
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

$stmt->next_result(); $stmt->store_result(); $stmt = $conn->prepare("SELECT @total AS totalArticles;");
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
