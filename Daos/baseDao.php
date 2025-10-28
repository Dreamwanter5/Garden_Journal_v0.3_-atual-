<?php

class BaseDAO
{
    protected $connection;

    function __construct()
    {
        $dsn = "mysql:host=localhost;port=3306;dbname=gardenjournal;charset=utf8";
        $username = "root";
        $password = "";

        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("[BaseDAO] Erro de conexão: " . $e->getMessage());
            die("Erro ao conectar ao banco de dados.");
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
            error_log("[BaseDAO::executaComParametros] SQL: " . $sql);
            error_log("[BaseDAO::executaComParametros] Params: " . print_r($parametros, true));
            error_log("[BaseDAO::executaComParametros] PDOException: " . $e->getMessage());
            throw $e;
        }
    }

    public function executa($sql)
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("[BaseDAO::executa] SQL: " . $sql);
            error_log("[BaseDAO::executa] PDOException: " . $e->getMessage());
            throw $e;
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}