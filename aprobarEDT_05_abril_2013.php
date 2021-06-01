<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function ismaxlength(obj){
var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
if (obj.getAttribute && obj.value.length>mlength)
obj.value=obj.value.substring(0,mlength)
}

//-->
</script>
<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

if(trim($recarga) == "2")
{
	$error="no";
//echo "procesando solicitud";

	include("fncEnviaMailPEAR.php");

				//consulta el nombre del usuario que genera la solicitud
				$sql_usuario="select nombre,apellidos from HojaDeTiempo.dbo.Usuarios where unidad=".$_SESSION["sesUnidadUsuario"];
				$cur_usuario=mssql_query($sql_usuario);
				if  (trim($cur_usuario)=="")  
				{
					$error="si";
				}
				if($datos_usuario=mssql_fetch_array($cur_usuario))
				{
					$nombre_usuario=$datos_usuario["nombre"];
					$apellido_usuario=$datos_usuario["apellidos"];
				}	

				$pTema = "<table width='100%'  border='0' cellspacing='1' cellpadding='0'>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Asunto:</td>";

				if($enviar==1)
				{
					$pTema = $pTema . "		<td  width='70%'>No aprobaci&oacute;n EDT </td>";
				}
				if($enviar==2)
				{
					$pTema = $pTema . "		<td  width='70%'>Aprobaci&oacute;n EDT </td>";
				}			
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Proyecto:</td>";
				$pTema = $pTema . "		<td width='70%'>".strtoupper($inf_proy)."</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Quien autoriza:</td>";
				$pTema = $pTema . "		<td width='70%'>".$nombre_usuario." ".$apellido_usuario."</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";

	//se se ha seleccionado no en la seccion liberar edt
	if($enviar==1)
	{
				$pAsunto = "No aprobaci\xf3n EDT";

//echo "procesando solicitud seccion 1";
				$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
/*
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Estado de la solicitud:</td>";
				$pTema = $pTema . "		<td width='70%'>No aprobada</td>";
				$pTema = $pTema . "	</tr>";
*/
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Fecha:</td>";
				$pTema = $pTema . "		<td width='70%'>".$fecha."</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";

				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td  colspan='2'><br>La EDT del proyecto  ".strtoupper($inf_proy)." no fue aprobada.</td>";
				$pTema = $pTema . "	</tr>";

				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td colspan='2'><br>Observaciones</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td colspan='2'>".$observacion."</td>";
				$pTema = $pTema . "	</tr>";

	
				$pTema = $pTema . "	<tr>";
				$pTema = $pTema . "		<td width='25%' class='Estilo2' colspan='2'>";					
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "</table>";
			
				$pFirma = ""; // Geotecnia - Investigaciones ";
	//echo $inf_proy."<br>";
	//echo $usu_firma;
//	echo $error."<br><br>";	




	
				//consulta la secuencia de la ultima solicitud generada, para la edt del proyecto
				$sql_secuen="select MAX(secuencia)as secuencia from HojaDeTiempo.dbo.AutorizaEDT where id_proyecto = ".$cualProyecto;
				$cur_secuen=mssql_query($sql_secuen);
				if  (trim($cur_secuen)=="")  
				{
					$error="si";
				}
//	echo $error." - $sql_secuen - ".mssql_get_last_message()."  **---  $fechaInicio <br>";			
				if($datos_secuen=mssql_fetch_array($cur_secuen))
					$secuencia=$datos_secuen["secuencia"];

		
				//consulta la unidad del usuario que realizo la solicitud
				$sql_usu_sol="select usuElabora from AutorizaEDT where id_proyecto=".$cualProyecto." and secuencia=".$secuencia;
				$cur_usu_sol=mssql_query($sql_usu_sol);
				if($datos_usu_sol=mssql_fetch_array($cur_usu_sol))
						$usu_firma=$datos_usu_sol["usuElabora"];	


///**** temporal
//	$usu_firma=18121;
/////// temporal			

				//consulta el correo de la persona que realiza la solicitud
				$sql_usu_aprueba="select * from HojaDeTiempo.dbo.Usuarios where unidad=".$usu_firma." and retirado is null";
				$eCursorMsql=mssql_query($sql_usu_aprueba);
				if  (trim($eCursorMsql)=="")  
				{
					$error="si";
				}	
//	echo $error." - $sql_usu_aprueba - ".mssql_get_last_message()."<br>";

				//actualiza  la informacion de respuesta, que le da el director del proyecto, se deja en evia firma (enviaAFirmaenviaAFirma (enviaAFirma) en 0, para indicar que la solicitud queda nuevamente disponible, para enviar a revision la EDT
				$sql_ins_solicitud="update  HojaDeTiempo.dbo.AutorizaEDT set validaVoBo=0, unidadVoBo=".$_SESSION["sesUnidadUsuario"].",comentaVoBo='".$observacion."',fechaVoBo=getdate(),usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate(),enviaAFirma=0,validaElabora=0";
				$sql_ins_solicitud=$sql_ins_solicitud." where id_proyecto = ".$cualProyecto." and secuencia=".$secuencia;
				$cur_ins_solicitud=mssql_query($sql_ins_solicitud);

//	echo $error." - $sql_ins_solicitud - ".mssql_get_last_message()."<br>";	
				if  (trim($cur_ins_solicitud)=="")  
				{
					$error="si";
				}

	//echo $error." - $sql_usu_aprueba - ".mssql_get_last_message();			
	//		echo "<br>".$pTema."<br>".$pFirma;
				if  (trim($error)=="no")  
				{
			//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
						
					while($eRegMsql = mssql_fetch_array($eCursorMsql))
					{		
					   $miMailUsuarioEM = $eRegMsql[email] ;
				
					   //***EnviarMailPEAR	
					   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
				
					   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
				
					   //***FIN EnviarMailPEAR
					   $miMailUsuarioEM = "";
				
					}
//echo "comit ";
					$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
					echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
				} 
				else 
				{
//echo "rollback ";
					$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
					echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
				}

		
	}
	//se se ha seleccionado si en la seccion liberar edt
	if($enviar==2)
	{
				$pAsunto = "Aprobaci\xf3n EDT";
/*			echo "<script>alert('Procesando la solicitud, por favor espere');</script>";
*/
//echo "procesando solicitud seccion 2";	

	//echo $inf_proy."<br>";
				$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");

				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Fecha de aprobaci&oacute;n (mes/dia/a&ntilde;o):</td>";
				$pTema = $pTema . "		<td width='70%'>".$fecha."</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";

				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='30%'>Fecha de Inicio del proyecto (mes/dia/a&ntilde;o):</td>";
				$pTema = $pTema . "		<td width='70%'>".$fechaInicio."</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";
				 //componemos el mensaje de las personas asociadas a las actividades de la EDT, con la cabesera, de el mensaje que le llegara a el director, cordinador, porgramador, ordenadro de gasto
				$pTema2=$pTema;
				$pTema3=$pTema;;

				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td width='100%' colspan='2'><br>La EDT del proyecto  ".strtoupper($inf_proy)." fue aprobada. Por favor defina el tiempo y asigne los participantes requeridos para su correspondiente ejecuci&oacute;n. </td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "		<td>&nbsp;</td>";
				$pTema = $pTema . "	</tr>";

			if(trim($observacion)!="")
			{
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td colspan='2'><br>Observaciones</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td colspan='2'>".$observacion."</td>";
				$pTema = $pTema . "	</tr>";
			}
				$pTema = $pTema . "	<tr>";
				$pTema = $pTema . "		<td width='25%' class='Estilo2' colspan='2'></td>";					
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "</table>";
			
				$pFirma = ""; // Geotecnia - Investigaciones ";

	


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
		
				//consulta la unidad porgramadores y ordenadores de gasto			
				$sql_pro_orde=" select unidad from HojaDeTiempo.dbo.Programadores where id_proyecto=".$cualProyecto." union select unidadOrdenador from GestiondeInformacionDigital.dbo.OrdenadorGasto where id_proyecto=".$cualProyecto;
				$cur_pro_orde=mssql_query($sql_pro_orde);
				if  (trim($cur_pro_orde)=="")  
				{
					$error="si";
				}
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
				if  (trim($cur_usu)=="")  
				{
					$error="si";
				}
//echo "<br>CORREO DIRECTO,COORDINADOR,PROGRAMADOR<br>".$sql_usu."  - $error -  ".mssql_get_last_message();

//////////////////////////////////////////////////////////////////temporal 1
$usu_firma=18121;

			//consulta el correo de la persona a quien se le realiza la solicitud
			$sql_usu_aprueba="select * from HojaDeTiempo.dbo.Usuarios where unidad in(".$usu_firma.",20400) and retirado is null";
			$cur_usu=mssql_query($sql_usu_aprueba);
			if  (trim($cur_usu)=="")  
			{
				$error="si";
			}
//////////////////////////////////////////////////////////////////temporal

			

			//consulta los responsables asignados en las diferentes actividades de la EDT del proyecto
			$sql_usuarios_edt="select distinct(id_encargado) from HojaDeTiempo.dbo.Actividades where id_proyecto=".$cualProyecto."  and id_encargado is not null";
			$cur_usuarios_edt=mssql_query($sql_usuarios_edt);
			if  (trim($cur_usuarios_edt)=="")  
			{
				$error="si";
			}
//echo "<br><br>".$sql_usuarios_edt."  - $error -  ".mssql_get_last_message();
			while($datos_usuarios=mssql_fetch_array($cur_usuarios_edt))
			{
				//consulta los tipos de actividades que se le asignaron al usuario en la EDT
				$sql_tipo_activi_asig="select COUNT(*)as cant_actividades,tipoActividad   from HojaDeTiempo.dbo.Actividades where id_proyecto=".$cualProyecto." and id_encargado=".$datos_usuarios["id_encargado"]."  group by(tipoActividad) order by tipoActividad";
				$cur_tipo_activi_asig=mssql_query($sql_tipo_activi_asig);
				if  (trim($cur_tipo_activi_asig)=="")  
				{
					$error="si";
				}
//echo "<br><br>".$sql_tipo_activi_asig."  - $error -  ".mssql_get_last_message()." ** ".$datos_usuarios["id_encargado"];
				$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
				$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
				$pTema2 = $pTema2 . "		<td>&nbsp;</td>";

				$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
				$pTema2 = $pTema2 . "		<td width='30%' colspan='5'>Actividades asignadas en el proyecto  ".$inf_proy."  * ".$datos_usuarios["id_encargado"]." </td>";
				$pTema2 = $pTema2 . "	</tr>";
				while($datos_tipo_activi_asig=mssql_fetch_array($cur_tipo_activi_asig))
				{

					//si la actividad asignada es un lote de control
					if($datos_tipo_activi_asig["tipoActividad"]==1)
					{
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
						$pTema2 = $pTema2 . "	</tr>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td colspan='2'>";

						$pTema2 = $pTema2 . "		<table  width='100%'>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td  colspan='2' align='center'>Lotes de control</td>";
						$pTema2 = $pTema2 . "	</tr>";
		
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td width='50%'>Lote de control</td>";
						$pTema2 = $pTema2 . "		<td >Nombre</td>";
						$pTema2 = $pTema2 . "	</tr>";

						//consulta los lotes de control asignados
						$sql_acti_lc="select macroactividad,nombre   from HojaDeTiempo.dbo.Actividades where id_proyecto=".$cualProyecto." and id_encargado =".$datos_usuarios["id_encargado"]." and nivel=1";
						$sql_acti_lc=$sql_acti_lc."order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
						$cur_acti_lc=mssql_query($sql_acti_lc);
						if  (trim($cur_acti_lc)=="")  
						{
							$error="si";
						}
						while($dato_acti_lc=mssql_fetch_array($cur_acti_lc))
						{
							$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
							$pTema2 = $pTema2 . "		<td width='40%'>".$dato_acti_lc["macroactividad"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti_lc["nombre"]."</td>";
							$pTema2 = $pTema2 . "	</tr>";							
						}	
						$pTema2 = $pTema2 . "		</table>";
						$pTema2 = $pTema2 . "		</td>";
						$pTema2 = $pTema2 . "	</tr>";
//echo "<br>     <t>".$sql_acti_lc."  - $error -  ".mssql_get_last_message()." ** ".$datos_usuarios["id_encargado"];
					}
					//si la actividad asignada es un lote de trabajo
					else if($datos_tipo_activi_asig["tipoActividad"]==2)
					{
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
						$pTema2 = $pTema2 . "	</tr>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td colspan='2'>";
						$pTema2 = $pTema2 . "		<table  width='100%'>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td colspan='3' align='center'>Lotes de trabajo</td>";
						$pTema2 = $pTema2 . "	</tr>";
		
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td >Lote de control</td>";
						$pTema2 = $pTema2 . "		<td >Lote de trabajo</td>";
						$pTema2 = $pTema2 . "		<td>Nombre</td>";
						$pTema2 = $pTema2 . "	</tr>";

						//consulta los lotes detrabajo asignados
						$sql_acti_lt=$sql_acti_lt="select A1.macroactividad,A1.nombre,A1.dependeDe,( select macroactividad from HojaDeTiempo.dbo.Actividades A2 where A2.id_proyecto=".$cualProyecto." and id_actividad=A1.dependeDe
						) as lote_control  from HojaDeTiempo.dbo.Actividades  as A1 where id_proyecto=".$cualProyecto." and id_encargado=".$datos_usuarios["id_encargado"]." and nivel=2
						ORDER BY cast((substring(A1.macroactividad,3,charindex('.', (A1.macroactividad))-3)) as int)";
						$cur_acti_lt=mssql_query($sql_acti_lt);
						if  (trim($cur_acti_lt)=="")  
						{
							$error="si";
						}
						while($dato_acti_lt=mssql_fetch_array($cur_acti_lt))
						{
							$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
							$pTema2 = $pTema2 . "		<td>".$dato_acti_lt["lote_control"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti_lt["macroactividad"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti_lt["nombre"]."</td>";
							$pTema2 = $pTema2 . "	</tr>";							
						}	
						$pTema2 = $pTema2 . "		</table>";
						$pTema2 = $pTema2 . "		</td>";
						$pTema2 = $pTema2 . "	</tr>";
//echo "<br>     <t>".$sql_acti_lt."  - $error -  ".mssql_get_last_message()." ** ".$datos_usuarios["id_encargado"];
					}					
					//si la actividad asignada es una division
					else if($datos_tipo_activi_asig["tipoActividad"]==3)
					{

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td colspan='2'>";
						$pTema2 = $pTema2 . "		<table  width='100%'>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td  colspan='5' align='center'>Divisones</td>";
						$pTema2 = $pTema2 . "	</tr>";
		
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td >Lote de control</td>";
						$pTema2 = $pTema2 . "		<td >Lote de trabajo</td>";
						$pTema2 = $pTema2 . "		<td >Divis&oacute;n</td>";
						$pTema2 = $pTema2 . "		<td >Nombre</td>";
						$pTema2 = $pTema2 . "		<td >Valor asignado</td>";
						$pTema2 = $pTema2 . "	</tr>";

						//consulta las divisiones asignadas
						$sql_acti_div="select A1.macroactividad,A1.nombre,A1.dependeDe,(select macroactividad from Actividades A2 where A2.id_proyecto=".$cualProyecto." and id_actividad=A1.dependeDe
						) as lote_trabajo, (select macroactividad from Actividades A2 where A2.id_proyecto=".$cualProyecto." and id_actividad=A1.actPrincipal) as lote_control ,ActividadesRecursos.valorActiv    from Actividades as A1
		left join ActividadesRecursos on ActividadesRecursos.id_actividad=A1.id_actividad and  ActividadesRecursos.id_proyecto=".$cualProyecto."
 
						where  A1.id_proyecto=".$cualProyecto." and id_encargado=".$datos_usuarios["id_encargado"]."  and nivel=3
						ORDER BY cast((substring(A1.macroactividad,3,charindex('.', (A1.macroactividad))-3)) as int)";

						$cur_acti_div=mssql_query($sql_acti_div);
//echo $sql_acti_div." <br>".mssql_get_last_message()."<br><br>";
						if  (trim($cur_acti_div)=="")  
						{
							$error="si";
						}
						while($dato_acti_div=mssql_fetch_array($cur_acti_div))
						{
							$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
							$pTema2 = $pTema2 . "		<td>".$dato_acti_div["lote_control"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti_div["lote_trabajo"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti_div["macroactividad"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti_div["nombre"]."</td>";

							$pTema2 = $pTema2 . "		<td >" ;

							 if(trim($dato_acti_div["valorActiv"])!="") 
									$pTema2 = $pTema2 . "$".$dato_acti_div["valorActiv"];  
							 else
									$pTema2 = $pTema2 . "$0";

							$pTema2 = $pTema2 . "</td>";

							$pTema2 = $pTema2 . "	</tr>";							
						}	
						$pTema2 = $pTema2 . "		</table>";
						$pTema2 = $pTema2 . "		</td>";
						$pTema2 = $pTema2 . "	</tr>";
//echo "<br>     <t>".$sql_acti_div."  - $error -  ".mssql_get_last_message()." ** ".$datos_usuarios["id_encargado"];
					}					
					//si la actividad asignada es una actividad					
// colspan='3'
					else if($datos_tipo_activi_asig["tipoActividad"]==4)
					{
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";
						$pTema2 = $pTema2 . "		<td>&nbsp;</td>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td colspan='2'>";
						$pTema2 = $pTema2 . "		<table  width='100%'>";

						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td  colspan='6' align='center'>Actividades</td>";
						$pTema2 = $pTema2 . "	</tr>";
		
						$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
						$pTema2 = $pTema2 . "		<td >Lote de control</td>";
						$pTema2 = $pTema2 . "		<td>Lote de trabajo</td>";
						$pTema2 = $pTema2 . "		<td >Divis&oacute;n</td>";
						$pTema2 = $pTema2 . "		<td>Actividad</td>";
						$pTema2 = $pTema2 . "		<td >Nombre</td>";
						$pTema2 = $pTema2 . "		<td >Valor asignado</td>";
						$pTema2 = $pTema2 . "	</tr>";

						//consulta la informacion de las actividades asignadas en la EDT
						$sql_acti="select A1.macroactividad,A1.nombre,A1.dependeDe,(select macroactividad from Actividades A2 where A2.id_proyecto=".$cualProyecto." and id_actividad=A1.dependeDe
						) as division,(	select macroactividad from Actividades A2 where A2.id_proyecto=".$cualProyecto." and id_actividad=A1.actPrincipal) as lote_control						
						 ,ActividadesRecursos.valorActiv from Actividades  as A1 

						left join ActividadesRecursos on ActividadesRecursos.id_actividad=A1.id_actividad and  ActividadesRecursos.id_proyecto=".$cualProyecto."

						where A1.id_proyecto=".$cualProyecto." and id_encargado=".$datos_usuarios["id_encargado"]." and nivel=4
						ORDER BY cast((substring(A1.macroactividad,3,charindex('.', (A1.macroactividad))-3)) as int)";
						$cur_acti=mssql_query($sql_acti);
//echo $sql_acti." <br>".mssql_get_last_message()."<br><br>";
						if  (trim($cur_acti)=="")  
						{
							$error="si";
						}
						while($dato_acti=mssql_fetch_array($cur_acti))
						{
							$pTema2 = $pTema2 . "	<tr class='Estilo2'>";
							$pTema2 = $pTema2 . "		<td>".$dato_acti["lote_control"]."</td>";

							//consulta los lotes de trabajo asociadas a las activdades del usuario
							$sql_lt_acti="select A1.macroactividad,A1.dependeDe,(select macroactividad from Actividades 
							 where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=A1.dependeDe) as lote_trabajo
							  from Actividades  as A1 where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$dato_acti["dependeDe"];
							$cur_lt_acti=mssql_query($sql_lt_acti);
							if  (trim($cur_lt_acti)=="")  
							{
								$error="si";
							}
							if($datos_lt_acti=mssql_fetch_array($cur_lt_acti))
								$pTema2 = $pTema2 . "		<td >".$datos_lt_acti["lote_trabajo"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti["division"]."</td>";
							$pTema2 = $pTema2 . "		<td>".$dato_acti["macroactividad"]."</td>";
							$pTema2 = $pTema2 . "		<td >".$dato_acti["nombre"]."</td>";
							$pTema2 = $pTema2 . "		<td >";

							 if(trim($dato_acti["valorActiv"])!="") 
									$pTema2 = $pTema2 . "$".$dato_acti["valorActiv"];  
							 else
									$pTema2 = $pTema2 . "$0";

							$pTema2 = $pTema2 . "		</td >";
							$pTema2 = $pTema2 . "	</tr>";							
						}	
						$pTema2 = $pTema2 . "		</table>";
						$pTema2 = $pTema2 . "		</td>";
						$pTema2 = $pTema2 . "	</tr>";
//echo "<br>     <t>".$sql_lt_acti."  - $error -  ".mssql_get_last_message()." ** ".$datos_usuarios["id_encargado"];
					}		
					else
					{
//echo "<br> *********************///////////////////////////////////ENTRO";
					}			
				}
				//envia el correo a cada uno de los usuarios asignados con las actividades asociadas
				$sql_usu_acti=" select email from HojaDeTiempo.dbo.Usuarios where unidad=".$datos_usuarios["id_encargado"];
///temporal
				$sql_usu_acti=" select email from HojaDeTiempo.dbo.Usuarios where unidad in (18121)";
//temporal
				$cur_usu_acti=mssql_query($sql_usu_acti);
				if  (trim($cur_usu_acti)=="")  
				{
					$error="si";
				}

				if  (trim($error)=="no")  
				{
//echo "<br><br> *******************************Genera correo a ".$datos_usuarios["id_encargado"]." ***************** <br>correo<br> $pTema2";


					if($datos_usu_acti=mssql_fetch_array($cur_usu_acti))
					{
					   $miMailUsuarioEM = $datos_usu_acti[email] ;
				
					   //***EnviarMailPEAR	
					   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
				
					   enviarCorreo($pPara, $pAsunto, $pTema2, $pFirma);
				
					   //***FIN EnviarMailPEAR
					   $miMailUsuarioEM = "";
					}



				}
				$pTema2=$pTema3;
			}

				//consulta la secuencia de la ultima solicitud generada, para la edt del proyecto
				$sql_secuen="select MAX(secuencia)as secuencia from HojaDeTiempo.dbo.AutorizaEDT where id_proyecto = ".$cualProyecto;
				$cur_secuen=mssql_query($sql_secuen);
				if  (trim($cur_secuen)=="")  
				{
					$error="si";
				}
//	echo $error." - $sql_secuen - ".mssql_get_last_message()."<br>";			
				if($datos_secuen=mssql_fetch_array($cur_secuen))
					$secuencia=$datos_secuen["secuencia"];

				//inserta  la informacion de respuesta, que le da el director del proyecto
				$sql_up_solicitud="update  HojaDeTiempo.dbo.AutorizaEDT set validaVoBo=1, unidadVoBo=".$_SESSION["sesUnidadUsuario"].",comentaVoBo='".$observacion."',fechaVoBo=getdate(),usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate(),fechaIniProy='".$fechaInicio."' ,validaElabora=1 ";
				$sql_up_solicitud=$sql_up_solicitud." where id_proyecto = ".$cualProyecto." and secuencia=".$secuencia;
				$cur_up_solicitud=mssql_query($sql_up_solicitud);
				if  (trim($cur_up_solicitud)=="")  
				{
					$error="si";
				}


				if  (trim($error)=="no")  
				{


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

//echo "comit ";
					$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
					echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
				} 
				else 
				{
//echo "rollback  ";
					$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
					echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
				}

	}

	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>

<?php 
				//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
				$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
				$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
				$sql=$sql." WHERE P.id_director *= D.unidad " ;
				$sql=$sql." AND P.id_coordinador *= C.unidad " ;
				$sql=$sql." AND P.id_proyecto = " . $cualProyecto." and D.retirado is null" ;
				$cursor = mssql_query($sql);
//echo $sql;
				//CONSULTA LA FECHA DE INICIO DEL PROYECTO
				$datos_inf_proy=mssql_fetch_array($cur=mssql_query("select fechaInicio from Proyectos where id_proyecto=" . $cualProyecto ));
?>

<html>
<head>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>


</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" type="post"   name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Enviar a Director</td>

  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="TituloTabla" width="20%" >Proyecto</td>
          <td width="1165" class="TxtTabla">

<?php

				 while ($reg=mssql_fetch_array($cursor)) 
				{
					 echo  "[".$reg[codigo].".".$reg[cargo_defecto]."]  -  ".  ucwords(strtolower($reg[nombre])) ;
					 $inf_proy="[".$reg[codigo].".".$reg[cargo_defecto]."]  -  ".  ucwords(strtolower($reg[nombre])) ;
//echo $inf_proy;
				}

 ?>

		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Fecha (mes/dia/a&ntilde;o)</td>
<?php
//		$mes= array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$sql_fech="select day(GETDATE()) as dia ,MONTH(GETDATE()) as mes,YEAR(GETDATE())as ano";
		$cur_fech=mssql_query($sql_fech);
		$datos_fech=mssql_fetch_array($cur_fech);

		if(!isset($enviar))
		{
				$enviar=1;
		}

		$sql_fecha_ini="select * from HojaDeTiempo.dbo.AutorizaEDT  where id_proyecto=" . $cualProyecto." order by (secuencia) desc";
		$cur_fecha_ini=mssql_query($sql_fecha_ini);
		if($datos_fecha_ini=mssql_fetch_array($cur_fecha_ini))
		{
//			$fecha_ini=trim($datos_fecha_ini["fechaIniProy"]);
			$comenta_vobo=$datos_fecha_ini["comentaVoBo"];
		}
?>
          <td class="TxtTabla"><input name="fecha" type="text" class="CajaTexto" id="fecha" value="<? echo 
//$datos_fech["mes"]."/".$datos_fech[dia]."/".$datos_fech["ano"]; 
date("m/d/Y ", strtotime($datos_fech["mes"]."/".$datos_fech[dia]."/".$datos_fech["ano"])); ?>" size="17" readonly ></td>
        </tr>
        <tr>
          <td  class="TituloTabla">Fecha de Inicio (mes/dia/a&ntilde;o)</td>

          <td class="TxtTabla"><input name="fechaInicio" type="text" class="CajaTexto" id="fechaInicio" value="<?php if($datos_inf_proy["fechaInicio"]!=""){  echo date("m/d/Y ", strtotime($datos_inf_proy["fechaInicio"])); } ?>" readonly /> <!-- readonly-->
<!--
<a href="javascript:cal.popup();"><img src="imagenes/cal.gif" width="16" height="16" border="0" /></a>
-->
            </td>
        </tr>
        <tr>

          <td class="TituloTabla">&iquest;Liberar la EDT?</td>
          <td class="TxtTabla"><input type="radio" name="enviar" id="enviar" value="1"  checked  >No <input type="radio"  name="enviar" id="enviar" value="2" >Si
	      </td>
        </tr>

<?php  
	//si se lecciona no liberara, se muestra la seccion, para ingresar las observaciones      
//if($enviar==1)
{
?>
        <tr>
          <td class="TituloTabla" colspan="2">Observaciones </td>
        </tr>
        <tr>
          <td class="TituloTabla" colspan="2" align="center">
            <textarea name="observacion" id="observacion" cols="100%" rows="5"  maxlength="490" onKeyUp="return ismaxlength(this)"  onKeyPress=" return acceptComilla(event)"  ><? echo $comenta_vobo; ?></textarea></td>
        </tr>

<?php
}
?>		

      </table>

<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<?php echo $cualProyecto; ?>">
  		    <input name="inf_proy" type="hidden" id="inf_proy" value="<?php echo $inf_proy; ?>">

  		    <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()" ></td>
        </tr>
  </table>
      </td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
<script language="JavaScript" type="text/JavaScript">

function envia2()
{ 

		//se se ha seleccionado no en la seccion "Liberar la EDT"
		if(document.Form1.enviar[0].checked)
		{

			if(document.Form1.observacion.value=="")
			{
				alert("Por favor diligencie el campo de observaciones ");
			}
			else if(document.Form1.fechaInicio.value=="")
			{
				alert("Por favor especifique la fecha de inicio del proyecto ");
			}
			else
			{
//				alert ("si");

				document.Form1.recarga.value="2";
				document.Form1.submit();
				
			}
		}

		//si se ha seleccionado si en la seccion "Liberar la EDT"
		if(document.Form1.enviar[1].checked)
		{
			if(document.Form1.fechaInicio.value=="")
			{
				alert("Por favor especifique la fecha de inicio del proyecto ");
			}
			else
			{

				document.Form1.recarga.value="2";
				document.Form1.submit();
				
			}
		}







	
}
var nav4 = window.Event ? true : false;

function acceptComilla(evt){   
var key = nav4 ? evt.which : evt.keyCode;   

return (key != 39);
}
</script>

<script language="JavaScript">
		 var cal = new calendar2(document.forms['Form1'].elements['fechaInicio']);
		 cal.year_scroll = true;
		 cal.time_comp = false;
</script>
</body>
</html>

<? mssql_close ($conexion); ?>	
