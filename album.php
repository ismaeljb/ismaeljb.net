<? session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<? 
include ('include/meta.php'); 
require_once ('include/conexion.php');
?>

<?
if (isset($_SESSION['user_name'])) // Hay sesión
{
	include ("include/functions.php");
	
	$num_fotos_pagina = 54;
	$directorio_originales = "albums/Backup_Originales";
	$album = $_GET["album"];
	$autor = $_GET["autor"];
	$pag = $_GET["pag"];

	$componentes = explode ("_", $album);
	$nombre_bonito = substr_replace ($album, '', -9);
	$titulo = $nombre_bonito." (".$componentes[count($componentes)-2]." ".$componentes[count($componentes)-1].") by ".$autor; 
	if (isset($album) && isset($autor))
	{
		$prefijo = md5($album);
		$nombre_xml = "albums/$album/xml/".$prefijo."_".$album."_".$autor."_".$pag.".xml";
	}
}

?>

<title>Fotos <? echo $titulo; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Keywords" content="3D Curve Gallery, Flash XML, Photo Gallery, Papervision3D, Tweener, ActionScript 3, flashmo, fluid layout" />
<meta name="Description" content="3D Curve Gallery is an open source image gallery using Flash XML ActionScript 3.0, Papervision3D and Tweener." />
<style type="text/css" media="screen">
	html, body, #flashmo_carousel	
	{ height:100%; }
	body						
	{ margin:0; padding:0; }
</style>	
<script type="text/javascript" src="swfobject.js"></script>
<script type="text/javascript">
var flashvars = {};
flashvars.xml_file = "<? echo $nombre_xml; ?>";
var params = {};
params.allowfullscreen = true;
var attributes = {};
swfobject.embedSWF("flashmo_236_3d_curve_gallery.swf", "flashmo_gallery", "100%", "100%", "9.0.0", false, flashvars, params, attributes);
</script>

</head>
<body>
<div id="wrapper">
<? include ("include/menu.php"); ?>

<div id="cabecera">
<? 

if (isset($_SESSION['user_name'])) // Hay sesión
{

	echo $titulo; 
		
	$ssql = "SELECT count(*) as cuenta FROM foto WHERE album = '$album' AND autor = '$autor';";
	$result = mysql_query($ssql, $conexion);
	$arr_aux = mysql_fetch_array($result);
	$num_fotos = $arr_aux["cuenta"];
	
	$num_paginas = ceil($num_fotos/$num_fotos_pagina);
	
	$ssql = "SELECT distinct autor FROM foto WHERE album = '$album';";
	$result = mysql_query($ssql, $conexion);
	
	if (mysql_num_rows($result) > 1) // Si hay mas de un autor ponemos todos
	{
		while ($autor_bucle = mysql_fetch_array($result))
		{
			if ($autor_bucle["autor"] != $_GET["autor"])
			{
			?>
			&spades; <a href="album.php?album=<? echo $album; ?>&amp;autor=<? echo $autor_bucle["autor"]; ?>&amp;pag=1" title="Fotos de <? echo $autor_bucle["autor"]; ?>"><? echo $autor_bucle["autor"]; ?></a>
			<?
			}
		}
	}


     // Hay algun .rar porque el .zip tiene un tamanyo maximo
	if (file_exists("$directorio_originales/$album"."_Originales.zip") || file_exists("$directorio_originales/$album"."_Originales.rar"))
    {
	    if (file_exists("$directorio_originales/$album"."_Originales.zip")) // Hay .zip 
		{
   			$ext = "zip";
			
		}
		else // Hay .rar
		{
		    $ext = "rar";
		}
		$tam = filesize("$directorio_originales/$album"."_Originales.$ext") / 1024;
   		$tam = round ($tam / 1024, 2);
	?>
	<br/><img src="img/winrar.gif" alt="logo winrar" align="absbottom"/> <a href="<? echo "$directorio_originales/$album"."_Originales.$ext"; ?>"> Descargar originales </a>(<? echo $tam; ?>MB)
   <?
   }
   else if (file_exists("$directorio_originales/$album"."_".$autor."_Originales.zip"))
   {
   		$tam = filesize("$directorio_originales/$album"."_".$autor."_Originales.zip") / 1024;
   		$tam = round ($tam / 1024, 2);
	?>
	<br/><img src="img/winrar.gif" alt="logo winrar" align="absbottom"/> <a href="<? echo "$directorio_originales/$album"."_".$autor."_Originales.zip"; ?>"> Descargar originales <? echo $autor; ?> </a>(<? echo $tam; ?>MB)   
	<?
   }
   else
   {
   ?>
	  <br/> Fotos orignales no disponibles 
	<?
   }
   ?>
   &spades; &spades; &spades; <a class="enlace_normal" href="presentacion.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>"> Ver presentaci&oacute;n (<? echo $autor; ?>) </a>
   <br/>
   <?
   
	if ($num_paginas > 1) // Hay mas de una pagina
	{
		if ($pag > 1) // Hay anteriores
		{
			$anterior = $pag-1;
			?>
			<a href="album.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>&amp;pag=<? echo $anterior; ?>" title="Anterior">&lt;&lt;</a>
			<?
		}
		?>
	Pagina <? echo $pag; ?> de <? echo $num_paginas; ?> 
	<?
		if ($pag < $num_paginas) // Hay siguientes
		{
			$siguiente = $pag+1;
			?>
			<a href="album.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>&amp;pag=<? echo $siguiente; ?>" title="Siguiente">&gt;&gt;</a>
			<?
		}
	} // Fin hay mas de una pagina
} // Fin hay sesion
	?>
	</div> <!-- Fin cabecera -->
</div> <!-- Fin wrapper -->
		<div id="flashmo_gallery">
			<div id="alternative_content">
				<h1><a href="http://www.flashmo.com" title="3D Curve Gallery">3D Curve Gallery</a> from flashmo.com</h1>
        		<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
            <br /><br />
			<a href="http://www.flashmo.com">Free Flash Gallery</a>
		</div>

		
</body>
</html>