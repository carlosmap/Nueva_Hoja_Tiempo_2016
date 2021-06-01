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
$sql2="SELECT * FROM ( ";
$sql2=$sql2." 	select A.id_proyecto, P.nombre, P.codigo, P.cargo_defecto ";
$sql2=$sql2." 	from asignaciones A, Proyectos P ";
$sql2=$sql2." 	where A.id_proyecto = P.id_proyecto ";
$sql2=$sql2." 	and A.unidad = " .  $cualUnidad ;
$sql2=$sql2." 	and (month(A.fecha_inicial) >= " . $mesActual ;
$sql2=$sql2." 	and year(A.fecha_inicial) >= ". $AnoActual ." )  ";
$sql2=$sql2." 	UNION ";
$sql2=$sql2." 	Select F.id_proyecto, P.nombre, P.codigo, P.cargo_defecto  ";
$sql2=$sql2." 	from ProgSumaGlobalUsu F, Proyectos P  ";
$sql2=$sql2." 	where F.id_proyecto = P.id_proyecto ";
$sql2=$sql2." 	and F.Unidad =  ". $cualUnidad ;
$sql2=$sql2." 	and (F.mes >= " . $mesActual . " and F.vigencia >= ".$AnoActual.") ";
$sql2=$sql2." 	UNION ";
$sql2=$sql2." 	Select F.id_proyecto, P.nombre, P.codigo, P.cargo_defecto  ";
$sql2=$sql2." 	from ProgAsignaRecursosUsu F, Proyectos P  ";
$sql2=$sql2." 	where F.id_proyecto = P.id_proyecto ";
$sql2=$sql2." 	and F.Unidad = ". $cualUnidad ;
$sql2=$sql2." 	and (F.mes >= ".$mesActual." and F.vigencia >= ".$AnoActual.") ";
$sql2=$sql2." ) A ";
$cursor2 = mssql_query($sql2);

//Encontrar las fechas mínima en que la persona seleccionada tiene asignaciones, ProgSumaGlobalUsu y ProgAsignaRecursosUsu
//para establecer la cantidad de meses a dibujar
//y mostrar las programaciones de esa persona.
$sql3="select * from ( ";
$sql3=$sql3." 	select coalesce(month(min(fecha_inicial)), 0) MesMin, coalesce(year(min(fecha_inicial)), 0) VigenciaMin, ";
$sql3=$sql3." 	coalesce(month(max(fecha_inicial)), 0) MesMax, coalesce(year(max(fecha_inicial)), 0) VigenciaMax  ";
$sql3=$sql3." 	from Asignaciones ";
$sql3=$sql3." 	where unidad = " . $cualUnidad ;
$sql3=$sql3." 	and (month(fecha_inicial) >= ". $mesActual ." and year(fecha_inicial) >= ". $AnoActual .") ";
$sql3=$sql3." 	UNION";
$sql3=$sql3." 	select coalesce(min(mes), 0) MesMin, coalesce(min(vigencia), 0) VigenciaMin, coalesce(max(mes), 0) MesMax, ";
$sql3=$sql3." 	coalesce(max(vigencia), 0) VigenciaMax ";
$sql3=$sql3." 	from ProgSumaGlobalUsu ";
$sql3=$sql3." 	where unidad = " . $cualUnidad ;
$sql3=$sql3." 	and (mes >= ".$mesActual." and vigencia >= ".$AnoActual.") ";
$sql3=$sql3." 	UNION ";
$sql3=$sql3." 	select coalesce(min(mes), 0) MesMin, coalesce(min(vigencia), 0) VigenciaMin, coalesce(max(mes), 0) MesMax, ";
$sql3=$sql3." 	coalesce(max(vigencia), 0) VigenciaMax ";
$sql3=$sql3." 	from ProgAsignaRecursosUsu ";
$sql3=$sql3." 	where unidad = ". $cualUnidad ;
$sql3=$sql3." 	and (mes >= " . $mesActual . " and vigencia >= " . $AnoActual . ")) A ";
$sql3=$sql3." where MesMin <> 0 ";
$sql3=$sql3." order by VigenciaMin asc, MesMin asc ";
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) {	 
	//El periodo inicial viene de primeras
	$pMinMes = $reg3[MesMin] ;
	$pMinVigencia = $reg3[VigenciaMin]  ;
}


