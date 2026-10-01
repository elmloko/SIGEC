<?php
// Panel de ventanilla. Su trabajo es recibir documentacion externa, darle hoja de ruta
// y ponerla en circulacion; por eso lo primero es la lista de lo que todavia no salio.
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$sinDato = function ($v) {
    return trim(str_replace('.', '', (string) $v)) === '';
};
$por_derivar = 0;
foreach ($estados as $k => $v) {
    if ($k == 10) {
        $por_derivar = (int) $v['cantidad'];
    }
}
// ultimos 7 dias
$dias_es = array('Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb');
$serie = array();
$maximo = 0;
$semana = 0;
for ($i = 6; $i >= 0; $i--) {
    $clave = date('Y-m-d', strtotime("-$i day"));
    $v = isset($vent_semana[$clave]) ? (int) $vent_semana[$clave] : 0;
    $serie[] = array('etq' => $dias_es[(int) date('w', strtotime($clave))], 'valor' => $v, 'hoy' => $i === 0);
    $maximo = max($maximo, $v);
    $semana += $v;
}
$foto = file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg') ? '/static/fotos/' . $user->username . '.jpg' : '/static/fotos/' . ($user->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
$estados_seg = array(1 => array('No recibido', '#B26B00', '#FFF3DC'), 2 => array('Pendiente', '#1A549A', '#EAF1F9'),
    4 => array('Derivado', '#227547', '#E6F4EC'), 10 => array('Archivado', '#4A5568', '#EEF2F7'),
    11 => array('Anulado', '#B42318', '#FDE8E8'), 6 => array('Agrupado', '#5B3C99', '#F1ECFA'));
?>
<style>
    .vd-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    /* cabecera */
    .vd-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px 18px;
        padding: 18px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .vd-cab img {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #E8EFF8;
    }
    .vd-cab-texto {
        flex: 1 1 240px;
        min-width: 0;
    }
    .vd-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .vd-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .vd-recibir {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        height: 46px;
        padding: 0 24px;
        border-radius: 10px;
        background: #1A549A;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(26, 84, 154, .25);
    }
    .vd-recibir:hover {
        background: #123E73;
        color: #fff;
        text-decoration: none;
    }
    .vd-recibir .fa {
        font-size: 17px;
    }
    /* distribucion */
    .vd-dos {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .vd-dos {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    .vd-panel-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 15px 18px;
        border-bottom: 3px solid #FECB34;
    }
    .vd-panel-cab h3 {
        flex: 1 1 auto;
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #123E73;
    }
    .vd-panel-cab h3 .fa {
        margin-right: 8px;
        color: #1A549A;
    }
    .vd-panel-cab p {
        flex: 1 1 100%;
        margin: 2px 0 0;
        font-size: 12px;
        color: #8a94a3;
    }
    .vd-cuenta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 26px;
        padding: 0 10px;
        border-radius: 20px;
        background: #FDE8E8;
        color: #B42318;
        font-size: 13px;
        font-weight: 700;
    }
    .vd-cuenta.cero {
        background: #E6F4EC;
        color: #1E7B45;
    }
    .vd-ver {
        font-size: 12px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
    }
    /* lista accionable */
    .vd-lista {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .vd-lista li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 18px;
        border-bottom: 1px solid #F1F4F8;
    }
    .vd-lista li:last-child {
        border-bottom: 0;
    }
    .vd-lista li:hover {
        background: #F7FAFD;
    }
    .vd-dias {
        flex: 0 0 auto;
        min-width: 56px;
        padding: 4px 8px;
        border-radius: 8px;
        background: #EEF2F7;
        color: #4A5568;
        font-size: 11.5px;
        font-weight: 700;
        text-align: center;
        line-height: 1.25;
    }
    .vd-dias.medio {
        background: #FFF3DC;
        color: #B26B00;
    }
    .vd-dias.alto {
        background: #FDE8E8;
        color: #B42318;
    }
    .vd-dias span {
        display: block;
        font-size: 9.5px;
        font-weight: 400;
    }
    .vd-texto {
        flex: 1 1 auto;
        min-width: 0;
    }
    .vd-texto b {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .vd-texto b.vacio {
        color: #9aa4b2;
        font-style: italic;
        font-weight: 400;
    }
    .vd-texto small {
        display: block;
        margin-top: 1px;
        font-size: 11.5px;
        color: #8a94a3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .vd-nur {
        font-family: Consolas, "Courier New", monospace;
        color: #1A549A;
        font-weight: 700;
    }
    .vd-botones {
        flex: 0 0 auto;
        display: flex;
        gap: 6px;
    }
    .vd-derivar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 30px;
        padding: 0 13px;
        border-radius: 8px;
        background: #1A549A;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }
    .vd-derivar:hover {
        background: #123E73;
        color: #fff;
        text-decoration: none;
    }
    .vd-editar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border: 1px solid #D5DCE6;
        border-radius: 8px;
        background: #fff;
        color: #1A549A;
        text-decoration: none;
    }
    .vd-editar:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    .vd-vacio {
        padding: 34px 16px;
        text-align: center;
        font-size: 13.5px;
        color: #8a94a3;
    }
    .vd-vacio .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 30px;
        color: #A7D9BC;
    }
    .vd-pie {
        padding: 11px 18px;
        border-top: 1px solid #F1F4F8;
        text-align: center;
    }
    .vd-aviso {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 14px 18px 0;
        padding: 10px 13px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
    }
    .vd-aviso a {
        color: #7A5A00;
        text-decoration: underline;
        font-weight: 600;
    }
    /* movimiento del dia */
    .vd-hoy {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 16px 18px;
    }
    .vd-hoy-item {
        flex: 1 1 90px;
        text-align: center;
        padding: 10px 6px;
        border-radius: 10px;
        background: #F7F9FC;
    }
    .vd-hoy-item b {
        display: block;
        font-size: 22px;
        font-weight: 600;
        color: #123E73;
        line-height: 1.1;
    }
    .vd-hoy-item span {
        display: block;
        margin-top: 3px;
        font-size: 11.5px;
        color: #6b7686;
    }
    /* barras */
    .vd-barras {
        display: flex;
        align-items: flex-end;
        gap: 9px;
        height: 92px;
        padding: 14px 18px 0;
    }
    .vd-barra {
        flex: 1 1 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        height: 100%;
    }
    .vd-barra i {
        display: block;
        width: 100%;
        max-width: 32px;
        min-height: 3px;
        border-radius: 5px 5px 0 0;
        background: #C4D4E8;
    }
    .vd-barra.hoy i {
        background: #1A549A;
    }
    .vd-barra em {
        margin-bottom: 3px;
        font-size: 11.5px;
        font-style: normal;
        font-weight: 600;
        color: #123E73;
    }
    .vd-barra span {
        margin-top: 5px;
        font-size: 10.5px;
        color: #8a94a3;
    }
    .vd-barra.hoy span {
        color: #1A549A;
        font-weight: 700;
    }
    /* accesos */
    .vd-accesos {
        margin: 0;
        padding: 6px 0;
        list-style: none;
    }
    .vd-accesos a {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
    }
    .vd-accesos a:hover {
        background: #F3F6FA;
        color: #123E73;
        text-decoration: none;
    }
    .vd-accesos .fa {
        width: 18px;
        text-align: center;
        color: #1A549A;
    }
    .vd-etq {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 700;
    }
