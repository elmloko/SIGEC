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

        // cancelar derivacion (solo mientras el destinatario no la haya recibido)
        $('a.recibir').click(function () {
            var $this = $(this);
            $('#input-hojaruta').val($this.attr('nur'));
            $('#input-entrada').val($this.attr('entrada'));
            $('#bj-modal-hr').text($this.attr('hr'));
        });
        $('#btn-recibir').click(function () {
            location.href = "/bandeja/cancel/?id=" + $('#input-hojaruta').val();
        });
    });
</script>

<div class="bj-toolbar">
    <h3><i class="md md-send"></i> Correspondencia enviada
        <span class="bj-contador" id="bj-visibles"><?php echo count($entrada); ?></span>
    </h3>
    <?php if (count($entrada) > 0): ?>
        <div class="bj-buscar">
            <i class="fa fa-search"></i>
            <input type="text" id="FilterTextBox" name="FilterTextBox" class="form-control" placeholder="Buscar por hoja de ruta, referencia, destinatario..."/>
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
            ?>
            <div id="e<?php echo $i; ?>" class="<?php echo $clases; ?>"
                 oficina="<?php echo HTML::chars($s->a_oficina); ?>" proceso="<?php echo HTML::chars($s->referencia); ?>"
                 fecha="<?php echo $s->fecha; ?>" hojaruta="<?php echo HTML::chars($s->nur); ?>">
                <div class="bj-item-cuerpo">
                    <div class="bj-item-cabecera">
                        <?php if ($urgente): ?><span class="bj-etiqueta bj-urgente-tag">Urgente</span><?php endif; ?>
                        <span class="bj-etiqueta <?php echo $s->oficial ? 'bj-oficial' : 'bj-copia-tag'; ?>"><?php echo $s->oficial ? 'Oficial' : 'Copia'; ?></span>
                        <?php if ($s->hijo == 1): ?>
                            <a href="/correspondence/agrupado/?hr=<?php echo urlencode($s->nur); ?>" class="bj-etiqueta bj-agrupado-tag">Agrupado</a>
                        <?php endif; ?>
                        <a href="/route/trace/?hr=<?php echo urlencode($s->nur); ?>" class="bj-nur" title="Ver seguimiento"><?php echo HTML::chars($s->nur); ?></a>
                        <span class="bj-dias <?php echo $clase_dias((int) $s->dias); ?>" title="Días sin que el destinatario la reciba"><?php echo (int) $s->dias == 1 ? '1 día' : (int) $s->dias . ' días'; ?></span>
                    </div>
                    <span class="bj-item-titulo"><a href="/document/detalle/<?php echo $s->id_doc; ?>"><?php echo HTML::chars($s->referencia); ?></a></span>
                    <div class="bj-item-detalle">
                        <div class="bj-persona">
                            <span style="display:inline">Para:</span> <b><?php echo HTML::chars($s->nombre_receptor); ?></b> &middot; <?php echo HTML::chars($s->cargo_receptor); ?>
                            <span><?php echo HTML::chars($s->a_oficina); ?></span>
                            <span class="bj-fecha"><i class="fa fa-calendar-o"></i> Enviada el <?php echo Date::fecha($s->fecha); ?></span>
                        </div>
                        <div class="bj-col-derecha">
                            <div class="bj-proveido">
                                <i class="fa fa-comments-o"></i><?php echo HTML::chars($s->proveido); ?>
                                <?php if ($s->accion != ''): ?><small><?php echo HTML::chars($s->accion); ?></small><?php endif; ?>
                            </div>
                            <div class="bj-item-pie">
                            <div class="bj-item-acciones">
                                <a href="/route/trace/?hr=<?php echo urlencode($s->nur); ?>" class="btn btn-sm btn-default-bright"><i class="md md-verified-user"></i> Ver seguimiento</a>
                                <a href="javascript:;" entrada="<?php echo $i; ?>" class="recibir btn btn-sm bj-btn-peligro"
                                   data-toggle="modal" data-target="#simpleModal" nur="<?php echo $s->id; ?>" hr="<?php echo HTML::chars($s->nur); ?>">
                                    <i class="md md-cancel"></i> Cancelar derivación</a>
                            </div>
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
        <i class="md md-send" style="color: var(--correos-azul, #1A549A)"></i>
        <h4>Sin envíos pendientes de recepción</h4>
        No tiene correspondencia enviada esperando que el destinatario la reciba.
    </div>
<?php } ?>

<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel">Cancelar derivación</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de cancelar la derivación de la hoja de ruta <b class="text-primary" id="bj-modal-hr"></b>?</p>
                <input type="hidden" id="input-hojaruta" value="0"/>
                <input type="hidden" id="input-entrada" value="0"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No, volver</button>
                <button type="button" id="btn-recibir" class="btn btn-danger btn-sm"><i class="md md-cancel"></i> Sí, cancelar derivación</button>
            </div>
        </div>
    </div>
</div>
