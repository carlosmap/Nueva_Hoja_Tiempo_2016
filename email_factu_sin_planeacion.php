
<?php
session_start();
//include("../verificaRegistro2.php");
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
include("fncEnviaMailPEAR.php");
$pAsunto3 = "Facturaci\xf3n sin planeaci\xf3n";

$meses= array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');


//$cualProyecto=683;
$cualVigencia=2013;
$cualMes=7;
$usuario=15712;


	$sql_usu="select unidad, (HojaDeTiempo.dbo.Usuarios.nombre+' '+apellidos) nom_usu, UPPER( Departamentos.nombre) depto, upper(Divisiones.nombre) div from HojaDeTiempo.dbo.Usuarios 
		inner join HojaDeTiempo.dbo.Departamentos on HojaDeTiempo.dbo.Usuarios.id_departamento=HojaDeTiempo.dbo.Departamentos.id_departamento
		inner join HojaDeTiempo.dbo.Divisiones on HojaDeTiempo.dbo.Departamentos.id_division= HojaDeTiempo.dbo.Divisiones.id_division
		where HojaDeTiempo.dbo.Usuarios.unidad=".$usuario;

	$cur_usu=mssql_query($sql_usu);
	$datos_usu=mssql_fetch_array($cur_usu);


	//LISTA LOS PROYECTOS EN LOS QUE EL USUARIO HA FACTURADO
	$sql_proys="
	SELECT distinct(id_proyecto) id_proyecto FROM FacturacionProyectos
	 where unidad=".$usuario." and vigencia=".$cualVigencia." and mes=".$cualMes." and esInterno='I'";
	$cur_proys=mssql_query($sql_proys);

