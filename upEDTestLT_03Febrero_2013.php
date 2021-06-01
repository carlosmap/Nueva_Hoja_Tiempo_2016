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
		//si la operacion seleccionada es de intercambio
		if($operacion==1)
		{	
			$error="no";
			//consultamos el lote de trabajo de  origen, para el intercambio
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=2";
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
			$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$LT." and actPrincipal=".$cualLC." and tipoActividad=2";
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
			//actualizamos el lote de trabajo de origen y destino, intercambiando la informacion correspondiente, y dejando intacto el id de el lote de trabajo
			//actualizamos primero el lote de trabajo de origen
			$sql_up_lt="update Actividades set macroactividad='".$macro_act_2."'";
//			 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."'";
			$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=2";
	
			$cur_up_lt=mssql_query($sql_up_lt);
//echo "2 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
			if  (trim($cur_up_lt) == "")  
			{
				$error="si";
			}
			//si no se presentan inconvenientes, se actualizan las  divisiones de el lote de trabajo
			else
			{
				//consultamos las  divisiones de el lote de trabajo
				$sql_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLT." and nivel=3";
				$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cursor_div=mssql_query($sql_div);
//	echo "3 ".$sql_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
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
						$macro_act_3=$macro_act_2.".".$cont_div;
					//actualizamos las divisiones de origen, con los datos de la division de destino
					$sql_up_div1="update Actividades set macroactividad='".$macro_act_3."'";
//					 $sql_up_div1=$sql_up_div1. "nivelesActiv='".$nivel_div1."'";
					$sql_up_div1=$sql_up_div1." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=3";
//	echo "5 ".$sql_up_div1."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
					$cur_up_div1=mssql_query($sql_up_div1);
					if  (trim($cur_up_div1) == "")  
					{
						$error="si";
					}
					else
					{
		//*********************** 1.  AQUI 1 pruebas_activida_dinamica.php
		
							//consultamos las actividades del la division
							$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_div["id_actividad"]." and nivel=4";
							$sql_act=$sql_act."order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
							$cursor_act=mssql_query($sql_act);
		//echo "3.0 ".$sql_act."<br><br>";
					//		$macro_act_4=$macro_act_3.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
//							$niveles_act_3=$nivel_div2."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
							$num_act=1;
//		echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
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
//		echo "4 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
							}	
					}//del else
						$cont_div++;
					
					}//del while
				}

			}


			//se extrahe el nivel de la division de origen hasta el id de la actividad  1-2
//			$nivel_div1=substr($niveles_act_1,0,strrpos($niveles_act_1, "-"));
			//y se le adiciona la actividad del lote de trabajo de origen
//			$nivel_div1=$nivel_div1."-".$LT;
//echo $nivel_div2;
			//actualizamos el lote de trabajo de  destino, intercambiando la informacion correspondiente, y dejando intacto el id de el lote de trabajo
			//actualizamos primero el lote de trabajo de destino
			$sql_up_lt="update Actividades set macroactividad='".$macro_act_1."'";
//			 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."'";
			$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$LT." and actPrincipal=".$cualLC." and tipoActividad=2";
	
			$cur_up_lt=mssql_query($sql_up_lt);
