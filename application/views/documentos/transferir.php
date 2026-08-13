<fieldset>
<h2><b>Referencia: </b> 
    <?php echo $documento->referencia;?>
</h2>
    <br/>
<form action="" method="post" > 
<?php
echo Form::label('usuario','Usuario:');
echo Form::select('destino',$destino);
echo '<br/>';
echo '<br/>';

echo Form::submit('submit', 'Transferir Documento', array('class'=>'button2'));
?>
    <br/>
    <br/>
</form>
</fieldset>