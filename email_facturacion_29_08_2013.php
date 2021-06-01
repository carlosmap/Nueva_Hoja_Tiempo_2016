<?php
session_start();
//include("../verificaRegistro2.php");
include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
include("fncEnviaMailPEAR.php");
$pAsunto = "Notificación situación no esperada";

$laUnidad=15712;
$cualProyecto=1547;
$cualVigencia=2013;
$cualMes=7;

		$sql_proy="select * from Proyectos where id_proyecto=".$cualProyecto;
		$cur_proy=mssql_query($sql_proy);
		if($datos_proy=mssql_fetch_array($$cur_proy))
		{	
			$nom_proy=$datos_proy["nombre"];
		}

		$sql_usu="select unidad, (HojaDeTiempo.dbo.Usuarios.nombre+' '+apellidos) nom_usu, UPPER( Departamentos.nombre) depto, upper(Divisiones.nombre) div from HojaDeTiempo.dbo.Usuarios 
		inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
		inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
		where HojaDeTiempo.dbo.Usuarios.unidad=".$laUnidad." and HojaDeTiempo.dbo.Usuarios.fechaRetiro is null";
		$cur_usu=mssql_query($sql_usu);
		if($datos_usu=mssql_fetch_array($cur_usu)) 
		{

				
?>
		<table width="100%" border="0" class="Estilo2">
				    <tr>
				      <td width="9%">&nbsp;</td>
				      <td width="91%">&nbsp;</td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Asunto</td>
				      <td>Notificación situación no esperada</td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Proyecto</td>
				      <td><?=$nom_proy; ?></td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Usuario</td>
				      <td><?=$datos_usu["nom_usu"] ?></td>
	      </tr>
				    <tr>
				      <td class="TituloTabla">División</td>
				      <td><?=$datos_usu["div"] ?></td>
	      </tr>
				    <tr>
				      <td class="TituloTabla">Departamento</td>
				      <td><?=$datos_usu["depto"] ?></td>
	      </tr>
				    <tr>
				      <td colspan="2">&nbsp;</td>
			        </tr>
			      </table>

<?
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
--where total_planeacion<total_facturacion_actividad
					";

		$CUR_FACT_EXCE_PLANEA=mssql_query($SQL_FAC_EXCE_PLANEA);
//echo $SQL_FAC_EXCE_PLANEA;
		$can_reg=mssql_num_rows($CUR_FACT_EXCE_PLANEA);
		if(0<$can_reg)
		{
?>
            <table width="100%" border="0">
              <tr>
                <td colspan="8" class="TituloUsuario">Facturación que excede la planeación</td>
              </tr>
              <tr class="TituloTabla2">
                <td>Actividad</td>
                <td>Horario</td>
                <td>CT</td>
                <td>Loc.</td>
                <td>Cargo</td>
                <td>Facturación</td>
            
              </tr>
<?php
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
			
								echo "<tr class='TxtTabla'><td colspan='5' >&nbsp;</td><td>
																						<table bgcolor='#FFFFFF' width='100%' >
																						<tr><td class='TituloTabla2' width='55%'>Total Facturaci&oacute;n </td><td class='TxtTabla'  align='right'> ".$total_f."</td>
																						</tr>
																						<tr><td class='TituloTabla2' width='55%' >Total Planeaci&oacute;n </td ><td class='TxtTabla' align='right'> ".$total."</td>
																						</tr>																			
																						</table> </td></tr>
<tr class='TituloUsuario'><td colspan='6' ></td></tr>";
			
			}
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
					planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$activi,$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
					$ban_activi=0;
				}


?>
              <tr class="TxtTabla">
                <td  ><?=$datos_fact_exce_planea["id_actividad"]."[".$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"]; ?></td>
                <td>
                <?
                                    $datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_fact_exce_planea["IDhorario"]));
                
                                    echo "[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];                
                ?> 
				</td>
                <td><?=$datos_fact_exce_planea["descripcion"]; ?></td>
                <td><?=$datos_fact_exce_planea["nomLocalizacion"]; ?></td>
                <td><?=$datos_fact_exce_planea["cargo"]; ?></td>
                <td align="right"><?=$datos_fact_exce_planea["total_facturacion"]; ?></td>            
              </tr>		
<?
				//A DEMAS SE IMPRIME LA FILA DE LOS TOTALES, CUANDO SE TRATA DE LA ULTIMA ACTIVIDAD A MOSTRAR
				if($can_res==$cont)
					planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$datos_fact_exce_planea["id_actividad"],$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
				$cont++;
			}

	}
?>
</table>
			<table>
				<tr><td>&nbsp;</td></tr>
            </table>
<?php
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
?>
            <table width="100%" border="0">
              <tr>
                <td colspan="8" class="TituloUsuario">Facturación que excede la planeación</td>
              </tr>
              <tr class="TituloTabla2">
                <td>Actividad</td>
                <td>Horario</td>
                <td>CT</td>
                <td>Loc.</td>
                <td>Cargo</td>
                <td>Facturación</td>
            
              </tr>
<?php

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
					planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$activi,$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
					$ban_activi=0;
				}


?>
              <tr class="TxtTabla">
                <td  ><?=$datos_fact_exce_planea["id_actividad"]."[".$datos_fact_exce_planea["macroactividad"]."] ".$datos_fact_exce_planea["nombre"]; ?></td>
                <td>
                <?
                                    $datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_fact_exce_planea["IDhorario"]));
                
                                    echo "[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];                
                ?> 
				</td>
                <td><?=$datos_fact_exce_planea["descripcion"]; ?></td>
                <td><?=$datos_fact_exce_planea["nomLocalizacion"]; ?></td>
                <td><?=$datos_fact_exce_planea["cargo"]; ?></td>
                <td align="right"><?=$datos_fact_exce_planea["total_facturacion"]; ?></td>            
              </tr>		
