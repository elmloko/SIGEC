<script>
    $(function () {
        $('#datatable1').DataTable({
            "dom": 'lCfrtip',
            "order": [],
            "colVis": {
                "buttonText": "Columnas",
                "overlayFade": 0,
                "align": "right"
            },
            "language": {
                "lengthMenu": '_MENU_ filas por página',
                "search": '<i class="fa fa-search"></i>',
                "paginate": {
                    "previous": '<i class="fa fa-angle-left"></i>',
                    "next": '<i class="fa fa-angle-right"></i>'
                }
            }
        });
    });
</script>
<div class="row">
    <div class="col-md-12">
        <form method="post" action="" class="form">
            <div class="card card-underline ">
                <div class="card-head">
                    <header>Tipos de Documentos</header>
                    <tools class="pull-right"><a href="/admin/config/tipo" class="btn btn-sm btn-primary-dark"><i
                                class="fa fa-plus"></i> Nuevo</a></tools>
                </div>
                <div class="card-body">
                    <table id="datatable1" class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th>Tipo documento</th>
                            <th>Abreviatura</th>
                            <th>Cite Tipo</th>
                            <th>cite Propio?</th>
                            <th>cite</th>
                            <th>Template</th>
                            <th>Template Via</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tipos as $t): ?>
                            <tr>
                                <td>
                                    <?php echo $t->tipo; ?>
                                </td>
                                <td>
                                    <?php echo $t->abreviatura; ?>
                                </td>
                                <td>
                                    <?php echo $t->cite_tipo; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($t->cite_propio > 0) {
                                        echo 'Si';
                                    } else {
                                        echo 'No';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $t->cite; ?>
                                </td>
                                <td>
                                    <?php echo $t->template; ?>
                                </td>
                                <td>
                                    <?php echo $t->template_via; ?>
                                </td>
                                <td>
                                    <a href="/admin/config/tipo/<?php echo $t->id ?>"
                                       class="btn btn-xs btn-primary-dark">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!--
                                    <a href="/admin/config/tipo/<?php echo $t->id ?>" class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                    -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>