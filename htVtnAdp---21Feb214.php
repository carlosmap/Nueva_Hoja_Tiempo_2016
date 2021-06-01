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
				$dia="dia".$cont_regis;
				$mes="mes".$cont_regis;
				$ano="ano".$cont_regis;
				$resumen="resumen".$cont_regis;
//				echo $$dia." -- ".$$mes." -- ".$$ano."<br>";
			

				$sql_update="
					 update FacturacionProyectos set resumen='".$$resumen."', usuarioMod=".$laUnidad." , fechaMod=GETDATE()
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
			echo "	<script>window.close();MM_openBrWindow('htFacturacion.php?pMes=".$cualMes."','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>";
		}

?>


<html>
<head>

<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="javascript" type="text/javascript">
	window.name = 'winADP';
	function trim(str) {
	  return str.replace(/^\s+|\s+$/g,"");
	}


	function valida()
	{

		var cont=1,ban=0;
		var cont_regis=parseInt(document.Form.cont.value);


		while(cont<cont_regis)
		{
			if(trim(document.getElementById("resumen"+cont).value)=="")
			{
				ban=1;
				break;				
			}
			cont++;
		}
		if(ban==1)
		{
			alert("El resumen de trabajo, es obligatorio");
		}
		else
		{
			document.Form.recarga.value=2;
			document.Form.submit();
		}
	
	}
</script>

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">

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
        <td colspan="4" class="TituloUsuario">.:: Informaci&oacute;n del registro</td>

    
      </tr>
        <tr>
          <td colspan="5" class="TxtTabla"><table width="100%" border="0" bgcolor="#FFFFFF" >
            <tr class="">