//echo "2.2 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
			if  (trim($cur_up_lt) == "")  
			{
				$error="si";
			}
			//si no se presentan inconvenientes, se actualizan las  divisiones de el lote de trabajo
			else
			{
				//consultamos las  divisiones de el lote de trabajo
				$sql_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$LT." and nivel=3";
				$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cursor_div=mssql_query($sql_div);
//echo "3.2 ".$sql_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
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
						$macro_act_3=$macro_act_1.".".$cont_div;
					//actualizamos las divisiones de origen, con los datos de la division de destino
					$sql_up_div1="update Actividades set macroactividad='".$macro_act_3."'";
//					 $sql_up_div1=$sql_up_div1. "nivelesActiv='".$nivel_div1."'";
					$sql_up_div1=$sql_up_div1." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_div["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=3";
//	echo "5.2 ".$sql_up_div1."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
					$cur_up_div1=mssql_query($sql_up_div1);
					if  (trim($cur_up_div1) == "")  
					{
						$error="si";
					}
		//*********************** 1.  AQUI 1 pruebas_activida_dinamica.php
		
							//consultamos las actividades del la division
							$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_div["id_actividad"]." and nivel=4";
							$sql_act=$sql_act."order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
							$cursor_act=mssql_query($sql_act);
		//echo "3.0 ".$sql_act."<br><br>";
					//		$macro_act_4=$macro_act_3.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
//							$niveles_act_3=$nivel_div2."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
							$num_act=1;
//	echo "3.2 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
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
//		echo "4.2 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
							}	
						$cont_div++;
					}
				}

			}


