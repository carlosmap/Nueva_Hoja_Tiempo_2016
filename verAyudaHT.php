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
if ($lNombre != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Encuentra la siguiente secuencia para la actividad en el proyecto
	$sql="Select Max(IDSitio) as MaximoSitio from sitiosTrabajo where id_proyecto =" . $miProyecto ;
	
	$cursor = mssql_query($sql);
	if ($reg=mssql_fetch_array($cursor)) {
		$pIdST = $reg[MaximoSitio] + 1;
		}
	else {
		$pIdST = 1;
	}
			
	//Realiza la inserción de la actividad en la tabla Actividades
	$query = "INSERT INTO sitiosTrabajo(id_proyecto, IDsitio, NomSitio )  " ;
	$query = $query . " VALUES( " . $miProyecto . ", " ;
	$query = $query . $pIdST . ", ";
	$query = $query . " '" . $lNombre . "' ";
	$query = $query . " ) ";
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosST.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function compareFechas() { 
//alert(document.Form1.lFechaInicio.value);
//alert(document.Form1.lFechaFin.value);
	fecha1=new Date(document.Form1.lFechaInicio.value); 
	fecha2=new Date(document.Form1.lFechaFin.value); 

	diferencia = fecha1 - fecha2; 
//  	alert(diferencia);
   	if (diferencia > 0) {
   		alert ("La fecha inicial es MAYOR que la fecha de finalización, por favor realice la corrección.");
		document.Form1.lFechaFin.value = "";
		}
//      return 1; 
//   else if (diferencia < 0) 
//   		alert ("La fecha inicial es MENOR que la fecha de finalización ");
//      return -1; 
//   else 
//   	alert ("La fecha inicial es IGUAL que la fecha de finalización ");
//      return 0; 
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Ayuda</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla"><img src="img/images/AyudaHT.jpg" width="569" height="200"></td>
        </tr>
        <tr>
          <td class="TxtTabla"><strong>Aspectos a tener en cuenta: </strong></td>
        </tr>
        <tr>
          <td class="TxtTabla">Clase de tiempo </td>
        </tr>
        <tr>
          <td class="TxtTabla">
            <li>Para Tipo de contrato de Tiempo completo la clase de tiempo debe ser 1. </li>
          </td>
        </tr>
		<tr>
          <td class="TxtTabla">
            <li>Para Tipo de contrato de Medio tiempo la clase de tiempo debe ser 2. </li>
          </td>
        </tr>
		<tr>
		  <td class="TxtTabla">Localizaci&oacute;n</td>
	    </tr>
		<tr>
          <td class="TxtTabla">
            <li>Para horarios de campo la localizaci&oacute;n es 2. </li>
          </td>
        </tr>
		<tr>
          <td class="TxtTabla">
            <li>Para personas que no han sido trasladadas, las novedades se reportan con lozalizaci&oacute;n 1.</li>
          </td>
        </tr>
		<tr>
          <td class="TxtTabla">
            <li>Para personas con traslado las novedades se reportan con lozalizaci&oacute;n 2.</li>
          </td>
        </tr>
		<tr>
          <td class="TxtTabla">
            <li>Para personas con categor&iacute;a 53 a 62 toda la facturaci&oacute;n se reporta con localizaci&oacute;n 3. </li>
          </td>
        </tr>
		
        <tr>
          <td height="3" align="right" class="TituloUsuario"> </td>
        </tr>
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onClick="MM_callJS('window.close();')" value="Cerrar"></td>
        </tr>
      </table>    </td>
  </tr>
</table>

</body>
</html>
