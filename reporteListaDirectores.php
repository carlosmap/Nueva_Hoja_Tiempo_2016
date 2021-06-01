<?
session_start();

echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=ReporteDirectoresProyectos" . date("Ymd_Hi") .  ".xls");
echo "</head>";

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

/*
Proyectos, Directores y Coordinadores
*/
$sql=" SELECT * FROM ";
$sql=$sql." ( ";
$sql=$sql." 	select P.* , D.nombre nomDirector, D.apellidos apeDirector, C.nombre nomCoordina, C.apellidos apeCoordina, ";
$sql=$sql." 	eD.extension extDir, eC.extension extCoordina, sC.id_division, D.email mailDirector, C.email mailCoordina ";
$sql=$sql." 	from HojaDeTiempo.dbo.proyectos P, HojaDeTiempo.dbo.Usuarios D, HojaDeTiempo.dbo.Usuarios C, ";
$sql=$sql." 		GestiondeInformacionDigital.dbo.extensiones eD, ";
$sql=$sql." 		GestiondeInformacionDigital.dbo.extensiones eC, ";
$sql=$sql." 		(select Distinct A.secuencia, A.id_division, B.id_proyecto ";
$sql=$sql." 		from GestiondeInformacionDigital.dbo.SolicitudCodigo A, GestiondeInformacionDigital.dbo.CargosSolCodigo B ";
$sql=$sql." 		where A.secuencia = B.secuencia ";
$sql=$sql." 		) sC ";
$sql=$sql." 	where P.id_director = D.unidad  ";
$sql=$sql." 	and P.id_coordinador *= C.unidad ";
$sql=$sql." 	and P.id_director *= eD.unidad  ";
$sql=$sql." 	and P.id_coordinador *= eC.unidad ";
$sql=$sql." 	and P.id_estado = 2 ";
$sql=$sql." 	and (P.codigo <> 'ACC' and P.codigo <> 'AUS' and P.codigo <> 'ENF' and P.codigo <> 'LIC'  ";
$sql=$sql." 	and P.codigo <> 'PER' and P.codigo <> 'SAN' and P.codigo <> 'VAC'  and P.codigo <> 'INF' and P.codigo <> 'MAP')   ";
$sql=$sql." 	and P.id_proyecto *= sC.id_proyecto ";
//Parámetros de Búsqueda
if (trim($cNombre) != "") {
	$sql=$sql." and P.nombre LIKE '%".$cNombre."%'" ;
}
if (trim($pEmp) != "") {
	$sql=$sql." and P.idEmpresa =" . $pEmp ;
}
if (trim($cNombreDir) != "") {
	$sql=$sql." and (D.nombre LIKE '%".$cNombreDir."%' OR D.apellidos LIKE '%".$cNombreDir."%') " ;
}
$sql=$sql." and CAST(P.codigo AS int)  < 78 "; //Para que solo saque los Proyectos
$sql=$sql." ) X ";
//Búsqueda: División
if (trim($pfDivision) != "") {
	$sql=$sql." where id_division = " . $pfDivision;
}
$sql=$sql." order by nombre " ;
$cursor = mssql_query($sql);

/*
Número Máximo de Ordenadores del Gasto
*/
$sqlMaxOG = " SELECT COUNT(DISTINCT A.unidadOrdenador) AS cantOrdenadores, A.id_proyecto, 
B.nombre AS nomProyecto
FROM GestiondeInformacionDigital.dbo.OrdenadorGasto AS A
JOIN HojaDeTiempo.dbo.Proyectos AS B ON A.id_proyecto = B.id_proyecto
WHERE A.id_proyecto IS NOT NULL
AND B.id_estado = 2
AND B.especial IS NULL
GROUP BY A.id_proyecto, B.nombre
ORDER BY cantOrdenadores DESC ";
$cursorMaxOG = mssql_query($sqlMaxOG);
if($regMaxOG = mssql_fetch_array($cursorMaxOG)){
	$maxOrdenadores = $regMaxOG['cantOrdenadores'];
}

