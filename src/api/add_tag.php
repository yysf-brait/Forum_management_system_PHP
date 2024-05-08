<?php
include '../config.php'; global $conn;

$tagName = $_GET['name'] ?? '';
$articleId = $_GET['article_id'] ?? 0;

if (empty($tagName)) {
    echo json_encode(['success' => false, 'message' => 'the tag name is required']);
    exit;
}

try {
        $stmt = $conn->prepare("INSERT INTO tags (tag_name) VALUES (?)");
    $stmt->bind_param("s", $tagName);
    $stmt->execute();
    $tagId = $conn->insert_id; 
        $stmt = $conn->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $articleId, $tagId);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'tag added successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'DataBase server error: ' . $e->getMessage()]);
}
?>
