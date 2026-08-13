<script type="text/javascript">
    $(function () {
        $("div#entrada .bandeja").each(function () {
            var t = $(this).text().toLowerCase(); //all row text
            $("<table class='indexColumn'></table>")
                    .hide().text(t).appendTo(this);
        });//each tr
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            //show all rows.
            $("div#entrada .bandeja:hidden").show();
            $.each(s, function () {
                $("div#entrada .bandeja .indexColumn:not(:contains('"
                        + this + "'))").parent().hide();
            });//each
        });//key up.
//modal
        /*     $("#dialog-confirm").dialog({
         resizable: false,
         height: 140,
         modal: true,
         autoOpen: false,
         buttons: {
         "Recibir": function () {
         
         $(this).dialog("close");
         },
         "Cancelar": function () {
         $(this).dialog("close");
         }
         }
         }); */
//recepcionar correspondencia
        /*
         $('a.recepcion').click(function(){
         var hr=$(this).attr('hs');
         $('p#mensaje').html("Recepcionar la correspondencia con hoja de ruta : "+hr+" ?");
         $('#dialog-confirm').dialog("open");
         });
         */

        /*  $('a.poplight[href^=#]').click(function () {
         var nur = $(this).attr('nur');
         var hs = $(this).attr('hs');
         $('h3.mensaje').text('Recibir la hoja de ruta : ' + hs);
         $('a#aceptar').attr('href', '/bandeja/receive/' + nur);
         var popID = $(this).attr('rel'); //Get Popup Name
         var popURL = $(this).attr('href'); //Get Popup href to define size
         
         //Pull Query & Variables from href URL
         var query = popURL.split('?');
         var dim = query[1].split('&');
         var popWidth = dim[0].split('=')[1]; //Gets the first query string value
         
         //Fade in the Popup and add close button
         $('#' + popID).fadeIn().css({'width': Number(popWidth)});//.prepend('<a href="#" class="close"><img src="/media/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');
         
         //Define margin for center alignment (vertical   horizontal) - we add 80px to the height/width to accomodate for the padding  and border width defined in the css
         var popMargTop = ($('#' + popID).height() + 80) / 2;
         var popMargLeft = ($('#' + popID).width() + 80) / 2;
         
         //Apply Margin to Popup
         $('#' + popID).css({
         'margin-top': -popMargTop,
         'margin-left': -popMargLeft
         });
         
         //Fade in Bacnorecibidoskground
         $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
         $('#fade').css({'filter': 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
         
         return false;
         });
         */

//Close Popups and Fade Layer
        /*      $('a.close, #fade, #cancelar').live('click', function () { //When clicking on the close or fade layer...
         $('#fade , .popup_block').fadeOut(function () {
         $('#fade, a.close').remove();  //fade them both out
         });
         return false;
         });
         */
        $('a.link2').click(function () {
            $this = $(this);
            var criterio = $this.attr('id');
            if ($this.is('.asc'))
            {
                $this.removeClass('asc');
                $this.addClass('desc');
                var sortdir = -1;
            }
            else
            {

                $this.addClass('asc');
                $this.removeClass('desc');
                var sortdir = 1;
            }
            $(this).siblings().removeClass('asc');
            $(this).siblings().removeClass('desc');
//ordenar      
            var nurs = $('div.bandeja').get();
            nurs.sort(function (a, b) {
                var val1 = $(a).attr('' + criterio).toUpperCase();
                var val2 = $(b).attr('' + criterio).toUpperCase();
                return (val1 < val2) ? -sortdir : (val1 > val2) ? sortdir : 0;
            });
            $.each(nurs, function (index, row) {
                $('div#entrada').append(row);
            });
            return false;
        });
        $("#FilterTextBox").focus();

        $('a.recibir').click(function () {
            var $this = $(this);
            var nur = $this.attr('nur');
            var hr = $this.attr('hr');
            var entrada = $this.attr('entrada');
            $('#input-hojaruta').val(nur);
            $('#input-entrada').val(entrada);
            $('span#hojaruta').text(hr);

        });
        $('#btn-recibir').click(function () {
            var id_seg = $('#input-hojaruta').val();
            var entrada = $('#input-entrada').val();
		location.href="/bandeja/cancel/?id="+id_seg;

        /*    $.ajax({
                type: "POST",
                data: {id: id_seg},
                url: "/bandeja/cancel/",
                success: function (jsondata)
                {
                    var options = {
                        show: false,
                    };
                    $('#simpleModal').modal('hide');
                    var obj = jQuery.parseJSON(jsondata);
                    var estado = obj.estado;
                    if (estado == 'ok') {
                        //location.reload(true);                        
                        $('#e' + entrada).addClass('animated zoomOut').remove(3);
                        //$('#e' + entrada);
                    }
                    else
                    {
                        alert(obj.error);
                    }
                }
            });
*/
        });

    });
