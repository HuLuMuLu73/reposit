<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к базе данных
$conn = new mysqli("MySQL-8.0", "root", "", "proekt");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $login = trim($_POST['login']);  // Исправлено на правильное имя поля
    $password = trim($_POST['password']);

    // Защита от SQL-инъекций
    $query = "SELECT * FROM users WHERE login=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    
    $stmt->bind_param("s", $login);
    if (!$stmt->execute()) {
        die("Ошибка выполнения запроса: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Отладочный вывод - УБРАТЬ В ПРОДАКШЕНЕ!
        echo "<pre>User from DB: ";
        print_r($user);
        echo "Entered password: " . $password . "</pre>";

        // Вариант 1: Если пароли хешированы
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            // ... остальные данные сессии
            header('Location: index.php');
            exit();
        }
        else {
            $error_message = "Неверный пароль!";
        }
    } else {
        $error_message = "Пользователь не найден!";
    }
    
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMaster - Вход</title>
    <link rel="stylesheet" type="text/css" href="2.css">
    <link rel="icon" type="images/x-icon" href="icon.webp"/>
    <style>
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1 class="">CodeMaster</h1>
    
    <div class="auth-container">
        <h2>Войти</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Логин:</label>
                <input type="text" id="username" name="login" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Запомнить меня</label>
            </div>
            
            <div class="buttons-container">
                <button type="submit" name="submit">Войти</button>
                <button type="button" class="employee-btn" onclick="window.location.href='employee_login.php'">Войти как сотрудник</button>
            </div>
            
            <div class="register-container">
                <span>Нет аккаунта?</span>
                <a href="register.php"><button type="button" class="register-btn">Зарегистрироваться</button></a>
            </div>
        </form>
    </div>
    
    <hr>
    
    <div>
        <h2>О нашей компании</h2>
        <p>CodeMaster - это ведущая компания по обучению программированию, которая помогает людям освоить востребованные IT-профессии с нуля или повысить свою квалификацию.</p>
        
        <p>Наша миссия - сделать качественное IT-образование доступным для каждого. Мы работаем с 2015 года и за это время обучили более 10 000 студентов, многие из которых сейчас работают в крупных IT-компаниях.</p>
        
        <p>Наши преподаватели - это практикующие разработчики с опытом работы в ведущих технологических компаниях. Они не только знают теорию, но и ежедневно применяют свои знания на практике.</p>
    </div>
</body>
</html>