<?php
require_once(__DIR__ . '/baseDao.php');
require_once(__DIR__ . '/categoriaDao.php');

class NotaDAO extends BaseDAO
{
    /**
     * Salva ou atualiza uma nota e vincula às categorias (recebe array de ids de categoria)
     * Retorna o identificador da nota (neste esquema usamos o título como chave)
     */
    public function salvar($titulo, $conteudo, $idUsuario, $categoriasIds)
    {
        try {
            $this->connection->beginTransaction();

            // Log inputs for debugging
            error_log("[NotaDAO::salvar] Saving note - Title: $titulo, UserId: $idUsuario");
            error_log("[NotaDAO::salvar] Categories: " . print_r($categoriasIds, true));

            // 1) Insert/update the note
            $sql = "INSERT INTO nota (nome, texto, dt, id_usuario) 
                    VALUES (:titulo, :conteudo, CURDATE(), :id_usuario)
                    ON DUPLICATE KEY UPDATE 
                        texto = VALUES(texto),
                        dt = VALUES(dt)";
            $this->executaComParametros($sql, [
                ':titulo' => $titulo,
                ':conteudo' => $conteudo,
                ':id_usuario' => $idUsuario
            ]);

            // 2) Handle categories if any
            if (!empty($categoriasIds)) {
                // Remove old links first
                $sqlDelete = "DELETE FROM nota_categoria WHERE id_nota = :id_nota";
                $this->executaComParametros($sqlDelete, [':id_nota' => $titulo]);

                // Add new category links
                $sqlInsert = "INSERT INTO nota_categoria (id_nota, id_categoria) 
                          VALUES (:id_nota, :id_categoria)";
                foreach ($categoriasIds as $categoriaId) {
                    $this->executaComParametros($sqlInsert, [
                        ':id_nota' => $titulo,
                        ':id_categoria' => $categoriaId
                    ]);
                }
            }

            $this->connection->commit();
            return $titulo;

        } catch (Exception $e) {
            $this->connection->rollBack();
            error_log("[NotaDAO::salvar] Error: " . $e->getMessage());
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