//echo $sql_up_act2."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		}
			
	//si la operacion seleccionada es mover
	if($operacion==2)
	{
			$error="no";
			$ban_error="si";

			//consultamos la division de  origen, para el movimiento
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=2";
			$cur_inter1=mssql_query($sql_inter1);
			if  (trim($cur_inter1) == "")  
			{
				$error="si";
			}	
//echo $sql_inter1."<br>";
			if($datos_inter1=mssql_fetch_array($cur_inter1))
			{
				//almacenmos la informacion de la actividad de origen, para ulizarla, al momento de actualizar la actividad de destino, que es donde quedara asociada la actividad
				$macro_act_1=$datos_inter1["macroactividad"];
				$depende_act_1=$datos_inter1["dependeDe"];
				$niveles_act_1=$datos_inter1["nivelesActiv"];
			//	$id_act_1=$datos_inter1["id_actividad"]:
				$id_act_1=$datos_inter1["id_actividad"];
//				$nombre_act_1=$datos_inter1["nombre"];
				$act_prin_act_1=$datos_inter1["actPrincipal"];

			}
		
			$act_desti_encontra="si"; //permite identificar, si la division de destino contiene actividades

			$cur_activi=0;		//identifica que la consulta de las divisiones del lote de trabajo de origen, traiga algun resultado
			$ban_error="no";	// identifica si se ha presentado algun error durante la consulta de las deivisiones del lote de trabajo de destino

			 //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.(2) de origen
			$num1=substr($macro_act_1,strrpos($macro_act_1, ".")+1,strlen($macro_act_1));
		
				//si el lote de trabajo seleccionado como destino, no es null, es por que el lote de control de destino, contiene lotes de trabajo
				if(trim($LT)!="")
				{
					//consultamos el lote de trabajo de destino, para el movimiento
//					$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$DI." and actPrincipal=".$LC." and tipoActividad=3";
					$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$LT." and actPrincipal=".$LC." and tipoActividad=2";
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
						$niveles_act_2=$datos_inter2["nivelesActiv"];
						$act_prin_act_2=$datos_inter2["actPrincipal"];
					}
		//echo "<br> 1 ".$sql_inter1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
					 //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.(2) de destino
					$num2=substr($macro_act_2,strrpos($macro_act_2, ".")+1,strlen($macro_act_2));
		//echo "macro 1 ".$macro_act_1." - ".$num1;
		//echo " macro 1 ".$macro_act_2." - ".$num2;
					$num2++;
		
		
		//			 //permite identificar si se esta recorriendo la actividad donde se va a traspasar la actividad, y asi modificar las actividades que estan por debajo
					//consultamos las actividades de la division, donde se va a mover la actividad destino
		//			$sql_activi="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4 and ".$AC." < id_actividad order by macroactividad";
					$sql_activi="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$LC."  and tipoActividad=2 and  cast (reverse(substring(reverse('".$macro_act_2."'),1,charindex('.', reverse('".$macro_act_2."'))-1)) as int )<
								cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					//CONSULTA SQL 
					//replace(sbustring(primero voltiamos la macro actividad), (indicamos la posicion inicial),( dentro del char index, indicamos que voltee la macro y que
					//busque la posicion donde esta ubucado el primer '.', que seria la posicion final del substring)) y voltiamos el valor a su pocision original
					//y luego lo convertimos a int, para compararlos
		
					$cur_activi=mssql_query($sql_activi);
//	echo "<br> 1 ".$sql_activi."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		//echo "<br>".$macro_act_1." - ".$depende_act_1." - ".$division_act_1." - ".$niveles_act_1." - ".$act_prin_act_1." - ".$nombre_act_1."<br>";
		//echo "1 ". $sql_activi."  --  ".mssql_get_last_message()." ".$error."  --  ".$cur_activi."<br><br>";
					if  (trim($cur_activi) == "")  
					{
						$error="si";
						$ban_error="si";  //con ban_error identificamos si  la consulta no ha presentado errores
					}	
		
					$cant_reg=mssql_num_rows($cur_activi);
					$reg=0;
					$band=0;
		
		//			$reg_encontrador=0; //esta variable nos permite saber si se encontraron registros en la consulta de las actividades 0=no 1=si
					while($datos_activi=mssql_fetch_array($cur_activi))
					{
		//				$reg_encontrador=1;
						$reg++;
						//antes de actualizar las actividades de la division, que estan por debajo de la actividad de destino, actualizamos la actividad de origen
						if($band==0)
						{
		
							//componemos el nivel de la division, descomponiendo el id division de destion, y remplanando el ultimo numero , por el de la actividad de origen
							$nivel_div2=substr($niveles_act_2,0,strrpos($niveles_act_2, "-"));
							$nivel_div2=$nivel_div2."-".$cualLT;
		
							//descomponemos la macro actividad de la division de destion, con el fin de añadirle el ultimo numero que identificar a la division a mover
							$macro_act2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;

							//acualizamos el lote de trabajo de origen, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
							$sql_up_lt="update Actividades set macroactividad='".$macro_act2."', dependeDe=".$depende_act_2.",";
							 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$nivel_div2."' , actPrincipal=".$act_prin_act_2;
							$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=2";
							$cur_up_lt=mssql_query($sql_up_lt);
//			echo "2.1 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cur_up_lt) == "")  
							{
								$error="si";
							}
							else
							{
								//consultamos las divisiones del lote de trabajo de origen, para actualizarlas
								$sql_act_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLT." and nivel=3";
								$sql_act_div=$sql_act_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
								$cur_act_div=mssql_query($sql_act_div);
//			echo "2.2 ".$sql_act_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
								if  (trim($cur_act_div) == "")  
								{
									$error="si";
								}
								$num_div=1; //identificara el ultimo numero que identifica la division
								while($datos_act_div=mssql_fetch_array($cur_act_div))
								{
									//generamos la macroactividad y el nivel de las divisiones asociadas a el lote de trabajo
									$macro_act3=$macro_act2.".".$num_div;
									$nivel_div3=$nivel_div2."-".$datos_act_div["id_actividad"];

									//acualizamos la division de origen, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
									$sql_up_act="update Actividades set macroactividad='".$macro_act3."',";
									 $sql_up_act=$sql_up_act. "nivelesActiv='".$nivel_div3."' , actPrincipal=".$act_prin_act_2;
									$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act_div["id_actividad"]."  and dependeDe=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=3";
									$cur_up_act=mssql_query($sql_up_act);
//				echo "2 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
									if  (trim($cur_up_act) == "")  
									{
										$error="si";
									}
									//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
									else
									{
										//consultamos las actividades del la division de origen, para actualizarlas
										$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_act_div["id_actividad"]." and nivel=4";
										$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					
										$cursor_act=mssql_query($sql_act);
										$macro_act_4=$macro_act3.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
										$niveles_act_4=$nivel_div3."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
					
//					echo "3.0 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
										$num_act=1;
					//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
										while($datos_act=mssql_fetch_array($cursor_act))
										{
												//formamos la macro actividad y el nivel de las actividades a intercambiar
												$macro_act_31=$macro_act_4.$num_act;
												$niveles_act_31=$niveles_act_4.$datos_act["id_actividad"];
												//actualizamos las actividades - sub actividades, etc, de la division
												$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$act_prin_act_2.",";
												 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
												$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]."  and dependeDe=".$datos_act_div["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
						
												$cur_up_act3=mssql_query($sql_up_act3);
												if  (trim($cur_up_act3) == "")  
												{
													$error="si";
												}	
												$num_act++;
//					echo "4 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
										}
									}
									$num_div++;
								}

								$band=1;
							}
		//echo "$band <br> 3 ".$sql_up_act3."   --  ".mssql_get_last_message()." ".$error."<br><br>";
						}
		 
						$num2++;
		
						$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
		//echo "$macro2<br>";
						//acualizamos los lotes de trabajo de destino, que estan por debajo intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
						$sql_up_lt="update Actividades set macroactividad='".$macro2."'";
						$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=2";
						$cur_up_lt=mssql_query($sql_up_lt);
//		echo "2.1 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
						if  (trim($cur_up_lt) == "")  
						{
							$error="si";
						}
						else
						{
//--
							//consultamos las divisiones del lote de trabajo de destino, para actualizarlas
							$sql_act_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$datos_activi["id_actividad"]." and nivel=3";
							$sql_act_div=$sql_act_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
							$cur_act_div=mssql_query($sql_act_div);
//		echo "2.2 ".$sql_act_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cur_act_div) == "")  
							{
								$error="si";
							}
							$num_div=1; //identificara el ultimo numero que identifica la division
							while($datos_act_div=mssql_fetch_array($cur_act_div))
							{
//--		
								$macro3=$macro2.".".$num_div;
								//actualizamos las divisiones de destino, que estan por debajo intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
								$sql_up_act="update Actividades set macroactividad='".$macro3."'";
								$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act_div["id_actividad"]."  and dependeDe=".$datos_activi["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=3";
				
								$cur_up_act=mssql_query($sql_up_act);
	//			echo "5 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
								if  (trim($cur_up_act) == "")  
								{
									$error="si";
								}
								//si no se presentaron errores, en la actualizacion del lote de trabajo, se actualiza las actividades asociadas a ellas
								else
								{
		
									//consultamos las actividades del la division de destino, para actualizarlas
									$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$datos_act_div["id_actividad"]." and nivel=4";
									$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				
									$cursor_act=mssql_query($sql_act);
									$macro_act_4=$macro3.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
				
			//	echo "6.0 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
									$num_act=1;
				//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
									while($datos_act=mssql_fetch_array($cursor_act))
									{
											//formamos la macro actividad y el nivel de las actividades a intercambiar
											$macro_act_41=$macro_act_4.$num_act;
											//actualizamos las actividades - sub actividades, etc, de la division
											$sql_up_act3="update Actividades set macroactividad='".$macro_act_41."'";
											$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]."  and dependeDe=".$datos_act_div["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=4";
					
											$cur_up_act3=mssql_query($sql_up_act3);
											if  (trim($cur_up_act3) == "")  
											{
												$error="si";
											}	
											$num_act++;
	//			echo "7 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
									}
								}
								$num_div++;
							}
						}		

		
		
					} //del while de las divisiones de destino
				} //del if DI!=""
					$band_reg_encon="si"; //esta variable nos permite saber, si se encontraron registros de las actividades asociadas  y/o no es la ultima actividad de la division del lote de control, a donde se movera la actividad
		
					//si no se encontraron registros en la consulta de las divisiones del lote de trabajo de origen, y no se presentaron errores, quiere decir 2 cosas:
					//1. que la el lote de trabajo no coneiene divisiones asociadas
					//2. que el lote de trabajo seleccionada como destino, no tiene lotes por debajo

/////16 NOV 2012		
					if(($cant_reg==0)and ($ban_error=="no"))
					{
						$band_reg_encon="no";
						//consultamos si el lote de control de destino, contiene lotes de trabajo asociados
						$sql_activi_lt="select * from Actividades where id_proyecto=".$cualProyecto." and  actPrincipal=".$LC." and dependeDe=".$LC." and tipoActividad=2 ";
						$cur_activi_lt=mssql_query($sql_activi_lt);
		

		
						if  (trim($cur_activi_lt) == "")  
						{
							$error="si";
						}
						$cant_reg_acti_lt=mssql_num_rows($cur_activi_lt);  //almacenamos la cantidad de divisiones encontradas
		//		echo "<br> 8 --$cant_reg_acti_lt-- ".$sql_activi_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";
						//consultamos el id del lote de control de destino a donde se movera la division 
						$sql_lc2="select * from Actividades where id_proyecto=".$cualProyecto." and  actPrincipal=".$LC." and id_actividad=".$LC." and tipoActividad=1 ";
						$cur_lc2=mssql_query( $sql_lc2);
						if  (trim($cur_lc2) == "")  
						{
							$error="si";
						}
	//	echo "9 ". $sql_lc2."  -- $error ".mssql_get_last_message()."<br><br>";
						while($datos_lc2=mssql_fetch_array($cur_lc2))
						{
							$div2=$datos_lc2["id_division"];
							$macro2=$datos_lc2["macroactividad"];
						}
		
						//si no se encontraron registros, quiere indicar que el lote de control  no tiene lotes de trabajo
						//situacion 1.
						if($cant_reg_acti_lt==0)
						{
		
							//construimos la macro-actividad, de la division a traspasar, con la macro de la division
							$macro_activida=$macro2.".1";
							$niveles_act_2="".$LC."-".$id_act_1; //inicializamos la variable niveles, con el nivel del lote de trabajo de destino

							//acualizamos el lote de trabajo de origen, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
							$sql_up_lt="update Actividades set macroactividad='".$macro_activida."', dependeDe=".$LC.",";
							 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$niveles_act_2."' , actPrincipal=".$LC;
							$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=2";
							$cur_up_lt=mssql_query($sql_up_lt);

//echo $niveles_act_2."  -  ".$macro_activida."<br>";
	//		echo "9.1 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cur_up_lt) == "")  
							{
								$error="si";
							}
							else
							{
									//consultamos las divisiones del lote de trabajo de origen, para actualizarlas
								$sql_act_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLT." and nivel=3";
								$sql_act_div=$sql_act_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
								$cursor_act_div=mssql_query($sql_act_div);
//				echo "10 ". $sql_act_div."  -- $error ".mssql_get_last_message()."<br><br>";
								$num_div=1;
								while($datos_act_div=mssql_fetch_array($cursor_act_div))
								{

									$macro_act_31=$macro_activida.".".$num_div;
									$niveles_act_31=$niveles_act_2."-".$datos_act_div["id_actividad"];
									//actualizamos  las divisiones del lote de trabajo de origen
									$sql_up_act4="update Actividades   set macroactividad='".$macro_act_31."'";
									$sql_up_act4=$sql_up_act4.",nivelesActiv='".$niveles_act_31."',actPrincipal=".$LC; 
									$sql_up_act4=$sql_up_act4." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act_div["id_actividad"]." and dependeDe=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=3";
									$cur_up_act4=mssql_query($sql_up_act4);

//echo $niveles_act_31."  -  ".$macro_act_31."<br>";
				echo "11 ". $sql_up_act4."  -- $error ".mssql_get_last_message()."<br><br>";
										if  (trim($cur_up_act4) == "")  
										{
											$error="si";
										}	
										//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
										else
										{
											//consultamos las actividades del la division de origen, para actualizarlas
											$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_act_div["id_actividad"]." and nivel=4";
											$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						
											$cursor_act=mssql_query($sql_act);
											$macro_act_4=$macro_act_31.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
						
		//				echo "12 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
											$num_act=1;
						//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
											while($datos_act=mssql_fetch_array($cursor_act))
											{
													//formamos la macro actividad y el nivel de las actividades a intercambiar
													$macro_act_41=$macro_act_4.$num_act;
													$niveles_act_41=$niveles_act_31."-A-".$datos_act["id_actividad"];
													//actualizamos las actividades - sub actividades, etc, de la division
													$sql_up_act3="update Actividades set macroactividad='".$macro_act_41."', actPrincipal=".$LC.",";
													 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_41."' ";
													$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
							
													$cur_up_act3=mssql_query($sql_up_act3);
													if  (trim($cur_up_act3) == "")  
													{
														$error="si";
													}	
													$num_act++;
//echo $niveles_act_41."  -  ".$macro_act_41."<br>";
						echo "13 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
											}
										}
									$num_div++;
								}
							}							
						}	
			
						//situacion 2.
						//se encontraron registros, pero la division seleccionada como destino, es la ultima del  lote de trabajo
						else
						{
							//extrhemos parte de la macro actividad, y le añadimos el ultimo numero de el lote de trabajo de destino, para componer la macro actividad a mover
							$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
		//echo $macro2." -- $num2 <br>";
							$niveles_act_2="".$LC."-".$id_act_1; //inicializamos la variable niveles, con el nivel del lote de trabajo  de destino, añadiendo el id del lote de origen

							//acualizamos el lote de trabajo de origen, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
							$sql_up_lt="update Actividades set macroactividad='".$macro2."', dependeDe=".$LC.",";
							 $sql_up_lt=$sql_up_lt. "nivelesActiv='".$niveles_act_2."' , actPrincipal=".$LC;
							$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=2";
							$cur_up_lt=mssql_query($sql_up_lt);

//echo $macro2."  -  ".$niveles_act_2."<br>";
	//		echo "9.1 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cur_up_lt) == "")  
							{
								$error="si";
							}		
							else
							{
									//consultamos las divisiones del lote de trabajo de origen, para actualizarlas
								$sql_act_div="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLT." and nivel=3";
								$sql_act_div=$sql_act_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
								$cursor_act_div=mssql_query($sql_act_div);
			///	echo "10 ". $sql_act_div."  -- $error ".mssql_get_last_message()."<br><br>";
								$num_div=1;
								while($datos_act_div=mssql_fetch_array($cursor_act_div))
								{
//									$macro_act_2=$macro2.".".$num_div;
//									$niveles_act_2="".$LC."-".$id_act_1; //inicializamos la variable niveles, con el nivel del lote de trabajo de destino

									$macro_act_31=$macro2.".".$num_div;
									$niveles_act_31=$niveles_act_2."-".$datos_act_div["id_actividad"];
//echo $niveles_act_31."  -  ".$macro_act_31."<br>";
									//actualizamos primero las divisiones del lote de trabajo de origen
									$sql_up_act5="update Actividades   set macroactividad='".$macro_act_31."'";
									$sql_up_act5=$sql_up_act5.",nivelesActiv='".$niveles_act_31."',actPrincipal=".$LC; 				
									$sql_up_act5=$sql_up_act5." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act_div["id_actividad"]." and dependeDe=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=3";
									$cur_up_act5=mssql_query($sql_up_act5);
			//	echo "$band <br> 13 ".$sql_up_act5."   --  ".mssql_get_last_message()." ".$error."<br><br>";	
									if  (trim($cur_up_act5) == "")  
									{
										$error="si";
									}	
								
									//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
									else
									{


										//consultamos las actividades del la division de origen, para actualizarlas
										$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_act_div["id_actividad"]." and nivel=4";
										$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					
										$cursor_act=mssql_query($sql_act);
										$macro_act_3=$macro_act_31.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
					
				//	echo "14 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
										$num_act=1;
					//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
										while($datos_act=mssql_fetch_array($cursor_act))
										{
												//formamos la macro actividad y el nivel de las actividades a intercambiar
												$macro_act_41=$macro_act_3.$num_act;
												$niveles_act_41=$niveles_act_31."-A-".$datos_act["id_actividad"];
												//actualizamos las actividades - sub actividades, etc, de la division
												$sql_up_act3="update Actividades set macroactividad='".$macro_act_41."', actPrincipal=".$LC.",";
												 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_41."' ";
												$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and dependeDe=".$datos_act_div["id_actividad"]." and tipoActividad=4";
						
												$cur_up_act3=mssql_query($sql_up_act3);
												if  (trim($cur_up_act3) == "")  
												{
													$error="si";
												}	
												$num_act++;
//echo $niveles_act_31."  -  ".$macro_act_31."<br>";
		//			echo "15 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
										}
									}
									$num_div++;
								}
							}
						}
				
					}	

			//consultamos los lotes de trabajo que estan por debajo del lote de origen, para actualizar la macro actividad  de cada una de ellas
			 $sql_act_lt="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLC." and tipoActividad=2 and  cast (reverse(substring(reverse('".$macro_act_1."'),1,charindex('.', reverse('".$macro_act_1."'))-1)) as int )<
						cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
			$cur_act_lt=mssql_query($sql_act_lt);
			while($datos_act_lt=mssql_fetch_array($cur_act_lt))
			{
				$macro_lt=substr($macro_act_1,0,strrpos($macro_act_1, ".")+1).$num1;
				$num1++;
				$sql_up_lt="update Actividades set macroactividad='".$macro_lt."'";
				$sql_up_lt=$sql_up_lt." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act_lt["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=2";
				$cur_up_lt=mssql_query($sql_up_lt);

//echo $niveles_act_31."  -  ".$macro_lt."<br>";
//echo "15.1 ".$sql_up_lt."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
				if  (trim($cur_up_lt) == "")  
				{
					$error="si";
				}
				else
				{
					//consultamos las divisiones de los lotes de trabajo
					 $sql_activi="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_act_lt["id_actividad"]." and tipoActividad=3
								 order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					$cur_activi=mssql_query($sql_activi);
	//	echo "15.1 ".$sql_activi."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
					$num_div=1;
					while($datos_activi=mssql_fetch_array($cur_activi))
					{
		
							$macro_div=$macro_lt.".".$num_div;

							//actualiza las divisiones
							$sql_up_act="update Actividades set macroactividad='".$macro_div."'";
							$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi["id_actividad"]." and dependeDe=".$datos_act_lt["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=3";
							$cur_up_act=mssql_query($sql_up_act);
							
		
//echo "  -  ".$macro_div."<br>";							
	//	echo "16 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cur_up_act) == "")  
							{
								$error="si";
							}
							//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
							else
							{
								//consultamos las actividades del la division de origen, para actualizarlas
								$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$datos_activi["id_actividad"]." and nivel=4";
								$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
			
								$cursor_act=mssql_query($sql_act);
								$macro_act_3=$macro_div.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
		
			
	//		echo "17 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
								$num_act=1;
			//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
								while($datos_act=mssql_fetch_array($cursor_act))
								{
										//formamos la macro actividad y el nivel de las actividades a intercambiar
										$macro_act_31=$macro_act_3.$num_act;
										//actualizamos las actividades - sub actividades, etc, de la division
										$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."'";
										$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and dependeDe=".$datos_activi["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
				
										$cur_up_act3=mssql_query($sql_up_act3);
										if  (trim($cur_up_act3) == "")  
										{
											$error="si";
										}	
										$num_act++;
//echo "  -  ".$macro_act_31."<br>";		
		//	echo "18 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
								}
							}
						$num_div++;
					}	
				}	
		
			}

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
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
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
    <td class="TituloUsuario">Intercambiar/Mover Lote de trabajo </td>
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
			}		
?>
		  </td>
		</tr>
<?php

	$sql_Lt="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=".$cualLT;
	$cur_Lt=mssql_query($sql_Lt);	
?>
		<tr>
          <td class="TituloTabla">Lote de trabajo Actual</td>
          <td class="TxtTabla">
<?php
			if($datos_Lt=mssql_fetch_array($cur_Lt))
			{
				echo $datos_Lt["macroactividad"]." - ".$datos_Lt["nombre"];
				$inf_lt= $datos_Lt["macroactividad"]." - ".$datos_Lt["nombre"];
				$lt_id=$datos_Lt["id_actividad"];
				$lt_div=$datos_Lt["id_division"];
				$lt_encargado= $datos_Lt["id_encargado"]; 
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
                        from usuarios U	where unidad='".$lt_encargado."'  and retirado is null" ;
        
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
<?php
					//si es la primera vez que se carga la pagina, definimos operancion=1, ya que este valor corresponde a intercambio, que esta marcado por defecto, en el radio button
					if(!isset($operacion))
					{
							$operacion=1;
					}
?>
          <td class="TxtTabla"><input name="operacion" type="radio" id="operacion" value="1" <?php if(($operacion==1)){ echo "checked"; $LC=$cualLC;  } ?>   onClick="document.Form1.submit();">
            <label for="operacion">Intercambio</label>
            <input type="radio" name="operacion" id="operacion" value="2"   <?php if($operacion==2){ echo "checked"; } ?>  onClick="document.Form1.submit();">
            <label for="operacion2">Mover</label></td>
		</tr>

		<tr>
			<td class="TituloTabla" colspan="2" align="center">
				&iquest;Seleccione la ubicaci&oacute;n donde desea mover/intercambiar la actividad?
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

		
//echo "cualLC ".$cualLC2." LC ".$LC."";
 ?>

		<select name="LC" id="LC"   class="CajaTexto"  onChange="document.Form1.submit();">
            <?php
					//consultamos los lotes de control asociados a la EDT del proyecto
					$sql_LC="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1";
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
		}
?>
	   	  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla">
		<select name="LT" id="LT" class="CajaTexto"  onChange="document.Form1.submit();">
            <?php
					//consultamos los lotes de trabajo asociados a el lote de control seleccionado
					$sql_LT="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto =".$cualProyecto." and dependeDe=".$LC." and nivel = 2 and id_actividad <>".$cualLT;
/*					if($operacion==2)
					{
						$sql_LT=$sql_LT."and dependeDe <>".$cualLC;
					}
*/
					$sql_LT=$sql_LT." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
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


	if(document.Form1.operacion[0].checked)
	{
		//si la opercacion seleccionada es intercambio

			if(document.getElementById("LT").value=="")
			{
				alert ('Seleccione un lote de trabajo');
			}	
		
			else if(document.getElementById("LT").value=="<?php echo $cualLT; ?>")
			{
				alert ('Por favor seleccione un lote de trabajo direfente, para realizar el intercambio.');
			}
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

	if(document.Form1.operacion[1].checked)
	{	
		//ai la operacion seleccionada es mover

			if(document.getElementById("LC").value=="")
			{
				alert ('Seleccione un lote de control');
			}	

			else if((document.getElementById("LC").value=="<?php echo $cualLC; ?>") || (document.getElementById("LT").value=="<?php echo $cualLT; ?>"))
			{
				alert ('Para mover el lote de trabajo <?php echo $inf_lt; ?>, por favor seleccione un lote de control diferente.');
			}
			
/*		
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
