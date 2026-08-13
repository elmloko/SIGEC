<script>
    $(function () {
        $('#addDestinatario_old').click(function () {
            var id_user = $(this).attr('rel');


            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 900) / 2;
            top = (top - 500) / 2;
            var res = window.showModalDialog("/content/destinos/" + id_user, "", "center:0;dialogWidth:860px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            if (res != null)
            {
                $("#destinatarios").addClass('loading');
                $.ajax({
                    type: "POST",
                    data: {destinos: res, id: id_user},
                    url: "/ajax/addUser",
                    // dataType: "html",
                    success: function (data)
                    {
                        location.reload(true);
                    }
                });
            }
        });
        $('#addDestinatario').click(function () {
            var id_user = $(this).attr('rel');

            eModal.iframe('/content/destinos/' + id_user, 'Agregar Destinatario')

            //location.reload(true);

        });
        $('a.delDestinatario').click(function ()
        {
            var nombre = $(this).attr('rel');
              if (confirm("Esta usted seguro de quitar de la lista a: \n" + nombre)) {
             return true;
             } else {
             return false;
             }
           /* eModal.confirm("Esta usted seguro de quitar de la lista a: \n" + nombre, 'Quitar usuario')
                    .then(
                            function () {
                                return true;
                            }
                    , function () {
                        return false;
                    });*/
        });



    });
