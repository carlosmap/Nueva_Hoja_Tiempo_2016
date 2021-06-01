<?php
$verUnidadDe = $zUnidad;

//NUEVA HOJA DE TIEMPO
	session_start();
	$nombrecomputador="sqlservidor";
	include "funciones.php";
	include "validaUsrBd.php";
	global $suma;
	define('COLORNORMAL','#FFFFCC');//Pinta donde se escriben las horas
	define('COLORFINSEMANA','#00CC00'); //'#ff0000');
	define('COLORFINMES','#FF0066');
	define('COLORFESTIVO','#FFA500');
	if($unidadvar != ""){
		$verUnidadDe = $unidadvar;
	}
	function cambiaDia($dd){
	switch ($dd) {
		case "01":
			$dd=1;
			break;
		case "02":
			$dd=2;
			break;
		case "03":
			$dd=3;
			break;
		case "04":
			$dd=4;
			break;
		case "05":
			$dd=5;
			break;
		case "06":
			$dd=6;
			break;
		case "07":
			$dd=7;
			break;
		case "08":
			$dd=8;
			break;
		case "09":
			$dd=9;
			break;
		}
		return $dd;
	}


function consultaADP($lg,$fch, $crg){

	//include "validaUsrBd.php";
	$sql = "SELECT adp FROM Adp
			WHERE (unidad = $lg) AND (fecha BETWEEN $fch) AND (cargo = '$crg')";

	$ap3 = mssql_query($sql);
	if(mssql_num_rows($ap3)>0){
		$reg1 = mssql_fetch_array($ap3);
		$adpUsr = $reg1[adp];
		return $adpUsr;
	}else{
		return -1;
	}
}

function consulta($arr, $sql){
	$suma=0;
	//include "validaUsrBd.php";
	$ap3 = mssql_query($sql);
	//if(mssql_num_rows($ap3)>0){
		while($reg1 = mssql_fetch_array($ap3)){
			$fecha = $reg1[fecha];
			$fch = explode(" ",$fecha);
			$dia =$fch[1];
			$dia=cambiaDia($dia);
			$arr[$dia+1] = $reg1[horas_registradas];
			$suma = $suma+$reg1[horas_registradas];
		}
		$arr[33] = $suma;
	return $arr;
	//}

}

function sumarArreglos($arr2){
	$res=0;
	for($i=2;$i<=32;$i++){
		$res = $res + $arr2[$i];
	}
	return $res;
}
	/*COMENTARIOS*/
	/*El algoritmo funciona de la siguiente manera:
	Las consultas a SQL Server siempre se regresan ordenadas para garantizar que un codigo de proyecto
	no se vuelva a encontrar mas adelante, asi se logra que se imprima un codigo en una sola linea, no
	importa que se encuentren dos registros del mismo en diferentes fechas; se compara que no cambien
	tipo de tiempo y codigo, cuando cambian se imprime el renglon. De igual forma funciona la impresion de
	los dias no laborados, se ordenan los registros devueltos por el tipo de tiempo no laborado y tan pronto cambie
	este tipo de tiempo, se imprime el renglon y se totaliza el arreglo, el nombre del tipo de tiempo se almacena
	antes de cambiar de registro.

	Permite visualizar la hoja de otras fechas  mediante decisión documentada mas adelante

	Gonzalo
	*/

	//El login es la unidad del usuario
	//if (isset($unidadvar))
	//	$verUnidadDe=$unidadvar;
	//else
		//$verUnidadDe=$Launidad;

	//Verifica que el usuario existe en la base de datos contrastandolo con la unidad
	//include "validaUsrBd.php";
	/*$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria,
		Usuarios.
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$verUnidadDe')";*/
	
	$sql = "SELECT     Usuarios.nombre AS nombre, Usuarios.apellidos AS apellidos, Departamentos.nombre 
			AS departamento, Dependencias.nombre AS dependencia, 
            Usuarios.unidad AS unidad, Categorias.nombre AS categoria, Divisiones.nombre AS division
			FROM Usuarios INNER JOIN
    	    Departamentos ON Usuarios.id_departamento = Departamentos.id_departamento INNER JOIN
		    Divisiones ON Departamentos.id_division = Divisiones.id_division INNER JOIN
		    Dependencias ON Divisiones.id_dependencia = Dependencias.id_dependencia INNER JOIN
		    Categorias ON Usuarios.id_categoria = Categorias.id_categoria
			WHERE     (Usuarios.unidad = '$verUnidadDe')";
			
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categoria=$fil[categoria];
		$nomb=$fil[nombre];
		$apel=$fil[apellidos];
		$depen = $fil[dependencia];
		$divis = $fil[division];
		$depar = $fil[departamento];
		
	} else {
		alert("Usuario no registrado");
		exit();
	}

?>

<?
//5Jul2007
//Para incrustar las horas laborales de oficina, campo y categoria 42 estipuladas para la vigencia seleccionada 

//Consulta ara traer las hora laborales en el mes seleccionado
$qSql="Select * from horasydiaslaborales ";
//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
if (trim($Flmes) == "") {
	$qSql=$qSql." where vigencia = year(getdate()) ";
	$qSql=$qSql." and mes = month(getdate()) ";
}
// si no, trae la información correspondiente al mes y año seleccionados en la lista
else {
	$qSql=$qSql." where vigencia = " . $Flano ;
	$qSql=$qSql." and mes = " .$Flmes;
}
$qCursor=mssql_query($qSql);
if ($qReg=mssql_fetch_array($qCursor)) {
	$horasOficina=$qReg[hOficina];
	$horasCampo=$qReg[hCampo];
	$horasCat42=$qReg[hCat42];
}


//echo "<br>" . $qSql;


//tabla que hará parte del encabezado
$vHLab="<table width='90%'  border='1' cellpadding='0' cellspacing='1' bordercolor='#FFFFFF'> ";
$vHLab=$vHLab."  <tr align='center' > ";
$vHLab=$vHLab."    <td width='25%'><font face='Arial, Helvetica, sans-serif' size='1'>Horas MES</font> </td> ";
$vHLab=$vHLab."    <td width='25%'><font face='Arial, Helvetica, sans-serif' size='1'>Oficina: $horasOficina</font></td> ";
$vHLab=$vHLab."    <td width='25%'><font face='Arial, Helvetica, sans-serif' size='1'>Campo: $horasCampo</font></td> ";
$vHLab=$vHLab."    <td width='25%'><font face='Arial, Helvetica, sans-serif' size='1'>Categoria 42: $horasCat42</font></td> ";
$vHLab=$vHLab."  </tr> ";
$vHLab=$vHLab."</table> ";

//6Julio2007
//Consulta para verificar si la Hoja ya está aprobada o no por el jefe seleccionado
$muestraBoton = 0;
$firmaJefeAprueba = "";
$ObsJefeAprueba = "";
$muestraContratos = 0;
$firmaContratos = "";
$ObsContratos = "";

