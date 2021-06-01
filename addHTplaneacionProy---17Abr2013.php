<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Carga la variable de la vigencia que viene de la página anterior
if (trim($lstVigencia) == "") {
	$lstVigencia = $cualVigencia;
}

//--Trae la información de la Actividad que se seleccionó
$nombreActSel= "";
$nivelActSel= "" ;
$nivelesSupActSel= "" ;
$fechaIniActSel= "" ;
$fechaFinActSel= "" ;
$sql01="SELECT *  ";
$sql01=$sql01." FROM Actividades ";
$sql01=$sql01." WHERE id_proyecto = " . $cualProyecto ;
$sql01=$sql01." AND id_actividad = " . $cualAct ;
$cursor01 = mssql_query($sql01);
if ($reg01=mssql_fetch_array($cursor01)) {
	$nombreActSel= $reg01[nombre] ;
	$nivelActSel= $reg01[nivel] ;
	$nivelesSupActSel= $reg01[nivelesActiv] ;
	
	$fechaIniActSel= $reg01[fecha_inicio] ;
	$fechaFinActSel= $reg01[fecha_fin] ;
	
	//3Abr2013
	//Definir la fecha inicio mínima y final máxima de todas las actividades que hacen parte del proyecto
	$minVigenciaP="";
	$maxVigenciaP="";
	$minMesP = "" ;
	$maxMesP = "" ;
	$cantMesesDibuja="";
	$sql03="SELECT YEAR(MIN(fecha_inicio)) fechaMin, YEAR(MAX(fecha_fin)) fechaMax, MONTH(MIN(fecha_inicio)) mesMin, MONTH(MAX(fecha_fin)) mesMax   ";
	$sql03=$sql03." FROM Actividades ";
	$sql03=$sql03." WHERE id_proyecto = " . $cualProyecto;
	$sql03=$sql03." AND id_actividad = " . $cualAct;
	$cursor03 = mssql_query($sql03);
	if ($reg03=mssql_fetch_array($cursor03)) {
		$minVigenciaP = $reg03[fechaMin] ;
		$maxVigenciaP = $reg03[fechaMax] ;
		$minMesP = $reg03[mesMin] ;
		$maxMesP = $reg03[mesMax] ;
		
		if ($maxVigenciaP > $minVigenciaP) {
			$maxMesP = 12 ;
		}
		
		//Calcula la cantidad de mees a dibujar
		$cantMesesDibuja=$maxMesP-$minMesP;
	}	
}

echo $minMesP . "<br>";
echo $maxMesP . "<br>";
echo $cantMesesDibuja . "<br>";

//Trae todas las actividades superiores a la seleccionada
//LC, LT, Div, Act
$nomLoteControl="";
$nivelLoteControl="";
$macroLoteControl="";
$nomLoteTrabajo="";
$nivelLoteTrabajo="";
$macroLoteTrabajo="";
$nomLoteDiv="";
$nivelLoteDiv="";
$macroLoteDiv="";
$fechaIniLoteDiv="";
$fechaFinLoteDiv="";
$nomLoteAct="";
$nivelLoteAct="";
$macroLoteAct="";
$fechaLoteAct="";
$fechaIniLoteAct="";
$fechaFinLoteAct="";
$sql02="SELECT *  ";
$sql02=$sql02." FROM Actividades ";
$sql02=$sql02." WHERE id_proyecto = " . $cualProyecto ;
$sql02=$sql02." AND id_actividad IN ( " . str_replace("A,", "", str_replace("-", ",", $nivelesSupActSel))  . ") " ;
$cursor02 = mssql_query($sql02);
while ($reg02=mssql_fetch_array($cursor02)) {
	if ($reg02[nivel] == 1) {
		$nomLoteControl=$reg02[nombre];
		$nivelLoteControl=$reg02[nivel];
		$macroLoteControl=$reg02[macroactividad];
	}
	if ($reg02[nivel] == 2) {
		$nomLoteTrabajo=$reg02[nombre];
		$nivelLoteTrabajo=$reg02[nivel];
		$macroLoteTrabajo=$reg02[macroactividad];
	}
	if ($reg02[nivel] == 3) {
		$nomLoteDiv=$reg02[nombre];
		$nivelLoteDiv=$reg02[nivel];
		$macroLoteDiv=$reg02[macroactividad];
		$fechaIniLoteDiv=$reg02[fecha_inicio];
		$fechaFinLoteDiv=$reg02[fecha_fin];
	}
	if ($reg02[nivel] == 4) {
		$nomLoteAct=$reg02[nombre];
		$nivelLoteAct=$reg02[nivel];
		$macroLoteAct=$reg02[macroactividad];
		$fechaIniLoteAct=$reg02[fecha_inicio];
		$fechaFinLoteAct=$reg02[fecha_fin];
	}
}



