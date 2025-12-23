<?php include '../views/layouts/header.php'; ?>

<div class="card shadow-sm mt-4" style="max-width: 600px; margin: auto;">
    <div class="card-header bg-white">
        <h4 class="mb-0">Editar Usuario</h4>
    </div>
    <div class="card-body">
        
        <form action="index.php?action=actualizar&id=<?= $usuario['id'] ?>" method="POST">
            
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Rol</label>
                <select name="rol" class="form-select">
                    <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <hr>
            
            <div class="mb-3">
                <label>Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between">
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