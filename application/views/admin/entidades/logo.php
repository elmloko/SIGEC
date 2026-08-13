<div class="row">
    <div class="col-lg-6 col-md-8">
        <div class="card card-underline ">
            <div class="card-head">
                <header><i class="fa fa-image"></i> Logo Entidad</header>
                <div class="tools">
                    <a class="btn btn-icon-toggle btn-close"><i class="md md-close"></i></a>
                </div>
            </div>
            <div class="card-body">

                <div class="col-md-8">
                    <div class="thumb">
                        <?php
                        $link = '<i class="fa fa-pencil"></i>';
                        $id = "btn_crop";
                        $title = "Editar foto";
                        $foto = 'static/logos/' . $entidad->logo;
                        if (file_exists(DOCROOT . $foto)) {
                            $id = "btn_foto";
                            $title = "Modificar";
                       
       
                        } else {
                            $foto = 'static/logos/entidad.jpg';
                            $id = "btn_foto";
                            $title = "Subir foto";
                            $link = '<i class="fa fa-camera" > </i>';
                        }
                        ?>
                        <img src="/<?php echo $foto ?>?t=<?php echo time(); ?>" height="90" width="240" alt="" class=" img-rounded" style="margin: 0">                                        
                        <h3 class="text-center col-lg-12">                                        
                            <?php echo $entidad->sigla; ?>                                       
                        </h3>                                    
                        <span> <a href="javascript:;"  title="<?php echo $title ?>"  alt="<?php echo $title ?>" id="<?php echo $id; ?>" class="col-md-12  text-center">
                                <?php echo $link; ?>
                            </a>
                            <?php if ($id == 'btn_crop') { ?>
                                <a href="javascript:;"  title="Modificar logo"  alt="Modificar foto" id="btn_foto" class="col-md-12  text-center">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            <?php } ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <p>
                        Agrege o cambie el logo de la entidad.<br> El formato permitido para tal proposito es png (recomendable 240x90).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>




<div id="modal-regular" class="modal" aria-hidden="true" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">Subir logo de la entidad</h4>
            </div>
            <div id="subir_foto">
                <form action="/admin/entidades/subirfoto" class="dropzone" id="myAwesomeDropzone">
                    <input type="hidden" name="username" id="ci" value="<?php echo $entidad->sigla; ?>"/>
                    <input type="hidden" name="idp" id="idp" value="<?php echo $entidad->id; ?>"/>

                </form> 
            </div>                
        </div>
    </div>
</div>