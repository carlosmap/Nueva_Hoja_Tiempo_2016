<?php
session_start();
$enviarMail = 1;
//En hdetiempo-aprobdivision se graban las variables de sseion usadaas a continuacion
//Dependiendo del perfil de la persona que autoriza que otras personas vean la hoja de tiempo
if($perfilQueAutoriza <> ""){
	//Guarda el perfil del usuario que actualmente está en el sistema
	if($dirDiv <> ""){
		$tmpPerfil = "dirDiv";
	}elseif($dirDep <> ""){
		$tmpPerfil = "dirDep";
	}elseif($dirProy <> ""){
		$tmpPerfil = "dirProy";
	}
	//Modifica temporalmente el perfil del usuario actual
	if($perfilQueAutoriza == "dirDivision"){
		$dirDiv = 1;
		$dirDep = "";
		$dirProy = "";
	}elseif($perfilQueAutoriza=="dirDepartamento"){
		$dirDep=1;
		$dirDiv="";
		$dirProy="";
	}elseif($perfilQueAutoriza=="dirProyecto"){
		$dirProy=1;
		$dirDiv="";
		$dirDep="";
	}
	$perfilQueAutoriza = "";
}

include "funciones.php";
include "validaUsrBd.php";
?>
<html>
<body bgcolor="6699FF"></body>
<form name="cmt" action="noaprobarFacturacion.php" method="POST" >
	<input type=hidden name="comVlr" value=<? echo $comVlr ?>>
	<input type=hidden name="flag" value=<? echo $flag?>>
	<input type=hidden name="datos" value=<? echo $datos?>>
</form>


<?php

echo "<script>";
echo "if (cmt.comVlr.value == '') {";
echo "cmt.comVlr.value = prompt('Por favor digite sus comentarios')";
echo "}";
echo "</script>";
//recibe la variable datos con lo que se va a aprobar
?>


<script>
	if (cmt.flag.value == '') {
		cmt.flag.value = 1
		cmt.submit();
	}
</script>


<?php


$aprobar = explode("*",substr($datos,1));
//verificamos que las horas hayan sido validadas por un superior inmediato dentro de la empresa
//se tienen tres perfiles que firmarán: Director de división, Director de proyecto, Director de departamento

//Para todos Verifica que el director de haya visto lo facturado
if($dirDiv == ""){
	foreach ($aprobar as $infDia){
		$dato = explode("-",$infDia);
		$fch = explode("/",$dato[3]);

		switch ($fch[0]) {
			case Ene:
				$fch[0]=1;
				break;
			case Feb:
				$fch[0]=2;
				break;
			case Mar:
				$fch[0]=3;
				break;
			case Apr:
				$fch[0]=4;
				break;
			case May:
				$fch[0]=5;
				break;
			case Jun:
				$fch[0]=6;
				break;
			case Jul:
				$fch[0]=7;
				break;
			case Aug:
				$fch[0]=8;
				break;
			case Sep:
				$fch[0]=9;
				break;
			case Oct:
				$fch[0]=10;
				break;
			case Nov:
				$fch[0]=11;
				break;
			case Dec:
				$fch[0]=12;
				break;

		}

		$dato[3]=$fch[0]."/".$fch[1]."/".$fch[2];

		$sql = "select * from horas	where id_proyecto = $dato[0] and id_actividad = $dato[1]
		and unidad = $dato[2] and fecha = '$dato[3]' and localizacion = $dato[4] and cargo = $dato[5]
		and clase_tiempo = $dato[6]";

		$ap = mssql_query($sql);
		$reg = mssql_fetch_array($ap);
		$estadoAprob = $reg[estadoAprobDivision];

		if(is_null($estadoAprob)){
			echo "<script>alert('El director de división no ha dado el visto bueno al tiempo que usted está aprobando. Por lo tanto no podrá continuar');</script>";
			echo "<script>window.close();</script>";
			exit();
		}/*elseif($estadoAprob == "SI"){
			echo "<script>alert('El director de división aprobó el tiempo consultado. Por lo tanto no podrá continuar en su proceso de aprobación');</script>";
			exit();
		}*/
	}
}

