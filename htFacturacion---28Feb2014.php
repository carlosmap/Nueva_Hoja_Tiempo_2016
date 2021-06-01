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
//$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion , A.esInterno" ;
$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
$sql02=$sql02." AND A.unidad = " . $laUnidad ;
$sql02=$sql02." AND A.mes = " . $pMes ;
$sql02=$sql02." AND A.vigencia = " . $pAno ;
$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
//$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion, A.esInterno " ;
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
              <td width="5%">VoBo</td>
              <td>Resumen</td>
              <td width="3%">ADP</td>
              <td width="1%">&nbsp;</td>
              <td width="1%">&nbsp;</td>
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
              <td width="5%" class="TxtTabla">
			  <?
			  //Verifica si el proyecto ya tiene VoBo en la facturación
			  //id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, unidadEncargado, validaEncargado, comentaEncargado, fechaAprEnc, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			  $tieneVBproy="";
			  $fechaVBproy="";
			  $encargadoVBproy="";
			  $sql13 = "SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
			  $sql13 = $sql13 . " FROM VoBoFactuacionProyHT A, Usuarios B ";
			  $sql13 = $sql13 . " WHERE A.unidadEncargado = B.unidad ";
			  $sql13 = $sql13 . " AND A.id_proyecto = " . $reg02['id_proyecto'] ;
			  $sql13 = $sql13 . " AND A.id_actividad = " . $reg02['id_actividad'] ;
			  $sql13 = $sql13 . " AND A.unidad = " . $laUnidad ;
			  $sql13 = $sql13 . " AND A.vigencia = " . $pAno ;
			  $sql13 = $sql13 . " AND A.mes = " . $pMes ;
			  $sql13 = $sql13 . " AND A.esInterno = '" . $reg02['esInterno'] . "'";
			  $cursor13 =	 mssql_query($sql13);
		      if ($reg13 = mssql_fetch_array($cursor13)) {
			  		$tieneVBproy = $reg13['validaEncargado'];
	 			  	$fechaVBproy = date("M d Y ", strtotime($reg13['fechaAprEnc'])) ;
			  		//$encargadoVBproy = $reg13['apellidos'] . " " . $reg13['nombre'] ;
					$encargadoVBproy = $reg13['NombreCorto']  ;
			  }
			  
			  ?>
			  <? if ($tieneVBproy == '1') { ?>
              		<img src="img/images/Aprobado.gif" width="21" height="24" /> <br>
			  <? } ?>
			  <? if ($tieneVBproy == '0') { ?>
			  		<img src="img/images/NoAprobado.gif" /> <br>
			  <? } ?>
			  <?
			  		echo $fechaVBproy . "<br>";
					echo $encargadoVBproy . "<br>";
			  ?>
			  </td>
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
              <td width="3%" align="right" class="TxtTabla">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><?
			  //--Traer los ADP
			  $sql10="SELECT * FROM AdpHT ";
			  $sql10=$sql10." WHERE id_proyecto = " . $reg02['id_proyecto'];
			  $sql10=$sql10." AND unidad = " . $laUnidad ;
			  $sql10=$sql10." AND vigencia = " . $pAno ;
			  $sql10=$sql10." and mes = " . $pMes ;
			  $cursor10 =	 mssql_query($sql10);
			  while ($reg10 = mssql_fetch_array($cursor10)) {
					echo $reg10['adp'] . "<br>";
			  }
			  ?></td>
                  <td width="1%">
				  <?
				  //El ADP sólo aplica para los proyectos especiales
				  //56=vacaciones, 60=enfermedades, 61=accidentes de trabajo, 62=permisos pacto, 63=licencias, 64=sanciones, 65=ausencias, 71=cursos de capacitación
				  
				  if (($reg02['id_proyecto'] == 56) OR ($reg02['id_proyecto'] == 60) OR ($reg02['id_proyecto'] == 61) OR ($reg02['id_proyecto'] == 62) OR ($reg02['id_proyecto'] == 63) OR ($reg02['id_proyecto'] == 64) OR ($reg02['id_proyecto'] == 65) OR ($reg02['id_proyecto'] == 71) ) {
				  ?>
				  <img src="img/images/alertaAzul.gif" alt="ADP" width="15" height="16" onclick="MM_openBrWindow('htVtnAdp.php?cualProyecto=<? echo $reg02['id_proyecto']; ?>&cualActiv=<? echo $reg02['id_actividad']; ?>&cualHorario=<? echo $reg02['IDhorario']; ?>&cualLocaliza=<? echo $reg02['localizacion']; ?>&cualClaseT=<? echo $reg02['clase_tiempo']; ?>&cualCargo=<? echo $reg02['cargo']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winvtnres1','scrollbars=yes,resizable=yes,width=400,height=600')" />
				  <?
				  }
				  ?>
				  
				  </td>
                </tr>
              </table>
