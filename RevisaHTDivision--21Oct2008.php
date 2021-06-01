<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$sqlDiv="Select * from divisiones ";
$sqlDiv=$sqlDiv." where (nombre <> '' and nombre <> 'sd') ";
$sqlDiv=$sqlDiv." order by nombre ";
$cursorDiv = mssql_query($sqlDiv);

if (trim($pDivision) == "" ) {
	$pDivision = 11;
}


//Lista los usuarios por división para conocer su Hoja de tiempo del periodo seleccionado

$sql="Select U.unidad, U.nombre, U.apellidos, U.id_departamento, U.id_categoria, ";
$sql=$sql." C.nombre nomCategoria, D.nombre nomDpto, A.vigencia, A.mes, A.fechaEnvio,  " ;
$sql=$sql." A.unidadJefe, A.validaJefe, A.unidadContratos, A.validaContratos, A.comentaContratos, A.comentaJefe " ;
$sql=$sql." from usuarios U, categorias C, departamentos D, AutorizacionesHT A " ;
$sql=$sql." where U.id_categoria = C.id_categoria " ;
$sql=$sql." and U.id_departamento = D.id_departamento " ;
$sql=$sql." and U.unidad *= A.unidad " ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql= $sql. " AND A.mes = month(getdate()) ";
	$sql= $sql. " AND A.vigencia = year(getdate()) ";
}
else {
	$sql= $sql. " AND A.mes = " . $pMes;
	$sql= $sql. " AND A.vigencia =  " . $pAno;
}
$sql=$sql." and D.id_division =  " . $pDivision ;
if ($pDivision != "") {
	if ($miDpto != "") {
		$sql=$sql." and D.id_departamento =  " . $miDpto ;
	}
}
if (trim($pFiltro) != "") {
	$sql=$sql." and U.id_categoria =  " . $pFiltro ;
}
$sql=$sql." and U.retirado is null " ;
$sql=$sql." order by U.apellidos  ";
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
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center">Hojas de tiempo por División</div>
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
    <td align="right" class="TituloTabla">Divisi&oacute;n:</td>
    <td colspan="3" class="TxtTabla">
	<select name="pDivision" class="CajaTexto" onChange="document.form1.submit();" >
	<? while ($regDiv=mssql_fetch_array($cursorDiv)) { 	
			if ($pDivision == $regDiv[id_division]) {
				$selDiv = "selected";
			}
			else {
				$selDiv = "";
			}
	
	?>
      	<option value="<? echo $regDiv[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($regDiv[nombre])) ; ?></option>
	<? } ?> 
	
	<? if ($pDivision == "888") { 
			$selDiv = "selected";
		}
	?>
	<option value="888" <? echo $selDiv; ?> ><? echo ":::Sin División:::" ; ?></option>
	
    </select>
	</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Departamento:</td>
    <td colspan="3" class="TxtTabla">
	<?
	//Trae los departamentos asociados la división seleccionada
	$dTSql="Select * from departamentos where id_division = " . $pDivision ;
	$dTcursor = mssql_query($dTSql);
	
	?>
	<select name="miDpto" class="CajaTexto" id="miDpto" onChange="document.form1.submit();" >
	<? if ($miDpto == "") { 
			$selItem="selected";
		}
	?>
		<option value="" <? echo $selItem; ?> >:::Todos:::</option>
	<? while ($regdT=mssql_fetch_array($dTcursor)) { 
			if ($miDpto == $regdT[id_departamento]) {
				$selIt="selected";
			}
			else {
				$selIt="";
			}
	?>
	  	<option value="<? echo $regdT[id_departamento]; ?>" <? echo $selIt; ?> ><? echo ucwords(strtolower($regdT[nombre])) ; ?></option>
	<? } ?>
    </select>
	</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Categor&iacute;a:</td>
    <td colspan="3" class="TxtTabla"><? 
		$fSql="SELECT * FROM categorias";
		$fCursor = mssql_query($fSql);
		if (trim($pFiltro) == "") {
			$selFiltro = "selected";
		}
		?>		<select name="pFiltro" class="CajaTexto" id="pFiltro" onChange="document.form1.submit();" >
		<option value="" <? echo $selFiltro ; ?> >:::Todas:::</option>
	   <? 
	   	while ($fReg=mssql_fetch_array($fCursor)) {
	   		if ($pFiltro == $fReg[id_categoria]) {
				$selFiltro="selected";
			}
			else {
				$selFiltro="";
			}
	    ?>
          <option value="<? echo $fReg[id_categoria] ; ?>" <? echo $selFiltro; ?> ><? echo $fReg[nombre] ; ?></option>
	   <? } ?>
        </select></td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
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
    <td width="10%" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
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
    <td class="TituloUsuario"> Estado Hojas de tiempo por Divisi&oacute;n </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%" rowspan="2">Unidad</td>
        <td rowspan="2">Categoria</td>
        <td rowspan="2">Usuarios que solicita la revisi&oacute;n </td>
        <td width="5%" rowspan="2">&iquest;Envi&oacute;<br />
          HT?</td>
        <td colspan="2">Aprobaci&oacute;n del Jefe </td>
        <td colspan="2">Contratos</td>
        <td width="5%" rowspan="2">&nbsp;</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="5%">Aprobado</td>
        <td width="20%">Quien firma </td>
        <td width="5%">Aprobado</td>
        <td width="20%">Quien aprueba </td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>
        <td><? echo $reg[nomCategoria]; ?></td>
        <td><? echo ucwords(strtolower($reg[apellidos]  . " " . $reg[nombre])); ?></td>
        <td width="5%" align="center">
		<?
		if ($reg[unidadJefe] != "") {
			echo "SI";
		}
		else {
			echo "NO";
		}
		?>
		</td>
        <td width="5%" align="center">
		<? if ($reg[validaJefe] == "1") { ?>
			<img src="img/images/Si.gif" />
		<? } ?>

		<? if (($reg[validaJefe] == "0") AND (trim($reg[comentaJefe]) != "")) { ?>
			<img src="img/images/No.gif" />
		<? } ?>

		</td>
        <td width="20%">
		<? 
		$uJsql="select * from usuarios where unidad = " . $reg[unidadJefe] ;
		$uJcursor = mssql_query($uJsql);
		if ($uJreg=mssql_fetch_array($uJcursor)) { 
			echo ucwords(strtolower($uJreg[apellidos]  . ", " . $uJreg[nombre]));
		}
		
		?>
		
		</td>
        <td width="5%">
		<? if ($reg[validaJefe] == "1") { ?>
			<img src="img/images/Si.gif" />
		<? } ?>

		<? if (($reg[validaJefe] == "0") AND (trim($reg[comentaJefe]) != "")) { ?>
			<img src="img/images/No.gif" />
		<? } ?>
		</td>
        <td width="20%">
		<? 
		$uJsql="select * from usuarios where unidad = " . $reg[unidadContratos] ;
		$uJcursor = mssql_query($uJsql);
		if ($uJreg=mssql_fetch_array($uJcursor)) { 
			echo ucwords(strtolower($uJreg[apellidos]  . ", " . $uJreg[nombre]));
		}
		
		?>
		</td>
        <td width="5%"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','verHTDivision.php?zUnidad=<? echo $reg[unidad]; ?>&Flmes=<? echo $reg[mes]; ?>&Flano=<? echo $reg[vigencia]; ?>');return document.MM_returnValue" value="Ver Hoja" /></td>
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
            <td class="TxtTabla"><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
