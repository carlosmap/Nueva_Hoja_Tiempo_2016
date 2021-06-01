<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer llos proyectos donde el usuario activo está involucrado como los directores y/o encargado
$sql="Select *  ";
$sql=$sql." from HojaDeTiempo.dbo.Proyectos " ;
$sql=$sql." where id_estado = 2 " ;
$sql=$sql." and (id_director =".$laUnidad." or id_coordinador = ".$laUnidad." ) " ;
//9Jul2008
//o como ordenadores del gasto
$sql=$sql." Union " ;
$sql=$sql." Select P.* " ;
$sql=$sql." from GestiondeInformacionDigital.dbo.OrdenadorGasto O, HojaDeTiempo.dbo.Proyectos P " ;
$sql=$sql." where O.id_proyecto = P.id_proyecto " ;
$sql=$sql." and O.unidadOrdenador = " . $laUnidad;
//14Abr2011
/*
$sql=$sql." Union " ;
$sql=$sql." Select B.* " ;
$sql=$sql." from HojaDeTiempo.dbo.AutorizaVerFact A, HojaDeTiempo.dbo.Proyectos B " ;
$sql=$sql." where A.id_proyecto = B.id_proyecto " ;
$sql=$sql." and A.unidad = " . $laUnidad;
*/

$cursor = mssql_query($sql);

//8Nov2007
//Verifica si la unidad Activa está dentro de la tabla RevisaGastosGenerales
//como jefe de algunos usuarios para la aprobacón de los Gastos Generales
$esRevisarGG = 0;
$sqlGG="Select count(*) hayRegGG ";
$sqlGG=$sqlGG." from RevisaGastosGenerales where unidadRevisa = " . $laUnidad ;
$cursorGG = mssql_query($sqlGG);
if ($regGG=mssql_fetch_array($cursorGG)) {
	$esRevisarGG = $regGG[hayRegGG];
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
<title>Aprobaci&oacute;n de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:200px; top:11px; width: 529px; height: 25px;">
		<div align="center"> 
		  Aprobaci&oacute;n de facturaci&oacute;n <br>
		  en proyectos a cargo 
		</div>
</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de aprobación para otros periodos </td>
  </tr>
</table>
	<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  <tr>
    <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
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
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes">
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
    </select></td>
    <td width="15%" align="right" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
	&nbsp;
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

    </select>	</td>
    <td width="10%"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
</table>
	</td>
  </tr>
</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Proyectos activos a su cargo </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td colspan="3">Informaci&oacute;n del proyecto </td>
        <td colspan="2">Facturaci&oacute;n</td>
        <td colspan="4">Vi&aacute;ticos</td>
        </tr>
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="5%">C&oacute;digo</td>
        <td width="5%">Cargo Defecto </td>
        <td width="10%">Aprobaci&oacute;n</td>
        <td width="5%">&nbsp;</td>
        <td width="5%">Cantidad de personas que viaticaron </td>
        <td width="5%">Cantidad de personas con autorizaci&oacute;n </td>
        <td width="5%">Aprobaci&oacute;n <br />
          de vi&aacute;ticos </td>
        <td width="3%">&nbsp;</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  	//8Nov2007
		//Si el proyecto es Gastos Generales no aparece por aquí porque debe aparecer para que apruebe su respectivo jefe
	  	if ($reg[id_proyecto] != 42) {
	  ?>

	  <tr class="TxtTabla">
        <td><? echo strtoupper($reg[nombre]); ?></td>
        <td width="5%" align="center"><? echo $reg[codigo] ?></td>
        <td width="5%" align="center"><? echo $reg[cargo_defecto] ?></td>
        <td width="10%" align="center">
		<?
		$cantAprobados = 0;
		$cantFacturacion = 0;
		$faltaAprobar = 0;
		//Calcula cuántos registros están con aprobación de facturación
		$cSqlAF="select count(*) cuantosAprobados from AprobacionFacHT ";
		$cSqlAF=$cSqlAF." where id_proyecto = " . $reg[id_proyecto] ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$cSqlAF=$cSqlAF." and mes = month(getdate()) " ;
			$cSqlAF=$cSqlAF." and vigencia = year(getdate()) " ;
		}
		else {
			$cSqlAF=$cSqlAF." and mes = " . $pMes;
			$cSqlAF=$cSqlAF." and vigencia = " . $pAno;
		}
		$cSqlAF=$cSqlAF." and validaEncargado = 1 " ;
		$cCursorAF = mssql_query($cSqlAF);
		if ($cRegAF=mssql_fetch_array($cCursorAF)) {
			$cantAprobados = $cRegAF[cuantosAprobados];
		}
//echo $cSqlAF . "<br>";

		//Cálcula cuántas unidades tienen facturación en ese proyecto
		$cSqlUF="select count(*) cuantosConFacturacion from  ";
		$cSqlUF=$cSqlUF." 	( ";
		$cSqlUF=$cSqlUF." 	select count(*) cuantosHay from horas ";
		$cSqlUF=$cSqlUF."   where id_proyecto =" . $reg[id_proyecto] ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$cSqlUF=$cSqlUF." and month(fecha) = month(getdate()) ";
			$cSqlUF=$cSqlUF." and year(fecha) = year(getdate())";
		}
		else {
			$cSqlUF=$cSqlUF." and month(fecha) = " . $pMes;
			$cSqlUF=$cSqlUF." and year(fecha) = " . $pAno;
		}
		$cSqlUF=$cSqlUF." 	group by  unidad ) A ";
		$cCursorUF = mssql_query($cSqlUF);
		if ($cRegUF=mssql_fetch_array($cCursorUF)) {
			$cantFacturacion = $cRegUF[cuantosConFacturacion];
		}
