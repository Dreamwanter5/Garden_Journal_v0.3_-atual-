<?php
require_once('BaseDAO.php');

class NotaDAO extends BaseDAO
{
    public function salvar($titulo, $conteudo, $idUsuario, $categorias)
{
    try {
        // Inicia transação
        $this->connection->beginTransaction();

        // 1. Salva/atualiza a nota
        $sql = "INSERT INTO nota (nome, texto, dt) 
                VALUES (:titulo, :conteudo, CURDATE())
                ON DUPLICATE KEY UPDATE texto = :conteudo, dt = CURDATE()";
        
        $stmt = $this->executaComParametros($sql, [
            ':titulo' => $titulo,
            ':conteudo' => $conteudo
        ]);
        
        $idNota = $this->getLastInsertId(); // Usando o novo método
        
        // 2. Remove vinculações anteriores
        $sqlDelete = "DELETE FROM nota_categoria WHERE id_nota = :id_nota";
        $this->executaComParametros($sqlDelete, [':id_nota' => $idNota]);
        
        // 3. Processa categorias
        $categoriaDAO = new CategoriaDAO();
        
        foreach ($categorias as $categoriaNome) {
            // Verifica se categoria existe
            if (!$categoriaDAO->existe($categoriaNome, $idUsuario)) {
                $categoriaDAO->inserir($categoriaNome, $idUsuario);
            }
            
            // Obtém ID da categoria
            $idCategoria = $categoriaDAO->buscarIdPorNome($categoriaNome, $idUsuario);
            
            // Vincula nota à categoria
            $sqlVinculo = "INSERT INTO nota_categoria (id_nota, id_categoria)
                           VALUES (:id_nota, :id_categoria)";
            $this->executaComParametros($sqlVinculo, [
                ':id_nota' => $idNota,
                ':id_categoria' => $idCategoria
            ]);
        }
        
        // Commit da transação
        $this->connection->commit();
        return $idNota;
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $this->connection->rollBack();
        throw $e;
    }
}

    public function buscarPorUsuario($idUsuario)
    {
        $sql = "SELECT n.nome AS titulo, n.texto, n.dt, 
                GROUP_CONCAT(c.nome) AS categorias
                FROM nota n
                JOIN nota_categoria nc ON n.nome = nc.id_nota
                JOIN categoria c ON nc.id_categoria = c.id_categoria
                WHERE c.id_usuario = :id_usuario
                GROUP BY n.nome";
        
        $stmt = $this->executaComParametros($sql, [':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}