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

//26Mar2008
//Trae los tipos de viático del proyecto seleccionado
$sql2="SELECT p.id_proyecto, p.IncluyeFestivos, v.*  " ;
$sql2=$sql2." FROM TiposViaticoProy p, TiposViatico v  " ;
$sql2=$sql2." Where p.IDTipoViatico = v.IDTipoViatico " ;
$sql2=$sql2." and p.id_proyecto = " . $cualProyecto ;
$cursor2 = mssql_query($sql2);
$cantTVProy = mssql_num_rows ($cursor2) ;

//26Mar2008
//Trae el listado de tipos de viático disponibles
$sql3="SELECT DISTINCT t.IDTipoViatico, t.NomTipoViatico  " ;
$sql3=$sql3." FROM TiposViatico t " ;
$sql3=$sql3." Where Not Exists " ;
$sql3=$sql3." (SELECT * " ;
$sql3=$sql3." FROM TiposViaticoProy p " ;
$sql3=$sql3." Where t.IDTipoViatico = p.IDTipoViatico " ;
$sql3=$sql3." AND id_proyecto = " . $cualProyecto ;
$sql3=$sql3." ) " ;
$sql3=$sql3." AND estadoTipoViatico = 'A' " ;
$cursor3 = mssql_query($sql3);



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
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 729px; height: 30px;">
Programaci&oacute;n de proyectos - Tipos de vi&aacute;tico </div>
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
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">  Proyecto </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="10%">ID</td>
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?>
		<? $codProyecto = trim($reg[codigo]) ;?></td>
        <td width="20%"><? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); ?></td>
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
            <td class="TituloUsuario">Tipos de vi&aacute;tico del proyecto </td>
          </tr>
        </table>
		</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Codigo</td>
        <td>Descripci&oacute;n</td>
        <td width="10%">Incluye Festivos </td>
        <td width="1%">&nbsp;</td>
      </tr>
      	<? 
		while ($reg2=mssql_fetch_array($cursor2)) {  
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg2[IDTipoViatico] ; ?></td>
        <td><? echo  ucfirst(strtolower($reg2[NomTipoViatico])) ; ?></td>
        <td width="10%" align="center">
		<? 
		if (trim($reg2[IncluyeFestivos]) == "1" ) {
			echo "SI" ; 
		}
		else {
			echo "NO" ; 
		}
		?>
		</td>
        <td width="1%" align="right">
		<? 
		//18Mar2008
		//Valida que el multiplicador de viático no se encuentre asociado a un viático
		$hayViatico = 0;
		$vSqlHP="select count(*) existeViatico from ViaticosProyecto ";
		$vSqlHP=$vSqlHP." WHERE id_proyecto = " . $reg2[id_proyecto] ;
		$vSqlHP=$vSqlHP." AND IDTipoViatico =" .$reg2[IDTipoViatico] ;
		$vCursorHP = mssql_query($vSqlHP);
		if ($vRegHP=mssql_fetch_array($vCursorHP)) {  
			$hayViatico= $vRegHP[existeViatico];
		}
		
		if  ($hayViatico == 0) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delTipoViaticoProy.php?cualProyecto=<? echo $cualProyecto; ?>&cualTipoV=<? echo  $reg2[IDTipoViatico] ; ?>','dHP','scrollbars=yes,resizable=yes,width=400,height=200')" /></a>
		<? } ?>
		</td>
	  </tr>
	  <? } ?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addTipoViaticoProy.php?cualProyecto=<? echo $cualProyecto ?>','vHP','scrollbars=yes,resizable=yes,width=400,height=200')" value="Agregar Tipo de vi&aacute;tico al Proyecto" /></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF" class="TxtTabla">&nbsp;		</td>
      </tr>
</table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Tipos de vi&aacute;tico  disponibles </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">C&oacute;digo</td>
        <td>Descripci&oacute;n</td>
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
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delTipoViatico.php?cualProyecto=<? echo $cualProyecto ?>&cualTipoV=<? echo $reg3[IDTipoViatico]; ?>','vdelHor','scrollbars=yes,resizable=yes,width=640,height=200')" /></a>
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
		<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addTipoViatico.php?cualProyecto=<? echo $cualProyecto ?>','vadHor','scrollbars=yes,resizable=yes,width=640,height=200')" value="Nuevo Tipo vi&aacute;tico" />
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
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectos.php');return document.MM_returnValue" value="Lista de Proyectos" />
    <input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
