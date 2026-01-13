<?php

class Contact extends Model
{
    public function create($data)
    {
        $requiredFields = ['nom', 'email', 'sujet', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        
        $insertData = [
            'nom' => $data['nom'],
            'email' => $data['email'],
            'sujet' => $data['sujet'],
            'message' => $data['message'],
            'date_envoi' => date('Y-m-d H:i:s'),
            'lu' => 0,
            'repondu' => 0
        ];
        
        return $this->insert('messages_contact', $insertData);
    }
    
    public function getAll()
    {
        $query = "SELECT * FROM messages_contact 
                  ORDER BY lu ASC, date_envoi DESC";
        return $this->select($query);
    }
    
    public function getById($id)
    {
        return $this->selectById('messages_contact', $id, 'id_message');
    }
    
    public function markAsRead($id)
    {
        return $this->updateById('messages_contact', $id, ['lu' => 1], 'id_message');
    }
    
    public function markAsReplied($id)
    {
        return $this->updateById('messages_contact', $id, ['repondu' => 1], 'id_message');
    }
    
    public function getUnreadCount()
    {
        return $this->count('messages_contact', ['lu' => 0]);
    }
}
?>