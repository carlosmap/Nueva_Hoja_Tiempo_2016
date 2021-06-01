<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

if ($pMes == "") {
echo ("<script>alert('Versión en prueba. No hacer uso oficial');</script>");
}
//9May2008
//Trae el listado de proyectos donde tiene facturación
$sqlF="select A.id_proyecto, sum(A.tiempo_asignado) tiempoAsignado, P.codigo, P.cargo_defecto, P.nombre ";
$sqlF=$sqlF." from asignaciones A, proyectos P ";
$sqlF=$sqlF." where A.id_proyecto = P.id_proyecto ";
$sqlF=$sqlF."and A.unidad = " . $laUnidad;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sqlF=$sqlF." and month(A.fecha_inicial)= month(getdate()) ";
	$sqlF=$sqlF." and year(A.fecha_inicial)= year(getdate()) ";
}
else {
	$sqlF=$sqlF." and month(A.fecha_inicial)= " . $pMes;
	$sqlF=$sqlF." and year(A.fecha_inicial)= " . $pAno;
}
$sqlF=$sqlF." and (A.clase_tiempo = 1 OR A.clase_tiempo = 2 ) ";
$sqlF=$sqlF." group by A.id_proyecto, P.codigo, P.cargo_defecto, P.nombre ";
$sqlF=$sqlF." order by P.nombre ";
$cursorF = mssql_query($sqlF);

//9May2008
//Trae el listado de proyectos y horas solicitadas para el periodo selecciona
$sqlW="SELECT S.* , P.codigo, P.cargo_defecto, P.nombre ";
$sqlW=$sqlW." FROM SolicitudHoras S, proyectos P ";
$sqlW=$sqlW." where S.id_proyecto = P.id_proyecto ";
$sqlW=$sqlW." and S.unidad = " . $laUnidad;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sqlW=$sqlW." and S.mes = month(getdate()) ";
	$sqlW=$sqlW." and S.vigencia = year(getdate()) ";
}
else {
	$sqlW=$sqlW." and S.mes = " . $pMes;
	$sqlW=$sqlW." and S.vigencia = " . $pAno;
}
$sqlW=$sqlW." order by P.nombre ";
$cursorW = mssql_query($sqlW);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempoSHoras";

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
	<div class="TxtNota1" style="position:absolute; left:2px; top:55px; width: 365px;">
		<div class="TxtNota2"> Solicitud de Programaci&oacute;n </div>
</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><?
		echo strtoupper($nombreempleado." ".$apellidoempleado);
	?></td>
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
    <td class="TituloUsuario">Proyectos con programaci&oacute;n para el periodo seleccionado </td>
  </tr>
</table>
    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr class="TituloTabla2">
              <td width="15%">C&oacute;digo</td>
              <td>Proyecto</td>
              <td width="15%">Cantidad de Horas Programadas </td>
            </tr>
       <?  while ($regF=mssql_fetch_array($cursorF)) {	  ?>
            <tr class="TxtTabla">
              <td width="15%"><? echo $regF[codigo] . "." . $regF[cargo_defecto] ; ?></td>
              <td><? echo ucwords(strtolower($regF[nombre])); ?></td>
              <td width="15%" align="right"><? echo $regF[tiempoAsignado]; ?></td>
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
              <td class="TituloUsuario">Horas solicitadas de programaci&oacute;n para el periodo seleccionado </td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr class="TituloTabla2">
              <td width="10%">C&oacute;digo</td>
              <td>Proyecto</td>
              <td width="10%">Cantidad de Horas Programadas </td>
              <td width="3%">Aprob</td>
              <td>Observaciones</td>
              <td width="1%">&nbsp;</td>
              <td width="1%">&nbsp;</td>
            </tr>
       <?  while ($regW=mssql_fetch_array($cursorW)) {	  ?>
            <tr class="TxtTabla">
              <td width="10%"><? echo $regW[codigo] . "." . $regW[cargo_defecto] ; ?></td>
              <td><? echo ucwords(strtolower($regW[nombre])); ?></td>
              <td width="10%"><? echo $regW[cantidadHoras]; ?></td>
              <td width="3%">
			  <? if ( ($regW[validaDirector] == "0") AND (trim($regW[comentaDirector]) != "") ) {  ?>
			  	<img src="img/images/No.gif" alt="No aprobado" width="12" height="16" />			  
				<? }  ?>
			  <? if ($regW[validaDirector] == "1") {  ?>
			  	<img src="img/images/Si.gif" alt="No aprobado" width="16" height="14" />			  
				<? }  ?>
				
			  </td>
              <td><? echo $regW[comentaDirector]; ?></td>
              <td width="1%">
			  <? if ($regW[validaDirector]!=1) { ?>
			  <a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upsolProgramacion.php?cualMes=<? echo $pMes; ?>&cualAno=<? echo $pAno; ?>&cualSec=<? echo $regW[secuencia]; ?>','vupsp','scrollbars=yes,resizable=yes,width=710,height=250')" /></a>
			  <? } ?>
			  </td>
              <td width="1%">
			  <? if ($regW[validaDirector]!=1) { ?>
			  <a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delsolProgramacion.php?cualMes=<? echo $pMes; ?>&cualAno=<? echo $pAno; ?>&cualSec=<? echo $regW[secuencia]; ?>','vupsp','scrollbars=yes,resizable=yes,width=710,height=250')" /></a>
			  <? } ?>
			  </td>
            </tr>
		<? } ?>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addsolProgramacion.php?cualMes=<? echo $pMes; ?>&cualAno=<? echo $pAno; ?>','adSolP','scrollbars=yes,resizable=yes,width=700,height=250')" value="Ingresar" /></td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" />            <input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosSolHoras.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>');return document.MM_returnValue" value="Revisar/Programar Horas solicitadas" />
            <input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosSolHorasAut.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>');return document.MM_returnValue" value="Procesar VoBo" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
