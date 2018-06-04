<? session_start(); ?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<? 
    include ('include/meta.php'); 
	include ("include/functions.php");
?>

<title>Administracion de ismaeljb.net</title>

</head>
<body>

<br/>

<? 
include ('include/mkdir_safemode_hosting24.php'); 
?>

<div id="wrapper">
<? include ("include/menu.php"); ?>

<?php

 // Inicio de sesion
require_once ('include/conexion.php');

// Ancho y alto de las fotos reducidas
define ("ancho_reducida", 640);
define ("alto_reducida",  480);
// Fotos mostradas por cada página del álbum
define ("tope_fotos_pagina", 20);
define ("tope_fotos_fila", 4);


$album = $_GET['album'];
$directorio_album = "albums/$album";
$directorio_originales = "albums/Backup_Originales";
$autor = $_GET['autor'];
$directorio_trabajo = "$directorio_album/$autor";
     
if (!isset($album) || !isset($autor) || !is_dir($directorio_album) || (!is_dir($directorio_trabajo) && $autor!="todas") || ($directorio_album == '.') || ($directorio_album == '..') || (!opendir("./$directorio_album")))
// El parametro pasado no es un directorio válido
{
  ?>
   <h1>El directorio no existe o no se puede abrir</h1><br/>
   </div></body></html>
   <?
   exit -3;
}

if (!isset($_SESSION['user_name']) || ($_SESSION['user_name'] != "admin")) // Hay sesión
{
	echo "NO ERES ADMIN!!";
	exit -2;
}

?>
<a style="color:#FFFFFF; text-decoration: none;" href="administracion.php">INDEX ADMIN</a>
<?

