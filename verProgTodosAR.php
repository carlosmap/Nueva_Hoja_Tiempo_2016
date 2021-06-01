<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador = C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

$mesActual=date("m"); //el mes actual
$AnoActual=date("Y"); //el año actual	

//1Abr2008
//encuentra el periodo de la fecha inicial minima de todos los usuarios
$qSql="SELECT * FROM ( ";
$qSql=$qSql." 	select coalesce(month(min(fecha_inicial)), 0) Mes, coalesce(year(min(fecha_inicial)), 0) Vigencia ";
$qSql=$qSql." 	from Asignaciones ";
$qSql=$qSql." 	where (month(fecha_inicial) >= ".$mesActual." and year(fecha_inicial) >= ".$AnoActual.")  ";
$qSql=$qSql."	AND UNIDAD IN ";
$qSql=$qSql." 	(select distinct unidad from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where id_proyecto = ". $cualProyecto ;
$qSql=$qSql." 	and unidadProgramador =". $laUnidad .") ";
$qSql=$qSql." 	UNION ";
$qSql=$qSql." 	select Mes, Vigencia from ProgSumaGlobalUsu ";
$qSql=$qSql." 	where (mes >= ".$mesActual." and vigencia >= ".$AnoActual.") ";
$qSql=$qSql." 	and UNIDAD IN ";
$qSql=$qSql." 	(select distinct unidad from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." 	and unidadProgramador =".$laUnidad .")";
$qSql=$qSql." 	UNION ";
$qSql=$qSql." 	select Mes, Vigencia from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where (mes >= ".$mesActual." and vigencia >= ".$AnoActual.") " ;
$qSql=$qSql." 	and UNIDAD IN ";
$qSql=$qSql." 	(select distinct unidad from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." 	and unidadProgramador =".$laUnidad .")";
$qSql=$qSql." ) A ";
$qSql=$qSql." WHERE Mes <> 0 ";
$qSql=$qSql." ORDER BY Vigencia asc, Mes asc ";
$cursorQ = mssql_query($qSql);
if ($regQ=mssql_fetch_array($cursorQ)) {	
	$pMesInicial = $regQ[Mes];
	$pAnoInicial = $regQ[Vigencia];
}

//encuentra el periodo de la fecha máxima de todos los usuarios
$qSql="SELECT * FROM ( ";
$qSql=$qSql." 	select coalesce(month(max(fecha_inicial)), 0) Mes, coalesce(year(max(fecha_inicial)), 0) Vigencia ";
$qSql=$qSql." 	from Asignaciones ";
$qSql=$qSql." 	where (month(fecha_inicial) >= ".$mesActual." and year(fecha_inicial) >= ".$AnoActual.")  ";
$qSql=$qSql."	AND UNIDAD IN ";
$qSql=$qSql." 	(select distinct unidad from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where id_proyecto = ". $cualProyecto ;
$qSql=$qSql." 	and unidadProgramador =". $laUnidad .") ";
$qSql=$qSql." 	UNION ";
$qSql=$qSql." 	select Mes, Vigencia from ProgSumaGlobalUsu ";
$qSql=$qSql." 	where (mes >= ".$mesActual." and vigencia >= ".$AnoActual.") ";
$qSql=$qSql." 	and UNIDAD IN ";
$qSql=$qSql." 	(select distinct unidad from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." 	and unidadProgramador =".$laUnidad .")";
$qSql=$qSql." 	UNION ";
$qSql=$qSql." 	select Mes, Vigencia from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where (mes >= ".$mesActual." and vigencia >= ".$AnoActual.") " ;
$qSql=$qSql." 	and UNIDAD IN ";
$qSql=$qSql." 	(select distinct unidad from ProgAsignaRecursosUsu ";
$qSql=$qSql." 	where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." 	and unidadProgramador =".$laUnidad .")";
$qSql=$qSql." ) A ";
$qSql=$qSql." WHERE Mes <> 0 ";
$qSql=$qSql." ORDER BY Vigencia desc, Mes desc ";
$cursorQ = mssql_query($qSql);
if ($regQ=mssql_fetch_array($cursorQ)) {	
	$pMesFinal = $regQ[Mes];
	$pAnoFinal = $regQ[Vigencia];
}

//ENCONTRAR la cantidad de meses entre los periodos
$pplazo = 0;
$fechaIni=$pMesInicial."/01/".$pAnoInicial;
$fechaMax=$pMesFinal."/01/".$pAnoFinal;
//echo $fechaIni . "<br>";
//echo $fechaMax . "<br>";

//calculo timestam de las dos fechas 
$timestamp1 = mktime(0,0,0,$pMesInicial,"1",$pAnoInicial); 
$timestamp2 = mktime(0,0,0,$pMesFinal,"1",$pAnoFinal); 
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


