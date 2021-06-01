<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php

session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


	include("fncEnviaMailPEAR.php");
	$division=0;

	//CONSULTA LA INFORMACION DE LA ACTIVIDAD, CEN EL FIN DE OBTENER EL ID DE LA DIVISION
	$sql_act="select * from Actividades where id_actividad=10 and id_proyecto=".$cualProyecto;
	$cur_Act=mssql_query($sql_act);
	if($datos_act=mssql_fetch_Array($cur_Act))
	{
			$division=$datos_act["id_division"];
/*
		if($datos_act["nivel"]==3)
			$sql_act2="select id_division from Actividades where id_actividad=10 and id_proyecto=".$cualProyecto." and nivel=3";

		if($datos_act["nivel"]==4)
			$sql_act2="select id_division from Actividades where id_actividad=(select dependeDe from Actividades where id_actividad=11 and id_proyecto=".$cualProyecto."  and 				nivel=4) 		and id_proyecto=".$cualProyecto."  and nivel=3";

		$cur_act2=mssql_query($sql_act2);
		if($datos_divs=mssql_fetcha_array($cur_act2))
		{
			$division=$datos_divs["id_division"];
		}
		*/
	}



	$pAsunto="Sobre planeación de actividades";
	$vigencia=2013;
	$mes=3;

	$meses = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );

	//CONSULTA LA INFORMACION DE LA PLANEACION DE LOS PARTICIPANTES, QUE TIENEN MAS DE UN HOMBRE MES (SUMANDO LO PLANEADO EN TODOS LOS PORYECTOS)
	$sql_usua="select SUM (hombresMes) as total_hombre_mes ,unidad from PlaneacionProyectos where vigencia=".$vigencia." and mes=".$mes." and unidad=18121
			   group by(unidad)  ";
	$eCursorMsql=mssql_query($sql_usua);
//echo " *** ".mssql_num_rows($eCursorMsql)." ---".$sql_usua."<br><br>";
	while($eRegMsql=mssql_fetch_array($eCursorMsql))
	{	


		$pTema='
		<table width="100%" border="0">
		  <tr class="Estilo2">
			<td width="14%">Asunto:</td>
			<td width="86%">Sobre planeaci&oacute;n  de actividades</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr class="Estilo2">
			<td colspan="2">El usuario nombre [unidad], se encunetra sobreplaneado en los siguientes periodos de tiempo:</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td colspan="2"><table width="100%" border="0">
			  <tr class="Estilo2">
				<td width="24%" align="left">Periodo</td>
				<td width="16%" align="left">Total Planeado</td>
				<td colspan="2" align="left">Detalle de planeacion</td>
				</tr> 

				<tr class="Estilo2">
						<td>  '.$meses[$mes].' - '.$vigencia.'</td>
						<td>'.$eRegMsql["total_hombre_mes"].'</td>
						<td><table>';



		$sql_usu_2="select SUM (hombresMes) as total_hombre_mes ,PlaneacionProyectos.id_proyecto, Proyectos.nombre ,unidad from PlaneacionProyectos 
					inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto where vigencia=".$vigencia."  and unidad=18121 and mes=".$mes."
					group by PlaneacionProyectos.id_proyecto ,unidad ,Proyectos.nombre ";
		$cur_sql_2=mssql_query($sql_usu_2);
//echo " *** ".mssql_num_rows($cur_sql_2)." ".$sql_usu_2."<br>";
		while($datos_cur=mssql_fetch_array($cur_sql_2))
		{
				$pTema=$pTema.'<tr class="Estilo2"> <td width="7%">'.$datos_cur["total_hombre_mes"].'</td>
							   <td width="53%">'.$datos_cur["nombre"].'</td> </tr>';
		}
	   $miMailUsuarioEM = "carlosmaguirre";

		$pTema= $pTema.'</table></td></tr></table></td>
		  </tr>
		</table> ';

	   //***EnviarMailPEAR	

	   $sql_correos="select email from Usuarios where unidad in(
						select unidad from Usuarios where unidad=".$eRegMsql["unidad"]." --usuario
						union
						select id_director from Proyectos where id_proyecto=".$cualProyecto." --director proy
						union
						select id_director from Divisiones where id_division=".$division." and estadoDiv='A'  --director div
						union
						select unidad from Usuarios where unidad=18120 --quien programa	)";
//	   $cur_correo=mssql_query($sql_correos);
//		while($datos_corre=mssql_fetch_array($cur_correo))
		{
		   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
		   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		   //***FIN EnviarMailPEAR
		   $miMailUsuarioEM = "";
		}

	}

 ?>
</body>
</html>