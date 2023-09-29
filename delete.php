<?php

$data = json_decode(file_get_contents('php://input'), true);

$mysql = new mysqli("localhost", "root", "", "todo");
$id = $data['id'];

$sql = "DELETE FROM `tasks` WHERE `tasks`.`id` = '$id'";
$mysql->query($sql);

$response = [
    'message' => "Вы успешно удалили задачу (id: $id)"
];

echo json_encode($response);

