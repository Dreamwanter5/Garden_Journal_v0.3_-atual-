<?php
session_start();

require_once(__DIR__ . '/baseDao.php');
require_once(__DIR__ . '/notaDao.php');
require_once(__DIR__ . '/categoriaDao.php');
require_once(__DIR__ . '/../Entidades/Usuario.php');

if (!isset($_SESSION["id"])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Não autenticado']);
    exit();
}

$dados = json_decode(file_get_contents('php://input'), true);

if (empty($dados['titulo'])) {
    $dados['titulo'] = 'Anotação sem título ' . date('d/m/Y H:i');
}

try {
    $notaDAO = new NotaDAO();
    $notaDAO->salvar(
        $dados['titulo'],
        $dados['conteudo'] ?? '',
        $_SESSION['id'],
        $dados['categorias'] ?? []
    );

    echo json_encode(['status' => 'success', 'message' => 'Nota salva com sucesso!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}