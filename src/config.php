<?php
$servername = "localhost";
$username = "root";
$password = "369874125DLTSa";
$dbname = "project240415";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
