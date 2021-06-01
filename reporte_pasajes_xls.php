<?
session_start();
echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=Reporte_Solicitud_Pasajes_" . date('Y-m-d') . ".xls");
echo "</head>";

//Validación de ingreso
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//Conexión a la Base de Datos
//include("../enlaceBD.php");
//$conexion = conectar();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reportes de los Proyectos</title>
<style type="text/css">
.TituloTabla2 td {
	font-weight: bold;
}
.TituloFormato {
	font-weight: bold;
	font-size: 24px;
}
</style>
</head>


<?php 
//Recupera Los datos de la búsqueda
$pMes = $_GET['pMes'];
$pAno = $_GET['pAno'];

//Seleccionar los registros de SolicitudPasajes
$sql2="Select S.*, A.nombre nombreAgencia, E.nombre nombreempresaA, U.nombre, U.apellidos ";
$sql2= $sql2. " from GestiondeInformacionDigital.dbo.SolicitudPasajes S, ";
$sql2= $sql2. " GestiondeInformacionDigital.dbo.Agencias A, GestiondeInformacionDigital.dbo.EmpresaAerea E, ";
$sql2= $sql2. " HojaDeTiempo.dbo.Usuarios U ";
$sql2= $sql2. " where S.codAgencia *= A.codAgencia ";
$sql2= $sql2. " and S.codEmpresa *= E.codEmpresa ";
$sql2= $sql2. " and S.unidad = U.unidad ";
$sql2= $sql2. " AND S.id_proyecto  = " . $_SESSION["sesProyReportes"] ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql2= $sql2. " AND month(S.fechaSolicitud) = month(getdate()) ";
	$sql2= $sql2. " AND year(S.fechaSolicitud) = year(getdate()) ";
}
else {
	$sql2= $sql2. " AND month(S.fechaSolicitud) = " . $pMes;
	$sql2= $sql2. " AND year(S.fechaSolicitud) =  " . $pAno;
}
$cursor2 = mssql_query($sql2);

?>
<body>


	<table width="100%" border="0" cellspacing="1" cellpadding="0">
     <tr>
       <td colspan="12" align="center" valign="middle" class="TituloFormato">Reporte Solicitud de Pasajes</td>

      </tr>

      <tr class="TituloTabla2">
        <td colspan="2">Solicitud</td>
        <td width="249" rowspan="2">Usuario que realiz&oacute; la solicitud </td>
        <td width="57" rowspan="2">C&oacute;digo</td>
        <td width="57" rowspan="2">Cargo</td>

        <td width="120"  rowspan="2">Comentario quien autoriza solicitud </td>
        <td width="125"  rowspan="2">Comentario jefe que autoriza firma 2  </td>
        <td  width="125" rowspan="2">Comentario agente de viajes </td>
        <td width="82"  rowspan="2">Comentario auditoria  </td>

        <td width="81" rowspan="2">Ruta Ida /
          Regreso </td>
        <td width="96" rowspan="2">Fecha Salida Ida / Regreso </td>
        <td width="90" rowspan="2">Hora Salida Ida / Regreso </td>
<!--
        <td colspan="4">Proceso Solicitud</td>
-->


        </tr>
      <tr class="TituloTabla2">
        <td width="80">No.</td>
        <td width="121">Fecha</td>
<!--
        <td width="5%">Finalizada Usuario </td>
        <td width="5%">Jefe Inmediato </td>
        <td width="5%">Jefe 2da Aut</td>
        <td width="5%">Servicios Generales </td>
-->
       </tr>
  <?
	  while ($reg2=mssql_fetch_array($cursor2)) {
	  ?>
      <tr class="TxtTabla">
        <td width="80"><? echo $reg2[secuencia]; ?></td>
        <td width="121">
		<? echo date("M d Y ", strtotime($reg2[fechaSolicitud])); ?>
		</td>
        <td width="249">
		  <?
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sqlU="Select * from usuarios where unidad =" . $reg2[unidad];
		$cursorU = mssql_query($sqlU);
		if ($regU=mssql_fetch_array($cursorU)) {
			echo "[" .$regU[unidad] . "] " . ucwords(strtolower($regU[nombre])) . " " . ucwords(strtolower($regU[apellidos])) ;
		}
		?></td>
        <td width="57"><? echo $reg2[codigo]; ?></td>
        <td width="57"><? echo $reg2[cargo]; ?></td>
		<td ><? echo $reg2[comentaJefe]; ?></td>
        <td ><? echo $reg2[comentaJefe2]; ?></td>
        <td ><? echo $reg2[comentaServiciosGen]; ?></td>
        <td ><? echo $reg2[comentaAuditoria]; ?></td>
        <td>
		<? 
		//	$val = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
		//	$val_remplazar = array("á", "´é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú");
//str_replace($val_remplazar, $val, $reg2[rutaIda]) str_replace($val_remplazar, $val, $reg2[rutaRegreso]
			echo  $reg2[rutaIda]." /<br> ".$reg2[rutaRegreso]; 
			?>
		</td>
        <td width="96">
		<? echo date("M d Y ", strtotime($reg2[fechaIda])) . " /<br> " . date("M d Y ", strtotime($reg2[fechaRegreso])); ?>
		</td>
        <td width="90">
		<? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg2[jornadaIda] == "1") {
			$miJornadaIda = "AM";
		}
		if ($reg2[jornadaIda] == "2") {
			$miJornadaIda = "PM";
		}
		
		if ($reg2[jornadaRegreso] == "1") {
			$miJornadaReg = "AM";
		}
		if ($reg2[jornadaRegreso] == "2") {
			$miJornadaReg = "PM";
		}
		
		?>
		<? echo $reg2[horaIda] . ":" . $reg2[minutosIda] . " " . $miJornadaIda . " /<br>" . $reg2[horaRegreso] . ":" . $reg2[minutosRegreso] . " " . $miJornadaReg; ?>
		</td>

	  <?
	  }
	  ?>
	</table>

</body>
</html>