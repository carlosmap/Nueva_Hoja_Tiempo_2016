<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer la información del proyecto seleccionado
$sql="Select * ";
$sql=$sql." from Proyectos " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDProyecto = $reg[id_proyecto];
	$elProyecto = $reg[nombre];
	$elCodigo = $reg[codigo];
	$elCargoDef = $reg[cargo_defecto];
}

//--Traer las personas relacionadas a la facturación de un proyecto en un mes  y año especificos
$sql="Select H.unidad, U.nombre, U.apellidos, sum(horas_registradas) as totalHorasR ";
$sql=$sql." from horas H, usuarios U " ;
$sql=$sql." where H.unidad = U.unidad " ;
$sql=$sql." and H.id_proyecto = " . $cualProyecto ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql=$sql." and month(H.fecha) = month(getdate()) " ;
	$sql=$sql." and year(H.fecha) = year(getdate()) " ;
	$mesEnvio = date("m");
	$anoEnvio=date("Y");
}
else {
	$sql=$sql." and month(H.fecha) = " . $pMes;
	$sql=$sql." and year(H.fecha) = " . $pAno;
	$mesEnvio =  $pMes;
	$anoEnvio= $pAno;
}

$sql=$sql." group by H.unidad, U.nombre, U.apellidos " ;
$sql=$sql." order by  U.apellidos " ;
$cursor = mssql_query($sql);
//echo $sql . "<br>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reportes de Hoja de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:200px; top:8px; width: 529px; height: 25px;">
		<div align="center">Reportes Hoja de Tiempo <br> Director del proyecto		</div>
</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
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



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Informaci&oacute;n del proyecto </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Nombre</td>
        <td width="10%">C&oacute;digo</td>
        <td width="10%">Cargo</td>
		<? if($_SESSION["sesUnidadUsuario"] == 16374 or $_SESSION["sesUnidadUsuario"] == 15712 
		or $_SESSION["sesUnidadUsuario"] == 900047 or $_SESSION["sesUnidadUsuario"] == 16614 
		or ($_SESSION["sesUnidadUsuario"] == 15682 AND $elIDProyecto == 1271) ){ ?>
		<td width="10%">XLS Facturaci&oacute;n </td>
		<td width="1%">Log</td>
		<? } ?>
      </tr>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($elProyecto)) ; ?></td>
        <td width="10%"><? echo $elCodigo ; ?></td>
        <td width="10%"><? echo $elCargoDef ; ?></td>
        <?
		/*
		 if($_SESSION["sesUnidadUsuario"] == 16374 or $_SESSION["sesUnidadUsuario"] == 15712 
		or $_SESSION["sesUnidadUsuario"] == 900047 or $_SESSION["sesUnidadUsuario"] == 16614
		or ($_SESSION["sesUnidadUsuario"] == 15682 or $_SESSION["sesUnidadUsuario"] == 14497 AND $elIDProyecto == 1271) ){ 
		*/
		//28Mar2011
		//De acuerdo con instrucción de Silvia Palacio se habilita el botón para todo el mundo invlioucrado en el proyecto
		//Director, Coordinar y Ordenadores de gasto
		?>
		<td width="10%" align="center"><input name="Submit2" type="button" class="Boton" onclick="MM_openBrWindow('reporteFactProyecto.php?cualPr=<? echo $elIDProyecto; ?>&mes=<? echo $pMes; ?>&anio=<? echo $pAno; ?>','repFact','scrollbars=yes,resizable=yes,width=1024,height=768')" value="Generar XLS" /></td>
		<td width="1%" align="center"><a href="#" onclick="MM_openBrWindow('logReporteFactProyecto.php?cualPr=<? echo $elIDProyecto; ?>','','scrollbars=yes,resizable=yes,width=600,height=300')"><img src="img/images/ver.gif" width="16" height="16" border="0" /></a></td>
		<? // 		} ?>
      </tr>
    </table></td>
      </tr>
    </table>
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo del Director o encargado </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Unidad</td>
        <td>Usuarios que facturaron al Proyecto </td>
        <td width="60%"><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr align="center">
            <td>Actividades <br />
              Programadas </td>
            <td width="8%">Loc</td>
            <td width="8%">Clase <br />
              tiempo </td>
            <td width="15%">Cargo</td>
            <td width="15%">Horas</td>
            <td width="15%">Horas <br />
              Facturadas <br />
              Mes </td>
          </tr>
        </table>          </td>
        <td>Horas Registradas Mes Actual </td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>
        <td><? echo ucwords(strtolower($reg[nombre] . " " . $reg[apellidos])); ?></td>
        <td width="60%" align="center">
		<?
		//Trae las actividades en que un usuario tiene asignación y las respectivas horas asignadas
		$sQry="select A.* , B.nombre nomActividad ";
		$sQry=$sQry." from asignaciones A, actividades B ";
		$sQry=$sQry." where A.id_proyecto = B.id_proyecto ";
		$sQry=$sQry." and A.id_actividad = B.id_actividad ";
		$sQry=$sQry." and A.unidad = " . $reg[unidad];  
		$sQry=$sQry." and A.id_proyecto =" . $elIDProyecto;
		$sQry=$sQry." and month(A.fecha_inicial) = " . $mesEnvio;
		$sQry=$sQry." and year(A.fecha_inicial) =" . $anoEnvio;
		$cursorQry = mssql_query($sQry);
//		echo $sQry . "<br>" ;
		?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" bgcolor="#FFFFFF" class="TxtTabla">
	  <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
		   <?
		  while ($regQry=mssql_fetch_array($cursorQry)) {
		  ?>
		
          <tr>
            <td align="left"><? echo ucwords(strtolower($regQry[nomActividad])); ?></td>
            <td width="8%" align="right"><? echo $regQry[localizacion]; ?></td>
            <td width="8%" align="right"><? echo $regQry[clase_tiempo]; ?></td>
            <td width="15%" align="right"><? echo $regQry[cargo]; ?></td>
            <td width="15%" align="right"><? echo $regQry[tiempo_asignado]; ?></td>
            <td width="15%" align="right">
			<?
			//Trae el valor facturado por actividad en el mes seleccionado
			$sumaporActividad = 0;
			$qQryH = "select sum(horas_registradas) totHorasReg from horas ";
			$qQryH = $qQryH . " where unidad = " . $reg[unidad];
			$qQryH = $qQryH . " and id_proyecto = " . $elIDProyecto;
			$qQryH = $qQryH . " and id_actividad = " . $regQry[id_actividad];
			$qQryH = $qQryH . " and clase_tiempo = " . $regQry[clase_tiempo];
			$qQryH = $qQryH . " and SUBSTRING(cargo, 3, 2) = '" . $regQry[cargo] . "' ";
			$qQryH = $qQryH . " and localizacion = '" . $regQry[localizacion] . "' ";
			$qQryH = $qQryH . " and month(fecha) = " . $mesEnvio;
			$qQryH = $qQryH . " and year(fecha) = " . $anoEnvio;
//			echo $qQryH . "<br>" ;
			$qCursorQH = mssql_query($qQryH);
			if ($qRegQH=mssql_fetch_array($qCursorQH)) {
				$sumaporActividad = $qRegQH[totHorasReg];
			}
			echo $sumaporActividad ;
			?>			</td>
          </tr>
		  <? } ?>
        </table>	</td>
  </tr>
</table>		</td>
        <td width="5%" align="center">
		<? echo $reg[totalHorasR]; ?>
		</td>
        </tr>
	  <? } ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT2.php');return document.MM_returnValue" value="Listado de Proyectos" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
