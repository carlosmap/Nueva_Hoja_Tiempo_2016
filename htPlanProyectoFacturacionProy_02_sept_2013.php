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

		$pTema=$pTema."<tr class='TxtTabla'><td colspan='5' >&nbsp;</td><td>
																				<table  bgcolor='#FFFFFF' width='100%'  >
																				<tr class='TxtTabla'><td width='55%'>Total Facturaci&oacute;n </td><td  class='Estilo2'  align='right'> ".$total_f."</td>
																				</tr>
																				<tr class='TxtTabla'><td  width='55%' >Total Planeaci&oacute;n </td ><td  class='Estilo2' align='right'> ".$total."</td>
																				</tr>																			
																				</table> </td></tr>
<tr class='TituloUsuario'><td colspan='6' bgcolor='#999999' ></td></tr>";
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

		 $sql_fact="select distinct(FacturacionProyectos.unidad), Upper(Usuarios.nombre+' '+ Usuarios.apellidos) nombre , upper(Departamentos.nombre) nombre_depto ,upper(Divisiones.nombre) nombre_div from  FacturacionProyectos
		 inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
		 inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
		inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
		inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria 
		 where FacturacionProyectos.id_proyecto=".$proyecto." and FacturacionProyectos.vigencia=".$lstVigencia." and FacturacionProyectos.esInterno='I' and FacturacionProyectos.mes=".$mes;

		//

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

			if($facturacion==1) 
			{ 
/*
				$sql_fact=$sql_fact."(select SUM(hombresMesF) total_facturacion from FacturacionProyectos where id_proyecto=".$proyecto." and id_actividad=18 and unidad=15712
					 and vigencia=2013 and esInterno='I' and mes=7 GROUP BY mes,vigencia ) total_facturacion ,
						
				(select SUM(hombresMes) total_planeacion from PlaneacionProyectos where id_proyecto=".$proyecto." and id_actividad=18 and unidad=15712 and vigencia= 2013
				 and esInterno='i' and mes=7
				GROUP BY hombresMes,mes) total_planeacion ";
*/	
			}
			//FACTURACION QUE ESTA POR DEBAJO DE LA PLANEACION
			if($facturacion==2) 
			{ 

	
	
			}
			//SE AÑADE EL FRAGMENTO DE CODIGO A LA CONSULTA, CUANDO SE SELECCIONA LA FACTURACION SIN PLANEACION
			if($facturacion==3) 
			{ 
				$sql_fact=$sql_fact."and id_actividad not in (
					select id_actividad from PlaneacionProyectos where id_proyecto=".$proyecto." and vigencia=".$lstVigencia." and mes=".$mes." and esInterno='I'
					) ";
	
			}	
			$cur_fact=mssql_query($sql_fact);
echo $sql_fact."<br><br>".mssql_get_last_message()."<br>";

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
    	    <td width="15%" height="20" class="FichaAct" >Usuarios por Proyecto</td>
    	    <td width="15%" height="20" class="FichaInAct" ><a href="htPlanProyectoConsolidadoUsu.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Usuarios por Divisi&oacute;n</a></td>

			 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
      <tr>
        <td colspan="5" class="TituloUsuario"></td>
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
					<td class="TituloTabla">Facturaci&oacute;n</td>
		          <td align="left" class="TxtTabla"><select name="facturacion" class="CajaTexto" id="facturacion" >
		            <option value="">::Seleccione Facturación::</option>

		            <option value="1" <? if($facturacion==1){ echo "selected"; } ?> >::Excede la planeación::</option>
		            <option value="2" <? if($facturacion==2){ echo "selected"; } ?> >::Inferior a la planeación::</option>
		            <option value="3" <? if($facturacion==3){ echo "selected"; } ?> >::Sin planeación::</option>
		          
	              </select></td>
	            </tr>
		        <tr class="TxtTabla" >
		          <td colspan="2" align="right" class="TxtTabla"><input type="hidden" name="recarga" value="0" id="recarga" />
		            <input type="hidden" name="ba" value="<?=$ba; ?>" id="ba" />
		<?php
			if((trim($division)!="") and (trim($lstVigencia)!="") and (trim($proyecto)!="") and ($recarga==1) )
			{
		?>
		            <input onclick="MM_openBrWindow('consolidado_vigencia_div_xls.php?division=<?=$division;?>&amp;departamento=<?=$departamento;?>&amp;categoria=<?=$categoria;?>&proyecto=<?=$proyecto; ?>&lstVigencia=<?=$lstVigencia; ?>&empleado=<?=$empleado; ?>','wRPT1','scrollbars=yes,resizable=yes,width=500,height=400')" type="button" class="Boton" value="Descargar en XLS" />

		<?php
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
            <td width="2%" class="TituloTabla2">P</td>
            <td width="5%" class="TxtTabla">Planeado</td>
            <td width="2%" class="TituloTabla2">F</td>
            <td width="5%" class="TxtTabla">Facturado</td>
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
            <td colspan="4" align="left" class="TituloUsuario">
<? if($facturacion==1) { $mensaje_fact="Facturaci&oacute;n que excede la planeaci&oacute;n"; }
					  if($facturacion==2) { $mensaje_fact="Facturaci&oacute;n que esta por debajo de lo planeado"; }
					  if($facturacion==3) { $mensaje_fact="Facturaci&oacute;n sin planeaci&oacute;n"; }
						echo $mensaje_fact;
					?>
			</td>
          </tr>

	
          <tr>
            <td colspan="4" align="right" class="TxtTabla">

<?
			while($datos_fac=mssql_fetch_array($cur_fact))
			{
?>
			<table width="100%" border="0">
              <tr>
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
							 and vigencia= T2.vigencia and esInterno='I' and mes=T2.mes GROUP BY mes,vigencia) total_facturacion_actividad
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
						 and vigencia= T2.vigencia and esInterno='I' and mes=T2.mes GROUP BY mes,vigencia) total_facturacion_actividad
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
							<td  >'.$datos_fact_exce_planea["id_actividad"]."[".$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"].'</td>
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
?>
                      <tr >
                        <td class="TxtTabla" >&nbsp;</td>
                        <td class="TxtTabla" >&nbsp;</td>
                        <td class="TxtTabla" >&nbsp;</td>
                        <td class="TxtTabla" >&nbsp;</td>
                        <td class="TxtTabla" >&nbsp;</td>
                        <td class="TxtTabla" >&nbsp;</td>
                      </tr>
<?
				}
?>
              </table>
<?	
			}
?>

<?
			while($datos_usuarios_planeados=mssql_fetch_array($cur_usuarios_planeados))
			{

			}

			if( ( (int) (mssql_num_rows($cur_usuarios_planeados) )==0) and(trim(mssql_num_rows($cur_usuarios_planeados))!=""))
			{
?>
          <tr >
                        <td colspan="19" align="left" class="TxtTabla"> </td>
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
