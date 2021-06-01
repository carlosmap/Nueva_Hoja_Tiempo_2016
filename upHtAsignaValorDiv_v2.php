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
/*$sql_valor_actividades="
select SUM(valor) as valor_actividades from Actividades where dependeDe in (
select id_actividad from Actividades where id_proyecto=" . $cualProyecto." and nivel=3 and id_division=".$div."
)and  id_proyecto=" . $cualProyecto." and nivel=4 ";
*/

//CONSULTA EL VALOR TOTAL ASIGNADO A LA DIVISION EN LOS DIFERENTES LOTES DE TRABAJO
$sql_valor_actividades="select sum(valorActiv) as valor_actividades from ActividadesRecursos where id_proyecto=" . $cualProyecto."  and id_actividad in(
select id_actividad from Actividades where id_proyecto=" . $cualProyecto."   and nivel=3 and id_division=".$div." )";
$cur_valor_actividades=mssql_query($sql_valor_actividades);
if($datos_valor_actividades=mssql_fetch_array($cur_valor_actividades))
	$valor_actividades=$datos_valor_actividades[valor_actividades];


if(trim($recarga) == "2"){

	$s = 1;
	$ban_valor=0;
	//CONSULTA EL VALOR ASIGANDO AL PROYECTO
	$sql_val_div="select valorProyecto from Proyectos where id_proyecto=".$cualProyecto;
	$cur_val_div=mssql_query($sql_val_div);
	if($dato_val_div=mssql_fetch_array($cur_val_div))
		$val_asig_proyecto=$dato_val_div["valorProyecto"];
//echo $sql_val_div."<br>".mssql_get_last_message()." ***************** ".$val_asig_proyecto."<br>";

	while ($s <= $cantReg) 
	{
		$elvalReal = "valReal" . $s;
	
		//CONSULTA LA SUMATORIA DE LAS DIVISIONES, ASOCIADAS AL PROYECTO
		$sql_val_div_lt="select SUM(valorReal) as val_divs from AsignaValorDivision where id_proyecto=".$cualProyecto." and id_division<>".$div;
	/*	$sql_val_div_lt="select SUM(valor) as valor_divisiones from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_division=".$division." and id_actividad <> ".$cualDIvision;
	*/
		$cur_val_div_lt=mssql_query($sql_val_div_lt);
		if($datos_val_lt=mssql_fetch_array($cur_val_div_lt))
			$val_div=$datos_val_lt["val_divs"];
	
		if(trim($val_div)=="")
			$val_div=0;
	
	//echo $sql_val_div."<br>".mssql_get_last_message()." --- ".$val_asig_proyecto."-".$val_div."<br>";	
		//SUMA EL VALOR TOTAL DE LAS DIVISIONES, MAS EL NUEVO VALOR DE LA DIVISIÓN
		$valor_divisiones_nuevo=$val_div+trim(${$elvalReal});
	
//	echo "<br> valor div: ".$valor_divisiones_nuevo."----- $val_div+trim(${$elvalReal})<br><br>";
	
		//SI EL VALOR ASIGNADO AL PROYECTO, ES INFERIOR A EL NUEVO VALOR DE LAS DIVISIONES, MUESTRA EL MENSAJE DE ERROR
		if($val_asig_proyecto<$valor_divisiones_nuevo)
		{
	
			//co0nsultamos el nombre de la division
			$sql_nom_div="select nombre from Divisiones where  estadoDiv='A' and id_division =".$div;
			$cursor_nom_div=mssql_query($sql_nom_div);
			if($datos_nom_div=mssql_fetch_array($cursor_nom_div))
			{
				$nom_divisions=strtoupper($datos_nom_div["nombre"]);
			}
	
			//CALCULA EL VALOR QUE SE DEBE ASIGNAR, PARA LA DIVISION EN EL LOTE DE TRABAJO
			$val_asignar=$val_asig_proyecto-$val_div;
//echo "Valores: $val_asig_proyecto-$val_div <br><br>"	;
			//SI EL VALOR DISPONIBLE PARA EL PROYECTO ES 0, ES POR QUE SE HA EXCEDIDO, EL VALOR ASIGNADO
			if($val_asignar!=0)
			{
				$mensaje=$mensaje.'La sumatoria real de las divisiones, no puede superar el valor del proyecto, asigne un valor igual o inferior a $'.$val_asignar.', en la división .\n';
	
			}
			$ban_valor=1;		
		}
		$s++;

	}
	//SI ALGUNO DE LOS VALORES SUPERA EL DE LA DIVISIONES, NO SE PERMITE LA INSERCION DE LAS DEMAS DIVISIONES
	if($ban_valor==1)	
	{
		echo '<script type="text/javascript" language="JavaScript"> alert("'.$mensaje.'"); </script>';
	}
	//SI LOS VALORES DE LA DIVISION CUMPLEN LAS CONDICIONES, SE REGISTRA LA INFORMACION DE TODAS LAS DIVISION
	else
	{

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

}


?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<script language="JavaScript" type="text/JavaScript">
<!--

var nav4 = window.Event ? true : false;
function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57));
}




