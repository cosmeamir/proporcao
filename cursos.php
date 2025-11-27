<?php
require_once __DIR__ . '/functions.php';
$cursos = getCursos($pdo);
include __DIR__ . '/header.php';
?>
<h2>Cursos</h2>
<?php if (empty($cursos)): ?>
    <p>Em breve adicionaremos novos cursos.</p>
<?php else: ?>
    <?php foreach ($cursos as $curso): ?>
        <div style="border:1px solid #e5e7eb; padding:16px; margin-bottom:12px; border-radius:8px;">
            <h3><?php echo sanitize($curso['nome']); ?></h3>
            <p><?php echo nl2br(sanitize($curso['descricao_curta'])); ?></p>
            <p><strong>Data:</strong> <?php echo $curso['proxima_data'] ? date('d/m/Y', strtotime($curso['proxima_data'])) : 'A agendar'; ?></p>
            <p><strong>Preço:</strong> <?php echo formatCurrency((float) $curso['preco']); ?></p>
            <a class="btn" href="/inscricao.php?id_curso=<?php echo $curso['id']; ?>">Inscrever-se</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php include __DIR__ . '/footer.php'; ?>
