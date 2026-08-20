<script>
    $(function () {
        $('.btn-eliminar-hr').click(function (e) {
            e.preventDefault();
            var nur = $(this).data('nur');
            if (confirm('¿Esta seguro de ELIMINAR DEFINITIVAMENTE la hoja de ruta ' + nur + '?\n\nSe borraran de forma permanente el documento, todo su seguimiento (derivaciones), sus agrupaciones y su correlativo NUR. Esta accion NO se puede deshacer.')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>

<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-road"></i> Hojas de Ruta generadas</header>
                <tools class="pull-right">
                    <span class="text-medium opacity-70"><?php echo (int) $count; ?> hoja(s) de ruta</span>
                </tools>
            </div>
            <div class="card-body">
                <form method="get" action="/admin/hojasruta/lista" class="form-inline" style="margin-bottom: 15px;">
                    <input type="text" name="q" class="form-control" placeholder="Buscar por NUR, cite, referencia, destinatario o creador"
                           value="<?php echo HTML::chars($q); ?>" style="width: 420px; max-width: 100%;"/>
                    <button type="submit" class="btn btn-sm btn-primary-dark"><i class="fa fa-search"></i> Buscar</button>
                    <?php if ($q !== ''): ?>
                        <a href="/admin/hojasruta/lista" class="btn btn-sm btn-default">Limpiar</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body no-padding">
                <table id="theTable" class="table table-condensed table-striped standard-table" width="100%">
                    <thead>
                    <tr>
                        <th>NUR</th>
                        <th>CITE</th>
                        <th>REFERENCIA</th>
                        <th>DESTINATARIO</th>
                        <th>CREADO POR</th>
                        <th>PROCESO</th>
                        <th>FECHA INFORME CREADO</th>
                        <th>AGRUPADO</th>
                        <th class="noprint">OPCIONES</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($hojasruta as $h): ?>
                        <tr>
                            <td><b><?php echo HTML::chars($h['nur']); ?></b></td>
                            <td><?php echo HTML::chars($h['cite_original']); ?></td>
                            <td><?php echo HTML::chars($h['referencia']); ?></td>
                            <td><?php echo HTML::chars($h['nombre_destinatario']); ?><br/>
                                <small class="opacity-70"><?php echo HTML::chars($h['cargo_destinatario']); ?></small>
                            </td>
                            <td><?php echo HTML::chars($h['creado_por']); ?></td>
                            <td><?php echo HTML::chars($h['proceso']); ?></td>
                            <td><?php echo Date::fecha_corta($h['fecha_creacion']); ?></td>
                            <td>
                                <?php if ((int) $h['agrupado'] > 0): ?>
                                    <a href="/admin/hojasruta/grupo/<?php echo $h['id']; ?>"
                                       class="btn btn-xs btn-warning" title="Ver documentos agrupados">
                                        <i class="md md-link"></i> Agrupado
                                    </a>
                                <?php else: ?>
                                    <span class="opacity-50">No agrupado</span>
                                <?php endif; ?>
                            </td>
                            <td class="noprint">
                                <a href="/route/trace/?hr=<?php echo $h['nur']; ?>" target="_blank"
                                   class="btn btn-sm btn-primary" title="Ver seguimiento">
                                    <i class="md md-visibility"></i>
                                </a>
                                <a href="/admin/hojasruta/editar/<?php echo $h['id']; ?>"
                                   class="btn btn-sm btn-primary-dark" title="Editar registro del documento">
                                    <i class="md md-edit"></i>
                                </a>
                                <form method="post" action="/admin/hojasruta/eliminar/<?php echo $h['id']; ?>" style="display:inline;">
                                    <input type="hidden" name="confirmar" value="1"/>
                                    <button type="submit" data-nur="<?php echo HTML::chars($h['nur']); ?>"
                                            class="btn btn-sm btn-danger btn-eliminar-hr" title="Eliminar informe (DROP)">
                                        <i class="md md-delete"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($count == 0): ?>
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron hojas de ruta.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                <?php echo $page_links; ?>
            </div>
        </div>
    </div>
</div>
