<?php

	include("fncEnviaMailPEAR.php");

	$pPara= "grodrig@ingetec.com.co, pbaron@ingetec.com.co, gbazurto@ingetec.com.co";
	$pAsunto= "Prueba Envio Mail  P Baron desde SOLICITUDES ";
	$pTema = "Se informa que se realizó la asignación de código para: con CCO y portal <b>" . $cualNombre . "[". trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b>";
	$pFirma = "Departamento Contratos - Sistemas";

	enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

?>
