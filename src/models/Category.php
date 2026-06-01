<?php
require_once __DIR__ . '/Model.php';

class Category extends Model
{
    protected $table = 'categories';

    /**
     * Get all categories ordered by name
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT * FROM categories 
            ORDER BY name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single category by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM categories 
            WHERE category_id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create a new category
     */
    public function create($name, $description = '')
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, description)
            VALUES (:name, :description)
        ");
        return $stmt->execute([
            ':name'        => $name,
            ':description' => $description
        ]);
    }

    /**
     * Update an existing category
     */
    public function update($category_id, $name, $description = '')
    {
        $stmt = $this->db->prepare("
            UPDATE categories 
            SET name = :name, description = :description
            WHERE category_id = :category_id
        ");
        return $stmt->execute([
            ':name'        => $name,
            ':description' => $description,
            ':category_id' => $category_id
        ]);
    }

    /**
     * Delete a category by ID
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM categories 
            WHERE category_id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get the total number of complaints in a category
     */
    public function getComplaintCount($category_id)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM complaints 
            WHERE category_id = :category_id
        ");
        $stmt->execute([':category_id' => $category_id]);
        $result = $stmt->fetch();
        return $result['total'];
    }
}