//--Trae las personas asociadas a la actividad
//Encargado de actividad, Programadores, Responsables delegados y participantes
$sql04="SELECT * ";
$sql04=$sql04." FROM usuarios ";
$sql04=$sql04." WHERE unidad IN ";
$sql04=$sql04." 	( ";
//$sql04=$sql04." 	--Encargado de la actividad ";
$sql04=$sql04." 	SELECT id_encargado ";
$sql04=$sql04." 	FROM Actividades ";
$sql04=$sql04." 	WHERE id_proyecto = " . $cualProyecto;
$sql04=$sql04." 	AND id_actividad = " . $cualAct;
//$sql04=$sql04." 	--Programadores de la Actividad ";
$sql04=$sql04." 	UNION ";
$sql04=$sql04." 	SELECT unidad  ";
$sql04=$sql04." 	FROM Programadores ";
$sql04=$sql04." 	WHERE id_proyecto = " . $cualProyecto;
$sql04=$sql04." 	AND id_actividad = " . $cualAct;
$sql04=$sql04." 	AND estado = 'A' ";
$sql04=$sql04." 	UNION ";
//$sql04=$sql04." 	--Rsponsables delegados de la actividad ";
$sql04=$sql04." 	SELECT unidad  ";
$sql04=$sql04." 	FROM ResponsablesActividad ";
$sql04=$sql04." 	WHERE id_proyecto = " . $cualProyecto;
$sql04=$sql04." 	AND id_actividad = " . $cualAct;
$sql04=$sql04." 	AND estado = 'A' ";
$sql04=$sql04." 	UNION ";
//$sql04=$sql04." 	--Participantesde la actividad ";
$sql04=$sql04." 	SELECT unidad  ";
$sql04=$sql04." 	FROM ParticipantesActividad ";
$sql04=$sql04." 	WHERE id_proyecto = " . $cualProyecto;
$sql04=$sql04." 	AND id_actividad = " . $cualAct;
$sql04=$sql04." 	AND estado = 'A' ";
$sql04=$sql04." )";
$sql04=$sql04." order by apellidos	 ";
$cursor04 = mssql_query($sql04);

//Define el array de meses a usar en la página
$vMeses= array("","Ene","Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"); 



//echo $sql04 . "<br>";
//*************HASTA AQUI

/*
echo $nombreActSel . "<br>";
echo $nivelActSel . "<br>";
echo $nivelesSupActSel . "<br>";
echo str_replace("-", ",", $nivelesSupActSel) . "<br>";
echo str_replace("A,", "", $nivelesSupActSel) . "<br>";
echo str_replace("A,", "", str_replace("-", ",", $nivelesSupActSel)) . "<br>";

echo $cualProyecto . "<br>";
echo $cualVigencia . "<br>";
echo $cualAct . "<br>";

exit;
*/

//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}


