<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";
//Crea el archivo HTML que se sube como réplica exacta del Excel
include "reporteFactProyectoArchivo.php";

//Arreglo de Meses
$meses = array("Todos", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre" , "Diciembre");

//Arreglo de años, desde 2006 hasta el año actual
for($i = 2006; $i <= date("Y"); $i++){
	$anios[] = $i;
}

/*
Si no vienen los parámetros Mes y Año, se toma como referencia los actuales
*/
if(trim($mes) == ""){
	$mes = date("m");
}
if(trim($anio) == ""){
	$anio = date("Y");
}

/*
Proyectos
*/
$sql1 = " SELECT * FROM HojaDeTiempo.dbo.Proyectos WHERE id_proyecto = " . $cualPr;
$cursor1 = mssql_query($sql1);
if($reg1 = mssql_fetch_array($cursor1)){
	$nombreProyecto = ucwords(strtolower($reg1['nombre']));
}

/*
Personas que facturaron a los proyectos
*/
$sql2 = " SELECT H.unidad , U.nombre, U.apellidos, U.NombreCorto, A.nombre nomActividad, H.cargo, H.clase_tiempo, H.localizacion,
SUM(H.horas_registradas) horasFacturadas, C.nombre nombreCat, D.nombre nomDepto, X.nombre nomDivision
FROM Horas H, Usuarios U, Actividades A, Categorias C, Departamentos D, Divisiones X
WHERE H.unidad = U.unidad
AND H.id_actividad = A.id_actividad
AND H.id_proyecto = A.id_proyecto
AND U.id_categoria = C.id_categoria
AND U.id_departamento = D.id_departamento
AND D.id_division = X.id_division ";
$sql2 = $sql2 . " AND H.id_proyecto = " . $cualPr . " ";
if(isset($mes) && $mes != 0){
	$sql2 = $sql2 . " AND MONTH(H.fecha) = " . $mes . " ";
}
if(isset($anio)){
	$sql2 = $sql2 . " AND YEAR(H.fecha) = " . $anio . " ";
}
$sql2 = $sql2 . " GROUP BY H.unidad , U.nombre, U.apellidos, U.NombreCorto, A.nombre, H.cargo, 
H.clase_tiempo, H.localizacion, C.nombre, D.nombre, X.nombre ";
$cursor2 = mssql_query($sql2);
$cantRegistros = mssql_num_rows($cursor2);

if($recarga == 2){
	$okGuardar = "Si";
	
	$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
	if(trim($cursorTran1) == ""){
		$okGuardar = "No";
	}
	
	$sqlIn1 = " SELECT COALESCE(MAX(secReporte), 0) AS laSecuencia FROM HojaDeTiempo.dbo.RptFacturacionProyectos ";
	$cursorIn1 = mssql_query($sqlIn1);
	if($regIn1 = mssql_fetch_array($cursorIn1)){
		$laSecuencia = $regIn1['laSecuencia'] + 1;
	}
	
	//Arma el nombre del archivo
	$nombreArchivo = $cualPr . "_ReporteFacturacion_" . $laSecuencia . "_" . date("Ymd_Hi") .".html";
	
	$sqlIn1 = " INSERT INTO HojaDeTiempo.dbo.RptFacturacionProyectos ( secReporte, unidad, id_proyecto, mesReporte, anioReporte, fechaReporte, archivoReporte ) ";
	$sqlIn1 = $sqlIn1 . " VALUES ( ";
	$sqlIn1 = $sqlIn1 . " " . $laSecuencia . ", ";
	$sqlIn1 = $sqlIn1 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
	$sqlIn1 = $sqlIn1 . " " . $cualPr . ", ";
	$sqlIn1 = $sqlIn1 . " " . $mes . ", ";
	$sqlIn1 = $sqlIn1 . " " . $anio . ", ";
	$sqlIn1 = $sqlIn1 . " '" . date("m/d/Y H:i:s") . "', ";
	$sqlIn1 = $sqlIn1 . " '" . $nombreArchivo . "' ";
	$sqlIn1 = $sqlIn1 . " ) ";
	$cursorIn1 = mssql_query($sqlIn1);
	if(trim($cursorIn1) == ""){
		$okGuardar = "No";
	}
	
	//Si la grabación fue exitosa
	if(trim($okGuardar) == "Si"){
		$cursorTran1 = mssql_query(" COMMIT TRANSACTION ");
		//Crea y Sube el archivo copia HTML
		crearArchivo($nombreArchivo, $sql2, $nombreProyecto);
		//Redireccionar a la página para generar el excel
		echo "<script>javascript:window.open('reporteFactProyectoExcel.php?sec=" . $laSecuencia . "&proy=" . $cualPr . "&mes=" . $mes . "&anio=" . $anio . "', '', 'scrollbars=yes,resizable=yes,width=800,height=600');</script>";
		echo "<script>window.close();</script>";
	} else {
		$cursorTran1 = mssql_query(" ROLLBACK TRANSACTION ");
		echo "<script>alert('No se pudo generar el registro');</script>";
		echo "<script>window.close();</script>";
	}
	exit;
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->

/*
Funciones para generar el reporte
*/
function generarRep(){
	document.form1.recarga.value = 2;
	document.form1.submit();
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" class="TxtTabla">
<table width="100%" class="TituloUsuario">
  <tr>
    <td>::: Reportes de Facturaci&oacute;n por Proyecto ::: </td>
  </tr>
</table>
<table width="100%"  cellspacing="1" class="TxtTabla">
  <tr>
    <td align="center">
	<form action="" name="form1" method="post">
	<table width="60%"  cellspacing="1" class="fondo">
      <tr>
        <td width="25%" class="TituloTabla">Proyecto</td>
        <td class="TxtTabla"><? echo $nombreProyecto; ?></td>
        </tr>
      <tr>
        <td class="TituloTabla">Mes</td>
        <td class="TxtTabla"><select name="mes" class="CajaTexto" id="mes">
		<? 
		for($i = 0; $i < count($meses); $i++){ 
			$optMes = "";
			if($mes == $i){
				$optMes = "selected";
			}
		?>
			<option value="<? echo $i; ?>" <? echo $optMes; ?>><? echo $meses[$i]; ?></option>
		<? } ?>
        </select></td>
        </tr>
      <tr>
        <td class="TituloTabla">A&ntilde;o</td>
        <td class="TxtTabla"><select name="anio" class="CajaTexto" id="anio">
		<? 
		for($j = 0; $j < count($anios); $j++){ 
			$optAn = "";
			if($anio == $anios[$j]){
				$optAn = "selected";
			}
		?>
			<option <? echo $optAn; ?>><? echo $anios[$j]; ?></option>
		<? } ?>
        </select></td>
      </tr>
      <tr align="right">
        <td colspan="2" class="TxtTabla"><input name="recarga" type="hidden" id="recarga" value="1">
		<input name="Submit" type="submit" class="Boton" value="Consultar"></td>
        </tr>
    </table>
	</form>	</td>
  </tr>
</table>
<!-- Espacio -->
<table width="100%"  cellspacing="1">
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
<!-- Resultados de la Consulta -->
<table width="100%"  cellspacing="1" class="fondo">
  <? if($cantRegistros == 0){ ?>
  <tr align="center">
    <td colspan="11" class="TxtTabla">La b&uacute;squeda no obtuvo resultados </td>
  </tr>
  <tr align="center">
    <td colspan="11" class="TxtTabla"><input name="Submit3" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cerrar Ventana"></td>
  </tr>
  <? } else { ?>
  <tr align="right">
    <td colspan="10" class="TxtTabla"><input name="Submit2" type="button" class="Boton" value="Generar Reporte en Excel" onClick="generarRep();"></td>
  </tr>
  <tr class="TituloTabla2">
    <td>Unidad</td>
    <td>Nombre completo </td>
    <td>Categor&iacute;a</td>
    <td>Actividad</td>
    <td>Cargo</td>
    <td>Clase Tiempo </td>
    <td>Localizaci&oacute;n</td>
    <td>Horas Facturadas </td>
    <td>Departamento</td>
    <td>Divisi&oacute;n</td>
  </tr>
  <? while($reg2 = mssql_fetch_array($cursor2)){ ?>
  <tr class="TxtTabla">
    <td><? echo $reg2['unidad']; ?></td>
    <td><? echo ucwords(strtolower($reg2['nombre'] . " " . $reg2['apellidos'])); ?></td>
    <td><? echo $reg2['nombreCat']; ?></td>
    <td><? echo strtoupper($reg2['nomActividad']); ?></td>
    <td><? echo $reg2['cargo']; ?></td>
    <td><? echo $reg2['clase_tiempo']; ?></td>
    <td><? echo $reg2['localizacion']; ?></td>
    <td><? echo $reg2['horasFacturadas']; ?></td>
    <td><? echo strtoupper($reg2['nomDepto']); ?></td>
    <td><? echo strtoupper($reg2['nomDivision']); ?></td>
  </tr>
  <? } // Fin del While de la Consulta ?>
  <? } // Fin del If de Cantidad de Registros ?>
</table>
</body>
</html>