$qSql2="Select vigencia, mes, unidad, unidadJefe, validaJefe, comentaJefe, validaContratos, unidadContratos, comentaContratos, fechaEnvio, fechaAprueba, fechaContratos ";
$qSql2=$qSql2." from AutorizacionesHT ";
//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
if (trim($Flmes) == "") {
	$qSql2=$qSql2." where vigencia = year(getdate()) ";
	$qSql2=$qSql2." and mes = month(getdate()) ";
}
else {
$qSql2=$qSql2." where vigencia = " . $Flano ;
$qSql2=$qSql2." and mes =" .$Flmes;
}
$qSql2=$qSql2." and unidad =" . $verUnidadDe;
$qCursor2 = mssql_query($qSql2);
$muestraBoton = 0;
if ($qReg2=mssql_fetch_array($qCursor2)) {
	$muestraBoton = $qReg2[validaJefe];
	$firmaJefeAprueba = $qReg2[unidadJefe];
	$ObsJefeAprueba = $qReg2[comentaJefe];
	$muestraContratos = $qReg2[validaContratos];
	$firmaContratos = $qReg2[unidadContratos];
	$ObsContratos = $qReg2[comentaContratos];
	
	$kfechaEnvio = $qReg2[fechaEnvio];
	$kfechaAprueba = $qReg2[fechaAprueba];
	$kfechaContratos = $qReg2[fechaContratos];
	
	if (trim($kfechaEnvio) != "") {
	 	$kfechaEnvio = date("M d Y ", strtotime($kfechaEnvio)) ;
	}
	if (trim($kfechaAprueba) != "") {
	 	$kfechaAprueba = date("M d Y ", strtotime($kfechaAprueba)) ;
	}
	if (trim($kfechaContratos) != "") {
	 	$kfechaContratos = date("M d Y ", strtotime($kfechaContratos)) ;
	}
	
}

$cantidadFilas = mssql_num_rows($qCursor2);
//$muestraBoton=1 si jefe ya autorizó, entonces armar el mensaje 
if ($cantidadFilas == 0) {
	$mensajeHT = "Hoja de tiempo sin enviar a revisión del Jefe";
}

if ($cantidadFilas > 0) {
	if(trim($firmaContratos) != "") {
		$mensajeContratos = $ObsContratos ;
	}
	else {
		$mensajeContratos = "Hoja de tiempo sin revisar en Contratos";
	}


	$mensajeHT = $ObsJefeAprueba ;
	if ($muestraBoton == 0) {
		//$mensajeHT = "Hoja de tiempo en revisión del jefe sin aprobación ";
		$cualImagen = "<img src='img/images/No.gif' alt='$mensajeHT' border='0'>";
	}
	else {
		//$mensajeHT = "Hoja de tiempo Aprobada";
		$cualImagen = "<img src='img/images/Si.gif' alt='$mensajeHT' border='0'>";
	}
	if ($muestraContratos == 0) {
		$cualImagenCont = "<img src='img/images/No.gif' alt='$mensajeContratos' border='0'>";
	}
	else {
		//$mensajeHT = "Hoja de tiempo Aprobada";
		$cualImagenCont = "<img src='img/images/Si.gif' alt='$mensajeContratos' border='0'>";
	}

}

//Armar la tabla que visualiza el mensaje en el encabezado
$vHLab2="<table width='100%'  border='0' cellspacing='0' cellpadding='0'> ";
$vHLab2=$vHLab2."  <tr> ";
$vHLab2=$vHLab2."    <td align='center'><font face='Verdana, Arial, Helvetica, sans-serif' size='2'><B>$mensajeHT</B></font></td> ";
$vHLab2=$vHLab2."  </tr> ";
$vHLab2=$vHLab2."</table> ";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<STYLE>
 H1.SaltoDePagina
 {
     PAGE-BREAK-BEFORE: auto
 }
</STYLE>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script>
<!--
function actualizarhiddenfin() {
	var fe=document.reporte.Flmes.value+'-1-'+document.reporte.Flano.value;
	document.reporte.fechafinal.value=fe;
}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<style type="text/css">
<!--
#Layer1 {
	position:absolute;
	width:226px;
	height:115px;
	z-index:1;
	left: 13px;
	top: 151px;
}
#Layer2 {
	position:absolute;
	width:200px;
	height:115px;
	z-index:1;
}
#Layer3 {
	position:absolute;
	width:680px;
	height:15px;
	z-index:1;
	left: 469px;
	top: -26px;
}
#Layer4 {
	position:absolute;
	width:991px;
	height:31px;
	z-index:2;
}
-->
</style>
</head>

<body bgcolor="#EAEAEA">

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" />
    &nbsp;<input name="Submit4" type="submit" class="Boton" onclick="MM_callJS('history.back()')" value="Revisi&oacute;n Personal" />
    <? 
	//Para bloquear este botón mientras se determina que va a hacer Rosa Amelia con la Hoja de tiempo
	if ($hoja == "ES PERSONAL") { 
	?>
	<input name="BotonHT" type="submit" class="Boton" id="BotonHT" onclick="MM_openBrWindow('upAutorizaHTcontratos.php?cualUnidad=<? echo $verUnidadDe; ?>&cualMes=<? echo $Flmes; ?>&cualAno=<? echo $Flano; ?>','vUPjef','scrollbars=yes,resizable=yes,width=400,height=300')" value="Tr&aacute;mite Contratos" />
	<? } ?>
</td>
  </tr>
</table>

<div style="position:absolute; left:10px; top:28px; height: 102px;">
<form action="hdetiempo.php" name=reporte method="post">
<?
	/***************llena un arreglo con la cabecera de la hoja de tiempo **************/
	$cabeceras[38];
	$cabeceras[0]="CODIGO";
	$cabeceras[1]="CT";
	$j=2;
	for($i=1;$i<=31;$i++){
		$cabeceras[$j]=$i;
		$j++;
	}
	$cabeceras[33]="TOTAL";
	$cabeceras[34]="VoBo";
	$cabeceras[35]="RESUMEN";

	/***************Inicializa arreglos arreglo con el final de la hoja de tiempo********************/

	//$MiMes=($verhoja=="Consultar"?$Flmes:date("m",time()));
	//$MiAnno=($verhoja=="Consultar"?$Flano:date("Y",time()));
	$MiMes = $Flmes;
	$MiAnno = $Flano;
	
	$numdias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));
	$cadfecha="'$MiMes/1/$MiAnno' and '$MiMes/$numdias/$MiAnno'";

//PBM 	
//11FEB2008
//VERIFICAR QUÉ Clase de tiempo es la persona para poner 1 o 2 en vacaciones
$tcSql="SELECT TipoContrato ";
$tcSql=$tcSql." FROM USUARIOS ";
$tcSql=$tcSql." WHERE (unidad = ".$verUnidadDe. ")";
$tcCursor = mssql_query($tcSql);
if ($tcReg=mssql_fetch_array($tcCursor)) {
	$elTCusu = $tcReg[TipoContrato];
}
if (strtoupper(trim($elTCusu)) == "TC") {
	$CTtcUsu = "1";
}
else {
	$CTtcUsu = "2";
}

	//Consulta las vacaciones
	$vacaciones[38];
	$vacaciones[0]="VACACIONES";
