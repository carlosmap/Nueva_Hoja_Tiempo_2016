<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	

//24Jul2013
//PBM
//Inicializa el valor de las listas Mes y vigencia 
if ( (trim($pMes) == "") AND (trim($recarga)=="") ) {
	$pMes=date("m");
}
if ( (trim($pAno) == "") AND (trim($recarga)=="") ) {
	$pAno=date("Y");
}


//24Jul2013
//PBM
//--Trae la planeación de una persona para un mes y año seleccionados
$sql01="SELECT A.id_proyecto, A.unidad, A.vigencia, A.mes, SUM(A.hombresMes) totHombresMes, SUM(A.horasMes) totHorasMes, B.nombre, B.codigo, B.cargo_defecto ";
$sql01=$sql01." FROM PlaneacionProyectos A, Proyectos B " ;
$sql01=$sql01." WHERE A.id_proyecto = B.id_proyecto " ;
$sql01=$sql01." AND A.unidad = " . $laUnidad ;
$sql01=$sql01." AND A.vigencia = " . $pAno ;
$sql01=$sql01." AND A.mes = " . $pMes ;
$sql01=$sql01." GROUP BY A.id_proyecto, A.unidad, A.vigencia, A.mes, B.nombre, B.codigo, B.cargo_defecto " ;
$cursor01 =	 mssql_query($sql01);





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winFacturacionHT";

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>


<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 557px; height: 30px;">
Hoja de tiempo - Facturaci&oacute;n de proyectos</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="Fecha"><? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
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
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  <tr>
    <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	$selMesTodos= "";
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		if ($pMes == "TODOS") {
			$selMesTodos= "selected"; //el mes seleccionado
		}
		else {
			$mesActual= $pMes; //el mes seleccionado
		}
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

    </select>
	<input name="recarga" type="hidden" id="recarga" value="1" />	</td>
    <td width="10%" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
</table></td>
      </tr>
    </table>

<script type="text/javascript" language="javascript">

function envia()
{
	document.form1.ban.value=2;


}

//-->
</script>



<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td align="center" class="TxtTabla">&nbsp;
	</td>
  </tr>
  <tr>
	<td height="1" align="center" class="TituloTabla"> </td>
  </tr>
</table>
<!-- No. de Registros -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">   Planeaci&oacute;n de proyectos para <? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
  </tr>
  <tr>
    <td align="right" class="TxtTabla">

	</td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Proyecto</td>
            <td>Hombres/Mes</td>
            <td>Horas/Mes</td>
            <td width="5%">&nbsp;</td>
          </tr>
		  <?
			while ($reg01 = mssql_fetch_array($cursor01)) {
		  ?>
          <tr class="TxtTabla">
            <td><? echo " [" . $reg01['codigo'] . "." . $reg01['cargo_defecto'] . "] " . $reg01['nombre'] ; ?></td>
            <td><? echo $reg01['totHombresMes']; ?></td>
            <td><? echo $reg01['totHorasMes']; ?></td>
            <td width="5%" align="center"><input name="Submit2" type="submit" class="Boton" value="Facturar" /></td>
          </tr>
		  <?
		  }
		  ?>
        </table></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><input name="Submit" type="submit" class="Boton" onclick="MM_callJS('window.close();')" value="Cerrar ventana" /></td>
  </tr>
</table>
</body>
</html>
