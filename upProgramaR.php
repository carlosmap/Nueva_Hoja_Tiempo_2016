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

//09Oct2008
//Traer la cantidad de horas asignadas en el mes en todos los proyectos para la persona seleccionada.

$phorasMesAsignadas=0;
$phorasMesAsignadasProy=0;
//if (trim($pMes) != "") {
	$fechaHM=explode("-",$pMes);
	$mesHM = $fechaHM[0];
	$AnoHM = $fechaHM[1];
	//Todos los proyectos
	$sqlHM="Select coalesce(sum(tiempo_asignado), 0) horasMesAsignadas ";
	$sqlHM=$sqlHM." from asignaciones  ";
	$sqlHM=$sqlHM." where unidad = " . $cualUnidad ;
	$sqlHM=$sqlHM." and (clase_tiempo = 1 or clase_tiempo = 2) ";
//	$sqlHM=$sqlHM." and month(fecha_inicial) = " . $mesHM ;
//	$sqlHM=$sqlHM." and year(fecha_inicial)=" . $AnoHM;
	$sqlHM=$sqlHM." and month(fecha_inicial) = " . $elMesActual ;
	$sqlHM=$sqlHM." and year(fecha_inicial)=" . $elAnoActual;

	$cursorHM = mssql_query($sqlHM);
	if ($regHM=mssql_fetch_array($cursorHM)) { 
		$phorasMesAsignadas=$regHM[horasMesAsignadas];
	}
/*
	//Proyecto activo
	$sqlHM="Select coalesce(sum(tiempo_asignado), 0) horasMesAsignadasProy ";
	$sqlHM=$sqlHM." from asignaciones  ";
	$sqlHM=$sqlHM." where unidad = " . $cualUnidad ;
	$sqlHM=$sqlHM." and (clase_tiempo = 1 or clase_tiempo = 2) ";
	$sqlHM=$sqlHM." and id_proyecto = " . $cualProyecto ;
	$sqlHM=$sqlHM." and id_actividad = " . $cualActividad ;	
	$sqlHM=$sqlHM." and month(fecha_inicial) = " . $mesHM ;
	$sqlHM=$sqlHM." and year(fecha_inicial)=" . $AnoHM;
	$cursorHM = mssql_query($sqlHM);
	if ($regHM=mssql_fetch_array($cursorHM)) { 
		$phorasMesAsignadasProy=$regHM[horasMesAsignadasProy];
	}
*/	
//}

//cierra9Oct2008


//*************

