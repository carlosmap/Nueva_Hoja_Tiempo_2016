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
<title>Eliminar registros</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>
<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<? include("bannerArriba.php") ; ?><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TxtNota2">Eliminación de registros</td>
  </tr>
</table>

<br>
<br>	
<br>
<br>

</body>
</html>

<?

	include "validaUsrBd.php";

	$vSql="Select * from ViaticosProyecto ";
	$vSql=$vSql." where unidad = " . $laUnidad;
	$vSql=$vSql." and id_proyecto = " . $idPry ;
	$vSql=$vSql." and '".$fecha."' BETWEEN FechaIni AND FechaFin ";
	$vCursor=mssql_query($vSql);
	if ($vReg=mssql_fetch_array($vCursor)) {
		echo "<script>alert('No se puede eliminar el tiempo porque hay viáticos asociados a este proyecto. Elimine los viáticos e inténtelo de nuevo')</script>";
		echo "<script>history.back()-1;</script>";
		exit;
	}


	

//and '2007-08-20' BETWEEN FechaIni AND FechaFin
	
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
