<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?
//13Nov2007
//--Consulta para taer el listado de usuarios que enviaron la HT a su jefe pero este no la ha aprobado
$sql2="Select A.* , U.nombre, U.apellidos, U.retirado, C.nombre nomCategoria, D.nombre nomDpto, V.nombre nomDivision  ";
$sql2= $sql2." from AutorizacionesHT A, Usuarios U, categorias C, Departamentos D, Divisiones V ";
$sql2= $sql2." where A.unidad = U.unidad  ";
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql2= $sql2. " and A.mes = month(getdate()) ";
	$sql2= $sql2. " and A.vigencia = year(getdate()) ";
}
else {
	$sql2= $sql2. " and A.mes = " . $pMes;
	$sql2= $sql2. " and A.vigencia = " . $pAno;
}
$sql2= $sql2." and A.validaJefe = 0 ";
$sql2= $sql2." and U.id_categoria = C.id_categoria ";
$sql2= $sql2." and U.id_departamento = D.id_departamento ";
$sql2= $sql2." and D.id_division = V.id_division ";
//$sql2= $sql2." order by U.apellidos ";
$sql2=$sql2." order by V.nombre , D.nombre ";
$cursor = mssql_query($sql2);

//*****Encuentra el último día de un mes
if ($pMes == "") {
	$esteMes=date("m"); 
	$esteAno=date("Y"); 
}
else {
	$esteMes=$pMes;
	$esteAno= $pAno;
}

if (($esteMes==1) OR ($esteMes==3) OR  ($esteMes==5) OR ($esteMes==7) OR ($esteMes==8) OR ($esteMes==10) OR ($esteMes==12)  ) {
	$esteDia=31;
}
if (($esteMes==4) OR ($esteMes==6) OR  ($esteMes==9) OR ($esteMes==11) ) {
	$esteDia=30;
}
if ($esteMes==2)  {
	if(checkdate(2, 29, $esteAno)) {
		$esteDia="29";
	}
	else {
		$esteDia="28";
	}
}
$estaFecha=$esteAno."-".$esteMes."-".$esteDia;

//******


//13Nov2007
//--Consulta para traer el listado de personas que no han enviado su Hoja de tiempo al jefe
$sql3="Select U.*, C.nombre nomCategoria, D.nombre nomDpto, V.nombre nomDivision    " ;
$sql3=$sql3." from usuarios  U, categorias C, Departamentos D, Divisiones V ";
$sql3=$sql3." where NOT EXISTS  ";
$sql3=$sql3." 	(  ";
$sql3=$sql3." 	Select *  ";
$sql3=$sql3." 	from AutorizacionesHT ";
$sql3=$sql3." 	where unidad = U.unidad  ";
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql3=$sql3." 	AND mes = month(getdate()) ";
	$sql3=$sql3." 	and vigencia = year(getdate()) ";
}
else {
	$sql3=$sql3." 	AND mes =" . $pMes;
	$sql3=$sql3." 	and vigencia = " . $pAno;
}
$sql3=$sql3." 	) ";
$sql3=$sql3." and U.unidad <> 20400 and U.unidad <> 52979709  ";
$sql3=$sql3." and U.retirado is null  ";
$sql3=$sql3." and U.id_categoria = C.id_categoria ";
$sql3=$sql3." and U.id_departamento = D.id_departamento ";
$sql3=$sql3." and D.id_division = V.id_division ";
$sql3=$sql3." and U.fechaIngreso <= CONVERT(DATETIME, '".$estaFecha."', 102) ";
$sql3=$sql3." and U.fechaIngreso  is not null ";
//$sql3=$sql3." order by U.apellidos ";
$sql3=$sql3." order by V.nombre , D.nombre ";
$cursor3 = mssql_query($sql3);
//echo $sql3 ;
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
</script>
<title>Hoja de tiempo</title>
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
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"  bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>
	<div class="TxtNota1" style="position:absolute; left:258px; top:8px; width: 522px;">
		<div align="center"> HOJAS DE TIEMPO <BR> 
	  SIN APROBACI&Oacute;N DEL JEFE / SIN ENV&Iacute;O AL JEFE </div>
