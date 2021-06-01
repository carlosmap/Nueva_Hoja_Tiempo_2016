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
if ($miVigencia != "") {
	$s = 1;
	$CantidadItem = 12;
//	echo "Elementos>>>>>>" ;
	while ($s <= $CantidadItem) {
		$elMes = "miMes" . $s;
		$elOficina = "miOfi" . $s;
		$elCampo = "miCampo" . $s;
		$elDias = "miDias" . $s;
		
		//Realiza la grabación en dbo.
		//id_proyecto, vigencia, mes, hOficina, hCampo, diasLaborales, fechaCrea, unidadCrea
		$query2 = "INSERT INTO horasydiasLaboralesProy(id_proyecto, vigencia, mes, hOficina, hCampo, diasLaborales, fechaCrea, unidadCrea) ";
		$query2 = $query2 . " VALUES (" . $miProyecto . ", ";
		$query2 = $query2 . $miVigencia . ", " ;	
		$query2 = $query2 . ${$elMes} . ", " ;	
		
		if (trim(${$elOficina}) == "") {
			$query2 = $query2 .  "0 , ";
		}
		else {
			$query2 = $query2 . ${$elOficina} . " , ";
		}
		if (trim(${$elCampo}) == "") {
			$query2 = $query2 .  "0 , ";
		}
		else {
			$query2 = $query2 . ${$elCampo} . " , ";
		}
		if (trim(${$elDias}) == "") {
			$query2 = $query2 .  "0 , ";
		}
		else {
			$query2 = $query2 . ${$elDias} . ",  ";
		}
		$query2 = $query2 . " '".gmdate ("n/d/y")."', " ;
		if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
			$query2=$query2. $laUnidad;
		}
		else {
			$query2=$query2. $_SESSION["sesUnidadUsuario"];
		}
		$query2 = $query2 . " ) ";
		$cursor2 = mssql_query($query2) ;
//		echo $query2; 
		$s = $s + 1;
	}
	

	//Si los cursores no presentaron problema
	if  (trim($cursor2) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosHorarioslocaliza.php?cualProyecto=$miProyecto&pAno=$miVigencia','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>D&iacute;as y Horas laborales del proyecto</title>
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
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">D&iacute;as y horas laborales del proyecto </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lPorcentaje','','RinRange1:100','lDescripcion','','R');return document.MM_returnValue" >
	    <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="20%">Vigencia</td>
        <td width="20%">Mes</td>
        <td width="20%">Oficina</td>
        <td width="20%">Campo</td>
        <td width="20%">D&iacute;as laborales </td>
        </tr>
	  <?
		for ($m=1; $m<=12; $m++) {
	  ?>
      <tr class="TxtTabla">
        <td width="20%"><? echo  $cualVigencia ; ?></td>
        <td width="20%">
		<? 
		if ($m == 1) {
			echo "Enero"; 		
		}
		if ($m == 2) {
			echo "Febrero"; 		
		}
		if ($m == 3) {
			echo "Marzo"; 		
		}
		if ($m == 4) {
			echo "Abril"; 		
		}
		if ($m == 5) {
			echo "Mayo"; 		
		}
		if ($m == 6) {
			echo "Junio"; 		
		}
		if ($m == 7) {
			echo "Julio"; 		
		}
		if ($m == 8) {
			echo "Agosto"; 		
		}
		if ($m == 9) {
			echo "Septiembre"; 		
		}
		if ($m == 10) {
			echo "Octubre"; 		
		}
		if ($m == 11) {
			echo "Noviembre"; 		
		}
		if ($m == 12) {
			echo "Diciembre"; 		
		}

		
		?>
		<input name="miMes<? echo $m; ?>" type="hidden" id="miMes<? echo $m; ?>" value="<? echo $m; ?>"></td>
        <td width="20%" align="center"><input name="miOfi<? echo $m; ?>" type="text" class="CajaTexto" id="miOfi<? echo $m; ?>" value="0" size="12" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
        <td width="20%" align="center"><input name="miCampo<? echo $m; ?>" type="text" class="CajaTexto" id="miCampo<? echo $m; ?>" value="0" size="12" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
        <td width="20%" align="center"><input name="miDias<? echo $m; ?>" type="text" class="CajaTexto" id="miDias<? echo $m; ?>" value="0" size="12" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
        </tr>
	  <? } ?>
    </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><span class="TituloTabla">
      <input name="miVigencia" type="hidden" id="miVigencia" value="<? echo $cualVigencia; ?>">
      <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
      </span>
      <input name="Submit" type="submit" class="Boton" value="Grabar" ></td></tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
