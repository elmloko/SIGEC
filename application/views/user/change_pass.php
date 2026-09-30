<?php
$dir_fotos = DOCROOT . 'static/fotos/';
$foto = file_exists($dir_fotos . $user->username . '.jpg')
    ? '/static/fotos/' . $user->username . '.jpg?v=' . filemtime($dir_fotos . $user->username . '.jpg')
    : '/static/fotos/' . ($user->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
$listo = sizeof($info) > 0;
?>
<style>
    .cp-envoltura {
        display: grid;
        grid-template-columns: minmax(0, 560px) minmax(0, 340px);
        gap: 18px;
        align-items: start;
    }
    .cp-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .cp-card .btn {
        margin: 0;
    }
    .cp-card .btn .fa {
        margin-right: 5px;
    }
    .cp-cab {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .cp-cab-icono {
        flex: 0 0 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-azul, #1A549A);
        color: #fff;
        font-size: 20px;
    }
    .cp-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .cp-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .cp-quien {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 18px 22px 0;
        padding: 10px 12px;
        border-radius: 10px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 12.5px;
        color: #6b7686;
    }
    .cp-quien img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }
    .cp-quien b {
        display: block;
        font-size: 13px;
        color: #2d3748;
    }
    .cp-cuerpo {
        padding: 18px 22px 6px;
    }
    .cp-campo {
        margin-bottom: 16px;
    }
    .cp-campo label {
        display: block;
        margin-bottom: 5px;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5568;
    }
    .cp-input {
        position: relative;
    }
    .cp-input > .fa {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9aa4b2;
    }
    .cp-input input {
        width: 100%;
        height: 44px;
        padding: 6px 44px 6px 38px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 10px;
        font-size: 15px;
        color: #2d3748;
        letter-spacing: .5px;
    }
    .cp-input input:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .12);
    }
    .cp-input input.cp-mal {
        border-color: #D32F2F;
    }
    .cp-input input.cp-bien {
        border-color: #2E9E5B;
    }
    .cp-ver {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #8a94a3;
        cursor: pointer;
    }
    .cp-ver:hover {
        background: var(--correos-fondo, #F3F5F8);
        color: var(--correos-azul, #1A549A);
    }
    .cp-ayuda {
        display: block;
        margin-top: 5px;
        min-height: 16px;
        font-size: 12px;
        color: #8a94a3;
    }
    .cp-ayuda.mal {
        color: #D32F2F;
    }
    .cp-ayuda.bien {
        color: #227547;
    }
    .cp-mayus {
        display: none;
        margin-top: 5px;
        font-size: 12px;
        color: #B7791F;
    }
    /* medidor de seguridad */
    .cp-medidor {
        display: flex;
        gap: 5px;
        margin-top: 8px;
    }
    .cp-medidor span {
        flex: 1 1 0;
        height: 5px;
        border-radius: 3px;
        background: #EEF2F7;
        transition: background .2s;
    }
    .cp-nivel {
        margin-top: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #8a94a3;
    }
    .cp-pie {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 22px;
        border-top: 1px solid #EEF2F7;
        background: #FAFBFC;
        border-radius: 0 0 14px 14px;
    }
    .cp-pie .cp-nota {
        margin-right: auto;
        font-size: 12px;
        color: #8a94a3;
    }
    .cp-alerta {
        margin: 18px 22px 0;
        padding: 10px 14px;
        border-radius: 10px;
        background: #FDE8E8;
        color: #B42318;
        font-size: 13px;
    }
    /* requisitos */
    .cp-requisitos {
        padding: 18px 20px;
    }
    .cp-requisitos h3 {
        margin: 0 0 12px;
        font-size: 15px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .cp-requisitos ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .cp-requisitos li {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 6px 0;
        font-size: 13px;
        color: #6b7686;
    }
    .cp-requisitos li .fa {
        flex: 0 0 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EEF2F7;
        color: #b7c0cc;
        font-size: 10px;
    }
    .cp-requisitos li.ok {
        color: #227547;
    }
    .cp-requisitos li.ok .fa {
        background: #2E9E5B;
        color: #fff;
    }
    .cp-requisitos li.opcional {
        font-style: italic;
    }
    .cp-consejos {
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 10px;
        background: var(--correos-amarillo-suave, #FFF7DD);
        font-size: 12.5px;
        color: #6b5a1e;
    }
    .cp-consejos b {
        display: block;
        margin-bottom: 4px;
        color: #8a6100;
    }
    /* listo */
    .cp-listo {
        padding: 36px 26px 30px;
        text-align: center;
    }
    .cp-listo .fa-check {
        width: 70px;
        height: 70px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #E6F4EC;
        color: #2E9E5B;
        font-size: 30px;
    }
    .cp-listo h2 {
        margin: 0 0 6px;
        font-size: 21px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .cp-listo p {
        margin: 0 auto 20px;
        max-width: 380px;
        font-size: 13.5px;
        color: #6b7686;
    }
    .cp-listo .cp-botones {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
    }
    @media (max-width: 991px) {
        .cp-envoltura {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="col-lg-12">
    <?php if ($listo): ?>
        <div class="cp-card" style="max-width:560px">
            <div class="cp-listo">
                <i class="fa fa-check"></i>
                <h2>Contraseña cambiada</h2>
                <p>Desde ahora ingrese al sistema con su contraseña nueva. Guárdela en un lugar seguro y no la comparta.</p>
                <div class="cp-botones">
                    <a href="/user/profile" class="btn btn-default-bright"><i class="fa fa-user"></i> Volver a mi perfil</a>
                    <a href="/" class="btn btn-primary"><i class="fa fa-home"></i> Ir al inicio</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="cp-envoltura">
            <form method="post" action="/user/pass" class="cp-card" id="form-pass" autocomplete="off" novalidate>
                <div class="cp-cab">
                    <div class="cp-cab-icono"><i class="fa fa-lock"></i></div>
                    <div>
                        <h2>Cambiar contraseña</h2>
                        <p>Por seguridad, primero confirme su contraseña actual.</p>
                    </div>
                </div>

                <div class="cp-quien">
                    <img src="<?php echo HTML::chars($foto); ?>" alt=""/>
                    <div><b><?php echo HTML::chars($user->nombre); ?></b>Usuario: <?php echo HTML::chars($user->username); ?></div>
                </div>

                <?php if (sizeof($errors) > 0): ?>
                    <div class="cp-alerta"><i class="fa fa-exclamation-triangle"></i>
                        <?php foreach ($errors as $e): ?><?php echo HTML::chars($e); ?> <?php endforeach; ?></div>
                <?php endif; ?>

                <div class="cp-cuerpo">
                    <div class="cp-campo">
                        <label for="pass_old">Contraseña actual</label>
                        <div class="cp-input">
                            <i class="fa fa-key"></i>
                            <input type="password" name="pass_old" id="pass_old" autocomplete="current-password" required/>
                            <button type="button" class="cp-ver" data-para="pass_old" title="Mostrar / ocultar"><i class="fa fa-eye"></i></button>
                        </div>
                        <span class="cp-mayus"><i class="fa fa-arrow-up"></i> Tiene activadas las MAYÚSCULAS</span>
                    </div>

                    <div class="cp-campo">
                        <label for="pass1">Contraseña nueva</label>
                        <div class="cp-input">
                            <i class="fa fa-lock"></i>
                            <input type="password" name="pass1" id="pass1" autocomplete="new-password" required/>
                            <button type="button" class="cp-ver" data-para="pass1" title="Mostrar / ocultar"><i class="fa fa-eye"></i></button>
                        </div>
                        <div class="cp-medidor" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
                        <div class="cp-nivel" id="cp-nivel">&nbsp;</div>
                        <span class="cp-mayus"><i class="fa fa-arrow-up"></i> Tiene activadas las MAYÚSCULAS</span>
                    </div>

                    <div class="cp-campo">
                        <label for="pass2">Repita la contraseña nueva</label>
                        <div class="cp-input">
                            <i class="fa fa-lock"></i>
                            <input type="password" name="pass2" id="pass2" autocomplete="new-password" required/>
                            <button type="button" class="cp-ver" data-para="pass2" title="Mostrar / ocultar"><i class="fa fa-eye"></i></button>
                        </div>
                        <span class="cp-ayuda" id="cp-coinciden"></span>
                    </div>
                </div>

                <div class="cp-pie">
                    <span class="cp-nota">Mínimo 6 caracteres.</span>
                    <a href="/user/profile" class="btn btn-default-bright">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="cp-guardar" disabled><i class="fa fa-check"></i> Cambiar contraseña</button>
                </div>
            </form>

            <div class="cp-card cp-requisitos">
                <h3><i class="fa fa-shield" style="color:#1A549A"></i> Su contraseña nueva</h3>
                <ul>
                    <li id="req-largo"><i class="fa fa-check"></i> Tiene al menos 6 caracteres</li>
                    <li id="req-distinta"><i class="fa fa-check"></i> Es distinta de la actual</li>
                    <li id="req-igual"><i class="fa fa-check"></i> Las dos contraseñas nuevas coinciden</li>
                    <li id="req-letras" class="opcional"><i class="fa fa-check"></i> Combina letras y números (recomendado)</li>
                    <li id="req-8" class="opcional"><i class="fa fa-check"></i> 8 caracteres o más (recomendado)</li>
                </ul>
                <div class="cp-consejos">
                    <b><i class="fa fa-lightbulb-o"></i> Consejos</b>
                    Evite su nombre, su usuario, su CI o fechas. Una frase corta con números es fácil de recordar y difícil de adivinar, por ejemplo <i>Correo$Bolivia26</i>.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (!$listo): ?>
    <script type="text/javascript">
        $(function () {
            var $act = $('#pass_old'), $p1 = $('#pass1'), $p2 = $('#pass2');
            var colores = ['#D32F2F', '#F59E0B', '#84CC16', '#2E9E5B'];
            var nombres = ['Muy débil', 'Débil', 'Aceptable', 'Segura'];
            $act.focus();

            // mostrar / ocultar
            $('.cp-ver').on('click', function () {
                var $i = $('#' + $(this).data('para'));
                var ver = $i.attr('type') === 'password';
                $i.attr('type', ver ? 'text' : 'password');
                $(this).find('.fa').toggleClass('fa-eye', !ver).toggleClass('fa-eye-slash', ver);
                $i.focus();
            });

            // aviso de mayusculas activadas
            $('#form-pass input').on('keyup keydown', function (e) {
                if (e.originalEvent && e.originalEvent.getModifierState) {
                    $(this).closest('.cp-campo').find('.cp-mayus').toggle(e.originalEvent.getModifierState('CapsLock'));
                }
            });

            function puntaje(p) {
                var s = 0;
                if (p.length >= 6) s++;
                if (p.length >= 10) s++;
                if (/[a-z]/i.test(p) && /\d/.test(p)) s++;
                if (/[A-Z]/.test(p) && /[a-z]/.test(p) || /[^a-z0-9]/i.test(p)) s++;
                return p.length < 6 ? Math.min(s, 1) : s;
            }
            function marcar(id, ok) {
                $('#' + id).toggleClass('ok', !!ok);
            }
            function revisar() {
                var a = $act.val(), n = $p1.val(), r = $p2.val();
                var largo = n.length >= 6, distinta = n !== '' && n !== a, igual = r !== '' && n === r;
                marcar('req-largo', largo);
                marcar('req-distinta', distinta);
                marcar('req-igual', igual);
                marcar('req-letras', /[a-z]/i.test(n) && /\d/.test(n));
                marcar('req-8', n.length >= 8);

                // medidor
                var p = n ? puntaje(n) : 0;
                $('.cp-medidor span').each(function (i) {
                    $(this).css('background', n && i < Math.max(p, 1) ? colores[Math.max(p, 1) - 1] : '#EEF2F7');
                });
                $('#cp-nivel').text(n ? nombres[Math.max(p, 1) - 1] : ' ').css('color', n ? colores[Math.max(p, 1) - 1] : '');

                // coinciden
                var $c = $('#cp-coinciden').removeClass('mal bien');
                $p2.removeClass('cp-mal cp-bien');
                if (r !== '') {
                    if (igual) {
                        $c.addClass('bien').html('<i class="fa fa-check"></i> Coinciden');
                        $p2.addClass('cp-bien');
                    } else {
                        $c.addClass('mal').html('<i class="fa fa-times"></i> No coinciden');
                        $p2.addClass('cp-mal');
                    }
                } else {
                    $c.text('');
                }
                $('#cp-guardar').prop('disabled', !(a !== '' && largo && distinta && igual));
            }
            $('#form-pass input').on('input keyup', revisar);
            $('#form-pass').on('submit', function () {
                revisar();
                if ($('#cp-guardar').prop('disabled')) {
                    return false;
                }
                $('#cp-guardar').prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando…');
            });
        });
    </script>
<?php endif; ?>
