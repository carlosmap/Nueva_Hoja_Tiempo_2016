<?php
session_start();
//include("../verificaRegistro2.php");

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

$_SESSION["sesProyReportes"] = 824;


/*
09MAR2011
PBM
TRAE EL LISTADO DE PERSONAS QUE HAN VIATICADO EN EL PROYECTO
*/
$sql="SELECT DISTINCT ViaticosProyecto.unidad, Usuarios.nombre, Usuarios.apellidos ";
$sql=$sql." FROM ViaticosProyecto, Usuarios ";
$sql=$sql." WHERE ViaticosProyecto.unidad = Usuarios.unidad ";
$sql=$sql." AND ViaticosProyecto.id_proyecto = " . $_SESSION["sesProyReportes"];
//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
//sino con lo seleccionado en las listas mes y a?o
if ($pMes == "") {
	$sql=$sql." AND MONTH(ViaticosProyecto.FechaIni) = month(getdate()) ";
	$sql=$sql." AND YEAR(ViaticosProyecto.FechaIni) = year(getdate()) ";
}
else {
	$sql=$sql." AND MONTH(ViaticosProyecto.FechaIni) = " . $pMes;
	$sql=$sql." AND YEAR(ViaticosProyecto.FechaIni) =  " . $pAno;
}
$cursor = mssql_query($sql);



?>
<html>
<head>
<title>Reportes de los Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winReportes";
</script>
<script language="JavaScript" type="text/JavaScript">
<!--


function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:774px; height:25px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota3">Aprobaci&oacute;n vi&aacute;ticos del proyecto </td>
    </tr>
  </table>
