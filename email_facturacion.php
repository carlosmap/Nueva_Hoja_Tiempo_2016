<?php
session_start();
//include("../verificaRegistro2.php");
include('../conectaBD.php');

//Establecer la conexi&oacute;n a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
include("fncEnviaMailPEAR.php");
$pAsunto = "H.T. Facturaci\xf3n - Notificaci\xf3n facturaci\xf3n vs planeaci\xf3n";

$laUnidad=15712;
$cualProyecto=683;
$cualVigencia=2013;
$cualMes=7;
$meses= array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

			//FUNCION QUE PINTA, LA FILA DEL TOTAL PLANEADO Y FACTURADO
			function planea_fact($id_proyecto,$unidad,$activi,$vigencia,$mes)
			{
										$sql_total="select  SUM(hombresMes) as total_H_M_P from PlaneacionProyectos where id_proyecto=".$id_proyecto." and id_actividad=".$activi." and vigencia=".$vigencia." and  unidad=".$unidad." and esInterno='I' and mes=".$mes." ";
			
										$cur_total=mssql_query($sql_total);
										if($datos_total=mssql_fetch_array($cur_total))
											$total=$datos_total["total_H_M_P"];		
			
									$sql_total_f="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$id_proyecto." and id_actividad=".$activi." and vigencia=".$vigencia." and  unidad=".$unidad. " and esInterno='I' and mes=".$mes;
			
//			echo "<br><br>".$sql_total_f ;
									$cur_total_f=mssql_query($sql_total_f);
									if($datos_total_f=mssql_fetch_array($cur_total_f))
										$total_f=$datos_total_f["total_H_M_F"];	
			
								$pTema=$pTema."<tr  class='Estilo2'><td colspan='5' >&nbsp;</td><td>
																						<table bgcolor='#FFFFFF' width='100%'  >
																						<tr><td class='Estilo2' width='55%'>Total Facturaci&oacute;n </td><td  class='Estilo2'  align='right'> ".$total_f."</td>
																						</tr>
																						<tr><td class='Estilo2' width='55%' >Total Planeaci&oacute;n </td ><td  class='Estilo2' align='right'> ".$total."</td>
																						</tr>																			
																						</table> </td></tr>
<tr class='TituloUsuario'><td colspan='6' bgcolor='#999999' ></td></tr>";
				return $pTema;
			}

		$sql_proy="select * from Proyectos where id_proyecto=".$cualProyecto;
		$cur_proy=mssql_query($sql_proy);
		if($datos_proy=mssql_fetch_array($cur_proy))
		{	
			$nom_proy=$datos_proy["nombre"];
		}

		$sql_usu="select unidad, (HojaDeTiempo.dbo.Usuarios.nombre+' '+apellidos) nom_usu, UPPER( Departamentos.nombre) depto, upper(Divisiones.nombre) div  from HojaDeTiempo.dbo.Usuarios 
		inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
		inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
		where HojaDeTiempo.dbo.Usuarios.unidad=".$laUnidad." and HojaDeTiempo.dbo.Usuarios.fechaRetiro is null";
		$cur_usu=mssql_query($sql_usu);
		if($datos_usu=mssql_fetch_array($cur_usu)) 
		{

				
			$pTema = '
		<table width="100%" border="0" class="Estilo2">
				    <tr>
				      <td width="10%">&nbsp;</td>
				      <td width="90%">&nbsp;</td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Asunto</td>
				      <td>H.T. Facturaci&oacute;n - Notificaci&oacute;n facturaci&oacute;n vs planeaci&oacute;n </td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Proyecto</td>
				      <td>'.$nom_proy.'</td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Usuario</td>
				      <td>['.$datos_usu["unidad"].'] '.$datos_usu["nom_usu"] .'</td>
	      </tr>
				    <tr>
				      <td class="TituloTabla">Divisi&oacute;n</td>
				      <td>'.$datos_usu["div"] .'</td>
	      </tr>
				    <tr>
				      <td class="TituloTabla">Departamento</td>
				      <td>'.$datos_usu["depto"] .'</td>
	      </tr>
	      </tr>
				    <tr>
				      <td class="TituloTabla">Mes Facturado</td>
				      <td>'.$meses[$cualMes] .'</td>
	      </tr>

	      </tr>
				    <tr>
				      <td class="TituloTabla">A&ntilde;o Facturado</td>
				      <td>'.$cualVigencia.'</td>
	      </tr>
				    <tr>
				      <td colspan="2">&nbsp;</td>
			        </tr>
			      </table>
			';


		}

		//CONSULTA LA INFORMACION DE LA FACTURACION QUE EXCEDE LA PLANEACION, REALIZANDO LA SUMATORIA TOTAL PLANEADO Y FACTURADO EN CADA ACTIVIDAD, ASOCIADA AL USUARIO
		//ADEMAS DE TOTALIZAR, EL VALOR FACTURADO, DISTINGUIENDO LOCALIZACION, CLASE DE TIEMPO, CARGO, Y CATEGORIA
		$SQL_FAC_EXCE_PLANEA="
