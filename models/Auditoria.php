<?php
// models/Auditoria.php
class Auditoria {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        // Tu consulta optimizada con JOIN
        $sql = "SELECT a.id, a.fecha, a.accion, a.detalles, u.nombre as nombre_usuario 
                FROM auditoria a 
                LEFT JOIN usuarios u ON a.usuario_id = u.id 
                ORDER BY a.fecha DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // --- NUEVA FUNCIÓN AGREGADA ---
    public function registrar($usuario_id, $accion, $detalles = '') {
        try {
            $sql = "INSERT INTO auditoria (usuario_id, accion, detalles) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuario_id, $accion, $detalles]);
        } catch (PDOException $e) {
            // Si falla el log, no queremos detener el sistema, así que no hacemos nada o guardamos en archivo error.log
        }
    }
}
?>