<?php

namespace App\Models;

use App\Core\Database;
use SQLite3;

class User extends Model
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'password',
        'created_at'
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function create(array $data): ?int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        try {
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $this->fillable) . 
                   ") VALUES (" . implode(', ', array_map(fn($field) => ":$field", $this->fillable)) . ")";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($this->fillable as $field) {
                $stmt->bindValue(":$field", $data[$field], SQLITE3_TEXT);
            }
            
            $result = $stmt->execute();
            if ($result === false) {
                throw new \Exception("Failed to create user: " . $this->db->lastErrorMsg());
            }
            
            return $this->db->lastInsertRowID();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
