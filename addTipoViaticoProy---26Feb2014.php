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
$sql3="SELECT DISTINCT t.IDTipoViatico, t.NomTipoViatico  " ;
$sql3=$sql3." FROM TiposViatico t " ;
$sql3=$sql3." Where Not Exists " ;
$sql3=$sql3." (SELECT * " ;
$sql3=$sql3." FROM TiposViaticoProy p " ;
$sql3=$sql3." Where t.IDTipoViatico = p.IDTipoViatico " ;
$sql3=$sql3." AND id_proyecto = " . $cualProyecto ;
$sql3=$sql3." ) " ;
$cursor3 = mssql_query($sql3);

//Si se presionó el botón Grabar
if ($miTipoV != "") {
	//Realiza la inserción del tipo de viático al proyecto en dbo.TiposViaticoProy
	//id_proyecto, IDTipoViatico, IncluyeFestivos
	$query = "INSERT INTO TiposViaticoProy (id_proyecto, IDTipoViatico, IncluyeFestivos) " ;
	$query = $query." VALUES ( " . $miProyecto . ", " ;
	$query = $query. $miTipoV . ", " ;
	$query = $query. $miFestivo ;
	$query = $query." ) " ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosTDV.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Tipos de vi&aacute;tico</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
			<select name="miTipoV" class="CajaTexto" id="miTipoV">
	      	<? 	while ($reg3=mssql_fetch_array($cursor3)) {  ?>
              <option value="<? echo  $reg3[IDTipoViatico] ; ?>"><? echo  ucfirst(strtolower($reg3[NomTipoViatico])) ; ?></option>
			<? } ?>
            </select>              
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">		    </td>
          </tr>
          <tr>
            <td class="TituloTabla">&iquest;Incluye Festivos?</td>
            <td class="TxtTabla"><input name="miFestivo" type="radio" value="1" checked>
              Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;              <input name="miFestivo" type="radio" value="0">
              No </td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla">      <input name="Submit" type="submit" class="Boton" value="Grabar" ></td></tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