</script>
<!-- BEGIN PROFILE HEADER -->
<section class="full-bleed">
    <div class="section-body style-default-dark force-padding text-shadow ">
        <div class="img-backdrop" style="background-image: url('/static/fondo.jpg')"></div>
        <div class="overlay overlay-shade-top stick-top-left height-3"></div>
        <div class="row">
            <div class="col-md-3 col-xs-5">
                <?php if (file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg')): ?>
                    <img class="img-circle border-white border-xl img-responsive " width="110" src="/static/fotos/<?php echo $user->username ?>.jpg?t=<?php echo time(); ?>" alt="" />
                    <?php
                else:
                    ?>
                    <img class="img-circle border-white border-xl img-responsive " width="110" src="/static/fotos/<?php echo $user->genero . '.jpg' ?>?t=<?php echo time(); ?>" alt="" />
                <?php endif; ?>

                <h3><?php echo $user->nombre; ?><br/><small><?php echo $user->cargo ?></small></h3>
            </div><!--end .col -->
            <div class="col-md-9 col-xs-7">
                <div class="width-3 text-center pull-right">
                    <strong class="text-xl"><?php echo $user->logins ?></strong><br/>
                    <span class="text-light opacity-75">Veces ingresadas</span>
                </div>

            </div><!--end .col -->
        </div><!--end .row -->
        <div class="overlay overlay-shade-bottom stick-bottom-left force-padding text-right">
            <div class="pull-right">
                <strong class="text-xl"><?php echo $user->email ?></strong><br/>
                <span class="text-light opacity-75">Ultimo Ingreso: <?php echo Date::fuzzy_span($user->last_login); ?></span>
            </div>
        </div>
    </div><!--end .section-body -->
</section>
<!-- END PROFILE HEADER  -->

<section>
    <div class="section-body no-margin">
        <div class="row">
            <div class="col-md-12">
                <?php if (sizeof($info) > 0) { ?>
                    <div role="alert" class="alert alert-success">
                        <?php foreach ($info as $k => $e): ?>
                            <?php echo $e; ?>  
                        <?php endforeach; ?>
                    </div>

                <?php } else { ?>
                    <?php if (sizeof($errors) > 0): ?>
                        <div role="alert" class="alert alert-success">
                            <?php foreach ($errors as $k => $e): ?>
                                <?php echo $e; ?>  
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php } ?>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card card-underline ">
                        <div class="card-head">
                            <header><i class="fa fa-user"></i> Datos de Usuario: <?php echo $user->username ?></header>

                        </div>
                        <div class="card-body">

                            <form method="post" action="" class="form" name="form-datos">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <?php echo Form::input('nombre', $user->nombre, array('class' => 'form-control')); ?>
                                            <label for="nombre">Nombre:</label>
                                        </div> 
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <?php echo Form::input('mosca', $user->mosca, array('class' => 'form-control')); ?>
                                            <label for="nombre">Mosca:</label>
                                        </div> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?php echo Form::input('cargo', $user->cargo, array('class' => 'form-control')); ?>
                                    <label for="nombre">Cargo:</label>
                                </div>
                                <!--<div class="form-group">
                                <?php // echo Form::input('email', $user->email, array('class' => 'form-control')); ?>
                                    <label for="nombre">E-mail:</label>
                                </div>                                 -->
                                <div class="form-group">
                                    <?php echo Form::submit('submit-usuario', 'Modificar datos', array('class' => 'btn btn-sm btn-primary-dark', 'type' => 'submit')); ?>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="">
                            <div class="col-md-12">


                            </div>
                        </div>
                    </div>
                    <!-- BEGIN MESSAGE ACTIVITY -->

                </div><!--end .col -->
                <div class="col-lg-4 col-md-6">
                    <div class="card card-underline style-default-dark">
                        <div class="card-head">
                            <header><i class="fa fa-image"></i> Foto de perfil </header>

                        </div>
                        <div class="card-body">

                            <div class="col-md-7">
                                <div class="thumb">


                                    <?php
                                    $link = '<i class="fa fa-pencil"></i>';
                                    $id = "btn_crop";
                                    $title = "Editar foto";
                                    $foto = 'static/fotos/' . $user->username . '.jpg';
                                    if (file_exists(DOCROOT . $foto)) {
                                        $id = "btn_foto";
                                        $title = "Modificar";
                                        $foto = 'static/fotos/' . $user->username . '.jpg';
                                    } else {
                                        $link = '<i class="fa fa-cut"></i>';
                                        $id = "btn_crop";
                                        $title = "Recortar foto";
                                        $foto = 'static/fotos/tmp/' . $user->username . '.jpg';
                                        if (!file_exists(DOCROOT . $foto)) {
                                            $foto = 'static/fotos/hombre.jpg';
                                            if ($user->genero != 'hombre') {
                                                $foto = 'static/fotos/mujer.jpg';
                                            }
                                            $id = "btn_foto";
                                            $title = "Subir foto";
                                            $link = '<i class="fa fa-camera" > </i>';
                                        }
                                    }
                                    ?>
                                    <img src="/<?php echo $foto ?>?t=<?php echo time(); ?>" width="180" alt="" class=" img-rounded" style="margin: 0">                                        
                                    <h3 class="text-center col-lg-12">                                        
                                        <?php echo $user->username; ?>                                       
                                    </h3>                                    
                                    <span> <a href="javascript:;"  title="<?php echo $title ?>"  alt="<?php echo $title ?>" id="<?php echo $id; ?>" class="col-md-12  text-center">
                                            <?php echo $link; ?>
                                        </a>
                                        <?php if ($id == 'btn_crop') { ?>
                                            <a href="javascript:;"  title="Modificar foto"  alt="Modificar foto" id="btn_foto" class="col-md-12  text-center">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        <?php } ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-5">
                                <p>
                                    Agrege o cambie su foto de perfil.<br> El formato permitido para la foto es jpg. <br>Una vez subido la imagen debe de recortarla.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END MESSAGE ACTIVITY -->

                <!-- BEGIN PROFILE MENUBAR -->
                <div class="  col-lg-4 col-md-8">
                    <div class="card card-underline style-default-dark">
                        <div class="card-head">
                            <header class="opacity-75"><small>Destinatarios</small></header>
                            <div class="tools">
                                <form class="navbar-search" role="search">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="friendSearch" placeholder="Enter your keyword">
                                    </div>
                                    <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>                                
                                </form>
                                <a href="javascript:;" class="btn btn-icon-toggle" rel="<?php echo $user->id ?>" id="addDestinatario" title="Agredar nuevo destinatario"><i class="fa fa-user-plus"></i></a>
                            </div><!--end .tools -->
                        </div><!--end .card-head -->
                        <div class="card-body no-padding height-8 scroll text-medium text-shadow ">
                            <ul class="list">                                              
                                <?php foreach ($destinatarios as $d): ?>                                                                                
                                    <li class="tile">
                                        <a class="tile-content ink-reaction" href="/user/profile/<?php echo $d['id'] ?>">
                                            <div class="tile-icon">
                                                <?php if (file_exists(DOCROOT . 'static/fotos/' . $d['username'] . '.jpg')): ?>
                                                    <img class="img-circle border-white border-xl img-responsive " width="110" src="/static/fotos/<?php echo $d['username'] ?>.jpg" alt="" />
                                                    <?php
                                                else:
                                                    ?>
                                                    <img class="img-circle border-white border-xl img-responsive " width="110" src="/static/fotos/<?php echo $d['genero'] . '.jpg' ?>" alt="" />
                                                <?php endif; ?>
                                            </div>
                                            <div class="tile-text text-sm"><?php echo $d['nombre'] ?><small><?php echo $d['cargo'] ?></small></div>
                                        </a>
                                        <a href="/user/xdes/?id_des=<?php echo $d['id']; ?>&id_user=<?php echo $user->id; ?>" rel="<?php echo $d['nombre'] ?>" class="btn btn-xs text-xs btn-danger  pull-right delDestinatario "><i class="md md-close"></i></a>
                                    </li>                                                             
                                <?php endforeach; ?>
                            </ul>
                        </div><!--end .card-body -->
                    </div><!--end .card -->
                </div><!--end .col -->
            </div><!--end .col -->
            <!-- END PROFILE MENUBAR -->

        </div><!--end .row -->
    </div><!--end .section-body -->
</section>









<div id="modal-regular" class="modal" aria-hidden="true" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">Subir Fotograf&iacute;a</h4>
            </div>
            <div id="subir_foto">
                <form action="/user/subirfoto" class="dropzone" id="myAwesomeDropzone">
                    <input type="hidden" name="username" id="ci" value="<?php echo $user->username; ?>"/>
                    <input type="hidden" name="idp" id="idp" value="<?php echo $user->id; ?>"/>

                </form> 
            </div>                
        </div>
    </div>
</div>
<div id="modal-crop" class="modal" aria-hidden="true" role="dialog" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
                <h3 class="modal-title">Recortar foto</h3>
            </div>
            <div id="recortar_foto" class="article ">
                <input type="hidden" id="ancho" name="ancho" />
                <input type="hidden" id="alto" name="alto" />
                <input type="hidden" id="imgancho" name="imgancho" />
                <input type="hidden" id="imgalto" name="imgalto" />
                <!--Image that we Will insert -->
                <img class='imagem_artigo' src="/<?php echo $foto; ?>?t<?php echo time(); ?>" id="cropbox" width="100%" height="100%" />
                <!--Form to crop-->
                <form id="coords"
                      class="coords"  name="fscrop"
                      method="post"
                      action="/user/profile">
                    <div class="inline-labels">
                        <input type="hidden" id="ci" name="username" value="<?php echo $user->username ?>" />
                        <label>X1 <input type="hidden" size="4" id="x1" name="x1" /></label>
                        <label>Y1 <input type="hidden" size="4" id="y1" name="y1" /></label>
                        <label>X2 <input type="hidden" size="4" id="x2" name="x2" /></label>
                        <label>Y2 <input type="hidden" size="4" id="y2" name="y2" /></label>
                        <label>W <input type="hidden" size="4" id="w" name="w" /></label>
                        <label>H <input type="hidden" size="4" id="h" name="h" /></label>
                    </div>
                    <input type="submit" name="scrop" value="Aceptar" class="btn btn-danger"/>
                </form>
            </div>
        </div>
    </div>
</div>





<!-- -->