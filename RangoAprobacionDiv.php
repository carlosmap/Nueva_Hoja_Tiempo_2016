<?php
session_start();
session_register('fiAprobacionDiv');
session_register('ffAprobacionDiv');

include "funciones.php";
include "validaUsrBd.php";

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript" src="ts_picker.js"></script>
<title>Reportes</title>

<script>

function regresa(){
	location.href='rpte_director_division.php';
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
<center><h2>APROBACIÓN HOJAS DE TIEMPO</h2></center><BR>

<p>Seleccione la fecha de inicio y final del periódo de aprobación</p>
<form name=reporte action="RangoAprobacionDiv.php" method="post">
	<table>
		<tr><td>Fecha Inicial (aaaa/mm/dd)</td><td><input type=text name=fechaInicial onblur="valFecha(this);" value=<?echo $fechaInicial;?>>
		<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.reporte.fechaInicial);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" 
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>
		</td></tr>
		<tr><td>Fecha Final (aaaa/mm/dd)</td><td><input type=text name=fechaFinal onblur="valFecha(this);" value=<?echo $fechaFinal;?>>
		<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.reporte.fechaFinal);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-130 id="gToday:normal1:agenda.js" 
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute; left:-500px; top:0px;">
		</iframe>
		</td></tr>
		<tr><td> </td><td> </td></tr>
		<tr><td><input type=submit name=valor value=generar></td><td><input type=button name=pagppal value=Regresar onclick='regresa()';></td</tr>
	</table>
</form>

<?php
if($valor=="generar"){
	if($fechaInicial==""){
		echo "<script>alert('La fecha inicial está en blanco');</script>";
		exit();
	}elseif($fechaFinal==""){
		echo "<script>alert('La fecha final está en blanco');</script>";
		exit();
	}
	$fiAprobacionDiv = $fechaInicial;
	$ffAprobacionDiv = $fechaFinal;
	$mesI = explode("/" , $fiAprobacionDiv);
	$mesF = explode("/" , $ffAprobacionDiv);
	
	if($mesI[0] == $mesF[0]){
		echo "<table><tr><td>Hojas de tiempo para su aprobación</td></tr>
		<tr><td> </td></tr></table>
		<table border=1><tr bgcolor=#CCCCCC><td><strong>NOMBRE</strong></td><TD><strong>APELLIDO</strong></TD><TD><strong>MÁS DETALLES</strong></TD></tr>";
		$sql="SELECT Usuarios.unidad, Usuarios.nombre AS nombre, Usuarios.apellidos AS apellido
		FROM Usuarios INNER JOIN Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento
		INNER JOIN Divisiones ON Departamentos.id_division = Divisiones.id_division
		WHERE (Departamentos.id_division = $dirDiv and usuarios.retirado is null) order by nombre";

		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$unidad = strtoupper($reg[unidad]);
			$nombre = strtoupper($reg[nombre]);
			$apellido = strtoupper($reg[apellido]);
			echo "<tr bgcolor=#FFFFCC><td>$nombre</td><td>$apellido</td><td><A href=javascript:muestraventana('hdetiempo-aprobDivision.php?und=$unidad')>H. de Tiempo</a></td></tr>";
		}
	}else{
		echo "<script>alert('No esta permitido aprobar periodos de tiempo que involucren varios meses');</script>";
	}
}
?>
</body>
</html>