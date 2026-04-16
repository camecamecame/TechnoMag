<?php
require '../db.php';
require 'check_admin.php'; // Только для админа!

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $price = $_POST['price'];
    $desc  = trim($_POST['description']);
    $img   = trim($_POST['image_url']);
    $type  = $_POST['type'];  
    $socket = $_POST['socket'];  

    if (empty($title)) {
        $message = '<div class="alert alert-danger">Заполните название!</div>';
    } else {
     
        $sql = "INSERT INTO products (title, description, price, image_url, type, socket) VALUES (:t, :d, :p, :i, :type, :socket)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':t' => $title, ':d' => $desc, ':p' => $price, ':i' => $img, ':type' => $type, ':socket' => $socket]);
        $message = '<div class="alert alert-success">Успешно добавлено!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Новый товар</h1>
        <a href="admin_panel.php" class="btn btn-secondary mb-3">← Назад</a>
        <?= $message ?>
        <form method="POST" class="card p-4">
            <input type="text" name="title" class="form-control mb-2" placeholder="Название" required>
            <input type="number" name="price" class="form-control mb-2" placeholder="Цена" step="0.01" required>
            <input type="text" name="image_url" class="form-control mb-2" placeholder="URL картинки">
            <textarea name="description" class="form-control mb-2" placeholder="Характеристики"></textarea>

   
            <select name="type" class="form-control mb-2" required>
                <option value="CPU">Процессор</option>
                <option value="GPU">Видеокарта</option>
                <option value="Motherboard">Материнская плата</option>
                <option value="RAM">Оперативная память</option>
                <option value="Storage">Накопитель</option>
                <option value="PowerSupply">Блок питания</option>
                <option value="Case">Корпус</option>
            </select>

      
            <select name="socket" class="form-control mb-2">
                <option value="">Выберите сокет (если применимо)</option>
                <option value="LGA1700">LGA 1700</option>
                <option value="AM4">AM4</option>
                <option value="LGA1200">LGA 1200</option>
                <option value="AM5">AM5</option>
       
            </select>

            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</body>
</html>
