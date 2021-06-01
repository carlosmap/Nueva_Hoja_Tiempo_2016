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

//Trae la información de la actividad
$qSql="select * from Actividades ";
$qSql=$qSql." where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." and id_actividad = " . $cualActividad;
$qCursor = mssql_query($qSql);
if ($qReg=mssql_fetch_array($qCursor)) {
	$pnombre = $qReg[nombre] ;
	$pFechaInicio = date("n/d/Y", strtotime($qReg[fecha_inicio])); 
	$pFechaFin = date("n/d/Y", strtotime($qReg[fecha_fin]));
	$pmacroactividad = $qReg[macroactividad] ;
	$pid_encargado = $qReg[id_encargado] ;
	$pavance_reportado = $qReg[avance_reportado] ;
	$presumen_avance = $qReg[resumen_avance] ;
}

//Traer la minima fecha inicial en asignaciones para la actividad seleccionada
$qSql2="SELECT MIN(fecha_inicial) minFechaI FROM asignaciones ";
$qSql2=$qSql2." WHERE id_proyecto = " . $cualProyecto ;
$qSql2=$qSql2." AND id_actividad =" . $cualActividad ;
$qCursor2 = mssql_query($qSql2);
if ($qReg2=mssql_fetch_array($qCursor2)) {
	if (trim($qReg2[minFechaI]) != "") {
		$pMinFechaIni = date("n/d/Y", strtotime($qReg2[minFechaI]));  
	}
	else {
		$pMinFechaIni = "";  
	}
}
//echo "Min asignaciones -->" . $pMinFechaIni ;

//Trae la máxima fecha final en asignaciones para la actividad seleccionada
$qSql2="SELECT MAX(fecha_final) maxFechaF FROM asignaciones ";
$qSql2=$qSql2." WHERE id_proyecto = " . $cualProyecto ;
$qSql2=$qSql2." AND id_actividad =" . $cualActividad ;
$qCursor2 = mssql_query($qSql2);
if ($qReg2=mssql_fetch_array($qCursor2)) {
	if (trim($qReg2[maxFechaF]) != "") {
		$pMinFechaFin = date("n/d/Y", strtotime($qReg2[maxFechaF]));  
	}
	else {
		$pMinFechaFin = "";  
	}
}

//echo "Max asignaciones -->" . $pMinFechaFin ;

//Si se presionó el botón Grabar
//if ($lNombre != "") {
if ($recarga == "2") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Realiza la inserción de la actividad en la tabla Actividades
	$query = "UPDATE Actividades SET " ;
	$query = $query . " nombre = '" . $lNombre. "', ";
	$query = $query . " fecha_inicio = '" . $lFechaInicio. "', ";
	$query = $query . " fecha_fin = '" . $lFechaFin. "', ";
	$query = $query . " macroactividad = '". $lMacroactividad . "', ";
	if (trim($pJefe) == "") {
		$query = $query . " id_encargado = NULL , ";
	}
	else {
		$query = $query . " id_encargado = ". $pJefe. ", ";
	}

	if (trim($lAvance)=="") {
		$query = $query . " avance_reportado=NULL, ";
	}
	else {
		$query = $query . " avance_reportado=" .$lAvance . ", ";
	}
	$query = $query . " resumen_avance='" . $llDescripcion . "'";
	$query = $query . " WHERE id_proyecto =" . $miProyecto ;
	$query = $query . " AND id_actividad = " . $miActividad ;
//	echo $query . "<br>";
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$miProyecto&cualActividad=$miActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

//rENOMBRA LAS VARIABLES PARA NO PERDER EL VALOR CUANDO RECARGA Y ENTRA A GRABAR
$lFechaInicio = $pFechaInicio; 
$lFechaFin = $pFechaFin ;



?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="ts_picker.js"></script>
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
    <td class="TituloUsuario">Programación de Proyectos - Actividades</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  onSubmit="MM_validateForm('lNombre','','R','lFechaInicio','','R','lFechaFin','','R','lAvance','','NisNum');return document.MM_returnValue" >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Nombre</td>
    <td class="TxtTabla"><input name="lNombre" type="text" class="CajaTexto" id="lNombre" value="<? echo $pnombre; ?>" size="70"></td>
  </tr>
  <tr>
    <td class="TituloTabla">C&oacute;digo Macroactividad (m&aacute;ximo 6 caracteres) </td>
    <td class="TxtTabla"><input name="lMacroactividad" type="text" class="CajaTexto" id="lMacroactividad" value="<? echo $pmacroactividad; ?>" maxlength="6">
      <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>">
      <input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualActividad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha inicio actividad </td>
    <td class="TxtTabla">
	<input name="lFechaInicio" class="CajaTexto"  value="<? echo $lFechaInicio;?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.Form1.lFechaInicio);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha fin actividad </td>
    <td class="TxtTabla">
	<input name="lFechaFin" class="CajaTexto" value="<? echo $lFechaFin;?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.Form1.lFechaFin);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=0 id="gToday:normal1:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>	</td>
  </tr>
  <tr>
    <td colspan="2" class="TituloTabla"><img src="img/images/Pixel.gif" width="4" height="4"></td>
    </tr>
  <tr>
    <td class="TituloTabla">Encargado actividad </td>
    <td class="TxtTabla"><select name="pJefe" class="CajaTexto" id="pJefe" >
	<option value=""  >Sin Encargado</option>
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
			if ($pid_encargado == $reg2[unidad]) {
				$selUsu = "selected";
			}
			else {
				$selUsu = "";
			}
		?>
      <option value="<? echo $reg2[unidad]; ?>" <? echo $selUsu; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . " (".$reg2[unidad].") - ". $reg2[TipoContrato] ;  ?></option>
      <? } ?>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">&Uacute;ltimo Avance reportado </td>
    <td class="TxtTabla"><input name="lAvance" type="text" class="CajaTexto" id="lAvance" value="<? echo $pavance_reportado ; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Descripci&oacute;n del avance </td>
    <td class="TxtTabla"><textarea name="llDescripcion" cols="70" class="CajaTexto" id="llDescripcion"><? echo $presumen_avance; ?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	  <input name="recarga" type="hidden" id="recarga" value="2">
	  <input name="fechaIniActual" type="hidden" id="fechaIniActual" value="<? echo $pFechaInicio; ?>">
      <input name="fechaFinActual" type="hidden"  value="<? echo $pFechaFin; ?>">      
	  <input name="maxfechaFinal" type="hidden"  value="<? echo $pMinFechaFin; ?>">    
	  <input name="minfechaInicial" type="hidden" id="minfechaInicial" value="<? echo $pMinFechaIni; ?>">
    <input name="Submit" type="submit" class="Boton" value="Grabar" onClick="compareFechas()" ></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
