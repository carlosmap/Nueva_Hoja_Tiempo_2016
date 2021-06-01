<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//echo "Proy=".  $cualProyecto . "<br>";
//echo "Act=" . $cualActividad . "<br>";
//exit;

	$meses= array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	function planea_fact($id_proyecto,$unidad,$activi,$vigencia,$mes)
	{
//echo "Ingerasssss2222<br>";
			$sql_total="select  SUM(hombresMes) as total_H_M_P from PlaneacionProyectos where id_proyecto=".$id_proyecto." and id_actividad=".$activi." and vigencia=".$vigencia." and  unidad=".$unidad." and esInterno='I' and mes=".$mes." ";

			$cur_total=mssql_query($sql_total);
			if($datos_total=mssql_fetch_array($cur_total))
				$total=$datos_total["total_H_M_P"];		

		$sql_total_f="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$id_proyecto." and id_actividad=".$activi." and vigencia=".$vigencia." and  unidad=".$unidad. " and esInterno='I' and mes=".$mes;

//			echo "<br><br>".$sql_total_f ;
		$cur_total_f=mssql_query($sql_total_f);
		if($datos_total_f=mssql_fetch_array($cur_total_f))
			$total_f=$datos_total_f["total_H_M_F"];	

		$pTema=$pTema."<tr class='TxtTabla'><td colspan='6' >&nbsp;</td><td>
																				<table  bgcolor='#FFFFFF' width='100%'  >
																				<tr class='TxtTabla'><td width='55%'>Total Facturaci&oacute;n </td><td  class='Estilo2'  align='right'> ".$total_f."</td>
																				</tr>
																				<tr class='TxtTabla'><td  width='55%' >Total Planeaci&oacute;n </td ><td  class='Estilo2' align='right'> ".$total."</td>
																				</tr>																			
																				</table> </td></tr>
<tr class='TituloUsuario'><td colspan='7' bgcolor='#999999' ></td></tr>";
		return $pTema;
	}

	if(trim($division)!="")
	{
		//	CONSULTA LOS USUARIOS PERTENECIENTES A LA DIVISION Y DEPARTAMENTO SELECCIONADOS
		$sql3 = " SELECT *, UPPER(Usuarios.nombre) as nom_usu ,UPPER(Usuarios.apellidos) as apellido FROM HojaDeTiempo.dbo.Usuarios";
		
		$sql3 = $sql3 . " inner join Departamentos on Usuarios.id_departamento =Departamentos.id_departamento " ;
		$sql3 = $sql3 . " inner join Divisiones on Departamentos.id_division =Divisiones.id_division ";
		
		$sql3=$sql3." WHERE retirado IS NULL ";		
		$sql3 = $sql3 . " AND Divisiones.id_division = " . $division;

		
		if(trim($departamento) != ""){
			$sql3 = $sql3 . " AND Departamentos.id_departamento = " . $departamento;
		}

		$sql3 = $sql3 . "ORDER BY apellidos ";
		$cursor3 = mssql_query($sql3);
	}


	if($recarga==1)
	{
			$sql_fact="";
			if ($facturacion==0)
			{
				$sql_fact="select * from ( (";
			}



			//FACTURACION QUE ESTA POR ENCIMA DE LA PLANEACION
			if( ($facturacion==1) || ($facturacion==0))
			{ 
				$sql_fact=$sql_fact." 
	--SE USA DISTINCT, POR QUE UN PARTICIPANTE, PUEDE REGISTRAR FACTURACION INFERIOR / SUERIOR A LA PLANEACION, EN LAS DIFERENTES ACTIVIDADES DE UN PROYECTO
	--SE IMPLEMENTO DISTINCT, PARA EVITAR, QUE LA CONSULTA ARROJE REGISTROS DUPLICADOS

					select distinct unidad,id_proyecto,nombre,nombre_depto,nombre_div,vigencia, '1' tipo,fechaRetiro from (	select Usuarios.unidad,id_proyecto, Upper(Usuarios.nombre+' '+ Usuarios.apellidos) nombre ,total_facturacion, hombresMes, upper(Departamentos.nombre) nombre_depto ,upper(Divisiones.nombre) nombre_div,vigencia,fechaRetiro  from (
					select id_proyecto,id_actividad,unidad,hombresMes,mes,vigencia,(
				
						select SUM(hombresMesF) total_facturacion from FacturacionProyectos where id_proyecto=PlaneacionProyectos.id_proyecto 
						and id_actividad=PlaneacionProyectos.id_actividad  and unidad=PlaneacionProyectos.unidad and vigencia=PlaneacionProyectos.vigencia
						 and esInterno='I' and mes=PlaneacionProyectos.mes GROUP BY mes,vigencia,id_actividad 
						 
					)total_facturacion from PlaneacionProyectos 
					where PlaneacionProyectos.id_proyecto=".$proyecto." and PlaneacionProyectos.vigencia=".$lstVigencia."  and PlaneacionProyectos.esInterno='I' and PlaneacionProyectos.mes=".$mes."
				) T1
				inner join Usuarios on T1.unidad=Usuarios.unidad
				inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
				inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
				inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria 
				where total_facturacion is not null ";
				if(trim($division)!="")			
				{
					$sql_fact=$sql_fact. " and Divisiones.id_division=".$division;
				}
				if(trim($empleado)!="")			
				{
					$sql_fact=$sql_fact. " and T1.unidad=".$empleado;
				}
				if(trim($departamento)!="")
				{
					$sql_fact=$sql_fact."  and Departamentos.id_departamento=".$departamento;
				}
				if(trim($categoria)!="")
				{
					$sql_fact=$sql_fact." and Categorias.id_categoria=".$categoria;
				}
				if(trim($lstVigencia)!="")
				{
					$sql_fact=$sql_fact." and T1.vigencia=".$lstVigencia;
				}	

				 $sql_fact=$sql_fact." and  hombresMes<total_facturacion )T1	 ";
			}


			if ($facturacion==0)
			{
				$sql_fact=$sql_fact." 	) union ( ";
			}

			//FACTURACION QUE ESTA POR DEBAJO DE LA PLANEACION
			if(($facturacion==2) || ($facturacion==0))
			{

				$sql_fact=$sql_fact." 
	--SE USA DISTINCT, POR QUE UN PARTICIPANTE, PUEDE REGISTRAR FACTURACION INFERIOR / SUERIOR A LA PLANEACION, EN LAS DIFERENTES ACTIVIDADES DE UN PROYECTO
	--SE IMPLEMENTO DISTINCT, PARA EVITAR, QUE LA CONSULTA ARROJE REGISTROS DUPLICADOS

					select distinct unidad,id_proyecto,nombre,nombre_depto,nombre_div,vigencia, '2' tipo,fechaRetiro from (	select Usuarios.unidad,id_proyecto, Upper(Usuarios.nombre+' '+ Usuarios.apellidos) nombre ,total_facturacion, hombresMes, upper(Departamentos.nombre) nombre_depto ,upper(Divisiones.nombre) nombre_div,vigencia,fechaRetiro  from (
					select id_proyecto,id_actividad,unidad,hombresMes,mes,vigencia,(
				
						select SUM(hombresMesF) total_facturacion from FacturacionProyectos where id_proyecto=PlaneacionProyectos.id_proyecto 
						and id_actividad=PlaneacionProyectos.id_actividad  and unidad=PlaneacionProyectos.unidad and vigencia=PlaneacionProyectos.vigencia
						 and esInterno='I' and mes=PlaneacionProyectos.mes GROUP BY mes,vigencia,id_actividad 
						 
					)total_facturacion from PlaneacionProyectos 
					where PlaneacionProyectos.id_proyecto=".$proyecto." and PlaneacionProyectos.vigencia=".$lstVigencia."  and PlaneacionProyectos.esInterno='I' and PlaneacionProyectos.mes=".$mes."
				) T1
				inner join Usuarios on T1.unidad=Usuarios.unidad
				inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
				inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
				inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria 
				where total_facturacion is not null ";

				if(trim($division)!="")			
				{
					$sql_fact=$sql_fact. " and Divisiones.id_division=".$division;
				}
				if(trim($empleado)!="")			
				{
					$sql_fact=$sql_fact. " and T1.unidad=".$empleado;
				}
				if(trim($departamento)!="")
				{
					$sql_fact=$sql_fact."  and Departamentos.id_departamento=".$departamento;
				}
				if(trim($categoria)!="")
				{
					$sql_fact=$sql_fact." and Categorias.id_categoria=".$categoria;
				}
				if(trim($lstVigencia)!="")
				{
					$sql_fact=$sql_fact." and T1.vigencia=".$lstVigencia;
				}	

				 $sql_fact=$sql_fact." and  hombresMes>total_facturacion )T2	 ";
//echo "Ingresooooo 2 <br><br>".$sql_fact."<br><br>";

			}

			//CUANDO SE CONSULTA LA INFORMACION DE LAS TRES CONSULTAS PRINCIPALES (FACTURACION QUE EXCEDE LA PLANEACION ),(FACTURACION QUE ES INFERIOR A LA PLANEACION )
			// (FACTURACION SIN PLANEACION)
			if ($facturacion==0)
			{
				$sql_fact=$sql_fact." 	) union ( ";
			}

			// FACTURACION SIN PLANEACION
			if(($facturacion==3) || ($facturacion==0))
			{ 
			 	$sql_fact=$sql_fact."select distinct(FacturacionProyectos.unidad), FacturacionProyectos.id_proyecto, Upper(Usuarios.nombre+' '+ Usuarios.apellidos) nombre , upper(Departamentos.nombre) nombre_depto ,upper(Divisiones.nombre) nombre_div, vigencia, '3' tipo, fechaRetiro  from  FacturacionProyectos
			 inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
			 inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
			inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
			inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria 
			 where FacturacionProyectos.id_proyecto=".$proyecto." and FacturacionProyectos.vigencia=".$lstVigencia." and FacturacionProyectos.esInterno='I' and FacturacionProyectos.mes=".$mes;
				if(trim($division)!="")			
				{
					$sql_fact=$sql_fact. " and Divisiones.id_division=".$division;
				}
				if(trim($empleado)!="")			
				{
					$sql_fact=$sql_fact. " and FacturacionProyectos.unidad=".$empleado;
				}
				if(trim($departamento)!="")
				{
					$sql_fact=$sql_fact."  and Departamentos.id_departamento=".$departamento;
				}
				if(trim($categoria)!="")
				{
					$sql_fact=$sql_fact." and Categorias.id_categoria=".$categoria;
				}
				if(trim($lstVigencia)!="")
				{
					$sql_fact=$sql_fact." and FacturacionProyectos.vigencia=".$lstVigencia;
				}

				$sql_fact=$sql_fact."and id_actividad not in (
					select id_actividad from PlaneacionProyectos where id_proyecto=".$proyecto." and vigencia=".$lstVigencia." and mes=".$mes." and esInterno='I'
					) ";
	
			}	
			if ($facturacion==0)
			{
				$sql_fact=$sql_fact." ) )T3 order by  tipo,unidad,id_proyecto";
			}


			$cur_fact=mssql_query($sql_fact);
//echo $sql_fact."<br><br>".mssql_get_last_message()."<br>";

	}

//Definir la fecha inicio mínima y final máxima de todas las actividades de todos los proyectos
$minVigenciaP="";
$maxVigenciaP="";
$sql03="SELECT YEAR(MIN(fecha_inicio)) fechaMin, YEAR(MAX(fecha_fin)) fechaMax, year(getdate()) as ano ";
$sql03=$sql03." FROM Actividades where id_proyecto not in(42,56,60,61,62,63,64,65) ";
$cursor03 = mssql_query($sql03);
if ($reg03=mssql_fetch_array($cursor03)) {
	$minVigenciaP = $reg03[fechaMin] ;
	$maxVigenciaP = $reg03[fechaMax] ;
	$anos= $reg03[ano];
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
function envia0()
{
	var error = 'n';
	var mensaje="";



		if(document.Form1.division.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione una división. \n';
		}
		if(document.Form1.proyecto.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione un proyecto. \n';
		}
		if(document.Form1.lstVigencia.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione una vigencia. \n';
		}
		if(document.Form1.facturacion.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione el tipo de facturación. \n';
		}	

		if(document.Form1.mes.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione un mes. \n';
		}	

		if(error=='s')
		{
			alert(mensaje);
		}
		else
		{	
			document.Form1.recarga.value = 1;
			document.Form1.submit();
		}
	
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>


</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;"> CONSOLIDADO POR DIVISION
</div>
	<form name="Form1" id="Form1" method="post" >
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
      </table>

	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
	        <td width="15%" height="20" class="FichaInAct"> <a href="htPlanProyectoConsolidadoDiv.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Divisi&oacute;n VS Proyectos</a>
<td width="15%" height="20" class="FichaInAct" > <a href="htPlanProyectoConsolidadoProy.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Usuarios por Proyecto</a></td>
    	    <td width="15%" height="20" class="FichaInAct" ><a href="htPlanProyectoConsolidadoUsu.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Usuarios por Divisi&oacute;n</a></td>
    	    <td width="15%"    class="FichaAct">Facturaci&Oacute;n por Proyecto</td>
			 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
      <tr>
        <td colspan="6" class="TituloUsuario"></td>
        </tr>
    </table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
                   <tr class="TxtTabla" >
            <td   align="center">&nbsp;</td>
          </tr>
          <tr>
            <td height="2" align="center" class="TxtTabla">
		      <table width="30%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">

		        <tr>
		          <td class="TituloUsuario" height="2" colspan="2" >Criterios de consulta </td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td width="15%" class="TituloTabla">Vigencia</td>
		          <td align="left"><select name="lstVigencia" class="CajaTexto" id="lstVigencia" >
		            <option value="">::Seleccione Vigencia::</option>
		            <? 
								if(!isset($lstVigencia))
									$lstVigencia=$anos;

                                for ($k=$minVigenciaP; $k<=$maxVigenciaP; $k++) { 
                                    if ($lstVigencia == $k) {
                                        $selVig = "selected";
                                    }
                                    else {
                                        $selVig = "";
                                    }
                                ?>
		            <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
		            <? } ?>
		            </select></td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td class="TituloTabla">Mes</td>
		          <td align="left"><select name="mes" class="CajaTexto" id="mes" >
		            <option value="">::Seleccione Mes::</option>
		            <? 
						for($i=1;$i<=12;$i++)
						{						
                                    if ($mes== $i) {
                                        $selMes = "selected";
                                    }
                                    else {
                                        $selMes = "";
                                    }
                                ?>
		            <option value="<? echo $i; ?>" <? echo $selMes; ?> ><? echo $meses[$i]; ?></option>
					<?	} ?>
	              </select></td>
					</tr>
		        <tr>
		          <td width="15%" class="TituloTabla">Divisi&oacute;n</td>
		          <td align="left" class="TxtTabla"><select name="division" id="division" class="CajaTexto" onchange="document.Form1.submit();">
		            <option value="">::Seleccione Divisi&oacute;n:: </option>
		            <?php
				$sql_divisiones="select * from Divisiones where  estadoDiv='A' order by nombre  ";
				$cursor_div=mssql_query($sql_divisiones);
				
				while($datos_div=mssql_fetch_array($cursor_div))
				{
							$select2="";
							if($division== $datos_div["id_division"])
							{
								$select2="selected";
							}

?>
		            <option value="<?php  echo  $datos_div["id_division"]; ?>" <?php echo $select2; ?>>
		              <?php  echo  strtoupper($datos_div["nombre"]); ?>
	                </option>
		            <?php
				}
?>
		            </select></td>
	            </tr>
		        <tr>
		          <td width="15%" class="TituloTabla">Departamento</td>
		          <td  align="left" class="TxtTabla"><select name="departamento" class="CajaTexto" id="departamento"  onchange="document.Form1.submit();">
		            <option value="">::Seleccione Departamento::</option>
		            <?php
				$sql_departamento="select * from Departamentos where  estadoDpto='A' and id_division=".$division ." order by nombre ";
				$cursor_dep=mssql_query($sql_departamento);
				
				while($datos_dep=mssql_fetch_array($cursor_dep))
				{
							$select2="";
							if($departamento== $datos_dep["id_departamento"])
							{
								$select2="selected";
							}

?>
		            <option value="<?php  echo  $datos_dep["id_departamento"]; ?>" <?php echo $select2; ?>>
		              <?php  echo  strtoupper($datos_dep["nombre"]); ?>
	                </option>
		            <?php
				}
?>
		            </select></td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td width="15%" class="TituloTabla">Proyectos</td>
		          <td  align="left"><select name="proyecto" class="CajaTexto" id="proyecto" >
		            <option value="">::Seleccione Proyecto::</option>
		            <?php
				$sql_proyecto="select * from Proyectos order by(nombre)";
				$cursor_proyecto=mssql_query($sql_proyecto);
				
				while($datos_proyecto=mssql_fetch_array($cursor_proyecto))
				{
							$select2="";
							if($proyecto== $datos_proyecto["id_proyecto"])
							{
								$select2="selected";
							}

?>

		            <option value="<?php  echo  $datos_proyecto["id_proyecto"]; ?>" <?php echo $select2; ?>>
		              <?php  echo "[".$datos_proyecto["codigo"].".".$datos_proyecto["cargo_defecto"]."] ". strtoupper($datos_proyecto["nombre"]); ?>
	                </option>
		            <?php
				}
?>
		            </select></td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td width="15%" class="TituloTabla">Categoria</td>
		          <td  align="left"><select name="categoria" class="CajaTexto" id="categoria"  >
		            <option value="">::Seleccione Categoria::</option>
		            <?
						$sql_div="select * from Categorias order by nombre ";
						$cur_div=mssql_query($sql_div);
						while($datos_div=mssql_fetch_array($cur_div))
						{
							$sel="";

							if($datos_div["id_categoria"]==$categoria)
								$sel="selected";

?>
		            <option value="<? echo $datos_div["id_categoria"]; ?>"  <? echo $sel; ?> ><? echo strtoupper($datos_div["nombre"]); ?></option>
		            <?
						}
?>
		            </select></td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td width="15%" class="TituloTabla">Empleado</td>
		          <td align="left"><label for="vigencia2"></label>
		            <select name="empleado" class="CajaTexto" id="empleado"  >
		              <option value="">::Seleccione Empleado::</option>
		              <? 

					while($datos_emple=mssql_fetch_array($cursor3))
					{
						$sel="";
						if($datos_emple["unidad"]==$empleado)
							$sel="selected";
				?>
		              <option value="<? echo $datos_emple["unidad"]; ?>" <? echo $sel; ?> > <? echo $datos_emple["nom_usu"]." ".$datos_emple["apellido"]."[".$datos_emple["unidad"]."] "; ?></option>
		              <? 	} ?>
	                </select></td>
	            </tr>

					<tr>
					<td class="TituloTabla">Facturaci&oacute;n</td>
		          <td align="left" class="TxtTabla"><select name="facturacion" class="CajaTexto" id="facturacion" >
		            <option value="0">::Seleccione Facturación::</option>

		            <option value="1" <? if($facturacion==1){ echo "selected"; } ?> >::Excede la planeación::</option>
		            <option value="2" <? if($facturacion==2){ echo "selected"; } ?> >::Inferior a la planeación::</option>
		            <option value="3" <? if($facturacion==3){ echo "selected"; } ?> >::Sin planeación::</option>
		          
	              </select></td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td colspan="2" align="right" class="TxtTabla"><input type="hidden" name="recarga" value="0" id="recarga" />
		            <input type="hidden" name="ba" value="<?=$ba; ?>" id="ba" />
		<?php
//			if((trim($division)!="") and (trim($lstVigencia)!="") and (trim($proyecto)!="") and ($recarga==1) )
			{
/*
		            <input onclick="MM_openBrWindow('consolidado_vigencia_div_xls.php?division=<?=$division;?>&amp;departamento=<?=$departamento;?>&amp;categoria=<?=$categoria;?>&proyecto=<?=$proyecto; ?>&lstVigencia=<?=$lstVigencia; ?>&empleado=<?=$empleado; ?>','wRPT1','scrollbars=yes,resizable=yes,width=500,height=400')" type="button" class="Boton" value="Descargar en XLS" />

*/
			}
		?>
<input name="Consultar" onclick="envia0();" type="button" class="Boton" id="Consultar" value="Consultar" /></td>
	            </tr>
		        <tr>
		          <td  colspan="2"><table cellspacing="0" cellpadding="0" border="0" width="100%">
		            <tbody>
		              <tr>
		                <td class="TituloUsuario" height="2"></td>
	                  </tr>
	                </tbody>
		            </table></td>
	            </tr>
          </table></td></tr>
		</table>

  	      </table>
</form >
			</td>

          </tr>
        </table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
  
          </tr>
        </table>	


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="4" align="left" class="TituloUsuario">&nbsp;</td>
          </tr>
</table>

	
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="4" align="left" class="TxtTabla">&nbsp;
			</td>
		  </tr>
          <tr>

<?					  if($facturacion==1) { $mensaje_fact="Facturaci&oacute;n que excede la planeaci&oacute;n"; }
					  if($facturacion==2) { $mensaje_fact="Facturaci&oacute;n inferior a la planeaci&oacute;n"; }
					  if($facturacion==3) { $mensaje_fact="Facturaci&oacute;n sin planeaci&oacute;n"; }

					if(($facturacion==1)||($facturacion==2)||($facturacion==3) )
					{
					?>				
			            <td colspan="4" align="left" class="TituloUsuario"><?=$mensaje_fact ?></td>
<?
					}
?>					
          </tr>

	
          <tr>
            <td colspan="4"  class="TxtTabla">

<?

			$ban=0;	//PERMITE IDENTIFICAR, SI SE ESTAN CONSULTANDO TODOS LOS CASOS DE FACTURACION (SIN PLANEACION, EXCEDE PLANEACION, INFERIOR A LA PLANEACION)
					// ban=0 (NO SE ESTANCONSULTADO) ban=1 (SE ESTAN CONSULTANDO TODOS LOS CASOS)
			while($datos_fac=mssql_fetch_array($cur_fact))
			{
				// SI SE ESTAN CONSULTANDO TODOS LOS CASOS DE FACTURACION ($facturacion=0)
				if ($facturacion==0)
				{

					//SE ASIGNA DE FORMA TEMPORAL, EL VALOR DEL TIPO DE FACTURACION DEVUELTO POR LA CONSULTA SUPERIOR
					$facturacion=$datos_fac["tipo"];
					$ban=1;


					// LA VARIABLE $tipo, PERMITE IDENTIFICAR, QUE TIPO DE FACTURACION SE ESTA RECORRIENDO, CUANDO SE ESTAN CONSULTANDO, TODOS LOS CASOS DE FACTURACION
					//ESTO, PARA MOSTRAR EL MENSAJE (EN AZUL) DEL TIPO DE FACTURACION 
					//SI ES LA PRIMERA VEZ QUE SE EJECUTA EL CICLO, SE CREA LA VARIABLE ($tipo), Y SE EVALUA EL TIPO DE FACTURACION, Y SE ASIGNA EL MENSAJE A MOSTRAR

					$mensaje="";
					if(!(isset($tipos)))
					{

						$tipos=$datos_fac["tipo"];
						if($tipos==1)
							$mensaje="Facturaci&oacute;n que Excede la planeaci&oacute;n ";
						
						if($tipos==2)
							$mensaje= "Facturaci&oacute;n Inferior a la planeaci&oacute;n";

						if($tipos==3)
							$mensaje= "Facturaci&oacute;n sin  planeaci&oacute;n";

					}
					else
					{
						// SI EL VALOR DE $tipo, ES DIFERENTE AL VALOR DE TIPO, EXTRAHIDO DE LA B.D. ES POR QUE SE ESTAN CONSULTANDO LOS USUARIOS CON UN TIPO DE 
						//FACTURACION DIFERENTE, ENTONCES SE CARGA LA VARIABLE $mensaje, CON EL MENSAJE DE LA FACTURACION CORRESPONDIENTE

						if($tipos!=$datos_fac["tipo"])	
						{
							//SE ASIGNA A $tipo EL TIPO DE FACTURACION, EXTRAHIDO DE LA B.D.
							$tipos=$datos_fac["tipo"];

							if($tipos==1)
								$mensaje="Facturaci&oacute;n que Excede la planeaci&oacute;n ";
							
							if($tipos==2)
								$mensaje= "Facturaci&oacute;n Inferior a la planeaci&oacute;n ";
	
							if($tipos==3)
								$mensaje= "Facturaci&oacute;n sin  planeaci&oacute;n ";

						}
					}
					if($mensaje!="")		
					{
?>
                        <table width="100%" border="0">
                          <tr>
                            <td class="TituloUsuario" ><?=$mensaje ?></td>
                          </tr>
                        </table>
<?
					}
				}
?>

			<table width="100%" border="0" bgcolor="#376b9a">
              <tr>
                <td class="TxtTabla" >

			<table width="100%" border="0">
              <tr>

                <td width="1%"  align="center" ><? if(trim($datos_fac["fechaRetiro"])!="") { ?> <img src="imagenes/Inactivo.gif" title="Retirado de la compañia" /> <? } ?></td>
                <td class="TituloTabla2" >Unidad</td>
                <td class="TxtTabla" ><?=$datos_fac["unidad"]; ?></td>
                <td class="TituloTabla2">Usuario</td>
                <td class="TxtTabla"><?=$datos_fac["nombre"]; ?></td>
                <td class="TituloTabla2" colspan="12">Deaprtamento</td>
                <td  class="TxtTabla"><?=$datos_fac["nombre_depto"]; ?></td>
                <td  class="TituloTabla2">Divisi&oacute;n</td>
                <td  class="TxtTabla"><?=$datos_fac["nombre_div"]; ?></td>
              </tr>
			</table>
			<table width="100%" bgcolor="#FFFFFF"  border="0" cellspacing="1" cellpadding="0">

              <tr>
<?
			 	if(($datos_fac["tipo"]==1)||($datos_fac["tipo"]==2))
				{
?>
	                <td width="1%" class="TituloTabla2" ></td>
<?
				}
?>
                <td class="TituloTabla2" >Actividad</td>
                <td width="20%" class="TituloTabla2" >Horario</td>
                <td width="10%" class="TituloTabla2">CT</td>
                <td width="10%" class="TituloTabla2">Loc.</td>

                <td width="8%" class="TituloTabla2">Cargo</td>
                <td width="15%"  class="TituloTabla2">Facturaci&oacute;n</td>
              </tr>
<?



				//CONSULTA LA INFORMACION DE LA FACTURACION, DE ACUERDO A LO SELECCIONADO EN EL CAMPO $facturacion
				switch($facturacion)
				{
					////CASO 1, FACTURAION QUE EXCEDE LA PLANEACION
					case 1:

							//CONSULTA LA INFORMACION DE LA FACTURACION QUE EXCEDE LA PLANEACION, REALIZANDO LA SUMATORIA TOTAL PLANEADO Y FACTURADO EN CADA ACTIVIDAD, ASOCIADA AL USUARIO
							//ADEMAS DE TOTALIZAR, EL VALOR FACTURADO, DISTINGUIENDO LOCALIZACION, CLASE DE TIEMPO, CARGO, Y CATEGORIA
							$SQL_FAC_EXCE_PLANEA="select * from (
							 select T2.*,upper(nombre)nombre,macroactividad,TipoLocalizacion.nomLocalizacion,descripcion,
							 (select SUM(hombresMesF) total_planeacion from FacturacionProyectos where id_proyecto=T2.id_proyecto and id_actividad=T2.id_actividad and unidad=T2.unidad
							 and vigencia= T2.vigencia and esInterno='I' and mes=T2.mes GROUP BY mes,vigencia) total_facturacion_actividad, ParticipantesActividad.estado
							  from (select * , (
								select SUM(hombresMes) total_planeacion from PlaneacionProyectos where id_proyecto=T1.id_proyecto and id_actividad=T1.id_actividad and unidad=".$datos_fac["unidad"]." and vigencia= T1.vigencia and esInterno=T1.esInterno and mes=T1.mes
									GROUP BY hombresMes,mes
										) total_planeacion
									 from (					 
								select distinct(mes) mes, SUM(hombresMesF) total_facturacion, id_actividad,id_proyecto,unidad,esInterno,vigencia,localizacion,clase_Tiempo,cargo,IDhorario
								 from FacturacionProyectos where id_proyecto=".$proyecto."  and unidad=".$datos_fac["unidad"]." and vigencia=".$lstVigencia."  and esInterno='I' and mes=".$mes." 
								GROUP BY mes, id_actividad,id_proyecto,unidad,esInterno,vigencia ,localizacion,clase_Tiempo,cargo,IDhorario						
										) T1 
										) T2
												 inner join Actividades on T2.id_actividad=Actividades.id_actividad and T2.id_proyecto=Actividades.id_proyecto
												 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=T2.clase_tiempo
												 inner join TipoLocalizacion on TipoLocalizacion.localizacion=T2.localizacion

inner join ParticipantesActividad  on Actividades.id_proyecto=ParticipantesActividad.id_proyecto and Actividades.id_actividad=ParticipantesActividad.id_actividad
 and T2.unidad=ParticipantesActividad.unidad


										)T3				 		 					
									 where total_planeacion<total_facturacion_actividad			";
							$CUR_FACT_EXCE_PLANEA=mssql_query($SQL_FAC_EXCE_PLANEA);
//echo $SQL_FAC_EXCE_PLANEA."***<br><br>-----".mssql_num_rows($CUR_FACT_EXCE_PLANEA)." **** ".mssql_get_last_message();
							$ban_activi=0;
							$activi=0;
							$can_res=mssql_num_rows($CUR_FACT_EXCE_PLANEA);
							$cont=1;
							while($datos_fact_exce_planea=mssql_fetch_array($CUR_FACT_EXCE_PLANEA))
							{


								//SE IMPRIME LA FILA DE LOS TOTALES, CUANDO SE CAMBIA DE ACTIVIDAD}
								// se imprime, antes de mostrar la informacion de la siguiente actividad
								if($ban_activi==0)
								{
										$activi=$datos_fact_exce_planea["id_actividad"];
										$ban_activi=1;

								}
								if($activi!=$datos_fact_exce_planea["id_actividad"])
								{
									//LLAMADA A LA FUNCION
									$pTema=$pTema.planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$activi,$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
									$ban_activi=0;

								}
				
				
								$pTema=$pTema.'
							  <tr class="TxtTabla" >
				            <td class="TxtTabla" >';

								//MUESTRA EL ICONO, CORRESPONDIENTE, DEPENDIENDO, SI EL USUARIO ESTA ACTIVO O INACTIVO EN LA ACTIVIDAD
								if($datos_fact_exce_planea["estado"]=='A')
								{ 
									$pTema=$pTema.'<img title="Activo" src="img/images/alertaAzul.gif"  width="15" height="13" />';
								}
								if (trim($datos_fact_exce_planea["estado"]) == "I") {

									$pTema=$pTema.'<img src="img/images/alertaRojo.gif" title="Inactivo" width="15" height="13" />';
								}

							 $pTema=$pTema.' </td>
								<td class="TxtTabla" >['.$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"].'</td>
								<td>';
													$datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_fact_exce_planea["IDhorario"]));
								
													$pTema=$pTema."[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];                
								$pTema=$pTema.' 
								</td>
								<td>'.$datos_fact_exce_planea["descripcion"].'</td>
								<td>'.$datos_fact_exce_planea["nomLocalizacion"].'</td>
								<td>'.$datos_fact_exce_planea["cargo"].'</td>
								<td align="right">'.$datos_fact_exce_planea["total_facturacion"].'</td>            
							  </tr>	';	
				
								//A DEMAS SE IMPRIME LA FILA DE LOS TOTALES, CUANDO SE TRATA DE LA ULTIMA ACTIVIDAD A MOSTRAR
								if($can_res==$cont)
								{
									$pTema=$pTema.planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$datos_fact_exce_planea["id_actividad"],$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
									echo $pTema;
									$pTema="";

								}
								$cont++;
							}
					break;
		
					//FACTURACIÓN QUE ESTA POR DEBAJO DE LO PLANEADO
					case 2:
						//CONSULTA LA INFORMACION DE LA FACTURACION QUE ES INFERIOR A LA PLANEACION, REALIZANDO LA SUMATORIA TOTAL PLANEADO Y FACTURADO EN CADA ACTIVIDAD, ASOCIADA AL USUARIO
						//ADEMAS DE TOTALIZAR EL VALOR FACTURADO, DISTINGUIENDO LOCALIZACION, CLASE DE TIEMPO, CARGO, Y CATEGORIA
						$SQL_FAC_INFE_PLANEA="
					select * from (
					 select T2.*,upper(nombre)nombre,macroactividad,TipoLocalizacion.nomLocalizacion,descripcion,
					 (select SUM(hombresMesF) total_planeacion from FacturacionProyectos where id_proyecto=T2.id_proyecto and id_actividad=T2.id_actividad and unidad=T2.unidad
						 and vigencia= T2.vigencia and esInterno='I' and mes=T2.mes GROUP BY mes,vigencia) total_facturacion_actividad, ParticipantesActividad.estado
					  from (select * , (
					select SUM(hombresMes) total_planeacion from PlaneacionProyectos where id_proyecto=T1.id_proyecto and id_actividad=T1.id_actividad and unidad=".$datos_fac["unidad"]." and vigencia= T1.vigencia and esInterno=T1.esInterno and mes=T1.mes
					GROUP BY hombresMes,mes
										) total_planeacion
									 from (					 
							select distinct(mes) mes, SUM(hombresMesF) total_facturacion, id_actividad,id_proyecto,unidad,esInterno,vigencia,localizacion,clase_Tiempo,cargo,IDhorario
							 from FacturacionProyectos where id_proyecto=".$proyecto."  and unidad=".$datos_fac["unidad"]." and vigencia=".$lstVigencia."  and esInterno='I' and mes=".$mes." 
							GROUP BY mes, id_actividad,id_proyecto,unidad,esInterno,vigencia ,localizacion,clase_Tiempo,cargo,IDhorario						
										) T1 
										) T2
												 inner join Actividades on T2.id_actividad=Actividades.id_actividad and T2.id_proyecto=Actividades.id_proyecto
												 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=T2.clase_tiempo
												 inner join TipoLocalizacion on TipoLocalizacion.localizacion=T2.localizacion
inner join ParticipantesActividad  on Actividades.id_proyecto=ParticipantesActividad.id_proyecto and Actividades.id_actividad=ParticipantesActividad.id_actividad
 and T2.unidad=ParticipantesActividad.unidad
					)T3				 		 
						where total_planeacion>total_facturacion_actividad			";				
						$CUR_FACT_INFE_PLANEA=mssql_query($SQL_FAC_INFE_PLANEA);
//echo $SQL_FAC_INFE_PLANEA."***<br><br>-----".mssql_num_rows($CUR_FACT_INFE_PLANEA)." **** ".mssql_get_last_message();
						$ban_activi=0;
						$activi=0;
						$can_res=mssql_num_rows($CUR_FACT_INFE_PLANEA);
						$cont=1;

						while($datos_fact_exce_planea=mssql_fetch_array($CUR_FACT_INFE_PLANEA))
						{
							//SE IMPRIME LA FILA DE LOS TOTALES, CUANDO SE CAMBIA DE ACTIVIDAD}
							// se imprime, antes de mostrar la informacion de la siguiente actividad
							if($ban_activi==0)
							{
									$activi=$datos_fact_exce_planea["id_actividad"];
									$ban_activi=1;
							}
							if($activi!=$datos_fact_exce_planea["id_actividad"])
							{
								//LLAMADA A LA FUNCION
								$pTema=$pTema.planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$activi,$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
								$ban_activi=0;
							}
			
			
						  $pTema=$pTema.'<tr class="TxtTabla">
				            <td class="TxtTabla" >
';
								if($datos_fact_exce_planea["estado"]=='A')
								{ 
									$pTema=$pTema.'<img title="Activo" src="img/images/alertaAzul.gif"  width="15" height="13" />';
								}
								if (trim($datos_fact_exce_planea["estado"]) == "I") {

									$pTema=$pTema.'<img src="img/images/alertaRojo.gif" title="Inactivo" width="15" height="13" />';
								}
							 $pTema=$pTema.' </td>

							<td  >'."[".$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"].'</td>
							<td>';
			
									   $datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_fact_exce_planea["IDhorario"]));
							
												$pTema=$pTema."[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];                
							$pTema=$pTema.'
							</td>
							<td>'.$datos_fact_exce_planea["descripcion"].'</td>
							<td>'.$datos_fact_exce_planea["nomLocalizacion"].'</td>
							<td>'.$datos_fact_exce_planea["cargo"].'</td>
							<td align="right">'.$datos_fact_exce_planea["total_facturacion"].'</td>            
						  </tr>	';	
			
							//A DEMAS SE IMPRIME LA FILA DE LOS TOTALES, CUANDO SE TRATA DE LA ULTIMA ACTIVIDAD A MOSTRAR
							if($can_res==$cont)
							{
								$pTema=$pTema.planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$datos_fact_exce_planea["id_actividad"],$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
								echo $pTema;
								$pTema="";
							}
							$cont++;
						}
					break;
		
					//FACTURACION SIN PLANEACION
					case 3:
						//CONSULTA LA FACTURACION SIN PLANEACION
						$SQL_FACT_SIN_PLANEA="select  FacturacionProyectos.id_actividad,IDhorario,Clase_Tiempo.descripcion,TipoLocalizacion.nomLocalizacion,cargo,upper(nombre)nombre,macroactividad ,FacturacionProyectos.clase_tiempo, FacturacionProyectos.localizacion,FacturacionProyectos.cargo from FacturacionProyectos 
						 inner join TipoLocalizacion on TipoLocalizacion.localizacion=FacturacionProyectos.localizacion
				
						 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=FacturacionProyectos.clase_tiempo
						 inner join Actividades on FacturacionProyectos.id_actividad=Actividades.id_actividad and FacturacionProyectos.id_proyecto=Actividades.id_proyecto
						 where FacturacionProyectos.id_proyecto=".$proyecto."  and unidad=".$datos_fac["unidad"]." and vigencia=".$lstVigencia."  and mes=".$mes."  and esInterno='I'
							and FacturacionProyectos.id_actividad not in (
										select id_actividad from PlaneacionProyectos where id_proyecto=".$proyecto." and vigencia=".$lstVigencia." and mes=".$mes." and unidad=".$datos_fac["unidad"]." and esInterno='I'
								)
						  group by  FacturacionProyectos.id_actividad,IDhorario,Clase_Tiempo.descripcion,cargo,TipoLocalizacion.nomLocalizacion,nombre,macroactividad,FacturacionProyectos.clase_tiempo,  FacturacionProyectos.localizacion ,FacturacionProyectos.cargo ";
						$CUR_FACT_SIN_PLANEA=mssql_query($SQL_FACT_SIN_PLANEA);
//echo $SQL_FACT_SIN_PLANEA."***<br><br>-----".mssql_num_rows($CUR_FACT_SIN_PLANEA)." **** ".mssql_get_last_message();
						$can_res=mssql_num_rows($CUR_FACT_SIN_PLANEA);
						$ban_activi=0;
						$cont=1;
						while($datos_fact_sin_planea=mssql_fetch_array($CUR_FACT_SIN_PLANEA))
						{
							//IMPRIME LA LINEA QUE SEPARA LAS ACTIVIDADES
							if($ban_activi==0)
							{
									$activi=$datos_fact_sin_planea["id_actividad"];
									$ban_activi=1;
							}
							if($activi!=$datos_fact_sin_planea["id_actividad"])
							{
			
								$pTema=$pTema. '<tr class="TxtTabla"><td colspan="6" bgcolor="#999999" height="2"  > </td></tr>';
								$ban_activi=0;
							}
			
							$pTema=$pTema.'				  <tr class="TxtTabla">
								<td> '."[".$datos_fact_sin_planea["macroactividad"]."] ".$datos_fact_sin_planea["nombre"].'</td>
												<td>';
							  
							$datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_fact_sin_planea["IDhorario"]));
											
																$pTema=$pTema."[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];
											
											
							
										   $pTema=$pTema.'     </td>
								<td>'.$datos_fact_sin_planea["descripcion"].'</td>
								<td>'.$datos_fact_sin_planea["nomLocalizacion"].'</td>
								<td>'.$datos_fact_sin_planea["cargo"].'</td>';
							
							 $pTema=$pTema.'   <td>';
							
									$sql_total="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$proyecto." and id_actividad=".$datos_fact_sin_planea["id_actividad"]." and vigencia=".$lstVigencia." and  unidad=".$datos_fac["unidad"]."  and esInterno='I' and IDhorario=".$datos_fact_sin_planea["IDhorario"]." and mes=".$mes." and Clase_Tiempo=".$datos_fact_sin_planea["clase_tiempo"]." and localizacion=".$datos_fact_sin_planea["localizacion"]." and cargo=".$datos_fact_sin_planea["cargo"]." ";
									$cur_total=mssql_query($sql_total);
