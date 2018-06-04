<? session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<? 
    include ('include/meta.php'); 
    include ("include/functions.php");
?>

<title>Administracion</title>


</head>
<body>

<? 
include ('include/mkdir_safemode_hosting24.php'); 
?>


<div id="wrapper">
<?
include ("include/menu.php");

function sure_remove_dir($dir, $deleteme) // Borra el contenido de un directorio 
// http://es2.php.net/manual/es/function.unlink.php
{
    if(!$dh = @opendir($dir)) return;
    while (false !== ($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj)) sure_remove_dir($dir.'/'.$obj, true);
    }
    if ($deleteme){
        closedir($dh);
        @rmdir($dir);
    }
}


//print_r ($_POST); 
//print_r ($_FILES);
//print_r ($_SERVER);


require_once ('include/conexion.php');


if ($_GET['logout'] == "Yes")
    unset($_SESSION['user_name']);

// Inicio de sesion
if ($_POST['pass'])
{

  $ssql = "SELECT * FROM admin
           WHERE id_admin = '1'     
           LIMIT 1;";

  $result = mysql_query($ssql, $conexion);
  $admin = mysql_fetch_object($result);

if ($_POST['pass'] == $admin->pass)
  {
     echo "<font color =\"#98FB98\"><b>Bienvenido JB!!</b></font><br><br>\n";
     $_SESSION['user_name'] = "admin";
  }
  else 
  {
     echo "<font color =\"red\"><b>Esa no es la clave</b></font><br><br>\n";
  }
}

?>

<br/>
<a href="log.txt" style="color: #FFFFFF;"> Fichero log </a><br/><br/>

<?

