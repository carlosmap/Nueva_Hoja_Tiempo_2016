<?php
	session_start();
	//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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

function desactivar(obj){
	document.autoriza.autorizar.checked=false
	document.autoriza.desautorizar.checked=false
	obj.checked=true
}
var newwindow;
function vermuestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes, toolbar=yes');
	if (window.focus) {newwindow.focus()}
}

function validaBlancos(){
	if(document.autoriza.estado.checked==false){
		if(document.autoriza.autorizado.value == "Seleccione un nombre"){
			alert('Usuario a autorizar no seleccionado');
			return false
		}
		if(document.autoriza.usrRevisado.value == "Seleccione un nombre"){
			alert('El segundo usuario no fue seleccionado');
			return false
		}
		return true
	}
	return true
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> REVISIÓN HOJAS DE TIEMPO </div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
 	 <tr><td> </td></tr>
  	 <tr><td> </td></tr>
     <td class="TituloUsuario">Seleccione la fecha de inicio y final del periódo de aprobación</td>
</table>

<div style="position:absolute; left:3px; top:127px;">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TxtTabla">
  <tr>
    <td>Revisión de hojas de tiempo que le fueron delegadas</td>
  </tr>
  <tr>
    <td>(Revisión y aprobación de la hoja de tiempo a otros funcionarios)</td>
  </tr>
  <tr>
    <td>Click sobre el nombre del funcionario para proceder con la revisión</td>
  </tr>
</table>

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

<form name=revisar action="RevisarHojasdeTiempo.php" method="post">
	<table class="TxtTabla">
		<tr><td>Fecha Inicial (aaaa/mm/dd)</td><td><input type=text name=fechaInicial onBlur="valFecha(this);" value=<?echo $fSemana;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.revisar.fechaInicial);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>
		</td></tr>

		<tr><td>Fecha Final (aaaa/mm/dd)</td><td><input type=text name=fechaFinal onBlur="valFecha(this);" value=<?echo $fechaFinal;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.revisar.fechaFinal);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-130 id="gToday:normal1:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute; left:-500px; top:0px;">
		</iframe>
		</td></tr>
	</table>
		<table>
		<tr><td> </td><td> </td></tr>
		<tr><td><input class='Boton' type=submit name=valor value=generar></td><td>
		  <td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a></td></td></tr>
	</table>
</form>
<hr>

<?php
if ($valor == "generar"){
	if($fechaInicial==""){
		echo "<script>alert('La fecha inicial está en blanco');</script>";
		exit();
	}elseif($fechaFinal==""){
		echo "<script>alert('La fecha final está en blanco');</script>";
		exit();
	}
	$fiAprobacion = $fechaInicial;
	$ffAprobacion = $fechaFinal;

	//Identifica el perfil del usuario que lo autorizó
	$sql = "SELECT * FROM DelegacionFunciones WHERE (autorizado = $laUnidad)";
	echo "<table border=1 class='TxtTabla'>";
	echo "<tr><td><b>FUNCIONARIO QUIEN AUTORIZÓ LA REVISIÓN</b></td><td>
	<b>FUNCIONARIOS AUTORIZADOS PARA SU REVISIÓN</b></td><td>
	<b>PERFIL AUTORIZADO</b></td></tr>";
	$ap = mssql_query($sql);
	while($reg = mssql_fetch_array($ap)){
		$quienAutoriza = $reg[autoriza];
		$unidadPorRevisar = $reg[undARevisar];
		$proARevisar = trim($reg[proyectoARevisar]);
		$perfildeQuienAutoriza = trim($reg[perfilAEmplear]);
		//Consulta el nombre de quien autoriza
		$sql2 = "select * from usuarios where unidad=$quienAutoriza";
		$ap2 = mssql_query($sql2);
		$reg2 = mssql_fetch_array($ap2);
		$nombreUsr1 = $reg2[nombre];
		$apelUsr1 = $reg2[apellidos];

		//consulta el nombre de las unidades por revisar
		$sql3 = "select * from usuarios where unidad=$unidadPorRevisar";
		$ap3 = mssql_query($sql3);
		$reg3 = mssql_fetch_array($ap3);
		$nombreUsr3 = $reg3[nombre];
		$apelUsr3 = $reg3[apellidos];
		$nau3 = ucwords($apelUsr3)." ".ucwords($nombreUsr3);
		$nau2 = ucwords($apelUsr1)." ".ucwords($nombreUsr1);
		echo "<tr><td>$nau2</td><td>
		<a href='javascript:vermuestraventana(\"hdetiempo-aprobDivision.php?und=$unidadPorRevisar&&codProyecto=$proARevisar
		&&autorizadoPor=$quienAutoriza&&perfil=$perfildeQuienAutoriza\")'>$nau3</a></td><TD>$perfildeQuienAutoriza</TD></tr>";

	}
	echo "</table>";
}

?>
</form>
</div>
</body>
</html>
