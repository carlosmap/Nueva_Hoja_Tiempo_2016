<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//09Mar2011
//Traer el resumen por día de lo que se hizo

$sql="SELECT H.*, A.nombre ";
$sql=$sql." FROM Horas H, Actividades A ";
$sql=$sql." where H.id_proyecto = A.id_proyecto ";
$sql=$sql." and H.id_actividad = A.id_actividad ";
$sql=$sql." and H.id_proyecto = " . $cualProyecto;
$sql=$sql." and MONTH(H.fecha)=".$mesAut;
$sql=$sql." and YEAR(H.fecha)=".$anoAut;
$sql=$sql." and H.unidad =".$cualUnidad;
if (trim($pCargo) != "") {
	$sql=$sql." and H.cargo = '".$pCargo."'";
}
$sql=$sql." order by H.fecha ";
$cursor = mssql_query($sql);

?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo - Resumen</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo - Resumen </td>
  </tr>
</table>



<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr class="TituloTabla2">
    <td>Actividad</td>
    <td width="8%">Fecha</td>
    <td width="10%">Cantidad Horas </td>
    <td>Resumen</td>
  </tr>
	 <?
  while ($reg=mssql_fetch_array($cursor)) {
  ?>
  <tr class="TxtTabla">
    <td><? echo $reg[nombre]; ?></td>
    <td width="8%"><? echo date("M d Y ", strtotime($reg[fecha])); ?></td>
    <td width="10%" align="right"><? echo $reg[horas_registradas]; ?></td>
    <td><? echo $reg[resumen_trabajo]; ?></td>
  </tr>
  <? } ?>
</table>
	</td>
  </tr>
</table>

</body>
</html>