</td>
              <td width="1%" class="TxtTabla"><img src="img/images/icoCuantia.gif" alt="Vi&aacute;ticos" width="16" height="16" onclick="MM_openBrWindow('addHtViaticosP.php?cualProyecto=<? echo $reg02['id_proyecto']; ?>&cualActiv=<? echo $reg02['id_actividad']; ?>&cualHorario=<? echo $reg02['IDhorario']; ?>&cualLocaliza=<? echo $reg02['localizacion']; ?>&cualClaseT=<? echo $reg02['clase_tiempo']; ?>&cualCargo=<? echo $reg02['cargo']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winvtnres1','scrollbars=yes,resizable=yes,width=500,height=580')" /></td>
              <td width="1%" class="TxtTabla"><img src="img/images/ver.gif" alt="Resumen" width="16" height="16" onclick="MM_openBrWindow('htVtnResumen.php?cualProyecto=<? echo $reg02['id_proyecto']; ?>&cualActiv=<? echo $reg02['id_actividad']; ?>&cualHorario=<? echo $reg02['IDhorario']; ?>&cualLocaliza=<? echo $reg02['localizacion']; ?>&cualClaseT=<? echo $reg02['clase_tiempo']; ?>&cualCargo=<? echo $reg02['cargo']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winvtnres1','scrollbars=yes,resizable=yes,width=400,height=600')" /></td>
              <td width="1%" class="TxtTabla"><img src="img/images/Del.gif" alt="Eliminar Facturaci&oacute;n" width="14" height="13" onclick="MM_openBrWindow('delHTporReg.php?cualProyecto=<? echo $reg02['id_proyecto']; ?>&cualActiv=<? echo $reg02['id_actividad']; ?>&cualHorario=<? echo $reg02['IDhorario']; ?>&cualLocaliza=<? echo $reg02['localizacion']; ?>&cualClaseT=<? echo $reg02['clase_tiempo']; ?>&cualCargo=<? echo $reg02['cargo']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winDelReg','scrollbars=yes,resizable=yes,width=700,height=500')" /></td>
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
              <td width="5%" class="TituloTabla2">&nbsp;</td>
              <td class="TituloTabla2">&nbsp;</td>
              <td width="3%" class="TituloTabla2">&nbsp;</td>
              <td width="1%" class="TituloTabla2">&nbsp;</td>
              <td width="1%" class="TituloTabla2">&nbsp;</td>
			  <td width="1%" class="TituloTabla2">&nbsp;</td>
			</tr>
