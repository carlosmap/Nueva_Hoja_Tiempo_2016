<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//OOF
//9Abr2013
//validacion de usuarios delegados Omar Osuna 
$laUnidadAntigua =  $laUnidad;;
$sql1="select unidadQueDelega,estado from DelegadosDivisionRpts 
	       where unidadDelegada = " . $laUnidad; 
$cursore = mssql_query($sql1);
if ($reg=mssql_fetch_array($cursore)) {
	$estado = $reg[estado];
	$unidad = $reg[unidadQueDelega];
}

if($estado=='A') {
	$laUnidad=$unidad;
}
//Trae la informaci�n de la divisiones que tiene a cargo, el director
//16Jul2007
$sql="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
$sql=$sql." from divisiones D, Usuarios U " ;
$sql=$sql." where D.id_director *= U.unidad " ;
$sql=$sql." and D.id_director = " . $laUnidad; 
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDDivision = $reg[id_division];
	$elNomDivision = $reg[nombre];
	$elNomDirector = $reg[nomDir] . " " . $reg[apeDir];
	
	$cDivisiones = $reg[id_division];
}
else
{
	//Si no es el director, Trae la informaci�n de la divisiones que tiene a cargo, el sub-director
	$sql2="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
	$sql2=$sql2." from divisiones D, Usuarios U " ;
	$sql2=$sql2." where D.id_director *= U.unidad " ;
	$sql2=$sql2." and D.id_subdirector = " . $laUnidad; 
	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) 
	{
		$elIDDivision = $reg2[id_division];
		$elNomDivision = $reg2[nombre];
		$elNomDirector = $reg2[nomDir] . " " . $reg2[apeDir];
		
		$cDivisiones = $reg2[id_division];
	}
}
//16Jul2007
//ParaMostrar los botones del Reporte del director de proyecto y de divisi�n
$muestraDirProyecto = 0;
$sqlB="select count(*) esDirector from proyectos  ";
$sqlB=$sqlB." where (id_director = ". $laUnidad . " or id_coordinador = " . $laUnidad . " ) "; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirProyecto = $regB[esDirector];
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

//	AGREGAR TABLA DINAMICA
function tblDinamic(){
			//
			var f = new Date();
			var a1 = document.getElementById('anio1').selectedIndex;
			var a2 = document.getElementById('anio2').selectedIndex;
			
			var m1 = document.getElementById('mes1').selectedIndex;
			var m2 = document.getElementById('mes2').selectedIndex;
			var div= document.getElementById('cDivisiones').value;
			var anio;
			var mes, ma1, ma2, mesf;			
			var tcol;
			
			if( ( ( a1 > a2 ) && ( m1 > m2 ) ) || ( a1 == a2 && m1 > m2 ) ) {
				//&& ( ( m1 > m2 ) || ( m1 < m2 ) )  ){
				//if( m1 > m2 ){
					alert ( 'No se pueden validar esas fechas' );
				//}
			}
			else if(div=="")
			{
				alert("Por favor seleccione una divisi�n ");
			}
			else if(a1>a2)
			{
				alert("La fecha inicial no puede ser mayor a la fecha final");
			}
			

//document.write(f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear());

/*
var mes_ac=((f.getMonth()+1))




			////PENDIENTE
			else if( (a2== f.getFullYear()) and ( mes_ac < m2 )   )
			{
				alert("El mes final no puede ser mayor al actual ");
			}
*/		
			
			else{				
				anio = ( a2 - a1 ) - 1 ;
				ma1 = 12 - m1;
				mes = ma1 + m2;
				if( mes > 12 ){
					mesf = mes - 12;
					anio = anio + 1;
					tcol = (anio * 12 ) + mesf;
				}
				else{
					tcol = (anio * 12 ) + mes;
				}
				var tbl = document.getElementById('tblDm');
				var tblDs = '';
				var rc;
				rc = tcol + 1;
	//			alert ( tcol);
				tblDs = "<table width='100%'  border='0' cellspacing='1' cellpadding='0'>"+
						"<tr class='TituloTabla2'><td width='1%' colspan='"+rc+"'>Facturaci�n mes a mes por proyecto</td></tr>"+
						"<tr class='TituloTabla2'><td>Proyecto</td>";

				var nMes = [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ];
				
				for( var i = 0; i < tcol; i++ ){
					if( i == 0 )
						tblDs += "<td> "+ nMes[a1] +" </td>";
					else
						tblDs += "<td> "+ nMes[i] +" </td>";
				}
				tblDs += "</tr></table><br />";
				tbl.innerHTML = "";
				tbl.innerHTML += tblDs;
			}
			return true;
		}
