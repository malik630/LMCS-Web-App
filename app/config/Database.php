<?php

class Database
{
    private $connection = null;
    private $host = 'localhost';
    private $dbname = 'TDW';
    private $username = 'admin';
    private $password = 'admin';
    private $inTransaction = false;
    
    public function connect()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Erreur de connexion à la base de données.");
        }
        return $this->connection;
    }
    
    public function disconnect()
    {
        if (!$this->inTransaction) {
            $this->connection = null;
        }
    }
    
    public function query($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur SQL: " . $e->getMessage() . "\nQuery: " . $query . "\nParams: " . json_encode($params));
        }
    }
    
    public function execute($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Erreur SQL: " . $e->getMessage() . "\nQuery: " . $query . "\nParams: " . json_encode($params));
        }
    }
    
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}
?>