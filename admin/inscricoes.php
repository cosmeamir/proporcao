<?php
require_once __DIR__ . '/../functions.php';
ensureAdmin();

$params = [];
$where = [];

if (!empty($_GET['curso'])) {
    $where[] = 'i.id_curso = :curso';
    $params['curso'] = (int) $_GET['curso'];
}
if (!empty($_GET['status'])) {
    $where[] = 'i.status_pagamento = :status';
    $params['status'] = $_GET['status'];
}
if (!empty($_GET['data_inicio'])) {
    $where[] = 'DATE(i.data_inscricao) >= :inicio';
    $params['inicio'] = $_GET['data_inicio'];
}
if (!empty($_GET['data_fim'])) {
    $where[] = 'DATE(i.data_inscricao) <= :fim';
    $params['fim'] = $_GET['data_fim'];
}

$sql = 'SELECT i.*, c.nome as curso_nome FROM inscricoes i INNER JOIN cursos c ON c.id = i.id_curso';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY i.data_inscricao DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inscricoes = $stmt->fetchAll();
$cursos = getCursos($pdo, true);

include __DIR__ . '/header.php';
?>
<h2>Inscrições</h2>
<form method="GET" style="margin-bottom:16px; display:flex; gap:8px; flex-wrap:wrap;">
    <select name="curso">
        <option value="">Todos os cursos</option>
        <?php foreach ($cursos as $curso): ?>
            <option value="<?php echo $curso['id']; ?>" <?php echo (($_GET['curso'] ?? '') == $curso['id']) ? 'selected' : ''; ?>><?php echo sanitize($curso['nome']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status">
        <option value="">Todos os status</option>
        <?php foreach (['pendente' => 'Pendente', 'em_analise' => 'Em análise', 'pago' => 'Pago', 'cancelado' => 'Cancelado'] as $key => $label): ?>
            <option value="<?php echo $key; ?>" <?php echo (($_GET['status'] ?? '') === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="data_inicio" value="<?php echo $_GET['data_inicio'] ?? ''; ?>">
    <input type="date" name="data_fim" value="<?php echo $_GET['data_fim'] ?? ''; ?>">
    <button class="btn" type="submit">Filtrar</button>
</form>

<table class="table">
    <thead>
    <tr>
        <th>Código</th>
        <th>Aluno</th>
        <th>Curso</th>
        <th>Data</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($inscricoes as $inscricao): ?>
        <tr>
            <td><?php echo sanitize($inscricao['codigo']); ?></td>
            <td><?php echo sanitize($inscricao['nome']); ?></td>
            <td><?php echo sanitize($inscricao['curso_nome']); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></td>
            <td><span class="badge <?php echo $inscricao['status_pagamento']; ?>"><?php echo str_replace('_', ' ', $inscricao['status_pagamento']); ?></span></td>
            <td>
                <a class="btn btn-secondary" href="/admin/inscricao_view.php?id=<?php echo $inscricao['id']; ?>">Ver</a>
                <?php if ($inscricao['pdf_path']): ?><a class="btn" href="<?php echo $inscricao['pdf_path']; ?>" target="_blank">PDF</a><?php endif; ?>
                <?php if ($inscricao['comprovativo_path']): ?><a class="btn" href="<?php echo $inscricao['comprovativo_path']; ?>" target="_blank">Comprovativo</a><?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/footer.php'; ?>
