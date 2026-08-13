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
    <link rel="shortcut icon" href="<?php echo url::base() . 'media/images/icon.png'; ?>"/>
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
                            <span class="text-lg text-bold text-primary">CORRESPONDENCIA - MOPSV</span>
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
                <!--
                <li class="dropdown hidden-xs">
                    <ul class="dropdown-menu animation-expand">
                        <li class="dropdown-header">Alertas</li>
                        <?php foreach ($alertas as $a): ?>
                            <li>
                                <a class="alert alert-callout alert-danger"
                                   href="/route/trace/?hr=<?php echo $a['nur'] ?>">
                                    <strong><?php echo $a['nur'] ?></strong><br/>
                                    <small><?php echo $a['proveido'] ?><br/></small>
                                    <small>Fecha max resp: <b><?php echo $a['fecha'] ?></b></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                -->
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
            <ul class="header-nav header-nav-toggle">
                <li>
                    <a class="btn btn-icon-toggle btn-default" href="#offcanvas-search" data-toggle="offcanvas"
                       data-backdrop="false">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                </li>
            </ul><!--end .header-nav-toggle -->
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
            <img src="/media/logo-transparente.png" width="235"/>
            <hr/>
            <!-- BEGIN MAIN MENU -->
            <ul id="main-menu" class="gui-controls">

                <?php echo $menutop; ?>
            </ul><!--end .main-menu -->
            <!-- END MAIN MENU -->

            <div class="menubar-foot-panel">
                <small class="no-linebreak hidden-folded">
                    <span class="opacity-75">Copyright &copy; 2016</span> <strong>SISTEMAS</strong>
                </small>
            </div>
        </div><!--end .menubar-scroll-panel-->
    </div><!--end #menubar-->
    <!-- END MENUBAR -->

    <!-- BEGIN OFFCANVAS RIGHT -->
    <div class="offcanvas">

        <!-- BEGIN OFFCANVAS SEARCH -->
        <div id="offcanvas-search" class="offcanvas-pane width-8">
            <div class="offcanvas-head">
                <header class="text-primary">A</header>
                <div class="offcanvas-tools">
                    <a class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                        <i class="md md-close"></i>
                    </a>
                </div>
            </div>
            <div class="offcanvas-body no-padding">
                <ul class="list ">
                    <!--
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>A</strong></div>
                        </div>
                    </li>

                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas" data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar6.jpg?1404026572" alt="" />
                            </div>
                            <div class="tile-text">
                                Nathan Peterson
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>P</strong></div>
                        </div>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas" data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar7.jpg?1404026721" alt="" />
                            </div>
                            <div class="tile-text">
                                Philip Ericsson
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>S</strong></div>
                        </div>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas" data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar10.jpg?1404026762" alt="" />
                            </div>
                            <div class="tile-text">
                                Samuel Parsons
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li> -->
                </ul>
                </form>
            </div><!--end .offcanvas-body -->
        </div><!--end .offcanvas-pane -->
        <!-- END OFFCANVAS SEARCH -->

        <!-- BEGIN OFFCANVAS CHAT -->
        <div id="offcanvas-chat" class="offcanvas-pane style-default-light width-12">
            <div class="offcanvas-head style-default-bright">
                <header class="text-primary">Chat with Ann Laurens</header>
                <div class="offcanvas-tools">
                    <a class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                        <i class="md md-close"></i>
                    </a>
                    <a class="btn btn-icon-toggle btn-default-light pull-right" href="#offcanvas-search"
                       data-toggle="offcanvas" data-backdrop="false">
                        <i class="md md-arrow-back"></i>
                    </a>
                </div>
                <form class="form">
                    <div class="form-group floating-label">
                        <textarea name="sidebarChatMessage" id="sidebarChatMessage" class="form-control autosize"
                                  rows="1"></textarea>
                        <label for="sidebarChatMessage">Leave a message</label>
                    </div>
                </form>
            </div>
            <div class="offcanvas-body">
                <!--
                <ul class="list-chats">
                    <li>
                        <div class="chat">
                            <div class="chat-avatar"><img class="img-circle" src="../../assets/img/avatar1.jpg?1403934956" alt="" /></div>
                            <div class="chat-body">
                                Yes, it is indeed very beautiful.
                                <small>10:03 pm</small>
                            </div>
                        </div><!--end .chat -->
                <!--  </li>
                  <li class="chat-left">
                      <div class="chat">
                          <div class="chat-avatar"><img class="img-circle" src="../../assets/img/avatar9.jpg?1404026744" alt="" /></div>
                          <div class="chat-body">
                              Did you see the changes?
                              <small>10:02 pm</small>
                          </div>
                      </div><!--end .chat -->
                <!--</li>
                <li>
                    <div class="chat">
                        <div class="chat-avatar"><img class="img-circle" src="../../assets/img/avatar1.jpg?1403934956" alt="" /></div>
                        <div class="chat-body">
                            I just arrived at work, it was quite busy.
                            <small>06:44pm</small>
                        </div>
                        <div class="chat-body">
                            I will take look in a minute.
                            <small>06:45pm</small>
                        </div>
                    </div><!--end .chat -->
                <!--</li>
                <li class="chat-left">
                    <div class="chat">
                        <div class="chat-avatar"><img class="img-circle" src="../../assets/img/avatar9.jpg?1404026744" alt="" /></div>
                        <div class="chat-body">
                            The colors are much better now.
                        </div>
                        <div class="chat-body">
                            The colors are brighter than before.
                            I have already sent an example.
                            This will make it look sharper.
                            <small>Mon</small>
                        </div>
                    </div><!--end .chat -->
                <!--</li>
                <li>
                    <div class="chat">
                        <div class="chat-avatar"><img class="img-circle" src="../../assets/img/avatar1.jpg?1403934956" alt="" /></div>
                        <div class="chat-body">
                            Are the colors of the logo already adapted?
                            <small>Last week</small>
                        </div>
                    </div><!--end .chat -->
                <!-- </li>
             </ul>
             -->
            </div><!--end .offcanvas-body -->
        </div><!--end .offcanvas-pane -->
        <!-- END OFFCANVAS CHAT -->

    </div><!--end .offcanvas-->
    <!-- END OFFCANVAS RIGHT -->
</div><!--end #base-->
<!-- END BASE -->


</body>
</html>
