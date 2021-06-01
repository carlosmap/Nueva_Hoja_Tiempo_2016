<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Traer la informaciónde la actividad para mostrarlo en el encabezado
$sql="select * , DATEDIFF(month, fecha_inicio, fecha_fin) AS NumMeses from actividades " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$sql=$sql." and id_actividad = " . $cualActividad ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) { 
	$pActividad=ucwords(strtolower($reg[nombre]));
	$pFechaI= $reg[fecha_inicio] ;
	$pFechaF=date("M d Y ", strtotime($reg[fecha_fin])); 
	$pNumMeses = $reg[NumMeses] + 1; // se suma 1 para contemplar todos los meses
//	echo $pNumMeses . "<br>"; 

//Verifica si la fecha de finalización es superior a la fecha actual
	$nFechaHoy = date("Ym"); 
	$nFechaFin =date("Ym ", strtotime($reg[fecha_fin])); 

}

//Trae la información del registro seleccionado
$sql2="select A.*, U.nombre, U.apellidos, U.TipoContrato, H.NomHorario,  ";
$sql2=$sql2." H.Lunes, H.Martes, H.Miercoles, H.Jueves, H.Viernes, H.Sabado, H.Domingo, C.descripcion ";
$sql2=$sql2." from asignaciones A, Usuarios U, Horarios H , Clase_tiempo C ";
$sql2=$sql2." where A.unidad=U.unidad ";
$sql2=$sql2." and A.IDhorario = H.IDhorario ";
$sql2=$sql2." and A.clase_tiempo = C.clase_tiempo ";
$sql2=$sql2." and A.id_proyecto =" . $cualProyecto ;
$sql2=$sql2." and A.id_actividad =" . $cualActividad ;
$sql2=$sql2." and A.unidad =" . $cualUnidad ;
$sql2=$sql2." and A.clase_tiempo =" . $cualClase ;
$sql2=$sql2." and A.localizacion =" . $cualLocaliza ;
$sql2=$sql2." and A.cargo = '".$cualCargo."'";
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) { 
	$pNombreA= ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . "(" . $reg2[unidad] . ") - " . $reg2[TipoContrato];
	$pClaseTA= $cualClase;
	$pLocalizaA = $cualLocaliza ;	
	$pCargoA = $cualCargo ;
	$pHorarioA = strtoupper($reg2[NomHorario]) . " ::: " . $reg2[Lunes] . "-" . $reg2[Martes] . "-" . $reg2[Miercoles] . "-" . $reg2[Jueves] . "-" . $reg2[Viernes] . "-" . $reg2[Sabado] . "-" . $reg2[Domingo] ;
	$pIdHorarioA = $reg2[IDhorario];
	$pNomClaseTA = $reg2[descripcion];
}

//Traer el tiempo asignado para el mes seleccionado
$sql3="select COALESCE(sum(tiempo_asignado), 0) tiempoMes from asignaciones ";
$sql3=$sql3." where id_proyecto =" . $cualProyecto ;
$sql3=$sql3." and id_actividad =" . $cualActividad ;
$sql3=$sql3." and unidad =" . $cualUnidad ;
$sql3=$sql3." and clase_tiempo =" . $cualClase ;
$sql3=$sql3." and localizacion =" . $cualLocaliza ;
$sql3=$sql3." and cargo = '". $cualCargo ."' " ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes 
if ($pMes == "") {
	//si la fecha final de la actividad es anterir a la fecha de hoy, selecciona la fecha final
	if (strval($nFechaFin) < strval($nFechaHoy)) {
		$elMesActual= substr($nFechaFin,4,5); //el mes actual
		$elAnoActual= substr($nFechaFin,0,4); //el año actual
		$sql3=$sql3." and month(fecha_inicial)= " . $elMesActual ;
		$sql3=$sql3." and year(fecha_inicial)= " . $elAnoActual;
	}
	else {
		//Carga la lista en el mes y año actual
		$elMesActual=date("m"); //el mes actual
		$elAnoActual=date("Y"); //el año actual
		$sql3=$sql3." and month(fecha_inicial)= month(getdate())";
		$sql3=$sql3." and year(fecha_inicial)= year(getdate())";
	}
}
else {
	$fechaMesSel=	explode("-",$pMes);
	$mesSel = $fechaMesSel[0];
	$AnoSel = $fechaMesSel[1];
	$sql3=$sql3." and month(fecha_inicial)= " . $mesSel; 
	$sql3=$sql3." and year(fecha_inicial)= " . $AnoSel;

	//Carga la lista en el mes y año del item seleccionado en la lista
	$elMesActual=$mesSel; //el mes actual
	$elAnoActual=$AnoSel; //el año actual
}
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) { 
	$pTiempoMes = $reg3[tiempoMes] ;
	if ($pTiempoMes != 0) {
		$pOperacion = "D"; //eliminar
	}
	else {
		$pOperacion = "N"; //No hay registro
	}
}

