<?php if (sizeof($error) > 0): ?>
    <div class="error">
        <p><span style="float: left; margin-right: .3em;" class=""></span>
            <?php foreach ($error as $k => $v): ?>
            <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (sizeof($info) > 0): ?>
    <div class="info">
        <p><span style="float: left; margin-right: .3em;" class=""></span>
            <?php foreach ($info as $k => $v): ?>
            <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card card-underline">
    <div class="card-head">
        <header>Editar documento de la hoja de ruta <b><?php echo HTML::chars($documento->nur); ?></b></header>
        <tools class="pull-right">
            <a href="/admin/hojasruta/lista" class="btn btn-sm btn-default">Volver al listado</a>
        </tools>
    </div>
    <div class="card-body">
        <form action="" method="post" id="frmDocumento" class="form form-validate">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo Form::input('nur', $documento->nur, array('class' => 'form-control')); ?>
                        <label>NUR (Hoja de Ruta)</label>
                    </div>
                    <small class="opacity-70">Cambiar el NUR actualiza en cascada el seguimiento, agrupaciones y todos los documentos de este expediente.</small>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo Form::input('codigo', $documento->codigo, array('class' => 'form-control')); ?>
                        <label>Codigo del documento</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo Form::input('fecha_creacion', $documento->fecha_creacion, array('class' => 'form-control')); ?>
                        <label>Fecha de creacion</label>
                    </div>
                    <small class="opacity-70">Formato: AAAA-MM-DD HH:MM:SS</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo Form::input('cite_original', $documento->cite_original, array('class' => 'form-control')); ?>
                        <label>Cite</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('referencia', $documento->referencia, array('class' => 'form-control')); ?>
                        <label>Referencia</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('nombre_remitente', $documento->nombre_remitente, array('class' => 'form-control')); ?>
                        <label>Remitente</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('cargo_remitente', $documento->cargo_remitente, array('class' => 'form-control')); ?>
                        <label>Cargo del remitente</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo Form::input('nombre_destinatario', $documento->nombre_destinatario, array('class' => 'form-control')); ?>
                        <label>Destinatario</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('cargo_destinatario', $documento->cargo_destinatario, array('class' => 'form-control')); ?>
                        <label>Cargo del destinatario</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" name="editar" value="Guardar cambios" class="btn btn-sm btn-primary-dark"/>
            </div>
        </form>
    </div>
</div>
