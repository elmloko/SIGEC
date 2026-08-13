<?php ?>
<script type="text/javascript">
    $(function () {
//incluir destinatario
        $('a.destino').click(function () {
            var nombre = $(this).attr('nombre');
            var cargo = $(this).attr('cargo');
            var via = $(this).attr('institucion');
            $('#destinatario').val(nombre);
            $('#cargodes').val(cargo);
            $('#instituciondes').val(via);
            $('#referencia').focus();
            return false;
        });
        $('#btnword').click(function () {
            $('#word').val(1);
            return true

        });
        $('#crear').click(function () {
            $('#word').val(0);
            return true
        });
    });
</script>

<div id="documento">

</div>
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header>
                <span class="text-medium">Formulario de recepci&oacute;n de documentos</span>
            </header>
        </div>
        <div class=" card-body">
            <form class="form" method="post" enctype="multipart/form-data" id="frmCreate">
                <div class="col-md-12 col-lg-8">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo Form::input('cite', Arr::get($_POST, 'cite', ''), array('id' => 'cite', 'index' => 1, 'class' => 'form-control required'));
                                echo Form::label('cite', 'Cite original:', array('class' => ''));
                                ?> 
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('destinatario', Arr::get($_POST, 'destinatario', ''), array('id' => 'destinatario', 'index' => 2, 'class' => 'form-control required'));
                                echo Form::label('Destinatario', 'Destinatario:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargodes', Arr::get($_POST, 'cargodes', ''), array('id' => 'cargodes', 'index' => 3, 'class' => 'form-control required', 'title' => 'Ejemplo: Lo citado'));
                                echo Form::label('cargodes', 'Cargo destinatario:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('instituciondes', Arr::get($_POST, 'instituciondes', ''), array('id' => 'instituciondes', 'index' => 4, 'class' => 'form-control required'));
                                echo Form::label('insituciondes', 'Institucion Destinatario:', array('class' => ''));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo Form::input('submit', 'Recepcionar Documento', array('type' => 'submit', 'index' => 10, 'class' => 'btn  btn-primary-dark', 'id' => 'crear'));
                                ?> 

                                <?php
                                //echo Form::input('cite', Arr::get($_POST, 'cite', ''), array('id' => 'cite', 'size' => 40, 'class' => 'form-control required'));
                                //echo Form::label('cite', 'Cite original:', array('class' => ''));
                                ?>
                                <label class="text-xl" for="submit">    </label>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('remitente', '', array('id' => 'remitente', 'class' => 'form-control required', 'index' => 6));
                                echo Form::label('remitente', 'Remitente:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargorem', '', array('id' => 'cargorem', 'class' => 'form-control required', 'index' => 7));
                                echo Form::label('cargorem', 'Cargo Remitente:', array('class' => ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('institucionrem', '', array('id' => 'intitucionrem', 'class' => 'form-control required', 'index' => 8));
                                echo Form::label('institucionrem', 'Institucion Remitente:', array('class' => ''));
                                ?>  
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <textarea id="descripcion" name="descripcion" class="form-control" index="5"></textarea>
                                <?php
                                echo Form::label('referencia', 'Referencia:', array('class' => ''));
                                ?> 
                            </div> 
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    echo Form::select('motivo', $motivos, '', array('id' => 'motivo', 'class' => 'form-control'));
                                    echo Form::label('motivo', 'Motivo:');
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    echo Form::input('adjunto', '', array('id' => 'adjunto', 'class' => 'form-control'));
                                    echo Form::label('adjunto', 'Adjunto:');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <?php
                                    echo Form::input('hojas', '', array('id' => 'hojas', 'class' => 'form-control'));
                                    echo Form::label('hojas', 'N° hojas:');
                                    ?>
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <?php echo Form::label('dest', 'Destinatarios:'); ?>
                            <div id="vias">
                                <ul>
                                    <?php foreach ($destinos as $d): ?>
                                        <li class="<?php echo $d->genero ?>"><?php echo HTML::anchor('#', $d->nombre, array('class' => 'destino', 'nombre' => $d->nombre, 'title' => $d->cargo, 'cargo' => $d->cargo, 'institucion' => $d->entidad)); ?></li>
                                    <?php endforeach; ?>            
                                </ul>
                            </div>
                        </div>
                    </div>


                </div>
            </form>
        </div>
    </div>
</div>

<div style="background-color:#efefef; width: 850px;">
    <form action="" method="post" enctype="multipart/form-data" id="frmCreate">
        <table width="100%">
            <tr>
                <td colspan="3">
                    <p>
                        <?php
                        echo Form::input('submit', 'Modificar', array('type' => 'submit', 'class' => 'button2', 'id' => 'crear'));
                        ?>    
                        <a href="/print.php/?code=<?php echo $documento->nur; ?>" class="button" title="Imprimir hoja de ruta" > Imprimir Hoja de Ruta</a>
                        <a href="/route/deriv/?hr=<?php echo $documento->nur; ?>" class="button" title="Derivar a partir del documento, si ya esta derivado muestra el seguimiento" >Derivar</a>
                        <a href="/ventanilla" class="button" title="Imprimir hoja de ruta" > + Recepcionar otro</a>
                    </p></td>
            </tr>
            <tr>
                <td colspan="3">
                    <?php if (sizeof($error) > 0): ?>
                        <div class="error">
                            <p><span style="float: left; margin-right: .3em;" class=""></span>
                                <?php foreach ($error as $k => $v): ?>
                                    <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                            <?php endforeach; ?>
                        </div>
                        <br/>
                    <?php endif; ?>
                    <?php if (sizeof($info) > 0): ?>
                        <div class="info2">
                            <p><span style="float: left; margin-right: .3em;" class=""></span>
                                <?php foreach ($info as $k => $v): ?>
                                    <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                            <?php endforeach; ?>
                        </div>
                        <br/>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style=" padding-left: 5px;">
                    <?php
                    echo Form::label('cite', 'Cite original:', array('class' => 'form'));
                    echo Form::input('cite', $documento->cite_original, array('id' => 'cite', 'size' => 40, 'class' => 'required'));
                    ?> 
                    <?php
                    echo Form::label('Destinatario', 'Destinatario:', array('class' => 'form'));
                    echo Form::input('destinatario', $documento->nombre_destinatario, array('id' => 'destinatario', 'size' => 40, 'class' => 'required'));
                    ?>
                    <?php
                    echo Form::label('cargodes', 'Cargo destinatario:', array('class' => 'form'));
                    echo Form::input('cargodes', $documento->cargo_destinatario, array('id' => 'cargodes', 'size' => 40, 'class' => 'required', 'title' => 'Ejemplo: Lo citado'));
                    ?>
                    <?php
                    echo Form::label('insituciondes', 'Institucion Destinatario:', array('class' => 'form'));
                    echo Form::input('instituciondes', $documento->institucion_destinatario, array('id' => 'instituciondes', 'size' => 40, 'class' => 'required'));
                    ?>
                </td>
                <td style=" padding-left: 5px;">
                    <?php
                    echo Form::label('fecha', 'Fecha del documento:', array('class' => 'form'));
                    echo Form::select('dia', $days, date('d', strtotime($documento->fecha_creacion)));
                    echo Form::select('mes', $months, date('m', strtotime($documento->fecha_creacion)));
                    echo Form::select('year', $years, date('Y', strtotime($documento->fecha_creacion)));
                    ?>    
                    <?php
                    echo Form::label('remitente', 'Remitente:', array('class' => 'form'));
                    echo Form::input('remitente', $documento->nombre_remitente, array('id' => 'remitente', 'size' => 40, 'class' => 'required'));
                    ?>
                    <?php
                    echo Form::label('cargorem', 'Cargo Remitente:', array('class' => 'form'));
                    echo Form::input('cargorem', $documento->cargo_remitente, array('id' => 'cargorem', 'size' => 40, 'class' => 'required'));
                    ?>
                    <?php
                    echo Form::label('institucionrem', 'Intitucion Remitente:', array('class' => 'form'));
                    echo Form::input('institucionrem', $documento->institucion_remitente, array('id' => 'intitucionrem', 'size' => 40, 'class' => 'required'));
                    ?>     
                </td>

                <td rowspan="2" style="padding-left: 5px;">
                    <?php echo Form::label('dest', 'Destinatarios:'); ?>
                    <div id="vias">
                        <ul>
                            <?php foreach ($destinos as $d): ?>
                                <li class="<?php echo $d->genero ?>"><?php echo HTML::anchor('#', $d->nombre, array('class' => 'destino', 'nombre' => $d->nombre, 'title' => $d->cargo, 'cargo' => $d->cargo, 'institucion' => $d->entidad)); ?></li>
                            <?php endforeach; ?>            
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left: 5px;">
                    <?php
                    echo Form::label('referencia', 'Referencia:', array('class' => 'form'));
                    ?>
                    <textarea id="descripcion" name="descripcion" style="width:500px;"><?php echo $documento->referencia; ?></textarea>    
                    <br/>    
                    <br/>
                </td>
            </tr>
        </table>
        <table>
            <tr>
               <!-- <td>        
                <?php
                // echo Form::label('grupo','Grupo Remitente:');
                // echo Form::select('grupo',$grupos,'',array('id'=>'grupo','style'=>'width:240px;')); 
                ?>
                </td>
                -->
                <td>
                    <?php
                    echo Form::label('motivo', 'Motivo:');
                    echo Form::select('motivo', $motivos, '', array('id' => 'motivo', 'style' => 'width:240px;'));
                    ?>
                </td>
                <!--<td>
                <?php
                // echo Form::label('proceso','Proceso:');
                // echo Form::select('proceso',$procesos,'',array('id'=>'proceso','style'=>'width:240px;')); 
                ?>
                </td>
                -->
                <td>        
                    <?php
                    echo Form::label('adjunto', 'Adjunto:');
                    echo Form::input('adjunto', $documento->adjuntos, array('id' => 'adjunto', 'size' => 40));
                    ?>
                </td>
                <td>
                    <?php
                    echo Form::label('hojas', 'N° de hojas:');
                    echo Form::input('hojas', $documento->hojas, array('id' => 'hojas'));
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3"><br/></td>
            </tr>
            <tr>            
                <?php if (isset($archivo->id)): ?>
                    <td>
                        <a href="/download/?file=<?php echo $archivo->id; ?>" style="color: #f2f2f2; border: 1px solid #111; padding:3px; width: 90px;  background-color: #555555; text-decoration: underline; display: block;   "><?php echo substr($archivo->nombre_archivo, 13); ?></a>
                        <input type="hidden" value="<?php echo $archivo->id; ?>" name="id_archivo"/>    
                    </td>
                    <td colspan="2">        
                        <label for="" style=" font-weight: bold; color:#333;">Cambiar documento escaneado (.pdf < 20M)</label>
                        <input type="file" name="archivo"/>
                        <?php
                        echo Form::input('submit', 'Cambiar documento', array('type' => 'submit', 'class' => 'button2', 'id' => 'crear'));
                        ?>
                    </td>
                <?php else: ?>
                    <td colspan="2">   
                        <input type="hidden" value="0" name="id_archivo"/>
                        <label for="" style=" font-weight: bold; color:#333;">Subir documento escaneado (.pdf < 20M)</label>
                        <input type="file" name="archivo"/>
                        <?php
                        echo Form::input('submit', 'Subir documento', array('type' => 'submit', 'class' => 'button2', 'id' => 'crear'));
                        ?>
                    </td>
                <?php endif; ?>        
            </tr>
        </table>
        <br/>
    </form>
</div>
