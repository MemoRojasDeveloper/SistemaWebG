<?php
// index.php (Router Principal)
session_start();
require_once '../config/db.php';
require_once '../controllers/UsuarioController.php';
require_once '../controllers/AuthController.php';

// Obtener la acción de la URL
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
        // Necesitamos el ID para editar
        if (isset($_GET['id'])) {
            $usuarioController->editar($_GET['id']);
        } else {
            header("Location: index.php");
        }
        break;

    case 'actualizar':
        // Procesar el formulario de edición
        if (isset($_GET['id'])) {
            $usuarioController->actualizar($_GET['id']);
        }
        break;

    case 'eliminar':
        // Dar de baja / alta
        if (isset($_GET['id'])) {
            $usuarioController->cambiarEstado($_GET['id']);
        }
        break;

    case 'auditoria':
        // Solo admin
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
            header("Location: index.php");
            exit;
        }
        
        // --- CORRECCIÓN AQUÍ ---
        // Antes decía: require_once 'models/Auditoria.php';
        require_once '../models/Auditoria.php'; 
        
        $auditoriaModel = new Auditoria($pdo);
        $logs = $auditoriaModel->getAll();
        
        // --- Y AQUÍ TAMBIÉN ---
        // Antes decía: require 'views/admin/auditoria.php';
        require '../views/admin/auditoria.php';
        break;

    default:
        echo "<h1>Página no encontrada</h1><a href='index.php'>Volver al inicio</a>";
        break;
}
?>