<?
	$cur_proy=(mssql_query("select ('['+ Proyectos.codigo+'.'+Proyectos.cargo_defecto+']') cod_proy ,Proyectos.nombre
									,Actividades.macroactividad, Actividades.nombre as actividad
									 from Actividades
									 inner join Proyectos on Actividades.id_proyecto=Proyectos.id_proyecto
								  where Actividades.id_proyecto = ".$cualProyecto." and Actividades.id_actividad=".$cualActiv));

#	echo "select ('['+ Proyectos.codigo+'.'+Proyectos.cargo_defecto+']') cod_proy ,Proyectos.nombre, Actividades.macroactividad, Actividades.nombre as actividad from Actividades inner join Proyectos on Actividades.id_proyecto=Proyectos.id_proyecto where Actividades.id_proyecto = ".$cualProyecto." and Actividades.id_actividad=".$cualActiv."<br />";
								  
	$datos_proy=mssql_fetch_array($cur_proy);
?>
              <td width="7%" class="TituloTabla2">Proyecto</td>
              <td align="left" class="TxtTabla"><?=$datos_proy["cod_proy"]." ".$datos_proy["nombre"] ?></td>

            <tr >
              <td class="TituloTabla2">Actividad</td>
              <td align="left"  class="TxtTabla"><?=$datos_proy["macroactividad"]." ".$datos_proy["actividad"] ?></td>
            </tr>
            <tr class="">
<?
	$cur_horario=(mssql_query("select NomHorario from Horarios where IDhorario= ".$cualHorario));
	$datos_horario=mssql_fetch_array($cur_horario);
?>

              <td class="TituloTabla2">Horario</td>
              <td align="left"  class="TxtTabla"><?=$datos_horario["NomHorario"] ?></td>
              </tr>
  <tr class="">
              <td class="TituloTabla2">Loc.</td>


              <td align="left" class="TxtTabla"><?
					$sql07="SELECT * FROM TipoLocalizacion where localizacion = ".$cualLocaliza;
				  	$cursor07 =	 mssql_query($sql07);
					if ($reg07 = mssql_fetch_array($cursor07)) 
					{
						echo $reg07["nomLocalizacion"];		
					}
					
				  ?></td>

              </tr>
				<tr>
					<td class="TituloTabla2">CT</td>
					<td class="TxtTabla"><?=$cualClaseT ?></td>
				</tr>	
            <tr class="">
<?
/*
	$cur_horario=($mssql_query("select NomHorario from Horarios where IDhorario= ".$cualHorario));
	$datos_horario=mssql_fetch_array($cur_horario);
*/
?>
<?
	//CONSULTA LA INFO DE LA FACTURACION
	$cur_inf_factu=mssql_query("SELECT  resumen,  day(fechaFacturacion) dia, MONTH(fechaFacturacion) mes , year(fechaFacturacion) ano, horasMesF
								FROM FacturacionProyectos
								WHERE FacturacionProyectos.unidad = ".$laUnidad."
								AND mes =  ".$cualMes."
								AND vigencia = ".$cualVigencia."
								AND FacturacionProyectos.id_proyecto = ".$cualProyecto."
								AND FacturacionProyectos.id_actividad = ".$cualActiv."
								AND IDhorario = ".$cualHorario."
								AND clase_tiempo = ".$cualClaseT."
								AND localizacion = ".$cualLocaliza."
								AND cargo = '".$cualCargo."' and esInterno='I'  order by fechaFacturacion ");


?>

              <td class="TituloTabla2">Cargo</td>
              <td align="left" class="TxtTabla"><?=$cualCargo ?></td>
            </tr>

			<tr>
              <td class="TituloTabla2">Vigencia</td>
              <td align="left" class="TxtTabla"><?=$cualVigencia ?></td>
            </tr>

			<tr>
              <td class="TituloTabla2">Mes</td>
<?
$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
              <td align="left" class="TxtTabla"><?=$mes[$cualMes] ?></td>
            </tr>
          
          </table></td>

        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5"   class="TituloUsuario">.::Relación de ADP</td> 
          
        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla"><!-- readonly-->
            <table width="100%" border="0"  bgcolor="#FFFFFF">
    <tr class="TituloTabla2">
      <td width="2%">&nbsp;</td>


      <td width="25%">Fecha Inicial</td>
      <td   width="25%" >Fecha Final</td>
      <td>ADP</td>
    </tr>
<?
	$cont=1;
	while($datos_inf_factu=mssql_fetch_array($cur_inf_factu))
	{
		if(( (int) $datos_inf_factu["dia"])<10)
			$datos_inf_factu["dia"]='0'.$datos_inf_factu["dia"];

		if(( (int) $datos_inf_factu["mes"])<10)
			$datos_inf_factu["mes"]='0'.$datos_inf_factu["mes"];
?>
    <tr>
      <td width="2%" class="TxtTabla">&nbsp;</td>
      <td width="25%" class="TxtTabla"><?=$datos_inf_factu["dia"] ?></td>
      <td width="25%" class="TxtTabla"><?=$datos_inf_factu["horasMesF"] ?></td>
      <td class="TxtTabla">&nbsp;</td>
    </tr>
<?
		$cont++;	
	}
?>
    <tr>
      <td colspan="4" align="right" class="TxtTabla">
		<input name="Submit" type="button" class="Boton" onClick="MM_openBrWindow('addVtnAdp.php?cualProyecto=<?= $cualProyecto ?>','','scrollbars=yes,resizable=yes,width=350,height=250')" value="Agregar ADP">
		<!--
        <input type="button" class="Boton" value="Agregar ADP" onClick="MM_openBrWindow('addVtnAdp.php','winADP','')" >
        -->
		<input type="hidden" name="dia<?=$cont ?>" id="dia<?=$cont ?>" value="<?=$datos_inf_factu["dia"] ?>">
		<input type="hidden" name="mes<?=$cont ?>" id="mes<?=$cont ?>" value="<?=$datos_inf_factu["mes"] ?>">
		<input type="hidden" name="ano<?=$cont ?>" id="ano<?=$cont ?>" value="<?=$datos_inf_factu["ano"] ?>">
      </td>
    </tr>
  </table></td>
          </tr>

	

      </table>



<table width="100%"  border="0" cellspacing="1" cellpadding="0">


  <tr>
    <td align="right" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
          <td align="right" class="TxtTabla"><input type="button" class="Boton" value="Cerrar Ventana" onClick="cerrar()" >
<?
			//PENDIENTE
			///****** INCLUIR VALIDACIONES DEL BOTON, RELACIONADOS CON EL VOBO DEL JEFE INMEDIATO, CONTRATOS, Y PROYECTO
			//CUANDO TENGA ALUNA DE ESTAS FIRMAS, NO DEBE MOSTRAR EL BOTON, Y MANTENDRA INABILITADOS LAS AREAS DE TEXTO
?>
          <input name="guardar" type="button" class="Boton" id="guardar" value="Guardar" onClick="valida()" >
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
</body>
</html>



<? mssql_close ($conexion); ?>	
