<?php

require_once('BaseDAO.php');
require_once('../Entidades/Usuario.php');

class UsuarioDAO extends BaseDAO
{
    public function inserir($usuario)
{
    try {
        $sql = "INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)";
        $parametros = array(
            ":nome" => $usuario->getNome(),
            ":email" => $usuario->getEmail(),
            ":senha" => $usuario->getSenha(),
        );
        
        $stmt = $this->executaComParametros($sql, $parametros);
        $rowCount = $stmt->rowCount();
        
        if ($rowCount === 0) {
            throw new Exception("Nenhum registro inserido");
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro PDO: " . $e->getMessage());
        throw new Exception("Erro ao inserir usuário no banco de dados");
    }
}


    public function autenticar($email, $senha)
{
    $sql = "SELECT id_usuario AS id, nome, email, senha
            FROM usuario
            WHERE email = :email AND senha = :senha";
    $stmt = $this->executaComParametros($sql, [
        ':email' => $email,
        ':senha' => $senha
    ]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        return new Usuario(
            $resultado['nome'],
            $resultado['email'],
            $resultado['senha'],
            $resultado['id']
        );
    }
    return null;
}
}


