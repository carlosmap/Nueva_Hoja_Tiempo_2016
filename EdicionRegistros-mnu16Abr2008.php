<?
	session_start();
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	$nombrecomputador="sqlservidor";
	include "funciones.php";
	include "validacion.php";
	//$login="12974";
	//$clave="1373";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edición Registros</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>
		function limpiaradpelim(){
			document.DatosEntrada.adp.checked=false;
			document.DatosEntrada.eliminar.checked=false;
		}
		
		function limpiarhoraselim(){
			document.DatosEntrada.horas.checked=false;
			document.DatosEntrada.eliminar.checked=false;
		}
		
		function limpiaradphoras(){
			document.DatosEntrada.horas.checked=false;
			document.DatosEntrada.adp.checked=false;
		}
		
		<!--Codigo Nuevo-->
			function limpiar(){
				document.DatosEntrada.horas.checked=false;
				document.DatosEntrada.adp.checked=false;
				document.DatosEntrada.eliminar.checked=false;
			}
		<!--fin-->
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
</script>

</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 485px;">
		<div align="center"> EDICI&Oacute;N DE REGISTROS - MEN&Uacute; GENERAL </div>
</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
   
  <TR><TD>
  <form name="DatosEntrada" action="" method="post">
	<table>
	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
	<tr><td class="TxtTabla">Cambiar horas y resumen del trabajo</td>
	<td><input type=radio name=horas value=1 onClick="limpiaradpelim()";></td></tr>
	<tr><td class="TxtTabla">Actualizar el ADP (Vacaciones, Permisos, etc)</td>
	<td><input type=radio name=adp value=2 onClick="limpiarhoraselim()";></td></tr>
	<tr><td class="TxtTabla">Eliminar un registro</td>
	<td><input type=radio name=eliminar value=3 onClick="limpiaradphoras()";></td></tr>
	
	<!--Codigo Nuevo-->
		<tr><td class="TxtTabla">Eliminar Viáticos</td>
		<td><input type=radio name=eliminaViatico value=4 onClick="limpiar()";></td></tr>
	<!--fin-->
	<tr class="TxtTabla">
	  <td> </td></tr>
	  </table>
	  <table class="TxtTabla">
	<tr>
	  <td><input name="Consulta" type="submit" class="Boton" value="Ir a la opción seleccionada"></td><td></a></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  P&aacute;gina principal Hoja tiempo   ">
	  </a></td></tr>
	</table>
</form>
<?


if($Consulta=="Ir a la opción seleccionada"){
	if ($horas==1){
		echo "<script>location.href='editar.php'</script>";
	}elseif($adp==2){
		echo "<script>location.href='EdicionRegistros-3.php'</script>";
	}elseif($eliminar==3){
		echo "<script>location.href='EdicionRegistros-4Elim.php'</script>";
		//Codigo Nuevo
	}elseif($eliminaViatico==4){
		echo "<script>location.href='EdicionRegistros-ElimViaticos.php'</script>";
	}
		//Fin
}
?>
</TD></TR>
</table>
</body>
</html>
