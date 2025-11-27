<?php
require_once __DIR__ . '/functions.php';

$idCurso = isset($_GET['id_curso']) ? (int) $_GET['id_curso'] : 0;
$curso = $idCurso ? getCurso($pdo, $idCurso) : null;

if (!$curso || !$curso['ativo']) {
    http_response_code(404);
    echo 'Curso não encontrado.';
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $telefone = sanitize($_POST['telefone'] ?? '');
    $comoConheceu = sanitize($_POST['como_conheceu'] ?? '');
    $lgpd = isset($_POST['lgpd']);

    if (!$nome) { $errors[] = 'Informe o seu nome.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'E-mail inválido.'; }
    if (!$telefone) { $errors[] = 'Informe o telefone.'; }
    if (!$lgpd) { $errors[] = 'É necessário aceitar a política de privacidade.'; }

    if (empty($errors)) {
        $inscricao = createInscricao($pdo, [
            'id_curso' => $curso['id'],
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'como_conheceu' => $comoConheceu,
        ]);

        require_once __DIR__ . '/libs/fpdf.php';
        $pdfRelative = '/storage/inscricoes/' . $inscricao['codigo'] . '.pdf';
        $pdfFile = __DIR__ . $pdfRelative;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,utf8_decode(SITE_NAME),0,1);
        $pdf->Ln(5);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,8,utf8_decode('Pré-inscrição'),0,1);
        $pdf->Ln(3);
        $pdf->Cell(0,8,utf8_decode('Código: ' . $inscricao['codigo']),0,1);
        $pdf->Cell(0,8,utf8_decode('Aluno: ' . $nome),0,1);
        $pdf->Cell(0,8,utf8_decode('E-mail: ' . $email),0,1);
        $pdf->Cell(0,8,utf8_decode('Telefone: ' . $telefone),0,1);
        $pdf->Ln(4);
        $pdf->Cell(0,8,utf8_decode('Curso: ' . $curso['nome']),0,1);
        $pdf->Cell(0,8,utf8_decode('Próxima data: ' . ($curso['proxima_data'] ? date('d/m/Y', strtotime($curso['proxima_data'])) : 'A agendar')),0,1);
        $pdf->Cell(0,8,utf8_decode('Carga horária: ' . $curso['carga_horaria']),0,1);
        $pdf->Cell(0,8,utf8_decode('Valor: ' . formatCurrency((float)$curso['preco'])),0,1);
        $pdf->Ln(6);
        $pdf->MultiCell(0,8,utf8_decode("Dados para pagamento:\n" . PAYMENT_DETAILS));
        $pdf->Ln(4);
        $pdf->MultiCell(0,7,utf8_decode('Observações: efetue o pagamento em até 48h e envie o comprovativo pelo site. Em caso de desistência, contacte-nos com antecedência.'));
        $pdf->Output('F', $pdfFile);

        updateInscricaoPdf($pdo, $inscricao['id'], $pdfRelative);

        header('Location: /obrigado.php?codigo=' . urlencode($inscricao['codigo']));
        exit;
    }
}

include __DIR__ . '/header.php';
?>
<h2>Inscrição - <?php echo sanitize($curso['nome']); ?></h2>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $e): ?>
            <div><?php echo $e; ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="POST">
    <label for="nome">Nome</label>
    <input type="text" name="nome" id="nome" required value="<?php echo $_POST['nome'] ?? ''; ?>">

    <label for="email">E-mail</label>
    <input type="email" name="email" id="email" required value="<?php echo $_POST['email'] ?? ''; ?>">

    <label for="telefone">Telefone</label>
    <input type="text" name="telefone" id="telefone" required value="<?php echo $_POST['telefone'] ?? ''; ?>">

    <label for="como_conheceu">Como nos conheceu? (opcional)</label>
    <input type="text" name="como_conheceu" id="como_conheceu" value="<?php echo $_POST['como_conheceu'] ?? ''; ?>">

    <p><strong>Curso selecionado:</strong> <?php echo sanitize($curso['nome']); ?> (<?php echo formatCurrency((float) $curso['preco']); ?>)</p>

    <label><input type="checkbox" name="lgpd" value="1" <?php echo isset($_POST['lgpd']) ? 'checked' : ''; ?>> Aceito a política de privacidade e o tratamento dos meus dados.</label>

    <button class="btn" type="submit">Confirmar inscrição</button>
</form>
<?php include __DIR__ . '/footer.php'; ?>
