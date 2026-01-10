<?php

class Settings extends Model
{
    public function getAllSettings()
    {
        $results = $this->selectAll('parametres');
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['param_key']] = $row['param_value'];
        }
        
        return $settings;
    }
    
    public function getSetting($key, $default = null)
    {
        $result = $this->selectAll('parametres', ['param_key' => $key]);
        
        if (!empty($result)) {
            return $result[0]['param_value'];
        }
        
        return $default;
    }
    
    public function updateSetting($key, $value)
    {
        $existing = $this->selectAll('parametres', ['param_key' => $key]);
        
        if (!empty($existing)) {
            return $this->update('parametres', 
                ['param_value' => $value], 
                ['param_key' => $key]
            );
        } else {
            return $this->insert('parametres', [
                'param_key' => $key,
                'param_value' => $value
            ]);
        }
    }
    
    public function deleteSetting($key)
    {
        return $this->delete('parametres', ['param_key' => $key]);
    }
    
    public function createBackup()
    {
        try {
            $backupDir = '../backups/';
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . $filename;
            
            // Récupérer toutes les tables
            $this->db->connect();
            $tables = $this->db->query("SHOW TABLES");
            
            $sql = "-- Backup créé le " . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            foreach ($tables as $table) {
                $tableName = reset($table);
                
                // Structure de la table
                $createTable = $this->db->query("SHOW CREATE TABLE `$tableName`");
                $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sql .= $createTable[0]['Create Table'] . ";\n\n";
                
                // Données de la table
                $rows = $this->db->query("SELECT * FROM `$tableName`");
                
                if (!empty($rows)) {
                    $sql .= "INSERT INTO `$tableName` VALUES\n";
                    $values = [];
                    
                    foreach ($rows as $row) {
                        $escaped = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            return "'" . addslashes($value) . "'";
                        }, array_values($row));
                        
                        $values[] = '(' . implode(', ', $escaped) . ')';
                    }
                    
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            $this->db->disconnect();
            
            // Écrire le fichier
            if (file_put_contents($filepath, $sql) === false) {
                return ['success' => false, 'error' => 'Impossible d\'écrire le fichier de sauvegarde'];
            }
            
            // Enregistrer dans la base
            $userId = $_SESSION['user_id'] ?? null;
            $this->insert('backups', [
                'filename' => $filename,
                'filesize' => filesize($filepath),
                'created_by' => $userId
            ]);
            
            return ['success' => true, 'filename' => $filename];
            
        } catch (Exception $e) {
            if (isset($this->db)) {
                $this->db->disconnect();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function restoreBackup($filepath)
    {
        try {
            $sql = file_get_contents($filepath);
            
            if ($sql === false) {
                return ['success' => false, 'error' => 'Impossible de lire le fichier'];
            }
            
            $this->db->connect();
            $this->db->execute("SET FOREIGN_KEY_CHECKS=0");

            $queries = array_filter(
                array_map('trim', explode(';', $sql)),
                function($query) {
                    return !empty($query) && !preg_match('/^--/', $query);
                }
            );
            
            foreach ($queries as $query) {
                if (!empty(trim($query))) {
                    $this->db->execute($query);
                }
            }
            $this->db->execute("SET FOREIGN_KEY_CHECKS=1");
            
            $this->db->disconnect();
            
            return ['success' => true];
            
        } catch (Exception $e) {
            if (isset($this->db)) {
                $this->db->disconnect();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getAllBackups()
    {
        return $this->selectAll('backups', [], 'created_at', 'DESC');
    }
    
    public function deleteBackup($filename)
    {
        return $this->delete('backups', ['filename' => $filename]);
    }
    
    public function getBackupById($id)
    {
        return $this->selectById('backups', $id);
    }
}
?>