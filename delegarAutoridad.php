<?php
	session_start();
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script>
function desactivar(obj){
	document.autoriza.autorizar.checked=false
	document.autoriza.desautorizar.checked=false
	obj.checked=true
}

function buscarProyectos(){
		document.autoriza.submit();

}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function validaBlancos(){
	if(document.autoriza.estado.checked==false){
		if(document.autoriza.autorizado.value == "Seleccione un nombre"){
			alert('Usuario a autorizar no seleccionado');
			return false
		}
		if(document.autoriza.usrRevisado.value == "Seleccione un nombre"){
			alert('El segundo usuario no fue seleccionado');
			return false
		}
		return true
	}
	return true
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Delegación de autoridad</title>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">

<? include("bannerArriba.php") ; ?>
<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
	<div align="center"> DELEGACIÓN DE FUNCIONES EN EL SISTEMA </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>
  
  <TR><TD>
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <TR><TD>  </TD></TR>
  <TR><TD>  </TD></TR>
  <tr>
    <td class="TituloUsuario">Delegar la revisión y aprobación de la hoja de tiempo a otros funcionarios</td>
</table>


<form name="autoriza" action="delegarAutoridad.php" method="POST">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class=TxtTabla><input type=radio name=autorizar onclick = desactivar(this); value=1 checked=true> Autorizar</td>
  </tr>
  <tr>
    <td class=TxtTabla><input type=radio name=desautorizar onclick = desactivar(this); value=2> Eliminar</td>
  </tr>
  <tr>
    <td class=TxtTabla><input type=radio name=estado onclick = desactivar(this); value=3> Estado Autorizaciones</td>
  </tr>
<tr><td><hr /> </td></tr>
</table>

<?php
//Permite seleccionar un usuario para autorizarlo que revise otros usuarios (la hoja de)
	$sql ="select * from usuarios order by apellidos, nombre";
	include "validaUsrBd.php";
	$ap = mssql_query($sql);
	echo "<table>";
	echo "<tr><td width='30%' class=TxtTabla >El usuario:";
	echo "</td><td>";
	echo "<select name=autorizado>";
	if($ap){
		while ($registro =  mssql_fetch_array($ap)){
			$nnb =ucwords($registro[apellidos]);
			$aap= ucwords($registro[nombre]);
			echo "<option value= $registro[unidad]>$nnb $aap</option>";
		}
		if($autorizado == ""){
			echo "<option selected value='Seleccione un nombre'>Seleccione un nombre</option>";
		}elseif($autorizado=="Seleccione un nombre"){
			echo "<option selected value='Seleccione un nombre'>Seleccione un nombre</option>";
		}else{
			$sql = "select * from usuarios where unidad = $autorizado";
			$ap = mssql_query($sql);
			$reg = mssql_fetch_array($ap);
			$nu = ucwords($reg[nombre]);
			$apel = ucwords($reg[apellidos]);
			echo "<option selected value= $autorizado> $apel $nu</option>";
		}
	}
	echo "</select>";

	//Permite seleccionar el usuario cuya hoja de tiempo sera revisada por el usuario arriba seleccionado
	$sql ="select * from usuarios order by apellidos, nombre";
	include "validaUsrBd.php";
	echo "<tr><td class=TxtTabla>";
	$ap = mssql_query($sql);
	echo "Revisará la hoja de tiempo de:";
	echo "</td><td>";
	echo "<select name=usrRevisado>";
	if($ap){
		while ($registro =  mssql_fetch_array($ap)){
			$nnb =ucwords($registro[apellidos]);
			$aap= ucwords($registro[nombre]);
			echo "<option value= $registro[unidad]>$nnb $aap</option>";
		}
		if($usrRevisado == ""){
			echo "<option selected value='Seleccione un nombre'>Seleccione un nombre</option>";
		}elseif($usrRevisado=="Seleccione un nombre"){
			echo "<option selected value='Seleccione un nombre'>Seleccione un nombre</option>";
		}else{
			$sql = "select * from usuarios where unidad = $usrRevisado";
			$ap = mssql_query($sql);
			$reg = mssql_fetch_array($ap);
			$nu = ucwords($reg[nombre]);
			$apel = ucwords($reg[apellidos]);
			echo "<option selected value= $usrRevisado> $apel $nu</option>";
		}
	}
	echo "</select>";
	echo "</td></tr>";

	//Identifica el/los perfiles de usuario de quien está autorizando para permitir que se seleccionen
	//Identifica si es director de departamento
	$id_div = esdirectordivision($laUnidad);
	$id_dep = esdirectordepartamento($laUnidad);
	$id_pro = esdirectordeproyecto($laUnidad);
	$id_coo = escoordinadordeproyecto($laUnidad);
	echo "<tr><td class=TxtTabla>Con el perfil:</font>
	</td><td><select name=elPerfil onchange = buscarProyectos();>";

	//Permite que la opción seleccionada no cambie al seleccionar la opción director de proyecto
	if($elPerfil <> ""){
		if($elPerfil == "dirDivision"){
			echo "<option selected value='dirDivision'>Director de division</option>";
		}elseif($elPerfil == "dirDepartamento"){
			echo "<option value='dirDepartamento'>Director de departamento</option>";
		}elseif($elPerfil == "dirProyecto"){
			echo "<option selected value='dirProyecto'>Director de proyecto</option>";
		}elseif($elPerfil === "cooProyecto"){
			echo "<option selected value='cooProyecto'>Coordinador de Proyecto</option>";
		}

	}

	if($id_div <> -1){
		echo "<option value='dirDivision'>Director de division</option>";
	}
	if($id_dep <> -1){
		echo "<option value='dirDepartamento'>Director de departamento</option>";
	}
	if($id_pro <> -1){
		echo "<option value='dirProyecto'>Director de proyecto</option>";
	}
	if($id_coo <> -1){
		echo "<option value='cooProyecto'>Coordinador de Proyecto</option>";
	}
	echo "</td></tr>";
	echo "</select>";
	echo "</td></tr>";

	if($elPerfil == "dirProyecto"){
		echo "<tr><td class=TxtTabla>Revisará la información del proyecto:</td><td><select name=elProyecto>";
		$sql = "select * from proyectos where id_director = '$laUnidad' or id_coordinador = '$laUnidad'";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$id_pro = $reg[id_proyecto];
			$no_pro = $reg[nombre];
			echo "<option value=$id_pro>$no_pro</option>";
		}
		if($elProyecto == ""){
			echo "<option selected value='Seleccione un proyecto'>Seleccione un Proyecto</option>";
		}elseif($elProyecto=="Seleccione un Proyecto"){
			echo "<option selected value='Seleccione un Proyecto'>Seleccione un Proyecto</option>";
		}else{
			$sql = "select * from proyectos where id_proyecto = $elProyecto";
			echo $sql;
			$ap = mssql_query($sql);
			$reg = mssql_fetch_array($ap);
			$np = ucwords($reg[nombre]);
			$id = $reg[id_proyecto];
			echo "<option selected value=$id>$np</option>";
		}
		echo "</td></tr>";
		echo "</select>";
	}
