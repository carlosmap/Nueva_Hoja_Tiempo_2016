<?php
session_start();
include "funciones.php";
include "validaUsrBd.php";

//echo $miCargoAdicional . "<br>";
//echo $laLocalizacion . "<br>";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript" src="ts_picker.js"></script>
	<title>Reporte de viáticos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

</script>
<script>
function activaTV(){ 
//alert ("Entro a envia 1");
//document.RViaticos.recarga.value="1";
document.RViaticos.submit();
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

</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<? include("bannerArriba.php") ; ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right">
	<?
		echo strtoupper($nombreempleado." ".$apellidoempleado);
	?>	</td>
  </tr>
</table>

<h3 class="TituloUsuario">Registro de viáticos</h3>
<!--Para el registro de viáticos previamente debe tener registrada las horas laboradas!-->
<br>

<form name=RViaticos action="grabaViaticos.php" method="post">
<table class="TxtTabla">
<tr>
<td width="160">Seleccione el sitio de trabajo</td>
<td width="246">
<select name=sitioTrabajo class="CajaTexto">
<?php
$sql = "select * from sitiostrabajo where id_proyecto = $id_proyecto";

$ap = mssql_query($sql);
if(mssql_num_rows($ap) > 0){

	while($reg = mssql_fetch_array($ap)){
		echo "<option value= $reg[IDsitio]>$reg[NomSitio]";
	}

	if($sitioTrabajo != ""){
		//Decide cual opción dejar seleccionada
		$sql="select * from sitiostrabajo where idsitio=$sitioTrabajo";
		$ap = mssql_query($sql);
		$reg = mssql_fetch_array($ap);
		echo "<option selected value= $sitioTrabajo>$reg[NomSitio]";
	}
}else{
	echo "<script>alert('No existen sitios de trabajo definidos para el proyecto seleccionado. Por lo tanto no podrá continuar');</script>";
	echo "<script>window.close();</script>";
	exit();
}
?>
</select>

</td>
</tr>

<tr>
<td>Multiplicador del viático</td>
<td>
<select name=multiViatico class="CajaTexto" >
<?php
$sql = "SELECT FraccionesVProy.IDfraccion AS IDFraccion, FraccionesV.Porcentaje, FraccionesV.Descripcion, FraccionesV.IDfraccion as IDfraccion,
	FraccionesVProy.id_proyecto FROM FraccionesVProy INNER JOIN
    FraccionesV ON FraccionesVProy.IDfraccion = FraccionesV.IDfraccion where FraccionesVProy.id_proyecto = $id_proyecto";

$ap = mssql_query($sql);
if(mssql_num_rows($ap) > 0){
	while($reg = mssql_fetch_array($ap)){
		echo "<option value=$reg[IDfraccion]>$reg[Porcentaje]-$reg[Descripcion]";
	}

	if($multiViatico != ""){
		//Decide cual opción dejar seleccionada
		$sql="select * from fraccionesv where idfraccion=$multiViatico";
		$ap = mssql_query($sql);
		$reg = mssql_fetch_array($ap);
		echo "<option selected value= $multiViatico>$reg[Porcentaje]-$reg[Descripcion]";
	}
}else{
	echo "<script>alert('No existen multiplicadores definidos para el proyecto seleccionado. Por lo tanto no podrá continuar');</script>";
	echo "<script>window.close();</script>";
	exit();
}
?>
</select>
</td>
</tr>


<tr>
<td>Tipo de Viático</td>
<td>
<select name=tipoViatico class="CajaTexto" onChange="document.RViaticos.submit()">
<?php
$sql = "SELECT  TiposViatico.IDTipoViatico AS IDTipoViatico, TiposViatico.NomTipoViatico AS NomTipoViatico,
	TiposViaticoProy.id_proyecto AS id_proyecto, TiposViaticoProy.IDTipoViatico AS IDTipoViatico
	FROM TiposViatico INNER JOIN TiposViaticoProy ON TiposViatico.IDTipoViatico = TiposViaticoProy.IDTipoViatico
	where TiposViaticoProy.id_proyecto = $id_proyecto";

$ap = mssql_query($sql);
if(mssql_num_rows($ap) > 0){
	while($reg = mssql_fetch_array($ap)){
		echo "<option value=$reg[IDTipoViatico]>$reg[NomTipoViatico]";
	}
	if($tipoViatico != ""){
		//Decide cual opción dejar seleccionada
		$sql="select * from tiposviatico where idtipoviatico=$tipoViatico";
		$ap = mssql_query($sql);
		$reg = mssql_fetch_array($ap);
		echo "<option selected value= $tipoViatico>$reg[NomTipoViatico]";
	}
}else{
	echo "<script>alert('No existen tipos de viáticos definidos para el proyecto seleccionado. Por lo tanto no podrá continuar');</script>";
	echo "<script>window.close();</script>";
	exit();
}
?>
</select>

</td>
</tr>


<tr>
  <td>Es d&iacute;a de regreso? </td>
  <td>
  <?
	//PBM - 13Sep2007
	//La lista Tipo de viático sólo está disponible para 1=ocasional
  if (trim($tipoViatico) == "" ) {
  	$actTV = "";
  }
  else {
  	if($tipoViatico != 1) {
		$actTV = "disabled";
	}
	else {
		$actTV = "";
	}
  }
  
  ?>
  <select name="diaCompleto" id="diaCompleto" class="CajaTexto" <? echo $actTV; ?> >
    <option value="2">Si</option>
    <option value="1" selected>No</option>
  </select></td>
</tr>
<tr>
<td>Fecha Inicial</td>
<td>
<input name=fechaInicialViatico type=text size= 30 class="CajaTexto" onBlur="valFecha(this);" value=<?echo $fechaInicialViatico;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.RViaticos.fechaInicialViatico);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-30 id="gToday:normal:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>

