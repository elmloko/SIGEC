<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="es" xml:lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<!--[if IE]> <script> (function() { var html5 = ("abbr,article,aside,audio,canvas,datalist,details," + "figure,footer,header,hgroup,mark,menu,meter,nav,output," + "progress,section,time,video").split(','); for (var i = 0; i < html5.length; i++) { document.createElement(html5[i]); } })(); </script> <![endif]-->    
    <meta http-equiv="Content-Language" content="es" />
    <link rel="shortcut icon" href="<?php echo url::base().'media/images/icon.png';?>" />
    <title><?php echo $title;?></title>
    <meta name="keywords" content="<?php echo $meta_keywords;?>" />
    <meta name="description" content="<?php echo $meta_description;?>" />
    <meta name="copyright" content="<?php echo $meta_copywrite;?>" />
    <?php foreach($styles as $file => $type) { echo HTML::style($file, array('media' => $type)), "\n"; }?>
    <?php foreach($scripts as $file) { echo HTML::script($file, NULL, TRUE), "\n"; }?>
    <style type="text/css"><?php echo $theme;?></style>
    <script type="text/javascript">
    </script>    
</head>
<body id="modx-body-tag">
    <div id="modx-browser"></div>
    <div id="top">
        <div id="modx-topbar">
            <div id="modx-logo"><div id="icon-logo"></div><a href="/user/info" class="nombre" ><?php echo $username;?></a> / <a href="/user/logout" title="Salir del Sistema">Salir</a></div>
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
                <?php echo $menutop;?>                
                </ul>            
        </div>    
        </div>
            <div id="titulo">                
                <h2 class="titulo"><?php echo $titulo;?><br/><span><?php echo $descripcion;?></span></h2>                
            </div>
    </div>
        <div id="lateral" style="width: 1px;">
        
        </div>        
        <div id="content" style="left: 0; margin-left: 0; ">                        
            <a id="toggler"></a>
            <div id="render">
                <?php echo $content;?>
                <div style="clear: both; display: block;margin: 5px 0; height: 10px;  ">                    
                </div>
            </div>
        </div>
</body>
</html>