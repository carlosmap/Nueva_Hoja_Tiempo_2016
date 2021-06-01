<?
session_start();

echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=" . $_GET["proy"] . "_ReporteFacturacion_" . $_GET["sec"] . "_" . date("Ymd_Hi") .".xls");
echo "</head>";

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

/*
Nombre del Proyecto
*/
$sql0 = " SELECT nombre FROM HojaDeTiempo.dbo.Proyectos WHERE id_proyecto = " . $_GET["proy"];
$cursor0 = mssql_query($sql0);
if($reg0 = mssql_fetch_array($cursor0)){
	$nombreProyecto = ucwords(strtolower($reg0['nombre']));
}

/*
Personas que facturaron a los proyectos
*/
$sql1 = " SELECT H.unidad , U.nombre, U.apellidos, U.NombreCorto, A.nombre nomActividad, H.cargo, H.clase_tiempo, 
SUM(H.horas_registradas) horasFacturadas, C.nombre nombreCat, D.nombre nomDepto, X.nombre nomDivision
FROM Horas H, Usuarios U, Actividades A, Categorias C, Departamentos D, Divisiones X
WHERE H.unidad = U.unidad
AND H.id_actividad = A.id_actividad
AND H.id_proyecto = A.id_proyecto
AND U.id_categoria = C.id_categoria
AND U.id_departamento = D.id_departamento
AND D.id_division = X.id_division ";
$sql1 = $sql1 . " AND H.id_proyecto = " . $_GET["proy"] . " ";
if(isset($mes) && $mes != 0){
	$sql1 = $sql1 . " AND MONTH(H.fecha) = " . $_GET["mes"] . " ";
}
if(isset($anio)){
	$sql1 = $sql1 . " AND YEAR(H.fecha) = " . $_GET["anio"] . " ";
}
$sql1 = $sql1 . " GROUP BY H.unidad , U.nombre, U.apellidos, U.NombreCorto, A.nombre, H.cargo, 
H.clase_tiempo, C.nombre, D.nombre, X.nombre ";
$cursor1 = mssql_query($sql1);


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>


<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" >
<table width="100%" border="1"  cellspacing="1">
  <tr>
    <td colspan="10"><strong>Reporte de Facturaci&oacute;n de Proyectos - Proyecto: <? echo $nombreProyecto; ?></strong></td>
  </tr>
  <tr>
    <td><strong>Unidad</strong></td>
    <td><strong>Nombre Completo </strong></td>
    <td><strong>Nombre Corto </strong></td>
    <td><strong>Actividad</strong></td>
    <td><strong>Cargo</strong></td>
    <td><strong>Clase Tiempo </strong></td>
    <td><strong>Horas Facturadas </strong></td>
    <td><strong>Categoria</strong></td>
    <td><strong>Departamento</strong></td>
    <td><strong>Divisi&oacute;n</strong></td>
  </tr>
  <? while($reg1 = mssql_fetch_array($cursor1)){ ?>
  <tr>
    <td><? echo $reg1['unidad']; ?></td>
    <td><? echo ucwords(strtolower($reg1['nombre'] . " " . $reg1['apellidos'])); ?></td>
    <td><? echo strtoupper($reg1['NombreCorto']); ?></td>
    <td><? echo strtoupper($reg1['nomActividad']); ?></td>
    <td>'<? echo $reg1['cargo']; ?></td>
    <td><? echo $reg1['clase_tiempo']; ?></td>
    <td><? echo $reg1['horasFacturadas']; ?></td>
    <td>'<? echo $reg1['nombreCat']; ?></td>
    <td><? echo strtoupper($reg1['nomDepto']); ?></td>
    <td><? echo strtoupper($reg1['nomDivision']); ?></td>
  </tr>
  <? } // Fin del While de la Consulta ?>
</table>
</body>
</html>
