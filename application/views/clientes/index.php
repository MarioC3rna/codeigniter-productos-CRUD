<?php // Vista principal: lista todos los clientes en una tabla ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Clientes</h1>
    <?php // Botón que lleva al formulario de creación ?>
   <a href="<?= site_url('clientes/crear') ?>" class="btn btn-primary">Nuevo cliente</a>
</div>

<table class="table table-striped table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php // Si no hay clientes registrados se muestra solo un mensaje ?>
        <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="7" class="text-center text-muted">No hay clientes registrados todavía.</td>
            </tr>
        <?php else: ?>
            <?php // Una fila <tr> por cada cliente que trajo el modelo ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente->id ?></td>
                    <?php // html_escape neutraliza caracteres peligrosos (protección XSS) ?>
                    <td><?= html_escape($cliente->nombre) ?></td>
                    <td><?= html_escape($cliente->apellido) ?></td>
                    <td><?= html_escape($cliente->direccion) ?></td>
                    <td><?= html_escape($cliente->telefono) ?></td>
                    <td><?= html_escape($cliente->email) ?></td>
                    <td class="text-end">
                        <a href="<?= site_url('clientes/editar/' . $cliente->id) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <?php // Eliminar va en formulario propio porque es POST (necesita token CSRF);
                              // confirm() pregunta antes de borrar ?>
                        <?= form_open('clientes/eliminar/' . $cliente->id, [
                            'class' => 'd-inline',
                            'onsubmit' => "return confirm('¿Eliminar este cliente?');",
                        ]) ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
