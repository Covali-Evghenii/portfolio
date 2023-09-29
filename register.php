<?php

session_start();

?>

<title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">

<form method="post" action="register.php" class="reg">
    <?php
        if (isset($_SESSION['error'])) {
    ?>
    <div class="error-message">
        <span>
            Ошибка: <?php echo $_SESSION['error']; ?>
        </span>
    </div>
    <?php
        }
    ?>
    <label>Логин</label>
    <input type="text" placeholder="Придумайте логин" name="name" class="name" required>
    <label>Пароль</label>
    <input type="password" placeholder="Придумайте пароль" name="pass" class="name" required>
    <button name="submit" style="font-size: 25px" type="submit" class="btn register">Зарегистрироваться</button>
    <br>
    <p style="font-size: 25px">Есть аккаунт?
        <a href="login.php" style="font-size: 25px" class="link">Авторизоваться</a>
    </p>
</form>

<?php
unset($_SESSION['error']);

if (isset($_POST['submit'])) {
    global $conn;
    require_once('db.php');

    $name = $_POST['name'];
    $pass = $_POST['pass'];

    if (empty($name) || empty($pass)) {
        $_SESSION['error'] = 'Заполните все поля.';
    } else {
        $checkSql = "SELECT id FROM `accounts` WHERE name = '$name' LIMIT 1";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            $_SESSION['error'] = 'Пользователь с таким именем уже существует.';
        } else {
            $hashedPassword = md5($pass);

            $sql = "INSERT INTO `accounts` (name, password) VALUES ('$name', '$hashedPassword')";

            if ($conn->query($sql)) {
                header('Location: /');
                exit();
            } else {
                $_SESSION['error'] = 'Произошла ошибка при выполнении запроса к базе данных.';
            }
        }
    }
}
