<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?
//inicializa el valor de retirado
if (trim($pRetirado) == "") {
	$pRetirado = "0";
}

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
//Para que muestre la Hojas de tiempo de los usuarios retirados

if (($pRetirado == "") OR ($pRetirado == "0")) {
	$sql2= $sql2. " and u.retirado IS NULL ";
}
if ($pRetirado == "1") {
	if ((trim($pMesRetiro) == "") OR (trim($pMesRetiro) == "1") ) { 
		if ($pMes == "") {
			$sql2= $sql2. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
			$sql2= $sql2. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
		}
	}
}

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

//$sql2= $sql2. " order by u.apellidos ";
$sql2= $sql2. " order by c.nombre , u.unidad ";


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

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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
	<div class="TxtNota1" style="position:absolute; left:258px; top:8px; width: 365px;">
		<div align="center"> REVISIÓN HOJAS DE TIEMPO <BR> CONTRATOS </div>
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
    <td class="TituloUsuario">Criterio de seleccio&oacute;n de usuarios de Ingetec </td>
  </tr>
</table>
<table width="100%"  border="0" cellpadding="0" cellspacing="1">

  <tr>
    <td class="TituloTabla">Divisi&oacute;n</td>
    <td><select name="pDivision" class="CajaTexto" id="pDivision" onChange="MM_callJS('document.form1.submit();')">
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
    </select>      </td>
  </tr>
  <tr>
    <td class="TituloTabla">Departamento</td>
    <td>
	<select name="pDepto" class="CajaTexto" id="pDepto" onChange="MM_callJS('document.form1.submit();')">
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
    </select>	</td>
  </tr>
  <tr>
    <td width="20%" class="TituloTabla">Unidad</td>
    <td><input name="pUnidad" type="text" class="CajaTexto" id="pUnidad"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Categor&iacute;a</td>
    <td><select name="pCategoria" class="CajaTexto" id="pCategoria" >
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
    <td class="TituloTabla">Ver usuarios retirados  ? </td>
    <td class="TxtTabla">
	<? 
	if ($pRetirado == 1) { 
		$selSI = "checked";
		$selNO = "";
	}
	else {
		$selSI = "";
		$selNO = "checked";
	}
	
	?>
	<input name="pRetirado" type="radio" value="1" <? echo $selSI; ?> >
    Si
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="pRetirado" type="radio" value="0" <? echo $selNO; ?> >
    No</td>
  </tr>
  <? if (trim($pRetirado) == "1") { ?>
  <tr>
    <td class="TituloTabla">M&eacute;s de retiro  ? </td>
    <td class="TxtTabla">
	<? 
	if ((trim($pMesRetiro) == "") OR (trim($pMesRetiro) == "1")) { 
		$selMRSI = "checked";
		$selMRNO = "";
	}
	else {
		$selMRSI = "";
		$selMRNO = "checked";
	}
	
	?>
	<input name="pMesRetiro" type="radio" value="1" <? echo $selMRSI; ?> >
      Seleccionado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
	  <input name="pMesRetiro" type="radio" value="2" <? echo $selMRNO; ?> >
      Cualquiera
	  </td>
  </tr>
  <? } ?>
  <tr>
    <td class="TituloTabla">Nombre</td>
    <td><input name="pNombre" type="text" class="CajaTexto" id="pNombre" size="50">
&nbsp;&nbsp;
<input name="Submit" type="submit" class="Boton" value="Buscar"></td>
  </tr>
</form>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHTAprob.php');return document.MM_returnValue" value="Reporte Usuarios con aprobaci&oacute;n">
    &nbsp;
    <input name="Submit4" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHTNoAprob.php');return document.MM_returnValue" value="Reporte Usuarios sin aprobaci&oacute;n">
    &nbsp;
	<input name="Submit4" type="submit" class="Boton" onClick="MM_goToURL('parent','ContratosHTNoAprobJefe.php');return document.MM_returnValue" value="Reporte Usuarios sin aprobaci&oacute;n Jefe / Sin env&iacute;o a Jefe">	</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Asociaci&oacute;n de perfiles a Usuarios</td>
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
        <td width="2%">Aprobado</td>
        <td width="1%">&nbsp;</td>
        <td width="5%">&nbsp;</td>
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
		<?
		//Consulta para verificar si la Hoja ya está aprobada o no por el jefe seleccionado
		$muestraContratos = "";
		$firmaContratos = "";
		$muestraJefe = "";
		$cualImprimio = 0;
		$qSql2="Select vigencia, mes, unidad, unidadJefe, validaJefe, comentaJefe, validaContratos, unidadContratos, comentaContratos, seImprimio ";
		$qSql2=$qSql2." from AutorizacionesHT ";
		$qSql2=$qSql2." where vigencia = " . $MiAnnoHT ;
		$qSql2=$qSql2." and mes =" .$miMesHT;
		$qSql2=$qSql2." and unidad =" . $reg[unidad];
		$qCursor2 = mssql_query($qSql2);
		$muestraBoton = 0;
		if ($qReg2=mssql_fetch_array($qCursor2)) {
			$muestraContratos = $qReg2[validaContratos];
			$muestraJefe = $qReg2[validaJefe];
			$firmaContratos = $qReg2[unidadContratos];
			$cualImprimio =  $qReg2[seImprimio];
		}
		?>
		<? if ($muestraContratos == "1") { ?>
        <img src="img/images/Si.gif" width="16" height="14"> 
		<? } ?>
		<? if (($muestraContratos == "0") AND (trim($firmaContratos ) != "")) { ?>
		<img src="img/images/No.gif" width="12" height="16">
		<? } ?>
		</td>
        <td width="1%">
		<?  if ($muestraJefe == "1") { ?>
		<input name="Submit" type="submit" class="Boton" onClick="MM_goToURL('parent','verhdetiempoCont.php?zUnidad=<? echo $reg[unidad]; ?>&Flmes=<? echo $miMesHT; ?>&Flano=<? echo $MiAnnoHT; ?>');return document.MM_returnValue" value="Ver Hoja" />
		<?  } ?>
		</td>
        <td width="5%">
		<?  if ($muestraJefe == "1") { ?>
		<img src="imagenes/imgPrint.gif" style="cursor:hand;" alt="Estado Impresi&oacute;n" width="14" height="14" border="0" onClick="MM_openBrWindow('upAutorizaHTimprime.php?cualUnidad=<? echo $reg[unidad]; ?>&cualMes=<? echo $miMesHT; ?>&cualAno=<? echo $MiAnnoHT; ?>','verUPImp','scrollbars=yes,resizable=yes,width=400,height=200')">
		<? if ($cualImprimio == "1") {
				echo "OK";
			}
		 ?>		
		 <? } ?>
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
