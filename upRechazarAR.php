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

//Trae la información del encabezado de la Programación de la asignación de recursos para identificar el proyecto 
//y el nombre del programado.
$sqlAR="SELECT S.*, P.nombre, U.nombre nomPrograma, U.apellidos apePrograma ";
$sqlAR=$sqlAR." FROM ProgAsignaRecursos S, Proyectos P, Usuarios U ";
$sqlAR=$sqlAR." WHERE S.id_proyecto = P.id_proyecto ";
$sqlAR=$sqlAR." AND S.unidadProgramador = U.unidad ";
$sqlAR=$sqlAR." AND S.id_proyecto = " . $cualProyecto ;
$sqlAR=$sqlAR." AND S.unidadProgramador =" . $cualUnidad ;
$cursorAR = mssql_query($sqlAR);
if ($regAR=mssql_fetch_array($cursorAR)) {	 
	$pnombre = ucwords(strtolower($regAR[nombre]));
	$pProgramador = ucwords(strtolower($regAR[nomPrograma])) . " " . ucwords(strtolower($regAR[apePrograma]));
}

//Si se presionó el botón Grabar
if ($Justificacion != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Realiza la actualización en de libera = 0 en la asignación de recursos del proyecto y programador seleccionado
	$sqlUp="UPDATE ProgAsignaRecursos SET liberar = '0' ";
	$sqlUp=$sqlUp." WHERE id_proyecto = " . $miProyecto ;
	$sqlUp=$sqlUp." AND unidadProgramador = " . $miUnidadProg ;
	$cursorUp = mssql_query($sqlUp);
	
	//Realiza la inserción de la bitácora para la Asignación de recursos
	$sqlAd="INSERT INTO BitacoraAsignaRecursos (id_proyecto, unidadProgramador, ";
	$sqlAd=$sqlAd." unidadProyecto, comentaProyecto) ";
	$sqlAd=$sqlAd." VALUES (" . $miProyecto . ", ";
	$sqlAd=$sqlAd. $miUnidadProg . ", ";
	$sqlAd=$sqlAd. $laUnidad . ", ";
	$sqlAd=$sqlAd."'". $Justificacion ."' ";
	$sqlAd=$sqlAd." ) ";
	$cursor = mssql_query($sqlAd);

	//Si los cursores no presentaron problema
	if  ((trim($cursorUp) != "") AND (trim($cursor) != "") ) {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosGeneralAR.php?cualProyecto=$miProyecto&cualUnidad=$miUnidadProg','winProgProyectos','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Asignaci&oacute;n de recursos</title>
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
    <td class="TituloUsuario">Programaci&oacute;n de proyectos - Asignaci&oacute;n de recursos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('Justificacion','','R');return document.MM_returnValue" >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td class="TituloTabla">Proyecto</td>
    <td class="TxtTabla">
      <? echo $pnombre ;	  ?>
	  <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
    </td>
  </tr>
  <tr>
    <td class="TituloTabla">Programador</td>
    <td class="TxtTabla">
	<? echo $pProgramador ; ?>
	<input name="miUnidadProg" type="hidden" id="miUnidadProg" value="<? echo $cualUnidad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">ATENCION</td>
    <td class="TxtTabla">Esta operaci&oacute;n es irreversible. Si presiona el bot&oacute;n Grabar, se generar&aacute; un registro (no editable) en la bit&aacute;cora con su justificaci&oacute;n, la programaci&oacute;n aparecer&aacute; disponible para edici&oacute;n por parte del programador y usted no podr&aacute; realizar ninguna actividad sobre esta informaci&oacute;n hasta que de nuevo sea liberada. </td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Justificaci&oacute;n      </td>
    <td class="TxtTabla"><textarea name="Justificacion" cols="70" class="CajaTexto" id="Justificacion"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">      <input name="Submit" type="submit" class="Boton" value="Grabar"></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
