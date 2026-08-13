
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header> <i class="fa fa-server"></i>
                Restablecer contraseña
            </header>
        </div>
        <div class="card-body">
            <div class="col-md-12">

                <?php if ($error != "") : ?>
                    <div role="alert" class="alert alert-danger">                        
                            <?php echo $error; ?>  
                    </div>

                <?php endif; ?>
                <?php if ($info != "") : ?>
                    <div role="alert" class="alert alert-success">                        
                            <?php echo $info; ?>  
                    </div>

                <?php  else: ?>

                <form class="form" method="post" action="">
                    <?php echo Form::hidden('pass_old', $user->password, array('autocomplete' => "off")); ?>
                    <?php echo Form::hidden('username', $user->username, array('autocomplete' => "off")); ?>
                    <div class="form-group">
                        <?php echo Form::password('pass_new', '', array('class' => 'form-control', 'autocomplete' => "off")); ?>
                        <label for="pass_new">Ingrese contraseña</label>
                    </div>

                    <div class="form-group">
                        <?php echo Form::password('pass_new2', '', array('class' => 'form-control', 'autocomplete' => "off")); ?>
                        <label for="pass_new2">Repita la contraseña ingresada</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::submit('submit', 'Restablecer', array('class' => 'btn btn-sm btn-primary-dark', 'type' => 'submit')); ?>
                    </div>
                </form>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
