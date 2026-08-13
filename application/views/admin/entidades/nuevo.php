<script>
    $(function () {
        $('#entidad').focus();
        $('#formulario').validate();
    });
</script>
<?php if ($mensaje != '') echo "Se creo corectamente la entidad '$mensaje'"; ?>
<?php if (sizeof($errors) > 0): ?>
    <ul>
        <?php foreach ($errors as $e): ?>
            <li><?php echo $e ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header><i class="fa fa-building-o"></i> Crear Entidad</header>
        </div>
        <div class="card-body">
            <form method="post" id="formulario" class="form form-validate floating-label">
                <div class="form-group">
                    <?php echo Form::input('entidad', Arr::get($_POST, 'entidad', ''), array('id' => 'entidad', 'class' => 'form-control required')); ?>
                    <?php echo Form::label('Nombre de la Entidad', 'Nombre de la Entidad'); ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo Form::input('sigla', Arr::get($_POST, 'sigla', ''), array('class' => 'form-control required')); ?>
                            <?php echo Form::label('Sigla Completa'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo Form::input('sigla2', Arr::get($_POST, 'sigla2', ''), array('size' => 3, 'maxlength' => 3, 'class' => 'form-control required')); ?>
                            <?php echo Form::label('sigla 2', 'Sigla Abreviada (Max. 3 caracteres - para la hora de ruta)'); ?>
                        </div>
                    </div>
                </div>


                <div class="form-group">                    
                    <?php echo Form::input('direccion', Arr::get($_POST, 'direccion', ''), array('class' => 'form-control')); ?>
                    <?php echo Form::label('direccion', 'Dirección:'); ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo Form::input('telefono', Arr::get($_POST, 'telefono', ''), array('class' => 'form-control')); ?>
                            <?php echo Form::label('Telefono', 'Teléfonos'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>


                <input type="submit" value="Crear entidad" class="btn btn-sm btn-primary-dark" />
            </form>
        </div>
    </div>
</div>