//	$vacaciones[1]="1";
	$vacaciones[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
		$vacaciones[$i]=" ";
	}

	$sql="SELECT * FROM Horas WHERE cargo = 'vac0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$vacaciones = consulta(&$vacaciones, $sql);

	$ADP = consultaADP($verUnidadDe,$cadfecha,'VAC0');
	if($ADP!=-1){
		$vacaciones[35]="ADP-VC/".$ADP;
	}else{
		$vacaciones[35]="ADP-VC/";
	}
	//*******************
	$enfermedad[38];
	$enfermedad[0]="ENFERMEDAD";
	//$enfermedad[1]="1";
	$enfermedad[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
		$enfermedad[$i]=" ";
	}
	$sql="SELECT * FROM Horas WHERE cargo = 'enf0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$enfermedad = consulta(&$enfermedad, $sql);

	$ADP = consultaADP($verUnidadDe,$cadfecha,'ENF0');
	if($ADP!=-1){
		$enfermedad[35]="ADP-INC/".$ADP;
	}else {
		$enfermedad[35]="ADP-INC/";
	}
	//*********************

	$acciddetrabajo[38];
	$acciddetrabajo[0]="ACCID.TRABAJ";
	//$acciddetrabajo[1]="1";
	$acciddetrabajo[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
			$acciddetrabajo[$i]=" ";
	}
	$sql="SELECT * FROM Horas WHERE cargo = 'acc0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$acciddetrabajo = consulta($acciddetrabajo, $sql);

	$ADP = consultaADP($verUnidadDe,$cadfecha,'ACC0');
	if($ADP!=-1){
		$acciddetrabajo[35]="ADP-INC/".$ADP;
	}else {
		$acciddetrabajo[35]="ADP-INC/";
	}

	$permisospacto[38];
	$permisospacto[0]="PERM PACTO";
	//$permisospacto[1]="1";
	$permisospacto[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
		$permisospacto[$i]=" ";
	}

	$sql="SELECT * FROM Horas WHERE cargo = 'per0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$permisospacto = consulta($permisospacto, $sql);

	$ADP = consultaADP($verUnidadDe,$cadfecha,'PER0');
	if($ADP!=-1){
		$permisospacto[35]="ADP-PR/".$ADP;
	}else {
		$permisospacto[35]="ADP-PR/";
	}

	$licencias[38];
	$licencias[0]="LICENCIAS";
	//$licencias[1]="1";
	$licencias[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
			$licencias[$i]=" ";
	}

	$ADP = consultaADP($verUnidadDe,$cadfecha,'LIC0');
	if($ADP!=-1){
		$licencias[35]="ADP-LC/".$ADP;
	}else {
		$licencias[35]="ADP-LC/";
	}


	$sql="SELECT * FROM Horas WHERE cargo = 'lic0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$licencias = consulta($licencias, $sql);

	$sanciones[38];
	$sanciones[0]="SANCIONES";
	//$sanciones[1]="1";
	$sanciones[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
			$sanciones[$i]=" ";
	}

	$ADP = consultaADP($verUnidadDe,$cadfecha,'SAN0');
	if($ADP!=-1){
		$sanciones[35]="ADP-SD/".$ADP;
	}else {
		$sanciones[35]="ADP-SD/";
	}


	$sql="SELECT * FROM Horas WHERE cargo = 'san0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$sanciones = consulta($sanciones, $sql);

	$ausencias[38];
	$ausencias[0]="AUSENCIAS";
	//$ausencias[1]="1";
	$ausencias[1]=$CTtcUsu;
	for($i=2;$i<=38;$i++){
			$ausencias[$i]=" ";
	}

	$sql="SELECT * FROM Horas WHERE cargo = 'aus0' AND unidad = $verUnidadDe AND fecha between $cadfecha";
	$ausencias = consulta($ausencias, $sql);

	$total[38];
	$total[0]="TOTAL";
	$total[1]=" ";
	for($i=2;$i<=38;$i++){
			$total[$i]="0";
	}

	$primas[37];
	$primas[0]="<center>CODIGO</center>";
	$primas[1]="<center>V</center>";
	$primas[2]="<center><b>DIAS DE VIÁTICOS, PRIMA DE LOCALIZACION, AUXILIO DE TRASLADO O AUXILIO ALIMENTACION</b></center>";
	for($i=3;$i<=37;$i++){
		$primas[$i]=" ";
	}

	/*
	//Se decide si es una fecha digitada o visualiza la hoja por primera vez
	$MiMes=date("m",time());
	$MiAnno=date("Y",time());
	$numdias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));


	//Corresponde al rango de fechas en un mes determinado
	$cadfecha="'$MiMes/1/$MiAnno' and '$MiMes/$numdias/$MiAnno'";
	*/

	/***************decide la fecha que colocara en el encabezado de la hoja*******************************************/
	/***************se refiere a la fecha a la cual corresponde la hoja************************************************/
	$TmpFlmes=nombremes_completo($MiMes);
	$fechasistema="del mes de $TmpFlmes de $MiAnno";
	/******************************************************************************************************************/

	echo "<br>";
	/***************************Dibuja el encabezado de la hoja de tiempo**********************************************/
/*
//Trae la información del encabezado de la tabla personalACTUALIZADO
//unidad, nombre, categoria, dependencia, division, departamento, seccion, sitiocontrato, sitiotrabajo, tipocontrato
$eSql="Select * from personalACTUALIZADO ";
$eSql=$eSql." where unidad = " . $verUnidadDe;
$eCursor=mssql_query($eSql);
if ($eReg=mssql_fetch_array($eCursor)) {
	$eNombreCorto=$eReg[nombre];
	if (trim($eReg[tipocontrato]) == "TC") {
		$eTipoContrato="";
	}
	else {
		$eTipoContrato=$eReg[tipocontrato];
	}
	$eCategoria=$eReg[categoria];
	$eDependencia=$eReg[dependencia];
	$eDivision=$eReg[division];
	$eDepartamento=$eReg[departamento];
	$eSeccion=$eReg[seccion];
	$eSitioC=$eReg[sitiocontrato];
	$eSitioT=$eReg[sitiotrabajo];
	
	//Si hay datos aqui, muestra el encabezado con la información de personalACTUALIZADO
	echo "<table class='TxtTabla' border='1' cellpadding='0' cellspacing='0' bordercolor='#999999' >";
	echo "<tr>";
	echo "<td colspan='6'><img src='pics/Image20783687.gif' width='150' heigth='75'></td>";
	echo "<td colspan='22'><font face=arial size=4><h3><center>HOJA DE TIEMPO<br>$fechasistema<br>$vHLab</center></h3></font></td>";
	echo "<td colspan='14' valign=top align=left><font face=arial size=2><b>".strtoupper($eNombreCorto)."   ". strtoupper($eTipoContrato)."    $laUnidad"."-"."$eCategoria";
	echo "<BR>DEP. ".strtoupper($eDependencia)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"."DIV. ".strtoupper($eDivision)." "."<br>DPT. ".strtoupper($eDepartamento)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SEC. ".strtoupper($eSeccion)."<br>S.C. ".$eSitioC."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S.T. ".$eSitioT."</td></tr>";
}
else {
	//Si no hay datos en personal actualizado muestra la información de usuarios (la tabla que viene mostrándose)
	echo "<table class='TxtTabla' border='1' cellpadding='0' cellspacing='0' bordercolor='#999999' >";
	echo "<tr>";
	echo "<td colspan='6'><img src='pics/Image20783687.gif' width='150' heigth='75'></td>";
	echo "<td colspan='22'><font face=arial size=4><h3><center>HOJA DE TIEMPO<br>$fechasistema<br>$vHLab</center></h3></font></td>";
	echo "<td colspan='14' valign=top align=left><font face=arial size=2><b>".strtoupper($nomb)." ".strtoupper($apel)."    $laUnidad"."-"."$categoria";
	echo "<BR>DEP. ".strtoupper($depen)." "."<br>DIV. ".strtoupper($divis)." "."<br>DPT. ".strtoupper($depar)."</td></tr>";
}
*/

