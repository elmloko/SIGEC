<?php ?>
<script type="text/javascript">
    $(function () {
//incluir destinatario
        $('a.destino').click(function () {
            var nombre = $(this).attr('nombre');
            var cargo = $(this).attr('cargo');
            var via = $(this).attr('institucion');
            $('#destinatario').val(nombre);
            $('#cargodes').val(cargo);
            $('#instituciondes').val(via);
            $('#referencia').focus();
            return false;
        });
        $('select').select2();
        //Verificación de correspondencia
        $('#cite').bind('blur', function () {

            var cite = $(this).val();
            if (cite == '') {
                $('#cite').val('S/C');
            }
            var oficina = $('#oficina').val();
            $.ajax({
                type: "POST",
                data: {cite: cite, oficina: oficina},
                url: "/ajax/vercite",
                dataType: "json",
                success: function (item) {
                    if (item.error) {
                        alert(item.error);
                        $('#cite').addClass('err').focus();
                        //$('input#crear').attr({'disabled':'disabled'});

                    }
                    else {
                        $('#cite').removeClass('err');
                        $('input#crear').removeAttr('disabled');
                    }
                },
                error: function () {
                }
            });
        });
        $('#cite').focus();
    });


    function validacionesDelFormulario() {

        var destinatario = $("#destinatario").val();
        var remitente = $("#remitente").val();
        var referencia = $("#descripcion").val();

        if (destinatario.length > 0&&remitente.length > 0&& referencia.length > 0) {
            return true;
        } else {
            alert("Usted debe completar los campos de 'DESTINATARIO' o 'REMITENTE' o 'REFERENCIA'");
            return false;
        }


    }
</script>
<style>
    input.err {
        border: 1px solid #D61010;
    }
</style>
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header>
                <span class="text-medium">Formulario de recepci&oacute;n de documentos</span>
            </header>
        </div>
        <div class=" card-body">
            <form class="form" method="post" enctype="multipart/form-data" id="frmCreate"
                  onsubmit="return validacionesDelFormulario()">
                <div class="col-md-12 col-lg-8">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo Form::input('cite', Arr::get($_POST, 'cite', ''), array('id' => 'cite', 'index' => 1, 'class' => 'form-control required'));
                                echo Form::label('cite', 'Cite original:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('destinatario', Arr::get($_POST, 'destinatario', ''), array('id' => 'destinatario', 'index' => 2, 'class' => 'form-control required'));
                                echo Form::label('Destinatario', 'Destinatario:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargodes', Arr::get($_POST, 'cargodes', ''), array('id' => 'cargodes', 'index' => 3, 'class' => 'form-control required', 'title' => 'Ejemplo: Lo citado'));
                                echo Form::label('cargodes', 'Cargo destinatario:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('instituciondes', Arr::get($_POST, 'instituciondes', ''), array('id' => 'instituciondes', 'index' => 4, 'class' => 'form-control required'));
                                echo Form::label('insituciondes', 'Institucion Destinatario:', array('class' => ''));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo Form::input('submit', 'Recepcionar Documento', array('type' => 'submit', 'index' => 10, 'class' => 'btn  btn-primary-dark', 'id' => 'crear'));
                                ?>

                                <?php
                                //echo Form::input('cite', Arr::get($_POST, 'cite', ''), array('id' => 'cite', 'size' => 40, 'class' => 'form-control required'));
                                //echo Form::label('cite', 'Cite original:', array('class' => ''));
                                ?>
                                <label class="text-xl" for="submit"> </label>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('remitente', '', array('id' => 'remitente', 'class' => 'form-control required', 'index' => 6));
                                echo Form::label('remitente', 'Remitente:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargorem', '', array('id' => 'cargorem', 'class' => 'form-control required', 'index' => 7));
                                echo Form::label('cargorem', 'Cargo Remitente:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('institucionrem', '', array('id' => 'intitucionrem', 'class' => 'form-control required', 'index' => 8));
                                echo Form::label('institucionrem', 'Institucion Remitente:', array('class' => ''));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <textarea id="descripcion" name="descripcion" class="form-control" index="5"></textarea>
                                <?php
                                echo Form::label('referencia', 'Referencia:', array('class' => ''));
                                ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    echo Form::select('motivo', $motivos, '', array('id' => 'motivo', 'class' => 'form-control'));
                                    echo Form::label('motivo', 'Motivo:');
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    echo Form::input('adjunto', '', array('id' => 'adjunto', 'class' => 'form-control'));
                                    echo Form::label('adjunto', 'Adjunto:');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <?php
                                    echo Form::input('hojas', '', array('id' => 'hojas', 'class' => 'form-control'));
                                    echo Form::label('hojas', 'N° hojas:');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php echo Form::label('dest', 'Destinatarios:'); ?>
                            <div id="vias">
                                <ul>
                                    <?php foreach ($destinos as $d): ?>
                                        <li class="<?php echo $d->genero ?>"><?php echo HTML::anchor('#', $d->nombre, array('class' => 'destino', 'nombre' => $d->nombre, 'title' => $d->cargo, 'cargo' => $d->cargo, 'institucion' => $d->entidad)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
