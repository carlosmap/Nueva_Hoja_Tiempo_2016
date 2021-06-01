<?php
session_start();
include "funciones.php";
include "validaUsrBd.php";
//$laUnidad = $sesUnidadUsuario;

if (trim($_SESSION["sesUnidadSuplanta"]) != "") {
	echo "<script>alert('No es posible cambiar de usuario a partir de una suplantación. Por favor salga del sistema y registrese de nuevo.')</script>";
	echo "<script>location.href=\"frm-GrabaTiempo.php\"</script>";
}
$sql = "SELECT * FROM HojaDeTiempo.dbo.Usuarios
	WHERE retirado IS NOT NULL ";
//	" AND fechaRetiro > DATEADD(mm, -3, GETDATE()) ";
$sql = $sql . " and unidad = "  . $boxUnidad;
$cursor = mssql_query($sql);

//	echo "1-laUnidad=" . $laUnidad . "<br>";
//	echo "1-Suplanta=" . $_SESSION["sesUnidadSuplanta"] . "<br>";

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script>
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cambiar usuario en el sistema</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<? include("bannerArriba.php") ; ?>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    
  </tr>
  <tr><td></td></tr>
  <tr><td> </td></tr>
    <tr>
      <td class="TituloUsuario">Hojas de tiempo - Usuarios retirados </td>
    </tr>

<tr><td>
<form name="frmAutorizar" action="AutorizaRetirados.php" method="POST">
<table>

</table>
<table width="100%"  border="1" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td class="TituloTabla">Unidad del usuario a suplantar </td>
    <td class="TxtTabla"><input name="boxUnidad" type="text" class="CajaTexto" id="boxUnidad" value="<? echo $boxUnidad; ?>" />
      <input name="Submit" type="submit" class="Boton" value="Consultar" /></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario retirado<br /></td>
    <td class="TxtTabla">
	<select name="lstRetirados" class="CajaTexto" id="lstRetirados">
	  <? while($reg = mssql_fetch_array($cursor)){ ?>
      <option value="<? echo $reg[unidad]; ?>"><? echo $reg[apellidos] . " " . $reg[nombre] . " [". $reg[unidad] . "] " ; ?></option>
	  <? } ?>
    </select>
	<?
	if ((trim($boxUnidad)!= "") AND (mssql_num_rows($cursor) == 0)) {
		echo "Este usuario no existe o no se encuentra retirado. Por favor verifique la información.";
	}
	?>
	</td>
  </tr>
</table>
<table>
<tr><td> </td></tr>
<tr>
<td>
<? if (mssql_num_rows($cursor) > 0) { ?>
<input name="Autorizar" type="submit" class="Boton" value="Cambiar">
<? } ?>
</td>
<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value=" Página principal Hoja tiempo  "></td></tr>

</table>
</form>
<?php
if($Autorizar == "Cambiar"){
	//$laUnidad = $cambioUnidad;
	$_SESSION["sesUnidadSuplanta"] = $laUnidad;
	$laUnidad = $lstRetirados;

	echo "<script>alert('Usuario cambiado')</script>";
	echo "<script>location.href=\"frm-GrabaTiempo.php\"</script>";
}

?>
</td></tr>
</table>
</body>
</html>
