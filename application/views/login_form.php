<div class="row">
    <div class="col-sm-6">
        <br/>
        <span class="text-lg  text-primary"> Formulario de Autenticaci&oacute;n </span>
        <br/><br/>
        <form class="form form-validate floating-label" action="" accept-charset="utf-8" method="post">
            <div class="form-group">
                <input type="text" value="<?php echo Arr::get($_POST, 'username', '') ?>" title="Ingrese su nombre de usuario por favor" class="required form-control"  maxlength="30" name="username" id="username"/>

                <label for="username">Username</label>
            </div>
            <div class="form-group">
                <input type="password" class="required form-control"  maxlength="25"  autocomplete="off" title="Ingrese su contrase&ntilde;a"  name="password" id="password"/>
                <label for="password">Password</label>
                <p class="help-block"><a href="/login/pass">Olvido su contraseña?</a></p>
            </div>
            <br/>
            <div class="row">
                <div class="col-xs-6 text-left">
                    <div class="checkbox checkbox-inline checkbox-styled">
                        <label>
                            <input type="checkbox" name="remember" value="1"> <span>Recordar</span>
                        </label>
                    </div>
                </div><!--end .col -->
                <div class="col-xs-6 text-right">
                    <input type="submit" style=" width: 150px;" class="login-button btn btn-primary btn-raised" id="submit" name="submit" value="Iniciar sesión"/>

                </div><!--end .col -->
            </div><!--end .row -->
        </form>
        <?php if (isset($errors['login'])): ?>
            <div id="error" class="info">        
                <div id="error_login" >
                    <span><?php echo $errors['login']; ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div><!--end .col -->
    <div class="col-sm-5 col-sm-offset-1 ">

        <h3 class="text-light text-center">
            <img title="" alt="" src="/media/logo.png" typeof="foaf:Image" /><hr>Sistema de Gestión de Correspondencia</a>
        </h3>
        <a class="btn btn-xs btn-block btn-raised btn-success" target="_blank" href="http://sigec.oopp.gob.bo/download/manual"><i class="fa fa-file-pdf-o"></i> Descargar Manual</a>
        <p>
        <p><i> Envie y reciba su correspondencia de manera mas sencilla  y rápida.</i>
        <i> Permite Gerenar todo tipo de documentos. </i></p>
        
        <a class="text-light text-center text-primary" target="_blank"
           href="http://192.168.10.50/consulta/advanced">
            <i class="fa fa-search"></i> Búsqueda de Correspondencia AEVIVIENDA
        </a>        
    </div><!--end .col -->

<!--
    <div class="col-sm-6">
        <a class="btn btn-xs btn-warning" target="_blank"
           href="http://192.168.10.50/consulta/advanced">
            <i class="fa fa-file-pdf-o"></i> Búsqueda de Correspondencia AEVIVIENDA
        </a>
    </div>
-->
</div><!--end .row -->

<script type="text/javascript">
    $(function () {
        $("#username").focus();
        //$('#error').fadeIn(5000).fadeOut(5000);
        //$('#loginform').validate();
    });
</script>
