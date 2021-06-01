<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	

//10jUN2008
//Si es perfil = 1 administrador y/o se trata de Camilo Marulanda muestra todos los proyectos
//de lo contrario sólo muestra los proyectos de la persona activa
//El listado de proyectos va a estar visible para
//Director
//Coordinador
//Ordenadores del gasto
//Programadores
//Responsables de actividad
//if (($_SESSION["sesPerfilUsuario"] == "1") OR ($laUnidad == "14384")) {
if ($_SESSION["sesPerfilUsuario"] == "1")  {
	$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
	$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
	$sql=$sql." WHERE P.id_director = D.unidad " ;
	$sql=$sql." AND P.id_coordinador = C.unidad " ;
	$sql=$sql." AND P.id_proyecto IN (683, 1547) " ;
}
else {
	$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
	$sql=$sql." FROM ( " ;
	$sql=$sql." 	Select id_proyecto " ;
	$sql=$sql." 	from HojaDeTiempo.dbo.Proyectos " ;
	$sql=$sql." 	where id_director = " . $laUnidad . " or id_coordinador = " . $laUnidad ;
	$sql=$sql." 	and id_estado = 2 " ;
	$sql=$sql." 	UNION " ;
	$sql=$sql." 	Select id_proyecto " ;
	$sql=$sql." 	from HojaDeTiempo.dbo.Programadores " ;
	$sql=$sql." 	where unidad = " . $laUnidad ;
	$sql=$sql." 	UNION " ;
	$sql=$sql." 	select id_proyecto " ;
	$sql=$sql." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto " ;
	$sql=$sql." 	where unidadOrdenador = " . $laUnidad ;
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
	$sql=$sql." AND P.id_estado = 2" ;
}
if (trim($pNombre) != "") {
	$sql=$sql." and P.nombre like '%".trim($pNombre)."%' " ;
}
if (trim($pProyecto) == 2) {
	$sql=$sql." AND P.especial is not null " ;
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
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 708px; height: 30px;">
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
        <td width="20%">Encargados</td>
        <td width="15%">Programaci&oacute;n Vs Facturaci&oacute;n </td>
        <td width="15%">Resumen</td>
        <td width="15%">Notificaciones</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
	   <tr class="TxtTabla">
	     <td width="20%" valign="top">
		<? echo  "<br><strong>Proyecto: </strong><br>" . ucwords(strtolower($reg[nombre])) . " [" . trim($reg[codigo]) . "." . $reg[cargo_defecto] . "] " ; ?>
		<? echo  "<br><br><strong>Director: </strong><br>" .  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" ; ?>
		<? echo  "<br><strong>Coordinador: </strong><br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])) . "<br>" ; ?>
		<? 
		$oSql="select O.*, U.nombre, U.apellidos ";
		$oSql=$oSql." from GestiondeInformacionDigital.dbo.OrdenadorGasto O, HojaDeTiempo.dbo.Usuarios U  ";
		$oSql=$oSql." where O.id_proyecto =" . $reg[id_proyecto] ;
		$oSql=$oSql." and O.unidadOrdenador = U.unidad ";
		$oCursor = mssql_query($oSql);
		echo "<br><strong>Ordenadores</strong><br>" ;
		while ($oReg=mssql_fetch_array($oCursor)) {
			echo  ucwords(strtolower($oReg[nombre])) . " " . ucwords(strtolower($oReg[apellidos])) . "<br>";
		}
		
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','pnfProgProyectos01.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Gesti&oacute;n del Proyecto" /></td>
          </tr>
          <tr>
            <td align="right"><input name="Submit5" type="submit" class="Boton" onclick="MM_goToURL('parent','pnf2ProgProyectos01.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Gesti&oacute;n del Proyecto V2" /></td>
          </tr>
        </table>
</td>
        <td width="15%"><img src="imagenes/imgProy01.jpg" width="350" height="197" /></td>
        <td width="15%" align="center"><img src="imagenes/imgProy02.jpg" width="200" height="151" />
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
                <tr class="TituloUsuario">
                  <td rowspan="2">Actividad</td>
                  <td colspan="2">Fecha</td>
                  <td width="5%" rowspan="2">Avance</td>
                </tr>
                <tr class="TituloUsuario">
                  <td>Inicio</td>
                  <td>Final</td>
                  </tr>
                <tr class="TxtTabla">
                  <td>Actividad 1 </td>
                  <td>1-Ene-2012</td>
                  <td>30-Dic-2012</td>
                  <td width="5%">55%</td>
                </tr>
                <tr class="TxtTabla">
                  <td>Actividad 2 </td>
                  <td>15-Abr-2012</td>
                  <td>18-Nov-2012</td>
                  <td width="5%">45%</td>
                </tr>
              </table></td>
            </tr>
          </table>          </td>
        <td width="15%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TituloUsuario">
                <td>Notificaci&oacute;n</td>
                <td width="5%">Problemas encontrado </td>
                <td width="1%">&nbsp;</td>
              </tr>
              <tr class="TxtTabla">
                <td>Sobrefacturaci&oacute;n por valor </td>
                <td width="5%" align="right">5</td>
                <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Detalle" width="16" height="16" border="0" /></a></td>
              </tr>
              <tr class="TxtTabla">
                <td>Sobrefacturaci&oacute;n por persona </td>
                <td align="right">2</td>
                <td><a href="#"><img src="img/images/ver.gif" alt="Detalle" width="16" height="16" border="0" /></a></td>
              </tr>
              <tr class="TxtTabla">
                <td>Hojas de tiempo sin actualizar </td>
                <td align="right">10</td>
                <td><a href="#"><img src="img/images/ver.gif" alt="Detalle" width="16" height="16" border="0" /></a></td>
              </tr>
              <tr class="TxtTabla">
                <td>Cambios en equipo de trabajo</td>
                <td width="5%" align="right">0</td>
                <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Detalle" width="16" height="16" border="0" /></a></td>
              </tr>
            </table></td>
          </tr>
        </table>          </td>
        </tr>
	  <? } ?>
	<? 
	//Para que este proyecto siempre le aparezca a Olga Lucia
	if ($laUnidad == 15320) { ?>
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
