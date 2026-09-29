<?php
// admin/document tambien usa esta vista sin estas variables
$es_autor = isset($es_autor) ? (bool) $es_autor : FALSE;
$nombre_proceso = isset($nombre_proceso) ? $nombre_proceso : '';
$derivado = ((int) $d->estado === 1);
$tiene_hr = trim($d->nur) != '';
$lista_archivos = array();
foreach ($archivo as $a) {
    $nombre = substr($a->nombre_archivo, 13);
    $lista_archivos[] = array(
        'id' => $a->id,
        'nombre' => $nombre,
        'es_pdf' => stripos($a->extension, 'pdf') !== FALSE || preg_match('/\.pdf$/i', $nombre),
        'tamanio' => $a->tamanio >= 1048576 ? number_format($a->tamanio / 1048576, 2) . ' MB' : number_format($a->tamanio / 1024, 0) . ' KB',
        'fecha' => $a->fecha ? date('d/m/Y H:i', strtotime($a->fecha)) : '',
    );
}
?>
<script type="text/javascript">
    $(function () {
        $("#imprime").click(function () {
            window.print();
            return false;
        });
        $('#det-regresar').click(function () {
            if (document.referrer && history.length > 1) {
                history.back();
                return false;
            }
        });
        // vista previa del PDF en un modal (bootstrap); al cerrar se limpia el visor
        $(document).on('click', '.det-ver-pdf', function (e) {
            e.preventDefault();
            var url = $(this).attr('data-url');
            var $visor = $('#det-visor');
            if (!$.fn.modal || !$visor.length) {
                window.open(url, '_blank');
                return false;
            }
            $visor.appendTo('body');
            $('#det-visor-titulo').text($(this).attr('data-nombre'));
            $('#det-visor-descargar').attr('href', $(this).attr('data-descargar'));
            $('#det-visor-pestana').attr('href', url);
            $('#det-visor-cargando').show();
            $('#det-visor-frame').attr('src', url);
            $visor.modal('show');
            return false;
        });
        $(document).on('hidden.bs.modal', '#det-visor', function () {
            $('#det-visor-frame').attr('src', 'about:blank');
        });
    });
