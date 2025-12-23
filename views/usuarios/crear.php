<?php include '../views/layouts/header.php'; ?>

<h2>Crear Usuario</h2>

<div class="card shadow-sm" style="max-width: 600px; margin: auto;">
    <div class="card-header bg-white">
        <h4 class="m-0">Nuevo Usuario</h4>
    </div>
<div class="card-body">

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); // Borrar el mensaje después de mostrarlo ?>
<?php endif; ?>

<form action="index.php?action=guardar" method="POST">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Contraseña</label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label>Rol</label>
        <select name="rol" class="form-select">
            <option value="usuario">Usuario</option>
            <option value="admin">Administrador</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>

<script>
    // Tu script del ojito aquí...
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