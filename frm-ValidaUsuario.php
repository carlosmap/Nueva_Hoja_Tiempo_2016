<?php
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	include "funciones.php";
	session_start();
		$login=$usuarios;
		$clave=$password;
		echo "<meta http-equiv='refresh' content='0;url=frm-GrabaTiempo.php'>";
			
?>
