<script type="text/javascript">
    $(function () {
        $('a#adicionar').click(function () {
            var val = '';
            $(':checked').each(function (i) {
                val = val + $(this).val() + ';';
            });
            if (val != '')
            {
                window.returnValue = val;
                window.close();
            }
            else
            {
                alert('Seleccione al menos un usuario.');
            }
        });
//add index column with all content.
        $("table#theTable tbody tr:has(td)").each(function () {
            var t = $(this).text().toLowerCase(); //all row text
            $("<td class='indexColumn'></td>")
                    .hide().text(t).appendTo(this);
        });//each tr
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            //show all rows.
            $("#theTable tbody tr:hidden").show();
            $.each(s, function () {
                $("#theTable tbody tr:visible .indexColumn:not(:contains('"
                        + this + "'))").parent().hide();
            });//each
        });//key up.    
        $("#theTable").tablesorter({sortList: [[0, 1]],
            widgets: ['zebra'],
            headers: {
                6: {sorter: false}
            }
        });
        //por defecto ubicamos el foco en buscar
        $('#FilterTextBox').focus();
    });
</script>
<h2 style="font-size: 12px; text-align: center; ">USUARIOS A SER AÑADIDOS A LISTA DE DESTINATARIOS</h2>
<p style="margin: 5px auto;"> <b>Filtrar/Buscar: </b><input type="text" id="FilterTextBox" name="FilterTextBox" size="40" /></p>
<a href="#" class="uibutton" id="adicionar">+ Adicionar</a>
<br/>
<br/>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-head">

            </div>
            <div class="card-body">
                <table id="theTable" class="tablesorter">
                    <thead>
                        <tr>
                            <th>
                                id
                            </th>

                            <th>
                                NOMBRE
                            </th>
                            <th>
                                CARGO
                            </th>
                            <th>
                                OFICINA
                            </th>
                            <th>
                                ENTIDAD
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($destinos as $e): ?>
                            <tr>
                                <td>
                                    <?php echo Form::checkbox('s', $e->id, FALSE, array('class' => 'check')); ?> 
                                </td>
                                <td>
                                    <?php echo $e->nombre; ?> 
                                </td>
                                <td>
                                    <?php echo $e->cargo; ?>              
                                </td>

                                <td>
                                    <?php echo $e->oficina; ?> 
                                </td>
                                <td>
                                    <?php echo $e->sigla; ?> 
                                </td>            

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php ?>
