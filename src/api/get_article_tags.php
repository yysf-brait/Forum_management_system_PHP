<?php
session_start();
header('Content-Type: application/json');

include '../config.php';  global $conn;  
$article_id = $_GET['article_id'] ?? 0;  
if (!$article_id) {
    echo json_encode(['error' => 'No article ID provided']);
    exit;
}

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
