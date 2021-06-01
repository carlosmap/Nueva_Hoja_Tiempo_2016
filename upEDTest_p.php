<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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



//CONSULTA EL VALOR ASIGNADO A LA DIVISION SELECCIONADO (DESTINO) EN EL (INTERCAMBIO/MOVIMIENTO)
$sql_valor_division=" SELECT  valorActiv from HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto = ".$cualProyecto." and id_actividad=".$DI."";
$cur_valor_division=mssql_query($sql_valor_division);
if($datos_valor_division=mssql_fetch_array($cur_valor_division))
	$valor_division_destino=$datos_valor_division["valorActiv"];

echo " valor division destino ".$sql_valor_division." <br> --".mssql_get_last_message()."   --- valor division $valor_division_destino <br><br>";

//CONSULTA EL VALOR ASINADO DE LA ACTIVIDAD DE ORIGEN
$cur_valor_act_origen=mssql_query("select *  FROM HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto=".$cualProyecto."  and id_actividad=".$cualACtividad);
if($datos_act_origen=mssql_fetch_array($cur_valor_act_origen))
	$valor_actividad_origen=$datos_act_origen["valorActiv"];

echo " valor actividad origen select *  FROM HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto=$cualProyecto and id_actividad=$cualACtividad <br> --".mssql_get_last_message()."   --- valor actividad de origen $valor_actividad_origen <br><br>";

