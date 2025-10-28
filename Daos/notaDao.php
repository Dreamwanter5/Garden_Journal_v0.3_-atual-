<?php
require_once(__DIR__ . '/baseDao.php');
require_once(__DIR__ . '/../Entidades/nota.php');

class NotaDAO extends BaseDAO
{
    public function salvar(Nota $nota)
    {
        try {
            $this->connection->beginTransaction();

            $titulo = $nota->getTitulo();
            $titulo = mb_substr(trim($titulo), 0, 45);
            $descricao = $nota->getDescricao();
            $conteudo = $nota->getConteudo();
            $idUsuario = (int) $nota->getIdUsuario();
            $categoriasIds = (array) $nota->getCategorias();

            error_log("[NotaDAO::salvar] title={$titulo} user={$idUsuario} categories=" . json_encode($categoriasIds));

            // Inserir ou atualizar a nota (inclui descricao)
            $sql = "INSERT INTO nota (nome, descricao, texto, dt, id_usuario)
                    VALUES (:titulo, :descricao, :conteudo, CURDATE(), :id_usuario)
                    ON DUPLICATE KEY UPDATE
                        descricao = VALUES(descricao),
                        texto = VALUES(texto),
                        dt = VALUES(dt),
                        id_usuario = VALUES(id_usuario)";
            $this->executaComParametros($sql, [
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':conteudo' => $conteudo,
                ':id_usuario' => $idUsuario
            ]);

            // Remover vínculos antigos
            $sqlDelete = "DELETE FROM nota_categoria WHERE id_nota = :id_nota";
            $this->executaComParametros($sqlDelete, [':id_nota' => $titulo]);

            // Normalizar e remover categorias duplicadas antes de inserir vínculos
            $categoriasIds = array_values(array_filter(array_unique(array_map('intval', $categoriasIds)), function ($v) {
                return $v > 0;
            }));

            // Inserir vínculos novos - usar INSERT IGNORE para evitar erro de duplicate key
            if (!empty($categoriasIds)) {
                $sqlVinculo = "INSERT IGNORE INTO nota_categoria (id_nota, id_categoria) VALUES (:id_nota, :id_categoria)";
                foreach ($categoriasIds as $idCategoria) {
                    $this->executaComParametros($sqlVinculo, [
                        ':id_nota' => $titulo,
                        ':id_categoria' => $idCategoria
                    ]);
                }
            }

            $this->connection->commit();
            return $titulo;
        } catch (Exception $e) {
            $this->connection->rollBack();
            error_log("[NotaDAO::salvar] Erro: " . $e->getMessage());
            throw $e;
        }
    }

    public function buscarPorUsuario($idUsuario)
    {
        $sql = "SELECT n.nome AS titulo, n.descricao, n.texto, n.dt, 
                GROUP_CONCAT(c.nome) AS categorias
                FROM nota n
                LEFT JOIN nota_categoria nc ON n.nome = nc.id_nota
                LEFT JOIN categoria c ON nc.id_categoria = c.id_categoria
                WHERE n.id_usuario = :id_usuario
                GROUP BY n.nome";

        $stmt = $this->executaComParametros($sql, [':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}