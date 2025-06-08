<?php
session_start();

$conn = mysqli_connect("MySQL-8.0", "root", "", "proekt");

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Добро пожаловать</title>
    <link rel="icon" type="images/x-icon" href="icon.webp"/>
</head>
<body>
    <h2>Добро пожаловать!</h2>
    <p>Вы успешно зарегистрировались и авторизовались!</p>
    <a href="index.php">Перейти на главную страницу</a>
</body>
</html>