<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$url = function ($estado) use ($q) {
    $p = array();
    if ($q !== '') {
        $p['q'] = $q;
    }
    if ($estado !== '') {
        $p['estado'] = $estado;
    }
    return '/ventanilla/listar' . ($p ? '?' . http_build_query($p) : '');
};
?>
<style>
    .vl-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .vl-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 18px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px 14px 0 0;
    }
    .vl-cab-icono {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #1A549A;
        color: #fff;
        font-size: 19px;
    }
    .vl-cab-texto {
        flex: 1 1 240px;
    }
    .vl-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .vl-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .vl-nuevo {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 36px;
        padding: 0 16px;
        border-radius: 9px;
        background: #1A549A;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .vl-nuevo:hover {
        background: #123E73;
        color: #fff;
        text-decoration: none;
    }
    .vl-filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 0 22px 14px;
    }
    .vl-buscar {
        position: relative;
        flex: 1 1 250px;
    }
    .vl-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        margin-top: -7px;
        color: #9aa4b2;
        font-size: 13px;
    }
    .vl-buscar input {
        width: 100%;
        height: 36px;
        padding: 6px 11px 6px 31px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        font-size: 13.5px;
        outline: none;
    }
    .vl-buscar input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .vl-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 36px;
        padding: 0 14px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
    }
    .vl-btn:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    .vl-pestanas {
        display: flex;
        gap: 4px;
    }
    .vl-pes {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 36px;
        padding: 0 14px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5566;
        text-decoration: none;
    }
    .vl-pes:hover {
        background: #EEF3FA;
        text-decoration: none;
        color: #123E73;
    }
    .vl-pes.activa {
        background: #1A549A;
        border-color: #1A549A;
        color: #fff;
    }
    .vl-pes.activa:hover {
        color: #fff;
    }
    .vl-tabla-caja {
        padding: 0 22px 18px;
    }
    table.vl-tabla {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        overflow: hidden;
    }
    .vl-tabla th {
        padding: 9px 12px;
        border-bottom: 1px solid #E3E8EF;
        background: #F7F9FC;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: #6b7686;
        text-align: left;
    }
    .vl-tabla td {
        padding: 10px 12px;
        border-bottom: 1px solid #F1F4F8;
        font-size: 12.5px;
        color: #334155;
        vertical-align: top;
        word-wrap: break-word;
    }
    .vl-tabla tr:last-child td {
        border-bottom: 0;
    }
    .vl-tabla tbody tr:hover td {
        background: #F7FAFD;
    }
    .vl-nur {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        background: #EEF3FA;
        color: #1A549A;
        font-family: Consolas, "Courier New", monospace;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
    }
    .vl-nur:hover {
        background: #1A549A;
        color: #fff;
        text-decoration: none;
    }
    .vl-ref b {
        display: block;
        font-weight: 600;
        color: #1f2937;
    }
    .vl-ref small {
        display: block;
        margin-top: 1px;
        font-size: 11px;
        color: #8a94a3;
    }
    .vl-quien b {
        display: block;
        font-weight: 600;
        color: #1f2937;
    }
    .vl-quien small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .vl-etq {
        display: inline-block;
        padding: 1px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .vl-etq.pendiente {
        background: #FDE8E8;
        color: #B42318;
    }
    .vl-etq.derivado {
        background: #E6F4EC;
        color: #1E7B45;
    }
    .vl-fecha {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        color: #8a94a3;
    }
    .vl-acc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 28px;
        padding: 0 10px;
        border: 1px solid #D5DCE6;
        border-radius: 8px;
        background: #fff;
        font-size: 11.5px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
        white-space: nowrap;
    }
    .vl-acc:hover {
        background: #EEF3FA;
        border-color: #B9C8DC;
        color: #123E73;
        text-decoration: none;
    }
    .vl-vacio {
        padding: 40px 16px;
        text-align: center;
        color: #8a94a3;
        font-size: 14px;
    }
    .vl-vacio .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 28px;
        color: #C4CEDB;
    }
    .vl-pie {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 22px;
        border-top: 1px solid #EEF1F5;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .vl-pie .pagination {
        margin: 0;
    }
</style>

