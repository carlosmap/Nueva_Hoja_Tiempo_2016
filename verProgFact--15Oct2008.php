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
	$sql3=$sql3." and month(fecha_inicial)= month(getdate())";
	$sql3=$sql3." and year(fecha_inicial)= year(getdate())";

	//Carga la lista en el mes y año actual
	$elMesActual=date("m"); //el mes actual
	$elAnoActual=date("Y"); //el año actual
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
		$pOperacion = "U";
	}
	else {
		$pOperacion = "I";
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
	if ($HorasAsignadas < $pHorasMes) {
		echo ("<script>alert('No puede programar menos horas que las reportadas. Por favor corrija la información.');</script>");
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
		if (trim($cualOperacion) == "I") {
			//Realiza la inserción de la persona a la tabla asignaciones
			$query = "INSERT INTO Asignaciones(id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo,   " ;
			$query = $query . " fecha_inicial, fecha_final, tiempo_asignado, IDhorario) ";
			$query = $query . " VALUES( " . $cualProyecto . ", " ;
			$query = $query . $cualActividad . ", ";	
			$query = $query . $cualUnidad . ", ";	
			$query = $query . $cualClase . ",  ";	
			$query = $query . $cualLocaliza . ", ";	
			$query = $query . " '" . $cualCargo . "', ";	
			$query = $query . " '"  . $kFechaIni. "', " ;	
			$query = $query . " '". $kFechaFin. "', " ;	
			$query = $query . $HorasAsignadas . ", ";	
			$query = $query . $cualHorario . "  ";	
			$query = $query . " ) ";	
		}
		else {
			$query = "UPDATE Asignaciones SET " ;
			$query = $query . " tiempo_asignado = " . $HorasAsignadas;
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
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$cualProyecto&cualActividad=$cualActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
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
</table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr class="TituloTabla2">
    <td width="25%">Periodo</td>
    <td width="25%">Horas Programadas </td>
    <td width="25%">Horas Reportadas </td>
    <td width="25%">Valor Recurso </td>
  </tr>
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
		
		
//		echo $miMes . "-" . $nombreMes . "-" . $miAno . "<br>";

?>
  <tr class="TxtTabla">
    <td width="25%"><? echo $nombreMes . "-" . $miAno ; ?></td>
    <td width="25%" align="right">
	<? 
//	$pSql="SELECT COALESCE(sum(tiempo_asignado), 0) horasProg ";
	$pSql="SELECT COALESCE(sum(tiempo_asignado), 0) horasProg , COALESCE(sum(valorProgramado), 0) valorProgramado ";
	$pSql=$pSql." FROM asignaciones  ";
	$pSql=$pSql." WHERE id_proyecto =" . $cualProyecto;
	$pSql=$pSql." and unidad =" . $cualUnidad;
	$pSql=$pSql." and id_actividad =" . $cualActividad;;	
	$pSql=$pSql." and clase_tiempo =" . $cualClase;
	$pSql=$pSql." and localizacion =" . $cualLocaliza;
	$pSql=$pSql." and cargo = '" . $cualCargo ."'";
	$pSql=$pSql." and month(fecha_inicial)= month(fecha_final) ";
	$pSql=$pSql." and year(fecha_inicial)= year(fecha_final) ";
	$pSql=$pSql." and month(fecha_inicial)= " . $miMes;
	$pSql=$pSql." and year(fecha_inicial)= " . $miAno;
	$pCursor = mssql_query($pSql);
	if ($pReg=mssql_fetch_array($pCursor)) { 
		echo $pReg[horasProg] ;
		$miVP = $pReg[valorProgramado] ;
	}

//cualCodigo=<? echo ; 

?>

	</td>
    <td width="25%" align="right">
	<? 
	$rSql="select COALESCE(sum(horas_registradas), 0) horasReport  ";
	$rSql=$rSql." from horas ";
	$rSql=$rSql." WHERE id_proyecto =" . $cualProyecto;
	$rSql=$rSql." and unidad =" . $cualUnidad;
	$rSql=$rSql." and id_actividad =" . $cualActividad;;	
	$rSql=$rSql." and clase_tiempo =" . $cualClase;
	$rSql=$rSql." and localizacion =" . $cualLocaliza;
	$rSql=$rSql." and cargo = '" . $cualCodigo . $cualCargo ."'";
	$rSql=$rSql." and month(fecha)=" . $miMes;
	$rSql=$rSql." and year(fecha)=" . $miAno;	
	$rCursor = mssql_query($rSql);
	if ($rReg=mssql_fetch_array($rCursor)) { 
		echo $rReg[horasReport] ;
	}

?>
	</td>
    <td width="25%" align="right"><? echo "$ " . number_format($miVP, 0 , ',', '.'); ?></td>
  </tr>
<?
		$miMes =$miMes+1;
		if ($miMes > 12) {
			$miMes =1;
			$miAno = $miAno + 1;
		}		
	}
	?>  
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar"></td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
