<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Database.php';

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all records from the table
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single record by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->table}_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Delete a record by ID
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->table}_id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get the total count of records in the table
     */
    public function count()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Check if a record exists by ID
     */
    public function exists($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE {$this->table}_id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
}
