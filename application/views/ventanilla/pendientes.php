<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
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
        background: #B42318;
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
    /* buscador */
    .vl-filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 0 22px 14px;
    }
    .vl-buscar {
        position: relative;
        flex: 1 1 260px;
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
        flex-wrap: wrap;
        gap: 4px;
    }
    .vl-pes {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 36px;
        padding: 0 13px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5566;
        text-decoration: none;
        white-space: nowrap;
    }
    .vl-pes:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    .vl-pes.activa {
        background: #1A549A;
        border-color: #1A549A;
        color: #fff;
    }
    .vl-pes.activa:hover {
        color: #fff;
    }
    /* tabla */
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
    .vl-sindato {
        color: #9aa4b2 !important;
        font-style: italic;
        font-weight: 400 !important;
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
    .vl-dias {
        display: inline-block;
        padding: 1px 9px;
        border-radius: 20px;
        background: #EEF2F7;
        color: #4A5568;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .vl-dias.medio {
        background: #FFF3DC;
        color: #B26B00;
    }
    .vl-dias.alto {
        background: #FDE8E8;
        color: #B42318;
    }
    .vl-fecha {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        color: #8a94a3;
    }
    .vl-acciones {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .vl-acc {
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
    .vl-acc.derivar {
        background: #1A549A;
        border-color: #1A549A;
        color: #fff;
    }
    .vl-acc.derivar:hover {
        background: #123E73;
        color: #fff;
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
        font-size: 30px;
        color: #A7D9BC;
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
        <span class="vl-cab-icono"><i class="fa fa-clock-o"></i></span>
        <div class="vl-cab-texto">
            <h2>Pendientes de derivar</h2>
            <p><?php echo $n($count); ?> documento<?php echo (int) $count == 1 ? '' : 's'; ?> recibido<?php echo (int) $count == 1 ? '' : 's'; ?> en ventanilla que todavía no se enviaron a su destinatario.</p>
        </div>
        <a href="/ventanilla" class="vl-nuevo"><i class="fa fa-inbox"></i> Recepcionar</a>
    </div>

    <form method="get" action="/ventanilla/pendientes" class="vl-filtros">
        <span class="vl-buscar">
            <i class="fa fa-search"></i>
            <input type="search" name="q" value="<?php echo $h($q); ?>" placeholder="Buscar por hoja de ruta, cite, referencia o remitente…" autocomplete="off">
        </span>
        <button type="submit" class="vl-btn"><i class="fa fa-search"></i> Buscar</button>
        <?php if ($antiguos !== ''): ?>
            <input type="hidden" name="antiguos" value="<?php echo $h($antiguos); ?>">
        <?php endif; ?>
        <?php
        $liga = function ($a) use ($q) {
            $p = array();
            if ($q !== '') {
                $p['q'] = $q;
            }
            if ($a !== '') {
                $p['antiguos'] = $a;
            }
            return '/ventanilla/pendientes' . ($p ? '?' . http_build_query($p) : '');
        };
        ?>
        <span class="vl-pestanas">
            <a href="<?php echo $liga(''); ?>" class="vl-pes<?php echo $antiguos === '' ? ' activa' : ''; ?>">Todos</a>
            <a href="<?php echo $liga('0'); ?>" class="vl-pes<?php echo $antiguos === '0' ? ' activa' : ''; ?>">Del último año (<?php echo $n($reparto['recientes']); ?>)</a>
            <a href="<?php echo $liga('1'); ?>" class="vl-pes<?php echo $antiguos === '1' ? ' activa' : ''; ?>">Anteriores (<?php echo $n($reparto['viejos']); ?>)</a>
        </span>
        <?php if ($q !== ''): ?>
            <a href="/ventanilla/pendientes" class="vl-btn"><i class="fa fa-times"></i> Quitar filtros</a>
        <?php endif; ?>
    </form>

    <?php if ($antiguos === '' AND (int) $reparto['viejos'] > 0): ?>
        <div style="display:flex;gap:10px;align-items:flex-start;margin:0 22px 14px;padding:11px 14px;border-radius:11px;background:#FFF7DD;color:#7A5A00;font-size:12.5px">
            <i class="fa fa-archive" style="margin-top:2px"></i>
            <span><b><?php echo $n($reparto['viejos']); ?></b> de estos pendientes tienen más de un año y vienen arrastrándose.
                Use <b>«Del último año»</b> para ver solo lo que corresponde derivar ahora.</span>
        </div>
    <?php endif; ?>

    <div class="vl-tabla-caja">
        <?php if (count($documentos) == 0): ?>
            <div class="vl-vacio">
                <?php if ($q !== ''): ?>
                    <i class="fa fa-search" style="color:#C4CEDB"></i>
                    Ningún pendiente coincide con «<?php echo $h($q); ?>».
                <?php else: ?>
                    <i class="fa fa-check-circle"></i>
                    No hay nada pendiente: todo lo recibido ya fue derivado.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <table class="vl-tabla">
                <colgroup>
                    <col style="width:130px"><col><col style="width:22%"><col style="width:20%"><col style="width:96px"><col style="width:120px">
                </colgroup>
                <thead>
                <tr>
                    <th>Hoja de ruta</th>
                    <th>Documento</th>
                    <th>Remitente</th>
                    <th>Dirigido a</th>
                    <th>Esperando</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($documentos as $d):
                    $dias = (int) $d['dias'];
                    $clase = $dias >= 7 ? 'alto' : ($dias >= 3 ? 'medio' : '');
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
                            <?php // hay registros antiguos cuyos campos quedaron en "." o vacios
                            $ref = trim(str_replace('.', '', (string) $d['referencia']));
                            if ($ref === ''): ?>
                                <b class="vl-sindato">Sin datos registrados</b>
                            <?php else: ?>
                                <b><?php echo $h($d['referencia']); ?></b>
                            <?php endif; ?>
                            <small><?php echo $h($d['cite_original'] != '' ? $d['cite_original'] : $d['codigo']); ?><?php if ((int) $d['hojas'] > 0): ?> · <?php echo (int) $d['hojas']; ?> hoja<?php echo (int) $d['hojas'] == 1 ? '' : 's'; ?><?php endif; ?></small>
                        </td>
                        <td class="vl-quien">
                            <?php $rem = trim(str_replace('.', '', (string) $d['nombre_remitente'])); ?>
                            <b<?php echo $rem === '' ? ' class="vl-sindato"' : ''; ?>><?php echo $rem === '' ? '—' : $h($d['nombre_remitente']); ?></b>
                            <small><?php echo $h($d['institucion_remitente'] != '' ? $d['institucion_remitente'] : $d['cargo_remitente']); ?></small>
                        </td>
                        <td class="vl-quien">
                            <?php $des = trim(str_replace('.', '', (string) $d['nombre_destinatario'])); ?>
                            <b<?php echo $des === '' ? ' class="vl-sindato"' : ''; ?>><?php echo $des === '' ? '—' : $h($d['nombre_destinatario']); ?></b>
                            <small><?php echo $h($d['cargo_destinatario']); ?></small>
                        </td>
                        <td>
                            <span class="vl-dias <?php echo $clase; ?>"><?php echo $dias; ?> día<?php echo $dias == 1 ? '' : 's'; ?></span>
                            <span class="vl-fecha"><?php echo date('d/m/Y H:i', strtotime($d['fecha_creacion'])); ?></span>
                        </td>
                        <td>
                            <div class="vl-acciones">
                                <a href="/route/deriv/?hr=<?php echo urlencode($d['nur']); ?>" class="vl-acc derivar"><i class="fa fa-paper-plane"></i> Derivar</a>
                                <a href="/ventanilla/edit/<?php echo (int) $d['id']; ?>" class="vl-acc"><i class="fa fa-pencil"></i> Editar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if (count($documentos) > 0): ?>
        <div class="vl-pie">
            <span>Mostrando <?php echo $n(count($documentos)); ?> de <?php echo $n($count); ?> · los más antiguos primero abajo</span>
            <?php echo $page_links; ?>
        </div>
    <?php endif; ?>
</div>