if ($_SESSION['user_name'] == "admin")
{

// 1. BORRADO DE FOTOS, COMENTARIOS DE FOTOS, VISITAS Y ALBUMES ---------------------------------------
if (isset($_GET['tabla']))
{
   $tabla = $_GET['tabla'];
   if ($tabla == "album") // Es un album -> Solo hay que eliminar el directorio
   {
      $dir = $_GET['id'];
      sure_remove_dir ($dir, true);

   }
   else
   {
      $id = $_GET['id'];
   
      $ssql = "DELETE FROM $tabla  
               WHERE id_$tabla='$id'
               LIMIT 1;";

      mysql_query($ssql, $conexion) or die ("No se ha podido eliminar el registro de la BD<br>\n"); 

      if ($tabla == "foto") // Se va a eliminar una foto -> hay que eliminar todos sus archivos
      {
          $ruta = explode("/", $id);
          $album = $ruta['1'];
          $autor = $ruta['2'];
          $foto = $ruta['3'];
          $original = "albums/$album/$autor/originales/$foto";
          $thumbnail = "albums/$album/$autor/thumbnails/$foto";


          // Se elimina del .zip reducidas
	// EN HOSTING24 YA NO PUEDO HACER EXEC	  
	  /*exec ("zip -d \"albums/$album/$album.zip\" \"albums/$album/$autor/$foto\" ", $res_zip);
	  if (!$res_zip)
	  {
	     echo "Ha habido un error al eliminar el archivo del .zip<br>";
	     exit -5; 
	  }*/
   
      // Se elimina del .zip originales
	  // EN HOSTING24 YA NO PUEDO HACER EXEC	
	  /*exec ("zip -d \"albums/$album/originales_$album.zip\" \"albums/$album/$autor/originales/$foto\" ", $res_zip);
	  if (!$res_zip)
	  {
	     echo "Ha habido un error al eliminar el archivo del .zip de originales<br>";
	     exit -5; 
	  }
*/
          unlink("$id");
          if (file_exists ($original))
             unlink($original);
          if (file_exists ($thumbnail))
             unlink($thumbnail);
          if (file_exists ($xml))          
             unlink($xml);  
   
     }
   } 
   echo "<font color =\"#98FB98\"><b>$tabla elminada/o</b></font><br><br>\n";
}
// 1. FIN --------------------------------------------------------------------------------------------------

// 2. CREACION DE ALBUMS -----------------------------------------------------------------------------------
// Se comprueba si se ha subido algún fichero (.rar o .zip) para la creacion de albums
if (isset($_POST['nombre_album']))
{
   
   $nombre_album = $_POST['nombre_album'];
   $mes_album = $_POST['mes_album'];
   $anyo_album = $_POST['anyo_album'];
   $autor_album = $_POST['autor_album'];
   
   $album = $nombre_album."_".$mes_album."_".$anyo_album;

   mkdirSafeMode ("albums/$album");
   mkdirSafeMode ("albums/$album/$autor_album");
    
   echo "<font color=\"#98FB98\"><b>Se han recibido datos para crear el album $album</b></font><br>\n";

} 
// 2. FIN --------------------------------------------------------------------------------------------------

// 3. TAGGEO DE LUGAR EN LAS FOTOS Y PUBLICAR/PRIVAR VARIAS-------------------------------------------------
if (isset($_POST['primera']) && ($_POST['primera'] != ""))
{
	$publicar_foto = isset($_POST['publicar']) && $_POST['publicar'] == "publicar";
	$privar_foto = isset($_POST['privar']) && $_POST['privar'] == "privar";

	if ($publicar_foto)
	{
		echo "<font color=\"#F5DEB3\"><b>Se han recibido datos para PUBLICAR fotos</b></font><br>\n";
	}
	else if ($privar_foto)
	{
		echo "<font color=\"#F5DEB3\"><b>Se han recibido datos para PRIVAR fotos</b></font><br>\n";
	}
	else
	{
		echo "<font color=\"#98FB98\"><b>Se han recibido datos para ETIQUETAR un directorio de fotos</b></font><br>\n";
	}


   $dir = $_POST['directorio'];
   $ruta = explode("/", $dir);
   $album = $ruta['1'];
   $autor = $ruta['2'];

   $primera = $_POST['primera'];
   $ultima = $_POST['ultima'];
   
   $ciudad = $_POST['ciudad'];
   $region = $_POST['region'];
   $pais = $_POST['pais'];


   $directorio = scandir ($dir);

   $indice_primera = array_search($primera, $directorio);
   $indice_ultima = array_search($ultima, $directorio);
   
   for ($i=$indice_primera; $i<=$indice_ultima; $i++) // Bucle de fotos tageadas
   {

    $foto = $directorio[$i];
    if (stripos ($foto, '.jpg')) // Es foto
    {
		
	  $id_foto = "/".$dir."/".$foto;
	  //$ssql = "INSERT INTO foto (id_foto) VALUES ('$id_foto')";

	  //$no_existe_foto = mysql_query($ssql, $conexion);
	  
	  if ($publicar_foto) // Estamos publicando fotos
	  {
		  //Generamos la ssql y actualizamos el registro
		  $ssql = "UPDATE foto 
				  SET es_publica = '1'
				  WHERE id_foto = '$id_foto'
				  LIMIT 1;";
		
		  mysql_query($ssql, $conexion) or die ("No se ha podido actualizar la foto $id_foto en la BD<br/>\n"); 	  
	  }
	  else if ($privar_foto) // Estamos des-publicando fotos
	  {
		  //Generamos la ssql y actualizamos el registro
		  $ssql = "UPDATE foto 
				  SET es_publica = '0'
				  WHERE id_foto = '$id_foto'
				  LIMIT 1;";
		
		  mysql_query($ssql, $conexion) or die ("No se ha podido actualizar la foto $id_foto en la BD<br/>\n"); 	  
	  }
	  else // Estamos etiquetando el lugar
	  {
		
		  $exif = exif_read_data($dir."/".$foto, 0, true);
		
		  // Este o no la foto en BD actualizamos todos los campos	
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
		  
		  //Generamos la ssql y actualizamos el registro
		  $ssql = "UPDATE foto 
				  SET fecha = '$fecha',
					  fab_camara = '$fab_camara', 
					  album = '$album', autor = '$autor', ciudad = '$ciudad', region = '$region', pais = '$pais'
				  WHERE id_foto = '$id_foto'
				  LIMIT 1;";
		
		  mysql_query($ssql, $conexion) or die ("No se ha podido actualizar la foto $id_foto en la BD<br/>\n"); 
	  }
		
	  echo "SE HA ACTUALIZADO LA FOTO: $id_foto <br/>";
		
     } // Fin es foto

   } // Fin bucle de fotos tageadas
}
// 3. FIN --------------------------------------------------------------------------------------------------

// 4. GIRO DE FOTOS (DEPRECATED) ---------------------------------------------------------------------------
/*
function giro_fotos ($lista_fotos, $grados, $conexion)
{

   foreach ($lista_fotos as $foto)
   {  
      // Obtencion de la ruta del thumbnail
      $ruta = explode("/", $foto);
      $album = $ruta['1'];
      $autor = $ruta['2'];
      $nombre_foto = $ruta['3'];
      $ruta_thumbnail = "albums/$album/$autor/thumbnails/$nombre_foto";

	  // ANTES DE GIRARLA INSETAMOS LA INFO DEL EXIF EN BD

	  // Se intenta insertar la foto en la BD, si no se puede es xq existe
	  //Generamos la ssql e insertamos el registro (vacio)
	  $ssql = "INSERT INTO foto (id_foto) VALUES ('/$foto')";

	  $no_existe_foto = mysql_query($ssql, $conexion);
	  
	  if ($no_existe_foto) // La foto NO esta en la BD => Se obtiene el exif y se inserta en BD
	  {
	  	  $exif = exif_read_data("$foto", 0, true);
	     
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
		  
		  $autor_foto = explode ('/', $directorio_trabajo);
		  $autor_foto = end($autor_foto);
	  
	      //Generamos la ssql e insertamos el registro
	      $ssql = "UPDATE foto 
	              SET fecha = '$fecha',
	                  fab_camara = '$fab_camara', 
	                  orientacion = '$orientacion', 
	                  tam_reducida = '$tam_reducida', 
	                  dist_focal = '$dist_focal', 
	                  t_exposicion = '$t_exposicion',
	                  flash = '$flash', 
	                  autor = '$autor_foto',
					  num_visitas = '1',
					  num_comentarios = '0'
	              WHERE id_foto = '/$foto'
	              LIMIT 1;";

	      mysql_query($ssql, $conexion) or die ("No se ha podido insertar la foto $id_foto en la BD<br/>\n"); 
	  }
	
      // Creacion del recurso imagen y rotacion
      $original = imagecreatefromjpeg($foto);
      $thumbnail = imagecreatefromjpeg($ruta_thumbnail);
      
      $rotada = imagerotate($original, $grados, 0);
      $rotada_th = imagerotate($thumbnail, $grados, 0);

      // Copia de la imagen al mismo archivo
      imagejpeg($rotada, $foto);
      imagejpeg($rotada_th, $ruta_thumbnail);    
	
      // Destruccion de los recursos
      imagedestroy ($original);
      imagedestroy ($thumbnail);
      */
      // Se vuelve a meter al .zip
	  // EN HOSTING24 YA NO PUEDO HACER EXEC	
      /*
	  exec ("zip \"albums/$album/$album.zip\" \"$foto\" ", $res_rar);
      if (!$res_rar)
      {
          echo "Ha habido un error al intentar comprimir los archivos reducidos<br/>";   
          echo "</div></body></html>\n";
          exit -5; 
      }
      */
/*
   }

   echo "<font color=\"#98FB98\"><b>Fotos giradas</b></font><br>\n";  

}

if (isset($_POST['giro_izda'])) // A la izquierda
{
   $giros_izda = $_POST['giro_izda'];
   giro_fotos ($giros_izda, 90, $conexion);
  
}

if (isset($_POST['giro_dcha'])) // A la derecha
{
   $giros_dcha = $_POST['giro_dcha'];
   giro_fotos ($giros_dcha, 270, $conexion);
}   
*/
// 4. FIN --------------------------------------------------------------------------------------------------

// 5. NUEVA NOTICIA (DEPRECATED) ---------------------------------------------------------------------------
/*
if ($titulo = $_POST['titulo']) // Se han recibido datos del formulario
{

   $foto = $_POST['foto'];
   $texto = $_POST['texto'];
   $texto = nl2br($texto); // Se sustituyen los saltos de linea por <br/>

   $ssql = "INSERT INTO novedad (titulo, fecha, foto, texto) 
            VALUES ('$titulo', NOW()+0, '$foto', '$texto')";

   mysql_query($ssql, $conexion) or die ("No se ha podido insertar el comentario en la BD<br/>\n"); 

   echo "<font color=\"#98FB98\"><b>Comentario insertado</b></font><br>\n";
}
*/
// 5. FIN --------------------------------------------------------------------------------------------------

// 6. MODIFICACION ALBUMS USUARIOS -------------------------------------------------------------------------
if (isset($_POST['album']))
{

	$album = $_POST['album'];
	$usuario = $_POST['usuario'];
	
	if ($_POST['accion'] == "Insertar") // Mete album en un usuario
	{
		 $ssql = "INSERT INTO acceso (user, album) VALUES ('$usuario','$album')";
	     
		 mysql_query($ssql, $conexion) or die ("No se ha podido incluir el album en el usuario<br/>\n"); 				  
	}
	else if ($_POST['accion'] == "Eliminar") // Elimina album en un usuario
	{
		$ssql = "DELETE FROM acceso WHERE user = '$usuario' AND album = '$album' LIMIT 1;";

		 mysql_query($ssql, $conexion) or die ("No se ha podido eliminar el album en el usuario<br/>\n"); 		
	}
}

// 6. FIN --------------------------------------------------------------------------------------------------

// 7. GENERACION DE XML PARA LOS FLASH ---------------------------------------------------------------------

if (isset($_POST['generar_xml']) && $_POST['generar_xml'] == "yes")
{
	include('include/xml_generator.php');
}

if (isset($_POST['generar_xml_publico']) && $_POST['generar_xml_publico'] == "yes")
{
	include('include/xml_generator.php');
}


?>
<div id="formulario_comentarios"><? include ('include/forms_admin.php'); ?></div>
<?
} // Fin sesion admin
?>

