<?php
// controllers/UsuarioController.php
require_once '../models/Usuario.php';
require_once '../models/Auditoria.php'; // Importar

class UsuarioController {
    private $model;
    private $auditoria; // Propiedad

    public function __construct($pdo) {
        $this->model = new Usuario($pdo);
        $this->auditoria = new Auditoria($pdo); // Inicializar
    }

    public function index() {
        $usuarios = $this->model->getAll();
        require '../views/usuarios/index.php'; 
    }

    public function crear() {
        require '../views/usuarios/crear.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $this->model->create($_POST['nombre'], $_POST['email'], $password, $_POST['rol']);
            
            // --- LOG CREAR ---
            // Nota: Aquí usamos $_SESSION['user_id'] porque es "quién hizo la acción"
            $detalles = "Creó al usuario: " . $_POST['email'];
            $this->auditoria->registrar($_SESSION['user_id'], 'CREAR_USUARIO', $detalles);

            header("Location: index.php?action=index");
        }
    }

    public function editar($id) {
        $usuario = $this->model->getById($id);
        require '../views/usuarios/editar.php';
    }

    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
            $this->model->update($id, $_POST['nombre'], $_POST['email'], $_POST['rol'], $password);
            
            // --- LOG EDITAR ---
            $detalles = "Actualizó datos del usuario ID: " . $id;
            $this->auditoria->registrar($_SESSION['user_id'], 'EDITAR_USUARIO', $detalles);

            header("Location: index.php");
        }
    }

    public function cambiarEstado($id) {
        if ($_SESSION['user_rol'] != 'admin') {
            header("Location: index.php");
            exit;
        }

        $usuario = $this->model->getById($id);
        $nuevoEstado = ($usuario['estado'] == 1) ? 0 : 1;
        $this->model->toggleEstado($id, $nuevoEstado);

        // --- LOG ESTADO ---
        $accion = $nuevoEstado == 0 ? 'BAJA_USUARIO' : 'REACTIVAR_USUARIO';
        $detalles = "Cambió estado del usuario ID: " . $id;
        $this->auditoria->registrar($_SESSION['user_id'], $accion, $detalles);

        header("Location: index.php");
    }
}
?>