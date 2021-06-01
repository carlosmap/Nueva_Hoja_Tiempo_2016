<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<title>Documento sin título</title>
</head>

<body class="TxtTabla" >
<table  width="100%"  border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td height="2" align="center" class="TxtTabla"><table width="30%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr>
        <td class="TituloUsuario" height="2" colspan="2" >Criterios de consulta </td>
      </tr>
      <tr class="TxtTabla" >
        <td class="TituloTabla">División</td>
        <td  align="left">&nbsp;</td>
      </tr>
      <tr class="TxtTabla" >
        <td class="TituloTabla">Departamento</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr class="TxtTabla" >
        <td width="15%" class="TituloTabla">Vigencia</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr class="TxtTabla" >
        <td class="TituloTabla">Mes</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td width="15%" class="TituloTabla">Proyecto</td>
        <td align="left" class="TxtTabla">&nbsp;</td>
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
    </table></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
          <tr>
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de USUARIOS </a></td>
  
          </tr>
  <tr>
    <td class="TituloUsuario">Usuarios que facturaron en el proyecto</td>
  </tr>

        </table>

<table width="100%" border="0" bgcolor="#FFFFFF" cellspacing="1">
  <tr>
    <td class="TituloTabla">Cantidad de Usuarios</td>
    <td class="TxtTabla">3</td>
    <td class="TituloTabla">Cant. Usuarios todos Vobo de proyecto</td>
    <td class="TxtTabla">0</td>
    <td class="TituloTabla">Cant. Usuarios con Vobo del jefe</td>
    <td class="TxtTabla">3</td>
    <td class="TituloTabla">Cant. Usuarios sin Vobo del contratos</td>
    <td class="TxtTabla">1</td>
  </tr>
</table>
<table width="100%" border="0"  bgcolor="#FFFFFF" cellspacing="1">
  <tr>
    <td width="1%"  align="center" ><? if(trim($datos_fac["fechaRetiro"])!="") { ?>
      <img src="imagenes/Inactivo.gif" title="Retirado de la compañia" />
      <? } ?></td>
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

<table width="100%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td width="1%" class="TituloTabla2" ></td>
    <td width="10%" class="TituloTabla2" >Actividad</td>
    <td colspan="2"  class="TituloTabla2">Con planeacion</td>
    <td width="14%"  class="TituloTabla2">Facturaci&oacute;n</td>
    <td width="5%"  class="TituloTabla2">Vobo Proyecto</td>
    <td width="14%"  class="TituloTabla2">Vobo Jefe</td> 
    <td width="14%"  class="TituloTabla2">Vobo Contratos</td>
  </tr>
  <tr>
    <td class="TxtTabla" ></td>
    <td class="TxtTabla" >[LT1.1.4] PLANEAMIENTO</td>
    <td width="5%"  class="TxtTabla"><span class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></span></td>
    <td width="9%"  class="TxtTabla">0.1</td>
    <td  class="TxtTabla">0.2</td>
    <td width="5%"  class="TxtTabla"><span class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></span></td>
    <td  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td  class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
  </tr>
  <tr>
    <td class="TxtTabla" ><img title="Activo en la actividad" src="img/images/alertaAzul.gif"  width="15" height="13" /></td>
    <td class="TxtTabla" >[LT1.1.2.A.1] Act2</td>
    <td  class="TxtTabla"><span class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></span></td>
    <td  class="TxtTabla">0.4</td>
    <td  class="TxtTabla">  <strong>0.5 </strong></td>
    <td width="5%"  class="TxtTabla"><span class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></span></td>
    <td  class="TxtTabla"><span class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></span></td>
    <td  class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td class="TxtTabla" ></td>
    <td class="TxtTabla" >[LT1.1.3.A.1] PPP</td>

    <td  class="TxtTabla"><span class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></span></td>
    <td  class="TxtTabla">&nbsp;</td>
    <td  class="TxtTabla">0.3</td>
    <td width="5%"  class="TxtTabla"><span class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></span></td>
    <td  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
  </tr>
  <tr>
    <td colspan="2" class="TxtTabla" ></td>
    <td  class="TituloTabla2">Totales</td>
    <td  class="TxtTabla">0.5</td>
    <td  class="TxtTabla">1.0</td>
    <td  class="TxtTabla">&nbsp;</td>
    <td  class="TxtTabla">&nbsp;</td>
    <td  class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <?
			 	if(($datos_fac["tipo"]==1)||($datos_fac["tipo"]==2))
				{
?>
    <td colspan="12" class="TxtTabla" >&nbsp; </td>
    <?
				}
?>
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
</table>
<table width="100%" border="0"  bgcolor="#FFFFFF" cellspacing="1">
  <tr>
    <td width="1%"  align="center" ><? if(trim($datos_fac["fechaRetiro"])!="") { ?>
      <img src="imagenes/Inactivo.gif" title="Retirado de la compañia" />
      <? } ?></td>
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
<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td width="1%" class="TituloTabla2" ></td>
    <td width="10%" class="TituloTabla2" >Actividad</td>
    <td colspan="2"  class="TituloTabla2">Con planeacion</td>
    <td width="14%"  class="TituloTabla2">Facturaci&oacute;n</td>
    <td width="5%"  class="TituloTabla2">Vobo Proyecto</td>
    <td width="14%"  class="TituloTabla2">Vobo Jefe</td>
    <td width="14%"  class="TituloTabla2">Vobo Contratos</td>
  </tr>
  <tr>
    <td class="TxtTabla" ></td>
    <td class="TxtTabla" >[LT1.1.4] PLANEAMIENTO</td>
    <td width="5%"  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td width="9%"  class="TxtTabla">0.1</td>
    <td  class="TxtTabla">0.2</td>
    <td width="5%"  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td  class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
  </tr>
  <tr>
    <td class="TxtTabla" ><img title="Activo en la actividad" src="img/images/alertaAzul.gif"  width="15" height="13" /></td>
    <td class="TxtTabla" >[LT1.1.2.A.1] Act2</td>
    <td  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td  class="TxtTabla">0.4</td>
    <td  class="TxtTabla"><strong>0.5</strong></td>
    <td width="5%"  class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
    <td  class="TxtTabla"><span class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></span></td>
    <td  class="TxtTabla"><span class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></span></td>
  </tr>
  <tr>
    <td class="TxtTabla" ></td>
    <td class="TxtTabla" >[LT1.1.3.A.1] PPP</td>
    <td  class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
    <td  class="TxtTabla">&nbsp;</td>
    <td  class="TxtTabla">0.3</td>
    <td width="5%"  class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
    <td  class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
    <td  class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="TxtTabla" ></td>
    <td  class="TituloTabla2">Totales</td>
    <td  class="TxtTabla">0.5</td>
    <td  class="TxtTabla">1.0</td>
    <td  class="TxtTabla">&nbsp;</td>
    <td  class="TxtTabla">&nbsp;</td>
    <td  class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <?
			 	if(($datos_fac["tipo"]==1)||($datos_fac["tipo"]==2))
				{
?>
    <td colspan="12" class="TxtTabla" >&nbsp;</td>
    <?
				}
?>
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
</table>
</body>
</html>