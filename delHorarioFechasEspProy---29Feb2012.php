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

//11May2011
//Trae el horario seleccionado
/*
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.localiza, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo  " ;
$sql3=$sql3." FROM Horarios h " ;
$sql3=$sql3." Where h.IDhorario =" . $cualHorario ;*/
$sql3="SELECT A.IDhorario, A.id_proyecto, A.HorarioDefecto, A.ubicacion, B.IDhorario, B.NomHorario,  ";
$sql3=$sql3." B.Lunes, B.Martes, B.Miercoles, B.Jueves, B.Viernes, B.Sabado, B.Domingo, B.localiza ";
$sql3=$sql3." FROM HorariosProy A, Horarios B ";
$sql3=$sql3." WHERE A.IDhorario = B.IDhorario ";
$sql3=$sql3." AND A.id_proyecto = " . $cualProy ;
$sql3=$sql3." and A.IDhorario = ". $cualHorario ;
$cursor3 = mssql_query($sql3);
if ($Reg3=mssql_fetch_array($cursor3)) {
	$pIDhorario = $Reg3[IDhorario];
	$pNomHorario= $Reg3[NomHorario];

	//Si el proyecto tiene localizacion asume la ubicación asignada al proyecto
	//si no asume la del horario como tal.
	if (trim($Reg3[ubicacion]) != "" ) {
		$reportaLocaliza = "P" ;
		$pLocaliza = $Reg3[ubicacion];
	}
	else {
		$reportaLocaliza = "H" ;
		$pLocaliza = $Reg3[localiza];
	}

	$pLunes= $Reg3[Lunes];
	$pMartes= $Reg3[Martes];
	$pMiercoles= $Reg3[Miercoles];
	$pJueves= $Reg3[Jueves];
	$pViernes= $Reg3[Viernes];
	$pSabado= $Reg3[Sabado];
	$pDomingo= $Reg3[Domingo];
	$pTotal = $Reg3[Lunes] + $Reg3[Martes] + $Reg3[Miercoles] + $Reg3[Jueves] + $Reg3[Viernes] + $Reg3[Sabado] + $Reg3[Domingo];
}

//18Mar2008
//Trae el festivo
$sql3a="select * from FechasEspecialesProy ";
$sql3a=$sql3a." where IDhorario =" . $cualHorario ;
$sql3a=$sql3a." and id_proyecto =" . $cualProy ;
$sql3a=$sql3a." and Fecha = '" . $cualFecha  . "' ";
$cursor3a = mssql_query($sql3a);
if ($Reg3a=mssql_fetch_array($cursor3a)) {
	$pIDhorario = $Reg3a[IDhorario];
	$pFecha = date("n/d/Y", strtotime($Reg3a[Fecha]));
	$pCuantasHoras = $Reg3a[CuantasHoras];
}
//echo $sql3a;

//Si se presionó el botón Grabar
if ($miHorario != "") {
	//Realiza la eliminación de la fecha en la tabla FechasEspeciales
	//IDhorario, Fecha, CuantasHoras
	$query = "DELETE FROM FechasEspecialesProy " ;
	$query = $query. " WHERE IDhorario = " . $miHorario ;
	$query = $query. " AND id_proyecto = " . $miProy ;
	$query = $query." AND Fecha = '" . $lFecha . "' ";
	$cursor = mssql_query($query);
	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Eliminación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
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

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
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
            <td width="14%">Localizaci&oacute;n</td>
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
            <td width="14%">
			<? 
		$pLocalizaTxt="";
		if ($pLocaliza==1) {
			$pLocalizaTxt=$pLocaliza.". Oficina";
		}
		if ($pLocaliza==2) {
			$pLocalizaTxt=$pLocaliza.". Campo";
		}
		if ($pLocaliza==3) {
			$pLocalizaTxt=$pLocaliza.". Planilla";
		}
		
		if (trim($reportaLocaliza)=="P") {
			echo "Proyecto <br>" . $pLocalizaTxt;
		}
		else {
			echo "Horario <br>" . $pLocalizaTxt;
		}
		?>
			</td>
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
            <td width="25%" class="TituloTabla">Fecha </td>
            <td class="TxtTabla">
			<input name="lFecha" class="CajaTexto" id="lFecha"  value="<? echo $pFecha; ?>" size="25"  readonly >
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe> 
		(mm/dd/AAAA) 
			</td>
          </tr>
          <tr>
            <td width="25%" class="TituloTabla">Cantidad Horas </td>
            <td class="TxtTabla"><input name="lCantidad" type="text" class="CajaTexto" id="lCantidad" value="<? echo $pCuantasHoras; ?>" size="10" readonly=""></td>
          </tr>
        </table>
		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar esta fecha especial del horario?<br>ATENCIÓN: Esta operación puede generar inconsistencia de información para el procedimiento de validación de tiempos ya generados.
            </strong></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="miHorario" type="hidden" id="miHorario" value="<? echo $pIDhorario; ?>">
      <input name="miProy" type="hidden" id="miProy" value="<? echo $cualProy;?>">
      <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
      <input name="Submit" type="submit" class="Boton" value="Borrar" ></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
