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

/*
Consulta usuarios de la Hoja de Tiempo
*/
$sql1 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios
WHERE retirado IS NULL
AND unidad = $kUnidad
ORDER BY apellidos ";
$cursor1 = mssql_query($sql1);

//Si se presionó el botón Grabar
if ($elProyecto != "") {
		
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Realiza la inserción de los programadores del proyecto
	$query = "DELETE FROM AutorizadosImpresion " ;
	$query = $query . " WHERE id_proyecto = " . $elProyecto . " " ;
	$query = $query . " AND unidad = " . $pJefe. " ";
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la Operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$elProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>Autorizaci&oacute;n de Impresiones</title>
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
    <td class="TituloUsuario">Programación de Proyectos - Autorizados para Impresi&oacute;n</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Usuario Autorizado </td>
    <td class="TxtTabla"><select name="pJefe" class="CajaTexto" id="pJefe" >
      <?
		while ($reg1 = mssql_fetch_array($cursor1)) {
		?>
      <option value="<? echo $reg1['unidad']; ?>" ><? echo ucwords(strtolower($reg1['apellidos'])) . ", " . ucwords(strtolower($reg1['nombre'])) . " (".$reg1['unidad'].") - ". $reg1['TipoContrato'] ;  ?></option>
      <? } ?>
    </select>
      <input name="elProyecto" type="hidden" id="elProyecto" value="<? echo $kProyecto; ?>">
      </td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Eliminar">
      <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cancelar"></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
