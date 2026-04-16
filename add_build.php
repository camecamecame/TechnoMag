<?php
require '../db.php';
require 'check_admin.php'; // Только для админа!

$message = '';


$sql_processors = "SELECT id, title FROM products WHERE type = 'CPU'";
$sql_motherboards = "SELECT id, title FROM products WHERE type = 'Motherboard'";
$sql_ram = "SELECT id, title FROM products WHERE type = 'RAM'";
$sql_gpu = "SELECT id, title FROM products WHERE type = 'GPU'";
$sql_storage = "SELECT id, title FROM products WHERE type = 'Storage'";
$sql_psu = "SELECT id, title FROM products WHERE type = 'PowerSupply'";
$sql_case = "SELECT id, title FROM products WHERE type = 'Case'";

$stmt_processors = $pdo->query($sql_processors);
$processors = $stmt_processors->fetchAll();

$stmt_motherboards = $pdo->query($sql_motherboards);
$motherboards = $stmt_motherboards->fetchAll();

$stmt_ram = $pdo->query($sql_ram);
$ram = $stmt_ram->fetchAll();

$stmt_gpu = $pdo->query($sql_gpu);
$gpu = $stmt_gpu->fetchAll();

$stmt_storage = $pdo->query($sql_storage);
$storage = $stmt_storage->fetchAll();

$stmt_psu = $pdo->query($sql_psu);
$psu = $stmt_psu->fetchAll();

$stmt_case = $pdo->query($sql_case);
$case = $stmt_case->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $total_price = 0;


    $cpu_id = $_POST['cpu'];
    $motherboard_id = $_POST['motherboard'];
    $ram_id = $_POST['ram'];
    $gpu_id = $_POST['gpu'];
    $storage_id = $_POST['storage'];
    $psu_id = $_POST['psu'];
    $case_id = $_POST['case'];


    if (empty($name)) {
        $message = '<div class="alert alert-danger">Заполните название сборки!</div>';
    } else {

        $sql = "INSERT INTO builds (name, description, total_price) VALUES (:name, :description, :total_price)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name' => $name, ':description' => $description, ':total_price' => $total_price]);
        $build_id = $pdo->lastInsertId();


        $items = [$cpu_id, $motherboard_id, $ram_id, $gpu_id, $storage_id, $psu_id, $case_id];
        foreach ($items as $item_id) {
            if ($item_id) {

                $sql_item = "SELECT price FROM products WHERE id = :id";
                $stmt_item = $pdo->prepare($sql_item);
                $stmt_item->execute([':id' => $item_id]);
                $product = $stmt_item->fetch();
                $total_price += $product['price'];


                $sql_build_item = "INSERT INTO build_items (build_id, product_id) VALUES (:build_id, :product_id)";
                $stmt_build_item = $pdo->prepare($sql_build_item);
                $stmt_build_item->execute([':build_id' => $build_id, ':product_id' => $item_id]);
            }
        }

       
        $sql_update_price = "UPDATE builds SET total_price = :total_price WHERE id = :build_id";
        $stmt_update_price = $pdo->prepare($sql_update_price);
        $stmt_update_price->execute([':total_price' => $total_price, ':build_id' => $build_id]);

        $message = '<div class="alert alert-success">Сборка успешно добавлена!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить сборку</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1>Добавить новую сборку</h1>
        <?= $message ?>
        
  
        <a href="admin_panel.php" class="btn btn-secondary mb-3">← Назад в админ-панель</a>
        
        <form method="POST" class="card p-4">
            <div class="mb-3">
                <label for="name" class="form-label">Название сборки</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание сборки</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label for="cpu" class="form-label">Выберите процессор</label>
                <select name="cpu" class="form-control" required>
                    <?php foreach ($processors as $processor): ?>
                        <option value="<?= $processor['id'] ?>"><?= $processor['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="motherboard" class="form-label">Выберите материнскую плату</label>
                <select name="motherboard" class="form-control" required>
                    <?php foreach ($motherboards as $motherboard): ?>
                        <option value="<?= $motherboard['id'] ?>"><?= $motherboard['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="ram" class="form-label">Выберите оперативную память</label>
                <select name="ram" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php foreach ($ram as $memory): ?>
                        <option value="<?= $memory['id'] ?>"><?= $memory['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="gpu" class="form-label">Выберите видеокарту</label>
                <select name="gpu" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php foreach ($gpu as $graphics_card): ?>
                        <option value="<?= $graphics_card['id'] ?>"><?= $graphics_card['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="storage" class="form-label">Выберите накопитель</label>
                <select name="storage" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php foreach ($storage as $disk): ?>
                        <option value="<?= $disk['id'] ?>"><?= $disk['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="psu" class="form-label">Выберите блок питания</label>
                <select name="psu" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php foreach ($psu as $power_supply): ?>
                        <option value="<?= $power_supply['id'] ?>"><?= $power_supply['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="case" class="form-label">Выберите корпус</label>
                <select name="case" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php foreach ($case as $pc_case): ?>
                        <option value="<?= $pc_case['id'] ?>"><?= $pc_case['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Добавить сборку</button>
        </form>
    </div>
</body>
</html>
