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
            
            // --- NUEVO: VERIFICACIÓN DE EMAIL DUPLICADO ---
            $existe = $this->model->getByEmail($_POST['email']);
            
            if ($existe) {
                // Si el correo ya existe:
                $_SESSION['error'] = "El correo <b>" . $_POST['email'] . "</b> ya está registrado. Por favor usa otro.";
                
                // Redirigir de vuelta al formulario de crear
                header("Location: index.php?action=crear");
                exit; // Detenemos el código aquí
            }
            // ----------------------------------------------

            // Si no existe, el código sigue normal:
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $this->model->create($_POST['nombre'], $_POST['email'], $password, $_POST['rol']);
            
            // Log de auditoría
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
            
            // 1. BUSCAMOS SI EL EMAIL YA EXISTE EN LA BASE DE DATOS
            $usuarioConEseEmail = $this->model->getByEmail($_POST['email']);

            // --- PROTECCIÓN DE ROL SUPER ADMIN ---
            // Si estamos editando al ID 1, forzamos que el rol sea SIEMPRE 'admin'
            $rol = $_POST['rol'];
            if ($id == 1) {
                $rol = 'admin'; 
            }
            // -------------------------------------

            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
            
            // OJO: Usamos la variable $rol, no $_POST['rol']
            $this->model->update($id, $_POST['nombre'], $_POST['email'], $rol, $password);

            // 2. VERIFICACIÓN INTELIGENTE:
            // Si el email existe... Y ADEMÁS... el ID de ese email NO es el mío
            // (significa que estoy intentando usar el correo de otra persona)
            if ($usuarioConEseEmail && $usuarioConEseEmail['id'] != $id) {
                
                // Guardamos el error
                $_SESSION['error'] = "El correo <b>" . $_POST['email'] . "</b> ya pertenece a otro usuario.";
                
                // Te devuelvo a la pantalla de edición (NO guardo nada)
                header("Location: index.php?action=editar&id=" . $id);
                exit; 
            }

            // 3. SI PASA LA PRUEBA, ACTUALIZAMOS NORMAL
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
            $this->model->update($id, $_POST['nombre'], $_POST['email'], $_POST['rol'], $password);
            
            // Log de auditoría
            $detalles = "Actualizó datos del usuario ID: " . $id;
            $this->auditoria->registrar($_SESSION['user_id'], 'EDITAR_USUARIO', $detalles);

            header("Location: index.php");
        }
    }

    public function cambiarEstado($id) {
        // --- PROTECCIÓN SUPER ADMIN ---
        if ($id == 1) {
            $_SESSION['error'] = "🚫 No puedes desactivar al Super Administrador. El sistema quedaría vulnerable.";
            header("Location: index.php");
            exit;
        }

        if ($_SESSION['user_rol'] != 'admin'&& $_SESSION['user_id'] != $id) {
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

    public function eliminar($id) {
        // --- PROTECCIÓN SUPER ADMIN ---
        // Asumiendo que el ID 1 es el Super Admin
        if ($id == 1) {
            $_SESSION['error'] = "🚫 ERROR CRÍTICO: El Super Administrador es intocable. No se puede eliminar.";
            header("Location: index.php");
            exit;
        }
        // 1. SEGURIDAD: Solo permitimos pasar si es Admin O si es el dueño de la cuenta.
        // Si NO es admin Y el ID a borrar NO es el suyo, lo expulsamos.
        if ($_SESSION['user_rol'] != 'admin' && $_SESSION['user_id'] != $id) {
            header("Location: index.php");
            exit;
        }

        $usuarioAnterior = $this->model->getById($id);
        
        if ($usuarioAnterior) {
            // 2. EJECUTAR EL BORRADO
            $this->model->delete($id);
            
            // 3. REGISTRAR EN AUDITORÍA
            $detalles = "Eliminó permanentemente al usuario: " . $usuarioAnterior['nombre'];
            $this->auditoria->registrar($_SESSION['user_id'], 'ELIMINAR_USUARIO', $detalles);

            // 4. CASO ESPECIAL: Si el usuario se borró a sí mismo
            if ($_SESSION['user_id'] == $id) {
                // Destruimos la sesión y lo mandamos al login
                session_destroy();
                header("Location: index.php?action=login");
                exit;
            }
        }
        
        // Si fue un admin borrando a otro, vuelve a la lista
        header("Location: index.php");
    }
}
?>