<?php
require_once('baseDAO.php');
require_once('categoriaDAO.php');

class NotaDAO extends BaseDAO
{
    public function salvarNota($titulo, $conteudoMarkdown, $conteudoHTML, $idUsuario, $categorias = [])
{
    try {
        $this->connection->beginTransaction();

        // 1. Salva a nota principal
        $sql = "INSERT INTO nota (titulo, conteudo_markdown, conteudo_html, id_usuario) 
                VALUES (:titulo, :conteudo_markdown, :conteudo_html, :id_usuario)";
        
        $stmt = $this->executaComParametros($sql, [
            ':titulo' => $titulo,
            ':conteudo_markdown' => $conteudoMarkdown,
            ':conteudo_html' => $conteudoHTML,
            ':id_usuario' => $idUsuario
        ]);
        
        $idNota = $this->getLastInsertId();
        
        // 2. Processa as categorias (se houver)
        if (!empty($categorias)) {
            $categoriaDAO = new CategoriaDAO();
            
            foreach ($categorias as $categoriaNome) {
                $categoriaNome = trim($categoriaNome);
                if (empty($categoriaNome)) continue;
                
                if (!$categoriaDAO->existe($categoriaNome, $idUsuario)) {
                    $categoriaDAO->inserir($categoriaNome, $idUsuario);
                }
                
                $idCategoria = $categoriaDAO->buscarIdPorNome($categoriaNome, $idUsuario);
                
                $sqlVinculo = "INSERT IGNORE INTO nota_categoria (id_nota, id_categoria) 
                               VALUES (:id_nota, :id_categoria)";
                $this->executaComParametros($sqlVinculo, [
                    ':id_nota' => $idNota,
                    ':id_categoria' => $idCategoria
                ]);
            }
        }
        
        $this->connection->commit();
        return $idNota;
        
    } catch (Exception $e) {
        $this->connection->rollBack();
        throw $e;
    }
}