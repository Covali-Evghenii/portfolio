<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    return;
}

$userId = $_SESSION['user_id'];

$mysql = new mysqli("localhost", "root", "", "todo");
$query = $mysql->query("SELECT * FROM tasks WHERE author = $userId");

$tasksCount = $query->num_rows;

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Todo APP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
              rel="stylesheet"
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
              crossorigin="anonymous"
        >
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
                crossorigin="anonymous">
        </script>
        <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
            </div>
        </div>
        <div class="header row border-bottom">
            <div class="col-md-4 p-4">
                <img src="logo.webp" alt="Todo list" class="logo">
            </div>
            <div class="logo-block col-md-4 col-md-pull-4 align-items-end">
                <form action="logout.php" method="post">
                    <span class="mr-3"><?php echo 'Добро пожаловать, ' . $_SESSION['username']; ?></span>
                    <button type="submit" class="btn btn-sm btn-outline-dark">Выход</button>
                </form>
            </div>
        </div>
        <div class="content">
            <div class="d-flex flex-column gap-4">
                <div class="add-container d-flex flex-column align-items-center gap-4">
                    <div class="row">
                        <div class="search-form d-flex gap-2">
                            <label>
                                <input name="add-item" class="search-form__txt" id="text" type="text" placeholder="Описание задачи...">
                            </label>
                            <button type="button" class="btn btn-outline-primary" id="main" onClick="submit(<?php echo $_SESSION['user_id']?>, <?php echo $tasksCount; ?>)">Добавить</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="priority">
                            <p class="select-priority">Выберите приоритет для задачи:</p>
                            <div>
                                <input checked type="radio" id="lowPriority" name="priority" value="low">
                                <label for="lowPriority">Низкий</label>
                            </div>
                            <div>
                                <input type="radio" id="mediumPriority" name="priority" value="medium">
                                <label for="mediumPriority">Средний</label>
                            </div>
                            <div>
                                <input type="radio" id="highPriority" name="priority" value="high">
                                <label for="highPriority">Высокий</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-container d-flex flex-column align-items-center gap-4">
                    <div class="is-completed">
                        <div>
                            <input type="checkbox" id="allFilter" class="filter-checkbox" checked>
                            <label for="allFilter" class="filter">Все</label></div>
                        <div>
                            <input type="checkbox" id="completedFilter" class="filter-checkbox">
                            <label for="completedFilter" class="filter">Выполненные</label>
                        </div>
                        <input type="checkbox" id="incompleteFilter" class="filter-checkbox">
                        <label for="incompleteFilter" class="filter">Невыполненные</label>
                    </div>
                    <div class="priority-filter">
                        <label for="priority">Приоритет:</label>
                        <select id="priority" class="btn precedence">
                            <option value="all">Все</option>
                            <option value="low">Низкий</option>
                            <option value="medium">Средний</option>
                            <option value="high">Высокий</option>
                        </select>
                    </div>
                </div>
                <div class="todos d-flex justify-content-center">
                    <table class="table table-bordered table-hover w-50">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Описание</th>
                                <th scope="col">Приоритет</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="todoTableBody">
                        <?php
                        $query = $mysql->query("SELECT * FROM tasks WHERE author = $userId");

                        $priorityClass = [
                            'low' => 'text-success',
                            'medium' => 'text-warning',
                            'high' => 'text-danger fw-bold',
                        ];

                        $priorityLabel = [
                            'low' => 'низкий',
                            'medium' => 'средний',
                            'high' => 'высокий',
                        ];

                        $idx = 1;

                        while ($result = $query->fetch_assoc()) {
                            ?>
                            <tr class="todo-item" id="row-<?php echo $result['id'] ?>" data-priority="<?php echo $result['priority'] ?>">
                                <th scope="row" style="width: 10%">
                                    <span class="item-id"><?php echo $idx . '. '; ?></span>
                                    <label>
                                        <input onclick="checkElement(event, <?php echo $result['id'] ?>)" type="checkbox" <?php echo $result['is_completed'] ? 'checked' : '' ?> class="is_completed" id="completed">
                                    </label>
                                </th>
                                <td class="item-name"><?php echo $result['name'] ?></td>
                                <td style="width: 15%;" class="item-priority <?php echo $priorityClass[$result['priority']] ?>"><?php echo $priorityLabel[$result['priority']] ?></td>
                                <td style="width: 15%;">
                                    <div class="action-buttons d-flex gap-2 justify-content-center">
                                        <button onclick="removeElement(event, <?php echo $result['id'] ?>)" type="button" name="delete" value="Delete" id="delete_btn" class="btn delete_btn btn-danger btn-sm mr-2">Удалить</button>
                                        <button onclick="editElement(<?php echo $result['id'] ?>)" type="button" name="edit" value="Edit" id="edit_btn" class="btn edit_btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal" data-task-name="<?php echo $result['name'] ?>">Редактировать</button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            $idx++;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <span class="visit">
                    <?php
                        echo "Последнее посещение: " . $_SESSION['last_login_at'];
                    ?>
                </span>
            </div>
        </div>
        <footer class="footer text-center py-3">
            <div class="container">
                <p class="text-body-secondary mb-0 border-top pb-3">© 2023 Covali Evghenii, MI-223</p>
            </div>
        </footer>
        <div class="modal fade" id="editTaskModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editTaskModal">Редактирование задачи</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="taskName" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary" id="saveChangesBtn">Сохранить изменения</button>
                    </div>
                </div>
            </div>
        </div>
        <script defer src="script.js"></script>
    </body>
</html>
