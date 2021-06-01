<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//Actualiza un usuario de la lista de usuarios de INGETEC S.A.
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//17Abr2008
//Trae la información de las actividades con facturación para el día seleccionado
$sql="select h.*, p.nombre, a.nombre nomActividad, c.descripcion  ";
$sql=$sql." from horas h, proyectos p, actividades a, clase_tiempo c ";
$sql=$sql." where h.id_proyecto = p.id_proyecto ";
$sql=$sql." and h.id_proyecto = a.id_proyecto ";
$sql=$sql." and h.id_actividad = a.id_actividad ";
$sql=$sql." and h.clase_tiempo = c.clase_tiempo ";
$sql=$sql." and h.id_proyecto = " . $cualProy ;
$sql=$sql." and h.id_actividad = " . $cualAct ;
$sql=$sql." and h.unidad = " . $laUnidad;
$sql=$sql." and h.fecha = '".$cualFecha."'";
$sql=$sql." and h.clase_tiempo = " . $cualClase ;
$sql=$sql." and h.cargo = " . $cualCargo ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) { 
	$pid_proyecto = $reg[id_proyecto] ;
	$pid_actividad =  $reg[id_actividad] ;
	$punidad =  $reg[unidad] ;
	$pfecha =  $reg[fecha] ;
	$plocalizacion = $reg[localizacion] ;
	$pcargo =  $reg[cargo] ;
	$pclase_tiempo =  $reg[clase_tiempo] ;
	$phoras_registradas =  $reg[horas_registradas] ;
	$presumen_trabajo = $reg[resumen_trabajo] ;
	$pnombre =  $reg[nombre] ;
	$pnomActividad =  $reg[nomActividad] ;
	$pdescripcion =  $reg[descripcion] ;
}

if($miResumen != "") {
	//Realiza las validaciones de rigor
	$laVigencia = date("Y", strtotime($miFecha)); 
	$elMes = date("n", strtotime($miFecha)); 

if ($_SESSION["sesUsuarioQUIMBO"] != 'SI') {
	//Valida que la hoja no esté firmada por el jefe
	$vSql="select coalesce(count(*), 0) hayFirma  ";
	$vSql=$vSql." from AutorizacionesHT ";
	$vSql=$vSql." where unidad = " . $miUnidad ;
	$vSql=$vSql." and vigencia = " . $laVigencia ;
	$vSql=$vSql." and mes = " . $elMes;
	$vSql=$vSql." and validaJefe = 1 ";
	$vCursor = mssql_query($vSql);
	if ($vReg=mssql_fetch_array($vCursor)) { 
		$phayFirma = $vReg[hayFirma] ;
		if ($phayFirma > 0) {
			echo "<script>alert('La hoja de tiempo ya tiene VoBo del jefe. ')</script>";
			echo "<script>window.close()</script>";
			exit();
		}
	}
}

	//Actualiza el resumen de trabajo de la HT
	$upSql="UPDATE Horas SET ";
	$upSql=$upSql." resumen_trabajo='" . $miResumen . "' ";
	$upSql=$upSql." WHERE  id_proyecto=" . $miProyecto ;
	$upSql=$upSql." AND id_actividad=" . $miActividad ;
	$upSql=$upSql." AND unidad=" . $miUnidad ;
	$upSql=$upSql." AND fecha='" . date("Y-n-d", strtotime($miFecha))  . "' ";
	$upSql=$upSql." AND localizacion=" . $miLocaliza;
	$upSql=$upSql." AND cargo='" . $miCargo ."' ";
	$upSql=$upSql." AND clase_tiempo=" . $miClase;
	$cursorUp = mssql_query($upSql) ;

	if (trim($cursorUp) != "")  {
		echo ("<script>alert('La Grabación se realizó con éxito. ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('editarResumen.php?timestamp=".date("m/d/Y", strtotime($miFecha))."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}

?>


<html>
<head>
<title>Hoja de tiempo</title>
    <LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" src="ts_picker.js"></script>
    <script language="JavaScript" type="text/JavaScript">
<!--
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' debe numérico.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
    } if (errors) alert('Validación:\n'+errors);
    document.MM_returnValue = (errors == '');
} }

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">

  <form action="" method="post" name = "addUsr" onSubmit="MM_validateForm('miResumen','','R');return document.MM_returnValue">
  
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Registro de facturaci&oacute;n para el d&iacute;a seleccionado </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="32%" class="TituloTabla">Proyecto</td>
    <td width="68%" class="TxtTabla">
	<? echo $pnombre ; ?>
    <input name="miProyecto" type="hidden" id="miProyecto" value="<? echo $pid_proyecto; ?>">	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Actividad</td>
    <td class="TxtTabla"><? echo $pnomActividad; ?><input name="miActividad" type="hidden" id="miActividad" value="<? echo $pid_actividad; ?>">
      <input name="miUnidad" type="hidden" id="miUnidad" value="<? echo $punidad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha</td>
    <td class="TxtTabla"><? echo $pfecha; ?>
      <input name="miFecha" type="hidden" id="miFecha" value="<? echo $pfecha; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Localizaci&oacute;n</td>
    <td class="TxtTabla"><? echo $plocalizacion; ?><input name="miLocaliza" type="hidden" id="miLocaliza" value="<? echo $plocalizacion; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Clase de tiempo </td>
    <td class="TxtTabla"><? echo $pdescripcion; ?>
      <input name="miClase" type="hidden" id="miClase" value="<? echo $pclase_tiempo; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Horas registradas </td>
    <td class="TxtTabla"><? echo $phoras_registradas; ?></td>
  </tr>
  <tr>
    <td class="TituloTabla">Cargo</td>
    <td class="TxtTabla"><? echo $pcargo ; ?>
      <input name="miCargo" type="hidden" id="miCargo" value="<? echo $pcargo ; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Resumen de trabajo </td>
    <td class="TxtTabla"><textarea name="miResumen" cols="50" rows="4" class="CajaTexto" id="miResumen"><? echo $presumen_trabajo; ?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" class="TxtTabla"><strong>NOTA: Para modificar la cantidad de horas facturadas en una fecha es necesario eliminar el registro y volver a registrarlo. </strong></td>
    </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td align="right" class="TxtTabla"><label>
      <input  type="submit" class="Boton" id="Grabar" value="Grabar" >
    </label></td>
  </tr>
</table></td>
  </tr>
</table>
</form>

</body>
</html>