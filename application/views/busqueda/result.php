<style type="text/css">
    td{margin: 0; padding:0 ; }

</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header><h4><?php echo $descripcion ?></h4></header>
            </div>
            <div class="card-body no-padding">

                <div class=" table-responsive">
                    <?php if ($count > 0) { ?>
                        <table id="" class="table table-bordered no-padding table-striped text-sm ">
                            <thead>
                                <tr>
                                    <th width="150">Entidad</th>
                                    <th width="150">Hoja de ruta</th>            
                                    <th width="160">Documento</th>            
                                    <th width="120">Tipo Doc</th>            
                                    <th width="285">Destinatario</th>
                                    <th width="285">Remitente</th>
                                    <th width="300">Referencia</th>    
                                    <th width="100">Fecha</th>    
                                    <th>Acción</th>    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $mes = 0;
                                $dia = 0;
                                $meses = array(1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic');
                                //    $meses=array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
                                // $dias=array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>'Domingo');
                                foreach ($results as $d):
                                    ?>
                                    <tr>
                                        <td ><b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['institucion_remitente']); ?></b></td>
                                        <td class="codigo" align="center">
                                            <a href="/route/trace/?hr=<?php echo $d['nur']; ?>"><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nur']); ?>
                                            </a> 
                                        </td>
                                        <td class="codigo" align="center">
                                            <a href="/document/detail/<?php echo $d['id']; ?>" ><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cite_original']); ?></a>               
                                        </td>
                                        <td align="center">
                                            <b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['tipo']); ?></b>
                                        </td>
                                        <td ><b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nombre_destinatario']); ?></b>
                                            <br/><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cargo_destinatario']); ?></td>
                                        <td ><b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nombre_remitente']); ?></b>
                                            <br/><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cargo_remitente']); ?></td>
                                        <td ><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['referencia']); ?></td>           
                                        <td ><?php echo $d['fecha_creacion']; ?></td>           
                                        <td ><?php if ($d['estado'] == 0 && $d['original'] == 1): ?>
                                                No Derivado          
                                            <?php else: ?>
                                                <a href="/route/trace/?hr=<?php echo $d['nur']; ?>" title="Ver seguimiento" class="text-success text-xl" ><i class="md md- md-verified-user "></i></a>           
                                            <?php endif; ?>                                        
                                        </td>
                                    </tr>        
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9"><?php echo $page_links; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php } else { ?>
                        <div class="info">
                            <p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>
                                <strong>Lo siento! </strong> Ningun proceso/tramite encontrado. Pruebe con <a href="/search/advanced" title="Ir a" style="text-decoration: underline;" > Busqueda avanzada</a></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>