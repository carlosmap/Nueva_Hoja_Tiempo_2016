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

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" >
<table width="100%"  border="1" cellspacing="1" cellpadding="0">
  <tr>
    <td colspan="11"><strong>::: Directores y Coordinadores de Proyectos Hoja de Tiempo ::: </strong></td>
  </tr>
  <tr>
    <td width="5%"><strong>C&oacute;digo</strong></td>
    <td><strong>Proyecto</strong></td>
    <td width="12%"><strong>Director</strong></td>
    <td width="12%"><strong>Mail Director</strong></td>
    <td width="7%"><strong>Coordinador</strong></td>
    <td width="7%"><strong>Mail Coordinador</strong></td>
    <td width="7%"><strong>Extensi&oacute;n</strong></td>
    <td width="12%"><strong>Ordenadores del gasto </strong></td>
    <td width="12%"><strong>Programadores de proyecto </strong></td>
    <td width="12%"><strong>Programadores de actividades </strong></td>
    <td width="7%"><strong>Empresa</strong></td>
  </tr>
  <?
  while ($reg=mssql_fetch_array($cursor)) {
  	
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
	
	$cuantasFilas = max($cuantosOG, $cuantosPP, $cuantosPA);
  
  ?>
  <tr valign="top" class="TxtTabla">
    <td width="5%">
      <table width="100%"  cellspacing="1">
        <tr>
          <td><? echo ucwords(strtolower($reg[codigo])) . "." . ucwords(strtolower($reg[cargo_defecto]))  ; ?></td>
        </tr>
        <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
		<tr>
          <td><font color="#FFFFFF"><? echo ucwords(strtolower($reg[codigo])) . "." . ucwords(strtolower($reg[cargo_defecto]))  ; ?></font></td>
        </tr>
		<? } ?>
    </table>    </td>
    <td>
      <table width="100%"  cellspacing="1">
        <tr>
          <td><? echo ucwords(strtolower($reg[nombre])); ?></td>
        </tr>
        <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
		<tr>
          <td><font color="#FFFFFF"><? echo ucwords(strtolower($reg[nombre])); ?></font></td>
        </tr>
		<? } ?>
      </table></td>
    <td width="12%">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? echo ucwords(strtolower($reg[apeDirector]))  . ", " . ucwords(strtolower($reg[nomDirector]))  ; ?></td>
  </tr>
  <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
  <tr>
    <td><font color="#FFFFFF"><? echo ucwords(strtolower($reg[apeDirector]))  . ", " . ucwords(strtolower($reg[nomDirector]))  ; ?></font></td>
  </tr>
  <? } ?>
</table>	</td>
    <td width="12%">
    <table width="100%"  cellspacing="1">
      <tr>
        <td><? echo trim($reg[mailDirector]) . "@ingetec.com.co" ; ?></td>
      </tr>
      <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
	  <tr>
        <td><font color="#FFFFFF"><? echo trim($reg[mailDirector]) . "@ingetec.com.co" ; ?></font></td>
      </tr>
	  <? } ?> 
    </table>	</td>
    <td width="7%">
    <table width="100%"  cellspacing="1">
      <tr>
        <td><? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo ucwords(strtolower($reg[apeCoordina])) . ", " . ucwords(strtolower($reg[nomCoordina]))  ; 
			}
			?></td>
      </tr>
      <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
	  <tr>
        <td><font color="#FFFFFF">
          <? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo ucwords(strtolower($reg[apeCoordina])) . ", " . ucwords(strtolower($reg[nomCoordina]))  ; 
			}
			?>
        </font></td>
      </tr>
	  <? } ?>
    </table>	</td>
    <td width="7%" align="left">
    <table width="100%"  cellspacing="1">
      <tr>
        <td><? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo trim($reg[mailCoordina]) . "@ingetec.com.co" ; 
			}
			?></td>
      </tr>
      <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
	  <tr>
        <td><font color="#FFFFFF">
          <? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo trim($reg[mailCoordina]) . "@ingetec.com.co" ; 
			}
			?>
        </font></td>
      </tr>
	  <? } ?>
    </table>	</td>
    <td width="7%" align="center">
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td><? echo $reg[extDir]; ?></td>
        </tr>
        <tr>
          <td><? echo $reg[extCoordina]; ?></td>
        </tr>
    </table></td>
    <td width="12%">
      
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <? while ($ogReg = mssql_fetch_array($ogCursor)) { ?>
        <tr>
          <td><? echo ucwords(strtolower($ogReg[apellidos])) . ", " . ucwords(strtolower($ogReg[nombre]))   ; ?></td>
          <td><? echo $ogReg[email] . "@ingetec.com.co" ; ?></td>
        </tr>
        <? } ?>
    </table></td>
    <td width="12%">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <? while ($prReg = mssql_fetch_array($prCursor)) { ?>
        <tr>
          <td><? echo ucwords(strtolower($prReg[apellidos])) . ", " . ucwords(strtolower($prReg[nombre]))   ; ?></td>
          <td><? echo $prReg[email] . "@ingetec.com.co"  ; ?></td>
        </tr>
        <? } ?>
    </table></td>
    <td width="12%">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	  <? while ($prActReg = mssql_fetch_array($prActCursor)) { ?>
	  <tr>
		<td><? echo ucwords(strtolower($prActReg[apellidos])) . ", " . ucwords(strtolower($prActReg[nombre]))   ; ?></td>
		<td><? echo $prActReg[email] . "@ingetec.com.co"  ; ?></td>
	  </tr>
	  <? } ?>
    </table></td>
    <td width="7%">
      <table width="100%"  cellspacing="1">
        <tr>
		<?
		//Trae elnombre de la empresa
		$eSql="select * from empresas ";
		$eSql=$eSql." where idEmpresa =" . $reg[idEmpresa]; 
		$eCursor = mssql_query($eSql);
		if ($eReg=mssql_fetch_array($eCursor)) {
			$nombreEmpresa = $eReg[nombre] ;
		}
		?>
          <td><? echo $nombreEmpresa; ?></td>
        </tr>
        <? for($i = 1; $i < $cuantasFilas; $i++){ ?>
		<tr>
          <td><font color="#FFFFFF"><? echo $nombreEmpresa; ?></font></td>
        </tr>
		<? } ?>
      </table>
</td>
  </tr>
  <? } ?>
</table>
</body>
</html>
