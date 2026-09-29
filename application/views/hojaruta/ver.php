<?php
// arma una URL de esta pagina conservando los filtros actuales
$url = function (array $cambios = array()) use ($filtros) {
    $p = array_merge($filtros, array('pagina' => 1), $cambios);
    $p = array_filter($p, function ($v) {
        return $v !== '' && $v !== NULL && $v !== 1;
    });
    return '/route/view' . ($p ? '?' . http_build_query($p) : '');
};
$total_general = array_sum(array_map('intval', $por_estado));
$clase_estado = array(1 => 'rv-e-norecibido', 2 => 'rv-e-pendiente', 4 => 'rv-e-derivado', 6 => 'rv-e-agrupado', 10 => 'rv-e-archivado', 11 => 'rv-e-anulado');
$hay_filtros = $filtros['q'] !== '' || $filtros['desde'] !== '' || $filtros['hasta'] !== '' || $filtros['estado'] !== '';
$desde_n = $total ? ($pagina - 1) * $por_pagina + 1 : 0;
$hasta_n = min($total, $pagina * $por_pagina);
?>
<style>
    .rv-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        width: 100%;
    }
    .rv-form .btn {
        margin: 0;
    }
    .rv-form .bj-buscar {
        flex: 1 1 280px;
    }
    .rv-fecha {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #6b7686;
    }
    .rv-fecha input {
        height: 34px;
        padding: 4px 8px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        background: var(--correos-fondo, #F3F5F8);
    }
    .rv-pestanas {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        width: 100%;
    }
    a.bj-filtro:hover {
        text-decoration: none;
    }
    .rv-estado {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .rv-e-norecibido { background: #FFF3DC; color: #B26B00; }
    .rv-e-pendiente { background: #EAF1F9; color: #1A549A; }
    .rv-e-derivado { background: #E6F4EC; color: #227547; }
    .rv-e-agrupado { background: #F1ECFA; color: #5B3C99; }
    .rv-e-archivado { background: #EEF2F7; color: #4a5568; }
    .rv-e-anulado { background: #FDE8E8; color: #B42318; }
    .rv-enviado {
        margin-left: auto;
        font-size: 12px;
        color: #7a8594;
        white-space: nowrap;
    }
    .rv-recepcion {
        display: block;
        margin-top: 4px;
        font-size: 12px;
    }
    .rv-recepcion.rv-ok { color: #227547; }
    .rv-recepcion.rv-espera { color: #B26B00; font-weight: 600; }
    .rv-recepcion.rv-espera-alta { color: #B42318; font-weight: 600; }
    .rv-paginacion {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 14px;
        font-size: 13px;
        color: #6b7686;
    }
    .rv-paginacion .pagination {
        margin: 0;
    }
    .rv-paginacion .pagination > .active > a {
        background: var(--correos-azul, #1A549A);
        border-color: var(--correos-azul, #1A549A);
    }
</style>

<div class="bj-toolbar">
    <h3><i class="md md-label"></i> Seguimiento de lo que derivé
        <span class="bj-contador"><?php echo number_format($total, 0, ',', '.'); ?></span>
    </h3>
    <form class="rv-form" method="get" action="/route/view">
        <div class="bj-buscar">
            <i class="fa fa-search"></i>
            <input type="text" name="q" class="form-control" value="<?php echo HTML::chars($filtros['q']); ?>"
                   placeholder="Hoja de ruta, destinatario, proveído, referencia o cite..."/>
        </div>
        <label class="rv-fecha" title="Enviadas desde">Desde <input type="date" name="desde" value="<?php echo HTML::chars($filtros['desde']); ?>"/></label>
        <label class="rv-fecha" title="Enviadas hasta">Hasta <input type="date" name="hasta" value="<?php echo HTML::chars($filtros['hasta']); ?>"/></label>
        <?php if ($filtros['estado'] !== ''): ?><input type="hidden" name="estado" value="<?php echo HTML::chars($filtros['estado']); ?>"/><?php endif; ?>
        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Buscar</button>
        <?php if ($hay_filtros): ?>
            <a href="/route/view" class="btn btn-sm btn-default-bright" title="Quitar filtros"><i class="fa fa-times"></i> Limpiar</a>
        <?php endif; ?>
    </form>
    <div class="rv-pestanas">
        <a class="bj-filtro <?php echo $filtros['estado'] === '' ? 'activo' : ''; ?>" href="<?php echo $url(array('estado' => '')); ?>">Todas <b><?php echo number_format($total_general, 0, ',', '.'); ?></b></a>
        <?php foreach ($estados as $id => $nombre): ?>
            <?php if (!empty($por_estado[$id])): ?>
                <a class="bj-filtro <?php echo $filtros['estado'] === (string) $id ? 'activo' : ''; ?>" href="<?php echo $url(array('estado' => (string) $id)); ?>">
                    <?php echo $nombre; ?> <b><?php echo number_format((int) $por_estado[$id], 0, ',', '.'); ?></b></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!$filas): ?>
    <div class="bj-vacio">
        <i class="md md-label" style="color: var(--correos-azul, #1A549A)"></i>
        <?php if ($hay_filtros): ?>
            <h4>Sin resultados</h4>
            Ninguna hoja de ruta derivada coincide con los filtros. <a href="/route/view">Ver todas</a>
        <?php else: ?>
            <h4>Aún no derivó hojas de ruta</h4>
            Las hojas de ruta que derive aparecerán aquí para que pueda hacerles seguimiento.
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="bj-lista">
        <?php foreach ($filas as $n):
            $estado = (int) $n['estado'];
            $urgente = (int) $n['prioridad'] > 0;
            $clases = 'bandeja bj-item' . ((int) $n['oficial'] ? '' : ' bj-copia') . ($urgente ? ' bj-urgente' : '');
            $dias = (int) $n['dias'];
            ?>
            <div class="<?php echo $clases; ?>">
                <div class="bj-item-cuerpo">
                    <div class="bj-item-cabecera">
                        <span class="rv-estado <?php echo isset($clase_estado[$estado]) ? $clase_estado[$estado] : ''; ?>">
                            <?php echo isset($estados[$estado]) ? $estados[$estado] : 'Estado ' . $estado; ?></span>
                        <?php if ($urgente): ?><span class="bj-etiqueta bj-urgente-tag">Urgente</span><?php endif; ?>
                        <span class="bj-etiqueta <?php echo (int) $n['oficial'] ? 'bj-oficial' : 'bj-copia-tag'; ?>"><?php echo (int) $n['oficial'] ? 'Oficial' : 'Copia'; ?></span>
                        <a href="/route/trace/?hr=<?php echo urlencode($n['nur']); ?>" class="bj-nur" title="Ver seguimiento"><?php echo HTML::chars($n['nur']); ?></a>
                        <span class="rv-enviado"><i class="fa fa-paper-plane-o"></i> <?php echo date('d/m/Y H:i', strtotime($n['fecha_emision'])); ?></span>
                    </div>
                    <span class="bj-item-titulo"><a href="/route/trace/?hr=<?php echo urlencode($n['nur']); ?>">
                            <?php echo HTML::chars($n['referencia'] != '' ? $n['referencia'] : 'Sin referencia'); ?></a></span>
                    <div class="bj-item-detalle">
                        <div class="bj-persona">
                            <span style="display:inline">Para:</span> <b><?php echo HTML::chars($n['nombre_receptor']); ?></b> &middot; <?php echo HTML::chars($n['cargo_receptor']); ?>
                            <span><?php echo HTML::chars($n['a_oficina']); ?></span>
                            <?php if ($n['cite'] != ''): ?><span class="bj-fecha"><i class="fa fa-file-text-o"></i> <?php echo HTML::chars($n['cite']); ?></span><?php endif; ?>
                            <?php if ($estado === 1): ?>
                                <span class="rv-recepcion <?php echo $dias > 3 ? 'rv-espera-alta' : 'rv-espera'; ?>"><i class="fa fa-clock-o"></i>
                                    Sin recibir <?php echo $dias <= 0 ? 'desde hoy' : 'hace ' . ($dias == 1 ? '1 día' : $dias . ' días'); ?></span>
                            <?php elseif ($n['fecha_recepcion']): ?>
                                <span class="rv-recepcion rv-ok"><i class="fa fa-check"></i> Recibido el <?php echo date('d/m/Y H:i', strtotime($n['fecha_recepcion'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="bj-col-derecha">
                            <div class="bj-proveido"><i class="fa fa-comments-o"></i><?php echo HTML::chars($n['proveido']); ?></div>
                            <div class="bj-item-pie">
                                <div class="bj-item-acciones">
                                    <a href="/route/trace/?hr=<?php echo urlencode($n['nur']); ?>" class="btn btn-sm btn-primary"><i class="md md-verified-user"></i> Ver seguimiento</a>
                                    <a href="/print/hr/?code=<?php echo urlencode($n['nur']); ?>" target="_blank" class="btn btn-sm btn-default-bright"><i class="fa fa-print"></i> Imprimir HR</a>
                                    <?php if ($estado === 1): ?>
                                        <a href="/bandeja/cancel/?id=<?php echo (int) $n['id']; ?>" class="btn btn-sm bj-btn-peligro rv-cancelar"
                                           data-nur="<?php echo HTML::chars($n['nur']); ?>" data-para="<?php echo HTML::chars($n['nombre_receptor']); ?>">
                                            <i class="md md-cancel"></i> Cancelar derivación</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="rv-paginacion">
        <span>Mostrando <?php echo $desde_n; ?>–<?php echo $hasta_n; ?> de <?php echo number_format($total, 0, ',', '.'); ?></span>
        <?php if ($total_paginas > 1): ?>
            <ul class="pagination pagination-sm">
                <li class="<?php echo $pagina <= 1 ? 'disabled' : ''; ?>"><a href="<?php echo $pagina <= 1 ? '#' : $url(array('pagina' => $pagina - 1)); ?>">&laquo; Anterior</a></li>
                <?php
                $inicio = max(1, $pagina - 2);
                $fin = min($total_paginas, $pagina + 2);
                if ($inicio > 1): ?>
                    <li><a href="<?php echo $url(array('pagina' => 1)); ?>">1</a></li>
                    <?php if ($inicio > 2): ?><li class="disabled"><a href="#">…</a></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($p = $inicio; $p <= $fin; $p++): ?>
                    <li class="<?php echo $p == $pagina ? 'active' : ''; ?>"><a href="<?php echo $url(array('pagina' => $p)); ?>"><?php echo $p; ?></a></li>
                <?php endfor; ?>
                <?php if ($fin < $total_paginas): ?>
                    <?php if ($fin < $total_paginas - 1): ?><li class="disabled"><a href="#">…</a></li><?php endif; ?>
                    <li><a href="<?php echo $url(array('pagina' => $total_paginas)); ?>"><?php echo $total_paginas; ?></a></li>
                <?php endif; ?>
                <li class="<?php echo $pagina >= $total_paginas ? 'disabled' : ''; ?>"><a href="<?php echo $pagina >= $total_paginas ? '#' : $url(array('pagina' => $pagina + 1)); ?>">Siguiente &raquo;</a></li>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(document).on('click', '.rv-cancelar', function () {
        return confirm('¿Cancelar la derivación de la hoja de ruta ' + $(this).attr('data-nur') + ' a ' + $(this).attr('data-para')
            + '?\n\nSolo es posible mientras el destinatario no la haya recibido.');
    });
    $(document).on('click', '.pagination .disabled a', function () {
        return false;
    });
</script>
