<?php
require_once __DIR__ . '/Model.php';

class Complaint extends Model
{
    protected $table = 'complaints';

    /**
     * Get all complaints with user and category information
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.name AS user_name, 
                   u.email AS user_email,
                   cat.name AS category_name
            FROM complaints c
            JOIN users u ON c.user_id = u.user_id
            JOIN categories cat ON c.category_id = cat.category_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single complaint by ID with user and category information
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.name AS user_name,
                   u.email AS user_email,
                   cat.name AS category_name
            FROM complaints c
            JOIN users u ON c.user_id = u.user_id
            JOIN categories cat ON c.category_id = cat.category_id
            WHERE c.complaint_id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get all complaints for a specific user
     */
    public function getByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   cat.name AS category_name
            FROM complaints c
            JOIN categories cat ON c.category_id = cat.category_id
            WHERE c.user_id = :user_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get complaints by status
     */
    public function getByStatus($status)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.name AS user_name,
                   cat.name AS category_name
            FROM complaints c
            JOIN users u ON c.user_id = u.user_id
            JOIN categories cat ON c.category_id = cat.category_id
            WHERE c.status = :status
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    /**
     * Get complaints by category
     */
    public function getByCategory($category_id)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.name AS user_name,
                   cat.name AS category_name
            FROM complaints c
            JOIN users u ON c.user_id = u.user_id
            JOIN categories cat ON c.category_id = cat.category_id
            WHERE c.category_id = :category_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':category_id' => $category_id]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new complaint
     */
    public function create($user_id, $category_id, $title, $description)
    {
        $stmt = $this->db->prepare("
            INSERT INTO complaints (user_id, category_id, title, description, status)
            VALUES (:user_id, :category_id, :title, :description, 'Open')
        ");
        $stmt->execute([
            ':user_id'     => $user_id,
            ':category_id' => $category_id,
            ':title'       => $title,
            ':description' => $description
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update the status of a complaint
     */
    public function updateStatus($complaint_id, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE complaints 
            SET status = :status
            WHERE complaint_id = :complaint_id
        ");
        return $stmt->execute([
            ':status'       => $status,
            ':complaint_id' => $complaint_id
        ]);
    }

    /**
     * Delete a complaint
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM complaints 
            WHERE complaint_id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get total complaint count by status
     */
    public function countByStatus($status)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM complaints 
            WHERE status = :status
        ");
        $stmt->execute([':status' => $status]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Get total complaint count for a specific user
     */
    public function countByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM complaints 
            WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Get the most recent complaints limited by count
     */
    public function getRecent($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.name AS user_name,
                   cat.name AS category_name
            FROM complaints c
            JOIN users u ON c.user_id = u.user_id
            JOIN categories cat ON c.category_id = cat.category_id
            ORDER BY c.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
