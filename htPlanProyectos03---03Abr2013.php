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

//------------------------

//10Jun2008
//Identificar si el usuario activo verá toda la información o sólo sus actividades
$esDC = 0 ;
$esProgP = 0;
$esOrdG = 0 ;
$todo= 0 ;
$verBoton="SI";

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
	$verBoton="SI";
}
else {
	$verBoton="NO";
}

//--------------

//09Oct2012
//Listar las actividades
$nivelLote="";
$lenNivelLote = 0;
$sql02="SELECT (valMacro *  factor) miOrden, * ";
$sql02=$sql02." FROM ";
$sql02=$sql02." 	( ";
$sql02=$sql02." 	Select ";
$sql02=$sql02." 	CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro, ";
$sql02=$sql02." 	factor = ";
$sql02=$sql02." 		case nivel ";
$sql02=$sql02." 			when 1 then 100000 ";
$sql02=$sql02." 			when 2 then 10000 ";
$sql02=$sql02." 			when 3 then 1000 ";
$sql02=$sql02." 			when 4 then 100 ";
$sql02=$sql02." 		end, A.*, U.nombre nomUsu, U.apellidos apeUsu ";
$sql02=$sql02." 	from Actividades A, Usuarios U ";
$sql02=$sql02." 	where A.id_encargado *= U.unidad ";
$sql02=$sql02." 	and A.id_proyecto = " . $cualProyecto;

if (trim($lstDiv) != "") {
	$nivelDiv= $lstLC . "-" . $lstLT . "-" . $lstDiv . "-";
	$lenNivelDiv = strlen($nivelDiv);
	$sql02=$sql02." and (A.id_actividad in ( " . $lstLC . "," . $lstLT . "," . $lstDiv . ") "   ;
	$sql02=$sql02." OR LEFT(A.nivelesActiv, ".$lenNivelDiv.") = '".$nivelDiv."' ) ";
}
else {
	if (trim($lstLT) != "") {
		$nivelLote= $lstLC . "-" . $lstLT . "-";
		$lenNivelLote = strlen($nivelLote);
		$sql02=$sql02." and (A.id_actividad in ( " . $lstLC . "," . $lstLT . ") "   ;
		$sql02=$sql02." OR LEFT(A.nivelesActiv, ".$lenNivelLote.") = '".$nivelLote."' ) ";
	}
	else {
		if (trim($lstLC) != "") {
			$sql02=$sql02." and A.actPrincipal = " . $lstLC;
		}
	}
}	
$sql02=$sql02." 	) Z ";
if ($verBoton=="NO") {
	$sql02=$sql02." WHERE id_actividad in ";
	$sql02=$sql02." 	( ";
	$sql02=$sql02." 	SELECT id_actividad ";
	$sql02=$sql02." 	FROM Actividades ";
	$sql02=$sql02." 	WHERE id_proyecto =  " . $cualProyecto;
	$sql02=$sql02." 	and id_encargado = " . $laUnidad ;
	$sql02=$sql02." 	UNION ";
	$sql02=$sql02." 	SELECT id_actividad ";
	$sql02=$sql02." 	FROM ResponsablesActividad ";
	$sql02=$sql02." 	WHERE id_proyecto =  " . $cualProyecto;
	$sql02=$sql02." 	AND unidad = " . $laUnidad ;
	$sql02=$sql02." 	) ";
}
$sql02=$sql02." order by (valMacro *  factor) ";
$cursor02 = mssql_query($sql02);
$cantRegEDT = mssql_num_rows($cursor02);
//echo $sql02 ;

//18-Ene-2013
$primerActiv = 1;
$cursor02pa = mssql_query($sql02);
if ($reg2pa=mssql_fetch_array($cursor02pa)) {
	$primerActiv =  $reg2pa[id_actividad] ;
}

