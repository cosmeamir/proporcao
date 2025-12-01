<?php
require_once __DIR__ . '/../functions.php';
ensureAdmin();

if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $curso = getCurso($pdo, $id);
    if ($curso) {
        $stmt = $pdo->prepare('UPDATE cursos SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => $curso['ativo'] ? 0 : 1, 'id' => $id]);
    }
    header('Location: /admin/cursos.php');
    exit;
}

$cursos = getCursos($pdo, true);
include __DIR__ . '/header.php';
?>
<h2>Cursos</h2>
<p><a class="btn" href="/admin/curso_form.php">Novo curso</a></p>
<table class="table">
    <thead>
    <tr>
        <th>Nome</th>
        <th>Data</th>
        <th>Preço</th>
        <th>Ativo</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($cursos as $curso): ?>
        <tr>
            <td><?php echo sanitize($curso['nome']); ?></td>
            <td><?php echo $curso['proxima_data'] ? date('d/m/Y', strtotime($curso['proxima_data'])) : 'A agendar'; ?></td>
            <td><?php echo formatCurrency((float) $curso['preco']); ?></td>
            <td><?php echo $curso['ativo'] ? 'Sim' : 'Não'; ?></td>
            <td>
                <a class="btn btn-secondary" href="/admin/curso_form.php?id=<?php echo $curso['id']; ?>">Editar</a>
                <a class="btn" href="/admin/cursos.php?toggle=<?php echo $curso['id']; ?>"><?php echo $curso['ativo'] ? 'Desativar' : 'Ativar'; ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/footer.php'; ?>
