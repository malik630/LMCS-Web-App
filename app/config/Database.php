<?php

class Database
{
    private $connection = null;
    private $host = 'localhost';
    private $dbname = 'TDW';
    private $username = 'admin';
    private $password = 'admin';
    
    public function connect()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
        return $this->connection;
    }
    
    public function disconnect()
    {
        $this->connection = null;
    }
    
    public function query($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function execute($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);
        return $stmt->execute($params);
    }
    
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}
?>