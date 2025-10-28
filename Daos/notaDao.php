<?php
require_once(__DIR__ . '/baseDao.php');
require_once(__DIR__ . '/../Entidades/nota.php');

class NotaDAO extends BaseDAO
{
    public function salvar(Nota $nota, ?string $tituloOriginal = null)
    {
        try {
            $this->connection->beginTransaction();

            $titulo = mb_substr(trim($nota->getTitulo()), 0, 45);
            $descricao = $nota->getDescricao();
            $conteudo = $nota->getConteudo();
            $idUsuario = (int) $nota->getIdUsuario();
            $categoriasIds = (array) $nota->getCategorias();

            $tituloOriginal = $tituloOriginal ? mb_substr(trim($tituloOriginal), 0, 45) : null;

            error_log("[NotaDAO::salvar] title={$titulo} original={$tituloOriginal} user={$idUsuario} categories=" . json_encode($categoriasIds));

            // Normaliza categorias
            $categoriasIds = array_values(array_filter(array_unique(array_map('intval', $categoriasIds)), fn($v) => $v > 0));

            if ($tituloOriginal && $tituloOriginal !== $titulo) {
                // Renomeio/edição: atualiza a nota usando a PK antiga
                $sqlUpdate = "UPDATE nota
                              SET nome = :novo_nome, descricao = :descricao, texto = :conteudo, dt = CURDATE(), id_usuario = :id_usuario
                              WHERE nome = :antigo_nome AND id_usuario = :id_usuario";
                $stmt = $this->executaComParametros($sqlUpdate, [
                    ':novo_nome' => $titulo,
                    ':descricao' => $descricao,
                    ':conteudo' => $conteudo,
                    ':id_usuario' => $idUsuario,
                    ':antigo_nome' => $tituloOriginal
                ]);

                if ($stmt->rowCount() === 0) {
                    // Se não encontrou a nota antiga, faz upsert pelo novo título
                    $sqlInsert = "INSERT INTO nota (nome, descricao, texto, dt, id_usuario)
                                  VALUES (:titulo, :descricao, :conteudo, CURDATE(), :id_usuario)
                                  ON DUPLICATE KEY UPDATE
                                    descricao = VALUES(descricao),
                                    texto = VALUES(texto),
                                    dt = VALUES(dt),
                                    id_usuario = VALUES(id_usuario)";
                    $this->executaComParametros($sqlInsert, [
                        ':titulo' => $titulo,
                        ':descricao' => $descricao,
                        ':conteudo' => $conteudo,
                        ':id_usuario' => $idUsuario
                    ]);
                } else {
                    // Atualiza vínculos existentes para o novo título
                    $sqlRenameCat = "UPDATE nota_categoria SET id_nota = :novo_nome WHERE id_nota = :antigo_nome";
                    $this->executaComParametros($sqlRenameCat, [
                        ':novo_nome' => $titulo,
                        ':antigo_nome' => $tituloOriginal
                    ]);
                }

                // Recria vínculos das categorias conforme seleção atual
                $this->executaComParametros("DELETE FROM nota_categoria WHERE id_nota = :id_nota", [':id_nota' => $titulo]);
                foreach ($categoriasIds as $idCategoria) {
                    $this->executaComParametros(
                        "INSERT IGNORE INTO nota_categoria (id_nota, id_categoria) VALUES (:id_nota, :id_categoria)",
                        [':id_nota' => $titulo, ':id_categoria' => $idCategoria]
                    );
                }
            } else {
                // Upsert padrão (mesmo título)
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

                // Atualiza categorias
                $this->executaComParametros("DELETE FROM nota_categoria WHERE id_nota = :id_nota", [':id_nota' => $titulo]);
                foreach ($categoriasIds as $idCategoria) {
                    $this->executaComParametros(
                        "INSERT IGNORE INTO nota_categoria (id_nota, id_categoria) VALUES (:id_nota, :id_categoria)",
                        [':id_nota' => $titulo, ':id_categoria' => $idCategoria]
                    );
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

    public function buscarPorTitulo($titulo, $idUsuario)
    {
        $sql = "SELECT n.nome AS titulo, n.descricao, n.texto, n.dt, 
                GROUP_CONCAT(nc.id_categoria) AS categorias
                FROM nota n
                LEFT JOIN nota_categoria nc ON n.nome = nc.id_nota
                WHERE n.nome = :titulo AND n.id_usuario = :id_usuario
                GROUP BY n.nome";

        $stmt = $this->executaComParametros($sql, [
            ':titulo' => $titulo,
            ':id_usuario' => $idUsuario
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $resultado['categorias'] = $resultado['categorias']
                ? array_map('intval', explode(',', $resultado['categorias']))
                : [];
        }

        return $resultado;
    }
}