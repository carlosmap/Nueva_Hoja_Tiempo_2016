<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	

//24Jul2013
//PBM
//Inicializa el valor de las listas Mes y vigencia 
if ( (trim($pMes) == "") AND (trim($recarga)=="") ) {
	$pMes=date("n");
}
if ( (trim($pAno) == "") AND (trim($recarga)=="") ) {
	$pAno=date("Y");
}


//24Jul2013
//PBM
//--Trae la planeación de una persona para un mes y año seleccionados
$sql01="SELECT A.id_proyecto, A.unidad, A.vigencia, A.mes, SUM(A.hombresMes) totHombresMes, SUM(A.horasMes) totHorasMes, B.nombre, B.codigo, B.cargo_defecto ";
$sql01=$sql01." FROM PlaneacionProyectos A, Proyectos B " ;
$sql01=$sql01." WHERE A.id_proyecto = B.id_proyecto " ;
$sql01=$sql01." AND A.unidad = " . $laUnidad ;
$sql01=$sql01." AND A.vigencia = " . $pAno ;
$sql01=$sql01." AND A.mes = " . $pMes ;
$sql01=$sql01." GROUP BY A.id_proyecto, A.unidad, A.vigencia, A.mes, B.nombre, B.codigo, B.cargo_defecto " ;
$sql01=$sql01." ORDER BY B.nombre " ;
$cursor01 =	 mssql_query($sql01);

//25Jul2013
//PBM
//--Trae los proyectos en los que una persona tiene facturación agrupada así:
//--Proyecto, Actividad, Horario, clase de tiempo, localización, cargo
$sql02="SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  ";
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
$sql02=$sql02." AND A.unidad = " . $laUnidad ;
$sql02=$sql02." AND A.mes = " . $pMes ;
$sql02=$sql02." AND A.vigencia = " . $pAno ;
$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
$sql02=$sql02." ORDER BY B.nombre " ;
$cursor02 =	 mssql_query($sql02);


//--Traer la cantidad de días de un mes determinado
$cantElMes="";
$totalDiasMes = 0;
if (strlen($pMes) == 1) {
	$cantElMes = "0" . $pMes;
}
else {
	$cantElMes = "" . $pMes;
}
$sql04="select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$pAno."' + '".$cantElMes."' + '01')))) diasDelMes ";
$cursor04 =	 mssql_query($sql04);
if ($reg04 = mssql_fetch_array($cursor04)) {
	$totalDiasMes =  $reg04['diasDelMes'];
}


//--Trae los proyectos seleccionados sin planeación para su respectiva facturación
$sql09="SELECT A.* , B.nombre, B.codigo, B.cargo_defecto ";
$sql09=$sql09." FROM ProyectosSinPlaneacion A, Proyectos B " ;
$sql09=$sql09." WHERE A.id_proyecto = B.id_proyecto  " ;
$sql09=$sql09." AND A.unidad = " . $laUnidad ;
$sql09=$sql09." AND A.mes = " . $pMes ;
$sql09=$sql09." AND A.vigencia = "  . $pAno ;
$cursor09 =	 mssql_query($sql09);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winFacturacionHT";

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>


<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 557px; height: 30px;">
Hoja de tiempo - Facturaci&oacute;n de proyectos</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="Fecha"><? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
      </tr>
</table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="TituloUsuario">Criterios de consulta </td>
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
	$selMesTodos= "";
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		if ($pMes == "TODOS") {
			$selMesTodos= "selected"; //el mes seleccionado
		}
		else {
			$mesActual= $pMes; //el mes seleccionado
		}
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

    </select>
	<input name="recarga" type="hidden" id="recarga" value="1" />	</td>
    <td width="10%" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
</table></td>
      </tr>
    </table>

<script type="text/javascript" language="javascript">

function envia()
{
	document.form1.ban.value=2;


}

//-->
</script>



<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td align="center" class="TxtTabla">&nbsp;
	</td>
  </tr>
  <tr>
	<td height="1" align="center" class="TituloTabla"> </td>
  </tr>
