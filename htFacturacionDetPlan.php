<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

////PARA QUITAR
//$V=2013;

//Trae La informacion del proyecto
$sql="SELECT P.id_proyecto,P.nombre,P.codigo, P.cargo_defecto,  convert(nvarchar,  fechaInicio ,103) fechaInicio,  
 convert(nvarchar,(select  max(fecha_fin) from Actividades where id_proyecto=".$cualProyecto." ) ,103) fechaFinaliza  ";
$sql=$sql." FROM proyectos P " ;
$sql=$sql." WHERE  P.id_proyecto = ".$cualProyecto.""; // . $cualProyecto ;
$cursor = mssql_query($sql);

//echo date('Y')." -------------------------";
if(!(isset($V)))
{
	$V=date('Y');
	
}

//CONSULTA LA INFORMACION DEL USUARIO
$cur_usu=mssql_query("
select (Usuarios.nombre+' '+Usuarios.apellidos) usuario, Usuarios.unidad, upper(Departamentos.nombre) dep, upper(Divisiones.nombre) div , Categorias.nombre cate from Usuarios 
inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
inner join Divisiones on Departamentos.id_division=Divisiones.id_division
inner join Categorias on Usuarios.id_categoria=Categorias.id_categoria
where unidad=".$laUnidad."");

//CONSULTA LAS ACTIVIDADES, EN LAS QUE EL USUARIO SE ENCUENTRA PLANEADO, COMO PARTICIPANTE ACTIVO
		$sql_parti="
			select distinct(P.id_actividad),  convert(nvarchar, fecha_inicio ,103) fecha_inicio, convert(nvarchar, fecha_fin ,103) fecha_fin  ,P.unidad,P.esInterno, P.vigencia , A.id_proyecto , A.macroactividad,UPPER(A.nombre) nombre ,A.id_actividad from PlaneacionProyectos  P
			inner join Actividades A on P.id_proyecto=A.id_proyecto and P.id_actividad=A.id_actividad
			inner join ParticipantesActividad PA on P.id_proyecto=PA.id_proyecto and P.id_actividad=PA.id_actividad and P.unidad=PA.unidad
			WHERE  P.id_proyecto = ".$cualProyecto." and P.unidad=".$laUnidad." and P.esInterno='".$cualTipoUsu."' and PA.estado='A' and P.vigencia=".$V." ";

			$cur_parti=mssql_query($sql_parti);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--


window.name="winHTDetP";


function envia0()
{

		document.Form1.recarga.value = 1;
		document.Form1.submit();
}

//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<? include("bannerArriba.php") ; ?>
<!--
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;">
Hoja de tiempo - PLANEACI&Oacute;N</div>
-->


    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario"> .: PROYECTO - PLANEACI&Oacute;N</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form name="Form1" id="Form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TituloTabla2">
        <td width="15%">Proyecto</td>
        <td  colspan="3" align="left" class="TxtTabla"><? echo "[". $reg[cargo_defecto]. ".". $reg[codigo]. "] ".$reg[nombre]; ?></td>
        </tr>



	   <tr valign="top" class="TxtTabla">
	     <td width="15%" class="TituloTabla2">Fecha de Inicio (dd/mm/aaaa)</td>
	     <td><? echo  $reg[fechaInicio] ; ?></td>
	     <td width="25%" class="TituloTabla2">Fecha de Finalizaci&oacute;n  (dd/mm/aaaa)</td>
	     <td><? echo  $reg[fechaFinaliza] ; ?></td>
	     </tr>

	   <tr valign="top" class="TxtTabla">
	     <td class="TituloTabla2">Horarios establecidos</td>
	     <td colspan="3"><table width="100%" border="0">

<?php
	$cur_horarios=mssql_query("select *, upper(NomHorario) nombre from Horarios where IDhorario in(
		select IDhorario from HorariosProy where id_proyecto=".$cualProyecto.")");
	while($datos_horar=mssql_fetch_array($cur_horarios))
	{
?>
	       <tr>
	         <td><?=$datos_horar["nombre"] ?> [ 
	         <?=$datos_horar["Lunes"] ?> -
	         <?=$datos_horar["Martes"] ?> -
	         <?=$datos_horar["Miercoles"] ?> -
	         <?=$datos_horar["Jueves"] ?> -
	         <?=$datos_horar["Viernes"] ?> -
	         <?=$datos_horar["Sabado"] ?> -
	         <?=$datos_horar["Domingo"] ?> ]
  			 </td>
	         </tr>
<?
	}
?>
	       </table></td>
	     </tr>
	   <tr valign="top" class="TxtTabla">
	     <td width="15%" class="TituloTabla2">Tipo de viaticos</td>
		<td>
			<table>

					
<?
	$cur_tipo_viatico=mssql_query("select *, upper(NomTipoViatico) nombre from TiposViatico where IDTipoViatico in (select IDTipoViatico  from TiposViaticoProy where id_proyecto=".$cualProyecto." )");
	while($dato_tipo_viatico=mssql_fetch_array($cur_tipo_viatico))
	{
?>
			<tr>
			     <td><?=$dato_tipo_viatico["nombre"]; ?></td>
			</tr>
<?
	}
?>

			</table>
		</td>
	     <td width="15%" class="TituloTabla2">Sitios de trabajo</td>
		<td>
			<table>
<?
	$cur_sitios=mssql_query("select *, upper(NomSitio) nombre from SitiosTrabajo where id_proyecto=".$cualProyecto);
	
	while($dato_sitio=mssql_fetch_array($cur_sitios))
	{
?>
			<tr>
	     <td><?=$dato_sitio["nombre"]; ?></td>
			</tr>
<?
	}
?>
			</table>
		</td>
	   </tr>

	  <? } ?>
	   <tr valign="top" class="TxtTabla">
	     <td colspan="4" class="TituloUsuario"  HEIGHT="5" >  </td>
	     </tr>
       <?
	  while ($reg=mssql_fetch_array($cur_usu)) {
	  ?>

               <tr valign="top" class="TxtTabla">
                 <td width="15%" class="TituloTabla2">Usuario</td>
                 <td><? echo  $reg[usuario] ; ?></td>
                 <td width="15%" class="TituloTabla2">Unidad</td>
                 <td><? echo  $reg[unidad] ; ?></td>
          </tr>
               <tr valign="top" class="TxtTabla">
                 <td width="15%" class="TituloTabla2">Divisi&oacute;n</td>
                 <td><? echo  $reg[div] ; ?></td>
                 <td width="15%" class="TituloTabla2">Categoria</td>
                 <td><? echo  $reg[cate] ; ?></td>
          </tr>
               <tr valign="top" class="TxtTabla">
                 <td width="15%" class="TituloTabla2">Departamento</td>
                 <td colspan="3"><? echo  $reg[dep] ; ?></td>
          </tr>
	  <? } ?>

    </table>
		
 
     

		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td >
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td width="20%" colspan="2"  align="center">&nbsp;</td>
            <td width="20%"  align="center">&nbsp;</td>
            <td width="30%"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td colspan="3" rowspan="2"  align="center" ><table width="100%" border="0" cellpadding="0" cellspacing="1"   bgcolor="#FFFFFF" >
                <tr>
                <td colspan="3" class="TituloUsuario" height="2" align="left" >Criterios de consulta </td>
                </tr>
          <tr class="TxtTabla" >
            
            <td width="20%"  align="center" class="TituloTabla2">Vigencia</td>
            <td width="80%"  align="left" colspan="2" >
