<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$iconos = array('circular' => 'fa-bullhorn', 'memorandum' => 'fa-file-text-o', 'informe' => 'fa-file-text-o',
    'nota interna' => 'fa-file-o', 'carta' => 'fa-envelope-o', 'doc. externo' => 'fa-inbox',
    'instructivo' => 'fa-list-ol', 'comunicado' => 'fa-bullhorn');
$total = 0;
$activos = 0;
foreach ($tipos as $t) {
    $total++;
    $activos += (int) $t->activo === 1 ? 1 : 0;
}
?>
<style>
    .ad-lista {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ad-l-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 18px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px 14px 0 0;
    }
    .ad-l-icono {
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
    .ad-l-texto {
        flex: 1 1 260px;
    }
    .ad-l-texto h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .ad-l-texto p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .ad-l-nuevo {
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
    .ad-l-nuevo:hover {
        background: #123E73;
        color: #fff;
        text-decoration: none;
    }
    .ad-l-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 0 22px 14px;
        padding: 11px 14px;
        border-radius: 11px;
        font-size: 13px;
    }
    .ad-l-msg.ok {
        background: #E6F6EC;
        color: #1E7B45;
    }
    .ad-l-msg.mal {
        background: #FDECEC;
        color: #912018;
    }
    /* tabla */
    .ad-tabla-caja {
        padding: 0 22px 22px;
    }
    table.ad-tabla {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        overflow: hidden;
    }
    .ad-tabla th {
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
    .ad-tabla td {
        padding: 10px 12px;
        border-bottom: 1px solid #F1F4F8;
        font-size: 12.5px;
        color: #334155;
        vertical-align: middle;
        word-wrap: break-word;
    }
    .ad-tabla tr:last-child td {
        border-bottom: 0;
    }
    .ad-tabla tbody tr:hover td {
        background: #F7FAFD;
    }
    .ad-tipo {
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .ad-tipo-icono {
        flex: 0 0 30px;
        height: 30px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 13px;
    }
    .ad-tipo b {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }
    .ad-tipo small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .ad-cite {
        font-family: Consolas, "Courier New", monospace;
        font-size: 11.5px;
        color: #4a5566;
    }
    .ad-etq {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .ad-etq.si {
        background: #E6F4EC;
        color: #1E7B45;
    }
    .ad-etq.no {
        background: #F1F4F8;
        color: #6b7686;
    }
    .ad-etq.baja {
        background: #FDECEC;
        color: #B42318;
    }
    .ad-num {
        font-weight: 600;
        color: #123E73;
    }
    .ad-num span {
        display: block;
        font-size: 11px;
        font-weight: 400;
        color: #8a94a3;
    }
    .ad-acciones {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }
    .ad-acc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 29px;
        padding: 0 11px;
        border: 1px solid #D5DCE6;
        border-radius: 8px;
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
        cursor: pointer;
    }
    .ad-acc:hover {
        background: #EEF3FA;
        border-color: #B9C8DC;
        color: #123E73;
        text-decoration: none;
    }
    .ad-acc.borrar {
        border-color: #E8B4B0;
        color: #B42318;
        padding: 0 9px;
    }
    .ad-acc.borrar:hover {
        background: #FDECEC;
        border-color: #D98A84;
    }
    .ad-acc[disabled] {
        opacity: .45;
        cursor: not-allowed;
    }
    .ad-acciones form {
        margin: 0;
    }
</style>

<div class="ad-lista">
    <div class="ad-l-cab">
        <span class="ad-l-icono"><i class="fa fa-files-o"></i></span>
        <div class="ad-l-texto">
            <h2>Tipos de documento</h2>
            <p><?php echo $activos; ?> en uso de <?php echo $total; ?>. Definen qué puede generar cada persona y cómo se numera su cite.</p>
        </div>
        <a href="/admin/config/tipo" class="ad-l-nuevo"><i class="fa fa-plus"></i> Nuevo tipo</a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="ad-l-msg ok"><i class="fa fa-check-circle"></i> <span><?php echo $h($mensaje); ?></span></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="ad-l-msg mal"><i class="fa fa-exclamation-triangle"></i> <span><?php echo $h($error); ?></span></div>
    <?php endif; ?>

    <div class="ad-tabla-caja">
        <table class="ad-tabla">
            <colgroup>
                <col><col style="width:26%"><col style="width:110px"><col style="width:120px"><col style="width:120px"><col style="width:120px">
            </colgroup>
            <thead>
            <tr>
                <th>Tipo</th>
                <th>Formato del cite</th>
                <th>Cite propio</th>
                <th>Documentos</th>
                <th>Lo pueden usar</th>
                <th style="text-align:right">Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tipos as $t):
                $u = isset($uso[$t->id]) ? $uso[$t->id] : array('documentos' => 0, 'usuarios' => 0);
                $icono = Arr::get($iconos, strtolower(trim($t->tipo)), 'fa-file-o');
                $activo = (int) $t->activo === 1;
                ?>
                <tr>
                    <td>
                        <span class="ad-tipo">
                            <span class="ad-tipo-icono"><i class="fa <?php echo $icono; ?>"></i></span>
                            <span>
                                <b><?php echo $h($t->tipo); ?>
                                    <?php if (!$activo): ?><span class="ad-etq baja">Desactivado</span><?php endif; ?>
                                </b>
                                <small><?php echo $h($t->abreviatura); ?><?php echo trim($t->plural) != '' ? ' · ' . $h($t->plural) : ''; ?></small>
                            </span>
                        </span>
                    </td>
                    <td><span class="ad-cite"><?php echo $h($t->descripcion != '' ? $t->descripcion : '—'); ?></span></td>
                    <td><span class="ad-etq <?php echo $t->cite_propio > 0 ? 'si' : 'no'; ?>"><?php echo $t->cite_propio > 0 ? 'Sí' : 'No'; ?></span></td>
                    <td class="ad-num"><?php echo $n($u['documentos']); ?><span>generados</span></td>
                    <td class="ad-num"><?php echo $n($u['usuarios']); ?><span>personas</span></td>
                    <td>
                        <div class="ad-acciones">
                            <a href="/admin/config/tipo/<?php echo (int) $t->id; ?>" class="ad-acc"><i class="fa fa-pencil"></i> Editar</a>
                            <?php if ((int) $u['documentos'] === 0): ?>
                                <form method="post" action="/admin/tipos/eliminar/<?php echo (int) $t->id; ?>">
                                    <button type="submit" class="ad-acc borrar btn-borrar-tipo" data-tipo="<?php echo $h($t->tipo); ?>" title="Eliminar"><i class="fa fa-trash-o"></i></button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="ad-acc borrar" disabled
                                        title="No se puede eliminar: ya hay <?php echo $n($u['documentos']); ?> documentos de este tipo."><i class="fa fa-trash-o"></i></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function () {
        $('.btn-borrar-tipo').on('click', function (e) {
            e.preventDefault();
            var tipo = String($(this).data('tipo'));
            if (!confirm('¿Eliminar el tipo de documento "' + tipo + '"?\n\nNo hay ningún documento generado con él, pero la acción no se puede deshacer.')) {
                return;
            }
            $(this).closest('form')[0].submit();
        });
    });
</script>
