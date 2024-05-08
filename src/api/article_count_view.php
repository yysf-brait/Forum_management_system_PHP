<?php
header('Content-Type: application/json');

include '../config.php';  
global $conn;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT article_count FROM article_count_view;";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(array("article_count" => (int)$row['article_count']));
} else {
    echo json_encode(array("error" => "Unable to fetch article count."));
}

$conn->close();
?>
