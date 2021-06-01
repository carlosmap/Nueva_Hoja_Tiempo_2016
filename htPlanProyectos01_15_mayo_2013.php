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

//09Oct2012
//Listar las actividades
/*
$sql02="Select A.*, U.nombre nomUsu, U.apellidos apeUsu  ";
$sql02=$sql02." from Actividades A, Usuarios U";
$sql02=$sql02." where A.id_encargado *= U.unidad  ";
$sql02=$sql02." and A.id_proyecto = " . $cualProyecto;
if (trim($lstLC) != "") {
	$sql02=$sql02." and A.actPrincipal = " . $lstLC;
}
//$sql02=$sql02." order by  SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad)) ";
//$sql02=$sql02." order by CAST((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))) AS int)  ";
//$sql02=$sql02." order by actPrincipal, CAST(REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','') AS int)   ";
//$sql02=$sql02." order by actPrincipal, CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int)  ";
$sql02=$sql02." order by actPrincipal, macroactividad, CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int)  ";
*/
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
if (trim($lstLC) != "") {
	$sql02=$sql02." and A.actPrincipal = " . $lstLC;
}
$sql02=$sql02." 	) Z ";
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


//31Ene2013
//--Traer las autorizaciones del proyecto
$sql05="SELECT A.*, B.nombre nomElabora, B.apellidos apeElabora, C.nombre nomVoBo, C.apellidos apeVoBo ";
$sql05=$sql05. " FROM AutorizaEDT A, Usuarios B, Usuarios C ";
$sql05=$sql05. " WHERE A.usuElabora *= B.unidad ";
$sql05=$sql05. " AND A.unidadVoBo *= C.unidad ";
$sql05=$sql05. " AND A.id_proyecto = " . $cualProyecto;
$cursor05 = mssql_query($sql05);

//Valida que la EDT no haya sido enviada al jefe
$activaBTN = "SI";
$cursor05b = mssql_query($sql05);
if ($reg05b=mssql_fetch_array($cursor05b)) {
	if ( $reg05b[enviaAFirma] == 1) { 
		$activaBTN = "NO" ;
	}
}
//echo $activaBTN . "<br>";

//Trae la información de la Asignación de los valores por división.
$sql06="Select A.* , D.nombre ";
$sql06=$sql06." from AsignaValorDivision A, Divisiones D ";
$sql06=$sql06." where id_proyecto = " . $cualProyecto;
$sql06=$sql06." and A.id_division = D.id_division ";
$cursor06 = mssql_query($sql06);



//Verifica cuál es el 


//REVISAR DE AQUI PARA ABAJO SI EL CODIGO SIRVE

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

//echo $verBoton . "<br>";

