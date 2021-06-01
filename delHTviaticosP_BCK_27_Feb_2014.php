<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php
session_start();
include "funciones.php";
include "validaUsrBd.php";

//FALTA EL VALOR DE LA VARIABLE $cualCargo , RELACIONADO EN LA TABLA  viaticosproyecto  EN EL CAMPO cargo
//echo $cualCargo . " --------------- <br>";
//echo $cualLocaliza . "<br>";

if($recarga==2)
{
	$i=1;
	$error="no";
	mssql_query("BEGIN TRANSACTION");
	while($i<=$cantReg)
	{
		$eili="eli".$i;
		if ( trim( ( (int) ${$eili}) )=="1")
		{
			$sitio="IDsitio".$i;
			$IDTipoViatico="IDTipoViatico".$i;
			$FechaIni="FechaIni".$i;
			$FechaFin="FechaFin".$i;
			$viaticoCompleto="viaticoCompleto".$i;

			$sql_elimi_viatico="delete from ViaticosProyectosHT where ViaticosProyectosHT.id_proyecto=".$cualProyecto." and id_actividad=".$cualActiv." and unidad=".$laUnidad." and mes=".$cualMes." 
				and vigencia=".$cualVigencia." and esInterno='I' and IDhorario=".$cualHorario." and clase_tiempo=".$cualClaseT." and localizacion=".$cualLocaliza." and cargo=".$cualCargo."	and IDsitio=".${$sitio}."	and IDTipoViatico=".${$IDTipoViatico}."
				and FechaIni='".${$FechaIni}."' and FechaFin='".${$FechaFin}."' and viaticoCompleto=".${$viaticoCompleto};

			$cur_elimini_viatico=mssql_query($sql_elimi_viatico);
			if(trim($cur_elimini_viatico)=="")
			{
				$error="si";
				break;
			}

//			echo $sql_elimi_viatico." ****** <br><br> ";
		}
		$i++;
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

<html >
<head>
<title>Eliminaci&oacute;n de vi&aacute;ticos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">


<script language="javascript" type="text/javascript">
	function sel_todo(opcion)
	{

		var cant_reg=parseInt(document.Form.cantReg.value);
		var expr1 = '';
		var expr2 = '';
		
		for( i=1;i<=cant_reg;i++)
		{

			if((document.Form.tod.checked) == true){
				expr1 = 'document.Form.eli' + i + '[0].checked = true';
//				expr2 = 'document.Form.eli' + i + '[1].checked = true';
			} 
			if((document.Form.tod.checked) == false){
//				expr1 = 'document.Form.eli' + i + '[0].checked = false';
				expr2 = 'document.Form.eli' + i + '[1].checked = true';
			}
			eval(expr1);
			eval(expr2);
		}

	}
	function valida()
	{

		var cantReg = parseInt(document.Form.cantReg.value);
		var expr1 = '';
		var mensaje='';
		var error = 'n';
		var cantSeleccionados = 0;
		for(var i = 1; i <= cantReg; i++){


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


<?
	$cur_proy=(mssql_query("select ('['+ Proyectos.codigo+'.'+Proyectos.cargo_defecto+']') cod_proy ,Proyectos.nombre
									,Actividades.macroactividad, Actividades.nombre as actividad
									 from Actividades
									 inner join Proyectos on Actividades.id_proyecto=Proyectos.id_proyecto
								  where Actividades.id_proyecto = ".$cualProyecto." and Actividades.id_actividad=".$cualActiv));
	$datos_proy=mssql_fetch_array($cur_proy);
?>

<?
$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form id="Form" name="Form"  action="" method="post" >

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">.:: Informaci&oacute;n del usuario</td>

  </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" >

  <tr>
    <td>  
      <table width="100%"  border="0"  bgcolor="#FFFFFF">
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


              <td width="7%" class="TituloTabla2">Proyecto</td>
              <td align="left" class="TxtTabla"><?=$datos_proy["cod_proy"]." ".$datos_proy["nombre"] ?></td>

            <tr >
              <td class="TituloTabla2">Actividad</td>
              <td align="left"  class="TxtTabla"><?=$datos_proy["macroactividad"]." ".$datos_proy["actividad"] ?>20</td>
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
              <td class="TituloTabla2">Localizaci&oacute;n (Loc.)</td>


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
					<td class="TituloTabla2">Clase de Tiempo (CT)</td>
					<td class="TxtTabla"><?=$cualClaseT ?></td>
				</tr>	
            <tr class="">
<?
/*
	$cur_horario=($mssql_query("select NomHorario from Horarios where IDhorario= ".$cualHorario));
	$datos_horario=mssql_fetch_array($cur_horario);
*/
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

              <td align="left" class="TxtTabla"><?=$mes[$cualMes] ?></td>
            </tr>
          
          </table></td>

        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5"   class="TituloUsuario">.::Eliminacion de vi&aacute;ticos</td> 
        </tr>
      </table>
      <table width="100%" border="0" bgcolor="#FFFFFF">
        <tr>
          <td width="15%" rowspan="2" class="TituloTabla2">Sitio de trabajo</td>
          <td width="15%" rowspan="2" class="TituloTabla2">Tipo de Vi&aacute;tico</td>
          <td width="8%" rowspan="2" nowrap class="TituloTabla2">Fecha Inicial<br>(mm/dd/aaaa)</td>
          <td width="8%" rowspan="2" class="TituloTabla2">Fecha Final<br>(mm/dd/aaaa)</td>
          <td rowspan="2" class="TituloTabla2">Trayecto</td>
          <td rowspan="2" class="TituloTabla2">Trabajo Realizado</td>
          <td width="5%" rowspan="2" class="TituloTabla2">Es d&iacute;a de regreso</td>
          <td colspan="2" width="9%" class="TituloTabla2">Eliminar<br>
			Todos
<input type="checkbox" name="tod" id="tod"  onClick="sel_todo();" >
<!--             Si
               <input type="radio" name="todo" id="todo" onClick="sel_todo(1);" >
              <br>
              No
  <input type="radio" name="todo" id="todo" onClick="sel_todo(0);" >
-->
          </td>
        </tr>
        <tr class="TituloTabla2">
          <td width="3%">Si</td>
          <td width="3%">No</td>
        </tr>
<?
		$sql_viaticos="select ViaticosProyectosHT.viaticoCompleto , ViaticosProyectosHT.IDsitio, ViaticosProyectosHT.IDTipoViatico,ViaticosProyectosHT.FechaFin,ViaticosProyectosHT.FechaIni, SitiosTrabajo.NomSitio, TiposViatico.NomTipoViatico, 
 ViaticosProyectosHT.FechaIni, ViaticosProyectosHT.FechaFin, 
month(ViaticosProyectosHT.FechaIni) m_i, day(ViaticosProyectosHT.FechaIni) d_i, year(ViaticosProyectosHT.FechaIni) y_i 
  ,
  month(ViaticosProyectosHT.FechaFin) m_f, day(ViaticosProyectosHT.FechaFin) d_f, year(ViaticosProyectosHT.FechaFin) y_f,

ViaticosProyectosHT.Trayecto, ViaticosProyectosHT.ObjetoComision from
		 ViaticosProyectosHT
		 inner join SitiosTrabajo on ViaticosProyectosHT.id_proyecto=SitiosTrabajo.id_proyecto and SitiosTrabajo.IDsitio=ViaticosProyectosHT.IDsitio
		 inner join TiposViaticoProy on 
		 ViaticosProyectosHT.id_proyecto= TiposViaticoProy.id_proyecto and ViaticosProyectosHT.IDTipoViatico =TiposViaticoProy.IDTipoViatico
		 inner join TiposViatico on TiposViatico.IDTipoViatico=ViaticosProyectosHT.IDTipoViatico
		where ViaticosProyectosHT.id_proyecto=".$cualProyecto." and id_actividad=".$cualActiv." and unidad=".$laUnidad." and mes=".$cualMes." and vigencia=".$cualVigencia." and esInterno='I' and IDhorario=".$cualHorario." and clase_tiempo=".$cualClaseT." 
		and localizacion=".$cualLocaliza." and cargo=".$cualCargo." and ViaticosProyectosHT.IDsitio=".$cualSitio." and ViaticosProyectosHT.IDTipoViatico=".$cualTipoViatico."
		order by FechaIni,FechaFin";
//sitio trabajo


//tipo viatico
//echo $sql_viaticos." <br>".mssql_get_last_message();
		$cur_viaticos= mssql_query($sql_viaticos);
		$i=1;
		while($datos_viaticos=mssql_fetch_array($cur_viaticos))
		{
?>
            <tr>
              <td class="TxtTabla">

              <?=str_replace('á','&aacute;',str_replace('é','&eacute;',str_replace('í','&iacute;',str_replace('ú','&uacute;',str_replace('ó','&oacute;',str_replace('ñ','&ntilde;',$datos_viaticos["NomSitio"])))))); ?>
              <input name="IDsitio<?=$i; ?>" id="IDsitio<?=$i; ?>" type="hidden" value="<?=$datos_viaticos["IDsitio"] ?>" /></td>
              <td class="TxtTabla">
              <?=$datos_viaticos["NomTipoViatico"]; ?>
              <input name="IDTipoViatico<?=$i; ?>" id="IDTipoViatico<?=$i; ?>" type="hidden" value="<?=$datos_viaticos["IDTipoViatico"]; ?>" /></td>
              <td class="TxtTabla">
<?
              if($datos_viaticos["m_i"]<10)
				{
					$datos_viaticos["m_i"]="0".$datos_viaticos["m_i"];
				}
              if($datos_viaticos["m_f"]<10)
				{
					$datos_viaticos["m_f"]="0".$datos_viaticos["m_f"];
				}
              if($datos_viaticos["d_i"]<10)
				{
					$datos_viaticos["d_i"]="0".$datos_viaticos["d_i"];
				}
              if($datos_viaticos["d_f"]<10)
				{
					$datos_viaticos["d_f"]="0".$datos_viaticos["d_f"];
				}
?>
              <?=$datos_viaticos["m_i"]."/".$datos_viaticos["d_i"]."/".$datos_viaticos["y_i"]; ?>
              <input name="FechaIni<?=$i; ?>" id="FechaIni<?=$i; ?>" type="hidden" value="<?=$datos_viaticos["FechaIni"] ?>" /></td>
              <td class="TxtTabla">
              <?=$datos_viaticos["m_f"]."/".$datos_viaticos["d_f"]."/".$datos_viaticos["y_f"]; ?>
              <input name="FechaFin<?=$i; ?>" id="FechaFin<?=$i; ?>" type="hidden" value="<?=$datos_viaticos["FechaFin"] ?>" /></td>
              <td class="TxtTabla">
              <?=$datos_viaticos["Trayecto"]; ?>
              </td>
              <td class="TxtTabla">
              <?=$datos_viaticos["ObjetoComision"]; ?>
              </td>
              <td class="TxtTabla">
				<?
					if($datos_viaticos["viaticoCompleto"]==1)
					{
						echo "No";
					}
					if($datos_viaticos["viaticoCompleto"]==2)
					{
						echo "Si";
					}
				?>
		      <input name="viaticoCompleto<?=$i; ?>" id="viaticoCompleto<?=$i; ?>" type="hidden" value="<?=$datos_viaticos["viaticoCompleto"] ?>" /></td>
              <td align="center" class="TxtTabla">
              <input type="radio" name="eli<?=$i ?>" id="eli<?=$i ?>" value="1" />
              </td>
              <td align="center" class="TxtTabla">
              <input name="eli<?=$i ?>" type="radio" id="eli<?=$i ?>" value="2" checked />
              </td>
            </tr>
<?
			$i++;
		}
?>

            <tr>
              <td colspan="9" align="right" class="TxtTabla"><input name="Enviar1" type="button" class="Boton" value=" Eliminar Viatico" onClick="valida()" >
	            <input name="recarga" id="recarga" type="hidden" value="1" />

	            <input type="hidden" name="cantReg" id="cantReg" value="<?= mssql_num_rows($cur_viaticos) ?>" />
			  </td>
            </tr>
      </table>
 </form>
    </body>
</html>