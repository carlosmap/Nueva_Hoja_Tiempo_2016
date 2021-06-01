<?php
 	session_start();
	include "funciones.php";

	//Busca el nombre del usuario que se le solicitan las actividades
	include "validaUsrBd.php";
	$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$laUnidad')";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categ = $fil[categoria];
		$nombUsrConsultado=$fil[nombre];
		$apelUsrConsultado=$fil[apelli];
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Listado de proyectos para aprobación de tiempo facturado</title>

<script>
	var newwindow;
	function vermuestraventana(url)
	{
		newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes');
		if (window.focus) {newwindow.focus()}
	}
	function MM_goToURL() { //v3.0
	  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
	  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
	}
</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> HOJA DE TIEMPO ELECTR&Oacute;NICA </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  
  </tr>
  	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
   <td class="TituloUsuario">Listado de proyectos en los cuales es Director/Coordinador</td>
</table>

<div style="position:absolute; left:6px; top:142px;">

<?php
	echo "<table class='TxtTabla'><tr><td>El rango de fechas. Desde $fiAprobacion hasta $ffAprobacion</td></tr><tr><td> </td></tr></table>
	
	<table border=1 class='TxtTabla'><tr><td><strong>NOMBRE DEL PROYECTO</strong></td><td><strong>APROBACIONES</strong></td></tr>";

		$sql="SELECT     id_proyecto, nombre
		FROM Proyectos WHERE     (id_director = $laUnidad or id_coordinador = $laUnidad)";

		include "validaUsrBd.php";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$id_proy = strtoupper($reg[id_proyecto]);
			$nomProy = strtoupper($reg[nombre]);
			echo "<tr><td>$nomProy</td><TD><a href='aprobaciones.php?idPy=$id_proy&&nomPro=$nomProy'>Continuar</a></TD></tr>";
		}
?>

</table>
<table>
<tr><td> </td></tr>
<tr><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','RangoAprobacion.php');return document.MM_returnValue" value="  Atras   "></a></td>
<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a>  </td></tr>
</table>

</div>
</body>
</html>
