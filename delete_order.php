<?php
require 'check_admin.php'; 
require '../db.php';


$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if ($order_id > 0) {

    $sql = "DELETE FROM orders WHERE id = :order_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':order_id' => $order_id]);


    header("Location: admin_orders.php");
    exit;
} else {

    header("Location: admin_orders.php");
    exit;
}
