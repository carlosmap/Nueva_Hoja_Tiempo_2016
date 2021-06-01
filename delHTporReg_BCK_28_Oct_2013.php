<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function ismaxlength(obj){
var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
if (obj.getAttribute && obj.value.length>mlength)
obj.value=obj.value.substring(0,mlength)
}

function cerrar()
{
	window.close();
//MM_openBrWindow('htFacturacion.php?pMes=<? //=$cualMes ?>','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');
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

		if($recarga==2)
		{
			mssql_query("BEGIN TRANSACTION");
			$cont_regis=1;
			$error="no";

			while($cont_regis<$cont)
			{
				$aplicaUsuario = "eli" . $cont_regis;
//echo $$aplicaUsuario." sss**<br>";
				if($$aplicaUsuario == 'si')
				{
	
					$dia="dia".$cont_regis;
					$mes="mes".$cont_regis;
					$ano="ano".$cont_regis;
					$resumen="resumen".$cont_regis;
//echo $$aplicaUsuario." --- ".$i." <br> ";
	//				echo $$dia." -- ".$$mes." -- ".$$ano."<br>";
				

					$sql_update="
						 delete FacturacionProyectos
						  WHERE FacturacionProyectos.unidad = ".$laUnidad." AND mes =  ".$cualMes." AND vigencia = ".$cualVigencia." AND FacturacionProyectos.id_proyecto = ".$cualProyecto." AND FacturacionProyectos.id_actividad = ".$cualActiv." 
						  AND IDhorario = ".$cualHorario." AND clase_tiempo = ".$cualClaseT." AND localizacion = ".$cualLocaliza." AND cargo = '".$cualCargo."' 
						  and DAY(fechaFacturacion)=".$$dia." and year(fechaFacturacion)=".$$ano."  and month(fechaFacturacion)=".$$mes." and esInterno='I' ";
//echo $sql_update."<br>".mssql_get_last_message()."<br><br>";
					$cur_update=mssql_query($sql_update);
					if(trim($cur_update)=="")
					{
						$error="si";
						break;
					}


				}
				$cont_regis++;
			}

			if(trim($error)=="no")
			{
				mssql_query(" COMMIT TRANSACTION");
				echo "<script>alert('La operaci\xf3n se realiz\xf3 con \xe9xito')</script>";
			}
			else
			{
				mssql_query(" ROLLBACK TRANSACTION");
				echo "<script>alert('Error en la operaci\xf3n')</script>";
			}
			echo "	<script>window.close();MM_openBrWindow('htFacturacion.php?pMes=".$cualMes."&pAno=".$cualVigencia."','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>";

		}

?>


<html>
<head>

<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="javascript" type="text/javascript">
	function trim(str) {
	  return str.replace(/^\s+|\s+$/g,"");
	}

	function sel_todo(opcion)
	{

		var cant_reg=parseInt(document.Form.cont.value);
		var expr1 = '';
		var expr2 = '';
		
		for( i=1;i<cant_reg;i++)
		{

			if(opcion == 1){
				expr1 = 'document.Form.eli' + i + '[0].checked = true';
				expr2 = 'document.Form.eli' + i + '[1].disabled = true';
			} else if(opcion == 0){
				expr1 = 'document.Form.eli' + i + '[1].disabled = false';
				expr2 = 'document.Form.eli' + i + '[1].checked = true';
			}
			eval(expr1);
			eval(expr2);
		}

	}
	function valida()
	{

		var cantReg = parseInt(document.Form.cont.value);
		var expr1 = '';
		var mensaje='';
		var error = 'n';
		var cantSeleccionados = 0;
		for(var i = 1; i < cantReg; i++){


			expr1 = 'document.Form.eli' + i + '[0].checked';

			if(eval(expr1) == true)
			{
				cantSeleccionados++;
			}

		}

		if(cantSeleccionados == 0){
			error = 's';
			mensaje = mensaje + 'Seleccione al menos un registro a eliminar. \n';
		}
		if(error == 's'){
			alert(mensaje);
		} else {
			document.Form.recarga.value = 2;
			document.Form.submit();
		}
	}
</script>

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">

<form name="Form" id="Form" action="" method="post" >
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">.:: Informaci&oacute;n del usuario</td>

  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td>  
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
<?
	$cur_usu=mssql_query("select unidad,UPPER( nombre) nombre,UPPER( apellidos) apellidos from Usuarios where unidad=".$laUnidad);
	$datos_usu=mssql_fetch_array($cur_usu);
?>
          <td class="TituloTabla" >Unidad</td>
          <td class="TxtTabla" ><?=$datos_usu["unidad"]; ?></td>
        </tr>
        <tr>
          <td class="TituloTabla" width="7%" >Nombre</td>
          <td  class="TxtTabla" ><?=$datos_usu["nombre"]." ".$datos_usu["apellidos"]; ?>
		  </td>
        </tr>
        <tr>
          <td colspan="5" class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
        <td colspan="4" class="TituloUsuario">.:: Informaci&oacute;n de la actividad</td>

    
      </tr>
        <tr>
          <td colspan="5" class="TxtTabla"><table width="100%" border="0" bgcolor="#FFFFFF" >
			<tr class="TituloTabla2">
                          <td width="10%">Proyecto</td>
                          <td width="10%">Actividad</td>
                          <td width="8%">Horario</td>
                          <td width="1%">Loc.</td>
                          <td width="1%">CT</td>
                          <td width="1%">Cargo</td>
              </tr>
			<tr class="TxtTabla">
<?
	$cur_proy=(mssql_query("select ('['+ Proyectos.codigo+'.'+Proyectos.cargo_defecto+']') cod_proy ,Proyectos.nombre
									,Actividades.macroactividad, Actividades.nombre as actividad
									 from Actividades
									 inner join Proyectos on Actividades.id_proyecto=Proyectos.id_proyecto
								  where Actividades.id_proyecto = ".$cualProyecto." and Actividades.id_actividad=".$cualActiv));
	$datos_proy=mssql_fetch_array($cur_proy);
?>
                          <td width="10%"><?="<b>[".$datos_proy["cod_proy"]."]</b> ".$datos_proy["nombre"] ?></td>
                          <td width="10%"><?="<b>[".$datos_proy["macroactividad"]."]</b> ".$datos_proy["actividad"] ?></td>
<?


	$datos_hor=mssql_fetch_array( mssql_query("select * from  Horarios where IDhorario=".$cualHorario));

	$horari="[".$datos_hor["Lunes"]."-".$datos_hor["Martes"]."-".$datos_hor["Miercoles"]."-".$datos_hor["Jueves"]."-".$datos_hor["Viernes"]."-".$datos_hor["Sabado"]."-".$datos_hor["Domingo"]."] ";   
//.$datos_hor["NomHorario"]
?>
                          <td width="8%"><?=$horari ?></td>
                          <td width="1%"><?
					$sql07="SELECT * FROM TipoLocalizacion where localizacion = ".$cualLocaliza;
				  	$cursor07 =	 mssql_query($sql07);
					if ($reg07 = mssql_fetch_array($cursor07)) 
					{
						echo $reg07["nomLocalizacion"];		
					}
					
				  ?></td>
                          <td width="1%"><?=$cualClaseT ?></td>
                          <td width="1%"><?=$cualCargo ?></td>
              </tr>
          
          </table></td>

        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5"   class="TituloUsuario">.::Resumen de trabajo</td> 
          
        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla"><!-- readonly-->
            <table width="100%" border="0"  bgcolor="#FFFFFF">
    <tr class="TituloTabla2">
      <td rowspan="2">Dia</td>
      <td rowspan="2" width="6%" >Horas registradas</td>
      <td rowspan="2">Resumen</td>
      <td colspan="2">Eliminar todo <br> Si
        <input type="radio" name="todo" id="todo" onClick="sel_todo(1);" >
		<br>
        No 
        <input type="radio" name="todo" id="todo" onClick="sel_todo(0);" ></td>
      </tr>
    <tr class="TituloTabla2">
      <td width="4%">Si</td>

      <td width="5%">No</td>
      </tr>
<?

	//CONSULTA LA INFO DE LA FACTURACION
	$cur_inf_factu=mssql_query("SELECT  resumen,  day(fechaFacturacion) dia, MONTH(fechaFacturacion) mes , year(fechaFacturacion) ano,horasMesF
								FROM FacturacionProyectos
								WHERE FacturacionProyectos.unidad = ".$laUnidad."
								AND mes =  ".$cualMes."
								AND vigencia = ".$cualVigencia."
								AND FacturacionProyectos.id_proyecto = ".$cualProyecto."
								AND FacturacionProyectos.id_actividad = ".$cualActiv."
								AND IDhorario = ".$cualHorario."
								AND clase_tiempo = ".$cualClaseT."
								AND localizacion = ".$cualLocaliza."
								AND cargo = '".$cualCargo."'");


?>
<?
	//CONSULTA LA INFO DE LA FACTURACION
	$cur_inf_factu=mssql_query("SELECT  resumen,  day(fechaFacturacion) dia, MONTH(fechaFacturacion) mes , year(fechaFacturacion) ano,horasMesF
								FROM FacturacionProyectos
								WHERE FacturacionProyectos.unidad = ".$laUnidad."
								AND mes =  ".$cualMes."
								AND vigencia = ".$cualVigencia."
								AND FacturacionProyectos.id_proyecto = ".$cualProyecto."
								AND FacturacionProyectos.id_actividad = ".$cualActiv."
								AND IDhorario = ".$cualHorario."
								AND clase_tiempo = ".$cualClaseT."
								AND localizacion = ".$cualLocaliza."
								AND cargo = '".$cualCargo."'");



	$cont=1;
	while($datos_inf_factu=mssql_fetch_array($cur_inf_factu))
	{
		if(( (int) $datos_inf_factu["dia"])<10)
			$datos_inf_factu["dia"]='0'.$datos_inf_factu["dia"];

		if(( (int) $datos_inf_factu["mes"])<10)
			$datos_inf_factu["mes"]='0'.$datos_inf_factu["mes"];
?>
    <tr>
      <td class="TxtTabla"><?=$datos_inf_factu["dia"] ?></td>
      <td class="TxtTabla"><?=$datos_inf_factu["horasMesF"] ?></td>
      <td class="TxtTabla">
		<?=$datos_inf_factu["resumen"] ?>
        </td>
      <td align="center" class="TxtTabla"><input name="eli<?=$cont ?>" type="radio" id="eli<?=$cont ?>" value="si"></td>
      <td align="center" class="TxtTabla"><input checked name="eli<?=$cont ?>" type="radio" id="eli<?=$cont ?>" value="no" ></td>
		<input type="hidden" name="dia<?=$cont ?>" id="dia<?=$cont ?>" value="<?=$datos_inf_factu["dia"] ?>">
		<input type="hidden" name="mes<?=$cont ?>" id="mes<?=$cont ?>" value="<?=$datos_inf_factu["mes"] ?>">
		<input type="hidden" name="ano<?=$cont ?>" id="ano<?=$cont ?>" value="<?=$datos_inf_factu["ano"] ?>">
    </tr>
<?
		$cont++;	
	}
?>
  </table></td>
          </tr>

	

      </table>



<table width="100%"  border="0" cellspacing="1" cellpadding="0">


  <tr>
    <td align="right" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="TxtTabla"><strong>&#191;Esta seguro de eliminar los registros seleccionados?</strong></td>
  </tr>
  <tr>
          <td align="center" class="TxtTabla"><input type="button" class="Boton" value="Cancelar" onClick="cerrar()" > &nbsp;
<?
			//PENDIENTE
			///****** INCLUIR VALIDACIONES DEL BOTON, RELACIONADOS CON EL VOBO DEL JEFE INMEDIATO, CONTRATOS, Y PROYECTO
			//CUANDO TENGA ALUNA DE ESTAS FIRMAS, NO DEBE MOSTRAR EL BOTON, Y MANTENDRA INABILITADOS LAS AREAS DE TEXTO
?>
          <input name="guardar" type="button" class="Boton" id="guardar" value="Eliminar" onClick="valida()" >
<?
			//PENDIENTE
?>
			<input type="hidden" name="cont" id="cont" value="<?=$cont; ?>" >
			<input type="hidden" name="recarga" id="recarga" value="1" >
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
