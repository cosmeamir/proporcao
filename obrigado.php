<?php
require_once __DIR__ . '/functions.php';
$codigo = sanitize($_GET['codigo'] ?? '');
$inscricao = $codigo ? findInscricaoByCodigo($pdo, $codigo) : null;

if (!$inscricao) {
    http_response_code(404);
    echo 'Inscrição não encontrada.';
    exit;
}

include __DIR__ . '/header.php';
?>
<h2>Obrigada, <?php echo sanitize($inscricao['nome']); ?>!</h2>
<p>A sua inscrição para o curso <strong><?php echo sanitize($inscricao['curso_nome']); ?></strong> foi registada.</p>
<p><strong>Código da inscrição:</strong> <?php echo sanitize($inscricao['codigo']); ?></p>
<p><strong>Valor:</strong> <?php echo formatCurrency((float) $inscricao['preco']); ?></p>
<pre style="background:#f8fafc; padding:12px; border-radius:6px;">Dados para pagamento:
<?php echo PAYMENT_DETAILS; ?></pre>
<p><a class="btn" href="<?php echo $inscricao['pdf_path']; ?>" target="_blank">Baixar comprovativo de pré-inscrição (PDF)</a></p>
<p><a class="btn btn-secondary" href="/comprovativo.php?codigo=<?php echo urlencode($inscricao['codigo']); ?>">Enviar comprovativo de pagamento</a></p>
<?php include __DIR__ . '/footer.php'; ?>
