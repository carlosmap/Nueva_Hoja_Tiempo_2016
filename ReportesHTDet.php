<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
	
//13Julio2007
//Traer la información del proyecto seleccionado
$sql="Select * ";
$sql=$sql." from Proyectos " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDProyecto = $reg[id_proyecto];
	$elProyecto = $reg[nombre];
	$elCodigo = $reg[codigo];
	$elCargoDef = $reg[cargo_defecto];
}


//Trae las actividades asociadas al proyecto seleccionado y el usuario activo.
$sql="SELECT A.*, B.macroactividad, B.nombre as actividad, B.fecha_inicio, B.fecha_fin ";
$sql=$sql." from asignaciones A, actividades B " ;
$sql=$sql." where A.id_proyecto = B.id_proyecto " ;
$sql=$sql." and A.id_actividad = B.id_actividad " ;
$sql=$sql." and A.unidad =" . $laUnidad; 
$sql=$sql." and A.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> Reportes Hoja de Tiempo </div>
	</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Informaci&oacute;n del proyecto </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Nombre</td>
        <td width="10%">C&oacute;digo</td>
        <td width="10%">Cargo</td>
      </tr>
      <tr class="TxtTabla">
        <td><? echo  ucwords(strtolower($elProyecto)) ; ?></td>
        <td width="10%"><? echo $elCodigo ; ?></td>
        <td width="10%"><? echo $elCargoDef ; ?></td>
      </tr>
    </table></td>
      </tr>
    </table>
		</td>
      </tr>
    </table>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Reporte  Hojas de tiempo - Programaci&oacute;n detallada de <? echo strtoupper($nombreempleado." ".$apellidoempleado);
	?></td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Macroactividad</td>
        <td>Actividad</td>
        <td width="12%">Fecha Inicial</td>
        <td width="12%">Fecha Final </td>
        <td width="5%">Clase de tiempo </td>
        <td width="10%">Horas Programadas</td>
        <td width="10%">Horas Reportadas </td>
        <td width="10%">Remanente</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td><? echo  $reg[macroactividad] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[actividad])) ; ?></td>
        <td width="12%" align="center"><? echo date("M d Y ", strtotime($reg[fecha_inicial])); ?></td>
        <td width="12%" align="center"><? echo date("M d Y ", strtotime($reg[fecha_final])); ?></td>
        <td width="5%" align="center"><? echo  ucwords(strtolower($reg[clase_tiempo])) ; ?></td>
        <td width="10%" align="right"><? echo  ucwords(strtolower($reg[tiempo_asignado])) ; ?></td>
        <td width="10%" align="right">
		<?
		//13Jul2007
		//Trae la cantidad de horas registradas para el proyecto, actividad, usuario y clase de tiempo del registro
		$sqlHR="select sum(horas_registradas) cantHorasReg from horas ";
		$sqlHR=$sqlHR." where unidad =" . $reg[unidad];
		$sqlHR=$sqlHR." and id_proyecto =" . $reg[id_proyecto];
		$sqlHR=$sqlHR." and id_actividad =" . $reg[id_actividad];
		$sqlHR=$sqlHR." and clase_tiempo = " .$reg[clase_tiempo];
		$sqlHR=$sqlHR." and month(fecha) = " . date("m", strtotime($reg[fecha_inicial]));
		$sqlHR=$sqlHR." and year(fecha) = " . date("Y", strtotime($reg[fecha_inicial]));
		$cursorHR = mssql_query($sqlHR);
		if ($regHR=mssql_fetch_array($cursorHR)) {
			if (trim($regHR[cantHorasReg]) == "" ) {
				$mHorasRep = 0;
			}
			else {
				$mHorasRep = $regHR[cantHorasReg];
			}
		}

		echo $mHorasRep ;		
		?>
		</td>
        <td width="10%" align="right"><? echo $reg[tiempo_asignado] - $mHorasRep ; ?></td>
        </tr>
	  <? } ?>
    </table>
		</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT.php');return document.MM_returnValue" value="Listado de proyectos" /></td>
          </tr>
        </table>
</body>
</html>