if(trim($recarga) == "2"){
	//Realiza la grabación en Muestra
	$msgGraba = "";
	$msgNOGraba = "";
	$s = 1;
	while ($s <= $pCantReg) {
		//Generar la secuencia  del lote de trabajo
		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sigienteSec =0;
		$sqlId = " SELECT COALESCE(MAX(codLoteControl), 0) AS elMax FROM EnsayosProyLC ";
		$sqlId = $sqlId. " WHERE id_proyecto = " . $_SESSION["sesProyLaboratorio"] ;
		$cursorId = mssql_query($sqlId);
		if($regId = mssql_fetch_array($cursorId)){
			$sigienteSec = $regId["elMax"] + 1;
		}

		//Recoger las variables
		$elpNombre = "pNombre" . $s;
		$elpSigla = "pSigla" . $s;

		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sqlIn1 = " INSERT INTO EnsayosProyLC ";
		$sqlIn1 = $sqlIn1 . " (id_proyecto, codLoteControl, nomLoteControl, siglaLC, fechaCrea, usuarioCrea ) ";
		$sqlIn1 = $sqlIn1 . " VALUES ( ";
		$sqlIn1 = $sqlIn1 . " " . $_SESSION["sesProyLaboratorio"] . ", ";
		$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";
		$sqlIn1 = $sqlIn1 . " '" . ${$elpNombre} . "', ";
		$sqlIn1 = $sqlIn1 . " '" . ${$elpSigla} . "', ";
		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";
		$sqlIn1 = $sqlIn1 . " ) ";
//		$cursorIn1 = mssql_query($sqlIn1);

		if  (trim($cursorIn1) != "")  {
			//echo "entro eal if 2" . "<br>";
			$msgGraba=$msgGraba."[".${$elpSigla}."] " ;
		}
		else {
			//echo "entro al else " . "<br>";
			$msgNOGraba=$msgNOGraba."[".${$elpSigla}."] " ; 
		}
				
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	//if  (trim($cursorIn1) != "")  {
	if  (trim($msgNOGraba) != "")  {
		echo ("<script>alert('No se grabaron los siguientes Lotes de control: $msgNOGraba ');</script>"); 
	} 
	
	if  (trim($msgGraba) != "")  {
		echo ("<script>alert('Se grabaron las siguientes Lotes de control: $msgGraba ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('pnfProgProyectos01.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--


function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}


function envia2(){ 
var v1,v2,v3, v4,v5,v6, v7,v8,v9, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, msg7, msg8, msg9, msg10, msg11, msg12, msg13, msg14, msg15, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
v7='s';
v8='s';
v9='s';
v10='s';
v11='s';
v12='s';
v13='s';
v14='s';
v15='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
msg7 = '';
msg8 = '';
msg9 = '';
msg10 = '';
msg11 = '';
msg12 = '';
msg13 = '';
msg14 = '';
msg15 = '';
mensaje = '';

CantCampos=1+(2*document.Form1.pCantReg.value);

	
//Valida que el campo Nombre no esté vacio
for (i=1;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v2='n';
		msg2 = 'Nombre es obligatorio. \n'
	}
}

//Valida que el campo Sigla no esté vacio
for (i=2;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v3='n';
		msg3 = 'Sigla es obligatorio. \n'
	}
}



//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s') && (v10=='s') && (v11=='s') && (v12=='s') && (v13=='s') && (v14=='s') && (v15=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6 + msg7 + msg8 + msg9 + msg10 + msg11 + msg12 + msg13 + msg14 + msg15;
		alert (mensaje);
	}
}
//-->
</script>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Planeaci&oacute;n de recursos </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="20%" class="TituloTabla">Lote de control </td>
          <td class="TxtTabla">
		  <?
		  echo "<B>" . " [" . $macroLoteControl . "] " .  strtoupper($nomLoteControl) . "</B>" ;
		  ?>
		  </td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla"><?
		  echo " [" . $macroLoteTrabajo . "] " .  strtoupper($nomLoteTrabajo) ;
		  ?></td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Divisi&oacute;n </td>
          <td class="TxtTabla"><?
		  echo " [" . $macroLoteDiv . "] " .  strtoupper($nomLoteDiv) ;
		  ?>
            <br>
		  <?
		  if ( (trim($fechaIniLoteDiv) != "" ) AND (trim($fechaFinLoteDiv) != "" )) {
			echo "FI [" . date("M d Y ", strtotime($fechaIniLoteDiv)) . "] - FF [" . date("M d Y ", strtotime($fechaFinLoteDiv)) . "] "; 
		  }
		  ?>
		  </td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Actividad</td>
          <td class="TxtTabla"><?
		  echo " [" . $macroLoteAct . "] " .  strtoupper($nomLoteAct) ;
		  ?>
		    <br>
		    <?
		  	if ( (trim($fechaIniLoteAct) != "" ) AND (trim($fechaFinLoteAct) != "" )) {
				echo "FI [" . date("M d Y ", strtotime($fechaIniLoteAct)) . "] - FF [" . date("M d Y ", strtotime($fechaFinLoteAct)) . "] "; 
			}
		  ?>
		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Vigencia</td>
          <td class="TxtTabla">
		  <select name="lstVigencia" class="CajaTexto" id="lstVigencia" onChange="document.form1.submit();">
		<? 
		for ($k=$minVigenciaP; $k<=$maxVigenciaP; $k++) { 
			if ($lstVigencia == $k) {
				$selVig = "selected";
			}
			else {
				$selVig = "";
			}
		?>
          <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
		<? } ?>
        </select>
		  </td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloUsuario"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">PLANEACI&Oacute;N DE RECURSOS </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="10%">Participantes</td>
          <td width="5%">Acci&oacute;n</td>
          <td width="5%">Hombres / Mes </td>
          <td width="5%">A partir de<br>(mes) </td>
          <td width="5%">Cu&aacute;ntas veces</td>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" class="TituloTabla2"><? echo $lstVigencia; ?></td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
		  <tr class="TituloTabla2">
		  <? for ($m=$minMesP; $m<=$maxMesP; $m++) { ?>
			<td width="5%"><? echo $vMeses[$m]; ?></td>
			<? } // for ?>
		  </tr>
		</table>
