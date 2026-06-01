<?php
require_once __DIR__ . '/Model.php';

class User extends Model
{
    protected $table = 'users';

    /**
     * Get a user by their email address
     */
    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Create a new user
     */
    public function create($name, $email, $password, $role = 'customer')
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role)
            VALUES (:name, :email, :password_hash, :role)
        ");
        return $stmt->execute([
            ':name'          => $name,
            ':email'         => $email,
            ':password_hash' => $password_hash,
            ':role'          => $role
        ]);
    }

    /**
     * Update an existing user
     */
    public function update($user_id, $name, $email)
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET name = :name, email = :email
            WHERE user_id = :user_id
        ");
        return $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':user_id' => $user_id
        ]);
    }

    /**
     * Update a user's password
     */
    public function updatePassword($user_id, $password)
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = :password_hash
            WHERE user_id = :user_id
        ");
        return $stmt->execute([
            ':password_hash' => $password_hash,
            ':user_id'       => $user_id
        ]);
    }

    /**
     * Verify a user's password
     */
    public function verifyPassword($password, $password_hash)
    {
        return password_verify($password, $password_hash);
    }

    /**
     * Check if an email already exists in the database
     */
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM users 
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    /**
     * Get all customers only
     */
    public function getAllCustomers()
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE role = 'customer'
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all admins only
     */
    public function getAllAdmins()
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE role = 'admin'
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get the total complaint count for a user
     */
    public function getComplaintCount($user_id)
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
}