//	AGREGAR TABLA DINAMICA
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reportes de Hoja de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:200px; top:8px; width: 529px; height: 25px;">
		<div align="center"> 
		  Reportes Hoja de Tiempo <br> Director de divisi�n
		</div>
</div>
	<form name="form1" method="post" action="" >
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">

      <tr>
    <td align="center"  class="TituloTabla">Divisiones</td>
    <td colspan="2" class="TxtTabla">
    <select id="cDivisiones" name="cDivisiones" onchange="document.form1.submit();"  class="CajaTexto" > <!--  -->
    	<option value="">::: Todo :::</option>
    	<?php
			#	FILTROS OSCAR LOPEZ
			#	13-08-2012
			#	LISTAR DIVISIONES ACTIVAS
			
			$sqlDiv = "select * from HojaDeTiempo.dbo.Divisiones div where estadoDiv = 'A'			";
			$sqlDiv = $sqlDiv . " and id_division = " . $cDivisiones;
			$sqlDiv = $sqlDiv . " order by nombre ";

			$qryDiv = mssql_query ( $sqlDiv );
			while( $rowDiv = mssql_fetch_array( $qryDiv ) ){ 
				if( trim($cDivisiones) == $rowDiv[id_division] ){
		?>
		        	<option value="<?php	echo $rowDiv[id_division];	?>" selected="selected" ><?php	echo strtoupper($rowDiv[nombre]);	?></option>
        <?php	
				}
				else{
			?>            
        	<option value="<?php	echo $rowDiv[id_division];	?>"><?php	echo $rowDiv[nombre];	?></option>
        <?php	
				}
			}
		?>
    </select>
    </td>
    <td class="TxtTabla" rowspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"  class="TituloTabla">Departamentos:</td>
    <td colspan="2" class="TxtTabla">
   
    <select name="cDepartamento" id="cDepartamento" onchange="document.form1.submit();"  class="CajaTexto" > <!--  -->
    	<option value="">::: Todo :::</option>
    	<?php
			#	OSCAR MAURICIO LOPEZ SEGURA
			#	13-08-2012
			#	LISTAR DEPENDENCIAS ACTIVAS
			
		if($cDivisiones!="")
		{

			$sqlDep = "select * from HojaDeTiempo.dbo.Departamentos dep where estadoDpto = 'A'";
			if( trim($cDivisiones) != "" )
			{
				$sqlDep .= " and dep.id_division = ".$cDivisiones." order by dep.nombre ";
			}
			

			$qryDep = mssql_query( $sqlDep );
			
									

			while( $rowDep = mssql_fetch_array( $qryDep ) ){ #, 
			if( trim($cDepartamento) == $rowDep[id_departamento] ){
		?>
		        	<option value="<?php	echo $rowDep[id_departamento];	?>" selected="selected" ><?php	echo $rowDep[nombre];	?></option>
        <?php	
				}
				else{
			?>         
	        <option value="<?php	echo $rowDep[id_departamento];	?>"><?php	echo $rowDep[nombre];	?></option>        
        <?php
				}
			}
		}
		?>
    </select></td>

  </tr>

  <tr>
    <td width="15%" align="center" class="TituloTabla">Proyecto:</td>
    <td colspan="2" class="TxtTabla">
   
      <select name="plstProyecto" class="CajaTexto" onchange="document.form1.submit();" id="plstProyecto" > <!--  -->
        <option value="">::: Todo :::</option>
        <? 
		if($cDivisiones!="")
		{	
			$sqlPro = "select distinct A.id_proyecto, B.nombre nomProyecto, B.id_proyecto , B.codigo, B.cargo_defecto
					   from HojaDeTiempo.dbo.horas A, HojaDeTiempo.dbo.Proyectos B, HojaDeTiempo.dbo.Usuarios C, 
					   HojaDeTiempo.dbo.Departamentos D, HojaDeTiempo.dbo.Divisiones E 
					   where A.id_proyecto = B.id_proyecto and A.unidad = C.unidad and C.id_departamento = D.id_departamento and 
					   D.id_division = E.id_division ";
			if( trim($cDivisiones)!= "" ){
				$sqlPro =$sqlPro." and D.id_division = ".$cDivisiones;
			}
			if( trim ($cDepartamento)!= "" ){
				$sqlPro = $sqlPro." and D.id_departamento = ".$cDepartamento;
						   #D.id_division = E.id_division and D.id_division = ".$cDivisiones." order by B.nombre";
			}
			$sqlPro =$sqlPro." order by B.nombre ";

			$qryPro = mssql_query( $sqlPro );
			while( $rqwPro = mssql_fetch_array( $qryPro ) ){
#	  while ($lstRegProy=mssql_fetch_array($lstCursorProy)) { 
#	  if ($lstRegProy[id_proyecto] == $pltProyecto) {
#	  		$selProyecto = "selected";
#	  }
#	  else {
#			$selProyecto = "";
#	  } 
				if( trim($plstProyecto) == $rqwPro[id_proyecto] ){
		  ?>
      				<option value="<? echo $rqwPro[id_proyecto]; ?>" <? echo $selProyecto; ?> selected="selected" ><? echo $rqwPro[nomProyecto] . " [" .  $rqwPro[codigo] . "." . $rqwPro[cargo_defecto] . "] "; ?></option>
    	  <?php	
	  			}	
				else{
	  		?>
			        <option value="<? echo $rqwPro[id_proyecto]; ?>" <? echo $selProyecto; ?> ><? echo $rqwPro[nomProyecto] . " [" .  $rqwPro[codigo] . "." . $rqwPro[cargo_defecto] . "] " ; ?></option>
	        <?php	
				}
			}
			
			}
			?>
        </select></td>
   
  </tr>
    
  <tr >
    <td  rowspan="3" align="center" class="TituloTabla"  >Fecha de Busqueda</td>    
