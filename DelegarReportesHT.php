<?php

//hecho por Omar Osuna
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Trae la información de la divisiones que tiene a cargo
//16Jul2007
$sql="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
$sql=$sql." from divisiones D, Usuarios U " ;
$sql=$sql." where D.id_director *= U.unidad " ;
//$sql=$sql." and D.id_director = " . $laUnidad; 
//14Ago2012
//PBM
//La anterior línea se cambió para que los subdirectores de división tambien tengan acceso a este reporte.
$sql=$sql." and (D.id_director = " . $laUnidad; 
$sql=$sql." or D.id_subdirector = " . $laUnidad . ") "; 
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDDivision = $reg[id_division];
	$elNomDivision = $reg[nombre];
	$elNomDirector = $reg[nomDir] . " " . $reg[apeDir];
}


//genera la consulta para traer los usuarios delegados para generar los reportes
//8 de abril 2013






//--PROYECTOS en los que ha participado la división AMBIENTAL



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="DelegarReportes";
</script><SCRIPT language=JavaScript>
<!--	



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
		<div align="center"> 
		 Delegar Reportes De Facturación <br> Director de división
		</div>
</div>
<form action="" method="post" name="form1">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>


	</td>
  </tr>
</table>
<?
$sql="select	a.unidadQueDelega, 
		b.nombre nombre1,b.apellidos apellido1,
		a.unidadDelegada ,
		c.nombre nombre2,
		c.apellidos apellido2,
		a.estado,
		a.fechaCrea  
from dbo.DelegadosDivisionRpts a , usuarios b, usuarios c 
where	a.unidadQueDelega=b.unidad and
		a.unidadDelegada=c.unidad  and 
		";

$sql=$sql." a.id_division='".$elIDDivision."'";
//$sql=$sql." and D.id_director = " . $laUnidad; 
//14Ago2012
//PBM
//La anterior línea se cambió para que los subdirectores de división tambien tengan acceso a este reporte.

$cursor1 = mssql_query($sql);

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Divisi&oacute;n</td>
        <td>Director</td>
        </tr>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($elNomDivision)) ; ?></td>
        <td ><? echo ucwords(strtolower($elNomDirector)) ; ?></td>
        </tr>
		<tr>
		<td class="TxtTabla" colspan=2>&nbsp;
		
		</td>
		</tr>
    </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloUsuario">
        <td colspan="2">Quien Delega </td>
        <td colspan="4">Informaci&oacute;n del delegado </td>
        <td width="5%">&nbsp;</td>
      </tr>
      <tr class="TituloTabla2">
        <td>Unidad quien delega</td>
        <td>Nombre quien delega</td>
        <td>Unidad delegado</td>
        <td>Nombre de delegado</td>
        <td>Estado</td>
        <td>Fecha</td>
        <td width="5%">Editar</td>
        
        </tr>
        <? while ($reg=mssql_fetch_array($cursor1)){ ?> 
      <tr class="TxtTabla">
      
        <td><? echo $unidadQueDelega = $reg[unidadQueDelega]; ; ?></td>
        <td ><?  $nombreDelega = $reg[nombre1]. " " .$reg[apellido1] ; echo ucwords(strtolower($nombreDelega))?></td>
          <td><? echo $unidadDelegada = $reg[unidadDelegada] ; ?></td>
        <td ><? $nombre_delegado = $reg[nombre2] . " " . $reg[apellido2];  echo ucwords(strtolower($nombre_delegado))?></td>
          <td><? if ($reg[estado]=='A'){echo'ACTIVO';} else{echo'INACTIVO';}
		  
		  
          ?></td>
           <td><? echo $fecha_delegacion = $reg[fechaCrea]; ?></td>
          <td width="5%">
           <input name="Submit" type="button" class="Boton" onClick="MM_openBrWindow('upDelegarReportesHT.php?accion=1&id_division=<?=$elIDDivision?>&unidad=<?= $unidadDelegada?>','vAF','scrollbars=yes,resizable=yes,width=700,height=400')" value="Editar"></td>
       
        </tr>
        <? }?>
    </table>
    
	      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onclick="MM_openBrWindow('addDelegarReportesHT.php?accion=1','vAF','scrollbars=yes,resizable=yes,width=700,height=180')" value="Nuevo" /></td>
            </tr>
          </table></td>
      </tr>
    </table>
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td></td>
      </tr>
    </table>
  
    

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		</td>
      </tr>
    </table>
    </form>
</body>
</html>