</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<? 	
		$r = 1;
		while ($reg04=mssql_fetch_array($cursor04)) { 	?>
        <tr class="TxtTabla">
          <td width="10%">		  <select name="lstUnidadP<? echo $r; ?>" class="CajaTexto" id="lstUnidadP<? echo $r; ?>" style='width:200px; ' >
            <option value="<? echo $reg04[unidad]; ?>"><? echo "[" . $reg04[unidad] . "] " . ucwords(strtolower($reg04[apellidos])) . ", " . ucwords(strtolower($reg04[nombre])) ;; ?></option>
          </select></td>
          <td width="5%"><input name="btnAccion<? echo $r; ?>" type="radio" value="1" checked>
Replicar<br>
<input name="btnAccion<? echo $r; ?>" type="radio" value="2">
Dividir</td>
          <td width="5%" align="center"><input name="txtHomMes<? echo $r; ?>" type="text" class="CajaTexto" id="txtHomMes<? echo $r; ?>"  size="10" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" ></td>
          <td width="5%" align="center">
		<select name="lstPartirMes<? echo $r; ?>" class="CajaTexto" id="lstPartirMes<? echo $r; ?>">
			<option value="0">..:: &nbsp;</option>
		  <? for ($m=$minMesP; $m<=$maxMesP; $m++) { ?>
            <option value="<? echo $m; ?>"><? echo $vMeses[$m]; ?></option>
			<? } // for ?>
          </select></td>
          <td width="5%" align="center"><input name="txtRepite<? echo $r; ?>" type="text" class="CajaTexto" id="txtRepite<? echo $r; ?>"  size="10" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" ></td>
          <td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
			  <tr class="TituloTabla2">
			  <? for ($m=$minMesP; $m<=$maxMesP; $m++) { ?>
				<td width="5%" class="TxtTabla"><input name="<? echo $m; ?>txtPlan<? echo $r; ?>" type="text" class="CajaTexto" id="<? echo $m; ?>txtPlan<? echo $r; ?>" size="10">	</td>
				<? } // for ?>
			  </tr>
			</table>
			</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<? 
		$r = $r + 1;
		} // Cierra While cursor04 ?>
      </table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="cantMeses" type="hidden" id="cantMeses" value="<? echo $cantMesesDibuja ; ?>">
  		    <input name="cantItems" type="hidden" id="cantItems" value="<? echo ($r - 1)  ; ?>">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>
			<? echo "r=" . ($r - 1) . "<br>"; ?>
			<? echo "cantMesesDibuja=" . $cantMesesDibuja . "<br>"; ?>

