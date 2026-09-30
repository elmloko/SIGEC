<?php
$con_via = $tipo->via != 0;
$es_carta = $documento->id_tipo == 5;
$iniciales = function ($nombre) {
    $partes = preg_split('/\s+/u', trim((string) $nombre), -1, PREG_SPLIT_NO_EMPTY);
    $ini = '';
    foreach (array_slice($partes, 0, 2) as $p) {
        $ini .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8');
    }
    return $ini !== '' ? $ini : '?';
};
$num_archivos = count($archivos);
$derivado = $documento->estado == 1;
// para derivar se exige al menos un archivo digital (salvo el usuario de despacho)
$puede_derivar = $num_archivos > 0 || $user == '95';
?>
<link rel="stylesheet" href="/static/css/documento-form.css?v=2"/>
<script type="text/javascript" src="/static/js/documento-form.js?v=2"></script>
<script type="text/javascript">
    $(function () {
        // el plugin redactor ya no se carga en esta pagina; sin esta verificacion el error detenia el resto de los scripts
        if ($.fn.redactor) {
            $('#descripcion').redactor({lang: 'es', css: 'docstyle.css'});
        }

        // avisa si se sale (o deriva) sin guardar los cambios
        var $form = $('#frm-editar');
        var inicial = $form.serialize();
        var enviando = false;
        $form.on('input change', ':input', function () {
            $('#gd-sin-guardar').toggle($form.serialize() !== inicial);
        });
        $form.on('submit', function (e) {
            if (e.isDefaultPrevented() || ($.fn.valid && !$form.valid())) {
                return;
            }
            enviando = true;
            $('#gd-guardar').html('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');
        });
        $('.gd-requiere-guardar').click(function () {
            if ($form.serialize() !== inicial) {
                if (!confirm('Tiene cambios sin guardar en el documento. ¿Continuar sin guardarlos?')) {
                    return false;
                }
                enviando = true;
            }
        });
        $(window).on('beforeunload', function () {
            if (!enviando && $form.serialize() !== inicial) {
                return 'Tiene cambios sin guardar.';
            }
        });
        // subir un archivo recarga la pagina: no avisar en ese caso
        $('#arch-form').on('submit', function () {
            enviando = true;
        });
    });

    function msg() {
        alert("Para derivar, primero suba el ARCHIVO DIGITAL (PDF) del documento.");
    }

    function validarTipoDeArchivoASubir() {
        var file = $("#file1").val();
        var ext = file.split(".");
        ext = ext[ext.length - 1].toLowerCase();
        if (["pdf"].lastIndexOf(ext) == -1) {
            alert("Solo debe subir archivos PDF");
            return false;
        }
        return true;
    }
