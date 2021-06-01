<?php
session_start();

$fecha = date('Y-m-d');

echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=Reporte_Facturacion_Proyectos" . $fecha . ".xls");
echo "</head>";

//Validación de ingreso
include "funciones.php";
include "validaUsrBd.php";
//$conexion = conectar();

 $meses= array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$cDivisiones=$_GET['division'];
$cDepartamento=$_GET['departamento'];
$plstProyecto=$_GET['proyecto'];
$mes1=$_GET['mes'];
$anio1=$_GET['ano'];
//$fecha_final2=$_GET['fecha_final'];
//$fecha_inicial=$_GET['fecha_inicial'];
//$cantidad_meses=$_GET['cant_meses'];
//$meses=$_GET['meses'];
$mes2=$_GET['mes2'];

$anio2=$_GET['ano2'];


//////*******************************  ESTRUCTURACION DE LA FECHA


				$fecha_inicial=" ' ".$anio1."-".$mes1."-01";
				$fecha_final=" ' ".$anio2."-".$mes2;
						
	//echo "Fechas ".$fecha_inicial.$fecha_final."<br>";     
	
	   //obtener la cantidad de dias del mes de la segunda fecha, para componer la fecha de la consulta
				$fecha_inicial=" ' ".$anio1."-".$mes1."-01'";
				$fecha_final=" ' ".$anio2."-".$mes2."-20'";
//			    $fecha_final
			   $sql_fecha1="SELECT DAY(DATEADD(month,DATEDIFF(month, 0, ".$fecha_final.") + 1,-1)) as dias_totales ";
			   $cursor_fechaini=mssql_query($sql_fecha1);
			   if($datos_fecha_ini=mssql_fetch_array($cursor_fechaini));
			   {
				   $dias_total_inicial=$datos_fecha_ini[dias_totales];
			   }
			   //componemos la fecha final completa
			   $fecha_final2=" ' ".$anio2."-".$mes2."-".$dias_total_inicial." '";
			   
			   $fecha_inicial_excel=" ' 01 ".$meses[$mes1]." ".$anio1." '";
	    	   $fech_final_excel= " ' ".$dias_total_inicial." ".$meses[$mes2]." ".$anio2." '";
//echo "<br> fecha completa inicial".$fecha_inicial." final ".$fecha_final2."'";


				//OBTENEMOS LA CANTIDAD DE MESES QUE HAY ENTRE LA FECHA INICAL Y LA FINAL
				$sql_meses_fechas="select  DATEDIFF(mm,".$fecha_inicial.",".$fecha_final2.") as cant_meses";
				$cur_meses=mssql_query($sql_meses_fechas);
				if($datos_meses=mssql_fetch_array($cur_meses))
				{
					$cantidad_meses=$datos_meses[cant_meses];
				}
				//obtenemos la cantidad de meses y le aumentamos 1, para que me imprima las columnas de los meses con el mes final
				$cantidad_meses+=1;

/////*************************************


/*
//Recupera Los datos de la búsqueda
$lstTematica = $_GET['t'];
$lstLote = $_GET['l'];
$lstRev = $_GET['r'];
$nombrePlano = $_GET['n'];
$descrPlano = $_GET['d'];
/*echo "tema".$lstTematica."<br>";
echo "lota".$lstLote."<br>";
echo "rev".$lstRev."<br>";
echo "nomb".$nombrePlano."<br>";
echo "desc".$descrPlano."<br>";*/

//Búsqueda de los registros

/*
$sqlA = " SELECT  A.idTematicaPadre, A.nombreTematica, B.numeroLote, B.nombreLote, 
	C.numeroPlano, C.descripcionPlano, 	D.*, D1.nombreOriginaMod, D2.nombreClaseMod, E.letraRevisionInterna
	FROM Tematicas A, LotesTrabajo B, Planos C, Revisiones D, 
	TipoOriginaModificacion D1, TipoClaseModificacion D2, ConsRevisionesInternas E
	WHERE A.idProyecto = B.idProyecto
	AND A.etapaProyecto = B.etapaProyecto
	AND A.idTematica = B.idTematica
	AND B.idProyecto = C.idProyecto
	AND B.etapaProyecto = C.etapaProyecto
	AND B.idTematica = C.idTematica
	AND B.idLote = C.idLote
	AND C.idProyecto = D.idProyecto
	AND C.etapaProyecto = D.etapaProyecto
	AND C.idTematica = D.idTematica
	AND C.idLote = D.idLote
	AND C.idPlano = D.idPlano 
	AND D.idOriginaMod = D1.idOriginaMod
	AND D.idClaseMod = D2.idClaseMod
	AND D.idRevisionInterna = E.idRevisionInterna ";
$sqlA = $sqlA . " AND A.idProyecto = " . $_SESSION["phsProyecto"];
$sqlA = $sqlA . " AND A.etapaProyecto = " . $_SESSION["phsEtapa"];

//Temáticas
if($lstTematica){
	$sqlA = $sqlA . " AND A.idTematica = " . $lstTematica . " ";
}
//Lotes
if($lstLote){
	$sqlA = $sqlA . " AND B.idLote = " . $lstLote . " ";
}
//Revisiones
if($lstRev){
	//$sqlA = $sqlA . " AND D.numeroRevision = " . $lstRev . " ";
	//$sqlA = $sqlA . " AND E.letraRevisionInterna = '" . $lstRev . "' ";
	$sqlA = $sqlA . " AND D.idRevisionInterna = '" . $lstRev . "' ";
} 

//Nombre o Descripción de Plano
if(trim($nombrePlano) != "" and trim($descrPlano) == ""){
	$sqlA = $sqlA . " AND C.numeroPlano LIKE '%" . $nombrePlano . "%' ";
}
else if(trim($nombrePlano) == "" and trim($descrPlano) != ""){
	$sqlA = $sqlA . " AND C.descripcionPlano LIKE '%" . $descrPlano . "%' ";
}
else if(trim($nombrePlano) != "" and trim($descrPlano) != ""){
	$sqlA = $sqlA . " AND ( C.descripcionPlano LIKE '%" . $descrPlano . "%' OR C.numeroPlano LIKE '%" . $nombrePlano . "%' ) ";
}

//Ordenamiento
$sqlA = $sqlA . " AND D.archivoDWG NOT LIKE '%.DWG'
				  ORDER BY A.idTematicaPadre ";
//FIN BÚSQUEDA


$cursorA = mssql_query($sqlA);
//echo $sqlA;
*/


