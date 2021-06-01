<?php
	session_start();
	include "funciones.php";
	//$laUnidad = $sesUnidadUsuario;
	
	//Para que funcione lo de Quimbo para Gloria Garcia = 14714, Cindy Alfonso =14966, Diana Amado = 16650
	$_SESSION["sesUsuarioQUIMBO"] = '';
	if (($laUnidad == 14714) OR ($laUnidad == 14966) OR ($laUnidad == 16650)) {
		$_SESSION["sesUsuarioQUIMBO"] = 'SI';
	}
	
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
    <tr><td class="TituloUsuario">Al cambiar de usuario el sistema se comportará para el usuario que acaba de elegir</td>
    </tr>

<tr><td>
<form name="frmAutorizar" action="AutorizaUnidades.php" method="POST">
<table>
<?php
		$sql = "select * from autorizaciones where undAutorizado='$laUnidad'";
	
		include "validaUsrBd.php";
		$ap0 = mssql_query($sql);
		$nuregistros = mssql_num_rows($ap0);
		echo "<br>";
		echo "<tr><td class='TxtTabla'>Seleccione la unidad: </td>";
		if ($nuregistros>0){
			echo "<TD><select name=cambioUnidad>";
			while($registro = mssql_fetch_array($ap0)){
				//Consulta el nombre del paciente
				$undUsr = $registro[undAutorizar];
//				$sql = "select nombre, apellidos from usuarios where unidad = '$undUsr' ";
				$sql = "select nombre, apellidos from usuarios where unidad = '$undUsr' order by apellidos ";
				$ap1 = mssql_query($sql);
				$Usr = mssql_fetch_array($ap1);
				$nomb = $Usr[nombre];
				$apell = $Usr[apellidos];
				//$nombreUsr = ucwords($nomb)." ".ucwords($apell);
				$nombreUsr = ucwords($apell)." ".ucwords($nomb);
				echo "<option value = $undUsr>$nombreUsr</option>";
			}
		echo "</TD></tr></select>";
		}else{
			echo "<script>alert('Usted no tiene usuarios a cargo!')</script>";
			echo "<script>location.href=\"frm-GrabaTiempo.php\"</script>";
		}
?>
</table>
<table>
<tr><td> </td></tr>
<tr><td><input name="Autorizar" type="submit" class="Boton" value="Cambiar"></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value=" Página principal Hoja tiempo  "></td></tr>

</table>
</form>

<?php
if($Autorizar == "Cambiar"){
	//$laUnidad = $cambioUnidad;
	$laUnidad = $cambioUnidad;
	echo "<script>alert('Usuario cambiado')</script>";
	echo "<script>location.href=\"frm-GrabaTiempoTmp.php?cambioUn=$cambioUnidad\"</script>";
}

?>
</td></tr>
</table>
</body>
</html>
