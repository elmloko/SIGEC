<script type="text/javascript">
    $(function () {
        var entidad = $('#id_entidad');
        var oficina = $('#id_oficina');
        var superior = $('#superior');
        entidad.selectChain({
            target: oficina,
            url: "/admin/ajax/oficinas/",
            type: 'post',
            data: "ajax=true"
        });
        oficina.selectChain({
            target: superior,
            url: "/admin/ajax/superior/",
            type: 'post',
            data: "ajax=true"
        });
        entidad.trigger('change');
        $('select').select2();

        //  $('.dep').trigger('change');
    });
</script>
<?php if (sizeof($options) < 1): ?>
    <div class="alert alert-warning">
        <p>Para crear un usuario primero debe de contar con una entidad y una oficina minimamente.
            <a href="/admin/entidad/nuevo" class="btn btn-sm btn-success"><i class="fa fa-building-o"></i> Crear entidad
            </a>
        </p>
    </div>
<?php else: ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-underline ">
                <div class="card-head">
                    <header><i class="fa fa-user-plus"></i> Nuevo usuario</header>
                </div>
                <div class="card-body">

                    <div class="row">
                        <?php if (sizeof($error) > 0): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($error as $k => $v): ?>
                                    <i class="md md-error"></i> <?php echo $v; ?>
                                    <br/>
                                <?php endforeach; ?>
                            </div>
                        <?php endif;
                        ?>
                    </div>
                    <div class="row">
                        <?php if ($message != ""): ?>
                            <div class="alert alert-success">

                                <i class="md md-done"></i> <?php echo $message; ?>
                            </div>
                        <?php endif;
                        ?>
                    </div>


                    <form class="form form-validate floating-labels" method="post" action="">

                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::select('id_entidad', $options, Arr::get($_POST, 'id_entidad', 0), array('class' => 'form-control ', 'id' => 'id_entidad')); ?>
                                    <?php echo Form::label('entidad', 'Entidad'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::select('id_oficina', array(), Arr::get($_POST, 'id_oficina', 0), array('class' => 'form-control required', 'id' => 'id_oficina')); ?>
                                    <?php echo Form::label('oficina', 'Oficina'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::input('nombre', Arr::get($_POST, 'nombre', ''), array('class' => 'form-control required', 'id' => 'nombre')); ?>
                                    <?php echo Form::label('nombre', 'Nombre completo'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::input('cargo', Arr::get($_POST, 'cargo', ''), array('class' => 'form-control required', 'id' => 'cargo')); ?>
                                    <?php echo Form::label('cargo', 'Cargo'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::input('cedula_identidad', Arr::get($_POST, 'cedula_identidad', ''), array('class' => 'form-control required', 'id' => 'cedula_identidad')); ?>
                                    <?php echo Form::label('cedula_identidad', 'Carnet de Identidad'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::input('email', Arr::get($_POST, 'email', '@correos.gob.bo'), array('class' => 'form-control required email', 'id' => 'nivel')); ?>
                                    <?php echo Form::label('email', 'Correo electronico'); ?>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <?php echo Form::select('dependencia', array(0 => 'Jefe de Oficina', 1 => 'Personal dependiente'), Arr::get($_POST, 'dependencia', 1), array('class' => 'form-control')); ?>
                                    <?php echo Form::label('dependencia', 'Nivel cargo'); ?>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?php echo Form::select('superior', array(0 => 'Es jefe'), 0, array('class' => 'form-control', 'id' => 'superior')); ?>
                                    <?php echo Form::label('superior', 'Superior'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?php echo Form::select('genero', array('hombre' => 'Hombre', 'mujer' => 'Mujer'), Arr::get($_POST, 'genero', 'hombre'), array('class' => 'form-control', 'id' => 'nombre')); ?>
                                    <?php echo Form::label('genero', 'Genero'); ?>
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-2">
                                <div class="form-group">
                                    <?php echo Form::input('mosca', Arr::get($_POST, 'mosca', ''), array('class' => 'form-control required', 'id' => 'mosca')); ?>
                                    <?php echo Form::label('mosca', 'Rubrica (mosca)'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <?php echo Form::select('nivel', $niveles, Arr::get($_POST, 'nivel', 2), array('class' => 'form-control', 'id' => 'nivel')); ?>
                                    <?php echo Form::label('nivel', 'Nivel'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group">
                                    <?php echo Form::input('username', Arr::get($_POST, 'username', ''), array('class' => 'form-control required', 'id' => 'username')); ?>
                                    <?php echo Form::label('usuario', 'Usuario'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <div class="form-group">
                                    <?php echo Form::password('password', '', array('class' => 'form-control', 'id' => 'password', 'minlength' => 6, 'autocomplete' => 'new-password')); ?>
                                    <?php echo Form::label('password', 'Contraseña (opcional)'); ?>
                                    <small class="opacity-50">Si se deja en blanco se usa la contraseña por defecto</small>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-12 col-lg-12">
                                    <div class="pull-right">
                                        <?php echo Form::submit('submit', 'Crear Usuario', array('class' => 'btn btn-xl btn-primary', 'id' => 'username')); ?>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