/*echo ("<script>window.close();</script>");
*/

?>

<html>
<head>
<title>::: Sistema de Gesti&oacute;n de Informaci&oacute;n en L&iacute;nea :::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK REL="stylesheet" HREF="http://www.ingetec.com.co/enlinea/mitu/css/estilo.css" TYPE="text/css">
<style type="text/css">
<!--
.Estilo2 {font-size: 8}
-->
</style>
</head>

<body  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="fondo" >

<table width="100%"  border="1" cellspacing="1">
  <tr>
    <td align="center" valign="middle" class="LetraIngetecForm"><img src="http://www.ingetec.com.co/enlinea/mitu/images/Ingetec.GIF" width="115" height="19" align="absmiddle"></td>
    <td colspan="5" align="center" valign="middle" class="TituloFormato">Reporte Facturacion por Proyectos</td>
    <td colspan="1" align="center" valign="middle" class="LetraFormato">&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="9">&nbsp; </td>
  </tr>
  
    <tr>
  	<td>Divisi&oacute;n</td>
    	<td><?php
			$sqlDiv = "select *from HojaDeTiempo.dbo.Divisiones div where estadoDiv = 'A' and id_division=".$cDivisiones;
			$qryDiv = mssql_query ( $sqlDiv );			
			while( $rowDiv = mssql_fetch_array( $qryDiv ) )
			{ 
				echo $rowDiv[nombre];			
			}
			 ?>
	     </td>
    <td colspan="5">&nbsp;</td>
  </tr>
  
  <tr>
  	<td >Fecha</td>    
   	<td>Desde</td>
    				
				
    <td align="right"><?php  echo $fecha_inicial_excel; ?></td>
  	<td>Hasta</td> 
    <td align="right"><?php echo $fech_final_excel; ?></td>    
    <td colspan="2">&nbsp;</td>           
  </tr>
  
  <tr>
  	<td colspan="7">&nbsp;</td>  	
  </tr>
  
  <tr>
  	<td>Proyecto</td>
	<td colspan="2">&nbsp;</td>    
    <td align="center" colspan="<?php echo $cantidad_meses; ?>">Meses</td>    
  </tr>
  <tr>
  	<td>&nbsp;</td>
    <td>Departamento</td>
 
    <?php
		for($a=0;$a<=$cantidad_meses;$a++)
		{
	?>
    <td ><?php echo $meses[$a];  ?> </td>
     <?php
		}
	 ?>   
  </tr>
  
  <tr>
  	<td>&nbsp;</td>
  	<td colspan="2">&nbsp;</td>    
  	<td >valor</td>    
  	<td >valor</td>        
  	<td >valor</td>    
  	<td >valor</td>            
  </tr>
  
  <tr>
  	<td>&nbsp;</td>
  	<td>Total</td> 
  	<td>&nbsp;</td>
  	<td>Total ene</td>           
  	<td>Total feb</td>    
  	<td>Total feb</td>    
  	<td>Total feb</td>            
  </tr>
 <!-- 
  <tr align="center" valign="middle">
    <td ><strong>CONSECUTIVO</strong></td>
    <td colspan="2"><strong>DOCUMENTO</strong></td>
    <td rowspan="2"><strong>REVISI&Oacute;N<br>
    No. </strong></td>
    <td rowspan="2"><strong>FECHA DE <br>
  APROBACI&Oacute;N </strong></td>
    <td rowspan="2"><strong>ORIGEN</strong></td>
    <td colspan="2"><strong>DEVOLUTIVO</strong></td>
    <td rowspan="2"><strong>OBSERVACIONES</strong></td>
  </tr>
  <tr>
    <td align="center" valign="middle"><strong>IDENTIFICACI&Oacute;N</strong></td>
    <td align="center" valign="middle"><strong>T&Iacute;TULO</strong></td>
    <td align="center" valign="middle"><strong>SI</strong></td>
    <td align="center" valign="middle"><strong>NO</strong></td>
  </tr>  

  <? //while($regA = mssql_fetch_array($cursorA)){ ?>
  <tr>
  	<td><?php echo $cDivisiones, $cDepartamento, $plstProyecto, $mes1, $anio1, $mes2, $anio2, $fecha_final2,$fecha_inicial,$cantidad_meses,$meses; ?></td>

    <td align="center">X</td>
    <td>&nbsp;</td>
  </tr>
  <?// } ?>
    -->
</table>

</body>
</html>