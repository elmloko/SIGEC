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
        $('#frmArchivar').submit(function () {
            var n = 0;
            $('input.principal').each(function () {
                if ($(this).is(':checked'))
                {
                    n += 1;
                }
            });
            if (n > 0)
                return true
            else
            {
                alert("Por favor selecione una hoja re ruta");
                return false;
            }
        });
    });

</script>
<div class="row">
    <div class="col-md-12">
        <form method="post" action="/bandeja/agruparf" id="frmArchivar"> 
            <div class="card card-underline">
                <div class="card-head">
                    <header>Agrupar hojas de ruta</header>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Seleccione la hoja de ruta que sera la principal.</label>
                        <div class="col-sm-9">
                            <?php foreach ($nurs as $k => $v): ?>
                                <div class="radio radio-styled">
                                    <label>
                                        <input type="radio" checked="" value="<?php echo $k; ?>"class="principal" name="principal">
                                        <span><?php echo $v; ?></span>
                                    </label>

                                </div>
                                <input type="hidden" value="<?php echo $k; ?>" name="seg[]" />    
                            <?php endforeach; ?>

                        </div><!--end .col -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr/>
                            <div class="form-group">                        
                                <?php echo Form::input('submit', '  Agrupar', array('type' => 'submit', 'class' => 'btn btn-sm btn-primary-dark', 'id' => 'list')); ?>       
                                <a onclick="javascript:history.back(); return false;" href="#"  class="btn btn-sm btn-default" ><i class="fa fa-reply"></i> Regresar<a/>
                                <input type="hidden" value="0" name="tipo" id="tipo"/> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>