//Encontrar las fechas máxima en que la persona seleccionada tiene asignaciones, ProgSumaGlobalUsu y ProgAsignaRecursosUsu
//para establecer la cantidad de meses a dibujar
//y mostrar las programaciones de esa persona.
$sql4="select * from ( ";
$sql4=$sql4." 	select coalesce(month(min(fecha_inicial)), 0) MesMin, coalesce(year(min(fecha_inicial)), 0) VigenciaMin, ";
$sql4=$sql4." 	coalesce(month(max(fecha_inicial)), 0) MesMax, coalesce(year(max(fecha_inicial)), 0) VigenciaMax ";
$sql4=$sql4." 	from Asignaciones ";
$sql4=$sql4." 	where unidad = " . $cualUnidad ;
$sql4=$sql4." 	and (month(fecha_inicial) >= ". $mesActual ." and year(fecha_inicial) >= ". $AnoActual .") ";
$sql4=$sql4." 	UNION ";
$sql4=$sql4." 	select coalesce(min(mes), 0) MesMin, coalesce(min(vigencia), 0) VigenciaMin, coalesce(max(mes), 0) MesMax, ";
$sql4=$sql4." 	coalesce(max(vigencia), 0) VigenciaMax ";
$sql4=$sql4." 	from ProgSumaGlobalUsu ";
$sql4=$sql4." 	where unidad = " . $cualUnidad ;
$sql4=$sql4." 	and (mes >= ".$mesActual." and vigencia >= ".$AnoActual.") ";
$sql4=$sql4." 	UNION ";
$sql4=$sql4." 	select coalesce(min(mes), 0) MesMin, coalesce(min(vigencia), 0) VigenciaMin, coalesce(max(mes), 0) MesMax, ";
$sql4=$sql4." 	coalesce(max(vigencia), 0) VigenciaMax ";
$sql4=$sql4." 	from ProgAsignaRecursosUsu ";
$sql4=$sql4." 	where unidad =". $cualUnidad ;
$sql4=$sql4." 	and (mes >= ".$mesActual." and vigencia >= ".$AnoActual.")) A ";
$sql4=$sql4." where MesMax <> 0 ";
$sql4=$sql4." order by VigenciaMax desc , MesMax desc ";
$cursor4 = mssql_query($sql4);
if ($reg4=mssql_fetch_array($cursor4)) {	 
	//El periodo mayor viene en el primer registro
	$pMaxMes = $reg4[MesMax] ;
	$pMaxVigencia = $reg4[VigenciaMax] ;
}

//echo $pMaxMes . "<br>";
//echo $pMaxVigencia . "<br>";


//ENCONTRAR la cantidad de meses entre los periodos
$pplazo = 0;
$fechaIni=$pMinMes."/01/".$pMinVigencia;
$fechaMax=$pMaxMes."/01/".$pMaxVigencia;
//echo $fechaIni . "<br>";
//echo $fechaMax . "<br>";

//calculo timestam de las dos fechas 
$timestamp1 = mktime(0,0,0,$pMinMes,"1",$pMinVigencia); 
$timestamp2 = mktime(0,0,0,$pMaxMes,"1",$pMaxVigencia); 
//$timestamp2 = mktime(4,12,0,$pMaxMes,"1",$pMaxVigencia); 
//resto a una fecha la otra 
$segundos_diferencia = $timestamp1 - $timestamp2; 
//convierto segundos en dias
$dias_diferencia = $segundos_diferencia / (60 * 60 * 24) ; 
//obtengo el valor absoulto de los meses (quito el posible signo negativo) 
$dias_diferencia = abs($dias_diferencia); 
$dias_diferencia = $dias_diferencia /30;
//quito los decimales a los meses de diferencia 
$dias_diferencia = floor($dias_diferencia); 
$pplazo = $dias_diferencia + 1; 


//Define un array para totalizar Tiempo Real por mes, e inicializa todo el array en 0
$totalTR[0] = 0;
for ($t=1; $t<=$pplazo ; $t++) { 
	$totalTR[$t] = 0;
}

