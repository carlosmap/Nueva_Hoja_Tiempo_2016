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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Eliminación de un registro</title>
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
	<div align="center">ELIMINACI&Oacute;N DE UN REGISTRO </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
     <td class="TituloUsuario">Eliminación de registros de la base de datos </td>
</table>

<div style="position:absolute; left:2px; top:134px;">

<form name="DatosEntrada" action="" method="post">
	<table class="TxtTabla">
	<tr><td>Seleccione la fecha:</td><td><input type="text" name="timestamp" value=<?echo $timestamp;?>>
	<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.DatosEntrada.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
				<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
				</iframe>
	</td></tr>
	</table>
	<table>
		<tr><td> </td></tr>
	<tr><td><input type="submit" class=Boton name="ConsultarDatos" value="Consultar"></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','EdicionRegistros-mnu.php');return document.MM_returnValue" value="  Atras   "></a></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  P&aacute;gina principal Hoja tiempo   ">
	</a></td></tr>
	
	</table>
</form>
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
	
	
	//impide que se borren registros de una semana atras

	//$mkFechaActual = mktime(24,00,00,03,22,2006);
	$fechAct = explode("-",date("n-d-Y"));

	$mktFechAct = mktime(0,0,0,$fechAct[0],$fechAct[1],$fechAct[2]);

	$fechDig = explode("/",$timestamp);


	$mktFechDig = mktime(0,0,0,$fechDig[0],$fechDig[1],$fechDig[2] );

	$difMkt =$mktFechAct-$mktFechDig;


//	if ($difMkt <= 5529600){
	if ($difMkt <= 8035200){

		
//		$sql="SELECT * FROM Horas WHERE (unidad = '$laUnidad') AND (fecha = '$timestamp')";

		$sql="select h.*, p.nombre, a.nombre nomActividad  ";
		$sql=$sql." from horas h, proyectos p, actividades a ";
		$sql=$sql." where h.id_proyecto = p.id_proyecto ";
		$sql=$sql." and h.id_proyecto = a.id_proyecto";
		$sql=$sql." and h.id_actividad = a.id_actividad ";
		$sql=$sql." and h.unidad = " . $laUnidad ;
		$sql=$sql." and h.fecha = '".$timestamp."'";

		$rpta=mssql_query($sql);

		$NumReg=mssql_num_rows($rpta);

		if($NumReg<=0){
			echo "<script>alert('No hay registros')</script>"	;
		}elseif($NumReg>0){
			
			//Despliega lo encontrado
			$i=1;
			echo "<table CLASS='TxtTabla'>";
			echo "<tr><td>En la fecha $timestamp se encontraron $NumReg registros. Seleccione el que desea ELIMINAR:</td></tr>";
			echo "<tr><td> </td></tr>";
			echo "<tr><td> </td></tr>";
			while($reg = mssql_fetch_array($rpta)){
				$idPry = $reg[id_proyecto];
				$idAct = $reg[id_actividad];
				$idLoc = $reg[localizacion];
				$idCar = $reg[cargo];
				$idCtp = $reg[clase_tiempo];
				$idRst = $reg[resumen_trabajo];
				$pProy = $reg[nombre];
				$pAct = $reg[nomActividad];
				$pHR = $reg[horas_registradas];
				echo "<tr><td>$i) $pProy - $pAct - $pHR -  <a href='EdicionRegistros-4Elim1.php?idPry=$idPry&&idAct=$idAct&&idLoc=$idLoc&&idCar=$idCar&&idCtp=$idCtp&&fecha=$timestamp'>$idRst ($timestamp)</a></td></tr>";
				$i++;
			}
			echo "</table>";
		}
	}else{
//		echo "<script>alert('No esta permitido eliminar un registro con más de un mes de antigüedad')</script>";
		echo "<script>alert('No esta permitido eliminar un registro con más de tres mes de antigüedad')</script>";
	}
}
?>
</div>
</body>
</html>
