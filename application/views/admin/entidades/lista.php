
<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-building-o"></i> Lista de Entidades</header>
                <div class="pull-right">
                    <a href="/admin/entidades/nuevo" class="btn btn-primary-dark"><i class="fa fa-plus"></i> Nueva Entidad</a>
                </div>
            </div>
            <div class="card-body">
                <table id="theTable" class="table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th>
                                id
                            </th>
                            <th>
                                Sigla
                            </th>
                            <th>
                                Entidad
                            </th>
                            <th>
                                Estado
                            </th>
                            <th>
                                Opciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entidades as $e): ?>
                            <tr>
                                <td>
                                    <?php
                                    $foto = 'static/logos/' . $e->logo;
                                    if (file_exists(DOCROOT . $foto)) {
                                        $foto = 'static/logos/' . $e->logo;
                                    } else {
                                        $foto = 'static/logos/entidad.jpg';
                                    }
                                    ?> 
                                    <img src="/<?php echo $foto ?>?t=<?php echo time(); ?>" height="45" width="120" alt="" class=" img-rounded" style="margin: 0">                                        
                                </td>

                                <td>
                                    <a href="/admin/oficinas/lista/<?php echo $e->id; ?>"><?php echo $e->sigla; ?></a> 
                                </td>
                                <td>
                                    <a href="/admin/oficinas/lista/<?php echo $e->id; ?>"><?php echo $e->entidad; ?></a> 
                                </td>
                                <td>
                                    <?php
                                    $checked = TRUE;
                                    if ($e->estado == 0)
                                        $checked = FALSE;
                                    echo Form::checkbox('estado', $e->estado, $checked);
                                    ?> 
                                </td>
                                <td>
                                    <a href="/admin/entidades/edit/<?php echo $e->id; ?>" class="btn btn-sm btn-primary-dark"> <i class="md md-edit"></i></a>               
                                    <a href="/admin/entidades/logo/<?php echo $e->id; ?>" class="btn btn-sm btn-accent"> <i class="md md-picture-in-picture"></i></a>               
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>