<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
	
//8Ago2007
//Consulta para traer la información del Proyecto, Director, Coordinador y sus correspondientes extensiones
//Solo para proyectos activos
$sql="select P.* , D.nombre nomDirector, D.apellidos apeDirector, C.nombre nomCoordina, C.apellidos apeCoordina, ";
$sql=$sql." eD.extension extDir, eC.extension extCoordina " ;
$sql=$sql." from HojaDeTiempo.dbo.proyectos P, HojaDeTiempo.dbo.Usuarios D, HojaDeTiempo.dbo.Usuarios C, " ;
$sql=$sql." GestiondeInformacionDigital.dbo.extensiones eD, GestiondeInformacionDigital.dbo.extensiones eC " ;
$sql=$sql." where P.id_director = D.unidad " ;
$sql=$sql." and P.id_coordinador *= C.unidad " ;
$sql=$sql." and P.id_director *= eD.unidad " ;
$sql=$sql." and P.id_coordinador *= eC.unidad " ;
$sql=$sql." and P.id_estado = 2 " ;
$sql=$sql . " and (P.codigo <> 'ACC' and P.codigo <> 'AUS' and P.codigo <> 'ENF' and P.codigo <> 'LIC'  ";
$sql=$sql . " and P.codigo <> 'PER' and P.codigo <> 'SAN' and P.codigo <> 'VAC')   ";
$sql=$sql." and P.nombre LIKE '%".$cNombre."%'" ;
$sql=$sql." order by P.nombre " ;
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

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Listado de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> DIRECTORES / COORDINADORES PROYECTOS </div>
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

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Criterio de Consulta </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  <tr>
    <td width="15%" align="right" class="TituloTabla">Nombre:&nbsp;</td>
    <td class="TxtTabla">
<input name="cNombre" type="text" class="CajaTexto" id="cNombre" value="<? echo $cNombre; ?>" size="85" />	
&nbsp;      </td>
    <td width="10%"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
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
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="3%">Shared Files </td>
        <td width="10%">C&oacute;digo</td>
        <td width="50%">Proyecto</td>
        <td>Encargados</td>
        <td width="10%">Extensi&oacute;n</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <td width="3%">
		<?
		$existeFTP = 0 ;
		$fSql="select count(*) hayFTP from PortalGID.dbo.asignaProyectosExt ";
		$fSql=$fSql." where id_proyecto =" . $reg[id_proyecto];
		$fCursor = mssql_query($fSql);
		if ($fReg=mssql_fetch_array($fCursor)) {
			$existeFTP = $fReg[hayFTP] ;
		}
		?>
		<? if ($existeFTP == 0) {  ?>
				<? if (($laUnidad == $reg[id_director]) OR ($laUnidad == $reg[id_coordinador])) { ?>
					<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addasignaProyExterno.php?cualProyecto=<? echo $reg[id_proyecto]; ?>','vAsPE','scrollbars=yes,resizable=yes,width=400,height=200')" value="Crear" />
				<? } ?>
		<? }
			else { ?>
				<? if (($laUnidad == $reg[id_director]) OR ($laUnidad == $reg[id_coordinador])) { ?>
					<a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" border="0" onclick="MM_openBrWindow('upasignaProyExterno.php?cualReg=<? echo $reg[id_proyecto]; ?>','vupAPE','scrollbars=yes,resizable=yes,width=400,height=200')" /></a>
				<? } 
				   else {
				?>	
					<img src="img/images/Si.gif" alt="Tiene Sistema de Gesti&oacute;n de archivos externos" />
				<? } ?>
		<? } ?>
		
		</td>
	    <td width="10%"><? echo ucwords(strtolower($reg[codigo])) . "." . ucwords(strtolower($reg[cargo_defecto]))  ; ?></td>
        <td width="50%"><? echo ucwords(strtolower($reg[nombre]))  ; ?></td>
        <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="TxtTabla"><? echo ucwords(strtolower($reg[nomDirector])) . " " . ucwords(strtolower($reg[apeDirector])) ; ?></td>
          </tr>
          <tr>
            <td class="TxtTabla"><? echo ucwords(strtolower($reg[nomCoordina])) . " " . ucwords(strtolower($reg[apeCoordina])) ; ?></td>
          </tr>
        </table></td>
        <td width="10%" align="center">
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td class="TxtTabla"><? echo $reg[extDir]; ?></td>
            </tr>
            <tr>
              <td class="TxtTabla"><? echo $reg[extCoordina]; ?></td>
            </tr>
          </table>
</td>
        </tr>
	  <? } ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
