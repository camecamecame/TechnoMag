<?php

session_start();
require '../db.php';


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  
}

$errorMsg = '';
$successMsg = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка CSRF! Запрос не прошел проверку.");
    }


    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if (empty($email) || empty($pass)) {
        $errorMsg = "Заполните все поля!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Некорректный формат Email!";
    } else {


        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password_hash'])) {


            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];


            if ($user['role'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: index.php"); 
            }
            exit;

        } else {
            $errorMsg = "Неверный email или пароль!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Вход</h4>
                </div>
                <div class="card-body">


                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger"><?= $errorMsg ?></div>
                    <?php endif; ?>

        
                    <form method="POST" action="login.php">
                        
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Email адрес</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Войти</button>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="register.php">Нет аккаунта? Зарегистрироваться</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