<?
				//A DEMAS SE IMPRIME LA FILA DE LOS TOTALES, CUANDO SE TRATA DE LA ULTIMA ACTIVIDAD A MOSTRAR
				if($can_res==$cont)
					planea_fact($datos_fact_exce_planea["id_proyecto"],$datos_fact_exce_planea["unidad"],$datos_fact_exce_planea["id_actividad"],$datos_fact_exce_planea["vigencia"],$datos_fact_exce_planea["mes"]);
				$cont++;
			}

	}
?>
            </table>
			<table>
				<tr><td>&nbsp;</td></tr>
            </table>

<?php


		//CONSULTA LA FACTURACION SIN PLANEACION
		$SQL_FACT_SIN_PLANEA="select  FacturacionProyectos.id_actividad,IDhorario,Clase_Tiempo.descripcion,TipoLocalizacion.nomLocalizacion,cargo,upper(nombre)nombre,macroactividad ,FacturacionProyectos.clase_tiempo, FacturacionProyectos.localizacion,FacturacionProyectos.cargo from FacturacionProyectos 
		 inner join TipoLocalizacion on TipoLocalizacion.localizacion=FacturacionProyectos.localizacion

		 inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=FacturacionProyectos.clase_tiempo
		 inner join Actividades on FacturacionProyectos.id_actividad=Actividades.id_actividad and FacturacionProyectos.id_proyecto=Actividades.id_proyecto
		 where FacturacionProyectos.id_proyecto=".$cualProyecto."  and unidad=".$laUnidad." and vigencia=".$cualVigencia."  and mes=".$cualMes."  and esInterno='I'
			and mes not in (
						select id_actividad from PlaneacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$cualVigencia." and mes=".$cualMes." and unidad=".$laUnidad." and esInterno='I'
				)
		  group by  FacturacionProyectos.id_actividad,IDhorario,Clase_Tiempo.descripcion,cargo,TipoLocalizacion.nomLocalizacion,nombre,macroactividad,FacturacionProyectos.clase_tiempo,  FacturacionProyectos.localizacion ,FacturacionProyectos.cargo ";

		$CUR_FACT_SIN_PLANEA=mssql_query($SQL_FACT_SIN_PLANEA);

		$can_reg=mssql_num_rows($CUR_FACT_SIN_PLANEA);
		if(0<$can_reg)
		{
?>

<table width="100%" border="0">
              <tr>
                <td colspan="7" class="TituloUsuario">Facturación sin planeación</td>
              </tr>
  <tr class="TituloTabla2">
                <td>Actividad</td>
                <td>Horario</td>
                <td>CT</td>
                <td>Loc.</td>
                <td>Cargo</td>
<!--
                <td>Planeación </td>
-->
                <td>Facturación</td>
  </tr>
<?php
			while($datos_fact_sin_planea=mssql_fetch_array($CUR_FACT_SIN_PLANEA))
			{
?>
  <tr class="TxtTabla">
    <td><?="[".$datos_fact_sin_planea["macroactividad"]."] ".$datos_fact_sin_planea["nombre"]; ?></td>
                    <td>
                <?
                                    $datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_fact_sin_planea["IDhorario"]));
                
                                    echo "[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];
                
                
                ?>
                    </td>
    <td><?=$datos_fact_sin_planea["descripcion"]; ?></td>
    <td><?=$datos_fact_sin_planea["nomLocalizacion"]; ?></td>
    <td><?=$datos_fact_sin_planea["cargo"]; ?></td>
                
                <?
                /*
                    <td>
                        $sql_total_fact="select  SUM(hombresMes) as total_H_M_P from PlaneacionProyectos where id_proyecto=".$cualProyecto."
                            and id_actividad=".$datos_fact_sin_planea["id_actividad"]." and vigencia=".$cualVigencia." and  unidad=".$laUnidad." and esInterno='I' and mes=".$cualMes."";
                
                        if($datos_total_fact=mssql_fetch_array(mssql_query($sql_total_fact)))
                        {
                            echo $datos_total_fact["total_H_M_P"];
                        }
                    </td>
                */
                ?>
                
                
                    <td>
                <?
                
                        $sql_total="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$datos_fact_sin_planea["id_actividad"]." and vigencia=".$cualVigencia." and  unidad=".$laUnidad."  and esInterno='I' and IDhorario=".$datos_fact_sin_planea["IDhorario"]." and mes=".$cualMes." and Clase_Tiempo=".$datos_fact_sin_planea["clase_tiempo"]." and localizacion=".$datos_fact_sin_planea["localizacion"]." and cargo=".$datos_fact_sin_planea["cargo"]." ";
                        $cur_total=mssql_query($sql_total);
                //echo mssql_get_last_message()."zsss <br> ".$sql_total;
                        if($datos_total=mssql_fetch_array($cur_total))
                            echo $datos_total["total_H_M_F"];		
                ?>
                    </td>
  </tr>
<?php
			}
?>
  </table>
<?
		}

	//ENVIA EL CORREO
	$pTema = $pTema.'</table>';
   $miMailUsuarioEM = 'carlosmaguirre' ;
   //***EnviarMailPEAR	
   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
?>
