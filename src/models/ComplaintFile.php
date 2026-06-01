<?php
require_once __DIR__ . '/Model.php';

class ComplaintFile extends Model
{
    protected $table = 'complaint_files';

    /**
     * Get all files for a specific complaint
     */
    public function getByComplaint($complaint_id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM complaint_files
            WHERE complaint_id = :complaint_id
            ORDER BY uploaded_at DESC
        ");
        $stmt->execute([':complaint_id' => $complaint_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get a single file by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM complaint_files
            WHERE file_id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create a new file record
     */
    public function create($complaint_id, $file_name, $file_path, $file_type)
    {
        $stmt = $this->db->prepare("
            INSERT INTO complaint_files 
                (complaint_id, file_name, file_path, file_type)
            VALUES 
                (:complaint_id, :file_name, :file_path, :file_type)
        ");
        $stmt->execute([
            ':complaint_id' => $complaint_id,
            ':file_name'    => $file_name,
            ':file_path'    => $file_path,
            ':file_type'    => $file_type
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Delete a file record by ID
     */
    public function delete($id)
    {
        // Get file path before deleting record
        $file = $this->getById($id);
        if ($file && file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
        $stmt = $this->db->prepare("
            DELETE FROM complaint_files
            WHERE file_id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Delete all files for a specific complaint
     */
    public function deleteByComplaint($complaint_id)
    {
        $files = $this->getByComplaint($complaint_id);
        foreach ($files as $file) {
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }
        }
        $stmt = $this->db->prepare("
            DELETE FROM complaint_files
            WHERE complaint_id = :complaint_id
        ");
        return $stmt->execute([':complaint_id' => $complaint_id]);
    }

    /**
     * Get the total number of files for a complaint
     */
    public function countByComplaint($complaint_id)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM complaint_files
            WHERE complaint_id = :complaint_id
        ");
        $stmt->execute([':complaint_id' => $complaint_id]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Check if a file type is allowed
     */
    public function isAllowedType($file_type)
    {
        $allowed = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf'
        ];
        return in_array($file_type, $allowed);
    }

    /**
     * Check if a file size is within the allowed limit
     */
    public function isAllowedSize($file_size, $max_size = 5242880)
    {
        // Default max size is 5MB (5 * 1024 * 1024)
        return $file_size <= $max_size;
    }
}
