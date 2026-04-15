<?php
session_start();
require '../db.php';

/* Проверка авторизации */
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещен");
}

/* Только администратор может удалять */
if ($_SESSION['user_role'] !== 'admin') {
    die("Доступ запрещен");
}


/* Проверяем id */
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Ошибка ID");
}

$id = (int)$_POST['id'];

/* Удаление */
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

/* Возврат обратно */
header("Location: index.php");
exit;