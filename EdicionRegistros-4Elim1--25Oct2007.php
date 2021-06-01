<?
	session_start();
	session_register("Sunidad");
	session_register("Sfecha");
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
<script language="JavaScript" src="ts_picker.js"></script>
</head>
<body>
<div id="Image20783687" style="position:absolute; left:19px; top:9px; width:154px; height:43px; z-index:5">
	<img src="picsI/Image20783687.gif" width="154" height="43" border="0" name="Image_Image20783687"></div>
	<div id="Layer3" style="position:absolute; left:-39px; top:60px; width:687px; height:22px; z-index:3">
	<img src="picsI/GreenRoundedImage3_0.gif" width="687" height="22" border="0" name="Image_Layer3"></div>
	<div id="Layer2" style="position:absolute; left:648px; top:60px; width:81px; height:36px; z-index:2">
	<img src="picsI/GreenRoundedImage2_0.gif" width="81" height="36" border="0" name="Image_Layer2"></div>
	<div id="Layer12" style="position:absolute; left:404px; top:-2px; width:295px; height:62px; z-index:1">
	<img src="picsI/GreenRoundedImage12_0.gif" width="295" height="62" border="0" name="Image_Layer12"></div>
<br>
<br>	
<br>
<br>

</body>
</html>
<font color="#0033FF" size="4" face="Arial"><b>ACTUALIZACIÓN DE REGISTROS</b></font>

<?

	include "validaUsrBd.php";
	
	$sql="DELETE FROM Horas WHERE id_proyecto = '$idPry' and id_actividad = '$idAct' and unidad = '$laUnidad' AND
	fecha = '$fecha' and localizacion = '$idLoc' and cargo = '$idCar' and clase_tiempo = '$idCtp' ";
	
	

	//echo "<script>rpt = confirm('Confirme si desea eliminar el registro !');</script>";
	//$rpte = print "<script>document.write(rpt)</script>";
	 //echo "EL VALOR DE $rpte;";
	 //if(eval($rpte)){
		if(mssql_query($sql)){
			echo "<script>alert('Registro eliminado: Click en el botón consultar para verificar');</script>";
			echo "<script>history.back()-1;</script>";
		}
	 //}else{
	 	//echo "<script>alert('Se canceló la eliminación del registro');</script>";
	 	//echo "<script>history.back()-1;</script>";
	 //}

?>



<div id="Layer26" style="position:absolute; left:29px; top:450px;  height:41px; z-index:2">
<hr></div>
<div id="Layer21" style="position:absolute; left:29px; top:480px; width:154px; height:41px; z-index:2">
<A href="hdetiempo.php">Hoja de tiempo</A></div>
<div id="Layer20" style="position:absolute; left:160px; top:480px; width:154px; height:41px; z-index:1">
<A href="frm-CambiarPasswd.php">Cambie Password</A></div>
<div id="Layer23" style="position:absolute; left:310px; top:480px; width:154px; height:41px; z-index:4">
<A href="frm-GrabaTiempo.php">Registrar Tiempo</A></div>
<div id="Layer22" style="position:absolute; left:470px; top:480px; width:154px; height:41px; z-index:3">
<A href="Inicio.php">Salir del Sistema</A></div>
<div id="Layer25" style="position:absolute; left:625px; top:480px; width:154px; height:41px; z-index:1">
<A href="reportes.php">Reportes</A></div>
</div>
