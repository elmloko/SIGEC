<style>
    .hrp-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 20px;
    }
    .hrp-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 14px 18px;
        border-bottom: 2px solid var(--correos-amarillo, #FECB34);
    }
    .hrp-card-head h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .hrp-card-head h3 .fa,
    .hrp-card-head h3 .md {
        color: var(--correos-azul, #1A549A);
        margin-right: 6px;
    }
    .hrp-card-body {
        padding: 16px 18px;
    }
    .hrp-ayuda {
        margin: 0 0 10px;
        font-size: 13px;
        color: #6b7686;
    }
    .hrp-card-body .select2-container {
        width: 100% !important;
    }
    .hrp-card-body .select2-container .select2-selection--single {
        height: 40px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 8px;
    }
    .hrp-card-body .select2-container .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .hrp-card-body .select2-container .select2-selection__arrow {
        height: 38px;
    }
    .hrp-opcion {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 14px 0;
        font-size: 13px;
        cursor: pointer;
    }
    .hrp-opcion input {
        width: 18px;
        height: 18px;
        margin: 0;
    }
    .hrp-opcion small {
        display: block;
        color: #8a94a3;
    }
    .hrp-error {
        display: none;
        margin: 10px 0 0;
    }
    .hrp-recientes {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .hrp-recientes li + li {
        border-top: 1px solid #EEF2F7;
    }
    .hrp-recientes a {
        display: block;
        padding: 10px 18px;
        color: inherit;
        transition: background .15s;
    }
    .hrp-recientes a:hover,
    .hrp-recientes a.activo {
        text-decoration: none;
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .hrp-recientes b {
        color: var(--correos-azul, #1A549A);
    }
    .hrp-recientes small {
        float: right;
        color: #8a94a3;
    }
    .hrp-recientes span {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: #4a5568;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .hrp-vacio {
        margin: 0;
        padding: 14px 18px;
        color: #8a94a3;
        font-size: 13px;
    }
    .hrp-visor {
        position: relative;
        height: calc(100vh - 220px);
        min-height: 420px;
        background: #525659;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
    }
    .hrp-visor iframe {
        display: none;
        width: 100%;
        height: 100%;
        border: 0;
    }
    .hrp-visor-mensaje {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px;
        text-align: center;
        color: #d7dbe0;
        background: #f3f5f8;
    }
    .hrp-visor-mensaje .md,
    .hrp-visor-mensaje .fa {
        font-size: 48px;
        color: #b7c0cc;
    }
    .hrp-visor-mensaje p {
        margin: 0;
        color: #6b7686;
    }
    .hrp-visor.cargando .hrp-visor-mensaje {
        background: #525659;
    }
    .hrp-visor.cargando .hrp-visor-mensaje p,
    .hrp-visor.cargando .hrp-visor-mensaje .fa {
        color: #fff;
    }
    .hrp-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .hrp-acciones .btn {
        margin: 0;
    }
    @media (max-width: 1199px) {
        .hrp-visor {
            height: 70vh;
        }
    }
</style>

<div class="row">
    <div class="col-lg-4">
        <div class="hrp-card">
            <div class="hrp-card-head">
                <h3><i class="fa fa-print"></i> Imprimir hoja de ruta</h3>
            </div>
            <div class="hrp-card-body">
                <p class="hrp-ayuda">Escriba al menos 2 caracteres de la hoja de ruta y elíjala de la lista, o escríbala completa.</p>
                <select id="hrp-hojaruta" class="form-control">
                    <?php if ($hr_inicial != ''): ?>
                        <option value="<?php echo HTML::chars($hr_inicial); ?>" selected><?php echo HTML::chars($hr_inicial); ?></option>
                    <?php endif; ?>
                </select>
                <label class="hrp-opcion">
                    <input type="checkbox" id="hrp-proveidos" value="1"/>
                    <span>Incluir proveídos<small>Imprime también el detalle de cada derivación</small></span>
                </label>
                <button type="button" id="hrp-ver" class="btn btn-primary btn-block"><i class="fa fa-eye"></i> Ver hoja de ruta</button>
                <div class="alert alert-danger hrp-error" id="hrp-error"></div>
            </div>
        </div>

        <div class="hrp-card">
            <div class="hrp-card-head">
                <h3><i class="fa fa-history"></i> Mis hojas de ruta recientes</h3>
            </div>
            <?php if (!$recientes): ?>
                <p class="hrp-vacio">Aún no generó hojas de ruta.</p>
            <?php else: ?>
                <ul class="hrp-recientes">
                    <?php foreach ($recientes as $r): ?>
                        <li>
                            <a href="#" data-hr="<?php echo HTML::chars($r['nur']); ?>">
                                <b><?php echo HTML::chars($r['nur']); ?></b>
                                <small><?php echo date('d/m/Y', strtotime($r['fecha'])); ?></small>
                                <span title="<?php echo HTML::chars($r['referencia']); ?>"><?php echo HTML::chars($r['referencia'] != '' ? $r['referencia'] : 'Sin referencia'); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="hrp-card">
            <div class="hrp-card-head">
                <h3><i class="md md-label"></i> <span id="hrp-titulo">Vista previa</span></h3>
                <div class="hrp-acciones" id="hrp-acciones" style="display:none">
                    <button type="button" class="btn btn-sm btn-primary" id="hrp-imprimir"><i class="fa fa-print"></i> Imprimir</button>
                    <a href="#" target="_blank" class="btn btn-sm btn-default-bright" id="hrp-pestana"><i class="fa fa-external-link"></i> Abrir en otra pestaña</a>
                    <a href="#" class="btn btn-sm btn-default-bright" id="hrp-seguimiento"><i class="md md-verified-user"></i> Seguimiento</a>
                </div>
            </div>
            <div class="hrp-visor" id="hrp-visor">
                <div class="hrp-visor-mensaje" id="hrp-mensaje">
                    <i class="md md-label"></i>
                    <p>Elija una hoja de ruta para ver cómo se imprimirá.</p>
                </div>
                <iframe id="hrp-frame" title="Vista previa de la hoja de ruta" src="about:blank"></iframe>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var actual = '';

        function urlPdf(hr) {
            return '/print/hr/?code=' + encodeURIComponent(hr) + '&p=' + ($('#hrp-proveidos').is(':checked') ? 1 : 0);
        }

        function mensaje(icono, texto, cargando) {
            $('#hrp-frame').hide();
            $('#hrp-visor').toggleClass('cargando', !!cargando);
            $('#hrp-mensaje').html('<i class="' + icono + '"></i><p></p>').show().find('p').text(texto);
        }

        function cargar(hr) {
            hr = $.trim(hr || '');
            $('#hrp-error').hide();
            if (hr === '') {
                $('#hrp-error').text('Escriba o elija una hoja de ruta.').show();
                return;
            }
            mensaje('fa fa-circle-o-notch fa-spin', 'Cargando hoja de ruta ' + hr + '...', true);
            // primero se verifica que exista
            $.ajax({type: 'POST', url: '/ajax/print_hs', data: {nur: hr}, dataType: 'json'})
                .done(function (data) {
                    if (!(data && data.nur > 0)) {
                        actual = '';
                        $('#hrp-acciones').hide();
                        $('#hrp-titulo').text('Vista previa');
                        mensaje('fa fa-exclamation-circle', 'La hoja de ruta ' + hr + ' no existe.');
                        $('#hrp-error').text('La hoja de ruta ' + hr + ' no existe. Revise el número.').show();
                        return;
                    }
                    actual = hr;
                    var url = urlPdf(hr);
                    $('#hrp-titulo').text('Hoja de ruta ' + hr);
                    $('#hrp-pestana').attr('href', url);
                    $('#hrp-seguimiento').attr('href', '/route/trace/?hr=' + encodeURIComponent(hr));
                    $('#hrp-acciones').show();
                    $('.hrp-recientes a').removeClass('activo').filter(function () {
                        return $(this).attr('data-hr') === hr;
                    }).addClass('activo');
                    $('#hrp-frame').off('load').on('load', function () {
                        $('#hrp-mensaje').hide();
                        $('#hrp-visor').removeClass('cargando');
                        $(this).show();
                    }).attr('src', url);
                    if (history.replaceState) {
                        history.replaceState(null, '', '/route/print?hr=' + encodeURIComponent(hr));
                    }
                })
                .fail(function () {
                    mensaje('fa fa-exclamation-triangle', 'No se pudo verificar la hoja de ruta. Intente nuevamente.');
                });
        }

        // buscador con autocompletado (select2 v4); tags permite escribir una hoja de ruta que no aparezca en la lista
        $('#hrp-hojaruta').select2({
            placeholder: 'Ej.: AGBC/2026-01105',
            tags: true,
            minimumInputLength: 2,
            ajax: {
                url: '/ajax/hojaruta',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {q: params.term};
                },
                processResults: function (data) {
                    return {results: data.items || []};
                }
            },
            language: {
                inputTooShort: function () {
                    return 'Escriba al menos 2 caracteres';
                },
                searching: function () {
                    return 'Buscando...';
                },
                noResults: function () {
                    return 'Sin coincidencias';
                }
            }
        }).on('select2:select', function (e) {
            cargar(e.params.data.id);
        });

        $('#hrp-ver').click(function () {
            cargar($('#hrp-hojaruta').val());
        });
        $('#hrp-proveidos').change(function () {
            if (actual) {
                cargar(actual);
            }
        });
        $('.hrp-recientes a').click(function () {
            var hr = $(this).attr('data-hr');
            if (!$('#hrp-hojaruta option').filter(function () { return this.value === hr; }).length) {
                $('#hrp-hojaruta').append(new Option(hr, hr, true, true));
            }
            $('#hrp-hojaruta').val(hr).trigger('change');
            cargar(hr);
            return false;
        });
        $('#hrp-imprimir').click(function () {
            // imprime el PDF del visor; si el navegador no lo permite, se abre en otra pestaña
            try {
                var w = document.getElementById('hrp-frame').contentWindow;
                w.focus();
                w.print();
            } catch (err) {
                window.open(urlPdf(actual), '_blank');
            }
        });

        <?php if ($hr_inicial != ''): ?>
        cargar(<?php echo json_encode($hr_inicial); ?>);
        <?php else: ?>
        setTimeout(function () {
            $('#hrp-hojaruta').select2('open');
        }, 200);
        <?php endif; ?>
    });
</script>
