<?php
require 'check_admin.php'; // Только админ!
require '../db.php';

// Получаем ID заказа из GET-параметра
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Проверяем, что ID заказа корректен
if ($order_id > 0) {
    // Выполняем запрос на удаление заказа
    $sql = "DELETE FROM orders WHERE id = :order_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':order_id' => $order_id]);

    // Перенаправляем на страницу управления заказами после удаления
    header("Location: admin_orders.php");
    exit;
} else {
    // Если ID заказа некорректен, перенаправляем обратно на страницу заказов
    header("Location: admin_orders.php");
    exit;
}