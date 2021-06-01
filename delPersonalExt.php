<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Encontrar el nombre del proyecto seleccionado
$sql="Select * from proyectos where id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elProyecto = $reg[nombre];
	}
else {
	$elProyecto= "";
}

//Encontrar el nombre de la actividad seleccionada
$sql="Select * from Actividades where id_proyecto = " . $cualProyecto ;
$sql=$sql." and id_actividad=" . $cualActividad;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laActividad = $reg[nombre];
}
else {
	$laActividad= "";
}

//Encuentra la información de la persona externa seleccionada
//identificacion, nombre, apellidos, estado
//id_proyecto, id_actividad, identificacion, servicio, valor, factor, unidad, fechaCrea
$sql3="SELECT A.*, P.nombre, P.apellidos ";
$sql3=$sql3." FROM ActividadesPersonalExt A, PersonalExterno P ";
$sql3=$sql3." WHERE A.identificacion = P.identificacion ";
$sql3=$sql3." AND A.id_proyecto =" . $cualProyecto ;
$sql3=$sql3." AND A.id_actividad =" . $cualActividad;
$sql3=$sql3." AND A.identificacion =" . $cualID ;
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) {
	$pidentificacion= $reg3[identificacion];
	$pnombre=$reg3[nombre];
	$papellidos=$reg3[apellidos];
	$pservicio = $reg3[servicio];
	$pvalor = $reg3[valor];
	$pfactor = $reg3[factor];
}




//$recarga = 2 si se presionó el botón Grabar
if ($recarga == "2") {
	//Realiza la actualización en dbo.ActividadesPersonalExt
	//id_proyecto, id_actividad, identificacion, servicio, valor, factor, unidad, fechaCrea
	$query = "DELETE FROM ActividadesPersonalExt  " ;
	$query = $query . " WHERE id_proyecto = " . $cualProyecto ;
	$query = $query . " AND id_actividad = " . $cualActividad ;
	$query = $query . " AND identificacion = " . $lIden ;

	$cursor = mssql_query($query) ;
	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Eliminación se realizó con éxito.');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$cualProyecto&cualActividad=$cualActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--
function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}

function envia2(){ 
var v1,v2,v3,v4,v5, v6, i, CantCampos, msg1, msg2, msg3, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
mensaje = '';

//alert (document.Form1.pAcargoDe[0].checked);
//alert (document.Form1.pAcargoDe[1].checked);
//alert (document.Form1.CantidadItem.value);

//validar que el nombre no se encuentre vacio
if (document.Form1.lNombre.value == '') {
	v1='n';
	msg1 = 'El nombre es obligatorio. \n'
}

//validar que el apellido no se encuentre vacio
if (document.Form1.lApellido.value == '') {
	v2='n';
	msg2 = 'El apellido es obligatorio. \n'
}


//validar que el servicio no se encuentre vacio
if (document.Form1.servicio.value == '') {
	v3='n';
	msg3 = 'El servicio es obligatorio. \n'
}

//validar que el valor no se encuentre vacio y sea numérico
if (document.Form1.valor.value == '') {
	v4='n';
	msg4 = 'El valor es obligatorio y numérico. \n'
}

if (isNaN(document.Form1.valor.value)) {
	v4='n';
	msg4 = 'El valor es obligatorio y numérico. \n'
}

//validar que el factor no se encuentre vacio y sea numérico
if (document.Form1.factor.value == '') {
	v5='n';
	msg5 = 'El factor es obligatorio y numérico. \n'
}

if (isNaN(document.Form1.factor.value)) {
	v5='n';
	msg5 = 'El factor es obligatorio y numérico. \n'
}

//validar que la identificación no se encuentre vacio y sea numérico
if (document.Form1.lIden.value == '') {
	v6='n';
	msg6 = 'La identificacion es obligatoria y numérica. \n'
}

if (isNaN(document.Form1.lIden.value)) {
	v6='n';
	msg6 = 'La identificacion es obligatoria y numérica. \n'
}


//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg6 + msg1 + msg2 + msg3 + msg4 + msg5 ;
		alert (mensaje);
	}

}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
<title>Gesti&oacute;n de Archivos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post" name="Form1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Personal externo  que paricipa en el proyecto </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td>    
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="39%" class="TituloTabla">Proyecto</td>
          <td class="TxtTabla"><? echo ucwords(strtolower($elProyecto)); ?>
            <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">            </td>
        </tr>
        <tr>
          <td class="TituloTabla">Actividad</td>
          <td class="TxtTabla"><? echo ucwords(strtolower($laActividad)); ?> <input name="cualActividad" type="hidden" id="cualActividad" value="<? echo $cualActividad; ?>">
            <input name="recarga" type="hidden"  value="2"></td>
        </tr>
        <tr>
          <td class="TituloTabla">Identificacion</td>
          <td class="TxtTabla"><input name="lIden" type="text" class="CajaTexto" id="lIden" value="<? echo $pidentificacion; ?>" size="50" readonly ></td>
        </tr>
        <tr>
          <td class="TituloTabla">Nombre</td>
          <td class="TxtTabla"><input name="lNombre" type="text" class="CajaTexto" id="lNombre" value="<? echo $pnombre; ?>" size="50" disabled></td>
        </tr>
        <tr>
          <td class="TituloTabla">Apellidos</td>
          <td class="TxtTabla"><input name="lApellido" type="text" class="CajaTexto" id="lApellido" value="<? echo $papellidos; ?>" size="50" disabled></td>
        </tr>
        <tr>
          <td class="TituloTabla">Servicio</td>
          <td class="TxtTabla"><textarea name="servicio" cols="50" class="CajaTexto" id="servicio" disabled><? echo $pservicio; ?></textarea></td>
        </tr>
        <tr>
          <td class="TituloTabla">Valor</td>
          <td class="TxtTabla"><input name="valor" type="text" class="CajaTexto" id="valor" value="<? echo $pvalor; ?>" disabled></td>
        </tr>
        <tr>
          <td class="TituloTabla">Factor</td>
          <td class="TxtTabla"><input name="factor" type="text" class="CajaTexto" id="factor" value="<? echo $pfactor; ?>" disabled></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar este registro?</strong></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cancelar">          <input name="Submit" type="submit" class="Boton" value="Borrar" ></td>
        </tr>
      </table></td>
  </tr>
</table>
	     </td>
  </tr>
</table>
</form> 

</body>
</html>

<? mssql_close ($conexion); ?>	
