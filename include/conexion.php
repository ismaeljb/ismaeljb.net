<?php
$hostname_conexion = "db431743879.db.1and1.com";//"db34.1and1.es";
$database_conexion = "db431743879";//"db216858134";
$username_conexion = "dbo431743879";//"dbo216858134";
$password_conexion = "3r6EXJXiuY9N";//"3r6EXJXiuY9N";//3r6EXJXiuY9N

$conexion = mysql_connect($hostname_conexion, $username_conexion, $password_conexion) 
            or  die ("No se ha podido conectar con la BD <br>\n".mysql_error());
mysql_select_db($database_conexion, $conexion) or die('Could not select database.');
?>