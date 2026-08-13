<?php
if (isset($error)):
    ?>
    <div class="row">
        <div class="col-md-12">
            <div role="alert" class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        </div>
    </div>

<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header>
                    <span class="text-lg text-primary"> Recuperar Contrase&ntilde;a </span>    
                </header>
            </div>

            <div class="card-body">
                <form class="form" action="" method="post">
                    <div class="form-group">
                        <?php echo Form::input('email', '@oopp.gob.bo', array('class' => 'form-control', 'id' => 'email')) ?>
                        <label for="email">Escriba su correo electronico</label>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Recuperar Contraseña" class="btn btn-sm btn-success" />
                        <a href="/login" class="btn btn-sm btn-default"><i class="fa fa-server"></i> Regrear al Login</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $("#email").focus();
    });
</script>  
