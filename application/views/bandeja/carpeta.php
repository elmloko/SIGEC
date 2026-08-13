<script>
    $(function () {
        $('a.desarchivar').click(function () {
            if (confirm("Esta usted seguro de desarchivar esta hoja de ruta?")) {
                return true;
            }
            else
            {
                return false;
            }
        });
    });
</script>
<div class="card card-underline">
    <div class="card-head">
        <div class="row">
            <header><i class="fa fa-archive"></i> <?php echo $carpetas->carpeta ?></header>
        </div>
    </div>
    <div class="card-body no-padding">
        <table class="table table-condensed table-bordered table-striped ">
            <thead>
                <tr>
                    <th>Hoja de Ruta</th>
                    <th>Documento</th>
                    <th>Referencia</th>
                    <th>Observaciones</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carpeta as $c): ?>
                    <tr>
                        <td><a href="/route/trace/?hr=<?php echo $c['nur'] ?>"><?php echo $c['nur']; ?></a></td>
                        <td><?php echo $c['codigo']; ?></td>            
                        <td><?php echo $c['referencia']; ?></td>
                        <td><?php echo $c['observaciones']; ?></td>
                        <td><?php echo $c['fecha_archivo']; ?></td>            
                        <td>
                            <?php if ($c['id_user'] == $user->id): ?>
                                <a href="/bandeja/unarchive/<?php echo $c['id_seg']; ?>"  title="Regresar el tramite a mis pendientes" class="btn btn-sm btn-danger desarchivar"><i class="fa fa-reply"> </i></a>
                            <?php endif; ?>
                        </td>            
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php // echo // $page_links; ?>
    </div>
</div>
<div class="alert alert-info text-center">

    &larr;<a onclick="javascript:history.back();
            return false;" href="#" style=" text-decoration: underline;  " > Regresar<a/></p>    
</div>