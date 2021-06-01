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

//Arreglo de Meses
$meses = array("Todos", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre" , "Diciembre");

/*
Proyecto
*/
$sql0 = " SELECT * FROM HojaDeTiempo.dbo.Proyectos WHERE id_proyecto = " . $cualPr;
$cursor0 = mssql_query($sql0);
if($reg0 = mssql_fetch_array($cursor0)){
	$nombreProyecto = ucwords(strtolower($reg0['nombre']));
}


/*
Log
*/
$sql1 = " SELECT A.*, B.nombre, B.apellidos
FROM HojaDeTiempo.dbo.RptFacturacionProyectos A, HojaDeTiempo.dbo.Usuarios B
WHERE A.unidad = B.unidad
AND A.id_proyecto = " . $cualPr;
$sql1 = $sql1 . " ORDER BY A.secReporte DESC ";
$cursor1 = mssql_query($sql1);


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
<!--

//-->

/*
Funciones para generar el reporte
*/
function generarRep(){
	document.form1.recarga.value = 2;
	document.form1.submit();
}//-->
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
    </table>
	</form>	</td>
  </tr>
</table>
<!-- Espacio -->
<!-- Resultados de la Consulta -->
<table width="100%"  cellspacing="1" class="fondo">
  <tr class="TituloTabla2">
    <td>Sec.</td>
    <td>Fecha de Reporte </td>
    <td>Persona que Gener&oacute; el reporte</td>
    <td>Mes</td>
    <td>A&ntilde;o</td>
    <td width="1%">&nbsp;</td>
  </tr>
  <? while($reg1 = mssql_fetch_array($cursor1)){ ?>
  <tr class="TxtTabla">
    <td><? echo $reg1['secReporte']; ?></td>
    <td><? echo date("m/d/Y H:i:s", strtotime($reg1['fechaReporte'])); ?></td>
    <td><? echo $reg1['unidad'] . " - " . ucwords(strtolower($reg1['apellidos'] . " " . $reg1['nombre'])); ?></td>
    <td><? echo $meses[$reg1['mesReporte']]; ?></td>
    <td><? echo $reg1['anioReporte']; ?></td>
    <td><a href="#" onClick="MM_openBrWindow('ReportesFact/<? echo $reg1['archivoReporte']; ?>','','scrollbars=yes,resizable=yes,width=600,height=600')"><img src="img/images/ver.gif" width="16" height="16" border="0"></a></td>
  </tr>
  <? } // Fin del While de la Consulta ?>
</table>
</body>
</html>
