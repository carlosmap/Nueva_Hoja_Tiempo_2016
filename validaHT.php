<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

if ($pMes == "") {
	$mesActual=12; //el mes que quiere gonzalo
	$AnoActual=2009; //el año que quiere gonzalo
//	$mesActual=date("m"); //el mes actual
//	$AnoActual=date("Y"); //el año actual	
}
else {
	$mesActual= $pMes; //el mes seleccionado
	$AnoActual= $pAno; //el año seleccionado
}

//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$sql="Select * from divisiones order by nombre ";
$cursor = mssql_query($sql);

//--Para traer los usuarios que pertenecen a una división
//con su respectivo total de facturación por días
$sql2="select U.unidad, U.nombre, U.apellidos, U.TipoContrato, D.id_division, C.nombre nomCategoria , D.nombre nomDepto, ";
$sql2=$sql2." V.d1, V.d2, V.d3, V.d4, V.d5, V.d6, V.d7, V.d8, V.d9, V.d10, V.d11, V.d12, V.d13, ";
$sql2=$sql2." V.d14, V.d15, V.d16, V.d17, V.d18, V.d19, V.d20, V.d21, V.d22, V.d23, V.d24, V.d25, ";
$sql2=$sql2." V.d26, V.d27, V.d28, V.d29, V.d30, V.d31 ";
$sql2=$sql2." from usuarios U, departamentos D, categorias C , ValidaUsuHT V  ";
$sql2=$sql2." where U.id_departamento = D.id_departamento ";
$sql2=$sql2." and U.id_categoria = C.id_categoria ";
$sql2=$sql2." and U.unidad *= V.unidad ";
$sql2=$sql2." and V.mes = " . $mesActual ; 
$sql2=$sql2." and V.ano =" . $AnoActual ;
//filtra la primera vez para administrativa, luego para la división seleccionada en la lista
if (trim($pDivision) == "" ) {
	$sql2=$sql2." and D.id_division = 11 ";
}
else {
	//Si se escoge sin división se trae todo lo que no tiene división, es decir está en blanco o es sd
	if (trim($pDivision) == "888") {
		$sql2=$sql2." and D.id_division > 25";
	}
	else {
		$sql2=$sql2." and D.id_division =" . $pDivision;
	}
}
$sql2=$sql2." and U.retirado is null  ";
$sql2=$sql2." order by U.apellidos ";
$cursor2 = mssql_query($sql2);
$cantidadEmpleados = mssql_num_rows($cursor2);

//para traer la fecha de corte de la información
$sql3="select max(fechaRep) fechaCorte,  max(horaRep) horaCorte from ValidaUsuHT ";
$sql3=$sql3." where mes = " . $mesActual ; 
$sql3=$sql3." and ano =" . $AnoActual ;
$cursor3 = mssql_query($sql3);

?>
<html>
<head>
<title>Validaci&oacute;n de la Hoja de Tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winHojaTiempo";
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

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:783px; height:38px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota2">Avance Hojas de Tiempo </td>
    </tr>
  </table>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
    <td width="15%" align="right" class="TituloTabla">División</td>
    <td width="15%" class="TxtTabla">
	<select name="pDivision" class="CajaTexto">
	<? while ($reg=mssql_fetch_array($cursor)) { 	
			if ($pDivision == $reg[id_division]) {
				$selDiv = "selected";
			}
			else {
				$selDiv = "";
			}
			if ((trim($reg[nombre])!= "sd") AND (trim($reg[nombre])!= "")) {
	?>
      	<option value="<? echo $reg[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($reg[nombre])) ; ?></option>
	<? 	} //if
	} //WHILE ?>
	 <?
	 if ($pDivision == "888") {
				$selDiv = "selected";
			}
			else {
				$selDiv = "";
			}
	 ?>
	<option value="888" <? echo $selDiv; ?> ><? echo "Sin División" ; ?></option>
    </select>	</td>
    <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td class="TxtTabla"><? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