//echo $cSqlUF . "<br>";		
		$faltaAprobar = $cantFacturacion - $cantAprobados;
//echo $faltaAprobar . "<br>";				
?>
<?
		if ($faltaAprobar == 0) { ?>
		<img src="../portal/images/Aprobado.gif" alt="Todos los registros aprobados" width="21" height="24" /> 
<?		}
		else { ?>
		<img src="../portal/images/NoAprobado.gif" alt="Registros sin aprobar" width="20" height="22" />
<?		}		?>
		</td>
        <td width="5%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstProyectosHTDetalle.php?cualProyecto=<? echo $reg[id_proyecto]; ?>&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Detalle Facturaci&oacute;n" /></td>
        <td width="5%" align="center">
		<?
		$pCuantosViatican = 0;
		//Verificar cuantas personas viaticaron en el proyecto
		$sqlVV="SELECT COUNT(*) cuantosViaticaron
			FROM (
				SELECT DISTINCT unidad
				FROM ViaticosProyecto ";
		$sqlVV=$sqlVV." where id_proyecto =" .  $reg[id_proyecto] ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlVV=$sqlVV." and month(FechaIni) = month(getdate()) ";
			$sqlVV=$sqlVV." and year(FechaIni) = year(getdate())";
		}
		else {
			$sqlVV=$sqlVV." and month(FechaIni) = " . $pMes;
			$sqlVV=$sqlVV." and year(FechaIni) = " . $pAno;
		}
		$sqlVV=$sqlVV." ) A " ;
		$cursorVV = mssql_query($sqlVV);
		if ($regVV=mssql_fetch_array($cursorVV)) {
			$pCuantosViatican = $regVV[cuantosViaticaron];
		}

		echo $pCuantosViatican;
		?>		</td>
	    <td width="5%" align="center"><?
		$pCuantosAprobados = 0;
		//Verificar cantidad de personas que ya se les aprobó los viáticos
		$sqlVA="SELECT COUNT(*) cuantosAprobados ";
		$sqlVA=$sqlVA." FROM dbo.AprobacionViaticosHT ";
		$sqlVA=$sqlVA." WHERE id_proyecto = " .  $reg[id_proyecto] ;
		$sqlVA=$sqlVA." and validaEncargado = '1' ";
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlVA=$sqlVA." and mes = month(getdate()) ";
			$sqlVA=$sqlVA." and vigencia = year(getdate())";
		}
		else {
			$sqlVA=$sqlVA." and mes = " . $pMes;
			$sqlVA=$sqlVA." and vigencia = " . $pAno;
		}
		$cursorVA = mssql_query($sqlVA);
		if ($regVA=mssql_fetch_array($cursorVA)) {
			$pCuantosAprobados = $regVA[cuantosAprobados];
		}
		echo $pCuantosAprobados;

		?></td>
	    <td width="5%" align="center"><?
		//Verificar si hay por aprobar
		$cantSinAprobar = $pCuantosViatican - $pCuantosAprobados ;

		if ($cantSinAprobar == 0) { ?>
		<img src="../portal/images/Aprobado.gif" alt="Todos los viaticos aprobados" width="21" height="24" /> 
<?		}
		else { ?>
		<img src="../portal/images/NoAprobado.gif" alt="Viáticos sin aprobar" width="20" height="22" />
<?		}		?></td>
	    <td width="3%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstApruebaViaticosHT.php?cualProyecto=<? echo $reg[id_proyecto]; ?>&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Detalle Vi&aacute;ticos" /></td>
	  </tr>
			<? } // Si el proyecto no es gastos generales ?>
	  <? } ?>
	  <?
	  //8Nov2007
	  //Muestra la fila si existen usuarios a cargo para aprobar Gastos Generales
		if ( $esRevisarGG != 0 ) {
	  ?>
	  <tr class="TxtTabla">
	    <td>GASTOS GENERALES </td>
	    <td width="5%" align="center">99</td>
	    <td width="5%" align="center">9</td>
	    <td align="center">
		<?
		$cantAprobados = 0;
		$cantFacturacion = 0;
		$faltaAprobar = 0;
		//Calcula cuántos registros están con aprobación de facturación
		$cSqlAF="select count(*) cuantosAprobados from AprobacionFacHT ";
		$cSqlAF=$cSqlAF." where id_proyecto = 42 " ; //Corresponde a Gastos Generales
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$cSqlAF=$cSqlAF." and mes = month(getdate()) " ;
			$cSqlAF=$cSqlAF." and vigencia = year(getdate()) " ;
		}
		else {
			$cSqlAF=$cSqlAF." and mes = " . $pMes;
			$cSqlAF=$cSqlAF." and vigencia = " . $pAno;
		}
		$cSqlAF=$cSqlAF." and validaEncargado = 1 " ;
		$cCursorAF = mssql_query($cSqlAF);
		if ($cRegAF=mssql_fetch_array($cCursorAF)) {
			$cantAprobados = $cRegAF[cuantosAprobados];
		}
