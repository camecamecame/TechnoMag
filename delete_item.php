<?php
session_start();
require '../db.php';


if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещен");
}


if ($_SESSION['user_role'] !== 'admin') {
    die("Доступ запрещен");
}



if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Ошибка ID");
}

$id = (int)$_POST['id'];


$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);


header("Location: index.php");
exit;
