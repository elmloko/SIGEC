<script type="text/javascript">
    $(function() {
        $('.destino1').click(function() {
            var nombre = $(this).attr('nombre');
            var cargo = $(this).attr('cargo');
            var via = $(this).attr('via');
            var cargo_via = $(this).attr('cargo_via');
            $('#destinatario').val(nombre);
            $('#cargo_des').val(cargo);
            $('#via').val(via);
            $('#cargovia').val(cargo_via);
            $('#referencia').focus();
            return false;
        });
        $('#noHojaRuta').click(function() {
            $('#hojaruta').val(0);
            return true
        });
        $('#cite_sup').click(function() {
            $('#cite_superior').val(1);
            $('#hojaruta').val(0);
            stat = 1;
            return true
        });
        $('#addDest').click(function() {
            var id_user = $(this).attr('rel');
            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 800) / 2;
            top = (top - 500) / 2;
            var res = window.showModalDialog("/content/destinos/" + id_user, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            if (res != null)
            {
                $("#destinatarios").addClass('loading');
                $.ajax({
                    type: "POST",
                    data: {destinos: res, id: id_user},
                    url: "/ajax/addUser",
                    // dataType: "html",
                    success: function(data)
                    {
                        location.reload(true);
                    }
                });
            }
        });
        /* $("#theTable").tablesorter({sortList:[[1,0]], 
         widgets: ['zebra'],
         headers: {             
         0: { sorter:false}                    
         }
         }); */
    });
    $(function() {
        var tabContainers = $('div.tabs > div');
        tabContainers.hide().filter(':first').show();
        $('div.tabs ul.tabNavigation a').click(function() {
            tabContainers.hide();
            tabContainers.filter(this.hash).show();
            $('div.tabs ul.tabNavigation a').removeClass('selected');
            $(this).addClass('selected');
            return false;
        }).filter(':first').click();

        $('#descripcion').redactor({lang: 'es', css: 'docstyle.css'});
//incluir destinatario
        $('a.destino').click(function() {
            var nombre = $(this).attr('nombre');
            var cargo = $(this).attr('cargo');
            var via = $(this).attr('via');
            var cargo_via = $(this).attr('cargo_via');
            $('#destinatario').val(nombre);
            $('#cargo_des').val(cargo);
            $('#via').val(via);
            $('#cargovia').val(cargo_via);
            $('#referencia').focus();
            return false;
        });



        $('#btnword').click(function() {
            $('#word').val(1);
            return true

        });
        $('#save').click(function() {
            $('#frmEditar').submit();
        });
        $('#subir').click(function() {
            var id = $(this).attr('rel');
            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 700) / 2;
            top = (top - 500) / 2;
            var r = window.showModalDialog("/archivo/add/" + id, "", "center:0;dialogWidth:600px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            alert(r);
            return false;
        });
      //  $("input.file").si();
    });
</script>
<style type="text/css">
    form#frmCreate{ padding: 0 5px; margin: 0;}
    .cke_contents{height: 500px;}
    cke_skin_kama{border: none;}
    div.si label.cabinet {
        width: 156px;
        height: 34px;
        display: block;
        overflow: hidden;
        position: relative;
        z-index: 3;
        float: left;
        cursor: pointer; 
    }
    div.si label.cabinet input {
        position: relative;
        left: -140px;
        top: 0;
        height: 100%;
        width: auto !important;
        z-index: 2;
        opacity: 0;
        -moz-opacity: 0;
        filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
    }
    div.si div.uploadButton {
        position: relative;
        float: left;
    }
    div.si div.uploadButton div {
        width: 156px;
        height: 34px;
        background: url(/media/images/examinar.png) 0 0 no-repeat;
        left: -156px;
        position: absolute;
        z-index: 1;

    }
    div.si label.selectedFile {
        margin-left: 5px;
        line-height: 34px;
    }

</style>

