<?php
require_once 'db_connect.php';

class RegistrationFunctions {
    private $db;

    public function __construct() {
        $this->db = DB(); // Initialize the database connection using DB() function
    }

    // Registers a new user in the database
    public function Register($username, $password) {
        try {
            // Check if the username is already taken
            if ($this->isUsername($username)) {
                return ['status' => false, 'message' => 'Username is already in use!'];
            }

            $query = $this->db->prepare("INSERT INTO users (username, password, role_id) VALUES (:username, :password, 2)");
            $query->bindParam(":username", $username, PDO::PARAM_STR);
            $enc_password = password_hash($password, PASSWORD_DEFAULT); // Hash password
            $query->bindParam(":password", $enc_password, PDO::PARAM_STR);
            $query->execute();

            return ['status' => true, 'message' => 'Successfully registered!'];
        } catch (PDOException $e) {
            return ['status' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    // Checks if a username is already in use
    public function isUsername($username) {
        try {
            $query = $this->db->prepare("SELECT user_id FROM users WHERE username=:username");
            $query->bindParam(":username", $username, PDO::PARAM_STR);
            $query->execute();

            return $query->rowCount() > 0; // Returns true if username exists
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // Logs in the user by verifying username and password (adjust username/email if needed)
    public function Login($username, $password) {
        try {
            $query = $this->db->prepare("SELECT user_id, password, role_id FROM users WHERE username = :username");
            $query->bindParam("username", $username, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_OBJ);
                if (password_verify($password, $result->password)) {
                    return ['user_id' => $result->user_id, 'role_id' => $result->role_id]; // Return user ID and role if verified
                }
            }
            return false; // Login failed
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // Retrieves user details based on the user ID
    public function UserDetails($user_id) {
        try {
            $query = $this->db->prepare("SELECT user_id, username, role_id FROM users WHERE user_id = :user_id");
            $query->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetch(PDO::FETCH_OBJ); // Return user details as an object
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

     // Retrieve all users (for admin use)
     public function getAllUsers() {
        try {
            $query = $this->db->prepare("SELECT user_id, username, role_id FROM users");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // Update user details (for admin use)
    public function updateUser($user_id, $username, $password, $role_id) {
        try {
            // Check if the username is already taken by another user
            $query = $this->db->prepare("SELECT user_id FROM users WHERE username = :username AND user_id != :user_id");
            $query->bindParam(":username", $username, PDO::PARAM_STR);
            $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                return ['status' => false, 'message' => 'Username is already in use by another user!'];
            }

            if (!empty($password)) {
                // If password is provided, update it
                $enc_password = password_hash($password, PASSWORD_DEFAULT);
                $query = $this->db->prepare("UPDATE users SET username = :username, password = :password, role_id = :role_id WHERE user_id = :user_id");
                $query->bindParam(":password", $enc_password, PDO::PARAM_STR);
            } else {
                // If password is not provided, don't update it
                $query = $this->db->prepare("UPDATE users SET username = :username, role_id = :role_id WHERE user_id = :user_id");
            }

            $query->bindParam(":username", $username, PDO::PARAM_STR);
            $query->bindParam(":role_id", $role_id, PDO::PARAM_INT);
            $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $query->execute();

            return ['status' => true, 'message' => 'User updated successfully!'];
        } catch (PDOException $e) {
            return ['status' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    // Delete a user (for admin use)
    public function deleteUser($user_id) {
        try {
            $query = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
            $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $query->execute();

            return ['status' => true, 'message' => 'User deleted successfully!'];
        } catch (PDOException $e) {
            return ['status' => false, 'message' => 'Deletion failed: ' . $e->getMessage()];
        }
    }
}
?>

