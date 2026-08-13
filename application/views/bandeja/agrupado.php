
<div class="row">

    <div class="col-md-12">

        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-link"></i> Correspondencia Agrupada</header>
                <div class=" pull-right">
                    <a href="/print/agrupado/?code=<?php echo $padre->nur; ?>" target="_blank" class="btn btn-sm btn-primary-dark"><i class="fa fa-print"></i> Imprimir</a>
                </div>
            </div>
            <div class="card-body">
                <header ><h4 class=" text-accent-dark">Principal : <a href="/route/trace/?hr=<?php echo $padre->nur; ?>"><?php echo $padre->nur; ?></a></h4></header>
                <div class="row">
                    <div class="col-md-2">
                        Cite Original:
                    </div>
                    <div class="col-md-10">
                        <?php echo $padre->cite_original; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Referencia:
                    </div>
                    <div class="col-md-10">
                        <?php echo $padre->referencia; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Destinatario:
                    </div>
                    <div class="col-md-10">
                        <?php echo $padre->nombre_destinatario; ?>
                        <br/><?php echo $padre->cargo_destinatario; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        Remitente:
                    </div>
                    <div class="col-md-10">
                        <?php echo $padre->nombre_remitente; ?>
                        <br/><?php echo $padre->cargo_remitente; ?>
                    </div>
                </div>
                <hr>
                <header ><h4 class=" text-accent-dark">Agrupados</h4></header>
                <table id="" class="table table-bordered">
                    <?php foreach ($hijos as $h): ?>
                        <tr>
                            <td>
                                <a href="/route/trace/?hr=<?php echo $h['nur']; ?>"><?php echo $h['nur']; ?></a>                        
                            </td>
                            <td>
                                <?php echo $h['documento']; ?>
                            </td>
                            <td>
                                <?php echo $h['referencia']; ?>
                            </td>
                            <td>
                                <?php echo $h['destinatario']; ?>
                                <br/><b><?php echo $h['cargo']; ?></b>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </table>    
            </div>
        </div>

        <div class="">

        </div>
    </div>
</div>
<?php // print_r($hijos); ?>
<!--
<div class="info" style="text-align:center;">
    <p><span style="float: left; margin-right: .3em;" class=""></span>    
        &larr;<a onclick="javascript:history.back();
                return false;" href="#" style="font-weight: bold; text-decoration: underline;  " > Regresar<a/></p>    
</div>
-->
<div class="info" style="display:none; text-align:center;">
    <p><span style="float: left; margin-right: .3em;" class=""></span>    
        &larr;<a onclick="" href="/bandeja/pendientes" style="font-weight: bold; text-decoration: underline;  " > Regresar<a/></p>    
</div>