//Si es director de proyecto. Aprueba y listo, pues si llegó hasta aquí es por que la división ya aprobó
//Pero si es director de departamento, debe verificar la aprobación del director de proyecto

if($dirDep <> ""){
	foreach ($aprobar as $infDia){
		$dato = explode("-",$infDia);
		//***
		$fch = explode("/",$dato[3]);

		switch ($fch[0]) {
			case Ene:
				$fch[0]=1;
				break;
			case Feb:
				$fch[0]=2;
				break;
			case Mar:
				$fch[0]=3;
				break;
			case Apr:
				$fch[0]=4;
				break;
			case May:
				$fch[0]=5;
				break;
			case Jun:
				$fch[0]=6;
				break;
			case Jul:
				$fch[0]=7;
				break;
			case Aug:
				$fch[0]=8;
				break;
			case Sep:
				$fch[0]=9;
				break;
			case Oct:
				$fch[0]=10;
				break;
			case Nov:
				$fch[0]=11;
				break;
			case Dec:
				$fch[0]=12;
				break;

		}

		$dato[3]=$fch[0]."/".$fch[1]."/".$fch[2];
		//***
		$sql = "select * from horas	where id_proyecto = $dato[0] and id_actividad = $dato[1]
		and unidad = $dato[2] and fecha = '$dato[3]' and localizacion = $dato[4] and cargo = $dato[5]
		and clase_tiempo = $dato[6]";

		if($ap = mssql_query($sql)){
			$reg = mssql_fetch_array($ap);
			$estadoAprob = $reg[estadoAprobProyecto];
		}else{
			echo "<script>alert('Error en la ejecución de la consulta');</script>";
			exit();
		}

		if(is_null($estadoAprob)){
			echo "<script>alert('El director de proyecto no ha dado el visto bueno al tiempo que usted está aprobando. Por lo tanto no podrá continuar en su proceso de aprobación');</script>";
			echo "<script>window.close();</script>";
			exit();
		}/*elseif($estadoAprob == "NO"){
			echo "<script>alert('El director de proyecto NO aprobó el tiempo que usted está consultando. Por lo tanto no podrá continuar en su proceso de aprobación');</script>";
			exit();
		}*/
	}
}

