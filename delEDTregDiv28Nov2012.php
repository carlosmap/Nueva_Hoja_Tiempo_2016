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

	//consultamos la divisiones del lote de trabajo
	$sql_div="select macroactividad,nombre,id_actividad from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualLT;
	$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
	$cont_acti=substr($div_macro,strrpos($div_macro, ".")+1,strlen($div_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2, que se eliminara para uzarlo en actualizacion

//echo "1. ".$sql_div."<br>".$cont_acti."  -  ".$div_macro."<br>";
	
	$cur_div=mssql_query($sql_div);
//echo $sql_div."<br><br>".mssql_get_last_message();
	$band=0; //con $band identificamos, si la secuencia del while, ha llegado al registro que se va a eliminar, para comenzar actualizar las divisiones y actividades asociadas al LT							   

	//recorremos la divisiones del LC
	while($datos_div=mssql_fetch_array($cur_div))
	{
		 $cont_act_div=1; //la utilizamos, para actialuzar las actividades del las divisiones, que estan por debajo de la division eliminada
//echo "entro".$datos_div["id_actividad"]."<br>";
		if($band==1)  //comienza a imprimir, como quedaran los registros, despues de la eliminacion
		{
			//extrhemos parte de la macro actividad, y le añadimos el ultimo numero de actividad que se va a eliminar, para componer la macro actividad
			$macro=substr($datos_div["macroactividad"],0,strrpos($datos_div["macroactividad"], ".")+1).$cont_acti; 
			$cont_acti++;
			//actualizamos la macro actividad de la  division
			$sql_up_act="update  Actividades set macroactividad='".$macro."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div["id_actividad"]." and dependeDe=".$cualLT." and nivel=3";
//echo "2. ".$sql_up_act." - ".mssql_get_last_message()."<br>"; 
			$cursor_up_ac=mssql_query($sql_up_act);
//echo $sql_up_act."<br><br>".mssql_get_last_message();
			if  (trim($cursor_up_ac) == "")  
			{
				$error="si";
			}
			// si no se presento ningun inconveniente, a momento de actualizar la division, uactializamos las actividades correspondientes 	
			else
			{
				//consultamos las actividades, que pertenecen a la division
				$sql_cons_activ="select * from Actividades where id_proyecto=".$cualProyecto."and nivel=4 and dependeDe=".$datos_div["id_actividad"];	
				$sql_cons_activ=$sql_cons_activ." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
				$cur_cons_activi=mssql_query($sql_cons_activ);

				while($datos_cons_activi=mssql_fetch_array($cur_cons_activi))
				{
					//actualizamos las actividades de la division 
					$sql_up_act="update  Actividades set macroactividad='".$macro.".A.".$cont_act_div."' where id_proyecto=".$cualProyecto." and id_actividad=".$datos_cons_activi["id_actividad"]." and dependeDe=".$datos_div["id_actividad"]." and nivel=4";
					$cur_up_act=mssql_query($sql_up_act);
//echo $sql_up_act."<br><br>".mssql_get_last_message();
					if  (trim($cur_up_act) == "")  
					{
						$error="si";
						break;
					}
					$cont_act_div++;
				}
			}

		}	
		if($cualDIvision==$datos_div["id_actividad"])  //si el registro que se esta extrayendo de la BD es el que se va a eliminar
		{
					$band=1;
					//eliminamos la actividades asociadas a la division

					$sql_ele_act="delete from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDIvision."  and  nivel=4";
					$cur_ele_act=mssql_query($sql_ele_act);	
//echo $sql_ele_act."<br><br>".mssql_get_last_message();
					if  (trim($cur_ele_act) == "")  
					{
						$error="si";
					}	

					//eliminamos la division
					$sql_ele_div="delete from Actividades where id_proyecto=".$cualProyecto." and  id_actividad=".$cualDIvision." and dependeDe =".$cualLT." and nivel=3";
					$cur_ele_div=mssql_query($sql_ele_div);	
//echo $sql_ele_div."<br><br>".mssql_get_last_message();
					if  (trim($cur_ele_div) == "")  
					{
						$error="si";
					}	

		}
	}

	if  (trim($error)=="no")  {
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operación realizada satisfactoriamente.');</script>"); 
	} 
	else {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la grabación');</script>");
	}

	echo ("<script>window.close();MM_openBrWindow('htProgProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
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
<title>Programaci&oacute;n de Proyectos</title>
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
	$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDIvision;
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
				$Lt_identi=$datos_Lt["macroactividad"];
				$Lt_nom=$datos_Lt["nombre"];

			}		
?>
		  </td>
		</tr>
<?php
	$sql_Ld="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDIvision;
//echo $sql_Ld;
	$cur_Ld=mssql_query($sql_Ld);	
?>
		<tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n a eliminar</td>
          <td class="TxtTabla">
<?php
			if($datos_Ld=mssql_fetch_array($cur_Ld))
			{
				echo $datos_Ld["macroactividad"]." - ".strtoupper($datos_Ld["nombre"]);
				$div_nom=$datos_Ld["nombre"];//almacenamos el nombre de la division, para utilizarlo en la vista previs
				$div_identi=$datos_Ld["id_actividad"];//almacenamos el id de la  de la division, para utilizarlo en la vista previs
				$div_macro=$datos_Ld["macroactividad"]; //almcenamos la macro actividad de la division, con el fin de utilizarlo, al momento de mostrar, como quedaran la informacion, depues de la eliminacion
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
	$sql_ant="select macroactividad,nombre,id_actividad,dependeDe from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDIvision;
	$sql_ant=$sql_ant." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";

	$cur_ant=mssql_query($sql_ant); //datos antes
	$cur_des=mssql_query($sql_ant); //datos despues
//	$num_filas=mssql_num_rows($cur_des);
//echo $sql_ant;	
	//consultamos si existen mas divisiones, por debajo de la que se eliminara, con el fin de validar si se mostrar la seccion de la vista previa
	$sql_reg_desp="select COUNT(*)as cant from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualLT." 
	 and cast(reverse(substring(reverse('".$div_macro."'),1,charindex('.', reverse('".$div_macro."'))-1)) as int)< cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cursor_reg_desp=mssql_query($sql_reg_desp);
	$datos_reg_desp=mssql_fetch_array($cursor_reg_desp);

////****************************************** si la cnatidad de registros que exiten en el Lote division, es mayor a 1, mostrar la tabla con los datos que se veran afectados
	if(1<=$datos_reg_desp["cant"])
	{
?>
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
                <td colspan="3"><p>Nombre</p></td>
                </tr>
					<td class="TxtTabla"><?php echo $Lt_identi; ?></td>
					<td class="TxtTabla"  colspan="3"><?php echo $Lt_nom ; ?></td>
				<tr>

<?php
		//consultamos todos las divisiones que pertenecen a el lote de trabajo, para mostrarlas en la tabla de la seccion 'Estructura Actual'
		$sql_div_lt="select  nombre,macroactividad,id_actividad from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and dependeDe=".$cualLT;
		$sql_div_lt=$sql_div_lt." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
//echo $sql_div_lt;
		$cur_div_lt=mssql_query($sql_div_lt);
		$cur_div_lt2=mssql_query($sql_div_lt);
		$cant_reg_elimi=1; //permite saber, cuantas actividades se eliminarian, y asi dibujar las filas correspondientes, para ajustar el tamaño de la tabla  NO2 de la vista previa
		while($datos_div_lt=mssql_fetch_array($cur_div_lt))
		{
?>

					<td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["macroactividad"]; ?></td>
					<td class="TxtTabla"  colspan="2">&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt["nombre"] ; ?>  </td>
					<td class="TxtTabla">
<?php
					//mostramos el icono de color rojo, que identifica la division a eliminar
					if(substr($div_macro,strrpos($div_macro, ".")+1,strlen($div_macro))== substr($datos_div_lt["macroactividad"],strrpos($datos_div_lt["macroactividad"], ".")+1,strlen($datos_div_lt["macroactividad"])))
					{
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaRojo.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}

					//mostramos las actividades que estan por debajo, con el icono de color rojo, con el fin de dar a conocer las actividades que se actualizaran
					if(substr($div_macro,strrpos($div_macro, ".")+1,strlen($div_macro))< substr($datos_div_lt["macroactividad"],strrpos($datos_div_lt["macroactividad"], ".")+1,strlen($datos_div_lt["macroactividad"])))
					{
?>
						<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaAzul.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
<?php
					}
?>
					</td>
				</tr>
<?php
			//validamos si la division, es la que esta acioada a la que se va a eliminar, con el fin de motrar sus actividades
			if( $div_identi==$datos_div_lt["id_actividad"])
			{
				while($datos_des=mssql_fetch_array($cur_des))
				{
	?>
					<tr>
						<td class="TxtTabla">&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
							<?php echo $datos_des["macroactividad"]; ?>
						</td>
						<td class="TxtTabla">&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
							<?php echo $datos_des["nombre"]; ?>
						</td>
						<td class="TxtTabla">
	<?php
						///solo mostramos el icono, en la actividad a eliminar y en las actividades que son las que estan por debajo de ella 
 
						if($div_identi<=$datos_des["dependeDe"])
						{
							$cant_reg_elimi++;
	?>
							<img src="http://www.ingetec.com.co/NuevaHojaTiempo/img/images/alertaRojo.gif" alt="Actividad afectada por la eliminación" width="12" height="12">
	<?php
						}
	?>
						</td>		
						<td class="TxtTabla">
						</td>			
					</tr>
	<?php
				}
			}
		}
?>
			</table>	

		</td>
        <td align="center" >
			<table width="100%" height="100%"    border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="35%" >Identificador</td>
                <td ><p>Nombre</p></td>
                </tr>
				<tr>
					<td class="TxtTabla"><?php echo $Lt_identi; ?></td>
					<td class="TxtTabla"  colspan="2"><?php echo $Lt_nom ; ?></td>
				</tr>
				<tr>
<?php
		while($datos_div_lt2=mssql_fetch_array($cur_div_lt2))
		{
			//validamos si el id del  division, que se esta imprimiendo, es = o mayor a la que se eliminara, con el fin de mostrar los cambios, tras la eliminacion
		

			if(substr($div_macro,strrpos($div_macro, ".")+1,strlen($div_macro))<= substr($datos_div_lt2["macroactividad"],strrpos($datos_div_lt2["macroactividad"], ".")+1,strlen($datos_div_lt2["macroactividad"])))
			{

				if(substr($div_macro,strrpos($div_macro, ".")+1,strlen($div_macro))== substr($datos_div_lt2["macroactividad"],strrpos($datos_div_lt2["macroactividad"], ".")+1,strlen($datos_div_lt2["macroactividad"])))
				{
					$cont_acti=substr($div_macro,strrpos($div_macro, ".")+1,strlen($div_macro)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la division LT2.2.2 que se va a eliminar					
				}
				//si el id_actividad no es igual, es por que se este imprimiendo los id_activiades que estan por debajo de la que se eliminara
				else
				{
					$macro=substr($datos_div_lt2["macroactividad"],0,strrpos($datos_div_lt2["macroactividad"], ".")+1).$cont_acti; 
					$cont_acti++;
?>
                <tr>
                        <td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $macro; ?>
                        </td>
                        <td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt2["nombre"]; ?>
                        </td>
                </tr>
<?php
				}
			}
			//si no se mostraran normalmente, los que estan antes de el
			else
			{
?>
                <tr>
					<td class="TxtTabla">&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt2["macroactividad"]; ?></td>
					<td class="TxtTabla"  colspan="2">&nbsp; &nbsp; &nbsp;<?php echo $datos_div_lt2["nombre"] ; ?>  </td>

				</tr>
<?php
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

		</td>
        </tr>
      <tr class="TxtTabla">
        <td align="center" colspan="2">&nbsp;</td>
        </tr>
    </table>

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
  		    <input name="div_macro" type="hidden" id="div_macro" value="<?php echo $div_macro; ?>">

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

