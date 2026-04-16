<?php
session_start();
require '../db.php'; // Подключение к базе данных


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
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];


    if (empty($email) || empty($password)) {
        $errorMsg = "Заполните все поля!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Некорректный формат Email!";
    } elseif ($password !== $passwordConfirm) {
        $errorMsg = "Пароли не совпадают!";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

     
        $sql = "INSERT INTO users (email, password_hash) VALUES (:email, :hash)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([':email' => $email, ':hash' => $hash]);
            $successMsg = "Регистрация успешна! <a href='login.php'>Войти</a>";
            unset($_SESSION['csrf_token']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMsg = "Этот email уже зарегистрирован.";
            } else {
                $errorMsg = "Ошибка: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Регистрация</h4>
                </div>
                <div class="card-body">
                
                    <?php if($errorMsg): ?>
                        <div class="alert alert-danger"><?= $errorMsg ?></div>
                    <?php endif; ?>
                    <?php if($successMsg): ?>
                        <div class="alert alert-success"><?= $successMsg ?></div>
                    <?php endif; ?>

                   
                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                        </div>
                     
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
