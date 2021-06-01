<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

if ($pMes == "") {
//	$mesActual=3; //el mes que quiere gonzalo
//	$AnoActual=2008; //el año que quiere gonzalo
	$mesActual=date("m"); //el mes actual
	$AnoActual=date("Y"); //el año actual	
}
else {
	$mesActual= $pMes; //el mes seleccionado
	$AnoActual= $pAno; //el año seleccionado
}

//Trae la información del usuario seleccionado
@mssql_select_db("HojaDeTiempo",$conexion);
$sql="Select * from usuarios where unidad = " .  $cualUnidad ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$munidad = $reg[unidad];
	$mNombre = ucwords(strtolower($reg[apellidos])) . ", " . ucwords(strtolower($reg[nombre])) ;
}

//Trae la información de los proyectos en los que posee asignación el usuario seleccionado
// a partir del mes mes y año actual en adelante
$sql2="select A.id_proyecto, P.nombre, P.codigo, P.cargo_defecto ";
$sql2=$sql2." from asignaciones A, Proyectos P ";
$sql2=$sql2." where A.id_proyecto = P.id_proyecto ";
$sql2=$sql2." and A.unidad = " .  $cualUnidad ;
$sql2=$sql2." and (month(A.fecha_inicial) >= ". $mesActual ;
$sql2=$sql2." and year(A.fecha_inicial) >= " . $AnoActual . ") ";
$sql2=$sql2." group by A.id_proyecto, P.nombre, P.codigo, P.cargo_defecto   ";
$cursor2 = mssql_query($sql2);

//Encontrar las fechas mínima y máxima en que la persona seleccionada tiene asignaciones
//para establecer la cantidad de meses a dibujar
//y mostrar las programaciones de esa persona.
$sql3="select min(fecha_inicial) FechaMin, max(fecha_inicial) FechaMax,  ";
$sql3=$sql3." coalesce(datediff(month, min(fecha_inicial), max(fecha_inicial)),0) meses ";
$sql3=$sql3." from Asignaciones ";
$sql3=$sql3." where unidad = ". $cualUnidad ;
$sql3=$sql3." and (month(fecha_inicial) >= " . $mesActual . " and year(fecha_inicial) >= " . $AnoActual . ") ";
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) {	 
	$pMinMes = date("m", strtotime($reg3[FechaMin])) ;
	$pMinVigencia = date("Y", strtotime($reg3[FechaMin])) ;
	$pMaxMes = date("m", strtotime($reg3[FechaMax])) ;
	$pMaxVigencia = date("Y", strtotime($reg3[FechaMax])) ;
	$pplazo = $reg3[meses] + 1;
}

?>
<html>
<head>
<title>Validaci&oacute;n de la Hoja de Tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winPUsu";
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:783px; height:38px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota2">Programación del usuario</td>
    </tr>
  </table>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>






<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Informaci&oacute;n del usuario </td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr class="TituloTabla2">
    <td width="20%">Unidad</td>
    <td>Nombre</td>
  </tr>
  <tr class="TxtTabla">
    <td width="20%"><? echo $munidad ; ?></td>
    <td><? echo $mNombre; ?></td>
  </tr>
