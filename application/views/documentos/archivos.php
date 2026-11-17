<?php
// Archivos digitales: una pagina de archivos (Model_Archivos::pagina_vista) con busqueda, fechas y orden en el servidor
$meses = array(1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$peso = function ($bytes) {
    return $bytes >= 1048576 ? number_format($bytes / 1048576, 1) . ' MB' : number_format($bytes / 1024, 0) . ' KB';
};
$lista = array();
foreach ($results as $r) {
    $nombre = substr($r['nombre_archivo'], 13);
    $ts = $r['fecha'] ? strtotime($r['fecha']) : 0;
    $lista[] = array(
        'id' => (int) $r['id'],
        'id_documento' => (int) $r['id_documento'],
        'nombre' => $nombre,
        'es_pdf' => stripos((string) $r['extension'], 'pdf') !== FALSE || preg_match('/\.pdf$/i', $nombre),
        'tamanio' => $peso((float) $r['tamanio']),
        'fecha' => $ts ? date('d/m/Y H:i', $ts) : '',
        'mes' => $ts ? $meses[(int) date('n', $ts)] . ' ' . date('Y', $ts) : 'Sin fecha',
        'cite' => $r['cite_original'] != '' ? $r['cite_original'] : $r['codigo'],
        'tipo' => $r['tipo'],
        'referencia' => $r['referencia'],
        'nur' => $r['nur'],
    );
}
$total_todos = (int) $resumen['total'];
$pagina = $f['pagina'];
$desde_n = $total ? ($pagina - 1) * $por_pagina + 1 : 0;
$hasta_n = min($total, $pagina * $por_pagina);
$hay_filtros = $f['q'] !== '' || $f['desde'] !== '' || $f['hasta'] !== '';
// meses agrupados solo cuando se ordena por fecha
$por_fecha = strpos($f['orden'], 'fecha') === 0;

// enlace con los filtros actuales cambiando algunos (se omiten vacios, el orden por defecto y la pagina 1)
$enlace = function ($cambios) use ($f, $accion) {
    $p = array();
    foreach (array_merge($f, $cambios) as $k => $v) {
        if ($v === '' || $v === NULL || ($k === 'pagina' && (int) $v === 1) || ($k === 'orden' && $v === 'fecha-desc')) {
            continue;
        }
        $p[$k] = $v;
    }
    return $accion . ($p ? '?' . http_build_query($p) : '');
};
// atajos de fecha
$hoy = date('Y-m-d');
$atajos = array(
    'Hoy' => array($hoy, $hoy),
    'Últimos 7 días' => array(date('Y-m-d', strtotime('-6 days')), $hoy),
    'Este mes' => array(date('Y-m-01'), $hoy),
    'Mes pasado' => array(date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('first day of last month'))),
    'Este año' => array(date('Y-01-01'), $hoy),
);
$e = function ($s) {
    return HTML::chars($s);
};
?>
<style>
    /* ===== cabecera (mismo estilo que Mis seguimientos) ===== */
    .arc-barra {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px 20px;
        padding: 18px 22px;
        margin-bottom: 16px;
        border-radius: 14px;
        background: linear-gradient(135deg, #123E73 0%, #1A549A 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(18, 62, 115, .18);
    }
    .arc-barra h3 { flex: 1 1 260px; margin: 0; font-size: 22px; font-weight: 700; color: #fff; }
    .arc-barra h3 .fa { color: var(--correos-amarillo, #FECB34); margin-right: 8px; }
    .arc-barra h3 small { display: block; margin-top: 4px; font-size: 13px; font-weight: 400; color: rgba(255, 255, 255, .8); }
    .arc-buscar { position: relative; flex: 0 1 360px; min-width: 200px; }
    .arc-buscar .fa { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #6b7686; }
    .arc-buscar input,
    .arc-orden {
        height: 42px;
        border: 0;
        border-radius: 21px;
        background: #fff;
        color: #1f2d3d;
        font-size: 13px;
        box-shadow: none;
    }
    .arc-buscar input { width: 100%; padding: 0 14px 0 38px; }
    .arc-orden { padding: 0 14px; cursor: pointer; }
    .arc-buscar input:focus,
    .arc-orden:focus { outline: 3px solid rgba(254, 203, 52, .7); }

    /* ===== resumen ===== */
    .arc-resumen {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }
    .arc-kpi {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        background: #fff;
    }
    .arc-kpi i {
        flex: 0 0 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .arc-kpi i.rojo { color: #D32F2F; background: #FDE8E8; }
    .arc-kpi i.verde { color: #2f855a; background: #E6F4EA; }
    .arc-kpi b { display: block; font-size: 20px; line-height: 1.1; color: var(--correos-azul-oscuro, #123E73); }
    .arc-kpi span { font-size: 12px; color: #6b7686; }
    .arc-cuenta { margin: 0 0 8px; font-size: 12.5px; color: #6b7686; }
    .arc-cuenta b { color: #1f2d3d; }

    .arc-mes {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 20px 0 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: #6b7686;
    }
    .arc-mes::after { content: ''; flex: 1 1 auto; height: 1px; background: var(--correos-borde, #DCE3EC); }
    .arc-mes span {
        padding: 1px 8px;
        border-radius: 10px;
        font-weight: 700;
        letter-spacing: 0;
        color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .arc-mes:first-child {
        margin-top: 0;
    }
    .arc-item {
        display: grid;
        grid-template-columns: 44px minmax(0, 2fr) minmax(0, 2fr) auto;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        margin-bottom: 10px;
        background: #fff;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .06);
        transition: box-shadow .15s;
    }
    .arc-item:hover {
        box-shadow: 0 6px 18px rgba(18, 62, 115, .10);
    }
    .arc-doc {
        padding-left: 14px;
        border-left: 1px dashed #E3E8EE;
    }
    .arc-icono {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        background: #FDE8E8;
        color: #D32F2F;
    }
    .arc-icono.arc-otro {
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
    }
    .arc-nombre {
        display: block;
        font-weight: 600;
        color: #2d3748;
        word-break: break-word;
        line-height: 1.3;
    }
    .arc-meta {
        display: block;
        margin-top: 3px;
        font-size: 12px;
        color: #8a94a3;
    }
    .arc-doc {
        min-width: 0;
        font-size: 13px;
    }
    .arc-doc a.arc-cite {
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .arc-tipo {
        display: inline-block;
        padding: 1px 7px;
        margin-right: 4px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .arc-ref {
        display: block;
        margin-top: 3px;
        color: #4a5568;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .arc-hr {
        display: inline-block;
        margin-top: 4px;
        font-size: 12px;
        font-weight: 600;
        color: var(--correos-azul, #1A549A);
    }
    .arc-acciones {
        display: flex;
        gap: 6px;
        white-space: nowrap;
    }
    .arc-acciones .btn {
        margin: 0;
    }
    .arc-vacio,
    .arc-sin-resultados {
        padding: 40px 20px;
        text-align: center;
        color: #7a8594;
        background: #fff;
        border-radius: 10px;
    }
    .arc-vacio .fa {
        display: block;
        margin-bottom: 10px;
        font-size: 40px;
        color: var(--correos-azul, #1A549A);
    }
    @media (max-width: 991px) {
        .arc-item {
            grid-template-columns: 44px minmax(0, 1fr);
        }
        .arc-doc,
        .arc-acciones {
            grid-column: 2;
        }
        .arc-doc { padding-left: 0; border-left: 0; }
        .arc-resumen { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .arc-barra { padding: 16px; }
        .arc-buscar,
        .arc-orden {
            flex: 1 1 100%;
        }
    }

    /* ===== filtros de fecha ===== */
    .arc-filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 16px;
        padding: 10px 14px;
        margin-bottom: 12px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--correos-borde, #DCE3EC);
    }
    .arc-filtros-tit { font-size: 11px; font-weight: 700; color: #6b7686; text-transform: uppercase; letter-spacing: .5px; }
    .arc-atajos { display: inline-flex; flex-wrap: wrap; gap: 4px; padding: 3px; border-radius: 10px; background: #F5F7FA; }
    .arc-atajos a {
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5568;
        white-space: nowrap;
    }
    .arc-atajos a:hover { color: var(--correos-azul, #1A549A); text-decoration: none; }
    .arc-atajos a.activo { background: #fff; color: var(--correos-azul, #1A549A); box-shadow: 0 1px 3px rgba(18, 62, 115, .18); }
    .arc-fechas { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: #6b7686; }
    .arc-fechas input {
        height: 32px;
        padding: 3px 8px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        color: #1f2d3d;
    }
    .arc-fechas button {
        height: 32px;
        padding: 0 12px;
        border: 0;
        border-radius: 8px;
        background: var(--correos-azul, #1A549A);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
    }
    .arc-activos { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-bottom: 10px; font-size: 12px; color: #6b7686; }
    .arc-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 14px;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
        font-weight: 600;
    }
    .arc-chip:hover { background: #dbe7f5; text-decoration: none; }
    .arc-quitar { margin-left: 4px; font-weight: 600; color: #c53030; }
    .arc-pie {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 16px;
        font-size: 12.5px;
        color: #6b7686;
    }
    .arc-pie .pagination { margin: 0; }
    .arc-pie .pagination > li > a { border-radius: 6px; margin: 0 2px; }
    .arc-pie .pagination > .active > a { background: var(--correos-azul, #1A549A); border-color: var(--correos-azul, #1A549A); }
    .arc-buscar .arc-x { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #6b7686; }
</style>

<div class="arc-barra">
    <h3><i class="fa fa-paperclip"></i>Archivos digitales
        <small>Los archivos que subiste a tus documentos. Puedes verlos sin descargarlos o bajarlos.</small>
    </h3>
    <form class="arc-buscar" method="get" action="<?php echo $e($accion); ?>" role="search">
        <?php foreach (array('desde', 'hasta') as $k): ?>
            <?php if ($f[$k] !== ''): ?><input type="hidden" name="<?php echo $k; ?>" value="<?php echo $e($f[$k]); ?>"/><?php endif; ?>
        <?php endforeach; ?>
        <?php if ($f['orden'] !== 'fecha-desc'): ?><input type="hidden" name="orden" value="<?php echo $e($f['orden']); ?>"/><?php endif; ?>
        <i class="fa fa-search"></i>
        <input type="text" name="q" value="<?php echo $e($f['q']); ?>" aria-label="Buscar" autocomplete="off"
               placeholder="Buscar por nombre, cite, referencia u hoja de ruta…"/>
        <?php if ($f['q'] !== ''): ?>
            <a class="arc-x" href="<?php echo $e($enlace(array('q' => '', 'pagina' => 1))); ?>" title="Quitar búsqueda"><i class="fa fa-times-circle"></i></a>
        <?php endif; ?>
    </form>
    <select class="arc-orden" title="Ordenar" aria-label="Ordenar" onchange="location.href = this.value">
        <?php foreach (array('fecha-desc' => 'Más recientes primero', 'fecha-asc' => 'Más antiguos primero', 'nombre-asc' => 'Nombre (A-Z)', 'tamanio-desc' => 'Más pesados primero') as $clave => $texto): ?>
            <option value="<?php echo $e($enlace(array('orden' => $clave, 'pagina' => 1))); ?>" <?php echo $f['orden'] === $clave ? 'selected' : ''; ?>><?php echo $texto; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<?php if (!$total_todos): ?>
    <div class="arc-vacio">
        <i class="fa fa-cloud-upload"></i>
        <h4>Aún no subiste archivos digitales</h4>
        Los archivos que subas a tus documentos aparecerán aquí.
    </div>
<?php else: ?>
    <div class="arc-resumen">
        <div class="arc-kpi"><i class="fa fa-files-o"></i><div><b><?php echo number_format($total_todos, 0, ',', '.'); ?></b><span>Archivos subidos</span></div></div>
        <div class="arc-kpi"><i class="fa fa-calendar verde"></i><div><b><?php echo number_format((int) $resumen['mes'], 0, ',', '.'); ?></b><span>Este mes</span></div></div>
        <div class="arc-kpi"><i class="fa fa-calendar-o"></i><div><b><?php echo number_format((int) $resumen['anio'], 0, ',', '.'); ?></b><span>Este año</span></div></div>
        <div class="arc-kpi"><i class="fa fa-database"></i><div><b><?php echo $peso((float) $resumen['bytes']); ?></b><span>Espacio usado</span></div></div>
    </div>

    <!-- fechas: atajos y rango libre (por la fecha en que se subio) -->
    <div class="arc-filtros">
        <span class="arc-filtros-tit">Subidos</span>
        <nav class="arc-atajos" aria-label="Rango de fechas">
            <a class="<?php echo $f['desde'] === '' && $f['hasta'] === '' ? 'activo' : ''; ?>" href="<?php echo $e($enlace(array('desde' => '', 'hasta' => '', 'pagina' => 1))); ?>">Todos</a>
            <?php foreach ($atajos as $texto => $r): ?>
                <a class="<?php echo $f['desde'] === $r[0] && $f['hasta'] === $r[1] ? 'activo' : ''; ?>" href="<?php echo $e($enlace(array('desde' => $r[0], 'hasta' => $r[1], 'pagina' => 1))); ?>"><?php echo $texto; ?></a>
            <?php endforeach; ?>
        </nav>
        <form class="arc-fechas" method="get" action="<?php echo $e($accion); ?>">
            <?php if ($f['q'] !== ''): ?><input type="hidden" name="q" value="<?php echo $e($f['q']); ?>"/><?php endif; ?>
            <?php if ($f['orden'] !== 'fecha-desc'): ?><input type="hidden" name="orden" value="<?php echo $e($f['orden']); ?>"/><?php endif; ?>
            <i class="fa fa-calendar"></i>
            <input type="date" name="desde" value="<?php echo $e($f['desde']); ?>" max="<?php echo $hoy; ?>" aria-label="Desde"/>
            <span>a</span>
            <input type="date" name="hasta" value="<?php echo $e($f['hasta']); ?>" max="<?php echo $hoy; ?>" aria-label="Hasta"/>
            <button type="submit">Buscar</button>
        </form>
    </div>

    <?php if ($hay_filtros): ?>
        <div class="arc-activos">
            <span>Filtrando por:</span>
            <?php if ($f['q'] !== ''): ?>
                <a class="arc-chip" href="<?php echo $e($enlace(array('q' => '', 'pagina' => 1))); ?>">«<?php echo $e($f['q']); ?>» <i class="fa fa-times"></i></a>
            <?php endif; ?>
            <?php if ($f['desde'] !== '' || $f['hasta'] !== ''): ?>
                <a class="arc-chip" href="<?php echo $e($enlace(array('desde' => '', 'hasta' => '', 'pagina' => 1))); ?>">
                    <?php echo $f['desde'] !== '' ? date('d/m/Y', strtotime($f['desde'])) : 'el inicio'; ?> – <?php echo $f['hasta'] !== '' ? date('d/m/Y', strtotime($f['hasta'])) : 'hoy'; ?> <i class="fa fa-times"></i></a>
            <?php endif; ?>
            <a class="arc-quitar" href="<?php echo $e($enlace(array('q' => '', 'desde' => '', 'hasta' => '', 'pagina' => 1))); ?>">Quitar todos</a>
        </div>
    <?php endif; ?>

    <?php if (!$lista): ?>
        <div class="arc-vacio">
            <i class="fa fa-search"></i>
            <h4>Sin resultados</h4>
            Ningún archivo coincide con la búsqueda o las fechas. <a href="<?php echo $e($accion); ?>">Ver todos</a>
        </div>
    <?php else: ?>
        <p class="arc-cuenta">Mostrando <b><?php echo $desde_n; ?>–<?php echo $hasta_n; ?></b> de <b><?php echo number_format($total, 0, ',', '.'); ?></b> archivo<?php echo $total == 1 ? '' : 's'; ?><?php echo $hay_filtros ? ($total == 1 ? ' que coincide' : ' que coinciden') : ''; ?></p>
        <div id="arc-lista">
            <?php $mes_actual = NULL; ?>
            <?php foreach ($lista as $a): ?>
                <?php if ($por_fecha && $a['mes'] !== $mes_actual): $mes_actual = $a['mes']; ?>
                    <div class="arc-mes"><?php echo $e($mes_actual); ?></div>
                <?php endif; ?>
                <div class="arc-item">
                    <div class="arc-icono <?php echo $a['es_pdf'] ? '' : 'arc-otro'; ?>"><i class="fa <?php echo $a['es_pdf'] ? 'fa-file-pdf-o' : 'fa-file-o'; ?>"></i></div>
                    <div>
                        <span class="arc-nombre"><?php echo $e($a['nombre']); ?></span>
                        <span class="arc-meta"><?php echo $a['tamanio']; ?> &middot; subido el <?php echo $a['fecha']; ?></span>
                    </div>
                    <div class="arc-doc">
                        <span class="arc-tipo"><?php echo $e($a['tipo']); ?></span>
                        <a class="arc-cite" href="/document/detalle/<?php echo $a['id_documento']; ?>" title="Ver documento"><?php echo $e($a['cite']); ?></a>
                        <span class="arc-ref" title="<?php echo $e($a['referencia']); ?>"><?php echo $e($a['referencia']); ?></span>
                        <?php if (trim($a['nur']) != ''): ?>
                            <a class="arc-hr" href="/route/trace/?hr=<?php echo urlencode($a['nur']); ?>" title="Ver seguimiento"><i class="md md-label"></i> <?php echo $e($a['nur']); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="arc-acciones">
                        <?php if ($a['es_pdf']): ?>
                            <a href="#" class="btn btn-sm btn-primary visor-pdf" title="Ver sin descargar"
                               data-url="/download/?file=<?php echo $a['id']; ?>&amp;ver=1" data-descargar="/download/?file=<?php echo $a['id']; ?>"
                               data-nombre="<?php echo $e($a['nombre']); ?>"><i class="fa fa-eye"></i> Ver</a>
                        <?php endif; ?>
                        <a href="/download/?file=<?php echo $a['id']; ?>" class="btn btn-sm btn-default-bright" title="Descargar"><i class="fa fa-download"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($paginas > 1): ?>
        <div class="arc-pie">
            <span>Página <b><?php echo $pagina; ?></b> de <b><?php echo $paginas; ?></b></span>
            <ul class="pagination pagination-sm">
                <li class="<?php echo $pagina <= 1 ? 'disabled' : ''; ?>"><a href="<?php echo $e($enlace(array('pagina' => max(1, $pagina - 1)))); ?>">&laquo; Anterior</a></li>
                <?php if ($pagina > 3): ?>
                    <li><a href="<?php echo $e($enlace(array('pagina' => 1))); ?>">1</a></li>
                    <?php if ($pagina > 4): ?><li class="disabled"><a href="#">…</a></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = max(1, $pagina - 2); $i <= min($paginas, $pagina + 2); $i++): ?>
                    <li class="<?php echo $i === $pagina ? 'active' : ''; ?>"><a href="<?php echo $e($enlace(array('pagina' => $i))); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <?php if ($pagina < $paginas - 2): ?>
                    <?php if ($pagina < $paginas - 3): ?><li class="disabled"><a href="#">…</a></li><?php endif; ?>
                    <li><a href="<?php echo $e($enlace(array('pagina' => $paginas))); ?>"><?php echo $paginas; ?></a></li>
                <?php endif; ?>
                <li class="<?php echo $pagina >= $paginas ? 'disabled' : ''; ?>"><a href="<?php echo $e($enlace(array('pagina' => min($paginas, $pagina + 1)))); ?>">Siguiente &raquo;</a></li>
            </ul>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
    $(document).on('click', '.arc-pie .pagination .disabled a', function () {
        return false;
    });
    // al cambiar una fecha con un atajo activo, el formulario manda el rango elegido a mano
    $('.arc-fechas input[type=date]').on('change', function () {
        var d = $('.arc-fechas input[name=desde]').val(), h = $('.arc-fechas input[name=hasta]').val();
        if (d && h && d > h) {
            $('.arc-fechas input[name=hasta]').val(d);
        }
    });
</script>

<?php echo View::factory('documentos/visor_pdf'); ?>
