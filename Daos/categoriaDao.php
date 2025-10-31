<?php
require_once(__DIR__ . '/baseDao.php');

class CategoriaDAO extends BaseDAO
{
    public function existe($nome, $idUsuario)
    {
        $sql = "SELECT COUNT(*) FROM categoria 
                WHERE nome = :nome AND id_usuario = :id_usuario";

        $stmt = $this->executaComParametros($sql, [
            ':nome' => $nome,
            ':id_usuario' => $idUsuario
        ]);

        return $stmt->fetchColumn() > 0;
    }

    public function inserir($nome, $idUsuario)
    {
        $sql = "INSERT INTO categoria (nome, data_criacao, id_usuario) 
                VALUES (:nome, CURDATE(), :id_usuario)";

        $this->executaComParametros($sql, [
            ':nome' => $nome,
            ':id_usuario' => $idUsuario
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function buscarPorUsuario($idUsuario)
    {
        $sql = "SELECT id_categoria, nome, data_criacao, emoji, imagem 
                FROM categoria 
                WHERE id_usuario = :id_usuario
                ORDER BY nome ASC";

        $stmt = $this->executaComParametros($sql, [':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function vincularNota($idNota, $idCategoria)
    {
        $sql = "INSERT INTO nota_categoria (id_nota, id_categoria) 
                VALUES (:id_nota, :id_categoria)";

        return $this->executaComParametros($sql, [
            ':id_nota' => $idNota,
            ':id_categoria' => $idCategoria
        ]);
    }

    public function buscarIdPorNome($nome, $idUsuario)
    {
        $sql = "SELECT id_categoria FROM categoria 
            WHERE nome = :nome AND id_usuario = :id_usuario";

        $stmt = $this->executaComParametros($sql, [
            ':nome' => $nome,
            ':id_usuario' => $idUsuario
        ]);

        return $stmt->fetchColumn();
    }

    // novo: renomear
    public function atualizarNome($idCategoria, $idUsuario, $novoNome)
    {
        $sql = "UPDATE categoria SET nome = :nome
                WHERE id_categoria = :id AND id_usuario = :id_usuario";

        $stmt = $this->executaComParametros($sql, [
            ':nome' => $novoNome,
            ':id' => $idCategoria,
            ':id_usuario' => $idUsuario
        ]);

        return $stmt->rowCount();
    }

    // novo: remover (pivot é ON DELETE CASCADE)
    public function remover($idCategoria, $idUsuario)
    {
        $sql = "DELETE FROM categoria
                WHERE id_categoria = :id AND id_usuario = :id_usuario";

        $stmt = $this->executaComParametros($sql, [
            ':id' => $idCategoria,
            ':id_usuario' => $idUsuario
        ]);

        return $stmt->rowCount();
    }
}