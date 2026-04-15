<?php
require '../db.php';

// Получаем id сборки из GET параметра
$build_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$build_id) {
    // Если id сборки не передан, перенаправляем на главную страницу
    header('Location: index.php');
    exit();
}

// Получаем информацию о сборке по id
$sql_build = "SELECT * FROM builds WHERE id = :id";
$stmt_build = $pdo->prepare($sql_build);
$stmt_build->execute([':id' => $build_id]);
$build = $stmt_build->fetch();

if (!$build) {
    // Если сборка не найдена, перенаправляем на главную страницу
    header('Location: index.php');
    exit();
}

// Получаем комплектующие этой сборки
$sql_items = "SELECT p.title, p.description, p.price FROM products p
              JOIN build_items bi ON p.id = bi.product_id
              WHERE bi.build_id = :build_id";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([':build_id' => $build_id]);
$items = $stmt_items->fetchAll();

// Проверка на авторизацию пользователя
session_start();
if (!isset($_SESSION['user_id'])) {
    // Если пользователь не авторизован, перенаправляем на страницу входа
    header('Location: login.php');
    exit();
}

// Обработка добавления сборки в заказ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем id пользователя из сессии
    $user_id = $_SESSION['user_id'];

    // Вставляем заказ в таблицу orders
    $sql_order = "INSERT INTO orders (user_id, build_id) VALUES (:user_id, :build_id)";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([':user_id' => $user_id, ':build_id' => $build_id]);

    // Перенаправляем пользователя на страницу с успешным заказом или на страницу профиля
    header('Location: order_success.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сборка: <?= htmlspecialchars($build['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Сборка: <?= htmlspecialchars($build['name']) ?></h1>
        <p><?= htmlspecialchars($build['description']) ?></p>
        <h3>Общая цена: <?= number_format($build['total_price'], 0, '.', ' ') ?> ₽</h3>

        <h3>Комплектующие:</h3>
        <div class="row">
            <?php foreach ($items as $item): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($item['description']) ?></p>
                            <p><strong>Цена: <?= number_format($item['price'], 0, '.', ' ') ?> ₽</strong></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Кнопка "Купить", которая добавит сборку в заказ -->
<!-- Кнопка "Купить" для добавления сборки в заказ -->
<form method="GET" action="make_order.php">
    <!-- Передаем ID сборки -->
    <input type="hidden" name="id" value="<?= $build['id'] ?>">  <!-- ID сборки -->
    <!-- Указываем тип (сборка) -->
    <input type="hidden" name="type" value="build">  <!-- Тип товара (сборка) -->
    <!-- Кнопка для отправки формы -->
    <button type="submit" class="btn btn-success">Купить</button>
</form>

        <!-- Кнопка возврата на предыдущую страницу -->
        <a href="index.php" class="btn btn-secondary mt-3">Назад</a>
    </div>
</body>
</html>