foreach ($aprobar as $infDia){
	$dato = explode("-",$infDia);
	//se connstruye la consulta que pondrá en el campo aprobado de la tabla horas la palabra SI
	$fch = explode("/",$dato[3]);

	switch ($fch[0]) {
		case Ene:
			$fch[0]=1;
			break;
		case Feb:
			$fch[0]=2;
			break;
		case Mar:
			$fch[0]=3;
			break;
		case Apr:
			$fch[0]=4;
			break;
		case May:
			$fch[0]=5;
			break;
		case Jun:
			$fch[0]=6;
			break;
		case Jul:
			$fch[0]=7;
			break;
		case Aug:
			$fch[0]=8;
			break;
		case Sep:
			$fch[0]=9;
			break;
		case Oct:
			$fch[0]=10;
			break;
		case Nov:
			$fch[0]=11;
			break;
		case Dec:
			$fch[0]=12;
			break;

	}

	$dato[3]=$fch[0]."/".$fch[1]."/".$fch[2];
	//Encuentra el mail del usuario para enviarle un correo diciendole que sus horas no fueron aprobadas
		if($enviarMail==1){
			$sqlMail = "select email from usuarios where unidad=$dato[2]";
			$ap = mssql_query($sqlMail);
			$reg = mssql_fetch_array($ap);
			$sqlProy = "select nombre from proyectos where id_proyecto=$dato[0]";
			$ap1 = mssql_query($sqlProy);
			$reg1 = mssql_fetch_array($ap1);
			$nombreProy = $reg1[nombre];

			//Quita los acentos
			$np = QuitarAcentos($nombreProy);
			$np = "Tiempo no aprobado ".$np;
			//$para = trim($reg[email])."@ingetec.com.co";
			//$asunto = "[".$dato[5]."]"." ".$np;
			//$mensaje=wordwrap("Rango de fechas $fiAprobacion-$ffAprobacion. Razón: $comVlr",70);
			//$de = "hojatiempo@ingetec.com.co";
			//mail($para, $asunto, $mensaje, "From:'$de'\nX-Mailer:Mi Script htm\n");
			
			
			//****************ENVIA CORREO************************
			include("fncEnviaMailPEAR.php");
			$pPara= trim($reg[email])."@ingetec.com.co";
			$pAsunto= "[".$dato[5]."]"." ".$np;
			$pTema = wordwrap("Rango de fechas $fiAprobacion-$ffAprobacion. Razón: $comVlr",70);
			$pFirma = "Sistema Portal Ingetec";
			
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			//**********FIN DE LA FUNCION ENVIA CORREO ***********
			
			$enviarMail = 0;
		}
	//Las horas que se visualizan para ser aprobadas son las que aún no están probadas o las que tienen un NO en el campo de aprobaciones
	if($dirDiv <> ""){ //es director de division
		$sql = "update horas set estadoAprobDivision = 'NO', comentariosDivision='$comVlr', revisadoPorDivision=$laUnidad
		where id_proyecto = $dato[0] and id_actividad = $dato[1]
		and unidad = $dato[2] and fecha = '$dato[3]' and localizacion = $dato[4] and cargo = $dato[5]
		and clase_tiempo = $dato[6]";

	}elseif($dirProy <> ""){ //es director de proyecto
		$sql = "update horas set estadoAprobProyecto = 'NO', comentariosProyecto='$comVlr', revisadoPorProyecto=$laUnidad
		where id_proyecto = $dato[0] and id_actividad = $dato[1]
		and unidad = $dato[2] and fecha = '$dato[3]' and localizacion = $dato[4] and cargo = $dato[5]
		and clase_tiempo = $dato[6]";

	}elseif($dirDep <> ""){
		$sql = "update horas set estadoAprobDpto = 'NO', comentariosDpto='$comVlr', revisadoPorDpto=$laUnidad
		where id_proyecto = $dato[0] and id_actividad = $dato[1]
		and unidad = $dato[2] and fecha = '$dato[3]' and localizacion = $dato[4] and cargo = $dato[5]
		and clase_tiempo = $dato[6]";
	}


	if(!mssql_query($sql)){
		echo "<script>alert('Error. El proceso de aprobación no finalizó adecuadamente');</script>";
		echo "<script>history.back()-1</script>";
	}
}
echo "<script>alert('Proceso terminado');</script>";
echo "<script>location.href='hdetiempo-aprobDivision.php'</script>";
//Recupera el perfil del usuario actual
if($tmpPerfil=="dirDivision"){
	$dirDiv = 1;
	$dirDep = "";
	$dirProy = "";
}elseif($tmpPerfil=="dirDepartamento"){
	$dirDiv = "";
	$dirDep = 1;
	$dirProy = "";
}elseif($tmpPerfil=="dirProyecto"){
	$dirDiv = "";
	$dirDep = "";
	$dirProy = 1;
}

//Quitar accentos de un texto
function QuitarAcentos($s) {
   $s = ereg_replace("[áàâãª]","a",$s);
   $s = ereg_replace("[ÁÀÂÃ]","A",$s);
   $s = ereg_replace("[ÍÌÎ]","I",$s);
   $s = ereg_replace("[íìî]","i",$s);
   $s = ereg_replace("[éèê]","e",$s);
   $s = ereg_replace("[ÉÈÊ]","E",$s);
   $s = ereg_replace("[óòôõº]","o",$s);
   $s = ereg_replace("[ÓÒÔÕ]","O",$s);
   $s = ereg_replace("[úùû]","u",$s);
   $s = ereg_replace("[ÚÙÛ]","U",$s);
   $s = str_replace("ç","c",$s);
   $s = str_replace("Ç","C",$s);
   return $s;
}
?>
</html>