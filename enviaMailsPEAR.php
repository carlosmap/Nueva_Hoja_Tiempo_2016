<?php

	include("fncEnviaMailPEAR.php");

	$pPara= "grodrig@ingetec.com.co, pbaron@ingetec.com.co, gbazurto@ingetec.com.co";
	$pAsunto= "Prueba Envio Mail  P Baron desde SOLICITUDES ";
	$pTema = "Se informa que se realiz� la asignaci�n de c�digo para: con CCO y portal <b>" . $cualNombre . "[". trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b>";
	$pFirma = "Departamento Contratos - Sistemas";

	enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

?>
