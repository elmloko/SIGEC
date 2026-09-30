<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!--[if IE]>
    <script> (function () {
        var html5 = ("abbr,article,aside,audio,canvas,datalist,details," + "figure,footer,header,hgroup,mark,menu,meter,nav,output," + "progress,section,time,video").split(',');
        for (var i = 0; i < html5.length; i++) {
            document.createElement(html5[i]);
        }
    })(); </script> <![endif]-->
    <meta http-equiv="Content-Language" content="es"/>
    <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->
    <link rel="shortcut icon" href="<?php echo url::base() . 'media/images/icon.png?v=correos'; ?>"/>
    <title><?php echo $title; ?></title>
    <meta name="keywords" content="<?php echo $meta_keywords; ?>"/>
    <meta name="description" content="<?php echo $meta_description; ?>"/>
    <meta name="copyright" content="<?php echo $meta_copywrite; ?>"/>
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
    <link rel="stylesheet" href="<?php echo URL::base(); ?>static/css/tema-correos.css?v=<?php echo @filemtime(DOCROOT . 'static/css/tema-correos.css'); ?>" media="all"/>
    <style type="text/css"><?php echo $theme; ?></style>
</head>

<!--
<script type = "text/javascript" >
    window.history.forward();
    function preventBack() {
            window.history.forward();
    }

    //setTimeout("disableBackButton()", 1);
    //setTimeout("preventBack()", 0);
    //window.onunload = function () { null };
</script>
-->

<!-- <body onload="preventBack();" onpageshow="if (event.persisted) preventBack();" onunload="" class="<?php echo $menubar; ?> header-fixed " >-->
<body class="<?php echo $menubar; ?> header-fixed ">

<!-- BEGIN HEADER-->
<?php echo View::factory('templates/cabecera')->set('usuario', $usuario)->set('titulo', isset($titulo) ? $titulo : ''); ?>
<!-- END HEADER-->

<!-- BEGIN BASE-->
<div id="base">

    <!-- BEGIN OFFCANVAS LEFT -->
    <div class="offcanvas">
    </div><!--end .offcanvas-->
    <!-- END OFFCANVAS LEFT -->

    <!-- BEGIN CONTENT-->
    <div id="content">

        <section class="bg1">

            <div class="section-body">
                <div class="row">
                    <?php echo $content; ?>
                </div><!--end .row -->
            </div><!--end .section-body -->
        </section>
    </div><!--end #content-->
    <!-- END CONTENT -->

    <!-- BEGIN MENUBAR-->
    <div id="menubar" class=" menubar-inverse">
        <div class="menubar-fixed-panel">
            <div>
                <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar"
                   href="javascript:void(0);">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
            <div class="expanded">
                <a href="/">
                    <span class="text-lg text-bold text-primary ">CORRESPONDENCIA&nbsp;</span>
                </a>
            </div>
        </div>
        <div class="menubar-scroll-panel">
            <?php $menu_html = (string) $menutop; ?>
            <a href="/" class="mn-logo" title="Inicio"><img src="/media/logo-transparente.png" alt="Correos de Bolivia"/></a>
            <?php if (strpos($menu_html, 'href="/document/"') !== FALSE): ?>
                <!-- accion principal -->
                <a href="/document" class="mn-cta"><i class="fa fa-plus"></i> Nuevo documento</a>
            <?php endif; ?>
            <div class="mn-seccion">Menú</div>
            <!-- BEGIN MAIN MENU -->
            <ul id="main-menu" class="gui-controls">
                <?php echo $menu_html; ?>
            </ul><!--end .main-menu -->
            <!-- END MAIN MENU -->

            <div class="menubar-foot-panel">
                <small class="mn-pie hidden-folded">
                    <b>SIGEC · Correos de Bolivia</b>
                    &copy; <?php echo date('Y'); ?> Área de Sistemas
                </small>
            </div>
        </div><!--end .menubar-scroll-panel-->
    </div><!--end #menubar-->
    <!-- END MENUBAR -->

    <!-- BEGIN OFFCANVAS RIGHT -->
    <div class="offcanvas">
    </div><!--end .offcanvas-->
    <!-- END OFFCANVAS RIGHT -->
</div><!--end #base-->
<!-- END BASE -->


</body>
</html>
