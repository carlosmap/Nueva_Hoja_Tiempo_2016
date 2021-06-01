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
    <td align="center" valign="middle" class="LetraIngetecForm"  width="115" height="49"><img src="http://www.ingetec.com.co/NuevaHojaTiempo/imagenes/logoIngetec.gif" width="115" height="49" align="absmiddle"></td>
    <td colspan="5" align="center" valign="middle" class="TituloFormato">Reporte Facturaci&oacute;n por Proyectos</td>
    <td colspan="<?php echo $cantidad_meses-2; ?>" align="center" valign="middle" class="LetraFormato">&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="<?php echo 9+($cantidad_meses-6) ?>">&nbsp; </td>
  </tr>
  
    <tr>
  	<td>Divisi&oacute;n</td>
    	<td><?php
			$sqlDiv = "select *from HojaDeTiempo.dbo.Divisiones div where estadoDiv = 'A' and id_division=".$cDivisiones;
			$qryDiv = mssql_query ( $sqlDiv );			
			while( $rowDiv = mssql_fetch_array( $qryDiv ) )
			{ 
				echo strtoupper($rowDiv[nombre]);			
			}
			 ?>
	     </td>
    <td colspan="<?php echo 5+($cantidad_meses-4); ?>">&nbsp;</td>
  </tr>
  
  <tr>
  	<td >Fecha</td>    
   	<td>Desde</td>
    				
				
    <td align="right"><?php  echo $fecha_inicial_excel; ?></td>
  	<td>Hasta</td> 
    <td align="right"><?php echo $fech_final_excel; ?></td>    
    <td colspan="<?php echo $cantidad_meses-2 ?>">&nbsp;</td>           
  </tr>
  <!--
  <tr>
  	<td colspan="<?php //echo 7+($cantidad_meses-4) ?>">&nbsp;</td>  	
  </tr>
  --->

<!-- ///////////////////////////////////**************************************** -->
<?php 	   
			   //Trae la información del proyecto
				$sql_proyecto=" select distinct A.id_proyecto, B.nombre nomProyecto
					from horas A, Proyectos B, Usuarios C, Departamentos D, Divisiones E
					where A.id_proyecto = B.id_proyecto
					and A.unidad = C.unidad
					and C.id_departamento = D.id_departamento
					and D.id_division = E.id_division
					and D.id_division =".$cDivisiones."		
					and A.fecha between ".$fecha_inicial." and ".$fecha_final2;
					
				if(trim($cDepartamento)!="")
				{
					$sql_proyecto=$sql_proyecto."and D.id_departamento=".$cDepartamento;
				}
				if(trim($plstProyecto)!="")
				{
					$sql_proyecto=$sql_proyecto."and B.id_proyecto=".$plstProyecto;
				}
				$sql_proyecto=$sql_proyecto."order by B.nombre";		