</div>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="Fecha"><? echo strtoupper($nombreempleado." ".$apellidoempleado); ?></td>
  </tr>
</table>
<table width="100%" border="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos 
	<?
		if ($pMes == "") {
			$miMesHT = gmdate ("n");
			$MiAnnoHT = gmdate ("Y");
		}
		else {
			$miMesHT = $pMes;
			$MiAnnoHT = $pAno;
		}
	?>
	</td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
<form name="form1" method="post" action="">

  <tr>
    <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}



	}
	
	?>
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes">
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select></td>
    <td width="15%" align="right" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
	&nbsp;
	<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>

    </select>	</td>
    <td width="10%"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
</form>
</table>
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
    <td><input name="Submit4" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHT.php');return document.MM_returnValue" value="Reporte Usuarios ">    
     &nbsp; <input name="Submit3" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHTAprob.php');return document.MM_returnValue" value="Reporte Usuarios con aprobaci&oacute;n">
&nbsp;
<input name="Submit4" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHTNoAprob.php');return document.MM_returnValue" value="Reporte Usuarios sin aprobaci&oacute;n">
<? if ($_SESSION["sesUnidadUsuario"] == 14888) { ?>
<input name="Submit5" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHTFaltantes.php');return document.MM_returnValue" value="Hojas faltantes">
<? } ?>
    </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Reporte de usuarios que enviaron la Hoja de tiempo al jefe y no se han aprobado. </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Unidad</td>
        <td>Usuario</td>
        <td width="5%">Categor&iacute;a</td>
        <td width="15%">Divisi&oacute;n</td>
        <td width="15%">Departamento</td>
        <td width="1%">&nbsp;</td>
        </tr>
	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>
        <td><? echo ucwords(strtolower($reg[apellidos])) . " " . ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="5%"><? echo strtoupper($reg[nomCategoria]) ; ?></td>
        <td width="15%"><? echo ucwords(strtolower($reg[nomDivision])) ; ?></td>
        <td width="15%"><? echo ucwords(strtolower($reg[nomDpto])) ; ?></td>
        <td width="1%">
		<input name="Submit" type="submit" class="Boton" onClick="MM_goToURL('parent','verhdetiempoCont2.php?zUnidad=<? echo $reg[unidad]; ?>&Flmes=<? echo $miMesHT; ?>&Flano=<? echo $MiAnnoHT; ?>');return document.MM_returnValue" value="Ver Hoja" />
		</td>
        </tr>
	  <?
	  }
	  ?>
    </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">&nbsp;</td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloUsuario">Reporte de usuarios que no han enviado su Hoja de tiempo al jefe </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="10%">Unidad</td>
          <td>Usuario</td>
          <td width="5%">Categor&iacute;a</td>
          <td width="15%">Divisi&oacute;n</td>
          <td width="15%">Departamento</td>
          <td width="1%">&nbsp;</td>
        </tr>
        	  <?
	  while ($reg3=mssql_fetch_array($cursor3)) {
	  ?>
      <tr class="TxtTabla">
        <td width="10%"><? echo $reg3[unidad]; ?></td>
        <td><? echo ucwords(strtolower($reg3[apellidos])) . " " . ucwords(strtolower($reg3[nombre])) ; ?></td>
        <td width="5%"><? echo strtoupper($reg3[nomCategoria]) ; ?></td>
        <td width="15%"><? echo ucwords(strtolower($reg3[nomDivision])) ; ?></td>
        <td width="15%"><? echo ucwords(strtolower($reg3[nomDpto])) ; ?></td>
        <td width="1%">
		<input name="Submit" type="submit" class="Boton" onClick="MM_goToURL('parent','verhdetiempoCont2.php?zUnidad=<? echo $reg3[unidad]; ?>&Flmes=<? echo $miMesHT; ?>&Flano=<? echo $MiAnnoHT; ?>');return document.MM_returnValue" value="Ver Hoja" />
		</td>
        </tr>
	  <?
	  }
	  ?>
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
