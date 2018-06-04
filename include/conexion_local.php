<?php
$hostname_conexion = "localhost";//"db34.1and1.es";
$database_conexion = "ismaeljb_ismaeljb";//"db216858134";
$username_conexion = "ismaeljb";//"dbo216858134";
$password_conexion = "liduvina";//"3r6EXJXiuY9N";//3r6EXJXiuY9N

$conexion = mysql_connect($hostname_conexion, $username_conexion, $password_conexion) 
            or  die ("No se ha podido conectar con la BD <br>\n".mysql_error());
mysql_select_db($database_conexion, $conexion) or die('Could not select database.');
?>