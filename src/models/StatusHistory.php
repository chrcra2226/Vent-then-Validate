<?php
require_once __DIR__ . '/Model.php';

class StatusHistory extends Model
{
    protected $table = 'status_history';

    /**
     * Get all status history records for a specific complaint
     */
    public function getByComplaint($complaint_id)
    {
        $stmt = $this->db->prepare("
            SELECT sh.*, 
                   u.name AS changed_by_name
            FROM status_history sh
            JOIN users u ON sh.changed_by = u.user_id
            WHERE sh.complaint_id = :complaint_id
            ORDER BY sh.changed_at DESC
        ");
        $stmt->execute([':complaint_id' => $complaint_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get a single status history record by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT sh.*, 
                   u.name AS changed_by_name
            FROM status_history sh
            JOIN users u ON sh.changed_by = u.user_id
            WHERE sh.history_id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create a new status history record
     */
    public function create($complaint_id, $old_status, $new_status, $changed_by, $notes = '')
    {
        $stmt = $this->db->prepare("
            INSERT INTO status_history 
                (complaint_id, old_status, new_status, changed_by, notes)
            VALUES 
                (:complaint_id, :old_status, :new_status, :changed_by, :notes)
        ");
        $stmt->execute([
            ':complaint_id' => $complaint_id,
            ':old_status'   => $old_status,
            ':new_status'   => $new_status,
            ':changed_by'   => $changed_by,
            ':notes'        => $notes
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Get the most recent status change for a complaint
     */
    public function getLatest($complaint_id)
    {
        $stmt = $this->db->prepare("
            SELECT sh.*, 
                   u.name AS changed_by_name
            FROM status_history sh
            JOIN users u ON sh.changed_by = u.user_id
            WHERE sh.complaint_id = :complaint_id
            ORDER BY sh.changed_at DESC
            LIMIT 1
        ");
        $stmt->execute([':complaint_id' => $complaint_id]);
        return $stmt->fetch();
    }

    /**
     * Get the total number of status changes for a complaint
     */
    public function countByComplaint($complaint_id)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM status_history
            WHERE complaint_id = :complaint_id
        ");
        $stmt->execute([':complaint_id' => $complaint_id]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Delete all status history for a complaint
     */
    public function deleteByComplaint($complaint_id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM status_history
            WHERE complaint_id = :complaint_id
        ");
        return $stmt->execute([':complaint_id' => $complaint_id]);
    }
}
