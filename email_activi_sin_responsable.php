<?php
session_start();
//include("../verificaRegistro2.php");
include('../conectaBD.php');

//Establecer la conexi&oacute;n a la base de datos
//$conexion = conectar();
//include "funciones.php";
//include "validacion.php";
//	include "validaUsrBd.php";
//include("fncEnviaMailPEAR.php");
$pAsunto3 = "H.T. Actividades sin responsable";

/*$cualProyecto=683;
$cualVigencia=2013;
$cualMes=7;
*/
$meses= array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');


		$sql_proy="select  ('['+codigo+'.'+cargo_defecto+']') codigo_cargo, * from Proyectos where id_proyecto=".$cualProyecto;

		$cur_proy=mssql_query($sql_proy);
		if($datos_proy=mssql_fetch_array($cur_proy))
		{	
			$nom_proy=$datos_proy["nombre"];
			$codigo=$datos_proy["codigo_cargo"];

		}

			$sql_aprobacion="select year(fechaIniProy) ano_ini,MONTH(fechaIniProy) mes_ini, DAY(fechaIniProy) dia_ini ,YEAR(fechaVoBo) ano_vob,MONTH(fechaVoBo )mes_vob, DAY(fechaVoBo) dia_vob
						, upper( nombre) nombre ,upper( apellidos) apellidos,unidad from AutorizaEDT 
						 inner join Usuarios on AutorizaEDT.unidadVoBo=Usuarios.unidad  
						 where id_proyecto=".$cualProyecto." and validaVoBo=1";

			$cur_aprobacion=mssql_query($sql_aprobacion);
			$datos_aprobacion=mssql_fetch_array($cur_aprobacion);

			if( ( (int) ($datos_aprobacion["mes_vob"]) )<=9)
				$datos_aprobacion["mes_vob"]="0".$datos_aprobacion["mes_vob"];

			if( ( (int) ($datos_aprobacion["dia_vob"]) )<=9)
				$datos_aprobacion["dia_vob"]="0".$datos_aprobacion["dia_vob"];

			if( ( (int) ($datos_aprobacion["mes_ini"]) )<=9)
				$datos_aprobacion["mes_ini"]="0".$datos_aprobacion["mes_ini"];

			if( ( (int) ($datos_aprobacion["dia_ini"]) )<=9)
				$datos_aprobacion["dia_ini"]="0".$datos_aprobacion["dia_ini"];

				
			$pTema3 = '
		<table width="100%" border="0" class="Estilo2">
				    <tr>
				      <td width="15%">&nbsp;</td>
				      <td width="90%">&nbsp;</td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Asunto</td>
				      <td>H.T. Planeaci&oacute;n - Actividades sin responsable</td>
			        </tr>
				    <tr>
				      <td class="TituloTabla">Proyecto</td>
				      <td>'.$codigo.' '.$nom_proy.'</td>
			        </tr>

				    <tr>
				      <td class="TituloTabla">Fecha de aprobaci&oacute;n <br>(dd/mm/aaaa)</td>
				      <td>'.$datos_aprobacion["dia_vob"].'/'.$datos_aprobacion["mes_vob"] .'/'.$datos_aprobacion["ano_vob"] .'</td>
	  			    </tr>

				    <tr>
				      <td class="TituloTabla">Usuario aprueba</td>
				      <td>['.$datos_aprobacion["unidad"].'] '.$datos_aprobacion["nombre"] .' '.$datos_aprobacion["apellidos"] .'</td>
	  			    </tr>

				    <tr>
				      <td class="TituloTabla">Fecha de inicio <br>(dd/mm/aaaa)</td>
				      <td>'.$datos_aprobacion["dia_ini"].'/'.$datos_aprobacion["mes_ini"] .'/'.$datos_aprobacion["ano_ini"] .'</td>
	  			    </tr>

			      </table>';


					$pTema3 =$pTema3. '<table width="100%" border="0"  class="Estilo2">
					  <tr  class="TituloTabla">
						<td>&nbsp;</td>
					  </tr>

					  <tr >
						<td  class="TituloTabla">Las siguientes actividades, no tienen un responsable asignado en la EDT del proyecto '.$nom_proy.'.</td>
					  </tr>

					  <tr  class="TituloTabla">
						<td>&nbsp;</td>
					  </tr>
					</table>
						
						<table width="100%" border="1"  class="Estilo2">';

					$sql_div="select nivel ,macroactividad, nombre,
						CAST(  REPLACE( REPLACE( REPLACE(REPLACE(macroactividad,'LC',''),'LT','' ),'.','0'),'A','')  as varchar) orde
						 from Actividades where id_proyecto=".$cualProyecto." and id_encargado is null and nivel =3 order by orde ";
					$cur_div=mssql_query($sql_div);

					if(0<( (int) mssql_num_rows($cur_div)))
					{
						$pTema3 =$pTema3. '
							  <tr>
								<td colspan="2" class="Estilo1" bgcolor="#999999"><p>Divisiones</p></td>
							  </tr>
							  <tr class="TituloTabla2">
								<td width="15%" align="center" >Macro Actividad</td>
								<td align="center" >Nombre Actividad</td>
							  </tr>';
					
					}

					while($datos_div=mssql_fetch_array($cur_div))
					{
						$pTema3 =$pTema3. '
							  <tr class="TituloTabla">
								<td width="15%">'.$datos_div["macroactividad"].'</td>
								<td>'.$datos_div["nombre"].'</td>
							  </tr>';

					}
	

					$sql_act="select nivel ,macroactividad, nombre,
						CAST(  REPLACE( REPLACE( REPLACE(REPLACE(macroactividad,'LC',''),'LT','' ),'.','0'),'A','')  as varchar) orde
						 from Actividades where id_proyecto=".$cualProyecto." and id_encargado is null and nivel =4 order by orde ";
					$cur_act=mssql_query($sql_act);

					 $pTema3 =$pTema3. ' <tr   class="TituloTabla">
						<td colspan="2">&nbsp;</td>
					  </tr>';


					if(0<( (int) mssql_num_rows($cur_act)))
					{
						$pTema3 =$pTema3.'<tr>
								<td colspan="2" class="Estilo1" bgcolor="#999999">Actividades</td>
							  </tr>
							  <tr class="TituloTabla2">
								<td width="15%" align="center" >Macro Actividad</td>
								<td align="center" >Nombre Actividad</td>
							  </tr>';
					}
					while($datos_act=mssql_fetch_array($cur_act))
					{


						$pTema3 =$pTema3. '
							  <tr class="TituloTabla">
								<td width="15%">'.$datos_act["macroactividad"].'</td>
								<td>'.$datos_act["nombre"].'</td>
							  </tr>';

					}
					$pTema3 =$pTema3. '</table>';

				//consulta la unidad del director y el coordinador de proyecto
				$sql="SELECT id_director,id_coordinador FROM  HojaDeTiempo.dbo.proyectos where id_proyecto = " . $cualProyecto." " ;
				$eCursorMsql=mssql_query($sql);
				if  (trim($eCursorMsql)=="")  
				{
					$error="si";
				}
				$usu_correo= array(); //almacena la unidad de los usuarios a los que se le enviara el correo
				$i=0;
				while($datos_dir_cor=mssql_fetch_array($eCursorMsql))
				{
					$usu_correo[$i]=$datos_dir_cor["id_coordinador"];
					$i++;
					$usu_correo[$i]=$datos_dir_cor["id_director"];
					$i++;
				}
				$i=0;
				//consulta el correo de los usuarios(director,cordinador,ordenadroes de G, y programadores) asociados al proyecto
				$sql_usu=" select email from HojaDeTiempo.dbo.Usuarios where unidad in(";
				foreach($usu_correo as $unid)
				{
					if($i==0)
					{
						$sql_usu=$sql_usu." ".$unid;		
						$i=1;
					}
					else
						$sql_usu=$sql_usu." ,".$unid;
				}
				$sql_usu=$sql_usu.",18121,20400 ) and retirado is null";
				$cur_usu=mssql_query($sql_usu);

