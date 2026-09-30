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
<body class="<?php echo $menubar; ?> header-fixed ">

<!-- BEGIN HEADER-->
<?php echo View::factory('templates/cabecera')->set('usuario', $usuario)->set('titulo', isset($titulo) ? $titulo : '')->set('admin', TRUE); ?>
<!-- END HEADER-->

<!-- BEGIN BASE-->
<div id="base">

    <!-- BEGIN CONTENT-->
    <div id="content">

        <section class=" bg1 ">
            <?php
            /* $auth = Auth::instance();
             echo $auth->get_user()->id;
             echo '<br/>';
             $session = Session::instance('database');
             echo session_id();
             echo '<br/>';
             //var_dump($auth->);
             echo $session->id();
             echo $session->name();
             //$sesion = $session->get('auth_user');
             //echo $sesion->;
            */ ?>
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
                <a href="../../html/dashboards/dashboard.html">
                    <span class="text-lg text-bold text-primary ">SIGEC&nbsp;</span>
                </a>
            </div>
        </div>
        <div class="menubar-scroll-panel">
            <?php $menu_html = (string) $menutop; ?>
            <a href="/" class="mn-logo" title="Inicio"><img src="/media/logo-transparente.png" alt="Correos de Bolivia"/></a>
            <?php if (strpos($menu_html, 'href="/document/"') !== FALSE): ?>
                <!-- accion principal -->
                <?php echo View::factory('templates/nuevo_documento'); ?>
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

        <!-- BEGIN OFFCANVAS SEARCH -->
        <div id="offcanvas-search" class="offcanvas-pane width-8">
            <div class="offcanvas-head">
                <header class="text-primary">Search</header>
                <div class="offcanvas-tools">
                    <a class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                        <i class="md md-close"></i>
                    </a>
                </div>
            </div>
            <div class="offcanvas-body no-padding">
                <ul class="list ">
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>A</strong></div>
                        </div>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar4.jpg?1404026791" alt=""/>
                            </div>
                            <div class="tile-text">
                                Alex Nelson
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar9.jpg?1404026744" alt=""/>
                            </div>
                            <div class="tile-text">
                                Ann Laurens
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>J</strong></div>
                        </div>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar2.jpg?1404026449" alt=""/>
                            </div>
                            <div class="tile-text">
                                Jessica Cruise
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar8.jpg?1404026729" alt=""/>
                            </div>
                            <div class="tile-text">
                                Jim Peters
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>M</strong></div>
                        </div>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar5.jpg?1404026513" alt=""/>
                            </div>
                            <div class="tile-text">
                                Mabel Logan
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar11.jpg?1404026774" alt=""/>
                            </div>
                            <div class="tile-text">
                                Mary Peterson
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar3.jpg?1404026799" alt=""/>
                            </div>
                            <div class="tile-text">
                                Mike Alba
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                    <li class="tile divider-full-bleed">
                        <div class="tile-content">
                            <div class="tile-text"><strong>N</strong></div>
                        </div>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar6.jpg?1404026572" alt=""/>
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
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar7.jpg?1404026721" alt=""/>
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
                        <a class="tile-content ink-reaction" href="#offcanvas-chat" data-toggle="offcanvas"
                           data-backdrop="false">
                            <div class="tile-icon">
                                <img src="../../assets/img/avatar10.jpg?1404026762" alt=""/>
                            </div>
                            <div class="tile-text">
                                Samuel Parsons
                                <small>123-123-3210</small>
                            </div>
                        </a>
                    </li>
                </ul>
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
                <ul class="list-chats">
                    <li>
                        <div class="chat">
                            <div class="chat-avatar"><img class="img-circle"
                                                          src="../../assets/img/avatar1.jpg?1403934956" alt=""/></div>
                            <div class="chat-body">
                                Yes, it is indeed very beautiful.
                                <small>10:03 pm</small>
                            </div>
                        </div><!--end .chat -->
                    </li>
                    <li class="chat-left">
                        <div class="chat">
                            <div class="chat-avatar"><img class="img-circle"
                                                          src="../../assets/img/avatar9.jpg?1404026744" alt=""/></div>
                            <div class="chat-body">
                                Did you see the changes?
                                <small>10:02 pm</small>
                            </div>
                        </div><!--end .chat -->
                    </li>
                    <li>
                        <div class="chat">
                            <div class="chat-avatar"><img class="img-circle"
                                                          src="../../assets/img/avatar1.jpg?1403934956" alt=""/></div>
                            <div class="chat-body">
                                I just arrived at work, it was quite busy.
                                <small>06:44pm</small>
                            </div>
                            <div class="chat-body">
                                I will take look in a minute.
                                <small>06:45pm</small>
                            </div>
                        </div><!--end .chat -->
                    </li>
                    <li class="chat-left">
                        <div class="chat">
                            <div class="chat-avatar"><img class="img-circle"
                                                          src="../../assets/img/avatar9.jpg?1404026744" alt=""/></div>
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
                    </li>
                    <li>
                        <div class="chat">
                            <div class="chat-avatar"><img class="img-circle"
                                                          src="../../assets/img/avatar1.jpg?1403934956" alt=""/></div>
                            <div class="chat-body">
                                Are the colors of the logo already adapted?
                                <small>Last week</small>
                            </div>
                        </div><!--end .chat -->
                    </li>
                </ul>
            </div><!--end .offcanvas-body -->
        </div><!--end .offcanvas-pane -->
        <!-- END OFFCANVAS CHAT -->

    </div><!--end .offcanvas-->
    <!-- END OFFCANVAS RIGHT -->

</div><!--end #base-->
<!-- END BASE -->


</body>
</html>
