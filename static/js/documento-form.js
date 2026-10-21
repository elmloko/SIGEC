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
    var nombres = {destinatario: 'A (destinatario)', via: 'VÍA', via2: 'VÍA 2', via3: 'VÍA 3'};

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
    // cada fila VIA (via / cargovia, via2 / cargovia2, ...)
    $('.gd-fila-via').each(function () {
        var $via = $(this).find('.gd-via'), $cargoVia = $(this).find('.gd-cargovia');
        $via.add($cargoVia).focus(function () {
            marcarDestino($via, $cargoVia);
        });
    });
    marcarDestino($('#destinatario'), $('#cargo_des'));

    // "Agregar otro VIA": muestra la siguiente fila oculta; el enlace solo queda en la ultima fila visible
    function filasVia(mostradas) {
        return $('.gd-fila-via').filter(function () {
            return (this.style.display !== 'none') === mostradas;
        });
    }
    function enlacesOtroVia() {
        var $mostradas = filasVia(true);
        $('.gd-otro-via').hide();
        if ($mostradas.length < $('.gd-fila-via').length) {
            $mostradas.last().find('.gd-otro-via').show();
        }
    }
    $('.gd-otro-via').click(function () {
        var $fila = filasVia(false).first().show();
        enlacesOtroVia();
        $fila.find('.gd-via').focus();
        return false;
    });
    enlacesOtroVia();

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

    /*
     * Autocompletado al escribir en A o VIA: sugiere personas de la libreta (por nombre o cargo)
     * y al elegir una completa el nombre y el cargo de esa fila.
     */
    var sinTildes = function (t) {
        return String(t || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    };
    var escHtml = function (t) {
        return $('<div>').text(t == null ? '' : String(t)).html();
    };
    var $sug = $('<ul class="gd-sugerencias"></ul>').hide().appendTo('body');
    var $sugCampo = null, $sugCargo = null, sugActivo = -1;

    function cerrarSugerencias() {
        $sug.hide().empty();
        sugActivo = -1;
    }

    function elegirSugerencia($li) {
        $sugCampo.val($li.data('nombre')).trigger('change');
        $sugCargo.val($li.data('cargo')).trigger('change');
        var $fila = $sugCampo.closest('.gd-fila').addClass('gd-completado');
        setTimeout(function () {
            $fila.removeClass('gd-completado');
        }, 900);
        cerrarSugerencias();
    }

    function mostrarSugerencias($campo, $campoCargo) {
        var t = sinTildes($.trim($campo.val()));
        $sugCampo = $campo;
        $sugCargo = $campoCargo;
        $sug.empty();
        sugActivo = -1;
        if (t === '') {
            cerrarSugerencias();
            return;
        }
        var vistos = {};
        $('#vias a.destino1').each(function () {
            var nombre = $(this).attr('nombre') || '', cargo = $(this).attr('cargo') || '';
            if (vistos[nombre + '|' + cargo] || $sug.children().length >= 8) {
                return;
            }
            if ((sinTildes(nombre) + ' ' + sinTildes(cargo)).indexOf(t) > -1) {
                vistos[nombre + '|' + cargo] = 1;
                $('<li><b>' + escHtml(nombre) + '</b><small>' + escHtml(cargo) + '</small></li>')
                    .data({nombre: nombre, cargo: cargo}).appendTo($sug);
            }
        });
        if (!$sug.children().length) {
            cerrarSugerencias();
            return;
        }
        var o = $campo.offset();
        $sug.css({top: o.top + $campo.outerHeight() + 2, left: o.left, width: Math.max($campo.outerWidth() * 2, 280)}).show();
    }

    $.each([['#destinatario', '#cargo_des'], ['#via', '#cargovia'], ['#via2', '#cargovia2'], ['#via3', '#cargovia3']], function (i, par) {
        var $campo = $(par[0]), $campoCargo = $(par[1]);
        if (!$campo.length) {
            return;
        }
        $campo.on('input', function () {
            mostrarSugerencias($campo, $campoCargo);
        }).on('keydown', function (e) {
            if (!$sug.is(':visible')) {
                return;
            }
            var $items = $sug.children();
            if (e.which === 40 || e.which === 38) { // flechas abajo / arriba
                sugActivo = (sugActivo + (e.which === 40 ? 1 : -1) + $items.length) % $items.length;
                $items.removeClass('activo').eq(sugActivo).addClass('activo');
                return false;
            }
            if ((e.which === 13 || e.which === 9) && sugActivo > -1) { // enter / tab
                elegirSugerencia($items.eq(sugActivo));
                return e.which === 9;
            }
            if (e.which === 13) {
                return false; // no enviar el formulario con la lista abierta
            }
            if (e.which === 27) {
                cerrarSugerencias();
            }
        }).on('blur', function () {
            setTimeout(cerrarSugerencias, 150);
        });
    });
    // mousedown para elegir antes de que el blur cierre la lista
    $sug.on('mousedown', 'li', function () {
        elegirSugerencia($(this));
        return false;
    });
    $(window).on('resize scroll', cerrarSugerencias);

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
