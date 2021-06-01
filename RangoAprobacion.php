<?php
	session_start();
	include "funciones.php";
	include "validaUsrBd.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RAngo de aprobación</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script>
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function Atras(){
	location.href='reportes.php';
}
var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes,toolbar=yes');
	if (window.focus) {newwindow.focus()}
}
//funcion para validar la fecha
function esDigito(sChr){
	var sCod = sChr.charCodeAt(0);
	return ((sCod > 47) && (sCod < 58));
}
function valSep(oTxt){
	var bOk = false;
	bOk = bOk || ((oTxt.value.charAt(4) == "-") && (oTxt.value.charAt(7) == "-"));
	bOk = bOk || ((oTxt.value.charAt(4) == "/") && (oTxt.value.charAt(7) == "/"));
	return bOk;
}
function finMes(oTxt){
	var nMes = parseInt(oTxt.value.substr(5, 2), 10);
	var nRes = 0;
	switch (nMes){
		case 1: nRes = 31; break;
		case 2: nRes = 29; break;
		case 3: nRes = 31; break;
		case 4: nRes = 30; break;
		case 5: nRes = 31; break;
		case 6: nRes = 30; break;
		case 7: nRes = 31; break;
		case 8: nRes = 31; break;
		case 9: nRes = 30; break;
		case 10: nRes = 31; break;
		case 11: nRes = 30; break;
		case 12: nRes = 31; break;
	}
	return nRes;
}
function valDia(oTxt){
	var bOk = false;
	var nDia = parseInt(oTxt.value.substr(8, 2), 10);
	bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));
	return bOk;
}
function valMes(oTxt){
	var bOk = false;
	var nMes = parseInt(oTxt.value.substr(5, 2), 10);
	bOk = bOk || ((nMes >= 1) && (nMes <= 12));
	return bOk;
}
function valAno(oTxt){
	var bOk = true;
	//var nAno = oTxt.value.substr(6);
	var nAno = oTxt.value.substr(0,4);
	bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));
	if (bOk){
		for (var i = 0; i < nAno.length; i++){
			bOk = bOk && esDigito(nAno.charAt(i));
		}
	}
	return bOk;
}
function valFecha(oTxt){
	var bOk = true;
	if (oTxt.value != ""){
		bOk = bOk && (valAno(oTxt));
		bOk = bOk && (valMes(oTxt));
		bOk = bOk && (valDia(oTxt));
		bOk = bOk && (valSep(oTxt));

		if (!bOk){
			alert("Fecha inválida");
			oTxt.value = "";
			oTxt.focus();
		}
	}
}
//fin de funcion para validar la fecha
</script>

</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> RANGO DE APROBACIÓN </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table class='TxtTabla' width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
  </tr>
   <td class="TituloUsuario">Defina el periodo de aprobación</td>
</table>

<div style="position:absolute; left:6px; top:133px;">


<?php
if($fechaInicial == ""){
	$fechaActual = explode("/",date("Y/m/d"));
	$mktActual = mktime(0,0,0,$fechaActual[1],$fechaActual[2],$fechaActual[0]);
	$mktSemana = $mktActual - 604800;
	$fSemana = date("Y/m/d",$mktSemana);
}else{
	$fSemana = $fechaInicial;
}

if($fechaFinal==""){
	$fechaFinal=date("Y/m/d");
}
?>

<form name=reporte action="RangoAprobacion.php" method="post">
	<table class="TxtTabla">
		<tr><td>Seleccione la fecha de inicio y final del periódo de aprobación</td></tr>
	</table>
	<table class="TxtTabla">
		<tr><td> </td><tr>
		<tr><td> </td><tr>
		<tr><td>Fecha Inicial (aaaa/mm/dd)</td><td><input type=text name=fechaInicial onBlur="valFecha(this);" value=<?echo $fSemana;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.reporte.fechaInicial);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>
		</td></tr>

		<tr><td>Fecha Final (aaaa/mm/dd)</td><td><input type=text name=fechaFinal onBlur="valFecha(this);" value=<?echo $fechaFinal;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.reporte.fechaFinal);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-130 id="gToday:normal1:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute; left:-500px; top:0px;">
		</iframe>
		</td></tr>
		<tr><td> </td><td> </td></tr>
	</table>
	
<table>
	<!--<tr><td><input class='Boton' type=submit name=valor value=Continuar></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','rpte_dir_proyecto_mnu.php');return document.MM_returnValue" value="  Atras   "></a></td>-->
	<tr><td><input class='Boton' type=submit name=valor value=Continuar></td><td></td>
	<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a>  </td></tr>
</table>
</form>

<?php
if($valor=="Continuar"){
	if($fechaInicial==""){
		echo "<script>alert('La fecha inicial está en blanco');</script>";
		exit();
	}elseif($fechaFinal==""){
		echo "<script>alert('La fecha final está en blanco');</script>";
		exit();
	}
	$fiAprobacion = $fechaInicial;
	$ffAprobacion = $fechaFinal;

	echo "<h4>Rango de fechas seleccionado desde $fiAprobacion hasta $ffAprobacion</h4>";

	$mesI = explode("/" , $fiAprobacion);
	$mesF = explode("/" , $ffAprobacion);

	if($mesI[0] == $mesF[0]){
		if($dirDep <> "" or $pac == 1){ //es director de departamento
			$sql="SELECT Usuarios.unidad, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido
			FROM Usuarios INNER JOIN Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento
			WHERE (Departamentos.id_departamento = $dirDep and usuarios.retirado is null) order by apellido";

		}elseif($dirDiv <> ""){//es  director de division
			$sql="SELECT Usuarios.unidad, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido
			FROM Usuarios INNER JOIN Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento
			WHERE (Departamentos.id_division = $dirDiv and usuarios.retirado is null) order by apellido";

		}elseif($dirProy <> ""){//es director de proyecto
				echo "<script>location.href='dir_proyecto_aprobaciones.php'</script>";
		}
		echo "<table class='TxtTabla'><tr><td>Hojas de tiempo para su aprobación</td></tr>
			<tr><td bgcolor=#BBD6F7 width='670'>Nota: Si en el rango de fechas que usted está digitando existen horas ya aprobadas, estas se visualizarán pero el sistema no las tendrá en cuenta para una nueva aprobación.
			</td></tr>
			<tr><td> </td></tr></table>
			<table border=1 class='TxtTabla'><tr bgcolor=#CCCCCC><td><strong>APELLIDOS</strong></td><TD><strong>NOMBRES</strong></TD><TD><strong>MÁS DETALLES</strong></TD></tr>";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$unidad = strtoupper($reg[unidad]);
			$nombre = strtoupper($reg[nombre]);
			$apellido = strtoupper($reg[apellido]);
			//echo "<tr bgcolor=#FFFFCC><td>$apellido</td><td>$nombre</td><td><A href=javascript:muestraventana('hdetiempo-aprobDivision.php?und=$unidad')>H. de Tiempo</a></td></tr>";
			echo "<tr bgcolor=#FFFFCC><td>$apellido</td><td>$nombre</td><td><A href=javascript:muestraventana('hdetiempo.php?unidadvar=$unidad')>H. de Tiempo</a></td></tr>";
		}
	}else{
		echo "<script>alert('No esta permitido aprobar periodos de tiempo que involucren varios meses');</script>";
	}
}
?>
</div>
</body>
</html>
