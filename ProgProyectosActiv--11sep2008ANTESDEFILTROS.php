<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//echo "Proy=".  $cualProyecto . "<br>";
//echo "Act=" . $cualActividad . "<br>";
//exit;

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);


//10Jun2008
//Identificar si el usuario activo verá toda la información o sólo sus actividades
$esDC = 0 ;
$esProgP = 0;
$esOrdG = 0 ;
$todo= 0 ;
$verProyecto="SI";

//El usuario es Director o Coordinador
$vSqlU="Select coalesce(count(*), 0) existeDir ";
$vSqlU=$vSqlU." from HojaDeTiempo.dbo.Proyectos ";
$vSqlU=$vSqlU." where (id_director = " . $laUnidad . " or id_coordinador = " . $laUnidad . " ) ";
$vSqlU=$vSqlU." and id_proyecto = " . $cualProyecto ;
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esDC =  $vRegU[existeDir] ;
}

//Si el usuarios es Programador del proyecto
$vSqlU="Select coalesce(count(*), 0) existeProg ";
$vSqlU=$vSqlU." from HojaDeTiempo.dbo.Programadores  ";
$vSqlU=$vSqlU." where unidad = " . $laUnidad ;
$vSqlU=$vSqlU." and id_proyecto = " . $cualProyecto ;
$vSqlU=$vSqlU." and progProyecto = 1 ";
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esProgP =  $vRegU[existeProg] ;
}

//Si el usuario es ordenador del gasto
$vSqlU="select coalesce(count(*), 0) existeOrd ";
$vSqlU=$vSqlU." from GestiondeInformacionDigital.dbo.OrdenadorGasto ";
$vSqlU=$vSqlU." where unidadOrdenador = ". $laUnidad ;
$vSqlU=$vSqlU." and id_proyecto =" . $cualProyecto ;
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esOrdG =  $vRegU[existeOrd] ;
}

//Si alguna de las variables es > 0 el usuario podrá ver todo
$todo= $esDC + $esProgP + $esOrdG ;
if ($todo > 0) {
	$verProyecto="SI";
}
else {
	$verProyecto="NO";
}

//Cierra 10Jun2008


//22Ene2008
//Trae el nombre de las actividades asociadas al proyecto
//Si el usuario es Director, Coordinador, Ordenador del gasto o Programador del proyecto
//ve todas las actividades del proyecto
$primerActiv = 1;
//if ($verProyecto=="SI") { 
//o cuando es el Administrador del sistema o se trata de Camilo Marulanda
//if (($verProyecto=="SI") OR ($_SESSION["sesPerfilUsuario"] == 1 ) OR ($laUnidad == 14384) ) { 
if (($verProyecto=="SI") OR ($_SESSION["sesPerfilUsuario"] == 1 )  ) { 
	$sql2="Select A.* , U.nombre nomUsu, U.apellidos apeUsu ";
	$sql2=$sql2." from Actividades A, Usuarios U" ;
	$sql2=$sql2." where A.id_encargado *= U.unidad " ;
	$sql2=$sql2." and A.id_proyecto = " . $cualProyecto ;
	
	//para que en Porce muestre ordenado por ID
	if ($cualProyecto == 697) {
		$sql2=$sql2." order by A.id_actividad " ;
	}
	else {
		$sql2=$sql2." order by A.nivelesActiv " ;
	}
	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) {
		$primerActiv =  $reg2[id_actividad] ;
	}
}
//Sino, se trata de responsable de actividad o programadores de actividad y ven sus actividades
else {
	$sql2="Select A.*, U.nombre nomUsu, U.apellidos apeUsu  ";
	$sql2=$sql2." from ( " ;
	$sql2=$sql2." Select id_actividad " ;
	$sql2=$sql2." from Actividades " ;
	$sql2=$sql2." where id_proyecto = " . $cualProyecto ;
	$sql2=$sql2." and id_encargado =" . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from ResponsablesActividad " ;
	$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
	$sql2=$sql2." and unidad = " . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from Programadores " ;
	$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
	$sql2=$sql2." and unidad = " . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from Actividades" ;
	$sql2=$sql2." where id_proyecto = " . $cualProyecto ;
	$sql2=$sql2." and dependeDe In " ;
	$sql2=$sql2."  (" ;
	$sql2=$sql2." select id_actividad" ;
	$sql2=$sql2." from Programadores " ;
	$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
	$sql2=$sql2." and unidad = " . $laUnidad ;
	$sql2=$sql2." )" ;
	$sql2=$sql2." ) R , Actividades A, Usuarios U " ;
	$sql2=$sql2." where R.id_actividad = A.id_actividad " ;
	$sql2=$sql2." AND A.id_encargado *= U.unidad " ;
	$sql2=$sql2." and A.id_proyecto = " . $cualProyecto ;
	$sql2=$sql2." order by A.nivelesActiv " ;
	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) {
		$primerActiv =  $reg2[id_actividad] ;
	}
}
$cursor2 = mssql_query($sql2);
//echo $sql2 ;