<div class="tabs">
    <ul class="tabNavigation">
        <li><a href="#editar">Edición</a></li>
        <li><a href="#adjuntos">Adjuntos</a></li>        
    </ul>
    <div id="editar"> 

        <div class="formulario"  >  

            <form action="/documento/edit/<?php echo $documento->id; ?>" method="post" id="frmEditar" >  
                <?php if (sizeof($mensajes) > 0): ?>
                    <div class="info">
                        <p><span style="float: left; margin-right: .3em;" class="ui-icon-info"></span>
                            <?php foreach ($mensajes as $k => $v): ?>
                                <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>        
                <br/>    
                <table width="100%">
                    <tr>
                        <td style=" padding-left: 1px;" rowspan="2" valign="top">
                            <p>Proceso: <?php echo Form::select('proceso', $options, $documento->id_proceso); ?></p>
                            <?php if ($documento->id_tipo == 5): ?>
                                <p>
                                    <label>Titulo:</label>
                                    <?php $titulo = array('', 'Señor', 'Señora', 'Señores'); ?>
                                    <?php echo Form::select('titulo', $titulo, $documento->titulo); ?>
                                </p>
                            <?php else: ?>
                                <input type="hidden" name="titulo" />   
                            <?php endif; ?>
                            <p>    
                            <p>
                                <?php
                                echo Form::hidden('id_doc', $documento->id);
                                echo Form::label('destinatario', 'Nombre del destinatario:', array('class' => 'form'));
                                echo Form::input('destinatario', $documento->nombre_destinatario, array('id' => 'destinatario', 'size' => 45, 'class' => 'required'));
                                ?>
                            </p>
                            <p>
                                <?php
                                echo Form::label('destinatario', 'Cargo Destinatario:', array('class' => 'form'));
                                echo Form::input('cargo_des', $documento->cargo_destinatario, array('id' => 'cargo_des', 'size' => 45, 'class' => 'required'));
                                ?>
                            </p> 
                            <?php if ($documento->id_tipo == 5): ?>
                                <p>
                                    <label>Institución Destinatario</label>
                                    <input type="text" size="40" value="<?php echo $documento->institucion_destinatario; ?>" name="institucion_des" />    
                                </p>
                                <input type="hidden" size="40" value="" name="via" />    
                                <input type="hidden" size="40" value="" name="cargovia" />    
                            <?php else: ?>
                                <input type="hidden" size="40" value="" name="institucion_des" />    

                                <p>
                                    <?php
                                    echo Form::label('via', 'Via:', array('class' => 'form'));
                                    echo Form::input('via', $documento->nombre_via, array('id' => 'via', 'size' => 45, 'class' => 'required'));
                                    ?>
                                    <?php
                                    echo Form::label('cargovia', 'Cargo Via:', array('class' => 'form'));
                                    echo Form::input('cargovia', $documento->cargo_via, array('id' => 'cargovia', 'size' => 45, 'class' => 'required'));
                                    ?>
                                <?php endif; ?>

                            </p>
                            <p>
                                <?php
                                echo Form::label('remitente', 'Nombre del remitente: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mosca', array('class' => 'form'));
                                echo Form::input('remitente', $user->nombre, array('id' => 'remitente', 'size' => 32, 'class' => 'required'));
                                ?>            
                                <?php
                                //  echo Form::label('mosca','Mosca:');
                                echo Form::input('mosca', $user->mosca, array('id' => 'mosca', 'size' => 4));
                                ?>
                                <?php
                                echo Form::label('cargo', 'Cargo Remitente:', array('class' => 'form'));
                                echo Form::input('cargo_rem', $user->cargo, array('id' => 'cargo_rem', 'size' => 45, 'class' => 'required'));
                                ?>

                                <?php
                                echo Form::label('adjuntos', 'Adjunto:', array('class' => 'form'));
                                echo Form::input('adjuntos', $documento->adjuntos, array('id' => 'adjuntos', 'size' => 45, 'class' => 'required', 'title' => 'Ejemplo: Lo citado'));
                                ?>
                                <?php
                                echo Form::label('hojas', 'Nro hojas:', array('class' => 'form'));
                                echo Form::input('hojas', $documento->hojas, array('id' => 'hojas', 'size' => 45, 'class' => '', 'title' => 'Ejemplo: 50'));
                                ?>
                                <?php
                                echo Form::label('copias', 'Con copia a:', array('class' => 'form'));
                                echo Form::input('copias', $documento->copias, array('id' => 'adjuntos', 'size' => 45, 'class' => 'required'));
                                ?>
                            </p>
                        </td>
                        <td style="padding-left: 5px;" valign="top">
                            <input type="submit" name="documento" value="Modificar documento" class="button2" />   
                            <?php if ($documento->estado == 1): ?> 
                                <a href="/route/trace/?hr=<?php echo $documento->nur; ?>" class="button" title="Ver seguimiento" ><img src="/media/images/tick.png"/>Ver Seg</a>      
                            <?php else: ?>
                                <?php if ($documento->nur != ''): ?>
                                    <a href="/route/deriv/?hr=<?php echo $documento->nur; ?>" class="button" title="Derivar documento" ><img src="/media/images/deriva.png"/> Derivar</a>      
                                <?php else: ?>
                                    <a href="/document/asignar/<?php echo $documento->id; ?>" class="button">Asignar HR</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="/word/<?php echo $tipo->action; ?>/<?php echo $documento->id; ?>" target="_blank" title="Editar este documento en word" class="button" ><img src="/media/images/word07.gif"/> Editar en word</a>       
                            <!--<a href="" class="button" onclick="javascript:history.back(); return false;" >Tranferir</a>-->
                            <a href="/documento/transferir/<?php echo $documento->id; ?>" class="button" title="Transferir documento a otro usuario"  >Tranferir</a>
                            <?php
                            echo Form::label('referencia', 'Referencia:', array('class' => 'form'));
                            ?>
                            <textarea name="referencia" id="referencia" style="width: 425px;"><?php echo $documento->referencia ?></textarea>
                        </td>
                        <td style="padding-left: 5px;" valign="top">
                            <?php echo Form::input('addDest', '+Agregar Destinatario', array('class' => 'button2', 'type' => 'button', 'id' => 'addDest', 'rel' => $user->id)); ?>
                            <?php echo Form::label('dest', 'Mis destinatarios:'); ?>
                            <div id="vias">
                                <ul>
                                    <?php foreach ($vias as $v): ?>
                                        <li class="<?php echo $v['genero'] ?>"><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => $v['via'], 'cargo_via' => $v['cargo_via'])); ?></li>
                                    <?php endforeach; ?>
                                    <!-- Inmediato superior -->    
                                    <?php foreach ($superior as $v) { ?>
                                        <li class="<?php echo $v['genero'] ?>"><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => '', 'cargo_via' => '')); ?></li>
                                    <?php } ?>
                                    <!-- DEstinatarios-->    
                                    <?php foreach ($destinos as $d): ?>
                                        <li class="<?php echo $d->genero ?>"><?php echo HTML::anchor('#', $d->nombre, array('class' => 'destino1', 'nombre' => $d->nombre, 'title' => $d->cargo, 'cargo' => $d->cargo, 'institucion' => $d->entidad)); ?></li>
                                    <?php endforeach; ?>            
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" id="word" value="0" name="word"  />
                            <div style="width: 695px;">
                                <?php
                                echo Form::textarea('descripcion', $documento->contenido, array('id' => 'descripcion', 'cols' => 50, 'rows' => 20));
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>


                <div id="op">
                    <!-- <a href="#" class="link imagen">Insertar Imagen</a>
                     <a href="#" class="link imagen">Seleccionar todo</a>    -->
                </div>
                <div style="clear:both; display: block;"></div>
                <input type="hidden" id="con" value="<?php echo strlen($documento->contenido . $documento->referencia); ?> "/>
                <p>
                <hr/>

                </p>


            </form>
        </div>
    </div>
    <div id="adjuntos">
        <div class="formulario">        
            <form method="post" enctype="multipart/form-data" action="" >
                <label>Selecione un archivo para subir...</label>
                <input type="file" class="file" name="archivo"/>                 
                <input type="hidden" name="id_doc" value="<?php echo $documento->id; ?>"/>
                <input type="submit" name="adjuntar" value="Subir archivo" class="button2"/>
            </form>        
            <div style="clear: both;">

            </div>
            <h2 style="text-align:center;">Archivos Adjuntos </h2><hr/>
            <table id="theTable">
                <thead>
                    <tr>
                        <th>NOMBRE ARCHIVO</th>
                        <th>TAMA&Ntilde;O</th>
                        <th>FECHA DE SUBIDA</th>
                        <th>ACCION</th>
                    </tr>
                </thead>
                <tbody>                
                    <?php foreach ($archivos as $a): ?>
                        <tr>
                            <td><a href="/download/?file=<?php echo $a->id; ?>"><?php echo substr($a->nombre_archivo, 13) ?></a></td>
                            <td align="center"><?php echo number_format(($a->tamanio / 1024) / 1024, 2) . ' MB'; ?></td>
                            <td align="center"><?php echo $a->fecha ?></td>
                            <td align="center"><a href="/archivo/eliminar/<?php echo $a->id; ?>" class="link delete">Eliminar</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>    
        </div>
    </div>
</div>