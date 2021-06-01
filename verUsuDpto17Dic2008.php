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

//10Abr2008
//Trae el listado de usuarios asociados a un Departamento
$sql="select * from usuarios  ";
$sql=$sql." where id_departamento = " . $cualDpto ;
$sql=$sql." order by apellidos"  ;
$cursor = mssql_query($sql);


?>


<html>
<head>
<title>Estructura Organizacional Ingetec</title>
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

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">

 
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Personal asociado al  Departamento</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0"><tr>
  <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr class="TituloTabla2">
      <td width="10%">Unidad</td>
      <td>Nombre</td>
      <td width="10%">Tipo contrato </td>
    </tr>
	<? while ($reg=mssql_fetch_array($cursor)) {  ?>
    <tr class="TxtTabla">
      <td width="10%"><? echo  $reg[unidad] ; ?></td>
      <td><? echo  ucwords(strtolower($reg[apellidos])) . ", " . ucwords(strtolower($reg[nombre])) ; ?></td>
      <td width="10%"><? echo  strtoupper($reg[TipoContrato]) ; ?></td>
    </tr>
	<? } ?>
  </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><input name="Submit" type="submit" class="Boton" onClick="MM_callJS('window.close()')" value="Cerrar"></td>
      </tr>
    </table></td>
</tr>
</table>


</body>
</html>