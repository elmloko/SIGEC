<div class="row">
    <div class="col-md-12">
        <form method="post" action="" class="form">
            <div class="card card-underline ">
                <div class="card-head">
                    <header>Agregar nuevo tipo de documento</header>
                </div>
                <div class="card-body">
                    <?php echo Form::hidden('id', Arr::get($tipo, 'id', 0), array('class' => 'required ')) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('tipo', Arr::get($tipo, 'tipo', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('tipo', 'Nombre tipo documento') ?>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('plural', Arr::get($tipo, 'plural', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('plural', 'Nombre tipo documento (Plural)') ?>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">

                            <div class="form-group">
                                <?php echo Form::input('abreviatura', Arr::get($tipo, 'abreviatura', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('tipo', 'Abreviatura') ?>
                            </div>

                        </div>
                        <div class="col-md-3">

                            <div class="form-group">
                                <?php echo Form::input('action', Arr::get($tipo, 'action', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('action', 'Slug (sin espacios ni caracteres esp.)') ?>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="checkbox checkbox-styled">
                                <label>
                                    <?php
                                    $checked = FALSE;
                                    if ($tipo['via'] === "1") {
                                        $checked = TRUE;
                                    }
                                    echo Form::checkbox('via', '1', $checked);
                                    ?>
                                    <?php echo Form::label('via', '¿Via? [para edición con vias]') ?>
                                    <!-- <span>¿Via? [para edición con vias]</span> -->
                                </label>
                            </div>

                        </div>
                        <div class="form-group">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('cite_tipo', Arr::get($tipo, 'cite_tipo', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('cite_tipo', 'Cite documento {$ent}{$ofi}{$cor}{$anio}{$tip}') ?>
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
                                    $checked2 = FALSE;
                                    if ($tipo['cite_propio'] === "1") {
                                        $checked2 = TRUE;
                                    }
                                    echo Form::checkbox('cite_propio', '1', $checked2);
                                    ?>
                                    <?php echo Form::label('cite_propio', '¿Cite Propio? [para documentos generalizados]') ?>
                                    <!-- <span>¿Cite Propio? [para documentos generalizados]</span> -->
                                </label>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('cite', Arr::get($tipo, 'cite', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('cite', 'Cite Propio') ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('template', Arr::get($tipo, 'template', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('template', 'Plantilla') ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('template_via', Arr::get($tipo, 'template_via', ''), array('class' => 'required form-control')) ?>
                                <?php echo Form::label('template_via', 'Plantilla Via') ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <input type="submit" name="submit"
                                   value="<?php echo Arr::get($tipo, 'submit', 'Crear tipo de documento') ?>"
                                   class="btn btn-sm btn-primary-dark"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>