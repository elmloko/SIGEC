<?php
// estado actual -> etiqueta y color
$estilo = array(
    0 => array('Sin derivar', '#6b7686', '#EEF2F7', 'fa-pencil'),
    1 => array('No recibido', '#B26B00', '#FFF3DC', 'fa-inbox'),
    2 => array('Pendiente', '#1A549A', '#EAF1F9', 'fa-clock-o'),
    4 => array('Derivado', '#227547', '#E6F4EC', 'fa-paper-plane'),
    6 => array('Agrupado', '#5B3C99', '#F1ECFA', 'fa-files-o'),
    10 => array('Archivado', '#4A5568', '#EEF2F7', 'fa-archive'),
    11 => array('Anulado', '#B42318', '#FDE8E8', 'fa-ban'),
);
$hay_filtros = $filtros['q'] !== '' || $filtros['anio'] || $filtros['tipo'] || $filtros['estado'] !== '';
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
?>
<style>
    .hl-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .hl-card .btn {
        margin: 0;
    }
    .hl-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 18px 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .hl-cab-icono {
        flex: 0 0 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-azul, #1A549A);
        color: #fff;
        font-size: 20px;
    }
    .hl-cab-texto {
        flex: 1 1 260px;
    }
    .hl-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .hl-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .hl-cifra {
        padding: 8px 16px;
        border-radius: 12px;
        background: var(--correos-fondo, #F3F5F8);
        text-align: center;
    }
    .hl-cifra b {
        display: block;
        font-size: 20px;
        line-height: 1.1;
        color: var(--correos-azul, #1A549A);
    }
    .hl-cifra span {
        font-size: 11.5px;
        color: #7a8594;
    }
    /* filtros */
    .hl-filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 14px 22px;
        border-bottom: 1px solid #EEF2F7;
    }
    .hl-buscar {
        position: relative;
        flex: 1 1 320px;
    }
    .hl-buscar .fa {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .hl-buscar input,
    .hl-filtros select {
        height: 38px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 19px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 13px;
        color: #2d3748;
    }
    .hl-buscar input {
        width: 100%;
        padding: 4px 12px 4px 34px;
    }
    .hl-filtros select {
        padding: 4px 12px;
        max-width: 200px;
    }
    .hl-buscar input:focus,
    .hl-filtros select:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        background: #fff;
    }
    .hl-filtros .btn {
        border-radius: 19px;
    }
    .hl-limpiar {
        font-size: 12.5px;
        color: #8a94a3;
    }
    /* tabla */
    .hl-tabla {
        width: 100%;
        border-collapse: collapse;
        /* anchos fijos: el texto largo se ajusta dentro de su columna y la tabla nunca se sale de la tarjeta */
        table-layout: fixed;
    }
    .hl-tabla td {
        overflow: hidden;
    }
    .hl-tabla td.hl-acciones {
        overflow: visible;
    }
    .hl-tabla th {
        padding: 10px 14px;
        border-bottom: 1px solid #EEF2F7;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #8a94a3;
        white-space: nowrap;
    }
    .hl-tabla td {
        padding: 12px 14px;
        border-bottom: 1px solid #F1F4F8;
        vertical-align: middle;
        font-size: 13px;
        color: #4a5568;
    }
    .hl-tabla tbody tr:hover {
        background: #FAFBFD;
    }
    .hl-tabla td:first-child,
    .hl-tabla th:first-child {
        padding-left: 22px;
    }
    .hl-nur {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        background: var(--correos-azul, #1A549A);
        color: #fff !important;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }
    .hl-nur:hover {
        text-decoration: none;
        background: var(--correos-azul-oscuro, #123E73);
    }
    .hl-sin-nur {
        font-size: 12px;
        color: #9aa4b2;
    }
    .hl-fecha {
        display: block;
        margin-top: 4px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .hl-persona b,
    .hl-persona span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .hl-doc .hl-cite {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .hl-doc .hl-ref {
        display: block;
        margin-top: 2px;
        color: #2d3748;
        line-height: 1.35;
    }
    .hl-etq {
        display: inline-block;
        margin-top: 5px;
        padding: 1px 8px;
        border-radius: 10px;
        background: #EEF2F7;
        font-size: 10.5px;
        font-weight: 700;
        color: #4a5568;
    }
    .hl-etq.agrupado {
        background: #F1ECFA;
        color: #5B3C99;
    }
    .hl-persona b {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #2d3748;
    }
    .hl-persona span {
        display: block;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .hl-estado {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .hl-quien {
        display: block;
        margin-top: 4px;
        font-size: 11.5px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .hl-acciones {
        padding-right: 22px !important;
        text-align: right;
        white-space: nowrap;
    }
    .hl-acciones .btn-ver {
        padding: 5px 10px;
    }
    .hl-menu-btn {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #6b7686;
    }
    .hl-menu-btn:hover,
    .open > .hl-menu-btn {
        background: var(--correos-fondo, #F3F5F8);
        color: var(--correos-azul, #1A549A);
    }
    .hl-acciones .dropdown-menu {
        min-width: 240px;
        padding: 6px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(18, 62, 115, .18);
    }
    .hl-acciones .dropdown-menu > li > form {
        margin: 0;
    }
    .hl-acciones .dropdown-menu > li > a,
    .hl-acciones .dropdown-menu > li > form > button {
        display: block;
        width: 100%;
        padding: 8px 12px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        text-align: left;
        font-size: 13px;
        color: #2d3748;
    }
    .hl-acciones .dropdown-menu .fa {
        width: 18px;
        margin-right: 6px;
        color: var(--correos-azul, #1A549A);
    }
    .hl-acciones .dropdown-menu > li > a:hover {
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .hl-acciones .dropdown-menu > li > form > button.peligro,
    .hl-acciones .dropdown-menu > li > form > button.peligro .fa {
        color: #D32F2F;
    }
    .hl-acciones .dropdown-menu > li > form > button.peligro:hover {
        background: #FDE8E8;
    }
    .hl-vacio {
        padding: 40px 20px;
        text-align: center;
        color: #9aa4b2;
    }
    .hl-pie {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 22px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .hl-pie .pagination {
        margin: 0;
    }
    mark {
        padding: 0;
        background: #FFF0B3;
        color: inherit;
    }
</style>

<div class="col-lg-12">
    <div class="hl-card">
        <div class="hl-cab">
            <div class="hl-cab-icono"><i class="fa fa-tags"></i></div>
            <div class="hl-cab-texto">
                <h2>Documentos y hojas de ruta</h2>
                <p>Documentos originales generados en el sistema, con el estado actual de su hoja de ruta.</p>
            </div>
            <div class="hl-cifra"><b><?php echo $n($count); ?></b><span><?php echo $hay_filtros ? 'coinciden' : 'en total'; ?></span></div>
        </div>

        <form method="get" action="/admin/hojasruta/lista" class="hl-filtros" id="hl-form">
            <div class="hl-buscar">
                <i class="fa fa-search"></i>
                <input type="search" name="q" value="<?php echo HTML::chars($filtros['q']); ?>" autocomplete="off"
                       placeholder="Hoja de ruta, cite, referencia, destinatario o creador…"/>
            </div>
            <select name="estado" title="Estado actual">
                <option value="">Todos los estados</option>
                <option value="0" <?php echo $filtros['estado'] === 0 ? 'selected' : ''; ?>>Sin derivar</option>
                <?php foreach ($estados as $id => $e): ?>
                    <option value="<?php echo (int) $id; ?>" <?php echo $filtros['estado'] === (int) $id ? 'selected' : ''; ?>><?php echo HTML::chars(isset($estilo[$id]) ? $estilo[$id][0] : $e); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tipo" title="Tipo de documento">
                <option value="">Todos los tipos</option>
                <?php foreach ($tipos as $id => $t): ?>
                    <option value="<?php echo (int) $id; ?>" <?php echo $filtros['tipo'] === (int) $id ? 'selected' : ''; ?>><?php echo HTML::chars($t); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="anio" title="Año">
                <option value="">Todos los años</option>
                <?php foreach ($anios as $a): ?>
                    <option value="<?php echo (int) $a; ?>" <?php echo $filtros['anio'] === (int) $a ? 'selected' : ''; ?>><?php echo (int) $a; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
            <?php if ($hay_filtros): ?>
                <a href="/admin/hojasruta/lista" class="hl-limpiar"><i class="fa fa-times"></i> Quitar filtros</a>
            <?php endif; ?>
        </form>

        <?php if ($count == 0): ?>
            <div class="hl-vacio"><i class="fa fa-search" style="font-size:28px;display:block;margin-bottom:8px"></i>
                No se encontraron documentos<?php echo $hay_filtros ? ' con esos filtros' : ''; ?>.</div>
        <?php else: ?>
            <table class="hl-tabla">
                <colgroup>
                    <col style="width:150px"/>
                    <col/>
                    <col style="width:19%"/>
                    <col class="hl-col-creador" style="width:15%"/>
                    <col style="width:18%"/>
                    <col style="width:92px"/>
                </colgroup>
                <thead>
                <tr>
                    <th>Hoja de ruta</th>
                    <th>Documento</th>
                    <th>Destinatario</th>
                    <th class="hl-col-creador">Creado por</th>
                    <th>Dónde está</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($hojasruta as $h):
                    $est = $h['estado_hr'] === NULL ? NULL : (int) $h['estado_hr'];
                    $st = $est !== NULL && isset($estilo[$est]) ? $estilo[$est] : array('Sin datos', '#9aa4b2', '#F3F5F8', 'fa-question');
                    ?>
                    <tr>
                        <td>
                            <?php if ($h['nur'] != ''): ?>
                                <a href="/route/trace/?hr=<?php echo urlencode($h['nur']); ?>" class="hl-nur" title="Ver seguimiento"><?php echo HTML::chars($h['nur']); ?></a>
                            <?php else: ?>
                                <span class="hl-sin-nur">Sin hoja de ruta</span>
                            <?php endif; ?>
                            <span class="hl-fecha"><?php echo $h['fecha_creacion'] ? date('d/m/Y H:i', strtotime($h['fecha_creacion'])) : ''; ?></span>
                        </td>
                        <td class="hl-doc">
                            <span class="hl-cite"><?php echo HTML::chars($h['cite_original']); ?></span>
                            <span class="hl-ref"><?php echo HTML::chars($h['referencia'] != '' ? $h['referencia'] : 'Sin referencia'); ?></span>
                            <?php if ($h['tipo'] != ''): ?><span class="hl-etq"><?php echo HTML::chars($h['tipo']); ?></span><?php endif; ?>
                            <?php if ($h['proceso'] != ''): ?><span class="hl-etq"><?php echo HTML::chars($h['proceso']); ?></span><?php endif; ?>
                            <?php if ((int) $h['agrupado'] > 0): ?><a href="/admin/hojasruta/grupo/<?php echo (int) $h['id']; ?>" class="hl-etq agrupado" title="Ver documentos agrupados"><i class="fa fa-link"></i> Agrupado</a><?php endif; ?>
                        </td>
                        <td class="hl-persona">
                            <b title="<?php echo HTML::chars($h['nombre_destinatario']); ?>"><?php echo HTML::chars($h['nombre_destinatario']); ?></b>
                            <span title="<?php echo HTML::chars($h['cargo_destinatario']); ?>"><?php echo HTML::chars($h['cargo_destinatario']); ?></span>
                        </td>
                        <td class="hl-persona hl-col-creador">
                            <b title="<?php echo HTML::chars($h['creado_por']); ?>"><?php echo HTML::chars($h['creado_por']); ?></b>
                            <span><?php echo HTML::chars($h['username']); ?></span>
                        </td>
                        <td>
                            <span class="hl-estado" style="background:<?php echo $st[2]; ?>;color:<?php echo $st[1]; ?>"><i class="fa <?php echo $st[3]; ?>"></i> <?php echo $st[0]; ?></span>
                            <?php if ($est && $h['con_quien'] != ''): ?>
                                <span class="hl-quien" title="<?php echo HTML::chars($h['con_quien']); ?>"><?php echo in_array($est, array(10, 11), TRUE) ? 'por ' : 'con '; ?><?php echo HTML::chars($h['con_quien']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="hl-acciones">
                            <?php if ($h['nur'] != ''): ?>
                                <a href="/route/trace/?hr=<?php echo urlencode($h['nur']); ?>" class="btn btn-xs btn-default-bright btn-ver" title="Ver seguimiento"><i class="fa fa-map-marker"></i></a>
                            <?php endif; ?>
                            <span class="dropdown">
                                <button type="button" class="hl-menu-btn" data-toggle="dropdown" title="Más acciones"><i class="fa fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="/admin/hojasruta/editar/<?php echo (int) $h['id']; ?>"><i class="fa fa-pencil"></i> Editar datos del documento</a></li>
                                    <li><a href="/document/detalle/<?php echo (int) $h['id']; ?>" target="_blank"><i class="fa fa-file-text-o"></i> Ver documento</a></li>
                                    <?php if ($h['nur'] != ''): ?>
                                        <li><a href="/print/seguimiento/?hr=<?php echo urlencode($h['nur']); ?>" target="_blank"><i class="fa fa-print"></i> Imprimir seguimiento</a></li>
                                    <?php endif; ?>
                                    <?php if ((int) $h['agrupado'] > 0): ?>
                                        <li><a href="/admin/hojasruta/grupo/<?php echo (int) $h['id']; ?>"><i class="fa fa-link"></i> Ver agrupados</a></li>
                                    <?php endif; ?>
                                    <li class="divider"></li>
                                    <li>
                                        <form method="post" action="/admin/hojasruta/eliminar/<?php echo (int) $h['id']; ?>" class="hl-form-eliminar">
                                            <input type="hidden" name="confirmar" value="1"/>
                                            <button type="submit" class="peligro btn-eliminar-hr" data-nur="<?php echo HTML::chars($h['nur'] != '' ? $h['nur'] : $h['cite_original']); ?>">
                                                <i class="fa fa-trash-o"></i> Eliminar definitivamente
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="hl-pie">
                <span>Mostrando <?php echo $n(count($hojasruta)); ?> de <?php echo $n($count); ?> · más recientes primero</span>
                <?php echo $page_links; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $(function () {
        // los filtros se aplican al cambiarlos
        $('#hl-form select').on('change', function () {
            $('#hl-form').submit();
        });
        // eliminar: doble confirmacion, escribiendo el numero de hoja de ruta
        $('.btn-eliminar-hr').on('click', function (e) {
            e.preventDefault();
            var nur = String($(this).data('nur'));
            if (!confirm('¿ELIMINAR DEFINITIVAMENTE ' + nur + '?\n\nSe borrarán para siempre el documento, todo su seguimiento (derivaciones), sus agrupaciones y su correlativo. Esta acción NO se puede deshacer.')) {
                return;
            }
            var escrito = prompt('Para confirmar, escriba el número: ' + nur);
            if (escrito === null) {
                return;
            }
            if ($.trim(escrito).toUpperCase() !== nur.toUpperCase()) {
                alert('El número no coincide. No se eliminó nada.');
                return;
            }
            $(this).closest('form').submit();
        });
    });
</script>
