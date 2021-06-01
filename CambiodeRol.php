<?php
	session_start();
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script>
<!--
function desactivar(obj){
		document.autoriza.autorizar.checked=false
		document.autoriza.desautorizar.checked=false
		obj.checked=true
	}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Autorización a terceros</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<? include("bannerArriba.php") ; ?>
<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
	<div align="center"> AUTORIZACIÓN A TERCEROS </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">

 <TR><TD><form name="autoriza" action="CambiodeRol.php" method="POST">

  <table>
	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
    <tr><td class="TxtTabla">Seleccione nombre: </td>
    <td>
	<?php
	//Lee las unidades existentes en la base de datos con sus nombres
	$sql ="select * from usuarios where retirado is null order by apellidos, nombre";
	include "validaUsrBd.php";
	$ap = mssql_query($sql);
	
	echo "<select name=autorizado>";
	if($ap){
		while ($registro =  mssql_fetch_array($ap)){
			$nnb =ucwords(strtolower($registro[apellidos]));
			$aap= ucwords(strtolower($registro[nombre]));
			echo "<option value= $registro[unidad]>$nnb $aap</option>";
		}
	}
	echo "</select>";
?>
	</td>
   </tr>

</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TxtTabla"><input type=radio name=autorizar onclick = desactivar(this); value=1>Autorizar</td></tr>
    <tr><td class="TxtTabla"><input type=radio name=desautorizar onclick = desactivar(this); value=2>Eliminar Autorización</td></tr>
	<tr><td> </td><tr>
</table>
<table>
	<tr><td><input name=Grabar type=submit class="Boton" value=Ejecutar></td>
    <td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value=" Página principal Hoja tiempo "></a></td>
  </tr>
</table>
</form>
</body>
</html>

<?php
	if($Grabar=="Ejecutar"){
		if($autorizar=="1"){
			$sql="insert into autorizaciones values($autorizado,$laUnidad)";
			if(mssql_query($sql)){
				echo "<script>alert('Autorización finalizada')</script>";
			}else{
				$sql = "select * from autorizaciones where undAutorizado=$autorizado";
				$ap = mssql_query($sql);
				$numreg=mssql_num_rows($ap);
				if($numreg>0){
					echo "<script>alert('El usuario seleccionado existe en la base de datos.')</script>";
					exit();
				}
			}
		}elseif($desautorizar=="2"){
			//Pregunta si el usuario existe
			$sql = "select * from autorizaciones where undAutorizado=$autorizado";
			$ap = mssql_query($sql);
			$numreg=mssql_num_rows($ap);
			if($numreg>0){
				$sql="delete from autorizaciones where undAutorizado = $autorizado";
				if(mssql_query($sql)){
					echo "<script>alert('Autorización eliminada')</script>";
				}
			}else{
				echo "<script>alert('El usuario seleccionado no tiene autorizaciones')</script>";
			}
		}
	}
?>
</TD></TD>
  </table>
</body>
</html>
