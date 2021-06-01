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

//CONSULTA EL VALOR TOTAL DE LAS ACTIVIDADES ASOCIADAS A LA DIVISON
//if(trim($recarga) == "")
{
	$valor_act_division=0;
	
	$sql_total_act_divi="
	select SUM(valorActiv) as total_actividades FROM HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto=".$cualProyecto." and id_actividad in (
	 select id_actividad from Actividades where id_proyecto=".$cualProyecto." and dependeDe=".$cualDIvision." and actPrincipal=".$cualLC." AND nivel = 4)";
	$cur_total_act=mssql_query($sql_total_act_divi);
	if($datos_total_div=mssql_fetch_array($cur_total_act))
		$valor_act_division=$datos_total_div["total_actividades"];
	if(trim($valor_act_division)=="")
			$valor_act_division=0;

}
//echo $valor_act_division." --- ".$sql_total_act_divi;


if(trim($recarga) == "2")
{


	//CONSULTA EL VALOR ASIGANDO A LA DIVISION EN EL PROYECTO
	$sql_val_div="select valorAsignado from AsignaValorDivision where id_proyecto=".$cualProyecto." and id_division=".$division;
	$cur_val_div=mssql_query($sql_val_div);
	if($dato_val_div=mssql_fetch_array($cur_val_div))
		$val_asig_div=$dato_val_div["valorAsignado"];
//echo $sql_val_div."<br>".mssql_get_last_message()." ***************** ".$val_asig_div."<br>";

	//CONSULTA LA SUMATORIA DE LAS DIVISION EN LOS DIFERENTES LOTES DE TRABAJO
	$sql_val_div_lt="select SUM(valor) as valor_divisiones from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_division=".$division." and id_actividad <> ".$cualDIvision;
	$cur_val_div_lt=mssql_query($sql_val_div_lt);
	if($datos_val_lt=mssql_fetch_array($cur_val_div_lt))
		$val_div_lt=$datos_val_lt["valor_divisiones"];

	if(trim($val_div_lt)=="")
		$val_div_lt=0;

//echo $sql_val_div_lt."<br>".mssql_get_last_message()." --- ".$val_asig_div."-".$val_div_lt."<br>";	
	//SUMA EL VALOR TOTAL DE LAS DIVISIONES, MAS EL VALOR DE LA NUEVA DIVISIÓN
	$valor_divisiones_nuevo=$val_div_lt+trim($valor);

//echo "<br> valor div: ".$valor_divisiones_nuevo."----- $val_div_lt+trim($valor)<br><br>";

	//SI EL VALOR ASIGNADO A LA DIVISION ES INFERIOR A LA SUMATORIA TOTAL DE LA DIVISION EN LOS DIFERENTES LOTES DE TRABAJO, MUESTRA EL MENSAJE DE ERROR
	if($val_asig_div<$valor_divisiones_nuevo)
	{

		//co0nsultamos el nombre de la division
		$sql_nom_div="select nombre from Divisiones where  estadoDiv='A' and id_division =".$division;
		$cursor_nom_div=mssql_query($sql_nom_div);
		if($datos_nom_div=mssql_fetch_array($cursor_nom_div))
		{
			$nom_divisions=strtoupper($datos_nom_div["nombre"]);
		}

		//CALCULA EL VALOR QUE SE DEBE ASIGNAR, PARA LA DIVISION EN EL LOTE DE TRABAJO
		$val_asignar=$val_asig_div-$val_div_lt;

		//SI EL VALOR DISPONIBLE PARA LA DIVISIÓN ES 0, ES POR QUE SE HA EXCEDIDO, EL VALOR ASIGNADO
		if($val_asignar!=0)
		{
			$mensaje=$mensaje.'La sumatoria de la division '.$nom_divisions.' en los diferentes lotes de trabajo, no puede superar el valor asignado, asigne un valor igual o inferior a $'.$val_asignar.'.\n';

		}
		else
		{
			$mensaje=$mensaje.'La division '.$nom_divisions.' ha alcansado el tope del valor asignado, en la EDT. \n';						
		}
		echo '<script type="text/javascript" language="JavaScript"> alert("'.$mensaje.'"); </script>';			
	}
	else 
	{
	
		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
		$error="no";

		
		//consulta la secuencia de actualizacion de la actividad
		$sql_secuen="select secuencia,valorActiv from ActividadesRecursos where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision."";
		$cur_secuen=mssql_query($sql_secuen);
		if  (trim($cur_secuen) == "")  
		{
			$error="si";
		}			
		if($dato_secu=mssql_fetch_array($cur_secuen))
		{
			$secu=$dato_secu["secuencia"]; 
			$valo=$dato_secu["valorActiv"]; 
		}

		//co0nsultamos el nombre de la division, para incluirlo en el insert 
		$sql_nom_div="select nombre from Divisiones where  estadoDiv='A' and id_division =".$division;
		$cursor_nom_div=mssql_query($sql_nom_div);
		if($datos_nom_div=mssql_fetch_array($cursor_nom_div))
		{
			$nom_division=$datos_nom_div["nombre"];
		}

		//actualizamos la division		
		$sql_up_act="update Actividades set nombre='".$nom_division."', id_division=".$division." ,usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate() ";
		if($valo!=$valor)
		{
			$sql_up_act=$sql_up_act.",valor=".$valor." ";
		}
		if(trim($encargado)!="")
		{
			$sql_up_act=$sql_up_act.", id_encargado=".$encargado." ";
		}
		$sql_up_act=$sql_up_act."where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." and dependeDe=".$cualLT." and nivel=3";

		$cur_up_act=mssql_query($sql_up_act);
//echo mssql_get_last_message()."- - ".$sql_up_act; 
		if  (trim($cur_up_act) == "")  
		{
			$error="si";
		}	

		$secu++;
		//si el valor de la division es modificado al ingresado, se actualiza el registro del valor de la actividad
//echo $valo."  ---  ".$valor."<br>";
		if($valo!=$valor)
		{

			$sel_acti_val1="select * from  ActividadesRecursos ";
			$sel_acti_val1=$sel_acti_val1."where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." ";
			$cur_sel_acti_val1=mssql_query($sel_acti_val1);
//			$datos_acti_val1=mssql_fetch_array);

			if ( ((mssql_num_rows($cur_sel_acti_val1))==0) and (trim($cur_sel_acti_val1)!=""))
			{
				$up_acti_val="insert into  ActividadesRecursos  ( id_proyecto,id_actividad,secuencia,valorActiv,unidad,fecha ,usuarioCrea,fechaCrea) values(".$cualProyecto.",".$cualDIvision." ,".$secu;
				$up_acti_val=$up_acti_val." ,".$valor.",".$_SESSION["sesUnidadUsuario"].",getdate()";
				$up_acti_val=$up_acti_val.",".$_SESSION["sesUnidadUsuario"].",getdate() ) ";
				$cur_up_acti_val=mssql_query($up_acti_val);
	
				if  (trim($cur_up_acti_val) == "")  
				{
					$error="si";
				}	
//echo $up_acti_val." * $error *". mssql_get_last_message()."<br>";			
			}
			if ( ((mssql_num_rows($cur_sel_acti_val1))!=0) and (trim($cur_sel_acti_val1)!=""))
			{
	
				$up_acti_val="update  ActividadesRecursos  set secuencia=".$secu;
				$up_acti_val=$up_acti_val.",valorActiv=".$valor.",unidad=".$_SESSION["sesUnidadUsuario"].",fecha=getdate()";
				$up_acti_val=$up_acti_val.",usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate() ";
				$up_acti_val=$up_acti_val."where id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision." ";
	
				$cur_up_acti_val=mssql_query($up_acti_val);

//echo $up_acti_val." * $error *". mssql_get_last_message()."<br>";		

				if  (trim($cur_up_acti_val) == "")  
				{
					$error="si";
				}	
			}
			if (trim($cur_sel_acti_val1)=="")
			{
				$error="si";
			}
//echo $up_acti_val." * $error *". mssql_get_last_message();	
		}

		//actualizamos el campo division de las actividades de la division
		$sql_up_act_div="update Actividades set id_division=".$division." where dependeDe=".$cualDIvision." and tipoActividad=4 and id_proyecto=".$cualProyecto." and actPrincipal=".$cualLC;
		$cursor_up_act_div=mssql_query($sql_up_act_div);
		if  (trim($cursor_up_act_div) == "")  
		{
			$error="si";
		}	
	
		if  (trim($error)=="no")  {
			$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
			echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
		} 
		else {
			$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
			echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
		}
	
		echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."&aa=".$cualDIvision."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

	}
}

