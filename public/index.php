<?php
// index.php (Router Principal)
session_start();

// --- LÓGICA DE TEMPORIZADOR DE INACTIVIDAD ---
if (isset($_SESSION['user_id'])) {
    
    // 1. Obtener el tiempo límite (si no existe, 10 min por defecto)
    $minutosLimite = isset($_SESSION['user_session_time']) ? $_SESSION['user_session_time'] : 10;
    $segundosLimite = $minutosLimite * 60; 

    // 2. Verificar inactividad
    if (isset($_SESSION['last_activity'])) {
        $tiempoInactivo = time() - $_SESSION['last_activity'];

        // 3. Si pasó el tiempo límite...
        if ($tiempoInactivo > $segundosLimite) {
            session_unset();
            session_destroy();
            // Redirigimos con mensaje de timeout
            header("Location: index.php?action=login&msg=timeout");
            exit;
        }
    }
    // 4. Resetear reloj
    $_SESSION['last_activity'] = time();
}

require_once '../config/db.php';
require_once '../controllers/UsuarioController.php';
require_once '../controllers/AuthController.php';
require_once '../helpers/lang.php';

$action = $_GET['action'] ?? 'index';

// SEGURIDAD: Forzar login
if (!isset($_SESSION['user_id']) && $action !== 'login' && $action !== 'auth') {
    header("Location: index.php?action=login");
    exit;
}

$usuarioController = new UsuarioController($pdo);
$authController = new AuthController($pdo);

switch ($action) {
    // --- AUTH ---
    case 'login':
        $authController->login();
        break;
    case 'auth':
        $authController->auth();
        break;
    case 'logout':
        $authController->logout();
        break;

    // --- USUARIOS ---
    case 'index':
        $usuarioController->index();
        break;
    
    case 'crear':
        $usuarioController->crear();
        break;
    
    case 'guardar':
        $usuarioController->guardar();
        break;
        
    case 'editar':
        if (isset($_GET['id'])) {
            $usuarioController->editar($_GET['id']);
        } else {
            header("Location: index.php");
        }
        break;

    // --- LOGICA COMPLETA DE ACTUALIZAR ---
    // --- LOGICA COMPLETA DE ACTUALIZAR (Corregida) ---
    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $rol = $_POST['rol']; 
            
            // 1. RECIBIMOS EL NUEVO TIEMPO
            $session_time = (int)$_POST['session_time'];
            if (!in_array($session_time, [5, 10, 15, 20])) {
                $session_time = 10; 
            }
        
            // 2. LOGICA PARA GUARDAR CON TRY-CATCH (Para evitar Error 500)
            $password = $_POST['password'];
            
            try {
                if (!empty($password)) {
                    // CASO A: Cambiaron la contraseña
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password = ?, session_time = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $email, $rol, $passwordHash, $session_time, $id]);
                } else {
                    // CASO B: Mantener contraseña actual
                    $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, session_time = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $email, $rol, $session_time, $id]);
                }
            
                // 3. ACTUALIZACIÓN INMEDIATA DE LA SESIÓN (Si me edito a mí mismo)
                if ($id == $_SESSION['user_id']) {
                    $_SESSION['user_nombre'] = $nombre; 
                    $_SESSION['user_session_time'] = $session_time; 
                }
            
                header("Location: index.php");
                exit;

            } catch (PDOException $e) {
                // Código 23000 es violación de integridad (ej: email duplicado)
                if ($e->getCode() == 23000) {
                    header("Location: index.php?action=editar&id=$id&error=email_duplicado");
                    exit;
                } else {
                    // Si es otro error, lo mostramos o lanzamos el error 500
                    throw $e;
                }
            }
        }
        break;

    case 'cambiar_estado': 
        if (isset($_GET['id'])) {
            $usuarioController->cambiarEstado($_GET['id']);
        }
        break;

    case 'eliminar': 
        if (isset($_GET['id'])) {
            $usuarioController->eliminar($_GET['id']);
        }
        break;

    case 'auditoria':
        // Solo admin
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
            header("Location: index.php");
            exit;
        }
        require_once '../models/Auditoria.php'; 
        $auditoriaModel = new Auditoria($pdo);
        $logs = $auditoriaModel->getAll();
        require '../views/admin/auditoria.php';
        break;

    default:
        // Si no existe la acción, redirigir al login o index
        header("Location: index.php");
        break;
}
?>