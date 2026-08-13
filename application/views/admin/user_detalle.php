<script type="text/javascript">
    $(function () {
        $('#addDes').click(function () {
            var id_user = $(this).attr('rel');
            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 800) / 2;
            top = (top - 500) / 2;
            var res = window.showModalDialog("/admin/content/destinos/" + id_user, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            if (res != null)
            {
                $("#destinatarios").addClass('loading');
                $.ajax({
                    type: "POST",
                    data: {destinos: res, id: id_user},
                    url: "/admin/ajax/addUser",
                    // dataType: "html",
                    success: function (data)
                    {
                        location.reload(true);
                    }
                });
            }
        });
        $('#addDoc').click(function () {
            var id_user = $(this).attr('rel');
            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 800) / 2;
            top = (top - 500) / 2;
            var res = window.showModalDialog("/admin/content/documentos/" + id_user, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            if (res != null)
            {
                $("#documentos").addClass('loading');
                $.ajax({
                    type: "POST",
                    data: {documentos: res, id: id_user},
                    url: "/admin/ajax/addDoc",
                    // dataType: "html",
                    success: function (data)
                    {
                        location.reload(true);
                    }
                });
            }
        });
        //quitar doc
        $('a.delDoc').click(function () {
            var tipo = $(this).attr('rel');
            if (confirm('Esta usted seguro de eliminar el tipo : ' + tipo))
            {
                return true;
            } else {
                return false;
            }
            return false;
        });
        //quitar destinatario
        $('a#aceptar').click(function ()
        {
            var tipos = $('#documentos').val();
            var id_user = $('#usuario').val();
            // console.log(x);
            $.ajax({
                type: "POST",
                data: {tipos: tipos, id_user: id_user},
                url: "/admin/ajax/addDocumentos",
                // dataType: "html",
                success: function (data)
                {
                    var xxx=parent.eModal.close();
                    console.log(xxx);
                }
            });

        });
        $('a.delDes').click(function ()
        {
            var nombre = $(this).attr('rel');
            if (confirm("Esta usted seguro de quitar de la lista a: \n" + nombre)) {
                return true;
            } else {
                return false;
            }

        });
        $('#formPass').validate();



        if (!$.isFunction($.fn.multiSelect)) {
            return;
        }
        $('#documentos').multiSelect({selectableOptgroup: true});



    });
</script>

<style type="text/css">
    label{color: #666; font-size: 12px; width: 140px;  float: left;   }
    label.error{display: inline; float:right; width: 15px; }
    input.error{border: 1px solid red; }
</style>

<!-- BEGIN MULTI-SELECT -->
<div class="row">
    <div class="col-lg-12">
        <h4 class=" text-info"><?php echo $user->nombre; ?></h4>
    </div><!--end .col -->
    <div class="col-lg-12 col-md-12">
        <article class="margin-bottom-xxl">
            <p>
                Seleccione los tipos de documentos permitidos para el usuario. Los de la derecha son los que puede generar.
            </p>
        </article>
    </div><!--end .col -->
</div>
<div class="row">
    <div class="col-lg-offset-1 col-md-10">
        <div class="card">
            <div class="card-body">
                <form class="form" id="frmDocumentos">
                    <input type="hidden" value="<?php echo $user->id ?>" name="usuario" id="usuario" />
                    <select id="documentos" multiple="multiple" name="documentos[]">
                        <?php
                        $tipoDocumentos = ORM::factory('tipos')->where('activo', '=', 1)->find_all();
                        foreach ($tipoDocumentos as $t):
                            $option = '<option value="';
                            $option.=$t->id . '" ';
                            if (array_key_exists($t->id, $documentos)) {
                                $option.=' selected="selected"';
                                //echo "sas";
                            }
                            $option.=">" . $t->tipo . "</option>";
                            echo $option;
                            $option = "";
                        endforeach;
                        ?>
                    </select>                    
                </form>
            </div><!--end .card-body -->
            <div class=" card-foot">
                <div class=" pull-right">
                    <a href="javascript:;" id="aceptar" class="btn btn-primary-dark">Aceptar</a>    
                </div>

            </div>
        </div><!--end .card -->
        <em class="text-caption">Multi-select</em>
    </div><!--end .col -->
</div><!--end .row -->
<!-- END MULTI-SELECT -->


