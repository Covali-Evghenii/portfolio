<?php

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$mysql = new mysqli("localhost", "root", "", "todo");

$name = $data['name'];
$user_id = $data['user_id'];
$priority = $data['priority'];

$maxIdQuery = "SELECT MAX(id) AS max_id FROM tasks";
$maxIdResult = $mysql->query($maxIdQuery);

$maxId = 1;

if ($maxIdResult && $row = $maxIdResult->fetch_assoc()) {
    $maxId = $row['max_id'] ?: 0;
}

$newId = $maxId + 1;

$sql = "INSERT INTO tasks (id, name, author, priority) VALUES ('$newId', '$name', '$user_id', '$priority')";
$mysql->query($sql);

$response = [
    'id' => $newId,
    'name' => $name,
    'user_id' => $user_id,
    'priority' => $priority,
];

echo json_encode($response);