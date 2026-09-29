/*
 * Bandeja: filtros rapidos (Todos / Oficial / Copia / Urgente) y contador de elementos visibles.
 * Convive con el filtro de texto propio de cada vista (#FilterTextBox), que oculta con .hide().
 */
$(function () {
    var $lista = $('.bj-lista');
    if (!$lista.length) {
        return;
    }

    function actualizar() {
        var total = $lista.find('.bandeja').length;
        var visibles = $lista.find('.bandeja').filter(function () {
            return $(this).css('display') !== 'none';
        }).length;
        $('#bj-visibles').text(visibles);
        $('.bj-sin-resultados').toggle(total > 0 && visibles === 0);
    }

    $(document).on('click', '.bj-filtro', function () {
        var filtro = $(this).attr('data-filtro') || '';
        $('.bj-filtro').removeClass('activo');
        $(this).addClass('activo');
        $lista.find('.bandeja').each(function () {
            $(this).toggleClass('bj-oculto', filtro !== '' && !$(this).is(filtro));
        });
        actualizar();
    });

    $('#FilterTextBox').on('keyup', function () {
        setTimeout(actualizar, 0);
    });
    // al recibir o cancelar por AJAX la vista quita el elemento
    $(document).ajaxComplete(function () {
        setTimeout(actualizar, 400);
    });
    actualizar();
});