select * from (
 select T2.*,upper(nombre)nombre,macroactividad,TipoLocalizacion.nomLocalizacion,descripcion,
 (select SUM(hombresMesF) total_planeacion from FacturacionProyectos where id_proyecto=T2.id_proyecto and id_actividad=T2.id_actividad and unidad=T2.unidad
	 and vigencia= T2.vigencia and esInterno='I' and mes=T2.mes GROUP BY mes,vigencia) total_facturacion_actividad
  from (select * , (
select SUM(hombresMes) total_planeacion from PlaneacionProyectos where id_proyecto=T1.id_proyecto and id_actividad=T1.id_actividad and unidad=".$laUnidad." and vigencia= T1.vigencia and esInterno=T1.esInterno and mes=T1.mes
GROUP BY hombresMes,mes
					) total_planeacion
				 from (					 
		select distinct(mes) mes, SUM(hombresMesF) total_facturacion, id_actividad,id_proyecto,unidad,esInterno,vigencia,localizacion,clase_Tiempo,cargo,IDhorario
		 from FacturacionProyectos where id_proyecto=".$cualProyecto."  and unidad=".$laUnidad." and vigencia=".$cualVigencia."  and esInterno='I' and mes=".$cualMes." 
		GROUP BY mes, id_actividad,id_proyecto,unidad,esInterno,vigencia ,localizacion,clase_Tiempo,cargo,IDhorario						
					) T1 
					) T2
							 inner join Actividades on T2.id_actividad=Actividades.id_actividad and T2.id_proyecto=Actividades.id_proyecto
							 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=T2.clase_tiempo
					 		 inner join TipoLocalizacion on TipoLocalizacion.localizacion=T2.localizacion
)T3				 		 

 where total_planeacion<total_facturacion_actividad
					";

		$CUR_FACT_EXCE_PLANEA=mssql_query($SQL_FAC_EXCE_PLANEA);
//echo $SQL_FAC_EXCE_PLANEA;
		$can_reg=mssql_num_rows($CUR_FACT_EXCE_PLANEA);
		if(0<((int)$can_reg))
		{
			$pTema=$pTema.'
            <table width="100%" border="1"  class="Estilo2">
              <tr>
                <td colspan="8" class="Estilo1" bgcolor="#999999">Facturaci&oacute;n que excede la planeaci&oacute;n</td>
              </tr>
              <tr class="TituloTabla2">
                <td>Actividad</td>
                <td>Horario</td>
                <td>CT</td>
                <td>Loc.</td>
                <td>Cargo</td>
                <td>Facturaci&oacute;n</td>
            
              </tr> ';


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
              <tr class="TxtTabla">
                <td  >['.$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"].'</td>
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
					$pTema=$pTema.planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$datos_fact_exce_planea["id_actividad"],$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
				$cont++;
			}

	}
			$pTema=$pTema.'
