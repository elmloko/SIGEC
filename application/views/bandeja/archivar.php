<?php
$h = function ($s) {
    return HTML::chars($s);
};
$fecha = function ($f, $hora = TRUE) {
    if (!$f || substr($f, 0, 4) == '0000') {
        return '';
    }
    return date($hora ? 'd/m/Y H:i' : 'd/m/Y', strtotime($f));
};
$clase_dias = function ($dias) {
    return $dias > 7 ? 'alto' : ($dias > 2 ? 'medio' : 'bajo');
};
$estados = Model_Derivaciones::$estados;

$total = count($hojas);
$oficiales = 0;
$urgentes = 0;
$max_dias = 0;
$suma_dias = 0;
foreach ($hojas as $s) {
    $oficiales += $s['oficial'] > 0 ? 1 : 0;
    $urgentes += $s['prioridad'] > 0 ? 1 : 0;
    $max_dias = max($max_dias, (int) $s['dias']);
    $suma_dias += (int) $s['dias'];
}
$prom_dias = $total ? round($suma_dias / $total) : 0;

// la carpeta usada mas recientemente por el usuario queda preseleccionada
$reciente = 0;
$ultima = '';
foreach ($carpetas as $c) {
    if ($c['ultima'] && $c['ultima'] > $ultima) {
        $ultima = $c['ultima'];
        $reciente = (int) $c['id'];
    }
}
$hay_carpetas = count($carpetas) > 0;
?>

<div class="ac-card ac-cab">
    <div class="ac-cab-fila">
        <span class="ac-cab-icono"><i class="fa fa-archive"></i></span>
        <div class="ac-cab-texto">
            <h2>Archivar correspondencia</h2>
            <p>Revise las hojas de ruta, elija la carpeta y confirme. Al archivarlas termina su trámite en su bandeja.</p>
        </div>
        <a href="/bandeja/pendientes" class="ac-btn"><i class="fa fa-reply"></i> Volver a pendientes</a>
    </div>
    <div class="ac-flujo" id="ac-flujo">
        <div class="ac-flujo-paso hecho">
            <span class="ac-flujo-num"><i class="fa fa-check"></i></span>
            <span><b>Seleccionar</b><small>En pendientes</small></span>
        </div>
        <div class="ac-flujo-paso hecho" data-paso="1">
            <span class="ac-flujo-num">1</span>
            <span><b>Revisar</b><small id="ac-flujo-hojas"><?php echo $total; ?> hoja<?php echo $total == 1 ? '' : 's'; ?> de ruta</small></span>
        </div>
        <div class="ac-flujo-paso actual" data-paso="2">
            <span class="ac-flujo-num">2</span>
            <span><b>Carpeta</b><small id="ac-flujo-carpeta">Sin elegir</small></span>
        </div>
        <div class="ac-flujo-paso" data-paso="3">
            <span class="ac-flujo-num">3</span>
            <span><b>Confirmar</b><small>Archivar</small></span>
        </div>
        <div class="ac-flujo-paso" data-paso="4">
            <span class="ac-flujo-num"><i class="fa fa-folder"></i></span>
            <span><b>Archivo</b><small>Consultar o desarchivar</small></span>
        </div>
    </div>
</div>

<?php if (!$total): ?>
    <div class="bj-vacio">
        <i class="fa fa-info-circle"></i>
        <h4>No hay hojas de ruta para archivar</h4>
        Vuelva a la bandeja y seleccione al menos una.
    </div>
<?php else: ?>

