<?php
// aviso para paginas que se abren dentro de un modal (iframe) cuando la sesion ya no existe
?>
<style>
    html, body {
        background: #F3F5F8 !important;
    }
    .sv {
        max-width: 420px;
        margin: 60px auto;
        padding: 30px 26px;
        border-radius: 14px;
        border-top: 4px solid #FECB34;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        text-align: center;
        font-family: Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .sv > .fa {
        width: 64px;
        height: 64px;
        margin: 0 auto 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFF7DD;
        color: #B7791F;
        font-size: 28px;
    }
    .sv h2 {
        margin: 0 0 6px;
        font-size: 19px;
        font-weight: 600;
        color: #123E73;
    }
    .sv p {
        margin: 0 0 18px;
        font-size: 13.5px;
        color: #6b7686;
    }
    .sv a {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 10px;
        background: #1A549A;
        color: #fff;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
    }
    .sv a .fa {
        margin-right: 6px;
    }
    .sv a:hover {
        background: #123E73;
    }
</style>
<div class="sv">
    <i class="fa fa-clock-o"></i>
    <h2>Su sesión terminó</h2>
    <p>Por seguridad, la sesión se cierra después de un tiempo sin actividad. Vuelva a ingresar para continuar.</p>
    <a href="/login" target="_top" id="sv-ingresar"><i class="fa fa-sign-in"></i> Volver a ingresar</a>
</div>
<script>
    // el aviso se ve dentro del modal: el enlace lleva a la pagina completa y vuelve a donde estaba
    (function () {
        var a = document.getElementById('sv-ingresar');
        try {
            var t = window.top.location;
            a.href = '/login?url=' + encodeURIComponent((t.pathname + t.search).replace(/^\//, ''));
        } catch (e) {
        }
    })();
</script>
