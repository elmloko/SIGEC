<?php
$h = function ($s) {
    return HTML::chars($s);
};
$fecha = function ($f) {
    return ($f && substr($f, 0, 4) != '0000') ? date('d/m/Y H:i', strtotime($f)) : '';
};
$clase_dias = function ($dias) {
    return $dias > 7 ? 'alto' : ($dias > 2 ? 'medio' : 'bajo');
};
$total = count($hojas);
// se sugiere como principal la hoja oficial mas antigua en la bandeja
$sugerida = 0;
$mas_dias = -1;
foreach ($hojas as $s) {
    $peso = (int) $s['dias'] + ($s['oficial'] > 0 ? 100000 : 0);
    if ($peso > $mas_dias) {
        $mas_dias = $peso;
        $sugerida = (int) $s['id'];
    }
}
?>
<div class="ac-card ac-cab">
    <div class="ac-cab-fila">
        <span class="ac-cab-icono"><i class="fa fa-object-group"></i></span>
        <div class="ac-cab-texto">
            <h2>Agrupar hojas de ruta</h2>
            <p>Junte varias hojas de ruta del mismo trámite bajo una principal. Desde ese momento solo deriva o archiva la principal.</p>
        </div>
        <a href="/bandeja/pendientes" class="ac-btn"><i class="fa fa-reply"></i> Volver a pendientes</a>
    </div>
    <div class="ac-flujo" id="ac-flujo">
        <div class="ac-flujo-paso hecho">
            <span class="ac-flujo-num"><i class="fa fa-check"></i></span>
            <span><b>Seleccionar</b><small>En pendientes</small></span>
        </div>
        <div class="ac-flujo-paso actual" data-paso="1">
            <span class="ac-flujo-num">1</span>
            <span><b>Principal</b><small id="ac-flujo-principal">Sin elegir</small></span>
        </div>
        <div class="ac-flujo-paso" data-paso="2">
            <span class="ac-flujo-num">2</span>
            <span><b>Confirmar</b><small id="ac-flujo-hijas">Agrupar</small></span>
        </div>
        <div class="ac-flujo-paso">
            <span class="ac-flujo-num"><i class="fa fa-object-group"></i></span>
            <span><b>Agrupada</b><small>Se deriva como una sola</small></span>
        </div>
    </div>
</div>

<?php if ($total < 2): ?>
    <div class="bj-vacio">
        <i class="fa fa-info-circle" style="color:#B26B00"></i>
        <h4>Seleccione al menos dos hojas de ruta</h4>
        Para agrupar necesita una hoja principal y una o más hojas que se unan a ella.
        <div style="margin-top:14px"><a href="/bandeja/pendientes" class="ac-btn"><i class="fa fa-reply"></i> Volver a pendientes</a></div>
    </div>
<?php else: ?>

