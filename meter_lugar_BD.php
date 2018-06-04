

<?
// Leer el lugar del xml y meterlo en BD

require_once ('include/conexion.php');

$album = $_GET["album"]; //"Adaja_Oct_2008";
$autor = $_GET["autor"]; //"Zamo";

$dir_foto = "albums/".$album."/".$autor;
$dir = "albums/".$album."/".$autor."/xmls";

// La foto ya tiene XML

$directorio = scandir ($dir);
// Quitamos . y ..
array_shift ($directorio);
array_shift ($directorio);


//var_dump($directorio);


foreach ($directorio as $xml)
{
	echo "xml tratado: $dir/$xml ==> ";
	
	$dom = file("$dir/$xml");
	
	$ciudad = $dom[2];
	$region = $dom[3];
	$pais = $dom[4];
	
	echo "CIUDAD: ".$ciudad." REGION: ".$region." PAIS: ".$pais."<br/>";
	
	
	$nombre_foto = str_replace (".xml", "", $xml);
	$id_foto = "/".$dir_foto."/".$nombre_foto;
	
	
	// Se intenta insertar la foto en la BD, si no se puede es xq existe
	// Generamos la ssql e insertamos el registro (vacio)
	$ssql = "INSERT INTO foto (id_foto) VALUES ('$id_foto')";

	$no_existe_foto = mysql_query($ssql, $conexion);
	
	/*if (!$no_existe_foto) // La foto ya esta en la BD => updateamos con la informacion
	{
	   $ssql = "UPDATE foto
		SET album = '$album', autor = '$autor', ciudad = '$ciudad', region = '$region', pais = '$pais'
		WHERE id_foto = '$id_foto'
		LIMIT 1;";

	   mysql_query($ssql, $conexion) or die ("No se ha podido actualizar la foto ".$id_foto. " en la BD<br/>\n"); 
	   
	   echo "SE HA UPDATEADO LA FOTO: $id_foto <br/>";
	}
	else // La foto no esta en la BD -> Informacion de la cabecera EXIF
	{*/
	 
	 // EXISTA LA FOTO O NO ACTUALIZAMOS EL REGISTRO EN BD
	  $exif = exif_read_data($dir_foto."/".$nombre_foto, 0, true);
		
	  foreach ($exif as $key => $section) 
	  {
	     foreach ($section as $name => $val) 
	        $exif_header[$key][$name] = "$val";
	  }
	   
	  $fecha = $exif_header["EXIF"]["DateTimeOriginal"];
	  $fab_camara = $exif_header["IFD0"]["Make"]." ".$exif_header["IFD0"]["Model"];		 
	  $orientacion = $exif_header["IFD0"]["Orientation"];
	  if ($orientacion == "1")
	     $orientacion = "Vertical";
	  else
	     $orientacion = "Horizontal";
	  $tamanyo_KB = round ($exif_header["FILE"]["FileSize"] / 1024, 2);
	  $tam_reducida = "$tamanyo_KB KB";
	  $dist_focal = $exif_header["EXIF"]["FocalLength"];
	  $t_exposicion = $exif_header["EXIF"]["ExposureTime"];
	  $flash = $exif_header["EXIF"]["Flash"];
	  
	 //Generamos la ssql e insertamos el registro
	 $ssql = "UPDATE foto 
			  SET fecha = '$fecha',
				  fab_camara = '$fab_camara', 
				  album = '$album', autor = '$autor', ciudad = '$ciudad', region = '$region', pais = '$pais'
			  WHERE id_foto = '$id_foto'
			  LIMIT 1;";
	
		mysql_query($ssql, $conexion) or die ("No se ha podido insertar la foto $id_foto en la BD<br/>\n"); 
		
	   echo "SE HA ACTUALIZADO LA FOTO: $id_foto <br/>";
	//}	
		 

}

?>
