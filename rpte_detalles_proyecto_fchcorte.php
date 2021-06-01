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
<title>Reporte con fecha de corte</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>

//funcion para validar la fecha
function esDigito(sChr){
	var sCod = sChr.charCodeAt(0);
	return ((sCod > 47) && (sCod < 58));
}
function valSep(oTxt){
	var bOk = false;
	bOk = bOk || ((oTxt.value.charAt(4) == "-") && (oTxt.value.charAt(7) == "-"));
	bOk = bOk || ((oTxt.value.charAt(4) == "/") && (oTxt.value.charAt(7) == "/"));
	return bOk;
}
function finMes(oTxt){
	var nMes = parseInt(oTxt.value.substr(5, 2), 10);
	var nRes = 0;
	switch (nMes){
		case 1: nRes = 31; break;
		case 2: nRes = 29; break;
		case 3: nRes = 31; break;
		case 4: nRes = 30; break;
		case 5: nRes = 31; break;
		case 6: nRes = 30; break;
		case 7: nRes = 31; break;
		case 8: nRes = 31; break;
		case 9: nRes = 30; break;
		case 10: nRes = 31; break;
		case 11: nRes = 30; break;
		case 12: nRes = 31; break;
	}
	return nRes;
}
function valDia(oTxt){
	var bOk = false;
	var nDia = parseInt(oTxt.value.substr(8, 2), 10);
	bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));
	return bOk;
}
function valMes(oTxt){
	var bOk = false;
	var nMes = parseInt(oTxt.value.substr(5, 2), 10);
	bOk = bOk || ((nMes >= 1) && (nMes <= 12));
	return bOk;
}
function valAno(oTxt){
	var bOk = true;
	//var nAno = oTxt.value.substr(6);
	var nAno = oTxt.value.substr(0,4);
	bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));
	if (bOk){
		for (var i = 0; i < nAno.length; i++){
			bOk = bOk && esDigito(nAno.charAt(i));
		}
	}
	return bOk;
}
function valFecha(oTxt){
	var bOk = true;
	if (oTxt.value != ""){
		bOk = bOk && (valAno(oTxt));
		bOk = bOk && (valMes(oTxt));
		bOk = bOk && (valDia(oTxt));
		bOk = bOk && (valSep(oTxt));

		if (!bOk){
			alert("Fecha inválida");
			oTxt.value = "";
			oTxt.focus();
		}
	}
}
//fin de funcion para validar la fecha

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
	<div class="TxtNota1" style="position:absolute; left:253px; top:17px; width: 569px;">
		<div align="center">DETALLES DEL PROYECTO CON FECHA DE CORTE</div>
</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
   <td class="TituloUsuario">Detalles proyecto con fecha de corte</td>
</table>

<div style="position:absolute; left:10px; top:150px;">

<form name=reporte action="rpte_detalles_proyecto_fchcorte.php" method="post">
	<input type=hidden name=pro value=<? echo $pro?>>
	<table class="TxtTabla">
		<tr><td>Seleccione la fecha de inicio y final</td></tr>
		<tr><td> </td></tr>
		<tr><td>Fecha Inicial (aaaa/mm/dd)</td><td><input type=text name=fechaInicial onBlur="valFecha(this);"
 		value=<?echo $fechaInicial;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.reporte.fechaInicial);return false;" HIDEFOCUS>
		<img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute;
		left:-500px; top:0px;">
		</iframe>
			</td></tr>
			<tr><td>Fecha Final (aaaa/mm/dd)</td><td><input type=text name=fechaFinal onBlur="valFecha(this);"
			 value=<?echo $fechaFinal;?>>
			<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.reporte.fechaFinal);return false;" HIDEFOCUS>
			<img name="popcal"
			 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
			<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-130 id="gToday:normal1:agenda.js"
			 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute;
			left:-500px; top:0px;">
		</iframe>
		</td></tr>
		<tr><td> </td><td> </td></tr>
		<tr><td><input type=submit class="Boton" name=valor value=generar></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','rpte_dir_proyecto.php');return document.MM_returnValue" value="  Atras   "></a></td></tr>
	</table>
</form>



<?php

