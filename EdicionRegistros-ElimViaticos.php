<?

/*NOTA : LOS CAMBIOS REALIZADOS PARA REQUERIMIENTO WILSON MARTINEZ - TEMA VIATICOS SE PUEDEN ENCONTRAR COMENTARIADOS CON AÑO 2014 */

//Inicializa las variables de sesión
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

//Validación de Ingreso
include "funciones.php";   					
include "validacion.php";
include "validaUsrBd.php";

//$laUnidad="12974";
//$clave="1373";

//echo "hola";	
//if($Elimine=="Eliminar")
if($accion==3)
{   
	//include "validaUsrBd.php";  														/*2014 : Se comentarea esta librearia esta en la parte superior */
	//	validarEliminacionViatico($fechaIni,$fechaFin);
	
	/******** 2014 : Se adicionan las fechas  ************/	
	$timestamp=$fechaIni;																/*2014 : Se asigna variable $timestamp=$fechaIni;	*/
	$timestampF=$fechaFin;																/*2014 : Se asigna variable $timestampF=$fechaFin;	*/
    /*****************************************************/
	//Verifica que la hoja no esté cerrada
	$fecha = explode("/",$timestamp);	
	$sql = "select * from autorizacionesht where unidad=$laUnidad and vigencia = $fecha[2] and mes = $fecha[0]";
	$ap = mssql_query($sql);
	$regV = mssql_fetch_array($ap);
	$valEncargado = $regV[validaJefe];
	
	if($valEncargado == 1) 
	{
		echo "<script>alert('Su hoja de tiempo ya fué aprobada. No podrá realizar ninguna modificación en este periodo. Su jefe inmediato podrá desbloquearla')</script>";	
		//exit();																		/*2014 : Se retira exit();
	}

	//22Mar2011
	//PBM
	//Verificar si ya existe VoBo para los viáticos del proyecto. si existe no deja grabar.
	 $vSql="Select v.* , p.nombre, a.nombre nomActividad, t.NomTipoViatico ";				// 2014 : Se adiciona campo t.NomTipoViatico
     $vSql=$vSql." from viaticosproyecto v, proyectos p, actividades a, TiposViatico t "; 	// 2014 : Se adiciona tabla TiposViatico t
	 $vSql=$vSql." where v.id_proyecto = p.id_proyecto ";
	 $vSql=$vSql." and v.id_proyecto = a.id_proyecto ";
	 $vSql=$vSql." and v.id_actividad = a.id_actividad ";
	 $vSql=$vSql." and v.IDTipoViatico = t.IDTipoViatico "; 								// 2014 : Se adiciona filtro and v.IDTipoViatico = t.IDTipoViatico
	 $vSql=$vSql." and v.unidad = " . $laUnidad;
	 $vSql=$vSql." and v.fechaIni = '" . $timestamp . "' " ;
	 $vSql=$vSql." and v.fechaFin = '" . $timestampF . "' ";
/*  
  $vSql=$vSql." and month(fechaIni)=" . $fecha[0] ;
  $vSql=$vSql." and year(fechaIni)=" . $fecha[2] ;
  echo $vSql . "<br>";
*/

  $pMiProyectoID = "" ;
  $vCursor = mssql_query($vSql);
  if ($vReg=mssql_fetch_array($vCursor)) 
  {
  	$pMiProyectoID = $vReg[id_proyecto] ;
  }

	$laAprobacionViaticos = 0; 
	$sqlA="SELECT * ";
	$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
	$sqlA=$sqlA." WHERE unidad = " .$laUnidad ;
	$sqlA=$sqlA." and id_proyecto =" . $pMiProyectoID ; 
	$sqlA=$sqlA." and mes = " . $fecha[0] ;
	$sqlA=$sqlA." and vigencia = " . $fecha[2] ;
	$cursorA = mssql_query($sqlA);
	if ($regA=mssql_fetch_array($cursorA)) 
	{
		$laAprobacionViaticos = $regA[validaEncargado] ; 
	}
	if (trim($laAprobacionViaticos) == "1" ) 
	{
		echo "<script>alert('Los viáticos ya fueron aprobados. No podrá realizar ninguna modificación en este periodo. El director/coordinador o encargado del proyecto podrá levantar el VoBo de los viáticos')</script>";	
		exit();																								/*2014 : Se retira exit(); */
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
	if ($difMkt <= 31104000)
	{
		$sql="SELECT * FROM viaticosproyecto WHERE (unidad = '$sesUnidadUsuario') AND (fechaIni = '$timestamp') AND (fechaFin = '$timestampF') AND (IDTipoViatico=$tipoV)"; //2014 : Se adiciona AND (IDTipoViatico=$tipoV)
		$rpta=mssql_query($sql);
		$NumReg=mssql_num_rows($rpta);
		if($NumReg<=0)
		{
			echo "<script>alert('No hay registros en las fechas indicadas')</script>"	;
			//exit();																									/*2014 : Se retira exit();
		}
		elseif($NumReg>0)
		{
			$sql="DELETE FROM viaticosproyecto WHERE (unidad = '$sesUnidadUsuario') AND (fechaIni = '$timestamp') AND (fechaFin = '$timestampF') AND (IDTipoViatico=$tipoV)"; //2014 : Se adiciona AND (IDTipoViatico=$tipoV)
			$rpta=mssql_query($sql);
			echo "<script>alert('Registros borrado')</script>"	;
			//exit();																									/*2014 : Se retira exit();
		
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
	}
	else
	{
/*		echo "<script>alert('No esta permitido eliminar un registro con más de un mes de antigüedad')</script>";
		echo "<script>alert('No esta permitido eliminar un registro con más de tres mes de antigüedad')</script>"; 
		echo "<script>alert('No esta permitido eliminar un registro con más de seis mes de antigüedad')</script>"; */
		echo "<script>alert('No esta permitido eliminar un registro con más de un año de antigüedad')</script>";
	}
	
	echo ("<script>MM_goToURL('parent','EdicionRegistros-ElimViaticos1.php');return document.MM_returnValue</script>");		/*2014 : Se redirecciona pantalla; */
}
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

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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

<form name="form1" method="post" action="">

<!-- Espacio -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<!-- Tabla de consulta - filtros -->
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
		  <tr>
			<td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
			<td width="30%" class="TxtTabla">
			<? 
			//Seleccionar el mes cuando se carga la página por primera vez
			//si no cuando se recarga la página
			if ($pMes == "") 
			{
				$mesActual=date("m"); //el mes actual
			}
			else 
			{
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
			<td width="10%" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
		  </tr>
	</table>

	<!-- Espacio -->
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td class="TxtTabla">&nbsp;</td>
  </tr>
</table>
	
	</td>
  </tr>
</table>

<!-- Titulo -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Registro de viáticos para el mes seleccionado</td>
  </tr>
</table>

<!-- Registros -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">	
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
		  <tr class="TituloTabla2">
			<td>Proyecto</td>
			<td>Actividad</td>
			<td>Viatico / Auxilio </td>
			<td>Fecha Inicial </td>
			<td>Fecha Final </td>
			<td>Trayecto</td>
			<td>Eliminar</td>
		  </tr>
		  <?
		  /*2014 : Se adiciona tabla y campos relacionados a TiposViatico */
		  $vSql="Select v.* , p.nombre, a.nombre nomActividad, t.NomTipoViatico ";				// 2014 : Se adiciona campo t.NomTipoViatico
		  $vSql=$vSql." from viaticosproyecto v, proyectos p, actividades a, TiposViatico t ";	// 2014 : Se adiciona tabla TiposViatico t
		  $vSql=$vSql." where v.id_proyecto = p.id_proyecto ";
		  $vSql=$vSql." and v.id_proyecto = a.id_proyecto ";
		  $vSql=$vSql." and v.id_actividad = a.id_actividad ";
		  $vSql=$vSql." and v.IDTipoViatico = t.IDTipoViatico "; 						       // 2014 : Se adiciona filtro and v.IDTipoViatico = t.IDTipoViatico
		  $vSql=$vSql." and v.unidad = " . $laUnidad;
		  $vSql=$vSql." and month(fechaIni)=" . $mesActual ;
		  $vSql=$vSql." and year(fechaIni)=" . $AnoActual ;
		  $vCursor = mssql_query($vSql);
		//	$pMiProyectoID = "";
		  while ($vReg=mssql_fetch_array($vCursor)) 
		  { 
		  ?>
		  <tr class="TxtTabla">
			<td><? echo  ucwords(strtolower($vReg[nombre])) ; ?>
			<? //$pMiProyectoID = $vReg[id_proyecto] ; ?>	</td>
			<td><? echo  ucwords(strtolower($vReg[nomActividad])) ; ?></td>
			<td><? echo  ucwords(strtolower($vReg[NomTipoViatico])) ; ?></td>
			<td><? echo date("Y/m/d", strtotime($vReg[FechaIni]));
			            $x=date("m/d/Y", strtotime($vReg[FechaIni])); ?></td>
			<td><? echo date("Y/m/d", strtotime($vReg[FechaFin]));
						$y=date("m/d/Y", strtotime($vReg[FechaFin])); ?></td>
			<td><? echo  ucwords(strtolower($vReg[Trayecto])) ; ?></td>
			<td>
			<!--
			<input name="fechaIni" type="text" id="fechaIni" value="<? //echo $x; //date("m/d/Y", strtotime($vReg[FechaIni])); ?>">
			<input name="fechaFin" type="text" id="fechaFin" value="<? //echo $y; //date("m/d/Y", strtotime($vReg[FechaFin])); ?>">
			<input name="tipoV" type="text" id="tipoV" value="<? //echo $vReg[IDTipoViatico] ?>">	
			<input name="recarga" type="hidden" id="recarga" value="1"> -->
			<input name="Elimine" type="submit" class="Boton" onClick="MM_goToURL('parent','EdicionRegistros-ElimViaticos.php?fechaIni=<? echo $x; ?>&fechaFin=<? echo $y; ?>&tipoV=<? echo $vReg[IDTipoViatico]; ?>&accion=3');return document.MM_returnValue" value="Eliminar">
			<!--<input type="button" class="Boton" name="Elimine" value="Eliminar"> -->
			</td>
		  </tr>
			<? 
			} ?>
		</table>
	</td>
  </tr>
</table>

<!-- Espacio -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  	<tr>
		<td><a href="#">
		  <input name=atras type=button class="Boton" onclick="MM_goToURL('parent','EdicionRegistros-mnu.php');return document.MM_returnValue" value="  Atras   "> </a><a href="#">
	  <input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  P&aacute;gina principal Hoja tiempo   "></a> </td>
    </tr>
  </table>	
</form>
</body>
</html>
