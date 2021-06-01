
<?php
session_start();
//Actualiza un usuario de la lista de usuarios de INGETEC S.A.
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Agrega los usuarios de las unidades 800037 a 800074
for($z = 15724 ; $z<=15810; $z++) {
	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 1, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns1 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 2, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns2 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 3, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns3 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 4, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns4 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 5, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns5 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 6, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns6 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 7, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns7 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 8, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns8 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (42, 1, " . $z . ", 9, 1, '9', '2006-01-03', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns9 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (56, 1, " . $z . ", 1, 1, '0', '2006-02-23', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns10 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (60, 1, " . $z . ", 1, 1, '0', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns11 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (61, 1, " . $z . ", 1, 1, '0', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns12 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (62, 1, " . $z . ", 1, 1, '0', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns13 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (63, 1, " . $z . ", 1, 1, '0', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns14 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (64, 1, " . $z . ", 1, 1, '0', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns15 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (65, 1, " . $z . ", 1, 1, '0', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns16 = mssql_query($qIns1) ;

	$qIns1="INSERT INTO Asignaciones (id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
	$qIns1=$qIns1." VALUES (71, 1, " . $z . ", 1, 1, '7', '2006-03-01', '2100-12-31', -1,7, 0, 0, NULL, NULL) ";
	$cIns17 = mssql_query($qIns1) ;
	
	if  ((trim($cIns1) != "") AND (trim($cIns2) != "") AND (trim($cIns3) != "") AND (trim($cIns4) != "") AND (trim($cIns5) != "") AND (trim($cIns6) != "") AND (trim($cIns7) != "") AND (trim($cIns8) != "") AND (trim($cIns9) != "") AND (trim($cIns10) != "") AND (trim($cIns11) != "") AND (trim($cIns12) != "") AND (trim($cIns13) != "") AND (trim($cIns14) != "") AND (trim($cIns15) != "") AND (trim($cIns16) != "") AND (trim($cIns17) != "") ) {
		echo "Insertó unidad " . $z . "<br>";
	}
	else {
		echo "Error unidad " . $z . "<br>";
	}

}
