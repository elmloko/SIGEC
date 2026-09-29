<?php
$es_admin = ((int) $user->nivel === 5);
$pasos = array();
foreach ($seguimiento as $s) {
    $pasos[] = $s;
}
// estado -> clase de color
$clase_estado = array(1 => 'tr-e-norecibido', 2 => 'tr-e-pendiente', 4 => 'tr-e-derivado', 6 => 'tr-e-agrupado', 10 => 'tr-e-archivado', 11 => 'tr-e-anulado');
// calculos compartidos con el PDF (print/seguimiento)
$actual = SeguimientoUtil::paso_actual($pasos);
$ultimo_oficial = SeguimientoUtil::ultimo_oficial($pasos);
$tenencia = SeguimientoUtil::tenencia($pasos);
$ruta = SeguimientoUtil::ruta($pasos);
$duracion = function ($segundos) {
    return SeguimientoUtil::duracion($segundos);
};
$dias_entre = function ($desde, $hasta = null) {
    return SeguimientoUtil::dias_entre($desde, $hasta);
};
$foto = function ($username, $genero) {
    return file_exists(DOCROOT . 'static/fotos/' . $username . '.jpg') ? '/static/fotos/' . $username . '.jpg' : '/static/fotos/' . $genero . '.jpg';
};
$fecha_hora = function ($fecha, $hora) {
    if (!$fecha) {
        return '';
    }
    return date('d/m/Y', strtotime($fecha)) . ($hora ? ' · ' . substr($hora, 0, 5) : '');
};
$dias_texto = function ($d) {
    return $d === null ? '' : ($d == 0 ? 'hoy' : ($d == 1 ? '1 día' : $d . ' días'));
};
$dias_creado = $detalle['fecha'] ? $dias_entre($detalle['fecha']) : null;
$n_copias = 0;
foreach ($pasos as $s) {
    $n_copias += (int) $s->oficial > 0 ? 0 : 1;
}
$urgente = $detalleTiempoDelTramite['tipo_tramite'] !== 'NO URGENTE';
?>
<style>
    .tr-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .tr-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .tr-hr {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 14px;
        background: var(--correos-azul, #1A549A);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
    }
    .tr-cab h2 {
        margin: 8px 0 0;
        font-size: 19px;
        font-weight: 700;
        line-height: 1.35;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .tr-botones .btn {
        margin: 0;
    }
    .tr-datos {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px 22px;
        padding: 16px 22px 18px;
    }
    .tr-dato small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .tr-dato span,
    .tr-dato a {
        font-size: 13px;
        color: #2d3748;
    }
    .tr-dato a {
        font-weight: 600;
        color: var(--correos-azul, #1A549A);
    }
    .tr-dato em {
        display: block;
        font-style: normal;
        font-size: 12px;
        color: #7a8594;
    }
    .tr-dato.tr-doble {
        grid-column: span 2;
    }
    .tr-adjuntos-doc a {
        display: inline-block;
        margin: 2px 4px 2px 0;
        padding: 2px 8px;
        border-radius: 6px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 12px;
    }

    /* resumen */
    .tr-resumen {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }
    .tr-kpi {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .10);
    }
    .tr-kpi-icono {
        flex: 0 0 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
    }
    .tr-kpi.tr-kpi-alerta .tr-kpi-icono { background: #FDE8E8; color: #D32F2F; }
    .tr-kpi.tr-kpi-ok .tr-kpi-icono { background: #E6F4EC; color: #2E9E5B; }
    .tr-kpi-texto {
        min-width: 0;
    }
    .tr-kpi-texto small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .tr-kpi-texto b {
        display: block;
        font-size: 15px;
        color: var(--correos-azul-oscuro, #123E73);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tr-kpi-texto span {
        display: block;
        font-size: 12px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* linea de tiempo */
    .tr-titulo {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        padding: 16px 22px;
        border-bottom: 2px solid var(--correos-amarillo, #FECB34);
    }
    .tr-titulo h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-titulo h3 .fa {
        color: var(--correos-azul, #1A549A);
        margin-right: 6px;
    }
    .tr-leyenda {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .tr-linea {
        position: relative;
        list-style: none;
        margin: 0;
        padding: 20px 22px 8px 22px;
    }
    .tr-linea:before {
        content: '';
        position: absolute;
        left: 43px;
        top: 20px;
        bottom: 20px;
        width: 3px;
        border-radius: 2px;
        background: var(--correos-borde, #DCE3EC);
    }
    .tr-paso {
        position: relative;
        display: grid;
        grid-template-columns: 44px minmax(0, 1fr);
        gap: 14px;
        margin-bottom: 16px;
    }
    .tr-num {
        position: relative;
        z-index: 1;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        background: #fff;
        border: 3px solid var(--correos-azul, #1A549A);
        color: var(--correos-azul, #1A549A);
    }
    .tr-paso.tr-copia .tr-num {
        border-color: #b7c0cc;
        color: #7a8594;
    }
    .tr-paso.tr-actual .tr-num {
        background: var(--correos-amarillo, #FECB34);
        border-color: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
        box-shadow: 0 0 0 6px rgba(254, 203, 52, .35);
    }
    .tr-caja {
        padding: 14px 16px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 12px;
        background: #fff;
    }
    .tr-paso.tr-copia .tr-caja {
        background: #FAFBFC;
    }
    .tr-paso.tr-actual .tr-caja {
        border: 2px solid var(--correos-amarillo, #FECB34);
        box-shadow: 0 6px 18px rgba(18, 62, 115, .10);
    }
    .tr-caja-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
    }
    .tr-etq {
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .tr-e-norecibido { background: #FFF3DC; color: #B26B00; }
    .tr-e-pendiente { background: #EAF1F9; color: #1A549A; }
    .tr-e-derivado { background: #E6F4EC; color: #227547; }
    .tr-e-agrupado { background: #F1ECFA; color: #5B3C99; }
    .tr-e-archivado { background: #EEF2F7; color: #4a5568; }
    .tr-e-anulado { background: #FDE8E8; color: #B42318; }
    .tr-oficial { background: var(--correos-azul, #1A549A); color: #fff; }
    .tr-copia-etq { background: #EEF2F7; color: #4a5568; }
    .tr-aqui {
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-accion {
        margin-left: auto;
        font-size: 12px;
        font-weight: 600;
        color: #8a6100;
    }
    .tr-flujo {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 30px minmax(0, 1fr);
        align-items: start;
        gap: 10px;
    }
    .tr-persona {
        display: flex;
        gap: 10px;
        min-width: 0;
    }
    .tr-persona img {
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        background: #EEF2F7;
    }
    .tr-persona-texto {
        min-width: 0;
        line-height: 1.35;
    }
    .tr-persona-texto small {
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #9aa4b2;
    }
    .tr-persona-texto b {
        display: block;
        font-size: 13px;
        color: #2d3748;
    }
    .tr-persona-texto span {
        display: block;
        font-size: 12px;
        color: #7a8594;
    }
    .tr-persona-texto a.tr-oficina {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--correos-azul, #1A549A);
        text-transform: uppercase;
    }
    .tr-persona-texto .tr-cuando {
        margin-top: 4px;
        font-size: 12px;
        color: #4a5568;
    }
    .tr-persona-texto .tr-cuando .fa {
        margin-right: 3px;
        color: #8a94a3;
    }
    .tr-cuando.tr-espera {
        color: #B26B00;
        font-weight: 600;
    }
    .tr-flecha {
        padding-top: 10px;
        text-align: center;
        font-size: 18px;
        color: #9aa4b2;
    }
    .tr-proveido {
        margin-top: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 13px;
        color: #2d3748;
    }
    .tr-proveido .md {
        margin-right: 5px;
        color: var(--correos-azul, #1A549A);
    }
    .tr-extras {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
    }
    .tr-extras .btn {
        margin: 0;
    }
    .tr-archivo {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        background: #FDE8E8;
        font-size: 12px;
        font-weight: 600;
        color: #B42318;
    }
    .tr-archivo:hover {
        text-decoration: none;
        color: #8a1c14;
    }
    .tr-archivo-desc {
        color: #7a8594;
        font-size: 12px;
    }
    .tr-doc-paso {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        background: var(--correos-azul-suave, #EAF1F9);
        font-size: 12px;
        font-weight: 600;
    }
    .tr-carpeta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        background: var(--correos-amarillo-suave, #FFF7DD);
        font-size: 12px;
        color: #8a6100;
    }
    .tr-alerta-obs {
        color: #D32F2F;
        font-weight: 600;
        font-size: 12px;
    }
    .tr-admin {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--correos-borde, #DCE3EC);
    }
    .tr-admin .btn {
        margin: 0;
    }
    /* mapa del recorrido */
    .tr-mapa-card {
        padding: 14px 18px 16px;
    }
    .tr-mapa-titulo {
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-mapa-titulo .fa {
        color: var(--correos-azul, #1A549A);
        margin-right: 5px;
    }
    .tr-mapa-titulo small {
        font-weight: normal;
        color: #8a94a3;
    }
    .tr-mapa {
        display: flex;
        align-items: stretch;
        gap: 4px;
        overflow-x: auto;
        /* espacio arriba para la etiqueta "Aquí" que sobresale del nodo */
        padding: 10px 2px 6px;
    }
    .tr-mapa-flecha {
        display: flex;
        align-items: center;
        font-size: 20px;
        color: #b7c0cc;
    }
    a.tr-nodo {
        position: relative;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 8px;
        max-width: 230px;
        padding: 8px 12px 8px 8px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 10px;
        background: #fff;
        color: inherit;
        transition: border-color .15s, box-shadow .15s;
    }
    a.tr-nodo:hover {
        text-decoration: none;
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 3px 10px rgba(18, 62, 115, .12);
    }
    .tr-nodo-num {
        flex: 0 0 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
    }
    .tr-nodo-inicio .tr-nodo-num {
        background: var(--correos-azul, #1A549A);
        color: #fff;
    }
    .tr-nodo-texto {
        min-width: 0;
        line-height: 1.25;
    }
    .tr-nodo-texto b {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: var(--correos-azul-oscuro, #123E73);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tr-nodo-texto small {
        display: block;
        font-size: 11px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    a.tr-nodo.tr-nodo-actual {
        border: 2px solid var(--correos-amarillo, #FECB34);
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .tr-nodo-actual .tr-nodo-num {
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-nodo-aqui,
    .tr-nodo-fin {
        position: absolute;
        top: -9px;
        right: 8px;
        padding: 1px 7px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .tr-nodo-aqui { background: var(--correos-amarillo, #FECB34); color: var(--correos-azul-oscuro, #123E73); }
    .tr-nodo-fin { background: #2E9E5B; color: #fff; }

    /* filtros de pasos */
    .tr-filtros {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .tr-filtro {
        padding: 4px 12px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 14px;
        background: #fff;
        font-size: 12px;
        color: #4a5568;
        cursor: pointer;
    }
    .tr-filtro:hover {
        border-color: var(--correos-azul, #1A549A);
    }
    .tr-filtro.activo {
        background: var(--correos-azul, #1A549A);
        border-color: var(--correos-azul, #1A549A);
        color: #fff;
    }
    .tr-filtro b {
        margin-left: 3px;
        color: inherit;
    }

    /* tiempo que la tuvo cada persona */
    .tr-tenencia { background: #E6F4EC; color: #227547; }
    .tr-tenencia-media { background: #FFF3DC; color: #B26B00; }
    .tr-tenencia-alta { background: #FDE8E8; color: #B42318; }

    /* resaltado al llegar desde el mapa */
    .tr-paso.tr-destello .tr-caja {
        animation: tr-destello 1.6s ease-out;
    }
    @keyframes tr-destello {
        0% { box-shadow: 0 0 0 0 rgba(254, 203, 52, .9); }
        100% { box-shadow: 0 0 0 14px rgba(254, 203, 52, 0); }
    }

    /* impresion (Ctrl+P): solo la informacion */
    @media print {
        #header, #menubar, .tr-botones, .tr-filtros, .tr-admin, .tr-archivo-desc, .modal { display: none !important; }
        #base { padding-left: 0 !important; }
        body, #content, .section-body { background: #fff !important; }
        .tr-card, .tr-kpi { box-shadow: none; border: 1px solid #ddd; }
        .tr-paso { page-break-inside: avoid; }
        .tr-mapa { flex-wrap: wrap; overflow: visible; }
    }

    .tr-vacio {
        padding: 40px 20px;
        text-align: center;
        color: #6b7686;
    }
    .tr-vacio .md {
        display: block;
        margin-bottom: 8px;
        font-size: 44px;
        color: #b7c0cc;
    }

    /* hoja de ruta sin derivar */
    .tr-sd {
        padding: 22px;
    }
    .tr-sd-estado {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .tr-sd-icono {
        flex: 0 0 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #B7791F;
    }
    .tr-sd-estado h4 {
        margin: 0 0 2px;
        font-size: 17px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-sd-estado p {
        margin: 0;
        font-size: 13px;
        color: #6b7686;
    }
    .tr-sd-pasos {
        display: flex;
        margin: 18px 0;
        border-radius: 10px;
        background: var(--correos-fondo, #F3F5F8);
        overflow: hidden;
    }
    .tr-sd-paso {
        flex: 1 1 0;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 10px 14px;
        font-size: 12px;
        color: #7a8594;
        border-right: 1px solid #fff;
    }
    .tr-sd-paso:last-child {
        border-right: 0;
    }
    .tr-sd-paso > span:last-child {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tr-sd-paso b {
        display: block;
        font-size: 13px;
        color: #4a5568;
    }
    .tr-sd-num {
        flex: 0 0 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        background: #fff;
        color: #9aa4b2;
        border: 2px solid #DCE3EC;
    }
    .tr-sd-paso.hecho .tr-sd-num {
        background: #2E9E5B;
        border-color: #2E9E5B;
        color: #fff;
    }
    .tr-sd-paso.actual {
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .tr-sd-paso.actual .tr-sd-num {
        border-color: var(--correos-amarillo, #FECB34);
        color: #8a6100;
    }
    .tr-sd-paso.actual b {
        color: var(--correos-azul-oscuro, #123E73);
    }
    .tr-sd-cuerpo {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }
    .tr-sd-cuerpo small {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .tr-sd-persona {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tr-sd-persona img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--correos-amarillo, #FECB34);
    }
    .tr-sd-persona b {
        display: block;
        color: #2d3748;
    }
    .tr-sd-persona b em {
        font-style: normal;
        font-weight: 600;
        color: var(--correos-azul, #1A549A);
    }
    .tr-sd-persona span {
        font-size: 12px;
        color: #7a8594;
    }
    .tr-sd-archivo {
        display: block;
        padding: 7px 10px;
        margin-bottom: 6px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        word-break: break-word;
    }
    .tr-sd-archivo:hover {
        text-decoration: none;
        border-color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .tr-sd-archivo .fa {
        color: #d32f2f;
        margin-right: 4px;
    }
    .tr-sd-nada {
        margin: 0;
        font-size: 13px;
        color: #9aa4b2;
    }
    .tr-sd-acciones {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #EEF2F7;
    }
    .tr-sd-acciones .btn {
        margin: 0;
    }
    .tr-sd-acciones .btn .fa {
        margin-right: 4px;
    }
    @media (max-width: 767px) {
        .tr-sd-pasos {
            flex-direction: column;
        }
        .tr-sd-cuerpo {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 1199px) {
        .tr-datos,
        .tr-resumen {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767px) {
        .tr-datos,
        .tr-resumen {
            grid-template-columns: minmax(0, 1fr);
        }
        .tr-dato.tr-doble {
            grid-column: auto;
        }
        .tr-flujo {
            grid-template-columns: minmax(0, 1fr);
        }
        .tr-flecha {
            transform: rotate(90deg);
            padding: 0;
        }
        .tr-accion {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<!-- cabecera -->
<div class="tr-card">
    <div class="tr-cab">
        <div style="min-width:0;flex:1 1 400px">
            <span class="tr-hr"><i class="md md-label"></i> Hoja de ruta <?php echo HTML::chars($detalle['nur']); ?></span>
            <?php if ($urgente): ?><span class="tr-etq tr-e-anulado" style="margin-left:6px">Urgente</span><?php endif; ?>
            <h2><?php echo HTML::chars($detalle['referencia'] != '' ? $detalle['referencia'] : 'Sin referencia'); ?></h2>
        </div>
        <div class="tr-botones">
            <?php if ($detalle['id_documento']): ?>
                <a href="/document/detalle/<?php echo (int) $detalle['id_documento']; ?>" class="btn btn-sm btn-default-bright"><i class="fa fa-file-text-o"></i> Ver documento</a>
            <?php endif; ?>
            <a href="/route/print?hr=<?php echo urlencode($detalle['nur']); ?>" class="btn btn-sm btn-default-bright"><i class="fa fa-print"></i> Imprimir HR</a>
            <a href="/print/seguimiento/?hr=<?php echo urlencode($detalle['nur']); ?>" target="_blank" class="btn btn-sm btn-primary"><i class="md md-print"></i> Imprimir seguimiento</a>
        </div>
    </div>
    <div class="tr-datos">
        <div class="tr-dato">
            <small>Documento original</small>
            <?php if ($detalle['id_documento']): ?>
                <a href="/document/detalle/<?php echo (int) $detalle['id_documento']; ?>"><?php echo HTML::chars($detalle['codigo']); ?></a>
            <?php else: ?><span>—</span><?php endif; ?>
        </div>
        <div class="tr-dato">
            <small>Tipo de documento</small>
            <span><?php echo HTML::chars($detalle['tipo']); ?></span>
        </div>
        <div class="tr-dato">
            <small>Proceso</small>
            <span><?php echo HTML::chars($detalle['proceso'] != '' ? $detalle['proceso'] : '—'); ?></span>
        </div>
        <div class="tr-dato">
            <small>Creado</small>
            <span><?php echo $detalle['fecha'] ? Date::fecha($detalle['fecha']) . ' · ' . date('H:i', strtotime($detalle['fecha'])) : '—'; ?></span>
        </div>
        <div class="tr-dato tr-doble">
            <small>Remitente</small>
            <span><?php echo HTML::chars($detalle['remitente']); ?></span>
            <em><?php echo HTML::chars($detalle['cargo_remitente']); ?></em>
        </div>
        <div class="tr-dato tr-doble">
            <small>Destinatario</small>
            <span><?php echo HTML::chars($detalle['destinatario']); ?></span>
            <em><?php echo HTML::chars($detalle['cargo_destinatario']); ?></em>
        </div>
        <?php if ($es_admin && count($archivo) > 0): ?>
            <div class="tr-dato" style="grid-column: 1 / -1">
                <small>Archivos adjuntos del documento original</small>
                <div class="tr-adjuntos-doc">
                    <?php foreach ($archivo as $a): ?>
                        <a href="/download/?file=<?php echo (int) $a->id; ?>" title="Descargar adjunto"><i class="fa fa-paperclip"></i> <?php echo HTML::chars(substr($a->nombre_archivo, 13)); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (count($pasos) > 0): ?>

    <!-- resumen -->
    <?php
    $paso_resumen = $actual !== null ? $pasos[$actual] : ($ultimo_oficial !== null ? $pasos[$ultimo_oficial] : end($pasos));
    $abierto = $actual !== null;
    $desde_resumen = (int) $paso_resumen->id_estado === 1 ? $paso_resumen->fecha_emision : ($paso_resumen->fecha_recepcion ? $paso_resumen->fecha_recepcion : $paso_resumen->fecha_emision);
    $dias_resumen = $dias_entre($desde_resumen);
    ?>
    <div class="tr-resumen">
        <div class="tr-kpi <?php echo $abierto && $dias_resumen > 7 ? 'tr-kpi-alerta' : ($abierto ? '' : 'tr-kpi-ok'); ?>">
            <div class="tr-kpi-icono"><i class="fa <?php echo $abierto ? 'fa-map-marker' : ((int) $paso_resumen->id_estado === 10 ? 'fa-archive' : 'fa-check'); ?>"></i></div>
            <div class="tr-kpi-texto">
                <small><?php echo $abierto ? 'Ahora está con' : 'Último destino'; ?></small>
                <b title="<?php echo HTML::chars($paso_resumen->nombre_receptor); ?>"><?php echo HTML::chars($paso_resumen->nombre_receptor); ?></b>
                <span><?php echo HTML::chars($paso_resumen->estado); ?><?php if ($abierto && $dias_resumen !== null): ?> · hace <?php echo $dias_texto($dias_resumen); ?><?php endif; ?></span>
            </div>
        </div>
        <div class="tr-kpi">
            <div class="tr-kpi-icono"><i class="fa fa-random"></i></div>
            <div class="tr-kpi-texto">
                <small>Recorrido</small>
                <b><?php echo count($pasos); ?> paso<?php echo count($pasos) == 1 ? '' : 's'; ?></b>
                <?php
                $n_oficiales = 0;
                foreach ($pasos as $p) {
                    $n_oficiales += (int) $p->oficial > 0 ? 1 : 0;
                }
                ?>
                <span><?php echo $n_oficiales; ?> oficial<?php echo $n_oficiales == 1 ? '' : 'es'; ?> · <?php echo count($pasos) - $n_oficiales; ?> en copia</span>
            </div>
        </div>
        <div class="tr-kpi">
            <div class="tr-kpi-icono"><i class="fa fa-clock-o"></i></div>
            <div class="tr-kpi-texto">
                <small>Tiempo del trámite</small>
                <b><?php echo $dias_creado !== null ? ($dias_creado == 1 ? '1 día' : $dias_creado . ' días') : '—'; ?></b>
                <span>desde que se creó</span>
            </div>
        </div>
        <div class="tr-kpi <?php echo $urgente ? 'tr-kpi-alerta' : ''; ?>">
            <div class="tr-kpi-icono"><i class="fa <?php echo $urgente ? 'fa-exclamation-triangle' : 'fa-flag-o'; ?>"></i></div>
            <div class="tr-kpi-texto">
                <small>Prioridad</small>
                <b><?php echo $urgente ? 'Urgente' : 'Normal'; ?></b>
                <span><?php echo $detalleTiempoDelTramite['fecha_plazo_urgente'] ? 'Plazo: ' . date('d/m/Y', strtotime($detalleTiempoDelTramite['fecha_plazo_urgente'])) : 'Sin plazo definido'; ?></span>
            </div>
        </div>
    </div>

    <?php if (count($ruta) > 1): ?>
        <!-- mapa del recorrido (solo pasos oficiales) -->
        <div class="tr-card tr-mapa-card">
            <div class="tr-mapa-titulo"><i class="fa fa-sitemap"></i> Camino de la hoja de ruta <small>(pasos oficiales · clic para ir al paso)</small></div>
            <div class="tr-mapa">
                <?php foreach ($ruta as $k => $nodo):
                    $es_nodo_actual = $nodo['paso'] !== null && $nodo['paso'] === $actual;
                    $es_ultimo = $k === count($ruta) - 1;
                    ?>
                    <?php if ($k > 0): ?><span class="tr-mapa-flecha"><i class="fa fa-angle-right"></i></span><?php endif; ?>
                    <a class="tr-nodo <?php echo $es_nodo_actual ? 'tr-nodo-actual' : ($k === 0 ? 'tr-nodo-inicio' : ''); ?>"
                       href="<?php echo $nodo['paso'] !== null ? '#tr-paso-' . $nodo['paso'] : '#tr-paso-0'; ?>"
                       title="<?php echo HTML::chars($nodo['persona']); ?>">
                        <span class="tr-nodo-num"><?php echo $k === 0 ? '<i class="fa fa-play"></i>' : $k; ?></span>
                        <span class="tr-nodo-texto">
                            <b><?php echo HTML::chars($nodo['oficina']); ?></b>
                            <small><?php echo HTML::chars($nodo['persona']); ?></small>
                        </span>
                        <?php if ($es_nodo_actual): ?><span class="tr-nodo-aqui">Aquí</span><?php elseif ($es_ultimo && $actual === null): ?><span class="tr-nodo-fin">Final</span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($agrupado->id)): ?>
        <div class="alert alert-warning"><i class="fa fa-folder-o"></i> Esta hoja de ruta fue agrupada dentro de la hoja de ruta principal
            <a href="/route/trace/?hr=<?php echo urlencode($agrupado->padre); ?>"><b><?php echo HTML::chars($agrupado->padre); ?></b></a>.</div>
    <?php endif; ?>

    <!-- linea de tiempo -->
    <div class="tr-card">
        <div class="tr-titulo">
            <h3><i class="fa fa-road"></i> Recorrido de la hoja de ruta</h3>
            <div class="tr-filtros">
                <span class="tr-filtro activo" data-filtro="todos">Todos <b><?php echo count($pasos); ?></b></span>
                <span class="tr-filtro" data-filtro="oficiales">Oficiales <b><?php echo count($pasos) - $n_copias; ?></b></span>
                <?php if ($n_copias): ?><span class="tr-filtro" data-filtro="copias">Copias <b><?php echo $n_copias; ?></b></span><?php endif; ?>
            </div>
        </div>
        <ul class="tr-linea">
            <?php
            $hijo = 0;
            foreach ($pasos as $i => $s):
                $id_seguimiento = (int) $s->id;
                $estado = (int) $s->id_estado;
                $es_actual = ($i === $actual);
                $clases = 'tr-paso' . ((int) $s->oficial > 0 ? '' : ' tr-copia') . ($es_actual ? ' tr-actual' : '');

                // justificaciones por retraso de este paso
                $justificaciones = DB::query(Database::SELECT, "SELECT os.observacion, os.fecha_observacion, u.nombre, u.cargo
                        FROM observacion_seguimiento os INNER JOIN users u ON os.id_usuario = u.id
                        WHERE os.id_seguimiento = :id AND os.nur = :nur AND os.id_estado = '2'
                        ORDER BY os.fecha_observacion DESC")
                    ->param(':id', $id_seguimiento)->param(':nur', (string) $s->nur)->execute()->as_array();
                // observaciones externas de este paso
                $observaciones = DB::query(Database::SELECT, "SELECT id, observacion, correo, telefono FROM observacion_seguimiento_externo WHERE id_seguimiento = :id")
                    ->param(':id', $id_seguimiento)->execute()->as_array();
                // documentos generados en este paso
                $documentos_paso = ORM::factory('documentos')->where('id_seguimiento', '=', $id_seguimiento)->find_all();
                // carpeta donde se archivo
                $archivado = false;
                if ($estado === 10) {
                    $mSeguimiento = new Model_Seguimiento();
                    $archivado = $mSeguimiento->hrArchivada($s->nur, $s->derivado_a);
                }
                $archivos_paso = isset($archivos_por_seguimiento[$s->id]) ? $archivos_por_seguimiento[$s->id] : array();
                ?>
                <li class="<?php echo $clases; ?>" id="tr-paso-<?php echo $i; ?>" <?php echo $es_actual ? 'data-actual="1"' : ''; ?>>
                    <div class="tr-num"><?php echo $i + 1; ?></div>
                    <div class="tr-caja">
                        <div class="tr-caja-cab">
                            <?php if ($es_actual): ?><span class="tr-etq tr-aqui"><i class="fa fa-map-marker"></i> Aquí está ahora</span><?php endif; ?>
                            <span class="tr-etq <?php echo isset($clase_estado[$estado]) ? $clase_estado[$estado] : 'tr-e-archivado'; ?>"><?php echo HTML::chars($s->estado); ?></span>
                            <span class="tr-etq <?php echo (int) $s->oficial > 0 ? 'tr-oficial' : 'tr-copia-etq'; ?>"><?php echo (int) $s->oficial > 0 ? 'Oficial' : 'Copia'; ?></span>
                            <?php if (isset($tenencia[$i])):
                                $t_dias = $tenencia[$i]['dias'];
                                ?>
                                <span class="tr-etq tr-tenencia <?php echo $t_dias > 7 ? 'tr-tenencia-alta' : ($t_dias > 2 ? 'tr-tenencia-media' : ''); ?>"
                                      title="Tiempo desde que <?php echo HTML::chars($s->nombre_receptor); ?> la recibió hasta que la derivó">
                                    <i class="fa fa-clock-o"></i> <?php echo $tenencia[$i]['en_curso'] ? 'La tiene hace ' : 'La tuvo '; ?><?php echo $duracion($tenencia[$i]['segundos']); ?></span>
                            <?php endif; ?>
                            <?php if ($s->accion != ''): ?><span class="tr-accion"><i class="fa fa-hand-o-right"></i> <?php echo HTML::chars($s->accion); ?></span><?php endif; ?>
                        </div>

                        <div class="tr-flujo">
                            <div class="tr-persona">
                                <img src="<?php echo HTML::chars($foto($s->u1, $s->s1)); ?>" alt=""/>
                                <div class="tr-persona-texto">
                                    <small>De</small>
                                    <a class="tr-oficina" href="/route/oficina/<?php echo (int) $s->id_de_oficina; ?>"><?php echo HTML::chars($s->de_oficina); ?></a>
                                    <b><?php echo HTML::chars($s->nombre_emisor); ?></b>
                                    <span><?php echo HTML::chars($s->cargo_emisor); ?></span>
                                    <div class="tr-cuando"><i class="fa fa-paper-plane-o"></i> Enviado el <?php echo $fecha_hora($s->fecha_emision, $s->hora_emision); ?></div>
                                </div>
                            </div>
                            <div class="tr-flecha"><i class="fa fa-long-arrow-right"></i></div>
                            <div class="tr-persona">
                                <img src="<?php echo HTML::chars($foto($s->u2, $s->s2)); ?>" alt=""/>
                                <div class="tr-persona-texto">
                                    <small>Para</small>
                                    <a class="tr-oficina" href="/route/oficina/<?php echo (int) $s->id_a_oficina; ?>"><?php echo HTML::chars($s->a_oficina); ?></a>
                                    <b><?php echo HTML::chars($s->nombre_receptor); ?></b>
                                    <span><?php echo HTML::chars($s->cargo_receptor); ?></span>
                                    <?php if ($s->fecha_recepcion): ?>
                                        <div class="tr-cuando"><i class="fa fa-check"></i> Recibido el <?php echo $fecha_hora($s->fecha_recepcion, $s->hora_recepcion); ?>
                                            <?php $espera = $dias_entre($s->fecha_emision . ' ' . $s->hora_emision, $s->fecha_recepcion . ' ' . $s->hora_recepcion); ?>
                                            <?php if ($espera): ?><span style="display:inline;color:#8a94a3">(<?php echo $dias_texto($espera); ?> después)</span><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="tr-cuando tr-espera"><i class="fa fa-clock-o"></i> Sin recibir desde hace <?php echo $dias_texto($dias_entre($s->fecha_emision . ' ' . $s->hora_emision)); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (trim($s->proveido) != ''): ?>
                            <div class="tr-proveido"><i class="md md-message"></i><?php echo HTML::chars($s->proveido); ?></div>
                        <?php endif; ?>

                        <?php if ($archivos_paso || count($documentos_paso) || $archivado || $justificaciones || $observaciones): ?>
                            <div class="tr-extras">
                                <?php foreach ($archivos_paso as $af):
                                    $nombre_af = substr($af->nombre_archivo, 13);
                                    ?>
                                    <a href="#" class="tr-archivo visor-pdf" title="Ver adjunto"
                                       data-url="/download/?file=<?php echo (int) $af->id; ?>&amp;ver=1" data-descargar="/download/?file=<?php echo (int) $af->id; ?>"
                                       data-nombre="<?php echo HTML::chars($nombre_af); ?>"><i class="fa fa-file-pdf-o"></i> <?php echo HTML::chars($nombre_af); ?></a>
                                    <a href="/download/?file=<?php echo (int) $af->id; ?>" class="tr-archivo-desc" title="Descargar"><i class="fa fa-download"></i></a>
                                <?php endforeach; ?>
                                <?php foreach ($documentos_paso as $d): ?>
                                    <a href="/vista/?doc=<?php echo urlencode($d->cite_original); ?>&amp;id_seg=<?php echo $id_seguimiento; ?>" target="_blank" class="tr-doc-paso">
                                        <i class="fa fa-file-text-o"></i> <?php echo HTML::chars($d->codigo); ?></a>
                                <?php endforeach; ?>
                                <?php if ($archivado): ?>
                                    <?php if ((int) $user->id_oficina === (int) $s->id_a_oficina): ?>
                                        <a href="/bandeja/folder/<?php echo (int) $archivado['id']; ?>" class="tr-carpeta"><i class="fa fa-folder-open"></i> Archivado en: <b><?php echo HTML::chars($archivado['carpeta']); ?></b></a>
                                    <?php else: ?>
                                        <span class="tr-carpeta"><i class="fa fa-folder"></i> Archivado en: <b><?php echo HTML::chars($archivado['carpeta']); ?></b></span>
                                    <?php endif; ?>
                                    <?php if (trim($archivado['observaciones']) != ''): ?>
                                        <span class="tr-archivo-desc"><b>Obs.:</b> <?php echo HTML::chars($archivado['observaciones']); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($justificaciones): ?>
                                    <a href="#" class="tr-alerta-obs" data-toggle="modal" data-target="#tr-justif-<?php echo $id_seguimiento; ?>">
                                        <i class="fa fa-exclamation-triangle"></i> Justificación por retraso (<?php echo count($justificaciones); ?>)</a>
                                <?php endif; ?>
                                <?php if ($observaciones): ?>
                                    <a href="#" class="tr-alerta-obs" data-toggle="modal" data-target="#tr-obs-<?php echo $id_seguimiento; ?>">
                                        <i class="fa fa-search"></i> Observaciones (<?php echo count($observaciones); ?>)</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($es_admin): ?>
                            <div class="tr-admin">
                                <?php if ($estado === 1): ?>
                                    <form method="post" action="/admin/hojasruta/revertir/<?php echo (int) $detalle['id_documento']; ?>" style="display:inline;">
                                        <input type="hidden" name="confirmar" value="1"/>
                                        <button type="submit" data-nur="<?php echo HTML::chars($s->nur); ?>" class="btn btn-xs btn-warning btn-revertir-hr-trace"
                                                title="Revertir esta derivación (solo si aún no fue recibida)"><i class="md md-undo"></i> Revertir</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ((int) $s->oficial > 0): ?>
                                    <a href="/admin/hojasruta/agregar/<?php echo $id_seguimiento; ?>" class="btn btn-xs btn-info"
                                       title="Agregar un destinatario en copia que <?php echo HTML::chars($s->nombre_emisor); ?> olvidó incluir"><i class="md md-person-add"></i> Agregar destinatario</a>
                                <?php endif; ?>
                                <?php if (!$archivos_paso): ?>
                                    <a href="/admin/hojasruta/crearinforme/<?php echo $id_seguimiento; ?>?nur=<?php echo urlencode($detalle['nur']); ?>" class="btn btn-xs btn-default-bright"
                                       title="Crear el informe de este paso"><i class="fa fa-pencil"></i> Informe</a>
                                <?php endif; ?>
                                <?php foreach ($archivos_paso as $af): ?>
                                    <a href="/admin/hojasruta/editarinforme/<?php echo (int) $af->id_documento; ?>?seg=<?php echo $id_seguimiento; ?>&amp;nur=<?php echo urlencode($detalle['nur']); ?>"
                                       class="btn btn-xs btn-default-bright" title="Editar este informe (datos, fecha y adjunto)"><i class="fa fa-pencil"></i> Editar informe</a>
                                <?php endforeach; ?>
                                <?php foreach ($documentos_paso as $d): ?>
                                    <a href="/admin/hojasruta/editarinforme/<?php echo (int) $d->id; ?>?seg=<?php echo $id_seguimiento; ?>&amp;nur=<?php echo urlencode($detalle['nur']); ?>"
                                       class="btn btn-xs btn-default-bright" title="Editar este informe (datos, fecha y adjunto)"><i class="fa fa-pencil"></i> <?php echo HTML::chars($d->codigo); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($justificaciones): ?>
                        <div class="modal fade" id="tr-justif-<?php echo $id_seguimiento; ?>" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Justificación por el retraso</h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php foreach ($justificaciones as $j): ?>
                                            <p><b><?php echo date('d/m/Y H:i', strtotime($j['fecha_observacion'])); ?></b> &middot; <?php echo HTML::chars($j['nombre']); ?>
                                                <small class="text-muted">(<?php echo HTML::chars($j['cargo']); ?>)</small><br/><?php echo nl2br(HTML::chars($j['observacion'])); ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($observaciones): ?>
                        <div class="modal fade" id="tr-obs-<?php echo $id_seguimiento; ?>" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Observaciones</h4>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table">
                                            <thead><tr><th>Observación</th><th>Correo</th><th>Teléfono</th></tr></thead>
                                            <tbody>
                                            <?php foreach ($observaciones as $o): ?>
                                                <tr>
                                                    <td><?php echo HTML::chars($o['observacion']); ?></td>
                                                    <td><?php echo HTML::chars($o['correo']); ?></td>
                                                    <td><?php echo HTML::chars($o['telefono']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </li>
                <?php
                $hijo += (int) $s->hijo;
            endforeach;
            ?>
        </ul>

        <?php if ($hijo > 0):
            $hijos = ORM::factory('agrupaciones')->where('padre', '=', $detalle['nur'])->find_all();
            ?>
            <div class="alert alert-warning" style="margin: 0 22px 18px">
                <i class="fa fa-folder-open-o"></i> <b>Agrupada con:</b>
                <?php foreach ($hijos as $h): ?>
                    <a href="/route/trace/?hr=<?php echo urlencode($h->hijo); ?>" style="margin-left:6px"><b><?php echo HTML::chars($h->hijo); ?></b></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<?php else:
    // aun sin derivar: el documento sigue con quien lo genero
    $generador = ORM::factory('users', isset($detalle['id_user']) ? $detalle['id_user'] : 0);
    $es_mio = $generador->loaded() && (int) $generador->id === (int) $user->id;
    $n_arch = count($archivo);
    ?>
    <div class="tr-card">
        <div class="tr-sd">
            <div class="tr-sd-estado">
                <span class="tr-sd-icono"><i class="fa fa-clock-o"></i></span>
                <div>
                    <h4>Aún no fue derivada</h4>
                    <p>
                        Generada <?php echo $dias_creado === null ? '' : ($dias_creado == 0 ? 'hoy' : 'hace ' . $dias_texto($dias_creado)); ?>
                        y todavía no salió de la bandeja de quien la creó. Cuando se derive, aquí verá su recorrido paso a paso.
                    </p>
                </div>
            </div>

            <!-- pasos para poder derivar -->
            <div class="tr-sd-pasos">
                <div class="tr-sd-paso hecho">
                    <span class="tr-sd-num"><i class="fa fa-check"></i></span>
                    <span><b>Documento generado</b><?php echo HTML::chars($detalle['codigo']); ?></span>
                </div>
                <div class="tr-sd-paso <?php echo $n_arch > 0 ? 'hecho' : 'actual'; ?>">
                    <span class="tr-sd-num"><?php echo $n_arch > 0 ? '<i class="fa fa-check"></i>' : '2'; ?></span>
                    <span><b>Archivo digital</b><?php echo $n_arch > 0 ? $n_arch . ($n_arch == 1 ? ' PDF subido' : ' PDF subidos') : 'Falta subir el PDF'; ?></span>
                </div>
                <div class="tr-sd-paso <?php echo $n_arch > 0 ? 'actual' : ''; ?>">
                    <span class="tr-sd-num">3</span>
                    <span><b>Derivar</b>Enviar al destinatario</span>
                </div>
            </div>

            <div class="tr-sd-cuerpo">
                <?php if ($generador->loaded()): ?>
                    <div class="tr-sd-quien">
                        <small>La tiene</small>
                        <div class="tr-sd-persona">
                            <img src="<?php echo $foto($generador->username, $generador->genero); ?>" alt=""/>
                            <div>
                                <b><?php echo HTML::chars($generador->nombre); ?><?php echo $es_mio ? ' <em>(usted)</em>' : ''; ?></b>
                                <span><?php echo HTML::chars($generador->cargo); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="tr-sd-archivos">
                    <small>Archivos digitales</small>
                    <?php if ($n_arch == 0): ?>
                        <p class="tr-sd-nada"><i class="fa fa-file-pdf-o"></i> Sin archivos todavía</p>
                    <?php else: ?>
                        <?php foreach ($archivo as $a): $nom = substr($a->nombre_archivo, 13); ?>
                            <a href="#" class="tr-sd-archivo visor-pdf" title="Ver archivo"
                               data-url="/download/?file=<?php echo (int) $a->id; ?>&amp;ver=1" data-descargar="/download/?file=<?php echo (int) $a->id; ?>"
                               data-nombre="<?php echo HTML::chars($nom); ?>"><i class="fa fa-file-pdf-o"></i> <?php echo HTML::chars($nom); ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($es_mio): ?>
                <div class="tr-sd-acciones">
                    <a href="/documento/edit/<?php echo (int) $detalle['id_documento']; ?>" class="btn btn-default-bright"><i class="fa fa-pencil"></i> Editar documento</a>
                    <?php if ($n_arch > 0): ?>
                        <a href="/route/deriv/?hr=<?php echo urlencode($detalle['nur']); ?>" class="btn btn-primary"><i class="fa fa-send-o"></i> Derivar ahora</a>
                    <?php else: ?>
                        <a href="/documento/edit/<?php echo (int) $detalle['id_documento']; ?>" class="btn btn-primary"><i class="fa fa-upload"></i> Subir archivo digital</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php echo View::factory('documentos/visor_pdf'); ?>

<script>
    $(function () {
        // los modales se mueven al body para quedar encima de todo
        $(document).on('click', '[data-toggle="modal"][data-target^="#tr-"]', function () {
            $($(this).attr('data-target')).appendTo('body').modal('show');
            return false;
        });
        $('.btn-revertir-hr-trace').click(function (e) {
            e.preventDefault();
            var nur = $(this).data('nur');
            if (confirm('¿Está seguro de REVERTIR la última derivación de la hoja de ruta ' + nur + '?\n\nEl documento volverá al estado anterior a esa derivación, para que pueda ser derivado nuevamente.')) {
                $(this).closest('form').submit();
            }
        });
        // filtro de pasos: todos / oficiales / copias
        $('.tr-filtro').click(function () {
            var f = $(this).attr('data-filtro');
            $('.tr-filtro').removeClass('activo');
            $(this).addClass('activo');
            $('.tr-paso').each(function () {
                var copia = $(this).hasClass('tr-copia');
                $(this).toggle(f === 'todos' || (f === 'copias' ? copia : !copia));
            });
        });
        // mapa del recorrido: desplazamiento suave hasta el paso y resaltado breve
        $('.tr-nodo').click(function (e) {
            var $destino = $($(this).attr('href'));
            if (!$destino.length) {
                return;
            }
            e.preventDefault();
            if (!$destino.is(':visible')) {
                $('.tr-filtro[data-filtro="todos"]').click();
            }
            $('html, body').animate({scrollTop: $destino.offset().top - 120}, 500);
            $destino.addClass('tr-destello');
            setTimeout(function () { $destino.removeClass('tr-destello'); }, 1600);
        });
        // lleva al paso donde esta ahora la hoja de ruta
        var $actual = $('.tr-paso[data-actual="1"]');
        if ($actual.length) {
            $('html, body').animate({scrollTop: $actual.offset().top - 120}, 800);
        }
    });
</script>
