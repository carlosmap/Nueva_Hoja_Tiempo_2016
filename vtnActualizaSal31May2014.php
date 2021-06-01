<?php
session_start();


//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//Trae la información de los salarios a 31May2014 segun Archivo de maría Cecilia Herrera
$sql01="SELECT * FROM HojaDeTiempo.dbo.TMPSalariosA31May2014Existen ";
//$sql01=$sql01." WHERE UNIDAD = 15712 ";
$cursor01 = mssql_query($sql01);

//echo $sql01 . "<br>";
while ($reg01=mssql_fetch_array($cursor01)) {
	//Trae la información del máximo salario del usuario
	$cualSalario = 0; 
	$sql02="SELECT *  ";
	$sql02=$sql02." FROM HojaDeTiempo.dbo.UsuariosSalario ";
	$sql02=$sql02." WHERE unidad = " . $reg01[Unidad];
	$sql02=$sql02." and FECHA = (SELECT MAX(fecha) FROM HojaDeTiempo.dbo.UsuariosSalario WHERE UNIDAD = ". $reg01[Unidad] . ")	";
	$cursor02 = mssql_query($sql02);

//	echo $sql02 . "<br>";
//	echo $cursor02 . "<br>";
//	echo "LLegó 3";

	if ($reg02=mssql_fetch_array($cursor02)) {
		//Si la fecha de UsuariosSalario es inferior a la fecha del Archivo proporcionado por Calidad se inserta un nuevo registro.
//		echo "Fecha TMP = " . $reg01[FechaAumento] . "<br>";
//		echo "Fecha UsuarioSalario = " . $reg02[fecha] . "<br>";
		if ($reg01[FechaAumento] > $reg02[fecha]) {
//			echo "La fecha es mayor" . "<br>";
			$sql03="INSERT INTO HojaDeTiempo.dbo.UsuariosSalario (unidad, fecha, salario)  ";
			$sql03=$sql03." VALUES ";
			$sql03=$sql03." ( ";
			$sql03=$sql03." " . $reg01[Unidad] . ", ";
			$sql03=$sql03." '" . date("m/d/Y", strtotime($reg01[FechaAumento])) . "', ";
			$sql03=$sql03." " . $reg01[SueldoActual] . " ";
			$sql03=$sql03." ) ";
			$cursor03 = mssql_query($sql03);
//			echo $sql03 . "<br>";
			
			//Si los cursores no presentaron problema
			if  (trim($cursor03) != "")  {
				echo "La Unidad ".$reg01[Unidad]." insertó correctamente. " . "<br>"; 
			} 
			else {
				echo "Error ". $sql03 . " .No insertó" . "<br>";
			}
		}
		else {
			echo "La Unidad ".$reg01[Unidad]." no requiere actualización de salario. FechaAumento= " . $reg01[FechaAumento] . "FechaSalario=" . $reg02[fecha] . "<br>"; 
		}
	}
}

?>
