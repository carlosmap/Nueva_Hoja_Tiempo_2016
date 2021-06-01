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

//Si se presion� el bot�n Grabar
if ($lNombre != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Encuentra la siguiente secuencia para la actividad en el proyecto
	$sql="select Max(id_actividad) maxIdActiv from Actividades where id_proyecto = " . $miProyecto ;
	$cursor = mssql_query($sql);
	if ($reg=mssql_fetch_array($cursor)) {
		$pIdActiv = $reg[maxIdActiv] + 1;
		}
	else {
		$pIdActiv = 1;
	}
	
		
	//Realiza la inserci�n de la actividad en la tabla Actividades
	$query = "INSERT INTO Actividades(id_proyecto, id_actividad, nombre, fecha_inicio, fecha_fin, macroactividad,  " ;
	$query = $query . " id_encargado, avance_reportado, resumen_avance, dependeDe ) ";
	$query = $query . " VALUES( " . $miProyecto . ", " ;
	$query = $query . $pIdActiv . ", ";
	$query = $query . " '" . $lNombre . "', ";
	$query = $query . " '" . $lFechaInicio . "',";
	$query = $query . " '" . $lFechaFin . "',";
	$query = $query . " '" . $lMacroactividad . "', ";
	if (trim($pJefe) == "") {
		$query = $query . " NULL, ";
	}
	else {
		$query = $query . $pJefe. ", ";
	}
	$query = $query . $lAvance . " ,";
	$query = $query . " '" . $llDescripcion . "', ";
	$query = $query . $depende ;
	$query = $query . " ) ";
	$cursor = mssql_query($query);

//echo $query . "<br>" ;
//exit;

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabaci�n se realiz� con �xito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabaci�n');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	

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
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
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
    <td class="TituloUsuario">Programaci�n de Proyectos - Actividades Responsable </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onClick="compareFechas()" onSubmit="MM_validateForm('lNombre','','R','lFechaInicio','','R','lFechaFin','','R','lAvance','','NisNum');return document.MM_returnValue" >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Nombre</td>
    <td class="TxtTabla"><input name="lNombre" type="text" class="CajaTexto" id="lNombre" size="70"></td>
  </tr>
  <tr>
    <td class="TituloTabla">C&oacute;digo Macroactividad (m&aacute;ximo 6 caracteres) </td>
    <td class="TxtTabla"><input name="lMacroactividad" type="text" class="CajaTexto" id="lMacroactividad" maxlength="6">
      <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>"></td>
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
    <td class="TxtTabla"><select name="pEncarg" class="CajaTexto" id="pEncarg" disabled >
	<option value="" selected >Sin Encargado</option>
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
		if ($cualResp == $reg2[unidad]) { 
			$selOpc = "selected";
		}
		else {
			$selOpc = "";
		}
		?>
      <option value="<? echo $reg2[unidad]; ?>" <? echo $selOpc; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . " (".$reg2[unidad].") - ". $reg2[TipoContrato] ;  ?></option>
      <? } ?>
    </select>
      <input name="pJefe" type="hidden" id="pJefe" value="<? echo $cualResp; ?>">
      <input name="depende" type="hidden" id="depende" value="<? echo $cualActDe; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">&Uacute;ltimo Avance reportado </td>
    <td class="TxtTabla"><input name="lAvance" type="text" class="CajaTexto" id="lAvance" value="0"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Descripci&oacute;n del avance </td>
    <td class="TxtTabla"><textarea name="llDescripcion" cols="70" class="CajaTexto" id="llDescripcion"></textarea></td>
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
