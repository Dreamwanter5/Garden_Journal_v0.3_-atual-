<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

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
$conteudo = $input['conteudo'] ?? '';
$categorias = $input['categorias'] ?? []; // array de ids esperados

if ($titulo === '') {
    http_response_code(400);
    echo json_encode(['mensagem' => 'Título obrigatório']);
    exit();
}

try {
    $notaDao = new NotaDAO();
    // Log the incoming data
    error_log('[AnotacaoController::salvar] Input data: ' . print_r($input, true));

    $categoriasIds = array_values(array_map('intval', (array) $categorias));
    // Log the processed categories
    error_log('[AnotacaoController::salvar] Categories: ' . print_r($categoriasIds, true));

    $idNota = $notaDao->salvar($titulo, $conteudo, (int) $_SESSION['id'], $categoriasIds);
    echo json_encode(['mensagem' => 'Nota salva com sucesso', 'id_nota' => $idNota]);
} catch (Exception $e) {
    error_log('[AnotacaoController::salvar] ERROR: ' . $e->getMessage());
    error_log('[AnotacaoController::salvar] Stack trace: ' . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro ao salvar nota', 'erro' => $e->getMessage()]);
}