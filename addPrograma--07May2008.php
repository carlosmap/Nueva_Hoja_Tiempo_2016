<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexi�n a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Traer la informaci�nde la actividad para mostrarlo en el encabezado
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

//trae los datosa de la licta clase de tiempo
$sql3="select * from clase_tiempo " ;
$cursor3 = mssql_query($sql3);

//Trae el cargo_defecto y los cargos_adicionales del proyeecto seleccionado	
$sql4="select id_proyecto, cargo_defecto cargos  " ;
$sql4=$sql4." from proyectos where id_proyecto = " . $cualProyecto ;
$sql4=$sql4." union " ;
$sql4=$sql4." select id_proyecto, cargos_adicionales cargos  " ;
$sql4=$sql4." from cargos where id_proyecto =" . $cualProyecto ;
$cursor4 = mssql_query($sql4);

//Trae los horarios del proyecto
$sql5="select H.* , A.NomHorario, A.Lunes, A.Martes, A.Miercoles,  " ;
$sql5=$sql5." A.Jueves, A.Viernes, A.Sabado, A.Domingo " ;
$sql5=$sql5." from HorariosProy H, Horarios A " ;
$sql5=$sql5." where H.IDhorario = A.IDHorario  " ;
$sql5=$sql5." and H.id_proyecto = " . $cualProyecto ;
$cursor5 = mssql_query($sql5);

//Si se presion� el bot�n Grabar
if ($HorasAsignadas != "") {

	//Valida que no ingrese 0 en horas registradas
	if ($HorasAsignadas == 0) {
			echo ("<script>alert('No puede asignar 0 horas al periodo seleccionado. Por favor corrija la informaci�n.');</script>");
	}
	else {
		//Direcciona a la BD a donde va a grabar
		@mssql_select_db("HojaDeTiempo");
		
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
	
		//Encuentra la siguiente secuencia para la actividad en el proyecto
		$vSql="select COALESCE(COUNT(*), 0) hayActiv from asignaciones ";
		$vSql=$vSql." where id_proyecto =" . $miProyecto  ;
		$vSql=$vSql." and id_actividad = " . $miActividad ;
		$vSql=$vSql." and unidad =" . $pUsuario ;
		$vSql=$vSql." and clase_tiempo =" . $pClase ;
		$vSql=$vSql." and localizacion =" . $pLocaliza ;
		$vSql=$vSql." and cargo = '" . $pCargo . "'";
		$vSql=$vSql." and month(fecha_inicial) =" . $mesSeleccionado ;
		$vSql=$vSql." and year(fecha_inicial) =" . $AnoSeleccionado ;
		$vCursor = mssql_query($vSql);
		if ($vReg=mssql_fetch_array($vCursor)) {
			$pExiste = $vReg[hayActiv];
		}
		if ($pExiste > 0) {
			echo ("<script>alert('El usuario ya esta registrado en la actividad con la clase de tiempo, localizaci�n y cargo seleccionados. Para realizar cambios a esta asignaci�n, cierre esta ventana y edite el registro existente');</script>");
			echo ("<script>window.close();</script>");
			exit;
		}
	
		//Realiza la inserci�n de la persona a la tabla asignaciones
		$query = "INSERT INTO Asignaciones(id_proyecto, id_actividad, unidad, clase_tiempo, localizacion, cargo,   " ;
		$query = $query . " fecha_inicial, fecha_final, tiempo_asignado, IDhorario) ";
		$query = $query . " VALUES( " . $miProyecto . ", " ;
		$query = $query . $miActividad . ", ";	
		$query = $query . $pUsuario . ", ";	
		$query = $query . $pClase . ",  ";	
		$query = $query . $pLocaliza . ", ";	
		$query = $query . " '" . $pCargo . "', ";	
		$query = $query . " '"  . $kFechaIni. "', " ;	
		$query = $query . " '". $kFechaFin. "', " ;	
		$query = $query . $HorasAsignadas . ", ";	
		$query = $query . $pHorario . "  ";	
		$query = $query . " ) ";	
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabaci�n se realiz� con �xito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabaci�n');</script>");
		};
		echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$miProyecto&cualActividad=$miActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
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
        if (isNaN(val)) errors+='- '+nm+' debe ser num�rico.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' debe ser un n�mero entre '+min+' y '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
  } if (errors) alert('Validaci�n:\n'+errors);
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
   		alert ("La fecha inicial es MAYOR que la fecha de finalizaci�n, por favor realice la correcci�n.");
		document.Form1.lFechaFin.value = "";
		}
//      return 1; 
//   else if (diferencia < 0) 
//   		alert ("La fecha inicial es MENOR que la fecha de finalizaci�n ");
//      return -1; 
//   else 
//   	alert ("La fecha inicial es IGUAL que la fecha de finalizaci�n ");
//      return 0; 
}
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programaci�n de Proyectos - Actividades</td>
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
    <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>">	<input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualActividad; ?>"></td>
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
    <td class="TxtTabla"><select name="pUsuario" class="CajaTexto" id="pUsuario" >
      <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra todos los usuarios. 
//		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
//		$sql2="Select * from Usuarios where id_categoria <= 40 "  ;
		$sql2="Select * from Usuarios  "  ;
		$sql2=$sql2." where retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
      <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . " (".$reg2[unidad].") - ". $reg2[TipoContrato] ;  ?></option>
      <? } ?>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Clase de tiempo </td>
    <td class="TxtTabla">
	<select name="pClase" class="CajaTexto" id="pClase">
	<? while ($reg3=mssql_fetch_array($cursor3)) { ?>
      <option value="<? echo  $reg3[clase_tiempo] ; ?>"><? echo  $reg3[descripcion] ; ?></option>
	<? } ?>  
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Localizaci&oacute;n</td>
    <td class="TxtTabla"><select name="pLocaliza" class="CajaTexto" id="pLocaliza">
      <option value="1">1 - Oficina</option>
      <option value="2">2 - Campo </option>
      <option value="3">3 - Personal de planilla</option>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Cargo facturaci&oacute;n </td>
    <td class="TxtTabla">
	<select name="pCargo" class="CajaTexto" id="pCargo">
	<? while ($reg4=mssql_fetch_array($cursor4)) { ?>
      <option value="<? echo  $reg4[cargos] ; ?>"><? echo  $reg4[cargos] ; ?></option>
	<? } ?>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Horario de proyecto </td>
    <td class="TxtTabla">
	<select name="pHorario" class="CajaTexto" id="pHorario">
	<? while ($reg5=mssql_fetch_array($cursor5)) { ?>
      <option value="<? echo  $reg5[IDhorario] ; ?>"><? echo strtoupper($reg5[NomHorario]) . ":::" . $reg5[Lunes] . "-" . $reg5[Martes] . "-" . $reg5[Miercoles] . "-". $reg5[Jueves] .  "-" . $reg5[Viernes]."-" .$reg5[Sabado]. "-" . $reg5[Domingo]  ; ?></option>
	<? } ?>
    </select>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Mes</td>
    <td class="TxtTabla">
	  <select name="pMes" class="CajaTexto" id="pMes">
	<?
	$elMesActual=date("m"); //el mes actual
	$elAnoActual=date("Y"); //el a�o actual
	
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
		
		if 	(($miMes == $elMesActual) AND ($miAno == $elAnoActual)) {
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
    <td class="TxtTabla"><input name="HorasAsignadas" type="text" class="CajaTexto" id="HorasAsignadas" size="20"></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
