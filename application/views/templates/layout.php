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
<header id="header">
    <div class="headerbar">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="headerbar-left">
            <ul class="header-nav header-nav-options">
                <li class="header-nav-brand">
                    <div class="brand-holder">
                        <a href="/">
                            <span class="text-lg text-bold text-primary">CORRESPONDENCIA - CORREOS DE BOLIVIA</span>
                        </a>
                    </div>
                </li>
                <li>
                    <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="headerbar-right">
            <ul class="header-nav header-nav-options">
                <li>
                    <!-- Search form -->
                    <!-- <form class="navbar-search" role="search" action="/search" method="GET">
                    <form class="navbar-search" role="search" action="/search">

                        <div class="form-group">
                            <input type="text" class="form-control" name="q" placeholder="Hoja de ruta">
                        </div>
                        <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
                    </form>
                    -->

                    <form action="/search/advanced">
                        <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i>
                        </button>
                    </form>
                </li>
            </ul><!--end .header-nav-options -->
            <ul class="header-nav header-nav-profile">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
                        <?php if (file_exists(DOCROOT . 'static/fotos/' . $usuario->username . '.jpg')): ?>
                            <img src="/static/fotos/<?php echo $usuario->username ?>.jpg?<?php echo time() ?>" alt=""/>
                            <?php
                        else:
                            ?>
                            <img src="/static/fotos/<?php echo $usuario->genero . '.jpg' ?>" alt=""/>
                        <?php endif; ?>
                        <span class="profile-info">
                                <?php echo $usuario->nombre; ?>
                            <small><?php echo $usuario->email ?></small>
                            </span>
                    </a>
                    <ul class="dropdown-menu animation-dock">
                        <li class="dropdown-header">Opciones de usuario</li>
                        <li><a href="/user/profile"><i class="fa fa-fw fa-cube text-success"></i> Perfil</a></li>
                        <li><a href="/user/pass"><i class="fa fa-fw fa-unlock-alt text-primary"></i> Cambiar Contrase&ntilde;a</a>
                        </li>
                        <li><a href="/user/logout"><i class="fa fa-fw fa-power-off text-danger"></i> Salir</a></li>
                    </ul><!--end .dropdown-menu -->
                </li><!--end .dropdown -->
            </ul><!--end .header-nav-profile -->
        </div><!--end #header-navbar-collapse -->
    </div>
</header>
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
