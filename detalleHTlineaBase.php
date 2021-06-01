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


function cerrar_ventana()
{
	window.close();MM_openBrWindow('htPlanProyectos03.php?cualProyecto=<?=$miProyecto ?>','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');
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
<?


	//CONSULTA LAS VIGENCIAS DEL PROYECTO
	$cur_vi=mssql_query("select distinct(vigencia) vigencia from PlaneacionProyectos  where id_proyecto=".$cualProyecto." order by vigencia ");
	$i=0;
	while($datos_vi=mssql_fetch_array($cur_vi))
	{
		$vigencias[$i]=$datos_vi["vigencia"];
		$i++;
		$max_vigen=$datos_vi["vigencia"];
		$can_fil=$i;
	}


	//FUNCION QUE GENERA LA TABLA DE INFORMACION DE LA PLANEACION, POR CADA ACTIVIDAD
	function vigenciass($cualProyecto,$datos_div_id_actividad,$vigencias,$max_vigen)
	{ 
		static $valor=0;

		$valor++;
		if($valor==1) // SE IMPRIME LA FILA, DE NOMBRE UNIDAD Y VIGENCIAS, LA PRIMERA VEZ QUE SE ACCEDE A LA FUNCION
		{
		
?>

                                        <tr class="TituloTabla2">
                                          <td>Unidad</td>
                                          <td>Nombre</td>
<?
                                                foreach($vigencias as $vig)
                                                {	
                                                        echo "<td>".$vig."</td>";
                                                     
                                                }
?>
                                          <td>Total</td>
                                        </tr>
		
<?
		}
									//CONSULTA LOS PARTICIPANTES DE LA DIVISION, ASOCIADOS EN LA PLANEACION
									$cur_partici=mssql_query(" select * from (select distinct(PlaneacionProyectos.unidad) as unidad, (Usuarios.apellidos+' '+Usuarios.nombre) as nombre from PlaneacionProyectos
									inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
									 where id_actividad=".$datos_div_id_actividad." and PlaneacionProyectos.id_proyecto=".$cualProyecto." 

										union

										select distinct(PlaneacionProyectos.unidad) as unidad, (TrabajadoresExternos.apellidos+' '+TrabajadoresExternos.nombre) as nombre from PlaneacionProyectos
								  		inner join ParticipantesExternos on ParticipantesExternos.id_proyecto= PlaneacionProyectos.id_proyecto and ParticipantesExternos.id_actividad=PlaneacionProyectos.id_actividad
										and ParticipantesExternos.consecutivo=PlaneacionProyectos.unidad
										inner join TrabajadoresExternos on ParticipantesExternos.consecutivo=TrabajadoresExternos.consecutivo
										 where PlaneacionProyectos.id_actividad=".$datos_div_id_actividad." and PlaneacionProyectos.id_proyecto=".$cualProyecto." 
										) participantes ");

									while($datos_partici=mssql_fetch_array($cur_partici))
									{		
					?>

                                        <tr class="TxtTabla">
                                          <td><?=$datos_partici["unidad"] ?></td>
                                          <td><?=$datos_partici["nombre"] ?></td>
<?
											//CONSULTA EL H/M DE CADA PARTICIPANTE
											$cur_hom_m=mssql_query(" select SUM(hombresMes) as hombre, vigencia from PlaneacionProyectos
											 where id_actividad=".$datos_div_id_actividad."  and unidad=".$datos_partici["unidad"]." and PlaneacionProyectos.id_proyecto=".$cualProyecto."  group by vigencia order by (vigencia) ");
											while($datos_hom_m=mssql_fetch_array($cur_hom_m))
											{	
?>
		                                      <!--    <td><? //=$datos_hom_m["hombre"] ?></td>-->
<?

                                                    $z=-1;
                                                    foreach($vigencias as $vig)
                                                    {
//echo $datos_hom_m["vigencia"]." ---*** ".$vig." -----".$max_vigen."<br>";
                                                        if($vig==$datos_hom_m["vigencia"])
                                                        {
                                                            $total_vig+=$datos_hom_m["hombre"];
                                        
                                                            $z++;
                                                            $val_totales[0][$z]+=$datos_hom_m["hombre"];
                                        
                                        //echo $val_totales[$z]." --- Z=".$z." val_vig ".$datos_hom_m["hombre"]."<br>";
                                        //$datos_vig_lc["vigencia"]." - "
 ?>
                                                          <td align="center" ><?=$datos_hom_m["hombre"] ?> </td>
<?
                                                        //SE CONSULTA LA VIGENCIA, DEL SIGUIENTE REGISTRO,  SIEMPRE Y CUANDO LA VIGENCIA, NO SEA IGUAL AL VALOR DE LA MAXIMA VIGENCIA
                                                          if($vig<$max_vigen)
                                                              $datos_hom_m=mssql_fetch_array($cur_hom_m);
                                                        }
                                                        else	
                                                        {
															$val_totales[0][$z]+=0;
                                                            echo "<td>&nbsp;  </td>";
                                                            $z++;
                                                        }
                                                    }
?>
													<td align="center" > <?=$total_vig; ?></td>													
<?
													$total_vig=0;
											}
?>

                                        </tr>

<?
									}
?>
										<tr  > 
											<td class="TituloTabla"></td>
											<td class="TituloTabla" align="center" >Total</td>
<?

										for($mm=0;$mm<=$z;$mm++)						
										{
											echo "<td class='TituloTabla' align='center' >".$val_totales[0][$mm]." </td>";
											if(trim($val_totales[0][$mm])!="")
												$val_t+=$val_totales[0][$mm];

										}
?>
											<td class='TituloTabla' align="center" ><?=$val_t; ?></td>
                                        </tr>
<?
		}
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

          <td colspan="2" class="TxtTabla"> <a  href="addHTlineaBase.php?cualProyecto=<?php echo $cualProyecto; ?>" class="menu" > << Regresar a la Linea Base  </a> </td>
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
              <td colspan="<?=$i+4; ?>" class="TituloTabla" ><?=$datos_lc["macroactividad"]." - ".$datos_lc["nombre"] ?></td>
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
										<tr class="TxtTabla" >
											<td>&nbsp;</td>
										</tr>
										<tr class="TituloTabla"  >
											<td colspan="<?=3+$can_fil ?>"></td>
										</tr>
                                            <tr class="TxtTabla" >
                                              <td colspan="4">&nbsp; <b> - <?=$datos_lt["macroactividad"]." - ".$datos_lt["nombre"]." - ".$datos_div["macroactividad"]." - ".$datos_div["nombre"] ?></b></td>
                                              </tr>
										<tr class="TituloTabla"  >
											<td colspan="<?=3+$can_fil ?>"></td>
										</tr>
<?
			
									vigenciass($cualProyecto,$datos_div["id_actividad"],$vigencias,$max_vigen);


									//CONSULTA LA PLANEACION DE ACTIVIDADES
									$cur_acti=mssql_query("select distinct(PlaneacionProyectos.id_actividad),Actividades.macroactividad,Actividades.nombre from PlaneacionProyectos
									inner join Actividades on Actividades.id_actividad=PlaneacionProyectos.id_actividad  and Actividades.id_proyecto=PlaneacionProyectos.id_proyecto
									where PlaneacionProyectos.id_proyecto=".$cualProyecto."
									 and Actividades.dependeDe=".$datos_div["id_actividad"]." and Actividades.actPrincipal=".$datos_lc["id_actividad"]." and nivel=4");
									while($datos_acti=mssql_fetch_array($cur_acti))
									{
					?>
										<tr class="TxtTabla" >
											<td>&nbsp;</td>
										</tr>
										<tr class="TituloTabla"  >
											<td colspan="<?=3+$can_fil ?>" ></td>
										</tr>
                                            <tr class="TxtTabla" >
                                              <td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<b>- - <?=$datos_lt["macroactividad"]." - ".$datos_lt["nombre"]." - ".$datos_div["macroactividad"]." - ".$datos_div["nombre"]." - ".$datos_acti["macroactividad"]." - ".$datos_acti["nombre"] ?></b></td>
                                              </tr>
										<tr class="TituloTabla"  >
											<td colspan="<?=3+$can_fil ?>"></td>
										</tr>
				<?
										vigenciass($cualProyecto,$datos_acti["id_actividad"],$vigencias,$max_vigen);
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
										<tr class="TxtTabla" >
											<td>&nbsp;</td>
										</tr>
										<tr class="TituloTabla"  >
											<td colspan="<?=3+$can_fil ?>"></td>
										</tr>
                                            <tr class="TxtTabla" >
                                              <td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;- - <b><?=$datos_lt["macroactividad"]." - ".$datos_lt["nombre"]." - ".$datos_acti2["macro_dep"]." - ".$datos_acti2["nom_dep"]." - ".$datos_acti2["macroactividad"]." - ".$datos_acti2["nombre"] ?></b></td>
                                              </tr>
										<tr class="TituloTabla"  >
											<td colspan="<?=3+$can_fil ?>"></td>
										</tr>
				<?
										vigenciass($cualProyecto,$datos_acti2["id_actividad"],$vigencias,$max_vigen);
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
          <td colspan="2" class="TxtTabla"> <a  href="addHTlineaBase.php?cualProyecto=<?php echo $cualProyecto; ?>" class="menu" > << Regresar a la Linea Base  </a> </td>
          </tr>

</table >

<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
          <td align="right" class="TxtTabla">
  		    <input name="Submit" type="button" class="Boton" value="Cerrar ventana" onClick="cerrar_ventana()" ></td>
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

<?PHP mssql_close ($conexion); ?>	
