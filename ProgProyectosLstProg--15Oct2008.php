<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Listado de personas programadas en un proyecto
$sql="SELECT DISTINCT A.unidad, U.nombre, U.apellidos, U.id_categoria, C.nombre nomCategoria, ";
$sql=$sql." D.nombre nomDpto ";
$sql=$sql." FROM asignaciones A, Usuarios U, Categorias C, Departamentos D ";
$sql=$sql." where A.unidad = U.unidad ";
$sql=$sql." AND U.id_categoria = C.id_categoria ";
$sql=$sql." AND U.id_departamento = D.id_departamento ";
$sql=$sql." AND A.id_proyecto = " . $lstProyecto ;
$sql=$sql." order by U.apellidos ";
$cursor = mssql_query($sql);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Listado de usuarios </td>
      </tr>
</table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Unidad</td>
        <td width="25%">Nombre</td>
        <td width="5%">Cat</td>
        <td><table width="100%"  border="1" cellspacing="1" cellpadding="0">
          <tr>
            <td width="10%">ID</td>
            <td>Actividades</td>
            <td width="15%">Horas Prog </td>
            <td width="7%">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <? while ($reg=mssql_fetch_array($cursor)) {  ?>
      <tr class="TxtTabla">
        <td width="5%"><? echo  $reg[unidad] ; ?></td>
        <td width="25%"><? echo  ucwords(strtolower($reg[apellidos])) . ", " . ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="5%"><? echo  strtoupper($reg[nomCategoria]) ; ?></td>
        <td>
		<?
		//--Listado de actividades por persona
		$sqlA="select A.id_actividad , B.nombre nomActividad, sum(A.tiempo_asignado) horasProg ";
		$sqlA=$sqlA." from asignaciones A, Actividades B ";
		$sqlA=$sqlA." where A.id_proyecto = B.id_proyecto ";
		$sqlA=$sqlA." AND A.id_actividad = B.id_actividad ";
		$sqlA=$sqlA." and A.id_proyecto = " . $lstProyecto ;
		$sqlA=$sqlA." and A.unidad =" . $reg[unidad] ;
		$sqlA=$sqlA." group by A.id_actividad, B.nombre ";
		$cursorA = mssql_query($sqlA);
		?>
		<table width="100%"  border="1" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF">
          <? while ($regA=mssql_fetch_array($cursorA)) {  
		  $msjHoras="";
		  //Trae la información de Clase de tiempo, localizacion, periodo y horas por cada persona
		  //--Cantidad de horas programadas por actividad
		  $sqlH="select clase_tiempo, localizacion, month(fecha_inicial) mes, year(fecha_inicial) vigencia, tiempo_asignado from asignaciones ";
		  $sqlH=$sqlH." where id_proyecto = " . $lstProyecto ;
		  $sqlH=$sqlH." and unidad = " . $reg[unidad] ;
		  $sqlH=$sqlH." and id_actividad = " . $regA[id_actividad];
		  $cursorH = mssql_query($sqlH);
		  while ($regH=mssql_fetch_array($cursorH)) { 
		  	$nomMes="";
		  	switch ($regH[mes]) {
				case 1:
					$nomMes="Ene";
					break;
				case 2:
					$nomMes="Feb";
					break;
				case 3:
					$nomMes="Mar";
					break;
				case 4:
					$nomMes="Abr";
					break;
				case 5:
					$nomMes="May";
					break;
				case 6:
					$nomMes="Jun";
					break;
				case 7:
					$nomMes="Jul";
					break;
				case 8:
					$nomMes="Ago";
					break;
				case 9:
					$nomMes="Sep";
					break;
				case 10:
					$nomMes="Oct";
					break;
				case 11:
					$nomMes="Nov";
					break;
				case 12:
					$nomMes="Dic";
					break;
			}
			
		  	$msjHoras=$msjHoras."[CT=".$regH[clase_tiempo]." L=".$regH[localizacion]." - ".$nomMes."-".$regH[vigencia]."=".$regH[tiempo_asignado]."] 
";
		  }
		  ?>
		  <tr>
		    <td width="10%"><? echo $regA[id_actividad] ; ?></td>
            <td><? echo $regA[nomActividad] ; ?></td>
            <td width="15%" align="right"><? echo $regA[horasProg] ; ?></td>
            <td width="7%" align="center"><img src="img/images/ver.gif" alt="<? echo $msjHoras; ?>" width="16" height="16" /></td>
		  </tr>
		  <? } ?>
        </table></td>
      </tr>
      <tr class="TituloTabla">
        <td height="1"> </td>
        <td height="1"> </td>
        <td height="1"> </td>
        <td height="1"> </td>
      </tr>
		<? } ?>
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
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_callJS('window.close()')" value="Cerrar ventana" />
    </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
