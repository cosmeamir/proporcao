<?php
require_once __DIR__ . '/../functions.php';
ensureAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT i.*, c.nome as curso_nome, c.preco, c.proxima_data FROM inscricoes i INNER JOIN cursos c ON c.id = i.id_curso WHERE i.id = :id');
$stmt->execute(['id' => $id]);
$inscricao = $stmt->fetch();

if (!$inscricao) {
    echo 'Inscrição não encontrada';
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status_pagamento'] ?? 'pendente';
    updateStatus($pdo, $inscricao['id'], $status);
    $stmt->execute(['id' => $id]);
    $inscricao = $stmt->fetch();
    $message = 'Status atualizado com sucesso.';
}

include __DIR__ . '/header.php';
?>
<h2>Inscrição <?php echo sanitize($inscricao['codigo']); ?></h2>
<?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
<p><strong>Aluno:</strong> <?php echo sanitize($inscricao['nome']); ?> (<?php echo sanitize($inscricao['email']); ?>)</p>
<p><strong>Telefone:</strong> <?php echo sanitize($inscricao['telefone']); ?></p>
<p><strong>Curso:</strong> <?php echo sanitize($inscricao['curso_nome']); ?> - <?php echo formatCurrency((float) $inscricao['preco']); ?></p>
<p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></p>
<p><strong>Pagamento:</strong> <span class="badge <?php echo $inscricao['status_pagamento']; ?>"><?php echo str_replace('_', ' ', $inscricao['status_pagamento']); ?></span></p>
<p><strong>Pré-inscrição:</strong> <?php if ($inscricao['pdf_path']): ?><a href="<?php echo $inscricao['pdf_path']; ?>" target="_blank">Baixar PDF</a><?php else: ?>N/A<?php endif; ?></p>
<p><strong>Comprovativo:</strong> <?php if ($inscricao['comprovativo_path']): ?><a href="<?php echo $inscricao['comprovativo_path']; ?>" target="_blank">Abrir</a><?php else: ?>Ainda não enviado<?php endif; ?></p>

<form method="POST">
    <label for="status_pagamento">Status de pagamento</label>
    <select name="status_pagamento" id="status_pagamento">
        <?php foreach (['pendente' => 'Pendente', 'em_analise' => 'Em análise', 'pago' => 'Pago', 'cancelado' => 'Cancelado'] as $key => $label): ?>
            <option value="<?php echo $key; ?>" <?php echo $inscricao['status_pagamento'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>
    <label><input type="checkbox" name="notify" value="1"> Enviar e-mail ao marcar como pago? (personalizar via PHPMailer)</label>
    <button class="btn" type="submit">Atualizar status</button>
</form>
<?php include __DIR__ . '/footer.php'; ?>
