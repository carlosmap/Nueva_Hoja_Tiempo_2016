<?php
 	session_start();
 	include "funciones.php";
	for($i=1;$i<=31;$i++){
		$sessArrayHdia[$i]="";
	}
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
<script>
var newwindow;
function vermuestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes, toolbar=yes');
	if (window.focus) {newwindow.focus()}
}
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Facturación al proyecto</title>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<? include("bannerArriba.php") ; ?>
<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
	<div align="center"> FACTURACI&Oacute;N AL PROYECTO </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
    <td class="TituloUsuario">Usuarios que facturaron al proyecto <?php echo $nomPro;?></td>
</table>

<div style="position:absolute; left:7px; top:138px;">


<?php
	echo "<table class='TxtTabla'><tr><td> </td></tr></table>
	<table border=1 class='TxtTabla'><td><strong>Aprobación</strong>
	</td><td><strong>Nombre del usuario</strong></td><td><strong>Apellido</strong></td></tr>";
	
		$sql = "SELECT DISTINCT Usuarios.unidad, Usuarios.nombre AS nombre, 
		Usuarios.apellidos AS apellido, Proyectos.id_proyecto
		FROM Usuarios INNER JOIN
		Horas ON Usuarios.unidad = Horas.unidad INNER JOIN
		Proyectos ON Horas.id_proyecto = Proyectos.id_proyecto
		WHERE (Horas.fecha BETWEEN '$fiAprobacion' AND '$ffAprobacion') AND (Horas.id_proyecto = '$idPy')
		AND (Proyectos.id_director = '$laUnidad' or id_coordinador = '$laUnidad')";

		include "validaUsrBd.php";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$unid = strtoupper($reg[unidad]);
			$nombU = strtoupper($reg[nombre]);
			$apelU = strtoupper($reg[apellido]);
			$idProyecto = strtoupper($reg[id_proyecto]);
			echo "<tr><td><a href='javascript:vermuestraventana(\"hdetiempo-aprobDivision.php?und=$unid&&codProyecto=$idProyecto\")'>
			Ver hoja</a></td><td>$nombU</td><TD>$apelU</TD></tr>";
		}
?>

</table>
<table>
<tr><td> </td></tr>
<tr><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','dir_proyecto_aprobaciones.php');return document.MM_returnValue" value="  Atras   "></a></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a></td></tr>
</table>

</div>
</body>
</html>