/*

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

*/





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
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 793px; height: 30px;">
Planeaci&oacute;n de proyectos - Estructura de descomposici&oacute;n de trabajo</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
        <td width="20%">Especificaciones generales del Proyecto </td>
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
            <td width="1%">
			<? if ($verBoton=="SI") {   ?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar Programador del Proyecto" width="14" height="13" border="0" onclick="MM_openBrWindow('delHTProgProy.php?kProyecto=<? echo $pReg[id_proyecto] ; ?>&kActiv=<? echo $pReg[id_actividad]; ?>&kUnidad=<? echo $pReg[unidad]; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
			<? } ?>			</td>
          </tr>
		<? } ?>
        </table>
		<? if ($verBoton=="SI") {    ?>
		<? 
		//Sólo se pueden grabar programadores, si ya existe la EDT
		if ($cantRegEDT > 0) { 
		?>
        <input name="Submit5" type="button" class="Boton" onclick="MM_openBrWindow('addHTProgProy.php?kProyecto=<? echo $reg[id_proyecto] ; ?>&kActiv=<? echo $primerActiv; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
		<? } //cantRegEDT ?>
		<? }  ?>		</td>
	    <td width="20%" align="right">
		<table width="100%"  border="1" cellspacing="0" cellpadding="0">
          <tr>
            <td width="40%" class="TituloTabla">Fecha de inicio: </td>
            <td><? echo date("M d Y ", strtotime($reg[fechaInicio])); ?></td>
          </tr>
          <tr>
            <td width="40%" class="TituloTabla">Valor del proyecto: </td>
            <td><? echo "$ " . number_format($reg[valorProyecto], 2, ",", "."); ?></td>
          </tr>
          <tr>
            <td width="40%" class="TituloTabla">Sistema de cobro: </td>
            <td>
			<? 
			$scpSql="SELECT * ";
			$scpSql=$scpSql." FROM TipoCobroProy ";
			$scpSql=$scpSql." WHERE idTipoCobro = " . $reg[idTipoCobro];
			$scpCursor = mssql_query($scpSql);
			if ($scpReg=mssql_fetch_array($scpCursor)) {
				echo $scpReg[nomTipoCobro]; 
			}

			?>
			</td>
          </tr>
        </table>
		<? if ($verBoton=="SI") {    ?>
        <input name="Submit5" type="button" class="Boton" onclick="MM_openBrWindow('addHTEspProy.php?kProyecto=<? echo $reg[id_proyecto] ; ?>','adEspP','scrollbars=yes,resizable=yes,width=500,height=300')" value="Insertar" />
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
            <td class="TituloUsuario">.: ASIGNACI&Oacute;N DE RECURSOS POR DIVISI&Oacute;N </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Divisi&oacute;n</td>
            <td width="20%">Valor Real </td>
            <td width="20%">Valor Asignado </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
		  <? while ($reg06=mssql_fetch_array($cursor06)) { ?>
          <tr class="TxtTabla">
            <td><? echo strtoupper($reg06[nombre]); ?></td>
            <td width="20%" align="right">$ <? echo number_format($reg06[valorReal],0,",","."); ?></td>
            <td width="20%" align="right">$ <? echo number_format($reg06[valorAsignado],0,",","."); ?></td>
            <td width="1%"><img src="img/images/actualizar.jpg" width="19" height="17" onclick="MM_openBrWindow('upHtAsignaValorDiv.php?cualProyecto=<? echo $cualProyecto; ?>&div=<? echo $reg06[id_division] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" /></td>
            <td width="1%"><img src="img/images/Del.gif" width="14" height="13" onclick="MM_openBrWindow('delHtAsignaValorDiv.php?cualProyecto=<? echo $cualProyecto; ?>&div=<? echo $reg06[id_division] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" /></td>
          </tr>
		  <?
			$total_real=$total_real+$reg06[valorReal] ;
			$total_asigando=$total_asigando+$reg06[valorAsignado] ;
			} ?>
		 <tr class="TxtTabla">
			<td width="20%" align="center" class="TituloTabla2">Total</td>
			<td width="20%" align="right"><?= number_format($total_real,0,",",".") ?></td>
			<td width="20%" align="right"><?=number_format($total_asigando,0,",",".") ?></td>
			<td align="right"></td>
			<td align="right"></td>
		</tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('addHtAsignaValorDiv.php?cualProyecto=<? echo $cualProyecto; ?>','addHTVAD','scrollbars=yes,resizable=yes,width=500,height=400')" value="Asignar Valores Divisi&oacute;n" /></td>
          </tr>
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
        <td width="15%" height="20" class="FichaAct">EDT</td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos02.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Participantes</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos03.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n</a></td>
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
                      <td width="20%" align="left" class="TituloTabla">lote de control </td>
                      <td align="left" class="TxtTabla">
					  <?
					  //Trae la información de los lotes de control
					  $lstSqlLC="SELECT * FROM Actividades ";
					  $lstSqlLC=$lstSqlLC." WHERE id_proyecto = " .  $cualProyecto ;
					  $lstSqlLC=$lstSqlLC." AND nivel = '1' ";
					  $lstCursorLC = mssql_query($lstSqlLC);

					  
					  ?>
					  <select name="lstLC" class="CajaTexto" id="lstLC">
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
                      <td width="5%" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Consultar" /></td>
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
            <td class="TituloUsuario">Estructura de descomposici&oacute;n de trabajo EDT </td>
          </tr>
        </table>
		</td>
      </tr>
	  </ form >
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="5%">Identificador</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td>LOTE DE CONTROL / LOTE DE TRABAJO / DIVISI&Oacute;N / ACTIVIDAD</td>
            <td width="20%">Responsable</td>
            <td width="15%">Valor Presupuestado </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
		   <? while ($reg02=mssql_fetch_array($cursor02)) { ?>
          <tr class="TxtTabla">
            <td width="5%"><? echo strtoupper($reg02[macroactividad]) ; ?></td>
            <td width="1%"><? echo $reg02[id_actividad] ; ?></td>
            <td width="1%">
			<? if (trim($verBoton) == "SI") { ?>
			<? if (trim($activaBTN) == "SI") { ?>
			<? if (trim($reg02[nivel]) == "1") { ?>
			<a href="#"><img src="img/images/icoNew.gif" alt="Ingresar Lote de trabajo" width="16" height="15" border="0" onclick="MM_openBrWindow('addHtLoteTrabajo.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[id_actividad] ; ?>','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" /></a>
			<? } ?>
			<? if (trim($reg02[nivel]) == "2") { ?>
			<a href="#"><img src="img/images/icoNew.gif" alt="Ingresar División" width="16" height="15" border="0" onclick="MM_openBrWindow('addHtDivision.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[dependeDe] ; ?>&cualLT=<? echo $reg02[id_actividad] ; ?>','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" /></a>
			<? } ?>
			<? if (trim($reg02[nivel]) == "3") { ?>
			<a href="#"><img src="img/images/icoNew.gif" alt="Ingresar Actividades" width="16" height="15" border="0" onclick="MM_openBrWindow('addHtActividades.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[dependeDe] ; ?>&cualDiv=<? echo $reg02[id_actividad] ?>&cualIDdiv=<? echo $reg02[id_division] ?>','winHH','scrollbars=yes,resizable=yes,width=1150,height=400')" /></a>
			<? } ?>
			<?  } //if activaBTN) == "SI ?>
			<? } // if verBoton ?>			
			</td>
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
            <td width="15%" align="right" >
			 <?
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
					echo "<B>$" . number_format($reg04[sumaLC], "2", ",", "." ) . "</B>" ;
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
					echo "$".number_format($reg04[sumaLT], "2", ",", "." )  ;
				}
			}

			//Traer el valor del recurso asignado para Divisiones 
			if (($reg02[nivel] == 3)) {
				$vla_act_div=0;
				$val_div=0;
				//Traer el valor del recurso asignado
				$sql04="SELECT * FROM ActividadesRecursos ";
				$sql04=$sql04." WHERE id_proyecto = " . $reg02[id_proyecto];
				$sql04=$sql04." AND id_actividad = " . $reg02[id_actividad];
				$cursor04 = mssql_query($sql04);
				//echo $sql04 ;
				echo '<table width="100%" cellspacing="1" cellpadding="0" border="0" bgcolor="#FFFFFF">';
				echo " <tr class='TxtTabla' >";
				echo '	<td rowspan="2" align="center" height="10%"  > <img src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif" title="vlAsig (Valor asignado) - vlDis (Valor disponible)" ></td>
					 	<td align="left" height="30%" ><b>vlAsig</b></td>';
				echo " 	<td align='right' height='60%'>";
				if ($reg04=mssql_fetch_array($cursor04)) {
					$val_div=$reg04[valorActiv];
					echo "<em>$" . number_format($reg04[valorActiv], "2", ",", "." ) . "</em>"  ;
				}
				else
					echo "<em>$0,00</em>"  ;
				echo " 	</td>";
				echo " </tr>";

				echo " <tr  class='TxtTabla'>";
				echo " 	<td align='left' height='30%' ><b>vlDis</b></td>";
				echo " 	<td align='right' height='60%'>";

					$cur_valor_acti=mssql_query("select SUM(valorActiv) as valor_actividades from ActividadesRecursos where id_proyecto=".$reg02[id_proyecto]." and id_actividad in (
										select id_actividad from Actividades where id_proyecto=".$reg02[id_proyecto]." and dependeDe=".$reg02[id_actividad]." and nivel=4)");


					if($datos_act=mssql_fetch_array($cur_valor_acti))
					{
						$vla_act_div=$datos_act["valor_actividades"];
					}
					$val_dispo=((float)$val_div)-((float)$vla_act_div);
					echo "<em>$". number_format($val_dispo, "2", ",", "." ) . "</em>";
//echo "<br><br> $val_div-$vla_act_div"; settype();
				echo " 	</td>";
				echo " </tr>";

				echo "</table>";
			}

			//Traer el valor del recurso asignado para las  Actividades 
			if (($reg02[nivel] == 4)) {
				//Traer el valor del recurso asignado
				$sql04="SELECT * FROM ActividadesRecursos ";
				$sql04=$sql04." WHERE id_proyecto = " . $reg02[id_proyecto];
				$sql04=$sql04." AND id_actividad = " . $reg02[id_actividad];
				$cursor04 = mssql_query($sql04);
				//echo $sql04 ;
				if ($reg04=mssql_fetch_array($cursor04)) {
					echo "<em>$" . number_format($reg04[valorActiv], "2", ",", "." ) . "</em>"  ;
				}
			}
			?>
			</td>
            <td width="1%">
			<? if (trim($verBoton) == "SI") { ?>
			<? if (trim($activaBTN) == "SI") { ?>
			<? if (trim($reg02[nivel]) == "1") { ?>
            <img src="img/images/imgFiles.gif" alt="Editar Estructura" width="14" height="14" onclick="MM_openBrWindow('upEDTestLC.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[id_actividad] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
			<? if (trim($reg02[nivel]) == "2") { ?>
            <img src="img/images/imgFiles.gif" alt="Editar Estructura" width="14" height="14" onclick="MM_openBrWindow('upEDTestLT.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[id_actividad] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
			<? if (trim($reg02[nivel]) == "3") { ?>
            <img src="img/images/imgFiles.gif" alt="Editar Estructura" width="14" height="14" onclick="MM_openBrWindow('upEDTestDiv.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[dependeDe] ; ?>&cualDIvision=<? echo $reg02[id_actividad] ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
            <? if (trim($reg02[nivel]) == "4") { ?>
			<img src="img/images/imgFiles.gif" alt="Editar Estructura" width="14" height="14" onclick="MM_openBrWindow('upEDTest.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualDiv=<? echo $reg02[dependeDe] ; ?>&cualACtividad=<? echo $reg02[id_actividad] ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
			<? } ?>   
			<?  } //if activaBTN) == "SI ?>
			<? } //if verBoton ?>         
            <td width="1%">
			<? if (trim($verBoton) == "SI") { ?>
			<? if (trim($activaBTN) == "SI") { ?>
			<? if (trim($reg02[nivel]) == "1") { ?>
            <img src="img/images/actualizar.jpg" width="19" height="17" onclick="MM_openBrWindow('upEDTregLC.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[id_actividad] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
			<? if (trim($reg02[nivel]) == "2") { ?>
            <img src="img/images/actualizar.jpg" width="19" height="17" onclick="MM_openBrWindow('upEDTregLT.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[id_actividad] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
			<? if (trim($reg02[nivel]) == "3") { ?>
            <img src="img/images/actualizar.jpg" width="19" height="17" onclick="MM_openBrWindow('upEDTregDiv.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[dependeDe] ; ?>&cualDIvision=<? echo $reg02[id_actividad] ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
            <? if (trim($reg02[nivel]) == "4") { ?>
			<img src="img/images/actualizar.jpg" width="19" height="17" onclick="MM_openBrWindow('upEDTreg.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualDiv=<? echo $reg02[dependeDe] ; ?>&cualACtividad=<? echo $reg02[id_actividad] ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
			<? } ?>			
			<?  } //if activaBTN) == "SI ?>
			<? } // if verBoton ?>

            <td width="1%">
			<? if (trim($verBoton) == "SI") { ?>
			<? if (trim($activaBTN) == "SI") { ?>
			<?
			$verEliminar =  0 ;
			//Valida que no existan registros asociados
			$sql03="SELECT COUNT(*) cuantos ";
			$sql03=$sql03." FROM Actividades ";
			$sql03=$sql03." WHERE id_proyecto = " . $reg02[id_proyecto];
			$sql03=$sql03." and ( ";
			$sql03=$sql03." (actPrincipal = ".$reg02[id_actividad]." and dependeDe <> '0') ";
			$sql03=$sql03." or dependeDe= " . $reg02[id_actividad] ;
			$sql03=$sql03." ) ";
			$cursor03 = mssql_query($sql03);
			if ($reg03=mssql_fetch_array($cursor03)) {
				$verEliminar =  $reg03[cuantos] ;
			}

			if ($verEliminar == 0) {
			?>
			<? if (trim($reg02[nivel]) == "1") { ?>
            <img src="img/images/Del.gif" width="14" height="13" onclick="MM_openBrWindow('delEDTregLC.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[id_actividad] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
			<? if (trim($reg02[nivel]) == "2") { ?>
            <img src="img/images/Del.gif" width="14" height="13" onclick="MM_openBrWindow('delEDTregLT.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[id_actividad] ; ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
			<? if (trim($reg02[nivel]) == "3") { ?>
            <img src="img/images/Del.gif" width="14" height="13" onclick="MM_openBrWindow('delEDTregDiv.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualLT=<? echo $reg02[dependeDe] ; ?>&cualDIvision=<? echo $reg02[id_actividad] ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
            <? } ?>
            <? if (trim($reg02[nivel]) == "4") { ?>
			<img src="img/images/Del.gif" width="14" height="13" onclick="MM_openBrWindow('delEDTreg.php?cualProyecto=<? echo $cualProyecto; ?>&cualLC=<? echo $reg02[actPrincipal] ; ?>&cualDiv=<? echo $reg02[dependeDe] ; ?>&cualACtividad=<? echo $reg02[id_actividad] ?>','winDedt','scrollbars=yes,resizable=yes,width=800,height=400')" />
			<? } ?>
			<? } // if de VerEliminar ?>
			<?  } //if activaBTN) == "SI ?>
			<? } // if verBoton ?>
			</td>
          </tr>
		  <? } //Cierra While ?>
        </table>
		<? if (trim($verBoton) == "SI") { ?>
		<? if (trim($activaBTN) == "SI") { ?>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla">
			  <? if (mssql_num_rows($cursor02) == 0 ) { ?>
			  <input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('PlantillaXLS.xls','winPlantilla','scrollbars=yes,resizable=yes,width=1000,height=500')" value="Descargar Plantilla .xls" />
			  <input name="Submit3" type="submit" class="Boton" onclick="MM_openBrWindow('addHTImportED.php?cualProyecto=<? echo $cualProyecto; ?>','winImportarP','scrollbars=yes,resizable=yes,width=650,height=300')" value="Importar EDT" />
			  <? } ?>
              <input name="Submit22" type="submit" class="Boton" onclick="MM_openBrWindow('addHtLoteControl.php?cualProyecto=<? echo $cualProyecto; ?>','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" value="Nuevo Lote de control" />
              <?
			  //Solo muestra los botones si hay registros en la EDT
			  if ($cantRegEDT > 0) {
			  ?>
			  <?
			  //Verifica la existencia de Responsables de actividades y Participantes de actividades
			$verEliminarEDT="NO";
			$sqlV01="select COUNT(*) hayResponsables from ResponsablesActividad ";
			$sqlV01=$sqlV01." where id_proyecto = " .  $cualProyecto ;
			$cursorV01 = mssql_query($sqlV01);
			if ($regV01=mssql_fetch_array($cursorV01)) {
				if ($regV01[hayResponsables] > 0) {
					$verEliminarEDT =  "SI" ;
				}
			}

			$sqlV02="select COUNT(*) hayParticipantes from ParticipantesActividad ";
			$sqlV02=$sqlV02." where id_proyecto = " .  $cualProyecto ;
			$cursorV02 = mssql_query($sqlV02);
			if ($regV02=mssql_fetch_array($cursorV02)) {
				if ($regV02[hayParticipantes] > 0) {
					$verEliminarEDT =  "SI" ;
				}
			}
			
			if ($verEliminarEDT ==  "NO") {
			  ?>
			  <input name="Submit7" type="submit" class="Boton" onclick="MM_openBrWindow('delEDTRecursos.php?cualProyecto=<? echo $cualProyecto; ?>','winDelEDT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Eliminar recursos actividades EDT" />

			  <input name="Submit7" type="submit" class="Boton" onclick="MM_openBrWindow('delEDTtotal.php?cualProyecto=<? echo $cualProyecto; ?>','winDelEDT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Eliminar toda la EDT" />
			<?
			}
			?>
			<? } // if ($cantRegEDT > 0)  
			if(($_SESSION["sesUnidadUsuario"] == 18121)or($_SESSION["sesUnidadUsuario"] == 20400)or($_SESSION["sesUnidadUsuario"] == 15712)or($_SESSION["sesUnidadUsuario"] == 12974))
			{
			?>  
			  <input name="Submit7" type="submit" class="Boton" onclick="MM_openBrWindow('delEDTFechaParticipantes.php?cualProyecto=<? echo $cualProyecto; ?>','winDelEDT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Eliminar EDT Total" />
			<?
			}
			?>
			  </td>
			  
            </tr>
          </table>
		  <?  } //if activaBTN) == "SI ?>
		  <? } // if verBoton ?>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Firmas de autorizaci&oacute;n de la EDT </td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr class="TituloTabla2">
              <td width="50%">Elaboraci&oacute;n</td>
              <td width="50%">VoBo EDT  del proyecto </td>
<!--              <td><span class="TituloTabla">Fecha de inicio del proyecto</span></td> -->
            </tr>
			<?
			while ($reg05=mssql_fetch_array($cursor05)) {
			//id_proyecto, secuencia, fechaIniProy, usuElabora, fechaElabora, comentaElabora, enviaAFirma, validaElabora, validaVoBo, unidadVoBo, comentaVoBo, fechaVoBo, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			//B.nombre nomElabora, B.apellidos apeElabora, C.nombre nomVoBo, C.apellidos apeVoBo
			?>
            <tr valign="top" class="TxtTabla">
              <td width="33%">[<? echo $reg05[usuElabora] ; ?>]<br />
                <? echo ucwords(strtolower($reg05[apeElabora])) . ", " . ucwords(strtolower($reg05[nomElabora])) ; ?> <br />
                Fecha de elaboraci&oacute;n: <? echo date("M d Y ", strtotime($reg05[fechaElabora])); ?> <br />
                Enviado a Director: 
				<? 
				$verBtnEnviaFirma="NO";
				if ($reg05[enviaAFirma] == 1) { 
						$verBtnEnviaFirma="SI";
				?>
				<img src="img/images/Si.gif" width="16" height="14" />
				<? } ?>
                <br />
				EDT finalizada:
				<? if ($reg05[validaElabora] == 1) { ?>
				<img src="img/images/Si.gif" width="16" height="14" />
				<? } ?>
                <br />				
                Observaciones: <? echo $reg05[comentaElabora] ; ?>
				</td>
              <td width="33%">
			  [<? echo $reg05[unidadVoBo] ; ?>]
			  <?
			  //Valida si es quien firma
			  $esQuienFirma="NO";
			  if ($reg05[unidadVoBo] == $laUnidad) {
			  	$esQuienFirma="SI";
			  }
			  ?>
              <br />
			  <? echo ucwords(strtolower($reg05[apeVoBo])) . ", " . ucwords(strtolower($reg05[nomVoBo])) ; ?>
              <br />
              Fecha de VoBo: <? if(trim($reg05[fechaVoBo])!=""){ echo date("M d Y ", strtotime($reg05[fechaVoBo])); } ?><br />
              VoBo. 
				<? 

				$muestraVoBoEDT = "NO";
				if ( $reg05[validaVoBo] == 1) { 
					$muestraVoBoEDT = "SI" ;
				?>
				<img src="img/images/Aprobado.gif" width="21" height="24" />
				<? } 
				else {
				?>			  
			  <img src="img/images/NoAprobado.gif" width="20" height="22" />
			  <? } ?>
			  
			  <br />
			  Observaciones: <? echo $reg05[comentaVoBo] ; ?></td>
<!--              <td>
			  <? 
/*
			  if (trim($reg05[fechaIniProy]) != "") { 
				  echo date("M d Y ", strtotime($reg05[fechaIniProy])); 
			  }
*/
			  ?>
			  </td>
-->
            </tr>
			<? } // while Reg5 ?>
            <tr class="TxtTabla">
              <td width="33%" align="right">
			  <? if (trim($verBoton) == "SI") { ?>
			  <? if (mssql_num_rows($cursor02) > 0 ) { ?>
              <? if (mssql_num_rows($cursor05) == 0 ) { ?>			  
              <input name="Submit6" type="submit" class="Boton" onclick="MM_openBrWindow('enviarEDT.php?cualProyecto=<? echo $cualProyecto; ?>','winSendEDT','scrollbars=yes,resizable=yes,width=900,height=300')" value="Enviar a Director" />
			  <? } 
			  else { 
			  		if ($verBtnEnviaFirma=="NO") { 
			  	?>
			  <input name="Submit6" type="submit" class="Boton" onclick="MM_openBrWindow('enviarEDT.php?cualProyecto=<? echo $cualProyecto; ?>','winSendEDT','scrollbars=yes,resizable=yes,width=900,height=300')" value="Enviar a Director" />
			  <? 
			  	}
			  } ?>
			   <? } ?>
		      <? } //if verBoton ?>  			  </td>
              <td width="33%" align="right">
			  <?
			  if ( $esQuienFirma=="SI") {
			  ?>
			  <?
			  if ($muestraVoBoEDT == "NO") {
			  ?>
			  <input name="Submit62" type="submit" class="Boton" onclick="MM_openBrWindow('aprobarEDT.php?cualProyecto=<? echo $cualProyecto; ?>','winApEDT','scrollbars=yes,resizable=yes,width=900,height=300')" value="VoBo de la EDT" />
			  <? } //IF muestraVoBoEDT ?>			  
			  <? } //IF esQuienFirma ?>			  </td>
<!--              <td>&nbsp;</td> -->
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="5" class="TituloTabla"> </td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TxtTabla">&nbsp;</td>
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
    <td>&nbsp;    </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
