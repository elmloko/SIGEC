<script type="text/javascript">
    var stat = 0;
    var $this;
    var $cargo;
    $(function () {
        $('#destinatario').focus();
        $('#destinatario').focus(function () {
            $this = $(this);
            $cargo = $('#cargo_des');
        });
        $('#via').focus(function () {
            $this = $(this);
            $cargo = $('#cargovia');
        });
        $('a.destino1').click(function () {
            var destino = $(this);
            if ($cargo != undefined) {
                var nombre = $(this).attr('nombre');
                var cargo = $(this).attr('cargo');
                // var a=$this.nodeName;
                // alert(index);
                // alert(foco.val());
                $this.val(nombre);
                $cargo.val(cargo);
            } else {
                $('#destinatario').val(destino.attr('nombre'));
                $('#cargo_des').val(destino.attr('cargo'));
            }
            //console.log($this);
            //console.log($('input:eq(' + parseInt(index) + ')').next().next().val(cargo));
            //console.log($('input:eq(' + parseInt(index) + 1 + ')').val(cargo));
            //$('input:eq('+index+')').next().val(cargo);
            return false;
        });
        $('#noHojaRuta').click(function () {
            $('#hojaruta').val(0);
            return true
        });
        $('#cite_sup').click(function () {
            $('#cite_superior').val(1);
            $('#hojaruta').val(0);
            stat = 1;
            return true
        });
        $('#addDest').click(function () {
            var id_user = $(this).attr('rel');
            eModal.setEModalOptions({
                loadingHtml: '<span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span><h4>Cargando usuarios...</h4>',

            });
            eModal.iframe('/content/destinos/' + id_user, 'Agregar Destinatario')

        });
        /* $("#theTable").tablesorter({sortList:[[1,0]],
         widgets: ['zebra'],
         headers: {
         0: { sorter:false}
         }
         }); */
    });
    $(function () {
        /* var tabContainers = $('div.tabs > div');
         tabContainers.hide().filter(':first').show();
         $('div.tabs ul.tabNavigation a').click(function() {
         tabContainers.hide();
         tabContainers.filter(this.hash).show();
         $('div.tabs ul.tabNavigation a').removeClass('selected');
         $(this).addClass('selected');
         return false;
         }).filter(':first').click();
         */
        $('#descripcion').redactor({lang: 'es', css: 'docstyle.css'});
        //incluir destinatario
        $('a.destino').click(function () {
            var nombre = $(this).attr('nombre');
            var cargo = $(this).attr('cargo');
            var via = $(this).attr('via');
            var cargo_via = $(this).attr('cargo_via');
            $('#destinatario').val(nombre);
            $('#cargo_des').val(cargo);
            $('#via').val(via);
            $('#cargovia').val(cargo_via);
            $('#referencia').focus();
            return false;
        });
        $('#btnword').click(function () {
            $('#word').val(1);
            return true

        });
        $('#save').click(function () {
            $('#frmEditar').submit();
        });
        $('#subir').click(function () {
            var id = $(this).attr('rel');
            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 700) / 2;
            top = (top - 500) / 2;
            var r = window.showModalDialog("/archivo/add/" + id, "", "center:0;dialogWidth:600px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            alert(r);
            return false;
        });
        // $("input.file").si();
        $('select').select2();
    });

    function msg() {
        alert("A usted le falta agregar 'ARCHIVO DIGITAL'");
    }

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

        /*
        var file = $("#file1").val();
        var filesizeBytes = document.getElementById('file1').files[0].size;

        var filesizeKB = (filesizeBytes / 1024 ).toFixed(2);
        var filesizeMB = (filesizeBytes / (1024 * 1024)).toFixed(2);

        var ext = file.split(".");
        ext = ext[ext.length - 1].toLowerCase();
        var arrayExtensions = ["pdf"];

        // Validamos que el tamaño de archivo sea >= 30Kb
        if (filesizeKB >= 30) {
            if (arrayExtensions.lastIndexOf(ext) == -1) {
                alert("Solo debe subir archivos PDF");
                return false;
            } else {
                return true;
            }
        }
        else {
            alert("Debe subir archivos >= 30 KB");
            return false;
        }
         */
    }

</script>
<style type="text/css">
    form#frmCreate {
        padding: 0 5px;
        margin: 0;
    }

    /* .cke_contents{height: 500px;}*/
    /* cke_skin_kama{border: none;}   */

