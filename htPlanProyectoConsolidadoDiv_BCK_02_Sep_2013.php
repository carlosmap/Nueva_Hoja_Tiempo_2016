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
	
	
			$sql_usuarios_facturacion="select distinct(Usuarios.unidad) ,Usuarios.nombre,Usuarios.apellidos, Categorias.nombre as categoria  from FacturacionProyectos 
				inner join Usuarios on Usuarios.unidad=FacturacionProyectos.unidad
				inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
				inner join Divisiones on Divisiones.id_division=Departamentos.id_division
				inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria ";
	
	
				$sql_usuarios_planeados=$sql_usuarios_planeados. "where  Divisiones.id_division=".$division;
				$sql_usuarios_facturacion=$sql_usuarios_facturacion. "where  Divisiones.id_division=".$division;
				if(trim($empleado)!="")			
				{
					$sql_usuarios_planeados=$sql_usuarios_planeados. " and PlaneacionProyectos.unidad=".$empleado;
					$sql_usuarios_facturacion=$sql_usuarios_facturacion. " and FacturacionProyectos.unidad=".$empleado;
				}
	
				if(trim($departamento)!="")
				{
					$sql_usuarios_planeados=$sql_usuarios_planeados."  and Departamentos.id_departamento=".$departamento;
					$sql_usuarios_facturacion=$sql_usuarios_facturacion."  and Departamentos.id_departamento=".$departamento;
				}
				if(trim($categoria)!="")
				{
					$sql_usuarios_planeados=$sql_usuarios_planeados." and Categorias.id_categoria=".$categoria;
					$sql_usuarios_facturacion=$sql_usuarios_facturacion." and Categorias.id_categoria=".$categoria;
				}
	
				if ((trim($mess)!="") and (trim($ano)!="") and (trim($mes2)!="") and (trim($ano2)!=""))
				{
					
					$sql_usuarios_planeados=$sql_usuarios_planeados." and PlaneacionProyectos.vigencia between ".$ano." and ".$ano2." and PlaneacionProyectos.mes between ".$mess." and ".$mes2." ";
					$sql_usuarios_facturacion=$sql_usuarios_facturacion." and FacturacionProyectos.vigencia between ".$ano." and ".$ano2." and FacturacionProyectos.mes between ".$mess." and ".$mes2." ";
				}

				$sql_total_usuarios="select * from ( (".$sql_usuarios_facturacion.") union (".$sql_usuarios_planeados.") ) total_usuarios_faurados_planeados  ORDER BY apellidos ";

	//				$sql_usuarios_planeados=$sql_usuarios_planeados." ORDER BY Usuarios.apellidos ";
				$cur_usuarios_planeados=mssql_query($sql_total_usuarios);

