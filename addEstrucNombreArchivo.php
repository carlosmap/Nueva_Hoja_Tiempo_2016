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

//Trae la información del proyecto seleccionado
$sql="SELECT * FROM HojaDeTiempo.dbo.proyectos WHERE id_proyecto =" .  $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$pNombre=$reg[nombre] ;
}

//Si se presionó el botón Grabar
if ($recarga == 2) {
	$cantSi = 0;
	//Verifica que al menos selecione un botón de opción si.
	$cantSi= $codNiv + $codProy + $codTem + $codLC + $codDis + $codTipoP + $codDpto + $codObra + $codEtapa + $codDib ;
	
	if ($cantSi == 0) {
		echo ("<script>alert('Es necesario seleccionar por lo menos algún parámetro en Si.');</script>");
		echo ("<script>window.close()</script>");	
		exit;
	}
	else {
		//Graba la información en la tabla dbo.EstructuraNomProyecto
		//id_proyecto, conCodNivel, conCodProyecto, conCodTema, conCodLote, conCodDisciplina, 
		//conCodTipoPlano, conCodDpto, conCodObra, conCodEtapa, conConsecutivo, conConsecutivoExt, 
		//conCodEstadoDib, unidad, fecha
		$query = "INSERT INTO  GestiondeInformacionDigital.dbo.EstructuraNomProyecto ( " ;
		$query = $query." id_proyecto, conCodNivel, conCodProyecto, conCodTema, conCodLote, conCodDisciplina,  " ;
		$query = $query." conCodTipoPlano, conCodDpto, conCodObra, conCodEtapa, conConsecutivo, conConsecutivoExt,  " ;
		$query = $query." conCodEstadoDib, unidad, fecha ) " ;
		$query = $query." VALUES(" . $miProyecto . " , " ;
		$query = $query . " '" . $codNiv . "', ";
		$query = $query . " '" . $codProy . "', ";
		$query = $query . " '" . $codTem . "', ";
		$query = $query . " '" . $codLC . "', ";
		$query = $query . " '" . $codDis . "', ";
		$query = $query . " '" . $codTipoP . "', ";
		$query = $query . " '" . $codDpto . "', ";
		$query = $query . " '" . $codObra . "', ";
		$query = $query . " '" . $codEtapa . "', ";
		//Graba uno de los 2 consecutivos
		if (trim($codSec) == "I" ) {
			$query = $query . " '1', ";
			$query = $query . " '0', ";
		}
		else {
			$query = $query . " '0', ";
			$query = $query . " '1', ";
		}
		$query = $query . " '" . $codDib . "', ";
		$query = $query . " " . $laUnidad . ", ";
		$query = $query . " '" . gmdate ("n/d/y") . "' ";
		$query = $query . " ) ";
		$cursor = mssql_query($query) ;	
		
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		echo ("<script>window.close();MM_openBrWindow('infProyecto.php?cNombre=$miNombre','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
	}
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Horarios</title>
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
    <td class="TituloUsuario">Estructura del nombre de los archivos del proyecto </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="14%" class="TituloTabla">Proyecto</td>
            <td width="98%" class="TxtTabla">
			<? echo $pNombre; ?>			<input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
            <input name="recarga" type="hidden" id="recarga" value="2">
            <input name="miNombre" type="hidden" id="miNombre" value="<? echo $pNombre; ?>"></td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene Nivel de estudio? </td>
            <td class="TxtTabla">
			<input name="codNiv" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codNiv" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Proyecto? </td>
            <td class="TxtTabla"><input name="codProy" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codProy" type="radio" value="0" checked>
            No</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene Tema? </td>
            <td class="TxtTabla">
			<input name="codTem" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codTem" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene Lote de control? </td>
            <td class="TxtTabla">
			<input name="codLC" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codLC" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Disciplina? </td>
            <td class="TxtTabla"><input name="codDis" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codDis" type="radio" value="0" checked>
            No</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Tipo de Plano? </td>
            <td class="TxtTabla">
			<input name="codTipoP" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codTipoP" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Departamento? </td>
            <td class="TxtTabla">
			<input name="codDpto" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codDpto" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Obra?</td>
            <td class="TxtTabla">
			<input name="codObra" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codObra" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Etapa? </td>
            <td class="TxtTabla">
			<input name="codEtapa" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codEtapa" type="radio" value="0" checked>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene Consecutivo? </td>
            <td class="TxtTabla"><input name="codSec" type="radio" value="I" checked>
            Por Proyecto y Departamento &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <br>            <input name="codSec" type="radio" value="E">
            Por Proyecto </td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de estado de dibujo? </td>
            <td class="TxtTabla">
			<input name="codDib" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codDib" type="radio" value="0" checked>
            No			</td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td align="center" class="TxtTabla">Esta información es irreversible. Por favor verifíquela antes de grabar.</td>
  </tr>
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