</div>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Proyecto</td>
      </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="fondo"><table width="100%" cellspacing="1">
      <tr class="TituloTabla2">
        <td>Nombre del Proyecto </td>
        <td>Codigo - Cargo del Proyecto </td>
        <td>Director</td>
        <td>Coordinador</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td width="1%">&nbsp;</td>
        </tr>
      <? 	  while($regP = mssql_fetch_array($cursorP)){ 	  ?>
      <tr class="TxtTabla">
        <td><? echo strtoupper($regP["nombre"]); ?></td>
        <td><? echo $regP["codigo"]. "." .$regP["cargo_defecto"]; ?></td>
        <td><? echo strtoupper($regP["nomDirector"]. " " .$regP["apeDirector"]); ?></td>
        <td><? echo strtoupper($regP["nomCoord"]. " " .$regP["apeCoord"]); ?></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
	  <? } ?>
    </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>
	<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellpadding="0" cellspacing="1">
    <form name="form1" id="form1" method="post" action="">	
      <tr>
        <td colspan="3" class="TituloUsuario">Criterios de consulta </td>
        </tr>
      <? if ($mostrar == "Siiii") { ?>
      <tr>
        <td width="20%" class="TituloTabla">Reporte</td>
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
          <select name="select" class="CajaTexto">
            <option value="1">Solicitud de elementos</option>
            <option value="2">Solicitud de pasajes</option>
            <option value="3">Solicitud de pasajes</option>
          </select></td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
	  <? } ?>
      <tr>
        <td class="TituloTabla">Mes</td>
        <td class="TxtTabla">		<? 
	//Seleccionar el mes cuando se carga la p?gina por primera vez
	//si no cuando se recarga la p?gina
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}



	}
	
	?>
	&nbsp;      
	<select name="pMes" class="CajaTexto" id="pMes">
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select>
		</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">A&ntilde;o</td>
        <td class="TxtTabla">
		<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los a?os de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el a?o cuando se carga la p?gina por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el a?o actual
		}
		else {
			$AnoActual= $pAno; //el a?o seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>
    </select>
		</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">&nbsp;</td>
        <td class="TxtTabla">&nbsp; </td>
        <td width="2%" class="TxtTabla"><input name="Submit3" type="submit" class="Boton" value="Consultar" /></td>
      </tr>
    </form>	  
    </table></td>
      </tr>
    </table>
	</td>
  </tr>
  <tr>
    <td class="menu"><a href="lstProyectosHT.php" class="menu" >&lt;&lt; Regresar al listado de proyectos </a>
	</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td colspan="2" class="fondo">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Vi&aacute;ticos</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloUsuario">
        <td>Usuario que viatic&oacute; en el proyecto </td>
        <td>Aprobado</td>
        <td>Qui&eacute;n aprob&oacute; </td>
        <td width="1%">&nbsp;</td>
      </tr>
	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TituloTabla">
        <td><? echo "[" . $reg[unidad] . "] " . $reg[nombre] . " " .  $reg[apellidos] ; ?>&nbsp;</td>
        <td>xx</td>
        <td>zz</td>
        <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('addlstProyectosHTDetalle2.php?cualProyecto=<? echo $cualProyecto; ?>&pMes=<? echo $mesEnvio ?>&pAno=<? echo $anoEnvio ?>&cualUnidad=<? echo $reg[unidad]; ?>','verHT','scrollbars=yes,resizable=yes,width=700,height=400')" /></a></td>
      </tr>
      <tr>
        <td colspan="3">
		<?
		//Traer los vi?ticos asociados al proyecto tla cual como fueron registrados
	$sql4="SELECT ViaticosProyecto.IDsitio, ViaticosProyecto.IDfraccion, ViaticosProyecto.IDTipoViatico, ViaticosProyecto.unidad, ViaticosProyecto.id_actividad, 
		ViaticosProyecto.localizacion, ViaticosProyecto.cargo, ViaticosProyecto.FechaIni, ViaticosProyecto.FechaFin, ViaticosProyecto.Trayecto, 
		ViaticosProyecto.ObjetoComision, ViaticosProyecto.viaticoCompleto, Actividades.nombre AS nomActividad, TiposViatico.NomTipoViatico, 
		SitiosTrabajo.NomSitio, Usuarios.nombre AS nomUsuario, Usuarios.apellidos AS apeUsuario
		FROM ViaticosProyecto INNER JOIN
		Usuarios ON ViaticosProyecto.unidad = Usuarios.unidad INNER JOIN
		SitiosTrabajo ON ViaticosProyecto.IDsitio = SitiosTrabajo.IDsitio AND ViaticosProyecto.id_proyecto = SitiosTrabajo.id_proyecto INNER JOIN
		TiposViatico ON ViaticosProyecto.IDTipoViatico = TiposViatico.IDTipoViatico INNER JOIN
		Actividades ON ViaticosProyecto.id_proyecto = Actividades.id_proyecto AND ViaticosProyecto.id_actividad = Actividades.id_actividad ";
	$sql4= $sql4. " WHERE ViaticosProyecto.id_proyecto = " . $_SESSION["sesProyReportes"] ;
	$sql4= $sql4. " AND ViaticosProyecto.Unidad = " . $reg[unidad];
	//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
	//sino con lo seleccionado en las listas mes y a?o
	if ($pMes == "") {
		$sql4= $sql4. " and MONTH(ViaticosProyecto.FechaIni) = month(getdate()) ";
		$sql4= $sql4. " and YEAR(ViaticosProyecto.FechaIni) = year(getdate()) ";
	}
	else {
		$sql4= $sql4. " and MONTH(ViaticosProyecto.FechaIni) = " . $pMes;
		$sql4= $sql4. " and YEAR(ViaticosProyecto.FechaIni) = " . $pAno;
	}
	$sql4= $sql4. " order by ViaticosProyecto.unidad ";
	@mssql_select_db("HojaDeTiempo",$conexion);
	$cursor4 = mssql_query($sql4);

		?>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TxtTabla">
        <td><strong>Actividad</strong></td>
        <td width="8%"><strong>Fecha Inicio </strong></td>
        <td width="8%"><strong>Fecha finalizaci&oacute;n </strong></td>
        <td width="10%"><strong>Sitio</strong></td>
        <td width="10%"><strong>Tipo de vi&aacute;tico </strong></td>
      </tr>
	  <?
	  while ($reg4=mssql_fetch_array($cursor4)) {
	  ?>
	  
      <tr class="TxtTabla">
        <td><? echo $reg4[nomActividad]; ?></td>
        <td width="8%"><? echo date("M d Y ", strtotime($reg4[FechaIni])); ?></td>
        <td width="8%"><? echo date("M d Y ", strtotime($reg4[FechaFin])); ?></td>
        <td width="10%"><? echo $reg4[NomSitio]; ?></td>
        <td width="10%"><? echo $reg4[NomTipoViatico]; ?></td>
      </tr>
	  <? } ?>
    </table>		</td>
        <td width="1%" class="TxtTabla">&nbsp;</td>
      </tr>
	  <? } ?>
    </table>	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" valign="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;	</td>
    <td align="right" valign="bottom"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close();')" value="Cerrar Ventana"></td>
  </tr>
</table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
</table>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
