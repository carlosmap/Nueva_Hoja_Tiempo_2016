<?
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>

<table width="100%" border="0">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
  <tr>
    <td>Asunto:</td>
    <td colspan="13">Planeacion de actividades</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
 
  </tr>
  <tr>
    <td colspan="14">Los siguientes participantes, han sido planeados en la actividad xxxx, del proyecto xxx, para el año xxx</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="7" align="center">2013</td>

  </tr>
  <tr>
    <td>Unidad</td>
    <td>Nombre</td>
    <td>Departamento</td>
    <td>División</td>
    <td>Proyecto</td>
<?
$minimoMes=4;
$maximoMes=10;
	for ($m=$minimoMes; $m<=$maximoMes; $m++) {		   
			echo '<td width="1%">'.$vMeses[$m] .'</td>';
	 }
?>


  </tr>
<?php

$vigencia="2013";
$unidades= array('18121','18120','18122'); 

$meses= array (); //MESES DE LA VIGENCIA


$vMeses= array("","Ene","Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"); 
/*$mess=1; //INDICE INTERNO DEL ARRAY	 meses
$messe=$minimoMes; // MINIMO MES DE LA VIFENCIA

while($messe<=$maximoMes)
{
	$meses[$mess]=$messe;
	$mess++;
	$messe++;
}
*/



foreach($unidades as $unid)
{
	$sql_planea_usu="select SUM (hombresMes) as total_hombre_mes ,unidad ,mes from PlaneacionProyectos where vigencia=".$vigencia." and mes in(";

/*
	foreach($meses as $mes)
		$sql_planea_usu=$sql_planea_usu." ".$mes.",";
*/
	for ($m=$minimoMes; $m<=$maximoMes; $m++) 
		$sql_planea_usu=$sql_planea_usu." ".$m.",";
	 

	$sql_planea_usu=$sql_planea_usu." 0";

	$sql_planea_usu=$sql_planea_usu.") and unidad=".$unid."   group by unidad,mes  order by mes";	
	$cur_planea_usu=mssql_query($sql_planea_usu);

echo $sql_planea_usu." ---- <br>".mssql_get_last_message()." *** ".mssql_num_rows($cur_planea_usu)."<br>";
	$colum=0;	
	while($datos__planea_usu=mssql_fetch_array($cur_planea_usu))
	{

				$sql_proy_planea=" select SUM (hombresMes) as total_hombre_mes ,PlaneacionProyectos.id_proyecto, Proyectos.nombre as proy ,PlaneacionProyectos.unidad,  Usuarios.nombre, Usuarios.apellidos
		 ,Divisiones.nombre as div,Departamentos.nombre as dep
		  from PlaneacionProyectos 
						inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
						inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
						inner join Divisiones on Divisiones.id_division=Departamentos.id_division
							inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto where vigencia=".$vigencia."  and PlaneacionProyectos.unidad=".$unid." and mes=".$datos__planea_usu["mes"]." 
							group by PlaneacionProyectos.id_proyecto ,PlaneacionProyectos.unidad ,Proyectos.nombre,Usuarios.nombre, Usuarios.apellidos,Divisiones.nombre ,Departamentos.nombre ";
		$cur_proy_planea=mssql_query($sql_proy_planea);


		//CONSULTA LOS PROYECTOS, EN LOS CUALES EL PARTICIAPENTE HA SIDO PLANEADO, ESTO PARA PODER DOBUJAR LA TABLA DE FORMA CORRECTA
		$sql_uu=" select distinct(id_proyecto) from (select SUM (hombresMes) as total_hombre_mes ,unidad, mes, id_proyecto from PlaneacionProyectos where vigencia=".$vigencia." and mes in(";

		for ($m=$minimoMes; $m<=$maximoMes; $m++) 
			$sql_uu=$sql_uu." ".$m.",";

		$sql_uu=$sql_uu." 0 ) and unidad in(".$unid.") group by unidad, mes , id_proyecto  ) aa ";
		$cur_uu=mssql_query($sql_uu);
		$cant_proy=mssql_num_rows($cur_uu);
//echo "<br>---//**************--------------".$sql_uu."<br>".$cant_proy."<br>";

echo $sql_proy_planea." ---- <br>".mssql_get_last_message()."<br><br>";


		while($datos_proy_planea=mssql_fetch_array($cur_proy_planea))
		{
			if($colum==0)
			{
				echo " <br>----> ".mssql_num_rows($cur_proy_planea)."<br>";
?>
              <tr>

                <td rowspan="<?=$cant_proy; ?>"> <?=$datos_proy_planea["unidad"]; ?></td>
                <td rowspan="<?=$cant_proy; ?>"> <?=$datos_proy_planea["nombre"]." ".$datos_proy_planea["apellidos"]; ?></td>
                <td rowspan="<?=$cant_proy; ?>"> <?=$datos_proy_planea["dep"]; ?></td>
                <td rowspan="<?=$cant_proy; ?>"> <?=$datos_proy_planea["div"]; ?></td>

<?
			}
?>
                <td> <? echo "[".$datos_proy_planea["id_proyecto"]."] ". $datos_proy_planea["proy"]; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
<?php
			$colum++;
//			echo $datos_proy_planea["total_hombre_mes"]."<br>";
		}
	}

}

?>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Total planeación</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>

  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
</table>

</body>
</html>
