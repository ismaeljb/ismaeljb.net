<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?
   include ('include/meta.php'); 

$name = $_GET["name"];

if ($name)
{
?>
<meta property="og:title" content="<? echo $name; ?>" />
<meta property="og:type" content="drink" />
<meta property="og:url" content="http://www.ismaeljb.net/beer.php?name=<? echo $name ?>" />
<meta property="og:image" content="http://www.ismaeljb.net/img/beer/<? echo $name ?>.jpg" />
<meta property="og:site_name" content="La web de JB" />
<meta property="fb:admins" content="100001331608613" />
<?
}

   include ("include/functions.php");
   
   require_once ('include/conexion.php');
?>

<?
$ssql0 = "SELECT count(*) FROM beer;";
$result = mysql_query($ssql0, $conexion);

$numero = mysql_fetch_array($result);

// Obtenemos las variables

$name = $_GET["name"];
$namelike = $_GET["namelike"];
$groupby = $_GET["groupby"];
$country = $_GET["country"];
$abv = $_GET["abv"];
$style = $_GET["style"];
$search = $_POST["search"];

$html_title = "Beer";
if ($name)
{
	$nombre_foto = $name.".jpg";
	$ssql = "SELECT * FROM beer WHERE nombre_imagen = '$nombre_foto';";
	$titulo = $name;
	$html_title = "Beer: ".$name;
}
else if ($namelike)
{
	if ($namelike == 'All')
	{
		$ssql = "SELECT * FROM beer ORDER BY nombre;";
		$titulo = "All Beers";
	}
	else
	{
		$ssql = "SELECT * FROM beer WHERE nombre like '$namelike%' ORDER BY nombre;";
		$titulo = "Beers beggining with ".$namelike;
	}
	
}
else if ($groupby)
{
	if ((strcasecmp($groupby, "country")<>0) && (strcasecmp($groupby, "abv")<>0) && (strcasecmp($groupby, "style")<>0))
	{
		unset ($groupby);
		$ssql = "SELECT * FROM beer ORDER BY RAND() LIMIT 3;";
	}
	else
	{
		$ssql = "SELECT $groupby, count(*) as cta FROM beer GROUP BY $groupby ORDER BY $groupby;";
	}
}
else if ($country)
{
	$ssql = "SELECT * FROM beer WHERE country = '$country' ORDER BY nombre;";
	$titulo = "Beers from ".$country;
}
else if ($abv)
{
	$ssql = "SELECT * FROM beer WHERE abv = '$abv' ORDER BY nombre;";
	$titulo = "Beers with Alcohol By Volume ".$abv."%";
}
else if ($style)
{
	$ssql = "SELECT * FROM beer WHERE style = '$style' ORDER BY nombre;";
	$titulo = $style." beers";
}
else if ($search)
{
	$ssql = "SELECT * FROM beer WHERE nombre like '%$search%' ORDER BY nombre;";
	$titulo = "Beers matching your search";
}
else // No hay nada seteado
{
	$ssql = "SELECT * FROM beer ORDER BY RAND() LIMIT 3;";
	$titulo = "Some random beers";
}


$result = mysql_query($ssql, $conexion);

?>

<title><? echo $html_title;?></title>

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

<div id="wrapper">
<? include ("include/menu.php"); ?>

<h1 style="font-size: 1.2em;"><? echo $titulo; ?></h1>

<?
$array_letras = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','Z', 'All');
foreach ($array_letras as $letra)
{
?>
<a id="enlace_beer" href="?namelike=<? echo $letra; ?>" title="Beers beggining with <? echo $letra; ?>"><? echo $letra; ?></a> |
<?
}
?>