<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
<div id="anyo_fotos" style="text-align: left; padding-left: 0.4em; width: 54em;">

<form name="admin3" action="administracion.php" method="post">

<b>Clave del admin</b> 
    <p><input class="pass" type="password" name="pass" id="pass" size="20" maxlength="20" tabindex="30" />
       <label for="pass"><small>Clave del administrador</small></label></p> 
       <input class="button" type="submit" value=" Enviar ">  
</form> 
<a href="administracion.php?logout=Yes"> Salir</a>
<br/>
</div>
<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>

</div>
<?
// 7. FIN -------------------------------------------------------------------------------------------------

// 8. PERMISOS SOBRE LOS DIRECTORIOS ----------------------------------------------------------------------
if (isset($_POST['activar_permisos']) && $_POST['activar_permisos'] == "yes")
{
	chmod ($_POST['directorio_trabajo'], 0711);
	chmod ($_POST['directorio_thumbnails'], 0711);
}
if (isset($_POST['desactivar_permisos']) && $_POST['desactivar_permisos'] == "yes")
{
	chmod ($_POST['directorio_trabajo'], 0311);
	chmod ($_POST['directorio_xml'], 0311);
	chmod ($_POST['directorio_thumbnails'], 0311);
}
// 8. FIN -------------------------------------------------------------------------------------------------

// 9. PUBLICAR / PRIVAR UNA FOTO --------------------------------------------------------------------------


// 9. FIN -------------------------------------------------------------------------------------------------
 include ('include/firma.html')?>

</body>
</html>
