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

$id = $_SESSION['id'];

// Защита от SQL-инъекций
$query = "SELECT id, login, email, first_name, last_name, middle_name, location, date FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo "Ошибка запроса: " . mysqli_error($conn);
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['id'];
    $login = $row['login'];
    $email = $row['email'];
    $first_name = $row['first_name']; // Имя
    $last_name = $row['last_name']; // Фамилия
    $middle_name = $row['middle_name']; // Отчество
    $location = $row['location'];
    $date = $row['date'];
} else {
    echo "Пользователь не найден.";
    exit;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Профиль</title>
    <link rel="icon" type="images/x-icon" href="icon.webp"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('windows.webp');
        }
        .profile-container {
            background-color: darkblue;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 1);
            width: 300px;
            text-align: center;
            color: white;
            border-block-color: white;
        }
        .profile-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #white;
        }
        .profile-container p {
            font-size: 16px;
            color: #white;
            margin: 10px 20px;
            text-align: left;
            margin-bottom: 0px;
        }
        .p{
            padding-bottom: 20px;
        }
        .profile-container a {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .profile-container a:hover {
            opacity: 0.9;
            color: black;
            background-color: white;
        }
        a.delete-account:hover{
            background-color: #dc3545;
        }
        php{
            color: green;
        }
        .profile-container a{
            background-color: black;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Ваши данные:</h1>
        <div class="p">
        <p>ID: <?php echo htmlspecialchars($id); ?></p>
        <p>Логин: <?php echo htmlspecialchars($login); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Имя: <?php echo htmlspecialchars($first_name); ?></p>
        <p>Фамилия: <?php echo htmlspecialchars($last_name); ?></p>
        <p>Отчество: <?php echo htmlspecialchars($middle_name); ?></p>
        <p>Локация: <?php echo htmlspecialchars($location); ?></p>
        <p>Дата рождения: <?php echo htmlspecialchars($date); ?></p>
        </div>
        <a href="index.php" class="words">На главную</a>
        <a href="password.php" class="words">Изменить пароль</a>
        <a href="delete_account.php" class="words delete-account">Удалить аккаунт</a>
        <a href="logout.php" class="words">ВЫХОД</a>
    </div>
</body>
</html>