//23Oct2007
//Trae la información del encabezado de la tabla usuarios
//--Trae la información del usuario con nombre del departamento y nombre de la categoria
	$encabSql="Select U.* , D.nombre nomDepartamento, C.nombre nomCategoria, A.nombre nomDivision, B.nombre nomDependencia ";
	$encabSql=$encabSql." from usuarios U, departamentos D, categorias C, divisiones A, dependencias B ";
	$encabSql=$encabSql." where U.id_departamento = D.id_departamento ";
	$encabSql=$encabSql." and U.id_categoria = C.id_categoria ";
	$encabSql=$encabSql." and D.id_division = A.id_division ";
	$encabSql=$encabSql." and A.id_dependencia = B.id_dependencia ";
	$encabSql=$encabSql." and U.unidad =  "  . $verUnidadDe;
	$encabCursor=mssql_query($encabSql);	

	if ($encabReg=mssql_fetch_array($encabCursor)) {
		if (trim($encabReg[NombreCorto]) == "") {
			$eNombreCorto= trim(ucwords($encabReg[apellidos])) . " " . trim(ucwords($encabReg[nombre]));
		}
		else {
			$eNombreCorto=$encabReg[NombreCorto];
		}
		if (trim($encabReg[TipoContrato]) == "TC") {
			$eTipoContrato="";
		}
		else {
			$eTipoContrato=$encabReg[TipoContrato];
		}
		$eCategoria=$encabReg[nomCategoria];
		$eDependencia=$encabReg[nomDependencia];
		if (trim(strtoupper($encabReg[nomDivision])) == 'SD') {
			$eDivision='';
		}
		else {
			$eDivision=$encabReg[nomDivision];
		}
		
		if (trim(strtoupper($encabReg[nomDepartamento])) == 'SD') {
			$eDepartamento='';
		}
		else {
			$eDepartamento=$encabReg[nomDepartamento];
		}
		$eSeccion=$encabReg[Seccion];
		$eSitioC=$encabReg[SitioContrato];
		$eSitioT=$encabReg[SitioTrabajo];
		
		//Si hay datos aqui, muestra el encabezado con la información de personalACTUALIZADO
		echo "<table class='TxtTabla' border='1' cellpadding='0' cellspacing='0' bordercolor='#999999' >";
		echo "<tr>";
		echo "<td colspan='6'><img src='pics/Image20783687.gif' width='150' heigth='75'></td>";
		echo "<td colspan='22'><font face=arial size=4><h3><center>HOJA DE TIEMPO<br>$fechasistema<br>$vHLab</center></h3></font></td>";
		echo "<td colspan='14' valign=top align=left><font face=arial size=2><b>".strtoupper($eNombreCorto)."   ". strtoupper($eTipoContrato)."    $verUnidadDe"."-"."$eCategoria";
		echo "<BR>DEP. ".strtoupper($eDependencia)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"."DIV. ".strtoupper($eDivision)." "."<br>DPT. ".strtoupper($eDepartamento)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SEC. ".strtoupper($eSeccion)."<br>S.C. ".strtoupper($eSitioC)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S.T. ".strtoupper($eSitioT)."</td></tr>";
	}




	/**********Dibuja el arreglo de la cabecera (los dias numerados total, VoBo Proy, resumen trabajo******************/
	echo "<tr bgcolor=#6699ff>";
	for($i=0;$i<=32;$i++){
		echo "<td style='width:20px;text-align:center'>$cabeceras[$i]</td>";
	}
	echo "<td><center>$cabeceras[33]</center></td>";
	echo "<td><center>$cabeceras[34]</center></td>";
	echo "<td colspan='4'>$cabeceras[35]</td>";
	echo "</tr>";

	/********Llena un vector con los dias festivos del mes ubicando un 1 en la posición del vector, cuando esta posición*/
	/**************************************** es festivo ********************************************* ***************/
	for ($i=1;$i<=31;$i++) $festivos[$i]=0;
	$sql="select day(fecha) as fest from festivos where year(fecha)=$MiAnno and month(fecha)=$MiMes order by fest";

	if ($res=mssql_query($sql)) {
		while ($filas=mssql_fetch_array($res)) {
			$i=$filas[fest];
			$festivos[$i]=1;
		}
	}

	/************ La siguiente corresponde a la consulta para extraer lo facvturado x dia******************************/

	$sql="SELECT estadoAprobDivision, comentariosDivision, revisadoPorDivision, estadoAprobProyecto, comentariosProyecto,
		revisadoPorProyecto, RTRIM(localizacion) + '-' + RTRIM(cargo) AS codigoproyecto,
		 horas_registradas, clase_tiempo, resumen_trabajo, day(fecha) as dia, id_actividad, id_proyecto FROM Horas
		WHERE     (fecha BETWEEN $cadfecha) AND (unidad = '$verUnidadDe') AND (cargo <> 'acc0')
		AND (cargo <> 'aus0') AND (cargo <> 'enf0') AND (cargo <> 'lic0') AND (cargo <> 'per0')
         AND (cargo <> 'san0') AND (cargo <> 'vac0')ORDER BY codigoproyecto, clase_tiempo, id_actividad";

	/***********inicializa el arreglo de horas en cada dia**********************************************/
	$j=2;
	for($i=1;$i<=31;$i++){
		$horasdia[$j]=" ";
		$j++;
	}
	/***********************Busca y organiza lo facturado por dia*************************************/
	if($resultado=mssql_query($sql)){
		$filas=mssql_fetch_array($resultado);
		$codproy=$filas[codigoproyecto];
		$id_proy=$filas[id_proyecto];
		$ttiempo=$filas[clase_tiempo];
		$mactividad=$filas[id_actividad];
		$hreg=$filas[horas_registradas];


/*********************************************IMPRIME LAS HORAS FACTURADAS***********************************************/
		$horasAlTiempo10 = 0; //tiempo10
		/*Como el resultado de la consulta ya viene ordenado se llena un arreglo hasta cuando el**
		 ********************tipo de tiempo y el cod cambien luego imprime y toma otro codigo*******/
		while($filas){

			while(($codproy==$filas[codigoproyecto]) and ($ttiempo==$filas[clase_tiempo]) and ($mactividad==$filas[id_actividad])){

					//Selecciona el nombre la actividad o la macroactividad
					//$sqlAct="select macroactividad,nombre from actividades where id_actividad='$mactividad' and id_proyecto='$id_proy'";
					$sqlAct = "SELECT Proyectos.nombre AS proyecto, Actividades.nombre AS nombre, 
								Actividades.macroactividad AS macroactividad FROM Actividades INNER JOIN
		                        Proyectos ON Actividades.id_proyecto = Proyectos.id_proyecto 
								where Actividades.id_actividad=$mactividad and Actividades.id_proyecto=$id_proy";
					$resultadoAct=mssql_query($sqlAct);
					$filasAct=mssql_fetch_array($resultadoAct);
					
					$nProyecto =  ucwords(substr($filasAct[proyecto],0,14));

					if($filasAct[macroactividad] <> NULL){
						$act=$filasAct[macroactividad];
					}else{
						$act=substr($filasAct[nombre],0,6);
					}
					//fin de selecciona

				$dia=$filas[dia];
				//si el dia el devuelto como 01 se quita el 0, pues 01 y 1 es DIFERENTE EN LINUX
				$numind=substr($dia,0,1);
				if($numind==0){
					$indice=substr($dia,1,1);
				}else{
					$indice=$dia;
				}
				
				//Se asignan las horas del dia determinado
				$horasdia[$indice]=$filas[horas_registradas];
				
				//se obtiene el total de horas al tiempo10
				$ttiempo==$filas[clase_tiempo];
				if($ttiempo == 10){ 
					$horasAlTiempo10=$horasAlTiempo10 + $filas[horas_registradas];
				}
	
				$aprobDiv[$indice] = $filas[estadoAprobDivision];
				$comenDiv[$indice] = $filas[comentariosDivision];
				$aprobPro[$indice] = $filas[estadoAprobProyecto];
				$comenPro[$indice] = $filas[comentariosProyecto];
				$aprobDto[$indice] = $filas[estadoAprobDpto];
				$comenDto[$indice] = $filas[comentariosDpto];


				$resumentrabajo[$indice]=$filas[resumen_trabajo];
				//$viaticos[$indice]=$filas[id_viatico];
				$filas=mssql_fetch_array($resultado);
			}

			/************************imprime el renglon con el codigo encontrado ******************************/

			$color=COLORNORMAL;
			echo "<tr bgcolor=$color>";

			echo "<td nowrap><font face='Arial' size=1>$codproy [$act] $nProyecto</font></td>";
			echo "<td><center>$ttiempo</center></td>";

			//sumahoras es el tiempo que se imprime en TIEMPO TOTAL
			$sumahoras=trim(array_sum($horasdia));

			for($i=1;$i<=31;$i++){
				// revision 2002-05-10. Si el dia es sabado o domingo, coloca un color especial
				$nombredia=date("w", mktime(0,0,0,$MiMes,$i,$MiAnno));
				$color='';
				if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
				if ($festivos[$i]==1) $color=COLORFESTIVO;
				if ($i>$numdias) $color=COLORFINMES;
				// revision 2002-05-03 Totaliza horas por dia
				$total[$i+1]+=(int) $horasdia[$i];

				if($horasdia[$i]==0){
					$horasdia[$i]=" ";
					echo "<td bgcolor='$color'>$horasdia[$i]</td>\r";
				} else {
					$resumen=trim($resumentrabajo[$i]);
					$hora = $horasdia[$i];
					if($aprobDiv[$i]=="SI" and $aprobPro[$i]=="SI"){
						$hora = "<font face='arial' size='2' color='green'><b>$horasdia[$i]</b></font>";
					}else{
						$hora = "<font face='arial' size='2' color='#000000'><b>$horasdia[$i]</b></font>";
					}
					echo "<td bgcolor='$color' style='text-align:center;'><a href=\"javascript:alert('$resumen');\">$hora</a></td>\r";
					$horasdia[$i]=" ";
				}
			}
			$color='';
			/***********************imprime el total de horas horizontal y cambia de codigo*******************/
		
			//10Jul2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro y verifica si fue o no aprobado y quien o hizo
			$vBJefe="";
			$vBNombre="";
			$vBComenta="";
			$cualImagenvb = "";
			$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
			$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
			$qSqlvb=$qSqlvb . " and A.id_proyecto = $id_proy ";
			$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
			$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
			$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
			$qCursorvb=mssql_query($qSqlvb);
			if ($qRegvb=mssql_fetch_array($qCursorvb)) {
				$vBJefe=$qRegvb[validaEncargado];
				$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
				$vBComenta=$qRegvb[comentaEncargado];
			}

			if ($vBJefe == 0) {
				$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
			}
			if ($vBJefe == 1) {
				$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
			}

			echo "<td><center>$sumahoras</center></td>";
			echo "<td align=center> $cualImagenvb  </td>";
			echo "<td colspan=3><font face='Verdana, Arial, Helvetica, sans-serif' size='1px'>".  ucwords(strtolower($vBNombre)) ."</font></td>";
			$codproy=$filas[codigoproyecto];
			$ttiempo=$filas[clase_tiempo];
			$mactividad=$filas[id_actividad];
			$id_proy=$filas[id_proyecto];
			$hreg=$filas[horas_registradas];

			// captura el valor de localizacion y compone el codigo del proyecto
			$cod=explode("-",$codproy);
			// Modificacion 2003-09-03 por Manuel Romero
			// si el codigo ya trae el cargo, se lo quita
			$cargos=separa_cargo($cod[1]);
			$codproyaux=$cod[0].$cargos["codigo"].$cod[2];
			echo "</tr>";
		}


		/**********************************FIN IMPRIME HORAS FACTURADAS*********************************************/

		/************************** VACACIONES , ENFERMEDAD, LICENCIAS, ETC*************************/
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td> </td>";
		}
		echo "<td colspan=4> </td>";
		echo "</tr>";

		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$vacaciones[$i]</center></td>";
			
			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de vacaciones y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vFechaVac="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 56 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vFechaVac=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vFechaVac="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($vacaciones[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$vacaciones[$i]</center></td>";
			}
			//Cierra 6Dic2007
		} //for
		echo "<td colspan=4>$vacaciones[35] $vFechaVac</td>";
		echo "</tr>";
		

		//Enfermedad
		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$enfermedad[$i]</center></td>";

			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de enfermedad y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vBFechaEnf="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 60 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad					
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vBFechaEnf=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vBFechaEnf="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($enfermedad[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$enfermedad[$i]</center></td>";
			}
			//Cierra 6Dic2007

		} //for
		echo "<td colspan=4>$enfermedad[35] $vBFechaEnf </td>";
		echo "</tr>";

		//Accidentes de trabajo
		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$acciddetrabajo[$i]</center></td>";

			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de accidentes de trabajo y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vBFechaAccT="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 61 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad					
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vBFechaAccT=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vBFechaAccT="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($acciddetrabajo[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$acciddetrabajo[$i]</center></td>";
			}
			//Cierra 6Dic2007
		} //for
		echo "<td colspan=4>$acciddetrabajo[35] $vBFechaAccT </td>";
		echo "</tr>";

		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$permisospacto[$i]</center></td>";

			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de permisospacto  y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vBFechaPerP="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 62 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad					
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vBFechaPerP=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vBFechaPerP="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($permisospacto[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$permisospacto[$i]</center></td>";
			}
			//Cierra 6Dic2007

		} //for
		echo "<td colspan=4>$permisospacto[35] $vBFechaPerP</td>";
		echo "</tr>";

		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$licencias[$i]</center></td>";
			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de licencias  y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vBFechaLic="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 63 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad					
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vBFechaLic=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vBFechaLic="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($licencias[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$licencias[$i]</center></td>";
			}
			//Cierra 6Dic2007

		} // for
		echo "<td colspan=4>$licencias[35] $vBFechaLic</td>";
		echo "</tr>";

		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$sanciones[$i]</center></td>";

			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de sanciones  y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vBFechaSan="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 64 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad					
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vBFechaSan=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vBFechaSan="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($sanciones[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$sanciones[$i]</center></td>";
			}
			//Cierra 6Dic2007
		} // for
		echo "<td colspan=4>$sanciones[35] $vBFechaSan</td>";
		echo "</tr>";

		echo "<tr>";
		for($i=0;$i<=34;$i++){
//			echo "<td><center>$ausencias[$i]</center></td>";
			//6Dic2007
			//Busca el registro en AprobacionFacHT para el mes y año del registro de ausencias  y verifica si fue o no aprobado y quien o hizo
			if ($i == 34) {
				$vBJefe="";
				$vBNombre="";
				$vBComenta="";
				$cualImagenvb = "";
				$vBFechaAus="";
				$qSqlvb=" Select A.*, U.nombre, U.apellidos from AprobacionFacHT A, Usuarios U ";
				$qSqlvb=$qSqlvb . " where A.unidadEncargado *= U.unidad ";
				$qSqlvb=$qSqlvb . " and A.id_proyecto = 65 ";
				//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
				if (trim($Flmes) == "") {
					$qSqlvb=$qSqlvb . " and A.vigencia = year(getdate()) ";
					$qSqlvb=$qSqlvb . " and A.mes = month(getdate()) ";
				}
				else {
					$qSqlvb=$qSqlvb . " and A.vigencia = $Flano ";
					$qSqlvb=$qSqlvb . " and A.mes = $Flmes ";
				}
				$qSqlvb=$qSqlvb . " and A.unidad = $verUnidadDe " ;
				$qCursorvb=mssql_query($qSqlvb);
				if ($qRegvb=mssql_fetch_array($qCursorvb)) {
					$vBJefe=$qRegvb[validaEncargado];
					$vBNombre= substr($qRegvb[nombre], 0, 1) . " " . substr($qRegvb[apellidos], 0, 10) ;				
					$vBComenta=$qRegvb[comentaEncargado];
					//08Jun2011 - PBM - Agregar la fecha de aprobación de la novedaad					
					if (trim($qRegvb[fechaAprEnc]) != "") {
						$vBFechaAus=" [" . date("M d Y", strtotime($qRegvb[fechaAprEnc])) . "] " ;
					}
					else {
						$vBFechaAus="";
					}
				}
	
				if ($vBJefe == 0) {
					$cualImagenvb = "<img src='img/images/No.gif' alt='$vBComenta' border='0'>";
				}
				if ($vBJefe == 1) {
					$cualImagenvb = "<img src='img/images/Si.gif' alt='$vBComenta' border='0'>";
				}
				if (trim($ausencias[33]) != "0") {
					echo "<td><center>$cualImagenvb</center></td>";
				}
				else {
					echo "<td><center>&nbsp;</center></td>";
				}
			}
			else {
				echo "<td><center>$ausencias[$i]</center></td>";
			}
			//Cierra 6Dic2007
		}
		echo "<td colspan=4>$ausencias[35] $vBFechaAus</td>";
		echo "</tr>";



		//obtiene la suma de todas horas no laboradas
		/*$sumhNoLab=0;
		$sumhNoLab = sumarArreglos($vacaciones);
		$sumhNoL=$sumhNoL+$sumhNoLab;
		$sumhNoLab = sumarArreglos($acciddetrabajo);
		$sumhNoL=$sumhNoL+$sumhNoLab;
		$sumhNoLab = sumarArreglos($permisospacto);
		$sumhNoL=$sumhNoL+$sumhNoLab;
		$sumhNoLab = sumarArreglos($licencias);
		$sumhNoL=$sumhNoL+$sumhNoLab;
		$sumhNoLab = sumarArreglos($sanciones);
		$sumhNoL=$sumhNoL+$sumhNoLab;
		$sumhNoLab = sumarArreglos($ausencias);
		$sumhNoL=$sumhNoL+$sumhNoLab;
		$sumhNoLab = sumarArreglos($enfermedad);
		$sumhNoL=$sumhNoL+$sumhNoLab;
			*/
		//suma lo que tiene el arreglo $total + lo que cada arreglo de vacaciones, permisos, etc, tiene
		for($i=2;$i<=32;$i++){
			$total[$i]=$total[$i]+$vacaciones[$i]+$enfermedad[$i]+$acciddetrabajo[$i]+$permisospacto[$i]+$licencias[$i]+$sanciones[$i]+$ausencias[$i];
		}


			echo "<tr>";
			for($i=0;$i<=35;$i++) {
				switch ($i) {
					case 33:
						$tot = array_sum($total);
						
						//Al total de horas de la hoja de tiempo le quieto la cantidad de horas del tiempo10
						$tot = $tot - $horasAlTiempo10;
						echo "<td nowrap><center>$tot</center></td>";
						break;
					case 34:
						echo "<td>&nbsp;</td>";
						break;
					case 35:
						echo "<td colspan=3>&nbsp;</td>";
						break;
					default:
						// revision 2002-05-10. Si el dia es sabado o domingo, coloca un color especial
						if ($i>1) {
							$nombredia=date("w", mktime(0,0,0,$MiMes,$i-1,$MiAnno));
							$color='';
							if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
							if ($i-1>$numdias) $color=COLORFINMES;
							if ($festivos[$i-1]==1) $color=COLORFESTIVO;
						}

						if ($i>0 && abs($total[$i])<0.001) $total[$i]="&nbsp;";
						//imprime el total vertical de cada uno de los dias del mes
						echo "<td nowrap bgcolor='$color'><center>$total[$i]</center></td>";
						break;
				}
			}
			echo "</tr>";


		/***********************imprime la seccion de prima de localizacion************************/
		//reciente
		echo "<tr>";
		echo "<td>$primas[0]</td>";
		echo "<td>$primas[1]</td>";
		echo "<td colspan='31'>$primas[2]</center></font></td>";
		echo "<td colspan='3' align='center'><b>D I A S</b></td>";
		echo "<td colspan='3' align='center'><b>DESCRIPCIÓN</b></td>";
		echo "</tr>";

			//Nuevo
			//imprime los viáticos
