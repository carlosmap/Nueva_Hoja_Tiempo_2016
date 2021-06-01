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

function valida()
{
	if(document.getElementById("nombre").value=="")
	{
		alert ('Asigne un nombre a el lote de control');
	}	
/*	else if(document.getElementById("encargado").value=="")
	{
		alert ('Seleccione un responsable');
	}
*/	
/*
	else if(document.getElementById("valor").value=="")
	{
		alert ('Asigne un valor a la actividad');
	}
*/
	else
	{
		document.Form1.submit();
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


		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
		$error="no";
		
		if(trim($encargado)=="")
		{
			$encargado="NULL";
		}
		//actualizamos el LC
		$sql_up_lc="update Actividades set nombre=upper('".$nombre."'), id_encargado=".$encargado." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLC." and nivel=1";
		$cur_up_lc=mssql_query($sql_up_lc);
//echo $sql_up_lc." - ". mssql_get_last_message();

		if  (trim($cur_up_lc) == "")  
		{
			$error="si";
		}	

	if  (trim($error)=="no")  {
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Actualización realizada satisfactoriamente.');</script>"); 
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 

<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Actualizar Lotes de control </td>
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
          <td width="42%" class="TituloTabla">Lote de control a actualizar</td>
          <td width="58%" class="TxtTabla">
<?php
			if($datos_Lc=mssql_fetch_array($cur_Lc))
			{
				echo $datos_Lc["nombre"];
				$Lc_macro=$datos_Lc["macroactividad"];//almacenamos la macroactividad de la division
				$Lc_nom=$datos_Lc["nombre"];		  //almacenamos el nombre del LT
				$Lc_encargado= $datos_Lc["id_encargado"];   //alamcenamos el encargado de LT
			}		
?>
		  </td>
		</tr>

		<tr>

          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>

		<tr>
			<td class="TituloTabla">Identificador</td>
			<td class="TxtTabla"><input name="identificador" type="text" class="CajaTexto" id="identificador" value="<?php echo $Lc_macro; ?>" readonly></td>
		</tr>
		<tr>
			<td class="TituloTabla">Nombre</td>
			<td class="TxtTabla">
				<input name="nombre" type="text" class="CajaTexto" id="nombre" value="<?php echo $Lc_nom; ?>" size="60"  >
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
					if($Lc_encargado==$reg2[unidad])
					{
						$sel="selected";
					}
                ?>
                    <option value="<? echo $reg2[unidad]; ?>" <? echo $sel ?>><? echo ucwords(strtolower($reg2[nombre])) . ", " . ucwords(strtolower($reg2[apellidos]));  ?></option>
                    <? } ?>
                  </select>
			</td>

		</tr>
<!--
		<tr>
	        <td class="TituloTabla">Valor Presupuestado</td>
			<td align="left" class="TxtTabla">$<input type="text" name="valor" id="valor"  onKeyPress="return acceptNum(event)" class="CajaTexto"  onfocus="Valor('<?php //echo $nom_act; ?>')" ></td>
		</tr>
-->        
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
