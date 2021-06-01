<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer la información del proyecto seleccionado
$sql="Select * ";
$sql=$sql." from Proyectos " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDProyecto = $reg[id_proyecto];
	$elProyecto = $reg[nombre];
	$elCodigo = $reg[codigo];
	$elCargoDef = $reg[cargo_defecto];
}



//--Traer las personas relacionadas a la facturación de un proyecto en un mes  y año especificos
$sql="Select H.unidad, U.nombre, U.apellidos, sum(horas_registradas) as totalHorasR ";
$sql=$sql." from horas H, usuarios U " ;
$sql=$sql." where H.unidad = U.unidad " ;
$sql=$sql." and H.id_proyecto = " . $cualProyecto ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql=$sql." and month(H.fecha) = month(getdate()) " ;
	$sql=$sql." and year(H.fecha) = year(getdate()) " ;
	$mesEnvio = date("m");
	$anoEnvio=date("Y");
}
else {
	$sql=$sql." and month(H.fecha) = " . $pMes;
	$sql=$sql." and year(H.fecha) = " . $pAno;
	$mesEnvio =  $pMes;
	$anoEnvio= $pAno;
}
if (trim($pCargo) != "") {
	$sql=$sql." and H.cargo = " . trim($pCodigo) . trim($pCargo) ;
}


//8Nov2007
//Si el proyecto es Gastos generales = 42, debe filtrar por los usuarios que asignaron la unidad activa
//como persona que revisará su proyecto de hoja de tiempo
if ($cualProyecto == 42) {
	$sql=$sql." and EXISTS " ;
	$sql=$sql." 	( " ;
	$sql=$sql." 	SELECT * FROM RevisaGastosGenerales " ;
	$sql=$sql." 	WHERE unidad = H.unidad " ;
	$sql=$sql." 	and  unidadRevisa = " . $laUnidad;
	$sql=$sql." 	) " ;
}
//fin 8NOv2007

