<script type="text/javascript">
    $(function () {
        $('#nueva').click(function () {
            $('div#folder').removeClass('oculto');
            $('div#lista').addClass('oculto');
            $('#nc').focus();
        });
        $('#list').click(function () {
            $('div#folder').addClass('oculto');
            $('div#lista').removeClass('oculto');
        });
        $('#btnLista').click(function () {
            $('input#tipo').val(1);
            return true;
        });
        $('#btnNuevo').click(function () {
            $('input#tipo').val(0);
            return true;
        });
        $('select').select2();
    });
</script>
<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-archive"></i> Archivar Correspondencia</header>
            </div>
            <div class="card-body">
                <form method="post" class="form" action="/bandeja/archivarf" id="frmArchivar"> 

                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            Hojas de ruta seleccionadas:
                            <div class="form-group">
                                <ul>
                                    <?php foreach ($nurs as $k => $v): ?>
                                        <li>
                                            <span class=" text-primary-dark"><?php echo $v; ?></span>
                                            <input type="hidden" value="<?php echo $k; ?>" name="seg[]"/>    
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <textarea  class="form-control" name="observaciones"></textarea>
                                <label>Observaciones:</label>
                            </div>
                            <div class="form-group">
                                <?php if (sizeof($options) > 0) { ?>
                                    <div id="lista">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <?php echo Form::select('carpeta_lista', $options); ?>
                                            </div>
                                            <div class="col-md-4">
                                                <?php echo Form::input('nueva', 'Nueva carpeta', array('type' => 'button', 'class' => 'btn btn-sm btn-default ', 'id' => 'nueva')); ?>     

                                            </div>
                                        </div>

                                        <hr/>

                                        <?php echo Form::input('submit', 'Archivar', array('type' => 'submit', 'class' => 'btn btn-sm btn-primary-dark', 'id' => 'btnLista')); ?>         
                                    </div>
                                <?php } ?>

                                <div id="folder"  class="<?php
                                if (sizeof($options) > 0) {
                                    echo "oculto";
                                }
                                ?>">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <?php echo form::input('carpeta_input', '', array('id' => 'nc', 'class' => 'form-control required')); ?>
                                                <label for="carpeta_imput">Carpeta</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">

                                            <?php if (sizeof($options) > 0) { ?>
                                                <?php echo Form::input('button', 'Ver lista', array('type' => 'button', 'class' => 'btn btn-sm btn-default', 'id' => 'list')); ?>

                                            <?php } ?>    
                                        </div>
                                    </div>
                                    <input type="hidden" value="0" name="tipo" id="tipo"/> 
                                    <hr/>
                                    <?php echo Form::input('submit', 'Archivar', array('type' => 'submit', 'class' => 'btn btn-sm btn-primary-dark', 'id' => 'btnNuevo')); ?>    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>         
            </div>
        </div>
    </div>
</div>
