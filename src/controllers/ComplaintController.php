<?php
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/Complaint.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/ComplaintFile.php';
require_once __DIR__ . '/../models/StatusHistory.php';
require_once __DIR__ . '/../../includes/validation.php';

class ComplaintController
{

    private $complaintModel;
    private $categoryModel;
    private $fileModel;
    private $historyModel;

    public function __construct()
    {
        $this->complaintModel = new Complaint();
        $this->categoryModel  = new Category();
        $this->fileModel      = new ComplaintFile();
        $this->historyModel   = new StatusHistory();
    }

    /**
     * Get all complaints (admin)
     */
    public function getAllComplaints()
    {
        return $this->complaintModel->getAll();
    }

    /**
     * Get complaints for a specific user (customer)
     */
    public function getUserComplaints($user_id)
    {
        return $this->complaintModel->getByUser($user_id);
    }

    /**
     * Get a single complaint by ID
     */
    public function getComplaint($complaint_id)
    {
        return $this->complaintModel->getById($complaint_id);
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        return $this->categoryModel->getAll();
    }

    /**
     * Handle complaint submission
     */
    public function submitComplaint($user_id, $category_id, $title, $description)
    {
        $category_id = sanitize($category_id);
        $title       = sanitize($title);
        $description = sanitize($description);

        $errors = validateComplaint($category_id, $title, $description);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $complaint_id = $this->complaintModel->create(
            $user_id,
            $category_id,
            $title,
            $description
        );

        // Record initial status in history
        $this->historyModel->create(
            $complaint_id,
            null,
            'Open',
            $user_id,
            'Complaint submitted.'
        );

        return [
            'success'      => true,
            'complaint_id' => $complaint_id,
            'message'      => 'Your complaint has been submitted successfully! We will review it shortly.'
        ];
    }

    /**
     * Handle complaint status update (admin)
     */
    public function updateStatus($complaint_id, $new_status, $admin_id, $notes = '')
    {
        // Get current complaint status
        $complaint = $this->complaintModel->getById($complaint_id);

        if (!$complaint) {
            return ['success' => false, 'errors' => ['Complaint not found.']];
        }

        $old_status = $complaint['status'];

        // Update complaint status
        $this->complaintModel->updateStatus($complaint_id, $new_status);

        // Record status change in history
        $this->historyModel->create(
            $complaint_id,
            $old_status,
            $new_status,
            $admin_id,
            $notes
        );

        return ['success' => true, 'message' => 'Complaint status updated successfully.'];
    }

    /**
     * Get status history for a complaint
     */
    public function getStatusHistory($complaint_id)
    {
        return $this->historyModel->getByComplaint($complaint_id);
    }

    /**
     * Get files for a complaint
     */
    public function getComplaintFiles($complaint_id)
    {
        return $this->fileModel->getByComplaint($complaint_id);
    }

    /**
     * Get dashboard statistics (admin)
     */
    public function getDashboardStats()
    {
        return [
            'total'     => $this->complaintModel->count(),
            'open'      => $this->complaintModel->countByStatus('Open'),
            'in_review' => $this->complaintModel->countByStatus('In Review'),
            'resolved'  => $this->complaintModel->countByStatus('Resolved'),
            'closed'    => $this->complaintModel->countByStatus('Closed')
        ];
    }

    /**
     * Get recent complaints for admin dashboard
     */
    public function getRecentComplaints($limit = 5)
    {
        return $this->complaintModel->getRecent($limit);
    }

    /**
     * Delete a complaint
     */
    public function deleteComplaint($complaint_id)
    {
        $this->fileModel->deleteByComplaint($complaint_id);
        $this->historyModel->deleteByComplaint($complaint_id);
        $this->complaintModel->delete($complaint_id);
        return ['success' => true, 'message' => 'Complaint deleted successfully.'];
    }
}
