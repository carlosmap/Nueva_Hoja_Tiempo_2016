<?php
 	session_start();
	include "funciones.php";
	include "validaUsrBd.php";
	$sql = "select * from departamentos where id_director = $laUnidad";
	$ap = mssql_query($sql);
	if(mssql_num_rows($ap) > 0){
		$pac = 1;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reportes</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>

function desabilitar(act){
	//al seleccionar un boton desabilita los demas
	document.rpteProyecto.proyectos_acargo.checked=false
	document.rpteProyecto.mi_programacion.checked=false
	document.rpteProyecto.aprobacion.checked=false
	act.checked=true
}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<? include("bannerArriba.php") ; ?>
<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
	<div align="center"> REPORTE DIRECTOR DE PROYECTO </div>
</div>
<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  <TR><TD>
  <form name=rpteProyecto action="rpte_dir_proyecto_mnu.php" method="post">
	<table>
	<TR><TD> </TD></TR>
	<TR><TD> </TD></TR>
	<tr><td class='TxtTabla'>Reporte de proyectos a  cargo</td>
	<td><input name="proyectos_acargo" type="radio" value="1" onclick='desabilitar(this)'></td></tr>
	<tr><td class="TxtTabla">Mi programación</td><td><input name="mi_programacion" type="radio" value="2" onclick='desabilitar(this)'></td></tr>
	<tr><td class="TxtTabla">Aprobaciones</td><td><input name="aprobacion" type="radio" value="3" onclick='desabilitar(this)'></td></tr>
	<?php
		if($pac == 1){
			echo "<tr><td>Personal a cargo</td><td><input name='personalCargo' type='radio' value='4' onclick='desabilitar(this)'></td></tr>";
		}
	?>
	</table>
<table>
	<tr><td> </td><td> </td></tr>
	<tr><td><input name=enviar type=submit class="Boton" value="Generar Reporte"></td><td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a></td></tr>
	<tr><td> </td><td> </td></tr>
</table>
</form>

<?php

if($enviar=="Generar Reporte"){
	//El sistema identificó al usuario como director de proyecto

	//$dirProy="SI";
	if($proyectos_acargo == 1){
		$sql = "select * from proyectos where id_director='$laUnidad' or id_coordinador=$laUnidad";
		echo $sql;
		$ap = mssql_query($sql);
		if(mssql_num_rows($ap)>0){
			$id=1;
		}else{
			$id=-1;
		}

		if($id == 1){
			echo "<script>location.href = 'rpte_dir_proyecto.php'</script>";
		}else{
			echo "<script>alert('Usted no tiene proyectos a cargo')</script>";
		}
	}elseif($mi_programacion==2){
		echo "<script>location.href = 'rpte_usuario_individual.php'</script>";
	}elseif($aprobacion==3){
		$pac = "";
		$dirDep = "";
		echo "<script>location.href='RangoAprobacion.php'</script>";
	}elseif($personalCargo==4){
		echo "<script>location.href='RangoAprobacion.php'</script>";
	}
}
?>

</table>
  
  
  </TD></TR></table>
  
</body>
</html>
