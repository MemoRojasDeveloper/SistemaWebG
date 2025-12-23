<?php
// models/Usuario.php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, nombre, email, rol, estado FROM usuarios");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($nombre, $email, $password, $rol) {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nombre, $email, $password, $rol]);
    }

    public function update($id, $nombre, $email, $rol, $password = null) {
        if ($password) {
            $sql = "UPDATE usuarios SET nombre=?, email=?, rol=?, password=? WHERE id=?";
            $params = [$nombre, $email, $rol, $password, $id];
        } else {
            $sql = "UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?";
            $params = [$nombre, $email, $rol, $id];
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    
    
    public function toggleEstado($id, $nuevoEstado) {
        $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nuevoEstado, $id]);
    }
}
?>