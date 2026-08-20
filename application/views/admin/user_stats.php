<style>
    .card-clickable { cursor: pointer; }
    .card-clickable:hover { opacity: 0.85; }
</style>
<div class="row" style="padding: 15px;">
    <div class="col-md-12">
        <h4><?php echo HTML::chars($user->nombre); ?> <small class="opacity-70"><?php echo HTML::chars($user->cargo); ?></small></h4>
    </div>
</div>

<div class="row" style="padding: 0 15px 15px;">
    <div class="col-md-3 col-sm-6">
        <a href="/admin/content/userStatsList/<?php echo $user->id; ?>?tipo=entrada" class="card-clickable">
            <div class="card">
                <div class="card-body no-padding">
                    <div class="alert alert-callout alert-warning no-margin">
                        <strong class="text-warning text-lg">Entrada <i class="md md-inbox"></i></strong>
                        <strong class="text-xl"><?php echo $stats['norecibido']; ?></strong><br/>
                        <span class="opacity-50">No recibidos</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6">
        <a href="/admin/content/userStatsList/<?php echo $user->id; ?>?tipo=pendientes" class="card-clickable">
            <div class="card">
                <div class="card-body no-padding">
                    <div class="alert alert-callout alert-danger no-margin">
                        <strong class="text-danger text-lg">Pendientes <i class="md md-timer"></i></strong>
                        <strong class="text-xl"><?php echo $stats['pendientes']; ?></strong><br/>
                        <span class="opacity-50">Accion pendiente</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6">
        <a href="/admin/content/userStatsList/<?php echo $user->id; ?>?tipo=archivo" class="card-clickable">
            <div class="card">
                <div class="card-body no-padding">
                    <div class="alert alert-callout alert-info no-margin">
                        <strong class="text-info text-lg">Archivo <i class="fa fa-archive"></i></strong>
                        <strong class="text-xl"><?php echo $stats['archivo']; ?></strong><br/>
                        <span class="opacity-50">Correspondencia archivada</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6">
        <a href="/admin/content/userStatsList/<?php echo $user->id; ?>?tipo=documentos" class="card-clickable">
            <div class="card">
                <div class="card-body no-padding">
                    <div class="alert alert-callout alert-success no-margin">
                        <strong class="text-success text-lg">Documentos <i class="fa fa-file-text"></i></strong>
                        <strong class="text-xl"><?php echo $stats['documentos']; ?></strong><br/>
                        <span class="opacity-50">Documentos generados</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
