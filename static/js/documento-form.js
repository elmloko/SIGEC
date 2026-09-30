/*
 * Formulario de documento (generar y editar): libreta de destinatarios.
 * Un clic en una persona de la libreta completa el nombre y cargo de la fila activa (A o VIA).
 * Usa las variables globales $this / $cargo que ya usaban las vistas.
 */
var $this;
var $cargo;

/*
 * La llama el modal "Agregar destinatario" (iframe) al terminar: agrega las personas a la libreta
 * sin recargar la pagina, para no perder lo que ya se escribio en el formulario.
 */
window.destinatariosAgregados = function (lista) {
    var $ul = $('#vias ul');
    if (!$ul.length) {
        window.location.reload();
        return;
    }
    var esc = function (t) {
        return $('<div>').text(t == null ? '' : String(t)).html();
    };
    var iniciales = function (nombre) {
        var p = $.trim(nombre || '').split(/\s+/).slice(0, 2);
        return $.map(p, function (x) {
            return x.charAt(0).toUpperCase();
        }).join('') || '?';
    };
    var $nuevos = $();
    $.each(lista || [], function (i, d) {
        // si ya estaba en la libreta no se repite
        if ($ul.find('a.destino1').filter(function () {
            return $(this).attr('nombre') === d.nombre;
        }).length) {
            return;
        }
        var $li = $('<li class="' + esc(d.genero) + ' gd-nuevo">' +
            '<a href="#" class="destino1 destinatario" nombre="' + esc(d.nombre) + '" cargo="' + esc(d.cargo) + '" title="' + esc(d.cargo) + '" via="" cargo_via="">' +
            '<span class="gd-avatar">' + esc(iniciales(d.nombre)) + '</span>' +
            '<span class="gd-persona"><b>' + esc(d.nombre) + '</b><small>' + esc(d.cargo) + '</small></span></a></li>');
        $ul.prepend($li);
        $nuevos = $nuevos.add($li);
    });
    $('#gd-buscar').val('').trigger('keyup');
    $('#vias').scrollTop(0);
    setTimeout(function () {
        $nuevos.removeClass('gd-nuevo');
    }, 2500);
    if (window.eModal && eModal.close) {
        eModal.close();
    }
};
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
