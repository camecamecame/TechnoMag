<?php

session_start();
require '../db.php';


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "
    SELECT 
        orders.id as order_id, 
        orders.created_at, 
        orders.status, 
        products.title, 
        products.price,
        products.image_url
    FROM orders 
    JOIN products ON orders.product_id = products.id 
    WHERE orders.user_id = ? 
    ORDER BY orders.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$my_orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Мой Проект</a>
            <div class="d-flex">
                <a href="change_password.php" class="btn btn-warning btn-sm me-2">
                    Сменить пароль
                </a>
                <span class="navbar-text text-white me-3">
                    Вы вошли как: <b><?= htmlspecialchars($_SESSION['user_role'] ?? 'User') ?></b>
                </span>
                <a href="login.php" class="btn btn-outline-light btn-sm">Выйти</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="mb-0">Мои заказы</h2>
                    </div>
                    <div class="card-body">
                        

                        <?php if (count($my_orders) > 0): ?>
                            
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>№ Заказа</th>
                                            <th>Дата</th>
                                            <th>Товар</th>
                                            <th>Цена</th>
                                            <th>Статус</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($my_orders as $order): ?>
                                            <tr>
                                 
                                                <td>#<?= $order['order_id'] ?></td>
                                                
                                     
                                                <td>
                                                    <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                                </td>
                                                
                                            
                                                <td>
                                                    <strong><?= htmlspecialchars($order['title']) ?></strong>
                                                </td>
                                                
                                          
                                                <td><?= number_format($order['price'], 0, '', ' ') ?> ₽</td>
                                                
                                        
                                                <td>
                                                    <?php 
                                        
                                                    $status_color = 'secondary';
                                                    if ($order['status'] == 'new') $status_color = 'primary';
                                                    if ($order['status'] == 'processing') $status_color = 'warning';
                                                    if ($order['status'] == 'done') $status_color = 'success';
                                                    ?>
                                                    <span class="badge bg-<?= $status_color ?>">
                                                        <?= htmlspecialchars($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>
                   
                            <div class="text-center py-5">
                                <h4 class="text-muted">Вы еще ничего не заказывали.</h4>
                                <a href="index.php" class="btn btn-primary mt-3">Перейти в каталог</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <form method="POST" action="delete_order.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    </form>

</body>
</html>
