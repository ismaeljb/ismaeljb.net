<?php

// Scipt que genera un fichero xml que sera la entrada
// para el carrusel de fotos en flash

// Recibe como parametro el nombre del album y el autor y genera los xmls correspondientes


$album = $_POST["album"];
$autor = $_POST["autor"];
$publicas = isset($_POST['generar_xml_publico']) && $_POST['generar_xml_publico'] == "yes";
$num_fotos_pagina = 54;

if (!isset($album) || strlen($album) == 0)
{
	echo "ES NECESARIO ESPECIFICAR UN ALBUM EXISTENTE";
	exit -1;
}

// RECIBE ALBUM Y AUTOR Y GENERAR UN XML CON 54 FOTOS CADA UNO
// COMO REQUISITO TODOS LOS DATOS DEL ALBUM DEBEN ESTAR EN BD

echo "<strong>GENERADOS XMLS PARA: ".$album." -- ".$autor."</strong><br/>";

$path_fotos = "albums/$album/$autor";

if ($publicas)
{
	$ssql = "SELECT * FROM foto WHERE album = '$album' AND autor = '$autor' AND es_publica = '1' ORDER BY id_foto";
}
else
{
	$ssql = "SELECT * FROM foto WHERE album = '$album' AND autor = '$autor' ORDER BY id_foto";
}
$result = mysql_query($ssql, $conexion);

$num_fotos = mysql_num_rows($result);
$indice_xml = 1;

if (!is_dir("albums/$album/xml")) // Directorio de xmls
	mkdirSafeMode("albums/$album/xml");


	
for ($i=0; $i<$num_fotos; $i+=$num_fotos_pagina) // Bucle de fotos por pagina
{
	if ($publicas)
	{
		$nombre_xml = "albums/$album/xml/pub_".$album."_".$autor."_".$indice_xml.".xml";
	}
	else
	{
		$prefijo = md5($album);
		$nombre_xml = "albums/$album/xml/".$prefijo."_".$album."_".$autor."_".$indice_xml.".xml";	
	}
		
	// Creamos/Abrimos el fichero
	if (!$gestor = fopen($nombre_xml, 'a')) 
	{
	  echo "No se puede crear el archivo $nombre";
	  exit;
	}
	
	// Truncamos el fichero por si acaso
	ftruncate($gestor, 0);

	$cadena = "";
	$cadena = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
	<photos>

	<config
		folder=\"albums/$album/$autor/\"
		enable_fullscreen=\"true\"
		galaxy_background=\"true\"	
		no_of_rings=\"3\"
		radius=\"228\"
		vertical_spacing=\"5\"
		default_zoom=\"6\"				
		show_tooltip=\"true\"	
		thumbnail_width=\"100\"
		thumbnail_height=\"100\"
		thumbnail_border_size=\"0\"
		thumbnail_border_color=\"#FFFFFF\"
		thumbnail_border_alpha=\"1\"
		photo_border_size=\"10\"
		photo_border_color=\"#FFFFFF\"
		close_button=\"true\"
		previous_button=\"true\"
		next_button=\"true\"
		description=\"true\"
		description_bg_color=\"#000000\"
		description_bg_alpha=\"0.6\"
		css_file=\"flashmo_210_style.css\"
		tween_duration=\"0.6\">
	</config>";
	
	$j = $i;
	$cta = 0;
	while ($cta<$num_fotos_pagina)
	{
				
		if ($j >= $num_fotos) // Ya no quedan mas fotos, repetimos
		{
			$result = mysql_query($ssql, $conexion); // Volvemos a recargar result con todo el array
			$j = 0;
			continue; 
		}
		
		$info_foto =  mysql_fetch_object($result);
		$componentes = explode ("/", $info_foto->id_foto);
		$foto = $componentes[4];
	
		// Titulo de la foto
		$componentes = explode ("_", $album);
		$nombre_bonito = substr_replace ($album, '', -9);
		$titulo = $nombre_bonito." (".$componentes[count($componentes)-2]." ".$componentes[count($componentes)-1].") by ".$autor;
		$titulo_corto = $nombre_bonito." (".$componentes[count($componentes)-2]." ".$componentes[count($componentes)-1].")"; 
					
		$id_foto = $info_foto->id_foto;
		$ciudad = $info_foto->ciudad;
		$region = $info_foto->region;
		$pais = $info_foto->pais;
		$fecha = $info_foto->fecha;
		$fab_camara = $info_foto->fab_camara;
		
		$tiene_lugar = ((isset($ciudad)) && (strlen($ciudad)>0));
		if ($tiene_lugar)
		{
			$lugar = "$ciudad, $region, $pais";
			$ciudad_bonita = str_replace("+", " ", $ciudad)." (".$pais.")";
		}
		$tiene_exif = (isset($fecha) && (strlen($fecha)>0) && isset($fab_camara) && (strlen($fab_camara)>0));
	
		$cadena .= "
	
		 <photo>
			<thumbnail>thumbnails/$foto</thumbnail>
			<filename>$foto</filename>
			<tooltip>$titulo_corto</tooltip>
			<description><![CDATA[<p class=\"subtitle\">$titulo</p>";
		if 	($tiene_lugar)
		{
			$cadena .= "<span class=\"highlight\">Lugar: </span><p><a href=\"http://maps.google.com/maps?hl=en&amp;q=$lugar\" target=\"_blank\">$ciudad_bonita</a></p>";
		}
		if 	($tiene_exif)
		{
			// Formateamos la fecha
			$formateo_fecha = explode(":", $fecha);
			$anyo = $formateo_fecha[0];
			$mes = $formateo_fecha[1];
			$dia_hora = explode(" ", $formateo_fecha[2]);
			$dia = $dia_hora[0];
			$hora = $dia_hora[1];
			$minuto = $formateo_fecha[3];
			$segundo = $formateo_fecha[4];
			
			$fecha_bonita = "$dia/$mes/$anyo - $hora:$minuto:$segundo";
			
			$cadena .= "<span class=\"highlight\">Fecha: </span><p>$fecha_bonita</p>";
			$cadena .= "<span class=\"highlight\">Camara: </span><p>$fab_camara</p>";
		}
		
		$cadena .= "<p><span class=\"note\"><a href=\"http://www.ismaeljb.net\" target=\"_blank\">www.ismaeljb.net</a></span></p>]]></description>
		</photo>";
		
		$j++;
		$cta++;
				
	} // Fin bucle fotos


	$cadena .= "
	</photos>";
	
	fwrite ($gestor, $cadena);
	
	fclose($gestor);
	
	$indice_xml++;
	
} // Fin de bucle fotos por pagina

?>


