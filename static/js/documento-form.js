/*
 * Formulario de documento (generar y editar): libreta de destinatarios.
 * Un clic en una persona de la libreta completa el nombre y cargo de la fila activa (A o VIA).
 * Usa las variables globales $this / $cargo que ya usaban las vistas.
 */
var $this;
var $cargo;
$(function () {
    if (!$('#destinatario').length) {
        return;
    }
    var nombres = {destinatario: 'A (destinatario)', via: 'VÍA', via2: 'VÍA'};

    function marcarDestino($campo, $campoCargo) {
        $this = $campo;
        $cargo = $campoCargo;
        $('.gd-fila').removeClass('gd-objetivo');
        $campo.closest('.gd-fila').addClass('gd-objetivo');
        $('#gd-objetivo-texto').text(nombres[$campo.attr('id')] || 'A (destinatario)');
    }

    $('#destinatario, #cargo_des').focus(function () {
        marcarDestino($('#destinatario'), $('#cargo_des'));
    });
    $('#via, #cargovia').focus(function () {
        marcarDestino($('#via'), $('#cargovia'));
    });
    $('#via2').focus(function () {
        marcarDestino($(this), $('#cargovia2'));
    });
    marcarDestino($('#destinatario'), $('#cargo_des'));

    $(document).on('click', 'a.destino1', function () {
        $this.val($(this).attr('nombre')).trigger('change');
        $cargo.val($(this).attr('cargo')).trigger('change');
        var $fila = $this.closest('.gd-fila').addClass('gd-completado');
        setTimeout(function () {
            $fila.removeClass('gd-completado');
        }, 900);
        // despues de elegir el destinatario, si hay via vacia, el siguiente clic completa la via
        if ($this.attr('id') === 'destinatario' && $('#via').length && $('#via').val() === '') {
            marcarDestino($('#via'), $('#cargovia'));
        }
        return false;
    });

    // limpiar una fila (destinatario o via)
    $('.gd-limpiar').click(function () {
        $(this).closest('.gd-fila').find('input[type=text]').val('');
        return false;
    });

    // buscador de la libreta
    $('#gd-buscar').on('keyup search', function () {
        var t = $.trim($(this).val().toLowerCase());
        var visibles = 0;
        $('#vias li').each(function () {
            var ok = $(this).text().toLowerCase().indexOf(t) > -1;
            $(this).toggle(ok);
            visibles += ok ? 1 : 0;
        });
        $('#gd-sin-resultados').toggle(visibles === 0);
    });

    $('#addDest').click(function () {
        var id_user = $(this).attr('rel');
        eModal.setEModalOptions({
            loadingHtml: '<span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span><h4>Cargando usuarios...</h4>'
        });
        eModal.iframe('/content/destinos/' + id_user, 'Agregar destinatario');
    });

    // contador de la referencia
    $('#referencia').on('input', function () {
        $('#gd-ref-contador').text($(this).val().length);
    }).trigger('input');
});
