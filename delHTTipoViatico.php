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

//26Mar2008
//Trae el tipo de viático seleccionado
$sql3="SELECT  t.IDTipoViatico, t.NomTipoViatico  " ;
$sql3=$sql3." FROM TiposViatico t " ;
$sql3=$sql3." Where t.IDTipoViatico = " . $cualTipoV ;
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) {  
	$pIDTipoViatico = $reg3[IDTipoViatico] ;
	$pNomTipoViatico = $reg3[NomTipoViatico] ;
}

//Si se presionó el botón Grabar
if ($miTipoV != "") {
	//Realiza la inserción del tipo de viático en dbo.TiposViatico
	//IDTipoViatico, NomTipoViatico
	$query = "DELETE FROM TiposViatico " ;
	$query = $query." WHERE IDTipoViatico = " . $miTipoV ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Eliminación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('admTiposViatico.php?cualProyecto=$miProyecto','winAdminHTs','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Tipos de vi&aacute;tico</title>
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
    <td class="TituloUsuario">Tipos de vi&aacute;tico </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="25%" class="TituloTabla">Tipo de vi&aacute;tico </td>
            <td class="TxtTabla"><input name="lTipo" type="text" disabled class="CajaTexto" id="lTipo" value="<? echo $pNomTipoViatico; ?>" size="100">
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
            <input name="miTipoV" type="hidden" id="miTipoV" value="<? echo $pIDTipoViatico ; ?>"> 			</td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar este tipo de vi&aacute;tico?</strong></td>
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
