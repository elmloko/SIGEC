<script>
    $(function () {
        $('#entidad').focus();
        $('#formulario').validate();
    });
</script>
<?php if ($mensaje != ''): ?>
    <div class="alert alert-success">    
        <?php echo $mensaje; ?>
    </div>
<?php endif ?>
<?php if ($errors != ''): ?>
    <div class="alert alert-danger">
        <?php echo $errors; ?>
    </div>
<?php endif ?>
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header><i class="fa fa-building-o"></i> Editar Entidad</header>
        </div>
        <div class="card-body">
            <form method="post" id="formulario" class="form form-validate floating-label">
                <input type="hidden" name="id" value="<?php echo $entidad['id'] ?>"/>
                <div class="form-group">
                    <?php echo Form::input('entidad', Arr::get($entidad, 'entidad', ''), array('id' => 'entidad', 'class' => 'form-control required')); ?>
                    <?php echo Form::label('Nombre de la Entidad', 'Nombre de la Entidad'); ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo Form::input('sigla', Arr::get($entidad, 'sigla', ''), array('class' => 'form-control required')); ?>
                            <?php echo Form::label('Sigla Completa'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo Form::input('sigla2', Arr::get($entidad, 'sigla2', ''), array('size' => 3, 'maxlength' => 3, 'class' => 'form-control required')); ?>
                            <?php echo Form::label('sigla 2', 'Sigla Abreviada (Max. 3 caracteres - para la hora de ruta)'); ?>
                        </div>
                    </div>
                </div>


                <div class="form-group">                    
                    <?php echo Form::input('direccion', Arr::get($entidad, 'direccion', ''), array('class' => 'form-control')); ?>
                    <?php echo Form::label('direccion', 'Dirección:'); ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo Form::input('telefono', Arr::get($entidad, 'telefono', ''), array('class' => 'form-control')); ?>
                            <?php echo Form::label('Telefono', 'Teléfonos'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>


                <input type="submit" value="Modificar" class="btn btn-sm btn-primary-dark" />
            </form>
        </div>
    </div>
</div>
