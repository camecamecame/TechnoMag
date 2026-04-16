<?php
session_start();
require '../db.php';


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (!isset($_SESSION['user_id'])) {
    die("Сначала войдите в систему! <a href='login.php'>Вход</a>");
}

$product_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];


$check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$check->execute([$product_id]);
$exists = $check->fetch();

if (!$exists) {
    die("Ошибка: Попытка заказать несуществующий товар! Ваш IP записан.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка CSRF! Запрос не прошел проверку.");
    }


    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);


    $stmt_order = $pdo->prepare("INSERT INTO orders (user_id, product_id) VALUES (?, ?)");
    try {
        $stmt_order->execute([$user_id, $product_id]);
        $order_id = $pdo->lastInsertId(); 

  
        $stmt_customer = $pdo->prepare("INSERT INTO customers (order_id, full_name, phone, address, email) VALUES (?, ?, ?, ?, ?)");
        $stmt_customer->execute([$order_id, $full_name, $phone, $address, $email]);


        header("Location: profile.php");
        exit; 
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Оформление заказа</h1>
        <a href="index.php" class="btn btn-secondary mb-3">← На главную</a>

        <form method="POST" class="card p-4">
            <div class="mb-3">
                <label for="full_name" class="form-label">ФИО</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Номер телефона</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Адрес доставки</label>
                <textarea name="address" id="address" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Электронная почта</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>


            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <button type="submit" class="btn btn-success">Оформить заказ</button>
        </form>
    </div>
</body>
</html>
