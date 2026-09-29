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
                $('div#entrada').append(row);
            });
            return false;
        });
        $("#FilterTextBox").focus();

        // recibir
        $('a.recibir').click(function () {
            var $this = $(this);
            $('#input-hojaruta').val($this.attr('nur'));
            $('#input-entrada').val($this.attr('entrada'));
            $('#bj-modal-hr').text($this.attr('hr'));
        });
        $('#btn-recibir').click(function () {
            var id_seg = $('#input-hojaruta').val();
            var entrada = $('#input-entrada').val();
            $.ajax({
                type: "POST",
                data: {id: id_seg},
                url: "/ajax/recibir/",
                success: function (jsondata) {
                    $('#simpleModal').modal('hide');
                    var obj = jQuery.parseJSON(jsondata);
                    if (obj.estado == 'ok') {
                        $('#e' + entrada).fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        alert(obj.error);
                    }
                }
            });
        });
    });
</script>

<div class="bj-toolbar">
    <h3><i class="md md-inbox"></i> Correspondencia entrante
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
    <div id="entrada" class="bj-lista">
        <?php
        $i = 1;
        foreach ($entrada as $s):
            $urgente = (int) $s->prioridad > 0;
            $clases = 'bandeja bj-item tipo' . $s->oficial . ($s->oficial ? ' bj-oficial-item' : ' bj-copia') . ($urgente ? ' bj-urgente' : '');
            $id_seguimiento = $s->id;
            ?>
            <div id="e<?php echo $i; ?>" class="<?php echo $clases; ?>"
                 oficina="<?php echo HTML::chars($s->de_oficina); ?>" proceso="<?php echo HTML::chars($s->referencia); ?>"
                 fecha="<?php echo $s->fecha; ?>" hojaruta="<?php echo HTML::chars($s->nur); ?>">
                <div class="bj-item-cuerpo">
                    <div class="bj-item-cabecera">
                        <?php if ($urgente): ?><span class="bj-etiqueta bj-urgente-tag">Urgente</span><?php endif; ?>
                        <span class="bj-etiqueta <?php echo $s->oficial ? 'bj-oficial' : 'bj-copia-tag'; ?>"><?php echo $s->oficial ? 'Oficial' : 'Copia'; ?></span>
                        <?php if ($s->hijo == 1): ?>
                            <a href="/correspondence/agrupado/?hr=<?php echo urlencode($s->nur); ?>" class="bj-etiqueta bj-agrupado-tag">Agrupado</a>
                        <?php endif; ?>
                        <a href="/route/trace/?hr=<?php echo urlencode($s->nur); ?>" class="bj-nur" title="Ver seguimiento"><?php echo HTML::chars($s->nur); ?></a>
                        <span class="bj-dias <?php echo $clase_dias((int) $s->dias); ?>"><?php echo (int) $s->dias == 1 ? '1 día' : (int) $s->dias . ' días'; ?></span>
                    </div>
                    <span class="bj-item-titulo"><a href="/document/detalle/<?php echo $s->id_doc; ?>"><?php echo HTML::chars($s->referencia); ?></a></span>
                    <div class="bj-item-detalle">
                        <div class="bj-persona">
                            <b><?php echo HTML::chars($s->nombre_emisor); ?></b> &middot; <?php echo HTML::chars($s->cargo_emisor); ?>
                            <span><?php echo HTML::chars($s->de_oficina); ?></span>
                            <span class="bj-fecha"><i class="fa fa-calendar-o"></i> <?php echo Date::fecha($s->fecha); ?></span>
                        </div>
                        <div class="bj-col-derecha">
                            <div class="bj-proveido">
                                <i class="fa fa-comments-o"></i><?php echo HTML::chars($s->proveido); ?>
                                <?php if ($s->accion != ''): ?><small><?php echo HTML::chars($s->accion); ?></small><?php endif; ?>
                            </div>
                            <div class="bj-item-pie">
                            <div class="bj-item-acciones">
                                <a href="javascript:;" entrada="<?php echo $i; ?>" class="recibir btn btn-primary btn-sm"
                                   data-toggle="modal" data-target="#simpleModal" nur="<?php echo $s->id; ?>" hr="<?php echo HTML::chars($s->nur); ?>">
                                    <i class="md md-inbox"></i> Recibir</a>
                                <a href="#" class="btn btn-sm bj-btn-peligro btn-modal-rechazo" data-toggle="modal"
                                   data-id-seguimiento="<?php echo $id_seguimiento; ?>"
                                   data-id-seguimiento-padre="<?php echo $s->id_seguimiento; ?>"
                                   data-nur="<?php echo HTML::chars($s->nur); ?>"
                                   data-id-usuario="<?php echo $user->id; ?>"
                                   data-estado="<?php echo $s->estado; ?>"
                                   data-target="#modal-rechazo-<?php echo $id_seguimiento; ?>">
                                    <i class="fa fa-reply"></i> Rechazar</a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- modal de rechazo de la derivacion -->
                <div class="modal fade" id="modal-rechazo-<?php echo $id_seguimiento; ?>" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Justificación del rechazo</h4>
                            </div>
                            <div class="modal-body">
                                <p>Ingrese una justificación por la cual desea rechazar la derivación de la hoja de ruta
                                    <b><?php echo HTML::chars($s->nur); ?></b>.</p>
                                <textarea style="height: 100px; width: 100%;" class="form-control texto-rechazo" name="texto-rechazo"
                                          id="texto-rechazo-<?php echo $id_seguimiento; ?>"
                                          placeholder="Ingrese su descripción del rechazo..."></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-danger" onclick="guardar_rechazo()">Rechazar derivación</button>
                                <input type="hidden" class="id_seguimiento" value="<?php echo $id_seguimiento; ?>"/>
                                <input type="hidden" class="id_seguimiento_padre" value="<?php echo $s->id_seguimiento; ?>"/>
                                <input type="hidden" class="nur" value="<?php echo HTML::chars($s->nur); ?>"/>
                                <input type="hidden" class="id_user" value="<?php echo $user->id; ?>"/>
                                <input type="hidden" class="estado" value="<?php echo $s->estado; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $i++;
        endforeach;
        ?>
    </div>
    <div class="bj-sin-resultados">No hay correspondencia que coincida con la búsqueda.</div>
