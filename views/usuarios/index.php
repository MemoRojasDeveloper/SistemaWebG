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
                    <th style="width: 250px;">Acciones</th> </tr>
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
                        <div class="btn-group" role="group">
                            
                            <a href="index.php?action=editar&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <?php if($_SESSION['user_rol'] == 'admin'): ?>
                                
                                <?php if($user['estado'] == 1): ?>
                                    <a href="index.php?action=cambiar_estado&id=<?= $user['id'] ?>" 
                                       class="btn btn-secondary btn-sm"
                                       title="Desactivar usuario">
                                        <i class="bi bi-toggle-on"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?action=cambiar_estado&id=<?= $user['id'] ?>" 
                                       class="btn btn-success btn-sm"
                                       title="Reactivar usuario">
                                        <i class="bi bi-toggle-off"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="index.php?action=eliminar&id=<?= $user['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   title="Eliminar permanentemente"
                                   onclick="return confirm('⚠️ PELIGRO: ¿Estás seguro de eliminar a <?= $user['nombre'] ?> PERMANENTEMENTE?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>

                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../views/layouts/footer.php'; ?>