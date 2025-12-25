<?php include '../views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="m-0 text-primary">Hola, <?= htmlspecialchars($_SESSION['user_nombre']) ?></h5>
        <small class="text-muted">Rol: 
            <?php if ($_SESSION['user_id'] == 1): ?>
                <strong class="text-dark border-bottom border-dark pb-1">
                    <i class="bi bi-stars"></i> Super Admin
                </strong>
            <?php else: ?>
                <strong><?= ucfirst($_SESSION['user_rol']) ?></strong>
            <?php endif; ?>
        </small>
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
                    <th style="width: 250px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr class="<?= $user['estado'] == 0 ? 'table-secondary text-muted' : '' ?>">
                    
                    <td><?= htmlspecialchars($user['nombre']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    
                    <td>
                        <?php if ($user['id'] == 1): ?>
                            <span class="badge bg-dark text-white border border-light">
                                <i class="bi bi-stars"></i> Super Admin
                            </span>
                        <?php else: ?>
                            <span class="badge bg-<?= $user['rol'] == 'admin' ? 'danger' : 'info' ?>">
                                <?= ucfirst($user['rol']) ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?= $user['estado'] == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?>
                    </td>

                    <td>
                        <div class="btn-group" role="group">
                            <?php 
                            // 1. DEFINICIÓN DE VARIABLES DE PERMISOS
                            $esAdmin = ($_SESSION['user_rol'] == 'admin');      // ¿Soy Administrador?
                            $esMiCuenta = ($_SESSION['user_id'] == $user['id']); // ¿Es mi propia fila?
                            $esFilaSuperAdmin = ($user['id'] == 1);              // ¿Esta fila es del Super Admin?

                            // 2. CASO ESPECIAL: FILA DEL SUPER ADMIN (ID 1)
                            if ($esFilaSuperAdmin): ?>
                                
                                <?php if ($esMiCuenta): ?>
                                    <a href="index.php?action=editar&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm" title="Editar mis datos">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                <?php endif; ?>

                                <span class="btn btn-outline-dark btn-sm disabled" title="Usuario Protegido (Super Admin)">
                                    <i class="bi bi-shield-fill-check"></i>
                                </span>

                            <?php else: ?>
                                <?php 
                                // REGLA DE ORO: Solo muestro botones si soy Admin O si es mi propia cuenta.
                                // Si soy un usuario normal viendo a otro, esto será falso y no veré nada.
                                if ($esAdmin || $esMiCuenta): 
                                ?>
                                    
                                    <a href="index.php?action=editar&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <?php if($user['estado'] == 1): ?>
                                        <?php
                                            // Mensaje de advertencia personalizado
                                            $msgDesactivar = ($esMiCuenta && !$esAdmin) 
                                                ? '⚠️ ADVERTENCIA: Si desactiva su cuenta no podrá acceder más. Debera contactar al administrador para poder reactivarla ¿Continuar?' 
                                                : '¿Desactivar usuario?';
                                        ?>
                                        <a href="index.php?action=cambiar_estado&id=<?= $user['id'] ?>" 
                                           class="btn btn-secondary btn-sm"
                                           title="Desactivar usuario"
                                           onclick="return confirm('<?= $msgDesactivar ?>')">
                                            <i class="bi bi-toggle-on"></i>
                                        </a>
                                    <?php else: ?>
                                        <?php if($esAdmin): ?>
                                        <a href="index.php?action=cambiar_estado&id=<?= $user['id'] ?>" 
                                           class="btn btn-success btn-sm"
                                           title="Reactivar usuario">
                                            <i class="bi bi-toggle-off"></i>
                                        </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php
                                        $msgEliminar = ($esMiCuenta && !$esAdmin) 
                                            ? '⚠️ PELIGRO: ¿Borrar TU PROPIA CUENTA? Esta acción es irreversible.' 
                                            : '⚠️ ¿Eliminar permanentemente a ' . htmlspecialchars($user['nombre']) . '?';
                                    ?>
                                    <a href="index.php?action=eliminar&id=<?= $user['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       title="Eliminar permanentemente"
                                       onclick="return confirm('<?= $msgEliminar ?>')">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>

                                <?php endif; // Fin del check de permisos (Admin o Mi Cuenta) ?>
                            <?php endif; // Fin del check Super Admin ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../views/layouts/footer.php'; ?>