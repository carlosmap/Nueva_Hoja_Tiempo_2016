<?
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ReporteDirectoresProyectos" . date("Ymd_Hi") .  ".xls");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

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
$sql=$sql." 	and P.codigo <> 'PER' and P.codigo <> 'SAN' and P.codigo <> 'VAC')   ";
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
    <td colspan="8"><strong>::: Directores y Coordinadores de Proyectos Hoja de Tiempo::: </strong></td>
  </tr>
  <tr>
    <td width="5%"><strong>C&oacute;digo</strong></td>
    <td><strong>Proyecto</strong></td>
    <td width="12%"><strong>Director/Coordinador</strong></td>
    <td width="7%"><strong>Extensi&oacute;n</strong></td>
    <td width="12%"><strong>Ordenadores del gasto </strong></td>
    <td width="12%"><strong>Programadores de proyecto </strong></td>
    <td width="12%"><strong>Programadores de actividades </strong></td>
    <td width="7%"><strong>Empresa</strong></td>
  </tr>
  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
  <tr valign="top" class="TxtTabla">
    <td width="5%"><? echo ucwords(strtolower($reg[codigo])) . "." . ucwords(strtolower($reg[cargo_defecto]))  ; ?><br />
        <?
		  //Trae los cargos adicionales del proyecto
		  $cSql="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		  $cSql=$cSql." WHERE id_proyecto =" . $reg[id_proyecto];
		  $cCursor = mssql_query($cSql);
		  $y=0;
		  while ($cReg=mssql_fetch_array($cCursor)) {
				if ($y==0)  {
					echo "<br /> Cargos adicionales <br />";
				}
		  		echo $cReg[cargos_adicionales] . "<br>";
				$y=$y+1;
		  }
		 
		  ?>
    </td>
    <td><? echo ucwords(strtolower($reg[nombre]))  ; ?></td>
    <td width="12%"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td><? echo ucwords(strtolower($reg[apeDirector]))  . ", " . ucwords(strtolower($reg[nomDirector])) . "<br>" . trim($reg[mailDirector]) . "@ingetec.com.co" ; ?></td>
        </tr>
        <tr>
          <td><? echo ucwords(strtolower($reg[apeCoordina])) . ", " . ucwords(strtolower($reg[nomCoordina])) . "<br>" . trim($reg[mailCoordina]) . "@ingetec.com.co" ; ?></td>
        </tr>
    </table></td>
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
      <?
		//lista de los ordenadores del gasto
		$ogSql="SELECT O.* , U.nombre, U.apellidos ";	
		$ogSql=$ogSql." FROM GestiondeInformacionDigital.dbo.OrdenadorGasto O,  ";		
		$ogSql=$ogSql." HojaDeTiempo.dbo.Usuarios U ";		
		$ogSql=$ogSql." WHERE O.unidadOrdenador = U.unidad ";		
		$ogSql=$ogSql." AND O.id_proyecto = " . $reg[id_proyecto];
  		$ogCursor = mssql_query($ogSql);

		?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <? while ($ogReg=mssql_fetch_array($ogCursor)) { ?>
        <tr>
          <td><? echo ucwords(strtolower($ogReg[apellidos])) . ", " . ucwords(strtolower($ogReg[nombre]))   ; ?></td>
        </tr>
        <? } ?>
    </table></td>
    <td width="12%">
      <?
		//Listado de programadores
		$prSql="select distinct P.unidad, P.progProyecto, U.nombre, U.apellidos  ";
		$prSql=$prSql." from HojaDeTiempo.dbo.Programadores P, HojaDeTiempo.dbo.Usuarios U ";
		$prSql=$prSql." where P.unidad = U.unidad ";
		$prSql=$prSql." and P.id_proyecto =" . $reg[id_proyecto];
		$prSql=$prSql." and P.progProyecto = 1" ;
  		$prCursor = mssql_query($prSql);
		?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <? while ($prReg=mssql_fetch_array($prCursor)) { ?>
        <tr>
          <td><? echo ucwords(strtolower($prReg[apellidos])) . ", " . ucwords(strtolower($prReg[nombre]))   ; ?></td>
        </tr>
        <? } ?>
    </table></td>
    <td width="12%"><?
		//Listado de programadores
		$prSql="select distinct P.unidad, P.progProyecto, U.nombre, U.apellidos  ";
		$prSql=$prSql." from HojaDeTiempo.dbo.Programadores P, HojaDeTiempo.dbo.Usuarios U ";
		$prSql=$prSql." where P.unidad = U.unidad ";
		$prSql=$prSql." and P.id_proyecto =" . $reg[id_proyecto];
		$prSql=$prSql." and P.progProyecto = 0" ;
  		$prCursor = mssql_query($prSql);
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <? while ($prReg=mssql_fetch_array($prCursor)) { ?>
          <tr>
            <td><? echo ucwords(strtolower($prReg[apellidos])) . ", " . ucwords(strtolower($prReg[nombre]))   ; ?></td>
          </tr>
          <? } ?>
      </table></td>
    <td width="7%">
      <? 
		//Trae elnombre de la empresa
		$eSql="select * from empresas ";
		$eSql=$eSql." where idEmpresa =" . $reg[idEmpresa]; 
		$eCursor = mssql_query($eSql);
		if ($eReg=mssql_fetch_array($eCursor)) {
			echo $eReg[nombre] ;
		}
		?>
    </td>
  </tr>
  <? } ?>
</table>
</body>
</html>