if (isset($_GET['publicar']) && $_GET['publicar'] == "yes")
{
	$id_foto = $_GET['id_foto'];
	echo "<font color=\"#F5DEB3\"><b>Se PUBLICA la foto $id_foto</b></font><br>\n";
  //Generamos la ssql y actualizamos el registro
  $ssql = "UPDATE foto 
		  SET es_publica = '1'
		  WHERE id_foto = '$id_foto'
		  LIMIT 1;";

  mysql_query($ssql, $conexion) or die ("No se ha podido actualizar la foto $id_foto en la BD<br/>\n"); 	  
}
if (isset($_GET['privar']) && $_GET['privar'] == "yes")
{
	$id_foto = $_GET['id_foto'];
	echo "<font color=\"#F5DEB3\"><b>Se PRIVA la foto $id_foto</b></font><br>\n";
  //Generamos la ssql y actualizamos el registro
  $ssql = "UPDATE foto 
		  SET es_publica = '0'
		  WHERE id_foto = '$id_foto'
		  LIMIT 1;";

  mysql_query($ssql, $conexion) or die ("No se ha podido actualizar la foto $id_foto en la BD<br/>\n"); 	  
}

	if ($autor == "todas") // Contamos el número de autores
	{
	   $autores = scandir("$directorio_album");
	   array_shift ($autores); // Quitamos . y ..
	   array_shift ($autores);
	   $autores = array_filter ($autores, "es_autor"); // Quitamos el .zip
	}
	else
	   $autores[0] = $autor;

	$j=0; // Subindice para la lista de fotos
	foreach ($autores as $author) // Bucle de autores
	{  

	   $directorio_trabajo = "$directorio_album/$author";
	   
	   if (!is_dir("$directorio_trabajo/thumbnails")) // Directorio de thumbnails (160x120)
	      mkdirSafeMode("$directorio_trabajo/thumbnails", 0777);
	                       
	   clearstatcache(); // Si no se hace hay problemas a la hora de crear los directorios anteriores

	   // Metemos todas las fotos del directorio en un array
	   if ($entradas_directorio = @scandir("$directorio_trabajo"))
	   {
	       ?>
	   		<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
			<div id="anyo_fotos">
			<b>DESACTIVAR PERMISOS</b><br/><br/>
			<form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
				<input class="button" type="submit" value=" DESACTIVAR PERMISOS "/>
				<input name="desactivar_permisos" id="desactivar_permisos" type="hidden" value="yes" />
				<input name="directorio_trabajo" id="directorio_trabajo" type="hidden" value="<? echo $directorio_trabajo; ?>" />
				<input name="directorio_xml" id="directorio_xml" type="hidden" value="<? echo "$directorio_album/xml"; ?>" />
				<input name="directorio_thumbnails" id="directorio_xml" type="hidden" value="<? echo "$directorio_trabajo/thumbnails"; ?>" />
			</form>
			<br/>
			</div>
		   <img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
		   <?
	   
			$entradas_directorio = array_filter ($entradas_directorio, "es_foto");
			foreach ($entradas_directorio as $foto) // Bucle de fotos
			{

			// 1. COMPROBAMOS SI TIENE THUMBNAIL Y EN CASO CONTRARIO LO CREAMOS
			if (!file_exists("$directorio_trabajo/thumbnails/$foto")) // La foto no tiene thumbnail (es nueva o no ha podido crearse)
			{

			  // Se crea el thumbnail		  
			  $str_thumbnail = exif_thumbnail("$directorio_trabajo/$foto", $width_thumb, $height_thumb, $type);
				
			  if ($str_thumbnail) // Se ha creado el thumbnail
			  {
				  $rsc_thumbnail = imagecreatefromstring($str_thumbnail);
				  imagejpeg($rsc_thumbnail, "$directorio_trabajo/thumbnails/$foto");
			 
				  imagedestroy ($rsc_thumbnail);		  
			  }
			}  // Fin la foto no tiene thumbnail
			// FIN 1

			// 2. COMPROBAMOS SI ESTA EN BD Y EN CASO CONTRARIO LA INSERTAMOS
			$id_foto = "/$directorio_trabajo/$foto";

			$ssql = "SELECT * FROM foto
				   WHERE id_foto = '$id_foto'
				   LIMIT 1;";

			$result = mysql_query($ssql, $conexion);
			$info_foto = mysql_fetch_object($result);
				  
			if (!$info_foto) // La foto no esta en BD
			{
			echo "LA FOTO CON ID $id_foto NO EXISTE EN BD. SE INSERTA<br/>";

			$ssql = "INSERT INTO foto (id_foto) VALUES ('$id_foto')";
			mysql_query($ssql, $conexion);

			$exif = exif_read_data($directorio_trabajo."/".$foto, 0, true);

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

			} // Fin la foto no esta en BD
			else // La foto si esta en BD
			{
			$ciudad[$j] = $info_foto->ciudad;
			$region[$j] = $info_foto->region;
			$pais[$j] = $info_foto->pais;
			$publica[$j] = $info_foto->es_publica;
			}
			// FIN 2  
								
			// Creacion de la lista para la tabla html        
			$lista_fotos[$j]=$foto;
			$path[$j]="$directorio_trabajo"; // Para encontrar el directorio en el buche html
			//$autor[$j]=$author;
				 
			$j++;        
				 
			} // Fin del bucle de fotos
	   }
	   else
	   {
			?>
			<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
			<div id="anyo_fotos">
			<b>ACTIVAR PERMISOS</b><br/><br/>
			<form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
				<input class="button" type="submit" value=" ACTIVAR PERMISOS "/>
				<input name="activar_permisos" id="activar_permisos" type="hidden" value="yes" />
				<input name="directorio_trabajo" id="directorio_trabajo" type="hidden" value="<? echo $directorio_trabajo; ?>" />
				<input name="directorio_thumbnails" id="directorio_xml" type="hidden" value="<? echo "$directorio_trabajo/thumbnails"; ?>" />
			</form>
			<br/>
			</div>
		   <img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
			<?
			exit -2;
	   }
	   
	         
	   
	          
	} // Fin bucle autores

   ?>
    <h3 style="text-align: center"><? echo str_replace("_", " ", $album); ?></h3>
      
 
   <table>
   <tr>
	 <?       

	   $long_lista_fotos = count($lista_fotos);
	   $primera = 0;
	   $ultima = $long_lista_fotos;
	      
	   if ($autor != "todas") // Si se ha elegido todas no hay separacion por paginas
	   {  
	   
	      // CREACION DE TOPES DE LAS FOTOS Y PARTICION EN PAGINAS	         
	      if ($long_lista_fotos > tope_fotos_pagina) // Mas del tope de fotos -> mas de una página
	      {
	         $num_pagina=$_GET['pag'];
	         $primera = $num_pagina * tope_fotos_pagina;
	         $ultima = $primera + tope_fotos_pagina;
	                         
	         if ($num_pagina != 0) // Si no es la primera, ponemos el enlace a anterior
	         {
	            $anterior = $num_pagina-1;
				?>
	            <td><a style="color:#FFFFFF; text-decoration: none;" href="album_admin.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>&amp;pag=<? echo $anterior; ?>"> &lt;&lt; Anterior</a>
				</td>
				<?
	         }
	         else
			 {
			 ?>
	            <td></td>
			 <?
			 }

	         $primera_bonito = $primera+1;
	         if ($ultima >= $long_lista_fotos)
	            $ultima = $long_lista_fotos; 
			
			 ?>	
	         <td align="right"><strong>(<? echo $primera_bonito; ?> - <? echo $ultima; ?>) </strong></td>
	         <td align="left"><strong>de <? echo $long_lista_fotos; ?></strong></td>
	         <?
	         if ($ultima < $long_lista_fotos)  // Si no son las ultimas, ponemos el enlace siguiente
	         {
	            $siguiente = $num_pagina+1;
				?>
	            <td align="right"><a style="color:#FFFFFF; text-decoration: none;" href="album_admin.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>&amp;pag=<? echo $siguiente; ?>">Siguiente &gt;&gt;</a></td>
				<?
	         }
			 else
			 {
			 ?>
	            <td></td>
			  <?
	      	 }
		  } // Fin mas del tope
	   } // Fin se ha elegido todas
	   else // Para que ponga el numero de fotos en todas
	   {
	   ?>
	       <td></td>
	       <td align="right"><strong><? echo $long_lista_fotos; ?></strong></td>
	       <td align="left">fotos</td>
	       <td></td>
		<?
	   }
	   

	   for ($i=$primera; $i<$ultima; $i++) // Bucle de la tabla de fotos
	   {        
	      if (is_int($i/tope_fotos_fila)) // Siguiente fila cada tope fotos
	      {
		  	?>
	         </tr>
	         <tr align="center">
			<?
	      }
	   ?>     
	      <td class="thumbnail"> 
		  <a href="foto_admin.php?album=<? echo $path[$i]; ?>&amp;autor=<? echo $autor; ?>&amp;foto=<? echo $lista_fotos[$i]; ?>&amp;pag=<? echo $num_pagina; ?>">
		  <?
		  $estilo_marco = ($publica[$i]) ? "marco_thumbnail_publica" : "marco_thumbnail";
		  
		  if (file_exists("$directorio_trabajo/thumbnails/$lista_fotos[$i]")) // La foto tiene thumbnail => Lo mostramos
		  {
		  ?>
		  <img class="<? echo $estilo_marco; ?>" src="<? echo $path[$i]; ?>/thumbnails/<? echo $lista_fotos[$i]; ?>" alt="<? echo  $lista_fotos[$i]; ?>" />
		  <?
		  }
		  else // La foto NO tiene thumbnail (no ha podido crearse) => Reducimos la propia imagen
		  {
		  ?>
		  <img class="<? echo $estilo_marco; ?>" src="<? echo $path[$i]; ?>/<? echo $lista_fotos[$i]; ?>" alt="<? echo $lista_fotos[$i]; ?>" width="160px" height="120px"/>
		  <?
		  }
		  ?>
		  </a><br/>

	<?	  
	      if (($ciudad[$i] != "") && ($ciudad[$i] != "0\n"))
	      {
	          $lugar = "$ciudad[$i], $region[$i], $pais[$i]";
		      $ciudad_bonita = str_replace("+", " ", $ciudad[$i]);
	?>
	         <a style="color:#FFFFFF; text-decoration:none;" href="http://maps.google.com/maps?hl=en&amp;q=<? echo $lugar; ?>" target="_blank"><? echo $ciudad_bonita; ?></a><br/>
		<?	 
	      }
?>
		  <a style="color:#FFFFFF; text-decoration:none;" href="?publicar=yes&id_foto=/<? echo $path[$i]; ?>/<? echo $lista_fotos[$i]; ?>&album=<? echo $album; ?>&autor=<? echo $autor; ?>&pag=<? echo $num_pagina; ?>">PUBLICAR</a>
 		  <a style="color:#FFFFFF; text-decoration:none;" href="?privar=yes&id_foto=/<? echo $path[$i]; ?>/<? echo $lista_fotos[$i]; ?>&album=<? echo $album; ?>&autor=<? echo $autor; ?>&pag=<? echo $num_pagina; ?>">PRIVAR</a>
<?
	      if ((isset($_SESSION['user_name'])) && ($_SESSION['user_name'] == "admin"))
		  {
	 	     echo "$lista_fotos[$i]<br/>";
	         //echo "<br/><a href=\"administracion.php?tabla=foto&amp;id=albums/$album/$autor/$lista_fotos[$i]\"><small>Eliminar foto</small></a><br/>";
	      }
		  ?>
	      </td>
		  <?
	   }  
	   ?>
	   </tr>
	   <tr>
	   <?
       if ($num_pagina != 0) // Si no es la primera, ponemos el enlace a anterior
       {
	       $anterior = $num_pagina-1;
	       echo "<td><a style=\"color:#FFFFFF; text-decoration: none;\" href=\"album_admin.php?album=$album&amp;autor=$autor&amp;pag=$anterior\"> &lt;&lt; Anterior</a></td>";
       }
		else
		{
		?>
			<td></td>
		<?
		}
		?>
		<td></td>
		<td></td>