//Trae los usuarios que han sido programados para el proyecto seleccionado y el usuario activo, con sus correspondientes salarios 
//(el último salario registrado en la tabla UsuariosSalario)
$sql4="select distinct P.unidad, U.nombre, U.apellidos, S.salario ";
$sql4=$sql4." from ProgAsignaRecursosUsu P, usuarios U, usuariosSalario S ";
$sql4=$sql4." where P.unidad = U.unidad ";
$sql4=$sql4." and P.id_proyecto = " . $cualProyecto ;
$sql4=$sql4." and P.unidadProgramador ="  . $laUnidad ;
$sql4=$sql4." and P.unidad = S.unidad ";
$sql4=$sql4." and S.fecha = (select max(fecha) maxFecha from usuariosSalario where unidad = P.unidad) ";
$sql4=$sql4." order by  U.apellidos ";
$cursor4 = mssql_query($sql4);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--

window.name="winProgTodos";

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos por Divisi&oacute;n</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 521px; height: 30px;">
Programaci&oacute;n de personal</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right">&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Empleados que participan en el proyecto </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="5%">Unidad</td>
            <td width="20%">Nombre</td>
            <td width="1%">&nbsp;</td>
			<? 
			$mesActual = $pMesInicial ;
			$anoActual = $pAnoInicial ;
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
         <? while ($reg4=mssql_fetch_array($cursor4)) {  ?>
          <tr class="TxtTabla">
            <td width="5%" rowspan="2"><? echo $reg4[unidad] ; ?></td>
            <td width="20%" rowspan="2"><? echo  ucwords(strtolower($reg4[apellidos])) . ", " .  ucwords(strtolower($reg4[nombre])) ; ?></td>
            <td width="1%" align="right"><strong>TR</strong></td>
            <? 
			$mesActualP = $pMesInicial ;
			$anoActualP = $pAnoInicial ;
			for ($p=1; $p<=$pplazo ; $p++) { 
				$phorasAsignadas = "";
				//Trae la programación para cada periodo del usuario en todos los proyectos
				//para el mes y año actual
				//calse de tiempo 1 o 2
				
				$sql5="SELECT sum(tiempo_Asignado) horasAsignadas FROM ASIGNACIONES ";
				$sql5=$sql5." WHERE unidad = " . $reg4[unidad];
				$sql5=$sql5." and (clase_tiempo = 1 OR clase_tiempo = 2)	" ;
				$sql5=$sql5." AND MONTH(fecha_inicial) = ".$mesActualP." and YEAR(fecha_inicial) = ".$anoActualP ;
				$cursor5 = mssql_query($sql5);
				if ($reg5=mssql_fetch_array($cursor5)) {	 
					$phorasAsignadas = $reg5[horasAsignadas];
				}
			?>
	            <td align="right">
				<? 
				if (trim($phorasAsignadas ) != "") {
					echo $phorasAsignadas ; 
				}
				?>				</td>
			<? 
			$mesActualP = $mesActualP + 1;
			if ($mesActualP > 12) {
				$mesActualP = 1;
				$anoActualP = $anoActualP + 1;
			}
			} ?>
          </tr>
          <tr class="TxtTabla">
            <td align="right" class="TxtTabla2"><strong>TT</strong></td>
            <? 
			$mesActualP = $pMesInicial ;
			$anoActualP = $pAnoInicial ;
			for ($p=1; $p<=$pplazo ; $p++) { 
				$phorasProg = "";
				//Trae la programación para cada periodo del usuario en todos los proyectos
				//para el mes y año actual
				$sql5="SELECT SUM(horasProg) horasProg FROM ( ";
				$sql5=$sql5." 	select sum(horasProgramadas) horasProg from ProgSumaGlobalUsu " ;
				$sql5=$sql5." 	WHERE unidad = " . $reg4[unidad];
				$sql5=$sql5." 	AND (mes = ".$mesActualP." and vigencia = ".$anoActualP.") " ;
				$sql5=$sql5." 	UNION ALL " ;
				$sql5=$sql5." 	select sum(horasProgramadas) horasProg from ProgAsignaRecursosUsu " ;
				$sql5=$sql5." 	WHERE unidad =  " . $reg4[unidad];
				$sql5=$sql5." 	AND (mes = ".$mesActualP." and vigencia = ".$anoActualP.") " ;
				$sql5=$sql5." ) A " ;
				$cursor5 = mssql_query($sql5);
				if ($reg5=mssql_fetch_array($cursor5)) {	 
					$phorasProg = $reg5[horasProg];
				}
			?>
            <td align="right" class="TxtTabla2">
<? 
				if (trim($phorasProg ) != "") {
					echo $phorasProg ; 
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
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_callJS('window.close()')" value="Cerrar Programaci&oacute;n" />
    </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
