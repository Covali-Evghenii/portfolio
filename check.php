<?php

$data = json_decode(file_get_contents('php://input'), true);

$mysql = new mysqli("localhost", "root", "", "todo");
$id = $data['id'];
$checked = $data['checked'];


$sql = "UPDATE `tasks` SET is_completed = $checked WHERE `tasks`.`id` = '$id'";
$mysql->query($sql);

$response = [
    'message'=> $checked ? "Вы успешно выполнпили задачу (id: $id)" : "Вы отменили выполнение задачи (id: $id)"
];

echo json_encode($response);