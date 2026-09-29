<?php
// resalta el texto buscado dentro de un valor ya escapado (el termino tambien se escapa)
$resaltar = function ($texto, $termino) {
    $seguro = HTML::chars((string) $texto);
    $termino = trim((string) $termino);
    if ($termino === '') {
        return $seguro;
    }
    return preg_replace('/' . preg_quote(HTML::chars($termino), '/') . '/iu', '<mark>$0</mark>', $seguro);
};
// iniciales para el avatar (maximo 2 letras)
$iniciales = function ($nombre) {
    $partes = preg_split('/\s+/u', trim((string) $nombre), -1, PREG_SPLIT_NO_EMPTY);
    $ini = '';
    foreach (array_slice($partes, 0, 2) as $p) {
        $ini .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8');
    }
    return $ini !== '' ? $ini : '?';
};
// icono segun el tipo de documento
$icono_tipo = function ($tipo) {
    $t = mb_strtolower((string) $tipo, 'UTF-8');
    $mapa = array('informe' => 'fa-file-text-o', 'memo' => 'fa-clipboard', 'circular' => 'fa-bullhorn', 'carta' => 'fa-envelope-o',
        'instructivo' => 'fa-list-ol', 'intructivo' => 'fa-list-ol', 'comunicado' => 'fa-comment-o', 'nota' => 'fa-pencil-square-o',
        'externo' => 'fa-inbox', 'resoluci' => 'fa-gavel', 'certificado' => 'fa-certificate');
    foreach ($mapa as $clave => $icono) {
        if (strpos($t, $clave) !== FALSE) {
            return $icono;
        }
    }
    return 'fa-file-o';
};
$hay_fechas = $filtros['start'] !== '' || $filtros['end'] !== '';
$hoy = date('Y-m-d');
?>
<style>
    .ba-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ba-card-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 20px;
        border-bottom: 2px solid var(--correos-amarillo, #FECB34);
    }
    .ba-card-head h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 500;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .ba-card-head h3 .fa {
        color: var(--correos-azul, #1A549A);
        margin-right: 6px;
    }
    .ba-form {
        padding: 18px 20px 20px;
    }
    /* grilla fija de 4 columnas: fila 1 identificacion, fila 2 referencia (doble) + personas */
    .ba-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px 18px;
    }
    .ba-campo.ba-doble {
        grid-column: span 2;
    }
    .ba-campo label .fa,
    .ba-campo label .md {
        width: 14px;
        margin-right: 3px;
        color: var(--correos-azul, #1A549A);
    }
    .ba-campo label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
    }
    .ba-campo input,
    .ba-campo select {
        width: 100%;
        height: 38px;
        padding: 6px 10px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
        background: #fff;
        font-size: 14px;
        color: #2d3748;
    }
    .ba-campo input:focus,
    .ba-campo select:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .12);
    }
    .ba-fechas {
        margin-top: 18px;
        padding: 14px 16px;
        border-radius: 8px;
        background: var(--correos-fondo, #F3F5F8);
    }
    .ba-fechas-fila {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
    }
    .ba-fechas .ba-campo {
        flex: 0 0 170px;
    }
    .ba-atajos-bloque label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
    }
    .ba-atajos-bloque .ba-atajos {
        min-height: 38px;
        align-items: center;
    }
    .ba-atajos {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .ba-atajo {
        padding: 5px 12px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 14px;
        background: #fff;
        font-size: 12px;
        color: #4a5568;
        cursor: pointer;
    }
    .ba-atajo:hover {
        border-color: var(--correos-azul, #1A549A);
    }
    .ba-atajo.activo {
        background: var(--correos-azul, #1A549A);
        border-color: var(--correos-azul, #1A549A);
        color: #fff;
    }
    .ba-nota-fecha {
        margin: 8px 0 0;
        font-size: 12px;
        color: #6b7686;
    }
    /* botones a la derecha de la franja de fechas */
    .ba-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-left: auto;
    }
    .ba-botones .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 38px;
        padding: 0 18px;
        line-height: 1;
    }
    @media (max-width: 1199px) {
        .ba-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 600px) {
        .ba-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .ba-campo.ba-doble {
            grid-column: auto;
        }
        .ba-fechas .ba-campo {
            flex: 1 1 140px;
        }
        .ba-botones {
            width: 100%;
            margin-left: 0;
        }
        .ba-botones .btn {
            flex: 1;
        }
    }
    .ba-botones .btn {
        margin: 0;
    }

    /* resultados */
    .ba-resultados-head {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 6px 14px;
        margin: 6px 0 14px;
    }
    .ba-resultados-head h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 500;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .ba-resultados-head h3 b {
        color: var(--correos-azul, #1A549A);
    }
    .ba-resultados-head small {
        font-size: 12px;
        color: #7a8594;
    }

    /* tarjeta: icono | contenido | acciones */
    .ba-item {
        display: grid;
        grid-template-columns: 52px minmax(0, 1fr) auto;
        gap: 0 18px;
        align-items: start;
        padding: 18px 20px;
        margin-bottom: 12px;
        background: #fff;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-left: 5px solid #2E9E5B;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(18, 62, 115, .05);
        transition: box-shadow .15s, transform .15s;
    }
    .ba-item.ba-item-no {
        border-left-color: #F2A900;
    }
    .ba-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(18, 62, 115, .10);
    }
    .ba-item-icono {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
    }
    .ba-item-main {
        min-width: 0;
    }
    .ba-item-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }
    .ba-tipo,
    .ba-estado {
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .ba-tipo {
        background: var(--correos-azul, #1A549A);
        color: #fff;
    }
    .ba-estado-no { background: #FFF3DC; color: #B26B00; }
    .ba-estado-si { background: #E6F4EC; color: #227547; }
    .ba-estado .fa {
        margin-right: 3px;
    }
    .ba-fecha {
        margin-left: auto;
        font-size: 12px;
        color: #7a8594;
        white-space: nowrap;
    }
    .ba-fecha b {
        color: #4a5568;
        font-weight: 600;
    }
    a.ba-titulo {
        display: block;
        margin: 2px 0 8px;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.35;
        color: var(--correos-azul-oscuro, #123E73);
    }
    a.ba-titulo:hover {
        color: var(--correos-azul, #1A549A);
        text-decoration: none;
    }
    .ba-ids {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }
    .ba-id {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 6px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
    }
    a.ba-id-hr {
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #8a6100;
    }
    a.ba-id-hr:hover {
        text-decoration: none;
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }

    /* flujo De -> Para */
    .ba-flujo {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 28px minmax(0, 1fr);
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        background: var(--correos-fondo, #F3F5F8);
    }
    .ba-persona {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .ba-avatar {
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        background: var(--correos-azul, #1A549A);
        color: #fff;
    }
    .ba-avatar.ba-avatar-para {
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .ba-persona-texto {
        min-width: 0;
        line-height: 1.3;
    }
    .ba-persona-texto small {
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #9aa4b2;
    }
    .ba-persona-texto b {
        display: block;
        font-size: 13px;
        color: #2d3748;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ba-persona-texto span {
        display: block;
        font-size: 11px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ba-flecha {
        text-align: center;
        font-size: 18px;
        color: #9aa4b2;
    }

    .ba-item-acciones {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 140px;
    }
    .ba-item-acciones .btn {
        margin: 0;
        text-align: left;
    }
    .ba-item-acciones .btn .fa,
    .ba-item-acciones .btn .md {
        width: 16px;
    }

    mark {
        padding: 0 2px;
        border-radius: 3px;
        background: var(--correos-amarillo, #FECB34);
        color: inherit;
    }
    .ba-vacio {
        padding: 36px 20px;
        text-align: center;
        color: #6b7686;
        background: #fff;
        border-radius: 10px;
    }
    .ba-vacio .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 40px;
        color: #b7c0cc;
    }
    .ba-paginacion {
        margin-top: 8px;
        text-align: center;
    }
    .ba-paginacion p,
    .ba-paginacion .pagination {
        margin: 0;
    }
    @media (max-width: 991px) {
        .ba-item {
            grid-template-columns: 44px minmax(0, 1fr);
        }
        .ba-item-icono {
            width: 44px;
            height: 44px;
            font-size: 18px;
        }
        .ba-item-acciones {
            grid-column: 2;
            flex-direction: row;
            flex-wrap: wrap;
            margin-top: 12px;
            min-width: 0;
        }
    }
    @media (max-width: 600px) {
        .ba-flujo {
            grid-template-columns: minmax(0, 1fr);
        }
        .ba-flecha {
            transform: rotate(90deg);
        }
    }
</style>

<div class="ba-card">
    <div class="ba-card-head">
        <h3><i class="fa fa-search"></i> Búsqueda avanzada</h3>
        <small class="text-muted">Complete uno o más campos; la búsqueda no distingue mayúsculas.</small>
    </div>
    <form class="ba-form" action="/search/advanced" method="get" id="form-search">
        <!-- fila 1: identificacion del documento -->
        <div class="ba-grid">
            <div class="ba-campo">
                <label for="ba-nur"><i class="md md-label"></i> Hoja de ruta</label>
                <input type="text" id="ba-nur" name="nur" value="<?php echo HTML::chars($filtros['nur']); ?>" placeholder="Ej.: AGBC/2026-01105"/>
            </div>
            <div class="ba-campo">
                <label for="ba-cite"><i class="fa fa-file-text-o"></i> Cite del documento</label>
                <input type="text" id="ba-cite" name="cite_original" value="<?php echo HTML::chars($filtros['cite_original']); ?>" placeholder="Ej.: INF/AGBC/DAF/SIS N° 0093"/>
            </div>
            <div class="ba-campo">
                <label for="ba-tipo"><i class="fa fa-tags"></i> Tipo de documento</label>
                <?php echo Form::select('tipo', $tipos, $filtros['tipo'], array('id' => 'ba-tipo')); ?>
            </div>
            <div class="ba-campo">
                <label for="ba-ent"><i class="fa fa-building-o"></i> Entidad remitente</label>
                <input type="text" id="ba-ent" name="entidad" value="<?php echo HTML::chars($filtros['entidad']); ?>" placeholder="Institución que envía"/>
            </div>

            <!-- fila 2: contenido y personas -->
            <div class="ba-campo ba-doble">
                <label for="ba-ref"><i class="fa fa-align-left"></i> Referencia</label>
                <input type="text" id="ba-ref" name="referencia" value="<?php echo HTML::chars($filtros['referencia']); ?>" placeholder="Palabras que contiene la referencia"/>
            </div>
            <div class="ba-campo">
                <label for="ba-rem"><i class="fa fa-user"></i> Remitente</label>
                <input type="text" id="ba-rem" name="remitente" value="<?php echo HTML::chars($filtros['remitente']); ?>" placeholder="Quién envía"/>
            </div>
            <div class="ba-campo">
                <label for="ba-dest"><i class="fa fa-sign-in"></i> Destinatario</label>
                <input type="text" id="ba-dest" name="destinatario" value="<?php echo HTML::chars($filtros['destinatario']); ?>" placeholder="A quién va dirigido"/>
            </div>
        </div>

        <!-- fila 3: fechas y acciones -->
        <div class="ba-fechas">
            <div class="ba-fechas-fila">
                <div class="ba-campo">
                    <label for="ba-desde"><i class="fa fa-calendar"></i> Creado desde</label>
                    <input type="date" id="ba-desde" name="start" value="<?php echo HTML::chars($filtros['start']); ?>"/>
                </div>
                <div class="ba-campo">
                    <label for="ba-hasta">Hasta</label>
                    <input type="date" id="ba-hasta" name="end" value="<?php echo HTML::chars($filtros['end']); ?>"/>
                </div>
                <div class="ba-atajos-bloque">
                    <label>Rango rápido</label>
                    <div class="ba-atajos">
                        <span class="ba-atajo" data-rango="hoy">Hoy</span>
                        <span class="ba-atajo" data-rango="7">Últimos 7 días</span>
                        <span class="ba-atajo" data-rango="mes">Este mes</span>
                        <span class="ba-atajo" data-rango="anio">Este año</span>
                        <span class="ba-atajo" data-rango="todas">Cualquier fecha</span>
                    </div>
                </div>
                <div class="ba-botones">
                    <a href="/search/advanced" class="btn btn-default-bright"><i class="fa fa-eraser"></i> Limpiar</a>
                    <button type="submit" name="buscar" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                </div>
            </div>
            <input type="hidden" name="todas" id="ba-todas" value="<?php echo $filtros['todas'] ? '1' : ''; ?>"/>
            <p class="ba-nota-fecha" id="ba-nota-fecha"></p>
        </div>
    </form>
</div>

<?php foreach ($mensajes as $m): ?>
    <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo HTML::chars($m); ?></div>
<?php endforeach; ?>

<?php if ($buscado && !isset($mensajes['criterio'])): ?>
    <div class="ba-resultados-head">
        <h3><b><?php echo number_format($count, 0, ',', '.'); ?></b> documento<?php echo $count == 1 ? '' : 's'; ?> encontrado<?php echo $count == 1 ? '' : 's'; ?></h3>
        <?php if ($count > 0): ?>
            <small><i class="fa fa-sort-amount-desc"></i> Del más reciente al más antiguo</small>
        <?php endif; ?>
    </div>

    <?php if ($count == 0): ?>
        <div class="ba-vacio">
            <i class="fa fa-search"></i>
            <h4>No se encontraron documentos</h4>
            <?php if ($hay_fechas): ?>
                La búsqueda está limitada a <?php echo $filtros['start'] === $filtros['end'] ? 'la fecha ' . date('d/m/Y', strtotime($filtros['start'])) : 'un rango de fechas'; ?>.
                <?php
                $sin_fechas = $_GET;
                unset($sin_fechas['start'], $sin_fechas['end'], $sin_fechas['page']);
                $sin_fechas['todas'] = 1;
                $sin_fechas['buscar'] = 1;
                ?>
                <br/><a href="/search/advanced?<?php echo HTML::chars(http_build_query($sin_fechas)); ?>"><b>Buscar en cualquier fecha</b></a>
            <?php else: ?>
                Pruebe con menos palabras o revise los datos ingresados.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($result as $d):
            $derivado = !((int) $d['estado'] === 0 && (int) $d['original'] === 1);
            $tiene_hr = trim($d['nur']) != '';
            $ts = $d['fecha_creacion'] ? strtotime($d['fecha_creacion']) : 0;
            ?>
            <div class="ba-item <?php echo $derivado ? '' : 'ba-item-no'; ?>">
                <div class="ba-item-icono"><i class="fa <?php echo $icono_tipo($d['tipo']); ?>"></i></div>

                <div class="ba-item-main">
                    <div class="ba-item-top">
                        <span class="ba-tipo"><?php echo HTML::chars($d['tipo']); ?></span>
                        <span class="ba-estado <?php echo $derivado ? 'ba-estado-si' : 'ba-estado-no'; ?>">
                            <i class="fa <?php echo $derivado ? 'fa-check' : 'fa-clock-o'; ?>"></i><?php echo $derivado ? 'Derivado' : 'No derivado'; ?></span>
                        <?php if ($ts): ?>
                            <span class="ba-fecha"><i class="fa fa-calendar-o"></i> <b><?php echo date('d/m/Y', $ts); ?></b> &middot; <?php echo date('H:i', $ts); ?></span>
                        <?php endif; ?>
                    </div>

                    <a class="ba-titulo" href="/document/detalle/<?php echo (int) $d['id']; ?>" title="Ver documento">
                        <?php echo trim($d['referencia']) != '' ? $resaltar($d['referencia'], $filtros['referencia']) : 'Sin referencia'; ?></a>

                    <div class="ba-ids">
                        <span class="ba-id" title="Cite del documento"><i class="fa fa-file-text-o"></i> <?php echo $resaltar($d['cite_original'], $filtros['cite_original']); ?></span>
                        <?php if ($tiene_hr): ?>
                            <a class="ba-id ba-id-hr" href="/route/trace/?hr=<?php echo urlencode($d['nur']); ?>" title="Ver seguimiento de la hoja de ruta">
                                <i class="md md-label"></i> <?php echo $resaltar($d['nur'], $filtros['nur']); ?></a>
                        <?php endif; ?>
                    </div>

                    <div class="ba-flujo">
                        <div class="ba-persona">
                            <span class="ba-avatar"><?php echo HTML::chars($iniciales($d['nombre_remitente'])); ?></span>
                            <div class="ba-persona-texto">
                                <small>De</small>
                                <b title="<?php echo HTML::chars($d['nombre_remitente']); ?>"><?php echo $resaltar($d['nombre_remitente'], $filtros['remitente']); ?></b>
                                <span title="<?php echo HTML::chars($d['cargo_remitente'] . ' ' . $d['institucion_remitente']); ?>">
                                    <?php echo HTML::chars($d['cargo_remitente']); ?><?php if (trim($d['institucion_remitente']) != ''): ?> &middot; <?php echo $resaltar($d['institucion_remitente'], $filtros['entidad']); ?><?php endif; ?></span>
                            </div>
                        </div>
                        <i class="fa fa-long-arrow-right ba-flecha"></i>
                        <div class="ba-persona">
                            <span class="ba-avatar ba-avatar-para"><?php echo HTML::chars($iniciales($d['nombre_destinatario'])); ?></span>
                            <div class="ba-persona-texto">
                                <small>Para</small>
                                <b title="<?php echo HTML::chars($d['nombre_destinatario']); ?>"><?php echo $resaltar($d['nombre_destinatario'], $filtros['destinatario']); ?></b>
                                <span title="<?php echo HTML::chars($d['cargo_destinatario']); ?>"><?php echo HTML::chars($d['cargo_destinatario']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ba-item-acciones">
                    <a href="/document/detalle/<?php echo (int) $d['id']; ?>" class="btn btn-sm btn-primary"><i class="fa fa-file-text-o"></i> Ver documento</a>
                    <?php if ($tiene_hr && $derivado): ?>
                        <a href="/route/trace/?hr=<?php echo urlencode($d['nur']); ?>" class="btn btn-sm btn-default-bright"><i class="md md-verified-user"></i> Seguimiento</a>
                    <?php endif; ?>
                    <?php if ($tiene_hr): ?>
                        <a href="/route/print?hr=<?php echo urlencode($d['nur']); ?>" class="btn btn-sm btn-default-bright"><i class="fa fa-print"></i> Imprimir HR</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="ba-paginacion"><?php echo $page_links; ?></div>
    <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
    $(function () {
        var $desde = $('#ba-desde'), $hasta = $('#ba-hasta'), $todas = $('#ba-todas');
        function iso(d) {
            return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
        }
        function nota() {
            var texto;
            if ($todas.val() === '1' || (!$desde.val() && !$hasta.val())) {
                texto = 'Se buscará en documentos de cualquier fecha.';
            } else {
                texto = 'Solo documentos creados ' + ($desde.val() ? 'desde el ' + $desde.val().split('-').reverse().join('/') : '')
                    + ($hasta.val() ? ' hasta el ' + $hasta.val().split('-').reverse().join('/') : '') + '.';
            }
            $('#ba-nota-fecha').text(texto);
            // marca el atajo que corresponde a las fechas actuales
            var hoy = new Date(), activo = '';
            if ($todas.val() === '1' || (!$desde.val() && !$hasta.val())) {
                activo = 'todas';
            } else if ($hasta.val() === iso(hoy)) {
                var d7 = new Date(hoy); d7.setDate(d7.getDate() - 6);
                if ($desde.val() === iso(hoy)) activo = 'hoy';
                else if ($desde.val() === iso(d7)) activo = '7';
                else if ($desde.val() === iso(new Date(hoy.getFullYear(), hoy.getMonth(), 1))) activo = 'mes';
                else if ($desde.val() === iso(new Date(hoy.getFullYear(), 0, 1))) activo = 'anio';
            }
            $('.ba-atajo').removeClass('activo').filter('[data-rango="' + activo + '"]').addClass('activo');
        }
        $('.ba-atajo').click(function () {
            var hoy = new Date(), desde = new Date(hoy), rango = $(this).attr('data-rango');
            if (rango === 'todas') {
                $desde.val('');
                $hasta.val('');
                $todas.val('1');
            } else {
                if (rango === '7') desde.setDate(hoy.getDate() - 6);
                if (rango === 'mes') desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                if (rango === 'anio') desde = new Date(hoy.getFullYear(), 0, 1);
                $desde.val(iso(desde));
                $hasta.val(iso(hoy));
                $todas.val('');
            }
            nota();
        });
        $desde.add($hasta).on('change', function () {
            $todas.val($desde.val() || $hasta.val() ? '' : '1');
            nota();
        });
        nota();
        $('#ba-nur').focus();
    });
</script>