/*
Obtiene el número máximo de Programadores de Proyecto
*/
$sqlMaxPP = " SELECT COUNT(DISTINCT A.unidad) AS cantProgramadores, A.id_proyecto, 
B.nombre AS nomProyecto
FROM HojaDeTiempo.dbo.Programadores AS A
JOIN HojaDeTiempo.dbo.Proyectos AS B ON A.id_proyecto = B.id_proyecto
WHERE A.id_proyecto IS NOT NULL
AND B.id_estado = 2
AND B.especial IS NULL
AND A.progProyecto = '1'
GROUP BY A.id_proyecto, B.nombre
ORDER BY cantProgramadores DESC ";
$cursorMaxPP = mssql_query($sqlMaxPP);
if($regMaxPP = mssql_fetch_array($cursorMaxPP)){
	$maxProgP = $regMaxPP['cantProgramadores'];
}

/*
Obtiene el número máximo de Programadores de Actividades
*/
$sqlMaxPA = " SELECT COUNT(DISTINCT A.unidad) AS cantProgramadores, A.id_proyecto, 
B.nombre AS nomProyecto
FROM HojaDeTiempo.dbo.Programadores AS A
JOIN HojaDeTiempo.dbo.Proyectos AS B ON A.id_proyecto = B.id_proyecto
WHERE A.id_proyecto IS NOT NULL
AND B.id_estado = 2
AND B.especial IS NULL
AND A.progProyecto = '0'
GROUP BY A.id_proyecto, B.nombre
ORDER BY cantProgramadores DESC ";
$cursorMaxPA = mssql_query($sqlMaxPA);
if($regMaxPA = mssql_fetch_array($cursorMaxPA)){
	$maxProgA = $regMaxPA['cantProgramadores'];
}