if(trim($recarga) == "2")
{
//echo $operacion;


		//si la operacion seleccionada es de intercambio
		if($operacion==1)
		{	
			//CONSULTA EL VALOR ASIGNADO A LA DIVISION (ORIGEN) 
			$sql_valor_division=" SELECT  valorActiv from HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto = ".$cualProyecto." and id_actividad=".$cualDiv."";
			$cur_valor_division=mssql_query($sql_valor_division);
			if($datos_valor_division=mssql_fetch_array($cur_valor_division))
				$valor_division_origen=$datos_valor_division["valorActiv"];
		
		echo " valor  division origen ".$sql_valor_division." <br> --".mssql_get_last_message()."   --- valor actividades  de origen $valor_division_origen <br><br>";
		
		
			//CONSULTA EL VALOR ASINADO DE LA ACTIVIDAD DE DESTINO
			$cur_valor_act_origen=mssql_query("select *  FROM HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto=".$cualProyecto."  and id_actividad=".$AC);
			if($datos_act_origen=mssql_fetch_array($cur_valor_act_origen))
				$valor_actividad_destino=$datos_act_origen["valorActiv"];
		
		echo " valor  actividad de destino ".$cur_valor_act_origen." <br> --".mssql_get_last_message()."   --- valor actividad de destino $valor_actividad_destino <br><br>";
		
			//consulta el valor total de las actividades asociadas a la division de destino, sin tener en cuenta la actividad seleccionada como destino
			$valor_actividades_division=0;
			$sql_valor_actividades_division="
				SELECT SUM(valorActiv) sumaDI
				FROM HojaDeTiempo.dbo.ActividadesRecursos 
				WHERE id_proyecto = ".$cualProyecto."
				AND id_actividad IN (
					  SELECT id_actividad
					  FROM HojaDeTiempo.dbo.Actividades
					  WHERE id_proyecto = ".$cualProyecto."
						 and actPrincipal=".$cualLC."
					  AND dependeDe = ".$DI."	
					  AND nivel = 4	
					  AND id_actividad <> ".$AC."
				)";
			$cur_valor_division=mssql_query($sql_valor_actividades_division);
			if($datos_valor_actividades_division=mssql_fetch_array($cur_valor_division))
			{
				$valor_actividades_division_destino=$datos_valor_actividades_division["sumaDI"];
			}
			if(trim($valor_actividades_division)=="")
				$valor_actividades_division_destino=0;
		
		echo " valor  actividades  de destino ".$sql_valor_actividades_division." <br> --".mssql_get_last_message()."   --- valor actividades de destino $valor_actividades_division_destino <br><br>";
		
			//consulta el valor total de las actividades asociadas a la division de origen , sin tener en cuenta la actividad seleccionada como origen
			$valor_actividades_division=0;
			$sql_valor_actividades_division="
				SELECT SUM(valorActiv) sumaDI
				FROM HojaDeTiempo.dbo.ActividadesRecursos 
				WHERE id_proyecto = ".$cualProyecto."
				AND id_actividad IN (
					  SELECT id_actividad
					  FROM HojaDeTiempo.dbo.Actividades
					  WHERE id_proyecto = ".$cualProyecto."
						 and actPrincipal=".$cualLC."
					  AND dependeDe = ".$cualDiv."	
					  AND nivel = 4	
					  AND id_actividad <> ".$cualACtividad."
				)";
			$cur_valor_division=mssql_query($sql_valor_actividades_division);
		
		
		
			if($datos_valor_actividades_division=mssql_fetch_array($cur_valor_division))
			{
				$valor_actividades_division_origen=$datos_valor_actividades_division["sumaDI"];
			}
			if(trim($valor_actividades_division)=="")
				$valor_actividades_division_origen=0;
		
		echo " valor total actividad de origen ".$sql_valor_actividades_division." <br> --".mssql_get_last_message()."   --- valor actividad de origen  $valor_actividades_division_origen <br><br>";
		
			//SUMA EL VALOR DE LAS ACTIVIDADES DE ORIGEN Y LA ACTIVIDAD QUE SE VA A INTERCAMBIAR
			$total_origen=$valor_actividades_division_origen+$valor_actividad_destino;
		
		
			//SUMA EL VALOR DE LAS ACTIVIDADES DE DESTINO Y LA ACTIVIDAD QUE SE VA A INTERCAMBIAR
			$total_destino=$valor_actividades_division_destino+$valor_actividad_origen;
		
		echo " valor que quedaria division de origen ".$total_origen." ($valor_actividades_division_origen+$valor_actividad_destino) <br> --".mssql_get_last_message()."   --- valor que quedaria la  division de destino  $total_destino   ($valor_actividades_division_destino+$valor_actividad_origen)<br><br>";
		
			// SI AL SUMAR LAS ACTIVIDADES DE LA DIVISION DE ORIGEN, ESTA ES MAYOR A EL VALOR ASIGNADO A LA DIVISION DE ORIGEN
			if(($total_origen>$valor_division_origen)or($total_destino>$valor_division_destino))
				echo "<script> alert('No se puede intercambiar las actividades, por que la sumatoria de ellas, supera el valor asignado de alguna de las divisiones asociadas.')</script>";		

			else // SI LOS VALORES SE ADAPTAN AL VALOR ASIGNADO A LAS DIVIDISIONES ASOCIADAS, SE PROCEDE AL INTERCAMBIO
			{			

				$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
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
	
				}
				//consultamos la actividad destino, para el intercambio
				$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$AC." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4";
				$cur_inter2=mssql_query($sql_inter2);
				if  (trim($cur_inter2) == "")  
				{
					$error="si";
				}	
	//echo "001 ".$sql_inter2."<br>";
				while($datos_inter2=mssql_fetch_array($cur_inter2))
				{
					//almacenmos la informacion de la actividad de destino, para ulizarla, al momento de actualizar la actividad de origen, que es donde quedara asociada la actividad
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

			}
	
	//echo $sql_up_act2."  --  ".mssql_get_last_message()." ".$error."<br><br>";
		}
			
		//si la operacion seleccionada es mover
		if($operacion==2)
		{
		
		
			//consulta el valor total de las actividades asociadas a la division de destino
			$valor_actividades_division=0;
			$sql_valor_actividades_division="
				SELECT SUM(valorActiv) sumaDI
				FROM HojaDeTiempo.dbo.ActividadesRecursos 
				WHERE id_proyecto = ".$cualProyecto."
				AND id_actividad IN (
					  SELECT id_actividad
					  FROM HojaDeTiempo.dbo.Actividades
					  WHERE id_proyecto = ".$cualProyecto."
						 and actPrincipal=".$LC."
					  AND dependeDe = ".$DI."	
					  AND nivel = 4	
				)";
			$cur_valor_division=mssql_query($sql_valor_actividades_division);
			if($datos_valor_actividades_division=mssql_fetch_array($cur_valor_division))
			{
				$valor_actividades_division_destino=$datos_valor_actividades_division["sumaDI"];
			}
			if(trim($valor_actividades_division)=="")
				$valor_actividades_division_destino=0;
		
		
		
			//SUMA EL VALOR DE LA ACTIVIDADES DE LA DIVISION DE DESTINO Y  EL VALOR DE LA ACTIVIDAD DE ORIGEN
			$nuevo_total_div_destino=$valor_actividades_division_destino+$valor_actividad_origen;
		
		echo " valor sumatoria actividad division destino ".$sql_valor_actividades_division." <br> --".mssql_get_last_message()."   --- valor actividades  de destino $valor_actividades_division_destino <br><br> valor que quedara la division de destino $nuevo_total_div_destino -- valor actual division de destino $valor_division_destino<br><br>";
		
			//SI EL NUEVO VALOR DE LA DIVISION, SUPERA EL VALOR ASIGNADO A LA DIVISION, MUESTRA EL MENSAJE
			if($nuevo_total_div_destino>$valor_division_destino)
			{
		
				echo "<script> alert('No se puede mover la actividad, a la division seleccionada, por que la sumatoria de las actividades, supera el valor asignado a la division ')</script>";
			}
			else ////SI ES INFERIOR O IGUAL, PERMITE EL MOVIMIENTO
			{
				$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
				$error="no";
				$ban_error="si";
	
				//consultamos la actividad, de destion, para obtener la macro actividad, que se utiliza, para listar las actividades que estan por debajo
				$sql_inter2="select * from Actividades where id_proyecto=".$cualProyecto;
				//si la division del lote de trabajo de destino, contiene actividades, adiciona a la consulta el id de la actividad seleccionada
				if(trim($AC)!="")
				{
					$sql_inter2=$sql_inter2."and id_actividad=".$AC;
				}
				$sql_inter2=$sql_inter2." and actPrincipal=".$LC." and dependeDe=".$DI." and tipoActividad=4";
				$cur_inter2=mssql_query($sql_inter2);
				if  (trim($cur_inter2) == "")  
				{
					$error="si";
				}	
				$act_desti_encontra="si"; //permite identificar, si la division de destino contiene actividades
	//echo $sql_inter2."<br>";
	//echo "<br> -0".$sql_inter2."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				if($datos_inter2=mssql_fetch_array($cur_inter2))
				{
					//almacenmos la informacion de la actividad de destino
					$macro_act_2=$datos_inter2["macroactividad"];
					$niveles_act_2=$datos_inter2["nivelesActiv"];
					$depende_act_2=$datos_inter2["dependeDe"];
					$act_prin_act_2=$datos_inter2["actPrincipal"];
					$division_act_2=$datos_inter2["id_division"];
				}
	/*
				//si la division no tiene actividades asociadas
				else
				{
					$act_desti_encontra="no"; 
				}
	*/
				//consultamos la actividad seleccionada en la pagina de la EDT (origen), para moverla
				$sql_inter1="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and actPrincipal=".$cualLC." and dependeDe=".$div_id." and tipoActividad=4";
				$cur_inter1=mssql_query($sql_inter1);
				if  (trim($cur_inter1) == "")  
				{
					$error="si";
				}	
	//echo "<br>0 ".$sql_inter1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
	//echo "<br> 0".$sql_inter1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				while($datos_inter1=mssql_fetch_array($cur_inter1))
				{
					//almacenmos la informacion de la actividad (origen) a mover, para ulizarla, al momento de actualizar las actividades que estan por debajo de la actividad  de destino
					$macro_act_1=$datos_inter1["macroactividad"];
					$niveles_act_1=$datos_inter1["nivelesActiv"];
					$depende_act_1=$datos_inter1["dependeDe"];
					$act_prin_act_1=$datos_inter1["actPrincipal"];
					$division_act_1=$datos_inter1["id_division"];
					$id_act_1=$datos_inter1["id_actividad"];
					$encargado_act_1=$datos_inter1["id_encargado"];
	
				}
	//echo "<br> 1 ".$sql_inter1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
	
	
				 //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2.A.(2) de origen
				$num1=substr($macro_act_1,strrpos($macro_act_1, ".")+1,strlen($macro_act_1));
	
			//si la division de destino contiene actividades asociadas
	//		if($act_desti_encontra=="si")
	//		{
				 //almacenamos el ultimo numero, despues del ultimo punto, que identifica la actividad LT2.2.2.A.(2) de destion
				$num2=substr($macro_act_2,strrpos($macro_act_2, ".")+1,strlen($macro_act_2));
	//echo "macro 1 ".$macro_act_1." - ".$num1;
	//echo " macro 1 ".$macro_act_2." - ".$num2;
				$num2++;
	
	//			 //permite identificar si se esta recorriendo la actividad donde se va a traspasar la actividad, y asi modificar las actividades que estan por debajo
				//consultamos las actividades de la division, donde se va a mover la actividad destino
	//			$sql_activi="select * from Actividades where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$DI." and tipoActividad=4 and ".$AC." < id_actividad order by macroactividad";
				$sql_activi="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$LC." and dependeDe=".$DI." and tipoActividad=4 and  cast (reverse(substring(reverse('".$macro_act_2."'),1,charindex('.', reverse('".$macro_act_2."'))-1)) as int )<
							cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				//CONSULTA SQL 
				//replace(sbustring(primero voltiamos la macro actividad), (indicamos la posicion inicial),( dentro del char index, indicamos que voltee la macro y que
				//busque la posicion donde esta ubucado el primer '.', que seria la posicion final del substring)) y voltiamos el valor a su pocision original
				//y luego lo convertimos a int, para compararlos
	
				$cur_activi=mssql_query($sql_activi);
	//echo "<br> 2 ".$sql_activi."  --  ".mssql_get_last_message()." ".$error."<br><br>";
	//echo "<br>".$macro_act_1." - ".$depende_act_1." - ".$division_act_1." - ".$niveles_act_1." - ".$act_prin_act_1." - ".$nombre_act_1."<br>";
	//echo "1 ". $sql_activi."  --  ".mssql_get_last_message()." ".$error."  --  ".$cur_activi."<br><br>";
				if  (trim($cur_activi) == "")  
				{
					$error="si";
				}	
				else
				{
					$ban_error="no";  //con ban_error identificamos si  la consulta no ha presentado errores
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
	
						//alamacenamos la macro actividad de la actividad a mover(origen), y el nivel
	
						//extrhemos parte de la macro actividad, y le añadimos el ultimo numero de actividad de destino, para componer la macro actividad a mover
						$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
	
						$niveles_act_2="".$LC."-".$LT."-".$DI."-A-".$id_act_1;
						//actualizamos primero la actividad de origen, con los datos de la division de destino
						$sql_up_act3="update Actividades   set macroactividad='".$macro2."', dependeDe=".$depende_act_2.", id_division=".$division_act_2." ";
						$sql_up_act3=$sql_up_act3.",nivelesActiv='".$niveles_act_2."',actPrincipal=".$act_prin_act_2; 
						//si las divisiones son diferentes, el encargado se actualiza como null
						if($division_act_2!=$division_act_1)
							$sql_up_act3=$sql_up_act3.", id_encargado=NULL";
						$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$id_act_1." and dependeDe=".$cualDiv." and actPrincipal=".$cualLC." and tipoActividad=4";
						$cur_up_act3=mssql_query($sql_up_act3);
	
						if  (trim($cur_up_act3) == "")  
						{
							$error="si";
						}	
						$band=1;
	//echo "$band <br> 3 ".$sql_up_act3."   --  ".mssql_get_last_message()." ".$error."<br><br>";
					}
	
					$num2++;
	
					$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
	//echo "$macro2<br>";
	
	//				$macro2="LT".$LC.".".$LT.".".$DI.".A.".$num2;
						
						//actualizamos la moacro-actividad de las actividades que estan por debajo de la actividad de destino
						$sql_up_act3="update Actividades  set macroactividad='".$macro2."'";
						$sql_up_act3=$sql_up_act3." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi["id_actividad"]." and dependeDe=".$datos_activi["dependeDe"]." and actPrincipal=".$datos_activi["actPrincipal"]." and tipoActividad=4";
						$cur_up_act3=mssql_query($sql_up_act3);
	
						if  (trim($cur_up_act3) == "")  
						{
							$error="si";
						}	
	//echo "$band <br> 4 ".$sql_up_act3."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				}
	
	
				$band_reg_encon="si"; //esta variable nos permite saber, si se encontraron registros de las actividades asociadas  y/o no es la ultima actividad de la division del lote de control, a donde se movera la actividad
	
				//si no se encontraron registros en la consulta de las actividades, y no se presentaron errores, quiere decir 2 cosas:
				//1. que la division no tiene actividades asociadas
				//2. que la actividad seleccionada como destino, no tiene actividades por debajo
	
				if(($cant_reg==0)and ($ban_error=="no"))
				{
					$band_reg_encon="no";
					//consultamos si la division destino, tiene actividades asociadas
					$sql_activi_div="select * from Actividades where id_proyecto=".$cualProyecto." and  actPrincipal=".$LC." and dependeDe=".$DI." and tipoActividad=4 ";
					$cur_activi_div=mssql_query($sql_activi_div);
	
	//echo "<br> 5 ".$sql_activi_div."  --  ".mssql_get_last_message()." ".$error."<br><br>";
	
					if  (trim($cur_activi_div) == "")  
					{
						$error="si";
					}
					$cant_reg_acti_div=mssql_num_rows($cur_activi_div);  //almacenamos la cantidad de actividades encontradas
	
					//consultamos el id de la division a la que esta asociado la division del lote de trabajo
					$sql_div2="select * from Actividades where id_proyecto=".$cualProyecto." and  actPrincipal=".$LC." and id_actividad=".$DI." and tipoActividad=3 ";
					$cur_div2=mssql_query( $sql_div2);
					if  (trim($cur_div2) == "")  
					{
						$error="si";
					}
	//echo "6 ". $sql_div2."  -- $error ".mssql_get_last_message()."<br><br>";
					while($datos_div2=mssql_fetch_array($cur_div2))
					{
						$div2=$datos_div2["id_division"];
						$macro2=$datos_div2["macroactividad"];
					}
	
					//si no se encontraron registros, quiere indicar que la division no tiene actividades
					//situacion 1.
					if($cant_reg_acti_div==0)
					{
	
						//construimos la macro-actividad, de la actividad a traspasar, con la macro de la division
						$macro_activida=$macro2.".A.1";
	
						$niveles_act_2="".$LC."-".$LT."-".$DI."-A-".$id_act_1;
						//actualizamos primero la actividad de origen, con los datos de la division de destino
						$sql_up_act4="update Actividades   set macroactividad='".$macro_activida."', dependeDe=".$DI.", id_division=".$div2." ";
						$sql_up_act4=$sql_up_act4.",nivelesActiv='".$niveles_act_2."',actPrincipal=".$LC; 
	
						if(($div2!=$division_act_1)or(trim($encargado_act_1)==""))
							$sql_up_act4=$sql_up_act4.", id_encargado=NULL";
	
						$sql_up_act4=$sql_up_act4." where id_proyecto=".$cualProyecto." and id_actividad=".$id_act_1." and dependeDe=".$cualDiv." and actPrincipal=".$cualLC." and tipoActividad=4";
						$cur_up_act4=mssql_query($sql_up_act4);
	
	//echo "7 ". $sql_up_act4."  -- $error ".mssql_get_last_message()."<br><br>";
	
						if  (trim($cur_up_act4) == "")  
						{
							$error="si";
						}	
						
					}		
					//situacion 2.
					//se encontraron registros, pero la actividad seleccionada como destino, es la ultima de la division del lote de trabajo
					else
					{
	//					$num2++;//incrementamos el valor de el ultimo numero de la actividad de destion, para actualizar la macroactividad que se va a mover
	
						//extrhemos parte de la macro actividad, y le añadimos el ultimo numero de actividad de destino, para componer la macro actividad a mover
						$macro2=substr($macro_act_2,0,strrpos($macro_act_2, ".")+1).$num2;
	//echo $macro2." -- $num2 <br>";
						$niveles_act_2="".$LC."-".$LT."-".$DI."-A-".$id_act_1;
						//actualizamos primero la actividad de origen, con los datos de la division de destino
						$sql_up_act5="update Actividades   set macroactividad='".$macro2."', dependeDe=".$depende_act_2.", id_division=".$division_act_2." ";
						$sql_up_act5=$sql_up_act5.",nivelesActiv='".$niveles_act_2."',actPrincipal=".$act_prin_act_2; 
						//si las divisiones son diferentes, el encargado se actualiza como null
						if(($div2!=$division_act_1)or(trim($encargado_act_1)==""))
							$sql_up_act5=$sql_up_act5.", id_encargado=NULL";
						$sql_up_act5=$sql_up_act5." where id_proyecto=".$cualProyecto." and id_actividad=".$id_act_1." and dependeDe=".$cualDiv." and actPrincipal=".$cualLC." and tipoActividad=4";
						$cur_up_act5=mssql_query($sql_up_act5);
	
						if  (trim($cur_up_act5) == "")  
						{
							$error="si";
						}	
	
	//echo "$band <br> 7.1 ".$sql_up_act5."   --  ".mssql_get_last_message()." ".$error."<br><br>";					
					}
			
				}			
	
	
				//consultamos las actividade que estan por debajo de la actividad de origen, para actualizar la macro actividad  de cada una de ellas
				$sql_activi1="select * from Actividades  where id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC." and dependeDe=".$cualDiv." and tipoActividad=4 and  cast (reverse(substring(reverse('".$macro_act_1."'),1,charindex('.', reverse('".$macro_act_1."'))-1)) as int )<
							cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
				$cur_activi1=mssql_query($sql_activi1);
	//echo "<br> 8 ".$sql_activi1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
				if  (trim($cur_activi1) == "")  
				{
					$error="si";
				}				
				while($datos_activi1=mssql_fetch_array($cur_activi1))
				{
						//formamos la macro actividad con el ultimo num de la actividad a mover
						$macro1=$macro_div_origen.".A.".$num1;
						//actualizamos la moacro-actividad de las actividades que estan por debajo de la actividad de origen
						$sql_up_act1="update Actividades set  macroactividad='".$macro1."'";
						$sql_up_act1=$sql_up_act1." where id_proyecto=".$cualProyecto." and id_actividad=".$datos_activi1["id_actividad"]." and dependeDe=".$datos_activi1["dependeDe"]." and actPrincipal=".$datos_activi1["actPrincipal"]." and tipoActividad=4";
						$cur_up_act1=mssql_query($sql_up_act1);
	//echo "<br> 6 ".$sql_up_act1."  --  ".mssql_get_last_message()." ".$error."<br><br>";
						if  (trim($cur_up_act1) == "")  
						{
							$error="si";
						}	
						$num1++;
				}	

			}		

		}

	if  (trim($error)=="no")  {
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
	} 
	if  (trim($error)=="si") {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
	}

	//SI ERROR ES DIFERENTE DE NULL, ES, POR QUE SE HA REALIZADO LA OPERACION DE MOVIMIENTO O INTERCAMBIO, SI NO ES POR QUE NO HA CUMPLIDO LA VALIDACION, DE INTERCAMBIO/MOVIMIENTO
	if  (trim($error)!="") 
	{
		echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
	}

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
    <td class="TituloUsuario">Intercambiar/Mover Actividad</td>
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
				echo $datos_Lt["macroactividad"]." - ".$datos_Lt["nombre"];
				$cualLT=$datos_Lt["id_actividad"];
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
				echo strtoupper($datos_Ld["macroactividad"]." - ".$datos_Ld["nombre"]);
				$div_id=$datos_Ld["id_actividad"];
				$id_div=$datos_Ld["id_division"];
				$macro_div_origen=$datos_Ld["macroactividad"];
				
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
				$inf_actividad=$datos_a["macroactividad"]." - ".$datos_a["nombre"];
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
                        from usuarios U	where unidad='".$acti_encargado."'  and retirado is null" ;
        
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

<!--
		<tr>
			<td class="TituloTabla">Identificador de la Actividad</td>
			<td class="TxtTabla">

<input name="identificador" type="text" class="CajaTexto" id="identificador" value="<?php // echo $acti_macro; ?>" readonly></td>
		</tr>
-->

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
//cualProyecto=683&cualLC=1&cualDiv=4&cualACtividad=5


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
/*
					//Cargamos el valor del LT, que se trahe como parametro, esta sentencia es verdadera, cuando se accede a la pagina por primera vez
					if(!isset($cualLT2))
					{
							$cualLT2=$cualLT;
					}

*/


					if(!isset($cualLT2))
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
								$cualLT2=$datos_Lt["id_actividad"];
							}	

