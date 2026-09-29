<?php
// ===== datos derivados para la vista =====
$dias_semana = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
$nombres_mes = array(1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$meses_cortos = array(1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
$hora = (int) date('G');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
$nombre_corto = trim(strtok((string) $user->nombre, ' '));
$hoy = $dias_semana[(int) date('w')] . ', ' . date('j') . ' de ' . $nombres_mes[(int) date('n')] . ' de ' . date('Y');

// documentos generados este mes vs el anterior
$gen = isset($actividad['generados']) ? $actividad['generados'] : array();
$gen_mes = $gen ? end($gen) : 0;
$gen_anterior = count($gen) > 1 ? $gen[count($gen) - 2] : 0;
$dif = $gen_mes - $gen_anterior;

// dias del pendiente mas antiguo
$dias_mas_antiguo = $pendientes_lista ? (int) $pendientes_lista[0]['dias'] : null;

// etiquetas del grafico mensual
$etiquetas_meses = array();
if (!empty($actividad['meses'])) {
    foreach ($actividad['meses'] as $m) {
        list($anio, $mes) = explode('-', $m);
        $etiquetas_meses[] = $meses_cortos[(int) $mes] . ' ' . substr($anio, 2);
    }
}

// documentos recientes: del mas nuevo al mas antiguo
$recientes = array();
foreach ($zdoc as $d) {
    $recientes[] = $d;
}
usort($recientes, function ($a, $b) {
    return (int) $b['id'] - (int) $a['id'];
});

$clase_dias = function ($dias) {
    return $dias > 30 ? 'dash-dias-alto' : ($dias > 7 ? 'dash-dias-medio' : 'dash-dias-bajo');
};
$texto_dias = function ($dias) {
    $dias = (int) $dias;
    return $dias <= 0 ? 'hoy' : ($dias == 1 ? '1 día' : $dias . ' días');
};

// tarjetas de estado (vienen del controlador con claves 1: entrada, 2: pendientes, 10: archivo)
$tarjetas = array();
foreach ($estados as $k => $v) {
    $extra = '';
    if ($k == 2 && $dias_mas_antiguo !== null) {
        $extra = 'El más antiguo espera ' . $texto_dias($dias_mas_antiguo);
    } elseif ($k == 1 && $por_recibir) {
        $urgentes = 0;
        foreach ($por_recibir as $p) {
            $urgentes += (int) $p['prioridad'] > 0 ? 1 : 0;
        }
        $extra = $urgentes ? $urgentes . ' urgente' . ($urgentes > 1 ? 's' : '') : 'Sin urgentes';
    }
    $tarjetas[] = array('titulo' => $v['titulo'], 'descripcion' => $v['descripcion'], 'accion' => $v['accion'],
        'cantidad' => (int) $v['cantidad'], 'icon' => $v['icon'], 'color' => $v['color'], 'extra' => $extra);
}
$extra_docs = $gen_mes . ' este mes';
if ($gen_anterior > 0 || $gen_mes > 0) {
    $extra_docs .= $dif == 0 ? ' (igual que el mes pasado)' : ' (' . ($dif > 0 ? '+' : '') . $dif . ' vs. mes pasado)';
}
$tarjetas[] = array('titulo' => 'Documentos', 'descripcion' => 'Documentos generados', 'accion' => '/document',
    'cantidad' => (int) $documentos, 'icon' => 'fa fa-file-text-o', 'color' => 'success', 'extra' => $extra_docs);
?>
<script>
    $(function () {
        var azul = '#1A549A', amarillo = '#FECB34', verde = '#2E9E5B';

        // ===== actividad de los ultimos 12 meses =====
        new Highcharts.Chart({
            chart: {renderTo: 'dash-actividad', backgroundColor: 'transparent', height: 300, style: {fontFamily: 'inherit'}},
            title: {text: ''},
            credits: {enabled: false},
            exporting: {enabled: false},
            xAxis: {categories: <?php echo json_encode($etiquetas_meses); ?>, tickLength: 0, lineColor: '#DCE3EC'},
            yAxis: {title: {text: ''}, allowDecimals: false, min: 0, gridLineColor: '#EEF2F7'},
            legend: {align: 'center', verticalAlign: 'top', itemStyle: {fontWeight: 'normal', color: '#4a5568'}},
            tooltip: {shared: true, borderColor: '#DCE3EC', backgroundColor: '#fff'},
            plotOptions: {
                column: {borderWidth: 0, pointPadding: 0.08, groupPadding: 0.12},
                spline: {lineWidth: 3, marker: {radius: 3}}
            },
            series: [
                {type: 'column', name: 'Documentos generados', color: azul, data: <?php echo json_encode(isset($actividad['generados']) ? $actividad['generados'] : array()); ?>},
                {type: 'column', name: 'Correspondencia recibida', color: amarillo, data: <?php echo json_encode(isset($actividad['recibidos']) ? $actividad['recibidos'] : array()); ?>},
                {type: 'spline', name: 'Correspondencia derivada', color: verde, data: <?php echo json_encode(isset($actividad['derivados']) ? $actividad['derivados'] : array()); ?>}
            ]
        });

        // ===== documentos por tipo (dona) =====
        var paleta = ['#1A549A', '#FECB34', '#3E86DD', '#F2A900', '#123E73', '#8FB0D8', '#C79201', '#2E9E5B', '#6B7686', '#D32F2F'];
        jQuery.getJSON('/ajax/misdocumentos/', {}, function (data) {
            var puntos = [], total = 0;
            $.each(data, function (i, e) {
                var n = parseFloat(e.cantidad) || 0;
                total += n;
                puntos.push({name: e.documento, y: n});
            });
            // de mayor a menor, y los colores de la marca para los tipos con mas documentos
            puntos.sort(function (a, b) {
                return b.y - a.y;
            });
            $.each(puntos, function (i, p) {
                p.color = paleta[i % paleta.length];
            });
            $('#dash-tipos-total').text(total.toLocaleString('es-BO'));
            if (!puntos.length) {
                $('#dash-tipos').html('<p class="dash-vacio">Aún no generó documentos.</p>');
                return;
            }
            new Highcharts.Chart({
                chart: {renderTo: 'dash-tipos', backgroundColor: 'transparent', height: 300, style: {fontFamily: 'inherit'}},
                title: {text: ''},
                credits: {enabled: false},
                exporting: {enabled: false},
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.point.name + '</b><br/>' + this.y + ' documentos (' + this.percentage.toFixed(1) + '%)';
                    }
                },
                legend: {
                    layout: 'vertical', align: 'right', verticalAlign: 'middle', itemMarginBottom: 4,
                    itemStyle: {fontWeight: 'normal', color: '#4a5568'},
                    labelFormatter: function () {
                        return this.name + ' <b>' + this.y + '</b>';
                    }
                },
                plotOptions: {
                    pie: {innerSize: '62%', borderWidth: 2, borderColor: '#fff', dataLabels: {enabled: false}, showInLegend: true, cursor: 'pointer',
                        center: ['40%', '50%'],
                        point: {events: {click: function () {
                                    window.location = '/document';
                                }}}}
                },
                series: [{type: 'pie', name: 'Documentos', data: puntos}]
            }, function (chart) {
                // total en el centro de la dona
                var serie = chart.series[0], c = serie.center;
                $('#dash-tipos-centro').css({left: (chart.plotLeft + c[0]) + 'px', top: (chart.plotTop + c[1]) + 'px'}).show();
            });
        });
    });