$colspan = 7 + $maxOrdenadores + $maxProgA + $maxProgA;

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" >
<table width="100%"  border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td colspan="<? echo $colspan; ?>"><strong>::: Directores y Coordinadores de Proyectos Hoja de Tiempo ::: </strong></td>
  </tr>
  <tr>
    <td width="5%" rowspan="2" align="center"><strong>C&oacute;digo</strong></td>
    <td rowspan="2" align="center"><strong>Proyecto</strong></td>
    <td width="12%" rowspan="2" align="center"><strong>Director</strong></td>
    <td width="12%" rowspan="2" align="center"><strong>Mail Director</strong></td>
    <td width="7%" rowspan="2" align="center"><strong>Coordinador</strong></td>
    <td rowspan="2" align="center"><strong>Mail Coordinador</strong></td>
    <td width="12%" colspan="<? echo $maxOrdenadores; ?>" align="center"><strong>Ordenadores del gasto </strong></td>
    <td width="12%" colspan="<? echo $maxProgP; ?>" align="center"><strong>Programadores de proyecto </strong></td>
    <td width="12%" colspan="<? echo $maxProgA; ?>" align="center"><strong>Programadores de actividades </strong></td>
    <td width="7%" rowspan="2" align="center"><strong>Empresa</strong></td>
  </tr>
  <tr>
  	<? for($numOrd = 1; $numOrd <= $maxOrdenadores; $numOrd++){ ?>
    	<td align="center">Ordernador <? echo $numOrd; ?></td>
	<? } ?>
	
	<? for($numPP = 1; $numPP <= $maxProgP; $numPP++){ ?>
   	<td width="12%" align="center">Programador <? echo $numPP; ?></td>
	<? } ?>
	
	<? for($numPA = 1; $numPA <= $maxProgA; $numPA++){ ?>
    	<td width="12%" align="center">Programador Act. <? echo $numPA; ?></td>
	<? } ?>
  </tr>
  <?
  while ($reg = mssql_fetch_array($cursor)) {
  	
	$cuantosOG = 0;
	$cuantosPP = 0;
	$cuantosPA = 0;
	$cuantasFilas = 0;
	
	//lista de los ordenadores del gasto
	$ogSql="SELECT O.* , U.nombre, U.apellidos, U.email ";	
	$ogSql=$ogSql." FROM GestiondeInformacionDigital.dbo.OrdenadorGasto O,  ";		
	$ogSql=$ogSql." HojaDeTiempo.dbo.Usuarios U ";		
	$ogSql=$ogSql." WHERE O.unidadOrdenador = U.unidad ";		
	$ogSql=$ogSql." AND O.id_proyecto = " . $reg[id_proyecto];
	$ogCursor = mssql_query($ogSql);
	$cuantosOG = mssql_num_rows($ogCursor);
	 
	//Listado de los programadores de Proyecto
	$prSql="select distinct P.unidad, P.progProyecto, U.nombre, U.apellidos, U.email  ";
	$prSql=$prSql." from HojaDeTiempo.dbo.Programadores P, HojaDeTiempo.dbo.Usuarios U ";
	$prSql=$prSql." where P.unidad = U.unidad ";
	$prSql=$prSql." and P.id_proyecto =" . $reg[id_proyecto];
	$prSql=$prSql." and P.progProyecto = 1" ;
	$prCursor = mssql_query($prSql);
	$cuantosPP = mssql_num_rows($prCursor);
	
	//Programadores de actividades
	//Listado de programadores
	$prActSql="select distinct P.unidad, P.progProyecto, U.nombre, U.apellidos, U.email  ";
	$prActSql=$prActSql." from HojaDeTiempo.dbo.Programadores P, HojaDeTiempo.dbo.Usuarios U ";
	$prActSql=$prActSql." where P.unidad = U.unidad ";
	$prActSql=$prActSql." and P.id_proyecto =" . $reg[id_proyecto];
	$prActSql=$prActSql." and P.progProyecto = 0" ;
	$prActCursor = mssql_query($prActSql);
	$cuantosPA = mssql_num_rows($prActCursor);
	
  ?>
  <tr valign="top" class="TxtTabla">
    <td width="5%">    <? echo ucwords(strtolower($reg[codigo])) . "." . ucwords(strtolower($reg[cargo_defecto]))  ; ?></td>
    <td>    <? echo ucwords(strtolower($reg[nombre])); ?></td>
    <td width="12%">	<? echo ucwords(strtolower($reg[apeDirector]))  . ", " . ucwords(strtolower($reg[nomDirector]))  ; ?></td>
    <td width="12%">    <? echo trim($reg[mailDirector]) . "@ingetec.com.co" ; ?></td>
    <td width="7%">    <? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo ucwords(strtolower($reg[apeCoordina])) . ", " . ucwords(strtolower($reg[nomCoordina]))  ; 
			}
			?></td>
    <td align="left">    <? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo trim($reg[mailCoordina]) . "@ingetec.com.co" ; 
			}
			?></td>
    
	<!-- Ordenadores del Gasto -->
	<? while ($ogReg = mssql_fetch_array($ogCursor)) { ?>
		<td width="12%"><? echo ucwords(strtolower($ogReg[apellidos])) . ", " . ucwords(strtolower($ogReg[nombre])) . " [" . $ogReg[email] . "@ingetec.com.co]" ?></td>
    <? } ?>
	<? for($iOrd = $cuantosOG; $iOrd < $maxOrdenadores; $iOrd++){ ?>
		<td width="12%">&nbsp;</td>
	<? } ?>
	
	<!-- Programadores Proyecto -->
	<? while ($prReg = mssql_fetch_array($prCursor)) { ?>
		<td width="12%"><? echo ucwords(strtolower($prReg[apellidos])) . ", " . ucwords(strtolower($prReg[nombre])) . " [" . $prReg[email] . "@ingetec.com.co]" ?></td>
	<? } ?>
	<? for($iProgP = $cuantosPP; $iProgP < $maxProgP; $iProgP++){ ?>
		<td width="12%">&nbsp;</td>
	<? } ?>
    
	<!-- Programadores Actividades -->
	
	<? while ($prActReg = mssql_fetch_array($prActCursor)) { ?>
		<td width="12%"><? echo ucwords(strtolower($prActReg[apellidos])) . ", " . ucwords(strtolower($prActReg[nombre])) . " [" . $prActReg[email] . "@ingetec.com.co]" ?></td>
	<? } ?>
	<? for($iProgA = $cuantosPA; $iProgA < $maxProgA; $iProgA++){ ?>
		<td width="12%">&nbsp;</td>
	<? } ?>
	<td width="7%"><?
	//Trae elnombre de la empresa
	$eSql="select * from empresas ";
	$eSql=$eSql." where idEmpresa =" . $reg[idEmpresa]; 
	$eCursor = mssql_query($eSql);
	if ($eReg=mssql_fetch_array($eCursor)) {
		echo $eReg[nombre] ;
	} else {
		echo "&nbsp;";
	}
	?></td>
  </tr>
  <? } ?>
</table>
</body>
</html>
