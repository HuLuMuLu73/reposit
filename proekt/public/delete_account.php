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

// Обработка подтверждения удаления
if (isset($_POST['confirm_delete'])) {
    // Удаление аккаунта из базы данных
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Уничтожение сессии и перенаправление на страницу регистрации
        session_destroy();
        header('Location: register.php');
        exit;
    } else {
        echo '<script type="text/javascript">alert("Ошибка при удалении аккаунта.")</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Удаление аккаунта</title>
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
        .delete-account-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .delete-account-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .delete-account-container p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
        }
        .delete-account-container button {
            width: 100%;
            padding: 10px;
            background-color: #dc3545;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .delete-account-container button:hover {
            opacity: 0.9;
        }
        .delete-account-container a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .delete-account-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="delete-account-container">
        <h1>Удаление аккаунта</h1>
        <p>Вы уверены, что хотите удалить свой аккаунт? Это действие нельзя отменить.</p>
        <form action="delete_account.php" method="POST">
            <button type="submit" name="confirm_delete">Да, удалить аккаунт</button>
        </form>
        <a href="profile.php">Нет, вернуться в профиль</a>
    </div>
</body>
</html>