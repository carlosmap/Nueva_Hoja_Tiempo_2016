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

//Trae la información de la programación de ls auma global para al proyecto seleccionado y el usuario activo
//id_proyecto, unidadProgramador, fechaInicio, plazo, valorSumaGlobal
$sql2="SELECT * FROM ProgSumaGlobal ";
$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
$sql2=$sql2." and unidadProgramador =" . $laUnidad ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {	 
	$pfechaInicio = date("M d Y ", strtotime($reg2[fechaInicio])) ;
	$pplazo = $reg2[plazo];
	$pvalorSumaGlobal = $reg2[valorSumaGlobal];
}

//Si se presionó el botón Grabar
if ($recarga != "") {
	//Realiza la inserción de la información de la programación en ProgSumaGlobal
	$query = "DELETE FROM ProgSumaGlobal ";	
	$query = $query . " where id_proyecto =" . $cualProyecto ;
	$query = $query . " and unidadProgramador =" . $laUnidad ;
	$cursor = mssql_query($query);
	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La operacion se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgDivisionDet.php?cualProyecto=$cualProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos por Divisi&oacute;n</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="ts_picker.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos -Informaci&oacute;n de la programaci&oacute;n </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Fecha Inicio </td>
    <td class="TxtTabla"><? echo $pfechaInicio ;	?>      </td>
  </tr>
  <tr>
    <td class="TituloTabla">Plazo </td>
    <td class="TxtTabla"><input name="lPlazo" type="text" class="CajaTexto" id="lPlazo" value="<? echo $pplazo; ?>" size="10" disabled > 
      meses </td>
  </tr>
  <tr>
    <td class="TituloTabla">Valor</td>
    <td class="TxtTabla"><input name="lvalor" type="text" class="CajaTexto" id="lvalor" value="<? echo $pvalorSumaGlobal; ?>" disabled>
      <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">
      <input name="recarga" type="hidden" id="recarga" value="2"></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar esta informaci&oacute;n de la programaci&oacute;n? </strong></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cancelar">          <input name="Submit" type="submit" class="Boton" value="Borrar"></td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
