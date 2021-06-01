<?php
session_start();

$fecha = date('Y-m-d');

echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=Consolidado_planeación_facturación_usuarios_por_proyecto_" . $fecha . ".xls");
echo "</head>";

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

		$sql_proy="select nombre from HojaDeTiempo.dbo.Proyectos where id_proyecto=".$proyecto;
		$cur_pro=mssql_query($sql_proy);
		if($datos_pr=mssql_fetch_array($cur_pro))
		{
			$nom_proy=$datos_pr["nombre"];
		}

		//CONSULTA DE LOS USUARIOS PLANEADOS
		$sql_usuarios_planeados="select distinct(Usuarios.unidad) ,Usuarios.nombre,Usuarios.apellidos, Categorias.nombre as categoria  from PlaneacionProyectos 
			inner join Usuarios on Usuarios.unidad=PlaneacionProyectos.unidad
			inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
			inner join Divisiones on Divisiones.id_division=Departamentos.id_division
			inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria ";

		//CONSULTA DE LOS USUARIOS QUE HAN GENERADO FACTURACION
		$sql_usuarios_facturados="select distinct(Usuarios.unidad) ,Usuarios.nombre,Usuarios.apellidos, Categorias.nombre as categoria  from FacturacionProyectos 
			inner join Usuarios on Usuarios.unidad=FacturacionProyectos.unidad
			inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
			inner join Divisiones on Divisiones.id_division=Departamentos.id_division
			inner join Categorias on Categorias.id_categoria=Usuarios.id_categoria ";

			$sql_usuarios_planeados=$sql_usuarios_planeados. "where  Divisiones.id_division=".$division;
			$sql_usuarios_facturados=$sql_usuarios_facturados. "where  Divisiones.id_division=".$division;
			if(trim($empleado)!="")			
			{
				$sql_usuarios_planeados=$sql_usuarios_planeados. " and PlaneacionProyectos.unidad=".$empleado;
				$sql_usuarios_facturados=$sql_usuarios_facturados. " and FacturacionProyectos.unidad=".$empleado;
			}
			if(trim($departamento)!="")
			{
				$sql_usuarios_planeados=$sql_usuarios_planeados."  and Departamentos.id_departamento=".$departamento;
				$sql_usuarios_facturados=$sql_usuarios_facturados."  and Departamentos.id_departamento=".$departamento;
			}
			if(trim($categoria)!="")
			{
				$sql_usuarios_planeados=$sql_usuarios_planeados." and Categorias.id_categoria=".$categoria;
				$sql_usuarios_facturados=$sql_usuarios_facturados." and Categorias.id_categoria=".$categoria;
			}
			if(trim($lstVigencia)!="")
			{
				$sql_usuarios_planeados=$sql_usuarios_planeados." and PlaneacionProyectos.vigencia=".$lstVigencia;
				$sql_usuarios_facturados=$sql_usuarios_facturados." and FacturacionProyectos.vigencia=".$lstVigencia;
			}

			if(trim($proyecto)!="")
			{
				$sql_usuarios_planeados=$sql_usuarios_planeados." and PlaneacionProyectos.id_proyecto=".$proyecto;
				$sql_usuarios_facturados=$sql_usuarios_facturados." and FacturacionProyectos.id_proyecto=".$proyecto;
			}

			$sql_total_usuarios="select * from ( (".$sql_usuarios_facturados.") union (".$sql_usuarios_planeados.") ) total_usuarios_faurados_planeados  ORDER BY apellidos ";

//				$sql_usuarios_planeados=$sql_usuarios_planeados." ORDER BY Usuarios.apellidos ";
			$cur_usuarios_planeados=mssql_query($sql_total_usuarios);

?>


<html>
<head>
<title>:::  :::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK REL="stylesheet" HREF="http://www.ingetec.com.co/enlinea/mitu/css/estilo.css" TYPE="text/css">
<style type="text/css">
<!--
.Estilo3 {
	font-family: "Swis721 BlkEx BT", "Arial Narrow";
	font-size: 11px;
}
.Estilo4 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
	font-weight: bold;
}
.Estilo5 {
	font-family: "Arial Narrow";
	font-weight: bold;
	font-size: 11px;
}
.Estilo6 {font-size: 8px}
-->
</style>
</head>


