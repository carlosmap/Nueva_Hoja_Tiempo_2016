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
if ($Comentario != "") {
	
	if 	($pLibera == 1) {
		//Realiza la actualización del campo Liberar en la tabla ProgSumaGlobal 
		$query = "UPDATE ProgAsignaRecursos SET liberar = " . $pLibera ;
		$query = $query . " WHERE id_proyecto = " . $miProyecto ;
		$query = $query . " AND unidadProgramador = " . $laUnidad ;
		$cursor = mssql_query($query);
	
		//BitacoraAsignaRecursos
		//secuencia, fecha, id_proyecto, unidadProgramador, comentaProgramador, unidadProyecto, comentaProyecto
		$query2= "INSERT INTO BitacoraAsignaRecursos (id_proyecto, unidadProgramador, comentaProgramador) " ;
		$query2 = $query2 . " VALUES( " . $miProyecto . ", " ;
		$query2 = $query2 . $laUnidad . ", ";
		$query2 = $query2 . " '" . $Comentario . "' ";
		$query2 = $query2 . " ) ";
		$cursor2 = mssql_query($query2);
		
		//Si los cursores no presentaron problema
		if  ((trim($cursor) != "") AND (trim($cursor2) != "")) {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		
	}
	else {
		//Realiza la actualización del campo Liberar en la tabla ProgSumaGlobal 
		$query = "UPDATE ProgSumaGlobal SET liberar = " . $pLibera ;
		$query = $query . " WHERE id_proyecto = " . $miProyecto ;
		$query = $query . " AND unidadProgramador = " . $laUnidad ;
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
	}
	echo ("<script>window.close();MM_openBrWindow('ProgDivisionRec.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	

}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos por Divisi&oacute;n</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="ts_picker.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
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
    <td class="TituloUsuario">Programación de Proyectos -Informaci&oacute;n de la programaci&oacute;n </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('Comentario','','R');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td colspan="2" class="TituloTabla"><img src="img/images/Pixel.gif" width="4" height="4"></td>
    </tr>
  <tr>
    <td class="TituloTabla">Comentario</td>
    <td class="TxtTabla"><textarea name="Comentario" cols="50" class="CajaTexto" id="Comentario">Programación finalizada.</textarea>
      <input name="miProyecto" type="hidden" id="miProyecto" value="<? echo $cualProyecto; ?>"></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Liberar la programaci&oacute;n </td>
    <td class="TxtTabla"><input name="pLibera" type="radio" value="1">
      Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      <input name="pLibera" type="radio" value="0" checked>
        No 	<!--Aqui va la fecha seleccionable-->
</td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
        </tr>
      </table>
      <span class="TxtTabla">      </span>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
