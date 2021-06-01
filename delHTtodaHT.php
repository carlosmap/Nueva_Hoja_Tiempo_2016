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
//MM_openBrWindow('htFacturacion.php?pMes=<? //=$pMes ?>','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');
}


//-->
</script>
<?php
session_start();

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//--Trae los proyectos en los que una persona tiene facturación agrupada así:
//--Proyecto, Actividad, Horario, clase de tiempo, localización, cargo
$sql02="SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  ";
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
$sql02=$sql02." AND A.unidad = " . $unidad_u ;
$sql02=$sql02." AND A.mes = " . $pMes ;
$sql02=$sql02." AND A.vigencia = " . $pAno." and esInterno='I' " ;
$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
$sql02=$sql02." ORDER BY B.nombre " ;
$cursor02 =	 mssql_query($sql02);

$totalDiasMes = 0;
if ($pMes<10) {
	$cantElMes = "0" . $pMes;
}
else {
	$cantElMes = "" . $pMes;
}
$sql04="select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$pAno."' + '".$cantElMes."' + '01')))) diasDelMes ";
$cursor04 =	 mssql_query($sql04);
if ($reg04 = mssql_fetch_array($cursor04)) {
	$totalDiasMes =  $reg04['diasDelMes'];
}

		if($recarga==2)
		{
			$error="no";
			mssql_query("BEGIN TRANSACTION");
		
					$sql_update="
						 delete FacturacionProyectos
						  WHERE FacturacionProyectos.unidad = ".$unidad_u." AND mes =  ".$pMes." AND vigencia = ".$pAno."  and year(fechaFacturacion)=".$pAno."  and month(fechaFacturacion)=".$pMes." and esInterno='I' ";
					$cur_update=mssql_query($sql_update);

//echo $sql_update."<br>".mssql_get_last_message()."<br><br>";

					if(trim($cur_update)=="")
					{
						$error="si";
						break;
					}
					if(trim($error)=="no")
					{
		
						$sql_elimi_viatico="delete from ViaticosProyectosHT where unidad=".$unidad_u." and mes=".$pMes." 
							and vigencia=".$pAno." and esInterno='I' ";
			
						$cur_elimini_viatico=mssql_query($sql_elimi_viatico);
						if(trim($cur_elimini_viatico)=="")
						{
							$error="si";
							break;
						}
					}			

					if(trim($error)=="no")
					{
		
						$sql_elimi_adp="delete from  AdpHT where unidad=".$unidad_u." and mes=".$pMes." 
							and vigencia=".$pAno." ";
			
						$cur_elimini_adp=mssql_query($sql_elimi_adp);
						if(trim($cur_elimini_adp)=="")
						{
							$error="si";
							break;
						}
					}			

					if(trim($error)=="no")
					{
						$sql_del="delete FROM VoBoFactuacionProyHT WHERE unidad=".$unidad_u." and vigencia=".$pAno." and mes= ".$pMes."  and esInterno='I' ";
						$cur_del=mssql_query($sql_del);
		
		//echo $sql_del."<br>".mssql_get_last_message()."<br>-- ".mssql_num_rows($cur_del)."<br>";
		
						if(trim($cur_del)=="")
						{
							$error="si";							
						}
					}

					if(trim($error)=="no")
					{
						$sql_elimi_Vobo="delete from  VoBoFirmasHT where vigencia=".$pAno." and unidad=".$unidad_u." and mes=".$pMes;
			
						$cur_elimini_Vobo=mssql_query($sql_elimi_Vobo);
						if(trim($cur_elimini_Vobo)=="")
						{
							$error="si";
							break;
						}
					}

			if(trim($error)=="no")
			{
//				mssql_query(" ROLLBACK TRANSACTION");
				mssql_query(" COMMIT TRANSACTION");
				echo "<script>alert('La operaci\xf3n se realiz\xf3 con \xe9xito')</script>";
			}
			else
			{
				mssql_query(" ROLLBACK TRANSACTION");
				echo "<script>alert('Error en la operaci\xf3n')</script>";
			}
			echo "	<script>window.close();MM_openBrWindow('htFacturacion.php?pMes=".$pMes."&pAno=".$pAno."','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>";

		}

?>


<html>
<head>

<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="javascript" type="text/javascript">

	function valida(mes,ano)
	{
		if(confirm('Desea eliminar toda la facturaci\xf3n para el mes de '+mes+' del '+ano+'?'))
		{
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
      <table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF" >
        <tr>
<?
	$cur_usu=mssql_query("select unidad,UPPER( nombre) nombre,UPPER( apellidos) apellidos from Usuarios where unidad=".$unidad_u);
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
        <td colspan="4" class="TituloUsuario">.:: Informaci&oacute;n de la facturaci&oacute;n</td>

    
      </tr>
        <tr>

			<tr>
              <td class="TituloTabla2">Vigencia</td>
              <td align="left" class="TxtTabla"><?=$pAno ?></td>
            </tr>

			<tr>
              <td class="TituloTabla2">Mes</td>
<?
$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
              <td align="left" class="TxtTabla"><?=$mes[$pMes] ?></td>
            </tr>
          
          </table></td>

    </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>

        <tr>
          <td colspan="5"  class="TxtTabla"><!-- readonly-->
<?
//********************************************


	include ("htTablaFacturacion.php");

?>
  
<?
//*********************************************
?>
		  </td>
    </tr>

	

      </table>



<table width="100%"  border="0" cellspacing="1" cellpadding="0">


  <tr>
    <td align="right" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="TxtTabla"><strong>Atenci&oacute;n: Esta operaci&oacute;n es irreversible. No podra recuperar la informaci&oacute;n de la vigencia y mes seleccionado.</strong></td>
  </tr>
  <tr>
    <td align="center" class="TxtTabla"><strong>&#191;Esta seguro de eliminar toda la informaci&oacute;n asociada 
      en la hoja de tiempo para el mes de <?=$mes[$pMes] ?>?</strong></td>
  </tr>
  <tr>
          <td align="center" class="TxtTabla"><input type="button" class="Boton" value="Cancelar" onClick="cerrar()" > &nbsp;
<?
			//PENDIENTE
			///****** INCLUIR VALIDACIONES DEL BOTON, RELACIONADOS CON EL VOBO DEL JEFE INMEDIATO, CONTRATOS, Y PROYECTO
			//CUANDO TENGA ALUNA DE ESTAS FIRMAS, NO DEBE MOSTRAR EL BOTON, Y MANTENDRA INABILITADOS LAS AREAS DE TEXTO
?>
          <input name="guardar" type="button" class="Boton" id="guardar" value="Eliminar" onClick="valida('<?=$mes[$pMes] ?>','<?=$pAno ?>')" >
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