//Define un array para totalizar Tiempo Tentativo por mes, e inicializa todo el array en 0
$totalTT[0] = 0;
for ($t=1; $t<=$pplazo ; $t++) { 
	$totalTT[$t] = 0;
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
			//Asigna el valor que retorne la consulta y lo almacena en la matriz conservando lo que traía
			$totalTR[$p] = $totalTR[$p] + $regPR[horasAsignadas] ;
		}
	?>
    <td align="right">
	<? if (trim($phorasAsignadas) != "") {
			echo $phorasAsignadas  ; 
		} ?>	
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
/*		$sqlPT="select sum(horasProgramadas) horasProgDiv from ProgSumaGlobalUsu  ";
		$sqlPT=$sqlPT." where id_proyecto =" . $reg2[id_proyecto] ;
		$sqlPT=$sqlPT." and unidad =" . $cualUnidad ; 
		$sqlPT=$sqlPT." and mes =" . $mesActualP ;
		$sqlPT=$sqlPT." and vigencia =" . $anoActualP ; */

		$sqlPT="SELECT SUM(horasProgDiv) horasProgDiv FROM ( ";
		$sqlPT=$sqlPT." 	select coalesce(sum(horasProgramadas), 0) horasProgDiv from ProgSumaGlobalUsu  " ;
		$sqlPT=$sqlPT." 	where id_proyecto =" . $reg2[id_proyecto] ;
		$sqlPT=$sqlPT." 	and unidad = " . $cualUnidad ; 
		$sqlPT=$sqlPT." 	and mes =" . $mesActualP ;
		$sqlPT=$sqlPT." 	and vigencia = " . $anoActualP ;
		$sqlPT=$sqlPT." 	UNION ALL " ;
		$sqlPT=$sqlPT." 	select coalesce(sum(horasProgramadas), 0) horasProgDiv from ProgAsignaRecursosUsu  " ;
		$sqlPT=$sqlPT." 	where id_proyecto = " . $reg2[id_proyecto] ;
		$sqlPT=$sqlPT." 	and unidad =" . $cualUnidad ; 
		$sqlPT=$sqlPT." 	and mes =" . $mesActualP ;
		$sqlPT=$sqlPT." 	and vigencia =" . $anoActualP ;
		$sqlPT=$sqlPT." ) A " ;
		$cursorPT = mssql_query($sqlPT);
		if ($regPT=mssql_fetch_array($cursorPT)) {	 
			$phorasProgDiv = $regPT[horasProgDiv];
			//Asigna el valor que retorne la consulta y lo almacena en la matriz conservando lo que traía
			$totalTT[$t] = $totalTT[$t] + $regPT[horasProgDiv];
		}
	?>
    <td align="right" class="TxtTabla2">
	<? 
	if ((trim($phorasProgDiv) != "") AND (trim($phorasProgDiv) != "0")) {
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
    <tr class="TituloTabla">
    <td colspan="3">Total</td>
    <? 
	$mesActualP = $pMinMes ;
	$anoActualP = $pMinVigencia ;
	for ($t=1; $t<=$pplazo ; $t++) { 
	?>
    <td align="right" ><? echo $totalTR[$t] + $totalTT[$t] ; ?></td>
	<? 
		$mesActualP = $mesActualP + 1;
		if ($mesActualP > 12) {
			$mesActualP = 1;
			$anoActualP = $anoActualP + 1;
		}
	} ?>
  </tr>
    <tr class="TituloTabla">
      <td colspan="3">Horas Laborales </td>
      <? 
	$mesActualP = $pMinMes ;
	$anoActualP = $pMinVigencia ;
	$numHoras = "";
	for ($t=1; $t<=$pplazo ; $t++) { 
		//Definir la cantidad de horas por mes a mostrar
		$hSql="select vigencia, mes, hOficina, hCampo, hCat42, diasLaborales ";
		$hSql=$hSql." from horasydiaslaborales ";
		$hSql=$hSql." where Vigencia = " . $anoActualP ;
		$hSql=$hSql." and mes = " . $mesActualP ;
		$hCursor = mssql_query($hSql);
		if ($hReg=mssql_fetch_array($hCursor)) {	 
			$numHoras = $hReg[hOficina] ;
		}
		else {
			$numHoras = "" ;		
		}
	?>
	  <td align="right" class="TxtTabla2" ><? echo $numHoras; ?></td>
	  <? 
		$mesActualP = $mesActualP + 1;
		if ($mesActualP > 12) {
			$mesActualP = 1;
			$anoActualP = $anoActualP + 1;
		}
	} ?>
    </tr>

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
        <td><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onClick="MM_callJS('window.close()')" value="Cerrar programaci&oacute;n" /></td>
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