//echo  mssql_get_last_message($cur_proys)." --- <br>". $sql_proys."<br><br>";

	while($datos_proys=mssql_fetch_array($cur_proys))
	{

		$pTema3="";

		//CONSULTA LAS ACTIVIDADES DEL PROYECTO, EN LOS QUE HA FACTURADO, SIN ESTAR PLANEADO	
		$sql_activi_sin_planea="			
			SELECT day(FacturacionProyectos.fechaFacturacion) dia, FacturacionProyectos.id_proyecto,FacturacionProyectos.IDhorario , FacturacionProyectos.clase_tiempo,
			 FacturacionProyectos.id_actividad,FacturacionProyectos.mes,FacturacionProyectos.vigencia ,Actividades.macroactividad,
			 Actividades.nombre actividad, TipoLocalizacion.nomLocalizacion,clase_tiempo.descripcion c_t,FacturacionProyectos.horasMesF,FacturacionProyectos.cargo ,FacturacionProyectos.hombresMesF 
			 ,FacturacionProyectos.resumen
			 FROM FacturacionProyectos
			
				inner join Actividades on FacturacionProyectos.id_actividad=Actividades.id_actividad and FacturacionProyectos.id_proyecto=Actividades.id_proyecto
				inner join TipoLocalizacion on TipoLocalizacion.localizacion=FacturacionProyectos.localizacion
				inner join Clase_Tiempo on Clase_Tiempo.clase_tiempo=FacturacionProyectos.clase_tiempo
			
			 where FacturacionProyectos.unidad=".$usuario." and FacturacionProyectos.vigencia=".$cualVigencia." and FacturacionProyectos.mes=".$cualMes." and FacturacionProyectos.esInterno='I' 
			 and FacturacionProyectos.id_proyecto=".$datos_proys["id_proyecto"]." and FacturacionProyectos.id_actividad  not in( 
					SELECT id_actividad FROM PlaneacionProyectos
					 where unidad=".$usuario." and vigencia=".$cualVigencia." and mes=".$cualMes." and esInterno='I' and id_proyecto=".$datos_proys["id_proyecto"].")";

			$cur_activi_sin_planea=mssql_query($sql_activi_sin_planea);
//echo  "<br> ****".mssql_get_last_message()." --- <br>". $sql_activi_sin_planea."<br><br>";
			$cont=1;
			$activi=""; //ALMACENA EL ID, DE LA ACTIVIDAD, Y CUANDO ESTA CAMBIA, SE COMPARA CON EL ID DE LA ACTIVIDAD DEL ARRAY $datos_activi_sin_planea, ESTO PARA 
						//DIBUJAR LA LINEA QUE SEPARA LAS ACTIVIDADES
			while($datos_activi_sin_planea=mssql_fetch_array($cur_activi_sin_planea))
			{
				//ASOCIA EL ENCABESADO DE LAS ACTIVIDADES SIN PLANEACION, CUANDO SE ESTA RECORRIENDO EL CICLO POR PRIMERA VEZ
				if($cont==1)
				{
					$activi=$datos_activi_sin_planea["actividad"];

					$sql_proy="select  ('['+codigo+'.'+cargo_defecto+']') codigo_cargo, * from Proyectos where id_proyecto=".$datos_activi_sin_planea["id_proyecto"];
					
					$cur_proy=mssql_query($sql_proy);
					if($datos_proy=mssql_fetch_array($cur_proy))
					{	
						$nom_proy=$datos_proy["nombre"];
						$codigo=$datos_proy["codigo_cargo"];
					
					}
						$pTema3 = '
					<table width="100%" border="0" class="Estilo2">
								<tr>
								  <td width="15%">&nbsp;</td>
								  <td width="90%">&nbsp;</td>
								</tr>
								<tr>
								  <td class="TituloTabla">Asunto</td>
								  <td>Facturaci&oacute;n sin planeaci&oacute;n</td>
								</tr>
								<tr>
								  <td class="TituloTabla">Proyecto</td>
								  <td>'.$codigo.' '.$nom_proy.'</td>
								</tr>
					
								<tr>
								  <td class="TituloTabla">Fecha facturaci&oacute;n (mm/aa)</td>
								  <td>'.$meses[$cualMes].'/'.$cualVigencia.' </td>
								</tr>
					
							  </table>';
					
					
					
								$pTema3 =$pTema3. '<table width="100%" border="0"  class="Estilo2">
								  <tr  class="TituloTabla">
									<td>&nbsp;</td>
								  </tr>
					
								  <tr >
									<td  class="TituloTabla">El usuario ['.$datos_usu["unidad"].'] '.$datos_usu["nom_usu"].' del departamento '.$datos_usu["depto"].',  ha registrado facturaci&oacute;n en el proyecto  '.$nom_proy.', para el mes de '.$meses[$cualMes].' del '.$cualVigencia.'. En los siguientes dias del mes.</td>
								  </tr>
					
								  <tr  class="TituloTabla">
									<td>&nbsp;</td>
								  </tr>
								</table>';
								$pTema3 =$pTema3. '
									<table width="100%" border="1" class="Estilo2" >
									  <tr>
										<td colspan="9" align="center">'.$meses[$cualMes].'/'.$cualVigencia.'</td>
									  </tr>
									  <tr>
										<td>Dia</td>
										<td>Actividad</td>
										<td>Horario</td>
										<td>Loc.</td>
										<td>CT</td>
										<td>Cargo</td>
										<td>Hombres mes</td>
										<td>Horas mes</td>
										<td>Resumen</td>
									  </tr>';
					$cont=0;
				}

				if($datos_activi_sin_planea["actividad"]!=$activi)
				{
					$activi=$datos_activi_sin_planea["actividad"];
					$pTema3 =$pTema3. '  <tr>
						<td colspan="9" >&nbsp;</td>
					  </tr>';
				}

				$pTema3 =$pTema3. '
                          <tr class="Estilo2">

                            <td>'.$datos_activi_sin_planea["dia"].'</td>

                            <td>['.$datos_activi_sin_planea["macroactividad"].'] '.$datos_activi_sin_planea["actividad"].'</td>
							<td>';
				$datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$datos_activi_sin_planea["IDhorario"]));
								
				$pTema3=$pTema3."[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ".$datos_hor["NomHorario"];                
							$pTema3=$pTema3.' 
							</td>
                            <td>'.$datos_activi_sin_planea["nomLocalizacion"].'</td>
                            <td>'.$datos_activi_sin_planea["c_t"].'</td>
                            <td>'.$datos_activi_sin_planea["cargo"].'</td>
                            <td>'.$datos_activi_sin_planea["hombresMesF"].'</td>
                            <td>'.$datos_activi_sin_planea["horasMesF"].'</td>
                            <td>'.$datos_activi_sin_planea["resumen"].'</td>
                          </tr>';

			}
			$pTema3 =$pTema3. '
				  <tr>
					<td colspan="9" >&nbsp;</td>
				  </tr>
				</table>';

			//SI SE ENCONTRARON REGISTROS DE ACTIVIDADES FACTURADAS, SIN PLANEACION , SE ENVIA EL CORREO
			if(0<( (int) mssql_num_rows($cur_activi_sin_planea)))
			{
			/////////////////////PARA QUITAR 1
					//ENVIA EL CORREO
					$pTema3 = $pTema3.'<BR><BR>';
				   $miMailUsuarioEM3 = 'carlosmaguirre' ;
				   //***EnviarMailPEAR	
				   $pPara2= trim($miMailUsuarioEM3) . "@ingetec.com.co";
				   enviarCorreo($pPara2, $pAsunto3, $pTema3, $pFirma3);
			/////////////////////PARA QUITAR 2
			}
	}

?>