</table>	</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td width="25%" align="right" class="TxtTabla">TR = Tiempo Real </td>
    <td width="25%" align="right" class="TxtTabla2">TT = Tiempo Tentativo </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Programaci&oacute;n</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr class="TituloTabla2">
    <td width="20%">Proyecto</td>
    <td width="5%">C&oacute;digo</td>
			<td width="1%">&nbsp;</td>
			<? 
			$mesActual = $pMinMes ;
			$anoActual = $pMinVigencia ;
			for ($e=1; $e<=$pplazo ; $e++) { 
				switch ($mesActual) {
				case 1:
					$nombreMes="Ene";
					break;
				case 2:
					$nombreMes="Feb";
					break;
				case 3:
					$nombreMes="Mar";
					break;
				case 4:
					$nombreMes="Abr";
					break;
				case 5:
					$nombreMes="May";
					break;
				case 6:
					$nombreMes="Jun";
					break;
				case 7:
					$nombreMes="Jul";
					break;
				case 8:
					$nombreMes="Ago";
					break;
				case 9:
					$nombreMes="Sep";
					break;
				case 10:
					$nombreMes="Oct";
					break;
				case 11:
					$nombreMes="Nov";
					break;
				case 12:
					$nombreMes="Dic";
					break;
				}
			?>
    <td><? echo $nombreMes . "-" . $anoActual;  ?></td>
	<? 
	$mesActual = $mesActual + 1;
	if ($mesActual > 12) {
		$mesActual = 1;
		$anoActual = $anoActual + 1;
	}
	} ?>
  </tr>
  <? while ($reg2=mssql_fetch_array($cursor2)) {  ?>
  <tr class="TxtTabla">
    <td width="20%" rowspan="2"><? echo  ucwords(strtolower($reg2[nombre])) ; ?></td>
    <td width="5%" rowspan="2"><? echo $reg2[codigo] . "." . $reg2[cargo_defecto] ; ?></td>
	<td width="1%"><strong>TR</strong></td>
	<? 
	$mesActualP = $pMinMes ;
	$anoActualP = $pMinVigencia ;
	for ($p=1; $p<=$pplazo ; $p++) { 
		$phorasAsignadas = "";
		//Trae la programación real de Asignaciones para el usuario seleccionado
		//el mes y año del periodo
		$sqlPR="select sum(tiempo_asignado) horasAsignadas ";
		$sqlPR=$sqlPR." from asignaciones where unidad = " . $cualUnidad ;
		$sqlPR=$sqlPR." and month(fecha_inicial) =" . $mesActualP ;
		$sqlPR=$sqlPR." and year(fecha_inicial) =" . $anoActualP ;
		$sqlPR=$sqlPR." and id_proyecto =" . $reg2[id_proyecto] ;
		$cursorPR = mssql_query($sqlPR);
		if ($regPR=mssql_fetch_array($cursorPR)) {	 
			$phorasAsignadas = $regPR[horasAsignadas];
		}

	?>
	
    <td align="right">
	<? 
	if (trim($phorasAsignadas) != "") {
		echo $phorasAsignadas  ; 
	}
	?>	
	</td>
	<? 
	$mesActualP = $mesActualP + 1;
	if ($mesActualP > 12) {
		$mesActualP = 1;
		$anoActualP = $anoActualP + 1;
	}
	} ?>
  </tr>
  <tr class="TxtTabla">
    <td width="1%" class="TxtTabla2"><strong>TT</strong></td>
  	<? 
	$mesActualP = $pMinMes ;
	$anoActualP = $pMinVigencia ;
	for ($t=1; $t<=$pplazo ; $t++) { 
		$phorasProgDiv = "";
		
		//Trae la programación tentativa totalizada para el usuario por cada proyecto por cada periodo
		$sqlPT="select sum(horasProgramadas) horasProgDiv from ProgSumaGlobalUsu  ";
		$sqlPT=$sqlPT." where id_proyecto =" . $reg2[id_proyecto] ;
		$sqlPT=$sqlPT." and unidad =" . $cualUnidad ; 
		$sqlPT=$sqlPT." and mes =" . $mesActualP ;
		$sqlPT=$sqlPT." and vigencia =" . $anoActualP ;
		$cursorPT = mssql_query($sqlPT);
		if ($regPT=mssql_fetch_array($cursorPT)) {	 
			$phorasProgDiv = $regPT[horasProgDiv];
		}
	?>
    <td align="right" class="TxtTabla2">
	<? 
	if (trim($phorasProgDiv) != "") {
		echo $phorasProgDiv  ; 
	}
	else {
		echo "&nbsp;";
	}
	?>	
	</td>
	<? 
		$mesActualP = $mesActualP + 1;
		if ($mesActualP > 12) {
			$mesActualP = 1;
			$anoActualP = $anoActualP + 1;
		}
	} ?>
  </tr>
  <? } ?>
</table>	</td>
  </tr>
</table>

<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_callJS('window.close()')" value="Cerrar programaci&oacute;n" /></td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td class="copyr">Ingetec S.A. @ 2007 </td>
      </tr>
</table>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
