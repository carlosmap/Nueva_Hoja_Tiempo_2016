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
$sql="select * from actividades " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$sql=$sql." and id_actividad = " . $cualActividad ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) { 
	$pActividad=ucwords(strtolower($reg[nombre]));
	$pFechaI= $reg[fecha_inicio] ;
	$pFechaF=date("M d Y ", strtotime($reg[fecha_fin])); 

}

//Trae la información del costo directo 
//dbo.ActividadesCostosD
//id_proyecto, id_actividad, secuencia, item, valorItem, unidad
$sql2="select * from ActividadesCostosD ";
$sql2=$sql2." where id_proyecto = " . $cualProyecto ;
$sql2=$sql2." and id_actividad =" . $cualActividad ;
$sql2=$sql2." and secuencia =" . $cualSecCD ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) { 
	$pitem=$reg2[item] ;
	$pvalorItem = $reg2[valorItem] ;
}

//Si se presionó el botón Grabar
if ($miProyecto != "") {
	//Realiza la inserción de la persona a la tabla dbo.ActividadesCostosD
	//id_proyecto, id_actividad, secuencia, item, valorItem, unidad
	$query = "DELETE FROM ActividadesCostosD ";
	$query = $query . " WHERE id_proyecto = " . $miProyecto ;
	$query = $query . " AND id_actividad = " . $miActividad ;
	$query = $query . " AND secuencia = " . $miItem ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$miProyecto&cualActividad=$miActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	

}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos por Divisi&oacute;n</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
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
        if (isNaN(val)) errors+='- '+nm+' debe ser numérico.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
  } if (errors) alert('Validación:\n'+errors);
  document.MM_returnValue = (errors == '');
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
    <td class="TituloUsuario">Programaci&oacute;n de proyectos  - Costos directos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lCosto','','R','lValor','','RisNum');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Actividad</td>
    <td class="TxtTabla">
	<?
	echo $pActividad;
	?>
    <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>">	<input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualActividad; ?>">
    <input name="miItem" type="hidden" id="miItem" value="<? echo $cualSecCD; ?>"></td>
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
    <td width="25%" class="TituloTabla">Costo directo </td>
    <td class="TxtTabla"><input name="lCosto" type="text" class="CajaTexto" id="lCosto" value="<? echo $pitem; ?>" size="70" disabled >
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Valor</td>
    <td class="TxtTabla"><input name="lValor" type="text" class="CajaTexto" id="lValor" value="<? echo $pvalorItem; ?>" disabled ></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar este costo directo? </strong></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">          <input name="Submit" type="submit" class="Boton" value="Borrar"></td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
