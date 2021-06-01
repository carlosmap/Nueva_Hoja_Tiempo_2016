<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Seleccionar los usuarios registrados a través de la Hoja de tiempo
//@mssql_select_db("HojaDeTiempo",$conexion);
$sql2="select u.unidad, u.nombre, u.apellidos, u.email , u.id_departamento, d.nombre as departamento, d.id_division,   ";
$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ,  ";
$sql2= $sql2. " u.id_categoria, c.nombre as categoria ";
$sql2= $sql2. " from usuarios u, departamentos d, divisiones v, dependencias x , categorias c ";
$sql2= $sql2. " where u.id_departamento = d.id_departamento ";
$sql2= $sql2. " and d.id_division = v.id_division  ";
$sql2= $sql2. " and v.id_dependencia = x.id_dependencia ";
$sql2= $sql2. " and u.id_categoria = c.id_categoria ";
$sql2= $sql2. " and u.retirado IS NULL ";
if (($pDivision != "") AND ($pDivision != "0")) {
	$sql2= $sql2. " and d.id_division = " . $pDivision;
}
if (($pDepto != "") AND ($pDepto != "0")) {
	$sql2= $sql2. " and u.id_departamento = " . $pDepto;
}

if ($pUnidad != "") {
	$sql2= $sql2. " and u.unidad = " . $pUnidad;
}
if ($pCategoria != "") {
	$sql2= $sql2. " and u.id_categoria = " . $pCategoria;
}
if ($pNombre != "") {
	$sql2= $sql2. " and (u.nombre LIKE '%".$pNombre."%' or u.apellidos LIKE '%".$pNombre."%')";
}

$sql2= $sql2. " order by u.apellidos ";
$cursor = mssql_query($sql2);



