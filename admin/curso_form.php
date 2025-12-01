<?php
require_once __DIR__ . '/../functions.php';
ensureAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$curso = $id ? getCurso($pdo, $id) : null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome'] ?? '');
    $descricaoCurta = $_POST['descricao_curta'] ?? '';
    $descricaoCompleta = $_POST['descricao_completa'] ?? '';
    $cargaHoraria = sanitize($_POST['carga_horaria'] ?? '');
    $nivel = sanitize($_POST['nivel'] ?? '');
    $preco = (float) ($_POST['preco'] ?? 0);
    $proximaData = $_POST['proxima_data'] ?: null;
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if (!$nome) { $errors[] = 'Nome é obrigatório.'; }

    if (empty($errors)) {
        if ($curso) {
            $stmt = $pdo->prepare('UPDATE cursos SET nome=:nome, descricao_curta=:descricao_curta, descricao_completa=:descricao_completa, carga_horaria=:carga_horaria, nivel=:nivel, preco=:preco, proxima_data=:proxima_data, ativo=:ativo WHERE id=:id');
            $stmt->execute([
                'nome' => $nome,
                'descricao_curta' => $descricaoCurta,
                'descricao_completa' => $descricaoCompleta,
                'carga_horaria' => $cargaHoraria,
                'nivel' => $nivel,
                'preco' => $preco,
                'proxima_data' => $proximaData,
                'ativo' => $ativo,
                'id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO cursos (nome, descricao_curta, descricao_completa, carga_horaria, nivel, preco, proxima_data, ativo) VALUES (:nome, :descricao_curta, :descricao_completa, :carga_horaria, :nivel, :preco, :proxima_data, :ativo)');
            $stmt->execute([
                'nome' => $nome,
                'descricao_curta' => $descricaoCurta,
                'descricao_completa' => $descricaoCompleta,
                'carga_horaria' => $cargaHoraria,
                'nivel' => $nivel,
                'preco' => $preco,
                'proxima_data' => $proximaData,
                'ativo' => $ativo,
            ]);
        }
        header('Location: /admin/cursos.php');
        exit;
    }
}

include __DIR__ . '/header.php';
?>
<h2><?php echo $curso ? 'Editar curso' : 'Novo curso'; ?></h2>
<?php if ($errors): ?><div class="alert alert-error"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
<form method="POST">
    <label for="nome">Nome</label>
    <input type="text" name="nome" id="nome" required value="<?php echo $curso['nome'] ?? ''; ?>">

    <label for="descricao_curta">Descrição curta</label>
    <textarea name="descricao_curta" id="descricao_curta" rows="3"><?php echo $curso['descricao_curta'] ?? ''; ?></textarea>

    <label for="descricao_completa">Descrição completa</label>
    <textarea name="descricao_completa" id="descricao_completa" rows="5"><?php echo $curso['descricao_completa'] ?? ''; ?></textarea>

    <label for="carga_horaria">Carga horária</label>
    <input type="text" name="carga_horaria" id="carga_horaria" value="<?php echo $curso['carga_horaria'] ?? ''; ?>">

    <label for="nivel">Nível</label>
    <input type="text" name="nivel" id="nivel" value="<?php echo $curso['nivel'] ?? ''; ?>">

    <label for="preco">Preço</label>
    <input type="number" step="0.01" name="preco" id="preco" value="<?php echo $curso['preco'] ?? ''; ?>">

    <label for="proxima_data">Próxima data</label>
    <input type="date" name="proxima_data" id="proxima_data" value="<?php echo $curso['proxima_data'] ?? ''; ?>">

    <label><input type="checkbox" name="ativo" <?php echo isset($curso['ativo']) ? ($curso['ativo'] ? 'checked' : '') : 'checked'; ?>> Curso ativo</label>

    <button class="btn" type="submit">Guardar</button>
</form>
<?php include __DIR__ . '/footer.php'; ?>
