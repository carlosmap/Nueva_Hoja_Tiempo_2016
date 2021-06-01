<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>

<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

$indi=0;
$sql_act1="
 select *  FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and nivel=1 order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
$cru_act1=mssql_query($sql_act1);
while($datos_act1=mssql_fetch_array($cru_act1))
{
	$actividades_orden[$indi]= "'".$datos_act1["macroactividad"]."'";
	$indi++;
	$sql_act2="	select  cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int),*  FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and nivel=2 and dependeDe=".$datos_act1["id_actividad"]." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
	$cru_act2=mssql_query($sql_act2);

echo " <br><br>".$sql_act2." <br><br>";

	while($datos_act2=mssql_fetch_array($cru_act2))
	{

		$actividades_orden[$indi]="'".$datos_act2["macroactividad"]."'";
		$indi++;

		$sql_act3="	select  cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int),*  FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and nivel=3 and dependeDe=".$datos_act2["id_actividad"]." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
		$cru_act3=mssql_query($sql_act3);

echo " <br><br>".$sql_act3." <br><br>";

		while($datos_act3=mssql_fetch_array($cru_act3))
		{
			$actividades_orden[$indi]="'".$datos_act3["macroactividad"]."'";
			$indi++;
			$sql_act4="
			select  cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int),*  FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and nivel=4 and dependeDe=".$datos_act3["id_actividad"]." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";

echo " <br><br>".$sql_act4." <br><br>";
			$cru_act4=mssql_query($sql_act4);
			while($datos_act4=mssql_fetch_array($cru_act4))
			{
				$actividades_orden[$indi]="'".$datos_act4["macroactividad"]."'";
				$indi++;
			}
		}
	}
}

$sql_act_total="select * from Actividades where id_proyecto=".$cualProyecto."  and macroactividad in(";
	for($z=0;$z<$indi ;$z++)
	{
		echo $actividades_orden[$z]."<br>";
		$sql_act_total=$sql_act_total." '".$actividades_orden[$z]."' ";
		if($z<$indi-1)
			$sql_act_total=$sql_act_total.",";
		
	}
$sql_act_total=$sql_act_total.")";
$cut_act_total=mssql_query($sql_act_total);
echo "".$sql_act_total."<br>";
while($datos_act_total=mssql_fetch_array($cut_act_total))
{
	echo $datos_act_total["macroactividad"]."<br>";
}
 
?>
</body>
</html>