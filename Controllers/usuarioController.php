<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . "/../Daos/usuarioDao.php");
require_once(__DIR__ . "/../Entidades/usuario.php");

header('Content-Type: application/json; charset=utf-8');

class UsuarioController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UsuarioDAO();
    }

    public function inserir()
    {
        $json = file_get_contents("php://input");
        error_log("[inserir] JSON bruto: " . $json);

        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['nome'], $data['email'], $data['senha'])) {
            http_response_code(400);
            echo json_encode(['mensagem' => 'Dados incompletos']);
            return;
        }

        $nome = trim($data['nome']);
        $email = trim($data['email']);
        $senha = trim($data['senha']);

        try {
            $usuario = new Usuario($nome, $email, $senha);
            $this->dao->inserir($usuario);
            http_response_code(201);
            echo json_encode(['mensagem' => 'Usuário cadastrado com sucesso']);
        } catch (Exception $e) {
            error_log("[inserir] Erro ao inserir usuário: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['mensagem' => 'Erro no servidor']);
        }
    }

    public function autenticar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $json = file_get_contents("php://input");
        error_log("[autenticar] JSON bruto: " . $json);

        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['email'], $data['senha'])) {
            http_response_code(400);
            echo json_encode(['mensagem' => 'Dados de login incompletos']);
            return;
        }

        $email = trim($data['email']);
        $senha = trim($data['senha']);

        try {
            $usuario = $this->dao->autenticar($email, $senha);
            if ($usuario !== null) {
                $_SESSION['id'] = $usuario->getid_usuario();
                $_SESSION['nome'] = $usuario->getNome();
                $_SESSION['email'] = $usuario->getEmail();
                echo json_encode(['mensagem' => 'Usuário autenticado com sucesso']);
            } else {
                http_response_code(401);
                echo json_encode(['mensagem' => 'Usuário/senha inválidos']);
            }
        } catch (Exception $e) {
            error_log("[autenticar] Erro: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['mensagem' => 'Erro no servidor']);
        }
    }
}

$acao = $_GET['acao'] ?? '';
$controller = new UsuarioController();

switch ($acao) {
    case 'inserir':
        $controller->inserir();
        break;
    case 'autenticar':
        $controller->autenticar();
        break;
    default:
        http_response_code(400);
        echo json_encode(['mensagem' => 'Ação inválida']);
        break;
}
