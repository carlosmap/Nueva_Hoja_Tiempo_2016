<?php
/*
26Ene2011
Abre el proyecto en una nueva sesi�n
*/

session_start();

$_SESSION["sesProyReportes"] = $_REQUEST["cualProy"];
echo "<script>location.href='rptProySolicitudes.php'</script>";

?>
