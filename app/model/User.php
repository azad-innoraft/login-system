<?php

namespace App\model;

use App\Core\Database;

class User {
    /**
     * @var Database $db Database instance 
     */
    private Database $db;

    public function __construct() {
        $config = require(BASE_PATH . "/app/config/config.php");
        $this->db = new Database($config);
    }

    /**
     * Creating new User 
     * 
     * @param string $name
     *  Name of the new user
     * 
     * @param string $email 
     *  Email of the new User 
     * 
     * @param string $password 
     *  Password 
     */
    public function create(string $name, string $email, string $password) {
        // Hashing password 
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return $this->db->query(
            "INSERT INTO users(name, email, password) VALUES (?, ?, ?)",
            [$name, $email, $hashedPassword]
        );
    }

    /**
     * Creating new User 
     * 
     * @param string $email 
     *  Email of the existing User 
     * 
     * @return array|false  
     */
    public function findByEmail(string $email) {
        return $this->db->query(
            "SELECT * FROM users WHERE email=?",
            [$email]
        )->find();
    }

    /**
     * Store the token token that is generated for new password generation  
     * 
     * @param string $email 
     *  Email of the existing User 
     * 
     * @param string $token 
     *  New generated token 
     */
    public function storeToken(string $email, string $token) {
        $this->db->query(
            "UPDATE users SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE email = ?",
            [$token, $email]
        );
    }

    /**
     * Verifing token is correct or not   
     * 
     * @param string $token 
     * 
     * @return bool 
     *  RETURN TRUE, if the token is verified, otherwise FALSE
     */
    public function verifyToken(string $token) {
        return $this->db->query(
            "SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW()",
            [$token]
        )->find();
    }

    /**
     * Password update logic   
     * 
     * @param string $token 
     * @param string $password 
     */
    public function updatePassword(string $token, string $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $this->db->query(
            "UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?",
            [$hashedPassword, $token]
        );
    }
}
