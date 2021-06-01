<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Búsqueda de la información
$sqlHyD = "SELECT vigencia, mes, hOficina, hCampo, diasLaborales
		   FROM HojaDeTiempo.dbo.horasydiasLaboralesProy
		   WHERE id_proyecto=".$cualProyecto."
		   AND vigencia=".$v."
		   AND mes=".$m;
$queryHyD = mssql_query($sqlHyD);
$horyDias = mssql_fetch_array($queryHyD);

if ($recarga == 2)
{
	$id_proyecto = $_REQUEST['miProyecto'];
	$vigencia = $_REQUEST['vigencia'];
	$mes = $_REQUEST['mes'];
	$hOficina = $_REQUEST['oficina'];
	$hCampo = $_REQUEST['campo'];
	$diasLaborales = $_REQUEST['dias'];
	
	$sqlIns = "UPDATE HojaDeTiempo.dbo.horasydiasLaboralesProy
			   SET vigencia=$vigencia,
			   mes=$mes,
			   hOficina='$hOficina',
			   hCampo='$hCampo',
			   diasLaborales=$diasLaborales
			   WHERE id_proyecto=$id_proyecto
			   AND vigencia=".$horyDias[vigencia]."
			   AND mes=".$horyDias[mes];
	//echo $sqlIns;
	//exit;
	$queryIns = mssql_query($sqlIns);
	
	if ($queryIns==FALSE)
	{
		echo ("<script>alert('Error durante la grabación');</script>");
	}
	else{
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosHorariosLGM.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Horarios</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
function enviar(){ 
var v1, v2, v3, msg1, msg2, msg3, mensaje;
v1='s';
v2='s';
v3='s';
msg1='';
msg2='';
msg3='';

	if ((document.Form1.vigencia.value == '') || (isNaN(document.Form1.vigencia.value))) {
		document.Form1.recarga.value="1";
		v1='n';
		msg1='El campo Vigencia es obligatorio y debe ser numérico. \n'
	}
	if ((document.Form1.mes.value == '') || (isNaN(document.Form1.mes.value))) {
		document.Form1.recarga.value="1";
		v2='n';
		msg2='El campo Mes es obligatorio y debe ser numérico. \n'
	}
	if ((document.Form1.dias.value == '') || (isNaN(document.Form1.dias.value))) {
		document.Form1.recarga.value="1";
		v3='n';
		msg3='El campo Días es obligatorio y debe ser numérico. \n'
	}
	
	if ((v1=='s') && (v2=='s') && (v3=='s'))
	{
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3;
		alert (mensaje);
	}
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Horas y d&iacute;as laborales </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">          
          <tr class="TituloTabla2">
		  	<td width="14%">Vigencia</td>
            <td width="14%">Mes</td>
            <td width="14%">Oficina</td>
            <td width="14%">Campo</td>
            <td width="14%">D&iacute;as</td>
          </tr>
          <tr align="center" class="TxtTabla">
            <td width="14%"><input name="vigencia" type="text" class="CajaTexto" id="vigencia" value="<? echo $horyDias[vigencia]; ?>" size="10" onBlur="totalizar('lLunes')" ></td>
			<td width="14%"><input name="mes" type="text" class="CajaTexto" id="mes" value="<? echo $horyDias[mes]; ?>" size="10" onBlur="totalizar('lLunes')" ></td>
            <td width="14%"><input name="oficina" type="text" class="CajaTexto" id="oficina" value="<? echo  $horyDias[hOficina]; ?>" size="10" onBlur="totalizar('lMartes')" ></td>
            <td width="14%"><input name="campo" type="text" class="CajaTexto" id="campo" value="<? echo $horyDias[hCampo]; ?>" size="10" onBlur="totalizar('lMiercoles')"></td>
            <td width="14%"><input name="dias" type="text" class="CajaTexto" id="dias" value="<? echo $horyDias[diasLaborales]; ?>" size="10" onBlur="totalizar('lJueves')" ></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="miProyecto" type="hidden" id="miProyecto" value="<? echo $cualProyecto; ?>">
      <input name="recarga" type="hidden" id="recarga" value="1">
    <input name="Submit" type="button" class="Boton" value="Grabar" onClick="enviar()"></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
