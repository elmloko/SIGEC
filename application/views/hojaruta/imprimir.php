<script type="text/javascript">
    $(function () {
        $('#boton').click(function ()
        {
            var hs = $('#selectbox-o').val().replace(/^\s+|\s+$/g, "");
            var n = $("input:checked").length;
            console.log(n);

            if (hs == "")
            {
                //  alert('Escriba una Hoja de Ruta por favor');
                eModal.alert('Escriba una Hoja de Ruta por favor', '');
            } else
            {
                $.ajax({
                    type: "POST",
                    data: {nur: hs},
                    url: "/ajax/print_hs",
                    dataType: "json",
                    success: function (data)
                    {
                        if (data.nur > 0)
                        {

                            window.open(
                                    '/print/hr/?code=' + hs + '&t=' + 2+'&p='+n,
                                    '_blank' // <- This is what makes it open in a new window.
                                    );                            
                        } else
                        {
                            eModal.alert('La hoja de ruta ' + hs + ' no existe');
                        }
                    }
                });
            }
            return false;
        });
        $('#nur').focus();

        //  $('#selectbox-o ').append('<option value="3" selected >SET VAlue</option>');        
        //$('#selectbox-o option[value="3"]').attr('selected','SELECTED');

        $('#selectbox-o').select2({
            ajax: {
                url: "/ajax/hojaruta",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 2
        });
        $("#selectbox-o-").select2({
            minimumInputLength: 2,
            tags: [],
            ajax: {
                url: '/ajax/hojaruta',
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.text,
                                //slug: item.slug,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });


    });
</script>

<div class="card card-underline">
    <div class="card-head">
        <header>Imprimir Hojas de Ruta</header>
    </div>
    <div class="card-body">
        <p>Escriba la Hoja de Ruta que desea imprimir y presione el boton [IMPRIMIR]: </p>
        <div class="row">
            <div class="col-md-4">
                <form action="javascript:;" method="post" class="form required" >
                    <div class="form-groups">
                        <select id="selectbox-o" class="form-control" >
                            <option value="">Escriba hoja de ruta</option>
                        </select>
                    </div>
                    <?php // echo Form::input('nur', Arr::get($_POST, 'nur', ''), array('id' => 'nur'))  ?>
                </form>
                <div class="form-groups">
                    <div class="checkbox checkbox-styled ">
                        <label> Imprimir con proveido 
                            <input type="checkbox" class="sel" rel="proveido" value="1" id="proveido" name="proveido"><span></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <a href="javascript:;" id="boton" class="btn btn-sm btn-primary-dark" ><i class="fa fa-print"></i> Imprimir</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="alert alert-info text-center">

    &larr;<a onclick="javascript:history.back();
            return false;" href="#" style=" text-decoration: underline;  " > Regresar<a/></p>    
</div>