if (trim($cualActividad) == "" ) {
	//$cualActividad = 1;
	$cualActividad = $primerActiv;
}

//23Ene2008
//Trae la información de asignaciones realizadas a la actividad seleccionada
/*
$sql3="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, ";
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario " ;
$sql3=$sql3." from asignaciones A, Usuarios U, Horarios H " ;
$sql3=$sql3." where A.unidad = U.unidad " ;
$sql3=$sql3." and A.IDhorario = H.IDhorario " ;
$sql3=$sql3." and A.id_proyecto = " . $cualProyecto ;
$sql3=$sql3." and A.id_actividad = " . $cualActividad ;
$sql3=$sql3." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, " ;
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario " ;
$sql3=$sql3." ORDER BY U.apellidos " ;
*/
//28Ago2008
//Para incluir la categoría
$sql3="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo,  ";
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario , C.nombre nomCat " ;
$sql3=$sql3." from asignaciones A, Usuarios U, Horarios H, Categorias C " ;
$sql3=$sql3." where A.unidad = U.unidad  " ;
$sql3=$sql3." and A.IDhorario = H.IDhorario  " ;
$sql3=$sql3." And U.id_categoria = C.id_categoria " ;
$sql3=$sql3." and A.id_proyecto = " . $cualProyecto ;
$sql3=$sql3." and A.id_actividad = " . $cualActividad ;
$sql3=$sql3." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, " ;
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario , C.nombre " ;
$sql3=$sql3." ORDER BY U.apellidos " ;
$cursor3 = mssql_query($sql3);

//1Jul2008
//Trae la información de los costos directos asociados a una actividad
$CDsql="select C.* , U.nombre, U.apellidos ";
$CDsql=$CDsql." from HojaDeTiempo.dbo.ActividadesCostosD C, HojaDeTiempo.dbo.Usuarios U ";
$CDsql=$CDsql." where C.unidad = U.unidad ";
$CDsql=$CDsql." and C.id_proyecto =" . $cualProyecto ;
$CDsql=$CDsql." and C.id_actividad =" . $cualActividad ;
$CDcursor = mssql_query($CDsql);

