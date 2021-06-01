<?php
 	session_start();
	include "funciones.php";
	include "validaUsrBd.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Reporte para el director de departamento</title>
<script>

function desabilitar(act){
	//al seleccionar un boton desabilita los demas
	document.rpteDepartamento.personal_acargo.checked=false
	document.rpteDepartamento.proyectos_acargo.checked=false
	document.rpteDepartamento.mi_programacion.checked=false
	document.rpteDepartamento.aprobacion.checked=false
	act.checked=true
}

var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes');
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

<h3>Reporte director de departamento</h3>
<br>
<form name=rpteDepartamento action="rpte_director_departamento.php" method="post">
<table>
	<tr><td>Reporte de personal a su cargo</td><td><input name="personal_acargo" type="radio" value="1" ></td></tr>
	<?php
		$sql = "select * from proyectos where id_director='$laUnidad'";
		$ap = mssql_query($sql);
		if(mssql_num_rows($ap)>0){
			echo "<tr><td>Reporte de proyectos a mi cargo</td><td><input name='proyectos_acargo' type='radio' value='2' onclick='desabilitar(this)'></td></tr>";
			echo "<tr><td>Aprobación facturado a mi proyecto</td><td><input name='aprobFact' type='radio' value='6' onclick='desabilitar(this);'></td></tr>";
		}
	?>

	<tr><td>Mi programación</td><td><input name="mi_programacion" type="radio" value="4" onclick='desabilitar(this)'></td></tr>
	<tr><td>Aprobación Hoja de Tiempo</td><td><input name="aprobacion" type="radio" value="5" onclick='desabilitar(this)'></td></tr>
	<?php
		if($esRevisor==1){
			echo "<tr><td>Revisar hojas de tiempo a mi cargo</td><td><input name='revisionhdet' type='radio value='7' onclick='desabilitar(this)'></td></tr>";
		}

	?>
	<tr><td> </td><td> </td></tr>
	<tr><td><input type=submit name=enviar value="Generar Reporte"></td><td></td></tr>
	<tr><td> </td><td> </td></tr>
	<tr><td><a href='frm-GrabaTiempo.php'>Regresar a la página principal</a><td></td></tr>
</table>
</form>

<?php

if($enviar=="Generar Reporte"){
	if($personal_acargo == 1){
	echo "<table><tr><td>Las siguientes personas están registradas en su departamento</td></tr>
	<tr><td> </td></tr></table>
	<table border=1><tr bgcolor=#CCCCCC><td><strong>APELLIDOS</strong></td><TD><strong>NOMBRES</strong></TD><TD><strong>REMANENTE TOTAL</strong></TD><TD><strong>MÁS DETALLES</strong></TD></tr>";

		//La variable dirDep es definida en reportes.php
		$sql="SELECT Usuarios.unidad, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido
		FROM Usuarios INNER JOIN Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento
		WHERE (Departamentos.id_departamento = $dirDep  and usuarios.retirado is null) order by apellido";


		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$unidad = strtoupper($reg[unidad]);
			$nombre = strtoupper($reg[nombre]);
			$apellido = strtoupper($reg[apellido]);
			//para cada usuario calcula las horas totales remanentes
			$sql = "SELECT SUM(Asignaciones.tiempo_asignado) AS totalProgramado, Asignaciones.unidad
			FROM Asignaciones INNER JOIN
			Proyectos ON Asignaciones.id_proyecto = Proyectos.id_proyecto
			WHERE     (Asignaciones.unidad = $unidad) AND (Proyectos.id_estado = 2) and Asignaciones.tiempo_asignado > 0
			GROUP BY Asignaciones.unidad";
			$ap1 = mssql_query($sql);
			$reg1 = mssql_fetch_array($ap1);
			$totProgramado = $reg1[totalProgramado];
			//Ahora lo reportado
			$sql = "SELECT     unidad, SUM(horas_registradas) AS totalRegistrado
			FROM  Horas	where horas_registradas > 0 and unidad=$unidad GROUP BY unidad";
			$ap2 = mssql_query($sql);
			$reg2 = mssql_fetch_array($ap2);
			$totRegistrado = $reg2[totalRegistrado];
			$totalRemanente = $totProgramado - $totRegistrado;

			echo "<tr bgcolor=#FFFFCC><td>$apellido</td><td>$nombre</td><td>$totalRemanente</td><td><A href=javascript:muestraventana('rpte_usuario.php?und=$unidad')>Ver detalles</a></td></tr>";
		}
	}elseif($proyectos_acargo == 2){
		$sql = "select * from proyectos where id_director='$laUnidad'";
		$ap = mssql_query($sql);
		if(mssql_num_rows($ap)>0){
			$id=1;
		}else{
			$id=-1;
		}

		if($id == 1){
			echo "<script>location.href = 'rpte_dir_proyecto_mnu.php'</script>";
		}else{
			echo "<script>alert('Usted no tiene proyectos a cargo')</script>";
		}
	}elseif($mi_programacion==4){
		echo "<script>location.href = 'rpte_usuario_individual.php'</script>";
	}elseif($aprobacion==5){
		echo "<script>location.href='RangoAprobacion.php'</script>";
	}elseif($aprobacion==6){
		$dirDep="";
		echo "<script>location.href='RangoAprobacion.php'</script>";
	}elseif($revisionhdet==7){
		echo "<script>location.href='RangoAprobacion.php'</script>";
	}
}
?>

</table>
</body>
</html>
