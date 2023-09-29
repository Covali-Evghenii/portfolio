<?php

session_start();

?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<title>Login</title>
<link rel="stylesheet" href="style.css">

<form method="post" action="login.php" class="log">
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
    <input type="text" placeholder="Введите свой логин" name="name" class="name" required>
    <label>Пароль</label>
    <input type="password" placeholder="Введите пароль" name="pass" class="name" required>
    <button name="submit" value="submit" style="font-size: 25px" type="submit" class="btn login" id="login">Войти</button>
    <br>
    <p style="font-size: 25px">Нет аккаунта?
        <a href="register.php" style="font-size: 25px" class="link">Зарегистрироваться</a>
    </p>
</form>

<?php
unset($_SESSION['error']);

if (!isset($_POST['submit'])) {
    return;
}

global $conn;
require_once ('db.php');

$name = $_POST['name'];
$pass = $_POST['pass'];

if (empty($name) || empty($pass)) {
    $_SESSION['error'] = 'Заполните все поля.';
    die();
}

$hashedPassword = md5($pass);
$sql = "SELECT * FROM `accounts` WHERE name = '$name' AND password = '$hashedPassword' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Пользователя с таким именем не существует или Вы ввели неверный пароль.';
    die();
} else {
    $user = $result->fetch_assoc();

    $_SESSION['last_login_at'] = date('Y-m-d H:i:s');
    # sql update user last_login_at

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $name;
}

header('Location: /');
