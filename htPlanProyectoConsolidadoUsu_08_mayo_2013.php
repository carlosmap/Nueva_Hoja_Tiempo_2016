<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//echo "Proy=".  $cualProyecto . "<br>";
//echo "Act=" . $cualActividad . "<br>";
//exit;

	if(trim($division)!="")
	{
		//	CONSULTA LOS USUARIOS PERTENECIENTES A LA DIVISION Y DEPARTAMENTO SELECCIONADOS
		$sql3 = " SELECT *, UPPER(Usuarios.nombre) as nom_usu ,UPPER(Usuarios.apellidos) as apellido FROM HojaDeTiempo.dbo.Usuarios";
		
		$sql3 = $sql3 . " inner join Departamentos on Usuarios.id_departamento =Departamentos.id_departamento " ;
		$sql3 = $sql3 . " inner join Divisiones on Departamentos.id_division =Divisiones.id_division ";
		
		$sql3=$sql3." WHERE retirado IS NULL ";		
		$sql3 = $sql3 . " AND Divisiones.id_division = " . $division;

		
		if(trim($departamento) != ""){
			$sql3 = $sql3 . " AND Departamentos.id_departamento = " . $departamento;
		}

		$sql3 = $sql3 . "ORDER BY apellidos ";
		$cursor3 = mssql_query($sql3);
	}


	if($recarga==1)
	{
		$sql_usuarios_planeados="select distinct(Usuarios.unidad) ,Usuarios.nombre,Usuarios.apellidos, Categorias.nombre as categoria  from PlaneacionProyectos 
			inner join Usuarios on Usuarios.unidad=PlaneacionProyectos.unidad
			inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
			inner join Divisiones on Divisiones.id_division=Departamentos.id_division
			inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria ";

			$sql_usuarios_planeados=$sql_usuarios_planeados. "where  Divisiones.id_division=".$division;
			if(trim($empleado)!="")			
				$sql_usuarios_planeados=$sql_usuarios_planeados. " and PlaneacionProyectos.unidad=".$empleado;

			if(trim($departamento)!="")
				$sql_usuarios_planeados=$sql_usuarios_planeados."  and Departamentos.id_departamento=".$departamento;

			if(trim($categoria)!="")
				$sql_usuarios_planeados=$sql_usuarios_planeados." and Categorias.id_categoria=".$categoria;

			if(trim($lstVigencia)!="")
				$sql_usuarios_planeados=$sql_usuarios_planeados." and PlaneacionProyectos.vigencia=".$lstVigencia;


				$sql_usuarios_planeados=$sql_usuarios_planeados." ORDER BY Usuarios.apellidos ";
			$cur_usuarios_planeados=mssql_query($sql_usuarios_planeados);
//echo $sql_usuarios_planeados."<br><br>".mssql_get_last_message()."<br>";

	}

