<?php include '../views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Registro de Auditoría</h2>
    <a href="index.php" class="btn btn-secondary">Volver al Panel</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped table-hover m-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['fecha'] ?></td>
                    <td>
                        <strong>
                            <?= $log['nombre_usuario'] ? htmlspecialchars($log['nombre_usuario']) : '<span class="text-muted">Sistema/Desconocido</span>' ?>
                        </strong>
                    </td>
                    <td><span class="badge bg-info text-dark"><?= $log['accion'] ?></span></td>
                    <td><?= htmlspecialchars($log['detalles']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../views/layouts/footer.php'; ?>