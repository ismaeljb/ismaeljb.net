


<hr></hr>

<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
<div id="anyo_fotos" style="text-align: left; padding-left: 0.4em; width: 54em;">
Haciendo click en los siguientes enlaces se crean los thumbnails (si no existen) y se inserta la informacion de cada foto en la BD (si no existe ya). Tambien da acceso al taggeo de la ubicacion de las fotos.<br/>
<!-- ADMINISTRACION DE ALBUMS -->
	   <?
	   // Esto lo unico que hace es poner un enlace a album_admin para poder tagear el lugar del album
	   
	   $directorio = scandir('albums');
	   array_shift ($directorio); // Quitamos . y ..
	   array_shift ($directorio);
	   $directorio = array_filter ($directorio, "es_album");
	   
	   foreach ($directorio as $album) // Bucle albums
	   {
	   	   $autores_album = scandir("albums/".$album."/");
		   // Quitamos . y ..
		   array_shift ($autores_album);
		   array_shift ($autores_album);
		   $autores_album = array_filter ($autores_album, "es_autor");
		   
		   // Si tiene alguna foto publica le cambiamos el color
		   $ssql = "SELECT count(*) as cuenta FROM foto WHERE es_publica = '1' AND album = '$album'";
		   $result =  mysql_query($ssql, $conexion);
		   $cuenta = mysql_fetch_array($result);
		   if ($cuenta["cuenta"] > 0)
		   {
				$color = "color: #EEE8AA";
		   }
		   else
		   {
				$color = "color: #FFFFFF";
		   }
		   
		   ?>
		   <span style="<? echo $color; ?>">
		   <? echo $album; ?>
		   </span>
		   <?
		   
		   foreach ($autores_album as $autor) // Bucle autores
	   	   {
				
				$permisos = substr(sprintf('%o', @fileperms("albums/".$album."/".$autor."/")), -4);
			   if ($permisos <> "0311" )
			   {
					$color = "color: #F79BB2";
			   }
			   else
			   {
					$color = "color: #FFFFFF";
			   }
		   
			   ?>
			   &spades; <a style="<? echo $color; ?>" href="album_admin.php?album=<? echo $album; ?>&amp;autor=<? echo $autor; ?>"><? echo $autor; ?></a>
			   <?
			   // Contamos el numero de fotos publicas
			   $ssql = "SELECT count(*) as cuenta FROM foto WHERE es_publica = '1' AND album = '$album' AND autor = '$autor'";
			   $result =  mysql_query($ssql, $conexion);
			   $cuenta = mysql_fetch_array($result);
			   if ($cuenta["cuenta"] > 0)
			   {
					echo "(".$cuenta["cuenta"]." pubs)";
			   }
			   
			   
		  } // FIN bucle autores
		  ?>
		  <br/>
		  <?
	   }// FIN bucle albums
	   ?>
	   
</div>
<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>	   
<hr></hr>

<img src="css/top_fotos.gif" alt="fondo menu top css" align="texttop"/>
<div id="anyo_fotos" style="text-align: left; padding-left: 0.4em; width: 54em;">

<form name="admin3" action="administracion.php" method="post" enctype="multipart/form-data">
  
    <b>Formulario para crear un album</b><br/><br/>

       <p style="font-size:1em"><input class="pass" type="text" name="nombre_album" id="nombre_album" size="20" maxlength="20" tabindex="9" />
       <label for="nombre_album">Nombre del álbum (requerido, SIN espacios)</label></p>
       <p style="font-size:1em"><input class="pass" type="text" name="mes_album" id="mes_album" size="3" maxlength="3" tabindex="10" />
       <label for="mes_album">Mes (requerido: con 3 letras, Mar, Abr, Jun, Jul...)</label></p>
       <p style="font-size:1em"><input class="pass" type="text" name="anyo_album" id="anyo_album" size="4" maxlength="4" tabindex="11" />
       <label for="anyo_album">Anyo (requerido: AAAA, de 1900 hasta 2500)</label></p>
       <p style="font-size:1em"><input class="pass" type="text" name="autor_album" id="autor_album" size="20" maxlength="20" tabindex="12" />
       <label for="autor_album">Autor (requerido, SIN espacios)</label></p>
       <!--
	   <input type="hidden" name="MAX_FILE_SIZE" value="300000000">
       <p style="font-size:1em"><input type="file" name="album" id="album" size="90" maxlength="90" tabindex="13" />
       <label for="album">Archivo comprimido con todas las fotos (requerido: max 300MB)</label></p>
	   -->

       <input class="button" type="submit" value=" Enviar " />

