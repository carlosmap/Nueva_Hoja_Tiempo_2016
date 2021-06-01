<?php
 	session_start();
	include "funciones.php";
	
	//Busca el nombre del usuario que se le solicitan las actividades
	
	include "validaUsrBd.php";
	$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$und')";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categ = $fil[categoria];
		$nombUsrConsultado=$fil[nombre];
		$apelUsrConsultado=$fil[apelli];
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<title>Reporte para el usuario</title>
<script>
var newwindow;
function vermuestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body class='fondo' leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> REPORTE USUARIO </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>
	
<br>
<br>
<?php 
//echo "<table><tr><td><a href=reportes.php>Cambiar fecha</a></td><td>          </td><td><a href=frm-GrabaTiempo.php>Página principal</a></td></tr></table>";
$nombUsrConsultado =strtoupper($nombUsrConsultado);
$apelUsrConsultado =strtoupper($apelUsrConsultado);
echo "Programación de "."$nombUsrConsultado "."$apelUsrConsultado. "."Categoría: "."$categ";

?>
<br>

<?php
	echo "<table class='TxtTabla'><tr><td> </td></tr></table>
	<table border=1 CLASS='TxtTabla'><tr bgcolor=#CCCCCC><td><strong>MÁS DETALLES</strong>
	</td><td><strong>NOMBRE DEL PROYECTO</strong></td></tr>";
	
		$sql="SELECT DISTINCT Proyectos.nombre AS proyecto, Proyectos.id_proyecto as id_proyecto
		FROM Asignaciones INNER JOIN Usuarios ON Asignaciones.unidad = Usuarios.unidad INNER JOIN
        Proyectos ON Asignaciones.id_proyecto = Proyectos.id_proyecto WHERE(Asignaciones.unidad = $und)
        and (Proyectos.id_estado = 2)";
		
		include "validaUsrBd.php";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$proyecto = strtoupper($reg[proyecto]);
			$id_proy = strtoupper($reg[id_proyecto]);
			//if($proyecto!="ENFERMEDADES" and $proyecto!="VACACIONES" and $proyecto!="ACCIDENTES DE TRABAJO" and $proyecto!="PERMISOS PACTO" and $proyecto!="LICENCIAS" and  $proyecto!="SANCIONES" and $proyecto!="AUSENCIAS" and $proyecto!="GASTOS GENERALES" and $proyecto!="INSCRIPCIONES"){ 
				echo "<tr bgcolor=#FFFFCC><td><a href='javascript:vermuestraventana(\"rpte_actividades.php?und=$und&&pro=$id_proy\")'>Ver detalles</a></td><td>$proyecto</td></tr>";
			//}		
		}

?>

</table>
</body>
</html>
