<?php
/*
 * Visor de PDF en modal (bootstrap), reutilizable.
 * Uso: echo View::factory('documentos/visor_pdf'); y en cada enlace:
 *   <a href="#" class="visor-pdf" data-url="/download/?file=ID&ver=1" data-descargar="/download/?file=ID" data-nombre="archivo.pdf">
 * Al cerrar (X, Esc o clic fuera) se vacia el visor.
 */
?>
<style>
    .visor-pdf-modal .modal-dialog { width: 94%; max-width: 1200px; margin: 20px auto; }
    .visor-pdf-modal .modal-content { border-radius: 8px; overflow: hidden; }
    .visor-pdf-modal .modal-header { background: var(--correos-azul, #1A549A); border-bottom: 3px solid var(--correos-amarillo, #FECB34); color: #fff; padding: 10px 16px; }
    .visor-pdf-modal .modal-title { color: #fff; font-size: 15px; word-break: break-word; padding-right: 40px; }
    .visor-pdf-modal .close { color: #fff; opacity: .9; font-size: 30px; text-shadow: none; }
    .visor-pdf-modal .close:hover { color: var(--correos-amarillo, #FECB34); opacity: 1; }
    .visor-pdf-modal .modal-body { position: relative; padding: 0; background: #525659; }
    .visor-pdf-modal iframe { display: block; width: 100%; height: calc(100vh - 140px); min-height: 300px; border: 0; }
    .visor-pdf-cargando { position: absolute; top: 40%; left: 0; right: 0; text-align: center; color: #fff; }
    @media (max-width: 767px) {
        .visor-pdf-modal .modal-dialog { width: auto; margin: 8px; }
    }
</style>
<div class="modal fade visor-pdf-modal" id="visor-pdf" tabindex="-1" role="dialog" aria-labelledby="visor-pdf-titulo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" title="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> <span id="visor-pdf-titulo"></span></h4>
                <div style="margin-top:6px">
                    <a href="#" id="visor-pdf-descargar" class="btn btn-xs btn-default-bright"><i class="fa fa-download"></i> Descargar</a>
                    <a href="#" id="visor-pdf-pestana" target="_blank" class="btn btn-xs btn-default-bright"><i class="fa fa-external-link"></i> Abrir en otra pestaña</a>
                </div>
            </div>
            <div class="modal-body">
                <div class="visor-pdf-cargando" id="visor-pdf-cargando"><i class="fa fa-circle-o-notch fa-spin"></i> Cargando documento...</div>
                <iframe id="visor-pdf-frame" src="about:blank" title="Vista previa del archivo"
                        onload="document.getElementById('visor-pdf-cargando').style.display = 'none';"></iframe>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // registrado sobre document y fuera de $(function) para que funcione aunque otro script de la pagina falle
    $(document).on('click', '.visor-pdf', function (e) {
        e.preventDefault();
        var url = $(this).attr('data-url');
        var $visor = $('#visor-pdf');
        if (!$.fn.modal || !$visor.length) {
            window.open(url, '_blank');
            return false;
        }
        if (!$visor.parent().is('body')) {
            $visor.appendTo('body');
        }
        $('#visor-pdf-titulo').text($(this).attr('data-nombre') || '');
        $('#visor-pdf-descargar').attr('href', $(this).attr('data-descargar'));
        $('#visor-pdf-pestana').attr('href', url);
        $('#visor-pdf-cargando').show();
        $('#visor-pdf-frame').attr('src', url);
        $visor.modal('show');
        return false;
    });
    $(document).on('hidden.bs.modal', '#visor-pdf', function () {
        $('#visor-pdf-frame').attr('src', 'about:blank');
    });
</script>