/*			$sql="SELECT rtrim(localizacion)+ '-' +rtrim(cargo) AS codigo, DAY(fechaIni) AS diaIni, DAY(fechaFin) AS diaFin, IDTipoViatico, id_proyecto, IDSitio, viaticoCompleto , id_actividad ";
			$sql1="FROM ViaticosProyecto WHERE  (fechaIni BETWEEN $cadfecha) AND (fechaFin BETWEEN $cadfecha) AND(unidad = '$verUnidadDe') ";
			$sql2 = "order by id_actividad";
*/
			//14Nov2007
			//Para mostrar los viáticos discriminados por actividad y visualizar la macroactividad			
			$sql="SELECT rtrim(V.localizacion)+ '-' +rtrim(V.cargo) AS codigo, DAY(V.fechaIni) AS diaIni, 
			DAY(V.fechaFin) AS diaFin, V.IDTipoViatico, V.id_proyecto, V.IDSitio, 
			V.viaticoCompleto , V.id_actividad, A.macroactividad
			FROM ViaticosProyecto  V, Actividades A
			WHERE  V.id_actividad = A.id_actividad
			and V.id_proyecto = A.id_proyecto ";
			$sql1=" and (V.fechaIni BETWEEN $cadfecha) AND (V.fechaFin BETWEEN $cadfecha) AND(V.unidad = '$verUnidadDe')";
			$sql2=" order by V.id_actividad ";
			
			
			$sql = $sql.$sql1.$sql2;
			//Nuevo
			//--
			if($resultado=mssql_query($sql)){
				
				if(mssql_num_rows($resultado)>0){
					
					$filas=mssql_fetch_array($resultado);
					
					$codigo=$filas[codigo];
					$idActividad=$filas[id_actividad];
					while($filas) {
						$cualMacroActividad = $filas[macroactividad] ;
						$cantDias = 0;
						   while(($idActividad==$filas[id_actividad]) AND ($codigo == $filas[codigo] ) ){
								//Nuevo
								$tipoViatico = $filas[IDTipoViatico];
								$sql3 = "select * from tiposviatico where idtipoviatico = '$tipoViatico'";
								$ap = mssql_query($sql3);
								$reg=mssql_fetch_array($ap);
								$nomTipoViatico = $reg[nomTipoViatico];
								//Identifica el nombre del proyecto
								$idProyect =  $filas[id_proyecto];
								$sql3 = "select nombre from proyectos where id_proyecto = $idProyect";
								$ap = mssql_query($sql3);
								$reg=mssql_fetch_array($ap);
								$nomdelProyecto = $reg[nombre];
								//--
								//Identifica el nombre del sitio
								$iddelSitio = $filas[IDSitio];
								$sql4 = "select * from sitiostrabajo where IDSitio = $iddelSitio and id_proyecto = $idProyect";
								$ap = mssql_query($sql4);
								$reg=mssql_fetch_array($ap);
								$nomdelSitio = $reg[NomSitio];

								//--
								$diaIni=$filas[diaIni];
								$diaFin=$filas[diaFin];
								$dia = $diaIni;
								//$cantDias = 0;
								do{
									//si el dia el devuelto como 01 se quita el 0, pues 01 y 1 es DIFERENTE EN LINUX
									$numind=substr($dia,0,1);
									if($numind==0){
										$indice=substr($dia,1,1);
									}else{
										$indice=$dia;
									}
									//$viaticos[$indice]="<center><b>X</b></center>";
									$viaticos[$indice]="<center><b>$filas[viaticoCompleto]</b></center>";
									$dia = $dia + 1;
									//Nuevo
									$cantDias = $cantDias + 1;
									//echo "cantidad dias ". $cantDias;
									//--
								}while($dia <= $diaFin);
									$filas=mssql_fetch_array($resultado);
									
							}
								//Nuevo
								if($tipoViatico == 1 or $tipoViatico == 2){
									$viaticos[32]=$cantDias;
								}elseif($tipoViatico == 3){
									$viaticos[33]=$cantDias;
								}elseif($tipoViatico == 4){
									$viaticos[34]=$cantDias;
								}
								
								//--
								//imprime el código encontrado y cambia de idActividad
								$viaticos[-1]="<font face='Arial' size=2>".$codigo."(".$cualMacroActividad.")"." [".substr($nomdelProyecto,0,10)."]"."</font>";
								//$viaticos[-1] = $codigo;
								$viaticos[35]="<font face='Arial' size=1>".ucwords(substr($nomdelProyecto,0,26))."<br>Sitio:".ucwords(substr($nomdelSitio,0,20))."</font>";
								echo "<tr>";
								for($i=-1;$i<=34;$i++){

									if($viaticos[$i]!=NULL and $i <= 31){
										//echo "lo que trae $viaticos[$i] ". $viaticos[$i];
										echo "<td>$viaticos[$i]</td>";
										//Borra el valor para imprimir el siguiente viatico
										$viaticos[$i]="";
									}else{
																				//22Mar2011
										//PBM
										//Verificar si ya existe VoBo para los viáticos del proyecto. si existe no deja grabar.
										if($i == 0){
											$laAprobacionViaticos = 0; 
											$cualImagenViaticos = "";
											$sqlA="SELECT * ";
											$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
											$sqlA=$sqlA." WHERE unidad = " .$verUnidadDe ;
											$sqlA=$sqlA." and id_proyecto =" . $idProyect ;
											if (trim($Flmes)=="") {
												$sqlA=$sqlA." and mes = month(getdate()) "  ;
												$sqlA=$sqlA." and vigencia = year(getdate()) " ;
											}
											else {
												$sqlA=$sqlA." and mes = " . $Flmes ;
												$sqlA=$sqlA." and vigencia = " . $Flano ;
											}
											$cursorA = mssql_query($sqlA);
										//	echo $sqlA;
										//	exit;
											if ($regA=mssql_fetch_array($cursorA)) {
												$laAprobacionViaticos = $regA[validaEncargado] ; 
											}
											if (trim($laAprobacionViaticos) == "1" ) {
												$cualImagenViaticos = "<img src='img/images/Si.gif' alt='Viáticos Aprobados' border='0'>";
											}	
											if (trim($cualImagenViaticos) == "") {
												echo "<td align='center'>&nbsp;</td>";
											}
											else {
												echo "<td align='center'>$cualImagenViaticos</td>";
											}									
											$viaticos[$i]=" ";
											//Cierre 22Mar2011	
										}elseif($i == 32){
											if($viaticos[$i]==NULL) $viaticos[$i]=" ";
											echo "<td align='center'>Viáticos<br>$viaticos[$i]</td>";
											 $viaticos[$i]=" ";
										}elseif($i == 33){
											if($viaticos[$i]==NULL) $viaticos[$i]=" ";
											echo "<td align='center'>Prima/AT<br>$viaticos[$i]</td>";
											$viaticos[$i]=" ";
										}elseif($i == 34){
											if($viaticos[$i]==NULL) $viaticos[$i]=" ";										
											echo "<td align='center'>Aux.Alim<br>$viaticos[$i]</td>";
											$viaticos[$i]=" ";
										}else{
											echo "<td> </td>";
										}
									}
								}
								echo "<td colspan=4>$viaticos[35]</td>";
								echo "</tr>";
							$idActividad=$filas[id_actividad];
							$codigo=$filas[codigo];

					}//Cierra externo
				}
			}
		/**********************imprime la seccion de firmas****************************************/

		echo "<tr><td colspan=4>CT CLASE DE TIEMPO</td><TD colspan=4>HORARIO</TD>
		<td colspan=9>CT CLASE DE TIEMPO</td>
		<TD COLSPAN=4>HORARIO</TD><TD COLSPAN=6>FIRMA DEL EMPLEADO</TD>
		<TD COLSPAN=5>Vo.Bo. JEFE INMEDIATO</TD>
		<TD colspan=5>Vo.Bo. JEFE DEPARTAMENTO</TD><TD>CONTRATOS</TD><TD>
		PERSONAL</TD></tr>";

		//9Jul2007
		//Trae el nombre del usuario que aprueba la hoja de tiempo segun lo indicado en 
		//la variable $firmaApruebaJefe
		$miUsuarioJefeFirma = "";
		//Consulta para traer el nombre del jefe que autoriza
		if (trim($firmaJefeAprueba) != "") {
			$sql0 = "select * from usuarios where unidad =" . $firmaJefeAprueba ;
			$cursor0 = mssql_query($sql0);
			if ($reg0=mssql_fetch_array($cursor0)) {
				$miUsuarioJefeFirma = strtoupper ($reg0[nombre] . " " . $reg0[apellidos]);
			}
		}
		$miUsuarioContrato = "";
		//Consulta para traer el nombre de quien realizó la revisión en Contratos
		if (trim($firmaJefeAprueba) != "") {
			$sql0 = "select * from usuarios where unidad =" . $firmaContratos ;
			$cursor0 = mssql_query($sql0);
			if ($reg0=mssql_fetch_array($cursor0)) {
				$miUsuarioContrato = strtoupper ($reg0[nombre] . " " . $reg0[apellidos]);
			}
		}


		echo "<tr><td colspan=4>1 Ordinario<br>2 Ordinario (1)<br>3 Nocturno Ordinario<br>4 Extra
		 Ordinario (2)<br>5 Extra nocturno</td><TD colspan=4>6 am-10 pm<br>6 am-10 pm<br>10 pm-6 am<br>6 am-10 pm<br>10 pm-6 am</TD>
		<td colspan=9>6 Descanso obligatorio <br>7 Extra descanso obligatorio (3)<br>8 Nocturno descanso obligatorio
		<br>9 Extra descanso obligatorio nocturno
		<br>10 Por compensar
		<br>11 Compensado
		<br>Viático (Clase o Localidad)
		</td>
		<TD COLSPAN=4>6 am-10 pm<br>6 am-10 pm<br>0am-6am y 10pm-12pm<br>0am-6am y 10pm-12pm</TD>
		<TD COLSPAN=6> $kfechaEnvio  </TD>
		<TD COLSPAN=5 align=center >$cualImagen <br> $miUsuarioJefeFirma <br> $kfechaAprueba </TD>
		<TD colspan=5> </TD><TD align=center> $cualImagenCont <br> $miUsuarioContrato <br> $kfechaContratos </TD><TD>
		 </TD></tr>";

	}
	echo "</table>";

