<script>
    $(function () {
        $('#id_entidad').change(function () {
            var id = $(this).val();
            console.log('id', id);
            location.href = '/admin/oficinas/lista/' + id;
        });
        $('select').select2();
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
<div class="pull-right">
    <?php
    echo Form::select(
        'id_entidad',
        $options,
        $id_entidad,
        array(
            'id' => 'id_entidad',
            'class' => 'form-control',
            'width' => '100%'
        )
    );
    ?>
</div>
<br/>
<br/>
<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header><?php echo $entidad ?></header>
                <tools class=" pull-right">
                    <a class="btn btn-sm btn-primary-dark" href="/admin/oficinas/create/<?php echo $id_entidad; ?>">+
                        Nueva Oficina</a>
                </tools>
            </div>
            <div class="card-body no-padding">
                <table id="datatable1" class="table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th>
                            SIGLA
                        </th>
                        <th>
                            OFICINA
                        </th>
                        <th>
                            ESTADO
                        </th>
                        <th>
                            ENTIDAD
                        </th>
                        <th>
                            OPCIONES
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($oficinas as $o): ?>
                        <tr>
                            <td>
                                <?php echo $o['sigla']; ?>
                            </td>
                            <td>
                                <a href="/admin/user/lista/<?php echo $o['id']; ?>"><?php echo $o['oficina']; ?></a>
                            </td>
                            <td>
                                <?php
                                $estado_oficina = $o['estado'] === '1' ? "ACTIVO" : "INACTIVO";
                                echo $estado_oficina;
                                ?>
                            </td>
                            <td>
                                <a href="/admin/oficinas/lista/<?php echo $o['id_entidad']; ?>"><?php echo $o['entidad']; ?></a>
                            </td>
                            <td>
                                <a href="/admin/oficinas/edit/<?php echo $o['id']; ?>"
                                   class="btn btn-sm btn-primary-dark"><i class="md md-edit"></i></a>
                                <!--
                                <a href="javascript:;" id_oficina="<?php echo $o['id']; ?>"
                                   class="btn btn-sm btn-danger"><i class="md md-delete"></i></a>
                                -->
                                <a href="/admin/oficinas/remove/<?php echo $o['id']; ?>"
                                   id_oficina="<?php echo $o['id']; ?>"
                                   class="btn btn-sm btn-danger"><i class="md md-delete"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