//echo $valor_act_division;
?>


<html>
<head>
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

/*
function Nombre(valor)
{
	if(document.getElementById("nombre").value==valor)
	{
		document.getElementById("nombre").value="";
	}
}
*/
function Valor(valor)
{
	if(document.getElementById("valor").value==valor)
	{
		document.getElementById("valor").value="";
	}
}

function valida()
{
/*
	if(document.getElementById("division").value=="")
	{
		alert ('Seleccione una divisi\xf3n');
	}	
*/
	if(document.getElementById("valor").value=="")
	{
		alert ('Asigne un valor a la actividad');
	}
	else if(document.getElementById("valor").value<<? echo $valor_act_division; ?>)
	{
		alert ('El valor asignado a la divisi\xf3n no puede ser inferior a la sumatoria de las actividades asociadas.');
	}
	else
	{
		document.Form1.submit();
	}
/*
	else if(document.getElementById("encargado").value=="")
	{
		alert ('Seleccione un responsable');
	}
*/	
}
//-->
</script>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Actualizar Divisi&oacute;n </td>
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
          <td width="42%" class="TituloTabla">Lote de control </td>
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
//consultamos la division del lote de trabajo
	$sql_Lt="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=2 and id_actividad=".$cualLT;
	$cur_Lt=mssql_query($sql_Lt);	
