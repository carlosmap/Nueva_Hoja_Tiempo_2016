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
	$sql_Lc="select * from Actividades where  id_proyecto=".$cualProyecto."  and nivel=1";
	$sql_Lc=$sql_Lc." order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
	$cur_sql_Lc=mssql_query($sql_Lc);

	$cont_acti=substr($Lc_macro,2,strlen($Lc_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica el LC LC2 que se va a eliminar							

	while($datos_sql_Lc=mssql_fetch_array($cur_sql_Lc))
	{

		if($band==1)  //comienza a actualizar, los lotes de control, que estan por debajo de el lote de control a eliminar
		{
			//generamos la nueva macro actividad, para los lotes de control, que estan por debajo de el lote eliminado
			$macro=substr($datos_sql_Lc["macroactividad"],0,2).$cont_acti;	
			//actualizamos el lote de control
			$sql_up_lc="update Actividades set macroactividad='".$macro."' where  id_proyecto=".$cualProyecto."  and nivel=1 and id_actividad=".$datos_sql_Lc["id_actividad"];
//echo $sql_up_lc." -- ". mssql_get_last_message()."<br>"; 
			$cur_up_lc=mssql_query($sql_up_lc);
			if  (trim($cur_up_lc) == "")  
			{
				$error="si";
			}
			// si no se presento ningun inconveniente, a momento de actualizar el lote de control, uactializamos los lotes de trabajo, divisiones , y actividades correspondientes
			else
			{
				//consultamos los LT del lote de trabajo,  para asi identificar, las actividades (LT,DIV,Activi)que se actualizaran
				$sql_LT="select * from Actividades where  id_proyecto=".$cualProyecto." and nivel=2 and dependeDe=".$datos_sql_Lc["id_actividad"];
				$sql_LT=$sql_LT." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cur_sql_LT=mssql_query($sql_LT);	
				$cont_acti2=1;
				while($datos_sql_LT=mssql_fetch_array($cur_sql_LT))
				{
					$macro2=substr($datos_sql_LT["macroactividad"],0,2).$cont_acti.".".$cont_acti2;
					//actualizamos los lotes de trabajo
					$sql_up_act="update  Actividades set macroactividad='".$macro2."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_sql_LT["id_actividad"]." and dependeDe=".$datos_sql_Lc["id_actividad"]." and nivel=2";
//echo $sql_up_act." -- ". mssql_get_last_message()."<br>"; 
					$cursor_up_ac=mssql_query($sql_up_act);
					if  (trim($cursor_up_ac) == "")  
					{
						$error="si";
					}
					// si no se presento ningun inconveniente, a momento de actualizar el lote de trabajo, actializamos las  divisiones , y actividades correspondientes
					else
					{

						//consultamos todos las divisiones que pertenecen a el lote de trabajo
						$sql_div_lt="select  nombre,macroactividad,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and dependeDe=".$datos_sql_LT["id_actividad"];
						$sql_div_lt=$sql_div_lt." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_div_lt=mssql_query($sql_div_lt);					
						$cont_acti3=1;//representa el ultimo numero, de las divisiones, y como todas tienen un cosecutivo, se lo adicionamos al momento de estrher la macro actividad de la division	
						while($datos_div_lt=mssql_fetch_array($cur_div_lt))
						{
							//estrahemos la macro de las divisiones del lote de control, hasta el identificador del lote de control, y le adicionamos, el consecutivo del lote de trabajo que se eliminara y el consecutivo de la division
							$macro3=substr($datos_div_lt["macroactividad"],0,2).$cont_acti.".".$cont_acti2.".".$cont_acti3; 			
	
							//Actualizamos la division
							$sql_up_div="update  Actividades set macroactividad='".$macro3."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div_lt["id_actividad"]." and dependeDe=".$datos_sql_LT["id_actividad"]." and nivel=3";
//echo $sql_up_div." -- ". mssql_get_last_message()."<br>"; 
							$cursor_sql_up_div=mssql_query($sql_up_div);
							if  (trim($cursor_sql_up_div) == "")  
							{
								$error="si";
							}
							else
							{
								//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa  
								$sql_des="select macroactividad,nombre,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and nivel=4 and dependeDe=".$datos_div_lt["id_actividad"];
								$sql_des=$sql_des." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";						
								$cur_des=mssql_query($sql_des); //datos despues
								$cont_acti4=1;  //consecutivo de las actividades
								while($datos_des=mssql_fetch_array($cur_des))
								{
									//estrahemos la macro de las actividades de las divisiones, hasta el identificador del lote de control, y le adicionamos, el consecutivo del lote de trabajo que se eliminara y el consecutivo de la division, y el consecutivo de la actividad
										$macro4=substr($datos_div_lt["macroactividad"],0,2).$cont_acti.".".$cont_acti2.".".$cont_acti3.".A.".$cont_acti4; 
//echo "<br> - ".$macro3."<br>";
										//Actualizamos las actividades de la division
										$sql_up_div="update  Actividades set macroactividad='".$macro4."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_des["id_actividad"]." and dependeDe=".$datos_div_lt["id_actividad"]." and nivel=4";
//echo $sql_up_div." -- ". mssql_get_last_message()."<br>"; 
										$cursor_sql_up_div=mssql_query($sql_up_div);
										if  (trim($cursor_sql_up_div) == "")  
										{
											$error="si";
										}	
									
									$cont_acti4++;
								}								
							}	
							$cont_acti3++;
						}
						
					}
					$cont_acti2++; //LT
				}		
				
			}
			$cont_acti++;  //identificador LC
		}

		//si el lote de control corresponde al que se va a eliminar, procedemos con la eliminacion de las actividades, divisiones, asociadas a lote de control 
		if($cualLC==$datos_sql_Lc["id_actividad"])
		{
			$band=1;

			//eliminamos las actividades del lote de control
			$sql_del_act="delete from Actividades where  id_proyecto=".$cualProyecto." and actPrincipal=".$datos_sql_Lc["id_actividad"]." and nivel=4";
			$cur_del_act=mssql_query($sql_del_act);	
			if  (trim($cur_del_act) == "")  
			{
				$error="si";
			}	
//echo $sql_del_act." -- ". mssql_get_last_message()."<br>"; 

			//eliminamos las divisiones del los lotes de trabajo, pertenecientes a la division
			$sql_del_div="delete from Actividades where  id_proyecto=".$cualProyecto." and actPrincipal=".$datos_sql_Lc["id_actividad"]." and nivel=3";
			$cur_del_div=mssql_query($sql_del_div);	
			if  (trim($cur_del_div) == "")  
			{
				$error="si";
			}	
//echo  $sql_del_div." -- ". mssql_get_last_message()."<br>"; 
			//eliminamos los lotes de trabajo de el lote de control
			$sql_del_div="delete from Actividades where  id_proyecto=".$cualProyecto." and actPrincipal=".$datos_sql_Lc["id_actividad"]." and nivel=2";
			$cur_del_div=mssql_query($sql_del_div);	
			if  (trim($cur_del_div) == "")  
			{
				$error="si";
			}
//echo $sql_del_div." -- ".mssql_get_last_message()."<br>"; 

			//eliminamos el lote de control
			$sql_del_div="delete from Actividades where  id_proyecto=".$cualProyecto." and id_actividad=".$datos_sql_Lc["id_actividad"];
			$cur_del_div=mssql_query($sql_del_div);	
			if  (trim($cur_del_div) == "")  
			{
				$error="si";
			}	
//echo $sql_del_div." -- ".mssql_get_last_message()."<br>"; 
		}
	}
//$macro=substr($Lc_macro,0,2).$cont_acti;
//echo $cont_acti." - ".$macro." - ".$Lc_macro;

		//$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");

	if  (trim($error)=="no")  {

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
    <td class="TituloUsuario">Eliminar Lote de control </td>
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
				$lc_identi= $datos_Lc["id_actividad"]; //almacenamos la id de el LC, para utilizarlo en la vist previa
				$Lc_act_prin=$datos_Lc["actPrincipal"];


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


	//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa
	
	//consultamos si existen mas Lotes de trabajo, por debajo de la que se eliminara, con el fin de validar si se mostrar la seccion de la vista previa
	$sql_reg_desp="select COUNT(*)as cant from Actividades where id_proyecto=".$cualProyecto." and nivel=1 
	  and cast(reverse(substring(reverse('".$Lc_macro."'),1,charindex('C', reverse('".$Lc_macro."'))-1)) as int)< cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";



	$cursor_reg_desp=mssql_query($sql_reg_desp);
	$datos_reg_desp=mssql_fetch_array($cursor_reg_desp);
	//$cant_reg_elimi=1; //permite saber, cuantas actividades se eliminarian, y asi dibujar las filas correspondientes, para ajustar el tamaño de la tabla  NO2 de la vista previa

////****************************************** si la cnatidad de registros que exiten en el Lote division, es mayor a 1, mostrar la tabla con los datos que se veran afectados
	if(1<=$datos_reg_desp["cant"])
	{
?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">

      </table>   
	   <table width="100%"   border="0" cellspacing="1" cellpadding="0" >
      <tr class="TituloTabla2">
        <td width="50%">Estructura Actual</td>
        <td width="50%"><p>Al eliminar el registro la estructura se vera de la siguiente forma</p></td>

        </tr>


      <tr class="TxtTabla">
        <td width="50%" align="center">
			
			<table width="100%" border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador </td>
                <td colspan="3" >Nombre</td>

                </tr>
<?php

			//consultamos los lotes de control del la EDT
		$sql_Lc_control="select * from Actividades where  id_proyecto=".$cualProyecto."  and nivel=1 order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
		$sql_Lc_control2=$sql_Lc_control;
		$cur_Lc_control=mssql_query($sql_Lc_control);
		  while($datos_Lc_control=mssql_fetch_array($cur_Lc_control))
		  {
		?>
                <tr>
                    <td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lc_control["macroactividad"]; ?></td>
                    <td class="TxtTabla" colspan="2">&nbsp; &nbsp; &nbsp;<?php echo $datos_Lc_control["nombre"]; ?></td>		
					<td  class="TxtTabla">

<?php
					//mostramos el icono de color rojo, que identifica la division a eliminar
					if(substr($Lc_macro,strrpos($Lc_macro, "C")+1,strlen($Lc_macro))== substr($datos_Lc_control["macroactividad"],strrpos($datos_Lc_control["macroactividad"], "C")+1,strlen($datos_Lc_control["macroactividad"])))
					{
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaRojo.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}

					//mostramos las actividades que estan por debajo, con el icono de color rojo, con el fin de dar a conocer las actividades que se actualizaran
					if(substr($Lc_macro,strrpos($Lc_macro, "C")+1,strlen($Lc_macro))< substr($datos_Lc_control["macroactividad"],strrpos($datos_Lc_control["macroactividad"], "C")+1,strlen($datos_Lc_control["macroactividad"])))
					{
							$cant_reg_elimi++;
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaAzul.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}
?>

					</td>
                </tr>
<?php

			}
			
		?>
			</table>
			</td>

        <td width="50%" align="center">
			
			<table width="100%" border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador </td>
                <td ><p>Nombre</p></td>
                </tr>
<?php
		//mostramos como quedara la estructura despues de la eliminación
		  $cur_Lc_control2=mssql_query($sql_Lc_control2);
			$cont_acti=1;
		  while($datos_Lc_control2=mssql_fetch_array($cur_Lc_control2))
		  {
				if($lc_identi==$datos_Lc_control2["id_actividad"])
				{
					$cont_acti=substr($datos_Lc_control2["macroactividad"],2,strlen($datos_Lc_control2["macroactividad"])); //almacenamos el ultimo numero, despues del ultimo punto, que identifica el LC LC2 que se va a eliminar							
				}
				else
				{
					//alteramos la macro del lote de control, y le añadimos el consecutivo de el lote de control a eliminar, y despues incrementamos el valor de la varibale de identificacion
					$macro=substr($datos_Lc_control2["macroactividad"],0,2).$cont_acti;
					$cont_acti++;
?>
                    <tr>
                        <td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $macro; ?></td>
                        <td class="TxtTabla" >&nbsp; &nbsp; &nbsp;<?php echo $datos_Lc_control2["nombre"]; ?></td>
    
                    </tr>		
<?
				}
	
			}

?>
                <tr>
					<td class="TxtTabla" colspan="2">&nbsp;</td>
				</tr>

			</table>
			</td>
			</tr>

<?php
	}
?>

        </table>
	</td>
	</tr>
	</table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
		<tr>
			
          <td  align="center" class="TxtTabla"><strong>¿Esta seguro de eliminar el registro y realizar los cambios sugeridos en la EDT<?php //echo $nom_act; ?>?</strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="2">
  		    <input name="Lc_macro" type="hidden" id="Lc_macro" value="<?php echo $Lc_macro; ?>">

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

