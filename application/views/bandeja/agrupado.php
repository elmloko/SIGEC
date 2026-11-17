<?php
$h = function ($s) {
    return HTML::chars($s);
};
$fecha = function ($f, $formato = 'd/m/Y H:i') {
    return ($f && substr($f, 0, 4) != '0000') ? date($formato, strtotime($f)) : '';
};
$titulo = function ($s) {
    return mb_convert_case(mb_strtolower($s, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
};
$nombre_estado = function ($id) use ($estados) {
    return isset($estados[$id]) ? $estados[$id] : 'Estado ' . (int) $id;
};
$total_hijas = count($hijos);
$fojas = (int) $padre->hojas;
foreach ($hijos as $hj) {
    $fojas += (int) $hj['hojas'];
}
$fecha_grupo = $hijos[0]['fecha_agrupado'];
$dias_grupo = $fecha_grupo ? max(0, (int) floor((time() - strtotime($fecha_grupo)) / 86400)) : 0;
$pasos_padre = isset($recorrido[$nur]) ? $recorrido[$nur] : array();
$estado_actual = $ultimo ? (int) $ultimo['estado'] : 0;
?>
<div class="ac-card ac-cab">
    <div class="ac-cab-fila">
        <span class="ac-cab-icono"><i class="fa fa-link"></i></span>
        <div class="ac-cab-texto">
            <h2>Hoja de ruta agrupada <span class="agd-nur"><?php echo $h($nur); ?></span></h2>
            <p><?php echo $padre->referencia ? $h($padre->referencia) : 'Sin referencia'; ?></p>
        </div>
        <div class="agd-botones">
            <a href="/route/trace/?hr=<?php echo urlencode($nur); ?>" class="ac-btn"><i class="fa fa-random"></i> Seguimiento</a>
            <a href="/print/agrupado/?code=<?php echo urlencode($nur); ?>" target="_blank" class="ac-btn primario"><i class="fa fa-print"></i> Imprimir carátula</a>
        </div>
    </div>
    <?php if ($ultimo): ?>
        <div class="agd-ubicacion">
            <span class="agd-ubic-icono e<?php echo $estado_actual; ?>"><i class="fa fa-map-marker"></i></span>
            <div class="agd-ubic-texto">
                <small>¿Dónde está ahora la principal?</small>
                <b><?php echo $h($ultimo['a_oficina'] ? $ultimo['a_oficina'] : $ultimo['nombre_receptor']); ?></b>
                <span class="ac-estado e<?php echo $estado_actual; ?>"><?php echo $h($nombre_estado($estado_actual)); ?></span>
                <em><?php echo $h($titulo($ultimo['nombre_receptor'])); ?><?php echo $ultimo['cargo_receptor'] ? ' · ' . $h($ultimo['cargo_receptor']) : ''; ?>
                    · derivada el <?php echo $fecha($ultimo['fecha_emision']); ?><?php echo $fecha($ultimo['fecha_recepcion']) ? ', recibida el ' . $fecha($ultimo['fecha_recepcion']) : ''; ?></em>
            </div>
            <?php if ($puede_desagrupar): ?>
                <a href="/route/deriv/?hr=<?php echo urlencode($nur); ?>" class="ac-btn"><i class="fa fa-share"></i> Derivar grupo</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($mensaje): ?>
    <div class="agd-alerta ok"><i class="fa fa-check-circle"></i> <?php echo $h($mensaje); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="agd-alerta error"><i class="fa fa-exclamation-triangle"></i> <?php echo $h($error); ?></div>
<?php endif; ?>

<div class="ac-stats">
    <div class="ac-card ac-stat"><i class="fa fa-files-o ac-i-azul"></i><div><b><?php echo $total_hijas + 1; ?></b><span>Hojas de ruta en el grupo</span></div></div>
    <div class="ac-card ac-stat"><i class="fa fa-file-text-o ac-i-ambar"></i><div><b><?php echo $fojas; ?></b><span>Fojas en total</span></div></div>
    <div class="ac-card ac-stat"><i class="fa fa-paperclip ac-i-gris"></i><div><b><?php echo count($documentos); ?></b><span>Documentos en la principal</span></div></div>
    <div class="ac-card ac-stat"><i class="fa fa-calendar ac-i-verde"></i><div><b><?php echo $dias_grupo == 1 ? '1 día' : $dias_grupo . ' días'; ?></b><span>Agrupada el <?php echo $fecha($fecha_grupo, 'd/m/Y'); ?></span></div></div>
</div>

<div class="ac-dos">
    <!-- hojas agrupadas -->
    <div class="ac-card ac-panel">
        <h3 class="ac-panel-titulo">
            <i class="fa fa-link"></i> Hojas de ruta unidas a la principal
            <span class="ac-contador" id="agd-visibles"><?php echo $total_hijas; ?></span>
        </h3>
        <p class="ac-panel-sub">Viajan junto con la principal: cuando se deriva o archiva la <?php echo $h($nur); ?>, todo el grupo la acompaña.</p>

        <div class="agd-barra">
            <div class="ac-buscar agd-buscar">
                <i class="fa fa-search"></i>
                <input type="text" id="agd-buscar" placeholder="Buscar por número, referencia, remitente…  (tecla /)" autocomplete="off">
            </div>
            <button type="button" class="ac-btn" id="agd-expandir"><i class="fa fa-expand"></i> <span>Ver recorridos</span></button>
            <button type="button" class="ac-btn" id="agd-copiar" data-nurs="<?php echo $h($nur . ', ' . implode(', ', array_map(function ($x) { return $x['nur']; }, $hijos))); ?>"><i class="fa fa-clipboard"></i> <span>Copiar números</span></button>
        </div>

        <ul class="ac-hojas" id="agd-hojas">
            <?php foreach ($hijos as $hj):
                $pasos = isset($recorrido[$hj['nur']]) ? $recorrido[$hj['nur']] : array();
                $sigue_agrupada = (int) $hj['estado_seg'] === 6;
                ?>
                <li class="ac-hoja agd-hoja<?php echo $sigue_agrupada ? '' : ' copia'; ?>">
                    <div class="ac-hoja-cab">
                        <div class="ac-hoja-cuerpo">
                            <div class="ac-hoja-tags">
                                <a href="/route/trace/?hr=<?php echo urlencode($hj['nur']); ?>" class="ac-hoja-nur" title="Ver seguimiento completo"><?php echo $h($hj['nur']); ?></a>
                                <?php if ($sigue_agrupada): ?>
                                    <span class="ac-tag oficial"><i class="fa fa-link"></i> Agrupada</span>
                                <?php else: ?>
                                    <span class="ac-tag medio" title="El seguimiento de esta hoja ya no figura como agrupado"><?php echo $h($nombre_estado($hj['estado_seg'])); ?></span>
                                <?php endif; ?>
                                <?php if ((int) $hj['hojas'] > 0): ?>
                                    <span class="ac-tag copia"><?php echo (int) $hj['hojas']; ?> foja<?php echo (int) $hj['hojas'] == 1 ? '' : 's'; ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($hj['id_doc']): ?>
                                <a href="/document/detalle/<?php echo (int) $hj['id_doc']; ?>" target="_blank" class="ac-hoja-ref"><?php echo $hj['referencia'] ? $h($hj['referencia']) : 'Sin referencia'; ?></a>
                            <?php else: ?>
                                <span class="ac-hoja-ref">Sin documento original registrado</span>
                            <?php endif; ?>
                            <?php if ($hj['codigo']): ?>
                                <span class="ac-hoja-codigo"><?php echo $h($hj['codigo']); ?><?php echo ($hj['cite_original'] && $hj['cite_original'] != $hj['codigo']) ? ' · Cite: ' . $h($hj['cite_original']) : ''; ?></span>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="ac-quitar agd-copiar-uno" data-nur="<?php echo $h($hj['nur']); ?>" title="Copiar número"><i class="fa fa-files-o"></i></button>
                    </div>

                    <div class="ac-hoja-datos">
                        <div class="ac-dato">
                            <small>Remitente</small>
                            <span title="<?php echo $h($hj['nombre_remitente']); ?>"><?php echo $h($titulo($hj['nombre_remitente'])); ?></span>
                            <em title="<?php echo $h($hj['cargo_remitente']); ?>"><?php echo $h($hj['cargo_remitente']); ?><?php echo $hj['institucion_remitente'] ? ' · ' . $h($hj['institucion_remitente']) : ''; ?></em>
                        </div>
                        <div class="ac-dato">
                            <small>Destinatario</small>
                            <span title="<?php echo $h($hj['nombre_destinatario']); ?>"><?php echo $h($titulo($hj['nombre_destinatario'])); ?></span>
                            <em title="<?php echo $h($hj['cargo_destinatario']); ?>"><?php echo $h($hj['cargo_destinatario']); ?></em>
                        </div>
                        <div class="ac-dato">
                            <small>Creada / agrupada</small>
                            <span><?php echo $fecha($hj['fecha_creacion']); ?></span>
                            <em title="<?php echo $h($hj['agrupado_por'] . ' · ' . $hj['agrupado_cargo']); ?>">Agrupada <?php echo $fecha($hj['fecha_agrupado']); ?> por <?php echo $h($titulo($hj['agrupado_por'])); ?></em>
                        </div>
                    </div>

                    <div class="ac-hoja-pie">
                        <a class="ac-ver-recorrido"><i class="fa fa-random"></i> <span>Ver recorrido</span> (<?php echo count($pasos); ?> paso<?php echo count($pasos) == 1 ? '' : 's'; ?>)</a>
                        <?php if ($puede_desagrupar && $sigue_agrupada): ?>
                            <form method="post" action="/bandeja/desagrupar" class="agd-form-quitar">
                                <input type="hidden" name="padre" value="<?php echo $h($nur); ?>">
                                <input type="hidden" name="id" value="<?php echo (int) $hj['id_agrupacion']; ?>">
                                <button type="submit" class="agd-btn-quitar" data-nur="<?php echo $h($hj['nur']); ?>"><i class="fa fa-chain-broken"></i> Quitar del grupo</button>
                            </form>
                        <?php endif; ?>
                        <a href="/route/trace/?hr=<?php echo urlencode($hj['nur']); ?>" class="ac-der"><i class="fa fa-external-link"></i> Seguimiento</a>
                    </div>

                    <ol class="ac-recorrido">
                        <?php foreach ($pasos as $p): ?>
                            <li class="ac-paso-r<?php echo (int) $p['id'] === (int) $hj['id_seguimiento'] ? ' actual' : ''; ?>">
                                <b><?php echo $h($p['de_oficina'] ? $p['de_oficina'] : $p['nombre_emisor']); ?></b>
                                <i class="fa fa-long-arrow-right ac-flecha"></i>
                                <b><?php echo $h($p['a_oficina'] ? $p['a_oficina'] : $p['nombre_receptor']); ?></b>
                                <span class="ac-estado e<?php echo (int) $p['estado']; ?>"><?php echo $h($nombre_estado($p['estado'])); ?></span>
                                <?php if ($p['oficial'] <= 0): ?><span class="ac-estado">Copia</span><?php endif; ?>
                                <small><?php echo $h($p['nombre_emisor']); ?> → <?php echo $h($p['nombre_receptor']); ?> · <?php echo $fecha($p['fecha_emision']); ?></small>
                                <?php if (trim($p['proveido'])): ?><span class="ac-prov">“<?php echo $h($p['proveido']); ?>”</span><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        <li class="ac-paso-r final"><b>Unida a <?php echo $h($nur); ?></b> <small>Desde aquí sigue el recorrido de la principal</small></li>
                    </ol>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="ac-nada" id="agd-nada"><i class="fa fa-search"></i> Ninguna hoja coincide con la búsqueda.</div>
    </div>

    <!-- principal -->
    <div class="ac-lateral">
        <div class="ac-card ac-panel">
            <h3 class="ac-panel-titulo"><i class="fa fa-sitemap"></i> Estructura del grupo</h3>
            <p class="ac-panel-sub">Haga clic en una hoja para ir a su seguimiento.</p>
            <div class="ag-arbol">
                <a class="ag-nodo principal" href="/route/trace/?hr=<?php echo urlencode($nur); ?>">
                    <i class="fa fa-star"></i>
                    <div><b><?php echo $h($nur); ?> · Principal</b><span><?php echo $h($padre->referencia); ?></span></div>
                </a>
                <?php foreach ($hijos as $hj): ?>
                    <a class="ag-nodo hija" href="/route/trace/?hr=<?php echo urlencode($hj['nur']); ?>">
                        <i class="fa fa-link"></i>
                        <div><b><?php echo $h($hj['nur']); ?></b><span><?php echo $h($hj['referencia']); ?></span></div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="agd-ficha">
                <div class="ac-dato"><small>Cite original</small><span><?php echo $h($padre->cite_original ? $padre->cite_original : '—'); ?></span></div>
                <div class="ac-dato"><small>Remitente</small><span><?php echo $h($titulo($padre->nombre_remitente)); ?></span><em><?php echo $h($padre->cargo_remitente); ?></em></div>
                <div class="ac-dato"><small>Destinatario</small><span><?php echo $h($titulo($padre->nombre_destinatario)); ?></span><em><?php echo $h($padre->cargo_destinatario); ?></em></div>
                <div class="ac-dato"><small>Agrupada por</small><span><?php echo $h($titulo($hijos[0]['agrupado_por'])); ?></span><em><?php echo $h($hijos[0]['agrupado_cargo']); ?> · <?php echo $fecha($fecha_grupo); ?></em></div>
            </div>
        </div>

        <div class="ac-card ac-panel">
            <h3 class="ac-panel-titulo"><i class="fa fa-paperclip"></i> Documentos de la principal <span class="ac-contador"><?php echo count($documentos); ?></span></h3>
            <ul class="agd-docs">
                <?php foreach ($documentos as $d): ?>
                    <li>
                        <a href="/document/detalle/<?php echo (int) $d['id']; ?>" target="_blank">
                            <i class="fa <?php echo $d['original'] ? 'fa-file-text' : 'fa-file-text-o'; ?>"></i>
                            <span>
                                <b><?php echo $h($d['codigo']); ?></b>
                                <em><?php echo $h($d['tipo'] ? $d['tipo'] : 'Documento'); ?><?php echo $d['original'] ? ' · Original' : ''; ?> · <?php echo $fecha($d['fecha_creacion'], 'd/m/Y'); ?></em>
                                <small><?php echo $h($d['referencia']); ?></small>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="ac-card ac-panel">
            <h3 class="ac-panel-titulo"><i class="fa fa-random"></i> Recorrido de la principal <span class="ac-contador"><?php echo count($pasos_padre); ?></span></h3>
            <ol class="ac-recorrido agd-recorrido-fijo">
                <?php foreach ($pasos_padre as $i => $p): ?>
                    <li class="ac-paso-r<?php echo $ultimo && (int) $p['id'] === (int) $ultimo['id'] ? ' actual' : ''; ?>">
                        <b><?php echo $h($p['de_oficina'] ? $p['de_oficina'] : $p['nombre_emisor']); ?></b>
                        <i class="fa fa-long-arrow-right ac-flecha"></i>
                        <b><?php echo $h($p['a_oficina'] ? $p['a_oficina'] : $p['nombre_receptor']); ?></b>
                        <span class="ac-estado e<?php echo (int) $p['estado']; ?>"><?php echo $h($nombre_estado($p['estado'])); ?></span>
                        <?php if ($p['oficial'] <= 0): ?><span class="ac-estado">Copia</span><?php endif; ?>
                        <small><?php echo $fecha($p['fecha_emision']); ?></small>
                        <?php if (trim($p['proveido'])): ?><span class="ac-prov">“<?php echo $h($p['proveido']); ?>”</span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
            <div class="ac-info">
                <b class="t"><i class="fa fa-info-circle"></i> Quitar hojas del grupo</b>
                <?php if ($puede_desagrupar): ?>
                    Mientras la principal siga en sus <b>Pendientes</b> puede quitar una hoja del grupo con <i>Quitar del grupo</i>: volverá a sus pendientes como una hoja independiente.
                <?php else: ?>
                    Solo quien tiene la principal <b>pendiente en su bandeja</b> puede quitar hojas del grupo. Ahora está en <b><?php echo $h($ultimo ? ($ultimo['a_oficina'] ? $ultimo['a_oficina'] : $ultimo['nombre_receptor']) : '—'); ?></b> (<?php echo $h($nombre_estado($estado_actual)); ?>).
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var $hojas = $('#agd-hojas .agd-hoja');

        // busqueda en vivo sobre el texto de cada hoja
        function normalizar(t) {
            t = (t || '').toLowerCase();
            return t.normalize ? t.normalize('NFD').replace(/[̀-ͯ]/g, '') : t;
        }
        $hojas.each(function () {
            $(this).data('texto', normalizar($(this).find('.ac-hoja-cab, .ac-hoja-datos').text()));
        });
        $('#agd-buscar').on('input keyup', function () {
            var q = normalizar($.trim(this.value)), visibles = 0;
            $hojas.each(function () {
                var ok = !q || $(this).data('texto').indexOf(q) !== -1;
                $(this).toggleClass('ac-oculto', !ok);
                visibles += ok ? 1 : 0;
            });
            $('#agd-visibles').text(visibles);
            $('#agd-nada').toggle(visibles === 0);
        });
        $(document).on('keydown', function (e) {
            if (e.key === '/' && !$(e.target).is('input, textarea, select')) {
                e.preventDefault();
                $('#agd-buscar').focus();
            }
        });

        // recorrido por hoja y todos a la vez
        $('#agd-hojas').on('click', '.ac-ver-recorrido', function () {
            var $hoja = $(this).closest('.ac-hoja').toggleClass('abierta');
            $(this).find('span').text($hoja.hasClass('abierta') ? 'Ocultar recorrido' : 'Ver recorrido');
        });
        $('#agd-expandir').on('click', function () {
            var abrir = $hojas.filter('.abierta').length < $hojas.length;
            $hojas.toggleClass('abierta', abrir);
            $hojas.find('.ac-ver-recorrido span').text(abrir ? 'Ocultar recorrido' : 'Ver recorrido');
            $(this).find('span').text(abrir ? 'Ocultar recorridos' : 'Ver recorridos');
            $(this).find('.fa').toggleClass('fa-expand', !abrir).toggleClass('fa-compress', abrir);
        });

        // copiar al portapapeles (con respaldo para http sin clipboard API)
        function copiar(texto, $btn) {
            var listo = function () {
                var $s = $btn.find('span'), antes = $s.text();
                $btn.addClass('agd-copiado');
                $s.text('¡Copiado!');
                setTimeout(function () { $btn.removeClass('agd-copiado'); $s.text(antes); }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(texto).then(listo);
                return;
            }
            var $t = $('<textarea>').val(texto).css({position: 'fixed', opacity: 0}).appendTo('body');
            $t[0].select();
            try { document.execCommand('copy'); listo(); } catch (e) {}
            $t.remove();
        }
        $('#agd-copiar').on('click', function () {
            copiar($(this).attr('data-nurs'), $(this));
        });
        $('#agd-hojas').on('click', '.agd-copiar-uno', function () {
            copiar($(this).attr('data-nur'), $(this));
        });

        $('.agd-btn-quitar').on('click', function (e) {
            if (!confirm('¿Quitar la hoja ' + $(this).attr('data-nur') + ' del grupo?\nVolverá a sus pendientes como hoja independiente.')) {
                e.preventDefault();
            }
        });
    });
</script>