?>
		<tr>
          <td class="TituloTabla">Lote de trabajo </td>
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
	$sql_Ld="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_actividad=".$cualDIvision;
//echo $sql_Ld;
	$cur_Ld=mssql_query($sql_Ld);	
?>
		<tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n a actualizar</td>
          <td class="TxtTabla">
<?php
			if($datos_Ld=mssql_fetch_array($cur_Ld))
			{
				echo strtoupper($datos_Ld["nombre"]);
				$div_nom=$datos_Ld["nombre"];//almacenamos el nombre de la division, para utilizarlo en la vista previs
				$div_macro=$datos_Ld["macroactividad"];//almacenamos la macroactividad de la division, para utilizarlo en la vista previs
				$div_encargado= $datos_Ld["id_encargado"]; 
				$div_val=$datos_Ld["valor"]; 
			}		
?>
		  </td>
        </tr>


          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>

		<tr>
			<td class="TituloTabla">Identificador</td>
			<td class="TxtTabla"><input name="identificador" type="text" class="CajaTexto" id="identificador" value="<?php echo $div_macro; ?>" readonly></td>
		</tr>
		<tr>
			<td class="TituloTabla">Division</td>
			<td class="TxtTabla">
<!--
			<select name="division" id="division" class="CajaTexto">
				<option value=""> </option>
<?php
/*
				//consultamos la actividad, para extrher la division, para mostrarla, como seleccionada en el select
				$sql_div_act="select * from Actividades where dependeDe=".$cualLT." and tipoActividad=3 and id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision;
				$cur_div_act=mssql_query($sql_div_act);
				while($datos_div_act=mssql_fetch_array($cur_div_act))
				{
					$id_div=$datos_div_act["id_division"];
				}

				$sql_divisiones="select * from Divisiones where  estadoDiv='A' and id_division  not in(
								 select id_division from Actividades where  dependeDe=".$cualLT." and tipoActividad=3 and id_proyecto=".$cualProyecto." and id_actividad<>".$cualDIvision.")";
				$cursor_div=mssql_query($sql_divisiones);
				
				while($datos_div=mssql_fetch_array($cursor_div))
				{
							if($id_div==$datos_div["id_division"])
							{
								$select2="selected";
							}

?>
					<option value="<?php  echo  $datos_div["id_division"]; ?>" <?php echo $select2; ?>><?php  echo  strtoupper($datos_div["nombre"]); ?> </option>
<?php
					$select2="";

				}
*/

?>

	        </select>
-->
<?
				$sql_div_act="select * from Actividades where dependeDe=".$cualLT." and tipoActividad=3 and id_proyecto=".$cualProyecto." and id_actividad=".$cualDIvision;
				$cur_div_act=mssql_query($sql_div_act);
				while($datos_div_act=mssql_fetch_array($cur_div_act))
				{
					$id_div=$datos_div_act["id_division"];
					$nom_div=$datos_div_act["nombre"];
				}

?>
		<input name="no_division" type="text" class="CajaTexto" id="no_division" value="<?php echo strtoupper($nom_div); ?>" readonly>
		<input name="division" type="hidden" id="division" value="<?php echo $id_div; ?>" readonly>
			</td>
		</tr>
		<tr>
			<td class="TituloTabla">Responsable</td>
			 <td align="left" class="TxtTabla">

                <select name="encargado" class="CajaTexto" id="encargado" >
                <option value="" ><? echo "   ";  ?></option>
                    <?
                //Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
        
				$sql2="select U.*, C.nombre nomCategoria  " ;
				$sql2=$sql2." from usuarios U, Categorias C ";
				$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
				$sql2=$sql2." and U.retirado is null ";
				$sql2=$sql2." and left(C.nombre,2) < 40 ";
				$sql2=$sql2." and  C.id_categoria <= 5 ";
				$sql2=$sql2." order by U.apellidos ";
        
                $cursor2 = mssql_query($sql2);
                while ($reg2=mssql_fetch_array($cursor2)) {
					$sel="";
					if($div_encargado==$reg2[unidad])
					{
						$sel="selected";
					}
                ?>
                    <option value="<? echo $reg2[unidad]; ?>" <? echo $sel ?>><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
                    <? } ?>
                  </select>
			</td>

		</tr>
		<tr>
	        <td class="TituloTabla">Valor</td>
			<td align="left" class="TxtTabla">$<input type="text" name="valor" id="valor" value="<?php echo $div_val; ?>"  onKeyPress="return acceptNum(event)" class="CajaTexto"  onfocus="Valor('<?php //echo $nom_act; ?>')" ></td>
		</tr>
        
      </table>

      <table width="100%"  border="0" cellspacing="1" cellpadding="0">


      <tr class="TxtTabla">
        <td align="center" >&nbsp;</td>
        </tr>

		<tr>
			
          <td  align="center" class="TxtTabla"><strong> &iquest;Esta seguro de actualizar el registro? </strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="2">

  		    <input name="Submit" type="button" class="Boton" value="Cancelar"  onClick="window.close()" >
  		    <input name="Submit" type="button" class="Boton" value="Actualizar" onClick="valida()">
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
