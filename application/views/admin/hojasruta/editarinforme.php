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
        <header>Editar informe <b><?php echo HTML::chars($documento->codigo); ?></b> (NUR <?php echo HTML::chars($documento->nur); ?>)</header>
        <tools class="pull-right">
            <a href="/route/trace/?hr=<?php echo urlencode($nur_origen); ?>" class="btn btn-sm btn-default">Volver al seguimiento</a>
        </tools>
    </div>
    <div class="card-body">
        <form action="" method="post" id="frmInforme" class="form form-validate">
            <div class="row">
                <?php if ($seguimiento->loaded()): ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo Form::input('fecha_paso', $seguimiento->fecha_emision, array('class' => 'form-control')); ?>
                        <label>Fecha del paso (linea de tiempo)</label>
                    </div>
                    <small class="opacity-70">Formato: AAAA-MM-DD HH:MM:SS. Es la fecha que se ve en "Seguimiento del proceso".</small>
                </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo Form::input('fecha_creacion', $documento->fecha_creacion, array('class' => 'form-control')); ?>
                        <label>Fecha de creacion del informe</label>
                    </div>
                    <small class="opacity-70">Formato: AAAA-MM-DD HH:MM:SS</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo Form::input('titulo', $documento->titulo, array('class' => 'form-control')); ?>
                        <label>Titulo</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('referencia', $documento->referencia, array('class' => 'form-control')); ?>
                        <label>Referencia</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::textarea('descripcion', $documento->contenido, array('class' => 'form-control', 'rows' => 6)); ?>
                        <label>Contenido</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo Form::input('remitente', $documento->nombre_remitente, array('class' => 'form-control')); ?>
                        <label>Remitente</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('cargo_rem', $documento->cargo_remitente, array('class' => 'form-control')); ?>
                        <label>Cargo del remitente</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('mosca', $documento->mosca_remitente, array('class' => 'form-control')); ?>
                        <label>Mosca</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('destinatario', $documento->nombre_destinatario, array('class' => 'form-control')); ?>
                        <label>Destinatario</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('cargo_des', $documento->cargo_destinatario, array('class' => 'form-control')); ?>
                        <label>Cargo del destinatario</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('institucion_des', $documento->institucion_destinatario, array('class' => 'form-control')); ?>
                        <label>Institucion del destinatario</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('copias', $documento->copias, array('class' => 'form-control')); ?>
                                <label>Copias</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('hojas', $documento->hojas, array('class' => 'form-control')); ?>
                                <label>Hojas</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" name="editar" value="Guardar cambios" class="btn btn-sm btn-primary-dark"/>
            </div>
        </form>
    </div>
</div>

<div class="card card-underline">
    <div class="card-head">
        <header>Archivo adjunto</header>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" action="">
            <input type="file" class="file" name="archivo" accept="application/pdf" required/>
            <label>Seleccione un archivo para subir (reemplaza/agrega el adjunto de este informe)...</label>
            <input type="submit" name="adjuntar" value="Subir archivo" class="btn btn-sm btn-primary-dark"/>
        </form>
        <hr/>
        <div class="table-responsive">
            <table class="table no-margin">
                <thead>
                <tr>
                    <th>NOMBRE ARCHIVO</th>
                    <th>TAMA&Ntilde;O</th>
                    <th>FECHA DE SUBIDA</th>
                    <th>OPCION</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($archivos as $a): ?>
                    <tr>
                        <td>
                            <a href="/download/?file=<?php echo $a->id; ?>"><?php echo substr($a->nombre_archivo, 13) ?></a>
                        </td>
                        <td align="center"><?php echo number_format(($a->tamanio / 1024) / 1024, 2) . ' MB'; ?></td>
                        <td align="center"><?php echo $a->fecha ?></td>
                        <td align="center">
                            <a href="/admin/hojasruta/eliminararchivoinforme/<?php echo $a->id; ?>?seg=<?php echo $seguimiento->id; ?>&nur=<?php echo urlencode($nur_origen); ?>"
                               onclick="return confirm('¿Eliminar este adjunto?');"
                               class="delete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($archivos) === 0): ?>
                    <tr>
                        <td colspan="4"><span class="opacity-50">Sin adjuntos</span></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
