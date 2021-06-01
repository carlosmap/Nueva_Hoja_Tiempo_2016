<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador = C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

//06Mar2008
//Trae los programadores que incluyeron suma global al proyecto seleccionado
$sql2="select *, U.nombre, U.apellidos ";
$sql2=$sql2." from ProgSumaGlobal P, Usuarios U ";
$sql2=$sql2." where P.unidadProgramador = U.unidad ";
$sql2=$sql2." AND P.id_proyecto =". $cualProyecto ;
$sql2=$sql2." order by apellidos ";
$cursor2 = mssql_query($sql2);

$sql3="select *, U.nombre, U.apellidos ";
$sql3=$sql3." from ProgAsignaRecursos  P, Usuarios U " ;
$sql3=$sql3." where P.unidadProgramador = U.unidad " ;
$sql3=$sql3." AND P.id_proyecto =". $cualProyecto ;
$sql3=$sql3." order by apellidos ";
$cursor3 = mssql_query($sql3);


//Trae la información de la bitácora de la suma global
//secuencia, fecha, id_proyecto, unidadProgramador, comentaProgramador, unidadProyecto, comentaProyecto
$sqlB="SELECT B.*, P.nombre, P.apellidos, C.nombre nomCoordina, C.apellidos apeCoordina  ";
$sqlB=$sqlB." FROM BitacoraSumaGlobal B, Usuarios P, Usuarios C ";
$sqlB=$sqlB." WHERE B.unidadProgramador *= P.Unidad ";
$sqlB=$sqlB." AND B.unidadProyecto *= C.unidad ";
$sqlB=$sqlB." AND B.id_proyecto =" . $cualProyecto ;
$sqlB=$sqlB." order by B.fecha ";
$cursorB = mssql_query($sqlB);

//Trae la información de la bitácora de la asignación de recursos
//secuencia, fecha, id_proyecto, unidadProgramador, comentaProgramador, unidadProyecto, comentaProyecto
$sqlBR="SELECT B.*, P.nombre, P.apellidos, C.nombre nomCoordina, C.apellidos apeCoordina  ";
$sqlBR=$sqlBR." FROM BitacoraAsignaRecursos B, Usuarios P, Usuarios C ";
$sqlBR=$sqlBR." WHERE B.unidadProgramador *= P.Unidad ";
$sqlBR=$sqlBR." AND B.unidadProyecto *= C.unidad ";
$sqlBR=$sqlBR." AND B.id_proyecto =" . $cualProyecto ;
$sqlBR=$sqlBR." order by B.fecha ";
$cursorBR = mssql_query($sqlBR);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winProgProyectos";

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 560px; height: 30px;">
Programaci&oacute;n de proyectos general </div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">  Proyecto </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="10%">ID</td>
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?>
		<? $codProyecto = trim($reg[codigo]) ;?></td>
        <td width="20%"><? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); ?></td>
        </tr>
	  <? } ?>
    </table>
		
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Suma Global </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Programador</td>
            <td width="15%">Fecha de inicio </td>
            <td width="15%">Plazo</td>
            <td width="15%">Valor</td>
            <td width="5%">&nbsp;</td>
          </tr>
	<? while ($reg2=mssql_fetch_array($cursor2)) {  ?>
          <tr class="TxtTabla">
            <td><? echo  ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) ; ?></td>
            <td width="15%"><? echo date("M d Y ", strtotime($reg2[fechaInicio])) ; ?></td>
            <td width="15%"><? echo $reg2[plazo] ; ?></td>
            <td width="15%" align="right">$ <? echo number_format($reg2[valorSumaGlobal], 0, ',', '.'); ?> </td>
            <td width="5%"><input name="Submit4" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosGeneralSG.php?cualProyecto=<? echo $reg2[id_proyecto]; ?>&cualUnidad=<? echo $reg2[unidadProgramador]; ?>');return document.MM_returnValue" value="Detalle" /></td>
          </tr>
	<? } ?>
        </table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Bit&aacute;cora Programaci&oacute;n por suma global </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="10%">Fecha</td>
            <td width="15%">Programador Divisi&oacute;n</td>
            <td width="30%">Comentarios Programador Divisi&oacute;n </td>
            <td width="15%">Director/Coordinador Proyecto </td>
            <td>Comentario Director/Coordinador Proyecto </td>
          </tr>
          <? while ($regB=mssql_fetch_array($cursorB)) {  ?>
		  <tr class="TxtTabla">
            <td width="10%"><? echo date("M d Y ", strtotime($regB[fecha])) ; ?></td>
            <td width="15%"><? echo ucwords(strtolower($regB[apellidos])) . ", " . ucwords(strtolower($regB[nombre])) ; ?></td>
            <td width="30%"><? echo ucfirst(strtolower($regB[comentaProgramador]))  ; ?></td>
            <td width="15%"><? echo ucfirst(strtolower($regB[apeCoordina])) . ", " . ucfirst(strtolower($regB[nomCoordina])) ; ?></td>
            <td><? echo ucfirst(strtolower($regB[comentaProyecto]))  ; ?></td>
          </tr>
		<? } ?>
        </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;
			</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Asignaci&oacute;n de recursos </td>
          </tr>
        </table></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Programador</td>
            <td width="15%">Fecha de inicio </td>
            <td width="15%">Plazo</td>
            <td width="5%">&nbsp;</td>
          </tr>
	<? while ($reg3=mssql_fetch_array($cursor3)) {  ?>
          <tr class="TxtTabla">
            <td><? echo  ucwords(strtolower($reg3[apellidos])) . ", " . ucwords(strtolower($reg3[nombre])) ; ?></td>
            <td width="15%"><? echo date("M d Y ", strtotime($reg3[fechaInicio])) ; ?></td>
            <td width="15%"><? echo $reg3[plazo] ; ?></td>
            <td width="5%"><input name="Submit4" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosGeneralAR.php?cualProyecto=<? echo $reg3[id_proyecto]; ?>&cualUnidad=<? echo $reg3[unidadProgramador]; ?>');return document.MM_returnValue" value="Detalle" /></td>
          </tr>
	<? } ?>
        </table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Bit&aacute;cora Programaci&oacute;n por asignaci&oacute;n de recursos </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="10%">Fecha</td>
            <td width="15%">Programador Divisi&oacute;n</td>
            <td width="30%">Comentarios Programador Divisi&oacute;n </td>
            <td width="15%">Director/Coordinador Proyecto </td>
            <td>Comentario Director/Coordinador Proyecto </td>
          </tr>
          <? while ($regBR=mssql_fetch_array($cursorBR)) {  ?>
		  <tr class="TxtTabla">
            <td width="10%"><? echo date("M d Y ", strtotime($regBR[fecha])) ; ?></td>
            <td width="15%"><? echo ucwords(strtolower($regBR[apellidos])) . ", " . ucwords(strtolower($regBR[nombre])) ; ?></td>
            <td width="30%"><? echo ucfirst(strtolower($regBR[comentaProgramador]))  ; ?></td>
            <td width="15%"><? echo ucfirst(strtolower($regBR[apeCoordina])) . ", " . ucfirst(strtolower($regBR[nomCoordina])) ; ?></td>
            <td><? echo ucfirst(strtolower($regBR[comentaProyecto]))  ; ?></td>
          </tr>
		<? } ?>
        </table>		
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF" class="TxtTabla">&nbsp;		</td>
      </tr>
</table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_callJS('window.close()')" value="Cerrar Programaci&oacute;n general de proyectos" />
    </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
