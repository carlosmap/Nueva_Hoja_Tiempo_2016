<?php
session_start();

$fecha = date('Y-m-d');

echo "<head>";
header("Content-Type: application/ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=Consolidado_planeación_facturación_división_vs_proyectos_" . $fecha . ".xls");
echo "</head>";

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


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

<?php

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
?>
<body  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="fondo" >
<table width="100%"  border="1" cellspacing="1">
<tr bordercolor="#000000">
  <td align="center" valign="middle" class="LetraIngetecForm Estilo3" width="145" height="49"><div align="center"><img src="http://www.ingetec.com.co/NuevaHojaTiempo/imagenes/logoIngetec.gif" width="140" height="49" align="absmiddle"></div></td>
  <td colspan="<?=mssql_num_rows($cur_proy)+4; ?>" align="center" valign="middle" class="TituloFormato Estilo4"> <b> <h3>Consolidado total de planeaci&oacute;n 

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
en todos los proyectos 
<?php
			$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
			if ((trim($mess)!="") and (trim($ano)!="") and (trim($mes2)!="") and (trim($ano2)!=""))
			{
				echo " ,desde ".$mes[$mess]." del ".$ano." hasta ".$mes[$mes2]." del ".$ano2;
			}




?>

</h3> </b>

</td>

</tr>

<tr align="center" valign="middle">
  <td class="TituloTabla2" ><strong>Unidad</strong></td>
  <td class="TituloTabla2" >Usuario</td>
  <td class="TituloTabla2" ><strong>Categoria</strong></td>
  <td class="TituloTabla2" >&nbsp;</td>
<?

				while($datos_proy=mssql_fetch_array($cur_proy))
				{
?>
    <td class="TituloTabla2"><? echo " [".$datos_proy["id_proyecto"]."]".$datos_proy["nombre"]; ?></td>
<?
					$id_proys[$z]= $datos_proy["id_proyecto"]; 
					$z++;
					
				}

?>
  <td class="TituloTabla2" >Total</td>



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

						<td align="left">
							<? echo $datos_tota_planea["total_hm"]; ?>						</td>
<?
					}
					else
					{
						echo "<td>	</td>";

					}
				}
?>
						<td align="left"><?
								echo $total_planeado;
?>						</td>

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
								$total_facturado=0;
?>
				</td>              </tr>
<!--
              <tr class="TituloUsuario">
                        <td colspan="<?  //mssql_num_rows($cur_proy)+2; ?>" align="left" class="TituloUsuario"> ZZZZZZ</td>
              </tr>
-->
<?
			}
?>
</table>
</body>
</html>