//Trae la sumatoria de horas reportadas para la actividad seleccionada
$sql4="select COALESCE(sum(horas_registradas), 0) totHorasMes ";
$sql4=$sql4." from horas ";
$sql4=$sql4." where id_proyecto = ". $cualProyecto ;
$sql4=$sql4." and id_actividad = " . $cualActividad ;
$sql4=$sql4." and unidad =" . $cualUnidad ;
$sql4=$sql4." and clase_tiempo =" . $cualClase ;
$sql4=$sql4." and localizacion =" . $cualLocaliza ;
$sql4=$sql4." and cargo = '" . trim($cualCodigo) . trim($cualCargo) . "'" ;
$sql4=$sql4." and month(fecha)=" . $elMesActual;
$sql4=$sql4." and year(fecha)=".$elAnoActual;
$cursor4 = mssql_query($sql4);
if ($reg4=mssql_fetch_array($cursor4)) { 
	$pHorasRep = $reg4[totHorasMes] ;
}


//*************

//Si se presionó el botón Grabar
//if ($HorasAsignadas != "") {
if ($recarga == "2") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Valida que no pueda programarse menos de lo que ya está reportado para el mes y año seleccionado
	if ($HorasAsignadas == 0) {
		echo ("<script>alert('No existe programación para este periodo. Por favor corrija la información.');</script>");
	}
	else {
		//Arma la fecha de inicio y la fecha final de acuerdo al periodo seleccionado
		$fechaSel=	explode("-",$pMes);
		$mesSeleccionado = $fechaSel[0];
		$AnoSeleccionado = $fechaSel[1];
		$kFechaIni = $mesSeleccionado."/01/".$AnoSeleccionado;
		if (($mesSeleccionado == 1) OR ($mesSeleccionado == 3) OR ($mesSeleccionado == 5) OR ($mesSeleccionado == 7) OR ($mesSeleccionado == 8) OR ($mesSeleccionado == 10) OR ($mesSeleccionado == 12)) {
			$diaFechaFin="31";
		}
		if (($mesSeleccionado == 2) ) {
			if(checkdate(2, 29, $AnoSeleccionado)) {
				$diaFechaFin="29";
			}
			else {
				$diaFechaFin="28";
			}
		}
		if (($mesSeleccionado == 4) OR ($mesSeleccionado == 6) OR ($mesSeleccionado == 9) OR ($mesSeleccionado == 11) ) {
			$diaFechaFin="30";
		}
		$kFechaFin = $mesSeleccionado."/".$diaFechaFin."/".$AnoSeleccionado;
	
		//Si cualOperacion = I se inserta un nuevo registro. 
		//Esto porque al buscar en Asignaciones tiempo_registrado es igual a 0, por lo tanto el registro no existe
		if (trim($cualOperacion) == "D") {
			$query = "DELETE FROM Asignaciones " ;
			$query = $query . " WHERE id_proyecto =" . $cualProyecto ;
			$query = $query . " AND id_actividad =" . $cualActividad ;
			$query = $query . " AND unidad =". $cualUnidad ;	
			$query = $query . " AND clase_tiempo =" . $cualClase;
			$query = $query . " AND localizacion =" . $cualLocaliza;
			$query = $query . " AND cargo = '" . $cualCargo . "' ";
			$query = $query . " AND MONTH(fecha_inicial) = " . $mesSeleccionado ;
			$query = $query . " AND YEAR(fecha_inicial) = " . $AnoSeleccionado ;		
		}
//		echo $query . "<br>";
		$cursor = mssql_query($query);

		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La operación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la operación');</script>");
		};
		echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$cualProyecto&cualActividad=$cualActividad&pfDivision=$pfDivision&miDpto=$miDpto&pFiltro=$pFiltro','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
	}
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--