</table>
			<table  class="Estilo2">
				<tr><td>&nbsp;</td></tr>
            </table>';

		//CONSULTA LA INFORMACION DE LA FACTURACION QUE ES INFERIOR A LA PLANEACION, REALIZANDO LA SUMATORIA TOTAL PLANEADO Y FACTURADO EN CADA ACTIVIDAD, ASOCIADA AL USUARIO
		//ADEMAS DE TOTALIZAR EL VALOR FACTURADO, DISTINGUIENDO LOCALIZACION, CLASE DE TIEMPO, CARGO, Y CATEGORIA
	$SQL_FAC_INFE_PLANEA="
select * from (
 select T2.*,upper(nombre)nombre,macroactividad,TipoLocalizacion.nomLocalizacion,descripcion,
 (select SUM(hombresMesF) total_planeacion from FacturacionProyectos where id_proyecto=T2.id_proyecto and id_actividad=T2.id_actividad and unidad=T2.unidad
	 and vigencia= T2.vigencia and esInterno='I' and mes=T2.mes GROUP BY mes,vigencia) total_facturacion_actividad
  from (select * , (
select SUM(hombresMes) total_planeacion from PlaneacionProyectos where id_proyecto=T1.id_proyecto and id_actividad=T1.id_actividad and unidad=".$laUnidad." and vigencia= T1.vigencia and esInterno=T1.esInterno and mes=T1.mes
GROUP BY hombresMes,mes
					) total_planeacion
				 from (					 
		select distinct(mes) mes, SUM(hombresMesF) total_facturacion, id_actividad,id_proyecto,unidad,esInterno,vigencia,localizacion,clase_Tiempo,cargo,IDhorario
		 from FacturacionProyectos where id_proyecto=".$cualProyecto."  and unidad=".$laUnidad." and vigencia=".$cualVigencia."  and esInterno='I' and mes=".$cualMes." 
		GROUP BY mes, id_actividad,id_proyecto,unidad,esInterno,vigencia ,localizacion,clase_Tiempo,cargo,IDhorario						
					) T1 
					) T2
							 inner join Actividades on T2.id_actividad=Actividades.id_actividad and T2.id_proyecto=Actividades.id_proyecto
							 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=T2.clase_tiempo
					 		 inner join TipoLocalizacion on TipoLocalizacion.localizacion=T2.localizacion
)T3				 		 
	where total_planeacion>total_facturacion_actividad			";

		$CUR_FACT_INFE_PLANEA=mssql_query($SQL_FAC_INFE_PLANEA);
