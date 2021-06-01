<?php
 	session_start();
	include "funciones.php";
	//Busca el nombre del usuario que entró
	include "validaUsrBd.php";
	$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$und')";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categ = $fil[categoria];
		$nombUsrConsultado=$fil[nombre];
		$apelUsrConsultado=$fil[apelli];
	}
	
	//Busca el nombre del proyecto
	$sql="SELECT nombre FROM Proyectos WHERE (id_proyecto = $pro)";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$nomProy = $fil[nombre];
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Detalle de actividades</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>
var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes');
	if (window.focus) {newwindow.focus()}
}
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center">DETALLE ACTIVIDADES USUARIO</div>
	</div>
	<div class="Titulos" style="position:absolute; left:10px; top:72px; width: 525px;">
		<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
	</div>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  	<TR><TD> </TD></TR>
  	<TR><TD> </TD></TR>
   <td class="TituloUsuario">Detalle de las actividades programadas en el proyecto <?php echo "Proyecto: "."<strong>".strtoupper($nomProy).".</strong>";?></td>
</table>

<div style="position:absolute; left:10px; top:150px;">
<table border=1 class='TxtTabla'>
<?php
	echo "<tr><td><strong>MACROACTIVIDAD</strong>
	</td><td><strong>NOMBRE DE LA ACTIVIDAD</strong></td><td><strong>Fecha Inicio</strong></td><td><strong>Fecha Fin</strong></td><td><strong>CLASE TIEMPO</strong></td><td><strong>HORAS PROGRAMADAS</strong></td>
	<td><strong>HORAS REPORTADAS</strong></td><td><strong>REMANENTE</strong></td></tr>";
	
	$sql="SELECT     Actividades.id_proyecto AS id_proyecto, Actividades.nombre AS nombreActividad, ISNULL(Actividades.macroactividad, 'SIN') AS macroactividad, 
		Asignaciones.clase_tiempo AS claseTiempo, Asignaciones.unidad AS unidad, Asignaciones.tiempo_asignado,
		ISNULL(SUM(Horas.horas_registradas), 0) AS reportado, Actividades.fecha_inicio AS feI, 
		Actividades.fecha_fin AS feF, Proyectos.id_estado FROM Actividades INNER JOIN
		Asignaciones ON Actividades.id_proyecto = Asignaciones.id_proyecto AND 
		Actividades.id_actividad = Asignaciones.id_actividad INNER JOIN
		Proyectos ON Actividades.id_proyecto = Proyectos.id_proyecto LEFT OUTER JOIN
		Horas ON Asignaciones.id_proyecto = Horas.id_proyecto AND Asignaciones.id_actividad = Horas.id_actividad AND 
		Asignaciones.unidad = Horas.unidad AND Asignaciones.clase_tiempo = Horas.clase_tiempo
		GROUP BY Actividades.id_proyecto, Actividades.nombre, Actividades.macroactividad, 
		Asignaciones.clase_tiempo, Asignaciones.unidad, 
		Asignaciones.tiempo_asignado, Actividades.fecha_inicio, Actividades.fecha_fin, Proyectos.id_estado
		HAVING      (Actividades.id_proyecto = $pro) AND (Asignaciones.unidad = $und) AND (Proyectos.id_estado = 2)";
		
		include "validaUsrBd.php";
		$ap = mssql_query($sql);
		while($reg = mssql_fetch_array($ap)){
			$mActividad = strtoupper($reg[macroactividad]);
			$fiAct		= $reg[feI];
			$ffAct		= $reg[feF];
			$nActividad = strtoupper($reg[nombreActividad]);
			$cTiempo    = strtoupper($reg[claseTiempo]);
			$tProgramado = strtoupper($reg[tiempo_asignado]);
			$tReportado = strtoupper($reg[reportado]);
			$tRemanente = $tProgramado - $tReportado;
			echo "<tr><td>$mActividad</td><td>$nActividad</td><td>$fiAct</td><td>$ffAct</td><td>$cTiempo</td><td>$tProgramado</td><td>$tReportado</td><td>$tRemanente</td></tr>";
		}

?>

</table>
<table>
<tr>
<!--<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','rpte_usuario_individual.php');return document.MM_returnValue" value="  Atras   "></a></td>-->
<!--<td><a href="#"><input name=atras type=button class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="  Inicio   "></a>  </td></tr>-->
<td><a href="#"><input name=atras type=button class="Boton" onclick="window.close();" value="  Cerrar   "></a>  </td></tr>
</table>
</div>
</body>
</html>
