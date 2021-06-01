<?
session_start();
session_register("Sunidad");
session_register("Sfecha");
session_register("$HR");
session_register("$RT");
//Evalua desde que computador esta entrando
/*	$dirInterna = gethostbyaddr($REMOTE_ADDR);
if($dirInterna!="192.168.1.1"){
echo "<h4>Su intento de acceso no está permitido...<h4>";
echo "<h4>usted se encuentra fuera de la red de INGETEC<h4>";
echo "Usted se encuentra en el siguiente equipo   ";
echo gethostbyaddr($REMOTE_ADDR);
exit;
}*/

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
$nombrecomputador="sqlservidor";
include "funciones.php";
include "validacion.php";
//$laUnidad="12974";
//$clave="1373";
?>

<html>
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="ts_picker.js"></script>
</head>
<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center">ACTUALIZACIÓN DE REGISTROS </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>


</body>
</html>


<?

if (empty($Grabar)) {
	include "validaUsrBd.php";

	$sql="SELECT * FROM Horas WHERE id_proyecto = '$idPry' and id_actividad = '$idAct' and unidad = '$laUnidad' AND
	fecha = '$fecha' and localizacion = '$idLoc' and cargo = '$idCar' and clase_tiempo = '$idCtp' ";


	$rpta=mssql_query($sql);


	//Despliega lo encontrado
	echo "<table class='TxtTabla'>";
	$reg = mssql_fetch_array($rpta);
	$HR = $reg[horas_registradas];
	$RT = $reg[resumen_trabajo];
	$CG = $reg[cargo];
	$IP = $reg[id_proyecto];
	$loc = $reg[localizacion];
	$acti = $reg[id_actividad];
	$ct = $reg[clase_tiempo];

	echo "</table>";
}
?>

<form name="edicion" action="EdicionRegistros-2.php" method="post">
<!--Almacena los valores para usarlos en la consulta-->
<input type=hidden name=idP value=<?php echo $IP?>>
<input type=hidden name=fechaC value=<?php echo $fecha?>>
<input type=hidden name=cargo value=<?php echo $CG?>>
<input type=hidden name=localiza value=<?php echo $loc?>>
<input type=hidden name=activida value=<?php echo $acti?>>
<input type=hidden name=claset value=<?php echo $ct?>>


<table class="TxtTabla">
<tr><td><b>Puede cambiar los siguientes datos:</font></b></td></tr>
<tr><td><br></td></tr>
<tr><td>Horas registradas</td><td><input type="text" name="horasregistradas" value='<?echo $HR;?>'></td></tr>
<tr><td>Resumen Trabajo</td><td><input type="text" name="resumentrabajo" value='<?echo $RT;?>' size=80></td></tr>
<tr><td> </td><td> </td></tr>
<br><br>
<tr><td><input name="Grabar" type="submit" value="Actualizar" class="Boton">  <a href="#"><input name=atras type=button class="Boton" onclick="history.back();" value="  Atras   "></a></td></tr>
<tr><td></td></tr>
	
</table>
</form>

<?
//Si le dan click al botón actualizar

if($Grabar=="Actualizar") {
	include "validaUsrBd.php";
	if ($horasregistradas<=0) {
		//Si pone cero se elimina el registro
		$SqlHoras="delete from horas where (id_proyecto = '$idP') AND (id_actividad= '$activida') and (unidad = '$laUnidad') AND (fecha = '$fechaC')
		and (localizacion = '$localiza') AND (cargo='$cargo') and (clase_tiempo = '$claset')";
		mssql_query($SqlHoras);
		echo "<script>alert('El registro fue eliminado')</script>"	;
	} elseif (trim($resumentrabajo)=='') {
		echo "<script>alert('Debe escribir el resumen del trabajo, no se admiten blancos')</script>"	;
	}	else {
		$SqlHoras="update horas set horas_registradas='$horasregistradas', resumen_trabajo='$resumentrabajo'
		where (id_proyecto = '$idP') AND (id_actividad= '$activida') and (unidad = '$laUnidad') AND (fecha = '$fechaC')
		and (localizacion = '$localiza') AND (cargo='$cargo') and (clase_tiempo = '$claset')";
		
		if(mssql_query($SqlHoras)){
			echo "<script>alert('El registro fué actualizado');</script>";
			echo "<script>history.back()-1</script>";
		}else{
			echo "<script>alert('El registro no fue actualizado. Consulte con el administrador')</script>"	;
		}
	}
}
?>