//Definir la fecha inicio mínima y final máxima de todas las actividades que hacen parte del proyecto
$minVigenciaP="";
$maxVigenciaP="";
$sql03="SELECT YEAR(MIN(fecha_inicio)) fechaMin, YEAR(MAX(fecha_fin)) fechaMax ";
$sql03=$sql03." FROM Actividades ";
$cursor03 = mssql_query($sql03);
if ($reg03=mssql_fetch_array($cursor03)) {
	$minVigenciaP = $reg03[fechaMin] ;
	$maxVigenciaP = $reg03[fechaMax] ;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">



function envia0()
{
	var error = 'n';
	var mensaje="";



		if(document.Form1.division.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione una división. \n';
		}
		if(document.Form1.lstVigencia.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione una vigencia. \n';
		}
	

		if(error=='s')
		{
			alert(mensaje);
		}
		else
		{	
			document.Form1.recarga.value = 1;
			document.Form1.submit();
		}
	
}

</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>


</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;"> CONSOLIDADO POR DIVISION
</div>
	
			<form name="Form1" id="Form1" method="post" >
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>

		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
	        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectoConsolidadoDiv.php" class="FichaInAct1" >Divisi&oacute;n VS Proyectos</a>
 <td width="15%" height="20" class="FichaInAct" > <a href="htPlanProyectoConsolidadoProy.php" class="FichaInAct1" >Usuarios por Proyecto</a></td>
    	    <td width="15%" height="20" class="FichaAct" >Usuarios por Divisi&oacute;n</td>

			 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
      <tr>
        <td height="2" colspan="5" class="TituloUsuario"></td>
        </tr>
    </table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
         
          <tr>
            <td class="TxtTabla" height="2" colspan="3"></td>
          </tr>
		
		</table>


          <tr>
            <td >
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td  align="center">&nbsp;</td>
            <td width="30%"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td width="40%" rowspan="2"   align="center">
              <table width="100%" border="0" cellpadding="0" cellspacing="1">
				<tr>
					<td width="20%" align="center" class="TituloTabla">Divisi&oacute;n</td>
					<td align="left">
			<select name="division" id="division" class="CajaTexto" onchange="document.Form1.submit();">
				<option value="">::Seleccione División:: </option>
<?php
				$sql_divisiones="select * from Divisiones where  estadoDiv='A' ";
				$cursor_div=mssql_query($sql_divisiones);
				
				while($datos_div=mssql_fetch_array($cursor_div))
				{
							$select2="";
							if($division== $datos_div["id_division"])
							{
								$select2="selected";
							}

?>
					<option value="<?php  echo  $datos_div["id_division"]; ?>" <?php echo $select2; ?>><?php  echo  strtoupper($datos_div["nombre"]); ?> </option>

<?php
				}
?>

	        </select>
				  </td>

				</tr>
                <tr>
<td width="20%"  align="center" class="TituloTabla2">Departamento</td>
            <td width="10%"  align="left"><select name="departamento" class="CajaTexto" id="departamento"  onchange="document.Form1.submit();">
              <option value="">::Seleccione Departamento::</option>
<?php
				$sql_departamento="select * from Departamentos where  estadoDpto='A' and id_division=".$division;
				$cursor_dep=mssql_query($sql_departamento);
				
				while($datos_dep=mssql_fetch_array($cursor_dep))
				{
							$select2="";
							if($departamento== $datos_dep["id_departamento"])
							{
								$select2="selected";
							}

?>
					<option value="<?php  echo  $datos_dep["id_departamento"]; ?>" <?php echo $select2; ?>><?php  echo  strtoupper($datos_dep["nombre"]); ?> </option>

<?php
				}
?>
            </select></td>
            </tr>
                <tr class="TxtTabla" >
                  <td width="20%"  align="center" class="TituloTabla2">Categoria</td>
                  <td width="10%"  align="left"><select name="categoria" class="CajaTexto" id="categoria" >
                    <option value="">::Seleccione Categoria::</option>
                    <?
						$sql_div="select * from Categorias";
						$cur_div=mssql_query($sql_div);
						while($datos_div=mssql_fetch_array($cur_div))
						{
							$sel="";

							if($datos_div["id_categoria"]==$categoria)
								$sel="selected";

?>
                    <option value="<? echo $datos_div["id_categoria"]; ?>"  <? echo $sel; ?> ><? echo strtoupper($datos_div["nombre"]); ?> </option>
                    <?
						}
?>
                  </select></td>
                </tr>
                <tr class="TxtTabla" >
                  <td align="right" class="TituloTabla2">Empleado</td>
                  <td align="left"><label for="vigencia2"></label>
                    <select name="empleado" class="CajaTexto" id="empleado" onchange="document.form1.submit();">
                      <option value="">::Seleccione Empleado::</option>
                      <? 

					while($datos_emple=mssql_fetch_array($cursor3))
					{
						$sel="";
						if($datos_emple["unidad"]==$empleado)
							$sel="selected";
				?>
                      <option value="<? echo $datos_emple["unidad"]; ?>" <? echo $sel; ?> > <? echo "[".$datos_emple["unidad"]."] ".$datos_emple["apellido"]." ".$datos_emple["nom_usu"]; ?></option>
                      <? 	} ?>
                    </select></td>
                </tr>
                <tr class="TxtTabla" >
                  <td align="right" class="TituloTabla2">Vigencia</td>
                  <td align="left">
                           <select name="lstVigencia" class="CajaTexto" id="lstVigencia" >
		                      <option value="">::Seleccione Vigencia::</option>
                                        <? 
                                for ($k=$minVigenciaP; $k<=$maxVigenciaP; $k++) { 
                                    if ($lstVigencia == $k) {
                                        $selVig = "selected";
                                    }
                                    else {
                                        $selVig = "";
                                    }
                                ?>
                                        <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
                                        <? } ?>
                                      </select>
				</td>
                </tr>

          <tr class="TxtTabla" >
            <td colspan="2" align="right" class="TxtTabla"><input type="hidden" name="recarga" value="0" id="recarga" />     
  <input type="hidden" name="ba" value="<?=$ba; ?>" id="ba" />     
              <input name="Consultar" onclick="envia0();" type="button" class="Boton" id="Consultar" value="Consultar" /></td>
          </tr>
            </table></td>

            <td width="30%" rowspan="2"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
          </tr>

  	      </table>
	  </form >
			</td>

          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
            <td width="2%" class="TituloTabla2">P</td>
            <td width="5%" class="TxtTabla">Planeado</td>
            <td width="2%" class="TituloTabla2">F</td>
            <td width="5%" class="TxtTabla">Facturado</td>
          </tr>
        </table>	


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="4" align="left" class="TituloUsuario">&nbsp;</td>
          </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr class="TxtTabla">
    <td>&nbsp;    </td>
    <td align="right"><img src="../portal/imagenes/ico100.gif" width="77" height="16">Programado</td>
    <td align="right">&nbsp;</td>
    <td align="right"><img src="../portal/imagenes/ico1.gif" width="77" height="16">Excede Hombre/Mes la programaci&oacute;n</td>
    <td align="right">&nbsp;</td>

    <td align="right"><img src="../portal/imagenes/eje100.gif" width="77" height="16">Ejecutado</td>
    <td align="right">&nbsp;</td>
    <td align="right"><img src="../portal/imagenes/eje1.gif" width="77" height="16">Excede Hombre/Mes la ejecuci&oacute;n </td>

  </tr>
</table>
	
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">

          <tr>
            <td colspan="4" align="left" class="TituloUsuario"></td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla">


           </td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla"><table width="100%" border="0">
              <tr>
                <td width="5%" class="TituloTabla2" rowspan="2">Unidad</td>
                <td class="TituloTabla2" rowspan="2">Usuario</td>
                <td width="5%" class="TituloTabla2" rowspan="2">Cat</td>
                <td width="2%" class="TituloTabla2" rowspan="2">&nbsp;</td>

                <td class="TituloTabla2" colspan="12"><? echo $lstVigencia; ?></td>

                <td rowspan="2" class="TituloTabla2">Total</td>
              </tr>
              <tr class="TituloTabla2">
                <td width="4%">Enero</td>
                <td width="4%"  >Febrero</td>
                <td width="4%"  >Marzo</td>
                <td width="4%" >Abril</td>
                <td width="4%"  >Mayo</td>
                <td width="4%"  >Junio</td>
                <td width="4%"  >Julio</td>
                <td width="4%"  >Agosto</td>
                <td width="4%"  >Septiembre</td>
                <td width="4%"  >Octubre</td>
                <td  >Noviembre</td>
                <td  >Diciembre</td>
                </tr>
<?
			while($datos_usuarios_planeados=mssql_fetch_array($cur_usuarios_planeados))
			{
				$total_planeado=0; //ALMACENA EL VALOR TOTAL PLANEADO, EN TODOS LOS PROYECTOS, PARA CADA USUARIO
?>
              <tr>
                <td rowspan="3" class="TxtTabla"><? echo $datos_usuarios_planeados["unidad"]; ?></td>
                <td rowspan="3" class="TxtTabla"><? echo $datos_usuarios_planeados["apellidos"]." ".$datos_usuarios_planeados["nombre"]; ?></td>
                <td width="5%" rowspan="3" align="center" class="TxtTabla"><? echo $datos_usuarios_planeados["categoria"]; ?></td>

                <td width="2%" class="TituloTabla2">P</td>
<?

						//CONSULTA LA INFORMACION DE LA PLANEACIÓN PARA LA PERSONA
						$sql_total="select (select  SUM(hombresMes) as total_H_M from PlaneacionProyectos where vigencia=".$lstVigencia." and  unidad=".$datos_usuarios_planeados["unidad"].") as total_H_M ,* from PlaneacionProyectos where  vigencia=".$lstVigencia." and  unidad=".$datos_usuarios_planeados["unidad"]." ORDER BY(mes)";
						$cur_total=mssql_query($sql_total);

//echo $sql_total." <br><br>".mssql_get_last_message();

					$ban=1;  //permite saber que mes del año se esta dibujando(1,2,3....,12)
					$total=0;
//----------------------------------------------------------------
						while($datos_total=mssql_fetch_array($cur_total))
						{
								
								$total=$datos_total["total_H_M"];			

								//AUMENTA EL VALOR DE $ban PARA IGUALAR ESTA A EL  MES MAS RECIENTE, PLANEADO PARA LA PERSONA
								while($ban<$datos_total["mes"])
								{
										$aplica="";
										if(($V<$ano_i))
										{
												$aplica='class="TxtTabla"';		
										}
										else if(($ano_f<$V))
										{
												$aplica='class="TxtTabla"';		
										}
										else
										{
									
											if($V==$ano_i)
											{
									
												if($ban<$mes_i)
												{
													$aplica='class="TxtTabla"';		
												}
											}
									
											if($V==$ano_f)
											{
									
												if($mes_f<$ban)
												{
													$aplica='class="TxtTabla"';		
												}
											}
									
										}

									echo "<td ".$aplica."></td>";
									$ban++;
								}

								//VERIFICA QUE EL MES DEL REGISTRO SEA IGUAL A LA VARIABLE QUE SE RRECORE PARA VALIDAR EL HOMBRE MES Y ASI MOSTRAR LA IMAGEN CORRESPONDIENTE
								if($ban==$datos_total["mes"])
								{
								
									$total_perso=$datos_total["hombresMes"];
				
									if($total_perso<=1)		//	SI Z ES MENOR O IGUAL A 1
									{
		
										if ( ($total_perso<=1) && (0.75<$total_perso) ) // si el valor esta entre 0.99 y 0.75
										{
		//									$total_perso-=0.75;
											$total_perso=0;
											$ima="../portal/imagenes/ico100.gif";
										}
		
										else if ( ($total_perso<=0.75) && (0.5<$total_perso) ) // si el valor esta entre 0.75 y 0.5
										{
		//									$total_perso-=0.5;
											$total_perso=0;
											$ima="../portal/imagenes/ico75.gif";
										}
										else if ( ($total_perso<=0.5) && (0.25<$total_perso) ) // si el valor esta entre 0.5 y 0.25
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="../portal/imagenes/ico50.gif";
										}
										else if ( ($total_perso<=0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="../portal/imagenes/ico25.gif";
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
				<td width="4%" align="center" background="<?  echo $ima; ?>" class="TxtTabla" ><?php echo $datos_total["hombresMes"]; ?></td>                        
		<?
								}		
								$ban++;
						}
//echo $ban." ** ";
//----------------------------------------------------------------
						while($ban<13)
						{
							echo "<td></td>";
							$ban++;
						}	
			?>
					<td class="TxtTabla"><? echo $total; ?></td>
              </tr>
              <tr >
                        <td colspan="14" align="left" class="TituloTabla"> </td>
               </tr>
              <tr>
                <td width="2%" class="TituloTabla2">F</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr class="TituloUsuario">
                        <td colspan="17" align="left" class="TituloUsuario"> </td>
              </tr>
<?
			}

			if( ( (int) (mssql_num_rows($cur_usuarios_planeados) )==0) and(trim(mssql_num_rows($cur_usuarios_planeados))!=""))
			{
?>
  	            <tr >
                        <td colspan="17" align="left" class="TxtTabla"> </td>
              </tr>
  	            <tr class="TituloTabla2">
                        <td colspan="17" align="left" class="TituloTabla2">No se encontraron registros. </td>
              </tr>
<?
			}
?>
            </table></td>
          </tr>
</table>

	<table width="100%" cellpadding="0" cellspacing="0" >
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
	</table>
<?
//Finaliza las conexiones a MySql y a SQL Server
mssql_close();
?>


</body>
</html>
