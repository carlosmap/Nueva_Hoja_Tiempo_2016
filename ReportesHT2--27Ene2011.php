<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//16Jull2007
//Trae el nombre de los proyectos donde el usuario activo se encuentra como director o coodinador
/*
$sql="select P.* , U.nombre nomDir, U.apellidos apeDir, C.nombre nomCoo, C.apellidos apeCoo ";
$sql=$sql." from proyectos P, usuarios U, usuarios C " ;
$sql=$sql." where P.id_director *= U.unidad " ;
$sql=$sql." and P.id_coordinador *= C.unidad " ;
$sql=$sql." and P.id_estado = 2 " ;
$sql=$sql." and (P.id_director = " . $laUnidad;  
$sql=$sql." or P.id_coordinador = " . $laUnidad . " ) ";
$sql=$sql." order by P.nombre ";
*/
$sql="SELECT P.*, D.nombre nomDir, D.apellidos apeDir, C.nombre nomCoo, C.apellidos apeCoo   ";
$sql=$sql." FROM ( " ;
$sql=$sql." 	Select id_proyecto " ;
$sql=$sql." 	from HojaDeTiempo.dbo.Proyectos " ;
$sql=$sql." 	where id_director = ". $laUnidad ." or id_coordinador = " . $laUnidad ;
$sql=$sql." 	and id_estado = 2 " ;
$sql=$sql." 	UNION " ;
$sql=$sql." 	Select id_proyecto " ;
$sql=$sql." 	from HojaDeTiempo.dbo.Programadores " ;
$sql=$sql." 	where unidad = " . $laUnidad ;
$sql=$sql." 	UNION " ;
$sql=$sql." 	select id_proyecto " ;
$sql=$sql." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto " ;
$sql=$sql." 	where unidadOrdenador =" . $laUnidad ;
$sql=$sql." 	and id_proyecto is not null " ;
$sql=$sql." 	UNION " ;
$sql=$sql." 	select id_proyecto  " ;
$sql=$sql." 	from HojaDeTiempo.dbo.actividades " ;
$sql=$sql." 	where id_encargado = " . $laUnidad ;
$sql=$sql." 	UNION " ;
$sql=$sql." 	select id_proyecto " ;
$sql=$sql." 	from HojaDeTiempo.dbo.ResponsablesActividad " ;
$sql=$sql." 	where unidad = " . $laUnidad ;
$sql=$sql." ) A, Proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE A.id_proyecto = P.id_proyecto " ;
$sql=$sql." AND P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_estado = 2 " ;
$sql=$sql." ORDER BY P.nombre " ;
$cursor = mssql_query($sql);

//16Jul2007
//ParaMostrar los botones del Reporte del director de proyecto y de división
$muestraDirDivision = 0;
$sqlB="select count(*) cuantosReg " ;
$sqlB=$sqlB." from divisiones where id_director = ". $laUnidad; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirDivision = $regB[cuantosReg];
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
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:8px; width: 365px;">
		<div align="center">  Hoja de Tiempo <br />
	    Reportes del proyecto </div>
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
    <td>&nbsp;</td>
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
        <td width="25%">Director / Coordinador</td>
        <td width="25%">Director / Coordinador</td>
        <td width="5%">&nbsp;</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="25%"><? echo  ucwords(strtolower($reg[nomDir] . " " . $reg[apeDir] )) ; ?></td>
        <td width="25%"><? echo  ucwords(strtolower($reg[nomCoo] . " " . $reg[apeCoo] )) ; ?></td>
        <td width="5%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT2Det.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Detalle" /></td>
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
</table><table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td><input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">
	<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT.php');return document.MM_returnValue" value="Programaci&oacute;n personal" />
	<? if ($muestraDirDivision > 0) { ?>
	<input name="Submit4" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT3.php');return document.MM_returnValue" value="Reporte Director de divisi&oacute;n" />	
	<? } ?>
	</td>
  </tr>
</table>
</body>
</html>
