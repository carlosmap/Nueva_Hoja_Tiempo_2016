<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Trae las diviviones activas
$sql01="SELECT *  ";
$sql01=$sql01." FROM Divisiones ";
$sql01=$sql01." WHERE estadoDiv = 'A' ";
$sql01=$sql01." AND id_division NOT IN  ";
$sql01=$sql01." 	( ";
$sql01=$sql01." 	SELECT id_division FROM AsignaValorDivision ";
$sql01=$sql01." 	WHERE id_proyecto = " . $cualProyecto ;
$sql01=$sql01." 	) ";
$sql01=$sql01." 	ORDER BY nombre ";
$cursor01 = mssql_query($sql01);

//Traer el valor del proyecto
$miValProy=0;
$sql02="SELECT *  ";
$sql02=$sql02." FROM Proyectos ";
$sql02=$sql02." WHERE id_proyecto = " . $cualProyecto ;
$cursor02 = mssql_query($sql02);
if ($reg02=mssql_fetch_array($cursor02)) {
	$miValProy=$reg02["valorProyecto"];
}

//Trae el valor previamente asignado a las divisiones
$mitotDivReal=0;
$mitotDivAsig=0;
$sql03="Select SUM(valorReal) valorReal, SUM(valorAsignado) valorAsignado  ";
$sql03=$sql03." from AsignaValorDivision  ";
$sql03=$sql03." WHERE id_proyecto = " . $cualProyecto ;
$cursor03 = mssql_query($sql03);
if ($reg03=mssql_fetch_array($cursor03)) {
	$mitotDivReal=$reg03["valorReal"];
	$mitotDivAsig=$reg03["valorAsignado"];
}