</script>

<?php if (sizeof($entrada) > 0) { ?>
    <div style=";position: fixed; z-index: 1; margin-top: -24px; padding: 2px; background: #fff;" class="col-lg-12 col-md-11">
        <div class="col-md-5">
            <h3><i class="md md-send"></i> Correspondencia Enviada</h3>
        </div>
        <div class="col-md-3"><i class="fa fa-filter"></i> Filtrar:          
            <input type="text" id="FilterTextBox" name="FilterTextBox" class="form-control" size="15" />
        </div>
        <div class="col-md-4">
            <div class="btn-group">
                <button class="btn ink-reaction btn-sm btn-default" type="button"><i class=" fa fa-sort-alpha-asc"></i> Ordenar por</button>
                <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-default dropdown-toggle" type="button" aria-expanded="false"><i class="fa fa-caret-down"></i></button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" class="link2" id="hojaruta">Hoja Ruta</a></li>
                    <li><a href="#" class="link2" id="fecha">Fecha</a></li>
                    <li><a href="#" class="link2" id="oficina">Oficina</a></li>
                    <li><a href="#" class="link2" id="proceso">Proceso</a></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 ">
            <div id="lateralddf">
                <div id="seleciones" ></div>
                <div id="opciones" class="oculto archive">
                </div>
                <div id="print_enviados" class="oculto archive">
                    <ul>
                        <li>
                            <a href="#" id="print_hr" ><img src="/media/images/excel.png" align="absmiddle"  /><b> Generar Excel</b></a>         
                        </li>                
                    </ul>
                    <div id="selecciones2" ></div>
                </div> 
            </div>
        </div>
    </div>
    <div id="entrada" class="card" style="margin-top: 42px; position: relative;">    
        <form action="/correspondence/doa" method="post" id="doa" >
            <?php
            $i = 1;
            foreach ($entrada as $s):
                ?>
                <div id="e<?php echo $i; ?>" class="bandeja tipo<?php echo $s->oficial; ?>" style="display:inline-block; " oficina="<?php echo $s->de_oficina ?>" proceso="<?php echo $s->referencia ?>"  fecha="<?php echo $s->fecha; ?>" hojaruta="<?php echo $s->nur; ?>">
                    <table class="oficial<?php echo $s->oficial; ?> ">
                        <tr>
                            <td width="118" rowspan="2"  align="center" valign="top" class="nur<?php echo $s->oficial; ?><?php echo $s->prioridad; ?>">                               				
                            </td>
                            <td valign="top" colspan="3" >
                                <h4 class="text-primary-dark"><a href="/document/detalle/<?php echo $s->id_doc ?>" ><?php echo $s->referencia; ?></a></h4>                                			 				
                            </td>

                        </tr>
                        <tr>
                            <td width="50%" colspan="2" valign="top">
                                <div>
                                    <span class=" text-light"><b><?php echo $s->nombre_receptor; ?> </b> - 
                                        <?php echo $s->cargo_receptor; ?></span><br/>
                                    <span class="oficina opacity-75"><?php echo $s->a_oficina; ?></span>	    
                                </div>
                            </td>            
                            <td class="derecha" valign="top">     
                                <span class="proveido text-accent-light"><i class=" fa fa-comments-o"></i> <?php echo $s->proveido; ?></span>
                                <br/><span class=" text-accent-dark"><?php echo $s->accion; ?></span><br/>                                                                    
                                <?php if ($s->hijo == 1): ?> <a href="/correspondence/agrupado/?hr=<?php echo $s->nur; ?>" class="link agrupado">Agrupado</a><?php endif; ?>
                            </td>            
                        </tr>
                        <tr>
                            <td width="88">
                                <a href="/route/trace/?hr=<?php echo $s->nur; ?>" class="nur<?php echo $s->oficial; ?>"><?php echo $s->nur ?></a>
                            </td>
                            <td colspan="2">
                                <span class=" opacity-75"><?php echo Date::fecha($s->fecha); ?></span>                
                            </td>
                            <td>                
                                <span class="opciones"> 


                                    <a href="javascript:;" entrada="<?php echo $i; ?>" class="recibir btn btn-primary-dark btn-sm" data-toggle="modal" data-target="#simpleModal" nur="<?php echo $s->id; ?>" hr="<?php echo $s->nur; ?>"><i class="md md-cancel"></i> Cancelar</a>                                   

                                </span>
                            </td>
                        </tr>
                        <?php
                        //$dias = floor((($segundos / 3600) / 24));
                        switch ($s->dias) {
                            case 0:
                                $color = "style-success";
                                break;
                            case 1:
                                $color = "style-success";
                                break;
                            case 2:
                                $color = "style-warning";
                                break;
                            default:
                                $color = "style-danger";
                                break;
                        }
                        ?>
                        <sup class="badge pull-2 <?php echo $color; ?> pull-right"><?php echo $s->dias; ?> dias</sup>
                    </table>    

                    <?php // $segundos = (time() - strtotime($s->fecha2));    ?>
                </div> 
                <?php
                $i++;
            endforeach;
            ?>
            <?php echo Form::hidden('accion', '', array('id' => 'accion')); ?>          
        </form>        
    </div>

<?php } else { ?>
<div class="alert alert-info"> 
        <p>
            <i class="fa fa-info-circle"></i> Lista Vacia,  Usted no tiene correspondencia entrante.</p>
    </div>
   
<?php } ?>

