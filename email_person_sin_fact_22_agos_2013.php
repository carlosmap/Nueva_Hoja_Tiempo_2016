
<?php
 
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


	include("fncEnviaMailPEAR.php");
	$pAsunto = "Personal sin registro de facturación";


	//CONSULTA EL NUMERO DE SEMANA ACTUAL DEL MES 
	$cur_semana_ac=mssql_query("select (datepart(week,getdate())- datepart(week, dateadd(dd,-day(getdate())+1,getdate()))+1 ) No_semana_actual");
	$dato_semana_actual=mssql_fetch_array($cur_semana_ac);
	$semana_actual =$dato_semana_actual["No_semana_actual"];

/*
	//CONSULTA LA FE
	$cur_fecha=mssql_query("select *,YEAR(fecha_inicio_semana) ano_inicio , YEAR(fecha_fin_semana) ano_fin from (
							select DATEADD(DAY,-7, GETDATE()) fecha_inicio_semana , GETDATE() fecha_fin_semana )aa");
	$datos_fecha=mssql_fetch_array($cur_fecha);
*/

	$pTema = '
				<table width="100%" border="0">
				  <tr>
					<td width="9%">&nbsp;</td>
					<td width="91%">&nbsp;</td>
				  </tr>
				  <tr>
					<td>Asunto:</td>
					<td>Personal sin registro de facturación</td>
				  </tr>
				  <tr>
					<td>División:</td>
					<td>div xxxx</td>
				  </tr>
				  <tr>
					<td>Fecha:</td>
					<td>fecha xxxxxx</td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>
				</table>
				<br>';

	$cur_division=mssql_query("select * from HojaDeTiempo.dbo.Divisiones where estadoDiv='A' and id_division=14  ");
	while($datos_divi=mssql_fetch_array($cur_division)) 
	{
		$cur_deptos=mssql_query("select * from HojaDeTiempo.dbo.Departamentos where estadoDpto='A' and id_division=14 and id_departamento=263 ");// .$datos_divi["id_division"]);
		while($datos_depto=mssql_fetch_array($cur_deptos))
		{

			$pTema = $pTema.'	<table width="100%" border="0">
				  <tr>
					<td>Departamento</td>
					<td colspan="2">'.$datos_depto["nombre"].'</td>
				  </tr>
				  <tr>
					<td colspan="7">&nbsp;</td>
				  </tr>
				  <tr>
					<td align="center">Unidad</td>
					<td align="center">Usuario</td>';

					$cont_semana=1;

					//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
					while($cont_semana<=$semana_actual-1)
					{
			//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA INICIAL DE LA SEMANA
						$cantidad_dias_inicial=7*($semana_actual-$cont_semana);
						//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA FINAL DE LA SEMANA
						$cantidad_dias_fina=( (int) $cantidad_dias_inicial)-7;
//echo $cantidad_dias_fina."**** <br><br>";

						//CONSULTA LAS FECHA A UTILIZAR EN LA CONSULTA DE FACTURACION, PARA LA SEMANA (FECHA INICIO), (FECHA FIN), (ANO INICIO), (ANO FIN)
						//(ANO INICIO), (ANO FIN), SE UTILIZAN, PARA TRAHER LA INFO DE LA FACTURACION, CUANDO LA SEMANA, ES LA PRIMERA DEL AÑO, Y ESTA CONTIENE DIAS DEL 
						//ANO ANTERIOR
						$cur_fechas_consul_factu=mssql_query(" select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
													select DATEADD(DAY,-".$cantidad_dias_inicial.", GETDATE()) fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", GETDATE())fecha_fin_semana )aa");
						$datos_fechas=mssql_fetch_array($cur_fechas_consul_factu);

echo mssql_get_last_message()."<br> <br> select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
													select DATEADD(DAY,-".$cantidad_dias_inicial.", GETDATE()) fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", GETDATE())fecha_fin_semana )aa";
						$ano_inicio=$datos_fechas["ano_inicio"];
						$ano_fin=$datos_fechas["ano_fin"];

						$dia_inicio_semana=$datos_fechas["dia_inicio"];
						$dia_fin_semana=$datos_fechas["dia_fin"];

						$mes_inicio_semana=$datos_fechas["mes_inicio"];
						$mes_fin_semana=$datos_fechas["mes_fin"];

						$fecha_inicio_semana=$datos_fechas["fecha_inicio_semana"];
						$fecha_fin_semana=$datos_fechas["fecha_fin_semana"];

							$pTema = $pTema.'<td align="center">Semana '.$cont_semana.' <br /> 
							Desde ('.$dia_inicio_semana.'/'.$mes_inicio_semana.'/'.$ano_inicio.') hasta ('.$dia_fin_semana.'/'.$mes_fin_semana.'/'.$ano_fin.')</td>' ;

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
						<td>'.$datos_usu_depto["unidad"].'</td>
						<td>'.$datos_usu_depto["nom_usu"].'</td>';

					//CONSTADOR DE LAS SEMANAS 
					$cont_semana=1;

					//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
					while($cont_semana<=$semana_actual)
					{
						//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA INICIAL DE LA SEMANA
						$cantidad_dias_inicial=7*($semana_actual-$cont_semana);
						//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA FINAL DE LA SEMANA
						$cantidad_dias_fina=$cantidad_dias_inicial-7;

						//CONSULTA LAS FECHA A UTILIZAR EN LA CONSULTA DE FACTURACION, PARA LA SEMANA (FECHA INICIO), (FECHA FIN), (ANO INICIO), (ANO FIN)
						//(ANO INICIO), (ANO FIN), SE UTILIZAN, PARA TRAHER LA INFO DE LA FACTURACION, CUANDO LA SEMANA, ES LA PRIMERA DEL AÑO, Y ESTA CONTIENE DIAS DEL 
						//ANO ANTERIOR
						$cur_fechas_consul_factu=mssql_query(" select *,YEAR(fecha_inicio_semana) ano_inicio , YEAR(fecha_fin_semana) ano_fin from (
													select DATEADD(DAY,-".$cantidad_dias_inicial.", GETDATE()) fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", GETDATE())fecha_fin_semana )aa");
						$datos_fechas=mssql_fetch_array($cur_fechas_consul_factu);

						$ano_inicio=$datos_fechas["ano_inicio"];
						$ano_fin=$datos_fechas["ano_fin"];
						$fecha_inicio_semana=$datos_fechas["fecha_inicio_semana"];
						$fecha_fin_semana=$datos_fechas["fecha_fin_semana"];

						//CONSULTA LA CANTIDAD DE REGISTROS CON FACTURACION, DENTRO DE LAS FECHA INDICADAS
						$cur_fact=mssql_query(" select count(*)  cant_reg from HojaDeTiempo.dbo.FacturacionProyectos where  vigencia between ".$ano_inicio."  and ".$ano_fin." and esInterno='I' 
and fechaFacturacion between '".$fecha_inicio_semana."' and '".$fecha_fin_semana."' and unidad=".$datos_usu_depto["unidad"]);
						$datos_reg_fact=mssql_fetch_array($cur_fact);

						$cant_reg_fact=$datos_reg_fact["cant_reg"];

						//SI NO HA REGISTROS DE FACTURACION
						if(( (int) $cant_reg_fact)==0)
						{
							$pTema = $pTema.'<td>ico</td>';
						}

						//SI HAY REGISTROS DE FACTURACION
						if(0<( (int) $cant_reg_fact))
						{
							$pTema = $pTema.'<td></td>';
						}
						$cont_semana++;

					}


				}
				$pTema = $pTema.' </tr>';
		}/// while depto
		$pTema = $pTema.'</table>';
	}//while división
	
		   $miMailUsuarioEM = 'carlosmaguirre' ;
	
		   //***EnviarMailPEAR	
		   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
	
		   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

	

?>