//echo $cSqlAF . "<br>";

		//Cálcula cuántas unidades tienen facturación en ese proyecto
		$cSqlUF="select count(*) cuantosConFacturacion from  ";
		$cSqlUF=$cSqlUF." 	( ";
		$cSqlUF=$cSqlUF." 	select count(*) cuantosHay from horas ";
		$cSqlUF=$cSqlUF."   where id_proyecto = 42 "; // corresponde a Gastos Generales 
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$cSqlUF=$cSqlUF." and month(fecha) = month(getdate()) ";
			$cSqlUF=$cSqlUF." and year(fecha) = year(getdate())";
		}
		else {
			$cSqlUF=$cSqlUF." and month(fecha) = " . $pMes;
			$cSqlUF=$cSqlUF." and year(fecha) = " . $pAno;
		}
		$cSqlUF=$cSqlUF." 	group by  unidad ) A ";
		$cCursorUF = mssql_query($cSqlUF);
		if ($cRegUF=mssql_fetch_array($cCursorUF)) {
			$cantFacturacion = $cRegUF[cuantosConFacturacion];
		}
//echo $cSqlUF . "<br>";		
		$faltaAprobar = $cantFacturacion - $cantAprobados;
//echo $faltaAprobar . "<br>";				
?>
<?
		if ($faltaAprobar == 0) { ?>
		<img src="../portal/images/Aprobado.gif" alt="Todos los registros aprobados" width="21" height="24" /> 
<?		}
		else { ?>
		<img src="../portal/images/NoAprobado.gif" alt="Registros sin aprobar" width="20" height="22" />
<?		}		?>
		</td>
	    <td>
		<input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstProyectosHTDetalle.php?cualProyecto=42&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Detalle Facturaci&oacute;n" />
		</td>
	    <td width="5%" align="center">
		<?
		$pCuantosViatican = 0;
		//Verificar cuantas personas viaticaron en el proyecto
		$sqlVV="SELECT COUNT(*) cuantosViaticaron
			FROM (
				SELECT DISTINCT unidad
				FROM ViaticosProyecto ";
		$sqlVV=$sqlVV." where id_proyecto = 42"  ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlVV=$sqlVV." and month(FechaIni) = month(getdate()) ";
			$sqlVV=$sqlVV." and year(FechaIni) = year(getdate())";
		}
		else {
			$sqlVV=$sqlVV." and month(FechaIni) = " . $pMes;
			$sqlVV=$sqlVV." and year(FechaIni) = " . $pAno;
		}
		$sqlVV=$sqlVV." ) A " ;
		$cursorVV = mssql_query($sqlVV);
		if ($regVV=mssql_fetch_array($cursorVV)) {
			$pCuantosViatican = $regVV[cuantosViaticaron];
		}

		echo $pCuantosViatican;
		?>
		</td>
	    <td width="5%" align="center">
		<?
		$pCuantosAprobados = 0;
		//Verificar cantidad de personas que ya se les aprobó los viáticos
		$sqlVA="SELECT COUNT(*) cuantosAprobados ";
		$sqlVA=$sqlVA." FROM dbo.AprobacionViaticosHT ";
		$sqlVA=$sqlVA." WHERE id_proyecto = 42 "  ;
		$sqlVA=$sqlVA." and validaEncargado = '1' ";
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlVA=$sqlVA." and mes = month(getdate()) ";
			$sqlVA=$sqlVA." and vigencia = year(getdate())";
		}
		else {
			$sqlVA=$sqlVA." and mes = " . $pMes;
			$sqlVA=$sqlVA." and vigencia = " . $pAno;
		}
		$cursorVA = mssql_query($sqlVA);
		if ($regVA=mssql_fetch_array($cursorVA)) {
			$pCuantosAprobados = $regVA[cuantosAprobados];
		}
		echo $pCuantosAprobados;

		?>
		</td>
	    <td width="5%" align="center">
		<?
		//Verificar si hay por aprobar
		$cantSinAprobar = $pCuantosViatican - $pCuantosAprobados ;

		if ($cantSinAprobar == 0) { ?>
		<img src="../portal/images/Aprobado.gif" alt="Todos los viaticos aprobados" width="21" height="24" /> 
<?		}
		else { ?>
		<img src="../portal/images/NoAprobado.gif" alt="Viáticos sin aprobar" width="20" height="22" />
<?		}		?>
		</td>
	    <td width="3%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstApruebaViaticosHT.php?cualProyecto=42&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Detalle Vi&aacute;ticos" /></td>
	  </tr>
		<? } // cierra if $esRevisarGG != 0 ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja tiempo" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
