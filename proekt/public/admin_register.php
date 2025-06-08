<?php
session_start();

// Подключение к базе данных
$conn = new mysqli("MySQL-8.0", "root", "", "proekt");

// Проверка соединения
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    // Получение и очистка данных из формы
    $login = trim($_POST['username']); 
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']); 
    $first_name = trim($_POST['first-name']); 
    $last_name = trim($_POST['last-name']); 
    $date = date('Y-m-d H:i:s');

    // Валидация логина
    if (strlen($login) < 4 || strlen($login) > 24) {
        echo '<script type="text/javascript">alert("Ошибка: длина ввода логина должна быть от 4 до 24 символов."); window.history.back();</script>';
        exit;
    }

    // Валидация пароля
    if (strlen($password) < 4 || strlen($password) > 24) {
        echo '<script type="text/javascript">alert("Ошибка: длина ввода пароля должна быть от 4 до 24 символов."); window.history.back();</script>';
        exit;
    }

    // Валидация email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script type="text/javascript">alert("Ошибка: некорректный ввод email."); window.history.back();</script>';
        exit;
    }

    // Проверка совпадения паролей
    if ($password != $confirm_password) {
        echo '<script type="text/javascript">alert("Пароли не совпадают"); window.history.back();</script>';
        exit;
    }

    // Проверка уникальности логина
    $query = "SELECT * FROM users WHERE login = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo '<script type="text/javascript">alert("Такой логин уже существует"); window.history.back();</script>';
        exit;
    }

    // Хеширование пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Вставка данных в базу
    $query = "INSERT INTO users (login, password, email, first_name, last_name, date, role) VALUES (?, ?, ?, ?, ?, ?, 'admin')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssss", $login, $hashedPassword, $email, $first_name, $last_name, $date);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: login.php');
        exit;
    } else {
        echo "Ошибка при регистрации: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMaster - Регистрация администратора</title>
    <link rel="stylesheet" type="text/css" href="3.css">
    <link rel="icon" type="images/x-icon" href="icon.webp"/>
</head>
<body>
    <h1>CodeMaster</h1>
    
    <div class="auth-container">
        <h2>Регистрация нового администратора</h2>
        
        <form action="admin_register.php" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="first-name">Имя</label>
                    <input type="text" id="first-name" name="first-name" required>
                </div>
                <div class="form-group">
                    <label for="last-name">Фамилия</label>
                    <input type="text" id="last-name" name="last-name" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-group">
                <label for="username">Придумайте логин</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Придумайте пароль</label>
                <input type="password" id="password" name="password" required>
                <div class="password-hint">Пароль должен содержать не менее 8 символов, включая цифры и буквы</div>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Повторите пароль</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            
            <div class="form-group" style="display:none;">
                <input type="text" name="middle-name" value="">
                <input type="date" name="date" value="">
                <input type="text" name="location" value="">
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">Я согласен с <a href="#">условиями использования</a> и <a href="#">политикой конфиденциальности</a></label>
            </div>

            <div class="buttons-container">
                <button type="submit" name="submit">Зарегистрироваться</button>
                <button type="button" class="secondary-btn" onclick="this.form.reset();">Очистить форму</button>
            </div>
            
            <div class="login-container">
                <span>Уже есть аккаунт?</span>
                <a href="login.php"><button type="button" class="login-btn">Войти</button></a>
            </div>
        </form>
    </div>
    
    <hr>
</body>
</html>