<script type="text/javascript">
    var stat = 0;
    var $this;
    var $cargo;
    $(function () {
        $('#destinatario').focus();
        $('#destinatario').focus(function () {
            $this = $(this);
            $cargo = $('#cargo_des');
        });
        $('#via').focus(function () {
            $this = $(this);
            $cargo = $('#cargovia');
        });
        $('#via2').focus(function () {
            $this = $(this);
            $cargo = $('#cargovia2');
        });
        $('#remitente').focus(function () {
            $this = $(this);
            $cargo = $('#cargo_rem');
        });
        $('a.destino1').click(function () {
            var destino = $(this);

            if ($cargo != undefined) {
                var nombre = $(this).attr('nombre');
                var cargo = $(this).attr('cargo');
                // var a=$this.nodeName;  
                // alert(index);
                // alert(foco.val());
                $this.val(nombre);
                $cargo.val(cargo);
            }
            else {
                $('#destinatario').val(destino.attr('nombre'));
                $('#cargo_des').val(destino.attr('cargo'));
            }
            //console.log($this);
            //console.log($('input:eq(' + parseInt(index) + ')').next().next().val(cargo));
            //console.log($('input:eq(' + parseInt(index) + 1 + ')').val(cargo));
            //$('input:eq('+index+')').next().val(cargo);            
            return false;
        });
        $('#noHojaRuta').click(function () {
            $('#hojaruta').val(0);
            return true
        });
        $('#cite_sup').click(function () {
            $('#cite_superior').val(1);
            $('#hojaruta').val(0);
            stat = 1;
            return true
        });

        $('#otrovia').click(function () {

            $('.via2').toggle(function () {
                return false;
            })

        });


        $('#addDest').click(function () {
            var id_user = $(this).attr('rel');
            eModal.setEModalOptions({
                loadingHtml: '<span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span><h4>Cargando usuarios...</h4>',

            });
            eModal.iframe('/content/destinos/' + id_user, 'Agregar Destinatario')

        });
    });
    $(function () {
        //var buttons = ['formatting', '|', 'bold', 'italic', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'table', 'link', 'alignment', '|', 'horizontalrule', 'underline', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'fontcolor', 'backcolor'];
        // $('#descripcion').redactor({lang: 'es', css: 'docstyle.css', focus: true,
        //   buttons: buttons});
//incluir destinatario

        //$('select').select2();
    });
    function isValidForms() {

        if ($('#proceso').val() > 0) {
            if (stat) {
                stat = 0;
                if (confirm("Esta seguro que desea generar un numero cite de su area superior.")) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        else {
            alert("Escoja un tipo de proceso por favor.");
            //return false;
        }
    }
    function activar(c) {
        if (c.checked) {
            document.getElementById('cite_sup').style.display = "block";
        } else {
            document.getElementById('cite_sup').style.display = "none";
        }
    }
</script>
<style type="text/css">
    .via2 {
        display: none;
    }

    .destinatario:hover {
        font-style: italic;
        font-weight: 600;
    }
</style>
<div class="col-lg-8">
    <div class="card card-underline ">
        <form class="form form-validate" action="/documento/generar/<?php echo $documento->action; ?>" method="post"
              id="frmCreate">
            <div class="card-head  ">
                <header><i class="fa fa-plus"></i> Generar <span class="text-primary"> <?php echo $tipo->tipo ?></span>
                </header>
                <div class="tools">
                    <input type="submit" class="btn btn-sm btn-primary-dark" name="submit"
                           value="Generar Documento con Hoja de Ruta"/>
                    <input type="submit" id="noHojaRuta"
                           title="Genera el documento sin hora de ruta para su posterior asignaci[on"
                           class="btn btn-sm btn-default" name="submit" value="Generar Documento sin Hoja de Ruta"/>
                    <!--<a href="" class="button" onclick="javascript:history.back(); return false;" >Cancelar</a>-->
                </div>
            </div><!--end .card-head -->

            <div class="card-body no-padding  ">
                <div class="">

                    <div class="col-lg-6 col-md-6">
                        <?php if ($user->cite_despacho): ?>
                            <div class="checkbox checkbox-styled">
                                <label>
                                    <input type="checkbox" name="cite_despacho" class="" value="1"
                                           onclick="activar(this);"> <span>Activar Generaci&oacute;n con Cite de Despacho</span>

                                </label>
                            </div>
                            <input style="display:none;" type="submit" class="button" name="submit" id="cite_sup"
                                   value="GENERAR CON CITE DIR/DESPACHO"/>
                        <?php endif; ?>
                        <div class="form-group ">
                            <?php echo Form::select('proceso', $options, '', array('class' => 'required form-control', 'title' => 'Elija un tipo de proceso por favor')); ?>
                            <br/>
                            <label for="proceso">Proceso: </label>
                        </div>
                        <?php if ($documento->tipo == 'Carta'): ?>
                            <div class="form-group">
                                <label>Titulo:</label>
                                <select name="titulo" class="form-control">
                                    <option></option>
                                    <option>Señor</option>
                                    <option>Señora</option>
                                    <option>Señores</option>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="titulo"/>
                        <?php endif; ?>
                        <div class="form-group">
                            <?php
                            echo Form::input('destinatario', '', array('id' => 'destinatario', 'class' => 'form-control', 'index' => '101'));
                            echo Form::label('destinatario', 'Nombre del destinatario:', array('index' => '100'));
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo Form::input('cargo_des', '', array('id' => 'cargo_des', 'size' => 48, 'class' => 'form-control', 'index' => '103'));
                            echo Form::label('destinatario', 'Cargo Destinatario:', array('class' => 'form', 'index' => '102'));
                            ?>
                        </div>

                        <?php if ($tipo->via == 0): ?>
                            <div class="form-group">
                                <label>Institución Destinatario</label>
                                <input type="text" size="40" class="form-control" name="institucion_des"/>
                                <input type="hidden" name="via"/>
                                <input type="hidden" name="cargovia"/>
                            </div>
                        <?php else: ?>

                            <input type="hidden" size="40" name="institucion_des"/>
                            <div class="form-group">
                                <?php
                                echo Form::input('via', '', array('id' => 'via', 'size' => 48, 'class' => 'form-control '));
                                echo Form::label('via', 'Via:', array('class' => 'form'));
                                ?>

                            </div>
                            <div class="form-group">
                                <?php
                                echo Form::input('cargovia', '', array('id' => 'cargovia', 'size' => 48, 'class' => 'form-control '));
                                echo Form::label('cargovia', 'Cargo Via:', array('class' => 'form'));
                                ?>
                                <!--<p class="help-block"><a href="javascript:;" id="otrovia" class=""><i class="fa fa-plus"></i> Agregar otro via</a></p>-->
                            </div>

                            <div class="form-group via2">
                                <?php
                                echo Form::input('via2', '', array('id' => 'via2', 'size' => 48, 'class' => 'form-control '));
                                echo Form::label('via2', 'Via:', array('class' => 'form'));
                                ?>
                            </div>
                            <div class="form-group via2">
                                <?php
                                echo Form::input('cargovia2', '', array('id' => 'cargovia2', 'size' => 48, 'class' => 'form-control required'));
                                echo Form::label('cargovia2', 'Cargo Via:', array('class' => 'form'));
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">

                            <textarea name="referencia" id="referencia" class="form-control required"
                                      title="Campo requerido"></textarea>
                            <label for="referencia">Referencia</label>
                        </div>


                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-10 col-md-9">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('remitente', $user->nombre, array('id' => 'remitente', 'size' => 35, 'class' => 'form-control required', 'readonly'));
                                    echo Form::label('remitente', 'Remitente:', array('class' => 'form', 'readonly'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('moscas', $user->mosca, array('id' => 'moscas', 'class' => 'form-control', 'readonly', 'size' => 5));
                                    echo Form::hidden('mosca', $user->mosca, array('id' => 'mosca'));
                                    echo Form::label('mosca', 'Mosca:');
                                    ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <?php
                            echo Form::input('cargo_rem', $user->cargo, array('id' => 'cargo_rem', 'size' => 48, 'class' => ' form-control', 'readonly'));
                            echo Form::label('cargo_rem', 'Cargo Remitente:', array('class' => 'form', 'readonly'));
                            ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('adjuntos', '', array('id' => 'adjuntos', 'size' => 48, 'class' => ' form-control', 'title' => 'Ejemplo: Lo citado'));
                                    echo Form::label('adjuntos', 'Adjunto:', array('class' => 'form'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-8">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('hojas', '', array('id' => 'hojas', 'class' => 'required form-control', 'title' => 'La casilla está vacía o ingrese número > 0', 'type' => 'number', 'min' => '1'));
                                    echo Form::label('hojas', 'Nro hojas:', array('class' => 'form'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <?php
                                    echo Form::input('copias', '', array('id' => 'adjuntos', 'class' => 'form-control '));
                                    echo Form::label('copias', 'Con copia a:', array('class' => 'for'));
                                    ?>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-10">
                                <?php echo Form::input('addDest', '+Agregar Destinatario', array('class' => 'btn btn-sm btn-default', 'type' => 'button', 'id' => 'addDest', 'rel' => $user->id)); ?>

                                <div id="vias">
                                    <ul>
                                        <!-- destinatarios -->
                                        <?php foreach ($destinatarios as $v) { ?>
                                            <li class="<?php echo $v['genero'] ?> "><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1 destinatario', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => '', 'cargo_via' => '')); ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="hojaruta" value="1" name="hojaruta"/>
                        <input type="hidden" id="cite_superior" value="0" name="cite_superior"/>

                        <div class="descripcion" style="width: 680px; float: left; ">
                            <?php
                            echo Form::hidden('descripcion', '', array('id' => 'descripcion'));
                            ?>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="col-lg-4 ">
    <div class="card card-underline">
        <div class="card-head  ">
            <header><i class="fa fa-fo"></i><span class="text-primary"><i class="fa fa-flash"></i> Tips: </span>
            </header>
        </div><!--end .card-head -->
        <div class="card-body">
            <p class=" opacity-50">

                <i> Puede generar un documento con hoja de ruta haciendo click en el boton azul [generar documento con
                    hoja de ruta], tambien puede generarlo sin hoja de ruta para luego elegir de entre los
                    pendientes.</i>
            </p>

            <p class=" opacity-50">
                <i>Puede escribir un destinatario de manera automatica haciendo click en el nombre del destinatario,
                    esto escribira en destinatario o via dependiendo donde se encuentre ubicado el cursor.</i>
            </p>

            <p class=" opacity-50">
                <i>Tambien puede agregar personas a la su lista de destinatarios haciendo click en el boton [ + Agregar
                    destinatario].</i>
            </p>
        </div>

    </div>
</div>


