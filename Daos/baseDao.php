<?php

class BaseDAO
{
    protected $connection;

    
    function __construct()
    {
        $connection_string = "mysql:host=localhost;port=3306;dbname=gardenjournal;charset=utf8";
        $db_user           = "root";
        $db_pass           = "";  // ou sua senha, se houver

        $this->connection = new PDO(
            $connection_string,
            $db_user,
            $db_pass
        );

        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function executaComParametros(
        $sql,
        $parametros
    ) {
        $stmt = $this->connection->prepare($sql);
        foreach ($parametros as $chave => $valor) {
            $stmt->bindValue($chave, $valor);
        }
        $stmt->execute();
        return $stmt;
    }

    public function executar($sql)
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function getLastInsertId() {
    return $this->connection->lastInsertId();
}
}
