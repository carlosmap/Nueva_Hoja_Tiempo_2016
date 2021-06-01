<?php
 	session_start();
	include "funciones.php";
	include "validaUsrBd.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte para el director de división</title>
<script>
	function MM_goToURL() { //v3.0
	  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
	  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
	}
	
	var newwindow;
	function muestraventana(url)
	{
		newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes');
		if (window.focus) {newwindow.focus()}
	}
	
	function desabilitar(act){
		//al seleccionar un boton desabilita los demas
		document.rpteDivision.personal_acargoGrp.checked=false
		document.rpteDivision.personal_acargo.checked=false
		document.rpteDivision.proyectos_acargo.checked=false
		document.rpteDivision.aprobFact.checked=false
		document.rpteDivision.aprobacion.checked=false
		document.rpteDivision.mi_programacion.checked=false
		act.checked=true
	}
</script>

</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> REPORTE DIRECTOR DIVISIÓN </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
   	 <tr><td> </td></tr>
 	 <tr><td> </td></tr>	 
     <td class="TituloUsuario">Reporte para el director de división. Seleccione la opción deseada</td>
</table>

<div style="position:absolute; left:4px; top:147px;">

<form name=rpteDivision action="rpte_director_division.php" method="post">
<table class="TxtTabla">
	<tr><td>Reporte de personal a su cargo (por persona)</td><td><input name="personal_acargo" type="radio" value="1" onClick="desabilitar(this);"></td></tr>
	<tr><td>Reporte de personal a su cargo (Agrupado)</td><td><input name="personal_acargoGrp" type="radio" value="3" onClick="desabilitar(this);"></td></tr>
	<?php
		$sql = "select * from proyectos where id_director='$laUnidad'";
		$ap = mssql_query($sql);
		if(mssql_num_rows($ap)>0){
			echo "<tr><td>Reporte de proyectos a su cargo</td><td><input name='proyectos_acargo' type='radio' value='2' onclick='desabilitar(this);'></td></tr>";
			echo "<tr><td>Aprobación facturado a mi proyecto</td><td><input name='aprobFact' type='radio' value='6' onclick='desabilitar(this);'></td></tr>";
		}
	?>

	<tr><td>Ver hojas de tiempo</td><td><input name="aprobacion" type="radio" value="5" onClick="desabilitar(this);"></td></tr>
	<tr><td>Mi programación</td><td><input name="mi_programacion" type="radio" value="4" onClick="desabilitar(this);"></td></tr>
	<tr><td> </td><td> </td></tr>
	<tr><td></td><td></td></tr>
	<tr><td> </td><td> </td></tr>
</table>
<table>	
<tr><td><input type=submit class='Boton' name=enviar value="Generar Reporte"></td>
  <td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Atras   "></a></td></tr>
</table>
</form>

<?php

if($enviar=="Generar Reporte"){

	if($personal_acargo == 1){
	echo "<table class='TxtTabla'><tr><td bgcolor=#BBD6F7 width='670'>Nota: Las siguientes personas están registradas en su división y la información desplegada corresponde al total de lo programado vs lo facturado por cada uno de
	los usuarios</td></tr></table>";

	echo "<table border=1 class='TxtTabla'><tr><td><strong>APELLIDOS</strong></td><TD><strong>NOMBRES</strong></TD><TD><strong>REMANENTE TOTAL</strong></TD><TD><strong>MÁS DETALLES</strong></TD></tr>";

		$sql="SELECT Usuarios.unidad, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido
		FROM Usuarios INNER JOIN Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento
		INNER JOIN Divisiones ON Departamentos.id_division = Divisiones.id_division
		WHERE (Departamentos.id_division = $dirDiv  and usuarios.retirado is null) order by apellido";


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

			echo "<tr><td>$apellido</td><td>$nombre</td><td>$totalRemanente</td><td><A href=javascript:muestraventana('rpte_usuario.php?und=$unidad')>Ver detalles</a></td></tr>";
		}
	}elseif($proyectos_acargo == 2){
			echo "<script>location.href = 'rpte_dir_proyecto.php'</script>";
	}elseif($personal_acargoGrp == 3){
		echo "<script>location.href = 'rpte_dirDiv_agrupado.php'</script>";
	}elseif($mi_programacion==4){
		echo "<script>location.href = 'rpte_usuario_individual.php'</script>";
	}elseif($aprobacion==5){
		echo "<script>location.href='RangoAprobacion.php'</script>";
	}elseif($aprobFact==6){
			$dirDiv = "";
			echo "<script>location.href='RangoAprobacion.php'</script>";
	}
}
?>

</table>
</div>
</body>
</html>
