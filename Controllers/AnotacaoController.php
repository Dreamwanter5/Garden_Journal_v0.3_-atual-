<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . "/../Daos/notaDao.php");

// Simulação básica - substitua pela sua lógica real
class AnotacaoController
{
    private $notas_dao;



    public function __construct()
    {

        $this->notas_dao = new NotaDAO();

    }
    public function salvar()
    {
        try {
            // Verifica se o usuário está logado
            if (!isset($_SESSION['id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Usuário não autenticado']);
                return;
            }

            // Lê os dados JSON
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                http_response_code(400);
                echo json_encode(['error' => 'Dados inválidos']);
                return;
            }

            // Valida dados obrigatórios
            if (empty($input['titulo']) || empty($input['conteudo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Título e conteúdo são obrigatórios']);
                return;
            }

            // Aqui você salvaria no banco de dados
            // Por enquanto, apenas simula o salvamento

            $dadosSalvos = [
                'id' => uniqid(),
                'titulo' => $input['titulo'],
                'conteudo' => $input['conteudo'],
                'categorias' => $input['categorias'] ?? [],
                'usuario_id' => $_SESSION['id'],
                'data_criacao' => date('Y-m-d H:i:s')
            ];

            $this->notas_dao->

                // Simula sucesso
                http_response_code(200);
            echo json_encode([
                'mensagem' => 'Nota salva com sucesso!',
                'dados' => $dadosSalvos
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }
}

// Executa a ação
$acao = $_GET['acao'] ?? '';

if ($acao === 'salvar') {
    $controller = new AnotacaoController();
    $controller->salvar();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ação não encontrada']);
}
?>