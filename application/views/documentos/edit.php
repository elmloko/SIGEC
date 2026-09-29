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

    /* ===== Archivos Digitales ===== */
    .archivos-digitales .arch-contador {
        background: var(--correos-azul, #1A549A);
        color: #fff;
        margin-left: 6px;
        vertical-align: middle;
    }
    .archivos-digitales .arch-zona {
        position: relative;
        display: block;
        margin: 0 0 10px;
        padding: 18px 12px;
        border: 2px dashed var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        background: var(--correos-fondo, #F3F5F8);
        text-align: center;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .archivos-digitales .arch-zona:hover,
    .archivos-digitales .arch-zona.arrastrando {
        border-color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .archivos-digitales .arch-zona.con-archivo {
        border-style: solid;
        border-color: var(--correos-amarillo, #FECB34);
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    /* el input queda invisible pero presente, para que el navegador valide "required" */
    .archivos-digitales .arch-zona input[type=file] {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        left: 50%;
        top: 50%;
    }
    .archivos-digitales .arch-zona-icono {
        display: block;
        font-size: 30px;
        color: var(--correos-azul, #1A549A);
        margin-bottom: 4px;
    }
    .archivos-digitales .arch-zona-texto {
        display: block;
        font-size: 12px;
        color: #5b6675;
        font-weight: normal;
    }
    .archivos-digitales .arch-zona-archivo {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        word-break: break-word;
    }
    .archivos-digitales .arch-subir {
        margin-bottom: 16px;
    }
    .archivos-digitales .arch-vacio {
        text-align: center;
        color: #8a94a3;
        padding: 16px 0 4px;
    }
    .archivos-digitales .arch-vacio .fa {
        font-size: 32px;
        opacity: .5;
    }
    .archivos-digitales .arch-vacio p {
        margin: 6px 0 0;
    }
    .archivos-digitales .arch-lista {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .archivos-digitales .arch-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px;
        margin-bottom: 8px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        background: #fff;
        transition: box-shadow .15s, border-color .15s;
    }
    .archivos-digitales .arch-item:hover {
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 2px 6px rgba(18, 62, 115, .12);
    }
    .archivos-digitales .arch-icono {
        flex: 0 0 auto;
        font-size: 26px;
        line-height: 1;
        color: #d32f2f;
        padding-top: 2px;
    }
    .archivos-digitales .arch-info {
        flex: 1 1 auto;
        min-width: 0;
    }
    .archivos-digitales .arch-nombre {
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        word-break: break-word;
        line-height: 1.3;
    }
    .archivos-digitales .arch-meta {
        font-size: 11px;
        color: #8a94a3;
        margin: 2px 0 6px;
    }
    .archivos-digitales .arch-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .archivos-digitales .arch-acciones .btn {
        margin: 0;
    }
    .archivos-digitales .arch-eliminar:hover {
        color: #fff;
        background: #d32f2f;
        border-color: #d32f2f;
    }
</style>
<script type="text/javascript">
    // Archivos Digitales: arrastrar y soltar, vista previa y confirmacion al eliminar
    $(function () {
        var $zona = $('#arch-zona');
        var $input = $('#file1');
        var textoInicial = $('#arch-zona-texto').html();

        function mostrarSeleccion() {
            var f = $input[0].files && $input[0].files[0];
            if (f) {
                var tam = f.size >= 1048576 ? (f.size / 1048576).toFixed(2) + ' MB' : Math.round(f.size / 1024) + ' KB';
                $('#arch-zona-archivo').text(f.name + ' (' + tam + ')');
                $('#arch-zona-texto').html('Archivo listo para subir. Haga clic para cambiarlo.');
                $zona.addClass('con-archivo');
                $('#arch-subir').prop('disabled', false);
            } else {
                $('#arch-zona-archivo').text('');
                $('#arch-zona-texto').html(textoInicial);
                $zona.removeClass('con-archivo');
                $('#arch-subir').prop('disabled', true);
            }
        }

        $input.on('change', mostrarSeleccion);
        $zona.on('dragover dragenter', function (e) {
            e.preventDefault();
            $zona.addClass('arrastrando');
        }).on('dragleave dragend drop', function (e) {
            e.preventDefault();
            $zona.removeClass('arrastrando');
        }).on('drop', function (e) {
            var archivos = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
            if (archivos && archivos.length) {
                try {
                    $input[0].files = archivos;
                } catch (err) {
                    alert('Su navegador no permite arrastrar archivos, haga clic para elegirlo.');
                    return;
                }
                mostrarSeleccion();
            }
        });
        $('#arch-form').on('submit', function (e) {
            // la validacion original (onsubmit) pudo cancelar el envio
            if (e.isDefaultPrevented() || $('#arch-subir').prop('disabled')) {
                return false;
            }
            $('#arch-subir').html('<i class="fa fa-circle-o-notch fa-spin"></i> Subiendo...');
            setTimeout(function () {
                $('#arch-subir').prop('disabled', true);
            }, 0);
        });

        $('.archivos-digitales').on('click', '.arch-ver', function () {
            eModal.iframe({
                url: $(this).data('url'),
                title: $(this).data('nombre'),
                size: eModal.size.xl
            });
            return false;
        }).on('click', '.arch-eliminar', function () {
            return confirm('¿Eliminar el archivo "' + $(this).data('nombre') + '"?');
        });
    });
</script>

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
            <div class="card card-underline archivos-digitales">
                <div class="card-head">
                    <header><i class="fa fa-paperclip"></i> Archivos Digitales
                        <span class="badge arch-contador"><?php echo count($archivos); ?></span>
                    </header>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" action="" id="arch-form"
                          onsubmit="return validarTipoDeArchivoASubir()">
                        <label for="file1" class="arch-zona" id="arch-zona">
                            <input type="file" id="file1" name="archivo" accept="application/pdf" required/>
                            <i class="fa fa-cloud-upload arch-zona-icono"></i>
                            <span class="arch-zona-texto" id="arch-zona-texto">
                                <b>Arrastre un PDF aqu&iacute;</b> o haga clic para elegirlo
                            </span>
                            <span class="arch-zona-archivo" id="arch-zona-archivo"></span>
                        </label>
                        <input type="hidden" name="id_doc" value="<?php echo $documento->id; ?>"/>
                        <button type="submit" name="adjuntar" value="1" class="btn btn-primary btn-block arch-subir" id="arch-subir" disabled>
                            <i class="fa fa-upload"></i> Subir archivo
                        </button>
                    </form>

                    <?php if (count($archivos) == 0): ?>
                        <div class="arch-vacio">
                            <i class="fa fa-file-pdf-o"></i>
                            <p>Este documento a&uacute;n no tiene archivos digitales.</p>
                        </div>
                    <?php else: ?>
                        <ul class="arch-lista">
                            <?php foreach ($archivos as $a): ?>
                                <?php
                                $nombre = substr($a->nombre_archivo, 13);
                                $es_pdf = stripos($a->extension, 'pdf') !== FALSE || preg_match('/\.pdf$/i', $nombre);
                                $tamanio = $a->tamanio >= 1048576
                                    ? number_format($a->tamanio / 1048576, 2) . ' MB'
                                    : number_format($a->tamanio / 1024, 0) . ' KB';
                                ?>
                                <li class="arch-item">
                                    <div class="arch-icono"><i class="fa <?php echo $es_pdf ? 'fa-file-pdf-o' : 'fa-file-o'; ?>"></i></div>
                                    <div class="arch-info">
                                        <div class="arch-nombre" title="<?php echo HTML::chars($nombre); ?>"><?php echo HTML::chars($nombre); ?></div>
                                        <div class="arch-meta">
                                            <?php echo $tamanio; ?> &middot;
                                            <?php echo $a->fecha ? date('d-m-Y H:i', strtotime($a->fecha)) : ''; ?>
                                        </div>
                                        <div class="arch-acciones">
                                            <?php if ($es_pdf): ?>
                                                <a href="javascript:void(0);" class="btn btn-xs btn-default-bright arch-ver"
                                                   data-url="/download/?file=<?php echo $a->id; ?>&amp;ver=1"
                                                   data-nombre="<?php echo HTML::chars($nombre); ?>" title="Ver sin descargar">
                                                    <i class="fa fa-eye"></i> Ver
                                                </a>
                                            <?php endif; ?>
                                            <a href="/download/?file=<?php echo $a->id; ?>" class="btn btn-xs btn-default-bright" title="Descargar">
                                                <i class="fa fa-download"></i> Descargar
                                            </a>
                                            <a href="/archivo/eliminar/<?php echo $a->id; ?>" class="btn btn-xs btn-default-bright arch-eliminar"
                                               data-nombre="<?php echo HTML::chars($nombre); ?>" title="Eliminar">
                                                <i class="fa fa-trash-o"></i> Eliminar
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>