<tr >
			   <td colspan="6" class="TituloUsuario" > RELACI&Oacute;N DE VIÁTICOS</td>
			   <?
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes 
			  for ($d3=1; $d3<=$totalDiasMes; $d3++) {
			  ?>
              <td width="1%" align="right" class="TituloUsuario">&nbsp;
			  
			  </td>
			  <?
			  } //cierra for $d3
			  ?>
			   <td colspan="7" align="right" class="TituloUsuario">&nbsp;</td>
		    </tr>			
			<?
			//Relación de viáticos
			//26Feb2014
			//PBM
			//--Trae la configuración de los viáticos registrados en la Hoja de tiempo
			$sql11 = "SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.esInterno, A.IDhorario, A.clase_tiempo, A.localizacion,  ";
			$sql11 = $sql11 . " A.cargo, A.IDsitio, A.IDTipoViatico, ";
			$sql11 = $sql11 . " B.nombre nomProyecto, B.codigo, B.cargo_defecto, ";
			$sql11 = $sql11 . " C.nombre nomActividad, C.macroactividad, ";
			$sql11 = $sql11 . " D.Lunes, D.Martes, D.Miercoles, D.Jueves, D.Viernes, D.Sabado, D.Domingo, ";
			$sql11 = $sql11 . " E.NomSitio, ";
			$sql11 = $sql11 . " F.NomTipoViatico ";
			$sql11 = $sql11 . " FROM ViaticosProyectosHT A, Proyectos B, Actividades C, Horarios D, SitiosTrabajo E, TiposViatico F ";
			$sql11 = $sql11 . " WHERE A.id_proyecto = B.id_proyecto ";
			$sql11 = $sql11 . " AND A.id_proyecto = C.id_proyecto ";
			$sql11 = $sql11 . " AND A.id_actividad = C.id_actividad ";
			$sql11 = $sql11 . " AND A.IDhorario = D.IDhorario ";
			$sql11 = $sql11 . " AND A.id_proyecto = E.id_proyecto ";
			$sql11 = $sql11 . " AND A.IDsitio = E.IDsitio ";
			$sql11 = $sql11 . " AND A.IDTipoViatico = F.IDTipoViatico ";
			$sql11 = $sql11 . " AND A.vigencia = " . $pAno ;
			$sql11 = $sql11 . " AND A.mes = " . $pMes;
			$sql11 = $sql11 . " AND A.unidad = " . $laUnidad ;
			$cursor11 =	 mssql_query($sql11);
			while ($reg11 = mssql_fetch_array($cursor11)) {
			
				//--Trae los viáticos de una configutación dada ordenado por fecha de inicio
				$sql12 = " SELECT *, DAY(FechaIni) diaIniV,  DAY(FechaFin) diaFinV ";
				$sql12 = $sql12 . " FROM ViaticosProyectosHT ";
				$sql12 = $sql12 . " WHERE id_proyecto = " . $reg11['id_proyecto'] ;
				$sql12 = $sql12 . " AND id_actividad = " . $reg11['id_actividad'] ;
				$sql12 = $sql12 . " AND vigencia = " . $reg11['vigencia'] ;
				$sql12 = $sql12 . " AND mes = " . $reg11['mes'] ;
				$sql12 = $sql12 . " AND esInterno = '" . $reg11['esInterno'] . "' ";
				$sql12 = $sql12 . " AND IDhorario = " . $reg11['IDhorario'] ;
				$sql12 = $sql12 . " AND clase_tiempo =" . $reg11['clase_tiempo'] ;
				$sql12 = $sql12 . " AND localizacion = " . $reg11['localizacion'] ;
				$sql12 = $sql12 . " AND cargo = '" . $reg11['cargo'] . "' ";
				$sql12 = $sql12 . " AND IDsitio = " . $reg11['IDsitio'] ;
				$sql12 = $sql12 . " AND IDTipoViatico = " . $reg11['IDTipoViatico'] ;
				$sql12 = $sql12 . " AND unidad = " . $laUnidad ;
				$sql12 = $sql12 . " order by fechaIni ";
				$cursor12 =	 mssql_query($sql12);
				$arrayViaticos = array();
				$aV=0;
				$totCantViaticos=0;
				//Llenar el array con 0
				for($aV=0; $aV<=$totalDiasMes; $aV++) {
					$arrayViaticos[$aV] = '&nbsp;';
				}
				while ($reg12 = mssql_fetch_array($cursor12)) {
//					echo $reg12['diaIniV'] . " - " . $reg12['diaFinV'] . "<br>";
					for($aV2=$reg12['diaIniV']; $aV2<=$reg12['diaFinV']; $aV2++) {
					 	$arrayViaticos[$aV2] =  $reg12['viaticoCompleto'] ;
						$totCantViaticos = $totCantViaticos + 1;
					}
				}
			?>
			 <tr >
			   <td class="TxtTabla" ><? echo $reg11['nomProyecto']; ?></td>
			   <td class="TxtTabla" ><? echo "<B>[" . $reg11['macroactividad'] . "]</B> " . strtoupper($reg11['nomActividad'])  ; ?></td>
			   <td class="TxtTabla" ><? // echo "[". $reg11['Lunes'] . "-" . $reg11['Martes'] . "-" . $reg11['Miercoles'] . "-" . $reg11['Jueves'] . "-" . $reg11['Viernes'] . "-" . $reg11['Sabado'] . "-" . $reg11['Domingo'] . "] " ; ?></td>
			   <td align="center" class="TxtTabla">&nbsp;</td>
			   <td align="center" class="TxtTabla">&nbsp;</td>
			   <td align="center" class="TxtTabla"><? echo $reg11['NomSitio']; ?></td>
			   			  <?
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes 
			  $totalHorasRegistro = 0; //Para calcular la cantidad de horas totales por registro
			  $totalResumenRegistro = ""; //Para relacionar el resumen de todos los días con facturación
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
					$usarClase="TxtTabla";
				}
				
				//Es sábado o domingo
				if ( ($esDia == 1) OR ($esDia ==7) ) {
					$usarClase="tdFinSemana";
				}
				

			  ?>
              <td width="1%" align="right" class="<? echo $usarClase; ?>">
			  <?
  				echo $arrayViaticos[$d3] ;
			  ?>
			  </td>
			  <?
			  } //cierra for $d3
			  ?>
			   <td align="right" class="TxtTabla"><? echo $totCantViaticos; ?></td>
			   <td width="5%" class="TxtTabla">
			   <?
			   	//--Verifica si los viáticos ya tiene VoBo 
				//--dbo.VoBoViaticosProyHT
				//--id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, IDhorario, clase_tiempo, localizacion, cargo, IDsitio, 
				//--IDTipoViatico, unidadEncargado, validaEncargado, comentaEncargado, fechaAprueba, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			   $tieneVBviatico="";
			   $fechaVBviatico="";
			   $encargadoVBviatico="";
			   $sql14="SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
			   $sql14=$sql14." FROM VoBoViaticosProyHT A, Usuarios B ";
			   $sql14=$sql14." WHERE A.unidad = B.unidad ";
			   $sql14=$sql14." AND A.id_proyecto = " . $reg11['id_proyecto'] ;
			   $sql14=$sql14." AND A.id_actividad = " . $reg11['id_actividad'] ;
			   $sql14=$sql14." AND A.unidad = " . $laUnidad ;
			   $sql14=$sql14." AND A.vigencia = " . $reg11['vigencia'] ;
			   $sql14=$sql14." AND A.mes = " . $reg11['mes'] ;
			   $sql14=$sql14." AND A.esInterno = '" . $reg11['esInterno'] . "' ";
			   $sql14=$sql14." AND A.IDhorario = " . $reg11['IDhorario'] ;
			   $sql14=$sql14." AND A.clase_tiempo = " . $reg11['clase_tiempo'] ;
			   $sql14=$sql14." AND A.localizacion = " . $reg11['localizacion'] ;
			   $sql14=$sql14." AND A.cargo = '" . $reg11['cargo'] . "' ";
			   $sql14=$sql14." AND A.IDsitio = " . $reg11['IDsitio'] ;
			   $sql14=$sql14." AND A.IDTipoViatico = " . $reg11['IDTipoViatico'] ;
			   $cursor14 =	 mssql_query($sql14);
		       if ($reg14 = mssql_fetch_array($cursor14)) {
				   $tieneVBviatico=$reg14['validaEncargado'];
				   $fechaVBviatico= date("M d Y ", strtotime($reg14['fechaAprueba'])) ;
				   //$encargadoVBviatico = $reg14['apellidos'] . " " . $reg14['nombre'] ;
				   $encargadoVBviatico=$reg14['NombreCorto']  ;
			   }


			   ?>
				<? if ($tieneVBviatico == '1') { ?>
              		<img src="img/images/Aprobado.gif" width="21" height="24" /> <br>
			  <? } ?>
			  <? if ($tieneVBviatico == '0') { ?>
			  		<img src="img/images/NoAprobado.gif" /> <br>
			  <? } ?>
			  <?
			  		echo $fechaVBviatico . "<br>";
					echo $encargadoVBviatico . "<br>";
			  ?>
  			   </td>
			   <td class="TxtTabla"><? echo $reg11['NomTipoViatico']; ?></td>
			   <td align="right" class="TxtTabla">&nbsp;</td>
			   <td class="TxtTabla">&nbsp;</td>
			   <td class="TxtTabla">&nbsp;</td>
			   <td class="TxtTabla">
			   <img src="img/images/Del.gif" alt="Eliminar Vi&aacute;tcos" width="14" height="13" onclick="MM_openBrWindow('delHTviaticosP.php?cualProyecto=<? echo $reg11['id_proyecto']; ?>&cualActiv=<? echo $reg11['id_actividad']; ?>&cualHorario=<? echo $reg11['IDhorario']; ?>&cualLocaliza=<? echo $reg11['localizacion']; ?>&cualClaseT=<? echo $reg11['clase_tiempo']; ?>&cualCargo=<? echo $reg11['cargo']; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>&cualSitio=<? echo $reg11['IDsitio']; ?>&cualTipoViatico=<? echo $reg11['IDTipoViatico']; ?>','winDelReg','scrollbars=yes,resizable=yes,width=700,height=500')" /></td>
		    </tr>
	<? 
	} //Cierra while $reg11
	// Cierra relación de viáticos 
	?>			
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit6" type="submit" class="Boton" onclick="MM_openBrWindow('pdfHtUsuario.php?laUnidad=<? echo $laUnidad; ?>&cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winPDFht','scrollbars=yes,resizable=yes,width=700,height=500')" value="Generar PDF" />                
              <input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('delHTtodaHT.php?cualVigencia=<? echo $pAno; ?>&cualMes=<? echo $pMes; ?>','winDelHTHT','scrollbars=yes,resizable=yes,width=1400,height=500')" value="Eliminar toda la Hoja de tiempo" /></td></tr>
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