//8Ago2008
//Trae la información del personal externos 
$PEsql="SELECT P.*, E.nombre , E.apellidos , U.nombre nomUsu, U.apellidos apeUsu ";
$PEsql=$PEsql." FROM HojaDeTiempo.dbo.ActividadesPersonalExt P,  ";
$PEsql=$PEsql." HojaDeTiempo.dbo.PersonalExterno E, HojaDeTiempo.dbo.Usuarios U  ";
$PEsql=$PEsql." WHERE P.identificacion = E.identificacion ";
$PEsql=$PEsql." AND P.unidad = U.unidad ";
$PEsql=$PEsql." AND P.id_proyecto = " . $cualProyecto ;
$PEsql=$PEsql." AND P.id_actividad = " . $cualActividad ;
$PEcursor = mssql_query($PEsql);

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
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 521px; height: 30px;">
Programaci&oacute;n de proyectos - Actividades </div>
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
        <td>Proyectos</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="20%">Programadores</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?>
		<? $codProyecto = trim($reg[codigo]) ;?></td>
        <td width="20%">
		<? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); ?>
		<? 
		$oSql="select O.*, U.nombre, U.apellidos ";
		$oSql=$oSql." from GestiondeInformacionDigital.dbo.OrdenadorGasto O, HojaDeTiempo.dbo.Usuarios U  ";
		$oSql=$oSql." where O.id_proyecto =" . $reg[id_proyecto] ;
		$oSql=$oSql." and O.unidadOrdenador = U.unidad ";
		$oCursor = mssql_query($oSql);
		echo "<br><strong>Ordenadores</strong><br>" ;
		while ($oReg=mssql_fetch_array($oCursor)) {
			echo  ucwords(strtolower($oReg[nombre])) . " " . ucwords(strtolower($oReg[apellidos])) . "<br>";
		}
		?>		</td>
        <td width="20%" align="right" valign="top">
		<?
		//Lista los programadores del proyecto
		$pSql="Select P.* , U.nombre, U.apellidos ";
		$pSql=$pSql." from programadores P, Usuarios U ";
		$pSql=$pSql." where P.unidad = U.unidad ";
		$pSql=$pSql." and P.id_proyecto = " . $reg[id_proyecto] ;
		$pSql=$pSql." and P.progProyecto = 1 ";
		$pCursor = mssql_query($pSql);
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<? while ($pReg=mssql_fetch_array($pCursor)) { ?>
          <tr>
            <td align="left"><? echo ucwords(strtolower($pReg[apellidos])). ", " . ucwords(strtolower($pReg[nombre]))   ; ?></td>
            <td width="1%">
			<? if ($verProyecto=="SI") {   ?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar Programador del Proyecto" width="14" height="13" border="0" onclick="MM_openBrWindow('delProgProy.php?kProyecto=<? echo $pReg[id_proyecto] ; ?>&kActiv=<? echo $pReg[id_actividad]; ?>&kUnidad=<? echo $pReg[unidad]; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
			<? } ?>			</td>
          </tr>
		<? } ?>
        </table>
		<? if ($verProyecto=="SI") {   ?>
        <input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addProgProy.php?kProyecto=<? echo $reg[id_proyecto] ; ?>&kActiv=<? echo $primerActiv; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
		<? }  ?>		</td>
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
            <td class="TituloUsuario">Actividades</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr align="right" class="TxtTabla">
            <td>&nbsp;</td>
            <td width="28%"><strong>Asg</strong>: Valor Asignado a la actividad </td>
            <td width="28%"><strong>Prg</strong>: Valor programado en la actividad </td>
            <td width="28%"><strong>Cdt</strong>: Valor de los costos directos de la actividad </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
		<form name="form1" id="form1" method="post" action="">
          <tr class="TituloTabla2">
            <td width="1%">&nbsp;</td>
            <td width="3%">&nbsp;</td>
            <td width="3%">ID</td>
            <td>Nombre</td>
            <td>MacroActividad</td>
            <td width="8%">Fecha Inicio <br />
              Fecha Fin </td>
            <td>Responsable</td>
            <td>Avance
            <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>" /></td>
            <td>Programadores</td>
            <td width="15%">Valor Recurso </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
		   <? while ($reg2=mssql_fetch_array($cursor2)) { ?>
          <tr class="TxtTabla">
            <td width="1%" align="center">
			<? if ($reg2[dependeDe] == 0) { ?>
			<img src="img/images/Aprobado.gif" alt="ActividadCreada por el Director y/o encargados del proyecto" width="21" height="24" />
			<? } ?>
			</td>
            <td width="3%" align="center">
			<? 
			if ($cualActividad == $reg2[id_actividad]) {
				$selActiv = "checked" ;
			}
			else {
				$selActiv = "" ;
			}
			
			?>
			<input name="cualActividad" type="radio" value="<? echo  $reg2[id_actividad] ; ?>" onClick="document.form1.submit();" <? echo $selActiv; ?> /></td>
            <td width="3%"><? echo  $reg2[id_actividad] ; ?></td>
            <td>
			<? 
			$cualNivel = 0;
			$numEspacios="";
			$cualNivel = substr_count($reg2[nivelesActiv], '-'); 

			for ($i=1; $i<=$cualNivel; $i++) {
				if ($cualNivel > 1) {
					$numEspacios=$numEspacios."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}
			?>
			<? echo $numEspacios . ucwords(strtolower($reg2[nombre])) ; ?></td>
            <td><? echo  ucwords(strtolower($reg2[macroactividad])) ; ?></td>
            <td width="8%">
			<? 
			if (trim($reg2[fecha_inicio]) != "") {
				echo date("M d Y ", strtotime($reg2[fecha_inicio])); 
			}
			?>
            <br />
			<? 
			if (trim($reg2[fecha_fin]) != "") {
				echo date("M d Y ", strtotime($reg2[fecha_fin])); 
			}
			?>			
			</td>
            <td align="right">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
					      <tr>
		        <td align="left">
				<? echo  ucwords(strtolower($reg2[nomUsu])) . " " . ucwords(strtolower($reg2[apeUsu])) ; 
				$cualResponsableEs=$reg2[id_encargado];
				?>
		          <?
		//Lista los responsables de la actividad
		$rSql="SELECT R.*, U.nombre, U.apellidos ";
		$rSql=$rSql." FROM ResponsablesActividad R, Usuarios U ";
		$rSql=$rSql." WHERE R.unidad = U.unidad ";
		$rSql=$rSql." AND R.id_proyecto = " . $reg2[id_proyecto] ;
		$rSql=$rSql." AND R.id_actividad = ". $reg2[id_actividad] ;
		$rCursor = mssql_query($rSql);
		
		?></td>
		        <td>&nbsp;</td>
		        </tr>
        	<? while ($rReg=mssql_fetch_array($rCursor)) { ?>
		      <tr>
                <td align="left"><? echo ucwords(strtolower($rReg[nombre])) . " " . ucwords(strtolower($rReg[apellidos])) ;?></td>
                <td width="1%">
				<? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
				<a href="#"><img src="img/images/Del.gif" alt="Eliminar Responsable del Proyecto" width="14" height="13" border="0" onclick="MM_openBrWindow('delProgResp.php?kProyecto=<? echo $rReg[id_proyecto] ; ?>&kActiv=<? echo $rReg[id_actividad]; ?>&kUnidad=<? echo $rReg[unidad]; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
				<? } ?>
				</td>
              </tr>
			  <? } ?>
            </table>
            <? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
			<input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
			<? } ?></td>
            <td><? echo  $reg2[avance_reportado] ; ?></td>
            <td align="right" valign="top">
			<?
		//Lista los programadores de la actividad
		$aSql="Select P.* , U.nombre, U.apellidos ";
		$aSql=$aSql." from programadores P, Usuarios U ";
		$aSql=$aSql." where P.unidad = U.unidad ";
		$aSql=$aSql." and P.id_proyecto = " . $reg2[id_proyecto] ;
		$aSql=$aSql." and P.id_actividad = " . $reg2[id_actividad] ;
		$aSql=$aSql." and P.progProyecto = 0 ";
		$aCursor = mssql_query($aSql);
		?>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<? while ($aReg=mssql_fetch_array($aCursor)) { ?>
              <tr>
                <td align="left"><? echo ucwords(strtolower($aReg[apellidos])). ", " . ucwords(strtolower($aReg[nombre]))   ; ?></td>
                <td width="1%">
				<? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="NO") {   ?>
				<a href="#"><img src="img/images/Del.gif" alt="Eliminar Programador del Proyecto" width="14" height="13" border="0" onclick="MM_openBrWindow('delProgProy.php?kProyecto=<? echo $aReg[id_proyecto] ; ?>&kActiv=<? echo $aReg[id_actividad]; ?>&kUnidad=<? echo $aReg[unidad]; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
			<? } ?>				</td>
              </tr>
			<? } ?>  
            </table>
            <? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if (($verProyecto=="NO") OR ($reg2[id_encargado] == $laUnidad ) ) {   ?>
			<input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addProgActiv.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
			<? } ?>			</td>
            <td width="15%" align="right" valign="top">
			<?
			//26Jun2008
			//Trae el último valor asignado a la actividad
			$miValorActiv= 0;
			$vrSql="SELECT COALESCE(MAX(valorActiv), 0) valorActiv FROM ActividadesRecursos ";
			$vrSql=$vrSql." where secuencia = (SELECT COALESCE(MAX(secuencia), 0) hayRecurso ";
			$vrSql=$vrSql." 		FROM ActividadesRecursos ";
			$vrSql=$vrSql." 		WHERE id_proyecto = " . $cualProyecto ;
			$vrSql=$vrSql." 		AND id_actividad = " . $reg2[id_actividad]  . ") ";
			$vrSql=$vrSql." AND id_proyecto = " . $cualProyecto ;
			$vrSql=$vrSql." AND id_actividad = " . $reg2[id_actividad] ;
			$vrCursor = mssql_query($vrSql);
			if ($vrReg=mssql_fetch_array($vrCursor)) {
				$miValorActiv = $vrReg[valorActiv] ;
			}
//			echo "$ " . number_format($miValorActiv, 0, ',','.');
			
			//Trae la sumatoria de los valores programados en las asignaciones para el proyecto y actividad
			$miTotalAsigna=0;
			$vrSql="select coalesce(sum(valorProgramado), 0) totProgramado ";
			$vrSql=$vrSql." from HojaDeTiempo.dbo.asignaciones  ";
			$vrSql=$vrSql." where id_proyecto =" . $cualProyecto ;
			$vrSql=$vrSql." and id_actividad =" . $reg2[id_actividad] ;
			$vrCursor = mssql_query($vrSql);
			if ($vrReg=mssql_fetch_array($vrCursor)) {
				$miTotalAsigna = $vrReg[totProgramado] ;
			}
//			echo "$ " . number_format($miTotalAsigna, 0, ',','.');

			//Trae la sumatoria de Costos Directos
			$miTotalCostosD=0;
			$vrSql="SELECT COALESCE(SUM(valorItem), 0) valorItem ";
			$vrSql=$vrSql." FROM ActividadesCostosD ";
			$vrSql=$vrSql." WHERE id_proyecto = " . $cualProyecto ;
			$vrSql=$vrSql." AND id_actividad =" . $reg2[id_actividad] ;
			$vrCursor = mssql_query($vrSql);
			if ($vrReg=mssql_fetch_array($vrCursor)) {
				$miTotalCostosD = $vrReg[valorItem] ;
			}
						
			//ValorRemanente
			$valorRemanente=$miValorActiv-$miTotalAsigna-$miTotalCostosD ;
			
			//25Jul2008
			//Trae la sumatoria del total asignado a las subactividades
			$miTotalSubAct=0;
			$vrSql="SELECT COALESCE(SUM(B.valorActiv), 0) totSA ";
			$vrSql=$vrSql." FROM (select id_actividad, max(secuencia) secuencia  from ActividadesRecursos ";
			$vrSql=$vrSql." 		where id_proyecto = " . $cualProyecto ;
			$vrSql=$vrSql." 		and id_actividad in (SELECT id_actividad FROM Actividades ";
			$vrSql=$vrSql." 				     WHERE id_proyecto = " . $cualProyecto ;
			$vrSql=$vrSql." 				     and tipoActividad > 1 ";

			$vrSql=$vrSql." 				     and actPrincipal = ".$reg2[actPrincipal]."  ";
			if ($reg2[tipoActividad] > 1) {
				$vrSql=$vrSql." 				     and nivelesActiv like '%".$reg2[nivelesActiv]."%'" ;
				$vrSql=$vrSql." 				     AND id_actividad <>	".$reg2[id_actividad] ;			
			}
			$vrSql=$vrSql." 				     ) ";			
			$vrSql=$vrSql." 		group by id_actividad  ";
			$vrSql=$vrSql." 		) A, ActividadesRecursos B ";
			$vrSql=$vrSql." WHERE A.id_actividad = B.id_actividad ";
			$vrSql=$vrSql." and A.secuencia = B.secuencia ";			
			$vrSql=$vrSql." and B.id_proyecto = " . $cualProyecto ;			
			$vrCursor = mssql_query($vrSql);
			if ($vrReg=mssql_fetch_array($vrCursor)) {
				$miTotalSubAct = $vrReg[totSA] ;
			}

			//Trae la sumatoria de la programación asignada a las subactividades de una actividad
			$miTotPrgSubAct=0;
			$vrSql="SELECT SUM(valorProgramado) totAsignaSA FROM Asignaciones ";
			$vrSql=$vrSql." WHERE id_proyecto = " . $cualProyecto ;	
			$vrSql=$vrSql." and id_actividad in (SELECT id_actividad FROM Actividades ";
			$vrSql=$vrSql." 		     WHERE id_proyecto = " . $cualProyecto ;	
			$vrSql=$vrSql." 		     and tipoActividad > 1  ";
			$vrSql=$vrSql." 		     and actPrincipal = " . $reg2[actPrincipal] ;
			if ($reg2[tipoActividad] > 1) {
				$vrSql=$vrSql." 		 and nivelesActiv like '%".$reg2[nivelesActiv]."%'" ;
				$vrSql=$vrSql." 		 AND id_actividad <>	".$reg2[id_actividad] ;			
			}
			$vrSql=$vrSql." 				     ) ";			
			$vrCursor = mssql_query($vrSql);
			if ($vrReg=mssql_fetch_array($vrCursor)) {
				$miTotPrgSubAct = $vrReg[totAsignaSA] ;
			}


			?>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="1%">Asg</td>
                <td align="right"><? echo "$ " . number_format($miValorActiv, 0, ',','.')?></td>
              </tr>
              <tr class="TxtTabla">
                <td>AsgS</td>
                <td align="right">
				<? echo "$ " . number_format($miTotalSubAct, 0, ',','.')?>
				</td>
              </tr>
              <tr class="TxtTabla">
                <td width="1%">Prg</td>
                <td align="right"><? echo "$ " . number_format($miTotalAsigna, 0, ',','.')?></td>
              </tr>
              <tr class="TxtTabla">
                <td>PrgS</td>
                <td align="right"><? echo "$ " . number_format($miTotPrgSubAct, 0, ',','.')?> </td>
              </tr>
              <tr class="TxtTabla">
                <td>Cdt</td>
                <td align="right">
				<? echo "$ " . number_format($miTotalCostosD, 0, ',','.')?>
				
				</td>
              </tr>
              <tr class="TxtTabla">
                <td width="1%">&nbsp;</td>
                <td align="right"><? echo "$ " . number_format($valorRemanente, 0, ',','.')?></td>
              </tr>
            </table>
	</td>
  </tr>
</table>

			</td>
            <td width="1%">
			<a href="#"><img src="img/images/icoCuantia.gif" alt="Valor del Recurso" width="16" height="16" border="0" onclick="MM_openBrWindow('addValorActiv.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $reg2[id_actividad] ; ?>','adVR','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
			</td>
            <td width="1%">
			<? //if ($verProyecto=="SI") {   ?>
			<a href="#"><img src="img/images/actualizar.jpg" alt="Editar" width="19" height="17" border="0" onclick="MM_openBrWindow('upActiv.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $reg2[id_actividad] ; ?>','vAddA','scrollbars=yes,resizable=yes,width=500,height=300')" /></a>
			<? //} ?>			</td>
            <td width="1%">
			<? 
			//10Sep2008
			//Para que el botón eliminar aparezca para camilo así no sea director del proyecto
			if (($verProyecto=="SI") OR ($laUnidad==14384)) {   ?>
			<?
			$hSql="SELECT COALESCE(COUNT(*), 0) hayAsigna ";
			$hSql=$hSql." FROM asignaciones  ";
			$hSql=$hSql." WHERE id_proyecto = " . $reg2[id_proyecto] ;
			$hSql=$hSql." AND id_actividad =" . $reg2[id_actividad] ;
			$hCursor = mssql_query($hSql);
			if ($hReg=mssql_fetch_array($hCursor)) {
				$cuantasAsignaciones = $hReg[hayAsigna];
			}
//			echo "=" . $cuantasAsignaciones;
			if ($cuantasAsignaciones == 0) {
			?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delActiv.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $reg2[id_actividad] ; ?>','vAddA','scrollbars=yes,resizable=yes,width=500,height=300')" /></a>
			<? } ?>
			<? } // if verProyecto ?>
			</td>
            <td width="1%">
			<? if ($verProyecto=="SI") {   ?>
			<a href="#"><img src="img/images/icoSubActv.gif" alt="Insertar Subactividad" width="25" height="23" border="0" onclick="MM_openBrWindow('addSubActiv.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $reg2[id_actividad] ; ?>','adSA','scrollbars=yes,resizable=yes,width=500,height=300')" /></a>
			<? }
				else {		?>
			<a href="#"><img src="img/images/icoSubActv.gif" alt="Insertar Subactividad" width="25" height="23" border="0" onclick="MM_openBrWindow('addSubActivResp.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $reg2[id_actividad] ; ?>','adSA','scrollbars=yes,resizable=yes,width=500,height=300')" /></a>				
			<? } ?>
			</td>
          </tr>
		  <? } ?>
		  </form> 
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
			<? if ($verProyecto=="SI") {   ?>
            <input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addActiv.php?cualProyecto=<? echo $cualProyecto ; ?>','vAddA','scrollbars=yes,resizable=yes,width=500,height=300')" value="Insertar" />
			<? } ?>
			</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="img/images/Pixel.gif" width="4" height="4" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Asignaciones para la actividad seleccionada </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="8%">Unidad</td>
        <td width="5%">Categor&iacute;a</td>
        <td>Nombre</td>
        <td width="5%">Clase Tiempo </td>
        <td width="5%">Localizaci&oacute;n</td>
        <td width="5%">Cargo</td>
        <td width="10%">Horas Programadas </td>
        <td width="10%">Horas Reportadas </td>
        <td width="15%">Horario</td>
        <td width="15%">Valor Recurso </td>
        <td width="1%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
      </tr>
	   <? while ($reg3=mssql_fetch_array($cursor3)) { ?>
      <tr class="TxtTabla">
        <td width="8%"><? echo  $reg3[unidad] ; ?></td>
        <td width="5%"><? echo  $reg3[nomCat] ; ?></td>
        <td><? echo  ucwords(strtolower($reg3[apellidos])) . ", " . ucwords(strtolower($reg3[nombre]))  ; ?></td>
        <td width="5%"><? echo  $reg3[clase_tiempo] ; ?></td>
        <td width="5%"><? echo  $reg3[localizacion] ; ?></td>
        <td width="5%"><? echo  $reg3[cargo] ; ?></td>
        <td width="10%">
		<?
		//23Ene2008
		//Traer el total de horas programadas para el usuario y actividad
		$sql4="select coalesce(sum(tiempo_asignado),0) horasProg from asignaciones  ";
		$sql4=$sql4." where id_proyecto = " . $cualProyecto ;
		$sql4=$sql4." and id_actividad =" . $cualActividad ;
		$sql4=$sql4." and unidad =" . $reg3[unidad] ;
		$sql4=$sql4." and clase_tiempo =" . $reg3[clase_tiempo] ;
		$sql4=$sql4." and localizacion =" . $reg3[localizacion] ;
		$sql4=$sql4." and cargo =" . $reg3[cargo] ;
		$sql4=$sql4." and month(fecha_inicial)=month(fecha_final) ";
		$sql4=$sql4." and year(fecha_inicial)=year(fecha_final) ";
		$cursor4 = mssql_query($sql4);
		if ($reg4=mssql_fetch_array($cursor4)) {
			echo  $reg4[horasProg];
		}
		?>
	</td>
        <td width="10%">
		<?
		//23Ene2008
		//Traer el total de horas reportadas para el usuario y la actividad
		$sql5="select coalesce(sum(horas_registradas),0) horasEje from horas  ";
		$sql5=$sql5." where id_proyecto = " . $cualProyecto ;
		$sql5=$sql5." and id_actividad = " . $cualActividad ;
		$sql5=$sql5." and unidad = " . $reg3[unidad] ;
		$sql5=$sql5." and clase_tiempo = " . $reg3[clase_tiempo] ;
		$sql5=$sql5." and localizacion = " . $reg3[localizacion] ;
		$sql5=$sql5." and cargo =" . $codProyecto . $reg3[cargo] ;
		$sql5=$sql5." and fecha > CONVERT(DATETIME, '2007-09-30 00:00:00', 102) " ;
		$cursor5 = mssql_query($sql5);
		if ($reg5=mssql_fetch_array($cursor5)) {
			echo  $reg5[horasEje];
		}

		?>
		</td>
        <td width="15%"><? echo $reg3[NomHorario]; ?></td>
        <td width="15%" align="right">
		<?
		$rTPSql="select coalesce(sum(valorProgramado), 0) valorProgramado  ";
		$rTPSql=$rTPSql." from asignaciones ";
		$rTPSql=$rTPSql." where id_proyecto = " . $reg3[id_proyecto];
		$rTPSql=$rTPSql." and id_actividad =" . $reg3[id_actividad];
		$rTPSql=$rTPSql." and unidad =" . $reg3[unidad];
		$rTPSql=$rTPSql." and clase_tiempo = " . $reg3[clase_tiempo];
		$rTPSql=$rTPSql." and localizacion =" . $reg3[localizacion];
		$rTPSql=$rTPSql." and cargo = '".$reg3[cargo]."'";
		$rTPcursor = mssql_query($rTPSql);
		if ($rTPreg=mssql_fetch_array($rTPcursor)) {
			echo "$ " . number_format($rTPreg[valorProgramado], 0, ',','.') ;
		}
		
		?>
		</td>
        <td width="1%">
		<a href="#"><img src="img/images/actualizar.jpg" alt="Edici&oacute;n" width="19" height="17" border="0" onclick="MM_openBrWindow('upProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3[unidad]; ?>&cualClase=<? echo $reg3[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3[localizacion]; ?>&cualCargo=<? echo $reg3[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vupP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a>
		</td>
        <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delPrograma.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3[unidad]; ?>&cualClase=<? echo $reg3[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3[localizacion]; ?>&cualCargo=<? echo $reg3[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vDelP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a></td>
        <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Reporte Programado/Facturado Mensual" width="16" height="16" border="0" onclick="MM_openBrWindow('verProgFact.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3[unidad]; ?>&cualClase=<? echo $reg3[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3[localizacion]; ?>&cualCargo=<? echo $reg3[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=200')" /></a></td>
      </tr>
	  <? } ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
			<input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('addProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>','verAdP','scrollbars=yes,resizable=yes,width=580,height=280')" value="Insertar" />
			<? if ($laUnidad == 12974) { ?>			
			<input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('addProgramaR2.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>','verAdP','scrollbars=yes,resizable=yes,width=580,height=280')" value="Insertar" />
			<? } ?>
			</td>
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
        <td class="TituloUsuario">Costos directos</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>ID</td>
        <td>Item</td>
        <td>Valor</td>
        <td>Qui&eacute;n lo ingres&oacute;</td>
        <td width="1%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
      </tr>
	  
	   <? while ($CDreg=mssql_fetch_array($CDcursor)) { ?>
      <tr class="TxtTabla">
        <td><? echo  $CDreg[secuencia] ; ?></td>
        <td><? echo  $CDreg[item] ; ?></td>
        <td align="right"><? echo "$ " . number_format($CDreg[valorItem], 0, ",", ".") ; ?></td>
        <td><? echo  ucwords(strtolower($CDreg[apellidos])) . ", " . ucwords(strtolower($CDreg[nombre])) ; ?></td>
        <td width="1%">
		<? 
		//Solo es visible por los responsables y/p programadores de la actividad
		if (($verProyecto=="NO") OR ($CDreg[unidad] == $laUnidad ) ) {   ?>
		<a href="#"><img src="img/images/actualizar.jpg" alt="Modificar Costo directo" width="19" height="17" border="0" onclick="MM_openBrWindow('upActivCostoD.php?cualProyecto=<? echo $CDreg[id_proyecto] ; ?>&cualActividad=<? echo $CDreg[id_actividad] ; ?>&cualSecCD=<? echo $CDreg[secuencia]; ?>','UPacd','scrollbars=yes,resizable=yes,width=500,height=280')" /></a>
		<? } ?>		</td>
        <td width="1%">
		<? 
		//Solo es visible por los responsables y/p programadores de la actividad
		if (($verProyecto=="NO") OR ($CDreg[unidad] == $laUnidad ) ) {   ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar Costo directo" width="14" height="13" border="0" onclick="MM_openBrWindow('delActivCostoD.php?cualProyecto=<? echo $CDreg[id_proyecto] ; ?>&cualActividad=<? echo $CDreg[id_actividad] ; ?>&cualSecCD=<? echo $CDreg[secuencia]; ?>','UPacd','scrollbars=yes,resizable=yes,width=500,height=280')" /></a>
		<? } ?>		</td>
      </tr>
		<? }?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TxtTabla"><input name="Submit6" type="submit" class="Boton" onclick="MM_openBrWindow('addActivCostoD.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>','adCDAct','scrollbars=yes,resizable=yes,width=500,height=280')" value="Insertar" /></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
	<?
	if (($laUnidad == 15712) OR ($laUnidad == 12974)) {
	?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Personal externo </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Identificaci&oacute;n</td>
        <td width="20%">Nombre</td>
        <td>Servicio</td>
        <td width="15%">Valor</td>
        <td width="5%">Factor</td>
        <td width="10%">Qui&eacute;n lo ingres&oacute; </td>
        <td width="1%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
      </tr>
      <? while ($PEreg=mssql_fetch_array($PEcursor)) { ?>
	  <tr class="TxtTabla">
        <td width="10%"><? echo  $PEreg[identificacion] ;  ?></td>
        <td width="20%"><? echo ucwords(strtolower($PEreg[apellidos])) . ", ". ucwords(strtolower($PEreg[nombre])) ; ?></td>
        <td><? echo ucfirst(strtolower($PEreg[servicio])) ; ?></td>
        <td width="15%" align="right"><? echo number_format($PEreg[valor],0,",", ".") ;  ?></td>
        <td width="5%" align="right"><? echo number_format($PEreg[factor],0,",", ".") ;  ?></td>
        <td width="10%"><? echo ucwords(strtolower($PEreg[apeUsu])) . ", ". ucwords(strtolower($PEreg[nomUsu])) ; ?></td>
        <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Modificar Personal externo " width="19" height="17" border="0" onclick="MM_openBrWindow('upPersonalExt.php?cualProyecto=<? echo $PEreg[id_proyecto]; ?>&cualActividad=<? echo $PEreg[id_actividad];  ?>&cualID=<? echo $PEreg[identificacion]; ?>','vupPEx','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
        <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar Personal externo" width="14" height="13" border="0" onclick="MM_openBrWindow('delPersonalExt.php?cualProyecto=<? echo $PEreg[id_proyecto]; ?>&cualActividad=<? echo $PEreg[id_actividad];  ?>&cualID=<? echo $PEreg[identificacion]; ?>','vupPEx','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
      </tr>
	  <? } ?>
    </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit7" type="submit" class="Boton" onclick="MM_openBrWindow('addPersonalExt.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>','adPEx','scrollbars=yes,resizable=yes,width=500,height=300')" value="Insertar" /></td>
          </tr>
        </table>	    <? } ?>
		</td>
      </tr>
    </table>	</td>
  </tr>
</table>

	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectos.php');return document.MM_returnValue" value="Lista de Proyectos" />
    <input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
