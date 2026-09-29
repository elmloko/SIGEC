<?php
$es_ventanilla = ($user->id == '255');
$editando = isset($editando) && $editando;

// preseleccionar al destinatario del documento (si esta en la lista)
$nombre_usuario_destinatario = $documento->nombre_destinatario;
$posicion_usuario_destinatario = 0;
foreach ($destinatarios as $id_usuario => $usuario) {
    if ($nombre_usuario_destinatario != '' && strpos($usuario, $nombre_usuario_destinatario) !== FALSE) {
        break;
    }
    $posicion_usuario_destinatario++;
}
if ($posicion_usuario_destinatario >= count($destinatarios)) {
    $posicion_usuario_destinatario = 0;
}

// ¿el usuario puede poner fecha de plazo al destinatario preseleccionado?
$usuarioPuedePonerPlazo = 0;
$ids_destinatarios = array_keys($destinatarios);
if (isset($ids_destinatarios[$posicion_usuario_destinatario])) {
    $habilitado = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM usuarios_habilitados_plazos WHERE id_usuario_padre = :padre AND id_usuario_hijo = :hijo')
            ->param(':padre', (int) $user->id)
            ->param(':hijo', (int) $ids_destinatarios[$posicion_usuario_destinatario])
            ->execute()->get('n');
    // solo se asigna plazo una vez por hoja de ruta
    $con_plazo = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM alertas a INNER JOIN seguimiento s ON a.id_seguimiento = s.id WHERE s.nur = :nur')
            ->param(':nur', (string) $documento->nur)
            ->execute()->get('n');
    if ($habilitado > 0 && $con_plazo == 0) {
        $usuarioPuedePonerPlazo = 1;
    }
}

// archivos digitales del documento original
$archivos_doc = ORM::factory('archivos')->where('id_documento', '=', $documento->id)->and_where('estado', '=', 1)->find_all();
$es_copia = ($oficial == 0);

