<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<? 
    include ('include/meta.php'); 
	include ("include/functions.php");
		
	$directorio = scandir ("img/hero");
	//array_shift ($heros);
	//array_shift ($heros);
	$heros = array_filter ($directorio, "es_foto");
	$clave_aleatoria = array_rand($heros);
	$hero = $heros[$clave_aleatoria];

?>

<title>Bienvenido a ismaeljb.net</title>


</head>
<body>
<!-- Google Analytics 20140828 -->
<? include_once ("include/analyticstracking.php"); ?>
<div id="wrapper">
<? include ("include/menu.php"); ?>
<br/>
<img src="img/hero/<? echo $hero; ?>" alt="<? echo $hero; ?>"/>

</div>

</body>
</html>