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



			//consultamos la division de  origen, para el intercambio
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and actPrincipal=".$cualLC." and tipoActividad=3";
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
			//consultamos la division destino, para el intercambio
			$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$DI." and actPrincipal=".$cualLC." and tipoActividad=3";
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
//				$nombre_act_2=$datos_inter2["nombre"];
/*
				$fecha_fin_2=$datos_inter2["fecha_fin"];
				$fecha_ini_2=$datos_inter2["fecha_inicio"];
*/
			}
//echo "<br>".$macro_act_1." - ".$depende_act_1." - ".$division_act_1." - ".$niveles_act_1."".$act_prin_act_1."<br>"; echo "<br>".$macro_act_2." - ".$depende_act_2." - ".$division_act_2." - ".$niveles_act_2."".$act_prin_act_2."<br>";



			//consultamos si el lote de trabajo de destino, contiene una division con el mismo nombre, que la de origen
			$sql_div_lt="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and id_division=".$division_act_1." and dependeDe=".$LT." and tipoActividad=3";
			$cur_div_lt=mssql_query($sql_div_lt);
			if  (trim($cur_div_lt) == "")  
			{
				$error="si";
			}	
			$cant_div=mssql_num_rows($cur_div_lt);
//echo "-4 ".$sql_div_lt."  --  ".mssql_get_last_message()." <br><br>";

			//consultamos si el lote de trabajo de origen, contiene una division con el mismo nombre, que la de destino
			$sql_div_lt2="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and id_division=".$division_act_2." and dependeDe=".$cualLT." and tipoActividad=3";
			$cur_div_lt2=mssql_query($sql_div_lt2);
			if  (trim($cur_div_lt2) == "")  
			{
				$error="si";
			}	
			$cant_div2=mssql_num_rows($cur_div_lt2);

