<? session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<? 
   include ('include/meta.php'); 
   include ("include/functions.php");
   include ("include/menu.php");
   require_once ('include/conexion.php');
?>

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

<title>Presentaci&oacute;n de im&aacute;genes</title>

<style type="text/css">
#foto {
filter: alpha(opacity=100)
}
</style>

<?
$album = $_GET['album'];
$autor = $_GET['autor'];

$directorio = "albums/$album/$autor";

$componentes = explode ("_", $album);
$nombre_bonito = substr_replace ($album, '', -9);
$titulo = $nombre_bonito." (".$componentes[count($componentes)-2]." ".$componentes[count($componentes)-1].") by ".$autor; 


$ssql = "SELECT id_foto FROM foto WHERE album = '$album' AND autor = '$autor'";
$result =  mysql_query($ssql, $conexion);

$i=0;
while ($fotos = mysql_fetch_array($result))
{
	$componentes = explode ("/", $fotos["id_foto"]);
	$imagenes[$i] = $componentes[4];
	$i++;
}


?>


<script type="text/javascript">
<!--

/*****************************************************************************
Presentación de Imágenes2 (SlideShow) por Tunait! 6/agosto/03
Actualizado el 28/12/2003
Si quieres usar este script en tu sitio eres libre de hacerlo con la condición de que permanezcan intactas estas líneas, osea, los créditos.

http://javascript.tunait.com
tunait@yahoo.com 
******************************************************************************/
var segundos = 5 //cada cuantos segundos cambia la imagen 

var dire =  <? echo "\"$directorio\""; //directorio o ruta donde están las imágenes ?> 

<?
	$i=0;
	echo "var imagenes=new Array()\n";
	foreach ($imagenes as $imag)
	{
	   echo "imagenes[$i]=\"$imag\";\n";
	   $i++;
	}

?>



if(dire != "" && dire.charAt(dire.length-1) != "/")
	{dire = dire + "/"}
var preImagenes = new Array()
for (pre = 0; pre < imagenes.length; pre++){
	preImagenes[pre] = new Image();
	preImagenes[pre].src = dire + imagenes[pre];
}
cont=0
function presImagen(){
	document.foto.src= dire + imagenes[cont];
	document.getElementById("nfoto").value = cont+1;
	
	subeOpacidad();
	if (cont < imagenes.length-1)
		{cont = cont+1;}
	else
		{cont=0;}
	tiempo=window.setTimeout('bajaOpacidad()',segundos*1000);
}
var iex = navigator.appName=="Microsoft Internet Explorer" ? true : false;
var fi = iex?'filters.alpha.opacity':'style.MozOpacity';
var opa = iex ? 100 : 1;
function bajaOpacidad(){
	opa = 0;
	cambia();
	presImagen();
}

function subeOpacidad(){
	opaci = iex?100:1;
	if(opa <= opaci){
		cambia();
		opa += iex?10: 0.1;
		setTimeout('subeOpacidad()',10);
	}
}
function cambia(){
	eval('document.foto.' + fi + ' = opa');
}
var tiempo
function inicio(){
	clearTimeout(tiempo);
	bajaOpacidad();
}

function anterior(){
	clearTimeout(tiempo)
	if (cont > 1)
	{
		cont = cont-1;
		cont = cont-1;
	}
	else
		cont = 0;
	bajaOpacidad()
}

function pausa(){
	clearTimeout(tiempo);
}

function menos_tiempo()
{
	if (segundos > 1)
		segundos = segundos-1;
	else
		segundos = 1;

	document.getElementById("segundos").value = segundos;
}

function mas_tiempo()
{
	if (segundos < 30)
		segundos = segundos+1;
	else
		segundos = 30;

	document.getElementById("segundos").value = segundos;
		
}

-->

</script>

</head>

<body>

<div id="wrapper" align="center">
<?
if (isset($_SESSION['user_name']))
{
	if (mysql_num_rows($result) == 0) // No hay fotos
	{
	    echo "<h1>¡Error!</h1>\n";
	    echo "<h2>El directorio no existe o la presentación aún no está disponible</h2>\n";
	    echo "</div></body></html>\n";
	    exit -3;
	}

?>



<br/>

<div id="cabecera">

<?


echo $titulo;

$ssql = "SELECT distinct autor FROM foto WHERE album = '$album';";
$result = mysql_query($ssql, $conexion);

if (mysql_num_rows($result) > 1) // Si hay mas de un autor ponemos todos
{
	while ($autor_bucle = mysql_fetch_array($result))
	{
		if ($autor_bucle["autor"] != $_GET["autor"])
		{
		?>
		&spades; <a href="?album=<? echo $album; ?>&amp;autor=<? echo $autor_bucle["autor"]; ?>&amp;pag=1" title="Fotos de <? echo $autor_bucle["autor"]; ?>">Presentaci&oacute;n (<? echo $autor_bucle["autor"]; ?>)</a>
		<?
		}
	}
}


?>
<br/><a class="enlace_normal" href="album.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>&amp;pag=1"> Volver al &aacute;lbum (<? echo $autor; ?>) </a>


</div>
   
<img src="<? echo "$directorio/$imagenes[0]"; ?>" name="foto" id="foto" alt="Fotos de la presentacion"/>

<table class="presentacion">
<tr> 
  <td align="center">
    <a href="javascript:anterior();" style="border:none"> <img style="border:none;" src="img/btn_retroceso.gif" alt="Botón retroceso"/></a>
    <a class="enlace_normal" href="javascript:inicio();" style="border:none"> <img style="border:none;" src="img/btn_play.gif" alt="Botón play"/></a>
	<a class="enlace_normal" href="javascript:pausa();" style="border:none"> <img style="border:none;" src="img/btn_pausa.gif" alt="Botón pausa"/></a>
	<a class="enlace_normal" href="javascript:inicio();" style="border:none"> <img style="border:none;" src="img/btn_avance.gif" alt="Botón avance"/></a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a class="enlace_normal" href="javascript:mas_tiempo();" style="border:none"><img style="border:none;" src="img/btn_mas.gif" alt="Botón mas"/></a>&nbsp;&nbsp;	
	<a class="enlace_normal" href="javascript:menos_tiempo();" style="border:none"><img style="border:none;" src="img/btn_menos.gif" alt="Botón menos"/></a>
	<br/>
	<input class="input_presentacion" type="text" name="nfoto" id="nfoto" size="3" value="1" disabled> de <input class="input_presentacion" type="text" name="nfoto" id="nfoto" size="3" value="<?echo "$i";?>" disabled />
	<br/><br/>
	Transici&oacute;n cada 
		<input class="input_presentacion" type="text" name="segundos" id="segundos" size="1" value="5" disabled>
	segundos
  </td>
</tr>
</table>

<?
} // Fin del hay sesión
else
{
	echo "<b>Debes iniciar sesi&oacute;n para ver esta presentaci&oacute;n.<br/> Ve a <a href=\"fotos.php\" class=\"enlace_normal\">fotos</a> e introduce la password o escribe a <a class=\"enlace_normal\" href=\"mailto:webmaster@ismaeljb.net\">webmaster@ismaeljb.net</a> si no la tienes</b>";
}
?>

</div>

<? include ('include/firma.html')?>

</body>
</html>