if(trim($recarga) == "2"){

	$s = 1;
	while ($s <= $cantReg) {
		//Recoger las variables
		$ellaDivision = "laDivision" . $s;
		$elvalReal = "valReal" . $s;
		$elvalAsignado = "valAsignado" . $s;
		
		//id_proyecto, id_division, valorReal, valorAsignado, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//AsignaValorDivision		

		if (trim(${$elvalReal}) != "") {
			$sqlIn1 = " INSERT INTO AsignaValorDivision";
			$sqlIn1 = $sqlIn1 . "( id_proyecto, id_division, valorReal, valorAsignado, usuarioCrea, fechaCrea ) ";
			$sqlIn1 = $sqlIn1 . " VALUES ( ";
			$sqlIn1 = $sqlIn1 . " ". $cualProyecto .", ";
			$sqlIn1 = $sqlIn1 . " " . ${$ellaDivision} . ", ";
			$sqlIn1 = $sqlIn1 . " " . ${$elvalReal}  . ", ";
			$sqlIn1 = $sqlIn1 . " " . ${$elvalAsignado} . ", ";
			$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "', ";
			$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "' ";
			$sqlIn1 = $sqlIn1 . " ) ";
			$cursorIn1 = mssql_query($sqlIn1);
		}
//		echo $sqlIn1 . "<br>";
		
		$s = $s + 1;
	}
//exit;

	if  (trim($cursorIn1) != "")  {
		echo ("<script>alert('La grabación se realizó con éxito.');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}


?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<script language="JavaScript" type="text/JavaScript">
<!--

function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}


function envia2(){ 
var v1,v2,v3, v4,v5,v6, v7,v8,v9, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, msg7, msg8, msg9, msg10, msg11, msg12, msg13, msg14, msg15, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
v7='s';
v8='s';
v9='s';
v10='s';
v11='s';
v12='s';
v13='s';
v14='s';
v15='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
msg7 = '';
msg8 = '';
msg9 = '';
msg10 = '';
msg11 = '';
msg12 = '';
msg13 = '';
msg14 = '';
msg15 = '';
mensaje = '';

CantCampos=2+(3*document.Form1.cantReg.value);

//Valida que si hay Valor Real exista Valor asignado
for (i=4;i<=CantCampos;i+=3) {
//	alert(document.Form1.elements[i].value);
//	alert(document.Form1.elements[i+1].value);
	if (document.Form1.elements[i].value != '') {
		if (document.Form1.elements[i+1].value == '') {
			v1='n';
			msg1 = 'El Valor asignado es obligatorio cuando hay Valor Real. \n';
		}
	}
}


//Valida que si hay Valor Asignado exista Valor Real
for (i=5;i<=CantCampos;i+=3) {
//	alert(document.Form1.elements[i].value);
//	alert(document.Form1.elements[i-1].value);
	if (document.Form1.elements[i].value != '') {
		if (document.Form1.elements[i-1].value == '') {
			v2='n';
			msg2 = 'El Valor Real es obligatorio cuando hay Valor Asignado. \n';
		}
	}
}

//Valida que Valor Real >= Valor Asignado
for (i=4;i<=CantCampos;i+=3) {
//	alert(document.Form1.elements[i].value);
//	alert(document.Form1.elements[i+1].value);
	if (document.Form1.elements[i].value != '') {
		if (document.Form1.elements[i+1].value != '') {
			if (parseFloat(document.Form1.elements[i].value) < parseFloat(document.Form1.elements[i+1].value) ) {
				v3='n';
				msg3 = 'El Valor real debe ser mayor o igual al Valor asignado. \n';
			}
		}
	}
}

//Valida que el valor real de las divisiones No supere el valor del proyecto
//Valida que el valor asignado de las divisiones No supere el valor del proyecto
sumaValorReal = 0;
sumaValorAsignado = 0;
nuevaSumaValorReal = 0;
nuevaSumaValorAsignado = 0;
for (i=4;i<=CantCampos;i+=3) {
//	alert(document.Form1.elements[i].value);
//	alert(document.Form1.elements[i+1].value);
	if (document.Form1.elements[i].value != '') {
		if (document.Form1.elements[i+1].value != '') {
			sumaValorReal = parseFloat(sumaValorReal) + parseFloat(document.Form1.elements[i].value);
			sumaValorAsignado = parseFloat(sumaValorAsignado) + parseFloat(document.Form1.elements[i+1].value);
		}
	}
}
//Aumenta el valor contra el valor de la división
nuevaSumaValorReal = parseFloat(sumaValorReal) + parseFloat(document.Form1.elValReal.value) ;
nuevaSumaValorAsignado = parseFloat(sumaValorAsignado) + parseFloat(document.Form1.elValAsig.value) ;
/*
alert(nuevaSumaValorReal);
alert(nuevaSumaValorAsignado);
alert(parseFloat(document.Form1.elValProy.value));
*/

//Valida que el valor real no supuere el valor del proyecto
if (parseFloat(nuevaSumaValorReal) > parseFloat(document.Form1.elValProy.value) ) {
//	alert("entró al if");
	v4='n';
	msg4 = 'El total del Valor real de las divisiones supera el valor total del Proyecto. Por favor verifique la información. \n';
}

//Valida que el valor real no supuere el valor del proyecto
if (parseFloat(nuevaSumaValorAsignado) > parseFloat(document.Form1.elValProy.value) ) {
//	alert("entró al if");
	v5='n';
	msg5 = 'El total del Valor asignado de las divisiones supera el valor total del Proyecto. Por favor verifique la información. \n';
}


//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s') && (v10=='s') && (v11=='s') && (v12=='s') && (v13=='s') && (v14=='s') && (v15=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6 + msg7 + msg8 + msg9 + msg10 + msg11 + msg12 + msg13 + msg14 + msg15;
		alert (mensaje);
	}
}
//-->
</script>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">.: ASIGNACI&Oacute;N DE RECURSOS POR DIVISI&Oacute;N </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="20%" class="TituloTabla">Valor del Proyecto </td>
          <td class="TxtTabla"><? echo "$ " . number_format($miValProy, 0, ",", "."); ?>
            <input name="elValProy" type="hidden" id="elValProy" value="<? echo $miValProy; ?>"></td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Valor  total Divisiones </td>
          <td class="TxtTabla"><strong>Real</strong>: 	<? echo "$ " . number_format($mitotDivReal, 0, ",", "."); ?>
            <input name="elValReal" type="hidden" id="elValReal" value="<? echo $mitotDivReal; ?>">
            <br>
            <strong>Asignado</strong>: <? echo "$ " . number_format($mitotDivAsig, 0, ",", "."); ?>	<input name="elValAsig" type="hidden" id="elValAsig" value="<? echo $mitotDivAsig; ?>"></td>
        </tr>
      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="33%">Divisi&oacute;n</td>
          <td width="33%">Valor Real </td>
          <td width="33%">Valor Asignado </td>
        </tr>
		<? 
		$r = 1;
		while ($reg01=mssql_fetch_array($cursor01)) { ?>
        <tr class="TxtTabla">
          <td><? echo strtoupper($reg01[nombre]); ?>
            <input name="laDivision<? echo $r; ?>" type="hidden" id="laDivision<? echo $r; ?>" value="<? echo $reg01[id_division]; ?>"></td>
          <td align="center"><input name="valReal<? echo $r; ?>" type="text" class="CajaTexto" id="valReal<? echo $r; ?>" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
          <td align="center"><input name="valAsignado<? echo $r; ?>" type="text" class="CajaTexto" id="valAsignado<? echo $r; ?>" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
        </tr>
		<? 
		$r = $r+1;
		} ?>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
			<input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">
			<input name="cantReg" type="hidden" id="cantReg" value="<? echo mssql_num_rows($cursor01); ?>">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
        </tr>
      </table>
      </td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
</body>
</html>

<? mssql_close ($conexion); ?>	