</tr>
 <tr >
 	    <td class="TxtTabla" align="center" width="50%">Desde</td>  <td class="TxtTabla" align="center" width="50%">Hasta</td>
 </tr>
<tr>
    <td  class="TxtTabla">
    <table width="100%">    
	    <tr>
    		<td>Mes</td>
            
    <td  class="TxtTabla"><select name="mes1" id="mes1" class="CajaTexto">
      <?php 
	  $meses= array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	  for ( $m2 = 1; $m2 <= 12; $m2++) 
	  {
		  if( trim($mes1) == $m2 )
		  {
	  ?>
	    		<option value="<?php echo $m2;	?>" selected="selected"><?php echo $meses[$m2];	?></option>
      <?php 
		  }
		  else
		  {
	  ?>
		      <option value="<?php echo $m2;	?>"><?php echo $meses[$m2];	?></option>
     <?php }
	  }
	 ?>
    </select>
    </td>
	<td>A&ntilde;o  </td>
    <td>

<select name="anio1" id="anio1" onchange="tblDinamic();" class="CajaTexto"> <!-- document.form1.submit(); -->
  <?php 
  	$band=0;
//	echo $anio1;
  	for ( $a2 = 2008; $a2 <= date( 'Y' ); $a2++) 
	{

		if(( trim($anio1)==$a2 )and($band==0))
		{			
  ?>
  			<option value="<?php echo $a2;	?>" selected="selected"><?php echo $a2;	?></option>
  <?php	
  			$band=1;
  		}	
  		else
		{
			if (( date( 'Y') == $a2 )and($band==0))
			{
	?>
			  <option value="<?php echo $a2;	?>" selected="selected"><?php echo $a2;	?></option>
  <?php	
  				$band=1;
  			}
  			else
			{
	?>			
        		<option value="<?php echo $a2;	?>"><?php echo $a2;	?></option>
  <?php 
			}
		}  
	}
  ?>
