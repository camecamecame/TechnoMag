<?php
require '../db.php';
require 'check_admin.php'; // Только для админа!

$message = '';

// Получаем id товара из GET параметра
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id) {
    // Получаем данные о товаре из базы данных
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Обрабатываем форму редактирования
        $title = trim($_POST['title']);
        $price = $_POST['price'];
        $desc  = trim($_POST['description']);
        $img   = trim($_POST['image_url']);

        if (empty($title)) {
            $message = '<div class="alert alert-danger">Заполните название!</div>';
        } else {
            // Обновляем товар в базе данных
            $sql = "UPDATE products SET title = :title, description = :description, price = :price, image_url = :image_url WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':title' => $title, ':description' => $desc, ':price' => $price, ':image_url' => $img, ':id' => $product_id]);
            $message = '<div class="alert alert-success">Товар успешно обновлен!</div>';
        }
    }
} else {
    // Если id товара не передан, перенаправляем на панель администратора
    header('Location: admin_panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать товар</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Редактировать товар</h1>
        <a href="admin_panel.php" class="btn btn-secondary mb-3">← Назад</a>
        <?= $message ?>
        <form method="POST" class="card p-4">
            <input type="text" name="title" class="form-control mb-2" placeholder="Название" value="<?= htmlspecialchars($product['title']) ?>" required>
            <input type="number" name="price" class="form-control mb-2" placeholder="Цена" value="<?= $product['price'] ?>" step="0.01" required>
            <input type="text" name="image_url" class="form-control mb-2" placeholder="URL картинки" value="<?= htmlspecialchars($product['image_url']) ?>">
            <textarea name="description" class="form-control mb-2" placeholder="Характеристики"><?= htmlspecialchars($product['description']) ?></textarea>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</body>
</html>