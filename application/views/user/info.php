<ul class="info">    
    <li><b>Oficina: </b><?php echo $oficina; ?></li>
    <li><b>Nombre: </b><?php echo HTML::chars($user->nombre); ?></li>
    <li><b>Cargo: </b><?php echo HTML::chars($user->cargo); ?></li>    
    <li><b>Email: </b><?php echo HTML::chars($user->email); ?></li>
    <li><b>Hora de Ingreso: </b><?php echo Date::fuzzy_span($user->last_login); ?>
    <?php $fecha=Date::span($user->last_login); 
        echo '(<b>'. date('H:i:s d-m-Y',$user->last_login).'</b>)';
    ?></li>
    <li>Numero de ingresos al sistema: <?php echo $user->logins; ?></li>   
</ul>