<?

				$cur_vigencia=mssql_query("select distinct(vigencia) vigencia, year(getdate()) fech_actu from PlaneacionProyectos where id_proyecto=".$cualProyecto." and  unidad=".$laUnidad." and esInterno='".$cualTipoUsu."' order by (vigencia) ");

?>
              <select name="V" class="CajaTexto" id="V" onchange="document.Form1.submit();" >
<?
				while($datos_vigencia=mssql_fetch_array($cur_vigencia))
				{
					if(!(isset($V)))
						$V=$datos_vigencia["fech_actu"] ;

					$sel="";
					if(trim($V)==$datos_vigencia["vigencia"] )
						$sel="selected";
?>

					<option value="<?=$datos_vigencia[vigencia]; ?>" <? echo $sel; ?>  ><?=$datos_vigencia[vigencia]; ?></option>
<?
				}
?>
              </select>             </td>
            
          </tr>

  <tr class="TxtTabla"  bgcolor="#FFFFFF"  >
            <td  align="center" class="TituloTabla2">Mostrar por </td>
<?php
		if(!isset($hm))
			$hm="hombre";
?>
            <td  align="center" class="TxtTabla">Hombres/Mes <input type="radio" name="hm" id="hm" <? if($hm=="hombre") { echo "checked"; } ?>  value="hombre"  onclick="document.Form1.submit()" /> </td>
            <td  align="center" class="TxtTabla">Horas/Mes <input type="radio" name="hm" id="hm" <? if($hm=="horas") { echo "checked"; } ?>   value="horas" onclick="document.Form1.submit()" /> </td>
          </tr>
          <tr class="TxtTabla"  bgcolor="#FFFFFF"  >
            <td colspan="3"  align="right" class="TxtTabla"><input name="Consultar" onclick="envia0();" type="submit" class="Boton" id="Consultar" value="Consultar" /></td>
            </tr>

            </table></td>
            <td width="30%" rowspan="4"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%" rowspan="3"  align="center"></td>

          </tr>

        
          <tr class="TxtTabla" >


        	    <td colspan="5"  align="right">&nbsp;</td>
        	    </tr>

  	      </table>
			</td>

          </tr>
        </table>	
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr class="TxtTabla">
    <td align="leftt" width="13%"><img src="imagenes/ico1.gif" width="77" height="16">Planeado</td>

    <td align="leftt" width="30%"><img src="../portal/imagenes/ico1.gif" width="77" height="16">Excede Hombre/Mes la planeaci&oacute;n</td>
    <td align="right" width="50%">&nbsp;</td>
    <td>&nbsp;    </td>
  </tr>
