<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    echo "Пользователь не авторизован.";
    exit;
}

$conn = mysqli_connect("MySQL-8.0", "root", "", "proekt");

if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

$user_id = $_SESSION['id'];

// Обработка формы смены пароля
if (isset($_POST['submit'])) {
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Проверка, что новый пароль и подтверждение совпадают
    if ($new_password !== $confirm_password) {
        echo '<script type="text/javascript">alert("Новый пароль и подтверждение не совпадают.")</script>';
        exit;
    }

    // Проверка длины нового пароля
    if (strlen($new_password) < 6 || strlen($new_password) > 24) {
        echo '<script type="text/javascript">alert("Длина пароля должна быть от 6 до 12 символов.")</script>';
        exit;
    }

    // Получение текущего пароля из базы данных
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_password_hash = $row['password'];

        // Проверка, что старый пароль верный
        if (!password_verify($old_password, $current_password_hash)) {
            echo '<script type="text/javascript">alert("Старый пароль неверный.")</script>';
            exit;
        }

        // Хеширование нового пароля
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Обновление пароля в базе данных
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $new_password_hash, $user_id);

        if (mysqli_stmt_execute($update_stmt)) {
            echo '<script type="text/javascript">alert("Пароль успешно изменен.")</script>';
            header('Location: profile.php'); // Перенаправление на страницу профиля
            exit;
        } else {
            echo '<script type="text/javascript">alert("Ошибка при изменении пароля.")</script>';
        }
    } else {
        echo '<script type="text/javascript">alert("Пользователь не найден.")</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Смена пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .password-change-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .password-change-form h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .password-change-form input[type="password"] {
            width: 93%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            margin-right: 200px;
        }
        .password-change-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .password-change-form input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="password-change-form">
        <h1>Смена пароля</h1>
        <form action="password.php" method="POST">
            <input type="password" name="old_password" placeholder="Старый пароль" required>
            <input type="password" name="new_password" placeholder="Новый пароль" required>
            <input type="password" name="confirm_password" placeholder="Подтвердите новый пароль" required>
            <input type="submit" name="submit" value="Изменить пароль">
        </form>
    </div>
</body>
</html>