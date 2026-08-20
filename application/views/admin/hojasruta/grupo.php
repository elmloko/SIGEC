<script>
    $(function () {
        $('.btn-desagrupar').click(function (e) {
            e.preventDefault();
            var nur = $(this).data('nur');
            if (confirm('¿Desagrupar la hoja de ruta ' + nur + '? Volvera a aparecer como pendiente independiente.')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>

<div class="card card-underline">
    <div class="card-head">
        <header><i class="fa fa-object-group"></i> Agrupacion de la hoja de ruta <b><?php echo HTML::chars($documento->nur); ?></b></header>
        <tools class="pull-right">
            <a href="/admin/hojasruta/lista" class="btn btn-sm btn-default">Volver al listado</a>
        </tools>
    </div>
    <div class="card-body">

        <?php if (isset($padre) && sizeof($padre) > 0): ?>
            <h6>Esta hoja de ruta fue agrupada dentro de:</h6>
            <table class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th>NUR PRINCIPAL</th>
                    <th>CITE</th>
                    <th>REFERENCIA</th>
                    <th>DESTINATARIO</th>
                    <th>FECHA DE AGRUPACION</th>
                    <th>AGRUPADO POR</th>
                    <th class="noprint">OPCIONES</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($padre as $p): ?>
                    <tr>
                        <td><a href="/route/trace/?hr=<?php echo $p['padre']; ?>" target="_blank"><b><?php echo HTML::chars($p['padre']); ?></b></a></td>
                        <td><?php echo HTML::chars($p['cite_original']); ?></td>
                        <td><?php echo HTML::chars($p['referencia']); ?></td>
                        <td><?php echo HTML::chars($p['nombre_destinatario']); ?><br/>
                            <small class="opacity-70"><?php echo HTML::chars($p['cargo_destinatario']); ?></small>
                        </td>
                        <td><?php echo Date::fecha_corta($p['fecha']); ?></td>
                        <td><?php echo HTML::chars($p['nombre']); ?><br/>
                            <small class="opacity-70"><?php echo HTML::chars($p['cargo']); ?></small>
                        </td>
                        <td class="noprint">
                            <form method="post" action="/admin/hojasruta/desagrupar/<?php echo $p['id_agrupacion']; ?>" style="display:inline;">
                                <input type="hidden" name="confirmar" value="1"/>
                                <input type="hidden" name="documento_id" value="<?php echo $documento->id; ?>"/>
                                <button type="submit" data-nur="<?php echo HTML::chars($documento->nur); ?>"
                                        class="btn btn-sm btn-warning btn-desagrupar" title="Desagrupar">
                                    <i class="fa fa-unlink"></i> Desagrupar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (isset($hijos) && sizeof($hijos) > 0): ?>
            <h6>Hojas de ruta agrupadas dentro de esta:</h6>
            <table class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th>NUR</th>
                    <th>CITE</th>
                    <th>REFERENCIA</th>
                    <th>TIPO</th>
                    <th>FECHA DE RECEPCION</th>
                    <th class="noprint">OPCIONES</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($hijos as $h): ?>
                    <tr>
                        <td><a href="/route/trace/?hr=<?php echo $h['nur']; ?>" target="_blank"><b><?php echo HTML::chars($h['nur']); ?></b></a></td>
                        <td><?php echo HTML::chars($h['cite_original']); ?></td>
                        <td><?php echo HTML::chars($h['referencia']); ?></td>
                        <td><?php echo HTML::chars($h['oficial']); ?></td>
                        <td><?php echo HTML::chars($h['fecha_recepcion']); ?></td>
                        <td class="noprint">
                            <form method="post" action="/admin/hojasruta/desagrupar/<?php echo $h['id_agrupacion']; ?>" style="display:inline;">
                                <input type="hidden" name="confirmar" value="1"/>
                                <input type="hidden" name="documento_id" value="<?php echo $documento->id; ?>"/>
                                <button type="submit" data-nur="<?php echo HTML::chars($h['nur']); ?>"
                                        class="btn btn-sm btn-warning btn-desagrupar" title="Desagrupar">
                                    <i class="fa fa-unlink"></i> Desagrupar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ((!isset($padre) || sizeof($padre) == 0) && (!isset($hijos) || sizeof($hijos) == 0)): ?>
            <div class="info">
                <p>Esta hoja de ruta no tiene ninguna agrupacion registrada.</p>
            </div>
        <?php endif; ?>

    </div>
</div>
