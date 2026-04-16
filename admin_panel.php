<?php
require '../db.php';
require 'check_admin.php'; // Только для админа!


$sql_products = "SELECT * FROM products ORDER BY id DESC";
$stmt_products = $pdo->query($sql_products);
$products = $stmt_products->fetchAll();

$sql_builds = "SELECT * FROM builds ORDER BY id DESC";
$stmt_builds = $pdo->query($sql_builds);
$builds = $stmt_builds->fetchAll();


if (isset($_GET['delete_product_id'])) {
    $delete_product_id = (int)$_GET['delete_product_id'];


    $delete_sql = "DELETE FROM products WHERE id = :id";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->execute([':id' => $delete_product_id]);


    header('Location: admin_panel.php');
    exit();
}


if (isset($_GET['delete_build_id'])) {
    $delete_build_id = (int)$_GET['delete_build_id'];


    $delete_sql = "DELETE FROM builds WHERE id = :id";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->execute([':id' => $delete_build_id]);


    header('Location: admin_panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
   <div class="alert alert-success">
        <h1>Панель Администратора</h1>
        <p>Добро пожаловать в систему управления.</p>
        <a href="add_item.php" class="btn btn-primary">Добавить товар</a>
        <a href="add_build.php" class="btn btn-primary">Добавить сборку</a>
        <a href="admin_orders.php" class="btn btn-primary">Посмотреть заказы</a>

        <a href="index.php" class="btn btn-primary">На главную</a>
        
        <a href="login.php" class="btn btn-danger">Выйти</a>
    </div>

        <h3>Все товары</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['title']) ?></td>
                        <td><?= number_format($product['price'], 0, '.', ' ') ?> ₽</td>
                        <td>
                            <a href="edit_item.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm">Редактировать</a>
    
                            <a href="admin_panel.php?delete_product_id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот товар?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Все сборки</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($builds as $build): ?>
                    <tr>
                        <td><?= $build['id'] ?></td>
                        <td><?= htmlspecialchars($build['name']) ?></td>
                        <td><?= number_format($build['total_price'], 0, '.', ' ') ?> ₽</td>
                        <td>
                            <a href="edit_build.php?id=<?= $build['id'] ?>" class="btn btn-warning btn-sm">Редактировать</a>
             
                            <a href="admin_panel.php?delete_build_id=<?= $build['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту сборку?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
