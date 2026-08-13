<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-building-o"></i> Lista de Usuarios</header>
                <div class="pull-right">
                    <a href="/admin/user/add" class="btn btn-primary-dark"><i class="fa fa-plus"></i> Nuevo Usuario</a>
                </div>
            </div>
            <div class="card-body">
                <table id="theTable" class="table table-bordered table-striped table-responsive">
                    <thead>
                    <tr>
                        <th>
                            NOMBRE DE USUARIO
                        </th>
                        <th>
                            NOMBRE
                        </th>
                        <th>
                            CARGO
                        </th>
                        <!--
                        <th>
                            OPCIONES
                        </th>
                        -->
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <?php echo $u->username ?>
                            </td>
                            <td>
                                <?php echo $u->nombre ?>
                            </td>
                            <td>
                                <?php echo $u->cargo ?>
                            </td>
                            <!--
                            <td>
                                <a href="/admin/user/detalle/<?php echo $u->id ?>" title="Cambiar contraseña"><img
                                        src="/media/images/16x16/key.png"/></a>
                                <a href="/admin/user/detalle/<?php echo $u->id ?>" title="Dar de Baja"><img
                                        src="/media/images/16x16/down.png"/></a>
                            </td>
                            -->
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>