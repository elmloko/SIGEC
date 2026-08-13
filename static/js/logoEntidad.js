$(function () {
    $("#btn_foto").click(function () {
        $('#modal-regular').modal('show');
    });
    //mask

    $("a.sdoc").click(function () {

        var iddoc = $(this).attr('id_doc');
        $('#id_documento').val(iddoc);

        $('#modal-files').modal('show');
    });
    $("#btn_crop").click(function () {
        $('#modal-crop').modal('show');
    });

    $("#nuevo_dj").click(function () {
        $('#modal-declaracion').modal('show');
    });
    var ci = $('input#ci').val();
    var idp = $('input#idp').val();
    var direccion = "/admin/entidades/subirlogo/";
    $("#myAwesomeDropzone").dropzone({
        url: direccion,
        paramName: "file",
        maxFilesize: 5,
        addRemoveLinks: true,
        uploadMultiple: false,
        maxFiles: 1,
        acceptedFiles: 'image/jpeg,image/png',
        success: function (file) {
            window.location.href = "/admin/entidades/logo/"+idp;
            //     location.reload();
        }
    });

    var jcrop_api;

    $('#cropbox').Jcrop({
        onChange: showCoords,
        onSelect: showCoords,
        //aspectRatio: 1,
        aspectRatio:   24 / 9,
        onRelease: clearCoords
    }, function () {
        jcrop_api = this;
    });

    $('#coords').on('change', 'input', function (e) {
        var x1 = $('#x1').val(),
                x2 = $('#x2').val(),
                y1 = $('#y1').val(),
                y2 = $('#y2').val();
        jcrop_api.setSelect([x1, y1, x2, y2]);
    });


    // Simple event handler, called from onChange and onSelect
    // event handlers, as per the Jcrop invocation above
    function showCoords(c)
    {
        $('#x1').val(c.x);
        $('#y1').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);
    }
    ;

    function clearCoords()
    {
        $('#coords input').val('');
    }
    ;
    $("#recortar").click(function () {
        if ($("#foto_persona").val()) {
            //alert ('hola');
            var imagen = $("#foto_persona").val();
        } else {
            //alert ('chau');
            var imagen = $("#ci").val();
        }
        checkCoords();
        var v = $.ajax({
            url: '/admin/entidades/crop/',
            type: 'POST',
            datatype: 'jason',
            data: {x: $("#x").val(), y: $("#y").val(), w: $("#w").val(), h: $("#h").val(), ci: $("#ci").val()},
            success: function (data) {
                alert('Guardado con éxito');//alert (data);
                //var res = jQuery.parseJSON(data);
                //alert(res.msm);
                $("#ci").attr('disabled', 'disabled');
                $("#cerrar").click();
                //$("#boton_foto").unbind();
                $("#boton_foto").load('/admin/entidades/subirlogo' + imagen);
                //$("#btn_foto").hide();
            },
            error: function () {
                alert('Se ha producido un error Inesperado');
            }

        });
    });
    function updateCoords(c) {//alert (c.x+' | '+c.y+' | '+c.w+' | '+c.h);
        var newX = ($("#imgancho").val() * c.x) / $("#ancho").val();
        var newY = ($("#imgancho").val() * c.y) / $("#ancho").val();
        var newW = ($("#imgancho").val() * c.w) / $("#ancho").val();
        var newH = ($("#imgancho").val() * c.h) / $("#ancho").val();
        //alert(newX+"*"+$("#ancho").val()+"/"+$("#imgancho").val());
        //newX = newX*ancho/imgAncho;
        //alert (newX+" | "+newY+" | "+newW+" | "+newH+" | "+c.w+" | "+c.h);
        $('#x').val(newX);
        $('#y').val(newY);
        $('#w').val(newW);
        $('#h').val(newH);
    }
    ;
    function checkCoords() {
        if (parseInt($('#w').val()))
            return true;
        alert('Seleccione el espacio a cortar.');
        return false;
    }
});


