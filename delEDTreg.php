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

		$sql_eliminar="select macroactividad,nombre,id_actividad from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDiv;
		$sql_eliminar=$sql_eliminar." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
		$cursor_eliminar=mssql_query($sql_eliminar);
		if  (trim($cursor_eliminar) == "")  
		{
			$error="si";
		}	
//echo "0. $error ".$sql_eliminar." - ".mssql_get_last_message()."<br><br>"; 

			$cont_acti=substr($acti_macro,strrpos($acti_macro, ".")+1,strlen($acti_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2.A.(2) para uzarlo en la vista previa

			$band=0; //con $band identificamos, si la secuencia del while, ha llegado al registro que se va a eliminar, para comenzar a mostrar  como quedaran los cambios, apartir de este registro							   
			while($datos_eli=mssql_fetch_array($cursor_eliminar))
			{
				if($band==1)  
				{
					$macro=substr($datos_eli["macroactividad"],0,strrpos($datos_eli["macroactividad"], ".")+1).$cont_acti; //extrhemos parte de la macro actividad, y le añadimos el ultimo numero de actividad que se va a eliminar, para componer la macro actividad
					$cont_acti++;
					$sql_up_act="update  Actividades set macroactividad='".$macro."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_eli["id_actividad"]." and dependeDe=".$cualDiv." and nivel=4";
					$cursor_up_ac=mssql_query($sql_up_act);

//echo "2 $error ".$sql_up_act." - ".mssql_get_last_message()."<br>"; 
					if  (trim($cursor_up_ac) == "")  
					{
						$error="si";
					}	

				}
				if($cualACtividad==$datos_eli["id_actividad"])  //si el registro que se esta extrayendo de la BD es el que se va a eliminar
				{
					$band=1;
					$sql_del_act="delete ActividadesRecursos where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad;
					$cur_del_act=mssql_query($sql_del_act);	
					if  (trim($cur_del_act) == "")  
					{
						$error="si";
					}					
//echo "3. $error ".$sql_del_act." - ".mssql_get_last_message()."<br><br>"; 


					//eliminamos los participantes de la division
					$sql_part_act="delete from ParticipantesActividad   where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." ";
					$cur_par_act=mssql_query($sql_part_act);	
//echo $sql_ele_act."<br><br>".mssql_get_last_message();
					if  (trim($cur_par_act) == "")  
					{
						$error="si";
					}	

					$sql_ele_act="delete from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and dependeDe=".$cualDiv." and nivel=4";
					$cur_ele_act=mssql_query($sql_ele_act);	

//echo "1. $error ".$sql_ele_act." - ".mssql_get_last_message()."<br><br>"; 
					if  (trim($cur_ele_act) == "")  
					{
						$error="si";
					}					
				}



			}
	
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
    <td class="TituloUsuario">Eliminar Actividad</td>
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
				echo $datos_Lc["macroactividad"]." - ".$datos_Lc["nombre"];
			}		
?>
		  </td>
		</tr>
<?php
//consultamos la division del lote de trabajo, para trher el depende y asi consultar el lote de trabajo asociado
	$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDiv;
	$cur_a=mssql_query($sql_a);	
	if($datos_lts=mssql_fetch_array($cur_a))	
		$dep_div=$datos_lts["dependeDe"];

	$sql_Lt="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=".$dep_div;
	$cur_Lt=mssql_query($sql_Lt);	
?>
		<tr>
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla">
<?php
			if($datos_Lt=mssql_fetch_array($cur_Lt))
			{
				echo $datos_Lt["macroactividad"]." - ".$datos_Lt["nombre"];
			}		
?>
		  </td>
		</tr>
<?php
	$sql_Ld="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDiv;
//echo $sql_Ld;
	$cur_Ld=mssql_query($sql_Ld);	
?>
		<tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n</td>
          <td class="TxtTabla">
<?php
			if($datos_Ld=mssql_fetch_array($cur_Ld))
			{
				echo $datos_Ld["macroactividad"]." - ".strtoupper($datos_Ld["nombre"]);
				$div_nom=$datos_Ld["nombre"];//almacenamos el nombre de la division, para utilizarlo en la vista previs
				$div_identi=$datos_Ld["macroactividad"];//almacenamos la macroactividad de la division, para utilizarlo en la vista previs
			}		
?>
		  </td>
        </tr>
<?php
////******************************************  ACTIVIDAD A CAMBIAR
$cualAc=$cualACtividad;
///////***************************************
	$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=4 and id_actividad=".$cualACtividad;
	$cur_a=mssql_query($sql_a);	
?>
		<tr>
          <td class="TituloTabla">Actividad a eliminar</td>
          <td class="TxtTabla">
<?php
			if($datos_a=mssql_fetch_array($cur_a))
			{
				echo $datos_a["macroactividad"]." - ".$datos_a["nombre"];
				$nom_act=$datos_a["nombre"];
				$acti_macro= $datos_a["macroactividad"]; //almacenamos la macroactividad, para utilizarla en la vista previa
			}		
?>
		  </td>
        </tr>
		<tr>

          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>
        
      </table>

      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2"></td>
        </tr>
      </table>   

<?php
	//consultamos las actividades asociadas a la division, con el fin de mostrarlos en la vista previa
	$sql_ant="select macroactividad,nombre,id_actividad from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDiv;
	$sql_ant=$sql_ant." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
//echo $sql_ant;
//echo $sql_ant;
	$cur_ant=mssql_query($sql_ant); //datos antes
	$cur_des=mssql_query($sql_ant); //datos despues
//	$num_filas=mssql_num_rows($cur_des);
	

	$sql_reg_desp="select COUNT(*)as cant from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDiv." 
	 and cast(reverse(substring(reverse('".$acti_macro."'),1,charindex('.', reverse('".$acti_macro."'))-1)) as int)< cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";

	$cursor_reg_desp=mssql_query($sql_reg_desp);
	$datos_reg_desp=mssql_fetch_array($cursor_reg_desp);

////****************************************** si la cnatidad de registros que exiten en el Lote division, es mayor a 1, mostrar la tabla con los datos que se veran afectados
	if(1<=$datos_reg_desp["cant"])
	{
//				$act_max=substr($act_max,strrpos($act_max, ".")+1,strlen($act_max));
?>
	   <table width="100%"  border="0" cellspacing="1" cellpadding="0" >
      <tr class="TituloTabla2">
        <td width="50%">Estructura Actual</td>
        <td width="50%"><p>Al eliminar el registro la estructura se ver&aacute; de la siguiente forma</p></td>

        </tr>


      <tr class="TxtTabla">
        <td width="5%" align="center" valign="top">
			
			<table width="100%" border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador </td>
                <td colspan="2"><p>Nombre</p></td>
                </tr>
				<tr>

					<td class="TxtTabla"><?php echo $div_identi; ?></td>
					<td class="TxtTabla"  colspan="2"><?php echo $div_nom ; ?></td>
				</tr>
<?php
			while($datos_des=mssql_fetch_array($cur_des))
			{
?>
				<tr>
					<td class="TxtTabla">&nbsp; &nbsp; &nbsp;
						<?php echo $datos_des["macroactividad"]; ?>
					</td>
					<td class="TxtTabla">&nbsp; &nbsp; &nbsp;
						<?php echo $datos_des["nombre"]; ?>
					</td>
					<td class="TxtTabla">
<?php
					if(substr($acti_macro,strrpos($acti_macro, ".")+1,strlen($acti_macro))== substr($datos_des["macroactividad"],strrpos($datos_des["macroactividad"], ".")+1,strlen($datos_des["macroactividad"])))
					{
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaRojo.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}

					///solo mostramos el icono, en la actividad a eliminar y en las actividades que son las que estan por debajo de ella
					//comparando con el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2.A.(2, de la actividad seleccionada y la que se extrahe de la base de datos

					if(substr($acti_macro,strrpos($acti_macro, ".")+1,strlen($acti_macro))< substr($datos_des["macroactividad"],strrpos($datos_des["macroactividad"], ".")+1,strlen($datos_des["macroactividad"])))
					{
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
			</table>		</td>
        <td align="center" valign="top">
			<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador</td>
                <td ><p>Nombre</p></td>
                </tr>
				<tr>

					<td class="TxtTabla"><?php echo $div_identi; ?></td>
					<td class="TxtTabla"><?php echo $div_nom ; ?></td>
				</tr>
<?php
			$cont_acti=substr($acti_macro,strrpos($acti_macro, ".")+1,strlen($acti_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2.A.(2) para uzarlo en la vista previa
			$band=0; //con $band identificamos, si la secuencia del while, ha llegado al registro que se va a eliminar, para comenzar a mostrar  como quedaran los cambios, apartir de este registro							   
			while($datos_ant=mssql_fetch_array($cur_ant))
			{
				if($cualAc==$datos_ant["id_actividad"])  //si el registro que se esta extrayendo de la BD es el que se va a eliminar
				{
					$band=1;
					continue;  //saltamos la secuencia del while, para que no se imprima el registro a  eliminar
				}
				if($band==1)  //comienza a imprimir, como quedaran los registros, despues de la eliminacion
				{
					$macro=substr($datos_ant["macroactividad"],0,strrpos($datos_ant["macroactividad"], ".")+1).$cont_acti; //extrhemos parte de la macro actividad, y le añadimos el ultimo numero de actividad que se va a eliminar, para componer la macro actividad
					$cont_acti++;
?>
                    <tr>
                        <td class="TxtTabla"> &nbsp; &nbsp;  &nbsp;
                            <?php echo $macro; ?>
                        </td>
                        <td class="TxtTabla">&nbsp; &nbsp;  &nbsp;
                            <?php echo $datos_ant["nombre"]; ?>
                        </td>
                    </tr>
<?php
				}
				else //si ban=0, es por que se van a imprimir los registros, previos al registro a elminar, y se mostraran normalmente
				{
?>
                    <tr>
                        <td class="TxtTabla"> &nbsp; &nbsp;  &nbsp;
                            <?php echo $datos_ant["macroactividad"]; ?>
                        </td>
                        <td class="TxtTabla">&nbsp; &nbsp;  &nbsp;
                            <?php echo $datos_ant["nombre"]; ?>
                        </td>
                    </tr>
<?php
				}

			}
?>
                    <tr>
                        <td class="TxtTabla" colspan="2">&nbsp;                          
                        </td>

                    </tr>
			</table>		</td>
        </tr>
      <tr class="TxtTabla">
        <td width="5%" align="center" colspan="2">&nbsp;</td>
        </tr>
    </table>

<?php 
	}
	else
	{
?>
		
<?php
	}
?>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
		<tr>
			
          <td  align="center" class="TxtTabla"><strong>¿Esta seguro de eliminar el registro y realizar los cambios sugeridos en la EDT<?php //echo $nom_act; ?>?</strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="2">
  		    <input name="acti_macro" type="hidden" id="acti_macro" value="<?php echo $acti_macro; ?>">

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
