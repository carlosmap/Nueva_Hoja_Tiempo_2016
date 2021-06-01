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
$sql01=$sql01." AND A.unidad = " . $unidad_u ;
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
$sql02=$sql02." AND A.unidad = " . $unidad_u ;
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
$sql09=$sql09." AND A.unidad = " . $unidad_u ;
$sql09=$sql09." AND A.mes = " . $pMes ;
$sql09=$sql09." AND A.vigencia = "  . $pAno ;
$cursor09 =	 mssql_query($sql09);

$cur_usu=mssql_query("select nombre,apellidos from Usuarios where unidad=".$unidad_u);
$dato_usu=mssql_fetch_array($cur_usu);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--


function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
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
        <td align="right" class="Fecha"></td>
      </tr>
</table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
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

    <td class="TituloUsuario">   .:: Planeaci&oacute;n de proyectos para <? echo strtoupper($dato_usu["nombre"]." ".$dato_usu["apellidos"]); 	?></td>
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
            <td>&nbsp;</td>
          </tr>
		  <?
			while ($reg01 = mssql_fetch_array($cursor01)) {
		  ?>
          <tr class="TxtTabla">
            <td><? echo " [" . $reg01['codigo'] . "." . $reg01['cargo_defecto'] . "] " . $reg01['nombre'] ; ?></td>
            <td><? echo $reg01['totHombresMes']; ?></td>
            <td><? echo $reg01['totHorasMes']; ?></td>
            <td align="center">&nbsp;</td>
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
            </tr>
			<?
			while ($reg09 = mssql_fetch_array($cursor09)) {
			?>			
            <tr class="TxtTabla">
              <td><? echo "[" . $reg09['codigo'] . "." . $reg09['cargo_defecto'] . "] " . $reg09['nombre'];  ?></td>
            </tr>
			<?
			} //Cierra while $curso09 
			?>			
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
              <td colspan="4">Resumen</td>
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
				$sql06=$sql06." WHERE unidad = " . $unidad_u ;
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
              <td colspan="4" class="TxtTabla">
			  <?
//  			if (trim($totalResumenRegistro) != "") {
//					echo $totalResumenRegistro;
//				}

			  //--Trae el resumen de un proyecto SIN REPETIR una persona para una actividad, un mes y año específicos			  
			  $sql08="SELECT DISTINCT resumen  ";
			  $sql08=$sql08." FROM FacturacionProyectos ";
			  $sql08=$sql08." WHERE unidad = " . $unidad_u ;
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
				$sql07=$sql07." WHERE unidad =" . $unidad_u ;
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
              <td width="1%" class="TituloTabla2">&nbsp;</td>
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