</table>
<!-- No. de Registros -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">   .:: Planeaci&oacute;n de proyectos para <? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
  </tr>
  <tr>
    <td align="right" class="TxtTabla">

	</td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Proyecto</td>
            <td>Hombres/Mes</td>
            <td>Horas/Mes</td>
            <td width="5%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
          </tr>
		  <?
			while ($reg01 = mssql_fetch_array($cursor01)) {
		  ?>
          <tr class="TxtTabla">
            <td><? echo " [" . $reg01['codigo'] . "." . $reg01['cargo_defecto'] . "] " . $reg01['nombre'] ; ?></td>
            <td><? echo $reg01['totHombresMes']; ?></td>
            <td><? echo $reg01['totHorasMes']; ?></td>
            <td width="5%" align="center"><input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('addHTFacturacionProy.php?cualProyecto=<? echo $reg01['id_proyecto']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>&hayPlaneacion=1','wAFTP','scrollbars=yes,resizable=yes,width=1000,height=500')" value="Facturar" /></td>
            <td width="5%" align="center"><input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('htFacturacionDetPlan.php?cualProyecto=<? echo $reg01['id_proyecto']; ?>&cualTipoUsu=I','winHTDetP','scrollbars=yes,resizable=yes,width=1000,height=500')" value="Detalle Planeaci&oacute;n" /></td>
          </tr>
		  <?
		  }
		  ?>
        </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">.:: Proyectos no planeados </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr class="TituloTabla2">
              <td>Proyecto</td>
              <td width="5%">&nbsp;</td>
            </tr>
			<?
			while ($reg09 = mssql_fetch_array($cursor09)) {
			?>			
            <tr class="TxtTabla">
              <td><? echo "[" . $reg09['codigo'] . "." . $reg09['cargo_defecto'] . "] " . $reg09['nombre'];  ?></td>
              <td width="5%" align="center"><input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('addHTFacturacionProy.php?cualProyecto=<? echo $reg09['id_proyecto']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>&hayPlaneacion=0','wAFTP','scrollbars=yes,resizable=yes,width=1000,height=500')" value="Facturar" /></td>
            </tr>
			<?
			} //Cierra while $curso09 
			?>			
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit3" type="submit" class="Boton" onclick="MM_openBrWindow('addHTProySinPlan.php?cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','WinPsp','scrollbars=yes,resizable=yes,width=500,height=600')" value="Proyectos sin planeaci&oacute;n" /></td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">.:: Facturaci&oacute;n reportada </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr class="TituloTabla2">
              <td width="10%">Proyecto</td>
              <td width="10%">Actividad</td>
              <td width="8%">Horario</td>
              <td width="1%">Loc.</td>
              <td width="1%">CT</td>
			  <td width="1%">Cargo</td>
			  <?
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes
			  for ($d=1; $d<=$totalDiasMes; $d++) {
			  ?>
              <td width="1%"><? echo $d; ?></td>
			  <?
			  } //for d
			  ?>
              <td>Total</td>
              <td>VoBo</td>
              <td>Resumen</td>
              <td width="1%">&nbsp;</td>
            </tr>
		  <?
		  while ($reg02 = mssql_fetch_array($cursor02)) {
		  ?>
            <tr >
              <td width="10%" class="TxtTabla" ><? echo "<B>[" . $reg02['codigo'] . "." . $reg02['cargo_defecto'] . "]</B> " . strtoupper($reg02['nombre']) ; ?></td>
              <td width="10%" class="TxtTabla" ><? echo "<B>[" . $reg02['macroactividad'] . "]</B> " . strtoupper($reg02['nomActividad'])  ; ?></td>
              <td width="8%" class="TxtTabla" >
			  <? 
			  //Trae el Horario de lines a domingo
			  $cpHorario="";
			  $sql03="SELECT * FROM Horarios ";
			  $sql03=$sql03." WHERE IDhorario = " .$reg02['IDhorario'];
			  $cursor03 =	 mssql_query($sql03);
			  if ($reg03 = mssql_fetch_array($cursor03)) {
			  	$cpHorario="[". $reg03['Lunes'] . "-" . $reg03['Martes'] . "-" . $reg03['Miercoles'] . "-" . $reg03['Jueves'] . "-" . $reg03['Viernes'] . "-" . $reg03['Sabado'] . "-" . $reg03['Domingo'] . "] " ;
			  }
			  echo $cpHorario; 
			  ?>			  </td>
              <td width="1%" align="center" class="TxtTabla"><? echo $reg02['localizacion']; ?></td>
              <td width="1%" align="center" class="TxtTabla"><? echo trim(substr($reg02['descripcion'], 0, 2));  ?></td>
			  <td width="1%" align="center" class="TxtTabla"><? echo $reg02['cargo']; ?></td>
			  <?
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes 
			  $totalHorasRegistro = 0; //Para calcular la cantidad de horas totales por registro
			  $totalResumenRegistro = ""; //Para relacionar el resumen de todos los días con facturación
			  for ($d2=1; $d2<=$totalDiasMes; $d2++) {
			  
			  	//--Determina si el día es sábado, domingo, festivo o dia normal
			  	//--Domingo=1, Lunes = 2..., Sabado=7
				$fechaAconsultar=$pAno."-".$pMes."-".$d2;
				$esFestivo=0;
				$esDia=0;
				$usarClase="";
				$sql05 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
				$sql05 = $sql05 . " FROM Festivos ";
				$sql05 = $sql05 . " where fecha = '". $fechaAconsultar ."' ";
				$cursor05 =	 mssql_query($sql05);
				if ($reg05 = mssql_fetch_array($cursor05)) {
					$esFestivo=$reg05['hayFestivo'];
					$esDia=$reg05['diaSemana'];
				}
				
				//Es festivo
				if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {
					$usarClase="tdFestivo";
				}
				
				//Es dia Normal
				if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo == 0) ) {
					$usarClase="TxtTabla";
				}
				
				//Es sábado o domingo
				if ( ($esDia == 1) OR ($esDia ==7) ) {
					$usarClase="tdFinSemana";
				}
				
				//Trae la cantidad de horas para el día en el proyecto, actividad, Horario, localización, clase de tiempo, cargo y dia definido.
				//--Trae la facturación de una persona para un mes y año específicos
				//--id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, fechaFacturacion, IDhorario, clase_tiempo, localizacion, cargo, hombresMesF, horasMesF, 
				//--resumen, id_categoria, valorFacturado, salarioBase, tipoContrato, usuarioCrea, fechaCrea, usuarioMod, fechaMod
				$horasDia=0;
				$sql06="SELECT *  ";
				$sql06=$sql06." FROM FacturacionProyectos ";
				$sql06=$sql06." WHERE unidad = " . $laUnidad ;
				$sql06=$sql06." AND mes = " . $pMes ;
				$sql06=$sql06." AND vigencia = " . $pAno ;
				$sql06=$sql06." AND id_proyecto = " . $reg02['id_proyecto'] ;
				$sql06=$sql06." AND id_actividad = " . $reg02['id_actividad'] ;
				$sql06=$sql06." AND DAY(fechaFacturacion) = " . $d2 ;
				$sql06=$sql06." AND IDhorario = " . $reg02['IDhorario'] ;
				$sql06=$sql06." AND clase_tiempo = " . $reg02['clase_tiempo'] ;
				$sql06=$sql06." AND localizacion = " . $reg02['localizacion'] ;
				$sql06=$sql06." AND cargo = '" . $reg02['cargo'] . "' ";
				$cursor06 =	 mssql_query($sql06);
				if ($reg06 = mssql_fetch_array($cursor06)) {
					$horasDia=$reg06['horasMesF'];

					//Totaliza por registro
					$totalHorasRegistro = $totalHorasRegistro + $horasDia ;
					
					//Resumen total por registro
					$totalResumenRegistro = $totalResumenRegistro . "<br>". $reg06['resumen'] ; 
				}
			  ?>
              <td width="1%" align="right" class="<? echo $usarClase; ?>">
			  <?
  				if ($horasDia > 0) {
					echo number_format($horasDia, 0, ",", ".");
				}
			  ?>
			  </td>
			  <?
			  } //cierra for $d2
			  ?>
              <td align="right" class="TxtTabla">
			  <?
				if ($totalHorasRegistro > 0) {
					echo number_format($totalHorasRegistro, 0, ",", ".");
				}
			  ?>
			  </td>
              <td class="TxtTabla">y</td>
              <td class="TxtTabla">
			  <?