</table>	
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">

          <tr>
            <td  align="left" class="TituloUsuario">.: Planeaci&oacute;n Actividad</td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla"><table width="100%" border="0">
              <tr class="TituloTabla2">


                <td width="32%" colspan="3" rowspan="3">Actividad</td>
                <td width="3%" rowspan="4">Total</td>


                <td colspan="14"><? echo $V; ?></td>


              </tr>
              <tr class="TituloTabla2">
                <td>Horas MES</td>

                <td>Enero</td>
                <td  >Febrero</td>
                <td  >Marzo</td>
                <td >Abril</td>
                <td  >Mayo</td>
                <td  >Junio</td>
                <td  >Julio</td>
                <td  >Agosto</td>
                <td  >Septiembre</td>
                <td  >Octubre</td>
                <td  >Noviembre</td>
                <td  >Diciembre</td>
              </tr>
<?
				//CONSULTA LA CANTIDAD DE HORAS, ESTIPULADAS, EN CAMPO Y OFICINA, PARA CADA UNO DE LOS MESES DE LA VIGENCIA
				$cur_horas_laborales=mssql_query("select  mes,hOficina,hCampo from horasydiaslaborales where vigencia=".$V." order by mes");
		
?>
              <tr class="TituloTabla2">

                <td>Oficina</td>

<?
				while($datos_horas_laborales=mssql_fetch_array($cur_horas_laborales))
				{
?>
	                <td width="4%"><?=$datos_horas_laborales[hOficina]; ?></td>
<?
				}
				mssql_data_seek($cur_horas_laborales,0);
