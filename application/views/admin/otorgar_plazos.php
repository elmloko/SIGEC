<?php
$h = function ($s) {
    return HTML::chars($s);
};
$foto = file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg') ? '/static/fotos/' . $user->username . '.jpg' : '/static/fotos/' . ($user->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
$datos = array();
foreach ($personas as $p) {
    $datos[] = array(
        'id' => (int) $p['id'],
        'n' => trim($p['nombre']),
        'c' => trim($p['cargo']),
        'o' => trim($p['oficina']),
        'h' => (int) $p['habilitado'],
        's' => isset($elegidos[(int) $p['id']]) ? 1 : 0,
    );
}
$total = count($elegidos);
?>
<style>
    body {
        background: #F3F5F8;
    }
    .pz {
        max-width: 780px;
        margin: 0 auto;
        padding: 14px 16px 84px;
        font-family: Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .pz-quien {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
        border-radius: 12px;
        border-top: 3px solid #FECB34;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .pz-quien img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E8EFF8;
    }
    .pz-quien b {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #123E73;
    }
    .pz-quien span {
        display: block;
        font-size: 12.5px;
        color: #6b7686;
    }
    .pz-ayuda {
        margin: 0 2px 12px;
        font-size: 13px;
        color: #5b6574;
    }
    /* buscador y contador */
    .pz-barra {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .pz-buscar {
        position: relative;
        flex: 1 1 240px;
    }
    .pz-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        margin-top: -7px;
        color: #9aa4b2;
        font-size: 13px;
    }
    .pz-buscar input {
        width: 100%;
        height: 36px;
        padding: 6px 11px 6px 31px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13.5px;
        outline: none;
    }
    .pz-buscar input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .pz-barra button {
        height: 36px;
        padding: 0 12px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A549A;
    }
    .pz-barra button:hover {
        background: #EEF3FA;
    }
    .pz-cuenta {
        margin: 0 2px 8px;
        font-size: 12.5px;
        color: #6b7686;
    }
    .pz-cuenta b {
        color: #123E73;
    }
    /* lista */
    .pz-lista {
        max-height: 330px;
        overflow: auto;
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        background: #fff;
    }
    .pz-fila {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        padding: 9px 12px;
        border-bottom: 1px solid #F1F4F8;
        cursor: pointer;
        font-weight: 400;
    }
    .pz-fila:last-child {
        border-bottom: 0;
    }
    .pz-fila:hover {
        background: #F7FAFD;
    }
    .pz-fila input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .pz-marca {
        flex: 0 0 20px;
        width: 20px;
        height: 20px;
        border: 2px solid #C4CEDB;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: #fff;
        font-size: 11px;
    }
    .pz-fila input:checked ~ .pz-marca {
        border-color: #1A549A;
        background: #1A549A;
    }
    .pz-marca .fa {
        opacity: 0;
    }
    .pz-fila input:checked ~ .pz-marca .fa {
        opacity: 1;
    }
    .pz-fila input:checked ~ .pz-texto b {
        color: #123E73;
    }
    .pz-texto {
        flex: 1 1 auto;
        min-width: 0;
    }
    .pz-texto b {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #1f2937;
    }
    .pz-texto small {
        display: block;
        font-size: 11.5px;
        color: #8a94a3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pz-baja {
        flex: 0 0 auto;
        padding: 1px 8px;
        border-radius: 20px;
        background: #FDECEC;
        color: #B42318;
        font-size: 10.5px;
        font-weight: 700;
    }
    .pz-vacio {
        padding: 26px 14px;
        text-align: center;
        font-size: 13px;
        color: #8a94a3;
    }
    mark {
        padding: 0;
        background: #FFF3C4;
        color: inherit;
    }
    /* pie */
    .pz-pie {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        border-top: 1px solid #E3E8EF;
        background: rgba(255, 255, 255, .97);
    }
    .pz-estado {
        flex: 1 1 180px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .pz-estado.cambios {
        color: #B7791F;
        font-weight: 600;
    }
    .pz-estado.error {
        color: #B42318;
        font-weight: 600;
    }
    .pz-pie .btn {
        margin: 0;
    }
    .pz-pie .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        padding: 7px 22px;
        font-weight: 700;
    }
</style>

<div class="pz">
    <div class="pz-quien">
        <img src="<?php echo $foto; ?>" alt="">
        <span style="flex:1 1 auto;min-width:0">
            <b><?php echo $h($user->nombre); ?></b>
            <span><?php echo $h($user->cargo); ?><?php echo $oficina ? ' · ' . $h($oficina) : ''; ?></span>
        </span>
    </div>

    <p class="pz-ayuda">Marque a quiénes esta persona podrá <b>fijarles un plazo de respuesta</b> cuando les derive un documento.</p>

    <div class="pz-barra">
        <span class="pz-buscar">
            <i class="fa fa-search"></i>
            <input type="search" id="pz-q" placeholder="Buscar por nombre, cargo u oficina…" autocomplete="off">
        </span>
        <button type="button" id="pz-solo"><i class="fa fa-check-square-o"></i> Ver solo marcados</button>
        <button type="button" id="pz-limpiar"><i class="fa fa-eraser"></i> Quitar todos</button>
    </div>

    <p class="pz-cuenta"><b id="pz-n"><?php echo $total; ?></b> seleccionados · <span id="pz-vistos"><?php echo count($datos); ?></span> personas en la lista</p>

    <div class="pz-lista" id="pz-lista"></div>
</div>

<div class="pz-pie">
    <span class="pz-estado" id="pz-estado">Sin cambios</span>
    <a href="javascript:;" class="btn btn-default-bright" id="pz-cerrar">Cerrar</a>
    <a href="javascript:;" class="btn btn-primary" id="aceptar"><i class="fa fa-check"></i> Guardar</a>
</div>

<script src="/static/js/modal-alto.js"></script>
<script>
    $(function () {
        var ID = <?php echo (int) $user->id; ?>;
        var PERSONAS = <?php echo json_encode($datos); ?>;
        var marcados = {};
        PERSONAS.forEach(function (p) {
            if (p.s) {
                marcados[p.id] = true;
            }
        });
        var original = Object.keys(marcados).sort().join(',');
        var soloMarcados = false;

        function normal(t) {
            return (t || '').toString().toLowerCase()
                    .replace(/[áàä]/g, 'a').replace(/[éèë]/g, 'e').replace(/[íìï]/g, 'i')
                    .replace(/[óòö]/g, 'o').replace(/[úùü]/g, 'u').replace(/ñ/g, 'n');
        }
        function esc(s) {
            return $('<div>').text(s == null ? '' : s).html();
        }
        function resaltar(txt, palabras) {
            var r = esc(txt);
            if (!palabras.length) {
                return r;
            }
            var plano = normal(r);
            var trozos = [], usados = [];
            palabras.forEach(function (p) {
                var i = plano.indexOf(p);
                while (i > -1) {
                    usados.push([i, i + p.length]);
                    i = plano.indexOf(p, i + p.length);
                }
            });
            if (!usados.length) {
                return r;
            }
            usados.sort(function (a, b) {
                return a[0] - b[0];
            });
            var fin = 0, salida = '';
            usados.forEach(function (u) {
                if (u[0] < fin) {
                    return;
                }
                salida += r.slice(fin, u[0]) + '<mark>' + r.slice(u[0], u[1]) + '</mark>';
                fin = u[1];
            });
            return salida + r.slice(fin);
        }

        function seleccion() {
            return Object.keys(marcados).filter(function (k) {
                return marcados[k];
            });
        }
        function estado(txt, clase) {
            $('#pz-estado').removeClass('cambios error').addClass(clase || '').text(txt);
        }

        function pintar() {
            var palabras = normal($('#pz-q').val()).split(/\s+/).filter(Boolean);
            var lista = PERSONAS.filter(function (p) {
                if (soloMarcados && !marcados[p.id]) {
                    return false;
                }
                if (!palabras.length) {
                    return true;
                }
                var texto = normal(p.n + ' ' + p.c + ' ' + p.o);
                return palabras.every(function (w) {
                    return texto.indexOf(w) > -1;
                });
            });
            var html = '';
            lista.slice(0, 300).forEach(function (p) {
                html += '<label class="pz-fila">' +
                        '<input type="checkbox" value="' + p.id + '"' + (marcados[p.id] ? ' checked' : '') + '>' +
                        '<span class="pz-marca"><i class="fa fa-check"></i></span>' +
                        '<span class="pz-texto"><b>' + resaltar(p.n, palabras) + '</b>' +
                        '<small>' + resaltar(p.c, palabras) + (p.o ? ' · ' + resaltar(p.o, palabras) : '') + '</small></span>' +
                        (p.h ? '' : '<span class="pz-baja">De baja</span>') +
                        '</label>';
            });
            if (!lista.length) {
                html = '<div class="pz-vacio">' + (soloMarcados ? 'Todavía no marcó a nadie.' : 'Nadie coincide con la búsqueda.') + '</div>';
            } else if (lista.length > 300) {
                html += '<div class="pz-vacio">Hay ' + (lista.length - 300) + ' personas más: use el buscador para encontrarlas.</div>';
            }
            $('#pz-lista').html(html);
            $('#pz-vistos').text(lista.length);
            var sel = seleccion();
            $('#pz-n').text(sel.length);
            var cambio = sel.sort().join(',') !== original;
            estado(cambio ? 'Hay cambios sin guardar' : 'Sin cambios', cambio ? 'cambios' : '');
            if (window.ajustarModal) {
                window.ajustarModal();
            }
        }

        $('#pz-lista').on('change', 'input[type=checkbox]', function () {
            var id = parseInt(this.value, 10);
            if (this.checked) {
                marcados[id] = true;
            } else {
                delete marcados[id];
            }
            var sel = seleccion();
            $('#pz-n').text(sel.length);
            var cambio = sel.sort().join(',') !== original;
            estado(cambio ? 'Hay cambios sin guardar' : 'Sin cambios', cambio ? 'cambios' : '');
            if (soloMarcados) {
                pintar();
            }
        });
        var temporizador = null;
        $('#pz-q').on('input search', function () {
            clearTimeout(temporizador);
            temporizador = setTimeout(pintar, 150);
        });
        $('#pz-solo').on('click', function () {
            soloMarcados = !soloMarcados;
            $(this).html(soloMarcados ? '<i class="fa fa-list"></i> Ver todos' : '<i class="fa fa-check-square-o"></i> Ver solo marcados');
            pintar();
        });
        $('#pz-limpiar').on('click', function () {
            if (!seleccion().length) {
                return;
            }
            if (!confirm('¿Quitar a todas las personas marcadas?')) {
                return;
            }
            marcados = {};
            pintar();
        });

        function cerrar() {
            try {
                parent.eModal.close();
            } catch (e) {
                window.close();
            }
        }
        $('#pz-cerrar').on('click', function () {
            if (seleccion().sort().join(',') !== original && !confirm('Hay cambios sin guardar. ¿Cerrar de todos modos?')) {
                return;
            }
            cerrar();
        });

        $('#aceptar').on('click', function () {
            var $b = $(this);
            if ($b.prop('disabled')) {
                return;
            }
            var sel = seleccion();
            $b.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando…');
            estado('Guardando…');
            $.ajax({
                type: 'POST',
                url: '/admin/ajax/otorgarPlazosDeRespuestaAUsuarios',
                dataType: 'json',
                data: {id_usuarios_que_reciben_plazos: sel, id_user: ID}
            }).done(function (r) {
                if (r && r.ok) {
                    original = sel.sort().join(',');
                    estado('Guardado');
                    cerrar();
                } else {
                    estado((r && r.msg) || 'No se pudo guardar.', 'error');
                    $b.prop('disabled', false).html('<i class="fa fa-check"></i> Guardar');
                }
            }).fail(function () {
                estado('No se pudo guardar: revise su conexión.', 'error');
                $b.prop('disabled', false).html('<i class="fa fa-check"></i> Guardar');
            });
        });

        pintar();
        $('#pz-q').focus();
        $(window).on('load', function () {
            if (window.ajustarModal) {
                window.ajustarModal();
            }
        });
    });
</script>
