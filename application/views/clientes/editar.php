<?php // Formulario para editar un cliente ya existente.
      // El objeto $cliente llega desde el controlador con sus datos actuales ?>
<h1 class="h3 mb-3">Editar cliente</h1>

<?php // Errores de validación si el servidor rechazó los datos ?>
<?php if (validation_errors()): ?>
    <div class="alert alert-danger">
        <?= validation_errors('<p class="mb-0">', '</p>') ?>
    </div>
<?php endif; ?>

<?php // Se envía el id en la URL para que el controlador sepa a quién actualizar;
      // incluye automáticamente el token CSRF ?>
<?= form_open('clientes/editar/' . $cliente->id, ['class' => 'bg-white p-4 rounded shadow-sm']) ?>

    <div class="mb-3">
        <?= form_label('Nombre', 'nombre', ['class' => 'form-label']) ?>
        <?php // set_value: al entrar muestra lo guardado en la BD;
              // si la validación falló, muestra lo último que escribió el usuario ?>
        <?= form_input([
            'name' => 'nombre',
            'id' => 'nombre',
            'class' => 'form-control',
            'value' => set_value('nombre', $cliente->nombre),
            'maxlength' => 100,
            'required' => 'required',
        ]) ?>
    </div>

    <div class="mb-3">
        <?= form_label('Apellido', 'apellido', ['class' => 'form-label']) ?>
        <?= form_input([
            'name' => 'apellido',
            'id' => 'apellido',
            'class' => 'form-control',
            'value' => set_value('apellido', $cliente->apellido),
            'maxlength' => 100,
            'required' => 'required',
        ]) ?>
    </div>

    <div class="mb-3">
        <?= form_label('Dirección', 'direccion', ['class' => 'form-label']) ?>
        <?= form_input([
            'name' => 'direccion',
            'id' => 'direccion',
            'class' => 'form-control',
            'value' => set_value('direccion', $cliente->direccion),
            'maxlength' => 200,
        ]) ?>
    </div>

    <div class="mb-3">
        <?= form_label('Teléfono', 'telefono', ['class' => 'form-label']) ?>
        <input type="text" name="telefono" id="telefono" class="form-control" maxlength="20"
               value="<?= set_value('telefono', $cliente->telefono) ?>">
    </div>

    <div class="mb-3">
        <?= form_label('Email', 'email', ['class' => 'form-label']) ?>
        <input type="email" name="email" id="email" class="form-control"
               value="<?= set_value('email', $cliente->email) ?>">
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="<?= site_url('clientes') ?>" class="btn btn-outline-secondary">Cancelar</a>

<?= form_close() ?>
