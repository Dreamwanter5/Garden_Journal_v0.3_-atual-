<?php

class BaseDAO
{
    protected $connection;


    function __construct()
    {
        $connection_string = "mysql:host=localhost;port=3306;dbname=gardenjournal;charset=utf8";
        $db_user = "root";
        $db_pass = "";  // senha se houver

        try {
            $this->connection = new PDO(
                $connection_string,
                $db_user,
                $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );
        } catch (PDOException $e) {
            error_log("BaseDAO::__construct - Erro de conexão: " . $e->getMessage());
            throw $e;
        }
    }

    public function executaComParametros($sql, $parametros)
    {
        try {
            $stmt = $this->connection->prepare($sql);
            foreach ($parametros as $chave => $valor) {
                $stmt->bindValue($chave, $valor);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            // Log SQL, parâmetros e mensagem de erro para depuração
            error_log("[BaseDAO::executaComParametros] SQL: " . $sql);
            error_log("[BaseDAO::executaComParametros] Params: " . print_r($parametros, true));
            error_log("[BaseDAO::executaComParametros] PDOException: " . $e->getMessage());
            throw $e;
        }
    }

    public function executar($sql)
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}
