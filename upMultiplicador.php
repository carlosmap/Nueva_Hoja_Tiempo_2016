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

//19Mar2008
//Trae el listado de Multiplicadores disponibles
$sql3="SELECT DISTINCT f.IDfraccion, f.Porcentaje, f.Descripcion  " ;
$sql3=$sql3." FROM FraccionesV f " ;
$sql3=$sql3." Where f.IDfraccion = " . $cualMult ;
$vCursor3 = mssql_query($sql3);
if ($vReg3=mssql_fetch_array($vCursor3)) {
	$pIDfraccion = $vReg3[IDfraccion] ;
	$pPorcentaje = $vReg3[Porcentaje] ;
	$pDescripcion = $vReg3[Descripcion] ;
}

//Si se presionó el botón Grabar
if ($lPorcentaje != "") {
	//Verifica que la descripción del multiplicador de viático no exista
	$vSql1="SELECT count(*) as Cuantos FROM FraccionesV  " ;
	$vSql1=$vSql1." WHERE Upper(Descripcion) = '".$lDescripcion."' " ;
	$vSql1=$vSql1." AND IDfraccion <> ".$miMultiplicador." " ;
	$vCursor1 = mssql_query($vSql1);
	if ($vReg1=mssql_fetch_array($vCursor1)) {
		if ($vReg1[Cuantos] > 0) {
			echo ("<script>alert('La descripción del multiplicador de viático ya existe.  ');</script>");
			echo ("<script>window.close()</script>");	
			exit;
		}
	}

	//Realiza la ACTUALIZACIÓN del multiplicador en la tabla FraccionesV
	//IDfraccion, Porcentaje, Descripcion
	$query = "UPDATE FraccionesV SET  " ;
	$query = $query." Porcentaje = " . $lPorcentaje . " , " ;
	$query = $query . " Descripcion = '". $lDescripcion . "'  ";
	$query = $query . " WHERE  IDfraccion = ".$miMultiplicador ;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosMV.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Multiplicadores de vi&aacute;tico</title>
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
    <td class="TituloUsuario">Multiplicadores de vi&aacute;tico </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lPorcentaje','','RinRange1:100','lDescripcion','','R');return document.MM_returnValue" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="25%" class="TituloTabla">Porcentaje</td>
            <td class="TxtTabla"><input name="lPorcentaje" type="text" class="CajaTexto" id="lPorcentaje" value="<? echo $pPorcentaje; ?>" size="10">              <span class="TituloTabla">
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
              <input name="miMultiplicador" type="hidden" id="miMultiplicador" value="<? echo $pIDfraccion; ?>">
            </span></td>
          </tr>
          <tr>
            <td width="25%" class="TituloTabla">Descripci&oacute;n</td>
            <td class="TxtTabla"><input name="lDescripcion" type="text" class="CajaTexto" id="lDescripcion" value="<? echo $pDescripcion; ?>" size="100"></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
