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
//echo $operacion;

		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
		if($operacion==1)
		{	
			$error="no";
			//consultamos el lote de trabajo de  origen, para el intercambio
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualLC." and actPrincipal=".$cualLC." and tipoActividad=1";
			$cur_inter1=mssql_query($sql_inter1);
			if  (trim($cur_inter1) == "")  
			{
				$error="si";
			}	
//echo $sql_inter1."<br>";
			while($datos_inter1=mssql_fetch_array($cur_inter1))
			{
				//almacenmos la informacion de el lote de trabajo de origen, para ulizarla, al momento de actualizar el el lote de trabajo de destino, que es donde quedara asociada el lote de trabajo
				$macro_act_1=$datos_inter1["macroactividad"];
				$depende_act_1=$datos_inter1["dependeDe"];
				$division_act_1=$datos_inter1["id_division"];
				$niveles_act_1=$datos_inter1["nivelesActiv"];
			//	$id_act_1=$datos_inter1["id_actividad"]:
				$id_act_1=$datos_inter1["id_actividad"];
//				$nombre_act_1=$datos_inter1["nombre"];
				$act_prin_act_1=$datos_inter1["actPrincipal"];
/*
				$encargado_1=$datos_inter1["id_encargado"];
				$fecha_fin_1=$datos_inter1["fecha_fin"];
				$fecha_ini_1=$datos_inter1["fecha_inicio"];
*/
			}
			//consultamos el lote de trabajo destino, para el intercambio
			$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$LC." and actPrincipal=".$LC." and tipoActividad=1";
			$cur_inter2=mssql_query($sql_inter2);
			if  (trim($cur_inter2) == "")  
			{
				$error="si";
			}	
//echo "001 ".$sql_inter2."<br>";
			while($datos_inter2=mssql_fetch_array($cur_inter2))
			{
				//almacenmos la informacion de la actividad de destino, para ulizarla, al momento de actualizar la actividad de origen, que es donde quedara asociada la actividad
				$id_act_2=$datos_inter2["id_actividad"];
				$macro_act_2=$datos_inter2["macroactividad"];
				$depende_act_2=$datos_inter2["dependeDe"];
				$division_act_2=$datos_inter2["id_division"];
				$niveles_act_2=$datos_inter2["nivelesActiv"];
				$act_prin_act_2=$datos_inter2["actPrincipal"];
				$encargado_2=$datos_inter2["id_encargado"];
			}
//echo "<br>".$macro_act_1." - ".$depende_act_1." - ".$division_act_1." - ".$niveles_act_1."".$act_prin_act_1."<br>"; echo "<br>".$macro_act_2." - ".$depende_act_2." - ".$division_act_2." - ".$niveles_act_2."".$act_prin_act_2."<br>";

			//se extrahe el nivel de la division de destino hasta el id de la actividad  1-2
//			$nivel_div2=substr($niveles_act_2,0,strrpos($niveles_act_2, "-"));
			//y se le adiciona la actividad del lote de trabajo de origen
//			$nivel_div2=$nivel_div2."-".$cualLT;
//echo $nivel_div2;

			//actualizamos el lote de control de origen y destino, intercambiando la informacion correspondiente, y dejando intacto el id de el lote de control
			//actualizamos primero el lote de control de origen
			$sql_up_lc="update Actividades set macroactividad='".$macro_act_2."'";
//			 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."'";
			$sql_up_lc=$sql_up_lc." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLC." and actPrincipal=".$cualLC." and tipoActividad=1";
	
			$cur_up_lc=mssql_query($sql_up_lc);
//echo "2 ".$sql_up_lc."  --  ".mssql_get_last_message()." ".$error."<br><br>";
			if  (trim($cur_up_lc) == "")  
			{
				$error="si";
			}
			else
			{
				//consulta los lotes de trabajo
				$sql_lt="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLC." and nivel=2 ";
				$sql_lt=$sql_lt."  order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cur_lt=mssql_query($sql_lt);
//echo "2.1 ".$sql_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				if  (trim($cur_lt) == "")  
				{
					$error="si";
				}				
				//si no se presentan inconvenientes en la consulta, actualizamos los lostes de trabajo que pertenecen a el elote de control
				else
				{
					$cont_lt=1;
					while($datos_lt=mssql_fetch_array($cur_lt))
					{
						$macro_act_2="LT".substr($macro_act_2,2,strlen($macro_act_2));
						//creamos la macro-actividad de los lotes de trabajo asociados, al lote de control
						$macro_act_lt=$macro_act_2.".".$cont_lt;
						//actualizamos primero el lote de trabajo que pertenecen a el lote de control
						$sql_up_lt="update Actividades set macroactividad='".$macro_act_lt."'";
			//			 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."'";
						$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_lt["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=2";
				
						$cur_up_lt=mssql_query($sql_up_lt);
//			echo "2.2 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
						if  (trim($cur_up_lt) == "")  
						{
							$error="si";
						}
						//si no se presentan inconvenientes, se actualizan las  divisiones de el lote de trabajo
						else
						{
							//consultamos las  divisiones de el lote de trabajo
							$sql_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_lt["id_actividad"]." and nivel=3";
							$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
							$cursor_div=mssql_query($sql_div);
	//			echo "3 ".$sql_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cursor_div) == "")  
							{
								$error="si";
							}
							//si no se presentan inconvenientes, se actualizan las actividades de la division	
							else
							{
								$cont_div=1;
								while($datos_div=mssql_fetch_array($cursor_div))
								{
										//generamos la macro actividad de las divisiones
									$macro_act_3=$macro_act_lt.".".$cont_div;
									//actualizamos las divisiones de origen, con los datos de la division de destino
									$sql_up_div1="update Actividades set macroactividad='".$macro_act_3."'";
				//					 $sql_up_div1=$sql_up_div1. "nivelesActiv='".$nivel_div1."'";
									$sql_up_div1=$sql_up_div1." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div["id_actividad"]." and dependeDe=".$datos_lt["id_actividad"]."  and actPrincipal=".$cualLC." and tipoActividad=3";

									$cur_up_div1=mssql_query($sql_up_div1);
			//		echo "5 ".$sql_up_div1."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
									if  (trim($cur_up_div1) == "")  
									{
										$error="si";
									}
									else
									{
					//*********************** 1.  AQUI 1 pruebas_activida_dinamica.php
					
										//consultamos las actividades del la division
										$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_div["id_actividad"]." and nivel=4";
										$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
										$cursor_act=mssql_query($sql_act);
					//echo "3.0 ".$sql_act."<br><br>";
								//		$macro_act_4=$macro_act_3.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
			//							$niveles_act_3=$nivel_div2."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
										$num_act=1;
		//			echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
										while($datos_act=mssql_fetch_array($cursor_act))
										{
											//formamos la macro actividad y el nivel de las actividades a intercambiar
											$macro_act_4=$macro_act_3.".A.".$num_act;
			//								$niveles_act_31=$niveles_act_3.$datos_act["id_actividad"];
											//actualizamos las actividades - sub actividades, etc, de la division
											$sql_up_act3="update Actividades set macroactividad='".$macro_act_4."'";
			//								 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
											$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and dependeDe=".$datos_div["id_actividad"]." and tipoActividad=4";
					
											$cur_up_act3=mssql_query($sql_up_act3);
											if  (trim($cur_up_act3) == "")  
											{
												$error="si";
											}	
											$num_act++;
	//				echo "4 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
										}	
									}
									$cont_div++;
								}
							}
			
						}	
						$cont_lt++;
					}		
				}	
			}



			//se extrahe el nivel de la division de origen hasta el id de la actividad  1-2
//			$nivel_div1=substr($niveles_act_1,0,strrpos($niveles_act_1, "-"));
			//y se le adiciona la actividad del lote de trabajo de origen
//			$nivel_div1=$nivel_div1."-".$LT;
//echo $nivel_div2;


			//actualizamos el lote de control de destino
			$sql_up_lc="update Actividades set macroactividad='".$macro_act_1."'";
//			 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."'";
			$sql_up_lc=$sql_up_lc." where id_proyecto=".$cualProyecto." and id_actividad=".$LC." and actPrincipal=".$LC." and tipoActividad=1";
	
			$cur_up_lc=mssql_query($sql_up_lc);
//echo "2 ".$sql_up_lc."  --  ".mssql_get_last_message()." ".$error."<br><br>";
			if  (trim($cur_up_lc) == "")  
			{
				$error="si";
			}
			else
			{
				//consulta los lotes de trabajo
				$sql_lt="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$LC." and nivel=2 ";
				$sql_lt=$sql_lt."  order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cur_lt=mssql_query($sql_lt);
//echo "2 ".$sql_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				if  (trim($cur_lt) == "")  
				{
					$error="si";
				}	
				else
				{
					$cont_lt=1;
					while($datos_lt=mssql_fetch_array($cur_lt))
					{
						$macro_act_1="LT".substr($macro_act_1,2,strlen($macro_act_1));
						//creamos la macro-actividad de los lotes de trabajo asociados, al lote de control
						$macro_act_lt=$macro_act_1.".".$cont_lt;

						//actualizamos el lote de trabajo de  destino, intercambiando la informacion correspondiente, y dejando intacto el id de el lote de trabajo
						//actualizamos primero el lote de trabajo de destino
						$sql_up_lt="update Actividades set macroactividad='".$macro_act_lt."'";
			//			 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."'";
						$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_lt["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=2";
				
						$cur_up_lt=mssql_query($sql_up_lt);
//			echo "2.2 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
						if  (trim($cur_up_lt) == "")  
						{
							$error="si";
						}
						//si no se presentan inconvenientes, se actualizan las  divisiones de el lote de trabajo
						else
						{
							//consultamos las  divisiones de el lote de trabajo
							$sql_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$datos_lt["id_actividad"]." and nivel=3";
							$sql_div=$sql_div."  order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
							$cursor_div=mssql_query($sql_div);
//			echo "3.2 ".$sql_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cursor_div) == "")  
							{
								$error="si";
							}
							//si no se presentan inconvenientes, se actualizan las actividades de la division	
							else
							{
								$cont_div=1;
								while($datos_div=mssql_fetch_array($cursor_div))
								{
									//generamos la macro actividad de las divisiones
									$macro_act_3=$macro_act_lt.".".$cont_div;
								//actualizamos las divisiones de origen, con los datos de la division de destino
								$sql_up_div1="update Actividades set macroactividad='".$macro_act_3."'";
			//					 $sql_up_div1=$sql_up_div1. "nivelesActiv='".$nivel_div1."'";
								$sql_up_div1=$sql_up_div1." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=3";

								$cur_up_div1=mssql_query($sql_up_div1);
//				echo "5.2 ".$sql_up_div1."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
					//*********************** 1.  AQUI 1 pruebas_activida_dinamica.php
					
										//consultamos las actividades del la division
										$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$datos_div["id_actividad"]." and nivel=4";
										$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
										$cursor_act=mssql_query($sql_act);
					//echo "3.0 ".$sql_act."<br><br>";
								//		$macro_act_4=$macro_act_3.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
			//							$niveles_act_3=$nivel_div2."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
										$num_act=1;
//				echo "3.2 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
										while($datos_act=mssql_fetch_array($cursor_act))
										{
											//formamos la macro actividad y el nivel de las actividades a intercambiar
											$macro_act_4=$macro_act_3.".A.".$num_act;
			//								$niveles_act_31=$niveles_act_3.$datos_act["id_actividad"];
											//actualizamos las actividades - sub actividades, etc, de la division
											$sql_up_act3="update Actividades set macroactividad='".$macro_act_4."'";
			//								 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
											$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$LC." and dependeDe=".$datos_div["id_actividad"]." and tipoActividad=4";
					
											$cur_up_act3=mssql_query($sql_up_act3);
											if  (trim($cur_up_act3) == "")  
											{
												$error="si";
											}	
											$num_act++;
//					echo "4.2 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
										}	
									$cont_div++;
								}
							}
			
						}
						$cont_lt++;
					}//del while de lotes de trabajo
				}
			}

//echo $sql_up_act2."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		}



	if  (trim($error)=="no")  {
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
	} 
	else {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>

<html>
<head>

<title>.:: Planeación de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Intercambiar/Mover Lote de control </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>      
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
				echo $datos_Lc["macroactividad"]." - ".$datos_Lc["nombre"];
				$lc_encargado= $datos_Lc["id_encargado"]; 
				$lc_id=$datos_Lc["id_actividad"];
				$lc_div=$datos_Lc["id_division"];
			}		
?>
		  </td>
		</tr>

		<TR >
			<td class="TituloTabla"> Responsable

			</td>
			<td class="TxtTabla">
<?php
               $sql2="select U.*
                        from usuarios U	where unidad='".$lc_encargado."'  and retirado is null" ;
        
                $cursor2 = mssql_query($sql2);
                while ($reg2=mssql_fetch_array($cursor2)) 
					{
						echo "[".$reg2[unidad]."] ".ucwords(strtolower($reg2[nombre]))." ".ucwords(strtolower($reg2[apellidos])); 
           			}
 ?>
			</td>
		</TR>
		<tr>

          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>

<tr>
          <td class="TituloTabla">Tipo de operaci&oacute;n</td>

          <td class="TxtTabla"><label id="operacion" value=0>Intercambio</label>
           </td>
		</tr>

		<tr>
			<td class="TituloTabla" colspan="2" align="center">
				&iquest;Seleccione la ubicaci&oacute;n donde desea intercambiar la actividad?
			</td>
		</tr>
   <tr>
          <td class="TituloTabla">Lote de control </td>

<?php
//cualProyecto=683&cualLC=1&cualDIvision=4&cualACtividad=5


					//validamos si la variable que se forma del  select, esta definida, si no lo esta, es por que es la primera vez que se accede a la pagina
					//entonces  cargamos la variable, enviada como parametro, para cargar el select, del los lotes de trabajo 
					if(!isset($LC))
					{
							$LC=$cualLC;
					}
					//si la variable no esta definida, es por que es la primera vez que se carga la pagina, y asi, el selecct quedara por defecto, con el LC al cual pertenece el LT seleccionado por el usuario, en la
					//ventana anterior, la cual trahe como parametro el valor del LC, perteneciente al LT
					if(!isset($cualLC2))
					{
							$cualLC2=$cualLC;
					}

					if(!isset($cualLT2))
					{
							$cualLT2=$cualLT;
					}

					if(!isset($LT))
					{
						$LT=$cualLT2;
					}

//echo $LT." - ".$LC." - ".$cualLC2." - ".$cualLT2;
?>

          <td class="TxtTabla">
<?php		
		//si la operacion es intercambio, se  muestra solo el nombre del lote de control asociado a la actividad

		
//echo "cualLC ".$cualLC2." LC ".$LC."";
 ?>

		<select name="LC" id="LC"   class="CajaTexto"  onChange="document.Form1.submit();">
            <?php
					//consultamos los lotes de control asociados a la EDT del proyecto
					$sql_LC="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1 and id_actividad <> ".$cualLC;
					$sql_LC=$sql_LC."  order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int) ";
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
//echo "<br>cualLC ".$cualLC2." LC ".$LC."";
		
?>
	   	  </td>
        </tr>
    

			        
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

  		    <input name="cualLT" type="hidden" id="cualLT" value="<?php echo $cualLT; ?>">
  		    <input name="cualLT2" type="hidden" id="cualLT2" value="<?php echo $cualLT2; ?>">
  		    <input name="cualLC2" type="hidden" id="cualLC2" value="<?php echo $cualLC2; ?>">
<!--  		    <input name="operacion" type="hidden" id="operacion" value="<?php //echo $operacion; ?>">
-->

  		    <input name="operacion" type="hidden" id="operacion" value="1">
<!--


  		    <input name="cualLC2" type="hidden" id="cualLC2" value="<?php //echo $cualLC2; ?>">
  		    <input name="div_id" type="hidden" id="div_id" value="<?php //echo $div_id; ?>">
-->
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


			if(document.getElementById("LC").value=="")
			{
				alert ('Seleccione un lote de control');
			}	
		//si la opercacion seleccionada es intercambio
/*
			if(document.getElementById("LT").value=="")
			{
				alert ('Seleccione un lote de trabajo');
			}	
*/		

/*		
			else if(document.getElementById("AC").value=="")
			{
				alert ('Seleccione una actividad');
			}
*/
			else
			{
				document.Form1.operacion.value="1";
				document.Form1.recarga.value="2";
				document.Form1.submit();
			}

}



</script>
</body>
</html>

<? mssql_close ($conexion); ?>	