/*	
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}
	*/

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
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes" disabled>
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
    </select>		</td>
    <td width="15%" class="TituloTabla2">A&ntilde;o:</td>
    <td width="15%" class="TxtTabla"><select name="pAno" class="CajaTexto" id="pAno" disabled>
      <? 
	//Generar los años de 2007 a 2050
	for($i=2007; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
/*		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}*/
		
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
    </select></td>
    <td width="10%"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
</table>
	</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consolidado del estado de la Hoja de tiempo </td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <table width="100%"  border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td width="21%" class="TituloTabla">Fecha / Hora de corte: </td>
          <td class="TxtTabla">
		  <?
		  //select max(fechaRep), fechaCorte max(horaRep) horaCorte from ValidaUsuHT
			if ($reg3=mssql_fetch_array($cursor3)) {
				echo date("M d Y ", strtotime($reg3[fechaCorte])) . " / " . $reg3[horaCorte] ;
			}
		  ?>
		  </td>
          <td width="10%" align="right" class="TituloTabla">Total de empleados </td>
          <td width="10%" class="TxtTabla"><? echo $cantidadEmpleados ; ?></td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="img/images/Pixel.gif" width="4" height="4"></td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="2" cellpadding="0">
  <tr class="TituloTabla2">
    <td width="5%">&nbsp;</td>
    <td>Usuario</td>
    <td width="15%">Departamento</td>
    <td width="1%">CT</td>
    <td width="2%">1</td>
    <td width="2%">2</td>
    <td width="2%">3</td>
    <td width="2%">4</td>
    <td width="2%">5</td>
    <td width="2%">6</td>
    <td width="2%">7</td>
    <td width="2%">8</td>
    <td width="2%">9</td>
    <td width="2%">10</td>
    <td width="2%">11</td>
    <td width="2%">12</td>
    <td width="2%">13</td>
    <td width="2%">14</td>
    <td width="2%">15</td>
    <td width="2%">16</td>
    <td width="2%">17</td>
    <td width="2%">18</td>
    <td width="2%">19</td>
    <td width="2%">20</td>
    <td width="2%">21</td>
    <td width="2%">22</td>
    <td width="2%">23</td>
    <td width="2%">24</td>
    <td width="2%">25</td>
    <td width="2%">26</td>
    <td width="2%">27</td>
    <td width="2%">28</td>
    <td width="2%">29</td>
    <td width="2%">30</td>
    <td width="2%">31</td>
  </tr>
  <? while ($reg2=mssql_fetch_array($cursor2)) { ?>
  <tr class="TxtTabla">
    <td width="5%"><input name="Submit" type="submit" class="Boton" onClick="MM_goToURL('parent','verhdetiempoValidaHT.php?zUnidad=<? echo $reg2[unidad]; ?>&Flmes=<? echo $mesActual; ?>&Flano=<? echo $AnoActual; ?>');return document.MM_returnValue" value="Ver Hoja" /></td>
    <td><? echo  ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre])) ; ?></td>
    <td width="15%"><? echo  ucwords(strtolower($reg2[nomDepto]))  ; ?></td>
    <td width="1%">
	<? 
	//Define  cuál es la clase de tiempo para el usuario seleccionado
	if (strtoupper($reg2[TipoContrato]) == "TC") {
		$lCTiempo= "1";
	}
	else {
		$lCTiempo= "2";
	}
	echo  strtoupper($lCTiempo); ?></td>
    <td width="2%">
	<? if (trim($reg2[d1]) != "0") {
			echo $reg2[d1]; 
	   }
	?>
	</td>
	<td width="2%">
	<? if (trim($reg2[d2]) != "0") {
			echo $reg2[d2]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d3]) != "0") {
			echo $reg2[d3]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d4]) != "0") {
			echo $reg2[d4]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d5]) != "0") {
			echo $reg2[d5]; 
	   }
	?></td>
    <td width="2%"><? if (trim($reg2[d6]) != "0") {
			echo $reg2[d6]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d7]) != "0") {
			echo $reg2[d7]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d8]) != "0") {
			echo $reg2[d8]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d9]) != "0") {
			echo $reg2[d9]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d10]) != "0") {
			echo $reg2[d10]; 
	   }
	?></td>
    <td width="2%"><? if (trim($reg2[d11]) != "0") {
			echo $reg2[d11]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d12]) != "0") {
			echo $reg2[d12]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d13]) != "0") {
			echo $reg2[d13]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d14]) != "0") {
			echo $reg2[d14]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d15]) != "0") {
			echo $reg2[d15]; 
	   }
	?></td>
    <td width="2%"><? if (trim($reg2[d16]) != "0") {
			echo $reg2[d16]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d17]) != "0") {
			echo $reg2[d17]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d18]) != "0") {
			echo $reg2[d18]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d19]) != "0") {
			echo $reg2[d19]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d20]) != "0") {
			echo $reg2[d20]; 
	   }
	?></td>
    <td width="2%"><? if (trim($reg2[d21]) != "0") {
			echo $reg2[d21]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d22]) != "0") {
			echo $reg2[d22]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d23]) != "0") {
			echo $reg2[d23]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d24]) != "0") {
			echo $reg2[d24]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d25]) != "0") {
			echo $reg2[d25]; 
	   }
	?></td>
    <td width="2%"><? if (trim($reg2[d26]) != "0") {
			echo $reg2[d26]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d27]) != "0") {
			echo $reg2[d27]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d28]) != "0") {
			echo $reg2[d28]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d29]) != "0") {
			echo $reg2[d29]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d30]) != "0") {
			echo $reg2[d30]; 
	   }
	?></td>
	<td width="2%"><? if (trim($reg2[d31]) != "0") {
			echo $reg2[d31]; 
	   }
	?></td>
  </tr>
  <? } ?>
</table>	</td>
  </tr>
</table>

<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onClick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" /></td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td class="copyr">Ingetec S.A. @ 2007 </td>
      </tr>
</table>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
