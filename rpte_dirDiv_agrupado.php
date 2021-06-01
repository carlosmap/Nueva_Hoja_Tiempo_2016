<?php
 	session_start();
	include "funciones.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Usuarios agrupados por división</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>
	var newwindow;
	function muestraventana(url)
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
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 445px;">
		<div align="center"> REPORTE AGRUPADO POR USUARIOS </div>
</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>
<BR />
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  	 <tr><td></td></tr>
   	 <tr><td></td></tr>
     <td class="TituloUsuario">Reporte de usuarios asignados a una división</td>
</table>

<div style="position:absolute; left:3px; top:129px;">
<table class='TxtTabla'><tr><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','rpte_director_division.php');return document.MM_returnValue" value="  Atras   "></a></td>
<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a></td>
</table>
<?php
		$sql="SELECT Usuarios.unidad, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido
		FROM Usuarios INNER JOIN Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento
		INNER JOIN Divisiones ON Departamentos.id_division = Divisiones.id_division
		WHERE (Departamentos.id_division = $dirDiv) order by nombre";
		
		
		echo "<br>";
		//Extrae los usuarios
		include "validaUsrBd.php";
		
		//titulo
		echo "<table border=1 width=85% class='TxtTabla'><tr bgcolor=#CCCCCC><td><strong>Nombre/Apellido</strong></td><td><strong>Proyecto</strong><td><strong>Macroactividad</strong>
		</td><td><strong>Nombre de la Actividad</strong></td><td><strong>Clase Tiempo</strong></td><td><strong>Horas Programadas</strong></td>
		<td><strong>Horas Reportadas</strong></td><td><strong>Remanente</strong></td></tr>";
		
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$unidad = strtoupper($reg[unidad]);
			$nombre = strtoupper($reg[nombre]);
			$apellido = strtoupper($reg[apellido]);
			
			echo "<tr bgcolor=#FFFFCC><td> </td><td> </td>
			<td> </td><td> </td><td> </td>
			<td> </td><td> </td>
			<td> </td></tr>";

			
			//Ahora extrae los proyectos
			$sql="SELECT DISTINCT Proyectos.nombre AS proyecto, Proyectos.id_proyecto as id_proyecto
			FROM Asignaciones INNER JOIN Usuarios ON Asignaciones.unidad = Usuarios.unidad INNER JOIN
			Proyectos ON Asignaciones.id_proyecto = Proyectos.id_proyecto WHERE(Asignaciones.unidad = $unidad)";
			
			$ap1 = mssql_query($sql);
			while($reg = mssql_fetch_array($ap1)){
				$proyecto = strtoupper($reg[proyecto]);
				$id_proy = strtoupper($reg[id_proyecto]);
				
				if($proyecto!="ENFERMEDADES" and $proyecto!="VACACIONES" and
				$proyecto!="ACCIDENTES DE TRABAJO" and $proyecto!="PERMISOS PACTO" and
				$proyecto!="LICENCIAS" and  $proyecto!="SANCIONES" and $proyecto!="AUSENCIAS" and
				$proyecto!="GASTOS GENERALES" and $proyecto!="INSCRIPCIONES"){ 

					$sql="SELECT     Actividades.id_proyecto AS id_proyecto, Actividades.nombre AS nombreActividad, 
						ISNULL(Actividades.macroactividad, 'SIN') AS macroactividad, 
						Asignaciones.clase_tiempo AS claseTiempo, Asignaciones.unidad AS unidad, 
						Asignaciones.tiempo_asignado, ISNULL(SUM(Horas.horas_registradas), 
						0) AS reportado, Proyectos.id_estado FROM         Actividades INNER JOIN
						Asignaciones ON Actividades.id_proyecto = Asignaciones.id_proyecto AND 
						Actividades.id_actividad = Asignaciones.id_actividad INNER JOIN
						Horas ON Asignaciones.id_proyecto = Horas.id_proyecto AND 
						Asignaciones.id_actividad = Horas.id_actividad AND 
						Asignaciones.unidad = Horas.unidad AND Asignaciones.clase_tiempo = Horas.clase_tiempo INNER JOIN
						Proyectos ON Actividades.id_proyecto = Proyectos.id_proyecto
						GROUP BY Actividades.id_proyecto, Actividades.nombre, Actividades.macroactividad,
						Asignaciones.clase_tiempo, Asignaciones.unidad, 
						Asignaciones.tiempo_asignado, Proyectos.id_estado
						HAVING      (Actividades.id_proyecto = $id_proy) AND (Asignaciones.unidad = $unidad) AND (Proyectos.id_estado = 2)";
						$ap2 = mssql_query($sql);
					//WHERE     (Horas.fecha BETWEEN '$fi' AND '$ff')
					if(mssql_num_rows($ap2)>0){
						while($reg = mssql_fetch_array($ap2)){
							$mActividad = strtoupper($reg[macroactividad]);
							$nActividad = strtoupper($reg[nombreActividad]);
							$cTiempo    = strtoupper($reg[claseTiempo]);
							$tProgramado = strtoupper($reg[tiempo_asignado]);
							$tReportado = strtoupper($reg[reportado]);
							$tRemanente = $tProgramado - $tReportado;
							echo "<tr bgcolor=#E1DFCC><td>$nombre $apellido</td><td><center>$proyecto</center></td>
							<td><center>$mActividad</center></td><td>$nActividad</td><td><center>$cTiempo</center></td>
							<td><center>$tProgramado</center></td><td><center>$tReportado</center></td>
							<td><center>$tRemanente</center></td></tr>";
						}
						
					}
					
				}
			}
		}
		echo "</table>";	

?>
</div>
</body>
</html>
