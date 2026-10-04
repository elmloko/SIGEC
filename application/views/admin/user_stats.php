<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$foto = file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg') ? '/static/fotos/' . $user->username . '.jpg' : '/static/fotos/' . ($user->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';

$tarjetas = array(
    array('tipo' => 'entrada', 'titulo' => 'Sin recibir', 'pie' => 'Le llegaron y todavía no los abre',
        'valor' => $stats['norecibido'], 'icono' => 'fa-inbox', 'color' => '#B26B00', 'fondo' => '#FFF3DC'),
    array('tipo' => 'pendientes', 'titulo' => 'Pendientes', 'pie' => 'Recibidos, esperando que actúe',
        'valor' => $stats['pendientes'], 'icono' => 'fa-clock-o', 'color' => '#B42318', 'fondo' => '#FDE8E8'),
    array('tipo' => 'archivo', 'titulo' => 'Archivados', 'pie' => 'Trámites que ya cerró',
        'valor' => $stats['archivo'], 'icono' => 'fa-archive', 'color' => '#4A5568', 'fondo' => '#EEF2F7'),
    array('tipo' => 'documentos', 'titulo' => 'Documentos', 'pie' => 'Que generó esta persona',
        'valor' => $stats['documentos'], 'icono' => 'fa-file-text-o', 'color' => '#227547', 'fondo' => '#E6F4EC'),
);
$pendiente_total = (int) $stats['norecibido'] + (int) $stats['pendientes'];
$mas_viejo = (int) Arr::get($extra, 'mas_viejo', 0);
$dias_recibir = Arr::get($extra, 'dias_recibir', NULL);
$sin_recibir_3d = (int) Arr::get($extra, 'sin_recibir_3d', 0);

