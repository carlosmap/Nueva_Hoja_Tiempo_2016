<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer llos proyectos donde el usuario activo est? involucrado como los directores y/o encargado
$sql="Select * ";
$sql=$sql." from Proyectos " ;
$sql=$sql." where id_estado = 2" ;
$sql=$sql." and (id_director =" . $laUnidad . " or id_coordinador = ". $laUnidad . " ) " ;
//echo $sql;
$cursor = mssql_query($sql);

//8Nov2007
//Verifica si la unidad Activa est? dentro de la tabla RevisaGastosGenerales
//como jefe de algunos usuarios para la aprobac?n de los Gastos Generales
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
    <td class="TituloUsuario">Consulta de aprobaci?n para otros periodos </td>
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
        <td>Proyecto</td>
        <td width="10%">Aprobaci&oacute;n</td>
        <td width="10%">C&oacute;digo</td>
        <td width="10%">Cargo Defecto </td>
        <td width="5%">&nbsp;</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  	//8Nov2007
		//Si el proyecto es Gastos Generales no aparece por aqu? porque debe aparecer para que apruebe su respectivo jefe
	  	if ($reg[id_proyecto] != 42) {
	  ?>

	  <tr class="TxtTabla">
        <td><? echo strtoupper($reg[nombre]); ?></td>
        <td width="10%" align="center">
		<?
		$cantAprobados = 0;
		$cantFacturacion = 0;
		$faltaAprobar = 0;
		//Calcula cu?ntos registros est?n con aprobaci?n de facturaci?n
		$cSqlAF="select count(*) cuantosAprobados from AprobacionFacHT ";
		$cSqlAF=$cSqlAF." where id_proyecto = " . $reg[id_proyecto] ;
		//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
		//sino con lo seleccionado en las listas mes y a?o
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

		//C?lcula cu?ntas unidades tienen facturaci?n en ese proyecto
		$cSqlUF="select count(*) cuantosConFacturacion from  ";
		$cSqlUF=$cSqlUF." 	( ";
		$cSqlUF=$cSqlUF." 	select count(*) cuantosHay from horas ";
		$cSqlUF=$cSqlUF."   where id_proyecto =" . $reg[id_proyecto] ;
		//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
		//sino con lo seleccionado en las listas mes y a?o
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
        <td width="10%" align="center">
		<? echo $reg[codigo] ?>
		</td>
        <td width="10%" align="center"><? echo $reg[cargo_defecto] ?></td>
        <td width="5%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstProyectosHTDetalle.php?cualProyecto=<? echo $reg[id_proyecto]; ?>&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Detalle" /></td>
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
	    <td align="center">
		<?
		$cantAprobados = 0;
		$cantFacturacion = 0;
		$faltaAprobar = 0;
		//Calcula cu?ntos registros est?n con aprobaci?n de facturaci?n
		$cSqlAF="select count(*) cuantosAprobados from AprobacionFacHT ";
		$cSqlAF=$cSqlAF." where id_proyecto = 42 " ; //Corresponde a Gastos Generales
		//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
		//sino con lo seleccionado en las listas mes y a?o
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

		//C?lcula cu?ntas unidades tienen facturaci?n en ese proyecto
		$cSqlUF="select count(*) cuantosConFacturacion from  ";
		$cSqlUF=$cSqlUF." 	( ";
		$cSqlUF=$cSqlUF." 	select count(*) cuantosHay from horas ";
		$cSqlUF=$cSqlUF."   where id_proyecto = 42 "; // corresponde a Gastos Generales 
		//filtra el resultado de la consulta si la p?gina se carga por primera vez con el mes y a?o actual
		//sino con lo seleccionado en las listas mes y a?o
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
	    <td align="center">99</td>
	    <td align="center">9</td>
	    <td>
		<input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstProyectosHTDetalle.php?cualProyecto=42&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Detalle" />
		</td>
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
