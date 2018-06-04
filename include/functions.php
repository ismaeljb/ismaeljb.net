<?

// Funciones comunes

function es_autor ($entrada) // Para filtrar el array de autores
{
   return (!(stripos($entrada, '.rar')) && !(stripos($entrada, '.zip')) && !(stripos($entrada, '.db')) && ($entrada<>'xml'));
}

function es_foto ($entrada) // Para filtrar el array de fotos
{
	return stripos($entrada, '.jpg');
}

function es_album ($entrada) // Para filtrar el array de fotos
{
	return !(stripos($entrada, 'html')) && (stripos($entrada, 'Backup_Originales') === FALSE) && (stripos($entrada, 'uploads') === FALSE);
}
	


?>