<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel">Cancelar correspondencia</h4>
            </div>
            <div class="modal-body">
                <p>Esta usted seguro de cancelar la derivación la hoja de ruta: <span class="text-primary" id="hojaruta"></span> ?</p>
                <input type="hidden" id="input-hojaruta" name="" value="0"/>
                <input type="hidden" id="input-entrada" name="" value="0"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>              
                <button type="button" id="btn-recibir" class=" btn btn-primary-dark btn-sm">Cancelar derivación</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

















<script type="text/javascript">

    $(function () {
        $("div#entrada .bandeja").each(function () {
            var t = $(this).text().toLowerCase(); //all row text
            $("<table class='indexColumn'></table>")
                    .hide().text(t).appendTo(this);
        });//each tr
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            //show all rows.
            $("div#entrada .bandeja:hidden").show();
            $.each(s, function () {
                $("div#entrada .bandeja .indexColumn:not(:contains('"
                        + this + "'))").parent().hide();
            });//each--
        });//key up.      
//modal
        $('a.poplight[href^=#]').click(function () {
            var id_nur = $(this).attr('id_nur');
            var id_seg = $(this).attr('id_seg');
            var nur = $(this).attr('nuri');
            $('#id_nur').val(id_nur);
            $('#id_seg').val(id_seg);
            $('#nur').val(nur);
            $('#aceptar').attr('href', '/bandeja/cancel/' + id_seg);
            $('h3.mensaje').text('Cancelar derivación, HR : ' + nur);
            //$('a#aceptar').attr('href','/bandeja/responder/'+id_nur);
            var popID = $(this).attr('rel'); //Get Popup Name
            var popURL = $(this).attr('href'); //Get Popup href to define size

            //Pull Query & Variables from href URL
            var query = popURL.split('?');
            var dim = query[1].split('&');
            var popWidth = dim[0].split('=')[1]; //Gets the first query string value

            //Fade in the Popup and add close button
            $('#' + popID).fadeIn().css({'width': Number(popWidth)});//.prepend('<a href="#" class="close"><img src="/media/images/close.gif" class="btn_close" title="Close Window" alt="Close" /></a>');

            //Define margin for center alignment (vertical   horizontal) - we add 80px to the height/width to accomodate for the padding  and border width defined in the css
            var popMargTop = ($('#' + popID).height() + 80) / 2;
            var popMargLeft = ($('#' + popID).width() + 80) / 2;

            //Apply Margin to Popup
            $('#' + popID).css({
                'margin-top': -popMargTop,
                'margin-left': -popMargLeft
            });

            //Fade in Background
            $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
            $('#fade').css({'filter': 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 

            return false;
        });

//Close Popups and Fade Layer
        $('a.close, #fade, #cancelar').live('click', function () { //When clicking on the close or fade layer...
            $('#fade , .popup_block').fadeOut(function () {
                $('#fade, a.close').remove();  //fade them both out
            });
            return false;
        });
//imprimir
//archivo y pendientes
        $('.sel').bind('click', function () {
            var count = $('input:checked').length;
            if (count == 0) {
                $('#print_enviados').addClass('oculto');
            }
            else
            {
                var nurs = '';
                $('input:checked').each(function () {
                    if ($(this).is(':checked'))
                    {
                        nurs = nurs + "<br/>- " + $(this).attr('rel') + '<hr />';
                    }
                });
                $('#selecciones2').html(nurs);
                $('#print_enviados').removeClass('oculto');
            }
        });

        $('#print_hr').click(function () {
            $('#frmprint').submit();
            return false;
        });

    });
</script>
