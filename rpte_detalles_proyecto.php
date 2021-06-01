<?php
 	session_start();
	include "funciones.php";
	include "validaUsrBd.php";
	//Busca el nombre del proyecto
	$sql="SELECT nombre FROM Proyectos WHERE (id_proyecto = $pro)";
	
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$nomProy = $fil[nombre];
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Facturacion al proyecto</title>
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
<div class="TxtNota1" style="position:absolute; left:257px; top:18px; width: 470px;">
	<div align="center"> PERSONAS QUE FACTURARON AL PROYECTO </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
   <td class="TituloUsuario">Detalle de personas que facturaron al proyecto <?php echo "Proyecto: "."<strong>".strtoupper($nomProy).".</strong>";?></td>
</table>

<div style="position:absolute; left:10px; top:150px;">
<table border=1 class="TxtTabla">
<?php
	echo "<tr><td><strong>NOMBRE DEL EMPLEADO</strong>
	</td><td><strong>NOMBRE DE LA ACTIVIDAD</strong></td><td><strong>PROGRAMADO TOTAL</strong></td><td><strong>REPORTADO</strong></td>
	<td><strong>REMANENTE TOTAL</strong></td></tr>";
	
		$sql="SELECT     Proyectos.id_proyecto AS id_proyecto, Actividades.nombre AS nombreActividad, Asignaciones.fecha_inicial AS FechaInicial, 
		Asignaciones.fecha_final AS fechaFinal, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido, 
		Asignaciones.tiempo_asignado AS programado, SUM(Horas.horas_registradas) AS reportado, Proyectos.id_estado
		FROM         Proyectos INNER JOIN
		Actividades ON Proyectos.id_proyecto = Actividades.id_proyecto INNER JOIN
		Asignaciones ON Actividades.id_proyecto = Asignaciones.id_proyecto AND Actividades.id_actividad = Asignaciones.id_actividad INNER JOIN
		Usuarios ON Asignaciones.unidad = Usuarios.unidad INNER JOIN
		Horas ON Asignaciones.id_proyecto = Horas.id_proyecto AND Asignaciones.id_actividad = Horas.id_actividad AND 
		Asignaciones.unidad = Horas.unidad AND Asignaciones.clase_tiempo = Horas.clase_tiempo
		GROUP BY Proyectos.id_proyecto, Actividades.nombre, Asignaciones.fecha_inicial, Asignaciones.fecha_final, Usuarios.nombre, Usuarios.apellidos, 
		Asignaciones.tiempo_asignado, Proyectos.id_estado
		HAVING      (Proyectos.id_proyecto = $pro and Proyectos.id_estado=2) order by Usuarios.nombre";
		//WHERE     (Horas.fecha BETWEEN '$fi' AND '$ff')
		
		
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$nomEmp = strtoupper($reg[nombre]);
			$apeEmp = strtoupper($reg[apellido]);
			$nomActi    = strtoupper($reg[nombreActividad]);
			$tProgramado = strtoupper($reg[programado]);
			$tReportado = strtoupper($reg[reportado]);
			$tRemanente = $tProgramado - $tReportado;
			echo "<tr><td>$nomEmp $apeEmp</td><td>$nomActi</td><td>$tProgramado</td><td>$tReportado</td><td>$tRemanente</td></tr>";
		}
?>

</table>

<table>
	<TR><TD> </TD></TR>
<tr><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','rpte_dir_proyecto.php');return document.MM_returnValue" value="  Atras   "></a></td>
<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a>  </td></tr>
</table>
</div>
</body>
</html>