<div class="ac-stats">
    <div class="ac-card ac-stat">
        <i class="fa fa-file-text-o ac-i-azul"></i>
        <div><b id="ac-st-total"><?php echo $total; ?></b><span>Hojas de ruta a archivar</span></div>
    </div>
    <div class="ac-card ac-stat">
        <i class="fa fa-files-o ac-i-gris"></i>
        <div><b><?php echo $oficiales; ?> / <?php echo $total - $oficiales; ?></b><span>Oficiales / copias</span></div>
    </div>
    <div class="ac-card ac-stat">
        <i class="fa fa-clock-o ac-i-<?php echo $max_dias > 7 ? 'rojo' : ($max_dias > 2 ? 'ambar' : 'verde'); ?>"></i>
        <div><b><?php echo $prom_dias; ?> <small style="font-size:13px">día<?php echo $prom_dias == 1 ? '' : 's'; ?></small></b><span>Promedio en bandeja (máx. <?php echo $max_dias; ?>)</span></div>
    </div>
    <div class="ac-card ac-stat">
        <i class="fa fa-exclamation-triangle <?php echo $urgentes ? 'ac-i-rojo' : 'ac-i-verde'; ?>"></i>
        <div><b><?php echo $urgentes; ?></b><span><?php echo $urgentes ? 'Urgentes: revíselas antes' : 'Sin urgentes'; ?></span></div>
    </div>
</div>

