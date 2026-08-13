<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="es" xml:lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!--[if IE]> <script> (function() { var html5 = ("abbr,article,aside,audio,canvas,datalist,details," + "figure,footer,header,hgroup,mark,menu,meter,nav,output," + "progress,section,time,video").split(','); for (var i = 0; i < html5.length; i++) { document.createElement(html5[i]); } })(); </script> <![endif]-->
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Language" content="es" />
        <link rel="shortcut icon" href="/media/images/icon.png" />
        <title><?php echo $title; ?></title>
        <meta name="keywords" content="<?php echo $meta_keywords; ?>" />
        <meta name="description" content="<?php echo $meta_description; ?>" />
        <meta name="copyright" content="<?php echo $meta_copywrite; ?>" />
        <?php
        foreach ($styles as $file => $type) {
            echo HTML::style($file, array('media' => $type)), "\n";
        }
        ?>
        <?php
        foreach ($scripts as $file) {
            echo HTML::script($file, NULL, TRUE), "\n";
        }
        ?>
        <style type="text/css"><?php echo $theme; ?></style>
        <script type="text/javascript">

            var events_array = new Array();
            $(function() {
                //  var fecha=new Date();        
                $.ajax({
                    type: "POST",
                    // data: { date : fecha },
                    url: "/ajax/eventos",
                    dataType: "json",
                    success: function(data)
                    {
                        if (data)
                        {
                            $.each(data, function(i, item) {
                                events_array.push(
                                        {
                                            //startDate: new Date(2012,05,30,11,20),
                                            startDate: new Date(item.anio, item.mes - 1, item.dia, item.hora, item.minuto),
                                            endDate: new Date(item.anio2, item.mes2, item.dia2),
                                            title: item.titulo,
                                            description: item.descripcion,
                                            priority: item.prioridad, // 1 = Low, 2 = Medium, 3 = Urgent
                                            frecuency: item.frecuencia // 1 = Daily, 2 = Weekly, 3 = Monthly, 4 = Yearly
                                        });
                                //events_array=events_array.concat(evento);
                            });
                            $("#calendar").dp_calendar({
                                events_array: events_array,
                                onChangeMonth: function() {

                                },
                                onChangeDay: function() {
                                    //alert("onChangeDay Event Triggered!");
                                },
                                onClickMonthName: function() {
                                    //alert("onClickMonthName Event Triggered!");
                                },
                                onClickEvents: function() {
                                    //alert("onClickEvents Event Triggered!");
                                }
                            });
                        }
                        else
                        {
                            $("#calendar").dp_calendar();
                        }
                    },
                    error: function() {
                    }
                });
            });
        </script>    
    </head>
    <body class="metro">
        <div id="top">
            <div id="modx-topbar">
                <div id="modx-logo"><div id="icon-logo"></div><a href="/user/info" class="nombre" ><?php echo $username; ?></a> / <a href="/user/logout" title="Salir del Sistema">Salir</a></div>
                <div id="modx-site-name">                        
                </div>                        
            </div>
            <div id="modx-navbar">
                <div id="rightlogin"><span>
                        <form action="/search/" method="GET">           
                            <input type="text" name="q" class="txt_buscar" style="line-height: 20px; height: 20px; background: #363636; border: none; color:#efefef;   font-size: 13px; width: 150px;" />
                            <input type="submit" name="s" value="Buscar" id="searchsubmit" style="line-height: 20px; height: 25px;" />
                        </form>       
                    </span>
                </div>
                <div id="modx-topnav-div">            
                    <ul id="modx-topnav" class="typeface-js">
                        <?php echo $menutop; ?>                
                    </ul>

                </div>    
            </div>
            <div id="titulo">                
                <h2 class="titulo"><?php echo $titulo; ?><br/><span><?php echo $descripcion; ?></span></h2>                
            </div>
        </div>

        <div id="panel"> <!--the hidden panel -->
            <div class="content_panel">
                <div id="themeModifyBox" class="themeModifyBoxOpen">
                    <div id="themeModifyContent">

                        <div id="themeName">Cambiar color</div>
                        <div id="colorpicker"><div class="farbtastic"><div class="color" style="background-color: rgb(255, 0, 0);"></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker" style="left: 97px; top: 13px;"></div><div class="sl-marker marker" style="left: 147px; top: 147px;"></div></div></div>
                        <textarea id="colorcodes" readonly="readonly" onclick="this.focus();
                                this.select()"></textarea>
                    </div>

                    <label for="themeModifyBoxControl" id="themeModifyBoxTrigger">
                        <div id="themeModifyBoxControl"></div>
                    </label>
                </div>

                <a  href="javascript:void(0)" class='button2' id="hhh" />Aceptar</a>
                <a href='javascript:void(0)' class="button" id="cerrar" />Cancelar</a>
            </div>	
        </div>
        <!--if javascript is disabled use this link-->
        <a href="#" onclick="return();">
            <div id="tab"> <!-- this will activate your panel. -->
            </div> 
        </a>
        <div id="render" style="background: #f4f4f5;">
            <div class="dp_calendar" id="calendar" style="margin-top:2px;"></div> 
            <?php echo $content; ?>
            <div style="clear: both; display: block;margin: 5px 0; height: 10px;  ">                    
            </div>
        </div>

    </body>
</html>