?>
<div id="Layer4">
<table class='TxtTabla' width="98%" >
<? 
if (($muestraContratos == 0) AND ($ObsContratos != "")) { ?>
<tr>
  <td colspan="3"><B>Nota Devolución Contratos: </B><? echo $mensajeContratos; ?></td>
  </tr>
<? } ?>
</table>

</div>

</form>
<BR />
<BR />

<?
	if ($hay_errores==1) {
		$cadtmp="<b style='font-size:12px;'>ADVERTENCIA: </b><span style='font-size:12px;'>Usted tiene registrados uno o mas dias con con dos viaticos simultaneos. ";
		$cadtmp.="Por favor comuniquese con el encargado del sistema para corregir esta incongruencia.<br>";
		$cadtmp.="Las casillas marcadas en rojo corresponden a dias que tienen reportados viaticos mas de una vez.</span>";
		echo "<hr>$cadtmp<hr>";
	}
?>
<script>
function mostrarinstr() {
	if (xx.style.display=='none')
		xx.style.display='block';
	else
		xx.style.display='none';
}
</script>
<div id=xx style='display:none;'>
<p class=titulo>Instrucciones para imprimir la hoja de tiempo</p>
	1. Coloque los margenes de impresi&oacute;n en 10 mm por todos lados<br>
	2. Presione el bot&oacute;n que muestra las instrucciones de impresi&oacute;n para ocultar este texto.<br>
	3. Haga clic con el bot&oacute;n secundario del mouse (generalmente el derecho, si el usuario es diestro) sobre la hoja de tiempo.<br>
	4. Seleccione la opcion Imprimir.<br>
	5. Seleccione Propiedades...<br>
	6. Escoja la hoja tamaño carta y coloquela en sentido apaisado.<br>
	7. Presione el bot&oacute;n &lt;OK> dos veces para enviar el trabajo a la impresora.<br>
