<?php
session_start();
//include("../verificaRegistro2.php");

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer la información del proyecto seleccionado
$sql0="select P.* , U.nombre nomDirector, U.apellidos apeDirector, C.nombre nomCoo, C.apellidos apeCoo ";
$sql0=$sql0." from proyectos P, Usuarios U, Usuarios C ";
$sql0=$sql0." where P.id_director *= U.unidad ";
$sql0=$sql0." and P.id_coordinador *= C.unidad ";
$sql0=$sql0." and P.id_proyecto = " . $cualProyecto ;
$cursor0 = mssql_query($sql0);
if ($reg0=mssql_fetch_array($cursor0)) {
	$elIDProyecto = $reg0[id_proyecto];
	$elProyecto = $reg0[nombre];
	$elCodigo = $reg0[codigo];
	$elCargoDef = $reg0[cargo_defecto];
	$nombreDirector = $reg0[nomDirector] . " " . $reg0[apeDirector] ;
	$nombreCoordinador = $reg0[nomCoo] . " " . $reg0[apeCoo] ;
}


/*
09MAR2011
PBM
TRAE EL LISTADO DE PERSONAS QUE HAN VIATICADO EN EL PROYECTO
*/
$sql="SELECT DISTINCT ViaticosProyecto.unidad, Usuarios.nombre, Usuarios.apellidos ";
$sql=$sql." FROM ViaticosProyecto, Usuarios ";
$sql=$sql." WHERE ViaticosProyecto.unidad = Usuarios.unidad ";
$sql=$sql." AND ViaticosProyecto.id_proyecto = " . $cualProyecto;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
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
<title>Vi&aacute;ticos de los Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winHojaTiempo";
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
        </tr>
      
      <tr class="TxtTabla">
        <td><? echo strtoupper($elProyecto); ?></td>
        <td><? echo $elCodigo .  "." . $elCargoDef; ?></td>
        <td><? echo strtoupper($nombreDirector); ?></td>
        <td><? echo strtoupper($nombreCoordinador); ?></td>
        </tr>
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
      <tr>
        <td width="20%" class="TituloTabla">Mes</td>
        <td class="TxtTabla">		<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
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
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
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
    <td class="menu"><a href="lstProyectosHT.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>" class="menu" >&lt;&lt; Regresar al listado de proyectos </a>
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
        <td>Usuarios que viaticaron en el proyecto </td>
        <td width="5%">Aprobado</td>
        <td width="10%">Comentario</td>
        <td width="10%">Qui&eacute;n aprob&oacute; </td>
        <td width="1%">&nbsp;</td>
      </tr>
	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TituloTabla">
        <td><? echo "[" . $reg[unidad] . "] " . $reg[nombre] . " " .  $reg[apellidos] ; ?>&nbsp;
		
		
		</td>
        <td width="5%" align="center">
		<?
		$pEstaAprobado = "";
		$pComentaEncargado= "";
		$quienAprueba = "";
		
		//Verifica si ya están los viáticos aprobados o no
		$sqlAp="SELECT A.* , B.nombre, B.apellidos ";
		$sqlAp=$sqlAp." FROM AprobacionViaticosHT A, Usuarios B ";
		$sqlAp=$sqlAp." WHERE A.unidadEncargado = B.unidad ";
		$sqlAp=$sqlAp." AND A.id_proyecto = " . $elIDProyecto ;
		$sqlAp=$sqlAp." AND A.mes = " . $mesActual ;
		$sqlAp=$sqlAp." and A.vigencia = " . $AnoActual ;
		$sqlAp=$sqlAp." AND A.unidad = " . $reg[unidad] ;
		$cursorAp = mssql_query($sqlAp);
		if ($regAp=mssql_fetch_array($cursorAp)) {
			$pEstaAprobado = $regAp[validaEncargado];
			$pComentaEncargado= $regAp[comentaEncargado];
			$quienAprueba = $regAp[nombre] . " " . $regAp[apellidos] ;
		}
		?>
		<?

		if (trim($pEstaAprobado) == '1') { ?>
			<img src="../portal/images/Aprobado.gif" alt="Todos los viaticos aprobados" width="21" height="24" /> 
	<?		}
			else { ?>
			<img src="../portal/images/NoAprobado.gif" alt="Viáticos sin aprobar" width="20" height="22" />
	<?		}		?>
		</td>
        <td width="10%"><? echo $pComentaEncargado; ?></td>
        <td width="10%">
		<? 
		if (trim($pEstaAprobado) == '1') {
		echo $quienAprueba ; 
		}
		?></td>
        <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upApruebaViaticosHT.php?cualProyecto=<? echo $cualProyecto; ?>&pMes=<? echo $mesActual; ?>&pAno=<? echo $AnoActual ;?>&cualUnidad=<? echo $reg[unidad]; ?>','verHT','scrollbars=yes,resizable=yes,width=700,height=400')" /></a></td>
      </tr>
      <tr>
        <td colspan="4">
		<?
		//Traer los viáticos asociados al proyecto tla cual como fueron registrados
	$sql4="SELECT ViaticosProyecto.IDsitio, ViaticosProyecto.IDfraccion, ViaticosProyecto.IDTipoViatico, ViaticosProyecto.unidad, ViaticosProyecto.id_actividad, 
		ViaticosProyecto.localizacion, ViaticosProyecto.cargo, ViaticosProyecto.FechaIni, ViaticosProyecto.FechaFin, ViaticosProyecto.Trayecto, 
		ViaticosProyecto.ObjetoComision, ViaticosProyecto.viaticoCompleto, Actividades.nombre AS nomActividad, TiposViatico.NomTipoViatico, 
		SitiosTrabajo.NomSitio, Usuarios.nombre AS nomUsuario, Usuarios.apellidos AS apeUsuario
		FROM ViaticosProyecto INNER JOIN
		Usuarios ON ViaticosProyecto.unidad = Usuarios.unidad INNER JOIN
		SitiosTrabajo ON ViaticosProyecto.IDsitio = SitiosTrabajo.IDsitio AND ViaticosProyecto.id_proyecto = SitiosTrabajo.id_proyecto INNER JOIN
		TiposViatico ON ViaticosProyecto.IDTipoViatico = TiposViatico.IDTipoViatico INNER JOIN
		Actividades ON ViaticosProyecto.id_proyecto = Actividades.id_proyecto AND ViaticosProyecto.id_actividad = Actividades.id_actividad ";
	$sql4= $sql4. " WHERE ViaticosProyecto.id_proyecto = " . $cualProyecto ;
	$sql4= $sql4. " AND ViaticosProyecto.Unidad = " . $reg[unidad];
	//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
	//sino con lo seleccionado en las listas mes y año
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
    <td align="right" valign="bottom">
	<? if (mssql_num_rows($cursor) > 0) { ?>
	<input name="Submit" type="submit" class="Boton" onClick="MM_openBrWindow('addApruebaViaticosHT.php?cualProyecto=<? echo $cualProyecto; ?>&pMes=<? echo $mesActual; ?>&pAno=<? echo $AnoActual ;?>','wavHT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Tramitar Aprobaci&oacute;n Vi&aacute;ticos">
	<? } ?>
	</td>
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
