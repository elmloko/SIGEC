<script type="text/javascript">
    $(function () {
        $('#cancelar').click(function () {
            window.close();
        });
        $('a#adicionar').click(function () {
            var val = '';
            $(':checked').each(function (i) {
                val = val + $(this).val() + ';';
            });
            if (val != '')
            {
                var id_user=$('#id_user').val();
                $.ajax({
                    type: "POST",
                    data: {destinos: val, id: id_user},
                    url: "/ajax/addUser",
                    // dataType: "html",
                    success: function (data)
                    {
                        parent.location.reload(true);
                    }
                });              
            }
            else
            {
                alert('Seleccione al menos un usuario.');
            }
        });
//add index column with all content.
        $(".list-results .usuario").each(function () {
            var t = $(this).text().toLowerCase(); //all row text
            $("<td class='indexColumn'></td>")
                    .hide().text(t).appendTo(this);
        });//each tr
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            //show all rows.
            $(".list-results .usuario").show();
            $.each(s, function () {
                $(".list-results .usuario:visible .indexColumn:not(:contains('"
                        + this + "'))").parent().hide();
            });//each
        });//key up.            
        //por defecto ubicamos el foco en buscar
        $('#FilterTextBox').focus();
    });
</script>
<style>
    .filtro{ position: fixed; height: 60px; top: 0; z-index: 40; background: #efefef; width: 100%;  }
    .contenido{ margin-top:62px; position: relative; z-index: 1; background-color: #fff;  }
    .list-results{  background-color: #fff;  }
</style>
<input type="hidden" value="<?php echo $id;?>" id="id_user">
<div class="filtro col-md-12 col-xs-12 col-sm-12 col-lg-12 style-gray-dark">
    <div class="row">
        <div class="col-md-6 col-xs-6 col-sm-6 ">
            <form class="form">
                <div class="form-group">
                    <input type="text" id="FilterTextBox" class="form-control" name="FilterTextBox" size="40" />
                    <label>Filtrar</label>
                </div>
            </form>
        </div>
        <div class="col-md-6 col-xs-6 col-sm-6 "><br/>
<!--            <a href="#" class="btn btn-sm btn-default-dark pull-right" id="cancelar"><i class="md md-cancel"></i> Cancelar</a>-->
            <a href="#" class="btn btn-sm btn-primary-dark pull-right" id="adicionar"><i class="fa fa-user-plus"></i> Adicionar</a>
        </div>
    </div>

</div>
<div class="contenido ">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <!-- BEGIN SEARCH RESULTS LIST -->
            <div class="margin-bottom-xxl">
                <span class="text-light text-lg"></span>

            </div><!--end .margin-bottom-xxl -->
            <div class="list-results">
                <?php foreach ($destinos as $d): ?>
                    <div class="col-xs-12 col-lg-4 col-md-6 hbox-xs usuario style-default">
                        <div class="hbox-column width-2">
                            <?php if (file_exists(DOCROOT . 'static/fotos/' . $d->username . '.jpg')): ?>
                                <img  class="img-circle img-responsive pull-left" src="/static/fotos/<?php echo $d->username ?>.jpg?<?php echo time() ?>" alt="" />    
                                <?php
                            else:
                                ?>
                                <img  class="img-circle img-responsive pull-left" src="/static/fotos/<?php echo $d->genero . '.jpg' ?>" alt="" />
                            <?php endif; ?>

                        </div><!--end .hbox-column -->
                        <div class="hbox-column v-top">
                            <div class="clearfix">
                                <div class="col-lg-12 margin-bottom-lg">
                                    <span class="text-lg text-medium" ><?php echo $d->nombre ?></span>
                                </div>
                            </div>
                            <div class="clearfix opacity-75">
                                <div class="col-md-12">
                                    <span class="glyphicon glyphicon-phone text-sm"></span> &nbsp;<?php echo $d->cargo ?>
                                </div>                        
                            </div>
                            <div class="clearfix">
                                <div class="col-lg-12">
                                    <span class="opacity-75"><span class="glyphicon glyphicon-map-marker text-sm"></span> &nbsp;<?php echo $d->oficina ?></span>
                                </div>
                            </div>
                            <div class="stick-top-right small-padding">
                                <div class="checkbox checkbox-styled">
                                    <label>
                                        <input type="checkbox" name="s" class="check" value="<?php echo $d->id ?>"> <span></span>

                                    </label>
                                </div>
                            </div>
                        </div><!--end .hbox-column -->
                    </div><!--end .hbox-xs -->
                <?php endforeach; ?>   

            </div><!--end .list-results -->
        </div><!--end .list-results -->
    </div><!--end .list-results -->


</div>

<!-- BEGIN SEARCH RESULTS LIST -->