//Si se presionó el botón Grabar
//if ($HorasAsignadas != "") {
if ($recarga == "2") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Valida que no ingrese 0 en horas registradas
	if ($HorasAsignadas == 0) {
			echo ("<script>alert('No puede asignar 0 horas al periodo seleccionado. Por favor corrija la información.');</script>");
	}
	else {
		//09Oct22008
		//Valida que no se supere la cantidad de horas del mes con lo que se esta asignando de programación al proyecto
		//toma lo que tiene asignado en el proyecto, resta las horas asignadas ara ese mes y suma lo que está agregando
		//$nuevoTotalHoras = $totHorasMesProy - horasIni + $HorasAsignadas;
//				echo "<br> Horas para grabar=" .  $nuevoTotalHoras;
//				echo "<br> Horas Mesr=" .  $horasLabMes;

		//Especificar la localización para determinar con qué valor se debe valor Oficina o campo
		$horasLocaliza=0;
		$textoLocaliza="";
		if ($cualLocaliza == 1) {
			//Oficina
			$horasLocaliza=$horasLabMes;
			$textoLocaliza="oficina";
		}
		else {
			//Campo
			$horasLocaliza=$horasCamMes;
			$textoLocaliza="campo";
		}

		//31Oct2008
		//Verifica la cantidad de horas del horario escogido, si la suma semana <= 44 hace la validación, de lo contrario no
		$phorasHorario=0;
		$hSql="select (Lunes+Martes+Miercoles+Jueves+Viernes+Sabado+Domingo) totSemana ";
		$hSql=$hSql." from horarios where idHorario =" . $cualHorario ;
		$hCursor = mssql_query($hSql);
		if ($hReg=mssql_fetch_array($hCursor)) { 
			$phorasHorario=$hReg[totSemana];
		}
//		echo $phorasHorario;
		//Cierra

		//if ($HorasAsignadas > $horasLabMes) {
		//if ($HorasAsignadas > $horasLocaliza) {
//28Oct2008
//verificar si se trata de clase de tiempo 1 o 2, de lo contrario la validación NO aplica
//echo $cualClase ;
//		if (($HorasAsignadas > $horasLocaliza) AND (($cualClase == 1) OR ($cualClase == 2)) ) {
		if (($HorasAsignadas > $horasLocaliza) AND (($cualClase == 1) OR ($cualClase == 2)) AND ($phorasHorario <= 44 ) ) {
			echo ("<script>alert('No puede sobrepasar la cantidad de horas de ".$textoLocaliza." para el mes. Por favor corrija la información.');</script>");
		}
		else {
		//cierra 09Oct2008

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
				if ($mesSeleccionado == 2)  {
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
	
				//Trae el factor multiplicador de acuerdo con la clase de tiempo seleccionada
				$cualFactor = 0;
				$ctSql="select * from HojaDeTiempo.dbo.clase_tiempo ";
				$ctSql=$ctSql." where clase_tiempo = " . $cualClase ;
				$ctCursor = mssql_query($ctSql);
				if ($ctReg=mssql_fetch_array($ctCursor)) {
					$cualFactor = $ctReg[factor];
				}
				
				//Trae las horas laborales
				$horasLaborales = 0;
				$hlSql="select * from HojaDeTiempo.dbo.horasydiaslaborales ";
				$hlSql=$hlSql." where mes =" . $mesSeleccionado ;
				$hlSql=$hlSql." and vigencia =" . $AnoSeleccionado ;
				$hlCursor = mssql_query($hlSql);
				if ($hlReg=mssql_fetch_array($hlCursor)) {
					$horasLaborales = $hlReg[hOficina];
				}
				
				//Trae el salario del usuario seleccionado
				$cualSalario = 0;
				$salarioU="SELECT COALESCE(salario, 0) salario FROM UsuariosSalario  ";
				$salarioU=$salarioU." WHERE unidad = " . $cualUnidad;
				$salarioU=$salarioU." and fecha = (SELECT max(fecha) FROM UsuariosSalario WHERE unidad = " . $cualUnidad . ") ";
				$sUcursor = mssql_query($salarioU);
				if ($suReg=mssql_fetch_array($sUcursor)) {
					$cualSalario = $suReg[salario];
				}
				if ($cualSalario == 0) {
					echo ("<script>alert('El usuario no tiene definido un salario, por favor contacte al departamento de personal para que lo asignen, una vez establecido el salario proceda a realizar la programación de la persona');</script>");
					exit;
				}
		
				//Encontrar el valor calculado para la programación
				//(tiempo_asignado/horasLaborales)*salario*factorClaseTiempo
				//$recursoCalculado=($HorasAsignadas/$horasLaborales)*$cualSalario*$cualFactor;
	
				//Si Clase de tiempo = 1 o 2 se divide por la cantidad de horas laborales del Mes
				//Si Clase de tiempo > 2 se divide siempre por 240
				//if (($cualClase == 1) OR ($cualClase == 2)) {
				
				//08Sep2010 Se incluyó clase de tiempo 10 y 11 por instrucción de Enrique Piñeros para que calcule el salario como si se tratara de tiempo 1
				if (($cualClase == 1) OR ($cualClase == 2) OR ($cualClase == 10) OR ($cualClase == 11)) {
					$recursoCalculado=($HorasAsignadas/$horasLaborales)*$cualSalario*$cualFactor;
					//Si la clase de tiempo es 1 o 2 y el valor de recurso programado supera el valor de salario
					//se debe poner el valor del salario. El valor del recurso nunca puede superar el valor del salario.
					if ($recursoCalculado > $cualSalario) {
						$recursoCalculado=$cualSalario;
					}
				}
				else {
					$recursoCalculado=($HorasAsignadas/240)*$cualSalario*$cualFactor;
				}
				
			
				//Si cualOperacion = I se inserta un nuevo registro. 
				//Esto porque al buscar en Asignaciones tiempo_registrado es igual a 0, por lo tanto el registro no existe
				if (trim($cualOperacion) == "I") {
					//Realiza la inserción de la persona a la tabla Asignaciones
					//id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo, fecha_inicial, fecha_final, 
					//tiempo_asignado, IDhorario, valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion
					$query = "INSERT INTO Asignaciones(id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo,   " ;
					$query = $query . " fecha_inicial, fecha_final, tiempo_asignado, IDhorario,  ";
					$query = $query . " valorProgramado, salarioBase, fechaAsignacion, unidadAsignacion) ";
					$query = $query . " VALUES( " . $cualProyecto . ", " ;
					$query = $query . $cualActividad . ", ";	
					$query = $query . $cualUnidad . ", ";	
					$query = $query . $cualClase . ",  ";	
					$query = $query . $cualLocaliza . ", ";	
					$query = $query . " '" . $cualCargo . "', ";	
					$query = $query . " '"  . $kFechaIni. "', " ;	
					$query = $query . " '". $kFechaFin. "', " ;	
					$query = $query . $HorasAsignadas . ", ";	
					$query = $query . $cualHorario . ",  ";	
					$query = $query . $recursoCalculado . ",  ";	
					$query = $query . $cualSalario . ",  ";	
					$query = $query . " '" . gmdate ("n/d/y")  . "',  ";	
					$query = $query . $laUnidad . "  ";	
					$query = $query . " ) ";	
				}
				else {
					$query = "UPDATE Asignaciones SET " ;
					$query = $query . " tiempo_asignado = " . $HorasAsignadas . ", ";
					$query = $query . " valorProgramado = " . $recursoCalculado . ", " ;
					$query = $query . " salarioBase = " . $cualSalario . ", " ;
					$query = $query . " fechaAsignacion = '" . gmdate ("n/d/y") . "', " ;
					$query = $query . " unidadAsignacion = " . $laUnidad . " " ;
					$query = $query . " WHERE id_proyecto =" . $cualProyecto ;
					$query = $query . " AND id_actividad =" . $cualActividad ;
					$query = $query . " AND unidad =". $cualUnidad ;	
					$query = $query . " AND clase_tiempo =" . $cualClase;
					$query = $query . " AND localizacion =" . $cualLocaliza;
					$query = $query . " AND cargo = '" . $cualCargo . "' ";
					$query = $query . " AND MONTH(fecha_inicial) = " . $mesSeleccionado ;
					$query = $query . " AND YEAR(fecha_inicial) = " . $AnoSeleccionado ;		
				}
			}
			$gCursor = mssql_query($query);
	
			//Si los cursores no presentaron problema
			if  (trim($gCursor) != "") {
				echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
				
				//Para que tome el valor con el que grabó.
				$pTiempoMes	= $HorasAsignadas;
			} 
			else {
				/*echo ("<script>alert('Error durante la grabación');</script>");*/
			};
			
			if (trim($queHacer) == "Continuar") {
				echo ("<script>MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$cualProyecto&cualActividad=$cualActividad&pfDivision=$pfDivision&miDpto=$miDpto&pFiltro=$pFiltro','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");						
			}
			else {
				echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$cualProyecto&cualActividad=$cualActividad&pfDivision=$pfDivision&miDpto=$miDpto&pFiltro=$pFiltro','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
			}
			
		}
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


function envia2(){ 
document.Form1.queHacer.value="Cerrar";	
document.Form1.recarga.value="2";	
document.Form1.submit();
}

function envia3(){ 
document.Form1.queHacer.value="Continuar";	
document.Form1.recarga.value="2";	
document.Form1.submit();
}


function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' debe ser numérico.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' debe ser un número entre '+min+' y '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
  } if (errors) alert('Validación:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
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
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('HorasAsignadas','','RinRange-1:222');return document.MM_returnValue"  >
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
    <td class="TituloTabla">Horas laborales </td>
    <td class="TxtTabla"><?

		if (trim($pMes) != "") {
			$fechaSelB=	explode("-",$pMes);
			$mesSeleccionadoB = $fechaSelB[0];
			$AnoSeleccionadoB = $fechaSelB[1];
		}
		else {
			$mesSeleccionadoB = $elMesActual;
			$AnoSeleccionadoB = $elAnoActual;
		}
		
		//Trae las horas laborales
		$horasLaboralesB = 0;
		$horasCampoB = 0;
		$hlSqlB="select * from HojaDeTiempo.dbo.horasydiaslaborales ";
		$hlSqlB=$hlSqlB." where mes =" . $mesSeleccionadoB ;
		$hlSqlB=$hlSqlB." and vigencia =" . $AnoSeleccionadoB ;
		$hlCursorB = mssql_query($hlSqlB);
		if ($hlRegB=mssql_fetch_array($hlCursorB)) {
			$horasLaboralesB = $hlRegB[hOficina];
			$horasCampoB = $hlRegB[hCampo];
		}
		
		echo "Oficina: " . $horasLaboralesB . "		Campo: " . $horasCampoB;
	?>
	<input name="horasLabMes" type="hidden" id="horasLabMes" value="<? echo $horasLaboralesB; ?>">
	<input name="horasCamMes" type="hidden" id="horasCamMes" value="<? echo $horasCampoB; ?>" >
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Total horas mes asignadas en todos los proyectos CT=1 y CT=2 </td>
    <td class="TxtTabla"><? echo $phorasMesAsignadas; ?>          <input name="totHorasMes" type="hidden" id="totHorasMes" value="<? echo $phorasMesAsignadas; ?>">
          <input name="totHorasMesProy" type="hidden" id="totHorasMesProy" value="<? echo $phorasMesAsignadasProy; ?>">          </td>
  </tr>
  <tr>
    <td class="TituloTabla">Horas asignadas</td>
    <td class="TxtTabla"><input name="HorasAsignadas" type="text" class="CajaTexto" id="HorasAsignadas" value="<? echo $pTiempoMes; ?>" size="20">
      <input name="horasIni" type="hidden" id="horasIni" value="<? echo $pTiempoMes; ?>">
      <a href="#"><img src="img/images/icoCalcu.gif" alt="Calculadora" width="25" height="26" border="0" onClick="MM_openBrWindow('zf20.htm','vtnCalculadora','width=220,height=300')"></a></td>
  </tr>
  <tr>
    <td class="TituloTabla">Horas reportadas </td>
    <td class="TxtTabla"><? echo $pHorasRep; ?>
      <input name="pHorasMes" type="hidden" id="pHorasMes" value="<? echo $pHorasRep; ?>"></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	<input name="pfDivision" type="hidden" id="pfDivision" value="<? echo $pfDivision; ?>">
	  <input name="miDpto" type="hidden" id="miDpto" value="<? echo $miDpto; ?>">
	  <input name="pFiltro" type="hidden" id="pFiltro" value="<? echo $pFiltro; ?>">
	  <input name="queHacer" type="hidden" id="queHacer" value="Cerrar">
	<input name="recarga" type="hidden" id="recarga" value="2">      
      <input name="btnGraba" type="button" class="Boton" onClick="envia3()" value="Grabar y Continuar">
	  <input name="Submit" type="submit" class="Boton" onClick="envia2()" value="Grabar y Cerrar"></td></tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
