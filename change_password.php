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

    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $newPasswordConfirm = $_POST['new_password_confirm'];

    // Валидация
    if (empty($oldPassword) || empty($newPassword)) {
        $errorMsg = "Заполните все поля!";
    } elseif ($newPassword !== $newPasswordConfirm) {
        $errorMsg = "Пароли не совпадают!";
    } else {

        $sql = "SELECT password_hash FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (password_verify($oldPassword, $user['password_hash'])) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password_hash = :newPassword WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':newPassword' => $newPasswordHash, ':user_id' => $_SESSION['user_id']]);
            $successMsg = "Пароль успешно изменен!";
            unset($_SESSION['csrf_token']); 
        } else {
            $errorMsg = "Неверный старый пароль.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сменить пароль</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Сменить пароль</h4>
                </div>
                <div class="card-body">
  
                    <?php if($errorMsg): ?>
                        <div class="alert alert-danger"><?= $errorMsg ?></div>
                    <?php endif; ?>
                    <?php if($successMsg): ?>
                        <div class="alert alert-success"><?= $successMsg ?></div>
                    <?php endif; ?>

            
                    <form method="POST" action="change_password.php">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Старый пароль</label>
                            <input type="password" name="old_password" id="old_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Новый пароль</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirm" class="form-label">Подтверждение нового пароля</label>
                            <input type="password" name="new_password_confirm" id="new_password_confirm" class="form-control" required>
                        </div>
                 
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <button type="submit" class="btn btn-primary">Изменить пароль</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