<div class="vl-card">
    <div class="vl-cab">
        <span class="vl-cab-icono"><i class="fa fa-files-o"></i></span>
        <div class="vl-cab-texto">
            <h2>Correspondencia recepcionada</h2>
            <p>Todo lo que registró esta ventanilla: <?php echo $n($totales['total']); ?> en total,
                <?php echo $n($totales['pendientes']); ?> sin derivar.</p>
        </div>
        <a href="/ventanilla" class="vl-nuevo"><i class="fa fa-inbox"></i> Recepcionar</a>
    </div>

    <form method="get" action="/ventanilla/listar" class="vl-filtros">
        <span class="vl-buscar">
            <i class="fa fa-search"></i>
            <input type="search" name="q" value="<?php echo $h($q); ?>" placeholder="Buscar por hoja de ruta, cite, referencia o remitente…" autocomplete="off">
        </span>
        <?php if ($estado !== ''): ?>
            <input type="hidden" name="estado" value="<?php echo $h($estado); ?>">
        <?php endif; ?>
        <button type="submit" class="vl-btn"><i class="fa fa-search"></i> Buscar</button>
        <span class="vl-pestanas">
            <a href="<?php echo $url(''); ?>" class="vl-pes<?php echo $estado === '' ? ' activa' : ''; ?>">Todos</a>
            <a href="<?php echo $url('0'); ?>" class="vl-pes<?php echo $estado === '0' ? ' activa' : ''; ?>">Sin derivar</a>
            <a href="<?php echo $url('1'); ?>" class="vl-pes<?php echo $estado === '1' ? ' activa' : ''; ?>">Derivados</a>
        </span>
    </form>

    <div class="vl-tabla-caja">
        <?php if (count($documentos) == 0): ?>
            <div class="vl-vacio">
                <i class="fa fa-search"></i>
                <?php echo $q !== '' ? 'Nada coincide con «' . $h($q) . '».' : 'Todavía no hay correspondencia registrada.'; ?>
            </div>
        <?php else: ?>
            <table class="vl-tabla">
                <colgroup>
                    <col style="width:130px"><col><col style="width:22%"><col style="width:20%"><col style="width:110px"><col style="width:96px">
                </colgroup>
                <thead>
                <tr>
                    <th>Hoja de ruta</th>
                    <th>Documento</th>
                    <th>Remitente</th>
                    <th>Dirigido a</th>
                    <th>Estado</th>
                    <th style="text-align:center">Ver</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($documentos as $d):
                    $derivado = (int) $d['estado'] === 1;
                    ?>
                    <tr>
                        <td>
                            <?php if (trim($d['nur']) !== ''): ?>
                                <a href="/route/trace/?hr=<?php echo urlencode($d['nur']); ?>" class="vl-nur" title="Ver seguimiento"><?php echo $h($d['nur']); ?></a>
                            <?php else: ?>
                                <span style="color:#9aa4b2">sin hoja</span>
                            <?php endif; ?>
                        </td>
                        <td class="vl-ref">
                            <b><?php echo $h($d['referencia']); ?></b>
                            <small><?php echo $h($d['cite_original'] != '' ? $d['cite_original'] : $d['codigo']); ?></small>
                        </td>
                        <td class="vl-quien">
                            <b><?php echo $h($d['nombre_remitente']); ?></b>
                            <small><?php echo $h($d['institucion_remitente'] != '' ? $d['institucion_remitente'] : $d['cargo_remitente']); ?></small>
                        </td>
                        <td class="vl-quien">
                            <b><?php echo $h($d['nombre_destinatario']); ?></b>
                            <small><?php echo $h($d['cargo_destinatario']); ?></small>
                        </td>
                        <td>
                            <span class="vl-etq <?php echo $derivado ? 'derivado' : 'pendiente'; ?>"><?php echo $derivado ? 'Derivado' : 'Sin derivar'; ?></span>
                            <span class="vl-fecha"><?php echo date('d/m/Y H:i', strtotime($d['fecha_creacion'])); ?></span>
                        </td>
                        <td style="text-align:center">
                            <?php if ($derivado): ?>
                                <a href="/route/trace/?hr=<?php echo urlencode($d['nur']); ?>" class="vl-acc"><i class="md md-verified-user"></i> Seguir</a>
                            <?php else: ?>
                                <a href="/route/deriv/?hr=<?php echo urlencode($d['nur']); ?>" class="vl-acc"><i class="fa fa-paper-plane"></i> Derivar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if (count($documentos) > 0): ?>
        <div class="vl-pie">
            <span>Mostrando <?php echo $n(count($documentos)); ?> de <?php echo $n($count); ?> · más recientes primero</span>
            <?php echo $page_links; ?>
        </div>
    <?php endif; ?>
</div>