var nav4 = window.Event ? true : false;
function acceptNum(evt){   
var key = nav4 ? evt.which : evt.keyCode;   
return (key <= 13 || (key>= 48 && key <= 57));
}

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
	mensaje = '' ;
	var bandera=0;
	CantCampos=parseInt(document.Form1.cantReg.value);

	//VERIFICA, SI TODOS LOS CAMPOS VALOR REAL Y ASGINADO ESTAN VACIOS
	for (i=1;i<=CantCampos;i++) {
		if ( (document.getElementById("valReal"+i).value == '')&&(document.getElementById("valAsignado"+i).value == '') )
		{
			bandera++;
		}
	}
	//VALIDA SI SE INGRESO UN PORCENTAJE
	if(document.Form1.porcen.value=="")
	{
		v1='n';
		msg1 = 'Especifique el porcentaje para calcular el valor asignado. \n';
	}
	else
	{
		if(bandera==CantCampos)
		{
			v1='n';
			msg1 = 'Especifique el Valor asignado y Real en alguna de las divisiones. \n';
		}
		else
		{		
			//Valida que si hay Valor Real exista Valor asignado
			for (i=1;i<=CantCampos;i++) {
		
				if (document.getElementById("valReal"+i).value != '') {
					if (document.getElementById("valAsignado"+i).value == '') {
						v1='n';
						msg1 = 'El Valor asignado es obligatorio cuando hay Valor Real. \n';
					}
				}
		
				if (document.getElementById("valAsignado"+i).value != '') {
					if (document.getElementById("valReal"+i).value == '') {
						v2='n';
						msg2 = 'El Valor Real es obligatorio cuando hay Valor Asignado. \n';
					}
				}
		
				if (document.getElementById("valAsignado"+i).value != '') {
					if (document.getElementById("valReal"+i).value != '') {
						if (parseFloat(document.getElementById("valReal"+i).value) < parseFloat(document.getElementById("valAsignado"+i).value) ) {
							v3='n';
							msg3 = 'El Valor real debe ser mayor o igual al Valor asignado. \n';
						}
					}
				}
			}
		}
	}
	msg4=msg4+notifica();
	if(msg4!='')
		v4='n';
//alert(msg4);

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

function notifica()
{
	CantCampos=2+(3*document.Form1.cantReg.value);
	var msg4='';
	//Valida que el valor real de las divisiones No supere el valor del proyecto
	//Valida que el valor asignado de las divisiones No supere el valor del proyecto
	var sumaValorReal = 0;
	var sumaValorAsignado = 0;
	var nuevaSumaValorReal = 0;
	var nuevaSumaValorAsignado = 0;
	for (i=5;i<=CantCampos;i+=3) {
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
		msg4 = msg4+ 'El total del Valor asignado de las divisiones supera el valor total del Proyecto. Por favor verifique la información. \n';
	}
//alert(msg4);
	return msg4;
}

function inact_desact()
{

	//SI EL CAMPO DE PORCENTAJE, ESTA VACIO, SE DESABILITA LOS CAMPOS DE VALOR REAL Y ASIGNADO, Y SEL LIMPIAN LAS CELDAS
	if(document.Form1.porcen.value=="")
	{
		for(var z=1; z<=(parseInt(document.Form1.cantReg.value));z++ )
		{
			document.getElementById("valReal"+z).disabled=true;
			document.getElementById("valReal"+z).value="";

			document.getElementById("valAsignado"+z).disabled=true;
			document.getElementById("valAsignado"+z).value="";

		}

	}
	else  //SI NO SE HABILITAN
		for(var z=1; z<=(parseInt(document.Form1.cantReg.value));z++ )
		{
			document.getElementById("valReal"+z).disabled=false;
			document.getElementById("valAsignado"+z).disabled=false;

			//SI EL ALGUN MOMENTO SE CAMBIA EL VALOR DEL PORCENTAJE , SE RECALCULA EL VALOR ASIGNADO
			porcentaje(z);
		}

}

function porcentaje(campo)
{
//document.getElementById(
//alert( campo+" ---- "+parseFloat (document.getElementById("porcen").value) );
    var cantidad, porcentaje, resultado;
	if(document.getElementById("valReal"+campo).value!="")
	{
		cantidad = parseFloat (document.getElementById("valReal"+campo).value);
		porcentaje = parseFloat (document.getElementById("porcen").value);
		resultado=cantidad*porcentaje/100;
		document.getElementById("valAsignado"+campo).value= resultado;
	}
}

