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

//Si se presionó el botón Grabar
if ($elProyecto != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	//Realiza la eliminación de los responsable del proyecto
	$query = "DELETE FROM ResponsablesActividad  " ;
	$query = $query . " WHERE id_proyecto = " . $elProyecto ;
	$query = $query . " AND id_actividad = " .  $laActividad ;
	$query = $query . " AND unidad = " . $estaUnidad ;
	$cursor = mssql_query($query);
	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$elProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
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
    <td class="TituloUsuario">Programación de Proyectos - Programadores del proyecto </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Programador del proyecto </td>
    <td class="TxtTabla"><select name="pJefe" class="CajaTexto" id="pJefe" disabled >
	<option value="" selected >Escoja el programador</option>
      <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra todos los usuarios. 
//		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
//		$sql2="Select * from Usuarios where id_categoria <= 40 "  ;
		$sql2="Select * from Usuarios  "  ;
		$sql2=$sql2." where retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
			if ($kUnidad == $reg2[unidad]) {
				$selOpc="selected";
			}
			else {
				$selOpc="";
			}
		?>
      <option value="<? echo $reg2[unidad]; ?>" <? echo $selOpc; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . " (".$reg2[unidad].") - ". $reg2[TipoContrato] ;  ?></option>
      <? } ?>
    </select>
      <input name="elProyecto" type="hidden" id="elProyecto" value="<? echo $kProyecto; ?>">
      <input name="laActividad" type="hidden" id="laActividad" value="<? echo $kActiv; ?>">
      <input name="estaUnidad" type="hidden" id="estaUnidad" value="<? echo $kUnidad; ?>"></td>
  </tr>
  <tr align="center">
    <td colspan="2" class="TxtTabla"><strong>&iquest;Est&aacute; seguro que desea eliminar este programador del proyecto?</strong></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cancelar">      <input name="Submit" type="submit" class="Boton" value="Eliminar"></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
