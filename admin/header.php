<?php require_once __DIR__ . '/../functions.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<header>
    <div class="navbar">
        <h1>Administração</h1>
        <div>
            <?php if (!empty($_SESSION['admin_logged'])): ?>
                <a href="/admin/index.php">Dashboard</a>
                <a href="/admin/cursos.php">Cursos</a>
                <a href="/admin/inscricoes.php">Inscrições</a>
                <a href="/admin/logout.php">Sair</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>
