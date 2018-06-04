
<? 

function mkdirSafeMode( $dir )
{
	// OJO: Al hacer un ftp_mkdir hay que tener en cuenta que estamos
	// en el directorio raiz del usuario ftp
    $conn_id = ftp_connect( "home216799805.1and1-data.host" );
    if( ftp_login( $conn_id, "u46360356-phpusr", "=f{bbG(Bq&dv" ) )
    {
        if ( ftp_mkdir($conn_id, $dir) )
        {
            ftp_chmod( $conn_id, 0777, $dir );//permisos de lectura/escritura/ejecución
            ftp_close( $conn_id );
            return true;
        }
        else
        {
            ftp_close( $conn_id );
			echo "Ha ocurrido un error al intentar crear el directorio $dir [ftp_mkdir] <br/>";
            return false;
        }
    }
    else
    {
        ftp_close( $conn_id );
		echo "Ha ocurrido un error al intentar crear el directorio $dir [ftp_login] <br/>";
        return false;
    }
}
		
?>
