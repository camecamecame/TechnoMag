<?php
require 'check_admin.php'; // Только админ!
require '../db.php';


$sql = "
    SELECT 
        orders.id as order_id,
        orders.created_at,
        users.email,
        products.title,
        products.price
    FROM orders
    JOIN users ON orders.user_id = users.id
    JOIN products ON orders.product_id = products.id
    ORDER BY orders.id DESC
";

$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Управление заказами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="alert alert-success">
        <h1>Все заказы</h1>
        <a href="index.php" class="btn btn-primary">На главную</a>
        <a href="admin_panel.php" class="btn btn-primary">В админ-панель</a>
    </div>
    
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID Заказа</th>
                <th>Дата</th>
                <th>Клиент (Email)</th>
                <th>Товар</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['created_at'] ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['title']) ?></td>
                <td><?= $order['price'] ?> ₽</td>
                <td>
             
                    <a href="delete_order.php?id=<?= $order['order_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот заказ?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
