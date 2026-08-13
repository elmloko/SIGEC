<h2 class="subtitulo">Generar nuevo documento<br/><span>ELIJA UN TIPO DE DOCUMENTO QUE DESEA CREAR DE LISTA SIGUIENTE:</span></h2>
<ul id="nuevo">
<?php foreach($documentos as $d): ?>
    <li class="curved"> 
        <a href="/document/create/<?php echo $d->action;?>">
        <img src="/media/images/<?php echo $d->image?>"/>
        <p><?php echo $d->tipo;?><br/>
        <span><?php echo $d->descripcion;?></span></p>
        </a>
    </li>
<?php endforeach;?>
</ul>

