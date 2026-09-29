<?php
// conteos para los filtros rapidos
$n_oficial = $n_copia = $n_urgente = 0;
foreach ($entrada as $s) {
    $s->oficial ? $n_oficial++ : $n_copia++;
    $n_urgente += (int) $s->prioridad > 0 ? 1 : 0;
}
$clase_dias = function ($dias) {
    return $dias > 7 ? 'bj-dias-alto' : ($dias > 2 ? 'bj-dias-medio' : 'bj-dias-bajo');
};
?>
<script type="text/javascript">
    $(function () {
        // filtro de texto
        $("div#entrada .bandeja").each(function () {
            var t = $(this).text().toLowerCase();
            $("<table class='indexColumn'></table>").hide().text(t).appendTo(this);
        });
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            $("div#entrada .bandeja:hidden").show();
            $.each(s, function () {
                $("div#entrada .bandeja .indexColumn:not(:contains('" + this + "'))").parent().hide();
            });
        });

        // seleccion para agrupar / archivar
        function actualizarSeleccion() {
            var count = $('input.sel:checked').length;
            var nurs = '';
            $('input.sel:checked').each(function () {
                nurs += "\n " + $(this).attr('rel');
            });
            $('#sup-group,#sup-archive').text(count > 0 ? count : '').toggleClass('badge style-default', count > 0);
            $('#group,#archive').toggleClass('btn-primary', count > 0).toggleClass('btn-default-bright', count === 0);
            $('#group').attr('title', count > 0 ? 'Agrupar:' + nurs : 'Seleccione 2 o más hojas de ruta para agruparlas en un solo proceso');
            $('#archive').attr('title', count > 0 ? 'Archivar:' + nurs : 'Seleccione 1 o más hojas de ruta para archivarlas');
            $('.bandeja').each(function () {
                $(this).toggleClass('bj-seleccionado', $(this).find('input.sel').is(':checked'));
            });
        }
        $('.sel').bind('click', actualizarSeleccion);
        actualizarSeleccion();

        $('a#archive').click(function () {
            if ($('input.sel:checked').length < 1) {
                alert('Seleccione por lo menos 1 hoja de ruta para archivar');
                return false;
            }
            $('#accion').val('0');
            $('form#doa').submit();
        });
        $('a#group').click(function () {
            $('#accion').val('1');
            if ($('input.sel:checked').length > 1) {
                $('form#doa').submit();
            } else {
                alert('Para poder agrupar debe de seleccionar por lo menos 2 hojas de ruta');
                return false;
            }
        });

        // ordenar por atributo (hojaruta, fecha, oficina, proceso)
        $('a.link2').click(function () {
            var $this = $(this);
            var criterio = $this.attr('id');
            var sortdir;
            if ($this.is('.asc')) {
                $this.removeClass('asc').addClass('desc');
                sortdir = -1;
            } else {
                $this.addClass('asc').removeClass('desc');
                sortdir = 1;
            }
            $this.closest('li').siblings().find('a').removeClass('asc desc');
            var nurs = $('div.bandeja').get();
            nurs.sort(function (a, b) {
                var val1 = $(a).attr('' + criterio).toUpperCase();
                var val2 = $(b).attr('' + criterio).toUpperCase();
                return (val1 < val2) ? -sortdir : (val1 > val2) ? sortdir : 0;
            });
            $.each(nurs, function (index, row) {
                $('form#doa').append(row);
            });
            $('form#doa').append($('#accion'));
            return false;
        });
        $('#FilterTextBox').focus();
    });
