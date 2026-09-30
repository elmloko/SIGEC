/**
 * Ajusta el modal de eModal al contenido de la pagina que se abre dentro (iframe).
 *
 * eModal siempre crea el iframe con height:75vh y el dialogo en tamaño extra grande, asi que
 * una pagina corta queda con mucho espacio vacio debajo. Esto lo llama la propia pagina hija:
 * mide su contenido y le da ese alto al iframe, sin pasar del alto de la ventana.
 *
 * Uso desde la pagina que se abre en el modal:  ajustarModal();  (y de nuevo si cambia el alto)
 */
(function (window) {
    'use strict';

    function miIframe() {
        // el iframe del padre cuya ventana es esta misma pagina
        var marcos = window.parent.document.getElementsByTagName('iframe');
        for (var i = 0; i < marcos.length; i++) {
            try {
                if (marcos[i].contentWindow === window) {
                    return marcos[i];
                }
            } catch (e) {
                // otro origen: no es el nuestro
            }
        }
        return null;
    }

    var ultimas = {};

    function ajustarModal(opciones) {
        // se recuerdan las opciones: los reajustes automaticos (resize) no traen ninguna
        opciones = opciones || ultimas;
        ultimas = opciones;
        var anchoMaximo = opciones.ancho || 920;   // px
        var minimo = opciones.minimo || 200;       // px
        try {
            if (window.parent === window) {
                return; // no esta dentro de un modal
            }
            var marco = miIframe();
            if (!marco) {
                return;
            }
            var doc = document.documentElement, cuerpo = document.body;
            // el alto del iframe se pone a cero antes de medir: si no, el tema tiene elementos
            // al 100% y el contenido "mide" siempre lo mismo que el propio iframe
            var previo = marco.style.height;
            marco.style.height = '0px';
            // se mide el documento, no el body: si la pagina tiene una lista con su propio
            // desplazamiento, body.scrollHeight devuelve el contenido entero y no lo que se ve
            var alto = Math.max(doc.scrollHeight, doc.offsetHeight, cuerpo.offsetHeight);
            if (!alto) {
                marco.style.height = previo;
                return;
            }
            // el modal no puede pasar del alto de la ventana (dejando sitio para cabecera y margenes)
            var tope = Math.max(minimo, window.parent.innerHeight - 140);
            // unos pixeles de holgura para que no salga la barra de desplazamiento por redondeos
            marco.style.height = Math.min(Math.max(alto + 4, minimo), tope) + 'px';

            var dialogo = marco.closest ? marco.closest('.modal-dialog') : null;
            if (!dialogo && marco.parentNode) {
                dialogo = window.parent.jQuery ? window.parent.jQuery(marco).closest('.modal-dialog')[0] : null;
            }
            if (dialogo) {
                dialogo.style.maxWidth = anchoMaximo + 'px';
                dialogo.style.width = 'auto';
            }
        } catch (e) {
            // si el navegador no deja tocar el padre, el modal se queda como estaba
        }
    }

    window.ajustarModal = ajustarModal;

    // se reajusta al cambiar el tamaño de la ventana
    if (window.addEventListener) {
        window.addEventListener('resize', function () {
            ajustarModal();
        });
    }
})(window);
