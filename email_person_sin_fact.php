
<?php
 
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

/*
function $getdates
{
///'2013/09/01'; TENER PRESENTE ESTA FECHA
	return '2013/04/21';
}
*/
	include("fncEnviaMailPEAR.php");
	$pAsunto = "H.T. Facturaci\xf3n - Personal sin registro de facturaci\xf3n";

function genera_correo($getdates,$tipo,$pAsunto)
{



	$meses= array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
/*
	//CONSULTA LA FE
	$cur_fecha=mssql_query("select *,YEAR(fecha_inicio_semana) ano_inicio , YEAR(fecha_fin_semana) ano_fin from (
							select DATEADD(DAY,-7, '".$getdates."') fecha_inicio_semana , '".$getdates."' fecha_fin_semana )aa");
	$datos_fecha=mssql_fetch_array($cur_fecha);
*/

	$cur_division=mssql_query("select *, convert(nvarchar,'".$getdates."',103) as fecha ,MONTH('".$getdates."') mes, year('".$getdates."') ano from HojaDeTiempo.dbo.Divisiones where estadoDiv='A' and id_division in (14,11)  ");
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
					<td colspan="2" >Reporte de facturaci&oacute;n de los usuarios de la divisi&oacute;n <b> '.$datos_divi["nombre"].'</b>, que tienen facturaci&oacuten pendiente por registrar. En el mes de <b> '.$meses[$datos_divi["mes"]].' del '.$datos_divi["ano"].'</b>.</td>
				  </tr>

				</table>
				<br>';
// and id_departamento=263
				if (( (int) $datos_divi["mes"])<10)
				{
//								if ($datos_divi["mes"]!=("0".$datos_divi["mes"]) )
						$datos_divi["mes"]="0".$datos_divi["mes"];
				}

		$cur_deptos=mssql_query("select * from HojaDeTiempo.dbo.Departamentos where estadoDpto='A' and id_division=".$datos_divi["id_division"]." ");// .$datos_divi["id_division"]);
		while($datos_depto=mssql_fetch_array($cur_deptos))
		{

			//CONSULTA EL NUMERO DE SEMANA ACTUAL DEL MES 
			$cur_semana_ac=mssql_query("select (datepart(week,'".$getdates."')- datepart(week, dateadd(dd,-day('".$getdates."')+1,'".$getdates."'))+1 ) No_semana_actual");
			$dato_semana_actual=mssql_fetch_array($cur_semana_ac);
			$semana_actual =$dato_semana_actual["No_semana_actual"];

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

					<td colspan="6" align="center"> '.$meses[( (int) $datos_divi["mes"])].' - '.$datos_divi["ano"].'</td>
				  </tr>
				  <tr>
					<td align="center" width="10%">Unidad</td>
					<td align="center"  width="30%">Usuario</td>';

					$cont_semana=1;

					//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
					while($cont_semana<=$semana_actual-1)
					{
						$ban=0;
						//SI LA SEMANA ACTUAL ES LA PRIMERA DEL MES, CREAN LAS FECHAS DE INICIO Y FINZLIZACION DE LA PRIMERA SEMANA DEL MES
						//ESTO PARA NO TOMAR LOS DIAS FINALES DEL MES ANTERIOR, QUE PUEDEN HACER PARTE DE LA PRIMERA SEMANA
//echo $cont_semana." --- ".($semana_actual-1)."<br>";
						if($cont_semana==1)
						{
							//SELECCIONA EL DIA DE LA SEMANA, EN LA QUE CAHE EL PRIMER DIA DEL MES
							$datos_dia_mes=mssql_fetch_array(mssql_query("select DATEPART(DW,'".$datos_divi["ano"]."/".$datos_divi["mes"]."/1') dia_semana "));
							$dia_semana_pri_di_mes=$datos_dia_mes["dia_semana"];

							$dia_f=1;

							//REALIZA EL CONTADOR, PARA CALCULAR LA FECHA, EN LA QUE FINALIZA LA PRIMERA SEMANA DEL MES
							//se sabe que el PRIMER DIA DEL MES ES 1, PERO NO SE TIENE DETERMINADO EN QUE DIA FINALIZA LA SEMANA
							while($dia_semana_pri_di_mes<7)
							{
								$dia_f++;
								$dia_semana_pri_di_mes++;
							}
								$dia_f++;
							if($dia_f<10)
								$dia_f="0".$dia_f;

							$ano_inicio[$cont_semana]=$datos_divi["ano"];
							$ano_fin[$cont_semana]=$datos_divi["ano"];
	
							$dia_inicio_semana='01';
							$dia_fin_semana=$dia_f;
	
							$mes_inicio_semana=$datos_divi["mes"];
							$mes_fin_semana=$datos_divi["mes"];
	
							$fecha_inicio_semana[$cont_semana]=$datos_divi["ano"].'/'.$datos_divi["mes"].'/01';
							$fecha_fin_semana[$cont_semana]=$datos_divi["ano"].'/'.$datos_divi["mes"].'/'.$dia_f;

							
						}

						else
						{
	
							//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA INICIAL DE LA SEMANA
							$cantidad_dias_inicial=7*($semana_actual-$cont_semana);
							//PERMITE CALCULAR LA CANTIDAD DE DIAS A RESTAR, QUE PERMITE CALCULAR, LA FECHA FINAL DE LA SEMANA
							$cantidad_dias_fina=( (int) $cantidad_dias_inicial)-7;
	//echo $cantidad_dias_fina."**** <br><br>";
	
							//CONSULTA LAS FECHA A UTILIZAR EN LA CONSULTA DE FACTURACION, PARA LA SEMANA (FECHA INICIO), (FECHA FIN), (ANO INICIO), (ANO FIN)
							//(ANO INICIO), (ANO FIN), SE UTILIZAN, PARA TRAHER LA INFO DE LA FACTURACION, CUANDO LA SEMANA, ES LA PRIMERA DEL A??O, Y ESTA CONTIENE DIAS DEL 
							//ANO ANTERIOR
							$cur_fechas_consul_factu=mssql_query(" select *,YEAR(fecha_inicio_semana) ano_inicio, MONTH(fecha_inicio_semana) mes_inicio ,DAY(fecha_inicio_semana) dia_inicio , YEAR(fecha_fin_semana) ano_fin, MONTH(fecha_fin_semana) mes_fin , DAY(fecha_fin_semana) dia_fin   from (
														select DATEADD(DAY,-".$cantidad_dias_inicial.", '".$getdates."') fecha_inicio_semana , DATEADD(DAY,-".$cantidad_dias_fina.", '".$getdates."')fecha_fin_semana )aa");
							$datos_fechas=mssql_fetch_array($cur_fechas_consul_factu);
	
							$ano_inicio[$cont_semana]=$datos_fechas["ano_inicio"];
							$ano_fin[$cont_semana]=$datos_fechas["ano_fin"];
	
							if( ( (int) $datos_fechas["dia_inicio"])<10)
								$datos_fechas["dia_inicio"]="0".$datos_fechas["dia_inicio"];

							$dia_inicio_semana=$datos_fechas["dia_inicio"];

							if( ( (int) $datos_fechas["dia_fin"])<10)
								$datos_fechas["dia_fin"]="0".$datos_fechas["dia_fin"];

							$dia_fin_semana=$datos_fechas["dia_fin"];

							if( ( (int) $datos_fechas["mes_inicio"])<10)
								$datos_fechas["mes_inicio"]="0".$datos_fechas["mes_inicio"];
	
							$mes_inicio_semana=$datos_fechas["mes_inicio"];


							if( ( (int) $datos_fechas["mes_fin"])<10)
								$datos_fechas["mes_fin"]="0".$datos_fechas["mes_fin"];

							$mes_fin_semana=$datos_fechas["mes_fin"];
	
							$fecha_inicio_semana[$cont_semana]=$datos_fechas["fecha_inicio_semana"];
							$fecha_fin_semana[$cont_semana]=$datos_fechas["fecha_fin_semana"];

							$ban=0;  //PERMITE SABER SI SE CUMPLIO LA SIGUIENTE CONDICION, ESTO PARA PODER MOTRAR MAS ADELANTE, LA COLUMNA, CON LA FECHAS DE LA ULTIMA 
							//SEMANA DEL MES
							// SI $tipo==0, ES POR QUE SE VA A CONSULTAR, LA INFO DE LA ULTIMA SEMANA DEL MES ANTERIOR, Y AQUI SE ALMACENA EN EL ARRAY
							//LAS FECHA DE INICIO Y FINALIZACION DE LA ULTIMA SEMANA DEL MES
							if(($cont_semana==$semana_actual-1) and ($tipo==0) )
							{
//echo "Ingreso<br>";
								//CONSULTA QUE CALCULA LA CANTIDAD DE DIAS DEL MES, PARA ESTABLECER LA FECHA DE INCIO Y FINLIZACION, DE LA ULTIMA SEMANA DEL MES
								$cur_cant_dia_mes=mssql_fetch_array(mssql_query("select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$ano_fin[$cont_semana]."' + '".$datos_fechas["mes_inicio"]."' + '01')))) cant_dias "));
								$cant_dias=$cur_cant_dia_mes["cant_dias"];
								$fecha_inicio_semana[$cont_semana+1]=$datos_fechas["fecha_fin_semana"];
								$fecha_fin_semana[$cont_semana+1]=$ano_fin[$cont_semana]."/".$mes_fin_semana."/".$cant_dias;

								$ano_inicio[$cont_semana+1]=$datos_fechas["ano_inicio"];
								$ano_fin[$cont_semana+1]=$datos_fechas["ano_fin"];
/*
echo "Ingreso <br>";
$i=1;
foreach ($fecha_fin_semana as $nn)
{
	echo $fecha_inicio_semana[$i]." --- ". $nn."<br>";
$i++;
}
*/
								$ban=1;
								

							}


						}///del else
						$pTema = $pTema.'<td align="center">Semana '.$cont_semana.' <br /> 
								('.$dia_inicio_semana.'/'.$mes_inicio_semana.'/'.$ano_inicio[$cont_semana].') - ('.$dia_fin_semana.'/'.$mes_fin_semana.'/'.$ano_fin[$cont_semana].')</td>' ;

						// SI SE ALMACENO, LA FECHA DE INICIO  Y FINALIZACION DE LA ULTIMA SEMANA DEL MES ANTERIOR, SE A??ANDE AL CORREO, LA COLUMNA, CON LA FECHA CORRESPONDIENTE
						if($ban==1)
						{
							$pTema = $pTema.'<td align="center">Semana '.($cont_semana+1).' <br /> 
								('.$dia_fin_semana.'/'.$mes_inicio_semana.'/'.$ano_inicio[$cont_semana].') - ('.$cant_dias.'/'.$mes_fin_semana.'/'.$ano_fin[$cont_semana].')</td>' ;
						}
						$cont_semana++;
					}

					//SI SE CREO FORMO LA FECHA DE INICIO Y FINALIZACION, LA ULTIMA SEMANA DEL MES ANTERIOR, SE INCREMENTA $semana_actual,
					// PARA QUE SE CONSULTE LA FACTURACION DE  ESA SEMANA
					if($ban==1)
					{

						$semana_actual++;
					}
					$pTema = $pTema.'</tr>';

//echo "***********".($semana_actual-1)."<br>";

				//CONSULTA LOS USUARIOS ACTIVOS, QUE HACEN PARTE DEL DEPARTAMENTO
				$cur_usu_depto=mssql_query("select HojaDeTiempo.dbo.Usuarios.id_departamento, HojaDeTiempo.dbo.Divisiones.id_division, unidad, upper((HojaDeTiempo.dbo.Usuarios.apellidos+' '+HojaDeTiempo.dbo.Usuarios.nombre)) nom_usu from HojaDeTiempo.dbo.Usuarios 
				inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
				inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
				where  HojaDeTiempo.dbo.Usuarios.fechaRetiro is null 
				and HojaDeTiempo.dbo.Usuarios.id_departamento=".$datos_depto["id_departamento"]." and HojaDeTiempo.dbo.Divisiones.id_division=".$datos_divi["id_division"]." order by apellidos");
				while($datos_usu_depto=mssql_fetch_array($cur_usu_depto))
				{
					$pTema2='';

					$pTema2 = $pTema2.'  <tr>
						<td  width="10%" align="center" >'.$datos_usu_depto["unidad"].'</td>
						<td  width="30%">'.$datos_usu_depto["nom_usu"].'</td>';

					//CONSTADOR DE LAS SEMANAS 
					$cont_semana=1;
					$ban_facturacion=0; //PERMITE SABER, SI EXISTE ALMENOS UNA SEMANA SIN FACTURACION, POR CADA UNO DE LOS USUARIOS (0=HAY SEMANAS CON FACTURACION)
										//(1=SEMANAS SIN FACTURACION)
					//REALIZA EL CICLO, CONSULTANDO SEMANA A SEMANA, SI EL USUARIO, NO HA REGISTRADO FACTURACION
					while($cont_semana<=$semana_actual-1)
					{

						//CONSULTA LA CANTIDAD DE REGISTROS CON FACTURACION, DENTRO DE LAS FECHA INDICADAS
						$cur_fact=mssql_query(" select count(*)  cant_reg from HojaDeTiempo.dbo.FacturacionProyectos where  vigencia between ".$ano_inicio[$cont_semana]."  and ".$ano_fin[$cont_semana]." and esInterno='I' 
and fechaFacturacion between '".$fecha_inicio_semana[$cont_semana]."' and '".$fecha_fin_semana[$cont_semana]."' and unidad=".$datos_usu_depto["unidad"]);


//echo " select count(*)  cant_reg from HojaDeTiempo.dbo.FacturacionProyectos where  vigencia between ".$ano_inicio[$cont_semana]."  and ".$ano_fin[$cont_semana]." and esInterno='I' 
//and fechaFacturacion between '".$fecha_inicio_semana[$cont_semana]."' and '".$fecha_fin_semana[$cont_semana]."' and unidad=".$datos_usu_depto["unidad"]." --** ".mssql_get_last_message()."<br>";

						$datos_reg_fact=mssql_fetch_array($cur_fact);

						$cant_reg_fact=$datos_reg_fact["cant_reg"];



						//SI NO HA REGISTROS DE FACTURACION
						if(( (int) $cant_reg_fact)==0)
						{
//echo "---------------******".$cant_reg_fact." *////// ".$cont_semana."<br>";

							$ban_facturacion=1;
							$pTema2 = $pTema2.'<td align="center" bgcolor="#e9e9e9" ></td>';
						}

						//SI HAY REGISTROS DE FACTURACION
						if(0<( (int) $cant_reg_fact))
						{

							$pTema2 = $pTema2.'<td bgcolor="#999999"> &nbsp;</td>';
						}
						$cont_semana++;

					}

					//SI ALGUNA DE LAS SEMANAS NO TIENE FACTURACION, SE ASOCIA AL USUARIO EN EL CORREO
					if($ban_facturacion==1)
					{
						$pTema =$pTema." ".$pTema2;
					}

				}
				$pTema = $pTema.' </tr>';
				$pTema = $pTema.' <tr><td colspan=20 >&nbsp;</td></tr>';
		}/// while depto
		$pTema = $pTema.'</table><br>';

////FALTA A??ADIR LA CONSULTA, PARA TRAHER, EL EMAIL, DEL DIRECTOR DE DIVISION, PARA ENVIAR EL CORREO CORRESPONDIENTE
		   $miMailUsuarioEM = 'carlosmaguirre' ;	
		   //***EnviarMailPEAR	
		   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";	
		   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

	}//while divisi??n
	
}

///PARA QUITAR 1
// SE TIENE QUE ENVIAR COMO PARAMETRO, LA FECHA EN LA QUE SE EJECUTA EL SCRIPT.
	$getdates2='2013/09/01';
///PARA QUITAR 2

	//CONSULTA LAS PARTES DE LA FECHA ACTUAL
	$datos_fecha=mssql_fetch_array(mssql_query("SELECT MONTH('".$getdates2."') mes, year('".$getdates2."') ano, DAY('".$getdates2."') dia, '".$getdates2."' fecha_actual "));
	
///PARA QUITAR 1
/*
	$datos_fecha["ano"]="2013";
	$datos_fecha["mes"]="02";
	$datos_fecha["dia"]="06";
*/
///PARA QUITAR 2

//	$getdates2=$datos_fecha["fecha_actual"];


	//CONSULTA EL NUMERO DE SEMANA ACTUAL DEL MES 
	$cur_semana_ac=mssql_query("select (datepart(week,'".$getdates2."')- datepart(week, dateadd(dd,-day('".$getdates2."')+1,'".$getdates2."'))+1 ) No_semana_actual");
	$dato_semana_actual=mssql_fetch_array($cur_semana_ac);
	$semana_actual =$dato_semana_actual["No_semana_actual"];

//echo ' SELECT MONTH('.$getdates2.') mes, year('.$getdates2.') ano, DAY('.$getdates2.') dia, '.$getdates2.' fecha_actual ********///////////////////// '.$datos_fecha["dia"]."<br><br>";


	if(($semana_actual==1)||($semana_actual==2))
	{
		//SELECCIONA EL DIA DE LA SEMANA, EN LA QUE CAHE EL PRIMER DIA DEL MES, ESTO PARA IDENTIFICAR QUE SI EL PRIMER DIA DEL MES NO CAHE UN LUNES
		//YA QUE SI ES ASI, EL CONSOLIDADO DE LA ULTIMA SEMANA DEL MES ANTERIOR YA SE HABRIA GENERADO, POR QUE SE EJECUTO UN DOMINGO
		$datos_dia_mes=mssql_fetch_array(mssql_query("select DATEPART(DW,'".$getdates2."') dia_semana "));
		$dia_semana_pri_di_mes=$datos_dia_mes["dia_semana"];
		if($dia_semana_pri_di_mes!=2)
		{
			$mes=((int) $datos_fecha["mes"])-1;
			$ano=$datos_fecha["ano"];

			//SI EL MES ACTUAL ES ENERO, SE CONSULTA LO DE DICIEMBRE DEL A??O ANTERIOR
			if( ( (int) $datos_fecha["mes"] )==1 )
			{
				$mes=12;
				$ano=( ( (int) $datos_fecha["ano"] )-1);
			}


			//COSNULTA LA FECHA DEL ULTIMO DOMINGO DEL MES ANTERIOR, PARA COMPONER LA FECHA INICIAL DE LA ULTIMA SEMANA DEL MES ANTERIOR
			$cur_fechas_consul_factu=mssql_query(" select DATEADD(DAY,-7, '".$getdates2."') fecha_inicio_semana ");
							$datos_fechas=mssql_fetch_array($cur_fechas_consul_factu);


//IMPORTANTE
/*
FALTA CALCULAR LA CNATIDAD DE DIAS DEL MES ANTERIOR, PARA FORMAR CORRECTAMENTE, LA FECHA FINAL DE LA ULTIMA SEMANA DEL MES ANTERIOR
SE DEBE ENVIAR UN PARAMETRO A LA FUNCION, PARA CREAR UNA CONDICION , QUE CONSULTE LA INFORMACION DE ESTA ULTIMA SEMANA
*/

//			$getdates3="".$ano."/".$mes."/24";

			//GENERA EL CORREO DE LA FACTURACION DEL MES ANTERIOR, Y SE ENVIA EL PARAMETRO, 0, PARA QUE SE FORME LA FECHA DE LA ULTIMA SEMANA DEL MES ANTERIOR,
			// Y ASI FORMAR EN LA  FUNCION, TODAS LAS SEMANAS DEL MES 
			genera_correo($datos_fechas["fecha_inicio_semana"],0,$pAsunto);
		}
	}
//echo $semana_actual." **** ".$datos_fecha["dia"]."<br>"."select (datepart(week,'".$getdates2."')- datepart(week, dateadd(dd,-day('".$getdates2."')+1,'".$getdates2."'))+1 ) No_semana_actual";

	// SI LA SEMANA ACTUAL, NO ES LA PRIMERA DEL MES, Y EL DIA ACTUAL, NO EL PRIMERO DEL MES, SE GENERA EL CORREO CON LA INFORMACION DEL MES ACTUAL
	//POR EJ (01 DE SEPTIEMBRE 2013), NO FUNCIONARIA, POR QUE NO HABRIA QUE CONSULTAR PARA ESTE MES
	if(($semana_actual!=1)||( ((int) $datos_fecha["dia"] )!=1))
	{

		genera_correo($getdates2,1,$pAsunto);
//echo "<br><br>INGRESA <br>".$semana_actual." *** ".$datos_fecha["dia"] ;
	}

?>