<form method="post" action="/bandeja/agruparf" id="frmAgrupar">
    <div class="ac-dos">
        <!-- paso 1 -->
        <div class="ac-card ac-panel">
            <h3 class="ac-panel-titulo">
                <span class="ac-paso">1</span> Elija la hoja de ruta principal
                <span class="ac-contador"><?php echo $total; ?></span>
            </h3>
            <p class="ac-panel-sub">Haga clic en la hoja que encabezará el grupo. Las demás quedarán unidas a ella.</p>
            <ul class="ac-hojas" id="ag-hojas">
                <?php foreach ($hojas as $s):
                    $dias = (int) $s['dias'];
                    $es = (int) $s['id'] === $sugerida;
                    $clases = 'ac-hoja ag-hoja' . ($s['oficial'] > 0 ? '' : ' copia') . ($s['prioridad'] > 0 ? ' urgente' : '') . ($es ? ' principal' : '');
                    ?>
                    <li class="<?php echo $clases; ?>">
                        <label class="ag-elegir">
                            <input type="radio" name="principal" value="<?php echo (int) $s['id']; ?>"<?php echo $es ? ' checked' : ''; ?>>
                            <span class="ag-radio"></span>
                            <div class="ac-hoja-cuerpo">
                                <div class="ac-hoja-tags">
                                    <span class="ac-hoja-nur"><?php echo $h($s['nur']); ?></span>
                                    <span class="ag-rol"></span>
                                    <span class="ac-tag <?php echo $s['oficial'] > 0 ? 'oficial' : 'copia'; ?>"><?php echo $s['oficial'] > 0 ? 'Oficial' : 'Copia'; ?></span>
                                    <?php if ($s['prioridad'] > 0): ?><span class="ac-tag urgente">Urgente</span><?php endif; ?>
                                    <span class="ac-tag <?php echo $clase_dias($dias); ?>" title="Días desde que se la derivaron"><i class="fa fa-clock-o"></i> <?php echo $dias == 1 ? '1 día' : $dias . ' días'; ?></span>
                                </div>
                                <span class="ac-hoja-ref"><?php echo $s['referencia'] ? $h($s['referencia']) : 'Sin referencia'; ?></span>
                                <?php if ($s['codigo']): ?><span class="ac-hoja-codigo"><?php echo $h($s['codigo']); ?></span><?php endif; ?>
                            </div>
                        </label>
                        <input type="hidden" value="<?php echo (int) $s['id']; ?>" name="seg[]"/>

                        <div class="ac-hoja-datos">
                            <div class="ac-dato">
                                <small>Remitente</small>
                                <span title="<?php echo $h($s['nombre_emisor']); ?>"><?php echo $h(mb_convert_case(mb_strtolower($s['nombre_emisor'], 'UTF-8'), MB_CASE_TITLE, 'UTF-8')); ?></span>
                                <em title="<?php echo $h($s['cargo_emisor'] . ' · ' . $s['de_oficina']); ?>"><?php echo $h($s['cargo_emisor']); ?><?php echo $s['de_oficina'] ? ' · ' . $h($s['de_oficina']) : ''; ?></em>
                            </div>
                            <div class="ac-dato">
                                <small>Derivada / recibida</small>
                                <span><?php echo $fecha($s['fecha']); ?></span>
                                <em><?php echo $fecha($s['fecha_recepcion']) ? 'Recibida ' . $fecha($s['fecha_recepcion']) : 'Sin fecha de recepción'; ?></em>
                            </div>
                            <?php if ($s['accion']): ?>
                                <div class="ac-dato">
                                    <small>Acción solicitada</small>
                                    <span><?php echo $h($s['accion']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (trim($s['proveido'])): ?>
                            <div class="ac-proveido"><i class="fa fa-comment"></i><?php echo $h($s['proveido']); ?></div>
                        <?php endif; ?>
                        <div class="ac-hoja-pie">
                            <?php if ($s['id_doc']): ?>
                                <a href="/document/detalle/<?php echo (int) $s['id_doc']; ?>" target="_blank"><i class="fa fa-file-text-o"></i> Documento</a>
                            <?php endif; ?>
                            <a href="/route/trace/?hr=<?php echo urlencode($s['nur']); ?>" target="_blank" class="ac-der"><i class="fa fa-external-link"></i> Seguimiento</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- paso 2 -->
        <div class="ac-card ac-panel ac-lateral">
            <h3 class="ac-panel-titulo"><span class="ac-paso">2</span> Así quedará el grupo</h3>
            <p class="ac-panel-sub">Vista previa de la agrupación.</p>

            <div class="ag-arbol" id="ag-arbol"></div>

            <div class="ac-info">
                <b class="t"><i class="fa fa-info-circle"></i> ¿Qué pasará al agrupar?</b>
                <ul>
                    <li><i class="fa fa-star"></i> La <b>principal</b> sigue en sus <b>Pendientes</b> con la etiqueta <i>Agrupado</i>.</li>
                    <li><i class="fa fa-link"></i> Las demás pasan a estado <b>Agrupado</b> y salen de su bandeja.</li>
                    <li><i class="fa fa-share"></i> Cuando derive la principal, el grupo la acompaña y el destinatario lo verá.</li>
                    <li><i class="fa fa-search"></i> Puede ver el grupo desde la etiqueta <i>Agrupado</i> de la principal.</li>
                </ul>
            </div>

            <div class="ac-acciones">
                <a href="/bandeja/pendientes" class="ac-btn">Cancelar</a>
                <button type="submit" class="ac-btn primario" id="ag-enviar"><i class="fa fa-object-group"></i> <span>Agrupar</span></button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function () {
        function esc(t) {
            return $('<span>').text(t).html();
        }

        function actualizar() {
            var $sel = $('#ag-hojas input[name="principal"]:checked'),
                $principal = $sel.closest('.ag-hoja'),
                $hijas = $('#ag-hojas .ag-hoja').not($principal),
                n = $hijas.length,
                arbol = '';

            $('#ag-hojas .ag-hoja').removeClass('principal');
            $principal.addClass('principal');
            $('#ag-hojas .ag-rol').text('Se unirá');
            $principal.find('.ag-rol').text('Principal');

            if ($sel.length) {
                arbol += '<div class="ag-nodo principal"><i class="fa fa-star"></i><div><b>' + esc($principal.find('.ac-hoja-nur').text()) + '</b><span>' + esc($principal.find('.ac-hoja-ref').text()) + '</span></div></div>';
                $hijas.each(function () {
                    arbol += '<div class="ag-nodo hija"><i class="fa fa-link"></i><div><b>' + esc($(this).find('.ac-hoja-nur').text()) + '</b><span>' + esc($(this).find('.ac-hoja-ref').text()) + '</span></div></div>';
                });
            }
            $('#ag-arbol').html(arbol || '<p class="ac-panel-sub">Elija la hoja principal.</p>');

            $('#ac-flujo-principal').text($sel.length ? $principal.find('.ac-hoja-nur').text() : 'Sin elegir');
            $('#ac-flujo-hijas').text(n + ' hoja' + (n == 1 ? '' : 's') + ' se une' + (n == 1 ? '' : 'n'));
            $('#ac-flujo [data-paso="1"]').toggleClass('hecho', !!$sel.length).toggleClass('actual', !$sel.length);
            $('#ac-flujo [data-paso="2"]').toggleClass('actual', !!$sel.length);
            $('#ag-enviar span').text('Agrupar ' + (n + 1) + ' hojas de ruta');
            $('#ag-enviar').prop('disabled', !$sel.length);
        }

        $('#ag-hojas').on('change', 'input[name="principal"]', actualizar);

        $('#frmAgrupar').on('submit', function () {
            if (!$('#ag-hojas input[name="principal"]:checked').length) {
                return false;
            }
            $('#ag-enviar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Agrupando…');
            return true;
        });

        actualizar();
    });
</script>
<?php endif; ?>