$sql=$sql." group by H.unidad, U.nombre, U.apellidos " ;
$sql=$sql." order by U.apellidos " ;
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
<title>Revisión de hojas de tiempo</title>
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
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
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
    <td width="10%" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">C&oacute;digo - Cargo </td>
    <td colspan="3" class="TxtTabla"><input name="pCodigo" type="text" class="CajaTexto" id="pCodigo" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<? echo $elCodigo ; ?>" size="10" readonly  /> 
      - 
        <input name="pCargo" type="text" class="CajaTexto" id="pCargo" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<? echo $pCargo; ?>" size="10" /></td>
    <td><input name="Submit8" type="submit" class="Boton" value="Consultar" /></td>
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
        <td class="TituloUsuario">Informaci&oacute;n del proyecto </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Nombre</td>
        <td width="10%">C&oacute;digo</td>
        <td width="10%">Cargo</td>
      </tr>
      <tr class="TxtTabla">
        <td><? echo strtoupper($elProyecto) ; ?></td>
        <td width="10%"><? echo $elCodigo ; ?></td>
        <td width="10%"><? echo $elCargoDef ; ?></td>
      </tr>
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
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo del Director o encargado </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Unidad</td>
        <td>Usuarios que facturaron al Proyecto </td>
        <td>Horas Programadas </td>
        <td>Horas Registradas </td>
        <td width="2%">C&oacute;digo<br />
          Cargo<br />
          Registrado</td>
        <td width="12">Aprobado</td>
        <td width="15%">Quien aprob&oacute; </td>
        <td>Comentarios</td>
        <td width="1%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>
        <td><? echo ucwords(strtolower($reg[apellidos] . " " . $reg[nombre])) ; ?></td>
        <td width="5%" align="center">
		<?
		//08Mar2011
		//PBM
		//Muestra la cantidad de horas programadas para el código
		$prSql="SELECT SUM(tiempo_asignado) as tiempo_asignado ";
		$prSql=$prSql." FROM Asignaciones ";
		$prSql=$prSql." where id_proyecto =" . $cualProyecto;
		$prSql=$prSql." and MONTH(fecha_inicial) =". $mesEnvio ;
		$prSql=$prSql." and YEAR(fecha_inicial) =" . $anoEnvio;
		$prSql=$prSql." AND unidad =". $reg[unidad];
		$cursorpr = mssql_query($prSql);
		if ($regpr=mssql_fetch_array($cursorpr)) {
			echo $regpr[tiempo_asignado] . "<br>";
		}
		
		?>
		</td>
        <td width="5%" align="center">
		<? echo $reg[totalHorasR]; ?>		</td>
        <td width="2%" align="center">
		<?
		//25Jun2009
		//Patricia Barón Manrique
		//Muestra los cargos a los que facturó la persona
		$sqlCF="select distinct cargo ";
		$sqlCF=$sqlCF." from horas where unidad =" . $reg[unidad];;
		$sqlCF=$sqlCF." and month(fecha)= " . $mesEnvio ;
		$sqlCF=$sqlCF." and year(fecha)= " . $anoEnvio;
		$sqlCF=$sqlCF." and id_proyecto =" . $cualProyecto;
		$cursorCF = mssql_query($sqlCF);
		while ($regCF=mssql_fetch_array($cursorCF)) {
			echo $regCF[cargo] . "<br>";
		}
			
		?>
		</td>
        <td width="12" align="center">
		<?
		$laAprobacion = "";
		$elQueAprueba = "";
		$comentaAprueba = "";
		
		$sqlA="Select A.*, U.nombre, U.apellidos ";
		$sqlA=$sqlA." from AprobacionFacHT A, Usuarios U " ;
		$sqlA=$sqlA." where A.unidadEncargado *= U.unidad "  ;
		$sqlA=$sqlA." and A.id_proyecto = " . $elIDProyecto ;
		$sqlA=$sqlA." and A.unidad = " . $reg[unidad] ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlA=$sqlA." and A.mes = month(getdate()) " ;
			$sqlA=$sqlA." and A.vigencia = year(getdate()) " ;
		}
		else {
			$sqlA=$sqlA." and A.mes = " . $pMes;
			$sqlA=$sqlA." and A.vigencia = " . $pAno;
		}
		$cursorA = mssql_query($sqlA);
		if ($regA=mssql_fetch_array($cursorA)) {
			$laAprobacion = $regA[validaEncargado] ; 
			$elQueAprueba = $regA[nombre] . " " . $regA[apellidos] ;
			$comentaAprueba = $regA[comentaEncargado] ;
		}

		?>


				<? 
		if ($laAprobacion == "1") {
			echo "SI" ; 
		}
		if (($laAprobacion == "0") AND (trim($comentaAprueba) != "")) {
			echo "NO" ;
		}
		?>		</td>
        <td width="15%" align="center"><? echo $elQueAprueba; ?></td>
        <td width="30%"><? echo $comentaAprueba; ?></td>
        <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('addlstProyectosHTDetalle2.php?cualProyecto=<? echo $cualProyecto; ?>&pMes=<? echo $mesEnvio ?>&pAno=<? echo $anoEnvio ?>&cualUnidad=<? echo $reg[unidad]; ?>','verHT','scrollbars=yes,resizable=yes,width=700,height=400')" /></a></td>
        <td width="1%"><input name="Submit3" type="submit" class="Boton" onclick="MM_openBrWindow('verResumen.php?cualProyecto=<? echo $cualProyecto; ?>&cualUnidad=<? echo $reg[unidad]; ?>&anoAut=<? echo $anoEnvio; ?>&mesAut=<? echo $mesEnvio; ?>&pCargo=<? echo trim($pCodigo) . trim($pCargo); ?>','vResumen','scrollbars=yes,resizable=yes,width=700,height=400')" value="Resumen" /></td>
	  </tr>
	  <? } ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('addlstProyectosHTDetalle.php?cualProyecto=<? echo $cualProyecto; ?>&pMes=<? echo $mesEnvio ?>&pAno=<? echo $anoEnvio ?>','verHT','scrollbars=yes,resizable=yes,width=700,height=400')" value="Tramitar Aprobaci&oacute;n" /></td>
          </tr>
        </table>		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','lstProyectosHT.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno ;?>');return document.MM_returnValue" value="Listado de Proyectos" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
