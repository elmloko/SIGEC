<?php
$h = function ($s) {
    return HTML::chars($s);
};
$es_documentos = ($tipo === 'documentos');
$cuantos = is_array($result) ? count($result) : count($result->as_array());
?>
<style>
    body {
        background: #F3F5F8;
    }
    .el {
        max-width: 1100px;
        margin: 0 auto;
        padding: 14px 16px 24px;
        font-family: Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .el-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 12px 16px;
        margin-bottom: 14px;
        border-radius: 12px;
        border-top: 3px solid #FECB34;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .el-cab-texto {
        flex: 1 1 260px;
        min-width: 0;
    }
    .el-cab b {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #123E73;
    }
    .el-cab span {
        display: block;
        font-size: 12.5px;
        color: #6b7686;
    }
    .el-volver {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 34px;
        padding: 0 14px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
    }
    .el-volver:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    .el-tabla-caja {
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .1);
        overflow: hidden;
    }
    table.el-tabla {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .el-tabla th {
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
    .el-tabla td {
        padding: 9px 12px;
        border-bottom: 1px solid #F1F4F8;
        font-size: 12.5px;
        color: #334155;
        vertical-align: top;
        word-wrap: break-word;
    }
    .el-tabla tr:last-child td {
        border-bottom: 0;
    }
    .el-tabla tbody tr:hover td {
        background: #F7FAFD;
    }
    .el-nur {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        background: #EEF3FA;
        color: #1A549A;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
    }
    .el-nur:hover {
        background: #1A549A;
        color: #fff;
        text-decoration: none;
    }
    .el-quien b {
        display: block;
        font-weight: 600;
        color: #1f2937;
    }
    .el-quien small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .el-dias {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 20px;
        background: #EEF2F7;
        color: #4A5568;
        font-size: 11.5px;
        font-weight: 700;
    }
    .el-dias.medio {
        background: #FFF3DC;
        color: #B26B00;
    }
    .el-dias.alto {
        background: #FDE8E8;
        color: #B42318;
    }
    .el-vacio {
        padding: 34px 16px;
        text-align: center;
        color: #8a94a3;
        font-size: 13.5px;
    }
    .el-vacio .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 26px;
        color: #C4CEDB;
    }
    .el-ref {
        color: #1f2937;
    }
</style>

<div class="el">
    <div class="el-cab">
        <div class="el-cab-texto">
            <b><?php echo $h($titulo); ?></b>
            <span><?php echo $h($user->nombre); ?> · <?php echo $cuantos; ?> registro<?php echo $cuantos == 1 ? '' : 's'; ?></span>
        </div>
        <a href="/admin/content/userStats/<?php echo (int) $user->id; ?>" class="el-volver">
            <i class="fa fa-arrow-left"></i> Volver a estadísticas
        </a>
    </div>

    <div class="el-tabla-caja">
        <?php if ($cuantos == 0): ?>
            <div class="el-vacio"><i class="fa fa-inbox"></i> No hay nada que mostrar aquí.</div>
        <?php elseif ($es_documentos): ?>
            <table class="el-tabla">
                <colgroup>
                    <col style="width:140px"><col><col style="width:22%"><col style="width:20%"><col style="width:90px">
                </colgroup>
                <thead>
                <tr>
                    <th>Hoja de ruta</th>
                    <th>Cite y referencia</th>
                    <th>Destinatario</th>
                    <th>Código</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result as $d): ?>
                    <tr>
                        <td>
                            <?php if (trim($d->nur) !== ''): ?>
                                <a href="/route/trace/?hr=<?php echo urlencode($d->nur); ?>" target="_blank" class="el-nur" title="Ver seguimiento"><?php echo $h($d->nur); ?></a>
                            <?php else: ?>
                                <span style="color:#9aa4b2">sin hoja</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <b class="el-ref"><?php echo $h($d->cite_original); ?></b>
                            <div style="color:#6b7686"><?php echo $h($d->referencia); ?></div>
                        </td>
                        <td class="el-quien">
                            <b><?php echo $h($d->nombre_destinatario); ?></b>
                            <small><?php echo $h($d->cargo_destinatario); ?></small>
                        </td>
                        <td style="color:#8a94a3"><?php echo $h($d->codigo); ?></td>
                        <td><?php echo Date::fecha_corta($d->fecha_creacion); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <table class="el-tabla">
                <colgroup>
                    <col style="width:140px"><col><col style="width:22%"><col style="width:15%"><col style="width:92px"><col style="width:74px">
                </colgroup>
                <thead>
                <tr>
                    <th>Hoja de ruta</th>
                    <th>Referencia</th>
                    <th>De / para</th>
                    <th>Acción</th>
                    <th>Fecha</th>
                    <th>Días</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result as $r):
                    $dias = (int) $r->dias;
                    $clase = $dias >= 15 ? 'alto' : ($dias >= 5 ? 'medio' : '');
                    ?>
                    <tr>
                        <td><a href="/route/trace/?hr=<?php echo urlencode($r->nur); ?>" target="_blank" class="el-nur" title="Ver seguimiento"><?php echo $h($r->nur); ?></a></td>
                        <td>
                            <div class="el-ref"><?php echo $h($r->referencia); ?></div>
                            <small style="color:#8a94a3"><?php echo $h($r->codigo); ?></small>
                        </td>
                        <td class="el-quien">
                            <b><?php echo $h($r->nombre_destinatario); ?></b>
                            <small><?php echo $h($r->cargo_destinatario); ?></small>
                        </td>
                        <td style="color:#6b7686"><?php echo $h($r->accion); ?></td>
                        <td><?php echo Date::fecha_corta($r->fecha); ?></td>
                        <td><span class="el-dias <?php echo $clase; ?>"><?php echo $dias; ?> d</span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="/static/js/modal-alto.js"></script>
<script>
    $(function () {
        if (window.ajustarModal) {
            window.ajustarModal({ancho: 1140});
            $(window).on('load', function () {
                window.ajustarModal({ancho: 1140});
            });
        }
    });
</script>
