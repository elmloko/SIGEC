<?php
$M = 'Model_Indicadores';
// modo "propio": el usuario ve su propio tablero (sin enlaces a otras personas ni a reportes de administrador)
$propio = isset($modo) && $modo === 'propio';
?>
<div class="col-md-12 ind">
    <?php echo View::factory('reportes/indicadores/_cabecera')->set('f', $f)->set('d', $d)->set('pagina', $pagina)->set('modo', $propio ? 'propio' : 'admin'); ?>

<?php if (empty($d['perfil'])): ?>
    <section class="ind-card ind-vacio-grande">
        <i class="fa fa-user-circle-o" aria-hidden="true"></i>
        <h3>Elija un funcionario</h3>
        <p>Seleccione a la persona en el filtro de arriba para ver su tablero: lo que recibe y atiende, su cumplimiento de plazos comparado con su oficina, lo que tiene pendiente y con quién trabaja más.</p>
        <p>También puede llegar aquí desde <b>Desempeño</b>, eligiendo una oficina y haciendo clic en el nombre de un funcionario.</p>
    </section>
</div>
<?php return; endif; ?>
<?php
$p = $d['perfil'];
$v = $d['volumen'];
$e = $d['enviados'];
$r = $d['rezago'];
$t = $d['tiempos'];
$c = $d['cumplimiento'];
$o = $d['oficina'];
$tasa = $v['movimientos'] > 0 ? round(100 * $v['atendidos'] / $v['movimientos'], 1) : NULL;
$iniciales = '';
foreach (array_slice(preg_split('/\s+/', trim($p['nombre'])), 0, 2) as $parte) {
    $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1));
}
$pct = function ($n) { return $n === NULL ? '—' : number_format($n, 1, ',', '.') . '%'; };
// compara con su oficina; $menor_es_mejor para tiempos
$comparar = function ($propio, $oficina, $formato, $menor_es_mejor) {
    if ($propio === NULL || $oficina === NULL) {
        return '';
    }
    $mejor = $menor_es_mejor ? $propio <= $oficina : $propio >= $oficina;
    return '<span class="ind-comparacion ' . ($mejor ? 'ind-verde' : 'ind-rojo') . '">' . ($mejor ? '▲ mejor' : '▼ peor') . ' que su oficina (' . $formato($oficina) . ')</span>';
};
$meses_cortos = array('01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic');
$etiquetas_mes = array_map(function ($m) use ($meses_cortos) {
    return $meses_cortos[substr($m['mes'], 5, 2)] . ' ' . substr($m['mes'], 2, 2);
}, $d['tendencia']);
$max_remitente = $d['contrapartes']['remitentes'] ? max(array_map(function ($x) { return $x['total']; }, $d['contrapartes']['remitentes'])) : 1;
$max_destinatario = $d['contrapartes']['destinatarios'] ? max(array_map(function ($x) { return $x['total']; }, $d['contrapartes']['destinatarios'])) : 1;
$enlace_persona = $propio ? NULL : function ($id) use ($f) {
    return URL::site('reports/persona') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'usuario' => $id));
};
?>
    <section class="ind-perfil">
        <div class="ind-avatar" aria-hidden="true"><?php echo HTML::chars($iniciales); ?></div>
        <div class="ind-perfil-datos">
            <h2><?php echo HTML::chars($p['nombre']); ?>
                <?php if (!$p['habilitado']): ?><span class="ind-pastilla ind-gris">Deshabilitado</span><?php endif; ?>
            </h2>
            <p><?php echo HTML::chars($p['cargo']); ?></p>
            <p class="ind-detalle">
                <?php echo HTML::chars($p['oficina']); ?><?php echo $p['sigla'] ? ' (' . HTML::chars($p['sigla']) . ')' : ''; ?>
                · usuario <b><?php echo HTML::chars($p['username']); ?></b>
                · último ingreso <?php echo $p['ultimo_ingreso'] ? date('d/m/Y H:i', strtotime($p['ultimo_ingreso'])) : 'nunca'; ?>
                · <?php echo $M::numero($p['logins']); ?> ingresos en total
            </p>
        </div>
        <?php if ($p['id_oficina'] && !$propio): ?>
            <a class="ind-btn" href="<?php echo URL::site('reports/desempeno') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'oficina' => $p['id_oficina'])); ?>">Ver su oficina →</a>
        <?php endif; ?>
    </section>

    <h2 class="ind-seccion">Desempeño en el periodo</h2>
    <div class="ind-kpis">
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Recibió</span>
            <strong><?php echo $M::numero($v['movimientos']); ?></strong>
            <span class="ind-kpi-sub">derivaciones, de <?php echo $M::numero($v['hojas_movidas']); ?> hojas de ruta<?php echo $v['urgentes'] ? ' · ' . $M::numero($v['urgentes']) . ' urgentes' : ''; ?></span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tasa de atención</span>
            <strong class="ind-<?php echo $M::semaforo($tasa); ?>"><?php echo $pct($tasa); ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($v['atendidos']); ?> atendidas · <?php echo $M::numero($v['archivados']); ?> archivadas</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Atendidas en plazo</span>
            <strong class="ind-<?php echo $M::semaforo($c['porcentaje']); ?>"><?php echo $pct($c['porcentaje']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($c['en_plazo']); ?> de <?php echo $M::numero($c['cerrados']); ?> derivadas en menos de <?php echo $plazo; ?> días hábiles</span>
            <?php echo $o ? $comparar($c['porcentaje'], $o['cumplimiento']['porcentaje'], $pct, FALSE) : ''; ?>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tiempo medio de recepción</span>
            <strong><?php echo $M::duracion($t['recepcion']); ?></strong>
            <span class="ind-kpi-sub">desde que le derivan hasta que recibe</span>
            <?php echo $o ? $comparar($t['recepcion'], $o['tiempos']['recepcion'], array($M, 'duracion'), TRUE) : ''; ?>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tiempo medio de atención</span>
            <strong><?php echo $M::duracion($t['atencion']); ?></strong>
            <span class="ind-kpi-sub">desde que recibe hasta que deriva</span>
            <?php echo $o ? $comparar($t['atencion'], $o['tiempos']['atencion'], array($M, 'duracion'), TRUE) : ''; ?>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Derivó</span>
            <strong><?php echo $M::numero($e['total']); ?></strong>
            <span class="ind-kpi-sub">generó <?php echo $M::numero($v['documentos']); ?> documentos y creó <?php echo $M::numero($v['hojas_nuevas']); ?> hojas de ruta</span>
        </div>
    </div>

    <h2 class="ind-seccion">Su bandeja hoy <small>sin importar el periodo</small></h2>
    <div class="ind-kpis">
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Abiertos</span>
            <strong><?php echo $M::numero($r['total']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($r['pendientes']); ?> recibidos sin atender · <?php echo $M::numero($r['no_recibidos']); ?> sin recibir</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Vencidos</span>
            <strong class="<?php echo $r['vencidos'] ? 'ind-rojo' : 'ind-verde'; ?>"><?php echo $M::numero($r['vencidos']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $r['vencidos'] ? 'el más antiguo lleva ' . $M::numero($r['maximo']) . ' días hábiles' : 'todo al día'; ?></span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Por archivar</span>
            <strong class="<?php echo $r['por_archivar'] ? 'ind-ambar' : ''; ?>"><?php echo $M::numero($r['por_archivar']); ?></strong>
            <span class="ind-kpi-sub">con instrucción "Archivar" sin archivar</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Copias en bandeja</span>
            <strong><?php echo $M::numero($r['copias']); ?></strong>
            <span class="ind-kpi-sub">copias informativas sin cerrar</span>
        </div>
    </div>

    <div class="ind-grid">
        <section class="ind-card ind-span-2">
            <header><h3>Actividad de los últimos 12 meses</h3><p>Lo que recibió, lo que derivó y los documentos que generó</p></header>
            <div class="ind-chart ind-chart-alto"><canvas id="g-tendencia" aria-label="Actividad mensual"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Antigüedad de su bandeja</h3><p>Días hábiles que llevan sin atenderse</p></header>
            <div class="ind-chart"><canvas id="g-antiguedad" aria-label="Antigüedad"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>¿A qué hora deriva?</h3><p>Derivaciones hechas en el periodo, por hora del día</p></header>
            <div class="ind-chart"><canvas id="g-horas" aria-label="Por hora"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Qué le piden</h3><p>Instrucciones de lo que recibió en el periodo</p></header>
            <div class="ind-chart"><canvas id="g-acciones" aria-label="Instrucciones"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Documentos que generó</h3><p>Por tipo, en el periodo</p></header>
            <?php if ($d['tipos']): ?>
                <div class="ind-chart"><canvas id="g-tipos" aria-label="Documentos por tipo"></canvas></div>
            <?php else: ?>
                <p class="ind-vacio">No generó documentos en este periodo.</p>
            <?php endif; ?>
        </section>
        <section class="ind-card">
            <header><h3>Quiénes le derivan más</h3><p>En el periodo</p></header>
            <?php echo View::factory('reportes/indicadores/_ranking_personas')->set('filas', $d['contrapartes']['remitentes'])->set('maximo', $max_remitente)->set('enlace', $enlace_persona)->set('color', 'azul'); ?>
        </section>
        <section class="ind-card">
            <header><h3>A quiénes deriva más</h3><p>En el periodo</p></header>
            <?php echo View::factory('reportes/indicadores/_ranking_personas')->set('filas', $d['contrapartes']['destinatarios'])->set('maximo', $max_destinatario)->set('enlace', $enlace_persona)->set('color', 'amarillo'); ?>
        </section>
    </div>

    <section class="ind-card">
        <header class="ind-card-acciones">
            <div>
                <h3>Lo que tiene abierto hoy</h3>
                <p><?php echo count($d['abiertos']) >= 300 ? 'Se muestran los 300 más antiguos.' : $M::numero(count($d['abiertos'])) . (count($d['abiertos']) === 1 ? ' trámite' : ' trámites') . ', del más antiguo al más reciente.'; ?></p>
            </div>
            <?php if ($d['abiertos']): ?>
            <div class="ind-herramientas">
                <input type="search" class="ind-buscar" placeholder="Buscar…" data-buscar="tabla-abiertos" aria-label="Buscar en la tabla">
                <button type="button" class="ind-btn" data-exportar="tabla-abiertos" data-nombre="abiertos_<?php echo HTML::chars($p['username']) . '_' . date('Y-m-d'); ?>"><i class="fa fa-download" aria-hidden="true"></i> Excel (CSV)</button>
            </div>
            <?php endif; ?>
        </header>
        <?php if (!$d['abiertos']): ?>
            <p class="ind-vacio">No tiene nada pendiente.</p>
        <?php else: ?>
        <div class="ind-tabla-wrap ind-tabla-alta">
            <table class="ind-tabla" id="tabla-abiertos">
                <thead>
                    <tr>
                        <th data-tipo="texto">Hoja de ruta</th>
                        <th data-tipo="texto">Referencia</th>
                        <th data-tipo="texto">Enviado por</th>
                        <th class="num">Derivado el</th>
                        <th data-tipo="texto">Instrucción</th>
                        <th data-tipo="texto">Estado</th>
                        <th class="num">Días háb.</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($d['abiertos'] as $x): ?>
                    <tr>
                        <td data-valor="<?php echo HTML::chars($x['nur']); ?>">
                            <a class="ind-nombre" href="/route/trace/?hr=<?php echo urlencode($x['nur']); ?>"><?php echo HTML::chars($x['nur']); ?></a>
                            <?php if ($x['prioridad']): ?><span class="ind-pastilla ind-rojo">Urgente</span><?php endif; ?>
                            <?php if (!$x['oficial']): ?><span class="ind-pastilla ind-gris">Copia</span><?php endif; ?>
                        </td>
                        <td data-valor="<?php echo HTML::chars($x['referencia']); ?>" class="ind-referencia"><?php echo HTML::chars($x['referencia']); ?></td>
                        <td data-valor="<?php echo HTML::chars($x['nombre_emisor']); ?>">
                            <?php echo HTML::chars($x['nombre_emisor']); ?>
                            <span class="ind-detalle"><?php echo HTML::chars($x['de_oficina']); ?></span>
                        </td>
                        <td class="num" data-valor="<?php echo strtotime($x['fecha_emision']); ?>"><?php echo date('d/m/Y', strtotime($x['fecha_emision'])); ?></td>
                        <td data-valor="<?php echo HTML::chars($x['accion']); ?>"><?php echo HTML::chars($x['accion']); ?></td>
                        <td data-valor="<?php echo $x['estado']; ?>"><?php echo $x['estado'] == Model_Indicadores::NO_RECIBIDO ? 'Sin recibir' : 'Recibido'; ?></td>
                        <td class="num" data-valor="<?php echo $x['dias']; ?>"><b class="<?php echo $x['dias'] >= $plazo ? ($x['dias'] > 30 ? 'ind-rojo' : 'ind-ambar') : 'ind-verde'; ?>"><?php echo $M::numero($x['dias']); ?></b></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php echo View::factory('reportes/indicadores/_graficos'); ?>
<script>
(function () {
    var G = window.IndGraficos;
    var tendencia = <?php echo json_encode($d['tendencia']); ?>;
    G.barraLinea('g-tendencia', <?php echo json_encode($etiquetas_mes); ?>, [
        {label: 'Recibió', data: tendencia.map(function (m) { return m.recibidos; }), tipo: 'bar', color: G.c.azulClaro},
        {label: 'Derivó', data: tendencia.map(function (m) { return m.enviados; }), tipo: 'bar', color: G.c.azul},
        {label: 'Documentos generados', data: tendencia.map(function (m) { return m.documentos; }), tipo: 'line', color: G.c.amarillo}
    ]);
    G.barras('g-antiguedad', ['0–2', '3–<?php echo $plazo - 1; ?>', '<?php echo $plazo; ?>–10', '11–30', '31–120', 'más de 120'],
        <?php echo json_encode(array($r['b0'], $r['b1'], $r['b2'], $r['b3'], $r['b4'], $r['b5'])); ?>,
        {colores: [G.c.verde, G.c.verde, G.c.ambar, G.c.rojo, G.c.rojo, G.c.rojoOscuro], etiqueta: 'Abiertos'});
    var horas = <?php echo json_encode(array_values($d['horas'])); ?>;
    // solo de 6 a 22 h, salvo que haya actividad fuera de ese rango
    var desde = 6, hasta = 22;
    horas.forEach(function (n, h) { if (n > 0) { desde = Math.min(desde, h); hasta = Math.max(hasta, h); } });
    var rango = [];
    for (var h = desde; h <= hasta; h++) { rango.push(h); }
    G.barras('g-horas', rango.map(function (h) { return (h < 10 ? '0' : '') + h + ':00'; }), rango.map(function (h) { return horas[h]; }),
        {etiqueta: 'Derivaciones', colores: rango.map(function (h) { return h < 8 || h >= 19 ? G.c.ambar : G.c.azul; })});
    var acciones = <?php echo json_encode($d['acciones']); ?>;
    G.barras('g-acciones', acciones.map(function (a) { return a.etiqueta; }), acciones.map(function (a) { return +a.total; }), {horizontal: true, etiqueta: 'Recibidas'});
    var tipos = <?php echo json_encode($d['tipos']); ?>;
    if (tipos.length) {
        G.dona('g-tipos', tipos.map(function (t) { return t.etiqueta; }), tipos.map(function (t) { return +t.total; }));
    }
}());
</script>