</select>
</td>
  </tr>
  	</table>
  
  </td>
  
	<td>
    	<table width="100%">
        	<tr>
                
            	    <td class="TxtTabla">Mes</td>


    <td class="TxtTabla"><select name="mes2" id="mes2" class="CajaTexto">
      <?php 
	  			$band=0;
				for ( $m2 = 1; $m2 <= 12; $m2++) 
				{
					if((trim($mes2)==$m2)and($band==0))
					{
	?>
    					<option value="<?php echo $m2;?>" selected="selected"><?php echo $meses[$m2];	?></option>
    <?php				$band=1;	
					}
					else
					{
						if (( date( 'm') == $m2 )and($band==0)and(trim($mes2)<=date( 'm')))
						{
	?>
    		  				<option value="<?php echo $m2;?>" selected="selected"><?php echo $meses[$m2];	?></option>
                            
      <?php					$band=1;
	  					}
	  					else
						{	  
	  	?>
					      <option value="<?php echo $m2;	?>"><?php echo $meses[$m2];	?></option>
      <?php 			}
					}
				}
	  ?>
    </select>
    </td>
	<td class="TxtTabla">A&ntilde;o  </td>
    <td class="TxtTabla">

<select name="anio2" id="anio2" onchange="tblDinamic();" class="CajaTexto"> <!-- document.form1.submit(); -->
  <?php 
  				$band=0;
				for ( $a2 = 2008; $a2 <= date( 'Y' ); $a2++) 
				{
						if(( trim($anio2)==$a2 )and($band==0))
						{			
  ?>
				  			<option value="<?php echo $a2;	?>" selected="selected"><?php echo $a2;	?></option>
  <?php	
				  			$band=1;
  						}
						else
						{
					
							if (( date( 'Y') == $a2 )and ($band==0))
							{
			?>
								  <option value="<?php echo $a2;	?>" selected="selected"><?php echo $a2;	?></option>
  <?php	  						  $band=1;
							}	
							else
							{
					?>
								  <option value="<?php echo $a2;	?>"><?php echo $a2;	?></option>
	  <?php 				}
						}
  				}	?>
</select>
</td>
            </tr>
        </table>
    </td>  
  </tr>
<?php 
//echo "<br>---".$sqlDep."<br>";		
//echo "<br>---".$sqlPro."<br>";					


?>

  
  <tr>
	<td colspan="2" class="TxtTabla"></td>
	<td class="TxtTabla"><input name="Submit3" type="button" class="Boton" onclick="MM_openBrWindow('reporte_facturacion.php?division=<?=$_REQUEST['cDivisiones'];?>&amp;departamento=<?=$_REQUEST['cDepartamento'];?>&amp;proyecto=<?=$_REQUEST['plstProyecto'];?>&amp;mes=<?=$_REQUEST['mes1'];?>&amp;ano=<?=$_REQUEST['anio1'];?>&amp;mes2=<?=$_REQUEST['mes2'];?>&amp;ano2=<?=$_REQUEST['anio2'];?>','wRPT1','scrollbars=yes,resizable=yes,width=500,height=400')" value="XLS Facturaci&oacute;n" />


    </td>
     <td width="10%" class="TxtTabla">
     	<input name="accion" type="submit" class="Boton" value="Consultar" onclick="tblDinamic();"  />
     </td>
  </tr>
  <tr align="right">
    <td colspan="4" class="TxtTabla"><strong>NOTA: Cada vez que cambie un criterio de b&uacute;squeda es obligatorio presionar el bot&oacute;n Consultar.</strong></td>
    </tr>
      
</table>
	</td>
  </tr>
</table>

	<!--- 
		///////////////**************************    Codigo Anterior Aqui
	  --->
      
<?php 
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
				