?>

              </tr>
              <tr class="TituloTabla2">
                <td width="22%">Nombre</td>
                <td width="5%">Fecha Inicial<br>(dd/mm/aaaa)</td>
                <td width="5%">Fecha Final <br /> (dd/mm/aaaa)</td>

                <td width="4%">Campo</td>

<?
				while($datos_horas_laborales=mssql_fetch_array($cur_horas_laborales))
				{
?>
	                <td width="4%"><?=$datos_horas_laborales[hCampo]; ?></td>
<?
				}
?>

                </tr>
<?php
				while($datos_parti=mssql_fetch_array($cur_parti))
				{

?>
                      <tr class="TxtTabla">

                        <td width="22%" rowspan="2" align="left"><? echo $datos_parti["macroactividad"]." - ".$datos_parti["nombre"]; ?></td>
                            <td width="5%" ><? echo $datos_parti["fecha_inicio"]; ?></td>
                            <td width="5%"><? echo $datos_parti["fecha_fin"]; ?></td>
<?

						//CONSULTA LA FECHA DE INICIO Y FINALIZACION DE LA ACTIVIDAD
						$sql_act1="select fecha_inicio,fecha_fin, year(fecha_inicio) as y_i ,month(fecha_inicio) as m_i, day(fecha_inicio) as d_i  ,year(fecha_fin) as y_f ,month(fecha_fin) as m_f,day(fecha_fin) as d_f  from Actividades where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." ";
						$cur_act1=mssql_query($sql_act1);
						$datos_act1=mssql_fetch_array($cur_act1);
//echo $sql_act1." <br>--- ".mssql_get_last_message()."<br>";					
						$ano_i=$datos_act1["y_i"];
						$mes_i=$datos_act1["m_i"];
						$dia_i=$datos_act1["d_i"];
						$fecha_i=$datos_act1["fecha_inicio"];
					
						$ano_f=$datos_act1["y_f"];
						$mes_f=$datos_act1["m_f"];
						$dia_f=$datos_act1["d_f"];
						$fecha_f=$datos_act1["fecha_fin"];

						//CONSULTA LA INFORMACION DE LA PLANEACIÓN PARA LA PERSONA EN CADA UNA DE LAS ACTIVIDADES PLANEADAS
						$sql_total="
							select id_proyecto, id_actividad, 
							(select SUM(hombresMes) from PlaneacionProyectos  P where id_proyecto=".$datos_parti["id_proyecto"]." and unidad=".$datos_parti["unidad"]." and esInterno='".$cualTipoUsu."' and vigencia=".$datos_parti["vigencia"]." AND id_actividad=".$datos_parti["id_actividad"].") total_H_M ,
							(select SUM(horasMes) from PlaneacionProyectos  P where id_proyecto=".$datos_parti["id_proyecto"]." and unidad=".$datos_parti["unidad"]." and esInterno='".$cualTipoUsu."' and vigencia=".$datos_parti["vigencia"]." AND id_actividad=".$datos_parti["id_actividad"].") total_Horas_M   ,
							vigencia,mes,hombresMes,horasMes from PlaneacionProyectos  P where id_proyecto=".$datos_parti["id_proyecto"]." and unidad=".$datos_parti["unidad"]." and esInterno='".$datos_parti["esInterno"]."' and vigencia=".$datos_parti["vigencia"]." AND id_actividad=".$datos_parti["id_actividad"]. " ORDER BY(mes)" ;
						$cur_total=mssql_query($sql_total);

						$ban=1;  //permite saber que mes del año se esta dibujando(1,2,3....,12)
						while($datos_total=mssql_fetch_array($cur_total))
						{
	
								//SI ES EL PRIMER CICLO DEL WHILE, SE IMPRIME EL VALOR TOTAL DEL HOMBRE/MES Y/O HORAS/MES, PLANEADO EN LA VIGENCIA
								if($ban==1)				
								{
		?>
						<td width="3%" rowspan="2" align="center">

<?php 
									if(trim($hm)=="hombre")
										$total=$datos_total["total_H_M"];	

									if(trim($hm)=="horas")
										$total=$datos_total["total_Horas_M"];
?>
											<?php echo $total; ?>
						</td>
                <td width="5%" class="TxtTabla"></td>
		<?
								}

		
//echo " Activi ".$datos_total["id_actividad"]." --- mes ". $datos_total["mes"]." ********** <br>";
								//AUMENTA EL VALOR DE $ban PARA IGUALAR ESTA A EL  MES MAS RECIENTE, PLANEADO PARA LA PERSONA
								while($ban<$datos_total["mes"])
								{
//$op='';
										$aplica="";
										if(($V<$ano_i))
										{
												$aplica='class="TituloTabla2"';	
//$op=1;	
										}
										else if(($ano_f<$V))
										{
												$aplica='class="TituloTabla2"';		
//$op=2;
										}
										else
										{
									
											if($V==$ano_i)
											{
									
												if($ban<$mes_i)
												{
													$aplica='class="TituloTabla2"';		
//$op=3;
												}
											}
									
											if($V==$ano_f)
											{
									
												if($mes_f<$ban)
												{
													$aplica='class="TituloTabla2"';		
//$op=4;
												}
											}
									
										}

									echo "<td ".$aplica."> </td>";
									$ban++;
								}

								//VERIFICA QUE EL MES DEL REGISTRO SEA IGUAL A LA VARIABLE QUE SE RRECORE PARA VALIDAR EL HOMBRE MES Y ASI MOSTRAR LA IMAGEN CORRESPONDIENTE
								if($ban==$datos_total["mes"])
								{
								
									$total_perso=$datos_total["hombresMes"];
				
									if($total_perso<=1)		//	SI Z ES MENOR O IGUAL A 1
									{
										if($total_perso==1) //si EL VALOR ES 1
										{
											$total_perso-=1;
											$ima=" imagenes/ico1.gif";
										}
		
										else if ( ($total_perso<1) && (0.75<=$total_perso) ) // si el valor esta entre 0.99 y 0.75
										{
		//									$total_perso-=0.75;
											$total_perso=0;
											$ima="imagenes/ico2.gif";
										}
		
										else if ( ($total_perso<0.75) && (0.5<=$total_perso) ) // si el valor esta entre 0.75 y 0.5
										{
		//									$total_perso-=0.5;
											$total_perso=0;
											$ima="imagenes/ico3.gif";
										}
										else if ( ($total_perso<0.5) && (0.25<=$total_perso) ) // si el valor esta entre 0.5 y 0.25
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="imagenes/ico4.gif";
										}
										else if ( ($total_perso<0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="imagenes/ico6.gif";
										}
										else
										{ $ima="imagenes/ico5.gif"; } 
										
		
									}
									else //si el valor es mayor a 1
									{
										$ima="../portal/imagenes/ico1.gif";
										$total_perso-=1;
									}
		?>
						<td width="4%" align="center" background="<?  echo $ima; ?>" class="TxtTabla" ><?php 
									if(trim($hm)=="hombre")
															echo $datos_total["hombresMes"]; 
									if(trim($hm)=="horas")
															echo $datos_total["horasMes"]; 
		?>
						</td>                        
		<?
								}		
								$ban++;
						}
//echo $ban." ** ";
						while($ban<13)
						{
							echo "<td></td>";
							$ban++;
						}						
?>	
           	    </tr>

                <tr>

                </tr>
                <tr>
                    <td colspan="19">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" class="TituloTabla2">
                      <tr>
                        <td></td>
                        </tr>
                      </table></td>
                </tr>

<?
				}
?>
            </table></td>
          </tr>

          <tr>
            <td colspan="4" align="right" class="TxtTabla">&nbsp; <input type="hidden" name="recarga" value="0" id="recarga" /> </td>
          </tr>
        </table>
        </td>
      </tr>
	  </form >
    </table>
	<table>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
      </tr>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
      </tr>
	</table>


</body>
</html>
