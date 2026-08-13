<div class="row">
    <div class="col-md-12">
        <form method="post" action="" class="form" >
            <div class="card card-underline ">
                <div class="card-head">
                    <ul class="nav nav-tabs pull-right" data-toggle="tabs">
                        <li class="active"><a href="#first1">Configuración</a></li>
                        <li><a href="#second1">Prueba</a></li>                        
                    </ul>
                    <header>Correo Saliente</header>
                </div>
                <div class="card-body">
                    <div class="tab-pane active" id="first1">                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::input('smtpHost', Arr::get($tipo, 'smtpHost', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('tipo', 'Nombre del Servidor') ?>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::input('smtpPort', Arr::get($tipo, 'smtpPort', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('plural', 'Puerto') ?>
                                </div>

                            </div>                        
                        </div>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <?php echo Form::input('username', Arr::get($tipo, 'username', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('username', 'Nombre de usuario (correo)') ?>
                                </div>

                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <?php echo Form::input('password', Arr::get($tipo, 'action', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('password', 'Contraseña') ?>
                                </div>

                            </div>
                            
                            <div class="form-group">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::select('smtpSecure',Array('ssl'=>'SSL','tls'=>'TLS',''=>'NO'), Arr::get($tipo, 'smtpSecure', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('smtpSecure', 'Utilizar conexion segura') ?>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::input('descripcion', Arr::get($tipo, 'descripcion', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('descripcion', 'Descripción') ?>
                                </div>

                            </div>                        
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="checkbox checkbox-styled">
                                    <label>

                                        <?php
                                        $checked = FALSE;
                                        $chk = Arr::get($tipo, 'cite_propio', '0');
                                        if ($chk > 0) {
                                            $checked = TRUE;
                                        }
                                        echo Form::checkbox('cite_propio', Arr::get($tipo, 'cite_propio', ''), $checked);
                                        ?>
                                        <span>¿Cite Propio? [para documentos generalizados]</span>
                                    </label>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo Form::input('cite', Arr::get($tipo, 'cite', ''), array('class' => 'required form-control')) ?>
                                    <?php echo Form::label('cite', 'Cite Propio') ?>
                                </div>

                            </div>                        
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <input type="submit" name="submit" value="<?php echo Arr::get($tipo, 'submit', 'Crear Documento') ?>" class="btn btn-sm btn-primary-dark"/>
                            </div>
                        </div>


                    </div>
                    <div class="tab-pane" id="second1"><p>Ad ius duis dissentiunt, an sit harum primis persecuti, adipisci tacimates mediocrem sit et. Id illud voluptaria omittantur qui, te affert nostro mel. Cu conceptam vituperata temporibus has.</p>
                    </div>
                    <div class="tab-pane" id="third1"><p>Duo semper accumsan ea, quidam convenire cum cu, oportere maiestatis incorrupte est eu. Soluta audiam timeam ius te, idque gubergren forensibus ad mel, persius urbanitas usu id. Civibus nostrum fabellas mea te, ne pri lucilius iudicabit. Ut cibo semper vituperatoribus vix, cum in error elitr. Vix molestiae intellegat omittantur an, nam cu modo ullum scriptorem.</p>
                        <p>Quod option numquam vel in, et fuisset delicatissimi duo, qui ut animal noluisse erroribus. Ea eum veniam audire. Per at postea mediocritatem, vim numquam aliquid eu, in nam sale gubergren. Dicant vituperata consequuntur at sea, mazim commodo</p>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>