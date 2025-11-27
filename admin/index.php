<?php
require_once __DIR__ . '/../functions.php';
ensureAdmin();

$total = $pdo->query('SELECT COUNT(*) as c FROM inscricoes')->fetch()['c'] ?? 0;
$pendente = $pdo->query("SELECT COUNT(*) as c FROM inscricoes WHERE status_pagamento = 'pendente'")->fetch()['c'] ?? 0;
$analise = $pdo->query("SELECT COUNT(*) as c FROM inscricoes WHERE status_pagamento = 'em_analise'")->fetch()['c'] ?? 0;
$pago = $pdo->query("SELECT COUNT(*) as c FROM inscricoes WHERE status_pagamento = 'pago'")->fetch()['c'] ?? 0;

include __DIR__ . '/header.php';
?>
<h2>Dashboard</h2>
<div class="table">
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <div style="background:#eef2ff; padding:12px; border-radius:8px; min-width:180px;">Total inscrições<br><strong><?php echo $total; ?></strong></div>
        <div style="background:#fff7ed; padding:12px; border-radius:8px; min-width:180px;">Pagamento pendente<br><strong><?php echo $pendente; ?></strong></div>
        <div style="background:#fefce8; padding:12px; border-radius:8px; min-width:180px;">Em análise<br><strong><?php echo $analise; ?></strong></div>
        <div style="background:#ecfdf3; padding:12px; border-radius:8px; min-width:180px;">Pagos<br><strong><?php echo $pago; ?></strong></div>
    </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