</form>
<br/>
</div>
<img src="css/bottom_fotos.gif" alt="fondo menu bottom css" align="absbottom"/>


<hr></hr>
<form name="admin4" action="administracion.php" method="post">
  <table cellspacing="0" cellpadding="0" border="0">
    <tr><td colspan="2"><b>Formulario para meter albums en usuarios</b><br/><br/></td></tr>
    <tr>
       <td align="left" colspan="2">
	   <select name="album" id="album">
	   <?
	   $directorio = scandir('albums');
	   array_shift ($directorio); // Quitamos . y ..
	   array_shift ($directorio);
	   
	   foreach ($directorio as $album)
	   {
	   ?>
	   
	   <option value="<? echo $album; ?>"><? echo $album; ?></option>
	   
	   <?
	   }
	   ?>
	   </select>
	  
	   <select name="usuario" id="usuario">
	   <?
	   $ssql = "SELECT * FROM admin
				WHERE id_admin > '1'";
	   $result = mysql_query($ssql, $conexion);
	   
	   while ($admin = mysql_fetch_object($result))
	   {
	   ?>
	   
	   <option value="<? echo $admin->alias; ?>"><? echo $admin->alias; ?></option>
	   
	   <?
	   }
	   ?>
	   </select>
	   <input type="hidden" id="accion" name="accion" />
	   <input type="button" value=" Insertar " onclick="javascript:document.forms['admin4'].elements['accion'].value='Insertar'; submit(this.form);" />
   	   <input type="button" value=" Eliminar " onclick="javascript:document.forms['admin4'].elements['accion'].value='Eliminar'; submit(this.form);" />
       </td>
    </tr>
	
	  <?
	   $ssql = "SELECT * FROM acceso order by user, album";
	   $result = mysql_query($ssql, $conexion);
	   
	   $i=1;
	   while ($acceso = mysql_fetch_object($result))
	   {
	   		$acc_aux[$i] = $acceso->user;
	   		
	   		if (($acc_aux[$i] != $acc_aux[$i-1]) && ($i > 1))
			{
				?>
				<tr>
					<td colspan="2"><hr></hr></td>				
				</tr>
				<?
			
			}
	   ?>
	   	<tr>
			<td style="font-size: 0.8em;"><? echo $acceso->user; ?></td>
			<td style="font-size: 0.8em;"><? echo $acceso->album; ?></td>
		</tr>
	   <?

   
	   		$i++;
	   }
		?>
		<tr>
			<td colspan="2"><hr></hr></td>				
		</tr>
		<?


	   $ssql = "SELECT user, count(*) as num_albums FROM acceso GROUP BY user ORDER BY user";
	   $result = mysql_query($ssql, $conexion);
	   
	   while ($acceso = mysql_fetch_object($result))
	   {
	   ?>
	   	<tr>
			<td style="font-size: 0.8em; font-weight: bold;"><? echo $acceso->user; ?></td>
			<td style="font-size: 0.8em; font-weight: bold;"><? echo $acceso->num_albums; ?></td>
		</tr>
	   <?
	   }

		?>
		<tr>
			<td colspan="2"><hr></hr></td>				
		</tr>
		<?

	   $ssql = "SELECT * FROM admin where id_admin > 1";
	   $result = mysql_query($ssql, $conexion);
	   
	   while ($admin = mysql_fetch_object($result))
	   {
	   ?>
	   	<tr>
			<td style="font-size: 0.8em;"><? echo $admin->alias; ?></td>
			<td style="font-size: 0.8em;"><? echo $admin->pass; ?></td>
		</tr>
	   <?
	   }
?>	   
	   
	   
  </table>
</form>
<hr></hr>

