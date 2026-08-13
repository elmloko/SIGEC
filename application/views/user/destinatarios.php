<script type="text/javascript">
    $(function () {
        $('#addDes_old').click(function () {
            var id_user = $(this).attr('rel');
            var left = screen.availWidth;
            var top = screen.availHeight;
            left = (left - 800) / 2;
            top = (top - 500) / 2;
            var res = window.showModalDialog("/content/destinos/" + id_user, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
            if (res != null) {
                $("#destinatarios").addClass('loading');
                $.ajax({
                    type: "POST",
                    data: {destinos: res, id: id_user},
                    url: "/ajax/addUser",
                    // dataType: "html",
                    success: function (data) {
                        location.reload(true);
                    }
                });
            }
        });
        $('#addDes').click(function () {
            var id_user = $(this).attr('rel');

            eModal.iframe('/content/destinos/' + id_user, 'Agregar Destinatario')

            //location.reload(true);

        });
        //quitar destinatario
        $('a.delDes').click(function () {
            var nombre = $(this).attr('rel');
            if (confirm("Esta usted seguro de quitar de la lista a: \n" + nombre)) {
                return true;
            }
            else {
                return false;
            }

        });
        $("#theTable").tablesorter({
            sortList: [[1, 0]],
            widgets: ['zebra'],
            headers: {
                0: {sorter: false}
            }
        });
    });
</script>

<div id="destinatarios">
    <?php echo Form::input('addDes', '+ Adicionar', array('class' => 'button2', 'type' => 'button', 'id' => 'addDes', 'rel' => $user->id)); ?>
    <!--
    <a href="javascript:;" class="btn btn-icon-toggle" rel="<?php echo $user->id ?>"
       id="addDestinatario" title="Agredar nuevo destinatario">
        <i class="fa fa-user-plus"></i>
    </a>
    -->
    <h2 style="">Destinatarios Permitidos</h2>
    <br/>
    <table id="theTable" class="tablesorter">
        <thead>
        <tr>
            <th>ACCIÓN</th>
            <th>NOMBRE</th>
            <th>OFICINA</th>
            <th>ENTIDAD</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($destinatarios as $d): ?>
            <tr>
                <td><a href="/user/xdes/?id_des=<?php echo $d->id; ?>&id_user=<?php echo $user->id; ?>"
                       class="delDes button" rel="<?php echo $d->nombre; ?>">Quitar</a></td>
                <td><span><?php echo $d->nombre; ?></span> <br/><b><?php echo $d->cargo; ?></b></td>
                <td><?php echo strtoupper($d->oficina); ?></td>
                <td><b><?php echo $d->entidad; ?></b></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
<?php echo ''; ?>


<!-- BEGIN SEARCH RESULTS LIST -->