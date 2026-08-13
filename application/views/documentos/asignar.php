<script type="text/javascript">
    $(function () {
        //add index column with all content.
        $("table#theTables tbody tr:has(td)").each(function () {
            var t = $(this).text().toLowerCase(); //all row text
            $("<td class='indexColumn'></td>")
                    .hide().text(t).appendTo(this);
        });
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            $("#theTables tbody tr:hidden").show();
            $.each(s, function () {
                $("#theTables tbody tr:visible .indexColumn:not(:contains('" + this + "'))").parent().hide();
            });
        });
        $("#theTables").tablesorter({sortList: [[0, 1]], widgets: ['zebra'], headers: {2: {sorter: false}}});
        $('input').focus();
    });//document.ready
</script>
<div class="row">
    <div class="card card-underline">
        <div class="card-head">
            <header>
                <span>Asignar hoja de ruta al documento:</span><span class="text-primary-dark"><?php echo $documento->cite_original ?></span>
            </header>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-10">
                    Referencia: <?php echo $documento->referencia ?>
                </div>
                <div class="col-md-2">
                    <a href="/document/newHR/<?php echo $documento->id; ?>" class="btn btn-sm btn-accent-dark" > + ASIGNAR NUEVA HOJA RUTA</a>    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <hr/>
                    <span class="text-accent-dark"><i class="md md-help"></i> <i>   Asigne una nueva hoja de ruta en el boton asignar nueva hoja de ruta o tambien puede escoger de la lista de pendientes de abajo.</i></span>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6 col-lg-4">
        <form method="post" class="form">
            <div class="form-group">
                <input type="text" id="FilterTextBox" class="form-control" name="FilterTextBox"  />
                <label for="FilterTexBox">Filtrar</label>
            </div>
        </form>
    </div>

</div>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body no-padding">
                <table id="theTables" class=" table-striped table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th width="145" >Hoja de Ruta</th>
                            <th>Proceso</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendientes as $p): ?>
                            <tr>
                                <td><?php echo $p->nur; ?></td>
                                <td><?php echo $p->referencia; ?></td>
                                <td><a href="/document/asignacion/?hr=<?php echo $p->nur; ?>&id_doc=<?php echo $documento->id; ?>" class="btn btn-sm text-accent-dark btn-default-light" title="Asignar a la hoja de ruta '<?php echo $p->nur; ?>'"><i class="md md-class"></i> Asignar</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>