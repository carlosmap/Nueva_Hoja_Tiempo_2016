<?php
session_start();
include "funciones.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript" src="ts_picker.js"></script>
<title>Reportes</title>

<script>
//funcion para validar la fecha
function esDigito(sChr){
	var sCod = sChr.charCodeAt(0);
	return ((sCod > 47) && (sCod < 58));
}
function valSep(oTxt){
	var bOk = false;
	bOk = bOk || ((oTxt.value.charAt(4) == "-") && (oTxt.value.charAt(7) == "-"));
	bOk = bOk || ((oTxt.value.charAt(4) == "/") && (oTxt.value.charAt(7) == "/"));
	return bOk;
}
function finMes(oTxt){
	var nMes = parseInt(oTxt.value.substr(5, 2), 10);
	var nRes = 0;
	switch (nMes){
	case 1: nRes = 31; break;
	case 2: nRes = 29; break;
	case 3: nRes = 31; break;
	case 4: nRes = 30; break;
	case 5: nRes = 31; break;
	case 6: nRes = 30; break;
	case 7: nRes = 31; break;
	case 8: nRes = 31; break;
	case 9: nRes = 30; break;
	case 10: nRes = 31; break;
	case 11: nRes = 30; break;
	case 12: nRes = 31; break;
}
	return nRes;
}
function valDia(oTxt){
	var bOk = false;
	var nDia = parseInt(oTxt.value.substr(8, 2), 10);
	bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));
	return bOk;
}
function valMes(oTxt){
	var bOk = false;
	var nMes = parseInt(oTxt.value.substr(5, 2), 10);
	bOk = bOk || ((nMes >= 1) && (nMes <= 12));
	return bOk;
}
function valAno(oTxt){
	var bOk = true;
	//var nAno = oTxt.value.substr(6);
	var nAno = oTxt.value.substr(0,4);
	bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));
	if (bOk){
		for (var i = 0; i < nAno.length; i++){
		bOk = bOk && esDigito(nAno.charAt(i));
	}
}
return bOk;
}
function valFecha(oTxt){
	var bOk = true;
	if (oTxt.value != ""){
		bOk = bOk && (valAno(oTxt));
		bOk = bOk && (valMes(oTxt));
		bOk = bOk && (valDia(oTxt));
		bOk = bOk && (valSep(oTxt));

		if (!bOk){
			alert("Fecha inválida");
			oTxt.value = "";
			oTxt.focus();
		}
	}
}
//fin de funcion para validar la fecha
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div id="Image20783687" style="position:absolute; left:19px; top:9px; width:154px; height:43px; z-index:5">
	<img src="picsI/Image20783687.gif" width="154" height="43" border="0" name="Image_Image20783687"></div>
	<div id="Layer3" style="position:absolute; left:-39px; top:60px; width:687px; height:22px; z-index:3">
	<img src="picsI/GreenRoundedImage3_0.gif" width="687" height="22" border="0" name="Image_Layer3"></div>
	<div id="Layer2" style="position:absolute; left:648px; top:60px; width:81px; height:36px; z-index:2">
	<img src="picsI/GreenRoundedImage2_0.gif" width="81" height="36" border="0" name="Image_Layer2"></div>
	<div id="Layer12" style="position:absolute; left:404px; top:-2px; width:295px; height:62px; z-index:1">
	<img src="picsI/GreenRoundedImage12_0.gif" width="295" height="62" border="0" name="Image_Layer12"></div>
<br>
<br>
<br>
<br>


<?php
//if($valor=="generar"){

	$fi = $fechaIni;
	$ff = $fechaFin;
	$id = esdirectordivision($laUnidad);
	if($id != -1){
		$dirDiv = $id;
		//si es director de división, en rpte_dir_division.php se buscarán proyectos pues también puede ser
		//director de proyectos
		echo "<script>location.href = 'rpte_director_division.php'</script>";
	}

	$id = esdirectordeproyecto($laUnidad);

	if($id != -1){
		$dirProy = $id;
		//Se indaga si tambien es director de departamento
		echo "<script>location.href = 'rpte_dir_proyecto_mnu.php'</script>";
	}

	$id = escoordinadordeproyecto($laUnidad);

	if($id != -1){
		$dirProy = $id;
		echo "<script>location.href = 'rpte_dir_proyecto_mnu.php'</script>";
	}


	$id = esdirectordepartamento($laUnidad);

	if($id != -1){
		$dirDep = $id;
		echo "<script>location.href = 'rpte_director_departamento.php'</script>";
	}

	//Si no es ninguno de los anteriores, entonces es un usuario
		echo "<script>location.href = 'rpte_usuario_individual.php'</script>";

//}

//funciones
function esdirectordivision($lg){
	$sql = "select * from divisiones where id_director='$lg'";
	include "validaUsrBd.php";
	$ap = mssql_query($sql);
	$reg = mssql_fetch_array($ap);
	if(mssql_num_rows($ap)>0){
		$id_division = $reg[id_division];
		return $id_division;
	}else{
		return -1;
	}
}


function esdirectordepartamento($lg){
	$sql = "select * from departamentos where id_director='$lg'";
	include "validaUsrBd.php";
	$ap = mssql_query($sql);
	$reg = mssql_fetch_array($ap);
	if(mssql_num_rows($ap)>0){
		$id_departamento = $reg[id_departamento];
		return $id_departamento;
	}else{
		return -1;
	}
}

function esdirectordeproyecto($lg){
	$sql = "select * from proyectos where id_director='$lg'";
	include "validaUsrBd.php";
	$ap = mssql_query($sql);

	if(mssql_num_rows($ap)>0){
		/*$i=0;
		while($reg = mssql_fetch_array($ap)){
			$id_proyecto[$i] = $reg[id_proyecto];
			$i++;
		}*/
		return 1;
	}else{
		return -1;
	}
}


function escoordinadordeproyecto($lg){
	$sql = "select * from proyectos where id_coordinador='$lg'";
	include "validaUsrBd.php";
	$ap = mssql_query($sql);

	if(mssql_num_rows($ap)>0){
		/*$i=0;
		while($reg = mssql_fetch_array($ap)){
			$id_proyecto[$i] = $reg[id_coordinador];
			$i++;
		}*/
		return 1;
	}else{
		return -1;
	}
}
?>
</body>
</html>