</div>


</body>
</html>

<?
	function imprime_viaticos($viat) {
		global $verhoja,$sitant,$errores;
		global $MiMes,$MiAnno;

		// imprime los dias
		$cant=0;
		for ($i=1;$i<=31;$i++) {
			$nombredia=date("w", mktime(0,0,0,$MiMes,$i,$MiAnno));
			$numdias=date("t", mktime(0,0,0,$MiMes,$i,$MiAnno));
			$color='';
			if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
			if ($i>$numdias) $color=COLORFINMES;

			$cadtmp="";
			if ($errores[$i]=='1') $cadtmp="class='error'";
			echo "<td bgcolor='$color' $cadtmp><center>";
			if ($viat[$i]=='1') {
				echo "X";
				$cant++;
			} else echo "&nbsp;";
			echo "</center></td>";
		}
		// imprime los totales
		echo "<td bgcolor=''><center>$cant</center></td>";
		echo "<td colspan=4>SITIO: $sitant</td>";
		echo "</tr>";
	}
?>
<?php
	//Imprime el lado posterior de la hoja de tiempo

	
	//Identifica los cargos a los cuales ha facturado el usuario en un mes determinado
	$sql = "SELECT DISTINCT cargo AS Cargo FROM Horas
	WHERE (unidad = $verUnidadDe) AND (fecha BETWEEN $cadfecha) order by cargo";

	$ap3 = mssql_query($sql);
	$i=0;
	$j=0;

	while($reg = mssql_fetch_array($ap3)){
		$arregloM[$i] = $reg[Cargo];
		$i++;
	}




	//Se extrae lo programado
	$sql="SELECT     distinct Horas.cargo, Proyectos.nombre AS NombreProyecto,  Horas.fecha,
	Horas.id_proyecto,Horas.resumen_trabajo
	FROM Horas INNER JOIN Proyectos ON Horas.id_proyecto = Proyectos.id_proyecto
	WHERE     (Horas.unidad = $verUnidadDe) AND (Horas.fecha BETWEEN $cadfecha)
	ORDER BY Horas.fecha asc";

	$ap4 = mssql_query($sql);


	while($reg = mssql_fetch_array($ap4)){
		$fech = substr($reg[fecha],0,11);
		$fech2 = explode(" ",$fech);
		$dia = cambiaDia($fech2[1]);

		if($fech == substr($reg[fecha],0,11)){
				//$arreglo[$dia] = $arreglo[$dia].". ".$reg[resumen_trabajo].". "."<a href='javascript:alert(\"$reg[NombreProyecto]\")'>P</a>";
				$arreglo[$dia] = $arreglo[$dia].". "."<a href='javascript:alert(\"$reg[NombreProyecto]\")'>$reg[resumen_trabajo]</a>";
		}else{
			//$arreglo[$dia] = $reg[resumen_trabajo];
		}
	}
	
	
	//Imprime
	echo "<table class='TxtTabla' width=98% border = 1>";
	echo "<H1 class=SaltoDePagina>";
	echo "<tr><td width=4%><b>DIA</b></td><td width=96%><b><center>TRABAJO REALIZADO</b></center></td></tr>";
		foreach($arreglo as $key => $valor){
			$valor=substr($valor,1);
			echo "<tr><td>$key</td><td>$valor</td>";
		}
	echo "</table>";
	echo "</H1>";

	?>



</div>
</body>
</html>