//echo mssql_get_last_message()." <br> zsss <br> ".$sql_total."<br>";
									if($datos_total=mssql_fetch_array($cur_total))
										$pTema=$pTema.$datos_total["total_H_M_F"];		
			
							 $pTema=$pTema.'   </td>
			  </tr>';
			
							//IMPRIME LA FILA DE LOS TOTALES
							if($can_res==$cont)
							{
								echo $pTema;
								$pTema="";
							}
							$cont++;
						}


					break;

				}

				//SI $ban=1,  ES POR QUE SE ESTAN CONSULTANDO TODAS LAS SITUACIONES DE LA FACTURACION
				if ($ban==1)
				{
					//SE ASIGNA NUEVAMENTE, EL VALOR A LA VARIABLE $facturacion, PARA QUE SE PUEDA CONSULTAR, EN EL SIGUIENTE CICLO, EL TIPO DE FACTURACION
					//TRAHIDO DE LA CONSULTA SQL SUPERIOR
					$facturacion=0;	
				}
?>

                      <tr >
                        <td class="TxtTabla" colspan="7" >&nbsp;</td>

                      </tr>

              </table>
<!---------------------------->
			</td>
			</tr>
		</table>
<?	
			}

//			unset ($tipos);
			if( ( (int) (mssql_num_rows($cur_fact) )==0) and(trim(mssql_num_rows($cur_fact))!=""))
			{
?>
          <tr >
                        <td colspan="19" align="left" class="TxtTabla">&nbsp; </td>
          </tr>
  	            <tr class="TituloTabla2">
                        <td colspan="19" align="left" class="TituloTabla2">No se encontraron registros. </td>
              </tr>
<?
			}
?>
            </td>
          </tr>
</table>

	<table width="100%" cellpadding="0" cellspacing="0" >
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
	</table>
<?
//Finaliza las conexiones a MySql y a SQL Server
mssql_close();
?>


</body>
</html>
