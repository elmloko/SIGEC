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
        $('#btnword').click(function () {
            $('#word').val(1);
            return true

        });
        $('#crear').click(function () {
            $('#word').val(0);
            return true
        });
    });

    function validarTipoDeArchivoASubir() {

        var file = $("#file1").val();

        var ext = file.split(".");
        ext = ext[ext.length - 1].toLowerCase();
        var arrayExtensions = ["pdf"];

        if (arrayExtensions.lastIndexOf(ext) == -1) {
            alert("Solo debe subir archivos PDF");
            return false;
        } else {
            return true;
        }

    }
</script>

<div id="documento">

</div>
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header>
                <span class="text-medium">Editar: </span><span class="text-info"><?php echo $documento->nur ?> </span>
            </header>
                            <span class=" pull-right">
                    <a href="/ventanilla" class="btn btn-sm btn-success " title="Recepcionar nuevo"> + Recepcionar
                        otro</a>
                </span>

        </div>
        <div class=" card-body">
            <form class="form" method="post" enctype="multipart/form-data" id="frmCreate" onsubmit="return validarTipoDeArchivoASubir()">
                <div class="col-md-12 col-lg-8">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo Form::input('cite', $documento->cite_original, array('id' => 'cite', 'index' => 1, 'class' => 'form-control required'));
                                echo Form::label('cite', 'Cite original:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('destinatario', $documento->nombre_destinatario, array('id' => 'destinatario', 'index' => 2, 'class' => 'form-control required'));
                                echo Form::label('Destinatario', 'Destinatario:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargodes', $documento->cargo_destinatario, array('id' => 'cargodes', 'index' => 3, 'class' => 'form-control required', 'title' => 'Ejemplo: Lo citado'));
                                echo Form::label('cargodes', 'Cargo destinatario:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('instituciondes', $documento->institucion_destinatario, array('id' => 'instituciondes', 'index' => 4, 'class' => 'form-control required'));
                                echo Form::label('insituciondes', 'Institucion Destinatario:', array('class' => ''));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <!-- Muestra los Archivos  -->
                                 <a href="/print/hr/?code=<?php echo $documento->nur; ?>"
                                    class="btn btn-sm btn-accent-dark" title="Imprimir hoja de ruta"><i
                                    class="fa fa-print"></i> Hoja de Ruta</a>

                                <?php if (isset($archivo->id)) { ?>

                                    <?php
                                    echo Form::input('submit', 'Modificar', array('type' => 'submit', 'class' => 'btn btn-sm btn-primary-dark', 'id' => 'crear'));
                                    ?>
                                    <a href="/route/deriv/?hr=<?php echo $documento->nur; ?>"
                                       class="btn btn-sm btn-default"
                                       title="Derivar a partir del documento, si ya esta derivado muestra el seguimiento">Derivar</a>

                                <?php } ?>


                                <?php
                                //echo Form::input('cite', Arr::get($_POST, 'cite', ''), array('id' => 'cite', 'size' => 40, 'class' => 'form-control required'));
                                //echo Form::label('cite', 'Cite original:', array('class' => ''));
                                ?>
                                <label class="text-xl" for="submit"> </label>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('remitente', $documento->nombre_remitente, array('id' => 'remitente', 'class' => 'form-control required', 'index' => 6));
                                echo Form::label('remitente', 'Remitente:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargorem', $documento->cargo_remitente, array('id' => 'cargorem', 'class' => 'form-control required', 'index' => 7));
                                echo Form::label('cargorem', 'Cargo Remitente:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('institucionrem', $documento->institucion_remitente, array('id' => 'intitucionrem', 'class' => 'form-control required', 'index' => 8));
                                echo Form::label('institucionrem', 'Institucion Remitente:', array('class' => ''));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <textarea id="descripcion" name="descripcion" class="required form-control"
                                          index="5"><?php echo $documento->referencia; ?></textarea>
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
                                    echo Form::input('adjunto', $documento->adjuntos, array('id' => 'adjunto', 'class' => 'form-control'));
                                    echo Form::label('adjunto', 'Adjunto:');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <?php
                                    echo Form::input('hojas', $documento->hojas, array('id' => 'hojas', 'class' => 'form-control'));
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
                <div class="col-md-12 col-lg-4">
                    <?php if (sizeof($error) > 0): ?>
                        <div class="error">
                            <p><span style="float: left; margin-right: .3em;" class=""></span>
                                <?php foreach ($error as $k => $v): ?>
                                <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                            <?php endforeach; ?>
                        </div>
                        <br/>
                    <?php endif; ?>
                    <?php if (sizeof($info) > 0): ?>
                        <div class="info2">
                            <p><span style="float: left; margin-right: .3em;" class=""></span>
                                <?php foreach ($info as $k => $v): ?>
                                <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                            <?php endforeach; ?>
                        </div>
                        <br/>
                    <?php endif; ?>


                    <?php if (isset($archivo->id)): ?>
                        <div class="form-group">
                            Ver archivo: <a href="/download/?file=<?php echo $archivo->id; ?>"
                                            class="label border-gray style-danger"><?php echo substr($archivo->nombre_archivo, 13); ?></a>
                            <input type="hidden" value="<?php echo $archivo->id; ?>" name="id_archivo"/>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-12">
                                <div class="form-group">

                                    <input type="file" class="form-control" name="archivo"/>
                                    <label for="" style=" font-weight: bold; color:#333;">Cambiar documento escaneado
                                        (.pdf < 50M)</label>

                                </div>
                            </div>
                            <div class="col-md-6 col-lg-12">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('submit', 'Cambiar archivo', array('type' => 'submit', 'class' => 'btn btn-sm btn-success', 'id' => 'crear'));
                                    ?>
                                </div>
                            </div>
                        </div>


                    <?php else: ?>
                        <div class="form-group">
                            <input type="hidden" value="0" name="id_archivo"/>
                            <label for="" style=" font-weight: bold; color:#333;">Subir documento escaneado (.pdf <
                                50M)</label>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control" name="archivo" accept="application/pdf" required/>
                        </div>
                        <div class="form-group">

                            <?php
                            echo Form::input('submit', 'Subir archivo', array('type' => 'submit', 'class' => 'btn btn-sm btn-success', 'id' => 'crear'));
                            ?>
                        </div>
                    <?php endif; ?>

                </div>
            </form>
        </div>
    </div>
</div>