</td>
</tr>

<tr>
<td>Fecha Final</td>
<td>
<input name=fechaFinalViatico type=text size=30 class="CajaTexto" onBlur="valFecha(this);" value=<?echo $fechaFinalViatico;?>>
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.RViaticos.fechaFinalViatico);return false;" HIDEFOCUS><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-60 id="gToday:normal1:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute; left:-500px; top:0px;">
		</iframe>
</td>
</tr>

<tr>
<td>Trayecto</td>
<td>
<input name=trayectoViatico type=text size="30" class="CajaTexto" value=<?php echo $trayectoViatico;?> >

</td>
</tr>

<tr>
<td>Trabajo Realizado</td>
<td>
<textarea name="trabajoRealizado" rows="4" cols= 30 class="CajaTexto">
<?php echo $trabajoRealizado;?>
</textarea>
<input name="miCargoAdicional" type="hidden" id="miCargoAdicional" value="<? echo $miCargoAdicional; ?>">

</td>
</tr>

<tr><td></td><td><input name="Enviar" type="submit" class="Boton" value="Grabar Viatico"></td></tr>
<tr>
  <td colspan="2">NOTA: El &uacute;ltimo d&iacute;a de vi&aacute;ticos se ingresa de manera independiente </td>
  </tr>

</table>


</form>

