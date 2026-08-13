<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="Content-Language" content="es" />
    <link rel="shortcut icon" href="<?php echo url::base().'media/images/icon.png';?>" />
    <title><?php echo $title;?></title>
    <meta name="keywords" content="<?php echo $meta_keywords;?>" />
    <meta name="description" content="<?php echo $meta_description;?>" />
    <meta name="copyright" content="<?php echo $meta_copywrite;?>" />
    <?php foreach($styles as $file => $type) { echo HTML::style($file, array('media' => $type)), "\n"; }?>
    <?php foreach($scripts as $file) { echo HTML::script($file, NULL, TRUE), "\n"; }?>
<script type="text/javascript">
$(function(){
   $("#username").focus();
   $('#error').fadeIn(5000).fadeOut(5000);
   $('#loginform').validate();
});
</script>    
</head>
 <body>
     <div class="login-logo"><h2></h2></div>
     <div id="background"></div>    
   
     <div id="informacion">
          <div class="clearfix" id="bos-main-blocks">
           <ul>               
                <li class="block">
                <div class="content">
                <h2>
                <a href="/">Recepción y Envió</a></h2><p> Permite enviar y recibir correspondencia de manera mas sencilla <br></p>
                </div>
                <a href="/">
                <img title="" alt="" src="/media/images/inbox.png" typeof="foaf:Image" /></a>
                </li>
               
                <li class="block">
                <div class="content">
                <h2>
                <a href="/">Archivo</a></h2><p>Archive su correspondencia en carpetas personales. <br></p>
                </div>
                <a href="/">
                <img title="" alt="" src="/media/images/kfm.png" typeof="foaf:Image" /></a>
                </li>
               
                <li class="block">
                <div class="content">
                <h2>
                <a href="/">Reportes</a></h2><p>Genere reportes dinamicos y personalizados. <br></p>
                </div>
                <a href="/">
                <img title="" alt="" src="/media/images/reportes.png" typeof="foaf:Image" /></a>
                </li>
            </ul>
         </div>
     </div>   
     <div id="login">
        <div class="login-panel">
    
        <form action="" method="post" accept-charset="UTF-8" id="loginform">
            <fieldset>
                <h2>Iniciar Sesión</h2>
            <div style="padding-top: 4px;">
               <label for="username" id="txt-username">Usuario:</label>
            </div>
            <div style="padding-top: 4px;">
                <input type="text" value="<?php echo Arr::get($_POST, 'username','' )?>" title="IIngrese su nombre de usuario por favor" placeholder="Usuario" class="required"  maxlength="30" name="username" id="username"/>
            </div>
            <div style="padding-top: 12px;">
               <label for="password" id="txt-password">Contraseña:</label>
            </div>
            <div style="padding-top: 4px;">
               <input type="password" class="required"  maxlength="20" autocomplete="off" title="Ingrese su contrase&ntilde;a" placeholder="Contraseña" name="password" id="password"/>
            </div>
            <div style="padding-top: 16px;  ">
               <input type="submit" style=" width: 150px;" class="login-button" id="submit" name="submit" value="Iniciar sesión"/>
            </div>            
         </fieldset>
<a href="/archivos/GuiaRapidaSIGEC.pdf" title="Gu&iacute;a R&aacute;pida SIGEC">Gu&iacute;a R&aacute;pida SIGEC - en formato pdf.</a>            
<?php if(isset($errors['login'])): ?>
            <div id="error" class="info">        
                <div id="error_login" >
                  <span><?php echo $errors['login'];?></span>
                </div>
            </div>
      <?php endif;?>
            
      </form>
        
   </div>
 </div>
     <div id="copy">
         <span class="login-copyright">
             <b><a href="mailto:imchacolla@gmail.com" title="Ivan Marcelo Chacolla M." >Copyright (c)Sistemas 2012</a></b> | <b>Ministerio de Obras Públicas Servicios y Vivienda | Estado Plurinacional de Bolivia</b>
         </span>
     </div>
</body></html>
