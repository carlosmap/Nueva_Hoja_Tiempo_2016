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

//18Mar2008
//Trae el listado de horarios disponibles, es decir todos aquellos que uun no han sido asociados al proyecto seleccionado
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo  " ;
$sql3=$sql3." FROM Horarios h " ;
$sql3=$sql3." Where Not Exists  " ;
$sql3=$sql3."   (SELECT * " ;
$sql3=$sql3."   FROM HorariosProy p " ;
$sql3=$sql3."   Where h.IDhorario = p.IDhorario " ;
$sql3=$sql3."   AND id_proyecto = " . $cualProyecto ;
$sql3=$sql3."   ) " ;
$cursor3 = mssql_query($sql3);

//Si se presionó el botón Grabar
if ($miHorario != "") {
	//Realiza la inserción de la actividad en la tabla dbo.HorariosProy
	//IDhorario, id_proyecto, HorarioDefecto, ubicacion
	$query = "INSERT INTO  HorariosProy (IDhorario, id_proyecto, HorarioDefecto, ubicacion) " ;
	$query = $query." VALUES( " . $miHorario . " , " ;
	$query = $query . $miProyecto . ", ";
	$query = $query .  " 0, ";
	if (trim($lLocaliza) == "") {
		$query = $query .  " NULL ";
	}
	else {
		$query = $query .  " '".$lLocaliza."' ";
	}
	$query = $query . " ) ";
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectoConfig.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Configuraci&oacute;n del Proyecto - Horarios del Proyecto</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
function totalizar(campo){ 
	//Valida que los campos Lunes a domingo sean numéricos 
	if (document.Form1.elements[campo].value == '') {
		document.Form1.elements[campo].value = 0;
		alert('Los campos Lunes a Domingo son obligatorios y numéricos. Por favor verifique la información. \n');
	}
	if (isNaN(document.Form1.elements[campo].value)) {
		document.Form1.elements[campo].value = 0;
		alert('Los campos Lunes a Domingo son obligatorios y numéricos. Por favor verifique la información. \n');
	}
	else {
		//Valida que el valor ingresado de Lunes a domingo no sea mayor que 20
		if (document.Form1.elements[campo].value > 20) {
			document.Form1.elements[campo].value = 0;
			alert('Los campos Lunes a Domingo son obligatorios y numéricos entre el rango 1 - 20. Por favor verifique la información. \n');
		}
	}
	document.Form1.elements['lTotal'].value = parseFloat(document.Form1.elements['lLunes'].value)+parseFloat(document.Form1.elements['lMartes'].value)+parseFloat(document.Form1.elements['lMiercoles'].value)+parseFloat(document.Form1.elements['lJueves'].value)+parseFloat(document.Form1.elements['lViernes'].value)+parseFloat(document.Form1.elements['lSabado'].value)+parseFloat(document.Form1.elements['lDomingo'].value) ;
}

function enviar(){ 
var v1,msg1,mensaje;

//Valida que se haya ingresado el nombre del horario
	if (document.Form1.lNombre.value == '') {
		document.Form1.recarga.value="1";
		alert('El campo Nombre de horario es obligatorio. \n');
	}
	else {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">.: HORARIOS</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
		<form action="" method="post" name="Form1" >
          <tr>
            <td width="15%" class="TituloTabla">Horario</td>
            <td class="TxtTabla"><select name="miHorario" class="CajaTexto" id="miHorario">
              <? 	while ($reg3=mssql_fetch_array($cursor3)) {  ?>
              <option value="<? echo $reg3[IDhorario] ; ?>"><? echo ucfirst(strtolower($reg3[NomHorario])) . " " . $reg3[Lunes] . " - " . $reg3[Martes]  . " - " . $reg3[Miercoles]  . " - " . $reg3[Jueves]  . " - " . $reg3[Viernes]  . " - " . $reg3[Sabado]  . " - " . $reg3[Domingo] ; ?></option>
              <? } ?>
            </select>
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>"></td>
          </tr>
          <tr>
            <td width="15%" class="TituloTabla">Localizaci&oacute;n</td>
            <td class="TxtTabla"><select name="lLocaliza" class="CajaTexto" id="lLocaliza">
              <option value=" "> </option>
              <option value="1">Oficina</option>
              <option value="2">Campo</option>
              <option value="3">Planilla</option>
            </select></td>
          </tr>
          <tr align="right">
            <td colspan="2" class="TxtTabla"><span class="TxtTabla">
              <input name="Submit" type="submit" class="Boton" value="Grabar" >
            </span></td>
          </tr>
          <tr align="right">
            <td colspan="2" class="TxtTabla">&nbsp;</td>
          </tr>
		  </form>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla">&nbsp;</td>
  </tr>
</table>
      
  	</td>
  </tr>
</table>

</body>
</html>