function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();

}
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function compareFechas() { 
//alert(document.Form1.lFechaInicio.value);
//alert(document.Form1.lFechaFin.value);
	fecha1=new Date(document.Form1.lFechaInicio.value); 
	fecha2=new Date(document.Form1.lFechaFin.value); 

	diferencia = fecha1 - fecha2; 
//  	alert(diferencia);
   	if (diferencia > 0) {
   		alert ("La fecha inicial es MAYOR que la fecha de finalización, por favor realice la corrección.");
		document.Form1.lFechaFin.value = "";
		}
//      return 1; 
//   else if (diferencia < 0) 
//   		alert ("La fecha inicial es MENOR que la fecha de finalización ");
//      return -1; 
//   else 
//   	alert ("La fecha inicial es IGUAL que la fecha de finalización ");
//      return 0; 
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Actividades</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Actividad</td>
    <td class="TxtTabla">
	<?
	echo $pActividad;
	?>
    <input name="cualProyecto" type="hidden" id="cualProyecto"  value="<? echo $cualProyecto; ?>">	
	<input name="cualActividad" type="hidden" id="cualActividad" value="<? echo $cualActividad; ?>">
    <input name="cualUnidad" type="hidden" id="miUnidad" value="<? echo $cualUnidad; ?>">
    <input name="cualClase" type="hidden" id="cualClase" value="<? echo $cualClase; ?>">
	<input name="cualLocaliza" type="hidden" id="cualLocaliza" value="<? echo $cualLocaliza; ?>">
	<input name="cualCargo" type="hidden" id="cualCargo" value="<? echo $cualCargo; ?>">
    <input name="cualHorario" type="hidden" id="cualHorario" value="<? echo $pIdHorarioA; ?>">
    <input name="cualOperacion" type="hidden" id="cualOperacion" value="<? echo $pOperacion; ?>">	
	<input name="cualCodigo" type="hidden" id="cualCodigo" value="<? echo $cualCodigo; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha Inicial </td>
    <td class="TxtTabla">	
	<? echo date("M d Y ", strtotime($pFechaI)); 	?>
</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha Final </td>
    <td class="TxtTabla">	
	<?
	echo $pFechaF; 
	?>
</td>
  </tr>
  <tr>
    <td colspan="2" class="TituloTabla"><img src="img/images/Pixel.gif" width="4" height="4"></td>
    </tr>
  <tr>
    <td class="TituloTabla">Personal</td>
    <td class="TxtTabla">
	<?
	echo $pNombreA;
	?>
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Clase de tiempo </td>
    <td class="TxtTabla">
	<?
	echo $pNomClaseTA;
	?></td>
  </tr>
  <tr>
    <td class="TituloTabla">Localizaci&oacute;n</td>
    <td class="TxtTabla">
	<? 
	if ($pLocalizaA == 1) {
		echo "1 - Oficina";	
	}
	if ($pLocalizaA == 2) {
		echo "2 - Campo";	
	}
	if ($pLocalizaA == 3) {
		echo "3 - Personal de planilla";	
	}
	?></td>
  </tr>
  <tr>
    <td class="TituloTabla">Cargo facturaci&oacute;n </td>
    <td class="TxtTabla">
	<?
	echo $pCargoA ;
	?></td>
  </tr>
  <tr>
    <td class="TituloTabla">Horario de proyecto </td>
    <td class="TxtTabla">
	<?
	echo $pHorarioA ;
	?>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Mes</td>
    <td class="TxtTabla">
	  <select name="pMes" class="CajaTexto" id="pMes" onChange="envia1()" >
	<?
	
	$mesInicial=date("n",strtotime($pFechaI));
	$anoInicial=date("Y",strtotime($pFechaI));
	$miMes = $mesInicial;
	$miAno=$anoInicial;

	for ($i=0; $i<$pNumMeses; $i++ ) {
		switch ($miMes) {
		case 1:
			$nombreMes="Enero";
			break;
		case 2:
			$nombreMes="Febrero";
			break;
		case 3:
			$nombreMes="Marzo";
			break;
		case 4:
			$nombreMes="Abril";
			break;
		case 5:
			$nombreMes="Mayo";
			break;
		case 6:
			$nombreMes="Junio";
			break;
		case 7:
			$nombreMes="Julio";
			break;
		case 8:
			$nombreMes="Agosto";
			break;
		case 9:
			$nombreMes="Septiembre";
			break;
		case 10:
			$nombreMes="Octubre";
			break;
		case 11:
			$nombreMes="Noviembre";
			break;
		case 12:
			$nombreMes="Diciembre";
			break;
		}
		
		if 	(($miMes == $elMesActual) AND ($miAno == $elAnoActual) ) {
			$selItem = "selected" ;
		}
		else {
			$selItem = "" ;
		}
		
//		echo $miMes . "-" . $nombreMes . "-" . $miAno . "<br>";
?>
	 <option value="<? echo $miMes . "-" . $miAno ; ?>" <? echo $selItem; ?> ><? echo $nombreMes . "-" . $miAno ; ?></option>
<?
		$miMes =$miMes+1;
		if ($miMes > 12) {
			$miMes =1;
			$miAno = $miAno + 1;
		}		
	}
	?>
	    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Horas asignadas</td>
    <td class="TxtTabla"><input name="HorasAsignadas" type="text" class="CajaTexto" id="HorasAsignadas" value="<? echo $pTiempoMes; ?>" size="20" readonly></td>
  </tr>
  <tr>
    <td class="TituloTabla">Horas reportadas </td>
    <td class="TxtTabla"><? echo $pHorasRep; ?>
      <input name="pHorasMes" type="hidden" id="pHorasMes" value="<? echo $pHorasRep; ?>"></td>
  </tr>
  <tr align="center">
    <td colspan="2" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar la programaci&oacute;n para este peri&oacute;do?</strong></td>
    </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	<input name="pfDivision" type="hidden" id="pfDivision" value="<? echo $pfDivision; ?>">
	  <input name="miDpto" type="hidden" id="miDpto" value="<? echo $miDpto; ?>">
	  <input name="pFiltro" type="hidden" id="pFiltro" value="<? echo $pFiltro; ?>">
	<input name="recarga" type="hidden" id="recarga" value="2">      
      <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
      <? if ($pHorasRep <= 0)  { ?>
	  <input name="Submit" type="submit" class="Boton" value="Borrar">
	  <? }?>
	</td></tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
