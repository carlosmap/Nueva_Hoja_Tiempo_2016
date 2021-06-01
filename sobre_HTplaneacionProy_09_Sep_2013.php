<?
/*
session_start();

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
*/

/*
$vigencia="2013";
$unidades= array('18121','15712','18122'); 
*/

$meses= array (); //MESES DE LA VIGENCIA

function sobre_planeacion($minimoMes,$maximoMes,$vigencia,$unidades,$tipo_usuario)
		{
			//$tipo_usuario ALMACENA EL TIPO DE USUARIO( I O E), EN UN ARRAY, ESTE SE CARGA EN LA PAGINA DE PLANEACION DE PROYECTOS (upHTplaneacionProy.PHP)	
//echo $unidades[0]." - $minimoMes - $maximoMes - $vigencia ********************** <br><br>";
		?>

<?		
		$ban_reg=0; //PERMITE IDENTIFICAR, SI SE ENCONTRO ALMENOS, UN USUARIO SOBRE PLANEADO, DE NO SER ASI, NO SE ENVIA EL CORREO
		$pTema ='<table width="100%" border="0">
		  <tr class="Estilo2"  >
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		
		
		  </tr>
		  <tr class="Estilo2">
			<td width="5%" >Asunto:</td>
			<td >Sobre planeacion de participantes.</td>
		  </tr>
		  <tr class="Estilo2">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		 
		  </tr>
		  <tr class="Estilo2">
			<td colspan="2">Los siguientes participantes, tienen una dedicaci&oacute;n superior a 1 en los siguientes proyectos:</td>
		  </tr>
		  <tr class="Estilo2">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		
		
		  </tr>
		  <tr class="Estilo2">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		
		  </tr>
		
		  <tr class="Estilo2">
			<td colspan="2" >
				<table width="100%" border="1"  >

				  <tr class="Estilo2">
					<td colspan="5"></td>
					<td colspan="'.(($maximoMes-$minimoMes)+1).'" align="center" >'.$vigencia.'</td>
				  </tr>				
				
				  <tr class="Estilo2">
					<td>Unidad</td>
					<td>Nombre</td>
					<td>Departamento</td>
					<td>Divisi&oacute;n</td>
					<td>Proyecto</td>';

				
				
					$vMeses= array("","Ene","Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"); 
					for ($m=$minimoMes; $m<=$maximoMes; $m++) 
					{

					  $pTema = $pTema.'<td>'.$vMeses[$m].' </td>';
				
					}
				
				

				
				 $pTema = $pTema.' </tr>';
				$cur_tipo_usu=0; //CURSOR DEL ARRAY, DEL TIPO DE USUARIO 
				foreach($unidades as $unid)
				{
//tipo_usuario
					if($tipo_usuario[$cur_tipo_usu]=="I")	
					{
					//CONSULTA SI EL USUARIO ESTA SOBREPLANEADO EN ALMENOS, UN MES DURANTE LA VIGENCIA

					//COSNULTA PARA LOS USUARIOS INTERNOS
							$sql_planea_usu="select top(1) SUM (hombresMes) as total_hombre_mes ,PlaneacionProyectos.unidad ,mes 
											--,PlaneacionProyectos.id_proyecto
											, PlaneacionProyectos.unidad, Usuarios.nombre, 
											Usuarios.apellidos ,Divisiones.nombre as div,Departamentos.nombre as dep
											from PlaneacionProyectos 
											 inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad 
											 inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento 
											 inner join Divisiones on Divisiones.id_division=Departamentos.id_division 
											 inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto  
											where vigencia=".$vigencia." and mes in( "; 
						
											for ($m=$minimoMes; $m<=$maximoMes; $m++) 
												$sql_planea_usu=$sql_planea_usu." ".$m.",";
		//PlaneacionProyectos.id_proyecto,				
							$sql_planea_usu=$sql_planea_usu."0) and PlaneacionProyectos.unidad=".$unid." 
											and esInterno='I'
											group by  PlaneacionProyectos.unidad,mes,Usuarios.nombre, Usuarios.apellidos,Divisiones.nombre ,Departamentos.nombre 
											HAVING (SUM (hombresMes))>1 ";
					}


					if($tipo_usuario[$cur_tipo_usu]=="E")	
					{
						//COSNULTA PARA LOS USUARIOS  EXTERNOS
						$sql_planea_usu=" select top(1) SUM (hombresMes) as total_hombre_mes ,PlaneacionProyectos.unidad ,mes 
									--,PlaneacionProyectos.id_proyecto
									, PlaneacionProyectos.unidad, TrabajadoresExternos.nombre, 
									TrabajadoresExternos.apellidos,'' div, ''dep
									from PlaneacionProyectos 
									 inner join TrabajadoresExternos on PlaneacionProyectos.unidad=TrabajadoresExternos.consecutivo 
									 inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto  
									where vigencia=".$vigencia." and mes in( ";
											for ($m=$minimoMes; $m<=$maximoMes; $m++) 
												$sql_planea_usu=$sql_planea_usu." ".$m.",";

							$sql_planea_usu=$sql_planea_usu."0) and PlaneacionProyectos.unidad=".$unid." 
									and esInterno='E'
									group by  PlaneacionProyectos.unidad,mes,TrabajadoresExternos.nombre, TrabajadoresExternos.apellidos
									HAVING (SUM (hombresMes))>1 	";
					}

					$cur_planea_usu=mssql_query($sql_planea_usu);
//echo $sql_planea_usu." ---- <br>".mssql_get_last_message()." *** ".mssql_num_rows($cur_planea_usu)."<br>";
					while($datos__planea_usu=mssql_fetch_array($cur_planea_usu))
					{
						$ban_reg=1; //SI ENCUENTRA ALMENOS UN USUARIO SOBREPLANEADO, ENVIA EL CORREO
				
						//CONSULTA LOS PROYECTOS, EN LOS CUALES EL PARTICIAPENTE HA SIDO PLANEADO, ESTO PARA PODER DOBUJAR LA TABLA DE FORMA CORRECTA
						$sql_uu=" select distinct(id_proyecto),nombre,codigo,cargo_defecto from (select SUM (hombresMes) as total_hombre_mes ,unidad, mes,PlaneacionProyectos.id_proyecto ,Proyectos.nombre,Proyectos.codigo,Proyectos.cargo_defecto 
									from PlaneacionProyectos
										inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto 
									 where vigencia=".$vigencia." and mes in( ";
				
									for ($m=$minimoMes; $m<=$maximoMes; $m++) 
									{
										$sql_uu=$sql_uu." ".$m.",";
										$total_planeado[$m]=0;
									}
						
						$sql_uu=$sql_uu."0) and unidad in(".$unid.") and esInterno= '".$tipo_usuario[$cur_tipo_usu]."' group by unidad, mes , PlaneacionProyectos.id_proyecto,Proyectos.nombre,Proyectos.codigo,Proyectos.cargo_defecto  )   aa ";
// HAVING (SUM (hombresMes)	>1	
						$cur_uu=mssql_query($sql_uu);
						$cant_proy=mssql_num_rows($cur_uu);
//echo "<br><BR>---//**************--------------".$sql_uu." ".mssql_get_last_message()."<br>".$cant_proy."<br>";
				?>
				
				<?
				//echo $sql_proy_planea." ---- <br>".mssql_get_last_message()."<br><br>";
				
						$total_planeado= array();
						$cont=0;
						while($datos_uu=mssql_fetch_array($cur_uu))
						{
							if($cont==0)
							{
	
								 $pTema = $pTema.' <tr class="Estilo2">				
								<td rowspan="'.$cant_proy.' "> '.$datos__planea_usu["unidad"].' </td>
								<td rowspan="'.$cant_proy.' ">'.$datos__planea_usu["apellidos"].' '.$datos__planea_usu["nombre"].' </td>
								<td rowspan="'.$cant_proy.' ">'. $datos__planea_usu["dep"].' </td>
								<td rowspan="'.$cant_proy.' ">'. $datos__planea_usu["div"].' </td>';
							

							}

							$pTema = $pTema.'<td class="Estilo2" >['.$datos_uu["codigo"].'.'.$datos_uu["cargo_defecto"].'] '. $datos_uu["nombre"].' </td>';

							//CONSULTA LA INFORMACION DE LO PLANEADO EN CADA MES, DE ACUERDO AL PORYECTO CONSULTADO
							$sql_pro="select SUM (hombresMes) as total_hombre_mes ,PlaneacionProyectos.id_proyecto,PlaneacionProyectos.unidad,mes
										 from PlaneacionProyectos 
										 where vigencia=".$vigencia." and PlaneacionProyectos.unidad=".$unid." and id_proyecto=".$datos_uu["id_proyecto"]." and mes in(";
										for ($m=$minimoMes; $m<=$maximoMes; $m++) 
										{
				
											$sql_pro=$sql_pro." ".$m.",";
										}
							$sql_pro=$sql_pro." 0) and esInterno= '".$tipo_usuario[$cur_tipo_usu]."' group by PlaneacionProyectos.id_proyecto ,PlaneacionProyectos.unidad ,mes order by (mes) ";
// HAVING (SUM (hombresMes))>1
							$cur_proy_planea=mssql_query($sql_pro);
//				echo $sql_pro." --22222222222-- <br>".mssql_get_last_message()."<br><br>";
							$m=$minimoMes;
							
							while($datos_proy_planea=mssql_fetch_array($cur_proy_planea))
							{	
								for ($m;$m<=$maximoMes; $m++) 
								{
									if($datos_proy_planea["mes"]==$m)
									{
										$total_planeado[$m]+=( (float) $datos_proy_planea["total_hombre_mes"]);

										$pTema = $pTema.'<td class="Estilo2" align="right" >'.((float) $datos_proy_planea["total_hombre_mes"] ).'</td>';

										$m=$datos_proy_planea["mes"];
										$m++;
										break;
									}
									else
									{

										$pTema = $pTema.'<td>&nbsp; </td>';

									}
				
								}
							}
							$m--;
							for ($m++;$m<=$maximoMes; $m++) 
							{
				
								  $pTema = $pTema.'<td>&nbsp;</td>';
				
							}
				
							$cont++;
							unset($datos_proy_planea);

							  $pTema = $pTema.' </tr>';

				//			echo $datos_proy_planea["total_hombre_mes"]."<br>";
						}

					 $pTema = $pTema.' <tr class="Estilo2">
						<td colspan="4" >&nbsp;</td>

						<td>Total planeaci&oacute;n</td>';

						for ($m=$minimoMes; $m<=$maximoMes; $m++) 
						{
							if($total_planeado[$m]==0)
								$pTema = $pTema.'<td>&nbsp;</td>';
							else
								$pTema = $pTema.'<td>'.$total_planeado[$m].'</td>';
						}
 
					 $pTema = $pTema.' </tr>
					  <tr  >
						<td colspan="17">&nbsp;</td>
					  </tr>	';

					}
					$cur_tipo_usu++;
				}
				$pTema = $pTema.'

				</table>
			</td>
		  </tr>
		<tr  class="Estilo2"><td colspan="2" >Para consultar en detalle la planeaci&oacute;n de un participante, en un proyecto especifico, utilice el reporte Usuarios por proyecto, accediendo a el, atravez del boton Consolidados por divisi&oacute;n, ubicado en la parte superior de la pagina principal de la planeaci&oacute;n por proyectos. </td></tr>
		</table>';

		/*
				//consulta la unidad del director y el coordinador de proyecto
				$sql="SELECT id_director,id_coordinador FROM  HojaDeTiempo.dbo.proyectos where id_proyecto = " . $cualProyecto." " ;
				$eCursorMsql=mssql_query($sql);
				$usu_correo= array(); //almacena la unidad de los usuarios a los que se le enviara el correo
				$i=1;
				while($datos_dir_cor=mssql_fetch_array($eCursorMsql))
				{
					$usu_correo[$i]=$datos_dir_cor["id_coordinador"];
					$i++;
					$usu_correo[$i]=$datos_dir_cor["id_director"];
					$i++;
				}
		
				//consulta la unidad porgramadores y ordenadores de gasto			
		//select unidad from HojaDeTiempo.dbo.Programadores where id_proyecto=".$cualProyecto." union
				$sql_pro_orde=" select unidadOrdenador from GestiondeInformacionDigital.dbo.OrdenadorGasto where id_proyecto=".$cualProyecto;
				$cur_pro_orde=mssql_query($sql_pro_orde);
				while($datos_pro_orde=mssql_fetch_array($cur_pro_orde))
				{
					$usu_correo[$i]=$datos_pro_orde["unidad"];
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
				$sql_usu=$sql_usu.") and retirado is null";
				$cur_usu=mssql_query($sql_usu);				
		
				//se envia el correo a el director, cordinador, orenadores de gasto, y programadores del proyecto	
				while($eRegMsql = mssql_fetch_array($cur_usu))
				{		
				   $miMailUsuarioEM = $eRegMsql[email] ;
			
				   //***EnviarMailPEAR	
				   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
			
				   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			
				   //***FIN EnviarMailPEAR
				   $miMailUsuarioEM = "";
			
				}
		*/
			if($ban_reg==1) //SEW ENVIA EL CORREO SI EXISTE ALMENOS UN USUARIO SOBREPLANEADO
			{
		///////////////////////////**********************************************************PARA QUITAR
			   $miMailUsuarioEM = 'carlosmaguirre'; //$eRegMsql[email] ;	
				$pAsunto='Sobre planeación de proyectos';
			   //***EnviarMailPEAR	
			   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";	
			   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);	
			   //***FIN EnviarMailPEAR
			   $miMailUsuarioEM = "";
			}
		
		?>

<?
}
?>