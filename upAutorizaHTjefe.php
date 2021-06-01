<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Si $cualAno viene vacio es porque no han cambiado las listas en la hoja de tiempo, 
//por lo tanto el mes activo es el actual
if (trim($cualAno) == "") {
	$anoAut=date("Y");
	$mesAut=date("m");
}
else {
	$anoAut=$cualAno;
	$mesAut=$cualMes;
}

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Verificar si el usuario ya existe para mostrar el jefe ya seleccionado
$sql="Select * from AutorizacionesHT ";
$sql=$sql." where vigencia = " . $anoAut;
$sql=$sql." and mes = " . $mesAut ;
$sql=$sql." and unidad = " . $cualUnidad;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$pvalidaJefe = $reg[validaJefe] ;
	$pcomentaJefe = $reg[comentaJefe] ;
	$punidadJef = $reg[unidadJefe] ;
}


//Si se presionó el botón Grabar
if ($elAno != "") {
	//Verifica si el registro ya existe en la tabla AutorizacionsHT para 
	//Determinar si se inserta o se modifica.
	$query = "UPDATE  AutorizacionesHT SET "; 
	$query = $query . " validaJefe = '" . $pAprueba . "',  ";
	$query = $query . " comentaJefe = '" . $pComenta . "',  ";
	$query = $query . " fechaAprueba = '" . gmdate ("n/d/y") . "'  ";
	$query = $query . " WHERE vigencia = " . $elAno ;
	$query = $query . " AND mes = " . $elMes ;
	$query = $query . " AND unidad = " . $laUnidadUsu ;
	$cursor = mssql_query($query) ;	

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
		echo ("<script>window.close();MM_openBrWindow('ApruebaHT.php?pMes=$elMes&pAno=$elAno','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=*,height=*');</script>");
	echo ("<script>window.close()</script>");
	
}


?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo -  Aprobaci&oacute;n</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<form action="" method="post" name="Form1" id="Form1">
  <tr>
    <td width="25%" class="TituloTabla">A&ntilde;o</td>
    <td class="TxtTabla">
	<? echo $anoAut ; ?>
	<input name="elAno" type="hidden" id="elAno" value="<? echo $anoAut ; ?>">
	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Mes</td>
    <td class="TxtTabla">
	<? echo $mesAut ; ?>
	<input name="elMes" type="hidden" id="elMes" value="<? echo $mesAut ; ?>">
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla"><? echo $cualUnidad; ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<?
		$miUsuario = "";
		//Consulta para traer el nombre del jefe que autoriza
//		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $cualUnidad ;
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuario = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo strtoupper($miUsuario); ?>
		<input name="laUnidadUsu" type="hidden" id="laUnidadUsu" value="<? echo $cualUnidad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Hoja de tiempo aprobada? </td>
    <td class="TxtTabla">
	<?
	//Si ya esta o no aprobada la hoja de tiempo
	if ($pvalidaJefe == "1") {
		$selSI = "checked";
		$selNo = "";
	}
	if ($pvalidaJefe == "0") {
		$selSI = "";
		$selNo = "checked";
	}
	?>
	<input name="pAprueba" type="radio" class="CajaTexto" value="1" <? echo $selSI; ?> >
      Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      <input name="pAprueba" type="radio" class="CajaTexto" value="0" <? echo $selNo; ?>>
      No</td>
  </tr>
  <tr>
    <td class="TituloTabla">Comentarios.</td>
    <td class="TxtTabla"><textarea name="pComenta" cols="50" rows="4" class="CajaTexto" id="pComenta"><? echo $pcomentaJefe; ?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
    </tr>
  </form>
</table>

	</td>
  </tr>
</table>

</body>
</html>
