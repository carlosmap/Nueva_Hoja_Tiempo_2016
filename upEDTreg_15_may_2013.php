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

//CONSULTA EL VALOR ASIGNADO A LA DIVISION
$sql_valor_division=" SELECT  valorActiv from HojaDeTiempo.dbo.ActividadesRecursos where id_proyecto = ".$cualProyecto." and id_actividad=".$cualDiv;
$cur_valor_division=mssql_query($sql_valor_division);
if($datos_valor_division=mssql_fetch_array($cur_valor_division))
	$valor_division=$datos_valor_division["valorActiv"];
else
	$valor_division=0;


//consulta el valor total de las actividades asociadas a la division, EXCLUYENDO LA QUE SE VA A ACTUALIZAR
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
			  AND id_actividad<>".$cualACtividad."
		)";
	$cur_valor_division=mssql_query($sql_valor_actividades_division);
	if($datos_valor_actividades_division=mssql_fetch_array($cur_valor_division))
	{
		$valor_actividades_division=$datos_valor_actividades_division["sumaDI"];
	}
	if(trim($valor_actividades_division)=="")
		$valor_actividades_division=0;


if(trim($recarga) == "2")
{


		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
		$error="no";
		if(trim($encargado)=="")
			$encargado="NULL";

		//consulta la secuencia de actualizacion de la actividad
		$sql_secuen="select secuencia,valorActiv from ActividadesRecursos where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad."";
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

		//actualiza la actividad en la tabla Actividades
		$sql_up_act="update Actividades set nombre='".$nombre."', id_encargado=".$encargado.",usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate()";
		if($valo!=$valor)
		{
			$sql_up_act=$sql_up_act.",valor=".$valor." ";
		}
		$sql_up_act=$sql_up_act."  where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." and dependeDe=".$cualDiv." and nivel=4";

		$cur_up_act=mssql_query($sql_up_act);
//echo mssql_get_last_message()."- - ".$sql_up_act; 
		if  (trim($cur_up_act) == "")  
		{
			$error="si";
		}	


		$secu++;

		//actualiza el registro de los valores de las actividades

		//si el valor de la actividad es modificado al ingresado, se actualiza el registro del valor de la actividad
		if($valo!=$valor)
		{
			$sel_acti_val1="select * from  ActividadesRecursos ";
			$sel_acti_val1=$sel_acti_val1."where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." ";
			$cur_sel_acti_val1=mssql_query($sel_acti_val1);
//echo 	$sel_acti_val1." --- ",mssql_get_last_message()."<br><br>";

			if (( (mssql_num_rows($cur_sel_acti_val1))==0)and (trim($cur_sel_acti_val1)!=""))
			{		
				$up_acti_val="insert into  ActividadesRecursos  (id_proyecto,id_actividad,secuencia,valorActiv,unidad,fecha ,usuarioCrea,fechaCrea) values(".$cualProyecto." ,".$cualACtividad.",".$secu;
				$up_acti_val=$up_acti_val." ,".$valor.",".$_SESSION["sesUnidadUsuario"].",getdate()";
				$up_acti_val=$up_acti_val.",".$_SESSION["sesUnidadUsuario"].",getdate() ) ";
//echo 	$up_acti_val." --- ",mssql_get_last_message()."<br><br>";
				$cur_up_acti_val=mssql_query($up_acti_val);
	
				if  (trim($cur_up_acti_val) == "")  
				{
					$error="si";
				}	
			}

			if ( ((mssql_num_rows($cur_sel_acti_val1))!=0) and (trim($cur_sel_acti_val1)!=""))
			{
				$up_acti_val="update  ActividadesRecursos  set secuencia=".$secu;
				$up_acti_val=$up_acti_val.",valorActiv=".$valor.",unidad=".$_SESSION["sesUnidadUsuario"].",fecha=getdate()";
				$up_acti_val=$up_acti_val.",usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate() ";
				$up_acti_val=$up_acti_val."where id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad." ";
//echo 	$up_acti_val." --- ",mssql_get_last_message()."<br><br>";		
				$cur_up_acti_val=mssql_query($up_acti_val);
				if  (trim($cur_up_acti_val) == "")  
				{
					$error="si";
				}	
			}

			if (trim($cur_sel_acti_val1)=="")
			{
				$error="si";
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
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."&aa=".$cualACtividad."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>
<html>
<head>

<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Actualizar Actividad</td>
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
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n</td>
          <td class="TxtTabla">
<?php
			if($datos_Ld=mssql_fetch_array($cur_Ld))
			{
				echo strtoupper($datos_Ld["nombre"]);
				$div_nom=$datos_Ld["nombre"];//almacenamos el nombre de la division, para utilizarlo en la vista previs
				$div_identi=$datos_Ld["macroactividad"];//almacenamos la macroactividad de la division, para utilizarlo en la vista previs
			}		
?>
		  </td>
        </tr>
<?php

	$sql_a="select * from Actividades where id_proyecto=".$cualProyecto." and nivel=4 and id_actividad=".$cualACtividad;
	$cur_a=mssql_query($sql_a);	
?>
		<tr>
          <td class="TituloTabla">Actividad a actualizar</td>
          <td class="TxtTabla">
<?php
			if($datos_a=mssql_fetch_array($cur_a))
			{
				echo $datos_a["macroactividad"]." - ".$datos_a["nombre"];
				$nom_act=$datos_a["nombre"];
				$acti_macro= $datos_a["macroactividad"]; //almacenamos la macroactividad, para utilizarla en la informacion de la actividad
				$acti_encargado= $datos_a["id_encargado"]; 
				$acti_val=$datos_a["valor"]; 
			}		
?>
		  </td>
        </tr>
		<tr>

          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>

		<tr>
			<td class="TituloTabla">Identificador</td>
			<td class="TxtTabla"><input name="identificador" type="text" class="CajaTexto" id="identificador" value="<?php echo $acti_macro; ?>" readonly></td>
		</tr>
		<tr>
			<td class="TituloTabla">Nombre Actividad</td>
			<td class="TxtTabla"><input name="nombre" type="text" class="CajaTexto" id="nombre" value="<?php echo $nom_act; ?>" size="60" ></td>
<!--  onfocus="Nombre('<?php // echo $nom_act; ?>')" -->
		</tr>
		<tr>
			<td class="TituloTabla">Responsable</td>
			 <td align="left" class="TxtTabla">
<?php
				//consultamos la division a la que esta asociada la actividad
				$sql_div_act="select * from Actividades where dependeDe=".$cualDiv." and tipoActividad=4 and id_proyecto=".$cualProyecto." and id_actividad=".$cualACtividad;
//echo $sql_div_act;
				$cur_div_act=mssql_query($sql_div_act);
				while($datos_div_act=mssql_fetch_array($cur_div_act))
				{
					$id_div=$datos_div_act["id_division"];
				}
?>
                <select name="encargado" class="CajaTexto" id="encargado" >
                <option value="" ><? echo "   ";  ?></option>
                    <?
                //Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 


                $sql2="select U.*
                        from usuarios U				
                        inner join Departamentos as dep on dep.id_departamento=U.id_departamento
                        inner join Divisiones as div on dep.id_division=div.id_division
                         where div.id_division='".$id_div."' 
                         and retirado is null
                          order by U.apellidos  " ;
        
                $cursor2 = mssql_query($sql2);
                while ($reg2=mssql_fetch_array($cursor2)) {
					$sel="";
					if($acti_encargado==$reg2[unidad])
					{
						$sel="selected";
					}
                ?>
                    <option value="<? echo $reg2[unidad]; ?>" <? echo $sel ?>><? echo ucwords(strtolower($reg2[nombre])) . ", " . ucwords(strtolower($reg2[apellidos]));  ?></option>
                    <? } ?>
                  </select>
<? //echo $sql2 ?>
			</td>

		</tr>
		<tr>
	        <td class="TituloTabla">Valor</td>
			<td align="left" class="TxtTabla">$<input type="text" name="valor" id="valor" value="<?php echo $acti_val; ?>" onKeyPress="return acceptNum(event)" class="CajaTexto"  onfocus="Valor('<?php //echo $nom_act; ?>')" ></td>
		</tr>
        
      </table>

      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TxtTabla">
        <td align="center" >&nbsp;</td>
        </tr>
		<tr>
			
          <td  align="center" class="TxtTabla"><strong>¿Esta seguro de actualizar el registro?</strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="2">

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
<?
	//SUMA EL VALOR DE LAS ACTIVIDAD EDITADA + EL VALOR DE LAS ACTIVIDADES ALMACENADAS
	//COMPARA SI LA SUMA DE LAS ACTIVIDADES, NO SUPERA EL VALOR ASIGNADO A LA DIVISION
?>
	var total_acti_division=<? echo $valor_actividades_division; ?>;

	var total=parseInt(total_acti_division)+parseInt(document.getElementById("valor").value);
	if(document.getElementById("nombre").value=="")
	{
		alert ('Asigne un nombre a la actividad');
	}	
/*
	else if(document.getElementById("encargado").value=="")
	{
		alert ('Seleccione un responsable');
	}
*/	
	else if(document.getElementById("valor").value=="")
	{
		alert ('Asigne un valor a la actividad');
	}

	else if(total><? echo $valor_division; ?>)
	{
		alert('El valor de la actividad supera el valor asignado a la division, por favor verifique el valor asignado a la actividad. '); // total ingresadas '+total_acti_division+ ' total suma (ingre+regis): '+total);
		v2='n';
	}	
	else
	{
		document.Form1.submit();
	}
}
</script>
</body>
</html>

<? mssql_close ($conexion); ?>	
