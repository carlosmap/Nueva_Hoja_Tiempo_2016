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

//Trae la información del usuario seleccionado
//unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, email, 
//ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, Seccion, 
//NombreCorto, unidadJefe
$sql="select * from usuarios ";
$sql=$sql." where unidad =" . $cualUnidad ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$punidad = $reg[unidad];
	$pnombre = $reg[nombre];
	$papellidos = $reg[apellidos];
	$pid_departamento = $reg[id_departamento];
	$pid_categoria = $reg[id_categoria];
}

//9Abr2008
//Trae la información de los salarios del usuario seleccionado
$sql2="SELECT unidad, fecha, salario FROM UsuariosSalario  ";
$sql2=$sql2." where unidad =" . $cualUnidad ;
$sql2=$sql2." and fecha = '" .$cualFecha. "' "  ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {
	$pfecha = $reg2[fecha];
	$psalario =  number_format($reg2[salario], 0, ",", "") ; 
}


if($Unidad != "") {
	//Realiza la inserción en la tabla UsuariosSalarios
	$insertaUsr = "UPDATE UsuariosSalario SET salario =" . $ElSalario ;
	$insertaUsr = $insertaUsr. "  WHERE unidad=".$Unidad;
	$insertaUsr = $insertaUsr. "  AND fecha='".$miFecha . "' ";
	$cursorIns = mssql_query($insertaUsr) ;
	if (trim($cursorIns) != "")     {
		echo ("<script>alert('La Operación se realizó con éxito. ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('verSalarioUsuario.php?cualUnidad=$Unidad','winSalarioUsu','toolbar=yes,scrollbars=yes,resizable=yes,width=500,height=400');</script>");	
}

?>


<html>
<head>
<title>Salario Usuario</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="ts_picker.js">
<script language="JavaScript">
window.name="winSalarioUsu";
</script>

	
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
    <td class="TituloUsuario"> Usuarios INGETEC S.A.</td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0">
<form name="formulario" method="post" action="" onSubmit="MM_validateForm('LaFecha','','R','ElSalario','','RisNum','LaFecha','','R');return document.MM_returnValue">
  <tr>
    <td bgcolor="#FFFFFF" ><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="32%" class="TituloTabla">Unidad</td>
    <td width="68%" class="TxtTabla">
      <input name="Unidad" type="text" class="CajaTexto" id="Unidad" value="<? echo $punidad; ?>" readonly>
    </td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombres</td>
    <td class="TxtTabla"><label>
      <input name="Nombres" type="text" class="CajaTexto" id="Nombres" value="<? echo $pnombre; ?>" size="40" disabled>
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Apellidos</td>
    <td class="TxtTabla"><label>
      <input name="Apellidos" type="text" class="CajaTexto" id="Apellidos" value="<? echo $papellidos; ?>" size="40" disabled>
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><label>
      <select name="Categoria" class="CajaTexto" id="Categoria" disabled>
      <?php
	  	$sqlCat = "select * from categorias ";
		$catCursor = mssql_query($sqlCat);
		while ($reg = mssql_fetch_array($catCursor)){
		if ($pid_categoria == $reg[id_categoria] ) {
			$selCat = "selected";
		}
		else {
			$selCat = "";
		}
			echo "<option value =". $reg[id_categoria]. " $selCat >". $reg[nombre] ;
		}
	  ?>
      </select>
    </label></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="img/images/Pixel.gif" width="4" height="4"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
			
              <tr>
                <td width="32%" class="TituloTabla">Fecha </td>
                <td><input name="LaFecha" class="CajaTexto" id="LaFecha" value="<? echo date("M d Y ", strtotime($pfecha)); ?>" size="25"  readonly>
                  <input name="miFecha" type="hidden" id="miFecha" value="<? echo date("m/d/Y", strtotime($pfecha)); ?>">
				</td>
              </tr>
              <tr>
                <td width="32%" class="TituloTabla">Salario</td>
                <td><input name="ElSalario" type="text" class="CajaTexto" id="ElSalario" value="<? echo $psalario; ?>"></td>
              </tr>

            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="right"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  </form>
</table>

</body>
</html>