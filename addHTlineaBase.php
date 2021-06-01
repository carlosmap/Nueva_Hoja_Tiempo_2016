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

if(trim($recarga) == "2")
{
	$error="no";

	$sql_ver="select MAX(version) as version from PlaneacionProyectosVer where id_proyecto=".$cualProyecto;
	$cur_ver=mssql_query($sql_ver);
	if($datos_ver=mssql_fetch_array($cur_ver))
		$version=$datos_ver["version"];
	if(trim($version)=="")
		$version=0;

	$version++;
	$cursorTran1 = mssql_query(" BEGIN TRANSACTION");

	$sql_insert="
			insert into PlaneacionProyectosVer (id_proyecto,id_actividad,unidad,vigencia,mes,esInterno,version,fechaVer,usuarioVer, hombresMes,horasMes,id_categoria,valorPlaneado
			,salarioBase,fechaPlaneacion,unidadPlaneacion,usuarioCrea,fechaCrea,usuarioMod,fechaMod)
			select id_proyecto,id_actividad,unidad,vigencia,mes,esInterno,'".$version."',getdate(), ".$_SESSION["sesUnidadUsuario"]." , hombresMes,horasMes,id_categoria,valorPlaneado
			,salarioBase,fechaPlaneacion,unidadPlaneacion,usuarioCrea,fechaCrea,usuarioMod,fechaMod from PlaneacionProyectos where id_proyecto=".$cualProyecto;;

	$cur_insert=mssql_query($sql_insert);
	if(trim($cur_insert)!="")
	{
		echo ("<script  language='JavaScript' type='text/javascript' >alert('Operación realizada con exito. ');</script>"); 
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");
	}
	else
	{
		echo ("<script  language='JavaScript' type='text/javascript' >alert('Error durante la operación');</script>");
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");	
	}
	
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos03.php?cualProyecto=$cualProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

	


}

$sql_vig_lc="
		select hombresMes,activi_lc.actPrincipal,vigencia, Actividades.macroactividad from (
		select SUM(hombresMes) as hombresMes , actPrincipal, vigencia from PlaneacionProyectos
		inner join Actividades on PlaneacionProyectos.id_actividad=Actividades.id_actividad and PlaneacionProyectos.id_proyecto=Actividades.id_proyecto
		 where PlaneacionProyectos.id_actividad in (
			select id_actividad from Actividades where
			( nivel=3 or nivel=4 )  and id_proyecto=".$cualProyecto."
		)	and PlaneacionProyectos.id_proyecto=".$cualProyecto." 
		group by actPrincipal ,vigencia
		) as activi_lc
		inner join Actividades on Actividades.id_actividad= activi_lc.actPrincipal
		where id_proyecto=".$cualProyecto." order by activi_lc.actPrincipal, vigencia";
$cur_vig_lc=mssql_query($sql_vig_lc);

//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto." and D.retirado is null" ;
$cursor = mssql_query($sql);

?>


<html>
<head>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" >
function cerrar_ventana()
{
	window.close();MM_openBrWindow('htPlanProyectos03.php?cualProyecto=<?=$miProyecto ?>','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');
}
function envia2()
{
	if(confirm('Desea generar la linea base para el proyecto?'))
	{
		document.Form1.recarga.value=2;
		document.Form1.submit();
	}
}
</script>


</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">


<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Generar linea base</td>

  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>  
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="TituloTabla" width="20%" >Proyecto</td>
          <td width="1165" class="TxtTabla" colspan="4">

<?php

				 while ($reg=mssql_fetch_array($cursor)) 
				{
					 echo  "[".$reg[codigo].".".$reg[cargo_defecto]."]  -  ".  ucwords(strtolower($reg[nombre])) ;
					 $inf_proy="[".$reg[codigo].".".$reg[cargo_defecto]."]  -  ".  ucwords(strtolower($reg[nombre])) ;
//echo $inf_proy;
				}

 ?>

		  </td>
        </tr>
        <tr>
          <td colspan="5" class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
        <td colspan="4" class="TituloUsuario">Resumen de la planeaci&oacute;n</td>

<form action="detalleHTlineaBase.php?cualProyecto=<?=$cualProyecto; ?>" method="post"   name="detalle" id="detalle" > 
        <td colspan="2" align="center" class="TxtTabla" width="1%" >
			<input type="submit"  value="Detalle" class="Boton"  > 
		</td>
</form> 
    
      </tr>
		<form action="" type="post"   name="Form1">
        <tr>
          <td colspan="5" class="TxtTabla"><table width="100%" border="0">
            <tr class="TituloTabla2">
              <td>LC</td>
<?
				$cur_vi=mssql_query("select distinct(vigencia) vigencia from PlaneacionProyectos  where id_proyecto=".$cualProyecto." order by vigencia ");
				$i=0;
				while($datos_vi=mssql_fetch_array($cur_vi))
				{
					$vigencias[$i]=$datos_vi["vigencia"];
					$i++;
?>
	              <td><?=$datos_vi["vigencia"]; ?></td>

<?
					$max_vigen=$datos_vi["vigencia"];
				}