// derivaciones que este usuario ya hizo desde este paso (p.ej. copias enviadas antes de recargar)
$previas = DB::query(Database::SELECT, 'SELECT s.id, s.oficial, s.derivado_a, s.nombre_receptor, s.cargo_receptor, s.proveido, s.estado, a.accion
        FROM seguimiento s LEFT JOIN acciones a ON a.id = s.accion
        WHERE s.nur = :nur AND s.id_seguimiento = :id AND s.derivado_por = :user ORDER BY s.id')
        ->param(':nur', (string) $documento->nur)->param(':id', (int) $id_seguimiento)->param(':user', (int) $user->id)
        ->execute()->as_array();
// ¿ya se derivo la oficial desde este paso? (entonces solo quedan copias)
$ya_oficial = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM seguimiento WHERE nur = :nur AND id_seguimiento = :id AND oficial > 0')
        ->param(':nur', (string) $documento->nur)->param(':id', (int) $id_seguimiento)
        ->execute()->get('n') > 0;
?>
<style type="text/css">
    .dr-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .dr-card .btn {
        margin: 0;
    }
    .dr-card .btn .fa {
        margin-right: 5px;
    }

    /* ===== cabecera ===== */
    .dr-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 16px 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .dr-cab-icono {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: var(--correos-azul, #1A549A);
        color: #fff;
    }
    .dr-cab-titulo {
        flex: 1 1 260px;
        min-width: 0;
    }
    .dr-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-cab h2 span {
        color: var(--correos-azul, #1A549A);
    }
    .dr-cab small {
        font-size: 12px;
        color: #7a8594;
    }
    .dr-cab small b {
        color: var(--correos-azul, #1A549A);
    }
    .dr-doc {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1fr);
        gap: 12px 22px;
        padding: 14px 22px 16px;
    }
    .dr-doc small {
        display: block;
        margin-bottom: 2px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .dr-doc b {
        display: block;
        color: #2d3748;
        line-height: 1.3;
    }
    .dr-doc span {
        font-size: 12px;
        color: #7a8594;
    }
    .dr-doc .dr-ref b {
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-archivos {
        grid-column: 1 / -1;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }
    .dr-archivos small {
        display: inline;
        margin-right: 4px;
    }
    .dr-archivo {
        max-width: 100%;
        padding: 4px 10px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .dr-archivo:hover {
        text-decoration: none;
        border-color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .dr-archivo .fa {
        color: #d32f2f;
        margin-right: 3px;
    }
    .dr-sin-archivo {
        font-size: 12px;
        color: #B7791F;
    }

    /* ===== pasos del flujo ===== */
    .dr-flujo {
        display: flex;
        align-items: stretch;
        margin-bottom: 18px;
        padding: 0;
        list-style: none;
    }
    .dr-flujo li {
        position: relative;
        flex: 1 1 0;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 12px 16px 12px 26px;
        background: #fff;
        color: #9aa4b2;
        font-size: 12px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .10);
    }
    .dr-flujo li:first-child {
        padding-left: 16px;
        border-radius: 12px 0 0 12px;
    }
    .dr-flujo li:last-child {
        border-radius: 0 12px 12px 0;
    }
    /* flecha entre pasos */
    .dr-flujo li:not(:last-child)::after {
        content: '';
        position: absolute;
        z-index: 1;
        right: -11px;
        top: 50%;
        width: 22px;
        height: 22px;
        background: inherit;
        border-top: 2px solid #EEF2F7;
        border-right: 2px solid #EEF2F7;
        transform: translateY(-50%) rotate(45deg);
    }
    .dr-flujo b {
        display: block;
        font-size: 13px;
        color: #7a8594;
    }
    .dr-flujo-num {
        flex: 0 0 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        border: 2px solid #DCE3EC;
        background: #fff;
    }
    .dr-flujo li.hecho .dr-flujo-num {
        border-color: #2E9E5B;
        background: #2E9E5B;
        color: #fff;
    }
    .dr-flujo li.hecho b {
        color: #227547;
    }
    .dr-flujo li.actual {
        background: var(--correos-azul, #1A549A);
        color: rgba(255, 255, 255, .8);
    }
    .dr-flujo li.actual::after {
        border-color: var(--correos-azul, #1A549A);
    }
    .dr-flujo li.actual b {
        color: #fff;
    }
    .dr-flujo li.actual .dr-flujo-num {
        border-color: var(--correos-amarillo, #FECB34);
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-flujo em {
        display: block;
        font-style: normal;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dr-flujo li.omitido b {
        text-decoration: line-through;
    }

    /* ===== diseño de dos columnas ===== */
    .dr-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 18px;
        align-items: start;
    }

    /* ===== formulario del paso actual ===== */
    .dr-paso-cab {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid #EEF2F7;
    }
    .dr-paso-num {
        flex: 0 0 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 800;
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-paso-cab h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-paso-cab p {
        margin: 1px 0 0;
        font-size: 12.5px;
        color: #6b7686;
    }
    .dr-form {
        padding: 18px 22px 4px;
    }
    .dr-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 14px 18px;
    }
    .dr-completo {
        grid-column: 1 / -1;
    }
    .dr-campo label.dr-label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
    }
    .dr-req {
        color: #D32F2F;
    }
    .dr-campo textarea,
    .dr-campo input[type=text] {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        font-size: 14px;
        color: #2d3748;
        box-shadow: none;
    }
    .dr-campo input[type=text] {
        height: 38px;
    }
    .dr-campo textarea {
        min-height: 76px;
        resize: vertical;
    }
    .dr-campo textarea:focus,
    .dr-campo input[type=text]:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .12);
    }
    .dr-campo .select2-container {
        width: 100% !important;
    }
    .dr-ayuda {
        display: flex;
        justify-content: space-between;
        margin-top: 3px;
        font-size: 11px;
        color: #9aa4b2;
    }
    .dr-urgente {
        display: flex;
        align-items: center;
        gap: 10px;
        height: 38px;
        margin: 0;
        padding: 0 12px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        cursor: pointer;
        user-select: none;
        font-weight: 600;
        color: #4a5568;
    }
    .dr-urgente input {
        width: 16px;
        height: 16px;
        margin: 0;
        cursor: pointer;
    }
    .dr-urgente.activo {
        border-color: #D32F2F;
        background: #FDE8E8;
        color: #B42318;
    }
    .dr-frases {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }
    .dr-frase {
        padding: 3px 10px;
        border: 1px dashed var(--correos-borde, #DCE3EC);
        border-radius: 14px;
        background: #fff;
        font-size: 11px;
        color: #5b6675;
        cursor: pointer;
    }
    .dr-frase:hover {
        border-style: solid;
        border-color: var(--correos-azul, #1A549A);
        color: var(--correos-azul, #1A549A);
    }
    #dr-errores {
        display: none;
        margin: 14px 0 0;
        padding: 10px 14px;
        border-radius: 8px;
        background: #FDE8E8;
        color: #B42318;
        font-size: 13px;
    }
    #dr-errores ul {
        margin: 4px 0 0;
        padding-left: 18px;
    }
    .dr-acciones {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 18px;
        padding: 14px 22px;
        border-top: 1px solid #EEF2F7;
        background: #FAFBFC;
        border-radius: 0 0 12px 12px;
    }
    .dr-acciones .dr-nota {
        flex: 1 1 200px;
        font-size: 12px;
        color: #7a8594;
    }
    .dr-acciones .btn-lg {
        padding: 10px 20px;
        font-size: 14px;
    }
    .dr-aviso {
        margin: 14px 22px 0;
        padding: 10px 14px;
        border-radius: 8px;
        background: var(--correos-amarillo-suave, #FFF7DD);
        font-size: 13px;
        color: #8a6100;
    }

    /* ===== resumen ===== */
    .dr-resumen-cab {
        padding: 14px 18px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .dr-resumen-cab h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-resumen-cab p {
        margin: 2px 0 0;
        font-size: 12px;
        color: #7a8594;
    }
    .dr-bloque {
        padding: 14px 18px;
        border-bottom: 1px solid #EEF2F7;
    }
    .dr-bloque-titulo {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        color: #4a5568;
    }
    .dr-bloque-titulo em {
        font-style: normal;
        font-weight: 600;
        text-transform: none;
        color: #9aa4b2;
    }
    .dr-contador {
        min-width: 20px;
        padding: 0 7px;
        border-radius: 10px;
        background: #EEF2F7;
        color: #4a5568;
        font-size: 11px;
        text-align: center;
    }
    .dr-hueco {
        padding: 14px;
        border: 2px dashed var(--correos-borde, #DCE3EC);
        border-radius: 10px;
        text-align: center;
        font-size: 12px;
        color: #9aa4b2;
    }
    .dr-hueco .fa {
        display: block;
        margin-bottom: 4px;
        font-size: 20px;
        color: #c5ccd6;
    }
    .dr-fila {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        border-radius: 10px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        background: #fff;
    }
    .dr-fila:last-child {
        margin-bottom: 0;
    }
    .dr-fila.oficial1 {
        border-color: var(--correos-azul, #1A549A);
        border-left-width: 4px;
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .dr-fila-ok {
        flex: 0 0 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #E6F4EC;
        color: #2E9E5B;
    }
    .dr-fila-info {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 12px;
        color: #6b7686;
    }
    .dr-fila-info b {
        display: block;
        font-size: 13px;
        color: #2d3748;
    }
    .dr-fila-info .dr-prov {
        margin-top: 4px;
        font-style: italic;
        color: #4a5568;
        word-break: break-word;
    }
    .dr-tag {
        display: inline-block;
        margin: 3px 3px 0 0;
        padding: 1px 7px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #8a6100;
    }
    .dr-tag.urgente {
        background: #FDE8E8;
        color: #B42318;
    }
    .dr-quitar {
        flex: 0 0 auto;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9aa4b2;
    }
    .dr-quitar:hover {
        text-decoration: none;
        background: #FDE8E8;
        color: #D32F2F;
    }
    .dr-resumen-pie {
        padding: 14px 18px;
    }
    .dr-resumen-pie .btn {
        display: block;
        width: 100%;
        margin: 0 0 8px;
    }
    .dr-resumen-pie p {
        margin: 0;
        font-size: 11.5px;
        text-align: center;
        color: #9aa4b2;
    }
    /* cerrar copia (enterado) */
    .dr-enterado {
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .dr-enterado h4 {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .dr-enterado p {
        margin: 0 0 10px;
        font-size: 12px;
        color: #6b5a1e;
    }
    .dr-enterado input {
        width: 100%;
        height: 34px;
        margin-bottom: 8px;
        padding: 4px 10px;
        border: 1px solid #EBD58F;
        border-radius: 8px;
        font-size: 13px;
    }
    .dr-enterado .btn {
        display: block;
        width: 100%;
        margin: 0;
    }
    .dr-cerrada {
        padding: 18px;
        text-align: center;
        color: #227547;
    }
    .dr-cerrada .fa {
        display: block;
        margin-bottom: 6px;
        font-size: 34px;
    }
    .dr-cerrada b {
        display: block;
        font-size: 15px;
    }
    .dr-editando {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        border-left: 5px solid var(--correos-amarillo, #FECB34);
        font-size: 13px;
        color: #4a5568;
    }
    .dr-editando > .fa {
        flex: 0 0 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #B7791F;
        font-size: 16px;
    }
    .dr-editando b:first-child {
        display: block;
        color: var(--correos-azul-oscuro, #123E73);
    }
    @media (min-width: 1200px) {
        .dr-resumen {
            position: sticky;
            top: 80px;
        }
    }

    /* capa "derivando..." */
    #dr-capa {
        display: none;
        position: fixed;
        z-index: 3000;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(18, 62, 115, .35);
        align-items: center;
        justify-content: center;
    }
    #dr-capa div {
        padding: 18px 26px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .2);
        font-size: 15px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    #dr-capa .fa {
        margin-right: 8px;
        color: var(--correos-azul, #1A549A);
    }
    @media (max-width: 1199px) {
        .dr-layout {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 767px) {
        .dr-doc,
        .dr-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .dr-flujo {
            flex-direction: column;
        }
        .dr-flujo li,
        .dr-flujo li:first-child,
        .dr-flujo li:last-child {
            padding-left: 16px;
            border-radius: 0;
        }
        .dr-flujo li::after {
            display: none;
        }
    }
</style>

<!-- capa mientras se deriva -->
<div id="dr-capa"><div><i class="fa fa-circle-o-notch fa-spin"></i> <span id="dr-capa-texto">Derivando...</span></div></div>

<?php if (sizeof($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $k => $v): ?>
            <p><strong><?php echo $k; ?>:</strong> <?php echo $v; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- documento que se deriva -->
<div class="dr-card">
    <div class="dr-cab">
        <div class="dr-cab-icono"><i class="fa fa-paper-plane"></i></div>
        <div class="dr-cab-titulo">
            <h2><?php echo $editando ? 'Editar derivación' : 'Derivar'; ?> <span><?php echo HTML::chars($documento->nur); ?></span></h2>
            <small>Cite original: <b><?php echo HTML::chars($documento->cite_original); ?></b>
                <?php if ($proceso && $proceso->loaded()): ?> &middot; Proceso: <?php echo HTML::chars($proceso->proceso); ?><?php endif; ?>
            </small>
        </div>
        <?php if ($documento->estado == 0): ?>
            <a href="/print/hr/?code=<?php echo urlencode($documento->nur); ?>&amp;p=1" target="_blank" class="btn btn-sm btn-default-bright">
                <i class="fa fa-print"></i> Imprimir hoja de ruta</a>
        <?php endif; ?>
        <a href="/route/trace/?hr=<?php echo urlencode($documento->nur); ?>" class="btn btn-sm btn-default-bright">
            <i class="fa fa-map-marker"></i> Seguimiento</a>
    </div>
    <div class="dr-doc">
        <div class="dr-ref">
            <small>Referencia</small>
            <b><?php echo HTML::chars($documento->referencia != '' ? $documento->referencia : 'Sin referencia'); ?></b>
        </div>
        <div>
            <small>De</small>
            <b><?php echo HTML::chars($documento->nombre_remitente); ?></b>
            <span><?php echo HTML::chars($documento->cargo_remitente); ?></span>
        </div>
        <div>
            <small>Dirigido a</small>
            <b><?php echo HTML::chars($documento->nombre_destinatario); ?></b>
            <span><?php echo HTML::chars($documento->cargo_destinatario); ?></span>
        </div>
        <div class="dr-archivos">
            <small>Archivo digital</small>
            <?php if (count($archivos_doc) == 0): ?>
                <span class="dr-sin-archivo"><i class="fa fa-exclamation-triangle"></i> El documento no tiene PDF adjunto</span>
            <?php else: ?>
                <?php foreach ($archivos_doc as $a): $nom = substr($a->nombre_archivo, 13); ?>
                    <a href="#" class="dr-archivo visor-pdf" title="<?php echo HTML::chars($nom); ?>"
                       data-url="/download/?file=<?php echo (int) $a->id; ?>&amp;ver=1" data-descargar="/download/?file=<?php echo (int) $a->id; ?>"
                       data-nombre="<?php echo HTML::chars($nom); ?>"><i class="fa fa-file-pdf-o"></i> <?php echo HTML::chars($nom); ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($editando): ?>
    <div class="dr-card dr-editando">
        <i class="fa fa-pencil"></i>
        <div><b>Está corrigiendo una derivación que todavía nadie recibió.</b>
            Puede <b>agregar</b> a las personas que olvidó o <b>cancelar</b> (✕) a quien no correspondía. Cuando alguien la reciba, ya no podrá cambiarse.</div>
    </div>
<?php endif; ?>

<!-- pasos del flujo -->
<ol class="dr-flujo" id="dr-flujo">
    <li class="hecho" data-paso="1">
        <span class="dr-flujo-num"><i class="fa fa-check"></i></span>
        <span><b>Documento</b>Revisado y listo</span>
    </li>
    <li data-paso="2">
        <span class="dr-flujo-num">2</span>
        <span><b>Oficial</b><em class="dr-flujo-sub">Quién la atiende</em></span>
    </li>
    <li data-paso="3">
        <span class="dr-flujo-num">3</span>
        <span><b>Copias</b><em class="dr-flujo-sub">Opcional</em></span>
    </li>
    <li data-paso="4">
        <span class="dr-flujo-num">4</span>
        <span><b>Terminar</b><em class="dr-flujo-sub">Volver a la bandeja</em></span>
    </li>
</ol>

<div class="dr-layout">
    <!-- formulario del paso actual -->
    <div class="dr-card">
        <form action="/route/derivando/?nur=<?php echo urlencode($documento->nur); ?>" method="post" class="form" id="frmDerivar" onsubmit="return false;">
            <input type="hidden" value="<?php echo HTML::chars($hijo); ?>" name="hijo" id="hijo"/>
            <input type="hidden" value="<?php echo HTML::chars($documento->nur); ?>" name="nur" id="nur"/>
            <input type="hidden" value="<?php echo (int) $id_seguimiento; ?>" name="id_seg" id="id_seg"/>
            <input type="hidden" value="<?php echo HTML::chars($oficial); ?>" name="oficial" id="oficial"/>
            <input type="hidden" value="<?php echo HTML::chars($documento->estado); ?>" name="estado" id="estado"/>
            <input type="hidden" value="<?php echo (int) $user->id; ?>" name="user" id="user"/>
            <input type="hidden" value="<?php echo (int) $documento->id; ?>" name="document" id="document"/>
            <input type="hidden" value="<?php echo $posicion_usuario_destinatario; ?>" name="posicion_usuario_destinatario" id="posicion_usuario_destinatario"/>
            <input type="hidden" value="<?php echo $usuarioPuedePonerPlazo; ?>" name="usuario_puede_poner_plazo" id="usuario_puede_poner_plazo"/>
            <?php echo Form::hidden('tipo', '', array('id' => 'tipo')); ?>

            <div class="dr-paso-cab">
                <span class="dr-paso-num" id="dr-paso-num">2</span>
                <div>
                    <h3 id="dr-paso-titulo">¿Quién debe atender esta hoja de ruta?</h3>
                    <p id="dr-paso-texto">Elija a la persona que la recibirá como <b>oficial</b>. Solo puede haber una.</p>
                </div>
            </div>

            <?php if ($es_copia): ?>
                <div class="dr-aviso"><i class="fa fa-info-circle"></i> Usted recibió esta hoja de ruta como <b>copia</b>: solo puede derivarla como copia.</div>
            <?php endif; ?>

            <div class="dr-form">
                <div class="dr-grid">
                    <div class="dr-campo">
                        <label class="dr-label" for="destino">Derivar a <span class="dr-req">*</span></label>
                        <?php echo Form::select('destino', $destinatarios, Arr::get($_POST, 'destino', NULL), array('id' => 'destino', 'class' => 'required')); ?>
                    </div>
                    <div class="dr-campo" <?php echo $es_ventanilla ? 'style="display:none"' : ''; ?>>
                        <label class="dr-label">Prioridad</label>
                        <label class="dr-urgente" id="dr-urgente">
                            <input type="checkbox" value="1" id="checkbox_urgente"/> <i class="fa fa-bolt"></i> Urgente
                        </label>
                    </div>

                    <div class="dr-campo">
                        <label class="dr-label" for="accion">Acción <span class="dr-req">*</span></label>
                        <?php echo Form::select('accion', $acciones, Arr::get($_POST, 'accion', NULL), array('class' => 'required', 'id' => 'accion')); ?>
                    </div>
                    <div class="dr-campo" id="contenedor_calendario" <?php echo (!$es_ventanilla && $usuarioPuedePonerPlazo) ? '' : 'style="display:none"'; ?>>
                        <label class="dr-label" for="fecha">Fecha máxima de respuesta <span class="dr-req">*</span></label>
                        <div id="fecha-resp">
                            <input id="fecha" type="text" name="fecha" autocomplete="off" placeholder="dd/mm/aaaa"
                                   title="Fecha máxima de respuesta en caso de solicitar un informe o nota interna"/>
                        </div>
                    </div>

                    <div class="dr-campo dr-completo" <?php echo $es_ventanilla ? 'style="display:none"' : ''; ?>>
                        <label class="dr-label" for="proveido">Proveído <span class="dr-req">*</span></label>
                        <?php echo Form::textarea('proveido', Arr::get($_POST, 'proveido', ''), array('rows' => 3, 'class' => 'required', 'id' => 'proveido', 'placeholder' => 'Instrucción para quien recibe, por ejemplo: Para su conocimiento y fines consiguientes.')); ?>
                        <div class="dr-ayuda"><span>Frases frecuentes (clic para usar):</span><span><span id="dr-prov-contador">0</span> caracteres</span></div>
                        <div class="dr-frases">
                            <?php foreach (array('Para su conocimiento y fines consiguientes.', 'Favor atender.', 'Favor revisar y emitir informe.', 'Para su consideración.', 'Para su VoBo.', 'Para su archivo.') as $frase): ?>
                                <button type="button" class="dr-frase"><?php echo HTML::chars($frase); ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div id="dr-errores"></div>
            </div>

            <div class="dr-acciones">
                <?php if (!$es_copia): ?>
                    <a href="#" id="dOficial" class="btn btn-primary btn-lg"><i class="fa fa-paper-plane"></i> Derivar como oficial</a>
                <?php endif; ?>
                <a href="#" id="dCopia" class="btn btn-default-bright btn-lg"><i class="fa fa-copy"></i> <span id="dr-copia-texto">Enviar solo copia</span></a>
                <span class="dr-nota" id="dr-nota">La <b>oficial</b> es quien debe atenderla. Las <b>copias</b> son solo para conocimiento.</span>
            </div>
        </form>
    </div>

    <!-- resumen de la derivacion -->
    <div class="dr-card dr-resumen">
        <div class="dr-resumen-cab">
            <h3><i class="fa fa-list-ul"></i> Resumen de la derivación</h3>
            <p>A quiénes se envía esta hoja de ruta</p>
        </div>
        <div class="dr-bloque">
            <div class="dr-bloque-titulo"><i class="fa fa-paper-plane"></i> Oficial <em>&middot; quién la atiende</em></div>
            <div id="dr-lista-oficial"></div>
            <div class="dr-hueco" id="dr-hueco-oficial">
                <i class="fa fa-user"></i>
                <?php echo $es_copia ? 'No corresponde: usted recibió una copia' : 'Todavía no eligió a quién se deriva como oficial'; ?>
            </div>
        </div>
        <div class="dr-bloque">
            <div class="dr-bloque-titulo"><i class="fa fa-copy"></i> Copias <em>&middot; para conocimiento</em> <span class="dr-contador" id="dr-contador-copias">0</span></div>
            <div id="dr-lista-copias"></div>
            <div class="dr-hueco" id="dr-hueco-copias"><i class="fa fa-copy"></i> Sin copias (opcional)</div>
        </div>
        <?php if ($es_copia && !$es_ventanilla): ?>
            <div class="dr-bloque dr-enterado" id="dr-enterado">
                <h4><i class="fa fa-eye"></i> ¿Solo necesitaba conocerla?</h4>
                <p>Si no tiene que enviarla a nadie más, márquela como <b>Enterado</b>: sale de sus pendientes y queda archivada en la carpeta de copias de su oficina.</p>
                <input type="text" id="dr-enterado-obs" maxlength="200" placeholder="Observación (opcional)" autocomplete="off"/>
                <a href="#" class="btn btn-success" id="dr-enterado-btn"><i class="fa fa-check"></i> Enterado · cerrar copia</a>
            </div>
        <?php endif; ?>
        <div class="dr-resumen-pie">
            <a href="/bandeja" class="btn btn-primary" id="dr-terminar"><i class="fa fa-check"></i> Terminar y volver a la bandeja</a>
            <a href="/route/trace/?hr=<?php echo urlencode($documento->nur); ?>" class="btn btn-default-bright" id="dr-ver-seg"><i class="fa fa-map-marker"></i> Ver seguimiento</a>
            <p id="dr-pie-texto">Cuando termine de agregar destinatarios, pulse <b>Terminar</b>.</p>
        </div>
    </div>
</div>

<?php echo View::factory('documentos/visor_pdf'); ?>

<script type="text/javascript">
    // posicion inicial del destinatario (antes de aplicar select2)
    document.getElementById('destino').selectedIndex = <?php echo (int) $posicion_usuario_destinatario; ?>;
    <?php if ($es_ventanilla): ?>
    // ventanilla solo deriva "Para su conocimiento" (indice 4)
    document.getElementById('accion').selectedIndex = 4;
    document.getElementById('accion').disabled = true;
    <?php endif; ?>

    var DR_ES_COPIA = <?php echo $es_copia ? 'true' : 'false'; ?>;
    var DR_YA_OFICIAL = <?php echo $ya_oficial ? 'true' : 'false'; ?>;

    function visible(text) {
        $('#dr-capa-texto').text(text || 'Procesando...');
        $('#dr-capa').css('display', 'flex');
    }
    function ocultar() {
        $('#dr-capa').hide();
    }
    function escapar(t) {
        return $('<div>').text(t == null ? '' : String(t)).html();
    }
    function mostrarErrores(errores) {
        if (!errores.length) {
            $('#dr-errores').hide().empty();
            return;
        }
        var html = '<i class="fa fa-exclamation-triangle"></i> <b>Revise antes de derivar:</b><ul>';
        $.each(errores, function (i, e) {
            html += '<li>' + escapar(e) + '</li>';
        });
        $('#dr-errores').html(html + '</ul>').show();
    }

    // tarjeta de una persona en el resumen (cancelable si se derivo en esta pantalla)
    function tarjeta(d) {
        var html = '<div class="dr-fila oficial' + (d.oficial ? '1' : '0') + '">' +
            '<span class="dr-fila-ok"><i class="fa fa-check"></i></span>' +
            '<div class="dr-fila-info"><b>' + escapar(d.nombre) + '</b>' + escapar(d.cargo) + '<br/>' +
            (d.accion ? '<span class="dr-tag">' + escapar(d.accion) + '</span>' : '') +
            (d.urgente ? '<span class="dr-tag urgente">Urgente</span>' : '') +
            (d.proveido ? '<div class="dr-prov">“' + escapar(d.proveido) + '”</div>' : '') +
            (d.adjuntos ? '<div><i class="fa fa-paperclip"></i> ' + d.adjuntos + '</div>' : '') +
            (d.recibido ? '<div style="color:#2E9E5B"><i class="fa fa-check"></i> Ya la recibió</div>' : (d.cancelable && !d.nuevo ? '<div style="color:#B7791F"><i class="fa fa-clock-o"></i> No recibido: puede cancelarse</div>' : '')) +
            '</div>';
        if (d.cancelable) {
            html += '<a href="javascript:;" onclick="activar($(this));" class="dr-quitar" title="Cancelar esta derivación"' +
                ' id="' + escapar(d.id) + '" destino="' + escapar(d.id_destino) + '" oficial="' + (d.oficial ? '1' : '0') + '"><i class="fa fa-times"></i></a>';
        }
        return html + '</div>';
    }

    // actualiza los pasos, el titulo del formulario y los botones segun lo derivado
    function actualizarFlujo() {
        var hayOficial = DR_YA_OFICIAL || $('#dr-lista-oficial .dr-fila').length > 0;
        var nCopias = $('#dr-lista-copias .dr-fila').length;
        var soloCopias = hayOficial || DR_ES_COPIA;
        var algo = hayOficial || nCopias > 0;

        $('#dr-hueco-oficial').toggle(!$('#dr-lista-oficial .dr-fila').length);
        $('#dr-hueco-copias').toggle(nCopias === 0);
        $('#dr-contador-copias').text(nCopias);

        var $p = $('#dr-flujo li');
        $p.eq(1).attr('class', DR_ES_COPIA ? 'omitido' : (hayOficial ? 'hecho' : 'actual'))
            .find('.dr-flujo-num').html(hayOficial ? '<i class="fa fa-check"></i>' : '2');
        $p.eq(1).find('.dr-flujo-sub').text(DR_ES_COPIA ? 'No corresponde' : (hayOficial ? 'Asignada' : 'Quién la atiende'));
        $p.eq(2).attr('class', soloCopias ? (nCopias > 0 ? 'hecho' : 'actual') : '')
            .find('.dr-flujo-num').html(soloCopias && nCopias > 0 ? '<i class="fa fa-check"></i>' : '3');
        $p.eq(2).find('.dr-flujo-sub').text(nCopias > 0 ? nCopias + (nCopias === 1 ? ' enviada' : ' enviadas') : 'Opcional');
        $p.eq(3).attr('class', algo ? 'actual' : '');
        if (DR_ES_COPIA) {
            $p.eq(3).find('b').text(nCopias > 0 ? 'Terminar' : 'Enterado');
            $p.eq(3).find('.dr-flujo-sub').text(nCopias > 0 ? 'Volver a la bandeja' : 'O derive una copia');
            if (!algo) {
                $p.eq(3).attr('class', 'actual');
            }
            // al derivar una copia, la copia recibida ya queda cerrada
            $('#dr-enterado').toggle(nCopias === 0);
        }

        // formulario: paso 2 (oficial) o paso 3 (copias)
        $('#dOficial').toggle(!soloCopias);
        if (soloCopias) {
            $('#dr-paso-num').text('3');
            $('#dr-paso-titulo').text('¿A quién más desea informar?');
            $('#dr-paso-texto').html(DR_ES_COPIA && nCopias === 0
                ? 'Envíe <b>copias</b> a otras personas si corresponde. Si solo necesitaba conocerla, use <b>Enterado</b> en el resumen para cerrarla.'
                : 'Opcional: envíe <b>copias</b> a otras personas para su conocimiento. Si no necesita, pulse <b>Terminar</b>.');
            $('#dr-copia-texto').text('Enviar copia');
            $('#dCopia').removeClass('btn-default-bright').addClass('btn-primary');
            $('#dr-nota').html(hayOficial ? '<i class="fa fa-check-circle" style="color:#2E9E5B"></i> La <b>oficial</b> ya fue asignada: desde ahora solo se envían copias.' : 'Solo puede enviar copias.');
        } else {
            $('#dr-paso-num').text('2');
            $('#dr-paso-titulo').text('¿Quién debe atender esta hoja de ruta?');
            $('#dr-paso-texto').html('Elija a la persona que la recibirá como <b>oficial</b>. Solo puede haber una.');
            $('#dr-copia-texto').text('Enviar solo copia');
            $('#dCopia').removeClass('btn-primary').addClass('btn-default-bright');
            $('#dr-nota').html('La <b>oficial</b> es quien debe atenderla. Las <b>copias</b> son solo para conocimiento.');
        }

        // pie del resumen
        $('#dr-terminar').toggle(algo);
        $('#dr-pie-texto').html(algo ? 'Cuando termine de agregar destinatarios, pulse <b>Terminar</b>.' : 'Aquí verá a las personas a medida que derive.');
    }
    // compatibilidad con el nombre usado antes
    function actualizarLista() {
        actualizarFlujo();
    }

    // adicionar un destinatario
    function ajaxs(oficial) {
        var hijo = $('#hijo').val();
        var destinatario = $('#destino').val();
        var accion = $('#accion').val();
        var accion_texto = $('#accion option:selected').text();
        var proveido = $('#proveido').val();
        var user = $('#user').val();
        var adjunto = $('#adjunto').val();
        var id_seg = $('#id_seg').val();
        var estado = $('#estado').val();
        var document = $('#document').val();
        var tipo = $('#oficial').val();
        var fecha = $('#fecha').val();
        var valor_numerico_urgente = $('#checkbox_urgente')[0].checked ? 1 : 0;
        var usuario_puede_poner_plazo = $('#usuario_puede_poner_plazo').val();
        var errores = [];

        if (user != 255) {
            if ($.trim(proveido).length <= 0) {
                errores.push("Escriba el proveído (la instrucción para quien recibe).");
            }
            if (!destinatario || $("#destino option:selected").text().length <= 0) {
                errores.push("Elija a quién se deriva.");
            }
            if (usuario_puede_poner_plazo == 1) {
                if (fecha.length <= 0) {
                    errores.push("Ingrese la fecha máxima de respuesta.");
                } else {
                    var p = fecha.split('/');
                    var hoy = new Date();
                    var fecha_plazo = Date.parse(p[1] + "/" + p[0] + "/" + p[2]);
                    var fecha_hoy = Date.parse((hoy.getMonth() + 1) + "/" + hoy.getDate() + "/" + hoy.getFullYear());
                    if (fecha_plazo < fecha_hoy) {
                        errores.push("La fecha máxima de respuesta no puede ser anterior a hoy.");
                    }
                }
            }
        }
        mostrarErrores(errores);
        if (errores.length) {
            return;
        }
        if (adjunto == null) {
            adjunto = 0;
        }

        var nur = $('#nur').val();
        visible(oficial ? 'Derivando como oficial...' : 'Enviando copia...');
        $.ajax({
            type: "POST",
            data: {
                tipo: tipo,
                oficial: oficial,
                fecha: fecha,
                destino: destinatario,
                adjunto: adjunto,
                document: document,
                nur: nur,
                accion: accion,
                proveido: proveido,
                hijo: hijo,
                user: user,
                id_seg: id_seg,
                estado: estado,
                urgente: valor_numerico_urgente
            },
            url: "/ajax/derivar",
            dataType: "json",
            success: function (item) {
                ocultar();
                if (item.id) {
                    var adjuntos = [];
                    $.each(item.adjunto || [], function (k, v) {
                        adjuntos.push(escapar(v));
                    });
                    var es_oficial = item.oficial != "0";
                    $(es_oficial ? '#dr-lista-oficial' : '#dr-lista-copias').append(tarjeta({
                        id: item.id, id_destino: item.id_destino, oficial: es_oficial,
                        nombre: item.receptor_nombre, cargo: item.receptor_cargo,
                        accion: accion_texto, urgente: valor_numerico_urgente, proveido: item.proveido,
                        adjuntos: adjuntos.join(', '), cancelable: true
                    }));
                    actualizarFlujo();
                    // listo para el siguiente destinatario
                    $('#proveido').val('').trigger('input');
                    $('#checkbox_urgente').prop('checked', false).trigger('change');
                } else {
                    mostrarErrores([item.error || 'No se pudo derivar.']);
                }
            },
            error: function () {
                ocultar();
                mostrarErrores(['No se pudo comunicar con el servidor. Revise su conexión e intente nuevamente.']);
            }
        });
    }
    function activar(link) {
        var $this = link;
        if (!confirm('¿Cancelar la derivación a esta persona?')) {
            return false;
        }
        visible('Cancelando derivación...');
        $.ajax({
            type: "POST",
            data: {id: $this.attr('id'), destino: $this.attr('destino'), oficial: $this.attr('oficial'), document: $('#document').val()},
            url: "/ajax/eliminar",
            dataType: "json",
            success: function (item) {
                ocultar();
                if (item && item.error) {
                    mostrarErrores([item.error]);
                    return;
                }
                if ($this.attr('oficial') != '0') {
                    DR_YA_OFICIAL = false;
                }
                $this.closest('.dr-fila').remove();
                actualizarFlujo();
            },
            error: function () {
                ocultar();
                mostrarErrores(['No se pudo cancelar la derivación. Intente nuevamente.']);
            }
        });
        return false;
    }

    $(function () {
        // derivaciones hechas antes desde este paso (solo lectura)
        <?php foreach ($previas as $pv): ?>
        $(<?php echo (int) $pv['oficial'] > 0 ? "'#dr-lista-oficial'" : "'#dr-lista-copias'"; ?>).append(tarjeta(<?php echo json_encode(array(
            'oficial' => (int) $pv['oficial'] > 0,
            'nombre' => $pv['nombre_receptor'],
            'cargo' => $pv['cargo_receptor'],
            'accion' => $pv['accion'],
            'proveido' => $pv['proveido'],
            'cancelable' => (int) $pv['estado'] === 1,
            'id' => (int) $pv['id'],
            'id_destino' => (int) $pv['derivado_a'],
            'recibido' => (int) $pv['estado'] !== 1,
        )); ?>));
        <?php endforeach; ?>

        $('#dr-enterado-btn').on('click', function () {
            if (!confirm('¿Marcar esta copia como Enterado? Saldrá de sus pendientes y quedará archivada.')) {
                return false;
            }
            visible('Cerrando copia...');
            $.ajax({
                type: 'POST',
                url: '/route/enterado',
                dataType: 'json',
                data: {id_seg: $('#id_seg').val(), observaciones: $('#dr-enterado-obs').val()},
                success: function (r) {
                    ocultar();
                    if (!r || !r.ok) {
                        mostrarErrores([(r && r.error) || 'No se pudo cerrar la copia.']);
                        return;
                    }
                    // ciclo cerrado: todos los pasos completos y sin mas acciones
                    $('#dr-flujo li').attr('class', 'hecho').find('.dr-flujo-num').html('<i class="fa fa-check"></i>');
                    $('#dr-flujo li').eq(3).find('b').text('Enterado');
                    $('#dr-flujo li').eq(3).find('.dr-flujo-sub').text('Copia cerrada');
                    $('#frmDerivar .dr-form, #frmDerivar .dr-acciones, #frmDerivar .dr-aviso').hide();
                    $('#dr-paso-num').html('<i class="fa fa-check"></i>');
                    $('#dr-paso-titulo').text('Copia cerrada');
                    $('#dr-paso-texto').html('La hoja de ruta salió de sus pendientes y se archivó en la carpeta <b>' + escapar(r.carpeta) + '</b>.');
                    $('#dr-enterado').replaceWith('<div class="dr-cerrada"><i class="fa fa-check-circle"></i><b>Copia cerrada</b>Archivada en ' + escapar(r.carpeta) + '</div>');
                    $('#dr-terminar').show().html('<i class="fa fa-inbox"></i> Volver a la bandeja');
                    $('#dr-pie-texto').text('');
                },
                error: function () {
                    ocultar();
                    mostrarErrores(['No se pudo comunicar con el servidor. Intente nuevamente.']);
                }
            });
            return false;
        });
        $('#fecha').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", startDate: new Date()});
        $('#dOficial').on('click', function () {
            ajaxs(1);
            return false;
        });
        $('#dCopia').on('click', function () {
            ajaxs(0);
            return false;
        });
        $('#checkbox_urgente').on('change', function () {
            $('#dr-urgente').toggleClass('activo', this.checked);
        });
        $('.dr-frase').on('click', function () {
            var $p = $('#proveido');
            var actual = $.trim($p.val());
            $p.val(actual ? actual + ' ' + $(this).text() : $(this).text()).trigger('input').focus();
        });
        $('#proveido').on('input', function () {
            $('#dr-prov-contador').text($(this).val().length);
            if ($.trim($(this).val()) !== '') {
                mostrarErrores([]);
            }
        }).trigger('input');
        $('#destino, #accion').select2();
        actualizarFlujo();

        // ¿el destinatario elegido admite fecha de plazo?
        $('#destino').on('change', function () {
            $.ajax({
                type: "GET",
                data: {
                    id_usuario_logueado: $("#user").val(),
                    id_usuario_destinatario: $("#destino").val(),
                    hoja_de_ruta: $('#nur').val()
                },
                url: "/ajax/jsonVerificarIdUsuarioPuedaAsignarPlazos/",
                success: function (response) {
                    var json = typeof response === 'string' ? $.parseJSON(response) : response;
                    var si = json && json[0] && json[0].respuesta === 'SI';
                    $('#usuario_puede_poner_plazo').val(si ? 1 : 0);
                    $('#contenedor_calendario').toggle(si);
                }
            });
        });
    });
</script>