// ultimos 6 meses
$meses_nombre = array('', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
$serie = array();
$maximo = 0;
for ($i = 5; $i >= 0; $i--) {
    $clave = date('Y-m', strtotime(date('Y-m-01') . " -$i month"));
    $v = isset($por_mes[$clave]) ? (int) $por_mes[$clave] : 0;
    $serie[] = array('etiqueta' => $meses_nombre[(int) substr($clave, 5, 2)], 'valor' => $v);
    $maximo = max($maximo, $v);
}
?>
<style>
    body {
        background: #F3F5F8;
    }
    .es {
        max-width: 820px;
        margin: 0 auto;
        padding: 14px 16px 24px;
        font-family: Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .es-quien {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        margin-bottom: 14px;
        border-radius: 12px;
        border-top: 3px solid #FECB34;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .es-quien img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E8EFF8;
    }
    .es-quien b {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #123E73;
    }
    .es-quien span {
        display: block;
        font-size: 12.5px;
        color: #6b7686;
    }
    .es-ingreso {
        flex: 0 0 auto;
        text-align: right;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .es-ingreso b {
        font-size: 12.5px;
        color: #1f2937;
    }
    /* titulo de bloque */
    .es-titulo {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0 2px 8px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #8a94a3;
    }
    /* tarjetas */
    .es-grilla {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    .es-kpi {
        display: block;
        padding: 12px 14px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .1);
        text-decoration: none;
        transition: transform .12s, box-shadow .12s;
    }
    .es-kpi:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(18, 62, 115, .16);
        text-decoration: none;
    }
    .es-kpi-cab {
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .es-kpi-icono {
        flex: 0 0 32px;
        height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .es-kpi-cab b {
        font-size: 24px;
        font-weight: 600;
        color: #123E73;
        line-height: 1;
    }
    .es-kpi-nombre {
        display: block;
        margin-top: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }
    .es-kpi-pie {
        display: block;
        margin-top: 1px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .es-kpi-ver {
        display: block;
        margin-top: 7px;
        font-size: 11.5px;
        font-weight: 600;
        color: #1A549A;
        opacity: 0;
        transition: opacity .12s;
    }
    .es-kpi:hover .es-kpi-ver {
        opacity: 1;
    }
    .es-kpi.vacio .es-kpi-cab b {
        color: #9aa4b2;
    }
    /* fichas de detalle */
    .es-fichas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    .es-ficha {
        padding: 11px 14px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .1);
    }
    .es-ficha b {
        display: block;
        font-size: 19px;
        font-weight: 600;
        color: #123E73;
        line-height: 1.2;
    }
    .es-ficha b.alerta {
        color: #B42318;
    }
    .es-ficha b.bien {
        color: #227547;
    }
    .es-ficha span {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: #4a5566;
    }
    .es-ficha small {
        display: block;
        margin-top: 1px;
        font-size: 11px;
        color: #8a94a3;
    }
    /* mini grafico */
    .es-meses {
        padding: 14px 16px 10px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .1);
    }
    .es-meses h4 {
        margin: 0 0 12px;
        font-size: 13px;
        font-weight: 600;
        color: #123E73;
    }
    .es-meses h4 small {
        font-weight: 400;
        color: #8a94a3;
    }
    .es-barras {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        height: 90px;
    }
    .es-barra {
        flex: 1 1 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        height: 100%;
    }
    .es-barra i {
        display: block;
        width: 100%;
        max-width: 46px;
        min-height: 3px;
        border-radius: 5px 5px 0 0;
        background: #1A549A;
    }
    .es-barra.cero i {
        background: #E3E8EF;
    }
    .es-barra em {
        margin-bottom: 3px;
        font-size: 11.5px;
        font-style: normal;
        font-weight: 600;
        color: #123E73;
    }
    .es-barra span {
        margin-top: 5px;
        font-size: 11px;
        color: #8a94a3;
    }
    .es-nota {
        display: flex;
        gap: 9px;
        align-items: flex-start;
        margin-top: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
    }
</style>

<div class="es">
    <div class="es-quien">
        <img src="<?php echo $foto; ?>" alt="">
        <span style="flex:1 1 auto;min-width:0">
            <b><?php echo $h($user->nombre); ?></b>
            <span><?php echo $h($user->cargo); ?><?php echo $oficina ? ' · ' . $h($oficina) : ''; ?></span>
        </span>
        <span class="es-ingreso">
            Último ingreso<br>
            <b><?php echo $user->last_login ? date('d/m/Y H:i', $user->last_login) : 'Nunca'; ?></b>
        </span>
    </div>

    <div class="es-titulo"><i class="fa fa-inbox"></i> Correspondencia</div>
    <div class="es-grilla">
        <?php foreach ($tarjetas as $t): ?>
            <a href="/admin/content/userStatsList/<?php echo (int) $user->id; ?>?tipo=<?php echo $t['tipo']; ?>"
               class="es-kpi<?php echo (int) $t['valor'] === 0 ? ' vacio' : ''; ?>">
                <span class="es-kpi-cab">
                    <span class="es-kpi-icono" style="background:<?php echo $t['fondo']; ?>;color:<?php echo $t['color']; ?>"><i class="fa <?php echo $t['icono']; ?>"></i></span>
                    <b><?php echo $n($t['valor']); ?></b>
                </span>
                <span class="es-kpi-nombre"><?php echo HTML::chars($t['titulo']); ?></span>
                <span class="es-kpi-pie"><?php echo $t['pie']; ?></span>
                <span class="es-kpi-ver"><?php echo (int) $t['valor'] === 0 ? '—' : 'Ver la lista →'; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="es-titulo"><i class="fa fa-tachometer"></i> Cómo va respondiendo</div>
    <div class="es-fichas">
        <div class="es-ficha">
            <b class="<?php echo $pendiente_total > 20 ? 'alerta' : ($pendiente_total == 0 ? 'bien' : ''); ?>"><?php echo $n($pendiente_total); ?></b>
            <span>En su bandeja ahora</span>
            <small>sin recibir + pendientes</small>
        </div>
        <div class="es-ficha">
            <b class="<?php echo $mas_viejo > 15 ? 'alerta' : ($mas_viejo == 0 ? 'bien' : ''); ?>"><?php echo $mas_viejo > 0 ? $n($mas_viejo) . ' días' : '—'; ?></b>
            <span>El más antiguo sin cerrar</span>
            <small><?php echo $mas_viejo > 0 ? 'desde que se lo derivaron' : 'no tiene nada pendiente'; ?></small>
        </div>
        <div class="es-ficha">
            <b><?php echo $dias_recibir === NULL ? '—' : str_replace('.', ',', $dias_recibir) . ' días'; ?></b>
            <span>Tarda en recibir</span>
            <small>promedio de los últimos 6 meses</small>
        </div>
        <div class="es-ficha">
            <b><?php echo $n(Arr::get($extra, 'derivaciones', 0)); ?></b>
            <span>Derivaciones hechas</span>
            <small>documentos que envió a otros</small>
        </div>
    </div>

    <div class="es-meses">
        <h4>Documentos generados <small>· últimos 6 meses</small></h4>
        <div class="es-barras">
            <?php foreach ($serie as $s): ?>
                <span class="es-barra<?php echo $s['valor'] == 0 ? ' cero' : ''; ?>">
                    <em><?php echo $s['valor']; ?></em>
                    <i style="height:<?php echo $maximo > 0 ? max(3, round($s['valor'] / $maximo * 62)) : 3; ?>px"></i>
                    <span><?php echo $s['etiqueta']; ?></span>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($sin_recibir_3d > 0): ?>
        <div class="es-nota">
            <i class="fa fa-exclamation-triangle"></i>
            <span>Tiene <b><?php echo $n($sin_recibir_3d); ?></b> documento<?php echo $sin_recibir_3d == 1 ? '' : 's'; ?> que le derivaron hace más de 3 días y todavía no ha recibido.</span>
        </div>
    <?php endif; ?>
</div>

<script src="/static/js/modal-alto.js"></script>
<script>
    $(function () {
        if (window.ajustarModal) {
            window.ajustarModal();
            $(window).on('load', function () {
                window.ajustarModal();
            });
        }
    });
</script>
