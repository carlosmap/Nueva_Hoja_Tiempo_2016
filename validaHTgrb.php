<?php
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?
//Conecta la BD de la HT
@mssql_select_db("HojaDeTiempo",$conexion);

//Variables que identifican el mes y año de la facturación que va a generarse para el reporte.
$mes=gmdate ("n");
if ($mes == 1) {
	$mesRpt = 12;
	$anoRpt = gmdate ("Y") - 1;
}
else {
	$mesRpt = $mes - 1;
	$anoRpt = gmdate ("Y");
}


echo $mesRpt . "\n";
echo $anoRpt . "\n";

//Elimina el contenido de la tabla ValidaUsuHT
$sql0="DROP TABLE ValidaUsuHT ";
$cursor = mssql_query($sql0);
echo "Borró tablas ?";
//Selecciona todos lnos usuarios que se encuentran activos y los crea en la tabla ValidaUsuHT.
$sql1="select unidad, TipoContrato, ". $mesRpt ." mes, ". $anoRpt ." ano,  ";
$sql1=$sql1." 0 d1, 0 d2, 0 d3, 0 d4, 0 d5, 0 d6, 0 d7, 0 d8, 0 d9, 0 d10, ";
$sql1=$sql1." 0 d11, 0 d12, 0 d13, 0 d14, 0 d15, 0 d16, 0 d17, 0 d18, 0 d19, 0 d20, ";
$sql1=$sql1." 0 d21, 0 d22, 0 d23, 0 d24, 0 d25, 0 d26, 0 d27, 0 d28, 0 d29, 0 d30, 0 d31, ";
$sql1=$sql1." getdate() fechaRep, '                             ' horaRep ";
$sql1=$sql1." into ValidaUsuHT ";
$sql1=$sql1." from usuarios where retirado is null ";
$cursor1 = mssql_query($sql1);

//echo "realizó la consulta" . strftime('%r', strtotime('now'));

//Trae los datos de la tabla 
$sql2="select * from ValidaUsuHT ";
$cursor2 = mssql_query($sql2);


//Recorre los usuarios y por cada uno de ellos 
//Arma un vector con total por día de las horas facturadas.
//Luego hace update por cada usuario para actualizar las horas facturadas
while ($reg2=mssql_fetch_array($cursor2)) {
	
	echo $reg2[unidad];	
	
	//Determina la clase de tiempo del usuario 
	if (strtoupper($reg2[TipoContrato]) == "TC") {
		$lCTiempo= "1";
	}
	else {
		$lCTiempo= "2";
	}
	
	//Trae el total del horas facturadas x día al mes por mes y año
	$sql3="select fecha, COALESCE(sum(horas_registradas), 0) horasRep  ";
	$sql3=$sql3." from horas ";
	$sql3=$sql3." where unidad = " . $reg2[unidad];
	$sql3=$sql3." and clase_tiempo =" . $lCTiempo;
	$sql3=$sql3." and month(fecha) =" . $mesRpt;
	$sql3=$sql3." and year(fecha) =" . $anoRpt;
	$sql3=$sql3." group by fecha ";
	$sql3=$sql3." order by fecha ";
//	echo $sql3 . "<br>";
	$cursor3 = mssql_query($sql3);
	
	//Crea el vector de los días del mes
	$factMes = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

	//Recorre el cursor de las horas facturadas y actualiza las horas en el vector 
	while ($reg3=mssql_fetch_array($cursor3)) {
		$dia = date("d", strtotime($reg3[fecha]));
//		echo $dia . "<br>";
		$factMes[$dia-1] = $reg3[horasRep] ;
	}
	
	//Actualiza el registro
	$query = "UPDATE  ValidaUsuHT SET "; 
	$query = $query . " d1 = " . $factMes[0] . ", ";
	$query = $query . " d2 = " . $factMes[1] . ", ";
	$query = $query . " d3 = " . $factMes[2] . ", ";
	$query = $query . " d4 = " . $factMes[3] . ", ";
	$query = $query . " d5 = " . $factMes[4] . ", ";
	$query = $query . " d6 = " . $factMes[5] . ", ";
	$query = $query . " d7 = " . $factMes[6] . ", ";
	$query = $query . " d8 = " . $factMes[7] . ", ";
	$query = $query . " d9 = " . $factMes[8] . ", ";
	$query = $query . " d10 = " . $factMes[9] . ", ";
	$query = $query . " d11 = " . $factMes[10] . ", ";
	$query = $query . " d12 = " . $factMes[11] . ", ";
	$query = $query . " d13 = " . $factMes[12] . ", ";
	$query = $query . " d14 = " . $factMes[13] . ", ";
	$query = $query . " d15 = " . $factMes[14] . ", ";
	$query = $query . " d16 = " . $factMes[15] . ", ";
	$query = $query . " d17 = " . $factMes[16] . ", ";
	$query = $query . " d18 = " . $factMes[17] . ", ";
	$query = $query . " d19 = " . $factMes[18] . ", ";
	$query = $query . " d20 = " . $factMes[19] . ", ";
	$query = $query . " d21 = " . $factMes[20] . ", ";
	$query = $query . " d22 = " . $factMes[21] . ", ";
	$query = $query . " d23 = " . $factMes[22] . ", ";
	$query = $query . " d24 = " . $factMes[23] . ", ";
	$query = $query . " d25 = " . $factMes[24] . ", ";
	$query = $query . " d26 = " . $factMes[25] . ", ";
	$query = $query . " d27 = " . $factMes[26] . ", ";
	$query = $query . " d28 = " . $factMes[27] . ", ";
	$query = $query . " d29 = " . $factMes[28] . ", ";
	$query = $query . " d30 = " . $factMes[29] . ", ";
	$query = $query . " d31 = " . $factMes[30] . ", ";
	$query = $query . " fechaRep = '" . gmdate ("n/d/y") . "',  ";
	$query = $query . " horaRep = '". strftime('%r', strtotime('now')) . "'  ";
	$query = $query." where unidad = " . $reg2[unidad];
	$query = $query." and mes =" . $mesRpt;
	$query = $query." and ano =" . $anoRpt;
	$cursorUp = mssql_query($query) ;	
//	echo "Insertó registro <br>" .strftime('%r', strtotime('now')) ;
//	echo $query . "<br>";
	echo "Actualizó" ;
}
echo "::::::::::::::::::::::: Proceso Finalizado :::::::::::::::::::::::::::::::";
mssql_close ($conexion); 

?>	
