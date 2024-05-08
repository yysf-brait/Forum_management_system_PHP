<?php
header('Content-Type: application/json');

include '../config.php';  global $conn;

$query = "SELECT * FROM tag_article_count_view;";
$result = $conn->query($query);

$tags = [];

while ($row = $result->fetch_assoc()) {
    $tags[] = $row;
}

echo json_encode($tags);

$conn->close();
?>