function total()
{
	var totalReal=0, totalAsingado=0, CantCampos;
	CantCampos=parseInt(document.Form1.cantReg.value);
	for (i=1;i<=CantCampos;i++)
	{
		if( (document.getElementById("valReal"+i).value!=''))
		{
			totalReal=parseFloat(totalReal)+(parseFloat(document.getElementById("valReal"+i).value));
		}
		if(document.getElementById("valAsignado"+i).value!="")
		{
			totalAsingado=parseFloat(totalAsingado ) +( parseFloat(document.getElementById("valAsignado"+i).value) );
		}
	}		
	document.getElementById("totalAsignado1").value	=totalAsingado;
	document.getElementById("totalReal1").value	=totalReal;
	
	document.getElementById("Realfinal").value=parseFloat(document.getElementById("Realprevio").value)+(parseFloat(totalReal));
	document.getElementById("Realasignado").value=parseFloat(document.getElementById("Asignadoprevio").value)+(parseFloat(totalAsingado));



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
          <td width="22%" class="TituloTabla">Valor del Proyecto </td>
          <td width="78%" class="TxtTabla"><? echo "$ " . number_format($miValProy, 0, ",", "."); ?>
            <input name="elValProy" type="hidden" id="elValProy" value="<? echo $miValProy; ?>"></td>
        </tr>
        <tr>
          <td width="22%" class="TituloTabla">Valor  total Divisiones </td>
          <td class="TxtTabla"><strong>Real</strong>: 	<? echo "$ " . number_format($mitotDivReal, 0, ",", "."); ?>
            <input name="elValReal" type="hidden" id="elValReal" value="<? echo $mitotDivReal; ?>">
            <br>
            <strong>Asignado</strong>: <? echo "$ " . number_format($mitotDivAsig, 0, ",", "."); ?>	<input name="elValAsig" type="hidden" id="elValAsig" value="<? echo $mitotDivAsig; ?>"></td>
        </tr>

  <tr>
          <td width="22%" class="TituloTabla">Valor  de la Divisi&oacute;n en los Lotes de Trabajo</td>
          <td class="TxtTabla"><? echo "$" . number_format($valor_actividades, 0, ",", "."); ?>
            </td>
        </tr>
        <tr>
			<td class="TituloTabla">% para calcular el valor asignado</td>
	          <td class="TxtTabla">
            <input name="porcen" type="text" class="CajaTexto" id="porcen" onBlur="inact_desact();  total();" onChange="inact_desact(); total();"   onKeyPress="return acceptNum(event); inact_desact(); " size="3" maxlength="2" >
            %</td>
        </tr>

      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="33%">Divisi&oacute;n</td>
          <td width="33%">Valor Real </td>
          <td width="33%">Valor Asignado </td>
        </tr>
		<? 
		$val_real=0; $val_asignado=0;
		$r = 1;
		while ($reg01=mssql_fetch_array($cursor06)) { ?>
        <tr class="TxtTabla">
          <td><? echo strtoupper($reg01[nombre]); ?>
            <input name="laDivision<? echo $r; ?>" type="hidden" id="laDivision<? echo $r; ?>" value="<? echo $reg01[id_division]; ?>"></td>
          <td align="center">

<input name="valReal<? echo $r; ?>" type="text" onBlur="porcentaje('<? echo $r; ?>'); total();  if((notifica())!='') { alert(notifica()); } "  disabled class="CajaTexto" id="valReal<? echo $r; ?>" value="<? echo $reg01[valorReal];  $val_real=$reg01[valorReal];  ?>" onKeyPress="return acceptNum(event)"  ></td>
          <td align="center">

 <input name="valAsignado<? echo $r; ?>" type="text" disabled class="CajaTexto" id="valAsignado<? echo $r; ?>" onKeyPress="return acceptNum(event)" onBlur=" total(); if((notifica())!='') { alert(notifica()); }"  value="<? echo $reg01[valorAsignado]; $val_asignado=$reg01[valorAsignado]; ?>" ></td>

        </tr>
		<? 
		$r = $r+1;
		} ?>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="3" class="TxtTabla">&nbsp;</td>
          </tr>
        <tr>
          <td width="33%" class="TituloTabla">Total</td>
          <td width="33%" align="center" class="TxtTabla"><label for="textfield2"></label>
            <input name="totalReal1" type="text" disabled class="CajaTexto" id="totalReal1" readonly></td>
          <td width="33%" align="center" class="TxtTabla"><input name="totalAsignado1" type="text" disabled class="CajaTexto" id="totalAsignado1" readonly></td>
        </tr>
        <tr>
          <td class="TituloTabla">Total previamente grabado</td>


          <td align="center" class="TxtTabla"><input name="Realprevio" type="text" value="<? echo  $mitotDivReal-$val_real; ?>" disabled class="CajaTexto" id="Realprevio" readonly></td>
          <td align="center" class="TxtTabla"><input name="Asignadoprevio" type="text" disabled class="CajaTexto" value="<? echo $mitotDivAsig-$val_asignado; ?>" id="Asignadoprevio" readonly></td>
        </tr>
        <tr>
          <td class="TituloTabla">Total final</td>
          <td align="center" class="TxtTabla"><input name="Realfinal" type="text" disabled class="CajaTexto" id="Realfinal" readonly></td>
          <td align="center" class="TxtTabla"><input name="Realasignado" type="text" disabled class="CajaTexto" id="Realasignado" readonly></td>
        </tr>
        <tr>
          <td align="right"   colspan="3"class="TxtTabla">
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
