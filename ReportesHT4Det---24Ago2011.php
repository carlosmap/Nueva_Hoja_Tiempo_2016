<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Traer la informaci�n del proyecto seleccionado
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

//B�squeda de las actividades
$sqlAct = "SELECT * FROM Actividades
		   WHERE id_proyecto=".$cualProyecto;
if($pActividad != "")
{
	$sqlAct = $sqlAct." AND id_actividad=".$pActividad;
}
$cursorAct = mssql_query($sqlAct);

//B�squeda de las personas participantes en cada actividad
$sqlPart = "SELECT DISTINCT U.* 
			FROM Horas H,
			Usuarios U
			WHERE H.unidad=U.unidad
			AND H.id_proyecto=".$cualProyecto;

//B�squeda de las horas facturadas por persona
$sqlHora = "SELECT SUM(horas_registradas) AS horasReg
			FROM Horas
			WHERE id_proyecto=".$cualProyecto;
if ($pMes == "") {
	$sqlHora = $sqlHora." AND month(fecha) = month(getdate()) " ;
	$sqlHora = $sqlHora." AND year(fecha) = year(getdate()) " ;
	$mesEnvio = date("m");
	$anoEnvio=date("Y");
}
else {
	$sqlHora = $sqlHora." AND month(fecha) = " . $pMes;
	$sqlHora = $sqlHora." AND year(fecha) = " . $pAno;
	$mesEnvio =  $pMes;
	$anoEnvio= $pAno;
}

