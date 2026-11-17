<?php
// Mis seguimientos: hojas de ruta que el usuario creo o derivo (datos de Model_Hojasruta::sqlMisHojas)
$situaciones = array(
    // clave => (filtro, icono, clase, etiqueta en la tarjeta)
    'curso' => array('En curso', 'fa-share', 'ms-curso', 'En curso'),
    'sin_derivar' => array('Sin derivar', 'fa-inbox', 'ms-sin', 'Sin derivar'),
    'archivada' => array('Archivadas', 'fa-archive', 'ms-archivada', 'Archivada (FIN)'),
    'agrupada' => array('Agrupadas', 'fa-link', 'ms-agrupada', 'Agrupada'),
    'concluida' => array('Concluidas', 'fa-check', 'ms-concluida', 'Concluida'),
);
$total_todas = array_sum($resumen);
$hay_filtros = $f['q'] !== '' || $f['desde'] !== '' || $f['hasta'] !== '' || $f['sin_recibir'] || $f['situacion'] !== '';
$por_pagina = 25;
$desde_n = $total ? ($pagina - 1) * $por_pagina + 1 : 0;
$hasta_n = min($total, $pagina * $por_pagina);

$enlace = function ($cambios) use ($f, $pagina) {
    $p = array();
    foreach (array_merge($f, array('pagina' => $pagina), $cambios) as $k => $v) {
        // se omiten los filtros vacios y la pagina 1
        if ($v === '' || $v === NULL || $v === 0 || ($k === 'pagina' && (int) $v === 1)) {
            continue;
        }
        $p[$k] = $v;
    }
    return '/document/seguimientos' . ($p ? '?' . http_build_query($p) : '');
};
$fecha = function ($v) {
    $t = strtotime($v);
    return $t ? date('d/m/Y H:i', $t) : '—';
};
$hace = function ($v) {
    $dias = (int) floor((time() - strtotime($v)) / 86400);
    return $dias <= 0 ? 'hoy' : ($dias === 1 ? 'hace 1 día' : 'hace ' . $dias . ' días');
};
// color del tiempo: hasta 3 dias normal, hasta 7 atencion, mas de 7 demorado
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
$e = function ($s) {
    return HTML::chars($s);
};
?>
<style>
    .ms {
        --ms-azul: var(--correos-azul, #1A549A);
        --ms-azul-osc: var(--correos-azul-oscuro, #123E73);
        --ms-azul-suave: var(--correos-azul-suave, #EAF1F9);
        --ms-amarillo: var(--correos-amarillo, #FECB34);
        --ms-borde: var(--correos-borde, #DCE3EC);
        --ms-texto: #1f2d3d;
        --ms-gris: #6b7686;
        --ms-gris-claro: #F5F7FA;
        --ms-ok: #2f855a;
        --ms-ok-suave: #E6F4EA;
        --ms-alerta: #b7791f;
        --ms-alerta-suave: #FEF5E1;
        --ms-demora: #c53030;
        --ms-demora-suave: #FDECEC;
        color: var(--ms-texto);
    }
    .ms a:hover { text-decoration: none; }
    .ms-curso { --c: #1A549A; --c-suave: #EAF1F9; }
    .ms-sin { --c: #b7791f; --c-suave: #FEF5E1; }
    .ms-archivada { --c: #4a5568; --c-suave: #EDEFF2; }
    .ms-agrupada { --c: #6b46c1; --c-suave: #F1ECF9; }
    .ms-concluida { --c: #2f855a; --c-suave: #E6F4EA; }
    .ms-todas { --c: #123E73; --c-suave: #EAF1F9; }

    /* ===== cabecera ===== */
    .ms-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px 20px;
        padding: 18px 22px;
        margin-bottom: 16px;
        border-radius: 14px;
        background: linear-gradient(135deg, #123E73 0%, #1A549A 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(18, 62, 115, .18);
    }
    .ms-cab h2 { margin: 0; font-size: 22px; font-weight: 700; color: #fff; }
    .ms-cab h2 i { color: var(--ms-amarillo); margin-right: 8px; }
    .ms-cab p { margin: 4px 0 0; font-size: 13px; color: rgba(255, 255, 255, .8); }
    .ms-buscar { position: relative; flex: 0 1 380px; min-width: 220px; }
    .ms-buscar > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--ms-gris); }
    .ms-buscar input {
        width: 100%;
        height: 42px;
        padding: 0 40px 0 38px;
        border: 0;
        border-radius: 21px;
        background: #fff;
        font-size: 13px;
        color: var(--ms-texto);
    }
    .ms-buscar input:focus { outline: 3px solid rgba(254, 203, 52, .7); }
    .ms-buscar .ms-x { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--ms-gris); }

    /* ===== resumen por situacion ===== */
    .ms-resumen {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }
    .ms-kpi {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid var(--ms-borde);
        background: #fff;
        color: inherit;
        transition: box-shadow .15s, border-color .15s;
    }
    .ms-kpi:hover { color: inherit; box-shadow: 0 4px 12px rgba(18, 62, 115, .12); }
    .ms-kpi i {
        flex: 0 0 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: var(--c);
        background: var(--c-suave);
    }
    .ms-kpi b { display: block; font-size: 20px; line-height: 1.1; color: var(--ms-azul-osc); }
    .ms-kpi span { font-size: 12px; color: var(--ms-gris); }
    .ms-kpi.activa { border-color: var(--c); background: var(--c-suave); box-shadow: inset 0 0 0 1px var(--c); }
    .ms-kpi.activa i { color: #fff; background: var(--c); }
    .ms-kpi.vacia { opacity: .55; }

    /* ===== barra de filtros ===== */
    .ms-barra {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 16px;
        padding: 10px 14px;
        margin-bottom: 12px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--ms-borde);
    }
    .ms-barra-tit { font-size: 11px; font-weight: 700; color: var(--ms-gris); text-transform: uppercase; letter-spacing: .5px; }
    .ms-seg { display: inline-flex; padding: 3px; border-radius: 10px; background: var(--ms-gris-claro); }
    .ms-seg a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5568;
        white-space: nowrap;
    }
    .ms-seg a b { font-size: 11px; color: var(--ms-gris); }
    .ms-seg a.activo { background: #fff; color: var(--ms-azul); box-shadow: 0 1px 3px rgba(18, 62, 115, .18); }
    .ms-seg a.activo b { color: var(--ms-azul); }
    .ms-fechas { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: var(--ms-gris); }
    .ms-fechas input {
        height: 32px;
        padding: 3px 8px;
        border: 1px solid var(--ms-borde);
        border-radius: 8px;
        color: var(--ms-texto);
    }
    .ms-fechas button {
        height: 32px;
        padding: 0 12px;
        border: 0;
        border-radius: 8px;
        background: var(--ms-azul);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
    }
    .ms-cuenta { margin-left: auto; font-size: 12.5px; color: var(--ms-gris); white-space: nowrap; }
    .ms-cuenta b { color: var(--ms-texto); }

    /* filtros activos */
    .ms-activos {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        font-size: 12px;
        color: var(--ms-gris);
    }
    .ms-chip-filtro {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 14px;
        background: var(--ms-azul-suave);
        color: var(--ms-azul);
        font-weight: 600;
    }
    .ms-chip-filtro:hover { background: #dbe7f5; color: var(--ms-azul-osc); }
    .ms-quitar { margin-left: 4px; font-weight: 600; color: var(--ms-demora); }
    .ms-quitar:hover { color: #a32626; }

    /* aviso de envios sin recibir */
    .ms-aviso-global {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 12px 16px;
        margin-bottom: 14px;
        border-radius: 12px;
        background: var(--ms-demora-suave);
        border: 1px solid #F3C9C9;
        color: #8a2a2a;
        font-size: 13px;
    }
    .ms-aviso-global > i { font-size: 18px; color: var(--ms-demora); }
    .ms-aviso-global span { flex: 1 1 260px; }
    .ms-aviso-global a {
        padding: 6px 14px;
        border-radius: 8px;
        background: var(--ms-demora);
        color: #fff;
        font-weight: 600;
        white-space: nowrap;
    }
    .ms-aviso-global a:hover { color: #fff; background: #a32626; }

    /* ===== tarjeta de hoja de ruta ===== */
    .ms-lista { display: flex; flex-direction: column; gap: 12px; }
    .ms-hoja {
        position: relative;
        padding: 16px 18px 16px 22px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid var(--ms-borde);
        box-shadow: 0 1px 3px rgba(18, 62, 115, .06);
        transition: box-shadow .15s;
    }
    .ms-hoja:hover { box-shadow: 0 6px 18px rgba(18, 62, 115, .10); }
    .ms-hoja::before {
        content: '';
        position: absolute;
        left: 0;
        top: 14px;
        bottom: 14px;
        width: 5px;
        border-radius: 0 5px 5px 0;
        background: var(--c);
    }
    .ms-hoja-cab { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 10px; }
    .ms-nur { font-size: 16px; font-weight: 800; color: var(--ms-azul); letter-spacing: .2px; }
    .ms-nur:hover { color: var(--ms-azul-osc); }
    .ms-sit {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--c);
        background: var(--c-suave);
    }
    .ms-rol {
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #5b6573;
        background: var(--ms-gris-claro);
        border: 1px solid #E6EAF0;
    }
    .ms-acciones { display: flex; flex-wrap: wrap; gap: 6px; margin-left: auto; }
    .ms-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        white-space: nowrap;
        border: 1px solid var(--ms-borde);
        background: #fff;
        color: var(--ms-azul);
    }
    .ms-btn:hover { background: var(--ms-azul-suave); color: var(--ms-azul-osc); }
    .ms-btn.primario { background: var(--ms-azul); border-color: var(--ms-azul); color: #fff; }
    .ms-btn.primario:hover { background: var(--ms-azul-osc); color: #fff; }
    .ms-btn.peligro { height: 28px; border-color: var(--ms-demora); color: var(--ms-demora); font-size: 12px; }
    .ms-btn.peligro:hover { background: var(--ms-demora); color: #fff; }
    .ms-ref {
        display: block;
        margin: 8px 0 4px;
        font-size: 14.5px;
        font-weight: 600;
        line-height: 1.4;
        color: var(--ms-texto);
    }
    .ms-ref:hover { color: var(--ms-azul); }
    .ms-meta { display: flex; flex-wrap: wrap; gap: 4px 16px; font-size: 12px; color: var(--ms-gris); }
    .ms-meta i { margin-right: 4px; }

    /* recorrido: tu envio -> ahora la tiene */
    .ms-flujo {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 36px minmax(0, 1.15fr);
        margin-top: 14px;
    }
    .ms-flujo.solo { grid-template-columns: minmax(0, 1fr); }
    .ms-paso {
        min-width: 0;
        padding: 12px 14px;
        border-radius: 12px;
        background: var(--ms-gris-claro);
        border: 1px solid #E9EDF2;
    }
    .ms-paso.ahora { background: #fff; border-color: var(--ms-borde); }
    .ms-paso-tit {
        display: block;
        margin-bottom: 8px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: var(--ms-gris);
    }
    .ms-flecha { display: flex; align-items: center; justify-content: center; color: #a9b4c2; font-size: 18px; }
    .ms-persona { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .ms-persona + .ms-persona { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #E3E8EE; }
    .ms-avatar {
        flex: 0 0 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--ms-azul-osc);
        background: var(--ms-azul-suave);
    }
    .ms-avatar.yo { background: var(--ms-amarillo); }
    .ms-persona-txt { flex: 1 1 auto; min-width: 0; }
    .ms-nombre { display: block; font-size: 13px; font-weight: 700; line-height: 1.3; }
    .ms-nombre small { font-weight: 500; color: var(--ms-gris); }
    .ms-sub { display: block; font-size: 11.5px; color: var(--ms-gris); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ms-estado {
        display: inline-block;
        margin-top: 4px;
        padding: 1px 8px;
        border-radius: 5px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }
    .ms-estado.norecibido { color: var(--ms-demora); background: var(--ms-demora-suave); }
    .ms-estado.pendiente { color: var(--ms-azul); background: var(--ms-azul-suave); }
    .ms-estado.recibido { color: var(--ms-ok); background: var(--ms-ok-suave); }
    .ms-estado.fin { color: #4a5568; background: #E8EBF0; }
    .ms-proveido {
        display: block;
        margin-top: 8px;
        padding-left: 10px;
        border-left: 3px solid #D5DCE6;
        font-size: 12px;
        font-style: italic;
        color: #4a5568;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ms-tiempo { flex: 0 0 auto; text-align: right; }
    .ms-tiempo-valor {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }
    .ms-tiempo-valor.ok { color: var(--ms-ok); background: var(--ms-ok-suave); }
    .ms-tiempo-valor.alerta { color: var(--ms-alerta); background: var(--ms-alerta-suave); }
    .ms-tiempo-valor.demora { color: var(--ms-demora); background: var(--ms-demora-suave); }
    .ms-tiempo-valor.fin { color: var(--ms-gris); background: #EEF1F5; font-weight: 600; }
    .ms-pulso { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: ms-pulso 1.6s ease-in-out infinite; }
    @keyframes ms-pulso { 0%, 100% { opacity: 1; } 50% { opacity: .25; } }
    .ms-desde { display: block; margin-top: 3px; font-size: 11px; color: var(--ms-gris); white-space: nowrap; }
    .ms-cancelar-linea { margin-top: 10px; }

    /* otros envios sin recibir de la misma hoja */
    .ms-otros {
        margin-top: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px dashed #F0C2C2;
        background: #FFFAFA;
        font-size: 12.5px;
    }
    .ms-otros-fila { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
    .ms-otros-fila + .ms-otros-fila { margin-top: 8px; }
    .ms-otros-fila span { flex: 1 1 240px; }
    .ms-otros-fila span i { color: var(--ms-demora); margin-right: 4px; }

    /* ===== vacio y pie ===== */
    .ms-vacio {
        padding: 56px 16px;
        border-radius: 14px;
        border: 1px dashed var(--ms-borde);
        background: #fff;
        text-align: center;
        color: var(--ms-gris);
    }
    .ms-vacio i { display: block; margin-bottom: 10px; font-size: 40px; color: #c5cfdb; }
    .ms-vacio h4 { margin: 0 0 6px; color: var(--ms-texto); font-weight: 700; }
    .ms-pie {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 16px;
        font-size: 12px;
        color: var(--ms-gris);
    }
    .ms-leyenda { display: flex; flex-wrap: wrap; gap: 6px 14px; align-items: center; }
    .ms-leyenda span::before {
        content: '';
        display: inline-block;
        width: 9px;
        height: 9px;
        margin-right: 5px;
        border-radius: 50%;
        vertical-align: -1px;
    }
    .ms-leyenda .ok::before { background: var(--ms-ok); }
    .ms-leyenda .alerta::before { background: var(--ms-alerta); }
    .ms-leyenda .demora::before { background: var(--ms-demora); }
    .ms-pie .pagination { margin: 0; }
    .ms-pie .pagination > li > a { border-radius: 6px; margin: 0 2px; }
    .ms-pie .pagination > .active > a { background: var(--ms-azul); border-color: var(--ms-azul); }

    /* ===== pantallas angostas ===== */
    @media (max-width: 1199px) {
        .ms-resumen { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .ms-cab { padding: 16px; }
        .ms-buscar { flex: 1 1 100%; }
        .ms-resumen { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ms-cuenta { margin-left: 0; }
        .ms-acciones { margin-left: 0; width: 100%; }
        .ms-flujo { grid-template-columns: minmax(0, 1fr); }
        .ms-flecha { height: 28px; transform: rotate(90deg); }
        .ms-hoja { padding: 14px 14px 14px 18px; }
    }
</style>

<div class="ms">
    <!-- cabecera y busqueda -->
    <div class="ms-cab">
        <div>
            <h2><i class="fa fa-random"></i>Mis seguimientos</h2>
            <p>Dónde están las hojas de ruta que creaste o derivaste, quién las tiene y desde cuándo.</p>
        </div>
        <form class="ms-buscar" method="get" action="/document/seguimientos" role="search">
            <?php foreach (array('alcance', 'situacion', 'desde', 'hasta') as $k): ?>
                <?php if ($f[$k] !== ''): ?><input type="hidden" name="<?php echo $k; ?>" value="<?php echo $e($f[$k]); ?>"/><?php endif; ?>
            <?php endforeach; ?>
            <?php if ($f['sin_recibir']): ?><input type="hidden" name="sin_recibir" value="1"/><?php endif; ?>
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="<?php echo $e($f['q']); ?>" aria-label="Buscar"
                   placeholder="Buscar hoja de ruta, cite, referencia, destinatario…" autocomplete="off"/>
            <?php if ($f['q'] !== ''): ?>
                <a class="ms-x" href="<?php echo $e($enlace(array('q' => '', 'pagina' => 1))); ?>" title="Quitar búsqueda"><i class="fa fa-times-circle"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <!-- resumen por situacion (cada tarjeta filtra) -->
    <div class="ms-resumen">
        <a class="ms-kpi ms-todas <?php echo $f['situacion'] === '' ? 'activa' : ''; ?>" href="<?php echo $e($enlace(array('situacion' => '', 'pagina' => 1))); ?>">
            <i class="fa fa-th-large"></i>
            <div><b><?php echo number_format($total_todas, 0, ',', '.'); ?></b><span>Todas</span></div>
        </a>
        <?php foreach ($situaciones as $clave => $s): $n = isset($resumen[$clave]) ? $resumen[$clave] : 0; ?>
            <a class="ms-kpi <?php echo $s[2]; ?> <?php echo $f['situacion'] === $clave ? 'activa' : ''; ?> <?php echo $n ? '' : 'vacia'; ?>"
               href="<?php echo $e($enlace(array('situacion' => $clave, 'pagina' => 1))); ?>">
                <i class="fa <?php echo $s[1]; ?>"></i>
                <div><b><?php echo number_format($n, 0, ',', '.'); ?></b><span><?php echo $s[0]; ?></span></div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- alcance, fechas y cuenta -->
    <div class="ms-barra">
        <span class="ms-barra-tit">Mostrar</span>
        <nav class="ms-seg" aria-label="Qué hojas de ruta mostrar">
            <?php foreach (array('' => 'Todas', 'creadas' => 'Que creé', 'derivadas' => 'Que derivé') as $clave => $texto): ?>
                <a class="<?php echo $f['alcance'] === $clave ? 'activo' : ''; ?>" href="<?php echo $e($enlace(array('alcance' => $clave, 'situacion' => '', 'pagina' => 1))); ?>">
                    <?php echo $texto; ?> <b><?php echo number_format($alcances[$clave], 0, ',', '.'); ?></b></a>
            <?php endforeach; ?>
        </nav>
        <form class="ms-fechas" method="get" action="/document/seguimientos" title="Por la fecha de la última actividad: creación o tu último envío">
            <?php foreach (array('alcance', 'situacion', 'q') as $k): ?>
                <?php if ($f[$k] !== ''): ?><input type="hidden" name="<?php echo $k; ?>" value="<?php echo $e($f[$k]); ?>"/><?php endif; ?>
            <?php endforeach; ?>
            <?php if ($f['sin_recibir']): ?><input type="hidden" name="sin_recibir" value="1"/><?php endif; ?>
            <i class="fa fa-calendar"></i>
            <input type="date" name="desde" value="<?php echo $e($f['desde']); ?>" aria-label="Desde"/>
            <span>a</span>
            <input type="date" name="hasta" value="<?php echo $e($f['hasta']); ?>" aria-label="Hasta"/>
            <button type="submit">Aplicar</button>
        </form>
        <span class="ms-cuenta"><b><?php echo number_format($total, 0, ',', '.'); ?></b> hoja<?php echo $total == 1 ? '' : 's'; ?> de ruta<?php echo $paginas > 1 ? ' · página ' . $pagina . ' de ' . $paginas : ''; ?></span>
    </div>

    <!-- filtros activos -->
    <?php if ($hay_filtros): ?>
        <div class="ms-activos">
            <span>Filtrando por:</span>
            <?php if ($f['q'] !== ''): ?>
                <a class="ms-chip-filtro" href="<?php echo $e($enlace(array('q' => '', 'pagina' => 1))); ?>">«<?php echo $e($f['q']); ?>» <i class="fa fa-times"></i></a>
            <?php endif; ?>
            <?php if ($f['situacion'] !== '' && isset($situaciones[$f['situacion']])): ?>
                <a class="ms-chip-filtro" href="<?php echo $e($enlace(array('situacion' => '', 'pagina' => 1))); ?>"><?php echo $situaciones[$f['situacion']][0]; ?> <i class="fa fa-times"></i></a>
            <?php endif; ?>
            <?php if ($f['sin_recibir']): ?>
                <a class="ms-chip-filtro" href="<?php echo $e($enlace(array('sin_recibir' => 0, 'pagina' => 1))); ?>">Envíos sin recibir <i class="fa fa-times"></i></a>
            <?php endif; ?>
            <?php if ($f['desde'] !== '' || $f['hasta'] !== ''): ?>
                <a class="ms-chip-filtro" href="<?php echo $e($enlace(array('desde' => '', 'hasta' => '', 'pagina' => 1))); ?>">
                    <?php echo $f['desde'] !== '' ? date('d/m/Y', strtotime($f['desde'])) : '…'; ?> – <?php echo $f['hasta'] !== '' ? date('d/m/Y', strtotime($f['hasta'])) : 'hoy'; ?> <i class="fa fa-times"></i></a>
            <?php endif; ?>
            <a class="ms-quitar" href="<?php echo $e($enlace(array('q' => '', 'desde' => '', 'hasta' => '', 'situacion' => '', 'sin_recibir' => 0, 'pagina' => 1))); ?>">Quitar todos</a>
        </div>
    <?php endif; ?>

    <!-- aviso: envios tuyos que aun no recibieron -->
    <?php if ($sin_recibir > 0 && !$f['sin_recibir']): ?>
        <div class="ms-aviso-global">
            <i class="fa fa-paper-plane-o"></i>
            <span><b><?php echo number_format($sin_recibir, 0, ',', '.'); ?> hoja<?php echo $sin_recibir == 1 ? '' : 's'; ?> de ruta</b> que enviaste todavía no <?php echo $sin_recibir == 1 ? 'fue recibida' : 'fueron recibidas'; ?>. Mientras no la reciban, puedes cancelar el envío.</span>
            <a href="<?php echo $e($enlace(array('sin_recibir' => 1, 'situacion' => '', 'pagina' => 1))); ?>">Ver solo esas</a>
        </div>
    <?php endif; ?>

    <!-- lista -->
    <?php if (!$hojas): ?>
        <div class="ms-vacio">
            <i class="fa fa-folder-open-o"></i>
            <?php if ($hay_filtros || $f['alcance'] !== ''): ?>
                <h4>Sin resultados</h4>
                Ninguna hoja de ruta coincide con los filtros. <a href="/document/seguimientos">Ver todas</a>
            <?php else: ?>
                <h4>Aún no tienes seguimientos</h4>
                Las hojas de ruta que crees o derives aparecerán aquí para que sepas dónde están.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="ms-lista">
            <?php foreach ($hojas as $h): ?>
                <?php
                $sit = isset($situaciones[$h['situacion']]) ? $situaciones[$h['situacion']] : $situaciones['concluida'];
                $trace = '/route/trace/?hr=' . urlencode($h['nur']);
                $sin_derivar = $h['situacion'] === 'sin_derivar' || !isset($poseedores[$h['nur']]);

                // quien la tiene ahora (o con quien termino)
                $personas = array();
                if ($sin_derivar) {
                    $personas[] = array('nombre' => $user->nombre, 'detalle' => 'Aún no la derivaste', 'yo' => TRUE,
                        'estado' => array('En tu poder', 'pendiente'), 'desde' => $h['fecha_creacion'], 'corre' => TRUE);
                } else {
                    foreach ($poseedores[$h['nur']] as $p) {
                        $est = (int) $p['estado'];
                        $recibida = $p['fecha_recepcion'] && $est !== 1;
                        $personas[] = array(
                            'nombre' => $p['nombre_receptor'],
                            'detalle' => trim($p['cargo_receptor'] . ($p['a_oficina'] ? ' · ' . $p['a_oficina'] : ''), ' ·'),
                            'yo' => (int) $p['derivado_a'] === (int) $user->id,
                            'estado' => $est === 1 ? array('Sin recibir', 'norecibido') : ($est === 2 ? array('Pendiente', 'pendiente') : ($est === 10 ? array('Archivado', 'fin') : ($est === 6 ? array('Agrupado', 'fin') : NULL))),
                            'desde' => $recibida ? $p['fecha_recepcion'] : $p['fecha_emision'],
                            'corre' => in_array($est, array(1, 2), TRUE),
                        );
                    }
                }
                $alguno_corre = FALSE;
                foreach ($personas as $p) {
                    $alguno_corre = $alguno_corre || $p['corre'];
                }

                // envios tuyos sin recibir: el ultimo se cancela desde "Tu último envío"; aqui van los demas
                $otros_envios = array();
                foreach (isset($envios[$h['nur']]) ? $envios[$h['nur']] : array() as $env) {
                    if ((int) $env['id'] !== (int) $h['mi_id']) {
                        $otros_envios[] = $env;
                    }
                }
                $ultimo_sin_recibir = $h['mi_id'] && (int) $h['mi_estado'] === 1;
                ?>
                <article class="ms-hoja <?php echo $sit[2]; ?>">
                    <div class="ms-hoja-cab">
                        <a class="ms-nur" href="<?php echo $e($trace); ?>"><?php echo $e($h['nur']); ?></a>
                        <span class="ms-sit"><i class="fa <?php echo $sit[1]; ?>"></i> <?php echo $sit[3]; ?></span>
                        <?php if ((int) $h['creada']): ?><span class="ms-rol">La creaste</span><?php endif; ?>
                        <?php if ((int) $h['envios']): ?><span class="ms-rol">La derivaste<?php echo (int) $h['envios'] > 1 ? ' ' . (int) $h['envios'] . ' veces' : ''; ?></span><?php endif; ?>
                        <div class="ms-acciones">
                            <a class="ms-btn primario" href="<?php echo $e($trace); ?>"><i class="fa fa-eye"></i> Ver seguimiento</a>
                        </div>
                    </div>

                    <a class="ms-ref" href="<?php echo $e($trace); ?>"><?php echo $e($h['referencia'] != '' ? $h['referencia'] : 'Sin referencia'); ?></a>
                    <div class="ms-meta">
                        <span><i class="fa fa-file-text-o"></i><?php echo $e($h['cite_original'] ? $h['cite_original'] : $h['codigo']); ?></span>
                        <?php if ($h['fecha_creacion']): ?><span><i class="fa fa-calendar-o"></i>Creada el <?php echo $fecha($h['fecha_creacion']); ?></span><?php endif; ?>
                    </div>

                    <?php $solo_ahora = $sin_derivar && !$h['mi_id']; // sin derivar: basta la caja "En tu poder" ?>
                    <div class="ms-flujo <?php echo $solo_ahora ? 'solo' : ''; ?>">
                        <?php if (!$solo_ahora): ?>
                        <!-- lo que hiciste tu -->
                        <div class="ms-paso">
                            <?php if ($h['mi_id']): ?>
                                <span class="ms-paso-tit"><i class="fa fa-paper-plane-o"></i> Tu último envío · <?php echo $fecha($h['mi_fecha']); ?></span>
                                <div class="ms-persona">
                                    <div class="ms-avatar"><?php echo $e($iniciales($h['mi_para'])); ?></div>
                                    <div class="ms-persona-txt">
                                        <span class="ms-nombre"><?php echo $e($h['mi_para']); ?><?php echo (int) $h['mi_oficial'] ? '' : ' <small>(copia)</small>'; ?></span>
                                        <?php $mi_detalle = trim($h['mi_cargo'] . ($h['mi_oficina'] ? ' · ' . $h['mi_oficina'] : ''), ' ·'); ?>
                                        <?php if ($mi_detalle !== ''): ?><span class="ms-sub" title="<?php echo $e($mi_detalle); ?>"><?php echo $e($mi_detalle); ?></span><?php endif; ?>
                                        <?php if ($ultimo_sin_recibir): ?>
                                            <span class="ms-estado norecibido">Sin recibir · <?php echo $hace($h['mi_fecha']); ?></span>
                                        <?php elseif ($h['mi_recepcion']): ?>
                                            <span class="ms-estado recibido"><i class="fa fa-check"></i> Recibido el <?php echo $fecha($h['mi_recepcion']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (trim((string) $h['mi_proveido']) !== ''): ?>
                                    <span class="ms-proveido" title="<?php echo $e($h['mi_proveido']); ?>"><?php echo $e($h['mi_proveido']); ?></span>
                                <?php endif; ?>
                                <?php if ($ultimo_sin_recibir): ?>
                                    <div class="ms-cancelar-linea">
                                        <a class="ms-btn peligro ms-cancelar" href="/bandeja/cancel/?id=<?php echo (int) $h['mi_id']; ?>"
                                           data-nur="<?php echo $e($h['nur']); ?>" data-para="<?php echo $e($h['mi_para']); ?>"><i class="fa fa-undo"></i> Cancelar envío</a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="ms-paso-tit"><i class="fa fa-pencil"></i> La creaste</span>
                                <div class="ms-persona">
                                    <div class="ms-avatar yo"><?php echo $e($iniciales($user->nombre)); ?></div>
                                    <div class="ms-persona-txt">
                                        <span class="ms-nombre">Tú</span>
                                        <span class="ms-sub"><?php echo $sin_derivar ? 'Todavía no la enviaste a nadie' : 'No la derivaste tú'; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="ms-flecha" aria-hidden="true"><i class="fa fa-long-arrow-right"></i></div>
                        <?php endif; ?>

                        <!-- donde esta ahora -->
                        <div class="ms-paso ahora">
                            <span class="ms-paso-tit"><i class="fa <?php echo $solo_ahora ? 'fa-inbox' : ($alguno_corre ? 'fa-map-marker' : 'fa-flag-checkered'); ?>"></i> <?php echo $solo_ahora ? 'La creaste y sigue en tu poder' : ($alguno_corre ? 'Ahora la tiene' : 'Terminó con'); ?></span>
                            <?php foreach ($personas as $p): ?>
                                <?php $seg = $p['desde'] ? max(0, time() - (int) strtotime($p['desde'])) : 0; ?>
                                <div class="ms-persona">
                                    <div class="ms-avatar <?php echo $p['yo'] ? 'yo' : ''; ?>"><?php echo $e($iniciales($p['nombre'])); ?></div>
                                    <div class="ms-persona-txt">
                                        <span class="ms-nombre"><?php echo $e($p['nombre']); ?><?php echo $p['yo'] && !$sin_derivar ? ' <small>(tú)</small>' : ''; ?></span>
                                        <?php if ($p['detalle'] !== ''): ?><span class="ms-sub" title="<?php echo $e($p['detalle']); ?>"><?php echo $e($p['detalle']); ?></span><?php endif; ?>
                                        <?php if ($p['estado']): ?><span class="ms-estado <?php echo $p['estado'][1]; ?>"><?php echo $p['estado'][0]; ?></span><?php endif; ?>
                                    </div>
                                    <?php if ($p['desde']): ?>
                                        <div class="ms-tiempo">
                                            <?php if ($p['corre']): ?>
                                                <span class="ms-tiempo-valor <?php echo $nivel($seg); ?>" title="Tiempo que la tiene (sigue contando)"><span class="ms-pulso"></span><?php echo SeguimientoUtil::duracion($seg); ?></span>
                                            <?php else: ?>
                                                <span class="ms-tiempo-valor fin"><i class="fa fa-flag-checkered"></i> hace <?php echo SeguimientoUtil::duracion($seg); ?></span>
                                            <?php endif; ?>
                                            <span class="ms-desde"><?php echo $p['corre'] ? 'desde' : 'el'; ?> <?php echo $fecha($p['desde']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($otros_envios): ?>
                        <div class="ms-otros">
                            <?php foreach ($otros_envios as $env): ?>
                                <div class="ms-otros-fila">
                                    <span><i class="fa fa-paper-plane-o"></i>
                                        También enviaste a <b><?php echo $e($env['nombre_receptor']); ?></b><?php echo (int) $env['oficial'] ? '' : ' (copia)'; ?>
                                        el <?php echo $fecha($env['fecha_emision']); ?> y aún no la recibe.</span>
                                    <a class="ms-btn peligro ms-cancelar" href="/bandeja/cancel/?id=<?php echo (int) $env['id']; ?>"
                                       data-nur="<?php echo $e($h['nur']); ?>" data-para="<?php echo $e($env['nombre_receptor']); ?>"><i class="fa fa-undo"></i> Cancelar envío</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- pie: leyenda y paginas -->
    <div class="ms-pie">
        <div class="ms-leyenda">
            <?php if ($total): ?><b>Mostrando <?php echo $desde_n; ?>–<?php echo $hasta_n; ?> de <?php echo number_format($total, 0, ',', '.'); ?></b><?php endif; ?>
            <span class="ok">hasta 3 días</span>
            <span class="alerta">hasta 7 días</span>
            <span class="demora">más de 7 días</span>
        </div>
        <?php if ($paginas > 1): ?>
            <ul class="pagination pagination-sm">
                <li class="<?php echo $pagina <= 1 ? 'disabled' : ''; ?>"><a href="<?php echo $e($enlace(array('pagina' => max(1, $pagina - 1)))); ?>">&laquo; Anterior</a></li>
                <?php for ($i = max(1, $pagina - 2); $i <= min($paginas, $pagina + 2); $i++): ?>
                    <li class="<?php echo $i === $pagina ? 'active' : ''; ?>"><a href="<?php echo $e($enlace(array('pagina' => $i))); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <li class="<?php echo $pagina >= $paginas ? 'disabled' : ''; ?>"><a href="<?php echo $e($enlace(array('pagina' => min($paginas, $pagina + 1)))); ?>">Siguiente &raquo;</a></li>
            </ul>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click', '.ms-cancelar', function () {
        return confirm('¿Cancelar el envío de la hoja de ruta ' + $(this).attr('data-nur') + ' a ' + $(this).attr('data-para')
            + '?\n\nSolo es posible mientras no la haya recibido. La hoja vuelve a tu bandeja.');
    });
    $(document).on('click', '.ms-pie .pagination .disabled a', function () {
        return false;
    });
</script>
