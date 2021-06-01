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

//5Marzo2008
$sql6="select P.* , C.nombre ";
$sql6=$sql6." from ProgAsignaRecursosCat P, Categorias C ";
$sql6=$sql6." where P.id_categoria = C.id_categoria ";
$sql6=$sql6." AND P.id_categoria = " . $cualItem ;
$cursor6 = mssql_query($sql6);
if ($reg6=mssql_fetch_array($cursor6)) {	 
	$zid_categoria = $reg6[id_categoria];
	$zvalorItem = $reg6[valorItem];
}

//Si se presionó el botón Grabar
if ($miCategoria != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Realiza la inserción de la persona a la tabla ProgAsignaRecursosCat
	//id_proyecto, unidadProgramador, id_categoria, valorItem		
	$query = "DELETE FROM ProgAsignaRecursosCat " ;	
	$query = $query . " WHERE id_proyecto=" . $cualProyecto . " " ;
	$query = $query . " AND unidadProgramador=" .  $laUnidad . " ";	
	$query = $query . " AND id_categoria = " . $miCategoria . " ";	
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgDivisionRec.php?cualProyecto=$cualProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");			
}


?>
<html>
<head>
<title>Programaci&oacute;n de Asignaci&oacute;n de recursos</title>
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
    <td class="TituloUsuario">Programación de Proyectos - Asignaci&oacute;n por recursos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><select name="pCategoria" class="CajaTexto" id="pCategoria" disabled >
          <?
			@mssql_select_db("HojaDeTiempo",$conexion);
			$sql2="select *  " ;
			$sql2=$sql2." from Categorias " ;
			$sql2=$sql2." WHERE id_categoria =" . $zid_categoria ;
			$cursor2 = mssql_query($sql2);
			while ($reg2=mssql_fetch_array($cursor2)) {
			?>
          <option value="<? echo $reg2[id_categoria]; ?>" <? echo $selCat; ?> ><? echo $reg2[nombre]; ?></option>
          <? } ?>
            </select>
      <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">
      <input name="miCategoria" type="hidden" id="miCategoria" value="<? echo  $zid_categoria ; ?>">
</td>
  </tr>
  <tr>
    <td class="TituloTabla">Valor</td>
    <td class="TxtTabla"><input name="lValor" type="text" class="CajaTexto" id="lValor" value="<? echo $zvalorItem; ?>" disabled></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar este registro?</strong></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
          <input name="Submit" type="submit" class="Boton" value="Eliminar"></td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
