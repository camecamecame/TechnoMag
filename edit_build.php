<?php
require '../db.php';
require 'check_admin.php'; 

$message = '';


$build_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($build_id) {

    $sql = "SELECT * FROM builds WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $build_id]);
    $build = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name']);
        $desc  = trim($_POST['description']);
        $price = $_POST['total_price'];

        if (empty($name)) {
            $message = '<div class="alert alert-danger">Заполните название!</div>';
        } else {

            $sql = "UPDATE builds SET name = :name, description = :description, total_price = :total_price WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $name, ':description' => $desc, ':total_price' => $price, ':id' => $build_id]);
            $message = '<div class="alert alert-success">Сборка успешно обновлена!</div>';
        }
    }
} else {

    header('Location: admin_panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать сборку</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Редактировать сборку</h1>
        <a href="admin_panel.php" class="btn btn-secondary mb-3">← Назад</a>
        <?= $message ?>
        <form method="POST" class="card p-4">
            <input type="text" name="name" class="form-control mb-2" placeholder="Название" value="<?= htmlspecialchars($build['name']) ?>" required>
            <input type="number" name="total_price" class="form-control mb-2" placeholder="Цена" value="<?= $build['total_price'] ?>" step="0.01" required>
            <textarea name="description" class="form-control mb-2" placeholder="Описание"><?= htmlspecialchars($build['description']) ?></textarea>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</body>
</html>