//							$LT=$cualLT;
					}

					if(!isset($LT))
					{
						$LT=$cualLT2;
					}

					if(!isset($DI))
					{
							$DI=$cualDiv;
					}
					if(!isset($cualLDI))
					{
//echo "entro -- $cualLDI <br>";
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
					$sql_LT=$sql_LT."order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
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
					$sql_DI=$sql_DI."order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";			
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
<!--                <option value=""> </option>
-->
                <?php
					if((trim($DI)!="")and(trim($LC)!=""))
					{
                        $AC_selec=""; 
						//consultamos las actividades asociadas a la division seleccionada
                        $sql_AC="SELECT  id_actividad,nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$DI." and actPrincipal=".$LC." and nivel = 4   and id_actividad <> ".$cualACtividad;
						$sql_AC=$sql_AC." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
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
					}
     ?>
              </select>
              </td>
            </tr>
<?php
//echo $sql_AC."<br>";
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
  		    <input name="cualLT" type="hidden" id="cualLT" value="<?php echo $cualLT; ?>">
  		    <input name="cualLT2" type="hidden" id="cualLT2" value="<?php echo $cualLT2; ?>">
  		    <input name="cualLC2" type="hidden" id="cualLC2" value="<?php echo $cualLC2; ?>">
  		    <input name="cualLDI" type="hidden" id="cualLDI" value="<?php echo $cualLDI; ?>">



  		    <input name="div_id" type="hidden" id="div_id" value="<?php echo $div_id; ?>">
  		    <input name="macro_div_origen" type="hidden" id="macro_div_origen" value="<?php echo $macro_div_origen; ?>">

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
		
			else if(document.getElementById("LT").value=="")
			{
				alert ('Seleccione un lote de trabajo');
			}	
		
			else if(document.getElementById("DI").value=="")
			{
				alert ('Seleccione un a division');
			}

			else if(document.getElementById("DI").value=="<?php echo $cualDiv; ?>")
			{
				alert ('Para mover la actividad <?php echo $inf_actividad; ?>, por favor seleccione una divisi\xf3n diferente.');
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
