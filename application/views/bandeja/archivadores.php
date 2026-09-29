<?php
$total_archivados = 0;
foreach ($carpetas as $c) {
    $total_archivados += (int) $c->cc;
}
?>
<script type="text/javascript">
    $(function () {
        // filtro de carpetas por nombre
        $('#bj-buscar-carpeta').on('keyup', function () {
            var texto = $(this).val().toLowerCase();
            var visibles = 0;
            $('.bj-carpeta').each(function () {
                var ok = $(this).attr('data-nombre').indexOf(texto) > -1;
                $(this).toggle(ok);
                visibles += ok ? 1 : 0;
            });
            $('.bj-sin-resultados').toggle(visibles === 0);
        }).focus();
    });
</script>

<div class="bj-toolbar">
    <h3><i class="fa fa-archive"></i> Correspondencia archivada
        <span class="bj-contador"><?php echo number_format($total_archivados, 0, ',', '.'); ?></span>
    </h3>
    <?php if (count($carpetas) > 0): ?>
        <div class="bj-buscar">
            <i class="fa fa-search"></i>
            <input type="text" id="bj-buscar-carpeta" class="form-control" placeholder="Buscar carpeta..."/>
        </div>
        <div class="bj-filtros" style="font-size:12px;color:#7a8594">
            <?php echo count($carpetas); ?> carpeta<?php echo count($carpetas) == 1 ? '' : 's'; ?> &middot; haga clic en una para ver su contenido
        </div>
    <?php endif; ?>
</div>

<?php if (count($carpetas) > 0) { ?>
    <div class="bj-carpetas">
        <?php foreach ($carpetas as $c): ?>
            <a class="bj-carpeta" href="/bandeja/folder/<?php echo $c->id; ?>"
               data-nombre="<?php echo HTML::chars(mb_strtolower($c->carpeta, 'UTF-8')); ?>" title="<?php echo HTML::chars($c->carpeta); ?>">
                <div class="bj-carpeta-icono"><i class="fa fa-folder-open"></i></div>
                <div class="bj-carpeta-texto">
                    <span class="bj-carpeta-nombre"><?php echo HTML::chars($c->carpeta); ?></span>
                    <span class="bj-carpeta-cantidad"><b><?php echo number_format((int) $c->cc, 0, ',', '.'); ?></b>
                        proceso<?php echo (int) $c->cc == 1 ? '' : 's'; ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="bj-sin-resultados">Ninguna carpeta coincide con la búsqueda.</div>
<?php } else { ?>
    <div class="bj-vacio">
        <i class="fa fa-archive" style="color: var(--correos-azul, #1A549A)"></i>
        <h4>Archivo vacío</h4>
        No tiene correspondencia archivada.
    </div>
<?php } ?>