</script>
<style>
    .dash {
        --dash-azul: var(--correos-azul, #1A549A);
        --dash-azul-oscuro: var(--correos-azul-oscuro, #123E73);
        --dash-amarillo: var(--correos-amarillo, #FECB34);
        --dash-borde: var(--correos-borde, #DCE3EC);
    }
    .dash .dash-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .10);
        margin-bottom: 20px;
    }
    .dash .dash-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 2px solid var(--dash-amarillo);
    }
    .dash .dash-card-head h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--dash-azul-oscuro);
    }
    .dash .dash-card-head h3 .fa {
        color: var(--dash-azul);
        margin-right: 6px;
    }
    .dash .dash-card-head a.dash-ver {
        font-size: 12px;
        white-space: nowrap;
    }
    .dash .dash-card-body {
        padding: 14px 18px;
    }

    /* ===== saludo ===== */
    .dash-saludo {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px;
        margin-bottom: 20px;
        border-radius: 12px;
        background: linear-gradient(120deg, var(--correos-azul, #1A549A), var(--correos-azul-oscuro, #123E73));
        color: #fff;
        box-shadow: 0 4px 14px rgba(18, 62, 115, .25);
    }
    .dash-saludo h2 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 500;
        color: #fff;
    }
    .dash-saludo p {
        margin: 0;
        opacity: .85;
        font-size: 13px;
    }
    .dash-saludo p .fa {
        margin-right: 4px;
    }
    .dash-saludo .dash-fecha {
        display: inline-block;
        margin-top: 8px;
        padding: 3px 10px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .15);
        font-size: 12px;
    }
    .dash-saludo .dash-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .dash-saludo .dash-acciones .btn {
        margin: 0;
    }
    .dash-saludo .btn-dash-claro {
        background: rgba(255, 255, 255, .15);
        border: 1px solid rgba(255, 255, 255, .35);
        color: #fff;
    }
    .dash-saludo .btn-dash-claro:hover {
        background: rgba(255, 255, 255, .28);
        color: #fff;
    }
    .dash-saludo .dropdown-menu {
        max-height: 60vh;
        overflow-y: auto;
    }

    /* ===== tarjetas de estado ===== */
    .dash-kpis {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .dash-kpi {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .10);
        color: inherit;
        transition: transform .15s, box-shadow .15s;
    }
    .dash-kpi:hover,
    .dash-kpi:focus {
        text-decoration: none;
        color: inherit;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(18, 62, 115, .15);
    }
    .dash-kpi-icono {
        flex: 0 0 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .dash-kpi-texto {
        min-width: 0;
    }
    .dash-kpi-cantidad {
        display: block;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
        color: var(--dash-azul-oscuro);
    }
    .dash-kpi-titulo {
        display: block;
        font-weight: 600;
        color: #2d3748;
    }
    .dash-kpi-extra {
        display: block;
        font-size: 12px;
        color: #7a8594;
    }
    .dash-kpi-warning .dash-kpi-icono { background: #FFF3DC; color: #E08A00; }
    .dash-kpi-danger .dash-kpi-icono { background: #FDE8E8; color: #D32F2F; }
    .dash-kpi-info .dash-kpi-icono { background: #EAF1F9; color: #1A549A; }
    .dash-kpi-success .dash-kpi-icono { background: #E6F4EC; color: #2E9E5B; }

    /* ===== graficos ===== */
    .dash-tipos-contenedor {
        position: relative;
    }
    #dash-tipos-centro {
        display: none;
        position: absolute;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }
    #dash-tipos-centro b {
        display: block;
        font-size: 26px;
        line-height: 1;
        color: var(--dash-azul-oscuro);
    }
    #dash-tipos-centro span {
        font-size: 11px;
        color: #7a8594;
    }

    /* ===== listas ===== */
    .dash-lista {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .dash-lista li {
        border-bottom: 1px solid #EEF2F7;
    }
    .dash-lista li:last-child {
        border-bottom: 0;
    }
    .dash-lista a.dash-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 18px;
        color: inherit;
        transition: background .15s;
    }
    .dash-lista a.dash-item:hover {
        text-decoration: none;
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .dash-item-texto {
        flex: 1 1 auto;
        min-width: 0;
    }
    .dash-item-titulo {
        display: block;
        font-weight: 600;
        color: #2d3748;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .dash-item-meta {
        display: block;
        margin-top: 3px;
        font-size: 12px;
        color: #7a8594;
    }
    .dash-item-meta .dash-nur {
        color: var(--dash-azul);
        font-weight: 600;
    }
    .dash-dias {
        flex: 0 0 auto;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .dash-dias-bajo { background: #E6F4EC; color: #227547; }
    .dash-dias-medio { background: #FFF3DC; color: #B26B00; }
    .dash-dias-alto { background: #FDE8E8; color: #B42318; }
    .dash-etiqueta {
        display: inline-block;
        padding: 1px 6px;
        margin-right: 4px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        vertical-align: 1px;
    }
    .dash-urgente { background: #D32F2F; color: #fff; }
    .dash-copia { background: #EEF2F7; color: #4a5568; }
    .dash-tipo { background: var(--correos-azul-suave, #EAF1F9); color: var(--dash-azul-oscuro); }
    .dash-vacio {
        margin: 0;
        padding: 26px 18px;
        text-align: center;
        color: #8a94a3;
    }
    .dash-vacio .fa {
        display: block;
        font-size: 28px;
        margin-bottom: 6px;
        color: #2E9E5B;
    }
    .dash-editar {
        flex: 0 0 auto;
        color: #9aa4b2;
        font-size: 16px;
    }
    .dash-lista a.dash-item:hover .dash-editar {
        color: var(--dash-azul);
    }
</style>

<div class="dash">

    <!-- saludo y accesos rapidos -->
    <div class="dash-saludo">
        <div>
            <h2><?php echo $saludo . ', ' . HTML::chars($nombre_corto); ?></h2>
            <p>
                <i class="fa fa-user"></i><?php echo HTML::chars($user->cargo); ?>
                <?php if ($nombre_oficina != ''): ?>
                    &nbsp;&middot;&nbsp; <i class="fa fa-building-o"></i><?php echo HTML::chars($nombre_oficina); ?>
                <?php endif; ?>
            </p>
            <span class="dash-fecha"><i class="fa fa-calendar"></i> <?php echo ucfirst($hoy); ?></span>
        </div>
        <div class="dash-acciones">
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-accent dropdown-toggle" type="button" aria-expanded="false">
                    <i class="fa fa-plus"></i> Generar documento <i class="fa fa-caret-down"></i>
                </button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <?php foreach ($tipos as $t): ?>
                        <li><a href="/documento/generar/<?php echo HTML::chars($t['accion']); ?>"><?php echo HTML::chars($t['tipo']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="/bandeja" class="btn btn-dash-claro"><i class="fa fa-inbox"></i> Bandeja</a>
            <a href="/search/advanced" class="btn btn-dash-claro"><i class="fa fa-search"></i> Buscar</a>
        </div>
    </div>

    <!-- tarjetas de estado -->
    <div class="dash-kpis">
        <?php foreach ($tarjetas as $t): ?>
            <a href="<?php echo $t['accion']; ?>" class="dash-kpi dash-kpi-<?php echo $t['color']; ?>">
                <div class="dash-kpi-icono"><i class="<?php echo $t['icon']; ?>"></i></div>
                <div class="dash-kpi-texto">
                    <span class="dash-kpi-cantidad"><?php echo number_format($t['cantidad'], 0, ',', '.'); ?></span>
                    <span class="dash-kpi-titulo"><?php echo HTML::chars($t['titulo']); ?></span>
                    <span class="dash-kpi-extra"><?php echo HTML::chars($t['extra'] != '' ? $t['extra'] : $t['descripcion']); ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- graficos -->
    <div class="row">
        <div class="col-lg-7">
            <div class="dash-card">
                <div class="dash-card-head">
                    <h3><i class="fa fa-bar-chart"></i> Mi actividad de los últimos 12 meses</h3>
                </div>
                <div class="dash-card-body">
                    <div id="dash-actividad"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dash-card">
                <div class="dash-card-head">
                    <h3><i class="fa fa-pie-chart"></i> Documentos por tipo</h3>
                    <a href="/document" class="dash-ver">Ver todos <i class="fa fa-angle-right"></i></a>
                </div>
                <div class="dash-card-body dash-tipos-contenedor">
                    <div id="dash-tipos"></div>
                    <div id="dash-tipos-centro"><b id="dash-tipos-total">0</b><span>documentos</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- listas para actuar -->
    <div class="row">
        <div class="col-lg-4">
            <div class="dash-card">
                <div class="dash-card-head">
                    <h3><i class="fa fa-inbox"></i> Por recibir</h3>
                    <a href="/bandeja" class="dash-ver">Ir a la bandeja <i class="fa fa-angle-right"></i></a>
                </div>
                <?php if (!$por_recibir): ?>
                    <p class="dash-vacio"><i class="fa fa-check-circle"></i>No tiene correspondencia por recibir.</p>
                <?php else: ?>
                    <ul class="dash-lista">
                        <?php foreach ($por_recibir as $p): ?>
                            <li>
                                <a class="dash-item" href="/bandeja">
                                    <div class="dash-item-texto">
                                        <span class="dash-item-titulo">
                                            <?php if ((int) $p['prioridad'] > 0): ?><span class="dash-etiqueta dash-urgente">URGENTE</span><?php endif; ?>
                                            <?php if ((int) $p['oficial'] == 0): ?><span class="dash-etiqueta dash-copia">COPIA</span><?php endif; ?>
                                            <?php echo HTML::chars($p['referencia'] != '' ? $p['referencia'] : 'Sin referencia'); ?>
                                        </span>
                                        <span class="dash-item-meta">
                                            <span class="dash-nur"><?php echo HTML::chars($p['nur']); ?></span>
                                            &middot; de <?php echo HTML::chars($p['nombre_emisor']); ?>
                                        </span>
                                    </div>
                                    <span class="dash-dias <?php echo $clase_dias($p['dias']); ?>"><?php echo $texto_dias($p['dias']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dash-card">
                <div class="dash-card-head">
                    <h3><i class="fa fa-clock-o"></i> Pendientes más antiguos</h3>
                    <a href="/bandeja/pendientes" class="dash-ver">Ver todos <i class="fa fa-angle-right"></i></a>
                </div>
                <?php if (!$pendientes_lista): ?>
                    <p class="dash-vacio"><i class="fa fa-check-circle"></i>¡Al día! No tiene correspondencia pendiente.</p>
                <?php else: ?>
                    <ul class="dash-lista">
                        <?php foreach ($pendientes_lista as $p): ?>
                            <li>
                                <a class="dash-item" href="/route/trace/?hr=<?php echo urlencode($p['nur']); ?>" title="Ver seguimiento">
                                    <div class="dash-item-texto">
                                        <span class="dash-item-titulo">
                                            <?php if ((int) $p['prioridad'] > 0): ?><span class="dash-etiqueta dash-urgente">URGENTE</span><?php endif; ?>
                                            <?php echo HTML::chars($p['referencia'] != '' ? $p['referencia'] : 'Sin referencia'); ?>
                                        </span>
                                        <span class="dash-item-meta">
                                            <span class="dash-nur"><?php echo HTML::chars($p['nur']); ?></span>
                                            &middot; de <?php echo HTML::chars($p['nombre_emisor']); ?>
                                        </span>
                                    </div>
                                    <span class="dash-dias <?php echo $clase_dias($p['dias']); ?>"><?php echo $texto_dias($p['dias']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dash-card">
                <div class="dash-card-head">
                    <h3><i class="fa fa-file-text"></i> Documentos recientes</h3>
                    <a href="/document" class="dash-ver">Ver todos <i class="fa fa-angle-right"></i></a>
                </div>
                <?php if (!$recientes): ?>
                    <p class="dash-vacio"><i class="fa fa-file-o"></i>Aún no generó documentos.</p>
                <?php else: ?>
                    <ul class="dash-lista">
                        <?php foreach (array_slice($recientes, 0, 6) as $d): ?>
                            <li>
                                <a class="dash-item" href="/documento/edit/<?php echo (int) $d['id']; ?>" title="Editar documento">
                                    <div class="dash-item-texto">
                                        <span class="dash-item-titulo"><?php echo HTML::chars($d['referencia'] != '' ? $d['referencia'] : 'Sin referencia'); ?></span>
                                        <span class="dash-item-meta">
                                            <span class="dash-etiqueta dash-tipo"><?php echo HTML::chars($d['tipo']); ?></span>
                                            <?php echo HTML::chars($d['cite_original']); ?>
                                            <?php if ($d['nombre_destinatario'] != ''): ?>&middot; para <?php echo HTML::chars($d['nombre_destinatario']); ?><?php endif; ?>
                                            &middot; <?php echo HTML::chars($d['fecha_creacion']); ?>
                                        </span>
                                    </div>
                                    <i class="fa fa-pencil dash-editar"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
