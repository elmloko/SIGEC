<div class="row">
    <?php if (sizeof($carpetas) > 0) { ?>
        <?php foreach ($carpetas as $c): ?>
            <div class="col-md-3">
                <div class="card ">
                    <div class="card-body   no-padding">
                        <div class="alert alert-callout  alert-info no-margin">
                            <a class="mt" style=" display: block;" href="/bandeja/folder/<?php echo $c->id; ?>">  <strong class="opacity-75 text-medium">Procesos:  <i class="md md-audiotracs"></i></strong>
                                <h1 class="pull-right opacity-75"><i class="fa fa-folder-open"></i></h1>
                                <strong class="text-xl"><?php echo $c->cc ?></strong><br>
                                <span class="opacity-50 text-primary-dark"><?php echo $c->carpeta ?></span>
                            </a>
                        </div>
                    </div><!--end .card-body -->
                </div>
            </div>
        <?php endforeach; ?>    

    <?php } else { ?>
        <div class="info">
            <b>Bandeja Vacia!</b> Usted no tiene correspondencia archivada.
        </div>
    <?php } ?>

</div>