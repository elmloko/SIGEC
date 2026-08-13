<script type="text/javascript">
    $(function () {
        $('#pass_old').focus();
       // $('#form-pass').validate();
    });
</script>

<?php if (sizeof($info) > 0) { ?>
    <div class="alert alert-success">
            <?php foreach ($info as $k => $e): ?>
                <?php echo $e; ?>
            <?php endforeach; ?>
            
    </div>
    <a href="/">Ir al Inicio</a>
<?php } else { ?>
    <?php if (sizeof($errors) > 0): ?>
        <div class="alert alert-danger">          
                <?php foreach ($errors as $k => $e): ?>
                    <?php echo $e; ?>
                <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="col-md-6 col-lg-6">
        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-lock"></i> Cambio de Contraseña </header>
                <div class="tools">
                    <a class="btn btn-icon-toggle btn-close"><i class="md md-close"></i></a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" action="" class="form form-validate " id="form-pass" name="form-datos">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <?php echo Form::password('pass_old', '', array('class' => 'required form-control', 'autocomplete' => 'off')); ?>
                                <label for="pass_old">Ingrese su Contraseña actual:</label>
                            </div> 
                            <div class="form-group">
                                <?php echo Form::password('pass1', '', array('class' => 'required  form-control', 'autocomplete' => 'off')); ?>
                                <label for="pass2">Ingrese su Contraseña nueva:</label>
                            </div> 
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <?php echo Form::password('pass2', '', array('class' => 'required  form-control', 'autocomplete' => 'off')); ?>
                                <label for="pass2">Repita su Contraseña nueva:</label>
                            </div> 
                        </div>

                    </div>

                   <input type="submit" value="Cambiar contraseña" class="btn btn-sm btn-primary-dark" />
                </form>
            </div>
        </div>



    </div><!--end .col -->
<?php
}?>