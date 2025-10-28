<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// load entity first so DAOs that type-hint it won't fail
require_once(__DIR__ . '/../Entidades/nota.php');
require_once(__DIR__ . '/../Daos/notaDao.php');
require_once(__DIR__ . '/../Daos/categoriaDao.php');

session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['mensagem' => 'Não autenticado']);
    exit();
}

$acao = $_GET['acao'] ?? '';

if ($acao !== 'salvar') {
    http_response_code(400);
    echo json_encode(['mensagem' => 'Ação inválida']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$titulo = trim($input['titulo'] ?? '');
$descricao = $input['descricao'] ?? '';
$conteudo = $input['conteudo'] ?? '';
$conteudo_html = $input['conteudo_html'] ?? '';
$categorias = $input['categorias'] ?? [];

if ($titulo === '') {
    http_response_code(400);
    echo json_encode(['mensagem' => 'Título obrigatório']);
    exit();
}

try {
    // montar entidade Nota
    $nota = new Nota(
        $titulo,
        $descricao,
        $conteudo,
        (int) $_SESSION['id'],
        (array) $categorias,
        $conteudo_html
    );

    $notaDao = new NotaDAO();
    error_log('[AnotacaoController::salvar] Input: ' . print_r($input, true));

    $idNota = $notaDao->salvar($nota);
    echo json_encode(['mensagem' => 'Nota salva com sucesso', 'id_nota' => $idNota]);
} catch (Exception $e) {
    // log completo e resposta JSON com a mensagem para facilitar depuração
    error_log('[AnotacaoController::salvar] ERROR message: ' . $e->getMessage());
    error_log('[AnotacaoController::salvar] TRACE: ' . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro ao salvar nota', 'erro' => $e->getMessage()]);
}