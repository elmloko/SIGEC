<div class="row" style="padding: 15px;">
    <div class="col-md-12">
        <h4><?php echo HTML::chars($titulo); ?>
            <small class="opacity-70">de <?php echo HTML::chars($user->nombre); ?></small>
        </h4>
        <a href="/admin/content/userStats/<?php echo $user->id; ?>" class="btn btn-sm btn-default">
            <i class="fa fa-arrow-left"></i> Volver a estadisticas
        </a>
        <br/><br/>
    </div>
</div>

<div class="row" style="padding: 0 15px 15px;">
    <div class="col-md-12">
        <table class="table table-condensed table-striped">
            <?php if ($tipo === 'documentos'): ?>
                <thead>
                <tr>
                    <th>CODIGO</th>
                    <th>CITE</th>
                    <th>NUR</th>
                    <th>REFERENCIA</th>
                    <th>DESTINATARIO</th>
                    <th>FECHA</th>
                    <th>OPCIONES</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result as $d): ?>
                    <tr>
                        <td><?php echo HTML::chars($d->codigo); ?></td>
                        <td><?php echo HTML::chars($d->cite_original); ?></td>
                        <td><b><?php echo HTML::chars($d->nur); ?></b></td>
                        <td><?php echo HTML::chars($d->referencia); ?></td>
                        <td><?php echo HTML::chars($d->nombre_destinatario); ?><br/>
                            <small class="opacity-70"><?php echo HTML::chars($d->cargo_destinatario); ?></small>
                        </td>
                        <td><?php echo Date::fecha_corta($d->fecha_creacion); ?></td>
                        <td>
                            <?php if ($d->nur !== ''): ?>
                                <a href="/route/trace/?hr=<?php echo $d->nur; ?>" target="_blank"
                                   class="btn btn-xs btn-primary" title="Ver seguimiento">
                                    <i class="md md-visibility"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <thead>
                <tr>
                    <th>NUR</th>
                    <th>CODIGO</th>
                    <th>REFERENCIA</th>
                    <th>DESTINATARIO</th>
                    <th>ACCION</th>
                    <th>FECHA</th>
                    <th>DIAS</th>
                    <th>OPCIONES</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result as $r): ?>
                    <tr>
                        <td><b><?php echo HTML::chars($r->nur); ?></b></td>
                        <td><?php echo HTML::chars($r->codigo); ?></td>
                        <td><?php echo HTML::chars($r->referencia); ?></td>
                        <td><?php echo HTML::chars($r->nombre_destinatario); ?><br/>
                            <small class="opacity-70"><?php echo HTML::chars($r->cargo_destinatario); ?></small>
                        </td>
                        <td><?php echo HTML::chars($r->accion); ?></td>
                        <td><?php echo Date::fecha_corta($r->fecha); ?></td>
                        <td><?php echo HTML::chars($r->dias); ?></td>
                        <td>
                            <a href="/route/trace/?hr=<?php echo $r->nur; ?>" target="_blank"
                               class="btn btn-xs btn-primary" title="Ver seguimiento">
                                <i class="md md-visibility"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php endif; ?>
        </table>
        <?php if (sizeof($result) == 0): ?>
            <div class="info">
                <p>No hay registros para mostrar.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
