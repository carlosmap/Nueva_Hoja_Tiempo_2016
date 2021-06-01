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
/*
function Valor(valor)
{
	if(document.getElementById("valor").value==valor)
	{
		document.getElementById("valor").value="";
	}
}
*/
function valida()
{
	if(document.getElementById("nombre").value=="")
	{
		alert ('Asigne un nombre a el lote de control');
	}	
	else if(document.getElementById("encargado").value=="")
	{
		alert ('Seleccione un responsable');
	}
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

		//actualizamos el LT
		$sql_up_lt="update Actividades set nombre='".$nombre."', id_encargado=".$encargado." where id_proyecto=".$cualProyecto." and id_actividad=".$cualLT." and dependeDe=".$cualLC." and nivel=2";
		$cur_up_lt=mssql_query($sql_up_lt);

		if  (trim($cur_up_lt) == "")  
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
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

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
    <td class="TituloUsuario">Actualizar Lote de trabajo </td>
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
          <td class="TituloTabla">Lote de trabajo a actualizar</td>
          <td class="TxtTabla">
<?php
			if($datos_Lt=mssql_fetch_array($cur_Lt))
			{
				echo $datos_Lt["nombre"];
				$Lt_macro=$datos_Lt["macroactividad"];//almacenamos la macroactividad de la division
				$Lt_nom=$datos_Lt["nombre"];		  //almacenamos el nombre del LT
				$Lt_encargado= $datos_Lt["id_encargado"];   //alamcenamos el encargado de LT
			}		
?>
		  </td>
		</tr>

          <td class="TxtTabla" colspan="2">&nbsp;</td>
        </tr>

		<tr>
			<td class="TituloTabla">Identificador</td>
			<td class="TxtTabla"><input name="identificador" type="text" class="CajaTexto" id="identificador" value="<?php echo $Lt_macro; ?>" readonly></td>
		</tr>
		<tr>
			<td class="TituloTabla">Nombre</td>
			<td class="TxtTabla">
				<input name="nombre" type="text" class="CajaTexto" id="nombre" value="<?php echo $Lt_nom; ?>" size="60" >
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
					if($Lt_encargado==$reg2[unidad])
					{
						$sel="selected";
					}
                ?>
                    <option value="<? echo $reg2[unidad]; ?>" <? echo $sel ?>><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
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
