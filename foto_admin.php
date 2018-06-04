<? session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Fotos -> Album -> Foto</title>

<? include ('include/meta.php'); ?>


<script type="text/javascript" src="js/show_hide.js" language="JavaScript" ></script>
<script type="text/javascript" src="js/browserdetect.js" language="JavaScript" ></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8447274-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body>

<div id="contenido_ppal">

<?php

if (isset($_SESSION['user_name']))
{

	$directorio_trabajo =  $_GET['album'];
	$foto = $_GET['foto'];
	$num_pagina = $_GET['pag'];
	$autor = $_GET['autor'];
	   
	$dir_foto = "$directorio_trabajo/$foto";
	   
	if (!$_GET['foto'] || !file_exists($dir_foto)) // El parametro pasado no es una foto
	{
	   echo "<h1>La foto no existe, melón</h1>\n";
	   echo "</div></body></html>\n";
	   exit -3;
	}


	echo "<a style=\"color:#FFFFFF; text-decoration:none;\" href=\"javascript:history.back(1)\"> &lt;&lt; Volver al álbum</a><br/><br/>\n";
	echo "<img class=\"marco_thumbnail\" src=\"$dir_foto\" alt=\"$dir_foto\" /><br/><br/>\n"; 
	 
} // Fin del hay sesión
else
{
	echo "<b>Debes iniciar sesi&oacute;n para ver esta foto.<br/> Ve a <a href=\"fotos.php\" class=\"enlace_normal\">fotos</a> e introduce la password o escribe a <a class=\"enlace_normal\" href=\"mailto:webmaster@ismaeljb.net\">webmaster@ismaeljb.net</a> si no la tienes</b>";
}
?>
</div>

<? include ('include/firma.html')?>

</body>
</html>
