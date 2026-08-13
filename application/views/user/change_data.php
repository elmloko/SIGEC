<!-- BEGIN PROFILE HEADER -->
<section class="full-bleed">
    <div class="section-body style-default-dark force-padding text-shadow ">
        <div class="img-backdrop" style="background-image: url('/static/fondo.jpg')"></div>
        <div class="overlay overlay-shade-top stick-top-left height-3"></div>
        <div class="row">
            <div class="col-md-3 col-xs-5">
                <?php if (file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg')): ?>
                    <img class="img-circle border-white border-xl img-responsive " width="110" src="/static/fotos/<?php echo $user->username ?>.jpg" alt="" />
                    <?php
                else:
                    ?>
                    <img class="img-circle border-white border-xl img-responsive " width="110" src="/static/fotos/<?php echo $user->genero . '.jpg' ?>" alt="" />
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
            <div class="col-md-5">
                <div class="card card-underline">
                    <div class="card-head">
                        <header><i class="fa fa-user"></i> Datos de Usuario: <?php echo $user->username ?></header>
                        <div class="tools">
                            <a class="btn btn-icon-toggle btn-close"><i class="md md-close"></i></a>
                        </div>
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
                            <div class="form-group">
                                <?php echo Form::input('email', $user->email, array('class' => 'form-control')); ?>
                                <label for="nombre">E-mail:</label>
                            </div>                                
                            <div class="form-group">
                                <?php echo Form::submit('submit-usuario', 'Modificar datos', array('class' => 'btn btn-sm btn-primary-dark', 'type' => 'submit')); ?>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- BEGIN MESSAGE ACTIVITY -->
                <div class="tab-pane" id="activity">
                    <ul class="timeline collapse-lg timeline-hairline">
                        <li class="timeline-inverted">
                            <div class="timeline-circ circ-xl style-primary"><i class="md md-event"></i></div>
                            <div class="timeline-entry">
                                <div class="card style-default-light">
                                    <div class="card-body small-padding">
                                        <span class="text-medium">Received a <a class="text-primary" href="../../html/mail/inbox.html">message</a> from <span class="text-primary">Ann Lauren</span></span><br/>
                                        <span class="opacity-50">
                                            Saturday, Oktober 18, 2014
                                        </span>
                                    </div>
                                </div>
                            </div><!--end .timeline-entry -->
                        </li>

                    </ul>
                </div><!--end #activity -->
            </div><!--end .col -->
            <!-- END MESSAGE ACTIVITY -->

            <!-- BEGIN PROFILE MENUBAR -->
            <div class="col-lg-offset-1 col-lg-3 col-md-4">
                <div class="card card-underline style-default-dark">
                    <div class="card-head">
                        <header class="opacity-75"><small>Friends</small></header>
                        <div class="tools">
                            <!--
                            <form class="navbar-search" role="search">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="friendSearch" placeholder="Enter your keyword">
                                </div>
                                <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
                            </form>
                            -->
                            <form action="/search/advanced">
                                <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
                            </form> 
                        </div><!--end .tools -->
                    </div><!--end .card-head -->
                    <div class="card-body no-padding">
                        <ul class="list">
                            <li class="tile">
                                <a class="tile-content ink-reaction" href="#2">
                                    <div class="tile-icon">
                                        <img src="../../assets/img/avatar2.jpg?1404026449" alt="" />
                                    </div>
                                    <div class="tile-text">Abbey Johnson<small>Lorem ipsum dolor sit amet, consectetur adipisicing</small></div>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon">
                                        <img src="../../assets/img/avatar4.jpg?1404026791" alt="" />
                                    </div>
                                    <div class="tile-text">Alex Nelson<small>Proin nonummy, lacus eget pulvinar lacinia</small></div>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon">
                                        <img src="../../assets/img/avatar11.jpg?1404026774" alt="" />
                                    </div>
                                    <div class="tile-text">Mary Peterson<small>Nulla gravida orci a odio</small></div>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon">
                                        <img src="../../assets/img/avatar7.jpg?1404026721" alt="" />
                                    </div>
                                    <div class="tile-text">Trevor Hanson<small>Nullam varius, turpis et commodo pharetra</small></div>
                                </a>
                            </li>
                        </ul>
                    </div><!--end .card-body -->
                </div><!--end .card -->
                <div class="card card-underline style-default-dark">
                    <div class="card-head">
                        <header class="opacity-75"><small>Personal info</small></header>
                        <div class="tools">
                            <a class="btn btn-icon-toggle ink-reaction"><i class="md md-edit"></i></a>
                        </div><!--end .tools -->
                    </div><!--end .card-head -->
                    <div class="card-body no-padding">
                        <ul class="list">
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon">
                                        <i class="md md-location-on"></i>
                                    </div>
                                    <div class="tile-text">
                                        621 Johnson Ave, Suite 600
                                        <small>Street</small>
                                    </div>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon"></div>
                                    <div class="tile-text">
                                        San Francisco, CA 54321
                                        <small>City</small>
                                    </div>
                                </a>
                            </li>
                            <li class="divider-inset"></li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <div class="tile-text">
                                        (123) 456-7890
                                        <small>Mobile</small>
                                    </div>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-icon"></div>
                                    <div class="tile-text">
                                        (323) 555-6789
                                        <small>Work</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div><!--end .card-body -->
                </div><!--end .card -->
            </div><!--end .col -->
            <!-- END PROFILE MENUBAR -->

        </div><!--end .row -->
    </div><!--end .section-body -->
</section>





