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
//echo "procesando solicitud";


}

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

<script language="JavaScript" src="calendar.js"></script>


</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" type="post"   name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Detalle planeaci&oacute;n del proyecto</td>

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
        <tr>
          <td class="TituloTabla" width="20%" >Proyecto</td>
          <td width="1165" class="TxtTabla">

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
          
          <td colspan="2" class="TxtTabla">&nbsp;</td>
          </tr>
        <tr>
          <td class="TituloUsuario" colspan="2">Planeacion de las actividades en las vigencias</td>
        </tr>
        <tr>
          <td class="TxtTabla" colspan="2" align="center"><table width="100%" border="0">
<?
		$cur_lc=mssql_query("select id_actividad,macroactividad,nombre from Actividades where nivel=1 and id_proyecto=".$cualProyecto);
		while($datos_lc=mssql_fetch_array($cur_lc))
		{
?>
            <tr>
              <td colspan="4"><?=$datos_lc["macroactividad"]." - ".$datos_lc["nombre"] ?></td>
              </tr>
<?
                $cur_lt=mssql_query("select id_actividad,macroactividad,nombre from Actividades where nivel=2 and id_proyecto=".$cualProyecto." and dependeDe=".$datos_lc["id_actividad"]);
                while($datos_lt=mssql_fetch_array($cur_lt))
                {
					//CONSULTA LAS DIVISIONES QUE HACEN PARTE DEL LT
					$cur_div_t =mssql_query("select id_actividad,macroactividad,nombre from Actividades where nivel=3 and id_proyecto=".$cualProyecto." and dependeDe=".$datos_lt["id_actividad"]." and actPrincipal=".$datos_lc["id_actividad"]." ");
						while($datos_div_t=mssql_fetch_array($cur_div_t))
						{
//---------------
							$ban=0; //PERMITE DETERMINAR SI LA DIVISION TIENE PLANEACION
							//CONSULTA LA PLANEACIÓN DE LAS DIVISIONES 
								$cur_div=mssql_query("select distinct(PlaneacionProyectos.id_actividad),Actividades.macroactividad,Actividades.nombre from PlaneacionProyectos
				inner join Actividades on Actividades.id_actividad=PlaneacionProyectos.id_actividad  and Actividades.id_proyecto=PlaneacionProyectos.id_proyecto
				where PlaneacionProyectos.id_proyecto=".$cualProyecto." and Actividades.dependeDe=".$datos_lt["id_actividad"]." and Actividades.actPrincipal=".$datos_lc["id_actividad"]." and  Actividades.id_actividad=".$datos_div_t["id_actividad"]." and nivel=3 ");
								while($datos_div=mssql_fetch_array($cur_div))
								{
									$ban=1; //SI TIENE PLANEACION, SE CONSULTAN LA PLANEACION DE LAS ACTIVIDADES
?>
									<tr>
									  <td colspan="4"><?=$datos_lt["macroactividad"]." - ".$datos_lt["nombre"]." - ".$datos_div["macroactividad"]." - ".$datos_div["nombre"] ?></td>
									  </tr>
<?
?>
-----------------------
                                        <tr class="TituloTabla2">
                                          <td>Unidad</td>
                                          <td>Nombre</td>
                                          <td>Vigencia</td>
                                          <td>Total</td>
                                        </tr>
<?

									//CONSULTA LOS PARTICIPANTES DE LA DIVISION, ASOCIADOS EN LA PLANEACION
									$cur_partici=mssql_query("select distinct(PlaneacionProyectos.unidad) as unidad, (Usuarios.apellidos+' '+Usuarios.nombre) as nombre from PlaneacionProyectos
									inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
									 where id_actividad=".$datos_div["id_actividad"]." and PlaneacionProyectos.id_proyecto=".$cualProyecto." ");

									while($datos_partici=mssql_fetch_array($cur_partici))
									{		
					?>

                                        <tr>
                                          <td><?=$datos_partici["unidad"] ?></td>
                                          <td><?=$datos_partici["nombre"] ?></td>
<?
											//CONSULTA EL H/M DE CADA PARTICIPANTE
											$cur_hom_m=mssql_query(" select SUM(hombresMes) as hombre, vigencia from PlaneacionProyectos
											 where id_actividad=".$datos_div["id_actividad"]."  and unidad=".$datos_partici["unidad"]." and PlaneacionProyectos.id_proyecto=".$cualProyecto."  group by vigencia");
											while($datos_hom_m=mssql_fetch_array($cur_hom_m))
											{	
?>
		                                          <td><?=$datos_hom_m["hombre"] ?></td>
<?
											}
?>
                                        </tr>
<?
									}
?>
----------------
<?

									//CONSULTA LA PLANEACION DE ACTIVIDADES
									$cur_acti=mssql_query("select distinct(PlaneacionProyectos.id_actividad),Actividades.macroactividad,Actividades.nombre from PlaneacionProyectos
									inner join Actividades on Actividades.id_actividad=PlaneacionProyectos.id_actividad  and Actividades.id_proyecto=PlaneacionProyectos.id_proyecto
									where PlaneacionProyectos.id_proyecto=".$cualProyecto."
									 and Actividades.dependeDe=".$datos_div["id_actividad"]." and Actividades.actPrincipal=".$datos_lc["id_actividad"]." and nivel=4");
									while($datos_acti=mssql_fetch_array($cur_acti))
									{
					?>
										<tr>
										  <td colspan="4"><?=$datos_lt["macroactividad"]." - ".$datos_lt["nombre"]." - ".$datos_div["macroactividad"]." - ".$datos_div["nombre"]." - ".$datos_acti["macroactividad"]." - ".$datos_acti["nombre"] ?></td>
										  </tr>
				<?
									}
		
									$z++;
									
								}
								//SI LA DIVISION NO TIENE PLANEACION, SE CONSULTA LAS ACTIVIDADES ASOCIADAS A ELLA, PARA CONSULTAR LA PLANEACION DE ESTAS
								if($ban==0)
								{
									$cur_acti2=mssql_query("select distinct(PlaneacionProyectos.id_actividad),Act.macroactividad,Act.nombre 
,(select macroactividad from Actividades where Actividades.id_proyecto=".$cualProyecto." and id_actividad=Act.dependeDe) as macro_dep,
(select nombre from Actividades where Actividades.id_proyecto=".$cualProyecto." and id_actividad=Act.dependeDe) as nom_dep 
															from PlaneacionProyectos
															inner join Actividades as Act on Act.id_actividad=PlaneacionProyectos.id_actividad  and Act.id_proyecto=PlaneacionProyectos.id_proyecto
															where PlaneacionProyectos.id_proyecto=".$cualProyecto."
															 and Act.dependeDe=".$datos_div_t["id_actividad"]." 
															 and Act.actPrincipal=".$datos_lc["id_actividad"]."
															and nivel=4");
									while($datos_acti2=mssql_fetch_array($cur_acti2))
									{
					?>
										<tr>
										  <td colspan="4"><?=$datos_lt["macroactividad"]." - ".$datos_lt["nombre"]." - ".$datos_acti2["macro_dep"]." - ".$datos_acti2["nom_dep"]." - ".$datos_acti2["macroactividad"]." - ".$datos_acti2["nombre"] ?></td>
										  </tr>
				<?
									}
								}
///----------

						}

                }
?>
            <tr class="TxtTabla">
              <td>&nbsp;</td>
			</tr>
<?
		}
?>

          </table></td>
        </tr>


      </table>

<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<?php echo $cualProyecto; ?>">
  		    <input name="inf_proy" type="hidden" id="inf_proy" value="<?php echo $inf_proy; ?>">

  		    <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()" ></td>
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
