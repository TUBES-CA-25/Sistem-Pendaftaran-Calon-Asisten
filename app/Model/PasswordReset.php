<?php

namespace App\Model;

use App\Core\Model;
use App\Core\Database;

class PasswordReset extends Model
{
    protected static $table = 'password_resets';
    
    // Create or update token for email
    public function createToken($email, $token)
    {
        // Remove existing token if any
        $this->deleteByEmail($email);
        
        $query = "INSERT INTO " . static::$table . " (email, token, created_at) VALUES (:email, :token, :created_at)";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));
        
        return $stmt->execute();
    }
    
    // Find token by email and token string
    public function findByEmailAndToken($email, $token)
    {
        $query = "SELECT * FROM " . static::$table . " WHERE email = :email AND token = :token LIMIT 1";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Delete token
    public function deleteByEmail($email)
    {
        $query = "DELETE FROM " . static::$table . " WHERE email = :email";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindValue(':email', $email);
        
        return $stmt->execute();
    }
    
    // Get user by email (Helper function since User model uses username/stambuk)
    // We assume the user table has no email column based on previous checks, 
    // BUT the AuthController registers users with 'name' = $_POST['email'].
    // Wait, let's double check UserModel. 
    // UserModel has 'username', 'stambuk', 'password'.
    // AuthController::register: $name = $_POST['email']; ... cast to $name.
    // Mahasiswa::create($userId, $stambuk, $name);
    // So 'username' in 'user' table seems to be used for the Name?
    // Let's check AuthController again. 
    // $user->__construct2($name, $stambuk, $hashedPassword); => $this->username = $username.
    // So 'username' column in 'user' table stores the Name/Email? 
    // In register: $name = $_POST['email']; 
    // This is confusing. The variable is $name, but it comes from $_POST['email'].
    // Then $user->__construct2($name...) maps it to $this->username.
    // So `username` column likely holds the email or name.
    // If the form field says "email" but code says "name", checking the login form might clarify.
    // Login uses 'stambuk'.
    // If the register form asks for email but saves it as username, then we should look up by username.
}
