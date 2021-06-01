<?php
/*
26Ene2011
Abre el proyecto en una nueva sesión
*/

session_start();

$_SESSION["sesProyReportes"] = $_REQUEST["cualProy"];
echo "<script>location.href='rptProySolicitudes.php'</script>";

?>