</td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="TituloTabla">Lotes de control </td>
          <td class="TxtTabla"><strong>[LC1] GERENCIA DEL PROYECTO</strong></td>
        </tr>
        <tr>
          <td class="TituloTabla">Fecha Inicial </td>
          <td class="TxtTabla">1-Ene-2012</td>
        </tr>
        <tr>
          <td class="TituloTabla">Fecha Final </td>
          <td class="TxtTabla">15-Dic-2012</td>
        </tr>
        <tr>
          <td class="TituloTabla">Vigencia</td>
          <td class="TxtTabla"><select name="select" class="CajaTexto">
            <option value="2013">2013</option>
            <option value="2014">2014</option>
            <option value="2015">2015</option>
          </select></td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Cantidad de personas a programar </td>
          <td class="TxtTabla"><input name="pCantReg" type="text" class="CajaTexto" id="pCantReg" value="<? echo $pCantReg; ?>" size="10" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onChange="envia1()"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">PROGRAMACI&Oacute;N BASE </td>
        </tr>
      </table>      
	<? 
	//echo $brtHM1;
	if (($brtHM1 == "") OR ($brtHM1 == "0")) { 
	?>
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Participantes</td>
        <td width="3%">Hombres<br>
          /Mes</td>
        <td width="8%">Acci&oacute;n</td>
        <td width="5%">A partir de <br>
          (mes)</td>
        <td width="3%"><p>Ene<br>
          2013</p>          </td>
        <td width="3%">Feb<br>
          2013</td>
        <td width="3%">Mar<br>
          2013</td>
        <td width="3%">Abr<br>
          2013</td>
        <td width="3%">May<br>
          2013</td>
        <td width="3%">Jun<br>
          2013</td>
        <td width="3%">Jul<br>
          2013</td>
        <td width="3%">Ago<br>
          2013</td>
        <td width="3%">Sep<br>
          2013</td>
        <td width="3%">Oct<br>
          2013</td>
        <td width="3%">Nov<br>
          2013</td>
        <td width="3%">Dic<br>
          2013</td>
      </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  ?>
      <tr class="TxtTabla">
        <td align="center">
		<select name="pJefe" class="CajaTexto" id="pJefe" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
		$sql2=$sql2." order by U.apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		</td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="10" ></td>
        <td width="8%" align="left">&nbsp;
          <input name="brtHM<? echo $r; ?>" type="radio" value="1"  onClick="envia1()">
          Replicar&nbsp;
            <br>
            &nbsp;
            <input name="brtHM<? echo $r; ?>" type="radio" value="0" checked>
            Dividir </td>
        <td width="5%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
      </tr>
		<? 
		$r = $r + 1;
		$nuevoCodigo = $nuevoCodigo + 1;
		} ?>
    </table>
	<? }
	if ($brtHM1 == "1") { 
		
	?>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Participantes</td>
        <td width="3%">Hombres<br>
          /Mes</td>
        <td width="8%">Acci&oacute;n</td>
        <td width="5%">A partir de<br>
           (mes) </td>
        <td width="3%"><p>Ene<br>
          2013</p>          </td>
        <td width="3%">Feb<br>
          2013</td>
        <td width="3%">Mar<br>
          2013</td>
        <td width="3%">Abr<br>
          2013</td>
        <td width="3%">May<br>
          2013</td>
        <td width="3%">Jun<br>
          2013</td>
        <td width="3%">Jul<br>
          2013</td>
        <td width="3%">Ago<br>
          2013</td>
        <td width="3%">Sep<br>
          2013</td>
        <td width="3%">Oct<br>
          2013</td>
        <td width="3%">Nov<br>
          2013</td>
        <td width="3%">Dic<br>
          2013</td>
      </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  	if ($r==1) {
			$mostrar=1;
		}
		else {
			$mostrar="";
		}
	  ?>
      <tr class="TxtTabla">
        <td align="center">
		<select name="pJefe" class="CajaTexto" id="pJefe" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
		$sql2=$sql2." order by U.apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		</td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="10" ></td>
        <td width="8%" align="left">&nbsp;
          <input name="brtHM<? echo $r; ?>" type="radio" value="1" checked>
          Replicar&nbsp;
            <br>
            &nbsp;
            <input name="brtHM<? echo $r; ?>" type="radio" value="0">
            Dividir</td>
        <td width="5%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
      </tr>
		<? 
		$r = $r + 1;
		$nuevoCodigo = $nuevoCodigo + 1;
		} ?>
    </table>
	<? } ?>
      
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">NOTA:</td>
        </tr>
        <tr>
          <td class="TxtTabla">1. Esta ventana captura Hombres / Mes y las convierte en horas acorde con la totalida de horas por mes previamente definidas en la base de datos. </td>
        </tr>
        <tr>
          <td class="TxtTabla">2. </td>
        </tr>
      </table></td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
</body>
</html>

<? mssql_close ($conexion); ?>	
