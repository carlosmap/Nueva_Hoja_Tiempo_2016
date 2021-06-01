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
	include "validaUsrBd.php";
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
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<? include("bannerArriba.php") ; ?>
<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
	<div align="center">ELIMINACI&Oacute;N DE REGISTRO DE VIATICOS </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  <tr>
    <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}



	}
	
	?>
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes">
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select></td>
    <td width="15%" align="right" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
	&nbsp;
	<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>

    </select>	</td>
    <td width="10%"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Registro de viáticos para el mes seleccionado</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr class="TituloTabla2">
    <td>Proyecto</td>
    <td>Actividad</td>
    <td>Fecha Inicial </td>
    <td>Fecha Final </td>
    <td>Trayecto</td>
    </tr>
  <?
  $vSql="Select v.* , p.nombre, a.nombre nomActividad ";
  $vSql=$vSql." from viaticosproyecto v, proyectos p, actividades a ";
  $vSql=$vSql." where v.id_proyecto = p.id_proyecto ";
  $vSql=$vSql." and v.id_proyecto = a.id_proyecto ";
  $vSql=$vSql." and v.id_actividad = a.id_actividad ";
  $vSql=$vSql." and v.unidad = " . $laUnidad;
  $vSql=$vSql." and month(fechaIni)=" . $mesActual ;
  $vSql=$vSql." and year(fechaIni)=" . $AnoActual ;
  $vCursor = mssql_query($vSql);

	$pMiProyectoID = "";
	while ($vReg=mssql_fetch_array($vCursor)) {
  ?>
  <tr class="TxtTabla">
    <td><? echo  ucwords(strtolower($vReg[nombre])) ; ?>
	<? $pMiProyectoID = $vReg[id_proyecto] ; ?>
	</td>
    <td><? echo  ucwords(strtolower($vReg[nomActividad])) ; ?></td>
    <td><? echo date("M d Y ", strtotime($vReg[FechaIni])); ?></td>
    <td><? echo date("M d Y ", strtotime($vReg[FechaFin])); ?></td>
    <td><? echo  ucwords(strtolower($vReg[Trayecto])) ; ?></td>
    </tr>
	<? } ?>
</table></td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
       <td class="TituloUsuario">Eliminación de registro de vi&aacute;ticos de la base de datos </td>
</table>



<form name="DatosEntradaV" action="EdicionRegistros-ElimViaticos.php" method="post">
	<table width="310" class="TxtTabla">
	<tr>
	  <td width="101">Fecha Inicial:</td>
	  <td width="197"><input type="text" name="timestamp" value=<?echo $timestamp;?>>
	<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.DatosEntradaV.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
				<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
				</iframe>
	</td></tr>
	<tr>
	  <td width="101">Fecha Final:</td>
	  <td width="197"><input type="text" name="timestampF" value=<?echo $timestampF;?>>
	<a href="javascript:void(0)" onclick="gfPop.fPopCalendar(document.DatosEntradaV.timestampF);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
				<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-130 id="gToday:normal1:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute; left:-500px; top:0px;">
				</iframe>
	</td></tr>
	</table>
	<table>
		<tr><td> </td></tr>
	<tr><td><input type="submit" class=Boton name="Elimine" value="Eliminar"></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','EdicionRegistros-mnu.php');return document.MM_returnValue" value="  Atras   "></a></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  P&aacute;gina principal Hoja tiempo   ">
	</a></td></tr>
	
	</table>
</form>
<?

if($Elimine=="Eliminar"){
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

	//22Mar2011
	//PBM
	//Verificar si ya existe VoBo para los viáticos del proyecto. si existe no deja grabar.
	$laAprobacionViaticos = 0; 
	$sqlA="SELECT * ";
	$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
	$sqlA=$sqlA." WHERE unidad = " .$laUnidad ;
	$sqlA=$sqlA." and id_proyecto =" . $pMiProyectoID ;
	$sqlA=$sqlA." and mes = " . $fecha[0] ;
	$sqlA=$sqlA." and vigencia = " . $fecha[2] ;
	$cursorA = mssql_query($sqlA);
//	echo $sqlA;
//	exit;
	
	if ($regA=mssql_fetch_array($cursorA)) {
		$laAprobacionViaticos = $regA[validaEncargado] ; 
	}
	if (trim($laAprobacionViaticos) == "1" ) {
		echo "<script>alert('Los viáticos ya fueron aprobados. No podrá realizar ninguna modificación en este periodo. El director/coordinador o encargado del proyecto podrá levantar el VoBo de los viáticos')</script>";	
		exit();
	}	
	//Cierre 22Mar2011


	//impide que se borren registros de una semana atras

	//$mkFechaActual = mktime(24,00,00,03,22,2006);
	$fechAct = explode("-",date("n-d-Y"));

	$mktFechAct = mktime(0,0,0,$fechAct[0],$fechAct[1],$fechAct[2]);

	$fechDig = explode("/",$timestamp);


	$mktFechDig = mktime(0,0,0,$fechDig[0],$fechDig[1],$fechDig[2] );

	$difMkt =$mktFechAct-$mktFechDig;


//	if ($difMkt <= 5529600){
//	if ($difMkt <= 8035200){
//Para 180 días (180*24*60*60)
//Para 8 meses 240 días (240*24*60*60)
	if ($difMkt <= 31104000){
		$sql="SELECT * FROM viaticosproyecto WHERE (unidad = '$sesUnidadUsuario') AND (fechaIni = '$timestamp') AND (fechaFin = '$timestampF')";
		$rpta=mssql_query($sql);

		$NumReg=mssql_num_rows($rpta);

		if($NumReg<=0){
			echo "<script>alert('No hay registros en las fechas indicadas')</script>"	;
			exit();
		}elseif($NumReg>0){
			$sql="DELETE FROM viaticosproyecto WHERE (unidad = '$sesUnidadUsuario') AND (fechaIni = '$timestamp') AND (fechaFin = '$timestampF')";
			$rpta=mssql_query($sql);
			echo "<script>alert('Registros borrado')</script>"	;
			exit();
		
			//Despliega lo encontrado
			/*$i=1;
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
				echo "<tr><td>$i) <a href='EdicionRegistros-4Elim1.php?idPry=$idPry&&idAct=$idAct&&idLoc=$idLoc&&idCar=$idCar&&idCtp=$idCtp&&fecha=$timestamp'>$idRst</a> ($timestamp)</td></tr>";
				$i++;
			}
			echo "</table>";*/
		}
	}else{
/*		echo "<script>alert('No esta permitido eliminar un registro con más de un mes de antigüedad')</script>";
		echo "<script>alert('No esta permitido eliminar un registro con más de tres mes de antigüedad')</script>"; 
		echo "<script>alert('No esta permitido eliminar un registro con más de seis mes de antigüedad')</script>"; */
		echo "<script>alert('No esta permitido eliminar un registro con más de un año de antigüedad')</script>";
	}
}
?>

</body>
</html>
