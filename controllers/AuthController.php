<?php
// controllers/AuthController.php
require_once '../models/Usuario.php';
require_once '../models/Auditoria.php'; // 1. Importar el modelo

class AuthController {
    private $model;
    private $auditoria; // 2. Propiedad para auditoría

    public function __construct($pdo) {
        $this->model = new Usuario($pdo);
        $this->auditoria = new Auditoria($pdo); // 3. Inicializar
    }

    public function login() {
        require '../views/auth/login.php';
    }

    public function auth() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $usuario = $this->model->getByEmail($email);

        if ($usuario && password_verify($password, $usuario['password'])) {
            if ($usuario['estado'] == 1) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre'];
                $_SESSION['user_rol'] = $usuario['rol'];
                
                // --- REGISTRAR LOGIN ---
                $this->auditoria->registrar($usuario['id'], 'LOGIN', 'Inicio de sesión exitoso');

                header("Location: index.php");
                exit;
            } else {
                $error = "Usuario inactivo.";
                require '../views/auth/login.php';
            }
        } else {
            $error = "Credenciales incorrectas.";
            require '../views/auth/login.php';
        }
    }

    public function logout() {
        // --- REGISTRAR LOGOUT (Antes de destruir la sesión) ---
        if (isset($_SESSION['user_id'])) {
            $this->auditoria->registrar($_SESSION['user_id'], 'LOGOUT', 'Cierre de sesión');
        }

        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
?>