</style>

<div class="row">

    <div class="col-lg-8">
        <form action="/documento/edit/<?php echo $documento->id; ?>" class="form form-validate" method="post"
              id="frm-editar">
            <div class="card card-underline">
                <?php if (sizeof($mensajes) > 0): ?>
                    <div class="alert alert-success ">
                        <p>
                            <?php foreach ($mensajes as $k => $v): ?>
                            <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="card-head  ">
                    <header><i class="fa fa-pencil"></i> <?php echo $documento->nur ?> | <span
                            class="text-primary"> <?php // echo $tipo->tipo    ?><?php echo $documento->codigo ?></span>
                    </header>
                    <div class="tools">
                        <input type="submit" name="documento" value="Editar" class="btn btn-sm btn-primary-dark"/>

                        <?php if ($documento->estado == 1) { ?>

                            <a href="/route/trace/?hr=<?php echo $documento->nur; ?>" class="btn btn-sm btn-success"
                               title="Ver seguimiento"><i class="md md-verified-user"></i> Ver</a>
                        <?php } else { ?>

                            <?php
                            $error_subir_ningun_archivo = false;
                            ?>

                            <?php if ($documento->nur != '') { ?>

                                <?php if (count($archivos) > 0) { ?>
                                <?php } else {
                                    $error_subir_ningun_archivo = true;
                                    //      Se coloca excepción para el usuario de 'despacho' (no requiere subir adjuntos para derivar)
                                    if ($user == '95') {
                                        $error_subir_ningun_archivo = false;
                                    }
                                } ?>

                                <!--        Verificamos errores en el formulario -->
                                <?php if ($error_subir_ningun_archivo == false) { ?>

                                    <a href="/route/deriv/?hr=<?php echo $documento->nur; ?>"
                                       class="btn btn-sm btn-accent"
                                       title="Derivar documento"><i class="fa fa-send-o"></i> Derivar</a>

                                <?php } else { ?>
                                    <a href="javascript:msg();" style="color: #b7260b" class="button"
                                       title="Derivar documento">
                                    </a>
                                <?php } ?>


                            <?php } else { ?>

                                <a href="/document/asignar/<?php echo $documento->id; ?>" class="button">Asignar HR</a>
                            <?php } ?>

                        <?php } ?>
                        <a href="/plantilla/word/<?php echo $documento->id; ?>" target="_blank"
                           title="Editar este documento en word" class="btn btn-sm btn-default"><i
                                class="fa fa-file-word-o"></i> Plantilla</a>

                        <!--<a href="" class="button" onclick="javascript:history.back(); return false;" >Cancelar</a>-->


                    </div>
                </div><!--end .card-head -->

                <div class="card-body no-padding  ">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <?php echo Form::select('proceso', $options, $documento->id_proceso, array('class' => 'required form-control')); ?>
                            <br/>
                            <label for="proceso">Proceso: </label>
                        </div>
                        <?php if ($documento->id_tipo == 5): ?>
                            <div class="form-group">
                                <?php $titulo = array('', 'Señor' => 'Señor', 'Señora' => 'Señora', 'Señores' => 'Señores'); ?>
                                <?php echo Form::select('titulo', $titulo, $documento->titulo); ?>
                                <label>Titulo:</label>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="titulo"/>
                        <?php endif; ?>
                        <div class="form-group">
                            <?php
                            echo Form::hidden('id_doc', $documento->id);
                            echo Form::input('destinatario', $documento->nombre_destinatario, array('id' => 'destinatario', 'class' => 'form-control', 'index' => '101'));
                            echo Form::label('destinatario', 'Nombre del destinatario:', array('index' => '100'));
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo Form::input('cargo_des', $documento->cargo_destinatario, array('id' => 'cargo_des', 'size' => 48, 'class' => 'form-control required', 'index' => '103'));
                            echo Form::label('destinatario', 'Cargo Destinatario:', array('class' => 'form', 'index' => '102'));
                            ?>
                        </div>

                        <?php if ($tipo->via == 0): ?>
                            <div class="form-group">
                                <label>Institución Destinatario</label>
                                <input type="text" size="40" class="form-control"
                                       value="<?php echo $documento->institucion_destinatario; ?>"
                                       name="institucion_des"/>
                                <input type="hidden" name="via"/>
                                <input type="hidden" name="cargovia"/>
                            </div>
                        <?php else: ?>

                            <input type="hidden" size="40" name="institucion_des"/>
                            <div class="form-group">
                                <?php
                                echo Form::input('via', $documento->nombre_via, array('id' => 'via', 'size' => 48, 'class' => 'form-control '));
                                echo Form::label('via', 'Via:', array('class' => 'form'));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargovia', $documento->cargo_via, array('id' => 'cargovia', 'size' => 48, 'class' => 'form-control '));
                                echo Form::label('cargovia', 'Cargo Via:', array('class' => 'form'));
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">

                            <textarea name="referencia" id="referencia"
                                      class="required form-control"><?php echo $documento->referencia ?></textarea>
                            <label for="referencia">Referencia</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('remitente', $documento->nombre_remitente, array('id' => 'remitente', 'size' => 35, 'class' => 'form-control required', 'readonly'));
                                    echo Form::label('remitente', 'Remitente:', array('class' => 'form', 'readonly'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('mosca', $documento->mosca_remitente, array('id' => 'mosca', 'class' => 'form-control', 'size' => 5, 'readonly'));
                                    echo Form::label('mosca', 'Mosca:');
                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <?php
                            echo Form::input('cargo_rem', $documento->cargo_remitente, array('id' => 'cargo_rem', 'size' => 48, 'class' => 'required form-control', 'readonly'));
                            echo Form::label('cargo', 'Cargo Remitente:', array('class' => 'form', 'readonly'));
                            ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('adjuntos', $documento->adjuntos, array('id' => 'adjuntos', 'size' => 48, 'class' => ' form-control', 'title' => 'Ejemplo: Lo citado'));
                                    echo Form::label('adjuntos', 'Adjunto:', array('class' => 'form'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('hojas', $documento->hojas, array('id' => 'hojas', 'class' => 'required form-control', 'title' => 'La casilla está vacía o ingrese número > 0', 'type' => 'number', 'min' => '1'));
                                    echo Form::label('hojas', 'Nro hojas:', array('class' => 'form'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('copias', $documento->copias, array('id' => 'adjuntos', 'class' => 'form-control '));
                                    echo Form::label('copias', 'Con copia a:', array('class' => 'for'));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?php echo Form::input('addDest', '+ Agregar Destinatario', array('class' => 'btn btn-sm btn-default', 'type' => 'button', 'id' => 'addDest', 'rel' => $user->id)); ?>
                                <div id="vias">
                                    <ul>
                                        <!-- destinatarios -->
                                        <?php foreach ($destinatarios as $v) { ?>
                                            <li class="<?php echo $v['genero'] ?> "><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1 destinatario', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => '', 'cargo_via' => '')); ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="hojaruta" value="1" name="hojaruta"/>
                        <input type="hidden" id="cite_superior" value="0" name="cite_superior"/>

                        <div class="descripcion" style="width: 680px; float: left; display: none; ">
                            <?php
                            echo Form::hidden('descripcion', '', array('id' => 'descripcion'));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="card card-underline">
                <div class="card-head">
                    <header>Archivos Digitales</header>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" action=""
                          onsubmit="return validarTipoDeArchivoASubir()">
                        <input type="file" class="file" id="file1" name="archivo" class="formControl form-control"
                               accept="application/pdf" required/>
                        <label>Seleccione un archivo para subir...</label>
                        <input type="hidden" name="id_doc" value="<?php echo $documento->id; ?>"/>
                        <input type="submit" name="adjuntar" value="Subir archivo" class="btn btn-sm btn-primary-dark"/>
                    </form>
                    <hr/>
                    <div class=" table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>NOMBRE ARCHIVO</th>
                                <th>TAMA&Ntilde;O</th>
                                <th>FECHA DE SUBIDA</th>
                                <th>OPCION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($archivos as $a): ?>
                                <tr>
                                    <td>
                                        <a href="/download/?file=<?php echo $a->id; ?>"><?php echo substr($a->nombre_archivo, 13) ?></a>
                                    </td>
                                    <td align="center"><?php echo number_format(($a->tamanio / 1024) / 1024, 2) . ' MB'; ?></td>
                                    <td align="center"><?php echo $a->fecha ?></td>
                                    <td align="center"><a href="/archivo/eliminar/<?php echo $a->id; ?>"
                                                          class="delete btn btn-sm btn-danger"><i
                                                class="fa fa-trash-o"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>