<table align="center" style="font-size: 0.95em;" summary="Lo siento, pero ie es tan mierda que he optado por usar tablas para maquetar esto.">
<tr>
<td>
<a id="enlace_beer" href="?groupby=country" title="By Country">By Country</a>
&nbsp;|&nbsp;
<a id="enlace_beer" href="?groupby=abv" title="By ABV">By <acronym title="Alcohol By Volume">ABV</acronym></a>
&nbsp;|&nbsp;
<a id="enlace_beer" href="?groupby=style" title="By Style">By Style</a>
&nbsp;|&nbsp;
</td>
<td>
<br/>
<form name="search" action="<?echo $_SERVER["PHP_SELF"];?>" method="post">
	<input style="font-size: 0.8em; background: url('css/background_input.gif') repeat-x left top; border: 0.1em solid #000000; color: #000000;" type="text" name="search" id="search" size="15" maxlength="20" tabindex="1" />
	<input style="font-size: 0.8em; background: #CCCCCC; border: 0.1em solid #000000; color: #000000;" class="button" type="submit" value=" Search ">	
</form>
</td>
</tr>
</table>

<?


if (!isset($groupby)) // No es agrupado
{

	while ($beer = mysql_fetch_array($result)) // Bucle cervezas
	{
	$enlace = substr($beer["nombre_imagen"], 0, -4);
	$web_bonita = substr($beer["web"], 7);
	
	?>
	
	<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
	<div id="anyo_fotos" style="height: 20em;">
	<div id="anyo"><a href="?name=<? echo $enlace; ?>" title="<? echo $beer["nombre"]; ?>"> <? echo $beer["nombre"]; ?></a></div>
	<br/>
	<img align="right" width="150" height="200" src="img/beer/<? echo $beer["nombre_imagen"]; ?>" title="<? echo $beer["nombre"]; ?>" alt="<? echo $beer["nombre"]; ?>" style="padding-right: 1em"/>
	<ul style="text-align: left;">

	<div style="width: 15em; float:left; ">
	<li><b>Country:</b> <a style="font-size: 1em; font-weight:normal;" href="?country=<? echo $beer["country"]; ?>" title="More beers from <? echo $beer["country"]; ?>"> <? echo $beer["country"]; ?></a></li>
	</div>

	<div>
	<li><b><acronym title="Alcohol By Volume">ABV</acronym>:</b> <a style="font-size: 1em; font-weight:normal;" href="?abv=<? echo $beer["ABV"]; ?>" title="More beers with ABV <? echo $beer["ABV"]; ?>%"><? echo $beer["ABV"]; ?>%</a></li>
	</div>

	<div style="width: 15em; float:left; ">
	<li><b>Style:</b> <a style="font-size: 1em; font-weight:normal;" href="?style=<? echo $beer["style"]; ?>" title="More <? echo $beer["style"]; ?> beers"><? echo $beer["style"]; ?></a></li>
	</div>
	<li><b>Web:</b> <a href="<? echo $beer["web"]; ?>" target="_blank" title="<? echo $web_bonita; ?>"><? echo $web_bonita; ?></a><br/></li>
	<li><b>Description:</b> <? echo $beer["descripcion"]; ?></li>
	</ul>
	
	<table align="center">
	<tr>
	<td>
	<!-- Facebook -->
	<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.ismaeljb.net%2Fbeer.php%3Fname%3D<? echo $enlace; ?>&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font=arial&amp;colorscheme=dark&amp;height=21" 
	scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
	</td>
	<td>
	<!-- Twitter -->
	<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
	<div style="width:130px;">
	   <a href="http://twitter.com/share" class="twitter-share-button"
		  data-url="http://www.ismaeljb.net/beer.php?name=<? echo $enlace; ?>"
		  data-via="<? echo $enlace; ?>">Tweet</a>
	</div>
	</td>
	</tr>
	</table>
	
	</div>
	<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
	
	<?
	$beer = mysql_fetch_array($result);
	$enlace = substr($beer["nombre_imagen"], 0, -4);
	$web_bonita = substr($beer["web"], 7);
	
	if ($beer) // Existe?
	{
	?>
	
	<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
	<div id="anyo_fotos" style="height: 20em;">
	<div id="anyo"><a href="?name=<? echo $enlace; ?>" title="<? echo $beer["nombre"]; ?>"><? echo $beer["nombre"]; ?></a></div>
	<br/>
	<img align="left" width="150" height="200" src="img/beer/<? echo $beer["nombre_imagen"]; ?>" title="<? echo $beer["nombre"]; ?>" alt="<? echo $beer["nombre"]; ?>" style="padding-left: 1em"/>
	<ul style="text-align: left; margin-left: 14em;">
	<div style="width: 15em; float:left; ">
	<li><b>Country:</b> <a style="font-size: 1em; font-weight:normal;" href="?country=<? echo $beer["country"]; ?>" title="More beers from <? echo $beer["country"]; ?>"> <? echo $beer["country"]; ?></a></li>
	</div>
	<div>
	<li><b><acronym title="Alcohol By Volume">ABV</acronym>:</b> <a style="font-size: 1em; font-weight:normal;" href="?abv=<? echo $beer["ABV"]; ?>" title="More beers with ABV <? echo $beer["ABV"]; ?>%"><? echo $beer["ABV"]; ?>%</a></li>
	</div>
	<div style="width: 15em; float:left; ">
	<li><b>Style:</b> <a style="font-size: 1em; font-weight:normal;" href="?style=<? echo $beer["style"]; ?>" title="More <? echo $beer["style"]; ?> beers"><? echo $beer["style"]; ?></a></li>
	</div>
	<li><b>Web:</b> <a href="<? echo $beer["web"]; ?>" target="_blank" title="<? echo $web_bonita; ?>"><? echo $web_bonita; ?></a><br/></li>
	<li><b>Description:</b> <? echo $beer["descripcion"]; ?></li>
	</ul>
	
	<table align="center">
	<tr>
	<td>
	<!-- Facebook -->
	<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.ismaeljb.net%2Fbeer.php%3Fname%3D<? echo $enlace; ?>&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font=arial&amp;colorscheme=dark&amp;height=21" 
	scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
	</td>
	<td>
	<!-- Twitter -->
	<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
	<div style="width:130px;">
	   <a href="http://twitter.com/share" class="twitter-share-button"
		  data-url="http://www.ismaeljb.net/beer.php?name=<? echo $enlace; ?>"
		  data-via="<? echo $enlace; ?>">Tweet</a>
	</div>
	</td>
	</tr>
	</table>
	
	</div>
	<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
	
	<?
	} // Fin existe 
	
	} // Fin bucle cervezas
	
	
} // Fin No es agrupado
else // Es agrupado
{

	$num_filas = mysql_num_rows($result);
	$mitad = $num_filas/2;
?>
<br/><br/>
<div align="left" style="margin-left:20%; width: 18em; float:left; ">
<ul>
<?
	for ($i=0; $i<$mitad; $i++)
	{
		$list = mysql_fetch_array($result); // Bucle agrupados
		?>
		<li><a id="enlace_beer" href="?<? echo $groupby; ?>=<? echo $list[$groupby]; ?>" title="<? echo $list[$groupby]; ?>"> <? echo $list[$groupby]; ?></a> (<? echo $list["cta"]; ?>)</li>
		<?
	} // Fin bucle agrupados

?>
</ul>
</div>


<div align="left" style="margin-left:40%; width: 20em;">
<ul>
<?
	for ($j=$i; $j<$num_filas; $j++)
	{
		$list = mysql_fetch_array($result); // Bucle agrupados
		?>
		<li><a id="enlace_beer" href="?<? echo $groupby; ?>=<? echo $list[$groupby]; ?>" title="<? echo $list[$groupby]; ?>"> <? echo $list[$groupby]; ?></a> (<? echo $list["cta"]; ?>)</li>
		<?
	} // Fin bucle agrupados

	$j = $i;
?>
</ul>
</div>


<?

} // Fin Es agrupado

if (!$name)
{
?>
<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
<div id="anyo_fotos">
This is a collection of beers that I've personally tasted. <u>We've now <strong> <? echo $numero[0]; ?> </strong> different beers.</u><br/>
The pictures were taken by myself but descriptions and other details were got at different sites, most of them at <a href="http://www.ratebeer.com" target="_blank">www.ratebeer.com</a>.<br/>
</div>
<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
<?
} // Fin Del bucle de cervezas
?>
	
</div>

<?	
include ('include/firma.html');
?>


</body>
</html>