<?php
if($Enviar=="Grabar Viatico")
{

	include "validaUsrBd.php";
	//Verifica que la hoja no esté cerrada
	$fecha = explode("/",$fechaInicialViatico);	
	$sql = "select * from autorizacionesht where unidad=$laUnidad and vigencia = $fecha[2] and mes = $fecha[0]";
	$ap = mssql_query($sql);
	$regV = mssql_fetch_array($ap);
	$valEncargado = $regV[validaJefe];
	if($valEncargado == 1) 
	{
		echo "<script>alert('Su hoja de tiempo ya fué aprobada. No podrá realizar ninguna modificación en este periodo. Su jefe inmediato/Contratos podrá desbloquearla')</script>";	
		exit();
	}
	
	//22Mar2011
	//PBM
	//Verificar si ya existe VoBo para los viáticos del proyecto. si existe no deja grabar.
	$laAprobacionViaticos = 0; 
	$sqlA="SELECT * ";
	$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
	$sqlA=$sqlA." WHERE unidad = " .$laUnidad ;
	$sqlA=$sqlA." and id_proyecto =" . $id_proyecto ;
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
		exit();
	}	
	//Cierre 22Mar2011
	
	//verifica que las dos fechas no cambien de mes ni de año
	$fiv = explode("/",$fechaInicialViatico);
	$mesFiv = $fiv[0];
	$anoFiv = $fiv[2];
	$diaFiv = $fiv[1];
	$ffv = explode("/",$fechaFinalViatico);
	$mesFfv = $ffv[0];
	$anoFfv = $ffv[2];
	$diaFfv = $ffv[1];

	if($mesFiv != $mesFfv or $anoFiv != $anoFfv){
		echo "<script>alert('Las fechas inicial y final deben ser del mismo mes y de igual año')</script>";
		exit();
	}

	//Verifica que no se graben viáticos cuando en la tabla horas no haya el tiempo registrado
	$mktFiv = mktime(0,0,0,$mesFiv,$diaFiv,$anoFiv);
	$mktFfv = mktime(0,0,0,$mesFfv,$diaFfv,$anoFfv);

	//Verifica que la fecha final del viatico no sea menor que la fecha inicial
	if ($mktFfv < $mktFiv) 
	{
		echo "<script>alert('La fecha final del viatico debe ser mayor que la fecha inicial')</script>";
		exit();
	}
	
	while($mktFiv <= $mktFfv)
	{
		$laFecha = date("d/M/Y",$mktFiv);
		$fec = explode("/",$laFecha);

		switch ($fec[1]) 
		{
			case "Jan":
			$mes="01";
			break;
			case "Feb":
			$mes="02";
			break;
			case "Mar":
			$mes="03";
			break;
			case "Apr":
			$mes="04";
			break;
			case "May":
			$mes=05;
			break;
			case "Jun":
			$mes="06";
			break;
			case "Jul":
			$mes="07";
			break;
			case "Aug":
			$mes="08";
			break;
			case "Sep":
			$mes="09";
			break;
			case "Oct":
			$mes="10";
			break;
			case "Nov":
			$mes="11";
			break;
			case "Dec":
			$mes="12";
			break;
		}
		$laFecha = $fec[2]."/".$mes."/".$fec[0]	;
		$sql ="select * from horas where unidad=$laUnidad and id_actividad=$idActividad and localizacion=$laLocalizacion and
			cargo=$ElCargoAdicional and fecha = '$laFecha' and id_proyecto = $id_proyecto";

		//Se elimina la restricción que impedia que al no tener horas registradas en una fecha determinada no puede registrar viaticos
		/*$ap = mssql_query($sql);
		if(mssql_num_rows($ap)==0){
			echo "<script>alert('En la fecha $laFecha no hay tiempo registrado. Usted no puede registrar un viático, si aún no ha registrado las horas laboradas para esa fecha');</script>";
			exit();
		}*/

		
		$mktFiv = $mktFiv+86400;
	}

	//Verifica que en la tabla Viaticosproyecto no se traslapen las fechas
	//Fecha inicio y final del mes actual
	$fch = explode("/",$fechaInicialViatico);
	$fch2 = explode("/",$fechaFinalViatico);
	$diaIniAGrabar = $fch[1];
	$diaFinAGrabar = $fch2[1];
	$MiMes=$fch[0];
	$MiAnno=$fch[2];
	$numDias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));
	$fechaActual="'$MiMes/1/$MiAnno' and '$MiMes/$numDias/$MiAnno'";
	
	//$sql ="select * from viaticosproyecto where unidad=$laUnidad and fecha between '$fechaActual'";
	
	if($tipoViatico<5)											// 2014 : Las validaciones de traslapo de fechas para lo existente quedan igual (Viticos ocasionales y permanentes
																// Se crea el filtro para mantener todo igual				
	{		
		//2014 :  Se organiza consulta para que no tenga mayor cantidad de variables sql sql1 sql2 etc.
		$sql="SELECT rtrim(localizacion)+ '-' +rtrim(cargo) AS codigo, DAY(fechaIni) AS diaIni, DAY(fechaFin) AS diaFin, IDTipoViatico, id_proyecto, IDSitio ";
		$sql=$sql . " FROM ViaticosProyecto WHERE  ((fechaIni BETWEEN $fechaActual) and (fechaFin BETWEEN $fechaActual)) AND(unidad = '$laUnidad') ";
		$sql=$sql . " AND IDTipoViatico<5 ";					// 2014 : Se tienen en cuenta únicamente los tipos de viaticos menores a 5
		$sql=$sql . " ORDER BY codigo";
	}
	else
	{
		$sql="SELECT rtrim(localizacion)+ '-' +rtrim(cargo) AS codigo, DAY(fechaIni) AS diaIni, DAY(fechaFin) AS diaFin, IDTipoViatico, id_proyecto, IDSitio ";
		$sql=$sql . " FROM ViaticosProyecto WHERE  ((fechaIni BETWEEN $fechaActual) and (fechaFin BETWEEN $fechaActual)) AND(unidad = '$laUnidad') ";
		$sql=$sql . " AND IDTipoViatico=".$tipoViatico;			// 2014 : Se tienen en cuenta únicamente los tipos de viaticos menores a 5
		$sql=$sql . " ORDER BY codigo";	
	}	
		
	$ap = mssql_query($sql);
	$salir = 0;
	while($reg = mssql_fetch_array($ap))
	{
		$diaIniGrabado = $reg[diaIni];
		$diaFinGrabado = $reg[diaFin];
		for ($i=$diaIniAGrabar;$i<=$diaFinAGrabar;$i++)
		{  	if($i >= $diaIniGrabado and $i <= $diaFinGrabado)
			{
					$salir=$salir+1;
			}				
		}				
	}
	if($salir>0)
	{
		echo "<script>alert('No puede continuar. Se traslapan las fechas, por favor revise su hoja de tiempo');</script>";
		exit();
		$salir = 0;
	}		
    
 	$trabajoRealizado = trim($trabajoRealizado);

	//$sqla = "insert into viaticosproyecto values($id_proyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $laUnidad, ";
	//id_proyecto, IDsitio, IDfraccion, IDTipoViatico, unidad, id_actividad, localizacion, cargo, 
	//FechaIni, FechaFin, Trayecto, ObjetoComision, viaticoCompleto	
	//	$sqla = "insert into viaticosproyecto values($id_proyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $sesUnidadUsuario, ";

	//Si el tipo de viático es diferente de ocasional por defecto se graba es dia de regreso en 1.	
	if ($tipoViatico != 1) 
	{
		$diaCompleto = 1;
	}
	$sqla = "insert into viaticosproyecto values($id_proyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $laUnidad, ";
	$sqlb = "$idActividad, $laLocalizacion, '$miCargoAdicional', '$fechaInicialViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto')";
//	$sqlb = "$idActividad, $laLocalizacion, $miCargo, '$fechaInicialViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto')";
	$sql = $sqla.$sqlb;
	if($ap = mssql_query($sql))
	{
		echo "<script>alert('Viático grabado');</script>";
	}
	else
	{
		echo "<script>alert('Error. Información no grabada.');</script>";
	}

}
?>