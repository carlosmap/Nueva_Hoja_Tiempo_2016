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

//Trae la información de la estructura del nombre de archivo para el proyecto.
//EstructuraNomProyecto
//id_proyecto, conCodProyecto, conCodDisciplina, conCodTipoPlano, conCodDpto, 
//conCodObra, conCodEtapa, conConsecutivo, conCodEstadoDib, unidad, fecha
$sql="SELECT * FROM GestiondeInformacionDigital.dbo.EstructuraNomProyecto WHERE id_proyecto =" .  $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$mconCodProyecto = $reg[conCodProyecto] ;
	$mconCodDisciplina = $reg[conCodDisciplina] ;
	$mconCodTipoPlano = $reg[conCodTipoPlano] ;
	$mconCodDpto = $reg[conCodDpto] ;
	$mconCodObra = $reg[conCodObra] ;
	$mconCodEtapa = $reg[conCodEtapa] ;
	$mconConsecutivo = $reg[conConsecutivo] ;
	$mconCodEstadoDib = $reg[conCodEstadoDib] ;
	$munidad = $reg[unidad] ;
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Horarios</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
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

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
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
			<? echo $pNombre; ?>			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Proyecto? </td>
            <td class="TxtTabla">
			<?
			if ($mconCodProyecto == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			<input name="codProy" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codProy" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Disciplina? </td>
            <td class="TxtTabla">
			<?
			if ($mconCodDisciplina == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>

			<input name="codDis" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codDis" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Tipo de Plano? </td>
            <td class="TxtTabla">
			<?
			if ($mconCodTipoPlano == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			
			<input name="codTipoP" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codTipoP" type="radio" value="0" <? echo $SelNO; ?> disabled>
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Departamento? </td>
            <td class="TxtTabla">
			<?
			if ($mconCodDpto == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			
			<input name="codDpto" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codDpto" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Obra?</td>
            <td class="TxtTabla">
			<?
			if ($mconCodObra == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			
			<input name="codObra" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codObra" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de Etapa? </td>
            <td class="TxtTabla">
			<?
			if ($mconCodEtapa == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			
			<input name="codEtapa" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codEtapa" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene Consecutivo? </td>
            <td class="TxtTabla">
			<?
			if ($mconConsecutivo == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			
			<input name="codSec" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codSec" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No</td>
          </tr>
          <tr>
            <td class="TituloTabla">Tiene C&oacute;digo de estado de dibujo? </td>
            <td class="TxtTabla">
			<?
			if ($mconCodEstadoDib == 1) {
				$SelSI="checked";
				$SelNO="";
			}
			else {
				$SelSI="";
				$SelNO="checked";
			}
			?>
			
			<input name="codDib" type="radio" value="1" <? echo $SelSI; ?> disabled >
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <input name="codDib" type="radio" value="0" <? echo $SelNO; ?> disabled >
            No			</td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="Submit" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cerrar ventana" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
