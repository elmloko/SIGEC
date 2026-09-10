<?php if (sizeof($errors) > 0): ?>
    <div class="error">
        <p><span style="float: left; margin-right: .3em;" class=""></span>
            <?php foreach ($errors as $k => $v): ?>
            <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card card-underline">
    <div class="card-head">
        <header><i class="fa fa-user-plus"></i> Agregar destinatario a la hoja de ruta <b><?php echo HTML::chars($ref->nur); ?></b></header>
        <tools class="pull-right">
            <a href="/route/trace/?hr=<?php echo $ref->nur; ?>" class="btn btn-sm btn-default">Volver al seguimiento</a>
        </tools>
    </div>
    <div class="card-body">
        <div class="alert alert-callout alert-warning">
            Se enviara una <b>copia</b> de esta hoja de ruta a la persona que elijas, <b>en representacion de
            <?php echo HTML::chars($ref->nombre_emisor); ?></b> (<?php echo HTML::chars($ref->cargo_emisor); ?> -
            <?php echo HTML::chars($ref->de_oficina); ?>), tal como si esa persona la hubiera derivado. Usa esto
            solo cuando el usuario olvido incluir a alguien en una derivacion que ya realizo.
        </div>

        <form method="post" action="/admin/hojasruta/agregar/<?php echo $ref->id; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo Form::select('destino', $destinatarios, Arr::get($_POST, 'destino', NULL), array('id' => 'destino', 'class' => 'form-control required')); ?>
                        <label>Destinatario</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo Form::select('accion', $acciones, Arr::get($_POST, 'accion', NULL), array('id' => 'accion', 'class' => 'form-control required')); ?>
                        <label>Accion</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <?php echo Form::textarea('proveido', Arr::get($_POST, 'proveido', ''), array('rows' => 2, 'class' => 'form-control required', 'id' => 'proveido')); ?>
                        <label>Proveido</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" name="enviar" value="1" class="btn btn-sm btn-primary-dark">
                    <i class="md md-person-add"></i> Agregar destinatario (copia)
                </button>
                <a href="/route/trace/?hr=<?php echo $ref->nur; ?>" class="btn btn-sm btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#destino, #accion').select2();
    });
</script>
