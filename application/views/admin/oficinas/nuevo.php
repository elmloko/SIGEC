<script type="text/javascript">
    $(function () {
        $('#id_oficina').change(function () {
            var sigla = $(this).find('option:selected').text().split('|');
            var siglaofi = sigla[1] + '-';
            $('#sigla').val(siglaofi.trim());
            $('#oficina').focus();
        });
        //$('#frmOficina').validate();

        var entidad = $('#id_entidad');
        var oficina = $('#id_oficina');
        //var superior = $('#superior');
        entidad.selectChain({
            target: oficina,
            url: "/admin/ajax/oficinassigla/",
            type: 'post',
            data: "ajax=true"
        });
        //entidad.trigger('change');
        $('select').select2();


    });
</script>
<?php if (sizeof($error) > 0): ?>
    <div class="error">
        <p><span style="float: left; margin-right: .3em;" class=""></span>
            <?php foreach ($error as $k => $v): ?>
            <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (sizeof($info) > 0): ?>
    <div class="info">
        <p><span style="float: left; margin-right: .3em;" class=""></span>
            <?php foreach ($info as $k => $v): ?>
            <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card card-underline">
    <div class="card-head">
        <header>Nueva Oficina (Gerencia,Departamento o Direccion, Unidad, Area)</header>
    </div>
    <div class="card-body">
        <form action="" method="post" id="frmOficina" class="form form-validate">
            <div class="row">
                <div class="col-md-8 col-lg-8">

                    <div class="form-group">
                        <?php
                        /*
                        echo Form::select(
                            'id_entidad',
                            $entidades,
                            HTML::chars(Arr::get($_POST, 'id_entidad', NULL)),
                            array(
                                'id' => 'id_entidad',
                                'class' => 'required form-control'
                            )
                        );
                        */
                        echo Form::select(
                            'id_entidad',
                            $entidades,
                            $id_entidad,
                            array(
                                'id' => 'id_entidad',
                                'class' => 'required form-control'
                            )
                        );
                        ?>
                        <label> Entidad</label>
                    </div>
                    <div class="form-group">
                        <?php
                        echo Form::select(
                            'padre',
                            $options,
                            HTML::chars(Arr::get($_POST, 'padre', NULL)),
                            array(
                                'id' => 'id_oficina',
                                'class' => 'required form-control'
                            )
                        );
                        ?>
                        <label> Depende de Oficina</label>
                    </div>
                    <div class="form-group">
                        <?php echo Form::input('oficina', HTML::chars(Arr::get($_POST, 'oficina', '')), array('class' => 'required form-control', 'id' => 'oficina')); ?>
                        <label> Nombre Oficina</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php echo Form::input('sigla', HTML::chars(Arr::get($_POST, 'sigla')), array('id' => 'sigla', 'class' => 'required form-control')); ?>
                                <label> Sigla</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="submit" name="create" value="Crear Oficina" class="btn btn-sm btn-primary-dark"/>
                    </div>


                    <?php ?>


                </div>
            </div>
        </form>
    </div>

</div>


