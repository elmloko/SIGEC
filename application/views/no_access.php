<?php
/*
 * Aviso de documento no disponible.
 * Variables opcionales:
 *   $motivo: 'restringido' (por defecto) o 'inexistente'
 *   $codigo: cite del documento (solo se muestra el cite, nunca el contenido)
 */
$motivo = isset($motivo) ? $motivo : 'restringido';
$codigo = isset($codigo) ? $codigo : '';
$inexistente = $motivo === 'inexistente';
?>
<style>
    .na-card {
        max-width: 640px;
        margin: 30px auto;
        padding: 32px 30px 26px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        border-top: 4px solid var(--correos-amarillo, #FECB34);
        text-align: center;
    }
    .na-icono {
        width: 72px;
        height: 72px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #B7791F;
    }
    .na-icono.na-gris {
        background: var(--correos-fondo, #F3F5F8);
        color: #9aa4b2;
    }
    .na-card h2 {
        margin: 0 0 6px;
        font-size: 21px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .na-cite {
        display: inline-block;
        margin-bottom: 12px;
        padding: 3px 12px;
        border-radius: 14px;
        background: var(--correos-azul-suave, #EAF1F9);
        font-size: 12px;
        font-weight: 700;
        color: var(--correos-azul, #1A549A);
        word-break: break-word;
    }
    .na-card p {
        margin: 0 auto 6px;
        max-width: 500px;
        font-size: 14px;
        color: #5b6675;
    }
    .na-motivos {
        margin: 18px auto 0;
        max-width: 500px;
        padding: 14px 16px;
        border-radius: 10px;
        background: var(--correos-fondo, #F3F5F8);
        text-align: left;
        font-size: 13px;
        color: #4a5568;
    }
    .na-motivos b {
        display: block;
        margin-bottom: 6px;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .na-motivos ul {
        margin: 0;
        padding-left: 18px;
    }
    .na-motivos li {
        margin-bottom: 4px;
    }
    .na-acciones {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin-top: 22px;
    }
    .na-acciones .btn {
        margin: 0;
    }
    .na-acciones .btn .fa {
        margin-right: 4px;
    }
</style>
<div class="na-card">
    <?php if ($inexistente): ?>
        <div class="na-icono na-gris"><i class="fa fa-search"></i></div>
        <h2>Documento no encontrado</h2>
        <p>No existe un documento con ese número. Puede que el enlace esté incompleto o que el documento haya sido eliminado.</p>
    <?php elseif ($motivo === 'no_autor'): ?>
        <div class="na-icono"><i class="fa fa-lock"></i></div>
        <h2>No puede derivar esta hoja de ruta</h2>
        <?php if ($codigo != ''): ?>
            <div class="na-cite"><?php echo HTML::chars($codigo); ?></div>
        <?php endif; ?>
        <p>Todavía no fue derivada, y la primera derivación solo la puede hacer quien generó el documento.</p>
    <?php else: ?>
        <div class="na-icono"><i class="fa fa-lock"></i></div>
        <h2>Acceso restringido</h2>
        <?php if ($codigo != ''): ?>
            <div class="na-cite"><?php echo HTML::chars($codigo); ?></div>
        <?php endif; ?>
        <p>Este documento ya fue derivado y solo pueden verlo las personas que intervienen en su hoja de ruta.</p>
        <div class="na-motivos">
            <b><i class="fa fa-info-circle"></i> ¿Necesita verlo?</b>
            <ul>
                <li>Pida a quien lo tiene que se lo <b style="display:inline;margin:0">derive</b> o le envíe una copia.</li>
                <li>Si trabaja con él y no puede abrirlo, comuníquese con el área de Sistemas.</li>
            </ul>
        </div>
    <?php endif; ?>
    <div class="na-acciones">
        <a href="#" onclick="history.back(); return false;" class="btn btn-default-bright"><i class="fa fa-arrow-left"></i> Regresar</a>
        <a href="/bandeja" class="btn btn-default-bright"><i class="fa fa-inbox"></i> Mi bandeja</a>
        <a href="/search/advanced" class="btn btn-primary"><i class="fa fa-search"></i> Buscar correspondencia</a>
    </div>
</div>