//echo $SQL_FAC_EXCE_PLANEA." **** ";
		$can_reg=mssql_num_rows($CUR_FACT_INFE_PLANEA);
		if(0<$can_reg)
		{
			$pTema=$pTema.'
            <table width="100%" border="1"  class="Estilo2">
              <tr>
                <td colspan="8"   class="Estilo1" bgcolor="#999999">Facturaci&oacute;n que esta por debajo de lo planeado</td>
              </tr>
              <tr class="TituloTabla2">
                <td>Actividad</td>
                <td>Horario</td>
                <td>CT</td>
                <td>Loc.</td>
                <td>Cargo</td>
                <td>Facturaci&oacute;n</td>
            
              </tr>';

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
                <td  >['.$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"].'</td>
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
					$pTema=$pTema.planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$datos_fact_exce_planea["id_actividad"],$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
				$cont++;
			}

	}
		$pTema=$pTema.'
            </table>
			<table  class="Estilo2">
				<tr><td>&nbsp;</td></tr>
            </table> ';


		//CONSULTA LA FACTURACION SIN PLANEACION
		$SQL_FACT_SIN_PLANEA="select  FacturacionProyectos.id_actividad,IDhorario,Clase_Tiempo.descripcion,TipoLocalizacion.nomLocalizacion,cargo,upper(nombre)nombre,macroactividad ,FacturacionProyectos.clase_tiempo, FacturacionProyectos.localizacion,FacturacionProyectos.cargo from FacturacionProyectos 
		 inner join TipoLocalizacion on TipoLocalizacion.localizacion=FacturacionProyectos.localizacion

		 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=FacturacionProyectos.clase_tiempo
		 inner join Actividades on FacturacionProyectos.id_actividad=Actividades.id_actividad and FacturacionProyectos.id_proyecto=Actividades.id_proyecto
		 where FacturacionProyectos.id_proyecto=".$cualProyecto."  and unidad=".$laUnidad." and vigencia=".$cualVigencia."  and mes=".$cualMes."  and esInterno='I'
			and FacturacionProyectos.id_actividad not in (
						select id_actividad from PlaneacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$cualVigencia." and mes=".$cualMes." and unidad=".$laUnidad." and esInterno='I'
				)
		  group by  FacturacionProyectos.id_actividad,IDhorario,Clase_Tiempo.descripcion,cargo,TipoLocalizacion.nomLocalizacion,nombre,macroactividad,FacturacionProyectos.clase_tiempo,  FacturacionProyectos.localizacion ,FacturacionProyectos.cargo ";

		$CUR_FACT_SIN_PLANEA=mssql_query($SQL_FACT_SIN_PLANEA);

		$can_reg=mssql_num_rows($CUR_FACT_SIN_PLANEA);
		if(0<$can_reg)
		{
			$pTema=$pTema.'
<table width="100%" border="1"  class="Estilo2">
              <tr>
                <td colspan="7"  class="Estilo1" bgcolor="#999999">Facturaci&oacute;n sin planeaci&oacute;n</td>
              </tr>
  <tr class="TituloTabla2">
                <td>Actividad</td>
                <td>Horario</td>
                <td>CT</td>
                <td>Loc.</td>
                <td>Cargo</td>
			
                <td>Facturaci&oacute;n</td>
	  </tr> ';

			$ban_activi=0;
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

					$pTema=$pTema. '<tr class="TxtTabla"><td colspan="6" bgcolor="#999999" ></td></tr>';
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
                
                        $sql_total="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$datos_fact_sin_planea["id_actividad"]." and vigencia=".$cualVigencia." and  unidad=".$laUnidad."  and esInterno='I' and IDhorario=".$datos_fact_sin_planea["IDhorario"]." and mes=".$cualMes." and Clase_Tiempo=".$datos_fact_sin_planea["clase_tiempo"]." and localizacion=".$datos_fact_sin_planea["localizacion"]." and cargo=".$datos_fact_sin_planea["cargo"]." ";
                        $cur_total=mssql_query($sql_total);
                //echo mssql_get_last_message()."zsss <br> ".$sql_total;
                        if($datos_total=mssql_fetch_array($cur_total))
                            $pTema=$pTema.$datos_total["total_H_M_F"];		

                 $pTema=$pTema.'   </td>
  </tr>';

			}
				$pTema=$pTema.'
  </table>	
				';
		}
//echo  mssql_num_rows($CUR_FACT_EXCE_PLANEA)." --- ". mssql_num_rows($CUR_FACT_INFE_PLANEA)." --- ". mssql_num_rows($CUR_FACT_SIN_PLANEA);
	//SE ENVIA EL CORREO AL USUARIO, SIEMPRE Y CUANDO, ALGUNA DE LAS CONSULTAS, HALLA ARROJADO POR LO MENOS UN REGISTRO
	if ((0<( (int) mssql_num_rows($CUR_FACT_EXCE_PLANEA)))||(0< ((int) mssql_num_rows($CUR_FACT_INFE_PLANEA)))||(0< ( (int) mssql_num_rows($CUR_FACT_SIN_PLANEA))))
	{
		//ENVIA EL CORREO
		$pTema = $pTema.'<BR><BR>';
	   $miMailUsuarioEM = 'carlosmaguirre' ;
	   //***EnviarMailPEAR	
	   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
	   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
	}
?>