<form method="post" action="/bandeja/archivarf" id="frmArchivar" autocomplete="off">
    <input type="hidden" value="<?php echo $hay_carpetas ? 1 : 0; ?>" name="tipo" id="tipo"/>
    <div class="ac-dos">
        <!-- paso 1 -->
        <div class="ac-card ac-panel">
            <h3 class="ac-panel-titulo">
                <span class="ac-paso">1</span> Revise las hojas de ruta
                <span class="ac-contador" id="ac-total"><?php echo $total; ?></span>
            </h3>
            <p class="ac-panel-sub">Verifique que el trámite haya concluido. Puede ver el recorrido de cada una o quitarla de la selección.</p>
            <ul class="ac-hojas" id="ac-hojas">
                <?php foreach ($hojas as $s):
                    $pasos = isset($recorrido[$s['nur']]) ? $recorrido[$s['nur']] : array();
                    $dias = (int) $s['dias'];
                    $clases = 'ac-hoja' . ($s['oficial'] > 0 ? '' : ' copia') . ($s['prioridad'] > 0 ? ' urgente' : '');
                    ?>
                    <li class="<?php echo $clases; ?>">
                        <div class="ac-hoja-cab">
                            <div class="ac-hoja-cuerpo">
                                <div class="ac-hoja-tags">
                                    <a href="/route/trace/?hr=<?php echo urlencode($s['nur']); ?>" target="_blank" class="ac-hoja-nur" title="Ver seguimiento completo"><?php echo $h($s['nur']); ?></a>
                                    <span class="ac-tag <?php echo $s['oficial'] > 0 ? 'oficial' : 'copia'; ?>"><?php echo $s['oficial'] > 0 ? 'Oficial' : 'Copia'; ?></span>
                                    <?php if ($s['prioridad'] > 0): ?><span class="ac-tag urgente">Urgente</span><?php endif; ?>
                                    <span class="ac-tag <?php echo $clase_dias($dias); ?>" title="Días desde que se la derivaron"><i class="fa fa-clock-o"></i> <?php echo $dias == 1 ? '1 día' : $dias . ' días'; ?></span>
                                </div>
                                <?php if ($s['id_doc']): ?>
                                    <a href="/document/detalle/<?php echo (int) $s['id_doc']; ?>" target="_blank" class="ac-hoja-ref"><?php echo $s['referencia'] ? $h($s['referencia']) : 'Sin referencia'; ?></a>
                                <?php else: ?>
                                    <span class="ac-hoja-ref"><?php echo $s['referencia'] ? $h($s['referencia']) : 'Sin referencia'; ?></span>
                                <?php endif; ?>
                                <?php if ($s['codigo']): ?><span class="ac-hoja-codigo"><?php echo $h($s['codigo']); ?></span><?php endif; ?>
                            </div>
                            <input type="hidden" value="<?php echo (int) $s['id']; ?>" name="seg[]"/>
                            <button type="button" class="ac-quitar" title="Quitar de la selección"><i class="fa fa-times"></i></button>
                        </div>

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
                            <a class="ac-ver-recorrido"><i class="fa fa-random"></i> <span>Ver recorrido</span> (<?php echo count($pasos); ?> paso<?php echo count($pasos) == 1 ? '' : 's'; ?>)</a>
                            <a href="/route/trace/?hr=<?php echo urlencode($s['nur']); ?>" target="_blank" class="ac-der"><i class="fa fa-external-link"></i> Seguimiento</a>
                        </div>

                        <ol class="ac-recorrido">
                            <?php foreach ($pasos as $p):
                                $es_actual = (int) $p['id'] === (int) $s['id'];
                                ?>
                                <li class="ac-paso-r<?php echo $es_actual ? ' actual' : ''; ?>">
                                    <b><?php echo $h($p['de_oficina'] ? $p['de_oficina'] : $p['nombre_emisor']); ?></b>
                                    <i class="fa fa-long-arrow-right ac-flecha"></i>
                                    <b><?php echo $h($p['a_oficina'] ? $p['a_oficina'] : $p['nombre_receptor']); ?></b>
                                    <span class="ac-estado e<?php echo (int) $p['estado']; ?>"><?php echo isset($estados[$p['estado']]) ? $estados[$p['estado']] : 'Estado ' . (int) $p['estado']; ?></span>
                                    <?php if ($p['oficial'] <= 0): ?><span class="ac-estado">Copia</span><?php endif; ?>
                                    <?php if ($es_actual): ?><span class="ac-estado e2">Usted</span><?php endif; ?>
                                    <small><?php echo $h($p['nombre_emisor']); ?> → <?php echo $h($p['nombre_receptor']); ?> · <?php echo $fecha($p['fecha_emision']); ?></small>
                                    <?php if (trim($p['proveido'])): ?><span class="ac-prov">“<?php echo $h($p['proveido']); ?>”</span><?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                            <li class="ac-paso-r final">
                                <b>Archivo</b> <small>Quedará en la carpeta elegida a nombre de <?php echo $h($usuario->nombre); ?></small>
                            </li>
                        </ol>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- pasos 2 y 3 -->
        <div class="ac-card ac-panel ac-lateral">
            <h3 class="ac-panel-titulo"><span class="ac-paso">2</span> Elija la carpeta</h3>
            <p class="ac-panel-sub">Las carpetas son compartidas por su oficina.</p>

            <?php if ($hay_carpetas): ?>
                <div class="ac-modo" role="tablist">
                    <button type="button" class="activo" data-modo="1"><i class="fa fa-folder-open"></i> Existente (<?php echo count($carpetas); ?>)</button>
                    <button type="button" data-modo="0"><i class="fa fa-plus"></i> Nueva carpeta</button>
                </div>
            <?php endif; ?>

            <div id="ac-existente" class="<?php echo $hay_carpetas ? '' : 'ac-oculto'; ?>">
                <?php if (count($carpetas) > 5): ?>
                    <div class="ac-buscar">
                        <i class="fa fa-search"></i>
                        <input type="search" id="ac-q" placeholder="Buscar carpeta…">
                    </div>
                <?php endif; ?>
                <div class="ac-carpetas" id="ac-carpetas">
                    <?php foreach ($carpetas as $c):
                        $cc = (int) $c['cc'];
                        $sel = (int) $c['id'] === $reciente;
                        ?>
                        <label class="ac-carpeta<?php echo $sel ? ' sel' : ''; ?>" data-nombre="<?php echo $h(mb_strtolower(trim($c['carpeta']), 'UTF-8')); ?>">
                            <input type="radio" name="carpeta_lista" value="<?php echo (int) $c['id']; ?>"<?php echo $sel ? ' checked' : ''; ?>>
                            <i class="fa fa-folder"></i>
                            <span class="ac-carpeta-texto">
                                <span class="ac-carpeta-nombre" title="<?php echo $h($c['carpeta']); ?>"><?php echo $h(trim($c['carpeta'])); ?></span>
                                <span class="ac-carpeta-cc">
                                    <?php echo $cc ? $cc . ' archivado' . ($cc == 1 ? '' : 's') . ' por usted' : 'Aún sin documentos suyos'; ?><?php echo $sel ? ' · <b>última usada</b> ' . $fecha($c['ultima'], FALSE) : ''; ?>
                                </span>
                            </span>
                            <i class="fa fa-check-circle ac-check"></i>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="ac-nada" id="ac-nada">Ninguna carpeta coincide. <a href="#" id="ac-crear-busqueda">Crear una nueva con ese nombre</a></div>
            </div>

            <div id="ac-nueva" class="<?php echo $hay_carpetas ? 'ac-oculto' : ''; ?>">
                <?php if (!$hay_carpetas): ?>
                    <p class="text-muted" style="font-size:13px;margin:0 0 10px;">Su oficina aún no tiene carpetas. Cree la primera:</p>
                <?php endif; ?>
                <input type="text" class="ac-input" name="carpeta_input" id="nc" maxlength="100" placeholder="Nombre de la carpeta, p. ej. Contratos <?php echo date('Y'); ?>">
                <div class="ac-ayuda" id="ac-ayuda-nueva">La carpeta quedará disponible para toda su oficina.</div>
            </div>

            <label class="ac-etiqueta" for="ac-obs">Observaciones <span class="text-muted" style="font-weight:normal">(opcional)</span></label>
            <textarea class="ac-textarea" name="observaciones" id="ac-obs" maxlength="1000" placeholder="Motivo del archivo, ubicación física, nro. de archivador, etc."></textarea>
            <div class="ac-ayuda"><span>Se guarda en cada hoja archivada.</span><span id="ac-obs-cc">0 / 1000</span></div>

            <div class="ac-info">
                <b class="t"><i class="fa fa-info-circle"></i> ¿Qué pasará al archivar?</b>
                <ul>
                    <li><i class="fa fa-inbox"></i> Las hojas salen de su bandeja de <b>Pendientes</b>.</li>
                    <li><i class="fa fa-flag-checkered"></i> Su estado cambia a <b>Archivado</b> y figura así en el seguimiento.</li>
                    <li><i class="fa fa-folder-open"></i> Las encontrará en <b>Bandeja › Archivo</b>, dentro de la carpeta.</li>
                    <li><i class="fa fa-undo"></i> Si fue un error, use <b>Desarchivar</b> en el Archivo y vuelven a sus pendientes.</li>
                </ul>
            </div>

            <h3 class="ac-panel-titulo" style="margin-top:18px"><span class="ac-paso">3</span> Confirme</h3>
            <div class="ac-resumen" id="ac-resumen"></div>
            <div class="ac-acciones">
                <a href="/bandeja/pendientes" class="ac-btn">Cancelar</a>
                <button type="submit" class="ac-btn primario" id="ac-enviar"><i class="fa fa-archive"></i> <span>Archivar</span></button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function () {
        var $tipo = $('#tipo'),
            $nc = $('#nc'),
            $q = $('#ac-q');

        function esc(t) {
            return $('<span>').text(t).html();
        }

        function modo(m) {
            $tipo.val(m);
            $('.ac-modo button').removeClass('activo').filter('[data-modo="' + m + '"]').addClass('activo');
            $('#ac-existente').toggleClass('ac-oculto', m != 1);
            $('#ac-nueva').toggleClass('ac-oculto', m != 0);
            if (m == 0) {
                $nc.focus();
            }
            actualizar();
        }

        function carpetaSel() {
            var $r = $('#ac-carpetas input:checked');
            return $r.length ? $.trim($r.closest('.ac-carpeta').find('.ac-carpeta-nombre').text()) : '';
        }

        function existente(nombre) {
            nombre = $.trim(nombre).toLowerCase();
            return nombre ? $('#ac-carpetas .ac-carpeta').filter(function () {
                return $(this).attr('data-nombre') === nombre;
            }).first() : $();
        }

        function paso(n, estado) {
            $('#ac-flujo [data-paso="' + n + '"]').removeClass('hecho actual').addClass(estado || '');
        }

        function actualizar() {
            var n = $('#ac-hojas .ac-hoja').length,
                nueva = $tipo.val() == 0,
                destino = nueva ? $.trim($nc.val()) : carpetaSel(),
                txt = n + ' hoja' + (n == 1 ? '' : 's') + ' de ruta';
            $('#ac-total, #ac-st-total').text(n);
            $('#ac-flujo-hojas').text(txt);
            $('#ac-flujo-carpeta').text(destino ? destino + (nueva ? ' (nueva)' : '') : 'Sin elegir');
            $('#ac-enviar span').text('Archivar ' + txt);
            $('#ac-enviar').prop('disabled', !n || !destino);
            paso(2, destino ? 'hecho' : 'actual');
            paso(3, destino ? 'actual' : '');
            $('#ac-resumen').toggleClass('listo', !!destino).html(destino
                ? '<i class="fa fa-check-circle" style="color:#2E9E5B"></i> Se archivará' + (n == 1 ? '' : 'n') + ' <b>' + txt + '</b> en la carpeta <b>' + esc(destino) + '</b>' + (nueva ? ' <span class="ac-estado e2">se creará</span>' : '') + '.'
                : '<i class="fa fa-hand-o-up"></i> Elija o cree una carpeta para continuar.');
            $('.ac-quitar').toggle(n > 1);
        }

        $('.ac-modo button').on('click', function () {
            modo($(this).attr('data-modo'));
        });

        $('#ac-carpetas').on('change', 'input', function () {
            $('#ac-carpetas .ac-carpeta').removeClass('sel');
            $(this).closest('.ac-carpeta').addClass('sel');
            actualizar();
        });

        $q.on('input', function () {
            var t = $.trim($(this).val()).toLowerCase(), vis = 0;
            $('#ac-carpetas .ac-carpeta').each(function () {
                var ok = !t || $(this).attr('data-nombre').indexOf(t) !== -1;
                $(this).toggle(ok);
                vis += ok ? 1 : 0;
            });
            $('#ac-nada').toggle(vis === 0);
        });

        $('#ac-crear-busqueda').on('click', function (e) {
            e.preventDefault();
            $nc.val($.trim($q.val())).trigger('input');
            modo(0);
        });

        // avisa si el nombre nuevo ya existe y permite usar esa carpeta
        $nc.on('input', function () {
            var $ex = existente($(this).val()), $ayuda = $('#ac-ayuda-nueva');
            $nc.removeClass('error');
            if ($ex.length) {
                $ayuda.addClass('aviso').html('Ya existe una carpeta con ese nombre. <a id="ac-usar">Usar la existente</a>');
            } else {
                $ayuda.removeClass('aviso').text('La carpeta quedará disponible para toda su oficina.');
            }
            actualizar();
        });
        $(document).on('click', '#ac-usar', function () {
            var $ex = existente($nc.val());
            $ex.find('input').prop('checked', true).trigger('change');
            $q.val('').trigger('input');
            modo(1);
            $ex[0].scrollIntoView({block: 'nearest'});
        });

        $('#ac-hojas').on('click', '.ac-ver-recorrido', function () {
            var $hoja = $(this).closest('.ac-hoja').toggleClass('abierta');
            $(this).find('span').text($hoja.hasClass('abierta') ? 'Ocultar recorrido' : 'Ver recorrido');
        });

        $('#ac-hojas').on('click', '.ac-quitar', function () {
            if ($('#ac-hojas .ac-hoja').length > 1) {
                $(this).closest('.ac-hoja').slideUp(150, function () {
                    $(this).remove();
                    actualizar();
                });
            }
        });

        $('#ac-obs').on('input', function () {
            $('#ac-obs-cc').text($(this).val().length + ' / 1000');
        });

        $('#frmArchivar').on('submit', function () {
            if ($tipo.val() == 0 && !$.trim($nc.val())) {
                $nc.addClass('error').focus();
                return false;
            }
            if ($tipo.val() == 1 && !$('#ac-carpetas input:checked').length) {
                return false;
            }
            paso(3, 'hecho');
            $('#ac-enviar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Archivando…');
            return true;
        });

        actualizar();
    });
</script>
<?php endif; ?>
