<?php
 	session_start();
	include "funciones.php";
	//Busca el nombre del usuario que se le solicitan las actividades
	//Este script muestra los proyectos en los cuales est´ça programado un usuario normal
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte para el usuario</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
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
		<div align="center">  </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  		<TR><TD> </TD></TR>
		<TR><TD> </TD></TR>
   <td class="TituloUsuario">Usted tiene programación o ha sido programado en los siguientes proyectos</td>
</table>



<div style="position:absolute; left:10px; top:150px;">
<table class="TxtTabla"  border="1">
<tr><td>MÁS DETALLES</td>
<td>NOMBRE DEL PROYECTO</td></tr>
<?php
	$sql="SELECT DISTINCT Proyectos.nombre AS proyecto, Proyectos.id_proyecto AS id_proyecto, Proyectos.id_estado
	FROM         Asignaciones INNER JOIN
	Usuarios ON Asignaciones.unidad = Usuarios.unidad INNER JOIN
	Proyectos ON Asignaciones.id_proyecto = Proyectos.id_proyecto
	WHERE     (Asignaciones.unidad = $laUnidad) AND (Proyectos.id_estado = 2)";
	
	include "validaUsrBd.php";
	$ap = mssql_query($sql);
	
	while($reg = mssql_fetch_array($ap)){
		$proyecto = strtoupper($reg[proyecto]);
		$id_proy = strtoupper($reg[id_proyecto]);
		echo "<tr><td><a href='javascript:vermuestraventana(\"rpte_actividades_individual.php?und=$laUnidad&&pro=$id_proy\")'>Ver detalles</a></td><td>$proyecto</td></tr>";
	}
?>


</table>

<table>
<tr><td></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a>  </td></tr>
</table>
</div>
</body>
</html>
