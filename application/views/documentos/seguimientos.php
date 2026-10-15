<?php
// situaciones de una hoja de ruta (las calcula Model_Hojasruta::seguimientosUsuario)
$situaciones = array(
    // (filtro, icono, clase, etiqueta en la fila)
    'curso' => array('En curso', 'fa-share', 'seg-curso', 'En curso'),
    'sin_derivar' => array('Sin derivar', 'fa-inbox', 'seg-sin', 'Sin derivar'),
    'archivada' => array('Archivadas (FIN)', 'fa-archive', 'seg-archivada', 'Archivada (FIN)'),
    'agrupada' => array('Agrupadas', 'fa-link', 'seg-agrupada', 'Agrupada'),
    'concluida' => array('Concluidas', 'fa-check', 'seg-concluida', 'Concluida'),
);
$total_todas = array_sum($resumen);

$enlace = function ($cambios) use ($situacion, $q, $pagina) {
    $p = array_merge(array('situacion' => $situacion, 'q' => $q, 'pagina' => $pagina), $cambios);
    $p = array_filter($p, function ($v) {
        return $v !== '' && $v !== NULL && $v !== 1;
    });
    return '/document/seguimientos' . ($p ? '?' . http_build_query($p) : '');
};
$fecha = function ($f) {
    $t = strtotime($f);
    return $t ? array(date('d/m/Y', $t), date('H:i', $t)) : array('—', '');
};
// color del tiempo que lleva: hasta 3 dias normal, hasta 7 atencion, mas de 7 demorado
$nivel = function ($segundos) {
    $dias = $segundos / 86400;
    return $dias <= 3 ? 'ok' : ($dias <= 7 ? 'alerta' : 'demora');
};
// iniciales para el avatar (primer nombre y primer apellido)
$iniciales = function ($nombre) {
    $partes = preg_split('/\s+/u', trim((string) $nombre));
    $ini = mb_substr($partes[0], 0, 1, 'UTF-8');
    if (count($partes) > 2) {
        $ini .= mb_substr($partes[count($partes) - 2], 0, 1, 'UTF-8');
    } elseif (count($partes) === 2) {
        $ini .= mb_substr($partes[1], 0, 1, 'UTF-8');
    }
    return mb_strtoupper($ini, 'UTF-8');
};
?>
<style>
    .seg {
        --seg-azul: var(--correos-azul, #1A549A);
        --seg-azul-oscuro: var(--correos-azul-oscuro, #123E73);
        --seg-azul-suave: var(--correos-azul-suave, #EAF1F9);
        --seg-amarillo: var(--correos-amarillo, #FECB34);
        --seg-borde: var(--correos-borde, #DCE3EC);
        --seg-texto: #2d3748;
        --seg-gris: #7a8594;
        --seg-ok: #2f855a;
        --seg-ok-suave: #E6F4EA;
        --seg-alerta: #b7791f;
        --seg-alerta-suave: #FEF5E1;
        --seg-demora: #c53030;
        --seg-demora-suave: #FDECEC;
    }
    .seg-curso { --c: #1A549A; --c-suave: #EAF1F9; }
    .seg-sin { --c: #b7791f; --c-suave: #FEF5E1; }
    .seg-archivada { --c: #4a5568; --c-suave: #EDEFF2; }
    .seg-agrupada { --c: #6b46c1; --c-suave: #F1ECF9; }
    .seg-concluida { --c: #2f855a; --c-suave: #E6F4EA; }
    .seg-todas { --c: #123E73; --c-suave: #EAF1F9; }

    /* ===== encabezado ===== */
    .seg-encabezado {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .seg-encabezado h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        color: var(--seg-azul-oscuro);
    }
    .seg-encabezado h2 i {
        color: var(--seg-amarillo);
        margin-right: 6px;
    }
    .seg-encabezado h2 small {
        display: block;
        margin-top: 4px;
        font-size: 13px;
        font-weight: 400;
        color: #6b7686;
    }
    .seg-buscar {
        position: relative;
        display: flex;
        align-items: center;
        width: 340px;
        max-width: 100%;
    }
    .seg-buscar > i {
        position: absolute;
        left: 14px;
        color: var(--seg-gris);
        pointer-events: none;
    }
    .seg-buscar input {
        width: 100%;
        height: 40px;
        padding: 0 40px 0 38px;
        border: 1px solid var(--seg-borde);
        border-radius: 20px;
        background: #fff;
        font-size: 13px;
        color: var(--seg-texto);
        box-shadow: 0 1px 2px rgba(18, 62, 115, .06);
        transition: border-color .15s, box-shadow .15s;
    }
    .seg-buscar input:focus {
        outline: none;
        border-color: var(--seg-azul);
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .15);
    }
    .seg-buscar .seg-limpiar {
        position: absolute;
        right: 12px;
        color: var(--seg-gris);
    }

    /* ===== filtros por situacion ===== */
    .seg-filtros {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(165px, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .seg-filtro {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #fff;
        border: 1px solid var(--seg-borde);
        border-radius: 12px;
        color: inherit;
        overflow: hidden;
        transition: box-shadow .15s, transform .15s, border-color .15s;
    }
    .seg-filtro::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--c);
    }
    .seg-filtro:hover,
    .seg-filtro:focus {
        text-decoration: none;
        color: inherit;
        box-shadow: 0 6px 16px rgba(18, 62, 115, .12);
        transform: translateY(-2px);
    }
    .seg-filtro.activa {
        border-color: var(--c);
        background: var(--c-suave);
        box-shadow: 0 0 0 1px var(--c) inset;
    }
    .seg-filtro-icono {
        flex: 0 0 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        color: var(--c);
        background: var(--c-suave);
    }
    .seg-filtro.activa .seg-filtro-icono {
        color: #fff;
        background: var(--c);
    }
    .seg-filtro b {
        display: block;
        font-size: 22px;
        line-height: 1.1;
        color: var(--seg-azul-oscuro);
    }
    .seg-filtro span {
        font-size: 12px;
        color: #4a5568;
    }

    /* ===== lista ===== */
    .seg-lista {
        background: #fff;
        border: 1px solid var(--seg-borde);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .08);
        overflow: hidden;
    }
    .seg-fila {
        display: grid;
        grid-template-columns: minmax(260px, 1.3fr) minmax(260px, 1.2fr) 170px 44px;
        gap: 18px;
        align-items: center;
        padding: 16px 20px 16px 22px;
        border-bottom: 1px solid #edf1f6;
        position: relative;
        transition: background .15s;
    }
    .seg-fila:last-child {
        border-bottom: 0;
    }
    .seg-fila::before {
        content: '';
        position: absolute;
        left: 0;
        top: 12px;
        bottom: 12px;
        width: 4px;
        border-radius: 0 4px 4px 0;
        background: var(--c);
    }
    .seg-fila:hover {
        background: #f8fafd;
    }
    .seg-cabecera {
        display: grid;
        grid-template-columns: minmax(260px, 1.3fr) minmax(260px, 1.2fr) 170px 44px;
        gap: 18px;
        padding: 10px 20px 10px 22px;
        background: #f5f8fb;
        border-bottom: 1px solid var(--seg-borde);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6b7686;
    }

    /* hoja de ruta */
    .seg-hr-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .seg-nur {
        font-size: 14px;
        font-weight: 700;
        color: var(--seg-azul);
    }
    .seg-nur:hover {
        color: var(--seg-azul-oscuro);
    }
    .seg-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        color: var(--c);
        background: var(--c-suave);
    }
    .seg-ref {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-size: 13px;
        line-height: 1.4;
        color: var(--seg-texto);
    }
    .seg-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 12px;
        margin-top: 5px;
        font-size: 11px;
        color: var(--seg-gris);
    }
    .seg-meta i {
        margin-right: 3px;
    }

    /* quien la tiene */
    .seg-persona {
        display: flex;
        align-items: center;
        gap: 11px;
    }
    .seg-persona + .seg-persona {
        margin-top: 10px;
    }
    .seg-avatar {
        flex: 0 0 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: var(--seg-azul-oscuro);
        background: var(--seg-azul-suave);
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px var(--seg-borde);
    }
    .seg-avatar.yo {
        background: var(--seg-amarillo);
    }
    .seg-persona-texto {
        min-width: 0;
    }
    .seg-nombre {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--seg-texto);
    }
    .seg-cargo {
        display: block;
        font-size: 11px;
        color: var(--seg-gris);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 360px;
    }
    .seg-estado {
        padding: 1px 7px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    .seg-estado.norecibido { color: var(--seg-demora); background: var(--seg-demora-suave); }
    .seg-estado.pendiente { color: var(--seg-azul); background: var(--seg-azul-suave); }
    .seg-estado.fin { color: #4a5568; background: #EDEFF2; }

    /* tiempo */
    .seg-tiempo + .seg-tiempo {
        margin-top: 10px;
    }
    .seg-tiempo-valor {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }
    .seg-tiempo-valor.ok { color: var(--seg-ok); background: var(--seg-ok-suave); }
    .seg-tiempo-valor.alerta { color: var(--seg-alerta); background: var(--seg-alerta-suave); }
    .seg-tiempo-valor.demora { color: var(--seg-demora); background: var(--seg-demora-suave); }
    .seg-tiempo-valor.fin { color: var(--seg-gris); background: #f1f4f8; font-weight: 600; }
    .seg-tiempo-valor .seg-pulso {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        animation: seg-pulso 1.6s ease-in-out infinite;
    }
    @keyframes seg-pulso {
        0%, 100% { opacity: 1; }
        50% { opacity: .25; }
    }
    .seg-barra {
        height: 4px;
        margin: 6px 0 4px;
        border-radius: 4px;
        background: #edf1f6;
        overflow: hidden;
        max-width: 150px;
    }
    .seg-barra span {
        display: block;
        height: 100%;
        border-radius: 4px;
    }
    .seg-barra .ok { background: var(--seg-ok); }
    .seg-barra .alerta { background: var(--seg-alerta); }
    .seg-barra .demora { background: var(--seg-demora); }
    .seg-desde {
        display: block;
        font-size: 11px;
        color: var(--seg-gris);
        white-space: nowrap;
    }

    /* accion */
    .seg-ver {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--seg-azul);
        background: var(--seg-azul-suave);
        transition: background .15s, color .15s;
    }
    .seg-ver:hover,
    .seg-ver:focus {
        color: #fff;
        background: var(--seg-azul);
        text-decoration: none;
    }

    .seg-vacio {
        padding: 56px 16px;
        text-align: center;
        color: var(--seg-gris);
    }
    .seg-vacio i {
        display: block;
        margin-bottom: 10px;
        font-size: 40px;
        color: #c5cfdb;
    }

    /* ===== pie ===== */
    .seg-pie {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 14px;
        font-size: 12px;
        color: #6b7686;
    }
    .seg-leyenda {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 14px;
    }
    .seg-leyenda span::before {
        content: '';
        display: inline-block;
        width: 9px;
        height: 9px;
        margin-right: 5px;
        border-radius: 50%;
        vertical-align: -1px;
    }
    .seg-leyenda .ok::before { background: var(--seg-ok); }
    .seg-leyenda .alerta::before { background: var(--seg-alerta); }
    .seg-leyenda .demora::before { background: var(--seg-demora); }
    .seg-pie .pagination {
        margin: 0;
    }
    .seg-pie .pagination > li > a {
        border-radius: 6px;
        margin: 0 2px;
    }

    /* ===== pantallas angostas ===== */
    @media (max-width: 991px) {
        .seg-cabecera {
            display: none;
        }
        .seg-fila {
            grid-template-columns: 1fr auto;
            gap: 12px;
        }
        .seg-fila > .seg-col-hr {
            grid-column: 1 / -1;
        }
        .seg-cargo {
            max-width: 70vw;
        }
    }
</style>

<div class="seg">
    <div class="seg-encabezado">
        <h2><i class="fa fa-random"></i>Seguimientos
            <small>Hojas de ruta que creaste: quién la tiene ahora y cuánto tiempo lleva</small>
        </h2>
        <form class="seg-buscar" method="get" action="/document/seguimientos">
            <?php if ($situacion !== ''): ?>
                <input type="hidden" name="situacion" value="<?php echo HTML::chars($situacion); ?>"/>
            <?php endif; ?>
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="<?php echo HTML::chars($q); ?>"
                   placeholder="Buscar hoja de ruta, cite o referencia" autocomplete="off"/>
            <?php if ($q !== ''): ?>
                <a class="seg-limpiar" href="<?php echo HTML::chars($enlace(array('q' => '', 'pagina' => 1))); ?>" title="Quitar búsqueda"><i class="fa fa-times-circle"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="seg-filtros">
        <a class="seg-filtro seg-todas <?php echo $situacion === '' ? 'activa' : ''; ?>" href="<?php echo HTML::chars($enlace(array('situacion' => '', 'pagina' => 1))); ?>">
            <div class="seg-filtro-icono"><i class="fa fa-random"></i></div>
            <div><b><?php echo number_format($total_todas, 0, ',', '.'); ?></b><span>Todas</span></div>
        </a>
        <?php foreach ($situaciones as $clave => $s): ?>
            <a class="seg-filtro <?php echo $s[2]; ?> <?php echo $situacion === $clave ? 'activa' : ''; ?>" href="<?php echo HTML::chars($enlace(array('situacion' => $clave, 'pagina' => 1))); ?>">
                <div class="seg-filtro-icono"><i class="fa <?php echo $s[1]; ?>"></i></div>
                <div><b><?php echo number_format(isset($resumen[$clave]) ? $resumen[$clave] : 0, 0, ',', '.'); ?></b><span><?php echo $s[0]; ?></span></div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="seg-lista">
        <?php if (!$hojas): ?>
            <div class="seg-vacio">
                <i class="fa fa-folder-open-o"></i>
                No hay hojas de ruta<?php echo $q !== '' ? ' que coincidan con «' . HTML::chars($q) . '»' : ''; ?>.
            </div>
        <?php else: ?>
            <div class="seg-cabecera">
                <div>Hoja de ruta</div>
                <div>¿Quién la tiene?</div>
                <div>Tiempo</div>
                <div></div>
            </div>
            <?php foreach ($hojas as $h): ?>
                <?php
                $creada = $fecha($h['fecha_creacion']);
                $sit = isset($situaciones[$h['situacion']]) ? $situaciones[$h['situacion']] : $situaciones['concluida'];
                // cada persona que la tiene: nombre, cargo/oficina, estado, desde cuando y si el tiempo sigue corriendo
                $personas = array();
                if ($h['situacion'] === 'sin_derivar' || !isset($poseedores[$h['nur']])) {
                    $personas[] = array(
                        'nombre' => $user->nombre, 'detalle' => 'En tu poder · aún no la derivaste', 'yo' => TRUE,
                        'estado' => NULL, 'desde' => $h['fecha_creacion'], 'corre' => TRUE,
                    );
                } else {
                    foreach ($poseedores[$h['nur']] as $p) {
                        $e = (int) $p['estado'];
                        $recibida = $p['fecha_recepcion'] && $e !== 1;
                        $personas[] = array(
                            'nombre' => $p['nombre_receptor'],
                            'detalle' => trim($p['cargo_receptor'] . ($p['a_oficina'] ? ' · ' . $p['a_oficina'] : ''), ' ·'),
                            'yo' => (int) $p['derivado_a'] === (int) $user->id,
                            'estado' => $e === 1 ? array('Sin recibir', 'norecibido') : ($e === 2 ? array('Pendiente', 'pendiente') : ($e === 10 ? array('Archivado (FIN)', 'fin') : NULL)),
                            'desde' => $recibida ? $p['fecha_recepcion'] : $p['fecha_emision'],
                            'corre' => in_array($e, array(1, 2), TRUE),
                        );
                    }
                }
                $trace = '/route/trace/?hr=' . urlencode($h['nur']);
                ?>
                <div class="seg-fila <?php echo $sit[2]; ?>">
                    <div class="seg-col-hr">
                        <div class="seg-hr-top">
                            <a class="seg-nur" href="<?php echo HTML::chars($trace); ?>"><?php echo HTML::chars($h['nur']); ?></a>
                            <span class="seg-chip"><i class="fa <?php echo $sit[1]; ?>"></i> <?php echo $sit[3]; ?></span>
                        </div>
                        <span class="seg-ref" title="<?php echo HTML::chars($h['referencia']); ?>"><?php echo HTML::chars($h['referencia']); ?></span>
                        <div class="seg-meta">
                            <span><i class="fa fa-file-text-o"></i><?php echo HTML::chars($h['cite_original'] ? $h['cite_original'] : $h['codigo']); ?></span>
                            <span><i class="fa fa-calendar-o"></i>Creada <?php echo $creada[0]; ?> <?php echo $creada[1]; ?></span>
                        </div>
                    </div>
                    <div>
                        <?php foreach ($personas as $p): ?>
                            <div class="seg-persona">
                                <div class="seg-avatar <?php echo $p['yo'] ? 'yo' : ''; ?>"><?php echo HTML::chars($iniciales($p['nombre'])); ?></div>
                                <div class="seg-persona-texto">
                                    <span class="seg-nombre">
                                        <?php echo HTML::chars($p['nombre']); ?>
                                        <?php if ($p['estado']): ?><span class="seg-estado <?php echo $p['estado'][1]; ?>"><?php echo $p['estado'][0]; ?></span><?php endif; ?>
                                    </span>
                                    <?php if ($p['detalle'] !== ''): ?><span class="seg-cargo" title="<?php echo HTML::chars($p['detalle']); ?>"><?php echo HTML::chars($p['detalle']); ?></span><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <?php foreach ($personas as $p): ?>
                            <?php
                            $f = $fecha($p['desde']);
                            $seg = max(0, time() - (int) strtotime($p['desde']));
                            $n = $nivel($seg);
                            ?>
                            <div class="seg-tiempo">
                                <?php if ($p['desde'] && $p['corre']): ?>
                                    <span class="seg-tiempo-valor <?php echo $n; ?>" title="Sigue contando"><span class="seg-pulso"></span><?php echo SeguimientoUtil::duracion($seg); ?></span>
                                    <div class="seg-barra"><span class="<?php echo $n; ?>" style="width: <?php echo min(100, max(4, round($seg / (10 * 86400) * 100))); ?>%"></span></div>
                                    <span class="seg-desde">desde <?php echo $f[0]; ?> · <?php echo $f[1]; ?></span>
                                <?php elseif ($p['desde']): ?>
                                    <span class="seg-tiempo-valor fin"><i class="fa fa-flag-checkered"></i> hace <?php echo SeguimientoUtil::duracion($seg); ?></span>
                                    <span class="seg-desde" style="margin-top: 5px;"><?php echo $f[0]; ?> · <?php echo $f[1]; ?></span>
                                <?php else: ?>
                                    <span class="seg-desde">—</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <a href="<?php echo HTML::chars($trace); ?>" class="seg-ver" title="Ver el seguimiento completo"><i class="fa fa-eye"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="seg-pie">
        <div class="seg-leyenda">
            <b><?php echo number_format($total, 0, ',', '.'); ?> hojas de ruta</b>
            <span class="ok">hasta 3 días</span>
            <span class="alerta">hasta 7 días</span>
            <span class="demora">más de 7 días</span>
        </div>
        <?php if ($paginas > 1): ?>
            <ul class="pagination pagination-sm">
                <li class="<?php echo $pagina <= 1 ? 'disabled' : ''; ?>"><a href="<?php echo HTML::chars($enlace(array('pagina' => max(1, $pagina - 1)))); ?>">&laquo;</a></li>
                <?php for ($i = max(1, $pagina - 3); $i <= min($paginas, $pagina + 3); $i++): ?>
                    <li class="<?php echo $i === $pagina ? 'active' : ''; ?>"><a href="<?php echo HTML::chars($enlace(array('pagina' => $i))); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <li class="<?php echo $pagina >= $paginas ? 'disabled' : ''; ?>"><a href="<?php echo HTML::chars($enlace(array('pagina' => min($paginas, $pagina + 1)))); ?>">&raquo;</a></li>
            </ul>
        <?php endif; ?>
    </div>
</div>
