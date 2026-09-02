<div class="row" style="padding: 15px;">
    <div class="col-md-12">
        <p>Esta es la contraseña que se asigna automáticamente a todo usuario nuevo y al usar "Resetear password".
            Al guardar, se actualizará este valor y <strong>solo</strong> los usuarios que todavía tengan la
            contraseña por defecto actual (los que no la hayan cambiado) recibirán la nueva. Los usuarios con
            una contraseña distinta no se modifican.</p>

        <div class="form-group">
            <label>Contraseña actual</label>
            <input type="text" id="pass-actual" class="form-control" value="<?php echo HTML::chars($passActual); ?>" readonly />
        </div>

        <div class="form-group">
            <label>Nueva contraseña por defecto</label>
            <input type="text" id="pass-nueva" class="form-control" placeholder="Minimo 6 caracteres" />
        </div>

        <div id="pass-defecto-msg"></div>

        <div class="pull-right" style="margin-top: 10px;">
            <a id="guardar-pass-defecto" class="btn btn-primary-dark">Guardar</a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#guardar-pass-defecto').click(function () {
        var nuevo = $('#pass-nueva').val();
        if (!nuevo || nuevo.length < 6) {
            $('#pass-defecto-msg').html('<div class="alert alert-danger">La contraseña debe tener al menos 6 caracteres</div>');
            return;
        }
        $.ajax({
            type: 'POST',
            data: {nuevo_pass: nuevo},
            url: '/admin/ajax/cambiarPassDefecto',
            dataType: 'json',
            success: function (data) {
                if (data.ok) {
                    $('#pass-actual').val(nuevo);
                    $('#pass-nueva').val('');
                    $('#pass-defecto-msg').html('<div class="alert alert-success">Contraseña por defecto actualizada. Usuarios afectados: ' + data.count + '</div>');
                } else {
                    $('#pass-defecto-msg').html('<div class="alert alert-danger">' + data.msg + '</div>');
                }
            },
            error: function () {
                $('#pass-defecto-msg').html('<div class="alert alert-danger">Ocurrio un error al guardar</div>');
            }
        });
    });
</script>
