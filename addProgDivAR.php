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

//Si se presionó el botón Grabar
if ($fechaI != "") {
	//Realiza la inserción de la información de la asignación de recursos
	//ProgAsignaRecursos
	//id_proyecto, unidadProgramador, fechaInicio, plazo
	$query = "INSERT INTO ProgAsignaRecursos(id_proyecto, unidadProgramador, fechaInicio, plazo)   " ;
	$query = $query . " VALUES( " . $cualProyecto . ", " ;
	$query = $query . $laUnidad . ", ";	
	$query = $query . " '"  . $fechaI . "', " ;	
	$query = $query . $lPlazo ;	
	$query = $query . " ) ";	
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgDivisionRec.php?cualProyecto=$cualProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>Programaci&oacute;n de Asignaci&oacute;n de recursos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="ts_picker.js"></script>
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
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos -Informaci&oacute;n base de la asignaci&oacute;n de recursos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('fechaI','','R','lPlazo','','RisNum','lvalor','','RisNum','fechaI','','R');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Fecha Inicio </td>
    <td class="TxtTabla">	<!--Aqui va la fecha seleccionable-->
	
		<input name="fechaI" class="CajaTexto" id="fechaI" value="<? echo $fechaI;?>" size="25"  readonly>
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.Form1.fechaI);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
</td>
  </tr>
  <tr>
    <td class="TituloTabla">Plazo </td>
    <td class="TxtTabla"><input name="lPlazo" type="text" class="CajaTexto" id="lPlazo" size="10"> 
      meses 
        <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>"></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
