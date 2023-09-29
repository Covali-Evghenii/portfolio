<?php

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Не хватает обязательных параметров']);
    exit;
}

$mysql = new mysqli("localhost", "root", "", "todo");

if ($mysql->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка подключения к базе данных']);
    exit;
}

$id = $mysql->real_escape_string($data['id']);
$name = $mysql->real_escape_string($data['name']);

$sql = "UPDATE `tasks` SET `name` = '$name' WHERE `tasks`.`id` = '$id'";

if ($mysql->query($sql)) {
    echo json_encode(['message' => "Вы успешно редактировали задачу (id:$id)", 'name' => $name]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при выполнении SQL-запроса']);
}

$mysql->close();