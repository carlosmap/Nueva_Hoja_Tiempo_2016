<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	
//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador

$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador = C.unidad " ;
//Sólo aparece el listado total de proyectos para el administrador del sistema perfil Administrado = 1
if ($_SESSION["sesPerfilUsuario"] != "1") {
	$sql=$sql." AND (P.id_director = " . $laUnidad . " or P.id_coordinador=". $laUnidad .") " ;
}
if (trim($pNombre) != "") {
	$sql=$sql." and P.nombre like '%".trim($pNombre)."%' " ;
}
if (trim($pProyecto) == 2) {
	$sql=$sql." AND especial is not null " ;
}


if ($pOrden == 1) {
	$sql=$sql." ORDER BY P.nombre  " ;
}
else {
	$sql=$sql." ORDER BY P.codigo, P.cargo_defecto " ;
}
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
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 448px; height: 30px;">
Programaci&oacute;n de proyectos</div>
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
        <td class="TituloUsuario">Criterios de consulta </td>
      </tr>
    </table>


    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellpadding="0" cellspacing="1">
    <form name="form1" id="form1" method="post" action="">	
      <tr>
        <td width="20%" class="TituloTabla">Ordenar por </td>
        <td class="TxtTabla">
		<?
		if ($pOrden == 1) {
			$selOrden1 = "checked";
			$selOrden2 = "";
		}
		else {
			$selOrden1 = "";
			$selOrden2 = "checked";
		}
		?>
		<input name="pOrden" type="radio" value="1" <? echo $selOrden1; ?>  onClick="document.form1.submit();" />
          Nombre 
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="pOrden" type="radio" value="2" <? echo $selOrden2; ?> onClick="document.form1.submit();" />
          C&oacute;digo</td>
        <td width="2%" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Proyectos</td>
        <td class="TxtTabla"><?
		if (($pProyecto == 1) or (trim($pProyecto) == "")) {
			$selP1 = "checked";
			$selP2 = "";
		}
		else {
			$selP1 = "";
			$selP2 = "checked";
		}
		?>
          <input name="pProyecto" type="radio" value="1" <? echo $selP1; ?>  onClick="document.form1.submit();" />
Todos &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="pProyecto" type="radio" value="2" <? echo $selP2; ?>   onClick="document.form1.submit();" />
Especial</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Nombre</td>
        <td class="TxtTabla"><input name="pNombre" type="text" class="CajaTexto" id="pNombre" size="70" /> </td>
        <td width="2%" class="TxtTabla"><input name="Submit3" type="submit" class="Boton" value="Consultar" /></td>
      </tr>
    </form>	  
    </table></td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">   Programaci&oacute;n de proyectos para <? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="2" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="10%">ID</td>
        <td>Proyectos</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="5%">&nbsp;</td>
        <td width="5%">&nbsp;</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?></td>
        <td width="20%"><? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); ?></td>
        <td width="5%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosActiv.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Actividades" /></td>
        <td width="5%"><input name="Submit4" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosST.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Sitios de trabajo" /></td>
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
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
