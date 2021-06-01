<?php
session_start();
include("../verificaRegistro2.php");

?>
<?

$colorOVR = "#D6D6D6";
$ColorOUT = "#E9E9E9";

?>
<html>
<head>

<title>.:: Men&uacute; Gesti&oacute;n de proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">


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

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right" valign="middle"><? include("bannerArriba.php") ; ?> </td>
  </tr>
</table>
<div style="position:absolute; left:2px; top:55px; width: 494px;">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TxtNota2">Gesti&oacute;n de proyectos </td>
  </tr>
</table>
</div>

<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td class="TituloUsuario">.:: Gesti&oacute;n de proyectos </td>
  </tr>
</table>
<table width="100%"  border="1" cellpadding="5" cellspacing="1" bordercolor="#D6D6D6">
      <tr align="center" bgcolor="<? echo $ColorOUT ?>"  >
        <td height="30" class="menu" onClick=mClk(this); onMouseOver="mOvr(this,'<? echo $colorOVR ?>');" onMouseOut="mOut(this,'<? echo $ColorOUT ?>');"><a href="#" class="menu" onClick="MM_openBrWindow('htPlanProyectos.php','winHojaTiempo','scrollbars=yes,resizable=yes,width=960,height=550')" >Planeación de proyectos</a></td>
      </tr>
      <? //if($_SESSION["sesUnidadUsuario"] == 15712 ){ ?>
	  <tr align="center" bgcolor="<? echo $ColorOUT ?>"  >
        <td height="30" class="menu" onClick=mClk(this); onMouseOver="mOvr(this,'<? echo $colorOVR ?>');" onMouseOut="mOut(this,'<? echo $ColorOUT ?>');"><a href="#" class="menu" onClick="MM_openBrWindow('htFacturacion.php','winHojaTiempo','scrollbars=yes,resizable=yes,width=1048,height=650')">Mi Hoja de tiempo </a></td>
      </tr>
	  <? //} ?>
      <tr align="center" bgcolor="<? echo $ColorOUT ?>"  >
        <td height="30" class="menu" onClick=mClk(this); onMouseOver="mOvr(this,'<? echo $colorOVR ?>');" onMouseOut="mOut(this,'<? echo $ColorOUT ?>');"><a href="#" class="menu" onClick="MM_openBrWindow('htPlanProyectoConsolidadoDiv.php','winHojaTiempo','scrollbars=yes,resizable=yes,width=960,height=550')"> Reportes de los directores de División</a> </td>
      </tr>
		<tr align="center" bgcolor="<? echo $ColorOUT ?>"  >
        <td height="30" class="menu" onClick=mClk(this); onMouseOver="mOvr(this,'<? echo $colorOVR ?>');" onMouseOut="mOut(this,'<? echo $ColorOUT ?>');"><a href="#" class="menu" onClick="MM_openBrWindow('htVoBoProyectos.php','winHojaTiempo','scrollbars=yes,resizable=yes,width=960,height=550')"> Aprobaci&oacute;n de la Facturaci&oacute;n de los Proyectos y </a>los vi&aacute;ticos </td>
      </tr>	  
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td align="center"><input name="Submit" type="submit" class="Boton" onClick="MM_callJS('window.close()')" value="Cerrar Ventana "></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>

