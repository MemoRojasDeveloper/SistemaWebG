<?php include '../views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="m-0 text-primary">Hola, <?= htmlspecialchars($_SESSION['user_nombre']) ?></h5>
        <small class="text-muted">Rol: <strong><?= ucfirst($_SESSION['user_rol']) ?></strong></small>
    </div>
    <div>
        <?php if($_SESSION['user_rol'] == 'admin'): ?>
            <a href="index.php?action=auditoria" class="btn btn-outline-dark btn-sm me-2">
                <i class="bi bi-shield-lock"></i> Auditoría
            </a>
        <?php endif; ?>
        <a href="index.php?action=logout" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="m-0">Lista de Usuarios</h4>
        <a href="index.php?action=crear" class="btn btn-primary">Crear Usuario</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-bordered m-0">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr class="<?= $user['estado'] == 0 ? 'table-secondary text-muted' : '' ?>">
                    <td><?= htmlspecialchars($user['nombre']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge bg-<?= $user['rol'] == 'admin' ? 'danger' : 'info' ?>">
                            <?= $user['rol'] ?>
                        </span>
                    </td>
                    <td>
                        <?= $user['estado'] == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?>
                    </td>
                    <td>
                        <a href="index.php?action=editar&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <?php if($_SESSION['user_rol'] == 'admin'): ?>
                            <?php if($user['estado'] == 1): ?>
                                <a href="index.php?action=eliminar&id=<?= $user['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Desactivar usuario?')">
                                    <i class="bi bi-person-x-fill"></i>
                                </a>
                            <?php else: ?>
                                <a href="index.php?action=eliminar&id=<?= $user['id'] ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('¿Reactivar usuario?')">
                                    <i class="bi bi-person-check-fill"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../views/layouts/footer.php'; ?>