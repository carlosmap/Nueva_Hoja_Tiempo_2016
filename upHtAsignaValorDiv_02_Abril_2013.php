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
/*$sql01="SELECT *  ";
$sql01=$sql01." FROM Divisiones ";
$sql01=$sql01." WHERE estadoDiv = 'A' ";
$sql01=$sql01." AND id_division  IN  ";
$sql01=$sql01." 	( ";
$sql01=$sql01." 	SELECT distinct(id_division) FROM Actividades ";
$sql01=$sql01." 	WHERE id_proyecto = " . $cualProyecto ;
$sql01=$sql01." 	) ";
$sql01=$sql01." 	ORDER BY nombre ";
*/

/*
//Trae las diviviones, que estan asociadas a la edt del proyecto, y los valores asignados
$sql01="
SELECT * FROM Divisiones 
left join AsignaValorDivision on AsignaValorDivision.id_division = Divisiones.id_division and AsignaValorDivision.id_proyecto=" . $cualProyecto ." 
where  Divisiones.id_division  IN  	(SELECT distinct(id_division) FROM Actividades 	WHERE id_proyecto = " . $cualProyecto .") ORDER BY Divisiones.nombre";
$cursor01 = mssql_query($sql01);
*/
//Traer el valor del proyecto
$miValProy=0;
$sql02="SELECT *  ";
$sql02=$sql02." FROM Proyectos ";
$sql02=$sql02." WHERE id_proyecto = " . $cualProyecto ;
$cursor02 = mssql_query($sql02);
if ($reg02=mssql_fetch_array($cursor02)) {
	$miValProy=$reg02["valorProyecto"];
}

if(trim($miValProy)=='')
{
	$miValProy=0;
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


//Trae la información del valor Asignado a  previamente a la division
$sql06="Select A.* , D.nombre ";
$sql06=$sql06." from AsignaValorDivision A, Divisiones D ";
$sql06=$sql06." where id_proyecto = " . $cualProyecto;
$sql06=$sql06." and A.id_division = D.id_division ";
$sql06=$sql06." and A.id_division = ".$div;
$cursor06 = mssql_query($sql06);

$valor_actividades=0;
//CONSULTA EL VALOR TOTAL ASIGNADO A LAS ACTIVIDADES DE LA DIVISION
$sql_valor_actividades="
select SUM(valor) as valor_actividades from Actividades where dependeDe in (
select id_actividad from Actividades where id_proyecto=" . $cualProyecto." and nivel=3 and id_division=".$div."
)and  id_proyecto=" . $cualProyecto." and nivel=4 ";
$cur_valor_actividades=mssql_query($sql_valor_actividades);
if($datos_valor_actividades=mssql_fetch_array($cur_valor_actividades))
	$valor_actividades=$datos_valor_actividades[valor_actividades];


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
			$sqlIn1 = " update AsignaValorDivision";
			$sqlIn1 = $sqlIn1 . " set valorReal=" . ${$elvalReal} . ", valorAsignado= ".${$elvalAsignado}.", usuarioMod= '" . $_SESSION["sesUnidadUsuario"] . "',fechaMod='". gmdate ("n/d/Y") . "'  ";
			$sqlIn1 = $sqlIn1 . "  where id_proyecto=".$cualProyecto." and id_division=".$div;
			$cursorIn1 = mssql_query($sqlIn1);
		}
//		echo $sqlIn1 . "<br>".mssql_get_last_message();
		
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

var nav4 = window.Event ? true : false;

function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57) );
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

//VALIDA QUE NO SE INGRESE UN VALOR EN EL CAMPO DE VALOR REAL Y VALOR ASIGNADO
for (i=4;i<=CantCampos;i+=3) {

	if ( (document.Form1.elements[i].value=='') && (document.Form1.elements[i+1].value=='' ) ) 
	{
		v3='n';
		msg3 = 'El Valor real y el valor asignado es obligatorio. \n';
		break;
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
				msg6 = 'El Valor real debe ser mayor o igual al Valor asignado. \n';
			}
		}
	}
}

