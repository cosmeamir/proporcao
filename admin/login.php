<?php
require_once __DIR__ . '/../functions.php';

if (!empty($_SESSION['admin_logged'])) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged'] = $user['id'];
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Credenciais inválidas.';
    }
}

include __DIR__ . '/header.php';
?>
<h2>Login do administrador</h2>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
<form method="POST">
    <label for="username">Utilizador</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Senha</label>
    <input type="password" name="password" id="password" required>

    <button class="btn" type="submit">Entrar</button>
</form>
<?php include __DIR__ . '/footer.php'; ?>