?>
<html>
<head>
<script>
var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,"name","height=400,width=650, resizable=yes, scrollbars=yes");
	if (window.focus) {newwindow.focus()}
}
function muestraventana2(url)
{
	newwindow=window.open(url,"name2","height=400,width=650, resizable=0, scrollbars=0");
	if (window.focus) {newwindow.focus()}
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
</script>
<title>Usuarios Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winHojaTiempo";
</script>
<SCRIPT language=JavaScript>
<!--
function mOvr(src,clrOver) {
    if (!src.contains(event.fromElement)) {
	  src.style.cursor = 'hand';
	  src.bgColor = clrOver;
	}
  }
  function mOut(src,clrIn) {
	if (!src.contains(event.toElement)) {
	  src.style.cursor = 'default';
	  src.bgColor = clrIn;
	}
  }
  function mClk(src) {
    if(event.srcElement.tagName=='TD'){
	  src.children.tags('A')[0].click();
    }
  }

//-->
</SCRIPT>

<script language="JavaScript" type="text/JavaScript">
<!--

function MM_swapImgRestore() { //v3.0
	var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
	var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
		if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
		for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
		if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
	var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"  bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 365px;">
LISTADO GENERAL DE USUARIOS</div>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="Fecha"><? echo strtoupper($nombreempleado." ".$apellidoempleado); ?></td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	
	</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Criterio de selecci&oacute;n de usuarios de Ingetec </td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
	<form name="form1" method="post" action="">
  <tr>
    <td width="25%" class="TituloTabla">Divisi&oacute;n</td>
    <td class="TxtTabla"><select name="pDivision" class="CajaTexto" id="pDivision" onChange="MM_callJS('document.form1.submit();')">
      <option value="0"> </option>
      <?
			$qSql1="Select * from divisiones " ;
			$qCursor1 = mssql_query($qSql1);
			while ($qReg1=mssql_fetch_array($qCursor1)) {
				if ($pDivision == $qReg1[id_division]) {
					$selDiv = "selected";
				}
				else {
					$selDiv = "";
				}
			?>
      <option value="<? echo $qReg1[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($qReg1[nombre])); ?></option>
      <? } ?>
    </select></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Departamento</td>
    <td class="TxtTabla"><select name="pDepto" class="CajaTexto" id="select" onChange="MM_callJS('document.form1.submit();')">
      <option value="0"> </option>
      <?
			  if (trim($pDivision) == "") {
				$qSql2="Select * from departamentos where id_division = 1 "  ;
			  }
			  else {
				$qSql2="Select * from departamentos where id_division =" . $pDivision ;			  
			  }
			  
			$qCursor2 = mssql_query($qSql2);
			while ($qReg2=mssql_fetch_array($qCursor2)) {
				if ($pDepto == $qReg2[id_departamento]) {
					$selDep = "selected";
				}
				else {
					$selDep = "";
				}
			?>
      <option value="<? echo $qReg2[id_departamento]; ?>" <? echo $selDep; ?> ><? echo ucwords(strtolower($qReg2[nombre])); ?></option>
      <? } ?>
    </select></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Unidad</td>
    <td class="TxtTabla"><input name="pUnidad" type="text" class="CajaTexto" id="pUnidad2"></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><select name="pCategoria" class="CajaTexto" id="select2" >
      <option value="" ><? echo ""; ?></option>
      <?

			$sql2="Select * from Categorias " ;
			$cursor2 = mssql_query($sql2);
			while ($reg2=mssql_fetch_array($cursor2)) {
				if ($pCategoria == $reg2[id_categoria]) {
					$selCat = "selected";
				}
				else {
					$selCat = "";
				}
			?>
      <option value="<? echo $reg2[id_categoria]; ?>" <? echo $selCat; ?> ><? echo $reg2[nombre]; ?></option>
      <? } ?>
    </select></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Nombre</td>
    <td class="TxtTabla"><input name="pNombre" type="text" class="CajaTexto" id="pNombre2" size="50">
      <input name="Submit" type="submit" class="Boton" value="Buscar"></td>
  </tr>
  </form>
</table></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Listado General de Usuarios Activos</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Unidad</td>
        <td width="30%">Usuario</td>
        <td>Categoria</td>
        <td>Divisi&oacute;n</td>
        <td>Departamento</td>
        <td width="2%">Modificar</td>
        <td width="1%">Retirar</td>
        <td width="1%">Salario</td>
      </tr>
	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>
        <td width="30%"><? echo ucwords(strtolower($reg[apellidos])) . " " . ucwords(strtolower($reg[nombre])) ; ?></td>
        <td><? echo ucwords(strtolower($reg[categoria]))  ; ?></td>
        <td><? echo ucwords(strtolower($reg[division]))  ; ?></td>
        <td><? echo ucwords(strtolower($reg[departamento]))  ; ?></td>
        <td width="2%" align="center">
        <a href="#"><img src="img/images/actualizar.jpg" alt="Editar" width="19" height="17" border="0" 
        onclick="MM_openBrWindow('upUsuariosHT.php?cualUnidad=<? echo $reg[unidad] ; ?>
		','vAddA','scrollbars=yes,resizable=yes,width=500,height=325')" /></a>
        </td>
        <td width="1%" align="center"><a href="#"><img src="img/images/No.gif" alt="Retirar usuario" width="12" height="16" border="0" onClick="MM_openBrWindow('upUsuariosRetira.php?cualUnidad=<? echo $reg[unidad] ; ?>','vRUsu','scrollbars=yes,resizable=yes,width=500,height=325')"></a></td>
        <td width="1%" align="center"><a href="#"><img src="img/images/icoCuantia.gif" alt="Actualizar Salario" width="16" height="16" border="0" onClick="MM_openBrWindow('verSalarioUsuario.php?cualUnidad=<? echo $reg[unidad] ; ?>','winSalarioUsu','scrollbars=yes,resizable=yes,width=500,height=400')"></a></td>
      </tr>
	  <?
	  }
	  ?>
    </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit3" type="submit" class="Boton" onClick="MM_goToURL('parent','ProgMantenimiento.php');return document.MM_returnValue" value="Mantenimiento">
          <input name="InsertarUsr" type="button" class="Boton" onClick="MM_openBrWindow('addUsuariosHT.php','addUsr','scrollbars=yes,resizable=yes,width=600,height=300')" value="Insertar">
          &nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input name="Submit2" type="submit" class="Boton" onClick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal  Hoja de tiempo"></td>
    <td align="right" valign="bottom">&nbsp;</td>
  </tr>
</table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
