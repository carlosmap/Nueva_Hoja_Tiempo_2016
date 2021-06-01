<?php
 	session_start();
	include "funciones.php";
	//Busca el nombre del usuario que entró
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
	
	//Busca el nombre del proyecto
	$sql="SELECT nombre FROM Proyectos WHERE (id_proyecto = $pro)";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$nomProy = $fil[nombre];
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Reporte de las actividades de cada usuario</title>
<script>
var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
	<div id="Image20783687" style="position:absolute; left:19px; top:9px; width:154px; height:43px; z-index:5">
	<img src="picsI/Image20783687.gif" width="154" height="43" border="0" name="Image_Image20783687"></div>
	<div id="Layer3" style="position:absolute; left:-39px; top:60px; width:687px; height:22px; z-index:3">
	<img src="picsI/GreenRoundedImage3_0.gif" width="687" height="22" border="0" name="Image_Layer3"></div>
	<div id="Layer2" style="position:absolute; left:648px; top:60px; width:81px; height:36px; z-index:2">
	<img src="picsI/GreenRoundedImage2_0.gif" width="81" height="36" border="0" name="Image_Layer2"></div>
	<div id="Layer12" style="position:absolute; left:404px; top:-2px; width:295px; height:62px; z-index:1">
	<img src="picsI/GreenRoundedImage12_0.gif" width="295" height="62" border="0" name="Image_Layer12"></div>
<br>
<br>	
<br>	
<br>
<?php
echo "<a href=javascript:history.back()-1>Regresar</a>";
echo "<br><br>";
$nombUsrConsultado =strtoupper($nombUsrConsultado);
$apelUsrConsultado =strtoupper($apelUsrConsultado);
echo "Programación detallada de "."$nombUsrConsultado ".$apelUsrConsultado."";
echo " Proyecto: ".strtoupper($nomProy)."";


?>
<br>

<?php
	echo "<table><tr><td> </td></tr></table>
	<table border=1><tr bgcolor=#CCCCCC><td><strong>MACROACTIVIDAD</strong>
	</td><td><strong>NOMBRE DE LA ACTIVIDAD</strong></td><td><strong>Fecha Inicio</strong></td><td><strong>Fecha Fin</strong></td><td><strong>CLASE TIEMPO</strong></td><td><strong>HORAS PROGRAMADAS</strong></td>
	<td><strong>HORAS REPORTADAS</strong></td><td><strong>REMANENTE</strong></td></tr>";
	
		/*$sql="SELECT Actividades.id_proyecto AS id_proyecto, Actividades.nombre AS nombreActividad, ISNULL(Actividades.macroactividad, 'SIN') AS macroactividad, 
        Asignaciones.clase_tiempo AS claseTiempo, Asignaciones.unidad AS unidad, Asignaciones.tiempo_asignado, ISNULL(SUM(Horas.horas_registradas),0) AS reportado , Actividades.fecha_inicio as feI, Actividades.fecha_fin as feF
		FROM Actividades INNER JOIN Asignaciones ON Actividades.id_proyecto = Asignaciones.id_proyecto AND Actividades.id_actividad = Asignaciones.id_actividad LEFT OUTER JOIN
		Horas ON Asignaciones.id_proyecto = Horas.id_proyecto AND Asignaciones.id_actividad = Horas.id_actividad AND 
		Asignaciones.unidad = Horas.unidad AND Asignaciones.clase_tiempo = Horas.clase_tiempo
		GROUP BY Actividades.id_proyecto, Actividades.nombre, Actividades.macroactividad, Asignaciones.clase_tiempo, Asignaciones.unidad, 
		Asignaciones.tiempo_asignado, Actividades.fecha_inicio, Actividades.fecha_fin
		HAVING (Actividades.id_proyecto = $pro) AND (Asignaciones.unidad = $und)";
		//a la consulta le quite el
		//WHERE     (Horas.fecha BETWEEN '$fi' AND '$ff')			
		//echo $sql;*/
		
		$sql="SELECT     Actividades.id_proyecto AS id_proyecto, Actividades.nombre AS nombreActividad, 
		ISNULL(Actividades.macroactividad, 'SIN') AS macroactividad, 
		Asignaciones.clase_tiempo AS claseTiempo, Asignaciones.unidad AS unidad, Asignaciones.tiempo_asignado, ISNULL(SUM(Horas.horas_registradas), 
		0) AS reportado, Actividades.fecha_inicio AS feI, Actividades.fecha_fin AS feF, Proyectos.id_estado
		FROM   Actividades INNER JOIN	Asignaciones ON Actividades.id_proyecto = Asignaciones.id_proyecto AND 
		Actividades.id_actividad = Asignaciones.id_actividad INNER JOIN
		Proyectos ON Actividades.id_proyecto = Proyectos.id_proyecto LEFT OUTER JOIN
		Horas ON Asignaciones.id_proyecto = Horas.id_proyecto AND Asignaciones.id_actividad = Horas.id_actividad AND 
		Asignaciones.unidad = Horas.unidad AND Asignaciones.clase_tiempo = Horas.clase_tiempo
		GROUP BY Actividades.id_proyecto, Actividades.nombre, Actividades.macroactividad, Asignaciones.clase_tiempo,
		Asignaciones.unidad, 
		Asignaciones.tiempo_asignado, Actividades.fecha_inicio, Actividades.fecha_fin, Proyectos.id_estado
		HAVING      (Actividades.id_proyecto = $pro) AND (Asignaciones.unidad = $und) AND (Proyectos.id_estado = 2)";
		
		include "validaUsrBd.php";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$mActividad = strtoupper($reg[macroactividad]);
			$fiAct		= $reg[feI];
			$ffAct		= $reg[feF];
			$nActividad = strtoupper($reg[nombreActividad]);
			$cTiempo    = strtoupper($reg[claseTiempo]);
			$tProgramado = strtoupper($reg[tiempo_asignado]);
			$tReportado = strtoupper($reg[reportado]);
			$tRemanente = $tProgramado - $tReportado;
			echo "<tr bgcolor=#FFFFCC><td>$mActividad</td><td>$nActividad</td><td>$fiAct</td><td>$ffAct</td><td>$cTiempo</td><td>$tProgramado</td><td>$tReportado</td><td>$tRemanente</td></tr>";
		}
	

?>

</table>
</body>
</html>