if($valor=="generar"){

	if($fechaInicial==""){
		echo "<script>alert('La fecha inicial está en blanco');</script>";
		exit();
	}elseif($fechaFinal==""){
		echo "<script>alert('La fecha final está en blanco');</script>";
		exit();
	}
	/*$mesI = explode("/" , $fechaInicial);
	$mesF = explode("/" , $fechaFinal);

	if($mesI[0] == $mesF[0]){
		echo "<script>location.href='dir_proyecto_aprobaciones.php'</script>";
	}else{
		echo "<script>alert('No esta permitido aprobar periodos de tiempo que involucren varios meses');</script>";
	}*/


	echo "<table class='TxtTabla'><tr><td>Personas que facturaron al proyecto <strong>$nomProy</strong>. </td></tr></table>
	<table border=1  class='TxtTabla'><tr bgcolor=#CCCCCC><td><strong>NOMBRE DEL EMPLEADO</strong></td>
	<td><strong>NOMBRE DE LA ACTIVIDAD</strong></td>
	<td><strong>CLASE DE TIEMPO</strong></td>
	<td><strong>PROGRAMADO TOTAL</strong></td>
	<td><strong>REPORTADO EN EL PERIODO</strong></td>
	<td><strong>REPORTADO TOTAL</strong></td>
	<td><strong>REMANENTE TOTAL</strong></td></tr>";

	//Obtiene el tiempo reportado total. Las dos consultas son iguales, solo que a la primera le quito el condicional de fecha
	//para evitar que el reportado sea obtenido en un perido determinado



	$sql="SELECT     Proyectos.id_proyecto AS id_proyecto, Actividades.nombre AS nombreActividad, actividades.id_actividad,
		Asignaciones.fecha_inicial AS FechaInicial,horas.unidad,
		Asignaciones.fecha_final AS fechaFinal, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido,
		Asignaciones.tiempo_asignado AS programado, horas.clase_tiempo,SUM(Horas.horas_registradas) AS reportado, Proyectos.id_estado
		FROM         Proyectos INNER JOIN
		Actividades ON Proyectos.id_proyecto = Actividades.id_proyecto INNER JOIN
		Asignaciones ON Actividades.id_proyecto = Asignaciones.id_proyecto AND
		Actividades.id_actividad = Asignaciones.id_actividad INNER JOIN
		Usuarios ON Asignaciones.unidad = Usuarios.unidad INNER JOIN
		Horas ON Asignaciones.id_proyecto = Horas.id_proyecto AND Asignaciones.id_actividad = Horas.id_actividad AND
		Asignaciones.unidad = Horas.unidad AND Asignaciones.clase_tiempo = Horas.clase_tiempo
		WHERE     (Horas.fecha BETWEEN '$fechaInicial' AND '$fechaFinal')
		GROUP BY Proyectos.id_proyecto, Actividades.nombre, actividades.id_actividad, Asignaciones.fecha_inicial,
		Asignaciones.fecha_final,
		Usuarios.nombre, Usuarios.apellidos,
		Asignaciones.tiempo_asignado, horas.clase_tiempo,Proyectos.id_estado, horas.unidad
		HAVING      (Proyectos.id_proyecto = $pro and Proyectos.id_estado=2) order by Usuarios.nombre";

	$ap = mssql_query($sql);
		$i=0;
		while($reg = mssql_fetch_array($ap)){
			$idpro = $reg[id_proyecto];
			$nomEmp = strtoupper($reg[nombre]);
			$id_act = $reg[id_actividad];
			$unidadEmpl = $reg[unidad];
			$apeEmp = strtoupper($reg[apellido]);
			$nomActi    = strtoupper($reg[nombreActividad]);
			$tProgramado = $reg[programado];
			$tReportado = $reg[reportado];
			$cltiempo=$reg[clase_tiempo];

			//Obtiene el valor de lo reportado en su totalidad. Buscar realizar esto con la clausula select IN
			$sql2="SELECT     SUM(Horas.horas_registradas) AS reporte
			FROM         horas
			where 	(horas.id_proyecto = $idpro)  AND (Horas.unidad = $unidadEmpl)
			and (horas.id_actividad=$id_act) and (horas.clase_tiempo=$cltiempo)";


			$res = mssql_query($sql2);
			$tRepor = mssql_fetch_array($res);
			$tReportadoTotal = $tRepor[reporte];
				$tRemanente = $tProgramado - $tReportadoTotal;
				echo "<tr><td>$nomEmp $apeEmp</td><td>$nomActi</td>
				<td>$cltiempo</td><td>$tProgramado</td>
				<td>$tReportado</td><td>$tReportadoTotal</td><td>$tRemanente</td></tr>";
		}
}
?>

</table>
</div>

</body>
</html>