<body  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="fondo" >
<table width="100%"  border="1" cellspacing="1">
<tr bordercolor="#000000">
  <td align="center" valign="middle" class="LetraIngetecForm Estilo3" width="145" height="49"><div align="center"><img src="http://www.ingetec.com.co/NuevaHojaTiempo/imagenes/logoIngetec.gif" width="140" height="49" align="absmiddle"></div></td>
  <td colspan="16" align="center" valign="middle" class="TituloFormato Estilo4"><b><h3>Consolidado total planeaci&oacute;n 

y facturaci&oacute;n 
<?php
	if(trim($division)!="")
	{
		$sql_dep="select nombre from HojaDeTiempo.dbo.Divisiones where id_division=".$division;
		$cur_dep=mssql_query($sql_dep);
		if($datos_dep=mssql_fetch_array($cur_dep))
		{
			$nom_div=$datos_dep["nombre"];
			echo " en la divisi&oacute;n ".$nom_div." ";
		}
	}
	if(trim($departamento)!="")
	{
		$sql_dep="select nombre from HojaDeTiempo.dbo.Departamentos where id_departamento=".$departamento." and id_division=".$division;
		$cur_dep=mssql_query($sql_dep);
		if($datos_dep=mssql_fetch_array($cur_dep))
		{
			$nom_dep=$datos_dep["nombre"];
			echo " y el departamento ".$nom_dep." ";
		}
	}
?>


para el proyecto <?=$nom_proy; ?> durante el a&ntilde;o <?=$lstVigencia; ?> </h3></b>
</td>
<?php


 ?>
</tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="4" align="right">
           </td>
          </tr>
  <tr>
    <td bgcolor="#ffb546">&nbsp;    </td>
    <td align="left">Planeado</td>
    <td align="right"  bgcolor="#ff6633" >&nbsp;</td>
    <td align="left">Excede Hombre/Mes la planeaci&oacute;n</td>
    <td align="right" bgcolor="#38a9a9" >&nbsp;</td>
    <td align="left">Facturado</td>
    <td align="right" bgcolor="#50d3d3" >&nbsp;</td>
    <td align="left">Excede Hombre/Mes la facturaci&oacute;n</td>

  </tr>
          <tr>
            <td colspan="4" align="right">
           </td>
          </tr>
</table>
	
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">


          <tr>
            <td colspan="4" align="right" ><table width="100%" border="1">
              <tr>
                <td width="5%" rowspan="2" class="TituloTabla2">Unidad</td>
                <td rowspan="2" class="TituloTabla2">Usuario</td>
                <td width="5%" rowspan="2" class="TituloTabla2">Categoria</td>
                <td width="2%" rowspan="2" class="TituloTabla2">&nbsp;</td>

                <td colspan="12" class="TituloTabla2"><? echo $lstVigencia; ?></td>

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
                <td width="4%" >Noviembre</td>
                <td width="4%" >Diciembre</td>