</style>

<div class="vd-card vd-cab">
    <img src="<?php echo $foto; ?>" alt="">
    <div class="vd-cab-texto">
        <h2><?php echo $h($user->nombre); ?></h2>
        <p><?php echo $h($user->cargo); ?><?php echo !empty($nombre_oficina) ? ' · ' . $h($nombre_oficina) : ''; ?>
            &nbsp;·&nbsp; <?php echo $n($vent_hoy['recibidos']); ?> recibidos hoy</p>
    </div>
    <a href="/ventanilla" class="vd-recibir"><i class="fa fa-inbox"></i> Recepcionar correspondencia</a>
</div>

<div class="vd-dos">
    <!-- lo que ventanilla registro y todavia no puso en circulacion -->
    <div class="vd-card">
        <div class="vd-panel-cab">
            <h3><i class="fa fa-paper-plane"></i>Listos para derivar</h3>
            <span class="vd-cuenta<?php echo count($vent_pendientes) == 0 ? ' cero' : ''; ?>"><?php echo $n(count($vent_pendientes)); ?></span>
            <a href="/ventanilla/pendientes?antiguos=0" class="vd-ver">Ver todos →</a>
            <p>Documentación que recibió y registró, pero que todavía no salió hacia su destinatario. Hasta que no se derive, el trámite no empieza.</p>
        </div>

        <?php if (!empty($vent_viejos)): ?>
            <div class="vd-aviso">
                <i class="fa fa-archive" style="margin-top:2px"></i>
                <span>Además hay <b><?php echo $n($vent_viejos); ?></b> registros sin derivar de hace más de un año, que vienen arrastrándose.
                    <a href="/ventanilla/pendientes?antiguos=1">Revisarlos aparte</a>.</span>
            </div>
        <?php endif; ?>

        <?php if (!$vent_pendientes): ?>
            <div class="vd-vacio">
                <i class="fa fa-check-circle"></i>
                Todo lo recibido este año ya está en circulación.
            </div>
        <?php else: ?>
            <ul class="vd-lista">
                <?php foreach ($vent_pendientes as $p):
                    $dias = (int) $p['dias'];
                    $clase = $dias >= 7 ? 'alto' : ($dias >= 3 ? 'medio' : '');
                    $vacio = $sinDato($p['referencia']);
                    $de = $sinDato($p['nombre_remitente']) ? '' : $p['nombre_remitente'];
                    $para = $sinDato($p['nombre_destinatario']) ? '' : $p['nombre_destinatario'];
                    ?>
                    <li>
                        <span class="vd-dias <?php echo $clase; ?>"><?php echo $dias; ?><span>día<?php echo $dias == 1 ? '' : 's'; ?></span></span>
                        <span class="vd-texto">
                            <b<?php echo $vacio ? ' class="vacio"' : ''; ?> title="<?php echo $h($p['referencia']); ?>"><?php echo $vacio ? 'Sin datos registrados' : $h($p['referencia']); ?></b>
                            <small>
                                <span class="vd-nur"><?php echo $h($p['nur']); ?></span>
                                <?php if ($de !== '' OR $para !== ''): ?>
                                    · <?php echo $h($de !== '' ? $de : 'Remitente sin registrar'); ?> → <?php echo $h($para !== '' ? $para : 'sin destinatario'); ?>
                                <?php endif; ?>
                            </small>
                        </span>
                        <span class="vd-botones">
                            <a href="/route/deriv/?hr=<?php echo urlencode($p['nur']); ?>" class="vd-derivar"><i class="fa fa-paper-plane"></i> Derivar</a>
                            <a href="/ventanilla/edit/<?php echo (int) $p['id']; ?>" class="vd-editar" title="Completar o corregir los datos"><i class="fa fa-pencil"></i></a>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="vd-pie">
                <a href="/ventanilla/pendientes?antiguos=0" class="vd-ver">Ver la lista completa →</a>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <!-- movimiento del dia -->
        <div class="vd-card">
            <div class="vd-panel-cab">
                <h3><i class="fa fa-calendar"></i>Movimiento</h3>
            </div>
            <div class="vd-hoy">
                <div class="vd-hoy-item">
                    <b><?php echo $n($vent_hoy['recibidos']); ?></b>
                    <span>recibidos hoy</span>
                </div>
                <div class="vd-hoy-item">
                    <b><?php echo $n($vent_hoy['derivados']); ?></b>
                    <span>ya derivados</span>
                </div>
                <div class="vd-hoy-item">
                    <b><?php echo $n($semana); ?></b>
                    <span>en 7 días</span>
                </div>
            </div>
            <div class="vd-barras">
                <?php foreach ($serie as $s): ?>
                    <span class="vd-barra<?php echo $s['hoy'] ? ' hoy' : ''; ?>">
                        <em><?php echo $s['valor']; ?></em>
                        <i style="height:<?php echo $maximo > 0 ? max(3, round($s['valor'] / $maximo * 56)) : 3; ?>px"></i>
                        <span><?php echo $s['etq']; ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
            <div style="height:14px"></div>
        </div>

        <!-- seguimiento de lo que ya salio -->
        <div class="vd-card">
            <div class="vd-panel-cab">
                <h3><i class="md md-verified-user"></i>Últimos puestos en circulación</h3>
                <p>Para seguir dónde están.</p>
            </div>
            <?php if (!$vent_encamino): ?>
                <div class="vd-vacio" style="padding:22px 16px"><i class="fa fa-paper-plane" style="color:#C4CEDB;font-size:22px"></i> Todavía no derivó nada.</div>
            <?php else: ?>
                <ul class="vd-lista">
                    <?php foreach ($vent_encamino as $e):
                        $est = isset($estados_seg[(int) $e['estado_seg']]) ? $estados_seg[(int) $e['estado_seg']] : array('—', '#4A5568', '#EEF2F7');
                        $vacio = $sinDato($e['referencia']);
                        ?>
                        <li style="padding:10px 18px">
                            <span class="vd-texto">
                                <b<?php echo $vacio ? ' class="vacio"' : ''; ?>><?php echo $vacio ? 'Sin datos registrados' : $h($e['referencia']); ?></b>
                                <small>
                                    <a href="/route/trace/?hr=<?php echo urlencode($e['nur']); ?>" class="vd-nur"><?php echo $h($e['nur']); ?></a>
                                    · <span class="vd-etq" style="background:<?php echo $est[2]; ?>;color:<?php echo $est[1]; ?>"><?php echo $est[0]; ?></span>
                                    <?php if ((int) $e['dias'] > 0): ?> · hace <?php echo (int) $e['dias']; ?> día<?php echo (int) $e['dias'] == 1 ? '' : 's'; ?><?php else: ?> · hoy<?php endif; ?>
                                </small>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="vd-pie">
                    <a href="/ventanilla/listar?estado=1" class="vd-ver">Ver todo lo derivado →</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="vd-card">
            <div class="vd-panel-cab">
                <h3><i class="fa fa-th-large"></i>Accesos</h3>
            </div>
            <ul class="vd-accesos">
                <li><a href="/ventanilla"><i class="fa fa-inbox"></i> Recepcionar correspondencia</a></li>
                <li><a href="/ventanilla/pendientes"><i class="fa fa-clock-o"></i> Pendientes de derivar</a></li>
                <li><a href="/ventanilla/listar"><i class="fa fa-files-o"></i> Todo lo recepcionado</a></li>
                <li><a href="/search/advanced"><i class="fa fa-search"></i> Búsqueda avanzada</a></li>
            </ul>
        </div>
    </div>
</div>
