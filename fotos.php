<? session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<? 
    include ('include/meta.php'); 
	include ("include/functions.php");
?>

<title>Fotos</title>

<!-- Google Analytics 20140420 -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-8447274-3', 'ismaeljb.net');
  ga('send', 'pageview');

</script>

</head>
<body>

<?
/*
$heros = scandir ("img/hero");
array_shift ($heros);
array_shift ($heros);
$clave_aleatoria = array_rand($heros);
$hero = $heros[$clave_aleatoria];
*/
?>


<div id="wrapper">
<? include ("include/menu.php"); ?>

<?php

// FUNCIONALIDADES DEL SCRIPT:

// fotos.php
// 1. Lista todos los directorios de la carpeta actual cuyo formato sea Nombre_Mes_AAAA 
//    y estén dentro de las variables $meses y $anyos y los muestra
// 2. Lista los autores de cada album 
// 3. Cuando el usuario elige un album se llama a album.php pasándole el nombre del álbum
//    (que coincide con el de la carpeta) y el del autor en caso de que haya

// album.php
// Espera que todas las fotos hayan sido reducidas previamente a 640x480
// 1. Si se ha elegido autor se listan las fotos del álbum de ese autor.
//    Si no hay autor se listarán todas las que haya en el álbum.
// 2. Crea los subdirectorios xmls y thumbnails.
// 3. Crea los thumbnails y guarda los originales en /originales
// 4. Comprime todas las fotos del album en un .zip
// 5. Las estadisticas de visitas y comentarios se sacan del XML de cada foto
// 6. Si se elige foto llama a foto.php pasándole el nombre de la foto

// foto.php
// 3. Muestra la foto elegida en tamaño reducido con toda su información.
//    Inserta la foto en la BD la primera vez que ésta es visitada

// Estas variables hay que tenerlas en cuenta a la hora de poner el nombre a las carpetas
$meses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');

$albumes = array();

 // Inicio de sesion
require_once ('include/conexion.php');
if ($_POST['pass'])
{
	$ssql = "SELECT * FROM admin
			 WHERE id_admin > '1'";

	$result = mysql_query($ssql, $conexion);
	
	while ($admin = mysql_fetch_object($result))
	{
		if ($_POST['pass'] == $admin->pass)
		{
			$_SESSION['user_name'] = $admin->alias;
		}
	}

	if (isset($_SESSION['user_name']))
	{
		$mensaje = "La password es correcta";
		$color = "#7BBF76";
		
		$timestamp = date ('[H:i:s] [d/m/Y]');
		$log_line = "$timestamp - ".$_SESSION['user_name']." acaba de iniciar sesion\n";
		file_put_contents ("log.txt", $log_line, FILE_APPEND);
	}
	else 
	{
		$mensaje = "Esa no es la password";
		$color = "#721414";
	}
}
	

