<?php
session_start();
$isLoggedIn = isset($_SESSION['id']);
$username = $isLoggedIn ? $_SESSION['login'] : '';


$fullName = '';

if ($isLoggedIn) {
    $conn = mysqli_connect("MySQL-8.0", "root", "", "proekt");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $query = "SELECT role, first_name, last_name FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . mysqli_error($conn));
    }
    
    if (!mysqli_stmt_bind_param($stmt, "i", $_SESSION['id'])) {
        die("Ошибка привязки параметров: " . mysqli_stmt_error($stmt));
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Ошибка выполнения запроса: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        die("Ошибка получения результата: " . mysqli_error($conn));
    }
    
    if ($row = mysqli_fetch_assoc($result)) {
        $isAdmin = ($row['role'] === 'admin');
        $fullName = htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMaster - Обучение программированию</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" type="images/x-icon" href="icon.webp"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Основные стили */
        body{
            background-image: url(code.png);
        }
        /* Стили для основного контента */
        main {
            padding: 20px;
        }
        
        /* Остальные стили секций */
        .section {
            padding: 60px 0;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Шапка сайта -->
    <header>
        <div class="auth-section">
            <div class="container">
                <div class="header-content" style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Логотип -->
                    <a href="#" class="logo" style="flex: 1;">Code<span>Master</span></a>
                    
                    <!-- Навигация -->
                    <nav style="flex: 2; display: flex; justify-content: center;">
                        <div style="display: flex; gap: 20px;">
                            <a href="#about">О нас</a>
                            <a href="#services">Наши услуги</a>
                            <a href="#reviews">Отзывы</a>
                            <a href="#contact">Контакты</a>
                        </div>
                    </nav>
                    
                    <!-- Информация о пользователе -->
                    <div style="flex: 1; display: flex; justify-content: flex-end; margin-left: 10px;">
                        <?php if ($isLoggedIn): ?>
                            <div class="user-profile">
                                <div class="user-info-badge">
                                    <i class="fas fa-user"></i>
                                    <a href="profile.php" class="user-link"><?= $fullName ?></a>
                                    <?php if ($isAdmin): ?>
                                        <span class="admin-badge">Администратор</span>
                                    <?php endif; ?>
                                </div>
                                <a href="logout.php" class="logout-btn">
                                    <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; gap: 10px;">
                                <a href="register.php"><button class="reg-btn">Регистрация</button></a>
                                <a href="login.php"><button class="login-btn">Войти</button></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Кнопка админа -->

<!-- Кнопка админа (только для админов) -->
<?php if ($isAdmin): ?>
    <div class="admin-btn" id="adminBtn">
        <span>!</span>
    </div>

    <!-- Модальное окно админа -->
    <div class="admin-modal" id="adminModal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal">&times;</span>
            <h3>Функции администратора</h3>
            <button class="admin-function-btn" id="deleteUserBtn">Удалить пользователя</button>
            <button class="admin-function-btn" id="makeAdminBtn">Сделать пользователя администратором</button>
        </div>
    </div>
    <div class="overlay" id="overlay"></div>

    <script>
    // Скрипт админ-панели
    document.addEventListener('DOMContentLoaded', function() {
        const adminBtn = document.getElementById('adminBtn');
        const adminModal = document.getElementById('adminModal');
        const closeModal = document.getElementById('closeModal');
        const overlay = document.getElementById('overlay');
        
        if (adminBtn && adminModal) {
            adminBtn.addEventListener('click', function() {
                adminModal.style.display = 'block';
                overlay.style.display = 'block';
            });
            
            closeModal.addEventListener('click', function() {
                adminModal.style.display = 'none';
                overlay.style.display = 'none';
            });
            
            overlay.addEventListener('click', function() {
                adminModal.style.display = 'none';
                overlay.style.display = 'none';
            });
        }
    });
    </script>
<?php endif; ?>

    <!-- Основное содержимое -->
    <main>
        <!-- О компании -->
        <section id="about" class="section about">
            <div class="container">
                <h2 class="section-title">О нашей компании</h2>
                <div class="about-content">
                    <div class="about-text">
                        <p>CodeMaster - это ведущая компания по обучению программированию, которая помогает людям освоить востребованные IT-профессии с нуля или повысить свою квалификацию.</p>
                        <p>Наша миссия - сделать качественное IT-образование доступным для каждого. Мы работаем с 2015 года и за это время обучили более 10 000 студентов, многие из которых сейчас работают в крупных IT-компаниях.</p>
                    </div>
                    <div class="about-image">
                        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Программирование" style="max-width: 100%;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Преимущества -->
        <section id="advantages" class="section advantages">
            <div class="container">
                <div class="preim">
                    <h2 style="background-color: white; border: 20px white;" class="section-title">Наши преимущества</h2>
                </div>
                <div class="advantages-container">
                    <div class="advantage-card">
                        <h2>Опытные преподаватели</h2>
                        <p>Наши преподаватели - практикующие разработчики с опытом работы в ведущих IT-компаниях.</p>
                    </div>
                    
                    <div class="advantage-card">
                        <h2>Практическая направленность</h2>
                        <p>80% времени уделяем практике и реальным проектам, которые можно добавить в портфолио.</p>
                    </div>
                    
                    <div class="advantage-card">
                        <h2>Гибкий график</h2>
                        <p>Занятия проходят в удобное время, можно совмещать с работой или учебой.</p>
                    </div>
                    
                    <div class="advantage-card">
                        <h2>Сертификат</h2>
                        <p>После успешного окончания курса вы получаете сертификат, подтверждающий ваши навыки.</p>
                    </div>
                    
                    <div class="advantage-card">
                        <h2>Помощь с трудоустройством</h2>
                        <p>Помогаем с составлением резюме и подготовкой к собеседованиям, сотрудничаем с IT-компаниями.</p>
                    </div>
                    
                    <div class="advantage-card">
                        <h2>Поддержка после курса</h2>
                        <p>Даже после окончания курса вы можете обращаться к преподавателям за советами.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Услуги -->
        <section id="services" class="section services">
            <div class="container">
                <div class="course-block">
                    <h1>Основы программирования</h1>
                    <p>Идеальный курс для тех, кто только начинает свой путь в IT. Вы освоите базовые концепции программирования и алгоритмы.</p>
                    <ul>
                        <li><strong>Длительность</strong>: 2 месяца</li>
                        <li><strong>Формат</strong>: онлайн/оффлайн</li>
                        <li><strong>Уровень</strong>: начальный</li>
                    </ul>
                    <div class="course-footer">
                        <div class="price">15 000 руб.</div>
                        <button class="enroll-btn">Записаться</button>
                    </div>
                </div>

                <div class="course-block">
                    <h2>Веб-разработка</h2>
                    <p>Научитесь создавать современные веб-приложения с использованием HTML, CSS, JavaScript и фреймворков.</p>
                    <ul>
                        <li><strong>Длительность</strong>: 4 месяца</li>
                        <li><strong>Формат</strong>: онлайн/оффлайн</li>
                        <li><strong>Уровень</strong>: начальный/средний</li>
                    </ul>
                    <div class="course-footer">
                        <div class="price">25 000 руб.</div>
                        <button class="enroll-btn">Записаться</button>
                    </div>
                </div>

                <div class="course-block">
                    <h2>Python для анализа данных</h2>
                    <p>Освойте Python и библиотеки для анализа данных (Pandas, NumPy, Matplotlib) и научитесь извлекать ценные инсайты.</p>
                    <ul>
                        <li><strong>Длительность</strong>: 3 месяца</li>
                        <li><strong>Формат</strong>: онлайн</li>
                        <li><strong>Уровень</strong>: средний</li>
                    </ul>
                    <div class="course-footer">
                        <div class="price">22 000 руб.</div>
                        <button class="enroll-btn">Записаться</button>
                    </div>
                </div>

                <div class="course-block">
                    <h2>Мобильная разработка (iOS/Android)</h2>
                    <p>Научитесь создавать мобильные приложения для платформ iOS и Android с использованием современных технологий.</p>
                    <ul>
                        <li><strong>Длительность</strong>: 5 месяцев</li>
                        <li><strong>Формат</strong>: оффлайн</li>
                        <li><strong>Уровень</strong>: средний</li>
                    </ul>
                    <div class="course-footer">
                        <div class="price">30 000 руб.</div>
                        <button class="enroll-btn">Записаться</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Отзывы -->
        <section id="reviews" class="section reviews">
            <div class="container">
                <h2 class="section-title">Отзывы наших студентов</h2>
                <div class="reviews-grid">
                    <div class="review-card">
                        <p class="review-text">"Курс по веб-разработке превзошел все мои ожидания. Преподаватели объясняют сложные вещи простым языком. После курса смог устроиться на работу junior-разработчиком."</p>
                        <p class="review-author">— Аноним, 28 лет</p>
                    </div>
                    <div class="review-card">
                        <p class="review-text">"Благодаря курсу по Python смог автоматизировать многие процессы на своей работе, что сэкономило компании время и деньги. Теперь рассматривают мое повышение."</p>
                        <p class="review-author">— Аноним, 32 года</p>
                    </div>
                    <div class="review-card">
                        <p class="review-text">"Очень понравился подход к обучению - много практики, мало воды. После основ программирования планирую продолжить обучение на более продвинутых курсах."</p>
                        <p class="review-author">— Аноним, 21 год</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Контакты -->
        <section id="contact" class="section contact">
            <div class="container">
                <h2 class="section-title">Контакты</h2>
                <div class="contact-content">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h3>Адрес</h3>
                                <p>г. Москва, ул. Программистов, д. 15, офис 304</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h3>Телефон</h3>
                                <p>+7 (495) 123-45-67</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h3>Email</h3>
                                <p>info@codemaster.ru</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3>Часы работы</h3>
                                <p>Пн-Пт: 9:00 - 20:00<br>Сб-Вс: 10:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2245.3727899248117!2d37.61531071593095!3d55.75202398055309!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a5a738fa419%3A0x7c347d506f52311f!2z0JrRgNCw0YHQvdCw0Y8g0YPQuy4sIDE1LCDQnNC-0YHQutCy0LAsINCg0L7RgdGB0LjRjywgMTA3MDc4!5e0!3m2!1sru!2sru!4v1620000000000!5m2!1sru!2sru" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Подвал -->
        <footer>
            <div class="container">
                <div class="footer-content">
                    <a href="https://www.aviasales.ru/" target="_blank">
                        <div class="footer-logo">Code<span>Master</span></div>
                    </a>
                </div>
                <div class="copyright">
                    &copy; 2025 CodeMaster. Все права защищены.
                </div>
            </div>
        </footer>
    </main>

    <button id="backToTopBtn" title="Наверх">↑</button>

    <script>
        // Кнопка "Наверх"
        window.onscroll = function() { scrollFunction(); };
        function scrollFunction() {
            const btn = document.getElementById("backToTopBtn");
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btn.style.display = "block";
            } else {
                btn.style.display = "none";
            }
        }
        document.getElementById("backToTopBtn")?.addEventListener("click", function() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    </script>
</body>
</html>