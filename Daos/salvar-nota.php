<?php
session_start();

require_once(__DIR__ . 'baseDao.php');
require_once(__DIR__ . 'notaDao.php');
require_once(__DIR__ . 'categoriaDao.php');

header('Content-Type: application/json');

if (!isset($_SESSION["id"])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Não autenticado']);
    exit();
}

$input = file_get_contents('php://input');
$dados = json_decode($input, true);

if (!$dados) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
    exit();
}

// Validação mínima
if (empty($dados['titulo'])) {
    $dados['titulo'] = 'Nota sem título - ' . date('d/m/Y H:i');
}

try {
    $notaDAO = new NotaDAO();
    
    // Agora recebemos o HTML já convertido do JavaScript
    $idNota = $notaDAO->salvarNota(
        $dados['titulo'],
        $dados['conteudo_markdown'] ?? '',  // Markdown original
        $dados['conteudo_html'] ?? '',      // HTML já convertido
        $_SESSION['id'],
        $dados['categorias'] ?? []
    );

    echo json_encode([
        'status' => 'success', 
        'message' => 'Nota salva com sucesso!',
        'id_nota' => $idNota
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao salvar nota: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor']);
}