</script>
<style>
    .bj-item.bj-seleccionado {
        background: var(--correos-amarillo-suave, #FFF7DD);
        border-color: var(--correos-amarillo, #FECB34);
    }
</style>

<div class="bj-toolbar">
    <h3><i class="fa fa-clock-o"></i> Correspondencia pendiente
        <span class="bj-contador" id="bj-visibles"><?php echo count($entrada); ?></span>
    </h3>
    <?php if (count($entrada) > 0): ?>
        <div class="bj-buscar">
            <i class="fa fa-search"></i>
            <input type="text" id="FilterTextBox" name="FilterTextBox" class="form-control" placeholder="Buscar por hoja de ruta, referencia, remitente..."/>
        </div>
        <div class="bj-acciones">
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-default-bright dropdown-toggle" type="button" aria-expanded="false">
                    <i class="fa fa-sort-amount-asc"></i> Ordenar por <i class="fa fa-caret-down"></i>
                </button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" class="link2" id="hojaruta">Hoja de ruta</a></li>
                    <li><a href="#" class="link2" id="fecha">Fecha</a></li>
                    <li><a href="#" class="link2" id="oficina">Oficina</a></li>
                    <li><a href="#" class="link2" id="proceso">Referencia</a></li>
                </ul>
            </div>
            <a href="javascript:;" class="btn btn-sm btn-default-bright" id="group"><i class="fa fa-link"></i> Agrupar <sup id="sup-group"></sup></a>
            <a href="javascript:;" class="btn btn-sm btn-default-bright" id="archive"><i class="fa fa-archive"></i> Archivar <sup id="sup-archive"></sup></a>
            <a href="/print/pendientes/?id=<?php echo time(); ?>" target="_blank" class="btn btn-sm btn-default-bright"><i class="fa fa-print"></i> Imprimir</a>
        </div>
        <div class="bj-filtros">
            <span class="bj-filtro activo" data-filtro="">Todas <b><?php echo count($entrada); ?></b></span>
            <span class="bj-filtro" data-filtro=".bj-oficial-item">Oficial <b><?php echo $n_oficial; ?></b></span>
            <span class="bj-filtro" data-filtro=".bj-copia">Copia <b><?php echo $n_copia; ?></b></span>
            <?php if ($n_urgente): ?>
                <span class="bj-filtro" data-filtro=".bj-urgente">Urgente <b><?php echo $n_urgente; ?></b></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if (count($entrada) > 0) { ?>
    <div id="entrada">
        <form action="/bandeja/doa" method="post" id="doa" class="bj-lista">
            <?php
            foreach ($entrada as $s):
                $id_seguimiento = $s->id;
                $nur = $s->nur;
                $id_usuario = $user->id;
                $urgente = (int) $s->prioridad > 0;
                $clases = 'bandeja bj-item tipo' . $s->oficial . ($s->oficial ? ' bj-oficial-item' : ' bj-copia') . ($urgente ? ' bj-urgente' : '');

                // numero de derivaciones del proceso
                $nro_derivaciones = DB::query(Database::SELECT, "SELECT COUNT(1) AS n FROM seguimiento s USE INDEX (INDEX_NUR)
                        INNER JOIN users u ON (s.derivado_por = u.id)
                        WHERE s.nur = :nur AND s.oficial != 0 AND u.nivel != 4")
                    ->param(':nur', $nur)->execute()->get('n');
                // numero de justificaciones por retraso
                $nro_justificaciones = DB::query(Database::SELECT, "SELECT COUNT(1) AS n FROM observacion_seguimiento USE INDEX (IDX_NUR)
                        WHERE nur = :nur AND id_estado = 2")
                    ->param(':nur', $nur)->execute()->get('n');
                // justificaciones de rechazo (con autor)
                $rechazos = DB::query(Database::SELECT, "SELECT os.observacion, os.fecha_observacion, u.nombre, u.cargo
                        FROM observacion_seguimiento os USE INDEX (IDX_NUR)
                        INNER JOIN users u USE KEY (PRIMARY) ON os.id_usuario = u.id
                        WHERE os.nur = :nur AND os.id_estado = '1'
                        ORDER BY os.fecha_observacion DESC")
                    ->param(':nur', $nur)->execute()->as_array();
                ?>
                <div class="<?php echo $clases; ?>"
                     oficina="<?php echo HTML::chars($s->de_oficina); ?>" proceso="<?php echo HTML::chars($s->referencia); ?>"
                     fecha="<?php echo $s->fecha2; ?>" hojaruta="<?php echo HTML::chars($s->nur); ?>">
                    <div class="bj-item-check">
                        <input type="checkbox" name="id_seg[]" value="<?php echo $s->id; ?>" rel="<?php echo HTML::chars($s->nur); ?>"
                               class="sel" title="Seleccionar para agrupar o archivar">
                    </div>
                    <div class="bj-item-cuerpo">
                        <div class="bj-item-cabecera">
                            <?php if ($urgente): ?><span class="bj-etiqueta bj-urgente-tag">Urgente</span><?php endif; ?>
                            <span class="bj-etiqueta <?php echo $s->oficial ? 'bj-oficial' : 'bj-copia-tag'; ?>"><?php echo $s->oficial ? 'Oficial' : 'Copia'; ?></span>
                            <?php if ($s->hijo == 1): ?>
                                <a href="/bandeja/agrupado/?hr=<?php echo urlencode($s->nur); ?>" class="bj-etiqueta bj-agrupado-tag">Agrupado</a>
                            <?php endif; ?>
                            <a href="/route/trace/?hr=<?php echo urlencode($s->nur); ?>" class="bj-nur" title="Ver seguimiento"><?php echo HTML::chars($s->nur); ?></a>
                            <span class="bj-dias <?php echo $clase_dias((int) $s->dias); ?>" title="Días desde que la recibió"><?php echo (int) $s->dias == 1 ? '1 día' : (int) $s->dias . ' días'; ?></span>
                        </div>
                        <span class="bj-item-titulo"><a href="/document/detalle/<?php echo $s->id_doc; ?>"><?php echo HTML::chars($s->referencia); ?></a></span>
                        <div class="bj-item-detalle">
                            <div class="bj-persona">
                                <b><?php echo HTML::chars($s->nombre_emisor); ?></b> &middot; <?php echo HTML::chars($s->cargo_emisor); ?>
                                <span><?php echo HTML::chars($s->de_oficina); ?></span>
                                <span class="bj-fecha"><i class="fa fa-calendar-o"></i> <?php echo Date::fecha($s->fecha2); ?></span>
                            </div>
                            <div class="bj-col-derecha">
                                <div class="bj-proveido">
                                    <i class="fa fa-comments-o"></i><?php echo HTML::chars($s->proveido); ?>
                                    <?php if ($s->accion != ''): ?><small><?php echo HTML::chars($s->accion); ?></small><?php endif; ?>
                                </div>
                                <div class="bj-item-pie">
                                <div class="bj-item-acciones">
                                    <a href="/route/deriv/?hr=<?php echo urlencode($s->nur); ?>" class="btn btn-sm btn-primary"
                                       title="El proceso tiene <?php echo (int) $nro_derivaciones; ?> derivación(es)"
                                       id_nur="<?php echo HTML::chars($s->nur); ?>" id_seg="<?php echo $s->id; ?>" nuri="<?php echo HTML::chars($s->nur); ?>">
                                        <i class="fa fa-share"></i> Derivar <sup class="badge style-default"><?php echo (int) $nro_derivaciones; ?></sup></a>
    
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-default-bright dropdown-toggle" type="button" aria-expanded="false">
                                            <i class="fa fa-reply"></i> Responder con <i class="fa fa-caret-down"></i>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <?php foreach ($tipos as $t): ?>
                                                <li><a href="/route/responder/?id_seg=<?php echo $s->id; ?>&amp;d=<?php echo $t['id']; ?>&amp;n=<?php echo urlencode($s->nur); ?>"><?php echo HTML::chars($t['tipo']); ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
    
                                    <a href="#" class="btn btn-sm btn-default-bright btn-modal-justificacion" data-toggle="modal"
                                       title="El proceso tiene <?php echo (int) $nro_justificaciones; ?> justificación(es)"
                                       data-id-seguimiento="<?php echo $id_seguimiento; ?>" data-nur="<?php echo HTML::chars($nur); ?>"
                                       data-id-usuario="<?php echo $id_usuario; ?>" data-target="#myModal-<?php echo $id_seguimiento; ?>">
                                        <i class="fa fa-pencil-square-o"></i> Justificar <sup class="badge style-default"><?php echo (int) $nro_justificaciones; ?></sup></a>
    
                                    <?php if ($rechazos): ?>
                                        <a href="#" class="bj-ver-rechazo" title="Ver justificación de rechazo" data-toggle="modal"
                                           data-target="#modal-texto-rechazo-<?php echo $id_seguimiento; ?>"><i class="fa fa-exclamation-triangle"></i></a>
                                    <?php endif; ?>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- modal: justificacion por retraso -->
                    <div class="modal fade" id="myModal-<?php echo $id_seguimiento; ?>" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Justificación por el retraso</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Ingrese una justificación por la cual tiene un retraso en la derivación de la hoja de ruta
                                        <b><?php echo HTML::chars($nur); ?></b>.</p>
                                    <textarea style="height: 100px; width: 100%;" class="form-control texto-justificacion"
                                              id="texto-justificacion-<?php echo $id_seguimiento; ?>" name="texto-justificacion"
                                              placeholder="Ingrese su justificación..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-primary" onclick="guardar_justificacion()">Guardar</button>
                                    <input type="hidden" class="id_seguimiento" value="<?php echo $id_seguimiento; ?>"/>
                                    <input type="hidden" class="nur" value="<?php echo HTML::chars($nur); ?>"/>
                                    <input type="hidden" class="id_user" value="<?php echo $id_usuario; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($rechazos): ?>
                        <!-- modal: justificaciones de rechazo -->
                        <div class="modal fade" id="modal-texto-rechazo-<?php echo $id_seguimiento; ?>" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><i class="fa fa-exclamation-triangle" style="color:#D32F2F"></i> Justificación del rechazo
                                            <small><?php echo HTML::chars($nur); ?></small></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php foreach ($rechazos as $r): ?>
                                            <p>
                                                <b><?php echo date('d/m/Y H:i', strtotime($r['fecha_observacion'])); ?></b> &middot;
                                                <?php echo HTML::chars($r['nombre']); ?> <small class="text-muted">(<?php echo HTML::chars($r['cargo']); ?>)</small><br/>
                                                <?php echo nl2br(HTML::chars($r['observacion'])); ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php echo Form::hidden('accion', '', array('id' => 'accion')); ?>
        </form>
    </div>
    <div class="bj-sin-resultados">No hay correspondencia que coincida con la búsqueda.</div>
<?php } else { ?>
    <div class="bj-vacio">
        <i class="fa fa-check-circle"></i>
        <h4>¡Al día!</h4>
        No tiene correspondencia pendiente.
    </div>
<?php } ?>

<script>
    $(document).ready(function () {
        // los modales se mueven al <body> para que queden encima de todo
        $(document).on('click', '.btn-modal-justificacion, .bj-ver-rechazo', function () {
            if ($(this).is('.btn-modal-justificacion')) {
                $(".id_seguimiento").val($(this).data('id-seguimiento'));
                $(".nur").val($(this).data('nur'));
                $(".id_user").val($(this).data('id-usuario'));
            }
            $($(this).data('target')).appendTo("body").modal('show');
            return false;
        });
    });

    // justificacion por retraso
    function guardar_justificacion() {
        var id_seguimiento = $('.id_seguimiento').val();
        var observacion = $.trim($('#texto-justificacion-' + id_seguimiento).val());
        if (observacion.length > 0) {
            $.ajax({
                type: "POST",
                data: {
                    id_seguimiento: id_seguimiento,
                    nur: $('.nur').val(),
                    observacion: observacion,
                    id_usuario: $('.id_user').val()
                },
                url: "/ajax/guardar_justificacion",
                success: function () {
                    location.reload(true);
                }
            });
            $('.texto-justificacion').val('');
            $('#myModal-' + id_seguimiento).modal('hide');
        } else {
            alert("(*) Usted debe llenar una justificación de retraso");
        }
    }
</script>