if (isset($_SESSION['user_name'])) // Hay sesión
{


	$directorio = scandir('albums');
	array_shift ($directorio); // Quitamos . y ..
	array_shift ($directorio);

	if ($_SESSION['user_name'] != "admin") // No es el admin
	{
		// Filtramos los albumes para que solo vea los que puede ver
		$ssql = "SELECT * FROM acceso
				WHERE user = '".$_SESSION['user_name']."'";
		$result =  mysql_query($ssql, $conexion);
			
		$i=0;
		while ($album_accesible = mysql_fetch_object($result))
		{
			$albums_accesibles[$i] = $album_accesible->album;
			$i++;
		}
	}
	else // Es el admin, acceso a todos
	{
		$albums_accesibles = $directorio;
	}	

$i=0;
foreach ($directorio as $album)
{
 
   // Antes de meterlo en el array tenemos que compbrobar si el usuario va a tener acceso!!
   if (in_array($album, $albums_accesibles) || !isset($albums_accesibles))
   {
	   $componentes = explode ("_", $album);
	   $long = count($componentes);
	   $anyo = $componentes[$long-1];
	   $mes = $componentes[$long-2];

	   $albumes [$anyo][$mes][$i] = $album;
   }
   $i++;
}
ksort($albumes);

$num_albums = count($albums_accesibles);
$anyos = array_reverse(array_keys($albumes));

?>

<br/>
Bienvenido! Tu usuario tiene acceso a <b><? echo $num_albums; ?></b> albums de fotos
<?
if (!$albumes) 
{
	echo "<h2>¡¡ No hay ningún album para el año seleccionado!!</h2>";
	exit -4;
}


	    // Bucle de construccion del listado html
		foreach ($anyos as $anyo) // Bucle de años
		{
		   $cuenta = 0;
		
		   ?>
		   
		   <img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
		   <div id="anyo_fotos">
		   <div id="anyo"><? echo $anyo; ?></div>
		   <?
		   
		
	       if (array_key_exists($anyo, $albumes))
		   {
		
				?>
				<table>
		 	    <tr>				
				<?
			
				foreach ($meses as $mes)  // Bucle de meses
		        {

				   if (array_key_exists($mes, $albumes[$anyo]))
				   {	
			          			  
					  foreach ($albumes[$anyo][$mes] as $album) // Bucle de álbumes
					  {
							$lista_fotos = array();
						   if ($cuenta > 3)
						   {
							?>
							<tr>
							<?
						   
						   }
						
						$ssql = "SELECT distinct autor FROM foto WHERE album = '$album'";
						$result =  mysql_query($ssql, $conexion);
						$array_autor = mysql_fetch_array($result);
						$autor = $array_autor[0];
						
					
						$nombre_bonito = substr_replace ($album, '', -9);
						
						if (isset($_SESSION['user_name']) && ($_SESSION['user_name'] == "admin")) // Soy admin acceso SIEMPRE
						{
							$accesible = true;
						}
						else // No somos admin solo acceso a los permitidos
						{
							$accesible = in_array($album, $albums_accesibles);
						}

						if ($accesible)
						{
					
							$path = "albums/$album/$autor/thumbnails";
							
							$ssql = "SELECT distinct id_foto FROM foto WHERE album = '$album' AND autor = '$autor'";
							$result =  mysql_query($ssql, $conexion);
							
							$i=0;
							while ($fotos = mysql_fetch_array($result))
							{
								$componentes = explode ("/", $fotos["id_foto"]);
								$lista_fotos[$i] = $componentes[4];
								$i++;
							}
							
							$foto_aleatoria = mt_rand(0, count($lista_fotos)-1);

							?>							
							<td style="text-align: center">
							
							<a class="enlace_thumbnail" href="album.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>&amp;pag=1">
							<? echo $nombre_bonito; ?><br/>
							
							<img class="marco_thumbnail" src="<? echo $path; ?>/<? echo $lista_fotos[$foto_aleatoria]; ?>" alt="<? echo  $lista_fotos[$foto_aleatoria]; ?>" />
							<br/>
							<? echo $mes." - ".$anyo; ?>
							</a>
							</td>
							<?
							
	                                       					
						   $cuenta++;
						   if ($cuenta > 3)
						   {
							?>
							</tr>
							<?
							$cuenta = 0;
						   }
						
						}
						
					   	  
					  } // Fin bucle albumes			  
				   }
				   			   
				} // Fin bucle meses	
			    ?>
			  </tr>
			  </table>
			<?
	  	  } // Fin de existe el album
?>
		</div>
		<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
<?

		} // Fin bucle de años

		?>

	   </div>
	   
		<?
	} // Fin Hay sesión
	else // No hay sesión
	{
?>
<br/><br/><br/>
<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
<div id="anyo_fotos">

<form name="admin3" action="<?echo $_SERVER["PHP_SELF"];?>" method="post">

	<font color ="<?echo $color;?>"><b><?echo $mensaje;?></b></font><br/>
     Para ver las fotos introduce la password.<br/> 
       <p align="center"><input class="pass" type="password" name="pass" id="pass" size="20" maxlength="20" tabindex="30" />
       <label for="pass">Password</label><br/><br/>
       <input class="button" type="submit" value=" Enviar "><br/><br/>
		</p>
</form> 
Si crees que deber&iacute;as tener password y no la tienes escribe a <a class="enlace_normal" href="mailto:webmaster@ismaeljb.net">webmaster@ismaeljb.net</a><br/>
</div>
<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>

</div>
<?	

	} // Fin NO hay sesion
include ('include/firma.html');
?>

</body>
</html>
