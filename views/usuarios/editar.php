<?php include '../views/layouts/header.php'; ?>
<?php 
// SEGURIDAD: Evitar que usuarios normales editen a otros
if ($_SESSION['user_rol'] != 'admin' && $_SESSION['user_id'] != $usuario['id']) {
    $_SESSION['error'] = "⛔ Acceso denegado: No tienes permiso para editar a este usuario.";
    header("Location: index.php");
    exit;
}
?>

<div class="card shadow-sm mt-4" style="max-width: 600px; margin: auto;">
    <div class="card-header bg-white">
        <h4 class="mb-0">Editar Usuario</h4>
    </div>
    <div class="card-body">
        
        <?php if (isset($_GET['error']) && $_GET['error'] == 'email_duplicado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-2"></i> 
                El correo electrónico ya está registrado por otro usuario.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="index.php?action=actualizar&id=<?= $usuario['id'] ?>" method="POST">
            
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <?php if ($usuario['id'] == 1): ?>
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white border-dark">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>
                        <input type="text" class="form-control bg-light fw-bold" value="Super Administrador" disabled readonly>
                    </div>
                    <div class="form-text text-muted">
                        <i class="bi bi-info-circle"></i> Este rol es permanente y no se puede modificar.
                    </div>
                    <input type="hidden" name="rol" value="admin">
                <?php else: ?>
                    <select name="rol" class="form-select">
                        <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                        <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                <?php endif; ?>
            </div>

            <hr class="my-4">
            
            <div class="mb-3">
                <label class="form-label">Nueva Contraseña <small class="text-muted">(dejar en blanco para mantener la actual)</small></label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Tiempo de inactividad (Auto-Logout)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-hourglass-split"></i></span>
                    <select name="session_time" class="form-select">
                        <?php 
                        $tiempos = [5, 10, 15, 20];
                        $valorActual = isset($usuario['session_time']) ? $usuario['session_time'] : 10;
                        foreach ($tiempos as $t): 
                        ?>
                            <option value="<?= $t ?>" <?= $valorActual == $t ? 'selected' : '' ?>>
                                <?= $t ?> minutos
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-text text-muted">
                    El sistema cerrará tu sesión automáticamente si no detecta actividad por este tiempo.
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Datos</button>
            </div>
            
        </form>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });
</script>

<?php include '../views/layouts/footer.php'; ?>