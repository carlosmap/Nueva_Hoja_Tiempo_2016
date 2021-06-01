<?
	session_start();
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	$nombrecomputador="sqlservidor";
	include "funciones.php";
	include "validacion.php";
	//$laUnidad="12974";
	//$clave="1373";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>ADP. Edición</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>
var newwindow;
function vermuestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes, toolbar=yes');
	if (window.focus) {newwindow.focus()}
}
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> ACTUALIZACIÓN DEL ADP </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>	
     <td class="TituloUsuario">Actualización del ADP/Ingreso del mismo</td>
</table>

<div style="position:absolute; left:4px; top:137px;">

<form name="DatosEntrada" action="" method="post">
	<table class="TxtTabla">
	<tr><td>Seleccione la fecha:</td><td><input type="text" name="timestamp" value=<?echo $timestamp;?>>
	<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.DatosEntrada.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
				<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
				</iframe>
		</td></tr>
	</table>
		<table>
	<tr><td> </td></tr>
	<tr><td> </td></tr>
	<tr><td><input type="submit" class=Boton name="ConsultarDatos" value="Consultar"></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','EdicionRegistros-mnu.php');return document.MM_returnValue" value="  Atras   "></a></td></tr>

</form>
</div>
</body>
</html>

<?

if($ConsultarDatos=="Consultar"){
	
	include "validaUsrBd.php";
	
	//Verifica que la hoja no esté cerrada
	$fecha = explode("/",$timestamp);	
	
	$sql = "select * from autorizacionesht where unidad=$laUnidad and vigencia = $fecha[2] and mes = $fecha[0]";
	$ap = mssql_query($sql);
	$regV = mssql_fetch_array($ap);
	$valEncargado = $regV[validaJefe];
	
	if($valEncargado == 1) {
		echo "<script>alert('Su hoja de tiempo ya fué aprobada. No podrá realizar ninguna modificación en este periodo. Su jefe inmediato podrá desbloquearla')</script>";	
		
		exit();
	}
	
	$sql="SELECT * FROM adp WHERE unidad='$laUnidad' and fecha='$timestamp'";
	

	$rpta=mssql_query($sql);
	$reg = mssql_fetch_array($rpta);
	$adpActual = $reg[adp];
	$cargo2 = $reg[cargo];

echo "<form name='edicion' action='' method='post'>
	<input type=hidden name=cargo value='$cargo2'>
	<input type=hidden name=fecha value='$timestamp'>
	<table class='TxtTabla'>
	<tr><td>DIGITE EL NUEVO ADP</td></tr>
	<tr><td> </td></tr>
	<tr><td>ADP Actual</td><td>$adpActual</td></tr>
	<tr><td>Cambiarlo por</td><td><input type='text' name='adpNuevo' value='$adpNuevo' size=20></td></tr>
	<tr><td> </td><td> </td></tr>
	<br><br>
	<tr><td><input name='Grabar' class='Boton' type='submit' value='Actualizar'></td></tr>

	</table>
	</form>";

}
//Si le dan click al botón actualizar
?>


<?php
if($Grabar=="Actualizar"){
	include "validaUsrBd.php";
	
	$SqlUpdate="update adp set adp='$adpNuevo' where
	(unidad = '$laUnidad') AND (cargo = '$cargo') AND (fecha = '$fecha')";
	
	
	mssql_query($SqlUpdate);
	
		
	if(mssql_query($SqlUpdate)){
		echo "<script>alert('El registro fué actualizado');</script>";
	}else{
		echo "<script>alert('El registro no fue actualizado. Consulte con el administrador')</script>";
	}

}
?>