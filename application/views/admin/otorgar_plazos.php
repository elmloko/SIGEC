<!-- BEGIN MULTI-SELECT -->
<div class="row">
    <div class="col-lg-12">
        <h4 class=" text-info"><?php echo $user->nombre; ?></h4>
    </div><!--end .col -->
    <div class="col-lg-12 col-md-12">
        <article class="margin-bottom-xxl">
            <p>
                Seleccione los usuarios a los cuales podrá asignar plazos de respuesta.
            </p>
        </article>
    </div><!--end .col -->
</div>
<div class="row">
    <div class="col-lg-offset-1 col-md-10">
        <div class="card">
            <div class="card-body">
                <input type="hidden" value="<?php echo $user->id ?>" name="usuario" id="usuario"/>
                <h4>Seleccionar</h4>
                <select id='usuarios-con-privilegios' multiple='multiple'>
                    <?php
                    foreach ($users_all as $user):
                        $option = '<option value="';
                        $option .= $user->id . '" ';
                        if (array_key_exists($user->id, $usuarios_que_reciben_plazos)) {
                            $option .= ' selected="selected"';
                        }
                        $option .= ">" . $user->nombre . ' - ' . $user->cargo . "</option>";
                        echo $option;
                        $option = "";
                    endforeach;
                    ?>
                </select>
            </div>
            <div class=" card-foot">
                <div class=" pull-right">
                    <a id="aceptar" class="btn btn-primary-dark">Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MULTI-SELECT -->

<style type="text/css">
    label {
        color: #666;
        font-size: 12px;
        width: 140px;
        float: left;
    }

    label.error {
        display: inline;
        float: right;
        width: 15px;
    }

    input.error {
        border: 1px solid red;
    }
</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.3.1/jquery.quicksearch.min.js"></script>
<script type="text/javascript">
    //$('#usuarios-con-privilegios').multiSelect({selectableOptgroup: true});

    $('#usuarios-con-privilegios').multiSelect({

        selectableHeader: "<input type='text' class='search-input' autocomplete='on' placeholder=''>",
        selectionHeader: "<input type='text' class='search-input' autocomplete='on' placeholder=''>",
        afterInit: function (ms) {
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function () {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
        }
    });

    // Master-Detail de Documentos permitidos por Usuario
    $('a#aceptar').click(function () {

        var id_user = $('#usuario').val();
        var id_usuarios_que_reciben_plazos = $('#usuarios-con-privilegios').val();

        console.log('id_usuarios_que_reciben_plazos: ' + id_usuarios_que_reciben_plazos);
        console.log('id_user: ' + id_user);

        $.ajax({
            type: "POST",
            data: {
                id_usuarios_que_reciben_plazos: id_usuarios_que_reciben_plazos,
                id_user: id_user
            },
            url: "/admin/ajax/otorgarPlazosDeRespuestaAUsuarios",
            success: function (data) {
                console.log(data);
            }
        });
    });
</script>