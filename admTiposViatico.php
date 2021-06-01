<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//26Mar2008
//Trae el listado de tipos de viático disponibles
$sql3="SELECT DISTINCT t.IDTipoViatico, t.NomTipoViatico  " ;
$sql3=$sql3." FROM TiposViatico t " ;
/*
$sql3=$sql3." Where Not Exists " ;
$sql3=$sql3." (SELECT * " ;
$sql3=$sql3." FROM TiposViaticoProy p " ;
$sql3=$sql3." Where t.IDTipoViatico = p.IDTipoViatico " ;
$sql3=$sql3." AND id_proyecto = " . $cualProyecto ;
$sql3=$sql3." ) " ;
*/

$cursor3 = mssql_query($sql3);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winAdminHTs";

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Planeaci&oacute;n de Proyectos - Tipos de vi&aacute;tico</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 729px; height: 30px;">
Planeaci&oacute;n de proyectos - Tipos de vi&aacute;tico </div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" class="FichaInAct"><a href="admHorarios.php" class="FichaInAct1">Horarios </a></td>
        <td width="15%" class="FichaAct">Tipos de vi&aacute;tico </td>
        <td>&nbsp;</td>
      </tr>
</table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="1" class="TituloUsuario"> </td>
      </tr>
    </table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">.: TIPOS DE VI&Aacute;TICO </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="5%">C&oacute;digo</td>
        <td>Tipo de vi&aacute;tico </td>
        <td width="1%">&nbsp;</td>
      </tr>
      	<? 
		while ($reg3=mssql_fetch_array($cursor3)) {  
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg3[IDTipoViatico] ; ?></td>
        <td><? echo  ucfirst(strtolower($reg3[NomTipoViatico])) ; ?></td>
        <td width="1%" align="right">
		<? 
			//Sólo aparece para el personal asignado al perfil de Administración del sistema = 1
			if ($_SESSION["sesPerfilUsuario"] == "1") { ?>
		<? 
		//Verifica la existencia del tipo de viático en viáticos proyecto
		$phayViaticoProy = 0;
		$vhSql="select count(*) existeViaticoProy from HojaDeTiempo.dbo.viaticosProyecto ";
		$vhSql=$vhSql." where IDTipoViatico = " .$reg3[IDTipoViatico] ;
		$vhCursor = mssql_query($vhSql);
		if ($vhReg=mssql_fetch_array($vhCursor)) {
			$phayViaticoProy = $vhReg[existeViaticoProy];
		}

		if ($phayViaticoProy == 0) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHTTipoViatico.php?cualProyecto=<? echo $cualProyecto ?>&cualTipoV=<? echo $reg3[IDTipoViatico]; ?>','vdelHor','scrollbars=yes,resizable=yes,width=640,height=200')" /></a>
		<? } ?>
		<? } //if del perfil ?>
		</td>
	  </tr>
		<? } ?>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right">
		    <? 
			//Sólo aparece para el personal asignado al perfil de Administración del sistema = 1
			if ($_SESSION["sesPerfilUsuario"] == "1") { ?>
		<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addHTTipoViatico.php?cualProyecto=<? echo $cualProyecto ?>','vadHor','scrollbars=yes,resizable=yes,width=640,height=200')" value="Nuevo Tipo vi&aacute;tico" />
		<? } ?>
		</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_callJS('window.close();')" value="Cerrar Ventana" />
    </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