//VALIDA QUE EL VALOR INGRESADO PARA LA DIVISION NO SE INFERIOR AL DE LOS REGISTRADOS EN LAS ACTIVIDADES
for (i=4;i<=CantCampos;i+=3) {

	if (document.Form1.elements[i].value != '') {
		if (document.Form1.elements[i+1].value != '') {
			if ( (parseFloat(document.Form1.elements[i].value) < parseFloat(document.Form1.valor_actividades.value) ) || (parseFloat(document.Form1.elements[i+1].value) < parseFloat(document.Form1.valor_actividades.value) ) ) 
			{
				v3='n';
				msg7 = 'El Valor real y/o el valor asignado a la división, no puede ser inferior al valor total de las actividades. \n';
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
//alert(document.Form1.elements[i].value+" *** "+document.Form1.elements[i+1].value);
			sumaValorReal = parseFloat(sumaValorReal) + parseFloat(document.Form1.elements[i].value);
			sumaValorAsignado = parseFloat(sumaValorAsignado) + parseFloat(document.Form1.elements[i+1].value);
		}
	}
}
//Aumenta el valor contra el valor de la división
nuevaSumaValorReal = parseFloat(sumaValorReal); // + parseFloat(document.Form1.elValReal.value) ;
nuevaSumaValorAsignado = parseFloat(sumaValorAsignado); // + parseFloat(document.Form1.elValAsig.value) ;
/*
alert(nuevaSumaValorReal);
alert(nuevaSumaValorAsignado);
alert(parseFloat(document.Form1.elValProy.value));
*/
//alert(nuevaSumaValorReal+" ****/ "+document.Form1.elValProy.value);

//Valida que el valor real no supuere el valor del proyecto
if (parseFloat(nuevaSumaValorReal) > parseFloat(document.Form1.elValProy.value) ) {
//	alert("entró al if");
	v4='n';
	msg4 = 'El total del Valor real de las división supera el valor total del Proyecto. Por favor verifique la información. \n';
}

//Valida que el valor real no supuere el valor del proyecto
if (parseFloat(nuevaSumaValorAsignado) > parseFloat(document.Form1.elValProy.value) ) {
//	alert("entró al if");
	v5='n';
	msg5 = 'El total del Valor asignado de las división supera el valor total del Proyecto. Por favor verifique la información. \n';
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

  <tr>
          <td width="20%" class="TituloTabla">Valor  total Actividades </td>
          <td class="TxtTabla"><? echo "$ " . number_format($valor_actividades, 0, ",", "."); ?>
            </td>
        </tr>

      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="33%">Divisi&oacute;n</td>
          <td width="33%">Valor Real </td>
          <td width="33%">Valor Asignado </td>
        </tr>
		<? 
		$r = 1;
		while ($reg01=mssql_fetch_array($cursor06)) { ?>
        <tr class="TxtTabla">
          <td><? echo strtoupper($reg01[nombre]); ?>
            <input name="laDivision<? echo $r; ?>" type="hidden" id="laDivision<? echo $r; ?>" value="<? echo $reg01[id_division]; ?>"></td>
          <td align="center"><input name="valReal<? echo $r; ?>" type="text" class="CajaTexto" id="valReal<? echo $r; ?>" onKeyPress="return acceptNum(event)" value="<? echo $reg01[valorReal];  ?>"></td>
          <td align="center"><input name="valAsignado<? echo $r; ?>" value="<? echo $reg01[valorAsignado]; ?>" type="text" class="CajaTexto" id="valAsignado<? echo $r; ?>" onKeyPress="return acceptNum(event)"> </td>

        </tr>
		<? 
		$r = $r+1;
		} ?>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
			<input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">
			<input name="cantReg" type="hidden" id="cantReg" value="<? echo mssql_num_rows($cursor06); ?>">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
			<input name="valor_actividades" type="hidden" id="valor_actividades" value="<? echo $valor_actividades; ?>">


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
