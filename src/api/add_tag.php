<?php
include '../config.php'; // 确保包含了数据库连接配置
global $conn;

$tagName = $_GET['name'] ?? '';
$articleId = $_GET['article_id'] ?? 0;

if (empty($tagName)) {
    echo json_encode(['success' => false, 'message' => '标签名不能为空']);
    exit;
}

try {
    // 插入新标签
    $stmt = $conn->prepare("INSERT INTO tags (tag_name) VALUES (?)");
    $stmt->bind_param("s", $tagName);
    $stmt->execute();
    $tagId = $conn->insert_id; // 获取新插入标签的ID

    // 将新标签关联到文章
    $stmt = $conn->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $articleId, $tagId);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => '新标签添加成功']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '数据库错误: ' . $e->getMessage()]);
}
?>
