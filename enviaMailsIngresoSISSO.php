<?php

	include("fncEnviaMailPEAR.php");

	#$pPara= "grodrig@ingetec.com.co, pbaron@ingetec.com.co, gbazurto@ingetec.com.co";
	$pPara= "pbaron@ingetec.com.co, oscarlopez@ingetec.com.co";
	$pAsunto= "Bienvenido al equipo de INGETEC.";
	$pTema = "<b>Bienvenido al equipo de INGETEC.</b>
				<br />&nbsp;<br />
				Si va trabajar en las instalaciones de Bogota por favor revise el siguiente procedimiento 
				<a href='http://www.ingetec.com.co/GIDPortal/sisso/index.html'>XXX</a>";
	$pFirma = "Departamento Contratos - Sistemas";
   
	enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

?>