//echo "<br> cantidad meses:".$cantidad_meses."<br>";	 
	?>
    <script type="text/javascript" language="javascript" >
		/*
		function valida_mese()
		{
			var cant_meses=<?php echo $cantidad_meses; ?>;
			alert (cant_meses);
			if(12<cant_meses)
			{
				alert("El rango entre las fechas seleccionadas debe ser menor a 12 meses");
				return false;
			}
			else
			{
				alert("verdad");
				return true;
			}
			
		}
		*/
	</script>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Informaci&oacute;n de la Divisi&oacute;n</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Divisi&oacute;n</td>
        <td width="40%">Director</td>
        </tr>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($elNomDivision)) ; ?></td>
        <td width="40%"><? echo ucwords(strtolower($elNomDirector)) ; ?></td>
        </tr>
    </table>
	      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
		  
	    </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3.php" class="FichaInAct1">Horas facturada por usuario y Proyecto </a> </td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3b.php" class="FichaInAct1">Horas y valor facturado <br />
        por usuario y proyecto </a></td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3c.php" class="FichaInAct1">Proyectos <br />
  con facturaci&oacute;n </a></td>
        <td width="15%" class="FichaAct">Facturaci&oacute;n por <br />
        Departamentos </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="1" colspan="5" class="TituloUsuario"> </td>
      </tr>
    </table>	
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td>&nbsp;</td>
		  </tr>      
      	</table>
        
        
       	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  			<tr>
		      <td class="TituloUsuario">Facturaci&oacute;n de la divisi&oacute;n - Proyectos con facturaci&oacute;n</td>
		  </tr>
		</table>
        
        
		 
		    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    	  <tr>
        				<td align="right" class="TituloTabla">Valor Facturado = (Salario Personal Divisi&oacute;n Administrativa/ 185) * Horas Facturadas </td>
	    		  </tr>
		    </table>
            
		    <table width="100%" border="1" cellspacing="0" cellpadding="0"">
		      <tr>
		        <td class="TituloUsuario" align="center"  width="40%">Proyectos</td>
                <? 
/*			$mes11=$mes1;
				$anio11=$anio1;
				for ($m=1; $m<=$cantidad_meses; $m++) 				
				{ 
					if($mes11==12)
					{
				?>                	
				        <td class="TituloUsuario"><? echo $meses[$mes11]."-".$anio11;?></td>                    
                <? 	
						$mes11=1;
						$anio11++;
					}
					else
					{	
				?>                	
				        <td class="TituloUsuario"><? echo $meses[$mes11]."-".$anio11;?></td>                    
                <? 												
						$mes11++;
					}
				} 
*/				
				?>
                 <td class="TituloUsuario" align="center" colspan="<?php echo $cantidad_meses; ?>">Meses</td>
	          </tr>
<!--
cDivisiones  
cDepartamento
plstProyecto
-->
               <?php
			 if($accion=="Consultar")
			 {
			   	if(($cantidad_meses<=12))
				{
					consulta($cDivisiones, $cDepartamento, $plstProyecto, $mes1, $anio1, $mes2, $anio2, $fecha_final2,$fecha_inicial,$cantidad_meses,$meses);
				}
				else
				{
					echo '<script> alert("El rango entre las fechas seleccionadas debe ser menor a 12 meses"); </script>';
				}
			 }