?>


              <td>Total</td>
            </tr>
<?php
		$val_totales=array();//ALMACENA EL VALOR TOTAL DE LAS VIGE3NCIAS

		while($datos_vig_lc=mssql_fetch_array($cur_vig_lc))
		{
?>
            <tr  class="TxtTabla">
              <td><?=$datos_vig_lc["macroactividad"]; ?></td>
<?php
			$total_vig=0;	//ALAMAECNA EL VALOR TOTAL DE CADA LC, EN LAS DIFERENTES VIGENCIAS
			$z=-1;
			foreach($vigencias as $vig)
			{
				if($vig==$datos_vig_lc["vigencia"])
				{
					$total_vig+=$datos_vig_lc["hombresMes"];

					$z++;
					$val_totales[$z]+=$datos_vig_lc["hombresMes"];

//echo $val_totales[$z]." --- Z=".$z." val_vig ".$datos_vig_lc["hombresMes"]."<br>";
//$datos_vig_lc["vigencia"]." - "
?>
	              <td><?=$datos_vig_lc["hombresMes"] ?></td>
<?
				//SE CONSULTA LA VIGENCIA, DEL SIGUIENTE REGISTRO,  SIEMPRE Y CUANDO LA VIGENCIA, NO SEA IGUAL AL VALOR DE LA MAXIMA VIGENCIA
				  if($vig<$max_vigen)
					  $datos_vig_lc=mssql_fetch_array($cur_vig_lc);
				}
				else	
				{
					echo "<td>&nbsp;</td>";
					$z++;
				}
			}

?>
              <td align="center"><?=$total_vig; ?></td>
            </tr>
<?
			$total+=$total_vig; 
		}
?>
            <tr class="TituloTabla2">
              <td>Totales</td>
<?
				foreach($val_totales as $val)
				{
?>
	              <td><?=$val ?></td>
<?
				}
?>
              <td><?=$total; ?></td>
            </tr>
          </table></td>
<?php
//		$mes= array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$sql_fech="select day(GETDATE()) as dia ,MONTH(GETDATE()) as mes,YEAR(GETDATE())as ano";
		$cur_fech=mssql_query($sql_fech);
		$datos_fech=mssql_fetch_array($cur_fech);

		if(!isset($enviar))
		{
				$enviar=1;
		}

		$sql_fecha_ini="select * from HojaDeTiempo.dbo.AutorizaEDT  where id_proyecto=" . $cualProyecto." order by (secuencia) desc";
		$cur_fecha_ini=mssql_query($sql_fecha_ini);
		if($datos_fecha_ini=mssql_fetch_array($cur_fecha_ini))
		{
//			$fecha_ini=trim($datos_fecha_ini["fechaIniProy"]);
			$comenta_vobo=$datos_fecha_ini["comentaVoBo"];
		}
?>
          </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5"   class="TituloUsuario">Importante</td>
        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla"><!-- readonly-->
            <p>
              <!--
<a href="javascript:cal.popup();"><img src="imagenes/cal.gif" width="16" height="16" border="0" /></a>
-->
            Al generar esta acci&oacute;n, se generara una copia identica de los lotes planeados hasta la fecha en las diferentes divisiones y actividades que conforman la EDT.</p>
            <p>Esta operaci&oacute;n es irreversible. Asegurese de generar la linea base del proyecto en el momento indicado. </p>
            <p>Los datos registrados en la linea base, seran tomados como referencia comparativa entre lo planeado inicialmente y el comportamiento real del proyecto. </p>
			<br>
</td>
          </tr>

<?php  
	//si se lecciona no liberara, se muestra la seccion, para ingresar las observaciones      
//if($enviar==1)
{
?>

<?php
}
?>		

      </table>



<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<?
	$sql_ver="select MAX(version) as version from PlaneacionProyectosVer where id_proyecto=".$cualProyecto;
	$cur_ver=mssql_query($sql_ver);
	if($datos_ver=mssql_fetch_array($cur_ver))
	{
		$version=$datos_ver["version"];
?>
  <tr>
    <td align="center" class="TituloTabla2"><p> Nota: Se ha generado con anterioridad un versi&oacute;n del proyecto. &iquest; Desea generar la version <?=($version+1) ?> ?</p> </td>
  </tr>
<?
	}
	if(trim($version)=="")
		$version=0;
?>

  <tr>
    <td align="center" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
          <td align="center" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<?php echo $cualProyecto; ?>">
  		    <input name="inf_proy" type="hidden" id="inf_proy" value="<?php echo $inf_proy; ?>">

  		    <input name="Submit" type="button" class="Boton" value="Generar" onClick="envia2();" >&nbsp; &nbsp;                                                                                                                 
  		    <input type="button" class="Boton" value="Cancelar" onClick="cerrar_ventana()" ></td>
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
