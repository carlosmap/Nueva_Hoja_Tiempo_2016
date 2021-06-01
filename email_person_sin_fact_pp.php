
<?php
 
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

function getdates()
{
	return '2013/08/04';
}
	include("fncEnviaMailPEAR.php");
	$pAsunto = "Personal sin registro de facturaci\xf3n";


	//CONSULTA EL NUMERO DE SEMANA ACTUAL DEL MES 
	$cur_semana_ac=mssql_query("select (datepart(week,'".getdates()."')- datepart(week, dateadd(dd,-day('".getdates()."')+1,'".getdates()."'))+1 ) No_semana_actual");

	$dato_semana_actual=mssql_fetch_array($cur_semana_ac);
	$semana_actual =((int) $dato_semana_actual["No_semana_actual"])-1;
echo "select (datepart(week,'".getdates()."')- datepart(week, dateadd(dd,-day('".getdates()."')+1,'".getdates()."'))+1 ) No_semana_actual ".$semana_actual."<br>";

	$meses= array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
/*
	//CONSULTA LA FE
	$cur_fecha=mssql_query("select *,YEAR(fecha_inicio_semana) ano_inicio , YEAR(fecha_fin_semana) ano_fin from (
							select DATEADD(DAY,-7, '".getdates()."') fecha_inicio_semana , '".getdates()."' fecha_fin_semana )aa");
	$datos_fecha=mssql_fetch_array($cur_fecha);
*/



	$cur_division=mssql_query("select *, convert(nvarchar,'".getdates()."',103) as fecha ,MONTH('".getdates()."') mes, year('".getdates()."') ano from HojaDeTiempo.dbo.Divisiones where estadoDiv='A' and id_division=14  ");
	while($datos_divi=mssql_fetch_array($cur_division)) 
	{
				$pTema = '
				<table width="100%" border="0" class="Estilo2">
				  <tr>
					<td width="9%">&nbsp;</td>
					<td width="91%">&nbsp;</td>
				  </tr>
				  <tr>
					<td>Asunto:</td>
					<td>Personal sin registro de facturaci&oacute;n</td>
				  </tr>
				  <tr>
					<td>Divisi&oacute;n:</td>
					<td>'.$datos_divi["nombre"].'</td>
				  </tr>
				  <tr>
					<td>Fecha:</td>
					<td>'.$datos_divi["fecha"].'</td>
				  </tr>




				  <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>

				  <tr>
					<td colspan="2" >Reporte de facturaci&oacute;n de los usuarios de la divisi&oacute;n <b> '.$datos_divi["nombre"].'</b>, que tienen facturaci&oacuten pendiente por registrar. En el mes de <b> '.$meses[$datos_divi["mes"]].'  del '.$datos_divi["ano"].'</b>.</td>
				  </tr>

				</table>
				<br>';
		$cur_deptos=mssql_query("select * from HojaDeTiempo.dbo.Departamentos where estadoDpto='A' and id_division=14 and id_departamento=263 ");// .$datos_divi["id_division"]);
		while($datos_depto=mssql_fetch_array($cur_deptos))
		{

			$pTema = $pTema.'	<table width="100%" border="1" class="Estilo2">
				  <tr>
					<td>Departamento</td>
					<td colspan="7">'.$datos_depto["nombre"].'</td>
				  </tr>
				  <tr>
					<td colspan="2">
						<table class="Estilo2">
							  <tr >
								<td width="20%"  bgcolor="#999999">&nbsp; </td>
								<td> Con  facturaci&oacute;n </td>
								<td    bgcolor="#e9e9e9" width="20%" >&nbsp; </td>
								<td> Sin facturaci&oacute;n</td>
							  </tr>
						</table>
					</td>
					<td colspan="6" align="center">'.$meses[$datos_divi["mes"]].' - '.$datos_divi["ano"].'</td>
				  </tr>
				  <tr>
					<td align="center" width="10%">Unidad</td>
					<td align="center"  width="30%">Usuario</td>';

					$cont_semana=1;

					//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
					while($cont_semana<=$semana_actual)
					{

/////////////////////*****************************************************************(INICIO) CODIGO DE FACTURACION DEL MES ANTERIOR
						//SI LA SEMANA ACTUAL ES LA PRIMERA DEL MES, VERIFICA LOS DIAS DE LA SEMANA DEL MES ANTERIOR
						if($semana_actual==1)
						{
							//SELECCIONA EL DIA DE LA SEMANA, EN LA QUE CAHE EL PRIMER DIA DEL MES
							$datos_dia_mes=mssql_fetch_array(mssql_query("select DATEPART(DW,'".$datos_divi["ano"]."/".$datos_divi["mes"]."/1') dia_semana "));
							$dia_semana_pri_di_mes=$datos_dia_mes["dia_semana"];

							//SI EL PRIMER DIA del mes, NO es  el primero DE LA SEMANA
							if($dia_semana_pri_di_mes!=1)
							{


								//SI pTema2, NO SE HA DEFINIDO
								if(!(isset($pTema2)))
									$pTema2=$pTema;

								//CONSULTA LA FECHA INICIAL DE LA ULTIMA SEMANA DEL MES ANTERIOR
								$datos_fecha_fin=mssql_fetch_array(mssql_query("select *, YEAR(fecha_inicio_semana) ano,MONTH(fecha_inicio_semana) mes  from(
select DATEADD(DAY,-7, '".getdates()."' ) fecha_inicio_semana 
)fechas "));
								$fecha_semana_inicio_mes_ante=$datos_fecha_fin["fecha_inicio_semana"];
/*
								$ano_semana_inicio_mes_ante=$datos_fecha_fin["ano"];
								$mes_semana_inicio_mes_ante=$datos_fecha_fin["mes"];
*/
/*
								//AÑADE UN "0" AL MES, SI ES INFERIOR A 10, PARA QUE LA SIGUIENTE COSNULTA, FUNCIONE
								if( ((int) $datos_divi["mes"] ) <10)
									$datos_divi["mes"]='0'.$datos_divi["mes"];
*/
								if (((int) $datos_fecha_fin["mes"])<9)
										$datos_fecha_fin["mes"]='0'.$datos_fecha_fin["mes"];
								
								//CONSULTA EL ULTIMO DIA DEL MES ANTERIOR
								$datos_semana_fin_mes_ante=mssql_fetch_array(mssql_query("select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$datos_fecha_fin["ano"]."' + '".$datos_fecha_fin["mes"]."' + '01')))) dia "));

								//COMPONE LA FECHA FINAL DEL MES ANTERIOR
								$fecha_semana_fin_mes_ante="".$datos_fecha_fin["ano"]."/".$datos_fecha_fin["mes"]."/".$datos_semana_fin_mes_ante["dia"];

echo "Ingresosss <br>".$fecha_semana_inicio_mes_ante."<br>".$fecha_semana_fin_mes_ante."<br>";
echo "<br>"."select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$datos_fecha_fin["ano"]."' + '".$datos_fecha_fin["mes"]."' + '01')))) dia <br> ";

								//CONSULTA EL NUMERO DE SEMANAS DEL MES ANTERIOR
								$cur_semana_ant=mssql_query("select (datepart(week,'".$fecha_semana_fin_mes_ante."')- datepart(week, dateadd(dd,-day('".$fecha_semana_fin_mes_ante."')+1,'".$fecha_semana_fin_mes_ante."'))+1 ) No_semana_ant");
								$dato_semana_mes_ant=mssql_fetch_array($cur_semana_ant);
								$semana_mes_ant =( (int)$dato_semana_mes_ant["No_semana_ant"]);
echo "Semana mes ant".$dato_semana_mes_ant["No_semana_ant"]."<br>";
//-------------------------------------
								$cont_semana_mes_ant=1;
			
								//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
								while($cont_semana_mes_ant<=$semana_mes_ant)
								{
									//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA INICIAL DE LA SEMANA
									$cantidad_dias_inicial=7*($semana_mes_ant-$cont_semana_mes_ant);
									//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA FINAL DE LA SEMANA
									$cantidad_dias_fina=( (int) $cantidad_dias_inicial)-7;
			//echo $cantidad_dias_fina."**** <br><br>";
			
									//CONSULTA LAS FECHA A UTILIZAR EN LA CONSULTA DE FACTURACION, PARA LA SEMANA (FECHA INICIO), (FECHA FIN), (ANO INICIO), (ANO FIN)
									//(ANO INICIO), (ANO FIN), SE UTILIZAN, PARA TRAHER LA INFO DE LA FACTURACION, CUANDO LA SEMANA, ES LA PRIMERA DEL AÑO, Y ESTA CONTIENE DIAS DEL 
									//ANO ANTERIOR
									$cur_fechas_consul_factu=mssql_query(" select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
																select DATEADD(DAY,-".$cantidad_dias_inicial.", '".$fecha_semana_fin_mes_ante."') fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", '".$fecha_semana_fin_mes_ante."')fecha_fin_semana )aa");

echo " select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
																select DATEADD(DAY,-".$cantidad_dias_inicial.", '".$fecha_semana_fin_mes_ante."') fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", '".$fecha_semana_fin_mes_ante."')fecha_fin_semana )aa <br><br>";


									$datos_fechas=mssql_fetch_array($cur_fechas_consul_factu);
			
									$ano_inicio[$cont_semana]=$datos_fechas["ano_inicio"];
									$ano_fin[$cont_semana]=$datos_fechas["ano_fin"];
			
									$dia_inicio_semana=$datos_fechas["dia_inicio"];
									$dia_fin_semana=$datos_fechas["dia_fin"];
			
									$mes_inicio_semana=$datos_fechas["mes_inicio"];
									$mes_fin_semana=$datos_fechas["mes_fin"];
			
									$fecha_inicio_semana[$cont_semana]=$datos_fechas["fecha_inicio_semana"];
									$fecha_fin_semana[$cont_semana]=$datos_fechas["fecha_fin_semana"];
			
										$pTema2= $pTema2.'<td align="center">Semana '.$cont_semana_mes_ant.' <br /> 
										('.$dia_inicio_semana.'/'.$mes_inicio_semana.'/'.$ano_inicio[$cont_semana].') - ('.$dia_fin_semana.'/'.$mes_fin_semana.'/'.$ano_fin[$cont_semana].')</td>' ;
			
									$cont_semana_mes_ant++;
								}
								$pTema2 = $pTema2.'</tr>';

								//CONSULTA LOS USUARIOS ACTIVOS, QUE HACEN PARTE DEL DEPARTAMENTO
								$cur_usu_depto=mssql_query("select HojaDeTiempo.dbo.Usuarios.id_departamento, HojaDeTiempo.dbo.Divisiones.id_division, unidad, upper((HojaDeTiempo.dbo.Usuarios.apellidos+' '+HojaDeTiempo.dbo.Usuarios.nombre)) nom_usu from HojaDeTiempo.dbo.Usuarios 
								inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
								inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
								where  HojaDeTiempo.dbo.Usuarios.fechaRetiro is null 
								and HojaDeTiempo.dbo.Usuarios.id_departamento=".$datos_depto["id_departamento"]." and HojaDeTiempo.dbo.Divisiones.id_division=".$datos_divi["id_division"]." order by apellidos");
								while($datos_usu_depto=mssql_fetch_array($cur_usu_depto))
								{
				
									$pTema2 = $pTema2.'  <tr>
										<td  width="10%" align="center" >'.$datos_usu_depto["unidad"].'</td>
										<td  width="30%">'.$datos_usu_depto["nom_usu"].'</td>';
				
									//CONSTADOR DE LAS SEMANAS 
									$cont_semana_mes_ant=1;
				
									//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION, EN EL MES ANTERIOR
									while($cont_semana_mes_ant<=$semana_mes_ant-1)
									{
				
										//CONSULTA LA CANTIDAD DE REGISTROS CON FACTURACION, DENTRO DE LAS FECHA INDICADAS
										$cur_fact=mssql_query(" select count(*)  cant_reg from HojaDeTiempo.dbo.FacturacionProyectos where  vigencia between ".$ano_inicio[$cont_semana]."  and ".$ano_fin[$cont_semana]." and esInterno='I' 
				and fechaFacturacion between '".$fecha_semana_inicio_mes_ante."' and '".$fecha_semana_fin_mes_ante."' and unidad=".$datos_usu_depto["unidad"]);
										$datos_reg_fact=mssql_fetch_array($cur_fact);
				
										$cant_reg_fact=$datos_reg_fact["cant_reg"];
				
										//SI NO HA REGISTROS DE FACTURACION
										if(( (int) $cant_reg_fact)==0)
										{
											$pTema2 = $pTema2.'<td align="center" bgcolor="#e9e9e9" ></td>';
										}
				
										//SI HAY REGISTROS DE FACTURACION
										if(0<( (int) $cant_reg_fact))
										{
											$pTema2= $pTema2.'<td bgcolor="#999999"> &nbsp;</td>';
										}
										$cont_semana_mes_ant++;
				
									}
				
									$pTema2 = $pTema2.' </tr>';
								}
//-------------------------------
								
								
								

							}//FI $dia_semana_pri_di_mes!=1
						} ///IF SEMANA ACTUAL==1
	/////////////////////*****************************************************************(FIN) CODIGO DE FACTURACION DEL MES ANTERIOR

						//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA INICIAL DE LA SEMANA
						$cantidad_dias_inicial=7*($semana_actual-$cont_semana);
						//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA FINAL DE LA SEMANA
						$cantidad_dias_fina=( (int) $cantidad_dias_inicial)-7;
//echo $cantidad_dias_fina."**** <br><br>";

						//CONSULTA LAS FECHA A UTILIZAR EN LA CONSULTA DE FACTURACION, PARA LA SEMANA (FECHA INICIO), (FECHA FIN), (ANO INICIO), (ANO FIN)
						//(ANO INICIO), (ANO FIN), SE UTILIZAN, PARA TRAHER LA INFO DE LA FACTURACION, CUANDO LA SEMANA, ES LA PRIMERA DEL AÑO, Y ESTA CONTIENE DIAS DEL 
						//ANO ANTERIOR
						$cur_fechas_consul_factu=mssql_query(" select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
													select DATEADD(DAY,-".$cantidad_dias_inicial.", '".getdates()."') fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", '".getdates()."')fecha_fin_semana )aa");

echo"222222222 select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
													select DATEADD(DAY,-".$cantidad_dias_inicial.", '".getdates()."') fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", '".getdates()."')fecha_fin_semana )aa <br><br>";
						$datos_fechas=mssql_fetch_array($cur_fechas_consul_factu);

						$ano_inicio[$cont_semana]=$datos_fechas["ano_inicio"];
						$ano_fin[$cont_semana]=$datos_fechas["ano_fin"];

						$dia_inicio_semana=$datos_fechas["dia_inicio"];
						$dia_fin_semana=$datos_fechas["dia_fin"];

						$mes_inicio_semana=$datos_fechas["mes_inicio"];
						$mes_fin_semana=$datos_fechas["mes_fin"];

						//SI SE ESTA RECORRIENDO LA PRIMERA SEL MES, SE COMPONE LA PRIMERA FECHA DE INICIO, CON EL DIA 01 DEL MES
						//ESTO PARA NO TOMAR LOS DIAS DEL MES ANTERIOR
						if($cont_semana==1)
						{
							$fecha_inicio_semana[$cont_semana]=$datos_fechas["ano_fin"].'/'.$mes_fin_semana.'/01';
							$dia_inicio_semana="1";
						}
						else
							$fecha_inicio_semana[$cont_semana]=$datos_fechas["fecha_inicio_semana"];

						$fecha_fin_semana[$cont_semana]=$datos_fechas["fecha_fin_semana"];

							$pTema = $pTema.'<td align="center">Semana '.$cont_semana.' <br /> 
							('.$dia_inicio_semana.'/'.$mes_fin_semana.'/'.$ano_inicio[$cont_semana].') - ('.$dia_fin_semana.'/'.$mes_fin_semana.'/'.$ano_fin[$cont_semana].')</td>' ;

						$cont_semana++;
					}
					$pTema = $pTema.'</tr>';

				//CONSULTA LOS USUARIOS ACTIVOS, QUE HACEN PARTE DEL DEPARTAMENTO
				$cur_usu_depto=mssql_query("select HojaDeTiempo.dbo.Usuarios.id_departamento, HojaDeTiempo.dbo.Divisiones.id_division, unidad, upper((HojaDeTiempo.dbo.Usuarios.apellidos+' '+HojaDeTiempo.dbo.Usuarios.nombre)) nom_usu from HojaDeTiempo.dbo.Usuarios 
				inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
				inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
				where  HojaDeTiempo.dbo.Usuarios.fechaRetiro is null 
				and HojaDeTiempo.dbo.Usuarios.id_departamento=".$datos_depto["id_departamento"]." and HojaDeTiempo.dbo.Divisiones.id_division=".$datos_divi["id_division"]." order by apellidos");
				while($datos_usu_depto=mssql_fetch_array($cur_usu_depto))
				{

					$pTema = $pTema.'  <tr>
						<td  width="10%" align="center" >'.$datos_usu_depto["unidad"].'</td>
						<td  width="30%">'.$datos_usu_depto["nom_usu"].'</td>';

					//CONSTADOR DE LAS SEMANAS 
					$cont_semana=1;

					//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
					while($cont_semana<=$semana_actual-1)
					{

						//CONSULTA LA CANTIDAD DE REGISTROS CON FACTURACION, DENTRO DE LAS FECHA INDICADAS
						$cur_fact=mssql_query(" select count(*)  cant_reg from HojaDeTiempo.dbo.FacturacionProyectos where  vigencia between ".$ano_inicio[$cont_semana]."  and ".$ano_fin[$cont_semana]." and esInterno='I' 
and fechaFacturacion between '".$fecha_inicio_semana[$cont_semana]."' and '".$fecha_fin_semana[$cont_semana]."' and unidad=".$datos_usu_depto["unidad"]);
						$datos_reg_fact=mssql_fetch_array($cur_fact);
echo
" select count(*)  cant_reg from HojaDeTiempo.dbo.FacturacionProyectos where  vigencia between ".$ano_inicio[$cont_semana]."  and ".$ano_fin[$cont_semana]." and esInterno='I' 
and fechaFacturacion between '".$fecha_inicio_semana[$cont_semana]."' and '".$fecha_fin_semana[$cont_semana]."' and unidad=".$datos_usu_depto["unidad"]."<br>";

						$cant_reg_fact=$datos_reg_fact["cant_reg"];

						//SI NO HA REGISTROS DE FACTURACION
						if(( (int) $cant_reg_fact)==0)
						{
							$pTema = $pTema.'<td align="center" bgcolor="#e9e9e9" ></td>';
						}

						//SI HAY REGISTROS DE FACTURACION
						if(0<( (int) $cant_reg_fact))
						{
							$pTema = $pTema.'<td bgcolor="#999999"> &nbsp;</td>';
						}
						$cont_semana++;

					}


				}
				$pTema = $pTema.' </tr>';
		}/// while depto
		$pTema = $pTema.'</table>';
		   $miMailUsuarioEM = 'carlosmaguirre' ;
	
		   //***EnviarMailPEAR	
		   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
	
		   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

		//SI SE GENERO EMAIL, PARA LOS DIAS DEL MES ANTERIOR
echo $pTema2."*******";
		if(trim($pTema2)!="")
		{
echo "ingresa1";
			$pTema2 = $pTema2.'</table>';
			   $miMailUsuarioEM = 'carlosmaguirre' ;
		
			   //***EnviarMailPEAR	
			   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
		
			   enviarCorreo($pPara, $pAsunto, $pTema2, $pFirma);
		}
	}//while división
	


	

?>
