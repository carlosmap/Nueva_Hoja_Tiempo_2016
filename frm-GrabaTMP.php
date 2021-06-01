<?php
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	include "funciones.php";
	session_start();
		$nombrecomputador="sqlservidor";
		$login=$usuarios;
		$clave=$password;
	
	//Busca el nombre del usuario que entró
	conexion($nombrecomputador,$login,$clave);
	$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$login')";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categoria=$fil[categoria];
		$nombreempleado=$fil[nombre];
		$apellidoempleado=$fil[apelli];
	} else {
		alert("Usuario no registrado");
		echo "<meta http-equiv='refresh' content='0;url=Inicio.php'>";
	}
	
		echo "<meta http-equiv='refresh' content='0;url=frm-GrabaTiempo.php'>";
			
?>
