<?php
require_once __DIR__ . '/config.php';

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function generateInscricaoCodigo(PDO $pdo): string
{
    $year = date('Y');
    $stmt = $pdo->query('SELECT MAX(id) as last_id FROM inscricoes');
    $lastId = (int) ($stmt->fetch()['last_id'] ?? 0);
    $next = $lastId + 1;
    return sprintf('PA-%s-%04d', $year, $next);
}

function getCurso(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM cursos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $curso = $stmt->fetch();
    return $curso ?: null;
}

function getCursos(PDO $pdo, bool $includeInactive = false): array
{
    $sql = 'SELECT * FROM cursos';
    if (!$includeInactive) {
        $sql .= ' WHERE ativo = 1';
    }
    $sql .= ' ORDER BY proxima_data ASC, nome ASC';
    return $pdo->query($sql)->fetchAll();
}

function formatCurrency(float $value): string
{
    return '€ ' . number_format($value, 2, ',', '.');
}

function createInscricao(PDO $pdo, array $data): array
{
    $codigo = generateInscricaoCodigo($pdo);
    $stmt = $pdo->prepare('INSERT INTO inscricoes (codigo, id_curso, nome, email, telefone, como_conheceu, data_inscricao, pdf_path, status_pagamento) VALUES (:codigo, :id_curso, :nome, :email, :telefone, :como_conheceu, NOW(), :pdf_path, :status_pagamento)');
    $stmt->execute([
        'codigo' => $codigo,
        'id_curso' => $data['id_curso'],
        'nome' => $data['nome'],
        'email' => $data['email'],
        'telefone' => $data['telefone'],
        'como_conheceu' => $data['como_conheceu'],
        'pdf_path' => $data['pdf_path'] ?? null,
        'status_pagamento' => 'pendente',
    ]);

    $data['codigo'] = $codigo;
    $data['id'] = (int) $pdo->lastInsertId();
    return $data;
}

function findInscricaoByCodigo(PDO $pdo, string $codigo): ?array
{
    $stmt = $pdo->prepare('SELECT i.*, c.nome as curso_nome, c.preco, c.proxima_data, c.carga_horaria FROM inscricoes i INNER JOIN cursos c ON i.id_curso = c.id WHERE i.codigo = :codigo');
    $stmt->execute(['codigo' => $codigo]);
    $result = $stmt->fetch();
    return $result ?: null;
}

function updateInscricaoPdf(PDO $pdo, int $id, string $pdfPath): void
{
    $stmt = $pdo->prepare('UPDATE inscricoes SET pdf_path = :pdf_path WHERE id = :id');
    $stmt->execute(['pdf_path' => $pdfPath, 'id' => $id]);
}

function updateComprovativo(PDO $pdo, string $codigo, string $path): void
{
    $stmt = $pdo->prepare('UPDATE inscricoes SET comprovativo_path = :path, status_pagamento = "em_analise" WHERE codigo = :codigo');
    $stmt->execute(['path' => $path, 'codigo' => $codigo]);
}

function updateStatus(PDO $pdo, int $id, string $status): void
{
    $stmt = $pdo->prepare('UPDATE inscricoes SET status_pagamento = :status WHERE id = :id');
    $stmt->execute(['status' => $status, 'id' => $id]);
}

function ensureAdmin(): void
{
    if (empty($_SESSION['admin_logged'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function renderFooter(): void
{
    echo '<footer><p>Desenvolvido e mantido por <a href="https://codigocosme.com" target="_blank">Código Cosme</a>.</p></footer>';
}
