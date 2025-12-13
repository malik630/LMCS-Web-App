<?php

class Partner
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function getAll()
    {
        $this->db->connect();
        $query = "SELECT * FROM partenaires WHERE is_deleted = 0 ORDER BY nom ASC";
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
}
?>