//echo "-4 ".$sql_div_lt2."  --  ".mssql_get_last_message()." <br><br>";

			$ban_encon="no"; //permite identificar si se encontraron divisiones conincidentes, entre los lotes de trabjo

			//si en el lote de trabajo de destino, ya existe la division que se va a intercambiar, y el lote de trabajo es diferente, para que permita el intercambio de divisiones entre de origen el lote de trabajo
			if(($cant_div>=1)and($cualLT!=$LT))
			{
				$error="si";
				$ban_encon="si";
				//consulta la division de origen
				$slq_div="select upper(nombre) as nombre from Divisiones where estadoDiv='A' and id_division=".$division_act_1;
				$cur_div=mssql_query($slq_div);
				if($datos_div=mssql_fetch_array($cur_div))
				{
//					$macro_div_origen=$datos_div["macroactividad"];
					$div_nom_origen=$datos_div["nombre"];
				}
				//consulta el lote de trabajo de destino
				$sql_lote=mssql_query("select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$LT." and tipoActividad=2");
				if($datos_lote=mssql_fetch_array($sql_lote))
				{
					$lote_nom_destino=$datos_lote["nombre"];
					$macro_lote_destino=$datos_lote["macroactividad"];
				}
				echo ("<script>alert('No se puede intercambiar la division ".$div_nom_origen." por que ya existe en el lote de trabajo $macro_lote_destino - ".$lote_nom_destino."');</script>");				
			}
			//si en el lote de trabajo de origen, ya existe la division que se va a intercambiar
			else if (($cant_div2>=1)and($cualLT!=$LT))
			{
				$error="si";
				$ban_encon="si";
				//consulta la division de destino
				$slq_div="select upper(nombre) as nombre from Divisiones where estadoDiv='A' and id_division=".$division_act_2;
				$cur_div=mssql_query($slq_div);
				if($datos_div=mssql_fetch_array($cur_div))
				{
					$div_nom_destino=$datos_div["nombre"];
					$macro_div_destino=$datos_div["macroactividad"];
				}
				//consulta el lote de trabajo de origen
				$sql_lote=mssql_query("select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and tipoActividad=2");
				if($datos_lote=mssql_fetch_array($sql_lote))
				{
					$lote_nom_origen=$datos_lote["nombre"];
					$macro_lote_origen=$datos_lote["macroactividad"];
				}
				echo ("<script>alert('No se puede intercambiar la division  ".$div_nom_destino." por que ya existe en el lote de trabajo $macro_lote_origen - ".$lote_nom_origen."');</script>");				
			}
			//si no exiten divisiones coincidentes entre los lotes de trabajo de origen y de destino, entonces se procede a actualizar las division es involucradas
			else
			{
/*
			//si se encontraron registros, se almacena la macro actividad de la division coincidente, para mas adelante, actualizar las actividades de la division a mover
			if($datos_div_lt=mssql_fetch_array($cur_div_lt))
			{
				//obtenemos la macro-actividad  y el id_actividad de destino
				$macro_div_lt=$datos_div_lt["macroactividad"];
				$id_div_lt=$datos_div_lt["id_actividad"];
				$nivel_div_lt=$datos_div_lt["nivelesActiv"];
				
				$macro_max_div=0;//se inicializa la variabel, que va a contener el ultimo numero de las actividades de la division LT1.1.13.A.(1), que se traspasaran

				/// se consulta la actividad con la mayor  macroactividad de la division de destino, y se extrahe el ultimo numero LT1.1.1.A.(1)
				$sql_macro_max_div="select MAX(cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)) as macro_max 
				from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$id_div_lt;
				$cur_macro_max_div=mssql_query($sql_macro_max_div);
				if  (trim($cur_macro_max_div) == "")  
				{
					$error="si";
				}	
				if($datos_macro_max_div=mssql_fetch_array($cur_macro_max_div))
				{
					$macro_max_div=$datos_macro_max_div["macro_max"];	
				}
echo "-3 ".$sql_macro_max_div."  --  ".mssql_get_last_message()." -- $macro_max_div <br><br>";
				//consultamos las actividades del la division de origen, para actualizarlas
				$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDIvision." and nivel=4";
				$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cursor_act=mssql_query($sql_act);
				if  (trim($cursor_act) == "")  
				{
					$error="si";
				}	
				$macro_act_3=$macro_div_lt.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
//				$niveles_act_3=$nivel_div_lt."-";  //inicializamos la variable niveles, con el nivel de la division  de destino

echo "-2 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
				$num_act=$macro_max_div+1;
//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	

				//actualizamos las actividades de la division, y las asociamos al de la division de destino
				while($datos_act=mssql_fetch_array($cursor_act))
				{
						//formamos la macro actividad y el nivel de las actividades a intercambiar
						$macro_act_31=$macro_act_3.$num_act;
						$niveles_act_31=$nivel_div_lt."-A-".$datos_act["id_actividad"];
						//actualizamos las actividades - sub actividades, etc, de la division
						$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$cualLC.",";
						 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."', dependeDe=".$id_div_lt;
						$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and dependeDe=".$cualDIvision."  and actPrincipal=".$cualLC." and tipoActividad=4";

						$cur_up_act3=mssql_query($sql_up_act3);
						if  (trim($cur_up_act3) == "")  
						{
							$error="si";
						}	
						$num_act++;
echo "-1 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
				}

				//se elimina el registro de la actividad de origen
				$sql_del_div="delete from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and id_actividad=".$cualDIvision." and tipoActividad=3";
				$cur_del_div=mssql_query($sql_del_div);

				if  (trim($cur_del_div) == "")  
				{
					$error="si";
				}				
echo "0 ".$sql_del_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	

			}
*/
			//si no se encontro una division igual en el lote de trabajo, entonces se realiza la actualizacion de intercambio
//			else
//			{

				//se extrahe el nivel de la division de destino hasta el id de la actividad  1-2
				$nivel_div2=substr($niveles_act_2,0,strrpos($niveles_act_2, "-"));
				//y se le adiciona la actividad de la division de origen
				$nivel_div2=$nivel_div2."-".$cualDIvision;
				//actualizamos las divisiones de origen y destino, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
				$sql_up_act="update Actividades set macroactividad='".$macro_act_2."', dependeDe=".$depende_act_2.",";
				 $sql_up_act=$sql_up_act. "nivelesActiv='".$nivel_div2."'";
				$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and actPrincipal=".$cualLC." and tipoActividad=3";
//	echo "2 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
				$cur_up_act=mssql_query($sql_up_act);
				if  (trim($cur_up_act) == "")  
				{
					$error="si";
				}
				//si no se presentan inconvenientes, se actualizan las actividades de la division	
				else
				{
	//*********************** 1.  AQUI 1 pruebas_activida_dinamica.php
	
						//consultamos las actividades del la division
						$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDIvision." and nivel=4";
						$sql_act=$sql_act."order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cursor_act=mssql_query($sql_act);
	//echo "3.0 ".$sql_act."<br><br>";
						$macro_act_3=$macro_act_2.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
						$niveles_act_3=$nivel_div2."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
						$num_act=1;
//	echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
						while($datos_act=mssql_fetch_array($cursor_act))
						{
							//formamos la macro actividad y el nivel de las actividades a intercambiar
							$macro_act_31=$macro_act_3.$num_act;
							$niveles_act_31=$niveles_act_3.$datos_act["id_actividad"];
							//actualizamos las actividades - sub actividades, etc, de la division
							$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."',";
							 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
							$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
	
							$cur_up_act3=mssql_query($sql_up_act3);
							if  (trim($cur_up_act3) == "")  
							{
								$error="si";
							}	
							$num_act++;
//	echo "4 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
						}	
	//*********************** 2.  AQUI 1 pruebas_activida_dinamica.php
				}
//			}


/*
			//si se encontraron registros, se almacena la macro actividad de la division coincidente, para mas adelante, actualizar las actividades de la division a mover
			if($datos_div_lt=mssql_fetch_array($cur_div_lt))
			{
				//obtenemos la macro-actividad  y el id_actividad de destino
				$macro_div_lt=$datos_div_lt["macroactividad"];
				$id_div_lt=$datos_div_lt["id_actividad"];
				$nivel_div_lt=$datos_div_lt["nivelesActiv"];
				
				$macro_max_div=0;//se inicializa la variabel, que va a contener el ultimo numero de las actividades de la division LT1.1.13.A.(1), que se traspasaran

				/// se consulta la actividad con la mayor  macroactividad de la division de destino, y se extrahe el ultimo numero LT1.1.1.A.(1)
				$sql_macro_max_div="select MAX(cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)) as macro_max 
				from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$id_div_lt;
				$cur_macro_max_div=mssql_query($sql_macro_max_div);
				if  (trim($cur_macro_max_div) == "")  
				{
					$error="si";
				}	
				if($datos_macro_max_div=mssql_fetch_array($cur_macro_max_div))
				{
					$macro_max_div=$datos_macro_max_div["macro_max"];	
				}
echo "-3 ".$sql_macro_max_div."  --  ".mssql_get_last_message()." -- $macro_max_div <br><br>";
				//consultamos las actividades del la division de origen, para actualizarlas
				$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$DI." and nivel=4";
				$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cursor_act=mssql_query($sql_act);
				if  (trim($cursor_act) == "")  
				{
					$error="si";
				}	
				$macro_act_3=$macro_div_lt.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
//				$niveles_act_3=$nivel_div_lt."-";  //inicializamos la variable niveles, con el nivel de la division  de destino

echo "-2 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
				$num_act=$macro_max_div+1;
//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	

				//actualizamos las actividades de la division, y las asociamos al de la division de destino
				while($datos_act=mssql_fetch_array($cursor_act))
				{
						//formamos la macro actividad y el nivel de las actividades a intercambiar
						$macro_act_31=$macro_act_3.$num_act;
						$niveles_act_31=$nivel_div_lt."-A-".$datos_act["id_actividad"];
						//actualizamos las actividades - sub actividades, etc, de la division
						$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$cualLC.",";
						 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."', dependeDe=".$id_div_lt;
						$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and dependeDe=".$DI."  and actPrincipal=".$cualLC." and tipoActividad=4";

						$cur_up_act3=mssql_query($sql_up_act3);
						if  (trim($cur_up_act3) == "")  
						{
							$error="si";
						}	
						$num_act++;
echo "-1 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
				}

				//se elimina el registro de la actividad de origen
				$sql_del_div="delete from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and id_actividad=".$DI." and tipoActividad=3";
				$cur_del_div=mssql_query($sql_del_div);

				if  (trim($cur_del_div) == "")  
				{
					$error="si";
				}				
echo "0 ".$sql_del_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	

			}

*/
//			else
//			{
				
				//se extrahe el nivel de la division hasta el id de la actividad de origen  1-2
				$nivel_div1=substr($niveles_act_1,0,strrpos($niveles_act_1, "-"));
				//y se le adiciona la actividad de la division de destino
				$nivel_div1=$nivel_div1."-".$DI;
	
				//actualizamos la division de destino, con los datos de la de origen
				$sql_up_act2="update Actividades set macroactividad='".$macro_act_1."', dependeDe=".$depende_act_1.",";
				 $sql_up_act2=$sql_up_act2. "nivelesActiv='".$nivel_div1."'";
				$sql_up_act2=$sql_up_act2." where id_proyecto=".$cualProyecto." and id_actividad=".$DI." and actPrincipal=".$cualLC." and tipoActividad=3";
//	echo "5 ".$sql_up_act2."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
				$cur_up_act2=mssql_query($sql_up_act2);
				if  (trim($cur_up_act2) == "")  
				{
					$error="si";
				}		
				
				//si no se presentan inconvenientes, se actualizan las actividades de la division	
				else
				{
						//consultamos las actividades del la division
						$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$id_act_2." and nivel=4";
						$sql_act=$sql_act."order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cursor_act=mssql_query($sql_act);
//	echo "5.0 ".$sql_act."   --  ".mssql_get_last_message()."<br><br>";
						$macro_act_4=$macro_act_1.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
						$niveles_act_4=$nivel_div1."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
						$num_act=1;
						while($datos_act=mssql_fetch_array($cursor_act))
						{
							//formamos la macro actividad y el nivel de las actividades a intercambiar
							$macro_act_41=$macro_act_4.$num_act;
							$niveles_act_41=$niveles_act_4.$datos_act["id_actividad"];
							//actualizamos las actividades - sub actividades, etc, de la division
							$sql_up_act4="update Actividades set macroactividad='".$macro_act_41."',";
							 $sql_up_act4=$sql_up_act4. "nivelesActiv='".$niveles_act_41."' ";
							$sql_up_act4=$sql_up_act4." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
				
							$cur_up_act4=mssql_query($sql_up_act4);
							if  (trim($cur_up_act4) == "")  
							{
								$error="si";
							}	
							$num_act++;
//	echo "6 ".$sql_up_act4."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
						}					
				}
//			}
			}

//echo $sql_up_act2."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		}
			
	//si la operacion seleccionada es mover
	if($operacion==2)
	{
			$error="no";
			$ban_error="si";

			//consultamos la division de  origen, para el movimiento
			$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and actPrincipal=".$cualLC." and tipoActividad=3";
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
				$division_act_1=$datos_inter1["id_division"];
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

			//consultamos si el lote de trabajo de destino, contiene una division con el mismo nombre, que la de origen
			$sql_div_lt="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and id_division=".$division_act_1." and dependeDe=".$LT." and tipoActividad=3";
			$cur_div_lt=mssql_query($sql_div_lt);
			if  (trim($cur_div_lt) == "")  
			{
				$error="si";
			}	
//echo "-4 ".$sql_div_lt."  --  ".mssql_get_last_message()." <br><br>";
			//si se encontraron registros, se almacena la macro actividad de la division coincidente, para mas adelante, actualizar las actividades de la division a mover
			if($datos_div_lt=mssql_fetch_array($cur_div_lt))
			{
				//obtenemos la macro-actividad  y el id_actividad de destino
				$macro_div_lt=$datos_div_lt["macroactividad"];
				$id_div_lt=$datos_div_lt["id_actividad"];
				$nivel_div_lt=$datos_div_lt["nivelesActiv"];
				
				$macro_max_div=0;//se inicializa la variabel, que va a contener el ultimo numero de las actividades de la division LT1.1.13.A.(1), que se traspasaran

				/// se consulta la actividad con la mayor  macroactividad de la division de destino, y se extrahe el ultimo numero LT1.1.1.A.(1)
				$sql_macro_max_div="select MAX(cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)) as macro_max 
				from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$id_div_lt;
				$cur_macro_max_div=mssql_query($sql_macro_max_div);
				if  (trim($cur_macro_max_div) == "")  
				{
					$error="si";
				}	
				if($datos_macro_max_div=mssql_fetch_array($cur_macro_max_div))
				{
					$macro_max_div=$datos_macro_max_div["macro_max"];	
				}
//echo "-3 ".$sql_macro_max_div."  --  ".mssql_get_last_message()." -- $macro_max_div <br><br>";
				//consultamos las actividades del la division de origen, para actualizarlas
				$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDIvision." and nivel=4";
				$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cursor_act=mssql_query($sql_act);
				if  (trim($cursor_act) == "")  
				{
					$error="si";
				}	
				$macro_act_3=$macro_div_lt.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
//				$niveles_act_3=$nivel_div_lt."-";  //inicializamos la variable niveles, con el nivel de la division  de destino

//echo "-2 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
				$num_act=$macro_max_div+1;
//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	

				//actualizamos las actividades de la division, y las asociamos al de la division de destino
				while($datos_act=mssql_fetch_array($cursor_act))
				{
						//formamos la macro actividad y el nivel de las actividades a intercambiar
						$macro_act_31=$macro_act_3.$num_act;
						$niveles_act_31=$nivel_div_lt."-A-".$datos_act["id_actividad"];
						//actualizamos las actividades - sub actividades, etc, de la division
						$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$LC.",";
						 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."', dependeDe=".$id_div_lt;
						$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";

						$cur_up_act3=mssql_query($sql_up_act3);
						if  (trim($cur_up_act3) == "")  
						{
							$error="si";
						}	
						$num_act++;
//echo "-1 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
				}

				//se elimina el registro de la actividad de origen
				$sql_del_div="delete from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and id_actividad=".$cualDIvision." and tipoActividad=3";
				$cur_del_div=mssql_query($sql_del_div);

				if  (trim($cur_del_div) == "")  
				{
					$error="si";
				}				
//echo "0 ".$sql_del_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";	

			}
			//si en el lote de trabajo no existe una division  igual a la que se va a mover, entonces
			else
			{
		
				//si la division seleccionada como destino, es null, es por que el lote de control de destino, no contiene divisiones
				if(trim($DI)!="")
				{
					//consultamos la division destino, para el movimiento
					$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$DI." and actPrincipal=".$LC." and tipoActividad=3";
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
		//echo "<br> 1 ".$sql_inter1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		
		
		
					 //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.(2) de destino
					$num2=substr($macro_act_2,strrpos($macro_act_2, ".")+1,strlen($macro_act_2));
		//echo "macro 1 ".$macro_act_1." - ".$num1;
		//echo " macro 1 ".$macro_act_2." - ".$num2;
					$num2++;
		
		
		//			 //permite identificar si se esta recorriendo la actividad donde se va a traspasar la actividad, y asi modificar las actividades que estan por debajo
					//consultamos las actividades de la division, donde se va a mover la actividad destino
		//			$sql_activi="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4 and ".$AC." < id_actividad order by macroactividad";
					$sql_activi="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$LT." and tipoActividad=3 and  cast (reverse(substring(reverse('".$macro_act_2."'),1,charindex('.', reverse('".$macro_act_2."'))-1)) as int )<
								cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					//CONSULTA SQL 
					//replace(sbustring(primero voltiamos la macro actividad), (indicamos la posicion inicial),( dentro del char index, indicamos que voltee la macro y que
					//busque la posicion donde esta ubucado el primer '.', que seria la posicion final del substring)) y voltiamos el valor a su pocision original
					//y luego lo convertimos a int, para compararlos
		
					$cur_activi=mssql_query($sql_activi);
//		echo "<br> 1 ".$sql_activi."  --  ".mssql_get_last_message()." ".$error."<br><br>";
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
							$nivel_div2=$nivel_div2."-".$cualDIvision;
		
							//descomponemos la macro actividad de la division de destion, con el fin de añadirle el ultimo numero que identificar a la division a mover
							$macro_act2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
		
							//actualizamos la division de origen, intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
							$sql_up_act="update Actividades set macroactividad='".$macro_act2."', dependeDe=".$depende_act_2.",";
							 $sql_up_act=$sql_up_act. "nivelesActiv='".$nivel_div2."' , actPrincipal=".$act_prin_act_2;
							$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and actPrincipal=".$cualLC." and tipoActividad=3";
							$cur_up_act=mssql_query($sql_up_act);
//		echo "2 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							if  (trim($cur_up_act) == "")  
							{
								$error="si";
							}
							//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
							else
							{
								//consultamos las actividades del la division de origen, para actualizarlas
								$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDIvision." and nivel=4";
								$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
			
								$cursor_act=mssql_query($sql_act);
								$macro_act_3=$macro_act2.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
								$niveles_act_3=$niveles_act_2."-A-";  //inicializamos la variable niveles, con el nivel de la division  de destino
			
//			echo "3.0 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
								$num_act=1;
			//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
								while($datos_act=mssql_fetch_array($cursor_act))
								{
										//formamos la macro actividad y el nivel de las actividades a intercambiar
										$macro_act_31=$macro_act_3.$num_act;
										$niveles_act_31=$nivel_div2."-A-".$datos_act["id_actividad"];
										//actualizamos las actividades - sub actividades, etc, de la division
										$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$act_prin_act_2.",";
										 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
										$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
				
										$cur_up_act3=mssql_query($sql_up_act3);
										if  (trim($cur_up_act3) == "")  
										{
											$error="si";
										}	
										$num_act++;
//			echo "4 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
								}
							}
							$band=1;
		//echo "$band <br> 3 ".$sql_up_act3."   --  ".mssql_get_last_message()." ".$error."<br><br>";
						}
		 
						$num2++;
		
						$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
		//echo "$macro2<br>";
		
						//actualizamos las divisiones de destino, que estan por debajo intercambiando la informacion correspondiente, y dejando intacto el id de la actividad
						$sql_up_act="update Actividades set macroactividad='".$macro2."'";
						$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=3";
		
						$cur_up_act=mssql_query($sql_up_act);
//		echo "5 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
						if  (trim($cur_up_act) == "")  
						{
							$error="si";
						}
						//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
						else
						{
		
							//consultamos las actividades del la division de destino, para actualizarlas
							$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$datos_activi["id_actividad"]." and nivel=4";
							$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
		
							$cursor_act=mssql_query($sql_act);
							$macro_act_3=$macro2.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
		
//		echo "6.0 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
							$num_act=1;
		//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
							while($datos_act=mssql_fetch_array($cursor_act))
							{
									//formamos la macro actividad y el nivel de las actividades a intercambiar
									$macro_act_31=$macro_act_3.$num_act;
									//actualizamos las actividades - sub actividades, etc, de la division
									$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."'";
									$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]."  and dependeDe=".$datos_activi["id_actividad"]." and actPrincipal=".$LC." and tipoActividad=4";
			
									$cur_up_act3=mssql_query($sql_up_act3);
									if  (trim($cur_up_act3) == "")  
									{
										$error="si";
									}	
									$num_act++;
//		echo "7 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
							}
						}
		
		
					} //del while de las divisiones de destino
				} //del if DI!=""
					$band_reg_encon="si"; //esta variable nos permite saber, si se encontraron registros de las actividades asociadas  y/o no es la ultima actividad de la division del lote de control, a donde se movera la actividad
		
					//si no se encontraron registros en la consulta de las actividades de la division de origen, y no se presentaron errores, quiere decir 2 cosas:
					//1. que la el lote de trabajo no coneiene divisiones asociadas
					//2. que ladivision seleccionada como destino, no tiene divisiones por debajo
		
					if(($cant_reg==0)and ($ban_error=="no"))
					{
						$band_reg_encon="no";
						//consultamos si el lote de trabajo de destino, contiene divisiones asociadas
						$sql_activi_div="select * from Actividades where id_proyecto=".$cualProyecto." and  actPrincipal=".$LC." and dependeDe=".$LT." and tipoActividad=3 ";
						$cur_activi_div=mssql_query($sql_activi_div);
		
	//	echo "<br> 8 ".$sql_activi_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		
						if  (trim($cur_activi_div) == "")  
						{
							$error="si";
						}
						$cant_reg_acti_div=mssql_num_rows($cur_activi_div);  //almacenamos la cantidad de divisiones encontradas
		
						//consultamos el id del lote de trabajo de destino a donde se movera la division 
						$sql_lt2="select * from Actividades where id_proyecto=".$cualProyecto." and  actPrincipal=".$LC." and id_actividad=".$LT." and tipoActividad=2 ";
						$cur_lt2=mssql_query( $sql_lt2);
						if  (trim($cur_lt2) == "")  
						{
							$error="si";
						}
	//	echo "9 ". $sql_lt2."  -- $error ".mssql_get_last_message()."<br><br>";
						while($datos_lt2=mssql_fetch_array($cur_lt2))
						{
							$div2=$datos_lt2["id_division"];
							$macro2=$datos_lt2["macroactividad"];
						}
		
						//si no se encontraron registros, quiere indicar que el lote de trabajo  no tiene divisiones
						//situacion 1.
						if($cant_reg_acti_div==0)
						{
		
							//construimos la macro-actividad, de la division a traspasar, con la macro de la division
							$macro_activida=$macro2.".1";
							$niveles_act_2="".$LC."-".$LT."-".$id_act_1; //inicializamos la variable niveles, con el nivel de la division  de destino
							//actualizamos primero la division de origen, con los datos de la division de destino
							$sql_up_act4="update Actividades   set macroactividad='".$macro_activida."', dependeDe=".$LT;
							$sql_up_act4=$sql_up_act4.",nivelesActiv='".$niveles_act_2."',actPrincipal=".$LC; 
							$sql_up_act4=$sql_up_act4." where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and dependeDe=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=3";
							$cur_up_act4=mssql_query($sql_up_act4);
	//	echo "10 ". $sql_up_act4."  -- $error ".mssql_get_last_message()."<br><br>";
							if  (trim($cur_up_act4) == "")  
							{
								$error="si";
							}	
							//si no se presentaron errores, en la actualizacion de la division, se actualiza las actividades asociadas a ellas
							else
							{
								//consultamos las actividades del la division de origen, para actualizarlas
								$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDIvision." and nivel=4";
								$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
			
								$cursor_act=mssql_query($sql_act);
								$macro_act_3=$macro_activida.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
			
	//		echo "11 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
								$num_act=1;
			//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
								while($datos_act=mssql_fetch_array($cursor_act))
								{
										//formamos la macro actividad y el nivel de las actividades a intercambiar
										$macro_act_31=$macro_act_3.$num_act;
										$niveles_act_31=$niveles_act_2."-A-".$datos_act["id_actividad"];
										//actualizamos las actividades - sub actividades, etc, de la division
										$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$LC.",";
										 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
										$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
				
										$cur_up_act3=mssql_query($sql_up_act3);
										if  (trim($cur_up_act3) == "")  
										{
											$error="si";
										}	
										$num_act++;
		//	echo "12 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
								}
							}
							
						}	
			
						//situacion 2.
						//se encontraron registros, pero la division seleccionada como destino, es la ultima del  lote de trabajo
						else
						{
							//extrhemos parte de la macro actividad, y le añadimos el ultimo numero de division de destino, para componer la macro actividad a mover
							$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
		//echo $macro2." -- $num2 <br>";
							$niveles_act_2="".$LC."-".$LT."-".$id_act_1; //inicializamos la variable niveles, con el nivel de la division  de destino, añadiendo el id de la actividad de origen
		
		
							//actualizamos primero la actividad de origen, con los datos de la division de destino
							$sql_up_act5="update Actividades   set macroactividad='".$macro2."', dependeDe=".$LT." ";
							$sql_up_act5=$sql_up_act5.",nivelesActiv='".$niveles_act_2."',actPrincipal=".$LC; 
		
							$sql_up_act5=$sql_up_act5." where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and dependeDe=".$cualLT." and actPrincipal=".$cualLC." and tipoActividad=3";
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
								$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDIvision." and nivel=4";
								$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
			
								$cursor_act=mssql_query($sql_act);
								$macro_act_3=$macro2.".A."; //inicializamos la variable macro, con la macro actividad de la division de destion
			
	//		echo "14 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
								$num_act=1;
			//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
								while($datos_act=mssql_fetch_array($cursor_act))
								{
										//formamos la macro actividad y el nivel de las actividades a intercambiar
										$macro_act_31=$macro_act_3.$num_act;
										$niveles_act_31=$niveles_act_2."-A-".$datos_act["id_actividad"];
										//actualizamos las actividades - sub actividades, etc, de la division
										$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."', actPrincipal=".$LC.",";
										 $sql_up_act3=$sql_up_act3. "nivelesActiv='".$niveles_act_31."' ";
										$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
				
										$cur_up_act3=mssql_query($sql_up_act3);
										if  (trim($cur_up_act3) == "")  
										{
											$error="si";
										}	
										$num_act++;
	//		echo "15 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
								}
							}
		
						}
				
					}	
			} //del else que idndica si no se encontro una division igual
	

			//consultamos las divisiones que estan por debajo de la division de origen, para actualizar la macro actividad  de cada una de ellas
			 $sql_activi="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualLT." and tipoActividad=3 and  cast (reverse(substring(reverse('".$macro_act_1."'),1,charindex('.', reverse('".$macro_act_1."'))-1)) as int )<
						cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
			$cur_activi=mssql_query($sql_activi);
