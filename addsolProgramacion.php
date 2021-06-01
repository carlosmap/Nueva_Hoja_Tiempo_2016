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

//trae los datosa de la licta clase de tiempo
$sql3="select * from clase_tiempo " ;
$cursor3 = mssql_query($sql3);


//Si se presionó el botón Grabar
if ($Horas != "") {
	//	SolicitudHoras
	//	secuencia, unidad, fechaSolicitud, id_proyecto, mes, vigencia, clase_tiempo, 
	//	localizacion, id_actividad, comentario, cantidadHoras, validaDirector, unidadDirector, 
	//	comentaDirector, requiereFirma, validaJefe, unidadJefe, comentaJefe

	//Valida que no ingrese 0 en horas solicitadas
	if ($Horas == 0) {
			echo ("<script>alert('No puede solicitar 0 horas al periodo seleccionado. Por favor corrija la información.');</script>");
	}
	else {
		//Direcciona a la BD a donde va a grabar
		@mssql_select_db("HojaDeTiempo");
		
		
		//Realiza la inserción de la persona a la tabla asignaciones
		$query = "INSERT INTO SolicitudHoras(unidad, fechaSolicitud, id_proyecto, mes, vigencia, clase_tiempo,   " ;
		$query = $query . " localizacion, comentario, cantidadHoras) ";
		$query = $query . " VALUES( " . $laUnidad . ", " ;
		$query = $query . " '" . gmdate ("n/d/y") . "', ";	
		$query = $query . $pProyecto . ", ";	
		$query = $query . $elMes . ",  ";	
		$query = $query . $elAno . ", ";	
		$query = $query . $pClase . ", ";	
		$query = $query . $pLocaliza. ", " ;	
		$query = $query . " '". $Comentarios. "', " ;	
		$query = $query . $Horas . " ";	
		$query = $query . " ) ";	
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		echo ("<script>window.close();MM_openBrWindow('solProgramacion.php?pMes=$elMes&pAno=$elAno','winHojaTiempoSHoras','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
	}
}


?>
<html>
<head>
<title>Solicitud de programaci&oacute;n</title>
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
          if (num<min || max<num) errors+='- '+nm+' debe ser un número entre '+min+' y '+max+'.\n';
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
    <td class="TituloUsuario">Solicitud de Programación </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('Horas','','RinRange1:222');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td class="TituloTabla">Mes</td>
    <td class="TxtTabla"><? echo $cualMes; ?></td>
  </tr>
  <tr>
    <td class="TituloTabla">A&ntilde;o</td>
    <td class="TxtTabla"><? echo $cualAno; ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Proyecto</td>
    <td class="TxtTabla">
	<select name="pProyecto" class="CajaTexto" id="pProyecto" >
		<?
		@mssql_select_db("HojaDeTiempo",$conexion);
//		$sql2="Select * from Proyectos order by nombre " ;
		$sql2="Select * from Proyectos  ";
		$sql2=$sql2 . " where id_estado = 2 ";
		$sql2=$sql2 . " and (codigo <> 'ACC' and codigo <> 'AUS' and codigo <> 'ENF' and codigo <> 'LIC'  ";
		$sql2=$sql2 . " and codigo <> 'PER' and codigo <> 'SAN' and codigo <> 'VAC')   ";
		$sql2=$sql2 . " order by nombre ";
		
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		if ($pProyecto == $reg2[id_proyecto]) {
			$selProy = "Selected";
			}
		else {
			$selProy = "";
		};
		
		?>
          <option value="<? echo $reg2[id_proyecto]; ?>" <? echo $selProy; ?> ><? echo ucwords(strtolower($reg2[nombre])); ?></option>
		 <? } ?> 
        </select>
	<input name="elMes" type="hidden" id="elMes" value="<? echo $cualMes; ?>">
	<input name="elAno" type="hidden" id="elAno" value="<? echo $cualAno; ?>">
    </td>
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
    <td class="TituloTabla">Horas solicitadas </td>
    <td class="TxtTabla"><input name="Horas" type="text" class="CajaTexto" id="Horas" size="20"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Comentarios</td>
    <td class="TxtTabla"><textarea name="Comentarios" cols="50" class="CajaTexto" id="Comentarios"></textarea></td>
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
