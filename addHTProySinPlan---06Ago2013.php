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

//--Trae el listado de proyectos que no tienen planeación
$sql01="SELECT *  ";
$sql01=$sql01." FROM Proyectos ";
$sql01=$sql01." WHERE id_estado = 2 ";
$sql01=$sql01." AND id_proyecto not in ( ";
$sql01=$sql01." 	SELECT DISTINCT id_proyecto ";
$sql01=$sql01." 	FROM PlaneacionProyectos  ";
$sql01=$sql01." 	WHERE unidad = " . $laUnidad ;
$sql01=$sql01." 	AND vigencia = " . $cualVigencia;
$sql01=$sql01." 	AND mes = "  . $cualMes;
$sql01=$sql01." ) ";
$sql01=$sql01." ORDER BY nombre ";
$cursor01 =	 mssql_query($sql01);




//Si se presionó el botón Grabar
if ($lNombre != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Encuentra la siguiente secuencia para la actividad en el proyecto
	$sql="Select Max(IDSitio) as MaximoSitio from sitiosTrabajo where id_proyecto =" . $miProyecto ;
	
	$cursor = mssql_query($sql);
	if ($reg=mssql_fetch_array($cursor)) {
		$pIdST = $reg[MaximoSitio] + 1;
		}
	else {
		$pIdST = 1;
	}
			
	//Realiza la inserción de la actividad en la tabla Actividades
	$query = "INSERT INTO sitiosTrabajo(id_proyecto, IDsitio, NomSitio )  " ;
	$query = $query . " VALUES( " . $miProyecto . ", " ;
	$query = $query . $pIdST . ", ";
	$query = $query . " '" . $lNombre . "' ";
	$query = $query . " ) ";
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectoConfig.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
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
    <td class="TituloUsuario">.: PROYECTOS SIN FACTURACI&Oacute;N </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td rowspan="2">Proyecto</td>
          <td colspan="2">&iquest;Seleccionar?</td>
        </tr>
        <tr class="TituloTabla2">
          <td width="5%">Si</td>
          <td width="5%">No</td>
        </tr>
		<?
		$r=1;
		while ($reg01 = mssql_fetch_array($cursor01)) {
		
			//Verifica si el proyecto ya está en los proyectos sin planeación 
			$hayProy=0;
			$sql02="SELECT COUNT(*) existeProy ";
			$sql02=$sql02." FROM ProyectosSinPlaneacion  ";
			$sql02=$sql02." WHERE id_proyecto = " . $reg01['id_proyecto'];
			$sql02=$sql02." AND unidad = " . $laUnidad ;
			$sql02=$sql02." AND mes = " . $cualMes;
			$sql02=$sql02." AND vigencia = " . $cualVigencia;
			$cursor02 = mssql_query($sql02);
			if ($reg02=mssql_fetch_array($cursor02)) {
				$hayProy = $reg02['existeProy'] ;
			}
			
			if ($hayProy > 0) {
				$chkSI="checked";
				$chkNO="";
			}
			else {
				$chkSI="";
				$chkNO="checked";
			}

		?>			
        <tr class="TxtTabla">
          <td><? echo "[" . $reg01['codigo'] . "." . $reg01['cargo_defecto'] . "] " . $reg01['nombre'];  ?></td>
          <td width="5%" align="center"><? echo $hayProy; ?><input name="btnSelecciona<? echo $r; ?>" type="radio" value="1" <? echo $chkSI; ?> ></td>
          <td width="5%" align="center"><input name="btnSelecciona<? echo $r; ?>" type="radio" value="0" <? echo $chkNO; ?> ></td>
        </tr>
		<? 
			$r=$r+1;
		} //Cierra while 01 ?>
      </table>
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lNombre','','R');return document.MM_returnValue" >
  <tr>
    <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
  </tr>
  </form>
</table>  	</td>
  </tr>
</table>

</body>
</html>
