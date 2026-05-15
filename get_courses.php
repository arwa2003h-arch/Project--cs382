<?php
header('Content-Type: application/json');
require_once 'config.php';

$db = new Database();
$result = $db->conn->query("SELECT id, name FROM courses ORDER BY name");

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode(['status' => 'success', 'courses' => $courses]);
?>