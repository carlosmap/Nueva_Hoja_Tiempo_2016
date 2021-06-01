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

//Trae la información del registro seleccionado
//dbo.HorasTMP
//id_proyecto, id_actividad, unidad, fecha, localizacion, cargo, clase_tiempo, 
//horas_registradas, resumen_trabajo, estadoAprobDivision, comentariosDivision, 
//revisadoPorDivision, estadoAprobProyecto, comentariosProyecto, revisadoPorProyecto
$qSql="select * from HorasTMP ";
$qSql=$qSql." where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." and id_actividad = " . $cualActividad;
$qSql=$qSql." and unidad = " . $cualUnidad;
$qSql=$qSql." and fecha = '".$cualFecha."'" ;
$qSql=$qSql." and localizacion = " . $cualLocaliza;
$qSql=$qSql." and cargo = '".$cualCargo."' " ;
$qSql=$qSql." and clase_tiempo = " . $cualClase;
$qCursor = mssql_query($qSql);
if ($qReg=mssql_fetch_array($qCursor)) {
	$phoras_registradas = $qReg[horas_registradas] ;
	$presumen_trabajo = $qReg[resumen_trabajo] ;
}

if (trim($otroProy) == "") {
	$otroProy = "NO";
}

//Si se presionó el botón Grabar
//if ($lNombre != "") {
if ($recarga == "2") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	if ($otroProy == "NO") {
		if ($Horas > 0) {
			//Realiza la inserción de la actividad en la tabla Actividades
			$query = "UPDATE HorasTMP SET " ;
			$query = $query . " horas_registradas = " . $Horas. ", ";
			$query = $query . " resumen_trabajo = '" . $lResumen. "' ";
			$query = $query . " WHERE id_proyecto =" . $miProyecto ;
			$query = $query . " AND id_actividad = " . $miActividad ;
			$query = $query . " AND unidad = " . $miUnidad ;
			$query = $query . " AND fecha = '" . $miFecha . "' ";		
			$query = $query . " AND localizacion = " . $miLocaliza ;
			$query = $query . " AND cargo = '" . $miCargo . "' ";
			$query = $query . " AND clase_tiempo = " . $miClase ;
		}
		else {
			$query = "DELETE FROM HorasTMP " ;
			$query = $query . " WHERE id_proyecto =" . $miProyecto ;
			$query = $query . " AND id_actividad = " . $miActividad ;
			$query = $query . " AND unidad = " . $miUnidad ;
			$query = $query . " AND fecha = '" . $miFecha . "' ";		
			$query = $query . " AND localizacion = " . $miLocaliza ;
			$query = $query . " AND cargo = '" . $miCargo . "' ";
			$query = $query . " AND clase_tiempo = " . $miClase ;
		}
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
	}
	
	if ($otroProy == "SI") {
		//Realiza un insert del nuevo proyecto
		//dbo.HorasTMP
		//id_proyecto, id_actividad, unidad, fecha, localizacion, cargo, clase_tiempo, horas_registradas, 
		//resumen_trabajo, estadoAprobDivision, comentariosDivision, revisadoPorDivision, estadoAprobProyecto, 
		//comentariosProyecto, revisadoPorProyecto
		$qry="INSERT INTO HorasTMP (";
		$qry=$qry." id_proyecto, id_actividad, unidad, fecha, localizacion, cargo, clase_tiempo, horas_registradas, ";
		$qry=$qry." resumen_trabajo ) ";
		$qry=$qry." VALUES (";
		$qry=$qry. $pProyecto . ", ";
		$qry=$qry. $miActividad . ", ";
		$qry=$qry. $miUnidad . ", ";
		$qry=$qry." '" . $miFecha . "', ";
		$qry=$qry. $miLocaliza . ", ";
//		$qry=$qry." '" . $nuevoCargo . "', ";
		$qry=$qry." '" . trim($cpCodigo) . trim($cpCargo) . "', ";
		$qry=$qry. $miClase . ", ";
		$qry=$qry. $horasProyecto . ", ";
		$qry=$qry." '" . $lResumenProyecto . "' ";
		$qry=$qry." ) ";
		$curqry = mssql_query($qry);		

		//Modifica el registro actual o lo elimina si horas es igual a 0
		if ($Horas > 0) {
			$query = "UPDATE HorasTMP SET " ;
			$query = $query . " horas_registradas = " . $Horas. ", ";
			$query = $query . " resumen_trabajo = '" . $lResumen. "' ";
			$query = $query . " WHERE id_proyecto =" . $miProyecto ;
			$query = $query . " AND id_actividad = " . $miActividad ;
			$query = $query . " AND unidad = " . $miUnidad ;
			$query = $query . " AND fecha = '" . $miFecha . "' ";		
			$query = $query . " AND localizacion = " . $miLocaliza ;
			$query = $query . " AND cargo = '" . $miCargo . "' ";
			$query = $query . " AND clase_tiempo = " . $miClase ;
			$cursor = mssql_query($query);
		}
		else {
			$query = "DELETE FROM HorasTMP " ;
			$query = $query . " WHERE id_proyecto =" . $miProyecto ;
			$query = $query . " AND id_actividad = " . $miActividad ;
			$query = $query . " AND unidad = " . $miUnidad ;
			$query = $query . " AND fecha = '" . $miFecha . "' ";		
			$query = $query . " AND localizacion = " . $miLocaliza ;
			$query = $query . " AND cargo = '" . $miCargo . "' ";
			$query = $query . " AND clase_tiempo = " . $miClase ;
			$cursor = mssql_query($query);
		}
	
		//Si los cursores no presentaron problema
		if  ((trim($curqry) != "") AND (trim($cursor) != "")) {
			echo ("<script>alert('La operación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};

	
	}
	
	echo ("<script>window.close();MM_openBrWindow('verHTTMP.php?zUnidad=$miUnidad&Flmes=$Flmes&Flano=$Flano','winHTTMP','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}




?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="ts_picker.js"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}

function envia12(){ 
//alert ("Entro a envia 1");
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
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
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

	fIni = new Date(document.Form1.minfechaInicial.value);
	fFin = new Date(document.Form1.maxfechaFinal.value);
//alert(document.Form1.minfechaInicial.value);
//alert(document.Form1.maxfechaFinal.value);
	
	diferencia = fecha1 - fecha2; 
//  	alert(diferencia);
   	if (diferencia > 0) {
   		alert ("La fecha inicial es MAYOR que la fecha de finalización, por favor realice la corrección.");
		document.Form1.lFechaFin.value = document.Form1.fechaFinActual.value ;
		document.Form1.recarga.value = 1;
	}
		
	if (document.Form1.minfechaInicial.value != '') {
		diferenciaIni = fecha1 - fIni;
//		alert(diferenciaIni);
		if (diferenciaIni > 0) {
			alert ("No puede poner esta fecha de inicio de actividad, hay facturación antes de esta fecha.");
			document.Form1.lFechaInicio.value = document.Form1.fechaIniActual.value ;
			document.Form1.recarga.value = 1;
		}
	}
	
	if (document.Form1.maxfechaFinal.value != '') {
		diferenciaFin = fFin - fecha2 ;
	//	alert(diferenciaFin);	
		if (diferenciaFin > 0) {
			alert ("No puede poner esta fecha de fin de actividad, hay facturación despues de esta fecha");
			document.Form1.lFechaFin.value = document.Form1.fechaFinActual.value ;
			document.Form1.recarga.value = 1;
		}
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
    <td class="TituloUsuario">Facturaci&oacute;n</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  onSubmit="MM_validateForm('Horas','','RisNum','horasProyecto','','RisNum','lResumen','','R','lResumenProyecto','','R');return document.MM_returnValue" >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Horas registradas </td>
    <td class="TxtTabla"><input name="Horas" type="text" class="CajaTexto" id="Horas" value="<? echo $phoras_registradas ; ?>" maxlength="6">
      <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>">
      <input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualActividad; ?>">
      <input name="miUnidad" type="hidden" id="miUnidad" value="<? echo $cualUnidad; ?>">
      <input name="miFecha" type="hidden" id="miFecha" value="<? echo $cualFecha; ?>">
      <input name="miLocaliza" type="hidden" id="miLocaliza" value="<? echo $cualLocaliza; ?>">
      <input name="miCargo" type="hidden" id="miCargo" value="<? echo $cualCargo; ?>">
      <input name="miClase" type="hidden" id="miClase" value="<? echo $cualClase; ?>">
      <input name="Flano" type="hidden" id="Flano" value="<? echo $Flano ; ?>">
      <input name="Flmes" type="hidden" id="Flmes" value="<? echo $Flmes; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Resumen del trabajo</td>
    <td class="TxtTabla"><textarea name="lResumen" cols="70" class="CajaTexto" id="lResumen"><? echo $presumen_trabajo; ?></textarea></td>
  </tr>
  <tr>
    <td class="TituloTabla">Otro proyecto </td>
    <td class="TxtTabla">
	<?
	if ($otroProy == "NO") {
		$selNo = "checked";
		$selSi = "";
	}
	else {
		$selNo = "";
		$selSi = "checked";
	}
	
	?>
	<input name="otroProy" type="radio"  value="NO" <? echo $selNo ; ?>  onClick="envia1()" >
      No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="otroProy" type="radio" value="SI" <? echo $selSi ; ?> onClick="envia1()"  >
        Si</td>
  </tr>
  <? if ($otroProy == "SI") {?>
  <tr>
    <td class="TituloTabla">Cual proyecto?</td>
    <td class="TxtTabla"><select name="pProyecto" class="CajaTexto" id="pProyecto" onChange="envia1()">
		<?

		$psql2="Select * from HojaDeTiempo.dbo.ProyectosTMP  ";
		$psql2=$psql2 . " where id_estado = 2 ";
		$psql2=$psql2 . " and (codigo <> 'ACC' and codigo <> 'AUS' and codigo <> 'ENF' and codigo <> 'LIC'  ";
		$psql2=$psql2 . " and codigo <> 'PER' and codigo <> 'SAN' and codigo <> 'VAC')   ";
		$psql2=$psql2 . " order by nombre ";
		
		$pcursor2 = mssql_query($psql2);
		while ($preg2=mssql_fetch_array($pcursor2)) {
		if ((trim($pProyecto) == "") AND (trim($esteCargo) == "")) {
			$esteProyecto = trim($preg2[id_proyecto]) ;
			$esteCargo = trim($preg2[codigo]) . trim($preg2[cargo_defecto]) ; 
			$esteCodigo = trim($preg2[codigo]) ;
			$esteCargoDef = trim($preg2[cargo_defecto]) ;
		}
		if ($pProyecto == $preg2[id_proyecto]) {
			$selProy = "Selected";
			$esteProyecto = trim($preg2[id_proyecto]) ;
			$esteCargo = trim($preg2[codigo]) . trim($preg2[cargo_defecto]) ; 
			$esteCodigo = trim($preg2[codigo]) ;
			$esteCargoDef = trim($preg2[cargo_defecto]) ;
		}
		else {
			$selProy = "";
		};
		
		?>
          <option value="<? echo $preg2[id_proyecto]; ?>" <? echo $selProy; ?> ><? echo ucwords(strtolower($preg2[nombre])); ?></option>
		 <? } ?> 
        </select>
      <input name="nuevoCargo" type="hidden" id="nuevoCargo" value="<? echo $esteCargo; ?>">
	  <?
	  	//Consulta los cargos adicionales del proyecto seleccionado
		$sql4="Select * from HojaDeTiempo.dbo.cargosTMP where id_proyecto = " . $esteProyecto ;
		$cursor4 = mssql_query($sql4);
	  ?>
	  </td>
  </tr>
  <tr>
    <td class="TituloTabla">C&oacute;digo - Cargo</td>
    <td class="TxtTabla">
	<input name="cpCodigo" type="text" class="CajaTexto" id="cpCodigo" value="<? echo $esteCodigo; ?>" size="10" readonly> 
	- 
	<select name="cpCargo" class="CajaTexto" id="cpCargo" >
          <option value="<? echo $esteCargoDef; ?>"><? echo $esteCargoDef; ?></option>
		  <? 
		  while ($reg4=mssql_fetch_array($cursor4)) { ?>
		  		<option value="<? echo $reg4[cargos_adicionales]; ?>"><? echo $reg4[cargos_adicionales]; ?></option>
		  <? } ?>
        </select>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Horas registradas nuevo proyecto </td>
    <td class="TxtTabla"><input name="horasProyecto" type="text" class="CajaTexto" id="horasProyecto" size="15"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Resumen del trabajo nuevo proyecto </td>
    <td class="TxtTabla">
	<textarea name="lResumenProyecto" cols="70" class="CajaTexto" id="lResumenProyecto"><? echo $presumen_trabajo; ?></textarea>	</td>
  </tr>
  <? } ?>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	  <input name="recarga" type="hidden" id="recarga" value="33">
    <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia12()" ></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