//  			if (trim($totalResumenRegistro) != "") {
//					echo $totalResumenRegistro;
//				}

			  //--Trae el resumen de un proyecto SIN REPETIR una persona para una actividad, un mes y año específicos			  
			  $sql08="SELECT DISTINCT resumen  ";
			  $sql08=$sql08." FROM FacturacionProyectos ";
			  $sql08=$sql08." WHERE unidad = " . $laUnidad ;
			  $sql08=$sql08." AND mes = " . $pMes ;
			  $sql08=$sql08." AND vigencia =" . $pAno ;
			  $sql08=$sql08." AND id_proyecto = " . $reg02['id_proyecto'] ;
			  $sql08=$sql08." AND id_actividad = " . $reg02['id_actividad'] ;
			  $sql08=$sql08." AND IDhorario = " . $reg02['IDhorario'] ;
			  $sql08=$sql08." AND clase_tiempo = " . $reg02['clase_tiempo'] ;
			  $sql08=$sql08." AND localizacion = " . $reg02['localizacion'] ;
			  $sql08=$sql08." AND cargo = '". $reg02['cargo'] ."' ";
			  $cursor08 =	 mssql_query($sql08);
			  while ($reg08 = mssql_fetch_array($cursor08)) {
					echo $reg08['resumen'] . "<br>";
				}
			  
			   
			  
			  ?>
			  </td>
              <td width="1%" class="TxtTabla"><img src="img/images/ver.gif" width="16" height="16" onclick="MM_openBrWindow('htVtnResumen.php?cualProyecto=<? echo $reg02['id_proyecto']; ?>&cualActiv=<? echo $reg02['id_actividad']; ?>&cualHorario=<? echo $reg02['IDhorario']; ?>&cualLocaliza=<? echo $reg02['localizacion']; ?>&cualClaseT=<? echo $reg02['clase_tiempo']; ?>&cualCargo=<? echo $reg02['cargo']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winvtnres1','scrollbars=yes,resizable=yes,width=400,height=600')" /></td>
            </tr>
			<? 
			} //while $reg02
			?>
			<tr class="TituloTabla2" >
              <td colspan="6" class="TituloTabla2" >TOTAL CLASES DE TIEMPO 1 - 2 - 3 Y 11 </td>
              <?
			  $totalHorasMensual=0;
			  for ($d3=1; $d3<=$totalDiasMes; $d3++) {
			  
			  	//--Determina si el día es sábado, domingo, festivo o dia normal
			  	//--Domingo=1, Lunes = 2..., Sabado=7
				$fechaAconsultar=$pAno."-".$pMes."-".$d3;
				$esFestivo=0;
				$esDia=0;
				$usarClase="";
				$sql05 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
				$sql05 = $sql05 . " FROM Festivos ";
				$sql05 = $sql05 . " where fecha = '". $fechaAconsultar ."' ";
				$cursor05 =	 mssql_query($sql05);
				if ($reg05 = mssql_fetch_array($cursor05)) {
					$esFestivo=$reg05['hayFestivo'];
					$esDia=$reg05['diaSemana'];
				}
				
				//Es festivo
				if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {
					$usarClase="tdFestivo";
				}
				
				//Es dia Normal
				if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo == 0) ) {
					$usarClase="TituloTabla2";
				}
				
				//Es sábado o domingo
				if ( ($esDia == 1) OR ($esDia ==7) ) {
					$usarClase="tdFinSemana";
				}

				//--Totaliza por día para clases de tiempo 1, 2, 3 y 11
				$totalDiario=0;
				$sql07="SELECT SUM(horasMesF) totDia  ";
				$sql07=$sql07." FROM FacturacionProyectos ";
				$sql07=$sql07." WHERE unidad =" . $laUnidad ;
				$sql07=$sql07." AND mes = " . $pMes ;
				$sql07=$sql07." AND vigencia = " . $pAno ;
				$sql07=$sql07." AND DAY(fechaFacturacion) = " . $d3 ;
				$sql07=$sql07." AND clase_tiempo IN (1, 2, 3, 11) ";
				$cursor07 =	 mssql_query($sql07);
				if ($reg07 = mssql_fetch_array($cursor07)) {
					$totalDiario=$reg07['totDia'];
					
					//Totaliza la sumatoria de todos los resultados para clase de tiempo 1, 2, 3 y 11
					$totalHorasMensual=$totalHorasMensual+$totalDiario;
				}

			  ?>
              <td align="right" class="<? echo $usarClase; ?>">
			  <?
				if ($totalDiario > 0) {
					echo number_format($totalDiario, 0, ",", ".");
				}
			  ?>
			  </td>
			  <?
			  } //Cierra el for d3
			  ?>
              <td align="right" class="TituloTabla2">
			  <?
				if ($totalHorasMensual > 0) {
					echo number_format($totalHorasMensual, 0, ",", ".");
				}
			  ?>
			  </td>
              <td class="TituloTabla2">&nbsp;</td>
              <td class="TituloTabla2">&nbsp;</td>
              <td width="1%" class="TituloTabla2">&nbsp;</td>
			</tr>
          </table></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><input name="Submit" type="submit" class="Boton" onclick="MM_callJS('window.close();')" value="Cerrar ventana" /></td>
  </tr>
</table>
</body>
</html>
