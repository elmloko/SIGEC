<script type="text/javascript">
    var stat = 0;
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
     /*   $("#theTable").tablesorter({sortList: [[1, 0]],
            widgets: ['zebra'],
            headers: {
                0: {sorter: false}
            }
        }); */
    });
    $(function() {
        var buttons = ['formatting', '|', 'bold', 'italic', '|','unorderedlist', 'orderedlist', 'outdent', 'indent', '|','table', 'link', 'alignment', '|', 'horizontalrule','underline', 'alignleft', 'aligncenter', 'alignright', 'justify', '|','fontcolor','backcolor'];
$('#descripcion').redactor({lang : 'es',css: 'docstyle.css',focus: true,
			buttons: buttons});
//incluir destinatario

    });
    function isValidForm() {
        if (stat)
        {
            stat = 0;
            if (confirm("Esta seguro que desea generar un numero cite de su area superior."))
            {
                return true;
            } else {
                return false;
            }
        } else
        {
            return true;
        }
    }
    function activar(c)
    {
        if (c.checked)
        {
            document.getElementById('cite_sup').style.display = "block";
        } else
        {
            document.getElementById('cite_sup').style.display = "none";
        }
    }
</script>
<div class="formulario">
    <form onsubmit="return isValidForm();" action="/documento/generar/<?php echo $documento->action; ?>" method="post" id="frmCreate">
        <table width="100%">        
            <tr>
                <td  valign="top" style=" padding-left: 1px;" rowspan="2">
                    <?php if ($user->cite_despacho): ?>
                        <br/>
                        <input type="checkbox" onclick="activar(this);">Activar Generaci&oacute;n con Cite de Despacho<br>
                        <input style="display:none;" type="submit" class="button" name="submit" id="cite_sup" value="GENERAR CON CITE DIR/DESPACHO"/>
                        <br/>
                    <?php endif; ?>
                    <label>Proceso: </label><?php echo Form::select('proceso', $options, NULL); ?><br/>    
                    <?php if ($documento->tipo == 'Carta'): ?>
                        <p>
                            <label>Titulo:</label>
                            <select name="titulo">
                                <option></option>
                                <option>Señor</option>
                                <option>Señora</option>
                                <option>Señores</option>    
                            </select>
                        </p>
                    <?php else: ?>
                        <input type="hidden" name="titulo" />   
                    <?php endif; ?>
                    <p>
                        <?php
                        echo Form::label('destinatario', 'Nombre del destinatario:', array('class' => 'form'));
                        echo Form::input('destinatario', '', array('id' => 'destinatario', 'size' => 48, 'class' => 'required'));
                        ?>
                    </p>
                    <p>
                        <?php
                        echo Form::label('destinatario', 'Cargo Destinatario:', array('class' => 'form'));
                        echo Form::input('cargo_des', '', array('id' => 'cargo_des', 'size' => 48, 'class' => 'required'));
                        ?>
                    </p>   
                    <?php if ($tipo->via == 0): ?>
                        <p>
                            <label>Institución Destinatario</label>
                            <input type="text" size="40" name="institucion_des" />    
                            <input type="hidden" name="via" />   
                            <input type="hidden" name="cargovia" />   
                        </p>
                        <p>
                        <?php else: ?>
                            <input type="hidden" size="40" name="institucion_des" />   
                            <?php
                            echo Form::label('via', 'Via:', array('class' => 'form'));
                            echo Form::input('via', '', array('id' => 'via', 'size' => 48, 'class' => 'required'));
                            ?>
                            <?php
                            echo Form::label('cargovia', 'Cargo Via:', array('class' => 'form'));
                            echo Form::input('cargovia', '', array('id' => 'cargovia', 'size' => 48, 'class' => 'required'));
                            ?>
                        <?php endif; ?>
                    </p>

                    <p>
                        <?php
                        echo Form::label('remitente', 'Remitente:', array('class' => 'form'));
                        echo Form::input('remitente', $user->nombre, array('id' => 'remitente', 'size' => 35, 'class' => 'required'));
                        ?>            
                        <?php
                        //echo Form::label('mosca','Mosca:');
                        echo Form::input('mosca', $user->mosca, array('id' => 'mosca', 'size' => 5));
                        ?>
                        <?php
                        echo Form::label('cargo', 'Cargo Remitente:', array('class' => 'form'));
                        echo Form::input('cargo_rem', $user->cargo, array('id' => 'cargo_rem', 'size' => 48, 'class' => 'required'));
                        ?>
                        <?php
                        echo Form::label('adjuntos', 'Adjunto:', array('class' => 'form'));
                        echo Form::input('adjuntos', '', array('id' => 'adjuntos', 'size' => 48, 'class' => 'required', 'title' => 'Ejemplo: Lo citado'));
                        ?>
                        <?php
                        echo Form::label('hojas', 'Nro hojas:', array('class' => 'form'));
                        echo Form::input('hojas', '0', array('id' => 'hojas', 'size' => 45, 'class' => '', 'title' => 'Ejemplo: 50'));
                        ?>
                        <?php
                        echo Form::label('copias', 'Con copia a:', array('class' => 'form'));
                        echo Form::input('copias', '', array('id' => 'adjuntos', 'size' => 48, 'class' => 'required'));
                        ?>
                    </p>
                </td>
                <td valign="top">
                    <br/>
                    <input type="submit"  class="button2" name="submit" value="Generar Documento con Hoja de Ruta"/>
                    <input type="submit"  id="noHojaRuta" title="Genera el documento sin hora de ruta para su posterior asignaci[on" class="button" name="submit" value="Generar Documento sin Hoja de Ruta"/>
                    <!--<a href="" class="button" onclick="javascript:history.back(); return false;" >Cancelar</a>-->
                    <hr/>        
                    <label>Referencia</label>
                    <textarea name="referencia" id="referencia" style="width: 400px; " ></textarea>    
                </td>
                <td valign="top">
                    <?php echo Form::input('addDest', '+Agregar Destinatario', array('class' => 'button2', 'type' => 'button', 'id' => 'addDest', 'rel' => $user->id)); ?>
                    <?php if ($documento->via > -10) { ?>   
                        <div id="vias">
                            <ul>

                                <?php foreach ($vias as $v): ?>
                                    <li class="<?php echo $v['genero'] ?>"><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => $v['via'], 'cargo_via' => $v['cargo_via'])); ?></li>
                                <?php endforeach; ?>
                                <!-- Inmediato superior -->    
                                <?php foreach ($superior as $v) { ?>
                                    <li class="<?php echo $v['genero'] ?>"><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => '', 'cargo_via' => '')); ?></li>
                                <?php } ?>
                                <!-- destinatarios propios -->
                                <?php foreach ($destinos as $d): ?>
                                    <li class="<?php echo $d->genero ?>"><?php echo HTML::anchor('#', $d->nombre, array('class' => 'destino1', 'nombre' => $d->nombre, 'title' => $d->cargo, 'cargo' => $d->cargo, 'institucion' => $d->entidad)); ?></li>
                                <?php endforeach; ?>            
                                <!-- dependientes -->    
                                <?php foreach ($dependientes as $v) { ?>
                                    <li class="<?php echo $v['genero'] ?>"><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => '', 'cargo_via' => '')); ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">

                    <input type="hidden" id="hojaruta" value="1" name="hojaruta"  />
                    <input type="hidden" id="cite_superior" value="0" name="cite_superior"  />
                    <div class="descripcion" style="width: 680px; float: left; ">
                        <?php
                        echo Form::textarea('descripcion', '', array('id' => 'descripcion', 'cols' => 60, 'rows' => 26));
                        ?>
                    </div>
                </td>

            </tr>

            <tr>
                <td colspan="2" style="padding-left: 5px;">
                    <div style="clear:both; display: block;"></div>
                </td>
                <td></td>
            </tr>
        </table>

    </form>
</div>