//3Abr2013
//Definir la fecha inicio mínima y final máxima de todas las actividades que hacen parte del proyecto
$minVigenciaP="";
$maxVigenciaP="";
$sql03="SELECT YEAR(MIN(fecha_inicio)) fechaMin, YEAR(MAX(fecha_fin)) fechaMax ";
$sql03=$sql03." FROM Actividades ";
$sql03=$sql03." WHERE id_proyecto = " . $cualProyecto;
$cursor03 = mssql_query($sql03);
if ($reg03=mssql_fetch_array($cursor03)) {
	$minVigenciaP = $reg03[fechaMin] ;
	$maxVigenciaP = $reg03[fechaMax] ;
}
echo $minVigenciaP . "<br>";
echo $maxVigenciaP . "<br>";




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;">
Planeaci&oacute;n de proyectos - Plan de trabajo </div>
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
    <td class="TituloUsuario"> .: PROYECTO</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form name="form1" id="form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="20%">Programadores</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><?
		//27Ene2009
		//Traer los cargos adicionales del proyecto
		$sqlCargos="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		$sqlCargos=$sqlCargos." where id_proyecto = " . trim($reg[id_proyecto]) ;
		$cursorCargos = mssql_query($sqlCargos);
		
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="3%"><strong><? echo  trim($reg[codigo])  ; ?></strong></td>
            <td width="1%"><strong>.</strong></td>
            <td width="5%"><strong><? echo  $reg[cargo_defecto] ; ?></strong></td>
            <td>[<? echo  $reg[descCargoDefecto] ; ?>]</td>
            </tr>
		<? while ($regCargos=mssql_fetch_array($cursorCargos)) { ?>
          <tr>
            <td width="3%">&nbsp;</td>
            <td width="1%">.</td>
            <td width="5%"><? echo $regCargos[cargos_adicionales]; ?></td>
            <td>[<? echo $regCargos[descripcion]; ?>]</td>
            </tr>
		<? } ?>
        </table>
		</td>
        <td width="20%">
		<? 
		echo "<B>Director: </B><br>" . ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" ;
		echo "<B>Coordinador: </B><br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])) . "<br>"; 
		$DirectorNombre =  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD]));
		$DirectorUnidad = $reg[id_director];
		?>
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
        <td width="20%" align="right">
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
            </tr>
		<? } ?>
        </table>				</td>
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
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr >
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos01.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >EDT</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos02.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Participantes</a></td>
        <td width="15%" height="20" class="FichaAct">Planeaci&oacute;n</td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos04.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Resumen</a></td>
        <td height="20" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td height="2" colspan="5" class="TituloUsuario"> </td>
        </tr>
    </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><table width="60%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="TituloUsuario">Criterios de consulta </td>
                  </tr>
                </table>
                  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                    <tr>
                      <td width="20%" align="left" class="TituloTabla">Lote de control </td>
                      <td align="left" class="TxtTabla">
					  <?
					  //Trae la información de los lotes de control
					  $lstSqlLC="SELECT * FROM Actividades ";
					  $lstSqlLC=$lstSqlLC." WHERE id_proyecto = " .  $cualProyecto ;
					  $lstSqlLC=$lstSqlLC." AND nivel = '1' ";
					  $lstCursorLC = mssql_query($lstSqlLC);

					  
					  ?>
					  <select name="lstLC" class="CajaTexto" id="lstLC" onChange="document.form1.submit();" >
                        <option value="">::: Todos:::</option>
						<? while ($lstRegLC=mssql_fetch_array($lstCursorLC)) { 
							if ($lstRegLC[id_actividad] == $lstLC) {
								$selLC = "selected";
							}
							else {
								$selLC = "";
							}
						
						?>
                        <option value="<? echo $lstRegLC[id_actividad]; ?>" <? echo $selLC; ?> ><? echo "[" . strtoupper($lstRegLC[macroactividad]) . "] " . strtoupper($lstRegLC[nombre]); ?></option>
						<? } ?>
                      </select></td>
                      <td width="5%" class="TxtTabla">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="left" class="TituloTabla">Lote de trabajo </td>
                      <td align="left" class="TxtTabla">
					  <?
					  //Trae la información de los lotes de trabajo del Lote de control seleccionado
					  $lstSqlLT="SELECT * FROM Actividades ";
					  $lstSqlLT=$lstSqlLT." WHERE id_proyecto = " .  $cualProyecto ;
					  $lstSqlLT=$lstSqlLT." AND nivel = '2' ";
					  $lstSqlLT=$lstSqlLT." AND dependeDe = " . $lstLC ;
					  $lstCursorLT = mssql_query($lstSqlLT);
					  ?>
					  <select name="lstLT" class="CajaTexto" id="lstLT" onChange="document.form1.submit();">
                        <option value="" selected >::: Todos:::</option>
						<? while ($lstRegLT=mssql_fetch_array($lstCursorLT)) { 
							if ($lstRegLT[id_actividad] == $lstLT) {
								$selLT = "selected";
							}
							else {
								$selLT = "";
							}
						
						?>
                        <option value="<? echo $lstRegLT[id_actividad]; ?>" <? echo $selLT; ?> ><? echo "[" . strtoupper($lstRegLT[macroactividad]) . "] " . strtoupper($lstRegLT[nombre]); ?></option>
						<? } ?>
                      </select>					  
					  </td>
                      <td class="TxtTabla">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="left" class="TituloTabla">Divisi&oacute;n</td>
                      <td align="left" class="TxtTabla"><?
					  //Trae la informaci&oacute;n de los lotes de trabajo del Lote de control seleccionado
					  $lstSqlDiv="SELECT * FROM Actividades ";
					  $lstSqlDiv=$lstSqlDiv." WHERE id_proyecto = " .  $cualProyecto ;
					  $lstSqlDiv=$lstSqlDiv." AND nivel = '3' ";
					  $lstSqlDiv=$lstSqlDiv." AND dependeDe = " . $lstLT ;
					  $lstCursorDiv = mssql_query($lstSqlDiv);
					  ?>
					  <select name="lstDiv" class="CajaTexto" id="lstDiv">
                        <option value="" selected >::: Todas:::</option>
						<? while ($lstRegDiv=mssql_fetch_array($lstCursorDiv)) { 
							if ($lstRegDiv[id_actividad] == $lstDiv) {
								$selDiv = "selected";
							}
							else {
								$selDiv = "";
							}
						
						?>
                        <option value="<? echo $lstRegDiv[id_actividad]; ?>" <? echo $selDiv; ?> ><? echo "[" . strtoupper($lstRegDiv[macroactividad]) . "] " . strtoupper($lstRegDiv[nombre]); ?></option>
						<? } ?>
                      </select>
					  </td>
                      <td class="TxtTabla"><input name="Submit3" type="submit" class="Boton" value="Consultar" /></td>
                    </tr>
                  </table>
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="2" class="TituloUsuario"> </td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">LOTES DE CONTROL / LOTES DE TRABAJO / DIVISIONES - ACTIVIDADES</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="1%"><img src="img/images/imgInfoG.gif" alt="Seleccionar la macroactividad donde se har&aacute; la planeaci&oacute;n" width="29" height="23" /></td>
            <td width="5%">Identificador</td>
            <td>LOTE DE CONTROL / LOTE DE TRABAJO / DIVISI&Oacute;N / ACTIVIDAD</td>
            <td width="20%">Responsable</td>
            <td width="15%">Valor Presupuestado </td>
            <td width="1%" class="TituloTabla">&nbsp;</td>
            <td width="8%">Fecha Inicio </td>
            <td width="8%">Fecha de Finalizaci&oacute;n </td>
            <td width="1%">&nbsp;</td>
          </tr>
		   <? while ($reg02=mssql_fetch_array($cursor02)) { ?>
          <tr class="TxtTabla">
            <td width="1%" align="center"><input type="checkbox" name="checkbox" value="checkbox" /></td>
            <td width="5%"><? echo strtoupper($reg02[macroactividad]) ; ?></td>
            <td>
			<table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
			  	<? if (trim($reg02[nivel]) == 2) { ?>
                <td width="3%">&nbsp;</td>
				<? } ?>
			  	<? if (trim($reg02[nivel]) == 3) { ?>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
				<? } ?>
			  	<? if (trim($reg02[nivel]) >= 4) { ?>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
				<? } ?>
                <td>
				<? 
				if ( (trim($reg02[nivel]) == 1) ) {
					echo "<B>" . strtoupper($reg02[nombre]) . "</B>"; 
				}
				else {
					if ( (trim($reg02[nivel]) == 2) OR (trim($reg02[nivel]) == 3) ) {
						echo  strtoupper($reg02[nombre]) ; 
					}
					else {
						echo ucfirst(strtolower($reg02[nombre]) ) ; 
					}
				}
				
				?>
				
				</td>
              </tr>
            </table>			</td>
            <td width="20%">
			<? echo "[" . strtoupper($reg02[id_encargado]) . "]" ; ?>
			<? echo  ucwords(strtolower($reg02[nomUsu])) . " " . ucwords(strtolower($reg02[apeUsu])) ; ?>	 </td>
            <td width="15%" align="right">
			$ <?
			//21Ene2012
			//Traer el valor del recurso asignado según el nivel de la actividad LC, LT, Div, Act
			
			//valor del recurso PARA lOTES DE Control
			if ($reg02[nivel] == 1 ) {
				$sql04="SELECT SUM(valorActiv) sumaLC ";
				$sql04=$sql04." FROM HojaDeTiempo.dbo.ActividadesRecursos  " ;
				$sql04=$sql04." WHERE id_proyecto = " . $reg02[id_proyecto];
				$sql04=$sql04." AND id_actividad IN ( " ;
				$sql04=$sql04." 	SELECT id_actividad  " ;
				$sql04=$sql04." 	FROM HojaDeTiempo.dbo.Actividades " ;
				$sql04=$sql04." 	WHERE id_proyecto = " . $reg02[id_proyecto];
				$sql04=$sql04." 	AND actPrincipal = " . $reg02[id_actividad];
				$sql04=$sql04." 	AND nivel = 3 " ;
				$sql04=$sql04." ) " ;
				$cursor04 = mssql_query($sql04);
				//echo $sql04 ;
				if ($reg04=mssql_fetch_array($cursor04)) {
					echo "<B>" . number_format($reg04[sumaLC], "2", ",", "." ) . "</B>" ;
				}
			}
			
			//valor del recurso PARA lOTES DE TRABAJO
			if ($reg02[nivel] == 2 ) {
				$sql04="SELECT SUM(valorActiv) sumaLT ";
				$sql04=$sql04." FROM HojaDeTiempo.dbo.ActividadesRecursos  " ;
				$sql04=$sql04." WHERE id_proyecto = " . $reg02[id_proyecto];
				$sql04=$sql04." AND id_actividad IN ( " ;
				$sql04=$sql04." 	SELECT id_actividad " ;
				$sql04=$sql04." 	FROM HojaDeTiempo.dbo.Actividades " ;
				$sql04=$sql04." 	WHERE id_proyecto = "  . $reg02[id_proyecto];
				$sql04=$sql04." 	AND dependeDe = " . $reg02[id_actividad];
				$sql04=$sql04." AND nivel = 3 " ;
				$sql04=$sql04." ) " ;
				$cursor04 = mssql_query($sql04);
				//echo $sql04 ;
				if ($reg04=mssql_fetch_array($cursor04)) {
					echo number_format($reg04[sumaLT], "2", ",", "." )  ;
				}
			}

			//Traer el valor del recurso asignado para Divisiones y Actividades 
			if (($reg02[nivel] == 3) OR ($reg02[nivel] == 4)) {
				//Traer el valor del recurso asignado
				$sql04="SELECT * FROM ActividadesRecursos ";
				$sql04=$sql04." WHERE id_proyecto = " . $reg02[id_proyecto];
				$sql04=$sql04." AND id_actividad = " . $reg02[id_actividad];
				$cursor04 = mssql_query($sql04);
				//echo $sql04 ;
				if ($reg04=mssql_fetch_array($cursor04)) {
					echo "<em>" . number_format($reg04[valorActiv], "2", ",", "." ) . "</em>"  ;
				}
			}
			?>
			</td>
            <td width="1%" class="TituloTabla">     </td>       
            <td width="8%" align="center">
			<? 
			if (trim($reg02[fecha_inicio]) != "" ) {
				echo date("M d Y ", strtotime($reg02[fecha_inicio])); 
			}
			?>
			</td>
            <td width="8%" align="center">
			<? 
			if (trim($reg02[fecha_fin]) != "" ) {
				echo date("M d Y ", strtotime($reg02[fecha_fin])); 
			}
			?>
			
			</td>
            <td width="1%">&nbsp;</td>
          </tr>
		  <? } //Cierra While ?>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit10" type="submit" class="Boton" onClick="MM_openBrWindow('addHTFechasEDT.php?cualProyecto=<? echo $cualProyecto; ?>','winAddHTFe','scrollbars=yes,resizable=yes,width=900,height=400')" value="Relacionar Fechas" /></td>
          </tr>
        </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">PLANEACI&Oacute;N DE RECURSOS </td>
          </tr>
        </table>
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="20%" class="TxtTabla">
		  
	      
  		  </td>
        <td>
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">

      <tr>
        <td class="TituloTabla">Vigencia</td>
        <td class="TxtTabla"><select name="select" class="CajaTexto">
          <option value="2013">2013</option>
          <option value="2014">2014</option>
          <option value="2015">2015</option>
        </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">Divisi&oacute;n</td>
        <td class="TxtTabla">
		<select name="pfDivision" class="CajaTexto" id="pfDivision" onChange="document.form1.submit();" >
		<? if (trim($pfDivision) == "") { 
			$selDiv = "selected";
			}
		?>
		<option value="" <? echo $selDiv; ?> ><? echo ":::Todas las Divisiones:::" ; ?></option>
	<? while ($fDivReg=mssql_fetch_array($fDivCursor)) { 	
			if ($pfDivision == $fDivReg[id_division]) {
				$selDiv = "selected";
			}
			else {
				$selDiv = "";
			}
	
	?>
      	<option value="<? echo $fDivReg[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($fDivReg[nombre])) ; ?></option>
	<? } ?> 
	
	<? if ($pfDivision == "888") { 
			$selDiv = "selected";
		}
	?>
	<option value="888" <? echo $selDiv; ?> ><? echo ":::Sin División:::" ; ?></option>
    </select>
		</td>
      </tr>
      <tr>
        <td class="TituloTabla">Departamento</td>
        <td class="TxtTabla">
		<?
	//Trae los departamentos asociados la división seleccionada
	$dTSql="Select * from departamentos where id_division = " . $pfDivision ;
	$dTcursor = mssql_query($dTSql);
	
	?>
	<select name="miDpto" class="CajaTexto" id="miDpto" onChange="document.form1.submit();" >
	<? if ($miDpto == "") { 
			$selItem="selected";
		}
	?>
		<option value="" <? echo $selItem; ?> >:::Todos:::</option>
	<? while ($regdT=mssql_fetch_array($dTcursor)) { 
			if ($miDpto == $regdT[id_departamento]) {
				$selIt="selected";
			}
			else {
				$selIt="";
			}
	?>
	  	<option value="<? echo $regdT[id_departamento]; ?>" <? echo $selIt; ?> ><? echo ucwords(strtolower($regdT[nombre])) ; ?></option>
	<? } ?>
    </select>		</td>
      </tr>
      <tr>
        <td width="20%" class="TituloTabla">Filtro Categor&iacute;a </td>
        <td class="TxtTabla">
		<? 
		$fSql="SELECT * FROM categorias";
		$fCursor = mssql_query($fSql);
		if (trim($pFiltro) == "") {
			$selFiltro = "selected";
		}
		?>		<select name="pFiltro" class="CajaTexto" id="pFiltro" onChange="document.form1.submit();" >
		<option value="" <? echo $selFiltro ; ?> >:::Todas:::</option>
	   <? 
	   	while ($fReg=mssql_fetch_array($fCursor)) {
	   		if ($pFiltro == $fReg[id_categoria]) {
				$selFiltro="selected";
			}
			else {
				$selFiltro="";
			}
	    ?>
          <option value="<? echo $fReg[id_categoria] ; ?>" <? echo $selFiltro; ?> ><? echo $fReg[nombre] ; ?></option>
	   <? } ?>
        </select></td>
      </tr>
      <tr>
        <td width="20%" class="TituloTabla">Ordenar por </td>
        <td class="TxtTabla">
		<?
		if ($pOrdena == 1) {
			$opc01="checked";
			$opc02="";
		}
		else {
			$opc01="";
			$opc02="checked";
		}
		?>
		<input name="pOrdena" type="radio" value="1" onClick="document.form1.submit();" <? echo $opc01; ?> />
          Alfab&eacute;ticamente por nombre 
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="pOrdena" type="radio" value="2" onClick="document.form1.submit();" <? echo $opc02; ?> />
          Categor&iacute;a</td>
      </tr>

    </table>
		</td>
        <td width="20%" class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloTabla">Programaci&oacute;n </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="8%">Unidad</td>
            <td width="5%">Categor&iacute;a </td>
            <td>Nombre</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center">2013</td>
              </tr>
            </table>              <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
              <tr align="center">
                <td width="8%">Ene</td>
                <td width="8%">Feb</td>
                <td width="8%">Mar</td>
                <td width="8%">Abr</td>
                <td width="8%">May</td>
                <td width="8%">Jun</td>
                <td width="8%">Jul</td>
                <td width="8%">Ago</td>
                <td width="8%">Sep</td>
                <td width="8%">Oct</td>
                <td width="8%">Nov</td>
                <td width="8%">Dic</td>
              </tr>
            </table></td>
            <td width="3%">h/mes</td>
            <td width="10%">Valor Planeado <br />
            Vigencia ??</td>
            <td width="10%">Valor Planeado para el proyecto </td>
            <td width="10%">Valor Facturado </td>
          </tr>
          <tr class="TxtTabla">
            <td width="8%">2964</td>
            <td width="5%">01-A</td>
            <td>Alberto Marulanda </td>
            <td width="1%" class="TituloTabla2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TituloTabla2"> &nbsp;&nbsp;P&nbsp;&nbsp;</td>
              </tr>
              <tr>
                <td class="TituloTabla2">&nbsp;&nbsp;F&nbsp;&nbsp;</td>
              </tr>
            </table></td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr align="right">
                <td width="8%">0.25</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">0.25</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">0.25</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
              <tr align="right">
                <td width="8%">0.15</td>
                <td width="8%">0.10</td>
                <td width="8%">0.25</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
            </table></td>
            <td width="3%" align="right"><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr>
                <td align="right">0.75</td>
              </tr>
              <tr>
                <td align="right">0.50</td>
              </tr>
            </table></td>
            <td width="10%" align="right">$ 25.000.000 </td>
            <td width="10%" align="right">$ 50.000.000</td>
            <td width="10%" align="right">$ 18.000.000</td>
          </tr>
          <tr class="TxtTabla">
            <td> 900047 </td>
            <td>02-B</td>
            <td>Claudia Patricia Torres </td>
            <td width="1%" class="TituloTabla2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TituloTabla2"> &nbsp;&nbsp;P&nbsp;&nbsp;</td>
              </tr>
              <tr>
                <td class="TituloTabla2">&nbsp;&nbsp;F&nbsp;&nbsp;</td>
              </tr>
            </table></td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr align="right">
                <td width="8%">0.50</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">0.25</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">0.25</td>
                <td width="8%">0.50</td>
                <td width="8%">1</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">1</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">1</td>
                <td width="8%">0.50</td>
              </tr>
              <tr align="right">
                <td width="8%">0.25</td>
                <td width="8%">0.20</td>
                <td width="8%">0.25</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
            </table></td>
            <td width="3%" align="right"><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr>
                <td align="right">5</td>
              </tr>
              <tr>
                <td align="right">0.75</td>
              </tr>
            </table></td>
            <td width="10%" align="right">$ 18.000.000 </td>
            <td width="10%" align="right">$ </td>
            <td width="10%" align="right">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td> 17333 </td>
            <td>35</td>
            <td>Diana Rocio g&oacute;mez </td>
            <td width="1%" class="TituloTabla2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TituloTabla2"> &nbsp;&nbsp;P&nbsp;&nbsp;</td>
              </tr>
              <tr>
                <td class="TituloTabla2">&nbsp;&nbsp;F&nbsp;&nbsp;</td>
              </tr>
            </table></td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr align="right">
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
              <tr align="right">
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
            </table></td>
            <td width="3%" align="right"><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr>
                <td align="right">0.75</td>
              </tr>
              <tr>
                <td align="right">0.50</td>
              </tr>
            </table></td>
            <td width="10%" align="right">$</td>
            <td width="10%" align="right">&nbsp;</td>
            <td width="10%" align="right">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td>14055</td>
            <td>68</td>
            <td>Norbey Ni&ntilde;o </td>
            <td width="1%" class="TituloTabla2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TituloTabla2"> &nbsp;&nbsp;P&nbsp;&nbsp;</td>
              </tr>
              <tr>
                <td class="TituloTabla2">&nbsp;&nbsp;F&nbsp;&nbsp;</td>
              </tr>
            </table></td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr align="right">
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
              <tr align="right">
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
              </tr>
            </table></td>
            <td width="3%" align="right"><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
              <tr>
                <td align="right">0.75</td>
              </tr>
              <tr>
                <td align="right">0.50</td>
              </tr>
            </table></td>
            <td width="10%" align="right">&nbsp;</td>
            <td width="10%" align="right">&nbsp;</td>
            <td width="10%" align="right">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onClick="MM_openBrWindow('pnfaddProgramacion01.php','wInf','scrollbars=yes,resizable=yes,width=1200,height=500')" value="Ingresar Programaci&oacute;n" />
            <input name="Submit2" type="submit" class="Boton" onClick="MM_openBrWindow('pnfaddProgramacion2.php','wInf','scrollbars=yes,resizable=yes,width=1200,height=500')" value="Editar Programaci&oacute;n" /></td>
          </tr>
        </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;
			</td>
          </tr>
        </table>
        </td>
      </tr>
	  </ form >
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>	</td>
  </tr>
</table>

</body>
</html>