//echo "15.1 ".$sql_activi."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
			while($datos_activi=mssql_fetch_array($cur_activi))
			{

					$macro_div=substr($macro_act_1,0,strrpos($macro_act_1, ".")+1).$num1;
					$num1++;

					$sql_up_act="update Actividades set macroactividad='".$macro_div."'";
					$sql_up_act=$sql_up_act." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=3";
					$cur_up_act=mssql_query($sql_up_act);
					

					
//echo "16 ".$sql_up_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
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

	
//	echo "17 ".$sql_act."  --  ".mssql_get_last_message()." --  $macro_act_3 <br><br>";
						$num_act=1;
	//echo "3 ".$sql_act."  --  ".mssql_get_last_message()." ".$error."<br><br>";	
						while($datos_act=mssql_fetch_array($cursor_act))
						{
								//formamos la macro actividad y el nivel de las actividades a intercambiar
								$macro_act_31=$macro_act_3.$num_act;
								//actualizamos las actividades - sub actividades, etc, de la division
								$sql_up_act3="update Actividades set macroactividad='".$macro_act_31."'";
								$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_act["id_actividad"]." and actPrincipal=".$cualLC." and tipoActividad=4";
		
								$cur_up_act3=mssql_query($sql_up_act3);
								if  (trim($cur_up_act3) == "")  
								{
									$error="si";
								}	
								$num_act++;
//	echo "18 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";				
						}
					}
			}		
	}

	if  ((trim($error)=="no"))  {
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
	} 
	else {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		//si se mostro el mensaje de las divisiones conincidentes, no se muestra el mensaje de error
		if($ban_encon=="no")
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
    <td class="TituloUsuario">Intercambiar/Mover Divisi&oacute;n </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
<?php

//consultamos la division del lote de trabajo, para trher el depende y asi consultar el lote de trabajo asociado
	$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDIvision;
	$cur_a=mssql_query($sql_a);	
	if($datos_lts=mssql_fetch_array($cur_a))	
		$dep_div=$datos_lts["dependeDe"];

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

	$sql_Lt="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=".$dep_div;
	$cur_Lt=mssql_query($sql_Lt);	
?>
		<tr>
          <td class="TituloTabla">Lote de trabajo Actual</td>
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
	$sql_Ld="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDIvision;
//echo $sql_Ld;
	$cur_Ld=mssql_query($sql_Ld);	
?>
		<tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n Actual</td>
          <td class="TxtTabla">
<?php
			if($datos_Ld=mssql_fetch_array($cur_Ld))
			{
				echo strtoupper($datos_Ld["macroactividad"]." - ".$datos_Ld["nombre"]);
				$div_id=$datos_Ld["id_actividad"];
				$id_div=$datos_Ld["id_division"];
				$div_encargado= $datos_Ld["id_encargado"]; 
				$inf_div=strtoupper($datos_Ld["macroactividad"]." - ".$datos_Ld["nombre"]);
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
                        from usuarios U	where unidad='".$div_encargado."'  and retirado is null" ;
        
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
          <td class="TxtTabla"><input name="operacion" type="radio" id="operacion" value="1" <?php if(($operacion==1)){ echo "checked";  } ?>   onClick="document.Form1.submit();">
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

					if(!isset($DI))
					{
							$DI=$cualDIvision;
					}
					if(!isset($cualLDI))
					{
							$cualLDI=$cualDiv;

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
            <option value=""> </option>
            <?php
					//consultamos los lotes de control asociados a el lote de control seleccionado
					$sql_LT="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto =".$cualProyecto." and dependeDe=".$LC." and nivel = 2";
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
        <tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n</td>
          <td class="TxtTabla">
		<select name="DI" id="DI"   class="CajaTexto"  onChange="document.Form1.submit();">

            <?php
					$divi_selec=""; //almacenamos la division (Hoja de tiempo) correspondiente a la Division (EDT) asociada al lote de trabajo, para almacenarla en el campo id_division, y asi referenciar las actividades por division
					//consultamos las divisiones  asociados al lote de trabajo
					$sql_DI="SELECT  id_actividad,upper(nombre) as nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$LT." and actPrincipal=".$LC." and nivel = 3 and id_actividad <>".$cualDIvision;
					$sql_DI=$sql_DI." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";			
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
<?php // echo $sql_DI." - ".mssql_get_last_message(); ?>
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
  		    <input name="cualLDI" type="hidden" id="cualLDI" value="<?php echo $cualLDI; ?>">
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
		
			else if(document.getElementById("DI").value=="")
			{
				alert ('Seleccione un a division');
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
		
			else if(document.getElementById("LT").value=="")
			{
				alert ('Seleccione un lote de trabajo');
			}	

			else if(document.getElementById("LT").value=="<?php echo $cualLT; ?>")
			{
				alert ('Para mover la divisi\xf3n <?php echo $inf_div; ?>, por favor seleccione un lote de trabajo diferente.');
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