</script>
<style type="text/css">
    /* ===== Editar documento: cabecera, pasos y acciones ===== */
    .gd-cab-codigo {
        display: inline-block;
        margin-top: 2px;
        font-size: 13px;
        font-weight: 700;
        color: var(--correos-azul, #1A549A);
        word-break: break-word;
    }
    .gd-mensaje {
        margin: 16px 22px 0;
        padding: 10px 14px;
        border-radius: 8px;
        background: #E6F4EC;
        color: #1E6B3E;
        font-size: 13px;
    }
    .gd-pasos {
        display: flex;
        margin: 16px 22px 0;
        border-radius: 10px;
        background: var(--correos-fondo, #F3F5F8);
        overflow: hidden;
    }
    .gd-paso {
        flex: 1 1 0;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 10px 14px;
        font-size: 12px;
        color: #7a8594;
        border-right: 1px solid #fff;
    }
    .gd-paso:last-child {
        border-right: 0;
    }
    .gd-paso b {
        display: block;
        font-size: 13px;
        color: #4a5568;
    }
    .gd-paso-num {
        flex: 0 0 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        background: #fff;
        color: #9aa4b2;
        border: 2px solid #DCE3EC;
    }
    .gd-paso.hecho .gd-paso-num {
        background: #2E9E5B;
        border-color: #2E9E5B;
        color: #fff;
    }
    .gd-paso.actual {
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .gd-paso.actual .gd-paso-num {
        border-color: var(--correos-amarillo, #FECB34);
        color: #8a6100;
    }
    .gd-paso.actual b {
        color: var(--correos-azul-oscuro, #123E73);
    }
    #gd-sin-guardar {
        display: none;
        margin-left: 8px;
        color: #B7791F;
        font-weight: 600;
    }
    .gd-acciones .btn .fa {
        margin-right: 4px;
    }
    .gd-libreta-edit div#vias {
        max-height: 320px;
    }
    @media (max-width: 767px) {
        .gd-pasos {
            flex-direction: column;
        }
        .gd-paso {
            border-right: 0;
            border-bottom: 1px solid #fff;
        }
    }

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

    /* visor de PDF (modal) */
    .arch-visor .modal-dialog {
        width: 94%;
        max-width: 1200px;
        margin: 20px auto;
    }
    .arch-visor .modal-content {
        border-radius: 8px;
        overflow: hidden;
    }
    .arch-visor .modal-header {
        background: var(--correos-azul, #1A549A);
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
        color: #fff;
        padding: 10px 16px;
    }
    .arch-visor .modal-title {
        color: #fff;
        font-size: 15px;
        word-break: break-word;
        padding-right: 40px;
    }
    .arch-visor .arch-visor-cerrar {
        color: #fff;
        opacity: .9;
        font-size: 30px;
        line-height: 1;
        text-shadow: none;
    }
    .arch-visor .arch-visor-cerrar:hover {
        color: var(--correos-amarillo, #FECB34);
        opacity: 1;
    }
    .arch-visor .arch-visor-acciones {
        margin-top: 6px;
    }
    .arch-visor .modal-body {
        position: relative;
        padding: 0;
        background: #525659;
    }
    .arch-visor iframe {
        display: block;
        width: 100%;
        height: calc(100vh - 140px);
        min-height: 300px;
        border: 0;
    }
    .arch-visor .arch-visor-cargando {
        position: absolute;
        top: 40%;
        left: 0;
        right: 0;
        text-align: center;
        color: #fff;
        font-size: 14px;
    }
    @media (max-width: 767px) {
        .arch-visor .modal-dialog {
            width: auto;
            margin: 8px;
        }
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
    });

    // Ver / Eliminar: se registran sobre document y fuera de $(function) para que funcionen
    // aunque otro script de la pagina falle al cargar.
    $(document).on('click', '.arch-ver', function (e) {
        e.preventDefault();
        var url = $(this).attr('data-url');
        var $visor = $('#arch-visor');
        if (!$.fn.modal || !$visor.length) {
            // sin el plugin de modal: abrir el PDF en otra pestaña
            window.open(url, '_blank');
            return false;
        }
        if (!$visor.parent().is('body')) {
            $visor.appendTo('body'); // fuera de las tarjetas para que el modal quede encima de todo
        }
        $('#arch-visor-titulo').text($(this).attr('data-nombre'));
        $('#arch-visor-descargar').attr('href', $(this).attr('data-descargar'));
        $('#arch-visor-pestana').attr('href', url);
        $('#arch-visor-cargando').show();
        $('#arch-visor-frame').attr('src', url);
        $visor.modal('show');
        return false;
    });
    $(document).on('click', '.arch-eliminar', function () {
        return confirm('¿Eliminar el archivo "' + $(this).attr('data-nombre') + '"?');
    });
    $(document).on('hidden.bs.modal', '#arch-visor', function () {
        $('#arch-visor-frame').attr('src', 'about:blank');
    });
</script>

<!-- visor de PDF de Archivos Digitales -->
<div class="modal fade arch-visor" id="arch-visor" tabindex="-1" role="dialog" aria-labelledby="arch-visor-titulo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close arch-visor-cerrar" data-dismiss="modal" aria-label="Cerrar" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> <span id="arch-visor-titulo"></span></h4>
                <div class="arch-visor-acciones">
                    <a href="#" id="arch-visor-descargar" class="btn btn-xs btn-default-bright"><i class="fa fa-download"></i> Descargar</a>
                    <a href="#" id="arch-visor-pestana" target="_blank" class="btn btn-xs btn-default-bright"><i class="fa fa-external-link"></i> Abrir en otra pesta&ntilde;a</a>
                </div>
            </div>
            <div class="modal-body">
                <div class="arch-visor-cargando" id="arch-visor-cargando">
                    <i class="fa fa-circle-o-notch fa-spin"></i> Cargando documento...
                </div>
                <iframe id="arch-visor-frame" src="about:blank" title="Vista previa del archivo"
                        onload="document.getElementById('arch-visor-cargando').style.display = 'none';"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-lg-8">
        <form action="/documento/edit/<?php echo $documento->id; ?>" class="form form-validate gd-card" method="post" id="frm-editar">
            <!-- tipo de documento, cite y proceso -->
            <div class="gd-cab">
                <div class="gd-cab-icono"><i class="fa fa-pencil"></i></div>
                <div class="gd-cab-titulo">
                    <h2>Editar <?php echo HTML::chars(mb_strtolower($tipo->tipo, 'UTF-8')); ?></h2>
                    <span class="gd-cab-codigo"><?php echo HTML::chars($documento->codigo); ?></span>
                    <small>
                        <?php if ($documento->nur != ''): ?>Hoja de ruta <b><?php echo HTML::chars($documento->nur); ?></b> &middot; <?php endif; ?>
                        Creado el <?php echo $documento->fecha_creacion ? date('d/m/Y H:i', strtotime($documento->fecha_creacion)) : '-'; ?>
                    </small>
                </div>
                <div class="gd-cab-proceso">
                    <label for="proceso">Proceso <span class="gd-req">*</span></label>
                    <?php echo Form::select('proceso', $options, $documento->id_proceso, array('id' => 'proceso', 'class' => 'required')); ?>
                </div>
            </div>

            <?php if (sizeof($mensajes) > 0): ?>
                <div class="gd-mensaje">
                    <?php foreach ($mensajes as $k => $v): ?>
                        <i class="fa fa-check-circle"></i> <strong><?php echo $k; ?></strong> <?php echo $v; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- en que paso esta el documento -->
            <div class="gd-pasos">
                <div class="gd-paso hecho">
                    <span class="gd-paso-num"><i class="fa fa-check"></i></span>
                    <span><b>Generado</b>Con cite asignado</span>
                </div>
                <div class="gd-paso <?php echo $num_archivos > 0 ? 'hecho' : ($derivado ? '' : 'actual'); ?>">
                    <span class="gd-paso-num"><?php echo $num_archivos > 0 ? '<i class="fa fa-check"></i>' : '2'; ?></span>
                    <span><b>Archivo digital</b><?php echo $num_archivos > 0 ? $num_archivos . ($num_archivos == 1 ? ' PDF subido' : ' PDF subidos') : 'Suba el PDF firmado'; ?></span>
                </div>
                <div class="gd-paso <?php echo $derivado ? 'hecho' : ($num_archivos > 0 ? 'actual' : ''); ?>">
                    <span class="gd-paso-num"><?php echo $derivado ? '<i class="fa fa-check"></i>' : '3'; ?></span>
                    <span><b>Derivado</b><?php echo $derivado ? 'Ya está en camino' : ($documento->nur != '' ? 'Pendiente de derivar' : 'Sin hoja de ruta'); ?></span>
                </div>
            </div>

            <!-- encabezado del documento, en el mismo orden que el impreso -->
            <div class="gd-hoja">
                <?php echo Form::hidden('id_doc', $documento->id); ?>
                <!-- A: -->
                <div class="gd-fila">
                    <div class="gd-etiqueta">A:</div>
                    <div>
                        <div class="gd-par<?php echo $es_carta ? ' gd-par-titulo' : ''; ?>">
                            <?php if ($es_carta): ?>
                                <?php echo Form::select('titulo', array('' => 'Título', 'Señor' => 'Señor', 'Señora' => 'Señora', 'Señores' => 'Señores'), $documento->titulo, array('id' => 'titulo', 'title' => 'Título')); ?>
                            <?php endif; ?>
                            <?php echo Form::input('destinatario', $documento->nombre_destinatario, array('id' => 'destinatario', 'autocomplete' => 'off', 'placeholder' => 'Nombre del destinatario')); ?>
                            <?php echo Form::input('cargo_des', $documento->cargo_destinatario, array('id' => 'cargo_des', 'class' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Cargo', 'title' => 'Escriba el cargo del destinatario')); ?>
                            <?php if (!$con_via): ?>
                                <input type="text" name="institucion_des" id="institucion_des" class="gd-completo" autocomplete="off" placeholder="Institución"
                                       value="<?php echo HTML::chars($documento->institucion_destinatario); ?>"/>
                            <?php endif; ?>
                        </div>
                        <div class="gd-ayuda"><span>Escríbalo o elíjalo de la libreta</span><a href="#" class="gd-limpiar"><i class="fa fa-times"></i> limpiar</a></div>
                    </div>
                </div>
                <?php if (!$es_carta): ?>
                    <input type="hidden" name="titulo"/>
                <?php endif; ?>

                <?php if ($con_via): ?>
                    <input type="hidden" name="institucion_des"/>
                    <!-- VIA: -->
                    <div class="gd-fila">
                        <div class="gd-etiqueta">VÍA:<small>opcional</small></div>
                        <div>
                            <div class="gd-par">
                                <?php echo Form::input('via', $documento->nombre_via, array('id' => 'via', 'autocomplete' => 'off', 'placeholder' => 'Nombre')); ?>
                                <?php echo Form::input('cargovia', $documento->cargo_via, array('id' => 'cargovia', 'autocomplete' => 'off', 'placeholder' => 'Cargo')); ?>
                            </div>
                            <div class="gd-ayuda"><span>Déjelo vacío si el documento va directo</span><a href="#" class="gd-limpiar"><i class="fa fa-times"></i> limpiar</a></div>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="via"/>
                    <input type="hidden" name="cargovia"/>
                <?php endif; ?>

                <!-- DE: -->
                <div class="gd-fila">
                    <div class="gd-etiqueta">DE:</div>
                    <div class="gd-remitente">
                        <div class="gd-avatar"><?php echo HTML::chars($iniciales($documento->nombre_remitente)); ?></div>
                        <div>
                            <b><?php echo HTML::chars($documento->nombre_remitente); ?></b>
                            <span><?php echo HTML::chars($documento->cargo_remitente); ?></span>
                        </div>
                        <?php if ($documento->mosca_remitente != ''): ?><span class="gd-mosca" title="Mosca">Mosca: <?php echo HTML::chars($documento->mosca_remitente); ?></span><?php endif; ?>
                    </div>
                </div>
                <?php echo Form::hidden('remitente', $documento->nombre_remitente, array('id' => 'remitente')); ?>
                <?php echo Form::hidden('cargo_rem', $documento->cargo_remitente, array('id' => 'cargo_rem')); ?>
                <?php echo Form::hidden('mosca', $documento->mosca_remitente, array('id' => 'mosca')); ?>

                <!-- REF: -->
                <div class="gd-fila">
                    <div class="gd-etiqueta">REF.: <span class="gd-req">*</span></div>
                    <div>
                        <textarea name="referencia" id="referencia" class="required" title="Escriba la referencia del documento"
                                  placeholder="Asunto del documento"><?php echo HTML::chars($documento->referencia); ?></textarea>
                        <div class="gd-ayuda"><span>Asunto que se verá en la bandeja y en la hoja de ruta</span><span><span id="gd-ref-contador">0</span> caracteres</span></div>
                    </div>
                </div>
            </div>

            <!-- anexos -->
            <div class="gd-anexos">
                <div>
                    <label for="adjuntos">Adjunto</label>
                    <?php echo Form::input('adjuntos', $documento->adjuntos, array('id' => 'adjuntos', 'title' => 'Ejemplo: Lo citado')); ?>
                </div>
                <div>
                    <label for="hojas">Hojas <span class="gd-req">*</span></label>
                    <?php echo Form::input('hojas', $documento->hojas, array('id' => 'hojas', 'class' => 'required', 'title' => 'La casilla está vacía o ingrese número > 0', 'type' => 'number', 'min' => '1')); ?>
                </div>
                <div>
                    <label for="copias">Con copia a</label>
                    <?php echo Form::input('copias', $documento->copias, array('id' => 'copias', 'autocomplete' => 'off', 'placeholder' => 'Opcional')); ?>
                </div>
            </div>

            <input type="hidden" id="hojaruta" value="1" name="hojaruta"/>
            <input type="hidden" id="cite_superior" value="0" name="cite_superior"/>
            <?php echo Form::hidden('descripcion', '', array('id' => 'descripcion')); ?>

            <div class="gd-acciones">
                <span class="gd-izq">
                    <a href="/plantilla/word/<?php echo $documento->id; ?>" target="_blank" class="btn btn-sm btn-default-bright"
                       title="Descargar la plantilla en Word para redactar el contenido"><i class="fa fa-file-word-o"></i> Plantilla Word</a>
                    <span id="gd-sin-guardar"><i class="fa fa-exclamation-circle"></i> Cambios sin guardar</span>
                </span>
                <button type="submit" name="documento" value="Editar" class="btn btn-primary" id="gd-guardar">
                    <i class="fa fa-floppy-o"></i> Guardar cambios
                </button>
                <?php if ($derivado): ?>
                    <a href="/route/trace/?hr=<?php echo urlencode($documento->nur); ?>" class="btn btn-success gd-requiere-guardar" title="Ver seguimiento">
                        <i class="fa fa-map-marker"></i> Ver seguimiento</a>
                <?php elseif ($documento->nur == ''): ?>
                    <a href="/document/asignar/<?php echo $documento->id; ?>" class="btn btn-accent gd-requiere-guardar" title="Asignar una hoja de ruta a este documento">
                        <i class="fa fa-tag"></i> Asignar hoja de ruta</a>
                <?php elseif ($puede_derivar): ?>
                    <a href="/route/deriv/?hr=<?php echo urlencode($documento->nur); ?>" class="btn btn-accent gd-requiere-guardar" title="Derivar documento">
                        <i class="fa fa-send-o"></i> Derivar</a>
                <?php else: ?>
                    <a href="javascript:msg();" class="btn btn-default-bright" title="Primero suba el archivo digital (PDF)" style="opacity:.6">
                        <i class="fa fa-send-o"></i> Derivar</a>
                <?php endif; ?>
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
                    <?php if (!empty($error_archivo)): ?>
                        <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo HTML::chars($error_archivo); ?></div>
                    <?php endif; ?>
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
                                                   data-descargar="/download/?file=<?php echo $a->id; ?>"
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
            <!-- libreta de destinatarios -->
            <div class="gd-card gd-libreta-edit">
                <div class="gd-libreta-cab">
                    <h3><i class="fa fa-users"></i> Libreta de destinatarios</h3>
                    <p>Clic en una persona para llenar: <b id="gd-objetivo-texto">A (destinatario)</b></p>
                    <div class="gd-libreta-buscar">
                        <i class="fa fa-search"></i>
                        <input type="search" id="gd-buscar" placeholder="Buscar por nombre o cargo..." autocomplete="off"/>
                    </div>
                </div>
                <div id="vias">
                    <ul>
                        <?php foreach ($destinatarios as $v): ?>
                            <li class="<?php echo HTML::chars($v['genero']); ?>">
                                <a href="#" class="destino1 destinatario" nombre="<?php echo HTML::chars($v['nombre']); ?>" cargo="<?php echo HTML::chars($v['cargo']); ?>"
                                   title="<?php echo HTML::chars($v['cargo']); ?>" via="" cargo_via="">
                                    <span class="gd-avatar"><?php echo HTML::chars($iniciales($v['nombre'])); ?></span>
                                    <span class="gd-persona"><b><?php echo HTML::chars($v['nombre']); ?></b><small><?php echo HTML::chars($v['cargo']); ?></small></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div id="gd-sin-resultados">Nadie coincide con la búsqueda.</div>
                </div>
                <div class="gd-libreta-pie">
                    <?php echo Form::input('addDest', '+ Agregar persona a la libreta', array('class' => 'btn btn-sm btn-default-bright btn-block', 'type' => 'button', 'id' => 'addDest', 'rel' => $user->id)); ?>
                </div>
            </div>
        </div>
    </div>

</div>





