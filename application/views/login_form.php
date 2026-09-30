<header class="login-brand">
    <img src="/media/logos/LOGO%2019-2-26.png" alt="Correos de Bolivia" class="brand-logo">
</header>

<div class="login-content">
    <img class="login-mascot" src="/media/monito.png" alt="Mensajero de Correos de Bolivia saludando">
    <h1 class="login-title">Iniciar sesión</h1>
    <p class="login-intro">Ingrese sus credenciales para acceder<br class="desktop-break"> al sistema.</p>

    <form class="login-form" action="" method="post" accept-charset="UTF-8" id="loginform">
        <div class="input-wrap<?php echo isset($errors['login']) ? ' input-error' : ''; ?>">
            <label class="sr-only" for="username">Correo electrónico o usuario</label>
            <svg class="input-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
            <input type="text" value="<?php echo HTML::chars(Arr::get($_POST, 'username', '')); ?>" title="Ingrese su usuario o correo electrónico" placeholder="Correo electrónico" class="login-input" maxlength="120" name="username" id="username" autocomplete="username" required<?php echo isset($errors['login']) ? ' aria-invalid="true" aria-describedby="error"' : ''; ?>>
        </div>

        <div class="input-wrap<?php echo isset($errors['login']) ? ' input-error' : ''; ?>">
            <label class="sr-only" for="password">Contraseña</label>
            <svg class="input-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg>
            <input type="password" class="login-input" maxlength="120" autocomplete="current-password" title="Ingrese su contraseña" placeholder="Contraseña" name="password" id="password" required<?php echo isset($errors['login']) ? ' aria-invalid="true" aria-describedby="error"' : ''; ?>>
            <button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.2-6 9.5-6 9.5 6 9.5 6-3.2 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.6"/><path class="eye-slash" d="m4 20 16-16"/></svg>
            </button>
        </div>

        <label class="remember-option">
            <input type="checkbox" name="remember" value="1">
            <span class="checkmark" aria-hidden="true"></span>
            <span>Recordarme</span>
        </label>

        <button class="login-submit" type="submit" name="submit" id="submit" value="1">
            <span>Iniciar sesión</span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h15M13 5l7 7-7 7"/></svg>
        </button>

        <?php if (isset($errors['login'])): ?>
            <div id="error" class="login-error" role="alert" aria-live="assertive">
                <span class="login-error-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16.5v.1"/></svg>
                </span>
                <div class="login-error-copy">
                    <strong>No se pudo iniciar sesión</strong>
                    <span><?php echo HTML::chars($errors['login']); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
    (function () {
        var username = document.getElementById('username');
        var toggle = document.querySelector('.password-toggle');
        var password = document.getElementById('password');

        if (username) username.focus();
        if (toggle && password) {
            toggle.addEventListener('click', function () {
                var show = password.type === 'password';
                password.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                toggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        }
    }());
</script>