<?php } else { ?>
    <div class="bj-vacio">
        <i class="fa fa-check-circle"></i>
        <h4>Bandeja de entrada vacía</h4>
        No tiene correspondencia por recibir.
    </div>
<?php } ?>

<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel">Recibir correspondencia</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de recibir la hoja de ruta <b class="text-primary" id="bj-modal-hr"></b>?</p>
                <input type="hidden" id="input-hojaruta" value="0"/>
                <input type="hidden" id="input-entrada" value="0"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-recibir" class="btn btn-primary btn-sm"><i class="md md-inbox"></i> Recibir</button>
            </div>
        </div>
    </div>
</div>

<!-- rechazo de la derivacion -->
<script>
    $(document).ready(function () {
        $(".btn-modal-rechazo").click(function () {
            $(".id_seguimiento").val($(this).data('id-seguimiento'));
            $(".id_seguimiento_padre").val($(this).data('id-seguimiento-padre'));
            $(".nur").val($(this).data('nur'));
            $(".id_user").val($(this).data('id-usuario'));
            $(".estado").val($(this).data('estado'));
            // el modal se mueve al <body> para que quede encima de todo
            $($(this).data('target')).appendTo("body").modal('show');
            return false;
        });
    });

    function guardar_rechazo() {
        var id_seguimiento = $('.id_seguimiento').val();
        var observacion = $.trim($('#texto-rechazo-' + id_seguimiento).val());
        if (observacion.length > 0) {
            $.ajax({
                type: "POST",
                data: {
                    id_seguimiento: id_seguimiento,
                    id_seguimiento_padre: $('.id_seguimiento_padre').val(),
                    nur: $('.nur').val(),
                    observacion: observacion,
                    id_usuario: $('.id_user').val(),
                    estado: $('.estado').val()
                },
                url: "/ajax/rechazar_derivacion",
                success: function () {
                    location.reload(true);
                }
            });
            $('.texto-rechazo').val('');
            $('#modal-rechazo-' + id_seguimiento).modal('hide');
        } else {
            alert("(*) Usted debe llenar una descripción del rechazo");
        }
    }
</script>
