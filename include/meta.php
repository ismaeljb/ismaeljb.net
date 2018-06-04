 <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
 <link rel="stylesheet" type="text/css" href="css/estilos_2011.css"/>
<?php
function using_ie()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = False;
    if(preg_match('/MSIE/i',$u_agent))
    {
        $ub = True;
    }
   
    return $ub;
}

if (using_ie())
	$css = "css/estilos_2011_ie.css";
else
	$css = "css/estilos_2011_noie.css";
?>
 <link rel="stylesheet" type="text/css" href="<? echo $css; ?>"/>
 <link rel="shortcut icon" href="img/logo_jb_2011.gif" type="image/gif" />
 <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />

 <link rel="start"
        href="index.php" />
  <meta name="author"
        content="Ismael JB" />
  <meta name="title"
        content="Pagina personal de JB" />
  <meta content="Pagina personal de JB"
        http-equiv="title" />
  <meta name="description"
        content=
        "Esta es la página personal de JB donde se pueden encontrar fotos, enlaces, y noticias sobre la vida del autor." />
  <meta name="keywords"
        xml:lang="es"
        content=
        "Esta es la página personal de JB donde se pueden encontrar fotos, enlaces, y noticias sobre la vida del autor." />
  <meta name="keywords"
        xml:lang="en"
        content=
        "personal web photos php mysql ismaeljb JB" />