//--and MONTH(A.fecha)=6
//--and YEAR(A.fecha)=2012 			
				$cursorProy2 = mssql_query($sql_proyecto);

				while ($regProy2=mssql_fetch_array($cursorProy2)) 
				{
					
								   //Trae la información del departamento que tiene a cargo
							$sqlDPTO="select distinct C.id_departamento, D.nombre nomDepartamento
								from horas A, Proyectos B, Usuarios C, Departamentos D, Divisiones E
								where A.id_proyecto = B.id_proyecto
								and A.unidad = C.unidad
								and C.id_departamento = D.id_departamento
								and D.id_division = E.id_division	
								and D.id_division =".$cDivisiones."							
								and A.fecha between ".$fecha_inicial." and ".$fecha_final2."
								and A.id_proyecto = ".$regProy2[id_proyecto]." ";																
								if($cDepartamento!="")
								{
									$sqlDPTO=$sqlDPTO." and D.id_departamento = ".$cDepartamento;
								}
								
								$sqlDPTO=$sqlDPTO." ORDER BY D.nombre";

								$cursorDpto=mssql_query($sqlDPTO);	
								
								$total_proyecto=0;	 //valor tofal de lo facturado en cada proyecto
//						$valores_facturacion[0][0]= $cantidad_meses;  

								$num_filas=mssql_num_rows($cursorDpto);
								$num_columnas=$cantidad_meses;
								$fila=0;			
								$columna=0;
						
										
										
			   	?>
                

  
  <tr>
  	<td>Proyecto</td>
	<td colspan="2">&nbsp; <?php echo $regProy2[nomProyecto]; ?> [ <?php echo $regProy2[id_proyecto]; ?>]</td>   
     
    <?php
									$mes11=$mes1;
									$anio11=$anio1;
									for ($m=1; $m<=$cantidad_meses; $m++) 				
									{ 
										if($mes11==12)
										{
		
	?>
    										<td align="center"><? echo substr($meses[$mes11],0,3)."<br>".$anio11;?></td>
     <?php
											$mes11=1;
											$anio11++;
										}
										else
										{	
		?>				
										 <td align="center"><? echo substr($meses[$mes11],0,3)."<br>".$anio11;?></td>                    
                <? 												
										 $mes11++;
										}	 
									}
									$mes11=$mes1;
									$anio11=$anio1;									
				
	 ?>    

  </tr>
  <tr>

    <?php 
								$num_filas=mssql_num_rows($cursorDpto);
								$num_columnas=$cantidad_meses;
								$fila=0;			
								$columna=0;
			while ($regDpto=mssql_fetch_array($cursorDpto)) 
			{
							$mes11=$mes1;
							$anio11=$anio1;
	 ?>
  	<td>&nbsp;</td>
    <td>Departamento</td>     
    <td><?php echo $regDpto[nomDepartamento];  ?></td>

<?php    
       for ($m=1; $m<=$cantidad_meses; $m++) 				
							{
								?>
                               	    <td align="right">
                                <?php 
								
									$sqlvFact="select SUM (t.vlrFacturado) as valor_facturado from(
									Select  D.id_departamento, Z.nombre nomDiv, Z.id_division,
									H.id_proyecto, P.nombre nomProyecto, D.nombre nom_departamento,  sum(H.horas_registradas) hFacturadas, A.salarioBase,
									P.codigo, ((A.salarioBase / 185) * sum(H.horas_registradas)) vlrFacturado
									
									from Horas H, Proyectos P, Asignaciones A, Usuarios U, Departamentos D, Divisiones Z, Categorias C
									
									where H.id_proyecto = P.id_proyecto and H.id_proyecto = A.id_proyecto and H.id_actividad = A.id_actividad
									and H.unidad = A.unidad and H.clase_tiempo = A.clase_tiempo
									and H.localizacion = A.localizacion and H.cargo = (P.codigo + A.cargo) 
									and MONTH(H.fecha)= MONTH(A.fecha_inicial)
									and YEAR(H.fecha)= YEAR(A.fecha_inicial)
									and H.unidad = U.unidad
									and U.id_departamento = D.id_departamento
									and D.id_division = Z.id_division
									and U.id_categoria = C.id_categoria ";
									$sqlvFact=$sqlvFact." and D.id_division =".$cDivisiones;
									$sqlvFact=$sqlvFact." and D.id_departamento = ".$regDpto[id_departamento];
									$sqlvFact=$sqlvFact." and MONTH(H.fecha) = ".$mes11;
									$sqlvFact=$sqlvFact." and YEAR(H.fecha)= ".$anio11;
									$sqlvFact=$sqlvFact." and H.id_proyecto =".$regProy2[id_proyecto];
									$sqlvFact=$sqlvFact." group by H.unidad, U.nombre, U.apellidos, D.nombre, D.id_departamento, Z.nombre, Z.id_division,
									 H.id_proyecto, P.nombre, H.clase_tiempo, A.salarioBase, U.id_categoria, C.nombre, P.codigo, P.cargo_defecto
									,H.cargo							
									
									UNION						
								
									Select D.id_departamento,Z.nombre nomDiv, Z.id_division,
									H.id_proyecto, P.nombre nomProyecto, D.nombre nom_departamento, sum(H.horas_registradas) hFacturadas, A.salarioBase,
									P.codigo, ((A.salarioBase / 185) * sum(H.horas_registradas)) vlrFacturado
									
									from Horas H, Proyectos P, Asignaciones A, Usuarios U, Departamentos D, Divisiones Z, Categorias C
									
									where H.id_proyecto = P.id_proyecto
									and H.id_proyecto = A.id_proyecto
									and H.id_actividad = A.id_actividad
									and H.unidad = A.unidad
									and H.clase_tiempo = A.clase_tiempo
									and H.localizacion = A.localizacion
									and H.cargo = (P.codigo + A.cargo)
									AND H.id_proyecto in (42, 48, 71, 61,65,60, 63, 62, 64, 56)
									and H.unidad = U.unidad
									and U.id_departamento = D.id_departamento
									and D.id_division = Z.id_division
									and U.id_categoria = C.id_categoria ";
									
								$sqlvFact=$sqlvFact." and D.id_division = ".$cDivisiones;
								$sqlvFact=$sqlvFact." and MONTH(H.fecha) = ".$mes11;
								$sqlvFact=$sqlvFact." and YEAR(H.fecha)= ".$anio11;
								$sqlvFact=$sqlvFact." and D.id_departamento=".$regDpto[id_departamento];
								$sqlvFact=$sqlvFact." and H.id_proyecto =".$regProy2[id_proyecto];
								$sqlvFact=$sqlvFact." group by H.unidad, U.nombre, U.apellidos, D.nombre, D.id_departamento, Z.nombre, Z.id_division, H.id_proyecto, P.nombre, H.clase_tiempo, A.salarioBase, U.id_categoria, C.nombre, P.codigo, P.cargo_defecto, H.cargo
								 
								 ) as t group by t.nom_departamento,t.nomProyecto";
								$cursor_vFact=mssql_query($sqlvFact);
								
								$ban=0; //bandera para determinar si l aconsulta ha traido algun valor o no
								while($datos_sqlvFact=mssql_fetch_array($cursor_vFact))
								{

										echo number_format($datos_sqlvFact[valor_facturado], 2, ",", ".");
										
										//almacenamos en una matriz los valores de la facturacion, para sumarlas mas adelante
										
										$matriz_valores[$fila][$columna]=$datos_sqlvFact[valor_facturado]; 
								//		echo "<br>".$matriz_valores[$fila][$columna];
								//		echo "<br>C: $columna - F: $fila";
										$columna++;
										$ban=1;
										
									//	echo "<br> Mes: $mes11  A&ntilde;o: $anio11 Proyecto: $regProy2[id_proyecto] Division: $cDivisiones Departamento: $regDpto[id_departamento]  <br>";
								}
								if($ban==0)
								{
									$matriz_valores[$fila][$columna]=0; 
									//echo "<br>C: $columna - F: $fila";
									$columna++;
								}
								if($mes11==12)
									{
										$mes11=1;
										$anio11++;
									}
									else
									{	
										$mes11++;
									}

								//echo "<br>C: $columna - F: $fila";
								?>
                               </td>
<?php

							}
								$fila++;
								$columna=0;		
//echo "sql princi: $cantidad_meses :<br>".$sqlvFact."</br>";
//echo mssql_get_last_message(); 
			
	  ?>
  </tr>
  

                              
              		
              <?php 
				}
				?>
				<tr><td  align="center" colspan="2">&nbsp;</td> 
                    	<td  align="center" >Total </td> 
                       	<?php 
								$valor_total_facturacion=0;								
								for($columna=0;$columna<$num_columnas;$columna++)
								{
									for($fila=0;$fila<$num_filas;$fila++)
									{
										$valor_total_facturacion+=$matriz_valores[$fila][$columna];
									}
						?>
                        			<td  align="right">
                                    
									$<?php 
									//"filas $num_filas Columnas $num_columnas Valor total: ".
									echo number_format($valor_total_facturacion, 2, ",", ".");
								//	echo "<br>C: $columna - F: $fila";
									?></td>
                                    <?php
									$valor_total_facturacion=0;
									
								}
								unset($matriz_valores);
								 ?>
	                     
                    <tr>
         
		<?	}							
?>								
</table>

</body>
</html>