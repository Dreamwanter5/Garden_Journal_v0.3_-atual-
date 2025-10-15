<?php
require_once('BaseDAO.php');

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
        
        return $this->executaComParametros($sql, [
            ':nome' => $nome,
            ':id_usuario' => $idUsuario
        ]);
    }

    public function buscarPorUsuario($idUsuario)
    {
        $sql = "SELECT id_categoria, nome, data_criacao, emoji, imagem 
                FROM categoria 
                WHERE id_usuario = :id_usuario";
        
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
}