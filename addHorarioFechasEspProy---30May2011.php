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

//17Mar2008
//Trae el horario seleccionado
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.localiza, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo  " ;
$sql3=$sql3." FROM Horarios h " ;
$sql3=$sql3." Where h.IDhorario =" . $cualHorario ;
//$sql3=$sql3." and h.id_proyecto =" . $cualProy ;
$cursor3 = mssql_query($sql3);
if ($Reg3=mssql_fetch_array($cursor3)) {
	$pIDhorario = $Reg3[IDhorario];
	$pNomHorario= $Reg3[NomHorario];
	if ($Reg3[localiza]==1){$pLocaliza=$Reg3[localiza].". Oficina";}
	if ($Reg3[localiza]==2){$pLocaliza=$Reg3[localiza].". Campo";}
	if ($Reg3[localiza]==1){$pLocaliza=$Reg3[localiza].". Planilla";}
	$pLunes= $Reg3[Lunes];
	$pMartes= $Reg3[Martes];
	$pMiercoles= $Reg3[Miercoles];
	$pJueves= $Reg3[Jueves];
	$pViernes= $Reg3[Viernes];
	$pSabado= $Reg3[Sabado];
	$pDomingo= $Reg3[Domingo];
	$pTotal = $Reg3[Lunes] + $Reg3[Martes] + $Reg3[Miercoles] + $Reg3[Jueves] + $Reg3[Viernes] + $Reg3[Sabado] + $Reg3[Domingo];
}

//Si se presionó el botón Grabar
if ($miHorario != "") {
	//Verifica que la fecha no se encuentre registrada previamente
	$vSql1="select * from FechasEspecialesProy " ;
	$vSql1=$vSql1." where IDhorario = " . $miHorario ;
	$vSql1=$vSql1." and id_proyecto = '". $miProy ."' " ;
	$vSql1=$vSql1." and Fecha = '". $lFecha ."' " ;
	$vCursor1 = mssql_query($vSql1);
	if ($vReg1=mssql_fetch_array($vCursor1)) {
		echo ("<script>alert('La fecha ya tiene asignación de horas. Corresponde a: " . $vReg1[CuantasHoras] . " Para modificarla elimínela y vuelva a ingresarla');</script>");
		echo ("<script>window.close()</script>");	
	}
	else {
		//Realiza la inserción de la actividad en la tabla FechasEspeciales
		//IDhorario, Fecha, CuantasHoras
		$query = "INSERT INTO FechasEspecialesProy (IDhorario, id_proyecto, Fecha, CuantasHoras) " ;
		$query = $query. "VALUES (" . $miHorario. ", ";
		$query = $query." '" . $miProy . "' , ";
		$query = $query." '" . $lFecha . "' , ";
		$query = $query. $lCantidad . " ) ";
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
	}
	echo ("<script>window.close();MM_openBrWindow('fechasEspecialesProy.php?cualHorario=$miHorario&cualProy=$miProy','winFechas','toolbar=yes,scrollbars=yes,resizable=yes,width=600,height=400');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Horarios</title>
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
    <td class="TituloUsuario">Horarios - Fechas especiales </td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lFecha','','R','lCantidad','','RinRange0:20','lFecha','','R');return document.MM_returnValue" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="14%" class="TituloTabla">Nombre de Horario </td>
            <td colspan="8" class="TxtTabla">
			<? echo $pNomHorario; ?>
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
              <input name="recarga" type="hidden" id="recarga" value="1">
			</td>
          </tr>
          <tr class="TxtTabla">
            <td colspan="9"><img src="img/images/Pixel.gif" width="4" height="4"></td>
          </tr>
          <tr class="TituloTabla2">
            <td width="14%">Localiza</td>
			<td width="14%">Lunes</td>
            <td width="14%">Martes</td>
            <td width="14%">Mi&eacute;rcoles</td>
            <td width="14%">Jueves</td>
            <td width="14%">Viernes</td>
            <td width="14%">S&aacute;bado</td>
            <td width="14%">Domingo</td>
            <td width="14%">Total</td>
          </tr>
          <tr align="center" class="TxtTabla">
            <td width="14%"><? echo $pLocaliza; ?></td>
			<td width="14%"><? echo $pLunes; ?></td>
            <td width="14%"><? echo $pMartes; ?></td>
            <td width="14%"><? echo $pMartes; ?></td>
            <td width="14%"><? echo $pJueves; ?></td>
            <td width="14%"><? echo $pViernes; ?></td>
            <td width="14%"><? echo $pSabado; ?></td>
            <td width="14%"><? echo $pDomingo; ?></td>
            <td width="14%"><? echo $pTotal; ?></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="img/images/Pixel.gif" width="4" height="4"></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Fechas especiales </td>
  </tr>
</table>

		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="25%" class="TituloTabla">Fecha</td>
            <td class="TxtTabla">
			<input name="lFecha" class="CajaTexto" id="lFecha"  value="<? echo $lFecha; ?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.Form1.lFecha);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
			</td>
          </tr>
          <tr>
            <td width="25%" class="TituloTabla">Cantidad Horas </td>
            <td class="TxtTabla"><input name="lCantidad" type="text" class="CajaTexto" id="lCantidad" value="0" size="10"></td>
          </tr>
        </table>
		
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="miHorario" type="hidden" id="miHorario" value="<? echo $pIDhorario; ?>">
      <input name="miProy" type="hidden" id="miProy" value="<? echo $cualProy;?>">
    <input name="Submit" type="submit" class="Boton" value="Grabar" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
