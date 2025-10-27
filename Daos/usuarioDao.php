<?php

require_once('baseDao.php');
require_once('../Entidades/usuario.php');

class UsuarioDAO extends BaseDAO
{
    public function inserir($usuario)
    {
        try {
            $sql = "INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)";
            $parametros = array(
                ":nome" => $usuario->getNome(),
                ":email" => $usuario->getEmail(),
                // hash da senha antes de salvar
                ":senha" => password_hash($usuario->getSenha(), PASSWORD_DEFAULT),
            );

            $stmt = $this->executaComParametros($sql, $parametros);
            $rowCount = $stmt->rowCount();

            if ($rowCount === 0) {
                throw new Exception("Nenhum registro inserido");
            }

            return true;

        } catch (PDOException $e) {
            error_log("Erro PDO: " . $e->getMessage());
            throw $e;
        }
    }


    public function autenticar($email, $senha)
    {
        $sql = "SELECT id_usuario AS id, nome, email, senha
                FROM usuario
                WHERE email = :email
                LIMIT 1";
        $stmt = $this->executaComParametros($sql, [
            ':email' => $email
        ]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && password_verify($senha, $resultado['senha'])) {
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