?>
<table>
<tr><td><input name=Grabar type=submit class="Boton" onClick="return validaBlancos();" value=Continuar></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','CambiodeRol-mnu.php');return document.MM_returnValue" value="  Atras   "></a>  </td></tr>
</table>
</form>
</body>
</html>

<?php
//Algunas funciones
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

<?php
	if($Grabar=="Continuar"){
		if($autorizar=="1"){
			if($elProyecto <> ""){
				$sql="insert into delegacionfunciones values($laUnidad,$autorizado,$usrRevisado,'$elPerfil','$elProyecto')";
			}else{
				$sql="insert into delegacionfunciones values($laUnidad,$autorizado,$usrRevisado,'$elPerfil','')";
			}
			if(mssql_query($sql)){
				echo "<script>alert('Autorización finalizada adecuadamente')</script>";
			}else{
				echo "<script>alert('Esta autorización ya existe en la base de datos')</script>";
				exit();
			}
		}elseif($desautorizar=="2"){
			//Pregunta si el usuario existe
			$sql = "select * from delegacionfunciones where autoriza=$laUnidad and autorizado = $autorizado
			 and undARevisar = $usrRevisado";
			$ap = mssql_query($sql);
			$numreg=mssql_num_rows($ap);
			if($numreg>0){
				$sql = "delete from delegacionfunciones where autoriza=$laUnidad and autorizado = $autorizado
			 	and undARevisar = $usrRevisado";
				if(mssql_query($sql)){
					echo "<script>alert('Autorización eliminada')</script>";
				}
			}else{
				echo "<script>alert('El usuario seleccionado no tiene autorizaciones en el sistema')</script>";
			}
		}elseif($estado==3){
			//muestra el estado de las autorizaciones para el usuario activo
			echo "<hr>";
			echo "<table border = 1>";
			echo "<tr class='TituloUsuario'><td>AUTORIZADO</td><td>PERSONAS A CARGO</td></tr>";
			$sql = "select * from delegacionfunciones where autoriza = $laUnidad";
			$ap = mssql_query($sql);
			$numReg = mssql_num_rows($ap);
			if($numReg > 0){
				while($reg = mssql_fetch_array($ap)){
					$undAutorizado = $reg[autorizado];
					$uRevisar = $reg[undARevisar];
					$sql = "select * from usuarios where unidad=$undAutorizado";
					$ap1 = mssql_query($sql);
					$reg1 = mssql_fetch_array($ap1);
					$nomAutorizado = ucwords($reg1[nombre]);
					$apelAutorizado = ucwords($reg1[apellidos]);
					//Ahora los nombres de las personas que tiene a su cargo
					$sql = "select * from usuarios where unidad=$uRevisar";
					$ap1 = mssql_query($sql);
					$reg1 = mssql_fetch_array($ap1);
					$nomRevisar = ucwords($reg1[nombre]);
					$apelRevisar = ucwords($reg1[apellidos]);
					echo "<tr><td>$apelAutorizado $nomAutorizado</td><td>$apelRevisar $nomRevisar</td></tr>";
				}
			}
		}
	}

?>
</TD></TD></table>

</body>
</html>
