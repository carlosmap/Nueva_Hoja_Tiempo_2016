<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

var nav4 = window.Event ? true : false;
function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57));
}

//cambia el valor de el nombre al hacer click sobre el campo de texto
function Nombre(valor)
{
	if(document.getElementById("nombre").value==valor)
	{
		document.getElementById("nombre").value="";
	}
}
function Valor(valor)
{
	if(document.getElementById("valor").value==valor)
	{
		document.getElementById("valor").value="";
	}
}


//-->
</script>
<?php
session_start();
//include("../../Users/CARLOS~1/AppData/Local/Temp/scp23751/var/www/html/verificaRegistro2.php");
//include('../../Users/CARLOS~1/AppData/Local/Temp/scp23751/var/www/html/conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";




if(trim($recarga) == "2")
{
echo $operacion;

		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
		//si la operacion seleccionada es de intercambio
		if($operacion==1)
		{	

			$error="no";

			//consultamos la actividad origen, para el intercambio
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and actPrincipal=".$cualLC." and dependeDe=".$div_id." and tipoActividad=4";
			$cur_inter1=mssql_query($sql_inter1);
			if  (trim($cur_inter1) == "")  
			{
				$error="si";
			}	
//echo $sql_inter1."<br>";
			while($datos_inter1=mssql_fetch_array($cur_inter1))
			{
				//almacenmos la informacion de la actividad de origen, para ulizarla, al momento de actualizar la actividad de destino, que es donde quedara asociada la actividad
				$macro_act_1=$datos_inter1["macroactividad"];
				$depende_act_1=$datos_inter1["dependeDe"];
				$division_act_1=$datos_inter1["id_division"];
				$niveles_act_1=$datos_inter1["nivelesActiv"];
//				$id_act_1=$datos_inter1[""]:
				$nombre_act_1=$datos_inter1["nombre"];
				$act_prin_act_1=$datos_inter1["actPrincipal"];

//=$datos_inter1[""]:
			}
			//consultamos la actividad destino, para el intercambio
			$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$AC." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4";
			$cur_inter2=mssql_query($sql_inter2);
			if  (trim($cur_inter2) == "")  
			{
				$error="si";
			}	
//echo $sql_inter2."<br>";
			while($datos_inter2=mssql_fetch_array($cur_inter2))
			{
				//almacenmos la informacion de la actividad de origen, para ulizarla, al momento de actualizar la actividad de origen, que es donde quedara asociada la actividad
				$macro_act_2=$datos_inter2["macroactividad"];
				$depende_act_2=$datos_inter2["dependeDe"];
				$division_act_2=$datos_inter2["id_division"];
				$niveles_act_2=$datos_inter2["nivelesActiv"];
//				$id_act_1=$datos_inter1[""]:
				$nombre_act_2=$datos_inter2["nombre"];
				$act_prin_act_2=$datos_inter2["actPrincipal"];

//=$datos_inter1[""]:
			}
//echo "<br>".$macro_act_1." - ".$depende_act_1." - ".$division_act_1." - ".$niveles_act_1."".$act_prin_act_1."<br>"; echo "<br>".$macro_act_2." - ".$depende_act_2." - ".$division_act_2." - ".$niveles_act_2."".$act_prin_act_2."<br>";

			//actualizamos las actividades de origen y destino, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
			$sql_up_act="update Actividades set macroactividad='".$macro_act_2."', dependeDe=".$depende_act_2.", id_division=".$division_act_2.", nivelesActiv='".$niveles_act_2."',actPrincipal=".$act_prin_act_2;
			//si las divisiones son diferentes, el encargado se actualiza como null
			if($division_act_2!=$division_act_1)
				$sql_up_act=$sql_up_act.", id_encargado=NULL";

			$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and tipoActividad=4";

			$cur_up_act=mssql_query($sql_up_act);

			if  (trim($cur_up_act) == "")  
			{
				$error="si";
			}	

//echo $sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";			
			
             $sql_up_act2="update Actividades set macroactividad='".$macro_act_1."', dependeDe=".$depende_act_1.", id_division=".$division_act_1." ,nivelesActiv='".$niveles_act_1."',actPrincipal=".$act_prin_act_1; 
			//si las divisiones son diferentes, el encargado se actualiza como null
			if($division_act_2!=$division_act_1)
				$sql_up_act2=$sql_up_act2.", id_encargado=NULL";

			$sql_up_act2=$sql_up_act2." where id_proyecto=".$cualProyecto." and id_actividad=".$AC."  and tipoActividad=4";

//and actPrincipal=".$cualLC." and dependeDe=".$DI."

			$cur_up_act2=mssql_query($sql_up_act2);

			if  (trim($cur_up_act2) == "")  
			{
				$error="si";
			}	

//echo $sql_up_act2."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		}
			
		//si la operacion seleccionada es mover
		if($operacion==2)
		{
			$error="no";

					//consultamos la actividad, de destion, para obtener la macro actividad, que se utiliza, para listar las actividades que estan por debajo
					$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$AC." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4";
					$cur_inter2=mssql_query($sql_inter2);
					if  (trim($cur_inter2) == "")  
					{
						$error="si";
					}	
		//echo $sql_inter2."<br>";
					while($datos_inter2=mssql_fetch_array($cur_inter2))
					{
						//almacenmos la informacion de la actividad de destino
						$macro_act_2=$datos_inter2["macroactividad"];
					}

			//consultamos la actividad seleccionada en la pagina de la EDT, para moverla
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and actPrincipal=".$cualLC." and dependeDe=".$div_id." and tipoActividad=4";
			$cur_inter1=mssql_query($sql_inter1);
			if  (trim($cur_inter1) == "")  
			{
				$error="si";
			}	
echo "<br> 0".$sql_inter1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
			while($datos_inter1=mssql_fetch_array($cur_inter1))
			{
				//almacenmos la informacion de la actividad a mover, para ulizarla, al momento de actualizar las actividades que estan por debajo de la actividad  de destino
				$macro_act_1=$datos_inter1["macroactividad"];
				$depende_act_1=$datos_inter1["dependeDe"];
				$division_act_1=$datos_inter1["id_division"];
				$niveles_act_1=$datos_inter1["nivelesActiv"];
//				$id_act_1=$datos_inter1[""]:
				$nombre_act_1=$datos_inter1["nombre"];
				$act_prin_act_1=$datos_inter1["actPrincipal"];
				$encargado_act_1=$datos_inter1["id_encargado"];

//=$datos_inter1[""]:
			}

//			$band=0; //permite identificar si se esta recorriendo la actividad donde se va a traspasar la actividad, y asi modificar las actividades que estan por debajo
			//consultamos las actividades de la division, donde se va a mover la actividad
//			$sql_activi="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4 and ".$AC." < id_actividad order by macroactividad";
			$sql_activi="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4 and '".$macro_act_2."'  < macroactividad order by macroactividad";

			$cur_activi=mssql_query($sql_activi);
echo "<br>".$macro_act_1." - ".$depende_act_1." - ".$division_act_1." - ".$niveles_act_1." - ".$act_prin_act_1." - ".$nombre_act_1."<br>";

echo "1 ". $sql_activi."  --  ".mssql_get_last_message()." ".$error."  --  ".$cur_activi."<br><br>";
			if  (trim($cur_activi) == "")  
			{
				$error="si";
			}	
			$cant_reg=mssql_num_rows($cur_activi);
			$reg=0;

			while($datos_activi=mssql_fetch_array($cur_activi))
			{
				$reg++;
				//si ban=1, es por que se esta recorriendo las actividades que estan por debajo de la actividad seleccionada con destino para el traspaso
//				if($band==1)
				{
					//actualizamod las actividades por debajo, primero con la informacion de la actividad a traspasar, y luego con la info de las actividades anteriores, con el fin de actualizarlas
					$sql_up_act3="update Actividades set nombre='".$nombre_act_1."'"; 
					if(($datos_activi["id_division"]!=$division_act_1)or(trim($encargado_act_1)==""))
						$sql_up_act3=$sql_up_act3.", id_encargado=NULL";
					else
						$sql_up_act3=$sql_up_act3.", id_encargado=".$encargado_act_1;

					$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi["id_actividad"]." and dependeDe=".$DI." and actPrincipal=".$cualLC." and tipoActividad=4";
					$cur_up_act3=mssql_query($sql_up_act3);

					if  (trim($cur_up_act3) == "")  
					{
						$error="si";
					}	

echo "2 ". $sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";

	//				$id_act_1=$datos_inter1[""]:
					$nombre_act_1=$datos_activi["nombre"];
					$encargado_act_1=$datos_activi["id_encargado"];
					$division_act_1=$datos_activi["id_division"];
				}

				//verificamos si el id de la actividad, que estamos consultando, corresponde a la actividad donde se ubicara la actividad a mover
/*				if($id_act_1==$datos_activi["id_actividad"])
				{
					$band=1;
				}
*/
				//validamos si es el ultimo registro, con el fin de almacenar los datos de este, y asi utilizarlo al momento de insertar el registro
				if($reg==$cant_reg)
				{
					$macro_act_4=$datos_activi["macroactividad"];
					$depende_act_4=$datos_activi["dependeDe"];
					$division_act_4=$datos_activi["id_division"];
					$niveles_act_4=$datos_activi["nivelesActiv"];
	//				$id_act_1=$datos_inter1[""]:
					$nombre_act_4=$datos_activi["nombre"];
					$act_prin_act_4=$datos_activi["actPrincipal"];
					$encargado_act_4=$datos_activi["id_encargado"];
				}
			}
echo $cant_reg."  -- reg:  ".$reg."<br>";

			//Consultamos el id de la actividad de mayor en los registros del proyecto
			$sigienteSec =0;
			$sqlId = " select MAX(id_actividad) as elMax from Actividades where id_proyecto=".$cualProyecto; //. $_SESSION["sesProyLaboratorio"] ;
			$cursorId = mssql_query($sqlId);
			if  (trim($cursorId) == "")  
			{
				$error="si";
			}	
echo "3 ". $sqlId."  --  ".mssql_get_last_message()." ".$error."<br><br>";
			if($regId = mssql_fetch_array($cursorId))
			{
				$sigienteSec = $regId["elMax"] + 1;

echo $macro_act_4." -- ";
				$cont_acti=substr($macro_act_4,strrpos($macro_act_4, ".")+1,strlen($macro_act_4)); //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2.A.(2) para uzarlo en la vista previa
				$cont_acti++;
				$macro_act_4=substr($macro_act_4,0,strrpos($macro_act_4, ".")+1).$cont_acti; //extrhemos parte de la macro actividad, y le añadimos el ultimo numero de actividad que se va a eliminar, para componer la macro actividad

echo $macro_act_4."  *  ".$cont_acti."<br>";
				//insertamos el ultimo lote de control de la division, ya que este se remplazo, al momento de traspasar la actividad
				$sqlIn1 = " INSERT INTO Actividades";
				$sqlIn1 = $sqlIn1 . "( id_proyecto, id_actividad, fecha_inicio, fecha_fin, nombre, macroactividad,id_encargado,dependeDe, actPrincipal, tipoActividad, nivelesActiv, nivel,id_division ) ";

				$sqlIn1 = $sqlIn1 . " VALUES ( ";
				$sqlIn1 = $sqlIn1 . " ".$cualProyecto.", ";
				$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";
		
				$sqlIn1 = $sqlIn1 . " getdate(), ";
				$sqlIn1 = $sqlIn1 . " getdate(), ";
		
				$sqlIn1 = $sqlIn1 . "  UPPER('" . $nombre_act_4 . "'), ";
				$sqlIn1 = $sqlIn1 . " '" . $macro_act_4 . "', ";

				if(trim($encargado_act_4)=="")
					$encargado_act_4='NULL';
				$sqlIn1 = $sqlIn1 . " " . $encargado_act_4 . ", ";
				$sqlIn1 = $sqlIn1 . $depende_act_4.", ";
				$sqlIn1 = $sqlIn1 . " " . $act_prin_act_4 . ", ";
				$sqlIn1 = $sqlIn1 . " '4', ";
		
				$sqlIn1 = $sqlIn1 . " '" . $LC. "-".$LT."-".$DI."-A-".$sigienteSec." ', ";
				$sqlIn1 = $sqlIn1 . " '4' ";
				$sqlIn1 = $sqlIn1 . " ,".$division_act_4.") ";
		//		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
		//		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";
		
				$cursorIn1 = mssql_query($sqlIn1);
				if  (trim($cursorIn1) == "")  
				{
					$error="si";
				}
 echo "4 ". $sqlIn1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				$sql_del_act="delete from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and actPrincipal=".$cualLC." and dependeDe=".$div_id." and tipoActividad=4";
				$cur_del_act=mssql_query($sql_del_act);
				if  (trim($cur_del_act) == "")  
				{
					$error="si";
				}
 echo "5 ". $sql_del_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";
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

//	echo ("<script>window.close();MM_openBrWindow('htProgProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>

<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Intercambiar/Mover Actividad</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../../Users/CARLOS~1/AppData/Local/Temp/scp23751/var/www/html/images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
<?php
	$sql_Lc="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=1 and id_actividad=".$cualLC;
	$cur_Lc=mssql_query($sql_Lc);		
?>
        <tr>
          <td width="42%" class="TituloTabla">Lote de control Actual</td>
          <td width="58%" class="TxtTabla">
<?php
			if($datos_Lc=mssql_fetch_array($cur_Lc))
			{
				echo $datos_Lc["nombre"];
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
          <td class="TituloTabla">Lote de trabajo Actual</td>
          <td class="TxtTabla">
<?php
			if($datos_Lt=mssql_fetch_array($cur_Lt))
			{
				echo $datos_Lt["nombre"];
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
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n Actual</td>
          <td class="TxtTabla">
<?php
			if($datos_Ld=mssql_fetch_array($cur_Ld))
			{
				echo strtoupper($datos_Ld["nombre"]);
				$div_id=$datos_Ld["id_actividad"];//almacenamos el nombre de la division, para utilizarlo en la vista previs
				
			}		
?>
		  </td>
        </tr>
<?php

	$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=4 and id_actividad=".$cualACtividad;
	$cur_a=mssql_query($sql_a);	
?>
		<tr>
          <td class="TituloTabla">Actividad</td>
          <td class="TxtTabla">
<?php
			if($datos_a=mssql_fetch_array($cur_a))
			{
				echo $datos_a["macroactividad"]." - ".$datos_a["nombre"];
				$nom_act=$datos_a["nombre"];
				$acti_macro= $datos_a["macroactividad"]; //almacenamos la macroactividad, para utilizarla en la informacion de la actividad
				$acti_encargado= $datos_a["id_encargado"]; 
			}		
?>
		  </td>
        </tr>
		<tr>

          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>

		<tr>
			<td class="TituloTabla">Identificador de la Actividad</td>
			<td class="TxtTabla"><input name="identificador" type="text" class="CajaTexto" id="identificador" value="<?php echo $acti_macro; ?>" readonly></td>
		</tr>
<tr>
          <td class="TituloTabla">Tipo de operaci&oacute;n</td>
<?php
					//si es la primera vez que se carga la pagina, definimos operancion=1, ya que este valor corresponde a intercambio, que esta marcado por defecto, en el radio button
					if(!isset($operacion))
					{
							$operacion=1;
					}
?>
          <td class="TxtTabla"><input name="operacion" type="radio" id="operacion" value="1" <?php if(($operacion==1)){ echo "checked";  } ?>   onChange="document.Form1.submit();">
            <label for="operacion">Intercambio</label>
            <input type="radio" name="operacion" id="operacion" value="2"  onChange="document.Form1.submit();"  <?php if($operacion==2){ echo "checked"; } ?>>
            <label for="operacion2">Mover</label></td>
		</tr>

   <tr>
          <td class="TituloTabla">Lote de control </td>

<?php
//cualProyecto=683&cualLC=1&cualDiv=4&cualACtividad=5

					//si la variable no esta definida, es por que es la primera vez que se carga la pagina, y asi, el selecct quedara por defecto, con el LC al cual pertenece el LT seleccionado por el usuario, en la
					//ventana anterior, la cual trahe como parametro el valor del LC, perteneciente al LT
					if(!isset($cualLC2))
					{
							$cualLC2=$cualLC;
					}



					//Cargamos el valor del LT, que se trahe como parametro, esta sentencia es verdadera, cuando se accede a la pagina por primera vez
					if(!isset($cualLT2))
					{
							$cualLT2=$cualLT;
					}
					//validamos si la variable que se forma del  select, esta definida, si no lo esta, es por que es la primera vez que se accede a la pagina
					//entonces  cargamos la variable, enviada como parametro, para cargar el select, del los lotes de trabajo 
					if(!isset($LC))
					{
							$LC=$cualLC;
					}
					if(!isset($LT))
					{
						//consultamos la division del lote de trabajo, para trher el depende y asi consultar el lote de trabajo asociado
							$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDiv;
							$cur_a=mssql_query($sql_a);	
							if($datos_lts=mssql_fetch_array($cur_a))	
								$dep_div=$datos_lts["dependeDe"];
						
							$sql_Lt="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=".$dep_div;
							$cur_Lt=mssql_query($sql_Lt);	
							if($datos_Lt=mssql_fetch_array($cur_Lt))
							{
								$cualLT=$datos_Lt["id_actividad"];
							}	

							$LT=$cualLT;
					}
					if(!isset($DI))
					{
							$DI=$cualDiv;
					}
//echo $LT." - ".$LC." - ".$cualLC2." - ".$cualLT2;
?>

          <td class="TxtTabla">
<?php		
		//si la operacion es intercambio, se  muestra solo el nombre del lote de control asociado a la actividad
		if($operacion==1)
		{
			$sql_LC1="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1 and id_actividad=".$cualLC;
			$cursor_sql_LC1=mssql_query($sql_LC1);
			while($datos_sql_LC1=mssql_fetch_array($cursor_sql_LC1))
			{
				echo $datos_sql_LC1["macroactividad"]." - ".$datos_sql_LC1["nombre"];
			}
		}

		//si la operacion, es  mover, se muestra el select con los lotes de control
		if($operacion==2)
		{
		

 ?>
		<select name="LC" id="LC"   class="CajaTexto"  onChange="document.Form1.submit();">
            <?php
					//consultamos los lotes de control asociados a la EDT del proyecto
					$sql_LC="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1";
					$cursor_sql_LC=mssql_query($sql_LC);
					while($datos_sql_LC=mssql_fetch_array($cursor_sql_LC))
					{
						//pertmite determinar el LC del LT, seleccionado  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLC2==$datos_sql_LC["id_actividad"])
						{
							$cualLC2=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select="selected";
							$LC_selec=$datos_sql_LC["macroactividad"];
						}
						else
						{
							if($LC==$datos_sql_LC["id_actividad"])
							{
								$select="selected";
								$LC_selec=$datos_sql_LC["macroactividad"];
							}
						}
						echo "<option value=".$datos_sql_LC["id_actividad"]." $select >".$datos_sql_LC["macroactividad"]." - ".$datos_sql_LC["nombre"]."</option>";
						$select="";
					}
 ?>
          </select>
<?php
		}
?>
	   	  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla">
		<select name="LT" id="LT"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value=""> </option>
            <?php
					//consultamos los lotes de control asociados a el lote de control seleccionado
					$sql_LT="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto =".$cualProyecto." and dependeDe=".$LC." and nivel = 2";
					$cursor_sql_LT=mssql_query($sql_LT);
					while($datos_sql_LT=mssql_fetch_array($cursor_sql_LT))
					{

						//pertmite determinar el LT seleccionado por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLT2==$datos_sql_LT["id_actividad"])
						{
							$cualLT2=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select2="selected";
							$LT_selec=$datos_sql_LT["macroactividad"];
						}
						else
						{
							if($LT==$datos_sql_LT["id_actividad"])
							{
								$select2="selected";
								$LT_selec=$datos_sql_LT["macroactividad"];
							}
						}
						echo "<option value=".$datos_sql_LT["id_actividad"]." $select2>".$datos_sql_LT["macroactividad"]." - ".$datos_sql_LT["nombre"]."</option>";
						$select2="";
					}
 ?>
          </select>
		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n</td>
          <td class="TxtTabla">
		<select name="DI" id="DI"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value=""> </option>
            <?php
					$divi_selec=""; //almacenamos la division (Hoja de tiempo) correspondiente a la Division (EDT) asociada al lote de trabajo, para almacenarla en el campo id_division, y asi referenciar las actividades por division
					//consultamos las divisiones  asociados al lote de trabajo
					$sql_DI="SELECT  id_actividad,upper(nombre) as nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$LT." and actPrincipal=".$LC." and nivel = 3";
					$cursor_sql_DI=mssql_query($sql_DI);
					while($datos_sql_DI=mssql_fetch_array($cursor_sql_DI))
					{
						//pertmite determinar la division, seleccionada  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLDI==$datos_sql_DI["id_actividad"])
						{
							$cualLDI=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select="selected";
							$DI_selec=$datos_sql_DI["macroactividad"];

							$divi_selec=$datos_sql_DI["id_division"]; //almacenamos el nombre de la division, para utilizarlo en el momento de traher lo responsalbes, ya que solo se mostraran los poertenecientes a la division seleccionada
						}
						else
						{
							if($DI==$datos_sql_DI["id_actividad"])
							{
								$select="selected";
								$DI_selec=$datos_sql_DI["macroactividad"];
								$divi_selec=$datos_sql_DI["id_division"];//almacenamos el id de la division, para utilizarlo en el momento de traher lo responsalbes, ya que solo se mostraran los poertenecientes a la division seleccionada
							}
						}
						echo "<option value=".$datos_sql_DI["id_actividad"]." $select >".$datos_sql_DI["macroactividad"]." - ".$datos_sql_DI["nombre"]."</option>";
						$select="";
					}
 ?>
          </select>
		  </td>
        </tr>


<?php 
		//si la operacion es intercambio, se muestra el select con los nombres de las actividades, asociadas, a las divison seleccionada, que es con la que se va a intercambiar la actividad
//		if($operacion==1)
		{
?>
            <tr>
              <td class="TituloTabla">Actividad</td>
              <td class="TxtTabla">
            <select name="AC" id="AC"   class="CajaTexto" >
                <option value=""> </option>
                <?php
                        $AC_selec=""; 
						//consultamos las actividades asociadas a la division seleccionada
                        $sql_AC="SELECT  id_actividad,nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$DI." and actPrincipal=".$LC." and nivel = 4 order by macroactividad";
                        $cursor_sql_AC=mssql_query($sql_AC);
                        while($datos_sql_AC=mssql_fetch_array($cursor_sql_AC))
                        {
                            //pertmite determinar la actividad, seleccionada  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
                            //y despues, se seleccionara el que el usuario escoga en el select
                            if($cualACtividad==$datos_sql_AC["id_actividad"])
                            {
	                         	$select="selected";
                            }

                            echo "<option value=".$datos_sql_AC["id_actividad"]." $select >".$datos_sql_AC["macroactividad"]." - ".$datos_sql_AC["nombre"]."</option>";
                            $select="";
                        }
     ?>
              </select>
              </td>
            </tr>
<?php
		}
?>
       
			        
      </table>

      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TxtTabla">
        <td align="center" >&nbsp;</td>
        </tr>
		<tr>
			
          <td  align="center" class="TxtTabla"><strong>&iquest;Esta seguro de actualizar el registro?</strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
<!--  		    <input name="operacion" type="hidden" id="operacion" value="<?php //echo $operacion; ?>">
-->

  		    <input name="div_id" type="hidden" id="div_id" value="<?php echo $div_id; ?>">
  		    <input name="Submit" type="button" class="Boton" value="Cancelar"  onClick="window.close()" >
  		    <input name="Submit" type="button" class="Boton" value="Actualizar" onClick="valida()" >
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
<script type="text/javascript">
function valida()
{
//	alert(document.getElementById("operacion").value);


	if(document.Form1.operacion[0].checked)
	{
		//si la opercacion seleccionada es intercambio

			if(document.getElementById("LT").value=="")
			{
				alert ('Seleccione un lote de trabajo');
			}	
		
			else if(document.getElementById("DI").value=="")
			{
				alert ('Seleccione un a division');
			}
			
			else if(document.getElementById("AC").value=="")
			{
				alert ('Seleccione una actividad');
			}
			else
			{
				document.Form1.operacion.value="1";
				document.Form1.recarga.value="2";
				document.Form1.submit();
			}

	}

	if(document.Form1.operacion[1].checked)
	{	
		//ai la operacion seleccionada es mover

			if(document.getElementById("LC").value=="")
			{
				alert ('Seleccione un lote de control');
			}	
		
			if(document.getElementById("LT").value=="")
			{
				alert ('Seleccione un lote de trabajo');
			}	
		
			else if(document.getElementById("DI").value=="")
			{
				alert ('Seleccione un a division');
			}
			
/*			else if(document.getElementById("AC").value=="")
			{
				alert ('Seleccione una actividad');
			}
*/
			else
			{

				document.Form1.operacion.value="2";
				document.Form1.recarga.value="2";
				document.Form1.submit();
			}
		
	}

}
</script>
</body>
</html>

<? mssql_close ($conexion); ?>	