//echo $sql_total_usuarios."<br><br>".mssql_get_last_message()."<br>";

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function envia0()
{
	var error = 'n';
	var mensaje="";



		if(document.Form1.division.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione una división. \n';
		}
/*		if(document.Form1.departamento.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione un departamento. \n';
		}
*/
		if ((document.Form1.mess.value!="") || (document.Form1.ano.value!="") || (document.Form1.mes2.value!="") || (document.Form1.ano2.value!=""))
		{
			if ((document.Form1.mess.value=="") || (document.Form1.ano.value==""))
			{
				error = 's';
				mensaje = mensaje + 'Seleccione el mes y el año en la seccion desde. \n';	
			}
			 if( (document.Form1.mes2.value=="") || (document.Form1.ano2.value==""))
			{
				error = 's';
				mensaje = mensaje + 'Seleccione el mes y el año en la seccion hasta. \n';	
			}
		}		

		if(error=="s")
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
	

        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>

		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
	        <td width="15%" height="20" class="FichaAct">Divisi&Oacute;n VS Proyectos    	    <td width="15%" height="20" class="FichaInAct" > <a href="htPlanProyectoConsolidadoProy.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Usuarios por Proyecto</a></td>
    	    <td width="15%" height="20" class="FichaInAct" ><a href="htPlanProyectoConsolidadoUsu.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Usuarios por Divisi&oacute;n</a></td>

    	    <td width="15%"    class="FichaInAct"><a href="htPlanProyectoFacturacionProy.php?division=<?=$division; ?>&departamento=<?=$departamento ?>&categoria=<?=$categoria ?>&empleado=<?=$empleado ?>" class="FichaInAct1" >Facturaci&oacute;n por Proyecto</a></td>

			 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
      <tr>
        <td colspan="5" class="TituloUsuario"></td>
        </tr>
    </table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">

		
		</table>
			<form name="Form1" id="Form1" method="post" >

          <tr>
            <td >
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
        

          <tr class="TxtTabla" >
            <td   align="center"></td>
          </tr>
          <tr class="TxtTabla" >

            <td width="40%" rowspan="2"   align="center" >
<table width="100%" border="0">
                   <tr class="TxtTabla" >
            <td   align="center">&nbsp;</td>
          </tr>
  <tr>
    <td align="center"  >
              <table width="40%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF" >
				
 <tr>
			 <td  colspan="2">
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tbody>
                <tr>
                <td class="TituloUsuario" height="2">Criterios de consulta </td>
                </tr>			
                </tbody>
                </table>
			 </td>
		</tr>
				<tr>
					<td width="15%" align="left" class="TituloTabla">Divisi&oacute;n</td>
					<td align="left" class="TxtTabla">
			<select name="division" id="division" class="CajaTexto" onchange="document.Form1.submit();">
				<option value="">::Seleccione División:: </option>
<?php
				$sql_divisiones="select * from Divisiones where  estadoDiv='A'  order by nombre";
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
<td width="15"  align="left" class="TituloTabla">Departamento</td>
            <td  align="left" class="TxtTabla"><select name="departamento" class="CajaTexto" id="departamento"  onchange="document.Form1.submit();">
              <option value="">::Seleccione Departamento::</option>
<?php
				$sql_departamento="select * from Departamentos where  estadoDpto='A' and id_division=".$division." order by nombre";
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
            <td width="15"  align="left" class="TituloTabla">Categoria</td>
            <td  align="left"><select name="categoria" class="CajaTexto" id="categoria" onchange="document.Form1.submit();" >
              <option value="">::Seleccione Categoria::</option>
              <?
						$sql_div="select * from Categorias order by nombre";
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
<?
$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
          <tr class="TxtTabla" >
            <td colspan="2" align="right" class="TxtTabla"><table width="100%" border="0">
              <tr>
                <td width="15%" align="left" class="TituloTabla">Desde</td>
                <td align="left"><select name="mess" class="CajaTexto" id="mess" >
                  <option value="">::Mes::</option>
               <?

                                    $m = 1;
                                    while( $m < count( $mes ) ){
                                        $sel="";
                                        if($mess==$m)
                                            $sel="selected";
                                ?>
                                    <option value="<?= $m ?>" <?php echo $sel; ?> <? echo  $sel; ?> ><?= $mes[$m] ?></option>
                                <?	$m++;
                                  }	
                                ?>
                </select> <select name="ano" class="CajaTexto" id="ano" >
                  <option value="">::Año::</option>
                  <?
						$sql_ano="SELECT MIN(vigencia) fechaMin, MAX(vigencia) fechaMax from PlaneacionProyectos ";
						$cur_ano=mssql_query($sql_ano);
						while($datos_ano=mssql_fetch_array($cur_ano))
						{
							//almacena el id de la division, este valor se utiliza para cargar la division seleccionada en (Asociacion de participanes)

							for($z=( (int) $datos_ano["fechaMin"]);$z<=( (int) $datos_ano["fechaMax"]);$z++)
							{

								$sel="";
								if($z==$ano)
									$sel="selected";
?>
		    	              <option value="<? echo $z; ?>"  <? echo $sel; ?> ><? echo $z; ?></option>
                  <?
							}
						}
?>
                </select></td>
                <td width="15%" align="left" class="TituloTabla"> Hasta</td>
                <td align="left"><select name="mes2" class="CajaTexto" id="mes2" >
                  <option value="">::Mes::</option>
               <?

                                    $m = 1;
                                    while( $m < count( $mes ) ){
                                        $sel="";
                                        if($mes2==$m)
                                            $sel="selected";
                                ?>
                                    <option value="<?= $m ?>" <?php echo $sel; ?> <? echo  $sel; ?> ><?= $mes[$m] ?></option>
                                <?	$m++;
                                  }	
                                ?>
                </select>
                  <select name="ano2" class="CajaTexto" id="ano2"  >
                    <option value="">::A&ntilde;o::</option>
                  <?
						$sql_ano="SELECT MIN(vigencia) fechaMin, MAX(vigencia) fechaMax from PlaneacionProyectos ";
						$cur_ano=mssql_query($sql_ano);
						while($datos_ano=mssql_fetch_array($cur_ano))
						{
							//almacena el id de la division, este valor se utiliza para cargar la division seleccionada en (Asociacion de participanes)

							for($z=( (int) $datos_ano["fechaMin"]);$z<=( (int) $datos_ano["fechaMax"]);$z++)
							{

								$sel="";
								if($z==$ano2)
									$sel="selected";
?>
		    	              <option value="<? echo $z; ?>"  <? echo $sel; ?> ><? echo $z; ?></option>
                  <?
							}
						}
?>
                  </select></td>
              </tr>
            </table></td>
            </tr>
          <tr class="TxtTabla" >
            <td width="15%" align="left" class="TituloTabla">Empleado</td>
            <td align="left"><label for="vigencia2"></label>
              <select name="empleado" class="CajaTexto" id="empleado" onchange="document.Form1.submit();">
	              <option value="">::Seleccione Empleado::</option>
                <? 

					while($datos_emple=mssql_fetch_array($cursor3))
					{
						$sel="";
						if($datos_emple["unidad"]==$empleado)
							$sel="selected";
				?>
                	  <option value="<? echo $datos_emple["unidad"]; ?>" <? echo $sel; ?> > <? echo $datos_emple["nom_usu"]." ".$datos_emple["apellido"]." [".$datos_emple["unidad"]."] "; ?></option>
                <? 	} ?>
                </select></td>
          </tr>

          <tr class="TxtTabla" >
            <td colspan="2" align="right" class="TxtTabla"><input type="hidden" name="recarga" value="0" id="recarga" />     
<input type="hidden" name="ba" value="<?=$ba; ?>" id="ba" />     
		<?php
			if ( (trim($division)!="") and ($recarga==1))
			{
		?>
         <input onClick="MM_openBrWindow('consolidado_div_xls.php?division=<?=$division;?>&departamento=<?=$departamento;?>&categoria=<?=$categoria;?>&mess=<?=$mess;?>&ano=<?=$ano;?>&mes2=<?=$mes2; ?>&ano2=<?=$ano2; ?>&empleado=<?=$empleado; ?>','wRPT1','scrollbars=yes,resizable=yes,width=500,height=400')" type="button" class="Boton" value="Descargar en XLS" />
		<?
			}
		?>

         <input name="Consultar" onclick="envia0();" type="submit" class="Boton" id="Consultar" value="Consultar" /></td>
            </tr>
		  <tr>
			 <td  colspan="2">
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tbody>
                <tr>
                <td class="TituloUsuario" height="2"> </td>
                </tr>			
                </tbody>
                </table>
			 </td>
		</tr>

            </table>



</td>
  </tr>
</table>

			</td>
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
          <tr>
            <td colspan="4" align="right" class="TxtTabla">


           </td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla"><table width="100%" border="0">
              <tr>
                <td width="5%" class="TituloTabla2">Unidad</td>
                <td class="TituloTabla2">Usuario</td>
                <td width="5%" class="TituloTabla2">Cat</td>
                <td width="2%" class="TituloTabla2">&nbsp;</td>
<?
		if($recarga==1)	
		{
				$id_proys=array (); //almacena los id_poryectos, que han sido planeados  
				$z=0;
				$sql_proy="select distinct(PlaneacionProyectos.id_proyecto), Proyectos.nombre from PlaneacionProyectos 

										inner join Proyectos on Proyectos.id_proyecto=PlaneacionProyectos.id_proyecto 
										inner join Usuarios on Usuarios.unidad=PlaneacionProyectos.unidad
										inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
										inner join Divisiones on Divisiones.id_division=Departamentos.id_division
										
										where  Divisiones.id_division=".$division;

				if(trim($departamento)!="")
					$sql_proy=$sql_proy."  and Departamentos.id_departamento=".$departamento;

				$sql_proy=$sql_proy." order by(PlaneacionProyectos.id_proyecto)";

				$cur_proy=mssql_query($sql_proy);
				while($datos_proy=mssql_fetch_array($cur_proy))
				{
?>
	                <td class="TituloTabla2"><? echo " [".$datos_proy["id_proyecto"]."]".$datos_proy["nombre"]; ?></td>
<?
					$id_proys[$z]= $datos_proy["id_proyecto"]; 
					$z++;
					
				}
		}
?>
                <td class="TituloTabla2">Total</td>
              </tr>
<?
			while($datos_usuarios_planeados=mssql_fetch_array($cur_usuarios_planeados))
			{
				$total_planeado=0; //ALMACENA EL VALOR TOTAL PLANEADO, EN TODOS LOS PROYECTOS, PARA CADA USUARIO
?>
              <tr>
                <td rowspan="3" align="left" class="TxtTabla"><? echo $datos_usuarios_planeados["unidad"]; ?></td>
                <td rowspan="3" align="left" class="TxtTabla"><? echo strtoupper( $datos_usuarios_planeados["apellidos"]." ".$datos_usuarios_planeados["nombre"]); ?></td>
                <td width="5%" rowspan="3" align="left" class="TxtTabla"><? echo $datos_usuarios_planeados["categoria"]; ?></td>

                <td width="2%" class="TituloTabla2">P</td>
<?

				foreach($id_proys as $m => $a)
				{
					$sql_tota_planea="select SUM(hombresMes) as total_hm from PlaneacionProyectos where  PlaneacionProyectos.unidad=".$datos_usuarios_planeados["unidad"]." ";
					$sql_tota_planea=$sql_tota_planea." and id_proyecto=".$a;

					if ((trim($mess)!="") and (trim($ano)!="") and (trim($mes2)!="") and (trim($ano2)!=""))
						$sql_tota_planea=$sql_tota_planea." and PlaneacionProyectos.vigencia between ".$ano." and ".$ano2." and PlaneacionProyectos.mes between ".$mess." and ".$mes2;

					$cur_tota_planea=mssql_query($sql_tota_planea);
//echo $sql_tota_planea."<br><br>".mssql_get_last_message()."<br>";
					if($datos_tota_planea=mssql_fetch_array($cur_tota_planea))
					{				
						$total_planeado+=( (float) $datos_tota_planea["total_hm"]);
?>               	

						<td align="left" class="TxtTabla">
							<? echo $datos_tota_planea["total_hm"]; ?>
						</td>
<?
					}
					else
					{
						echo "<td>	</td>";

					}
				}
?>
						<td align="left" class="TxtTabla">	
<?
								echo $total_planeado;
?>
						</td>

              </tr>
              <tr >
                        <td colspan="<?=2+mssql_num_rows($cur_proy) ?>" align="left" class="TituloTabla"> </td>
               </tr>
              <tr>
                <td width="2%" class="TituloTabla2">F</td>
<?

				foreach($id_proys as $m => $a)
				{
					$sql_tota_facturacion="select  SUM(hombresMesF) as total_hm from FacturacionProyectos where id_proyecto =".$a." and FacturacionProyectos.unidad=".$datos_usuarios_planeados["unidad"]." ";

					if ((trim($mess)!="") and (trim($ano)!="") and (trim($mes2)!="") and (trim($ano2)!=""))
						$sql_tota_facturacion=$sql_tota_facturacion." and FacturacionProyectos.vigencia between ".$ano." and ".$ano2." and FacturacionProyectos.mes between ".$mess." and ".$mes2;

					$cur_tota_planea=mssql_query($sql_tota_facturacion);
//echo $sql_tota_planea."<br><br>".mssql_get_last_message()."<br>";
					if($datos_tota_planea=mssql_fetch_array($cur_tota_planea))
					{				
						$total_facturado+=( (float) $datos_tota_planea["total_hm"]);
?>               	
						<td align="left" class="TxtTabla">
							<? echo $datos_tota_planea["total_hm"]; ?>
						</td>
<?
					}
					else
					{
						echo "<td>	</td>";

					}
				}
?>

                <td align="left"  class="TxtTabla">
<?
								echo $total_facturado;
?>
				</td>
              </tr>
              <tr class="TituloUsuario">
                        <td colspan="<?=5+mssql_num_rows($cur_proy) ?>" align="left" class="TituloUsuario"> </td>
              </tr>
<?
				$total_facturado=0;
			}
			if( ( (int) (mssql_num_rows($cur_usuarios_planeados) )==0) and(trim(mssql_num_rows($cur_usuarios_planeados))!=""))
			{
?>
  	            <tr >
                        <td colspan="17" align="left" class="TxtTabla"></td>
              </tr>
  	            <tr class="TituloTabla2">
                        <td colspan="17" align="left" class="TituloTabla2">No se encontraron registros. </td>
              </tr>
<?
			}
?>            </table></td>
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
mysql_close();
?>


</body>
</html>
