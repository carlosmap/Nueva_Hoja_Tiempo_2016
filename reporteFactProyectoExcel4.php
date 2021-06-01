<?
session_start();

echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=".$_GET["proy"]."_ReporteFacturacion_".date("Ymd_Hi").".xls");
echo "</head>";

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

/*
Nombre del Proyecto
*/
$sql0 = " SELECT nombre FROM HojaDeTiempo.dbo.Proyectos WHERE id_proyecto = ".$_GET["proy"];
$cursor0 = mssql_query($sql0);
if($reg0 = mssql_fetch_array($cursor0)){
	$nombreProyecto = ucwords(strtolower($reg0['nombre']));
	$elCodigo = $reg[codigo];
}

//Búsqueda de las actividades
$sqlAct = "SELECT * FROM Actividades
		   WHERE id_proyecto=".$_GET["proy"];
if($act != "")
{
	$sqlAct = $sqlAct." AND id_actividad=".$act;
}
$cursorAct = mssql_query($sqlAct);

//Búsqueda de las personas participantes en cada actividad
$sqlPart = "SELECT DISTINCT U.* 
			FROM Horas H,
			Usuarios U
			WHERE H.unidad=U.unidad
			AND H.id_proyecto=".$_GET["proy"];

//Búsqueda de las horas facturadas por persona
$sqlHora = "SELECT SUM(horas_registradas) AS horasReg
			FROM Horas
			WHERE id_proyecto=".$_GET["proy"];
if ($mes == "") {
	$sqlHora = $sqlHora." AND month(fecha) = month(getdate()) " ;
	$sqlHora = $sqlHora." AND year(fecha) = year(getdate()) " ;
	$mesEnvio = date("m");
	$anoEnvio=date("Y");
}
else {
	$sqlHora = $sqlHora." AND month(fecha) = " . $mes;
	$sqlHora = $sqlHora." AND year(fecha) = " . $anio;
	$mesEnvio =  $mes;
	$anoEnvio= $anio;
}

//Búsqueda de la información del resumen
$sqlRes = "SELECT * FROM Horas
		   WHERE id_proyecto=".$_GET["proy"];

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
    <td><strong>Actividad</strong></td>
    <td><strong>Participante</strong></td>
    <td><strong>Total Facturado </strong></td>
    <td><strong>Loc</strong></td>
    <td>Cargo</td>
    <td><strong>Clase Tiempo </strong></td>
    <td><strong>D&iacute;a</strong></td>
    <td><strong>Mes</strong></td>
    <td><strong>Horas</strong></td>
    <td><strong>Resumen</strong></td>
  </tr>
  
  <? while($regAct = mssql_fetch_array($cursorAct)){ 
		$nomAct = $regAct[macroactividad]." ".$regAct[nombre];
		
		$sqlPart1 = $sqlPart." AND H.id_actividad=".$regAct[id_actividad];
		$cursorPart = mssql_query($sqlPart1);
		
		$sqlHora1 = $sqlHora." AND id_actividad=".$regAct[id_actividad];
		$cursorHora1 = mssql_query($sqlHora1);
		$regHora1 = mssql_fetch_array($cursorHora1);
		//echo $sqlHora1;
			  
	  	if($regHora1[horasReg] != NULL)
		{
			while ($regPart = mssql_fetch_array($cursorPart))
			{
				$nomPart = ucwords(strtolower($regPart[nombre]." ".$regPart[apellidos]));
				
				$sqlHora2 = $sqlHora1." AND id_actividad=".$regAct[id_actividad]."
										AND unidad=".$regPart[unidad];
				$cursorHora = mssql_query($sqlHora2);
				$regHora = mssql_fetch_array($cursorHora);
				
				//Búsqueda de la información del resumen
				$sqlRes1 = $sqlRes." AND id_actividad=".$regAct[id_actividad]."
									 AND unidad=".$regPart[unidad];
				if ($mes == "") {
					$sqlRes1 = $sqlRes1." AND month(fecha) = month(getdate()) " ;
					$sqlRes1 = $sqlRes1." AND year(fecha) = year(getdate()) " ;
				}
				else {
					$sqlRes1 = $sqlRes1." AND month(fecha) = " . $mes;
					$sqlRes1 = $sqlRes1." AND year(fecha) = " . $anio;
				}
				$cursorRes = mssql_query($sqlRes1);
			  		  
			    if($regHora[horasReg] != NULL)
				{
					$totHora = $regHora[horasReg];
					
					while($regRes = mssql_fetch_array($cursorRes))
					{
						$charCodigo = strlen($elCodigo);
					  $cadCargo = $regRes[cargo];
					  $cargo = "";
					  for($i=$charCodigo; $i<strlen($cadCargo); $i++)
					  {
					  	$cargo = $cadCargo[$i];
					  }//cierra for($i=0; $i<strlen($cadCargo); $i++)
					?>
						<tr>
						  <td>&nbsp;<? echo $nomAct; ?></td>
						  <td>&nbsp;<? echo $nomPart; ?></td>
						  <td>&nbsp;<? echo $totHora; ?></td>
						  <td><? echo $regRes[localizacion]; ?></td>
						  <td><? echo $cargo; ?></td>
						  <td><? echo $regRes[clase_tiempo]; ?></td>
						  <td><? echo date("d", strtotime($regRes[fecha])); ?></td>
						  <td><? echo date("m", strtotime($regRes[fecha])); ?></td>
						  <td><? echo $regRes[horas_registradas]; ?></td>
						  <td><? echo $regRes[resumen_trabajo]; ?></td>
						</tr>
					<?
						$nomAct = "";
						$nomPart = "";
						$totHora = "";
					}//cierra while($regRes = mssql_fetch_array($cursorRes)){
				}//cierra if($regHora[horasReg] != NULL)
				
			}//cierra while ($regPart=mssql_fetch_array($cursorPart))
			
  	 	}//cierra if($regHora1[horasReg] != NULL)
		
     }//cierra while($regAct = mssql_fetch_array($cursorAct)) ?>
</table>
</body>
</html>