//echo mssql_get_last_message()." --- <br>".$sql_usu;
//echo  mssql_num_rows($CUR_FACT_EXCE_PLANEA)." --- ". mssql_num_rows($CUR_FACT_INFE_PLANEA)." --- ". mssql_num_rows($CUR_FACT_SIN_PLANEA);
	//SE ENVIA EL CORREO AL USUARIO, SIEMPRE Y CUANDO, ALGUNA DE LAS CONSULTAS, HALLA ARROJADO POR LO MENOS UN REGISTRO
	if ((0<( (int) mssql_num_rows($cur_act)))||(0< ((int) mssql_num_rows($cur_div))))
	{

				//se envia el correo a el director, cordinador, orenadores de gasto, y programadores del proyecto	
				while($eRegMsql = mssql_fetch_array($cur_usu))
				{		
				   $miMailUsuarioEM = $eRegMsql[email] ;
			
				   //***EnviarMailPEAR	
				   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
			
				   enviarCorreo($pPara, $pAsunto3, $pTema3, $pFirma3);
			
				   //***FIN EnviarMailPEAR
				   $miMailUsuarioEM = "";
			
				}

/*
/////////////////////PARA QUITAR 1
		//ENVIA EL CORREO
		$pTema3 = $pTema3.'<BR><BR>';
	   $miMailUsuarioEM3 = 'carlosmaguirre' ;
	   //***EnviarMailPEAR	
	   $pPara2= trim($miMailUsuarioEM3) . "@ingetec.com.co";
	   enviarCorreo($pPara2, $pAsunto3, $pTema3, $pFirma3);
/////////////////////PARA QUITAR 2
*/
	}
?>
