<?php

class Model
{
    protected $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function insert($table, array $data)
    {
        if (empty($data)) {
            return false;
        }
        
        $this->db->connect();
        
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => ":$field", $fields);
        
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        try {
            $this->db->execute($query, $data);
            $id = $this->db->lastInsertId();
            $this->db->disconnect();
            return $id;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Insert error: " . $e->getMessage());
            return false;
        }
    }
    
    public function selectById($table, $id, $primaryKey = 'id')
    {
        $this->db->connect();
        $query = "SELECT * FROM $table WHERE $primaryKey = :id";
        
        try {
            $result = $this->db->query($query, ['id' => $id]);
            $this->db->disconnect();
            return $result[0] ?? null;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Select error: " . $e->getMessage());
            return null;
        }
    }
    
    public function selectAll($table, array $conditions = [], $orderBy = null, $order = 'ASC', $limit = null)
    {
        $this->db->connect();
        
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "$field = :$field";
            $params[$field] = $value;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $orderClause = $orderBy ? "ORDER BY $orderBy $order" : '';
        $limitClause = $limit ? "LIMIT " . (int)$limit : '';
        
        $query = "SELECT * FROM $table $whereClause $orderClause $limitClause";
        
        try {
            $result = $this->db->query($query, $params);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("SelectAll error: " . $e->getMessage());
            return [];
        }
    }
    
    public function select($query, array $params = [])
    {
        $this->db->connect();
        
        try {
            $result = $this->db->query($query, $params);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Custom select error: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateById($table, $id, array $data, $primaryKey = 'id')
    {
        if (empty($data)) {
            return false;
        }
        
        $this->db->connect();
        
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $field => $value) {
            $fields[] = "$field = :$field";
            $params[$field] = $value;
        }
        
        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = :id",
            $table,
            implode(', ', $fields),
            $primaryKey
        );
        
        try {
            $result = $this->db->execute($query, $params);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($table, array $data, array $conditions)
    {
        if (empty($data) || empty($conditions)) {
            return false;
        }
        
        $this->db->connect();
        
        $setFields = [];
        $whereFields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $setFields[] = "$field = :set_$field";
            $params["set_$field"] = $value;
        }
        
        foreach ($conditions as $field => $value) {
            $whereFields[] = "$field = :where_$field";
            $params["where_$field"] = $value;
        }
        
        $query = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $setFields),
            implode(' AND ', $whereFields)
        );
        
        try {
            $result = $this->db->execute($query, $params);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Update with conditions error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteById($table, $id, $primaryKey = 'id')
    {
        $this->db->connect();
        $query = "DELETE FROM $table WHERE $primaryKey = :id";
        
        try {
            $result = $this->db->execute($query, ['id' => $id]);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($table, array $conditions)
    {
        if (empty($conditions)) {
            return false;
        }
        
        $this->db->connect();
        
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "$field = :$field";
            $params[$field] = $value;
        }
        
        $query = sprintf(
            "DELETE FROM %s WHERE %s",
            $table,
            implode(' AND ', $where)
        );
        
        try {
            $result = $this->db->execute($query, $params);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Delete with conditions error: " . $e->getMessage());
            return false;
        }
    }
    
    public function softDelete($table, $id, $primaryKey = 'id', $deleteColumn = 'is_deleted')
    {
        return $this->updateById($table, $id, [$deleteColumn => 1], $primaryKey);
    }

    public function count($table, array $conditions = [])
    {
        $this->db->connect();
        
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "$field = :$field";
            $params[$field] = $value;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT COUNT(*) as total FROM $table $whereClause";
        
        try {
            $result = $this->db->query($query, $params);
            $this->db->disconnect();
            return (int)($result[0]['total'] ?? 0);
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function exists($table, $id, $primaryKey = 'id')
    {
        return $this->selectById($table, $id, $primaryKey) !== null;
    }
    
    public function search($table, $field, $value, array $additionalConditions = [])
    {
        $this->db->connect();
        
        $where = ["$field LIKE :search_value"];
        $params = ['search_value' => "%$value%"];
        
        foreach ($additionalConditions as $condField => $condValue) {
            $where[] = "$condField = :$condField";
            $params[$condField] = $condValue;
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $query = "SELECT * FROM $table $whereClause";
        
        try {
            $result = $this->db->query($query, $params);
            $this->db->disconnect();
            return $result;
        } catch (Exception $e) {
            $this->db->disconnect();
            error_log("Search error: " . $e->getMessage());
            return [];
        }
    }
}
?>