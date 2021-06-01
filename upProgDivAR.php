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

//4Mar2008
//Trae la información de la programación de ls asignación de recursos para el proyecto seleccionado y el usuario activo
//ProgAsignaRecursos
//id_proyecto, unidadProgramador, fechaInicio, plazo
$sql2="SELECT * FROM ProgAsignaRecursos ";
$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
$sql2=$sql2." and unidadProgramador =" . $laUnidad ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {	 
	$pfechaInicio = date("d M Y ", strtotime($reg2[fechaInicio])) ;
	$pplazo = $reg2[plazo];
	$laFecha = mktime(0,0,0, date("n", strtotime($reg2[fechaInicio])), date("d", strtotime($reg2[fechaInicio])), date("Y", strtotime($reg2[fechaInicio]))); //fecha actual en número de segundos	
}

//Si se presionó el botón Grabar
if ($lPlazo != "") {
	//calcula el plazo aproximado en días
	$plazoEnDias= ($lPlazo-1)* 30;

	//Calcula la nueva fecha a partir del plazo establecido
	$nuevaFecha = ($miFecha + 24*60*60*$plazoEnDias); 
	$nuevoMes = gmdate("n", $nuevaFecha) ;
	$nuevoAno = gmdate("Y", $nuevaFecha) ;

	//Verifica que no exista programación de asignación de recursos en una fecha (mes y vigencia) posterior a la duración indicada
	//si hay programación no permite modificar el plazo con un valor menor
	$existeProgramacion = 0;
	$sql9="select count(*) hayProg from ProgAsignaRecursosUsu ";
	$sql9=$sql9." where id_proyecto =". $cualProyecto ;
	$sql9=$sql9." and unidadProgramador =". $laUnidad ;
	$sql9=$sql9." and ((mes > ".$nuevoMes." and vigencia = ".$nuevoAno.") or (vigencia > ".$nuevoAno.")) ";
	$cursor9 = mssql_query($sql9);
	if ($reg9=mssql_fetch_array($cursor9)) {	 
		$existeProgramacion = $reg9[hayProg];
	}
	
	//Si no hay información posterior en programación actualiza los datos, de lo contrario muestra un mensaje y anula la operaicón
	if ($existeProgramacion == 0) {
		//Realiza la inserción de la información de la programación en ProgAsignaRecursos
		$query = "UPDATE  ProgAsignaRecursos SET plazo= " . $lPlazo ;	
		$query = $query . " where id_proyecto =" . $cualProyecto ;
		$query = $query . " and unidadProgramador =" . $laUnidad ;
	//	echo $query . "<br>";
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
	}
	else {
		echo ("<script>alert('No es posible asignar este plazo, hay programación de empleados posterior a esta duración. Por favor corríja la información.');</script>");
	}
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
-->
</style>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Informaci&oacute;n base de la asignaci&oacute;n de recursos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lPlazo','','RisNum','lvalor','','RisNum');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Fecha Inicio </td>
    <td class="TxtTabla"><? echo $pfechaInicio ;	?>
      <input name="miFecha" type="hidden" id="miFecha" value="<? echo $laFecha; ?>">
      <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Plazo </td>
    <td class="TxtTabla"><input name="lPlazo" type="text" class="CajaTexto" id="lPlazo" value="<? echo $pplazo; ?>" size="10"> 
      meses </td>
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
