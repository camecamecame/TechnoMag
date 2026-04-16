<?php
session_start();
require '../db.php';


$items_per_page = 9;


$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


$offset = ($current_page - 1) * $items_per_page;


$type_filter = isset($_GET['type']) && !empty($_GET['type']) ? "WHERE type = :type" : '';
$socket_filter = isset($_GET['socket']) && !empty($_GET['socket']) ? "AND socket = :socket" : '';


$build_filter = isset($_GET['filter']) && $_GET['filter'] == 'builds' ? true : false;


if ($build_filter) {

    $sql_builds = "SELECT * FROM builds ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt_builds = $pdo->prepare($sql_builds);
    $stmt_builds->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt_builds->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt_builds->execute();
    $builds = $stmt_builds->fetchAll();
} else {

    $sql_products = "SELECT * FROM products $type_filter $socket_filter ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt_products = $pdo->prepare($sql_products);
    
    if (!empty($type_filter)) {
        $stmt_products->bindParam(':type', $_GET['type'], PDO::PARAM_STR);
    }
    if (!empty($socket_filter)) {
        $stmt_products->bindParam(':socket', $_GET['socket'], PDO::PARAM_STR);
    }

    $stmt_products->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt_products->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt_products->execute();
    $products = $stmt_products->fetchAll();
}


$sql_total = $build_filter
    ? "SELECT COUNT(*) FROM builds"
    : "SELECT COUNT(*) FROM products $type_filter $socket_filter";

$stmt_total = $pdo->prepare($sql_total);
if (!empty($type_filter)) {
    $stmt_total->bindParam(':type', $_GET['type'], PDO::PARAM_STR);
}
if (!empty($socket_filter)) {
    $stmt_total->bindParam(':socket', $_GET['socket'], PDO::PARAM_STR);
}
$stmt_total->execute();
$total_items = $stmt_total->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light px-4 mb-4">
    <span class="navbar-brand">ТехноМаг</span>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="btn btn-secondary btn-sm">Личный кабинет</a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_panel.php" class="btn btn-danger btn-sm">Админка</a>
            <?php endif; ?>
            <a href="login.php" class="btn btn-dark btn-sm">Выйти</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-sm">Войти</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <form method="GET" action="index.php" class="mb-4">
        <select name="type" class="form-control mb-3">
            <option value="">Все типы</option>
            <option value="CPU" <?= isset($_GET['type']) && $_GET['type'] == 'CPU' ? 'selected' : '' ?>>Процессор</option>
            <option value="Motherboard" <?= isset($_GET['type']) && $_GET['type'] == 'Motherboard' ? 'selected' : '' ?>>Материнская плата</option>
            <option value="RAM" <?= isset($_GET['type']) && $_GET['type'] == 'RAM' ? 'selected' : '' ?>>Оперативная память</option>
            <option value="Storage" <?= isset($_GET['type']) && $_GET['type'] == 'Storage' ? 'selected' : '' ?>>Накопитель</option>
            <option value="PowerSupply" <?= isset($_GET['type']) && $_GET['type'] == 'PowerSupply' ? 'selected' : '' ?>>Блок питания</option>
            <option value="Case" <?= isset($_GET['type']) && $_GET['type'] == 'Case' ? 'selected' : '' ?>>Корпус</option>
            <option value="GPU" <?= isset($_GET['type']) && $_GET['type'] == 'GPU' ? 'selected' : '' ?>>Видеокарта</option>
        </select>


        <?php if (isset($_GET['type']) && $_GET['type'] == 'CPU'): ?>
            <select name="socket" class="form-control mb-3">
                <option value="">Выберите сокет</option>
                <option value="LGA1700" <?= isset($_GET['socket']) && $_GET['socket'] == 'LGA1700' ? 'selected' : '' ?>>LGA 1700</option>
                <option value="AM4" <?= isset($_GET['socket']) && $_GET['socket'] == 'AM4' ? 'selected' : '' ?>>AM4</option>
                <option value="AM5" <?= isset($_GET['socket']) && $_GET['socket'] == 'AM5' ? 'selected' : '' ?>>AM5</option>
                <option value="LGA1200" <?= isset($_GET['socket']) && $_GET['socket'] == 'LGA1200' ? 'selected' : '' ?>>LGA 1200</option>
            </select>
        <?php endif; ?>

        <select name="filter" class="form-control mb-3">
            <option value="">Все товары</option>
            <option value="builds" <?= isset($_GET['filter']) && $_GET['filter'] == 'builds' ? 'selected' : '' ?>>Сборки</option>
        </select>

        <button type="submit" class="btn btn-primary">Применить фильтр</button>
    </form>


    <?php if ($build_filter): ?>
        <h2>Сборки компьютеров</h2>
        <div class="row">
            <?php foreach ($builds as $build): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($build['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($build['description']) ?></p>
                            <p><strong>Цена: <?= $build['total_price'] ?> ₽</strong></p>
                            <a href="build_detail.php?id=<?= $build['id'] ?>" class="btn btn-primary">Посмотреть</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <h2>Товары</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/300') ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="text-primary fw-bold"><?= $product['price'] ?> ₽</p>
                            <a href="make_order.php?id=<?= $product['id'] ?>" class="btn btn-primary">Купить</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=<?= $current_page - 1 ?>" class="btn btn-primary me-2">Предыдущая</a>
    <?php endif; ?>
    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?= $current_page + 1 ?>" class="btn btn-primary">Следующая</a>
    <?php endif; ?>
</div>
</div>
</body>
</html>
