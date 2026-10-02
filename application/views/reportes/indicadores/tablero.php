<?php
$M = 'Model_Indicadores';
$v = $d['volumen'];
$r = $d['rezago'];
$t = $d['tiempos'];
$c = $d['cumplimiento'];
$tasa = $v['movimientos'] > 0 ? round(100 * $v['atendidos'] / $v['movimientos'], 1) : NULL;
$meses_cortos = array('01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic');
$etiquetas_mes = array();
foreach ($d['tendencia'] as $m) {
    $etiquetas_mes[] = $meses_cortos[substr($m['mes'], 5, 2)] . ' ' . substr($m['mes'], 2, 2);
}
$por_usuario = $f['oficina'] > 0;
?>
<div class="col-md-12 ind">
    <?php echo View::factory('reportes/indicadores/_cabecera')->set('f', $f)->set('d', $d)->set('pagina', $pagina); ?>

    <h2 class="ind-seccion">Actividad del periodo</h2>
    <div class="ind-kpis">
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Derivaciones</span>
            <strong><?php echo $M::numero($v['movimientos']); ?></strong>
            <span class="ind-kpi-sub">sobre <?php echo $M::numero($v['hojas_movidas']); ?> hojas de ruta distintas</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Hojas de ruta nuevas</span>
            <strong><?php echo $M::numero($v['hojas_nuevas']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($v['documentos']); ?> documentos generados</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tasa de atención</span>
            <strong class="ind-<?php echo $M::semaforo($tasa); ?>"><?php echo $tasa === NULL ? '—' : number_format($tasa, 1, ',', '.') . '%'; ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($v['atendidos']); ?> derivadas o archivadas · <?php echo $M::numero($v['abiertos']); ?> siguen abiertas</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Atendidas en plazo</span>
            <strong class="ind-<?php echo $M::semaforo($c['porcentaje']); ?>"><?php echo $c['porcentaje'] === NULL ? '—' : number_format($c['porcentaje'], 1, ',', '.') . '%'; ?></strong>
            <span class="ind-kpi-sub">derivadas en menos de <?php echo $plazo; ?> días hábiles (<?php echo $M::numero($c['en_plazo']); ?> de <?php echo $M::numero($c['cerrados']); ?>)</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tiempo medio de recepción</span>
            <strong><?php echo $M::duracion($t['recepcion']); ?></strong>
            <span class="ind-kpi-sub">desde que se deriva hasta que se recibe</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tiempo medio de atención</span>
            <strong><?php echo $M::duracion($t['atencion']); ?></strong>
            <span class="ind-kpi-sub">desde que se recibe hasta que se vuelve a derivar</span>
        </div>
    </div>

    <h2 class="ind-seccion">Situación actual <small>sin atender a la fecha, sin importar el periodo</small></h2>
    <div class="ind-kpis">
        <a class="ind-kpi ind-kpi-link" href="<?php echo URL::site('reports/rezago') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'oficina' => $f['oficina'])); ?>">
            <span class="ind-kpi-titulo">Vencidos</span>
            <strong class="ind-rojo"><?php echo $M::numero($r['vencidos']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $r['total'] > 0 ? round(100 * $r['vencidos'] / $r['total']) : 0; ?>% de lo abierto lleva <?php echo $plazo; ?> días hábiles o más · ver detalle →</span>
        </a>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Abiertos</span>
            <strong><?php echo $M::numero($r['total']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($r['pendientes']); ?> recibidos sin atender · <?php echo $M::numero($r['no_recibidos']); ?> sin recibir</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Por archivar</span>
            <strong class="ind-ambar"><?php echo $M::numero($r['por_archivar']); ?></strong>
            <span class="ind-kpi-sub">con instrucción "Archivar" que nunca se archivaron</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Antigüedad promedio</span>
            <strong><?php echo number_format($r['promedio'], 0, ',', '.'); ?> <small>días háb.</small></strong>
            <span class="ind-kpi-sub">el más antiguo lleva <?php echo $M::numero($r['maximo']); ?> días hábiles</span>
        </div>
    </div>

    <div class="ind-grid">
        <section class="ind-card ind-span-2">
            <header><h3>Tendencia de los últimos 12 meses</h3><p>Derivaciones registradas y documentos generados por mes</p></header>
            <div class="ind-chart ind-chart-alto"><canvas id="g-tendencia" aria-label="Tendencia mensual"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Estado de las derivaciones</h3><p>Cómo están hoy las derivaciones del periodo</p></header>
            <div class="ind-chart"><canvas id="g-estados" aria-label="Estados"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Antigüedad de lo abierto</h3><p>Días hábiles que llevan sin atenderse</p></header>
            <div class="ind-chart"><canvas id="g-antiguedad" aria-label="Antigüedad del rezago"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Instrucciones más usadas</h3><p>Acción pedida al derivar en el periodo</p></header>
            <div class="ind-chart"><canvas id="g-acciones" aria-label="Instrucciones"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Carga por día de la semana</h3><p>Derivaciones del periodo</p></header>
            <div class="ind-chart"><canvas id="g-dias" aria-label="Carga por día"></canvas></div>
        </section>
    </div>

    <section class="ind-card">
        <header class="ind-card-acciones">
            <div>
                <h3><?php echo $por_usuario ? 'Funcionarios' : 'Oficinas'; ?> con más vencidos</h3>
                <p>Los 10 primeros. El detalle completo está en Desempeño.</p>
            </div>
            <a class="ind-btn" href="<?php echo URL::site('reports/desempeno') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'oficina' => $f['oficina'])); ?>">Ver desempeño completo →</a>
        </header>
        <?php echo View::factory('reportes/indicadores/_tabla_desempeno')->set('filas', $d['oficinas'])->set('por_usuario', $por_usuario)->set('f', $f)->set('id', 'tabla-top'); ?>
    </section>
</div>

<?php echo View::factory('reportes/indicadores/_graficos'); ?>
<script>
(function () {
    var G = window.IndGraficos;
    var tendencia = <?php echo json_encode($d['tendencia']); ?>;
    G.barraLinea('g-tendencia', <?php echo json_encode($etiquetas_mes); ?>, [
        {label: 'Derivaciones', data: tendencia.map(function (m) { return m.movimientos; }), tipo: 'bar', color: G.c.azul},
        {label: 'Aún abiertas', data: tendencia.map(function (m) { return m.abiertos; }), tipo: 'bar', color: G.c.rojo},
        {label: 'Documentos generados', data: tendencia.map(function (m) { return m.documentos; }), tipo: 'line', color: G.c.amarillo}
    ]);
    var estados = <?php echo json_encode($d['estados']); ?>;
    G.dona('g-estados', estados.map(function (e) { return e.etiqueta; }), estados.map(function (e) { return +e.total; }));
    G.barras('g-antiguedad', ['0–2', '3–<?php echo $plazo - 1; ?>', '<?php echo $plazo; ?>–10', '11–30', '31–120', 'más de 120'],
        <?php echo json_encode(array($r['b0'], $r['b1'], $r['b2'], $r['b3'], $r['b4'], $r['b5'])); ?>,
        {colores: [G.c.verde, G.c.verde, G.c.ambar, G.c.rojo, G.c.rojo, G.c.rojoOscuro], etiqueta: 'Abiertos'});
    var acciones = <?php echo json_encode($d['acciones']); ?>;
    G.barras('g-acciones', acciones.map(function (a) { return a.etiqueta; }), acciones.map(function (a) { return +a.total; }), {horizontal: true, etiqueta: 'Derivaciones'});
    var dias = <?php echo json_encode(array_values($d['dias'])); ?>;
    G.barras('g-dias', ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'], dias, {etiqueta: 'Derivaciones'});
}());
</script>
