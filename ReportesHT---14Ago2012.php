<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
	
//13Julio2007
//Trae el nombre de los proyectos donde el usuario tiene programaci?n
//Es decir en los proyectos donde se ha asignado tiempo de programaci?n
$sql="SELECT DISTINCT A.id_proyecto, P.nombre, P.id_estado, P.codigo ";
$sql=$sql." from asignaciones A, Proyectos P " ;
$sql=$sql." where A.id_proyecto = P.id_proyecto " ;
$sql=$sql." and P.id_estado = 2 " ;
$sql=$sql." and A.unidad = " . $_SESSION["sesUnidadUsuario"]; 
$sql=$sql." and (P.codigo <> 'ACC' and P.codigo <> 'AUS' and P.codigo <> 'ENF' and P.codigo <> 'LIC'   ";
$sql=$sql." and P.codigo <> 'PER' and P.codigo <> 'SAN' and P.codigo <> 'VAC')    ";
//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
//sino con lo seleccionado en las listas mes y a?o
/*if ($pMes == "") {
	$sql= $sql. " AND month(A.fecha_final) = month(getdate()) ";
	$sql= $sql. " AND year(A.fecha_final) = year(getdate()) ";
}
else {
	$sql= $sql. " AND month(A.fecha_final) = " . $pMes;
	$sql= $sql. " AND year(A.fecha_final) = " . $pAno;
}*/
$cursor = mssql_query($sql);


//16Jul2007
//ParaMostrar los botones del Reporte del director de proyecto y de divisi?n
$muestraDirDivision = 0;
$muestraDirProyecto = 0;

$sqlB="select count(*) cuantosReg " ;
$sqlB=$sqlB." from divisiones where id_director = ". $_SESSION["sesUnidadUsuario"]; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirDivision = $regB[cuantosReg];
}

/*
$sqlB="select count(*) esDirector from proyectos  ";
$sqlB=$sqlB." where (id_director = ". $_SESSION["sesUnidadUsuario"] . " or id_coordinador = " . $_SESSION["sesUnidadUsuario"] . " ) "; 
*/
$sqlB="SELECT count(*) cuantosProyecto ";
$sqlB=$sqlB." FROM (  "; 
$sqlB=$sqlB." 	Select id_proyecto "; 
$sqlB=$sqlB." 	from HojaDeTiempo.dbo.Proyectos "; 
$sqlB=$sqlB." 	where id_director = " . $_SESSION["sesUnidadUsuario"] . " or id_coordinador = " . $_SESSION["sesUnidadUsuario"]; 
$sqlB=$sqlB." 	and id_estado = 2 "; 
$sqlB=$sqlB." 	UNION  "; 
$sqlB=$sqlB." 	Select id_proyecto "; 
$sqlB=$sqlB." 	from HojaDeTiempo.dbo.Programadores "; 
$sqlB=$sqlB." 	where unidad = " . $_SESSION["sesUnidadUsuario"]; 
$sqlB=$sqlB." 	UNION "; 
$sqlB=$sqlB." 	select id_proyecto "; 
$sqlB=$sqlB." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto "; 
$sqlB=$sqlB." 	where unidadOrdenador = " . $_SESSION["sesUnidadUsuario"]; 
$sqlB=$sqlB." 	and id_proyecto is not null "; 
$sqlB=$sqlB." 	UNION "; 
$sqlB=$sqlB." 	select id_proyecto  "; 
$sqlB=$sqlB." 	from HojaDeTiempo.dbo.actividades "; 
$sqlB=$sqlB." 	where id_encargado = " . $_SESSION["sesUnidadUsuario"]; 
$sqlB=$sqlB." 	UNION "; 
$sqlB=$sqlB." 	select id_proyecto "; 
$sqlB=$sqlB." 	from HojaDeTiempo.dbo.ResponsablesActividad "; 
$sqlB=$sqlB." 	where unidad = " . $_SESSION["sesUnidadUsuario"]; 
$sqlB=$sqlB." ) A, Proyectos P, Usuarios D, Usuarios C  "; 
$sqlB=$sqlB." WHERE A.id_proyecto = P.id_proyecto  "; 
$sqlB=$sqlB." AND P.id_director *= D.unidad "; 
$sqlB=$sqlB." AND P.id_coordinador *= C.unidad "; 
$sqlB=$sqlB." AND P.id_estado = 2 "; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirProyecto = $regB[cuantosProyecto];
}

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
<title>Revisi?n de hojas de tiempo</title>
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

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td></td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Reporte  Hojas de tiempo - Programaci&oacute;n de <? echo strtoupper($nombreempleado." ".$apellidoempleado);
	?></td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Proyectos</td>
        <td width="5%">&nbsp;</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="5%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHTDet.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Detalle" /></td>
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
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">
	<? if ($muestraDirProyecto > 0) { ?>
	<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT2.php');return document.MM_returnValue" value="Reportes del Proyecto" />
	<? } ?>
&nbsp;
		<? if ($muestraDirDivision > 0) { ?>
	<input name="Submit4" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT3.php');return document.MM_returnValue" value="Reporte Director de divisi&oacute;n" />
		<? } ?>

	</td>
  </tr>
</table>
</body>
</html>
