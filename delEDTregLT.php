<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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


		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
		$error="no";
	//consultamos los LT del lote de control, al cual pretenece el LT a eliminar, para asi identificar, las actividades que se eliminaran, y las que se actualizaran
	$sql_LT="select * from Actividades where  id_proyecto=".$cualProyecto." and nivel=2 and dependeDe=".$cualLC;
	$sql_LT=$sql_LT." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cur_sql_LT=mssql_query($sql_LT);


//	$macro=substr($Lt_macro,0,strrpos($Lt_macro, ".")+1).$cont_acti; 
	$cont_acti=substr($Lt_macro,strrpos($Lt_macro, ".")+1,strlen($Lt_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2, que se eliminara para uzarlo en actualizacion

	while($datos_sql_LT=mssql_fetch_array($cur_sql_LT))
	{
		if($band==1)  //comienza a actualizar, los lotes de control, que estan por debajo de el lote de control a eliminar
		{
//*************************************************
		//obtenemos la macro actividad de los lotes de trabajo que se encuentran por debajo del el lote que se va a eliminar
			// y le adicionamos el ultimo numero del lote de trabajo a eliminar, y lo aumnetamos, para los demas, y asi mostrar los cambios depues de la operación
			$macro=substr($datos_sql_LT["macroactividad"],0,strrpos($datos_sql_LT["macroactividad"], ".")+1).$cont_acti; 
//echo $macro."<br>";
			//actualizamos el Lote de trabajo 
			$sql_up_act="update  Actividades set macroactividad='".$macro."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_sql_LT["id_actividad"]." and dependeDe=".$cualLC." and nivel=2";
//echo "2. ".$sql_up_act." - ".mssql_get_last_message()."<br>"; 
			$cursor_up_ac=mssql_query($sql_up_act);
			if  (trim($cursor_up_ac) == "")  
			{
				$error="si";
			}
			// si no se presento ningun inconveniente, a momento de actualizar el lote de trabjo, uactializamos las divisiones correspondientes 	
			else
			{	

					//consultamos todos las divisiones que pertenecen a el lote de trabajo, para mostrarlas en la tabla de la seccion 'Estructura Actual'
					$sql_div_lt="select  nombre,macroactividad,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and dependeDe=".$datos_sql_LT["id_actividad"];
					$sql_div_lt=$sql_div_lt." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
//echo $sql_div_lt."<br>";
					$cur_div_lt=mssql_query($sql_div_lt);
				
					$cont_acti2=1;//representa el ultimo numero, de las divisiones, y como todas tienen un cosecutivo, se lo adicionamos al momento de estrher la macro actividad de la division	
					while($datos_div_lt=mssql_fetch_array($cur_div_lt))
					{

						//estrahemos la macro de las divisiones del lote de control, hasta el identificador del lote de control, y le adicionamos, el consecutivo del lote de trabajo que se eliminara y el consecutivo de la division
//						$macro2=substr($datos_div_lt["macroactividad"],0,strrpos($datos_div_lt["macroactividad"], ".")+(-1)).$cont_acti.".".$cont_acti2; 			
						$macro2=$macro.".".$cont_acti2; 		
//echo $macro2."<br>";
//$macro2=substr($datos_Lt_control["macroactividad"],0,strrpos($datos_Lt_control["macroactividad"], ".")+1).$cont_acti.".".$cont_acti2; 

//echo $cont_acti." - ".$macro2." - ".$datos_div_lt["macroactividad"]." - ".$cont_acti."<br>";						

						//Actualizamos la division
						$sql_up_div="update  Actividades set macroactividad='".$macro2."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div_lt["id_actividad"]." and dependeDe=".$datos_sql_LT["id_actividad"]." and nivel=3";
//echo $sql_up_div;
						$cursor_sql_up_div=mssql_query($sql_up_div);
						if  (trim($cursor_sql_up_div) == "")  
						{
							$error="si";
						}		
						// si no se presento ningun inconveniente, a momento de actualizar la divisiones, uactializamos las actividades correspondientes 	
						else
						{																
//echo $macro2;
//echo $datos_div_lt["nombre"] ; 		
							//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa  
							$sql_ant="select macroactividad,nombre,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$datos_div_lt["id_actividad"];
							$sql_ant=$sql_ant." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";				
							$cur_des=mssql_query($sql_ant); //datos despues
							$cont_acti3=1;  //consecutivo de las actividades
								while($datos_des=mssql_fetch_array($cur_des))
								{
									//estrahemos la macro de las actividades de las divisiones, hasta el identificador del lote de control, y le adicionamos, el consecutivo del lote de trabajo que se eliminara y el consecutivo de la division, y el consecutivo de la actividad
										$macr=explode(".",$datos_des["macroactividad"]);
										$macro3=$macr[0].".".$cont_acti.".".$cont_acti2.".A.".$cont_acti3; 
//echo $macr[0]." - ". $macro3."<br>";
										//Actualizamos las actividades de la division
										$sql_up_div="update  Actividades set macroactividad='".$macro3."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_des["id_actividad"]." and dependeDe=".$datos_div_lt["id_actividad"]." and nivel=4";
										$cursor_sql_up_div=mssql_query($sql_up_div);
										if  (trim($cursor_sql_up_div) == "")  
										{
											$error="si";
										}	
										
//echo $macro3;
//echo $datos_des["nombre"];
			
									$cont_acti3++;
								}
						}
						$cont_acti2++; //aumentamos el identificador de la division
					}
					$cont_acti++;  //aumentamos el identificador del lote de trabajo
			}


///////********************************************

		}		
		//si el lote de control corresponde al que se va a eliminar, procedemos con la eliminacion de las actividades, divisiones, asociadas a lote de trabajo 
		if($cualLT==$datos_sql_LT["id_actividad"])
		{
					$band=1;
					//consultamos las divisiones  que peetenecen al LT
					$sql_div="select * from Actividades where  id_proyecto=".$cualProyecto."  and nivel=3 and dependeDe=".$datos_sql_LT["id_actividad"];
					$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";				
					$cur_div=mssql_query($sql_div);
					while($datos_div_LT=mssql_fetch_array($cur_div))
					{
						$cualDIvision=$datos_div_LT["id_actividad"];

						//eliminamos la actividades asociadas a la division
						$sql_ele_act="delete from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDIvision."  and  nivel=4";
						$cur_ele_act=mssql_query($sql_ele_act);	
						if  (trim($cur_ele_act) == "")  
						{
							$error="si";
						}	
	
						//eliminamos la division
						$sql_ele_div="delete from Actividades where id_proyecto=".$cualProyecto." and  id_actividad=".$cualDIvision." and dependeDe =".$cualLT." and nivel=3";
						$cur_ele_div=mssql_query($sql_ele_div);	
						if  (trim($cur_ele_div) == "")  
						{
							$error="si";
						}	
			
					}
						//eliminamos el lote de control
						$sql_ele_lt="delete from Actividades where  id_proyecto=".$cualProyecto."  and nivel=2 and dependeDe=".$cualLC." and id_actividad=".$cualLT;
						$cur_ele_lt=mssql_query($sql_ele_lt);	
						if  (trim($cur_ele_lt) == "")  
						{
							$error="si";
						}	
		}

	}


		//$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");

	if  (trim($error)=="no")  {
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operación realizada satisfactoriamente.');</script>"); 
	} 
	else {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la grabación');</script>");
	}

	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>

<html>
<head>
<?php 
//$_SESSION["sesProyLaboratorio"]
	//trahemos el lote de control mas actual asociado a el  proyecto .$cualProyecto

	$sql_max_lc="select MAX(lote) as lc_max from (SELECT str(SUBSTRING(macroactividad,3, LEN(macroactividad))) AS lote FROM Actividades WHERE id_proyecto=".$cualProyecto." and nivel = 1 ) A";
	$cursor_max_lc=mssql_query($sql_max_lc);
	if($datos_max_lc=mssql_fetch_array($cursor_max_lc))
	{
		$lc_max=$datos_max_lc["lc_max"];
	}
	//si no se encontraron registros, es por que es el primer lote de control que se creara en el proyecto
	else
	{
		$lc_max=0;
	}
	
?>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Eliminar Lote de trabajo </td>
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
<?php
	$sql_Lc="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=1 and id_actividad=".$cualLC;
	$cur_Lc=mssql_query($sql_Lc);		
?>
        <tr>
          <td width="42%" class="TituloTabla">Lote de control </td>
          <td width="58%" class="TxtTabla">
<?php
			if($datos_Lc=mssql_fetch_array($cur_Lc))
			{
				//almacenamos la informacion del LC, para utilizarlos en la seccion de vista previa
				echo $datos_Lc["macroactividad"]." - ".$datos_Lc["nombre"];
				$Lc_nom=$datos_Lc["nombre"];
				$Lc_macro=$datos_Lc["macroactividad"];
			}		
?>
		  </td>
		</tr>
<?php

	$sql_Lt="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=".$cualLT;
	$cur_Lt=mssql_query($sql_Lt);	
?>
		<tr>
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla">

<?php
			if($datos_Lt=mssql_fetch_array($cur_Lt))
			{
				echo $datos_Lt["macroactividad"]." - ".$datos_Lt["nombre"];
				$lt_identi= $datos_Lt["id_actividad"];
				$Lt_macro= $datos_Lt["macroactividad"];
			}		
?>
		  </td>
		</tr>
		<tr>
          <td class="TxtTabla" colspan="2">&nbsp;
				
			</td>
		</tr>
        
      </table>


<?php


/*	//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa
	$sql_ant="select macroactividad,nombre,id_actividad from Actividades where id_proyecto=".$cualProyecto." and dependeDe=27";

	$cur_des=mssql_query($sql_ant); //datos despues

*/


//	$num_filas=mssql_num_rows($cur_des);
	
	//consultamos si existen mas Lotes de trabajo, por debajo de la que se eliminara, con el fin de validar si se mostrar la seccion de la vista previa
	$sql_reg_desp="select COUNT(*)as cant from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualLC." 
	  and cast(reverse(substring(reverse('".$Lt_macro."'),1,charindex('.', reverse('".$Lt_macro."'))-1)) as int)< cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
// and ".$cualLT." < id_actividad";
	$cursor_reg_desp=mssql_query($sql_reg_desp);
	$datos_reg_desp=mssql_fetch_array($cursor_reg_desp);
	$cant_reg_elimi=1; //permite saber, cuantas actividades se eliminarian, y asi dibujar las filas correspondientes, para ajustar el tamaño de la tabla  NO2 de la vista previa

////****************************************** si la cnatidad de registros que exiten en el Lote division, es mayor a 1, mostrar la tabla con los datos que se veran afectados
	if(1<=$datos_reg_desp["cant"])
	{
?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">

      </table>   
	   <table width="100%"   border="0" cellspacing="1" cellpadding="0" >
      <tr class="TituloTabla2">
        <td width="50%">Estructura actual</td>
        <td width="50%"><p>Al eliminar el registro la estructura se ver&aacute; de la siguiente forma</p></td>

        </tr>


      <tr class="TxtTabla">
        <td width="50%" align="center" valign="top">
			
			<table width="100%" border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador </td>
                <td colspan="4"><p>Nombre</p></td>
                </tr>
				<tr>
					<td class="TxtTabla"><?php echo $Lc_macro; ?></td>
					<td class="TxtTabla"  colspan="3"><?php echo $Lc_nom ; ?></td>
                    <td class="TxtTabla">&nbsp;                        
                    </td>	
				</tr>
				<tr>

<?php

	//consultamos los lotes de trabajo del lote de control
$sql_Lt_control="select * from Actividades where  id_proyecto=".$cualProyecto."  and nivel=2 and dependeDe=".$cualLC;
$sql_Lt_control=$sql_Lt_control." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cur_Lt_control=mssql_query($sql_Lt_control);
  while($datos_Lt_control=mssql_fetch_array($cur_Lt_control))
  {
?>
				<tr>
						<td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lt_control["macroactividad"]; ?></td>
						<td class="TxtTabla" colspan="2">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lt_control["nombre"]; ?></td>
						<td class="TxtTabla">&nbsp;
							
						</td>	
						<td class="TxtTabla">
<?php
					//mostramos el icono de color rojo, que identifica la division a eliminar
					if(substr($Lt_macro,strrpos($Lt_macro, ".")+1,strlen($Lt_macro))== substr($datos_Lt_control["macroactividad"],strrpos($datos_Lt_control["macroactividad"], ".")+1,strlen($datos_Lt_control["macroactividad"])))
					{
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaRojo.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}

					//mostramos las actividades que estan por debajo, con el icono de color rojo, con el fin de dar a conocer las actividades que se actualizaran
					if(substr($Lt_macro,strrpos($Lt_macro, ".")+1,strlen($Lt_macro))< substr($datos_Lt_control["macroactividad"],strrpos($datos_Lt_control["macroactividad"], ".")+1,strlen($datos_Lt_control["macroactividad"])))
					{
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaAzul.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}
?>
<?php
/*
						//mostramos el icono de color azul, para referenciar, los LT que se veran afectadas por la eliminacion, 
						if($cualLT<=$datos_Lt_control["id_actividad"])
						{
							//solo incrementa la cuando lla actividad de el LT corresponde al que se eliminara
							if($cualLT==$datos_Lt_control["id_actividad"]) {
								$cant_reg_elimi++;							}	
	?>
							<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaAzul.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
	<?php
						}
*/
	?>
						</td>
				</tr>
<?php
/*
		//consultamos todos las divisiones que pertenecen a el lote de trabajo, para mostrarlas en la tabla de la seccion 'Estructura Actual'
		$sql_div_lt="select  nombre,macroactividad,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and dependeDe=".$datos_Lt_control["id_actividad"];
		$sql_div_lt=$sql_div_lt." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
//echo $sql_div_lt;
		$cur_div_lt=mssql_query($sql_div_lt);

		while($datos_div_lt=mssql_fetch_array($cur_div_lt))
		{
			$ban_div_may=0; //permite identificar las divisiones que estan or debajo de la que se va a eliminar, con el fin de mostrar el icono azul de las actividades a las que pertenece
?>

					<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["macroactividad"]; ?></td>
					<td class="TxtTabla"  colspan="2">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["nombre"] ; ?>  </td>
					<td class="TxtTabla">
<?php
						//mostramos el icono de color azul, para referenciar, las divisiones que se veran afectadas por la eliminacion, 
						if($lt_identi<=$datos_div_lt["dependeDe"])
						{
							$ban_div_may=1;
							//solo incrementa la cuando la actividad de la division es igual a el LT correspondiente al que se eliminara
							if($cualLT==$datos_div_lt["dependeDe"]) {
								$cant_reg_elimi++;							}	
	?>
							<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaAzul.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
	<?php
						}
	?>
					</td>
						<td class="TxtTabla">
						</td>	
				</tr>
<?php
	//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa  
	$sql_ant="select macroactividad,nombre,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$datos_div_lt["id_actividad"];
	$sql_ant=$sql_ant." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cur_des=mssql_query($sql_ant); //datos despues
				while($datos_des=mssql_fetch_array($cur_des))
				{
	?>
					<tr>
						<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
							<?php echo $datos_des["macroactividad"]; ?>
						</td>
						<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
							<?php echo $datos_des["nombre"]; ?>
						</td>
						<td class="TxtTabla">
	<?php
						///solo mostramos el icono, en la actividad a eliminar y en las actividades que son las que estan por debajo de ella 
						if(($ban_div_may==1)) 
						{
							//solo incrementa la cuando la dependencia de la division  de la actividad corresponde con el  LT que se eliminara
							if($cualLT==$datos_div_lt["dependeDe"]){
								$cant_reg_elimi++;							}	

	?>
							<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaAzul.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
	<?php
						}
	?>
						</td>		
						<td class="TxtTabla">
						</td>	
						<td class="TxtTabla">
						
						</td>			
					</tr>
	<?php
				}
		}
*/
}

?>
			</table>		</td>
<?php
	
  ?>
        <td align="center" valign="top" >
			<table width="100%" height="100%"    border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador</td>
                <td ><p>Nombre</p></td>
                </tr>

                <tr>

					<td class="TxtTabla"><?php echo $Lc_macro; ?></td>
					<td class="TxtTabla"  colspan="2"><?php echo $Lc_nom ; ?></td>


				</tr>

				<tr>


<?php
///////********************************************************************************************************************************************

	//consultamos los lotes de trabajo del lote de control
$sql_Lt_control="select * from Actividades where  id_proyecto=".$cualProyecto."  and nivel=2 and dependeDe=".$cualLC;
$sql_Lt_control=$sql_Lt_control." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cur_Lt_control=mssql_query($sql_Lt_control);
  while($datos_Lt_control=mssql_fetch_array($cur_Lt_control))
  {
	//imprimimos las atividades y divisiones, de los loste de control, que estan por encima de el lote de control a aeliminar, con la informacion actual
//	if($datos_Lt_control["id_actividad"]<$lt_identi)
	if(substr($datos_Lt_control["macroactividad"],strrpos($datos_Lt_control["macroactividad"], ".")+1,strlen($datos_Lt_control["macroactividad"])) < substr($Lt_macro,strrpos($Lt_macro, ".")+1,strlen($Lt_macro)))
	{

?>
				<tr>
						<td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lt_control["macroactividad"]."  ";  ?></td>
						<td class="TxtTabla" colspan="2">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lt_control["nombre"]; ?></td>


				</tr>
<?php
/*
		//consultamos todas las divisiones que pertenecen a el lote de trabajo, para mostrarlas en la tabla de la seccion 'Estructura Actual'
		$sql_div_lt="select  nombre,macroactividad,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and dependeDe=".$datos_Lt_control["id_actividad"];
		$sql_div_lt=$sql_div_lt." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
//echo $sql_div_lt;
		$cur_div_lt=mssql_query($sql_div_lt);		
		while($datos_div_lt=mssql_fetch_array($cur_div_lt))
		{
?>

					<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["macroactividad"]; ?></td>
					<td class="TxtTabla"  colspan="2">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["nombre"] ; ?>  </td>

				</tr>
<?php
	//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa  
	$sql_ant="select macroactividad,nombre,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$datos_div_lt["id_actividad"];
	$sql_ant=$sql_ant." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cur_des=mssql_query($sql_ant); //datos despues
				while($datos_des=mssql_fetch_array($cur_des))
				{
	?>
					<tr>
						<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
							<?php echo $datos_des["macroactividad"]; ?>
						</td>
						<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
							<?php echo $datos_des["nombre"]; ?>
						</td>
	

		
					</tr>
	<?php
				}
		}
*/
	}
	//si el lote es igual o mayor, se lista la info, con los cambios que se realizaran, al momento de la eliminación
	else
	{
//				$lt_identi= $datos_Lt["id_actividad"];
//			$Lt_macro= $datos_Lt["macroactividad"];

		//si el lote de trabajo que se esta extrayendo de la BD es el que se va a eliminar, se toma el ultimo numero de la macro actividad, para adicionarlo, a las macroactividades, de las actividades que se encuentra por debajo de el
		if($lt_identi==$datos_Lt_control["id_actividad"])
		{
			$cont_acti=substr($Lt_macro,strrpos($Lt_macro, ".")+1,strlen($Lt_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la division LT2.2.2 que se va a eliminar					
//			echo $cont_acti."<br>";
		}
		//si el id_actividad no es igual, es por que se este imprimiendo los id_activiades que estan por debajo de la que se eliminara
		else
		{
			//obtenemos la macro actividad de los lotes de trabajo que se encuentran por debajo del el lote que se va a eliminar
			// y le adicionamos el ultimo numero del lote de trabajo a eliminar, y lo aumnetamos, para los demas, y asi mostrar los cambios depues de la operación
			$macro=substr($datos_Lt_control["macroactividad"],0,strrpos($datos_Lt_control["macroactividad"], ".")+1).$cont_acti; 


?>
		<tr>
				<td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $macro.""; ?> 
				</td>
				<td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lt_control["nombre"]; ?>
				</td>
		</tr>		


<?php
//**************************************************
/*
			//consultamos todos las divisiones que pertenecen a el lote de trabajo, para mostrarlas en la tabla de la seccion 'Estructura Actual'
			$sql_div_lt="select  nombre,macroactividad,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and dependeDe=".$datos_Lt_control["id_actividad"];
			$sql_div_lt=$sql_div_lt." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	//echo $sql_div_lt;
			$cur_div_lt=mssql_query($sql_div_lt);
		
			$cont_acti2=1;//representa el ultimo numero, de las divisiones, y como todas tienen un cosecutivo, se lo adicionamos al momento de estrher la macro actividad de la division	
			while($datos_div_lt=mssql_fetch_array($cur_div_lt))
			{
				//estrahemos la macro de las divisiones del lote de control, hasta el identificador del lote de control, y le adicionamos, el consecutivo del lote de trabajo que se eliminara y el consecutivo de la division
				$macro2=substr($datos_Lt_control["macroactividad"],0,strrpos($datos_Lt_control["macroactividad"], ".")+1).$cont_acti.".".$cont_acti2; 

	?>
	
						<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<?php echo $macro2; ?></td>
						<td class="TxtTabla"  colspan="2">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["nombre"] ; ?>  </td>
	
					</tr>
	<?php
			//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa  
				$sql_ant="select macroactividad,nombre,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$datos_div_lt["id_actividad"];
				$sql_ant=$sql_ant." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cur_des=mssql_query($sql_ant); //datos despues
				$cont_acti3=1;  //consecutivo de las actividades
					while($datos_des=mssql_fetch_array($cur_des))
					{
						//estrahemos la macro de las actividades de las divisiones, hasta el identificador del lote de control, y le adicionamos, el consecutivo del lote de trabajo que se eliminara y el consecutivo de la division, y el consecutivo de la actividad
							$macr=explode(".",$datos_des["macroactividad"]);
							$macro3=$macr[0].".".$cont_acti.".".$cont_acti2.".A.".$cont_acti3; 
		?>
						<tr>
							<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
								<?php echo $macro3;  ?>
							</td>
							<td class="TxtTabla">&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
								<?php echo $datos_des["nombre"]; ?>
							</td>
		

		
						</tr>
		<?php
						$cont_acti3++;
					}
				$cont_acti2++; //aumentamos el identificador de la division
			}
*/
			$cont_acti++;  //aumentamos el identificador del lote de trabajo
//*********
		}
	}
}
		//se imprimen, la filas restantes de ajuste, en la tabla No 2
		for($f=1;$f<=$cant_reg_elimi; $f++)
		{
?>
                <tr>
					<td class="TxtTabla" colspan="2">&nbsp;</td>
				</tr>
<?php
		}
?>

    </table>

<?php 

	}

?>        </td>
        </tr>
        </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
		<tr>
			
          <td  align="center" class="TxtTabla"><strong>¿Esta seguro de eliminar el registro y realizar los cambios sugeridos en la EDT<?php //echo $nom_act; ?>?</strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="2">
  		    <input name="Lt_macro" type="hidden" id="Lt_macro" value="<?php echo $Lt_macro; ?>">

  		    <input name="Submit" type="button" class="Boton" value="Cancelar"  onClick="window.close()" >
  		    <input name="Submit" type="button" class="Boton" value="Eliminar" onClick="document.Form1.submit();" >
		</td>
        </tr>
      </table>
      </td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
</body>
</html>

<? mssql_close ($conexion); ?>	

