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

//19Mar2008
//Trae el listado de Multiplicadores disponibles
$sql3="SELECT DISTINCT f.IDfraccion, f.Porcentaje, f.Descripcion  " ;
$sql3=$sql3." FROM FraccionesV f " ;
$sql3=$sql3." Where f.IDfraccion = " . $cualMult ;
$vCursor3 = mssql_query($sql3);
if ($vReg3=mssql_fetch_array($vCursor3)) {
	$pIDfraccion = $vReg3[IDfraccion] ;
	$pPorcentaje = $vReg3[Porcentaje] ;
	$pDescripcion = $vReg3[Descripcion] ;
}

//Si se presionó el botón Grabar
if ($miMultiplicador != "") {
	//Realiza la Eliminación del multiplicador en la tabla FraccionesV
	//IDfraccion, Porcentaje, Descripcion
	$query = "DELETE FROM FraccionesV  " ;
	$query = $query . " WHERE  IDfraccion = ".$miMultiplicador ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosMV.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Multiplicadores de vi&aacute;tico</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
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
    <td class="TituloUsuario">Multiplicadores de vi&aacute;tico </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="25%" class="TituloTabla">Porcentaje</td>
            <td class="TxtTabla"><input name="lPorcentaje" type="text" class="CajaTexto" id="lPorcentaje" value="<? echo $pPorcentaje; ?>" size="10" disabled>              <span class="TituloTabla">
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
              <input name="miMultiplicador" type="hidden" id="miMultiplicador" value="<? echo $pIDfraccion; ?>">
            </span></td>
          </tr>
          <tr>
            <td width="25%" class="TituloTabla">Descripci&oacute;n</td>
            <td class="TxtTabla"><input name="lDescripcion" type="text" class="CajaTexto" id="lDescripcion" value="<? echo $pDescripcion; ?>" size="100" disabled></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar este multiplicador de vi&aacute;tico?</strong></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
    <input name="Submit" type="submit" class="Boton" value="Borrar" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
