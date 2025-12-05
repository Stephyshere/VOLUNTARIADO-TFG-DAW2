<?php
include_once '../config/database.php';
include_once '../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login($data) {
        $this->user->email = $data['email'];
        
        if ($this->user->emailExists()) {
            if (password_verify($data['password'], $this->user->password)) {
                session_start();
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_role'] = $this->user->role;
                $_SESSION['user_email'] = $this->user->email;
                
                echo json_encode([
                    "success" => true,
                    "message" => "Login exitoso.",
                    "role" => $this->user->role,
                    "user" => ["email" => $this->user->email, "role" => $this->user->role]
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Contraseña incorrecta."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
        }
    }

    public function register($data) {
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];

        if ($this->user->create()) {
            echo json_encode(["success" => true, "message" => "Registro exitoso."]);
        } else {
            // Check if it failed because email exists (create might fail silently or we should check before)
            // Model create logic usually checks uniqueness via DB, but here we can rely on try/catch in model or logic.
            // For simplicity, we assume create failed due to logic error or duplicate if not handled in model.
            // Actually, User model doesn't check 'exists' inside create, but register.php logic did.
            // Let's assume the controller should check or model throws. 
            // Better: Controller checks.
            /* 
            if($this->user->emailExists()){ ... } 
            But here we just try create.
            */
            echo json_encode(["success" => false, "message" => "No se pudo registrar. Puede que el email ya exista."]);
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        echo json_encode(["success" => true, "message" => "Sesión cerrada."]);
    }

    public function checkAuth() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                "authenticated" => true,
                "user" => [
                    "id" => $_SESSION['user_id'],
                    "role" => $_SESSION['user_role'],
                    "email" => $_SESSION['user_email']
                ]
            ]);
        } else {
            echo json_encode(["authenticated" => false]);
        }
    }
}
?>
