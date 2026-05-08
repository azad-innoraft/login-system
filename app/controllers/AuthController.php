<?php

namespace App\controllers;

use App\model\User;

class AuthController {
    /**
     * @var User $user User Model
     */
    private User $user;
    /**
     * @var array $errors Errors while making operation in User Model
     */
    private array $errors = [];

    public function __construct() {
        // Creating a userModel for creating users 
        $this->user = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    /**
     * register method register a new user
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? "");
            $email = trim($_POST['email'] ?? "");
            $password = $_POST['password'] ?? "";
            $confirmPassword = $_POST['confirm_password'] ?? "";

            // Validation 
            if (!$name) $this->errors['name'] = "Username is required!";
            if (!$email) $this->errors['email'] = "Email is required!";
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $this->errors['email'] = "Enter a valid email!";
            if (!$password || strlen($password) < 6) $this->errors['password'] = "Password must contain at least 6 letters!";
            if ($password !== $confirmPassword) $this->errors['confirm_password'] = "Confirm password does not match!";

            // Check user is already registered or not
            $alreadyRegistered = $this->user->findByEmail($email);

            if ($alreadyRegistered) {
                $this->errors['email'] = "Email is already registered!";
            }

            if (empty($this->errors)) {
                $this->user->create($name, $email, $password);
                $_SESSION['success'] = "Registration successful. Please login.";
                header("Location: /login");
                return;
            }
        }

        // rendering views 
        $errors = $this->errors;
        require_once BASE_PATH . "/app/views/auth/register.php";
    }

    /**
     * Login Logic
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? "");
            $password = $_POST['password'] ?? "";

            // validation 
            if (!$email) $this->errors['email'] = "Email is required!";
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $this->errors['email'] = "Enter a valid email!";
            if (!$password) $this->errors['password'] = "Password is required!";

            if (empty($this->errors)) {
                // Checking user is present or not 
                $user = $this->user->findByEmail($email);

                // Password validation 
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    header('Location: /form');
                    return;
                } else {
                    $this->errors['form'] = "Invalid Credentials";
                }
            }
        }


        $success = $_SESSION['success'] ?? "";
        unset($_SESSION['success']);
        $errors = $this->errors;
        // rendering views
        require_once BASE_PATH . "/app/views/auth/login.php";
    }

    /**
     * Logout Logic
     */
    public function logout() {
        // Unset all session variables
        session_unset();

        // Destroy the session
        session_destroy();

        header("Location: /login");
        exit;
    }

    /**
     * Find token
     */
    private function getResetToken(): string {
        if (!empty($_GET['token'])) {
            return trim($_GET['token']);
        }

        if (!empty($_POST['token'])) {
            return trim($_POST['token']);
        }

        $query = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $params);
            return trim($params['token'] ?? '');
        }

        return "";
    }

    /**
     * Forgot password Logic
     */
    public function forgot() {
        $message = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email'] ?? "");

            // Validation 
            if (!$email) $this->errors['email'] = "Email is required!";
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $this->errors['email'] = "Enter a valid email!";

            if (empty($this->errors)) {
                $token = bin2hex(random_bytes(32));
                $user = $this->user->findByEmail($email);

                if (!$user) {
                    $message['error'] = "No account found with this email.";
                } else {
                    // Storing the token in db 
                    $this->user->storeToken($email, $token);

                    // Sending the reset mail 
                    $mailer = new MailController();
                    if ($mailer->send($email, $token)) {
                        $message['success'] = "Check your mail for resetting password.";
                    } else {
                        $message['error'] = "Mail send failed. Please try again.";
                    }
                }
            }
        }

        $errors = $this->errors;
        require_once BASE_PATH . "/app/views/auth/forgot.php";
    }

    /**
     * Reset password Logic
     */
    public function reset() {
        $token = $this->getResetToken();

        // Verifing the token
        $user = $token ? $this->user->verifyToken($token) : false;
        $message = [];

        if (!$user) {
            $message['error'] = "Reset password link is invalid or expired.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
            $password = $_POST['password'] ?? "";
            $confirmPassword = $_POST['confirm_password'] ?? "";

            // Validation 
            if (!$password || strlen($password) < 6) $this->errors['password'] = "Password must contain at least 6 letters!";
            if ($password !== $confirmPassword) $this->errors['confirm_password'] = "Confirm password does not match!";

            // Password updation 
            if (empty($this->errors)) {
                $this->user->updatePassword($token, $password);
                $_SESSION['success'] = "Password reset successful. Please login.";
                header("Location: /login");
                return;
            }
        }

        $errors = $this->errors;
        // Rendering view
        require_once BASE_PATH . "/app/views/auth/reset.php";
    }
}