function consulta ($cDivisiones, $cDepartamento, $plstProyecto, $mes1, $anio1, $mes2, $anio2, $fecha_final2,$fecha_inicial,$cantidad_meses,$meses)				  
{		
?>
		<tr><td>&nbsp;</td></tr>
<?php 	   
			   //Trae la informaci�n del proyecto
				$sql_proyecto=" select distinct A.id_proyecto, B.nombre nomProyecto, B.codigo, B.cargo_defecto
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
/*
if(true){							
echo "<br> sql proyecto: ".$sql_proyecto."<br>";
echo mssql_get_last_message(); 
}
			
echo "<br>".mssql_get_last_message()."<br>"; 	
//echo $sql_proyecto;
echo "departemento:".$cDepartamento."<br>";		
$az=1;
*/
				while ($regProy2=mssql_fetch_array($cursorProy2)) 
				{						
			   	?>
	              <tr>
    	        	  	<td height="3"> </td> 
						<td   height="3"colspan="<?php echo $cantidad_meses; ?>"> </td>
        	      </tr>
			      <tr>
						<td class="TituloTabla"><?php echo $regProy2[nomProyecto] . " [" .  $regProy2[codigo] . "." . $regProy2[cargo_defecto] . "] "; //." - ".$regProy2[id_proyecto]; ?></td> 
              <?php
			  	$mes11=$mes1;
				$anio11=$anio1;
				for ($m=1; $m<=$cantidad_meses; $m++) 				
				{ 
					if($mes11==12)
					{
				?>                	
				        <td class="TituloUsuario" align="center" ><? echo substr($meses[$mes11],0,3)."<br>".$anio11;?></td>                    
                <? 	
						$mes11=1;
						$anio11++;
					}
					else
					{	
				?>                	
				        <td class="TituloUsuario" align="center" ><? echo substr($meses[$mes11],0,3)."<br>".$anio11;?></td>                    
                <? 												
						$mes11++;
					}
				} 
				?>          
                       
			      </tr>              
              <?php
	  


//--and MONTH(A.fecha)=6
//--and YEAR(A.fecha)=2012				   

			   //Trae la informaci�n del departamento que tiene a cargo
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
/*
if($az==1){							
echo "<br> sql departamento: ".$sqlDPTO."<br>";
echo mssql_get_last_message(); 
}
$az++;		
*/			
						$total_proyecto=0;	 //valor tofal de lo facturado en cada proyecto
//						$valores_facturacion[0][0]= $cantidad_meses;  

						$num_filas=mssql_num_rows($cursorDpto);
						$num_columnas=$cantidad_meses;
						$fila=0;			
						$columna=0;
						while ($regDpto=mssql_fetch_array($cursorDpto)) 
						{		
						  	$mes11=$mes1;
							$anio11=$anio1;
							//if($regDpto[nomDepartamento]!="")
							{
		   	?>
            
         			         <tr>
                    			<td class="TxtTabla"><dd><?php echo $regDpto[nomDepartamento]; //."-".$regDpto[id_departamento]; ?></dd></td>
            <?php                    
                                for ($m=1; $m<=$cantidad_meses; $m++) 				
								{ 
									
			?>					
	                                <td class="TxtTabla" align="right">
          	$<?
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
										//echo "<br>C: $columna - F: $fila";
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
								?>
                                </td>                                
                                    <?php			}
							$fila++;
							$columna=0;
						?>                    
            			     </tr>
              <?php 		}
			  			}		
echo mssql_get_last_message(); 						
//echo "<br> consulta union <br>".$sqlvFact."<br>";										
			  ?>
              		<tr>
                    	<td class="TituloTabla" align="center" >Total </td> 
                       	<?php 
								$valor_total_facturacion=0;								
								for($columna=0;$columna<$num_columnas;$columna++)
								{
									for($fila=0;$fila<$num_filas;$fila++)
									{
										$valor_total_facturacion+=$matriz_valores[$fila][$columna];
									}
						?>
                        			<td class="TituloTabla" align="right">
                                    
									$<?php 
									//"filas $num_filas Columnas $num_columnas Valor total: ".
									echo number_format($valor_total_facturacion, 2, ",", ".");
									?></td>
                                    <?php
									$valor_total_facturacion=0;
									
								}
								unset($matriz_valores);
								 ?>
	                     
                    <tr>
              <?php 
			  } 
}
			   ?>
      </table>

	 
        
</form>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
          <tr>
            <td class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P�gina principal Hoja de tiempo" /></td>
            <td align="right" class="TxtTabla">
			<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT.php');return document.MM_returnValue" value="Programaci&oacute;n personal" />
			<? if ($muestraDirProyecto > 0) { ?>
			<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT2.php');return document.MM_returnValue" value="Reporte Director de proyecto" />
			<? } ?>
			</td>
          </tr>
</table>

<?
$laUnidad = $laUnidadAntigua ;
?>	
</body>
</html>
