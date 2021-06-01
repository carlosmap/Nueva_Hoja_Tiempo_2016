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
//Trae los tipos de viático del proyecto seleccionado y tipo de viático seleccionado
$sql2="SELECT p.id_proyecto, p.IncluyeFestivos, v.*  " ;
$sql2=$sql2." FROM TiposViaticoProy p, TiposViatico v  " ;
$sql2=$sql2." Where p.IDTipoViatico = v.IDTipoViatico " ;
$sql2=$sql2." and p.id_proyecto = " . $cualProyecto ;
$sql2=$sql2." and p.IDTipoViatico = " . $cualTipoV ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {  
	$pIDTipoViatico = $reg2[IDTipoViatico] ;
	$pNomTipoViatico = ucfirst(strtolower($reg2[NomTipoViatico])) ;
	$pIncluyeFestivos = $reg2[IncluyeFestivos] ;
}


//Si se presionó el botón Grabar
if ($miTipoV != "") {
	//Realiza la inserción del tipo de viático al proyecto en dbo.TiposViaticoProy
	//id_proyecto, IDTipoViatico, IncluyeFestivos
	$query = "DELETE FROM TiposViaticoProy " ;
	$query = $query." WHERE id_proyecto = " . $miProyecto  ;
	$query = $query." AND IDTipoViatico =" .  $miTipoV ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Eliminación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosTDV.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
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
    <td class="TituloUsuario">Tipos de vi&aacute;tico del proyecto </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="25%" class="TituloTabla">Tipo de vi&aacute;tico </td>
            <td class="TxtTabla">
              <? echo $pNomTipoViatico; ?>
			  <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
              <input name="miTipoV" type="hidden" id="miTipoV" value="<? echo $pIDTipoViatico; ?>"></td>
          </tr>
          <tr>
            <td class="TituloTabla">&iquest;Incluye Festivos?</td>
            <td class="TxtTabla">
			<? 
		if (trim($pIncluyeFestivos) == "1" ) {
			echo "SI" ; 
		}
		else {
			echo "NO" ; 
		}
		?>
			</td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><strong>&iquest;Esta seguro de desasociar este Tipo de vi&aacute;tico del proyecto?</strong></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla">      <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
      <input name="Submit" type="submit" class="Boton" value="Borrar" ></td></tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
