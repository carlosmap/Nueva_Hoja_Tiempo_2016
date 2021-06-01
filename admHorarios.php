<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//14Mar2008
//Trae el listado de horarios disponibles, es decir todos aquellos que uun no han sido asociados al proyecto seleccionado
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo, h.localiza  " ;
$sql3=$sql3." FROM Horarios h " ;
/*
$sql3=$sql3." Where Not Exists  " ;
$sql3=$sql3."   (SELECT * " ;
$sql3=$sql3."   FROM HorariosProy p " ;
$sql3=$sql3."   Where h.IDhorario = p.IDhorario " ;
$sql3=$sql3."   AND id_proyecto = " . $cualProyecto ;
$sql3=$sql3."   ) " ;
*/
$cursor3 = mssql_query($sql3);

//31May2011
//Trae las horas y días laborales del proyecto
$sql04="SELECT * ";
$sql04=$sql04." FROM horasydiasLaboralesProy ";
$sql04=$sql04." WHERE id_proyecto = " . $cualProyecto ;
if ($pAno == "") {
	$sql04=$sql04." and vigencia =  year(getdate())";
}
else {
	$sql04=$sql04." and vigencia = " . $pAno;
}
$cursor04 = mssql_query($sql04);

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
<title>Planeaci&oacute;n de Proyectos - Horarios</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 560px; height: 30px;">
Planeaci&oacute;n de proyectos - Horarios </div>
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
        <td width="15%" class="FichaAct">Horarios</td>
        <td width="15%" class="FichaInAct"><a href="admTiposViatico.php" class="FichaInAct1">Tipos de vi&aacute;tico </a></td>
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
        <td bgcolor="#FFFFFF" class="TxtTabla">&nbsp;		</td>
      </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">.: HORARIOS </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="5%">C&oacute;digo</td>
        <td>Nombre</td>
        <td width="10%">Tipo N&oacute;mina<br />
        Localizaci&oacute;n</td>
        <td width="5%">Lunes</td>
        <td width="5%">Martes</td>
        <td width="5%">Mi&eacute;rcoles</td>
        <td width="5%">Jueves</td>
        <td width="5%">Viernes</td>
        <td width="5%">S&aacute;bado</td>
        <td width="5%">Domingo</td>
        <td width="5%">Total</td>
        <td width="5%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
      </tr>
      	<? 
		//SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo 		
		while ($reg3=mssql_fetch_array($cursor3)) {  
			$totHorario = 0 ;
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg3[IDhorario] ; ?></td>
        <td><? echo  ucfirst(strtolower($reg3[NomHorario])) ; ?></td>
        <td width="10%">
		<? 
		if ($reg3[localiza]==1) {
			$pLocalizaH=$reg3[localiza].". Oficina";
		}
		if ($reg3[localiza]==2) {
			$pLocalizaH=$reg3[localiza].". Campo";
		}
		if ($reg3[localiza]==3) {
			$pLocalizaH=$reg3[localiza].". Planilla";
		}
		echo $pLocalizaH; ?>		</td>
        <td width="5%" align="right"><? echo  $reg3[Lunes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Martes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Miercoles] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Jueves] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Viernes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Sabado] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Domingo] ; ?></td>
        <td width="5%" align="right">
		<? 
		$totHorario = $reg3[Lunes] + $reg3[Martes] + $reg3[Miercoles] + $reg3[Jueves] + $reg3[Viernes] + $reg3[Sabado] +  $reg3[Domingo];
		echo  $totHorario ; ?></td>
        <td width="5%" align="right"><input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('fechasEspeciales.php?cualHorario=<? echo $reg3[IDhorario]; ?>','winFechas','scrollbars=yes,resizable=yes,width=600,height=400')" value="Fechas Especiales" /></td>
        <td width="1%" align="right">
		<?
		$phayHorProy = 0;
		$vhSql="select count(*) hayHorProy ";
		$vhSql=$vhSql." from HojaDeTiempo.dbo.HorariosProy ";
		$vhSql=$vhSql." where IDhorario =" . $reg3[IDhorario]  ;
		$vhCursor = mssql_query($vhSql);
		if ($vhReg=mssql_fetch_array($vhCursor)) {
			$phayHorProy = $vhReg[hayHorProy];
		}
		if ($phayHorProy == 0) {
		?>
		<a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upHTHorario.php?cualProyecto=<? echo $cualProyecto ?>&cualHorario=<? echo $reg3[IDhorario]; ?>','vupHor','scrollbars=yes,resizable=yes,width=640,height=200')" /></a>
		<? } ?>		</td>
	    <td width="1%" align="right">
		<? if ($phayHorProy == 0) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHTHorario.php?cualProyecto=<? echo $cualProyecto ?>&cualHorario=<? echo $reg3[IDhorario]; ?>','vdelHor','scrollbars=yes,resizable=yes,width=640,height=200')" /></a>
		<? } ?>
		</td>
	  </tr>
		<? } ?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addHTHorario.php?cualProyecto=<? echo $cualProyecto ?>','vadHor','scrollbars=yes,resizable=yes,width=640,height=200')" value="Nuevo Horario" /></td>
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