</tr>
<?
			while($datos_usuarios_planeados=mssql_fetch_array($cur_usuarios_planeados))
			{
				$total_planeado=0; //ALMACENA EL VALOR TOTAL PLANEADO, EN TODOS LOS PROYECTOS, PARA CADA USUARIO
?>
              <tr>
                <td rowspan="2" align="left"><? echo $datos_usuarios_planeados["unidad"]; ?></td>
                <td rowspan="2" align="left"><? echo $datos_usuarios_planeados["apellidos"]." ".$datos_usuarios_planeados["nombre"]; ?></td>
                <td width="5%" rowspan="2" align="left"><? echo $datos_usuarios_planeados["categoria"]; ?></td>

                <td width="2%" class="TituloTabla2">P</td>
<?

						//CONSULTA LA INFORMACION DE LA PLANEACIÓN PARA LA PERSONA
						$sql_total="select (select  SUM(hombresMes) as total_H_M from PlaneacionProyectos where id_proyecto=".$proyecto." and vigencia=".$lstVigencia." and  unidad=".$datos_usuarios_planeados["unidad"].") as total_H_M , SUM(hombresMes)  as hombresMes,mes  from PlaneacionProyectos where id_proyecto=".$proyecto."  and vigencia=".$lstVigencia." and  unidad=".$datos_usuarios_planeados["unidad"]."   group by mes  ORDER BY(mes)";
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
												$aplica='class=""';		
										}
										else if(($ano_f<$V))
										{
												$aplica='class=""';		
										}
										else
										{
									
											if($V==$ano_i)
											{
									
												if($ban<$mes_i)
												{
													$aplica='class=""';		
												}
											}
									
											if($V==$ano_f)
											{
									
												if($mes_f<$ban)
												{
													$aplica='class=""';		
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
											$ima="#ffb546";
										}
		
										else if ( ($total_perso<=0.75) && (0.5<$total_perso) ) // si el valor esta entre 0.75 y 0.5
										{
		//									$total_perso-=0.5;
											$total_perso=0;
											$ima="#ffb546";
										}
										else if ( ($total_perso<=0.5) && (0.25<$total_perso) ) // si el valor esta entre 0.5 y 0.25
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="#ffb546";
										}
										else if ( ($total_perso<=0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="#ffb546 ";
										}
										else
										{ $ima="white"; } 
										
		
									}
									else //si el valor es mayor a 1
									{
										$ima="#ff6633";
										$total_perso-=1;
									}
		?>
				<td width="4%" align="left" bgcolor="<?  echo $ima; ?>" ><?php echo $datos_total["hombresMes"]; ?></td>                        
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
					<td align="left"><? echo $total; ?></td>
              </tr>

              <tr>
                <td width="2%" class="TituloTabla2">F</td>
<?php
			$sql_fact="select (select SUM(hombresMesF) as total_H_M from FacturacionProyectos where id_proyecto=".$proyecto." and vigencia=".$lstVigencia." and unidad=".$datos_usuarios_planeados["unidad"]." ) as total_H_M ,
			 SUM(hombresMesF) as hombresMes,mes from FacturacionProyectos where id_proyecto=".$proyecto." and vigencia=".$lstVigencia." and unidad=".$datos_usuarios_planeados["unidad"]."  group by mes ORDER BY(mes) ";

			$cur_fact=mssql_query($sql_fact);

					$ban=1;  //permite saber que mes del año se esta dibujando(1,2,3....,12)
					$total=0;
//----------------------------------------------------------------
						while($datos_total=mssql_fetch_array($cur_fact))
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
											$ima="#38a9a9";
										}
		
										else if ( ($total_perso<=0.75) && (0.5<$total_perso) ) // si el valor esta entre 0.75 y 0.5
										{
		//									$total_perso-=0.5;
											$total_perso=0;
											$ima="#38a9a9";
										}
										else if ( ($total_perso<=0.5) && (0.25<$total_perso) ) // si el valor esta entre 0.5 y 0.25
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="#38a9a9";
										}
										else if ( ($total_perso<=0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="#38a9a9";
										}
										else
										{ $ima="white"; } 
										
		
									}
									else //si el valor es mayor a 1
									{
										$ima="#50d3d3";
										$total_perso-=1;
									}
		?>
				<td width="4%" align="left" bgcolor="<?  echo $ima; ?>"  ><?php echo $datos_total["hombresMes"]; ?></td>                        
		<?
								}		
								$ban++;
						}
//echo $ban." ** ";
//----------------------------------------------------------------
						while($ban<13)
						{
							echo "<td class='TxtTabla'></td>";
							$ban++;
						}	
			?>
					<td align="left" class="TxtTabla"><? echo $total; ?></td>
              </tr>
<!--
              <tr class="TituloUsuario">
                        <td colspan="17" align="left" class="TituloUsuario"> </td>
              </tr>
-->
<?
			}

			if( ( (int) (mssql_num_rows($cur_usuarios_planeados) )==0) and(trim(mssql_num_rows($cur_usuarios_planeados))!=""))
			{
?>
  	            <tr >
                        <td colspan="17" align="left" > </td>
              </tr>
  	            <tr >
                        <td colspan="17" align="center">No se encontraron registros. </td>
              </tr>
<?
			}
?>
            </table></td>
          </tr>
</table>

	<table width="100%" cellpadding="0" cellspacing="0" >
		<tr>
            <td align="right" >&nbsp; </td>
      </tr>
		<tr>
            <td align="right">&nbsp; </td>
      </tr>
	</table>

</body>
</html>