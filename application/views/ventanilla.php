<style>
    .mt:hover,.mt:focus{ text-decoration: none !important;  ;   }
</style>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <div class="card">
            <div class="card-body no-padding">
                <div class="alert alert-callout alert-success no-margin">
                    <a  href="/ventanilla" style=" display: block;" class="mt"> 
                        <strong class="text-success text-lg">Recepcionar  </strong>
                        <h1 class="pull-right text-success"><i class="fa fa-file-text-o"></i></h1>
                        <strong class="text-xl"></strong><br/>
                        <span class="opacity-50">Registro de Corresppondencia</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <?php foreach ($estados as $k => $v): ?>      
        <!-- BEGIN ALERT - TIME ON SITE -->
        <div class="col-md-4 col-sm-6">
            <div class="card animation-expand">
                <div class="card-body   no-padding">
                    <div class="alert alert-callout  alert-<?php echo $v['color'] ?> no-margin">
                        <a  href="<?php echo $v['accion'] ?>" style=" display: block;" class="mt">  <strong class="text-<?php echo $v['color'] ?> text-lg"><?php echo $v['titulo'] ?> :  <i class="md md-audiotracs"></i></strong>
                            <h1 class="pull-right text-<?php echo $v['color'] ?>"><i class="<?php echo $v['icon'] ?>"></i></h1>
                            <strong class="text-xl"><?php echo number_format($v['cantidad'],0,',','.'); ?></strong><br/>
                            <span class="opacity-50"><?php echo $v['descripcion'] ?></span>
                        </a>
                    </div>
                </div><!--end .card-body -->
            </div><!--end .card -->
        </div><!--end .col -->
        <!-- END ALERT - TIME ON SITE -->
    <?php endforeach; ?>
    <!-- BEGIN ALERT - TIME ON SITE -->
    
    <!-- END ALERT - TIME ON SITE -->

</div>
<div class="row">

    <!-- BEGIN SITE ACTIVITY -->

    <div class="col-md-12">

        <div class="card card-underline">
            <div class="card-head ">
                <header class="text-primary "><i class="fa fa-file-text text-primary"></i> Correspondencia recibida recientemente</header>
                <div class="tools">
                    <div class="btn-group">
                        <button class="btn ink-reaction btn-sm btn-success" type="button"><i class="fa fa-file-text"></i> Generar</button>
                        <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-success dropdown-toggle" type="button" aria-expanded="false"><i class="fa fa-caret-down"></i></button>
                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                            <?php foreach ($tipos as $t): ?> 
                                <li><a href="/documento/generar/<?php echo $t['accion'] ?>"  id="hojaruta"><?php echo $t['tipo'] ?></a></li>
                            <?php endforeach; ?>
                            <li class="divider"></li>

                        </ul>
                    </div>

                </div>
            </div><!--end .card-head -->
            <div class="card-body no-padding height-10 scroll style-default-bright">
                <div class="table-responsive no-margin">
                    <table class="table table-striped no-margin">

                        <tbody>
                            <?php foreach ($zdoc as $d): ?>
                                <tr>
                                    <td class=" text-sm">                                        
                                        <small class="text-bold"><?php echo $d['cite_original'] ?></small><br/>
                                        <a href="/route/trace/?hr=<?php echo $d['nur'] ?>" class=" text-primary"><?php echo $d['nur'] ?></a>
                                    </td>
                                    <td class=" text-sm">
                                        <h4><?php echo $d['referencia'] ?></h4>
                                        <span>
                                            <?php echo $d['nombre_destinatario'] ?>
                                            |  <?php echo $d['cargo_destinatario'] ?>        
                                        </span>
                                        <small class="text-default-light pull-right"><?php echo $d['fecha_creacion'] ?></small>

                                    </td>
                                    <td class=" text-sm">
                                        <a href="/documento/edit/<?php echo $d['id'] ?>" class="btn btn-xs text-default-light"><i class="fa fa-pencil-square-o fa-2x"></i></a>
                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div>
    </div><!--end .col -->

    <div class="clearfix" id="bos-main-blocks">
        
        <hr/>
    </div>
