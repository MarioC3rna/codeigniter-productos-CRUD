<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Clientes</h1>
    <a href="<?= base_url('clientes/crear') ?>" class="btn btn-primary">Nuevo cliente</a>
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
        <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="7" class="text-center text-muted">No hay clientes registrados todavía.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente->id ?></td>
                    <td><?= html_escape($cliente->nombre) ?></td>
                    <td><?= html_escape($cliente->apellido) ?></td>
                    <td><?= html_escape($cliente->direccion) ?></td>
                    <td><?= html_escape($cliente->telefono) ?></td>
                    <td><?= html_escape($cliente->email) ?></td>
                    <td class="text-end">
                        <a href="<?= base_url('clientes/editar/' . $cliente->id) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
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