</script>
<style>
    .det-barra {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        margin-bottom: 18px;
        background: #fff;
        border-radius: 10px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .det-barra-titulo {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .det-barra-titulo h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        word-break: break-word;
    }
    .det-barra-titulo small {
        display: block;
        font-size: 12px;
        color: #7a8594;
        font-weight: normal;
    }
    .det-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .det-botones .btn {
        margin: 0;
    }

    /* la hoja del documento */
    .det-hoja {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        padding: 32px 40px;
        margin-bottom: 20px;
    }
    .det-encabezado {
        text-align: center;
        padding-bottom: 18px;
        margin-bottom: 18px;
        border-bottom: 2px solid var(--correos-amarillo, #FECB34);
    }
    .det-tipo {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 2px;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .det-cite {
        margin: 6px 0 10px;
        font-size: 17px;
        color: var(--correos-azul, #1A549A);
    }
    .det-hr {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 14px;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul-oscuro, #123E73);
        font-weight: 700;
    }
    .det-hr:hover {
        text-decoration: none;
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .det-campos {
        width: 100%;
        margin: 0 0 8px;
    }
    .det-campos th {
        width: 130px;
        padding: 8px 12px 8px 0;
        vertical-align: top;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #6b7686;
    }
    .det-campos td {
        padding: 8px 0;
        vertical-align: top;
        color: #2d3748;
    }
    .det-campos td b {
        display: block;
        font-size: 12px;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .det-campos tr + tr th,
    .det-campos tr + tr td {
        border-top: 1px solid #EEF2F7;
    }
    .det-referencia {
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .det-contenido {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px dashed var(--correos-borde, #DCE3EC);
        overflow-x: auto;
        line-height: 1.6;
    }
    .det-contenido img {
        max-width: 100%;
        height: auto;
    }
    .det-sin-contenido {
        margin-top: 18px;
        padding: 14px;
        border-radius: 8px;
        background: var(--correos-fondo, #F3F5F8);
        color: #7a8594;
        text-align: center;
    }

    /* panel lateral */
    .det-panel {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 20px;
    }
    .det-panel h3 {
        margin: 0;
        padding: 14px 18px;
        font-size: 15px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        border-bottom: 2px solid var(--correos-amarillo, #FECB34);
    }
    .det-panel h3 .fa {
        color: var(--correos-azul, #1A549A);
        margin-right: 6px;
    }
    .det-panel-cuerpo {
        padding: 12px 18px;
    }
    .det-archivo {
        display: flex;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #EEF2F7;
    }
    .det-archivo:last-child {
        border-bottom: 0;
    }
    .det-archivo-icono {
        font-size: 24px;
        color: #d32f2f;
        line-height: 1;
    }
    .det-archivo-info {
        min-width: 0;
        flex: 1;
    }
    .det-archivo-nombre {
        display: block;
        font-weight: 600;
        color: #2d3748;
        word-break: break-word;
        line-height: 1.3;
    }
    .det-archivo-meta {
        display: block;
        margin: 2px 0 6px;
        font-size: 11px;
        color: #8a94a3;
    }
    .det-archivo .btn {
        margin: 0 4px 0 0;
    }
    .det-datos {
        margin: 0;
    }
    .det-datos dt {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .det-datos dd {
        margin: 0 0 10px;
        color: #2d3748;
    }
    .det-estado {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
    }
    .det-estado-derivado { background: #E6F4EC; color: #227547; }
    .det-estado-pendiente { background: #FFF3DC; color: #B26B00; }
    .det-vacio {
        margin: 0;
        padding: 8px 0;
        color: #8a94a3;
    }

    /* visor de PDF */
    .det-visor .modal-dialog { width: 94%; max-width: 1200px; margin: 20px auto; }
    .det-visor .modal-content { border-radius: 8px; overflow: hidden; }
    .det-visor .modal-header { background: var(--correos-azul, #1A549A); border-bottom: 3px solid var(--correos-amarillo, #FECB34); color: #fff; padding: 10px 16px; }
    .det-visor .modal-title { color: #fff; font-size: 15px; word-break: break-word; padding-right: 40px; }
    .det-visor .close { color: #fff; opacity: .9; font-size: 30px; text-shadow: none; }
    .det-visor .close:hover { color: var(--correos-amarillo, #FECB34); opacity: 1; }
    .det-visor .modal-body { position: relative; padding: 0; background: #525659; }
    .det-visor iframe { display: block; width: 100%; height: calc(100vh - 140px); min-height: 300px; border: 0; }
    .det-visor-cargando { position: absolute; top: 40%; left: 0; right: 0; text-align: center; color: #fff; }

    @media (max-width: 767px) {
        .det-hoja { padding: 20px 16px; }
        .det-campos th { width: 90px; }
    }
    /* impresion: solo la hoja del documento */
    @media print {
        .det-barra, .det-lateral, #header, #menubar { display: none !important; }
        .det-principal { width: 100% !important; float: none !important; }
        .det-hoja { box-shadow: none; padding: 0; }
        body, #content, .section-body { background: #fff !important; }
        #base { padding-left: 0 !important; }
    }
</style>

<!-- barra superior -->
<div class="det-barra">
    <div class="det-barra-titulo">
        <a href="/document" id="det-regresar" class="btn btn-sm btn-default-bright" title="Regresar"><i class="fa fa-arrow-left"></i></a>
        <h2><?php echo HTML::chars($d->cite_original != '' ? $d->cite_original : $d->codigo); ?>
            <small><?php echo HTML::chars(ucfirst(mb_strtolower($tipo, 'UTF-8'))); ?><?php if ($tiene_hr): ?> &middot; Hoja de ruta <?php echo HTML::chars($d->nur); ?><?php endif; ?></small>
        </h2>
    </div>
    <div class="det-botones">
        <?php if ($tiene_hr): ?>
            <a href="/route/trace/?hr=<?php echo urlencode($d->nur); ?>" class="btn btn-sm btn-default-bright"><i class="md md-verified-user"></i> Ver seguimiento</a>
        <?php endif; ?>
        <?php if ($es_autor): ?>
            <a href="/documento/edit/<?php echo (int) $d->id; ?>" class="btn btn-sm btn-default-bright"><i class="fa fa-pencil"></i> Editar</a>
            <a href="/plantilla/word/<?php echo (int) $d->id; ?>" target="_blank" class="btn btn-sm btn-default-bright"><i class="fa fa-file-word-o"></i> Plantilla Word</a>
        <?php endif; ?>
        <a href="javascript:void(0)" id="imprime" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Imprimir</a>
    </div>
</div>

<div class="row">
    <!-- hoja del documento -->
    <div class="col-lg-8 det-principal">
        <div class="det-hoja">
            <div class="det-encabezado">
                <h1 class="det-tipo"><?php echo HTML::chars(mb_strtoupper($tipo, 'UTF-8')); ?></h1>
                <div class="det-cite"><?php echo HTML::chars($d->cite_original); ?></div>
                <?php if ($tiene_hr): ?>
                    <a href="/route/trace/?hr=<?php echo urlencode($d->nur); ?>" class="det-hr" title="Ver seguimiento"><i class="md md-label"></i> <?php echo HTML::chars($d->nur); ?></a>
                <?php endif; ?>
            </div>

            <table class="det-campos">
                <tr>
                    <th>A</th>
                    <td><?php echo HTML::chars($d->nombre_destinatario); ?><b><?php echo HTML::chars($d->cargo_destinatario); ?></b>
                        <?php if (trim($d->institucion_destinatario) != ''): ?><span class="text-muted"><?php echo HTML::chars($d->institucion_destinatario); ?></span><?php endif; ?>
                    </td>
                </tr>
                <?php if (trim($d->nombre_via) != ''): ?>
                    <tr>
                        <th>Vía</th>
                        <td><?php echo HTML::chars($d->nombre_via); ?><b><?php echo HTML::chars($d->cargo_via); ?></b></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th>De</th>
                    <td><?php echo HTML::chars($d->nombre_remitente); ?><b><?php echo HTML::chars($d->cargo_remitente); ?></b>
                        <?php if (trim($d->institucion_remitente) != ''): ?><span class="text-muted"><?php echo HTML::chars($d->institucion_remitente); ?></span><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td><?php echo Date::fecha($d->fecha_creacion); ?></td>
                </tr>
                <tr>
                    <th>Referencia</th>
                    <td class="det-referencia"><?php echo HTML::chars($d->referencia); ?></td>
                </tr>
            </table>

            <?php if (trim(strip_tags($d->contenido)) != ''): ?>
                <div class="det-contenido"><?php echo $d->contenido; ?></div>
            <?php else: ?>
                <div class="det-sin-contenido"><i class="fa fa-info-circle"></i> El contenido de este documento está en su archivo digital adjunto.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- panel lateral -->
    <div class="col-lg-4 det-lateral">
        <div class="det-panel">
            <h3><i class="fa fa-paperclip"></i> Archivos digitales (<?php echo count($lista_archivos); ?>)</h3>
            <div class="det-panel-cuerpo">
                <?php if (!$lista_archivos): ?>
                    <p class="det-vacio">Este documento no tiene archivos digitales.</p>
                <?php else: ?>
                    <?php foreach ($lista_archivos as $a): ?>
                        <div class="det-archivo">
                            <div class="det-archivo-icono"><i class="fa <?php echo $a['es_pdf'] ? 'fa-file-pdf-o' : 'fa-file-o'; ?>"></i></div>
                            <div class="det-archivo-info">
                                <span class="det-archivo-nombre"><?php echo HTML::chars($a['nombre']); ?></span>
                                <span class="det-archivo-meta"><?php echo $a['tamanio']; ?><?php if ($a['fecha']): ?> &middot; <?php echo $a['fecha']; ?><?php endif; ?></span>
                                <?php if ($a['es_pdf']): ?>
                                    <a href="javascript:void(0);" class="btn btn-xs btn-primary det-ver-pdf"
                                       data-url="/download/?file=<?php echo (int) $a['id']; ?>&amp;ver=1"
                                       data-descargar="/download/?file=<?php echo (int) $a['id']; ?>"
                                       data-nombre="<?php echo HTML::chars($a['nombre']); ?>"><i class="fa fa-eye"></i> Ver</a>
                                <?php endif; ?>
                                <a href="/download/?file=<?php echo (int) $a['id']; ?>" class="btn btn-xs btn-default-bright"><i class="fa fa-download"></i> Descargar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="det-panel">
            <h3><i class="fa fa-info-circle"></i> Datos del documento</h3>
            <div class="det-panel-cuerpo">
                <dl class="det-datos">
                    <dt>Estado</dt>
                    <dd>
                        <?php if ($derivado): ?>
                            <span class="det-estado det-estado-derivado"><i class="fa fa-check"></i> Derivado</span>
                        <?php elseif ($tiene_hr): ?>
                            <span class="det-estado det-estado-pendiente">Con hoja de ruta, sin derivar</span>
                        <?php else: ?>
                            <span class="det-estado det-estado-pendiente">Sin hoja de ruta</span>
                        <?php endif; ?>
                    </dd>
                    <dt>Tipo</dt>
                    <dd><?php echo HTML::chars($tipo); ?></dd>
                    <?php if ($nombre_proceso != ''): ?>
                        <dt>Proceso</dt>
                        <dd><?php echo HTML::chars($nombre_proceso); ?></dd>
                    <?php endif; ?>
                    <?php if ((int) $d->hojas > 0): ?>
                        <dt>Número de hojas</dt>
                        <dd><?php echo (int) $d->hojas; ?></dd>
                    <?php endif; ?>
                    <?php if (trim($d->adjuntos) != ''): ?>
                        <dt>Adjuntos</dt>
                        <dd><?php echo HTML::chars($d->adjuntos); ?></dd>
                    <?php endif; ?>
                    <?php if (trim($d->copias) != ''): ?>
                        <dt>Con copia a</dt>
                        <dd><?php echo HTML::chars($d->copias); ?></dd>
                    <?php endif; ?>
                    <?php if (trim($d->mosca_remitente) != ''): ?>
                        <dt>Mosca</dt>
                        <dd><?php echo HTML::chars($d->mosca_remitente); ?></dd>
                    <?php endif; ?>
                    <dt>Creado</dt>
                    <dd><?php echo $d->fecha_creacion ? date('d/m/Y H:i', strtotime($d->fecha_creacion)) : ''; ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- visor de PDF -->
<div class="modal fade det-visor" id="det-visor" tabindex="-1" role="dialog" aria-labelledby="det-visor-titulo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" title="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> <span id="det-visor-titulo"></span></h4>
                <div style="margin-top:6px">
                    <a href="#" id="det-visor-descargar" class="btn btn-xs btn-default-bright"><i class="fa fa-download"></i> Descargar</a>
                    <a href="#" id="det-visor-pestana" target="_blank" class="btn btn-xs btn-default-bright"><i class="fa fa-external-link"></i> Abrir en otra pestaña</a>
                </div>
            </div>
            <div class="modal-body">
                <div class="det-visor-cargando" id="det-visor-cargando"><i class="fa fa-circle-o-notch fa-spin"></i> Cargando documento...</div>
                <iframe id="det-visor-frame" src="about:blank" title="Vista previa del archivo"
                        onload="document.getElementById('det-visor-cargando').style.display = 'none';"></iframe>
            </div>
        </div>
    </div>
</div>
