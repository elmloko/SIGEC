<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffc400">
    <meta name="description" content="Sistema de Gestión de Correspondencia de Correos de Bolivia">
    <link rel="shortcut icon" href="/media/images/icon.png">
    <link rel="stylesheet" href="/media/css/login.css?v=login-20260930-4">
    <title><?php echo HTML::chars($title); ?></title>
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-side" aria-label="Acceso al sistema">
            <?php echo $content; ?>
            <footer class="login-footer">
                © <?php echo date('Y'); ?> Correos de Bolivia. Todos los derechos reservados.
            </footer>
        </section>

        <section class="welcome-side" aria-label="Información del sistema">
            <svg class="mail-route" viewBox="0 0 720 150" aria-hidden="true">
                <path d="M8 112 C112 105 143 72 207 51 C270 30 298 18 341 25 C396 35 412 91 476 91 C523 91 548 55 572 30" />
                <circle cx="572" cy="30" r="8" />
                <path d="M668 63 l29 7 -3 24 -30 -7 z M668 63 l12 19 17 -12" />
            </svg>

            <div class="welcome-card">
                <h1>Sistema de Gestión<br>de Correspondencia</h1>
                <span class="welcome-rule" aria-hidden="true"></span>
                <p>Envíe y reciba su correspondencia<br>de manera más sencilla y rápida.<br>Permite generar todo tipo de<br>documentos.</p>

                <ul class="feature-list">
                    <li>
                        <span class="feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48"><path d="m6 22 35-14-13 33-6-13-16-6Z"/><path d="m22 28 19-20"/></svg>
                        </span>
                        <span>Envío y<br>recepción</span>
                    </li>
                    <li>
                        <span class="feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48"><path d="M13 5h16l9 9v29H13z"/><path d="M29 5v10h9M19 25h13M19 32h13"/></svg>
                        </span>
                        <span>Generación<br>de documentos</span>
                    </li>
                    <li>
                        <span class="feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48"><path d="M5 13a4 4 0 0 1 4-4h10l5 5h15a4 4 0 0 1 4 4v20a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4z"/><path d="M5 20h38"/></svg>
                        </span>
                        <span>Gestión<br>organizada</span>
                    </li>
                    <li>
                        <span class="feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48"><circle cx="24" cy="24" r="18"/><path d="M24 13v12l8 5"/></svg>
                        </span>
                        <span>Mayor<br>agilidad</span>
                    </li>
                </ul>
            </div>

            <img class="postman-art" src="/media/personaje.png" alt="Mensajero de Correos de Bolivia trabajando en su computadora">
        </section>
    </main>
</body>
</html>