<?			
	   if ($ultima < $long_lista_fotos)  // Si no son las ultimas, ponemos el enlace siguiente
	   {
	       $siguiente = $num_pagina+1;
	       echo "<td align=\"right\"><a style=\"color:#FFFFFF; text-decoration: none;\" href=\"album_admin.php?album=$album&amp;autor=$autor&amp;pag=$siguiente\">Siguiente &gt;&gt;</a></td>";
	   }
	   else
	   {
	   ?>
			<td></td>
		
	   <?
	   }
	   ?>
	   </tr>
	   </table>
	   
	   <?
		$dir = $path[$i-1];
       ?>
	   
		<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
		<div id="anyo_fotos" style="text-align:left; padding-left: 0.2em;">
	    <b>Formulario para etiquetar el lugar </b><br/>
	    <form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
		   <p><input class="pass" type="text" name="directorio" id="directorio" size="70" maxlength="100" value="<? echo $dir; ?>" tabindex="1" />
	       <label for="directorio">Directorio de las fotos a etiquetar (requerido)</label></p>
	       <p><input class="pass" type="text" name="primera" id="primera" size="50" maxlength="50" tabindex="2" />
	       <label for="primera">Primera foto (requerido)</label></p>
	       <p><input class="pass" type="text" name="ultima" id="ultima" size="50" maxlength="50" tabindex="3" />
	       <label for="ultima">Ultima foto (requerido)</label></p>
	       <p><input class="pass" type="text" name="ciudad" id="ciudad" size="50" maxlength="50" tabindex="4" />
	       <label for="ciudad">Ciudad (requerido)</label></p>
	       <p><input class="pass" type="text" name="region" id="region" size="50" maxlength="50" tabindex="6" />
	       <label for="region">Region</label></p>
	       <p><input class="pass" type="text" name="pais" id="pais" size="50" maxlength="50" tabindex="6" />
	       <label for="pais">Pais (requerido)</label></p>
	       <br/>
	       <input class="button" type="submit" value=" Etiquetar "/>
	    </form>
		
		<b>Formulario para hacer fotos publicas </b><br/>
	    <form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
		   <p><input class="pass" type="text" name="directorio" id="directorio" size="70" maxlength="100" value="<? echo $dir; ?>" tabindex="7" />
	       <label for="directorio">Directorio de las fotos a publicar (requerido)</label></p>
	       <p><input class="pass" type="text" name="primera" id="primera" size="50" maxlength="50" tabindex="8" />
	       <label for="primera">Primera foto (requerido)</label></p>
	       <p><input class="pass" type="text" name="ultima" id="ultima" size="50" maxlength="50" tabindex="9" />
	       <label for="ultima">Ultima foto (requerido)</label></p>
	       <br/>
		   <input type="hidden" id="publicar" name="publicar" value="publicar"/>
	       <input class="button" type="submit" value=" Publicar "/>
	    </form>
		
		<b>Formulario para hacer fotos privadas </b><br/>
	    <form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
		   <p><input class="pass" type="text" name="directorio" id="directorio" size="70" maxlength="100" value="<? echo $dir; ?>" tabindex="10" />
	       <label for="directorio">Directorio de las fotos a privar (requerido)</label></p>
	       <p><input class="pass" type="text" name="primera" id="primera" size="50" maxlength="50" tabindex="11" />
	       <label for="primera">Primera foto (requerido)</label></p>
	       <p><input class="pass" type="text" name="ultima" id="ultima" size="50" maxlength="50" tabindex="12" />
	       <label for="ultima">Ultima foto (requerido)</label></p>
	       <br/>
		   <input type="hidden" id="privar" name="privar" value="privar"/>
	       <input class="button" type="submit" value=" Privar "/>
	    </form>
		
	   <br/>
	   </div>
	   <img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>
	   
	   <img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
		<div id="anyo_fotos">
	    <b>Generacion de los XML para el FLASH<br/> (asegurarse que las fotos ya tienen lugar etiquetado, si procede)</b><br/><br/>
		<form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
		    <input class="button" type="submit" value=" GENERAR XML "/>
			<input name="generar_xml" id="generar_xml" type="hidden" value="yes" />
			<input name="album" id="album" type="hidden" value="<? echo $album; ?>" />
			<input name="autor" id="autor" type="hidden" value="<? echo $autor; ?>" />
		</form>
		<br/>
		</div>
	   <img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>

	   <img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
		<div id="anyo_fotos">
	    <b>Generacion de los XML PUBLICOS para el FLASH<br/> (asegurarse que las fotos ya tienen lugar etiquetado, si procede)</b><br/><br/>
		<form name="admin" action="administracion.php" method="post" enctype="multipart/form-data">
		    <input class="button" type="submit" value=" GENERAR XML PUBLICO "/>
			<input name="generar_xml_publico" id="generar_xml_publico" type="hidden" value="yes" />
			<input name="album" id="album" type="hidden" value="<? echo $album; ?>" />
			<input name="autor" id="autor" type="hidden" value="<? echo $autor; ?>" />
		</form>
		<br/>
		</div>
	   <img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>	   
	   
	   
</div>
<? include ('include/firma.html') ?>

</body>
</html>
