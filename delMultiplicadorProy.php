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
//Trae el listado de Multiplicadores disponibles que aun no se han agregado al proyecto
$sql3="SELECT DISTINCT f.IDfraccion, f.Porcentaje, f.Descripcion  " ;
$sql3=$sql3." FROM FraccionesV f " ;
$sql3=$sql3." Where f.IDfraccion = " . $cualMult ;
$vCursor3 = mssql_query($sql3);

//Si se presionó el botón Grabar
if ($miMultiplicador != "") {
	//Realiza la eliminación del multiplicador en la tabla dbo.FraccionesVProy
	//id_proyecto, IDfraccion
	$query = "DELETE FROM HojaDeTiempo.dbo.FraccionesVProy  " ;
	$query = $query." WHERE id_proyecto =" . $miProyecto  ;
	$query = $query . " AND IDfraccion =" . $miMultiplicador ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Eliminación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosMV.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Multiplicadores de vi&aacute;tico del proyecto</title>
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
            <td width="25%" class="TituloTabla">Multiplicador</td>
            <td class="TxtTabla">
			<select name="miMultiplicador" class="CajaTexto" id="miMultiplicador">
			<? while ($reg3=mssql_fetch_array($vCursor3)) {  ?>
              <option value="<? echo  $reg3[IDfraccion] ; ?>"><? echo  ucfirst(strtolower($reg3[Descripcion])) . " - " . $reg3[Porcentaje]  ; ?></option>
			<? } ?>
            </select>			
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
            </td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de desasociar este multiplicador de vi&aacute;tico del proyecto?</strong></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">    <input name="Submit" type="submit" class="Boton" value="Borrar" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
