<?php
require_once __DIR__ . '/functions.php';

$codigo = sanitize($_GET['codigo'] ?? ($_POST['codigo'] ?? ''));
$inscricao = $codigo ? findInscricaoByCodigo($pdo, $codigo) : null;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$inscricao) {
        $error = 'Código de inscrição não encontrado.';
    } elseif (empty($_FILES['comprovativo']['name'])) {
        $error = 'Envie o ficheiro do comprovativo.';
    } else {
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($_FILES['comprovativo']['type'], $allowed, true)) {
            $error = 'Formato de ficheiro não permitido.';
        } else {
            $ext = pathinfo($_FILES['comprovativo']['name'], PATHINFO_EXTENSION);
            $fileRelative = '/storage/comprovativos/' . $inscricao['codigo'] . '.' . $ext;
            $targetPath = __DIR__ . $fileRelative;
            if (move_uploaded_file($_FILES['comprovativo']['tmp_name'], $targetPath)) {
                updateComprovativo($pdo, $inscricao['codigo'], $fileRelative);
                $message = 'Comprovativo enviado com sucesso. Assim que o pagamento for confirmado, enviaremos a confirmação por e-mail/WhatsApp.';
                $inscricao = findInscricaoByCodigo($pdo, $inscricao['codigo']);
            } else {
                $error = 'Não foi possível gravar o ficheiro.';
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<h2>Enviar comprovativo de pagamento</h2>
<p>Introduza o código da inscrição e envie o comprovativo em JPG, PNG ou PDF.</p>
<?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data">
    <label for="codigo">Código da inscrição</label>
    <input type="text" id="codigo" name="codigo" value="<?php echo $codigo; ?>" <?php echo $codigo ? 'readonly' : ''; ?> required>

    <label for="comprovativo">Comprovativo (JPG, PNG ou PDF)</label>
    <input type="file" id="comprovativo" name="comprovativo" accept=".jpg,.jpeg,.png,.pdf" required>

    <button class="btn" type="submit">Enviar</button>
</form>
<?php if ($inscricao && $inscricao['comprovativo_path']): ?>
    <p>Comprovativo atual: <a href="<?php echo $inscricao['comprovativo_path']; ?>" target="_blank">abrir</a></p>
<?php endif; ?>
<?php include __DIR__ . '/footer.php'; ?>