//B�squeda de la informaci�n del resumen
$sqlRes = "SELECT * FROM Horas
		   WHERE id_proyecto=".$cualProyecto;

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
		//Seleccionar el mes cuando se carga la p�gina por primera vez
		//si no cuando se recarga la p�gina
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
		//Generar los a�os de 2006 a 2050
		for($i=2006; $i<=2050; $i++) { 
			
			//seleccionar el a�o cuando se carga la p�gina por primera vez
			if ($pAno == "") {
				$AnoActual=date("Y"); //el a�o actual
			}
			else {
				$AnoActual= $pAno; //el a�o seleccionado
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
		<td width="10%" rowspan="2" valign="bottom" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar" /></td>
	  </tr>
	  <tr>
		<td align="right" class="TituloTabla">Actividad</td>
		<? //B�squeda de las actividades para mostrar en el men�
		$sqlActMen = "SELECT * FROM Actividades
					  WHERE id_proyecto=".$cualProyecto;;
		$cursorActMen = mssql_query($sqlActMen);
		?>
		<td colspan="3" class="TxtTabla">&nbsp;      <select name="pActividad" class="CajaTexto" id="pActividad">
			<option value=""></option>
			<?
			while($regActMen = mssql_fetch_array($cursorActMen))
			{
				$selAct = "";
				if($pActividad == $regActMen[id_actividad])
				{
					$selAct = "selected";
				}
				
				$nomActiv = $regActMen[macroactividad]." ".$regActMen[nombre];
				?>
				<option value="<? echo $regActMen[id_actividad]; ?>" <? echo $selAct; ?>><? echo $nomActiv; ?></option>
			<?
			}
			?>
		</select></td>
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
		<td width="10%" align="center"><input name="Submit2" type="button" class="Boton" onclick="MM_openBrWindow('reporteFactProyectoExcel4.php?proy=<?=$elIDProyecto;?>&mes=<?=$pMes;?>&anio=<?=$pAno;?>&act=<?=$pActividad;?>','','scrollbars=yes,resizable=yes,width=800,height=600')" value="Generar XLS" /></td>
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
	
	<!-- T�tulo -->
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="TituloUsuario">Reporte de facturaci&oacute;n </td>
	  </tr>
	</table>

	<!-- Tabla de facturaci�n -->
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
	  <tr class="TituloTabla2">
		<td width="25%">Actividad</td>
		<td><table width="100%" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <tr class="TituloTabla2">
            <td width="27%">Participante</td>
            <td width="8%">Total facturado </td>
            <td>Resumen de horas </td>
          </tr>
        </table></td>
	  </tr>
	  
	  <? while ($regAct = mssql_fetch_array($cursorAct)){ 
	  	  $sqlPart1 = $sqlPart." AND H.id_actividad=".$regAct[id_actividad];
		  $cursorPart = mssql_query($sqlPart1);
		  
		  $sqlHora1 = $sqlHora." AND id_actividad=".$regAct[id_actividad];
		  $cursorHora1 = mssql_query($sqlHora1);
		  $regHora1 = mssql_fetch_array($cursorHora1);
		  	  
	  	  if($regHora1[horasReg] != NULL)
		  {
	  ?>
		  <tr class="TxtTabla">
			<td width="25%" valign="top"><? echo $regAct[macroactividad]." ".$regAct[nombre]; ?></td>
			<td><table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
			  <? while ($regPart = mssql_fetch_array($cursorPart)){
			  	  $sqlHora2 = $sqlHora1." AND id_actividad=".$regAct[id_actividad]."
				  						 AND unidad=".$regPart[unidad];
				  $cursorHora = mssql_query($sqlHora2);
				  $regHora = mssql_fetch_array($cursorHora);
				  
				  //B�squeda de la informaci�n del resumen
				  $sqlRes1 = $sqlRes." AND id_actividad=".$regAct[id_actividad]."
				  					   AND unidad=".$regPart[unidad];
				  if ($pMes == "") {
					$sqlRes1 = $sqlRes1." AND month(fecha) = month(getdate()) " ;
					$sqlRes1 = $sqlRes1." AND year(fecha) = year(getdate()) " ;
				  }
				  else {
					$sqlRes1 = $sqlRes1." AND month(fecha) = " . $pMes;
					$sqlRes1 = $sqlRes1." AND year(fecha) = " . $pAno;
				  }
				  $cursorRes = mssql_query($sqlRes1);
			  
			  
			  	  if($regHora[horasReg] != NULL)
				  {
			  ?>
				  <tr class="TxtTabla">
					<td width="27%" valign="top"><? echo ucwords(strtolower($regPart[nombre]." ".$regPart[apellidos])); ?></td>
					<td width="8%" valign="top"><? echo $regHora[horasReg]; ?></td>
					<td><table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
					  <tr class="TituloTabla2">
                        <td width="7%">Loc</td>
                        <td width="7%">Clase tiempo </td>
                        <td width="7%">D&iacute;a</td>
                        <td width="7%">Horas</td>
                        <td>Resumen</td>
                      </tr>
					  <? while($regRes = mssql_fetch_array($cursorRes)){?>
					  <tr class="TxtTabla">
					    <td width="7%" align="right"><? echo $regRes[localizacion]; ?></td>
					    <td width="7%" align="right"><? echo $regRes[clase_tiempo]; ?></td>
					    <td width="7%" align="right"><? echo date("d", strtotime($regRes[fecha])); ?></td>
					    <td width="7%" align="right"><? echo $regRes[horas_registradas]; ?></td>
					    <td><? echo $regRes[resumen_trabajo]; ?></td>
				      </tr>
					  <? }//cierra while($regRes=mssql_fetch_array($cursorRes))?>
                    </table></td>
				  </tr>
			  <? 
			  	  }//cierra if($regHora[horasReg] != NULL)
			  }//cierra while($regPart=mssql_fetch_array($cursorPart)) ?>
			</table></td>
		  </tr>
	  <? }//cierra if($regHora1[horasReg] != NULL)
	  }//cierre while($regAct=mssql_fetch_array($cursorAct)) ?>
</table>

	
	<!-- Tabla de facturaci�n -->
	<!--
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
	  <tr class="TituloTabla2">
		<td width="25%" height="26">Actividad </td>
	    <td width="30%" rowspan="2" valign="top" class="TxtTabla"><table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <tr class="TituloTabla2">
            <td rowspan="2">Participantes</td>
            <td rowspan="2">Total Facturado </td>
            <td colspan="6">Resumen horas </td>
          </tr>
          <tr class="TituloTabla2">
            <td>Loc</td>
            <td>Clase tiempo </td>
            <td>Cargo</td>
            <td>Dia</td>
            <td>Hora</td>
            <td>Resumen</td>
          </tr>
          <tr class="TxtTabla">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
	  
	  <? /*while ($regAct = mssql_fetch_array($cursorAct)){ 
	  	  $sqlPart1 = $sqlPart." AND H.id_actividad=".$regAct[id_actividad];
		  $cursorPart = mssql_query($sqlPart1);
	  ?>
		  <tr class="TxtTabla">
			<td width="25%"><? echo $regAct[macroactividad]; ?></td>
		  </tr>
	  <? }*///cierre while($regAct=mssql_fetch_array($cursorAct)) ?>
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
	  //while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? //echo $reg[unidad]; ?></td>
        <td><? //echo ucwords(strtolower($reg[nombre] . " " . $reg[apellidos])); ?></td>
        <td width="60%" align="center">
		<?
		//Trae las actividades en que un usuario tiene asignaci�n y las respectivas horas asignadas
		/*$sQry="select A.* , B.nombre nomActividad ";
		$sQry=$sQry." from asignaciones A, actividades B ";
		$sQry=$sQry." where A.id_proyecto = B.id_proyecto ";
		$sQry=$sQry." and A.id_actividad = B.id_actividad ";
		$sQry=$sQry." and A.unidad = " . $reg[unidad];  
		$sQry=$sQry." and A.id_proyecto =" . $elIDProyecto;
		$sQry=$sQry." and month(A.fecha_inicial) = " . $mesEnvio;
		$sQry=$sQry." and year(A.fecha_inicial) =" . $anoEnvio;
		$cursorQry = mssql_query($sQry);*/
//		echo $sQry . "<br>" ;
		?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" bgcolor="#FFFFFF" class="TxtTabla">
	  <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
		   <?
		  //while ($regQry=mssql_fetch_array($cursorQry)) {
		  ?>
		
          <tr>
            <td align="left"><? //echo ucwords(strtolower($regQry[nomActividad])); ?></td>
            <td width="8%" align="right"><? //echo $regQry[localizacion]; ?></td>
            <td width="8%" align="right"><? //echo $regQry[clase_tiempo]; ?></td>
            <td width="15%" align="right"><? //echo $regQry[cargo]; ?></td>
            <td width="15%" align="right"><? //echo $regQry[tiempo_asignado]; ?></td>
            <td width="15%" align="right">
			<?
			//Trae el valor facturado por actividad en el mes seleccionado
			/*$sumaporActividad = 0;
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
		  <? }*/ ?>
        </table>	</td>
  </tr>
</table>		</td>
        <td width="5%" align="center">
		<? //echo $reg[totalHorasR]; ?>
		</td>
        </tr>
	  <? //} ?>
    </table>
	-->
	
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
