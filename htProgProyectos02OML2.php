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
//Identificar si el usuario activo ver� toda la informaci�n o s�lo sus TMPActividadesHT2
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

//Si alguna de las variables es > 0 el usuario podr� ver todo
$todo= $esDC + $esProgP + $esOrdG ;
if ($todo > 0) {
	$verProyecto="SI";
}
else {
	$verProyecto="NO";
}

//Cierra 10Jun2008

if (trim($pOrdenAct)=="") {
	$pOrdenAct=1;
}

//11Sep2008
//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$fDivSql="Select * from divisiones ";
$fDivSql=$fDivSql." where (nombre <> '' and nombre <> 'sd') ";
$fDivSql=$fDivSql."and estadoDiv = 'A' ";
$fDivSql=$fDivSql." order by nombre ";
$fDivCursor = mssql_query($fDivSql);

//22Ene2008
//Trae el nombre de las TMPActividadesHT2 asociadas al proyecto
//Si el usuario es Director, Coordinador, Ordenador del gasto o Programador del proyecto
//ve todas las TMPActividadesHT2 del proyecto
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
		//$sql2=$sql2." order by A.nivelesActiv " ;
		
		//11Sep2008
		//Para ordenar las TMPActividadesHT2 por nombre de la actividad y por macroactividad sin perder la 
		//jerarquia de TMPActividadesHT2 y subTMPActividadesHT2
		if ($pOrdenAct==1) {
			$sql2=$sql2." order by A.actPrincipal , nivelesActiv " ;
		}
		else {
			$sql2=$sql2." order by A.macroactividad, A.actPrincipal , nivelesActiv " ;
		}
	}
	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) {
		$primerActiv =  $reg2[id_actividad] ;
	}
}
//Sino, se trata de responsable de actividad o programadores de actividad y ven sus TMPActividadesHT2
else {
	$sql2="Select A.*, U.nombre nomUsu, U.apellidos apeUsu  ";
	$sql2=$sql2." from ( " ;
	$sql2=$sql2." Select id_actividad " ;
	$sql2=$sql2." from Actividades " ;
	$sql2=$sql2." where id_proyecto = " . $cualProyecto ;
	$sql2=$sql2." and id_encargado =" . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from ParticipantesActividad " ;
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
//	$sql2=$sql2." order by A.nivelesActiv " ;
	//11Sep2008
	//Para ordenar las TMPActividadesHT2 por nombre de la actividad y por macroactividad sin perder la 
	//jerarquia de TMPActividadesHT2 y subTMPActividadesHT2
	if ($pOrdenAct==1) {
		$sql2=$sql2." order by A.actPrincipal , nivelesActiv " ;
	}
	else {
		$sql2=$sql2." order by A.macroactividad, A.actPrincipal , nivelesActiv " ;
	}

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
//Trae la informaci�n de asignaciones realizadas a la actividad seleccionada
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

//10Sep2008
//Para incluir el filtro del orden de las registros que aparecen en recursos
if (trim($pOrdena)=="") {
	$pOrdena=1;
}

//28Ago2008
//Para incluir la categor�a
/*
$sql3="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo,  ";
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario , C.nombre nomCat " ;
$sql3=$sql3." from asignaciones A, Usuarios U, Horarios H, Categorias C " ;
$sql3=$sql3." where A.unidad = U.unidad  " ;
$sql3=$sql3." and A.IDhorario = H.IDhorario  " ;
$sql3=$sql3." And U.id_categoria = C.id_categoria " ;
$sql3=$sql3." and A.id_proyecto = " . $cualProyecto ;
$sql3=$sql3." and A.id_actividad = " . $cualActividad ;
if(trim($pFiltro)!="") {
	$sql3=$sql3." and U.id_categoria = " . $pFiltro ;
}
$sql3=$sql3." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, " ;
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario , C.nombre " ;
if ($pOrdena == 1) {
$sql3=$sql3." ORDER BY U.apellidos " ;
}
if ($pOrdena == 2) {
$sql3=$sql3." ORDER BY C.nombre  " ;
}
*/

//09Oct2008
//Para sacar el listado de usuarios asociados a una actividad 
$sql3u="select distinct A.unidad, U.nombre, U.apellidos, C.nombre nomCat   ";
$sql3u=$sql3u." from asignaciones A, Usuarios U, Horarios H, Categorias C , Departamentos D ";
$sql3u=$sql3u." where A.unidad = U.unidad  ";
$sql3u=$sql3u." and A.IDhorario = H.IDhorario  ";
$sql3u=$sql3u." And U.id_categoria = C.id_categoria ";
$sql3u=$sql3u." And U.id_departamento = D.id_departamento ";
$sql3u=$sql3u." and A.id_proyecto = " . $cualProyecto ;
$sql3u=$sql3u." and A.id_actividad = " . $cualActividad ;
if(trim($pFiltro)!="") {
	$sql3u=$sql3u." and U.id_categoria = " . $pFiltro ;
}
if(trim($pfDivision)!="") {
	if ($pfDivision == "888") { 
		$sql3u=$sql3u." and D.id_division > 25" ;
	}
	else {
		$sql3u=$sql3u." and D.id_division = " . $pfDivision ;
		if(trim($miDpto)!="") {
			$sql3u=$sql3u." and D.id_departamento = " . $miDpto ;	
		}
	}
}
if ($pOrdena == 1) {
$sql3u=$sql3u." ORDER BY U.apellidos " ;
}
if ($pOrdena == 2) {
$sql3u=$sql3u." ORDER BY C.nombre  " ;
}
$cursor3u = mssql_query($sql3u);


/*
//28Ago2008
//Para incluir Divisi�n y Departamento
$sql3="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo,  ";
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario , C.nombre nomCat  " ;
$sql3=$sql3." from asignaciones A, Usuarios U, Horarios H, Categorias C , Departamentos D " ;
$sql3=$sql3." where A.unidad = U.unidad  " ;
$sql3=$sql3." and A.IDhorario = H.IDhorario  " ;
$sql3=$sql3." And U.id_categoria = C.id_categoria " ;
$sql3=$sql3." And U.id_departamento = D.id_departamento " ;
$sql3=$sql3." and A.id_proyecto = " . $cualProyecto ;
$sql3=$sql3." and A.id_actividad = " . $cualActividad ;
if(trim($pFiltro)!="") {
	$sql3=$sql3." and U.id_categoria = " . $pFiltro ;
}
if(trim($pfDivision)!="") {
	if ($pfDivision == "888") { 
		$sql3=$sql3." and D.id_division > 25" ;
	}
	else {
		$sql3=$sql3." and D.id_division = " . $pfDivision ;
		
		if(trim($miDpto)!="") {
			$sql3=$sql3." and D.id_departamento = " . $miDpto ;	
		}
	}
}
$sql3=$sql3." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, " ;
$sql3=$sql3." U.nombre, U.apellidos, H.NomHorario , C.nombre " ;
if ($pOrdena == 1) {
$sql3=$sql3." ORDER BY U.apellidos " ;
}
if ($pOrdena == 2) {
$sql3=$sql3." ORDER BY C.nombre  " ;
}
//echo $sql3;
$cursor3 = mssql_query($sql3);

*/

//1Jul2008
//Trae la informaci�n de los costos directos asociados a una actividad
$CDsql="select C.* , U.nombre, U.apellidos ";
$CDsql=$CDsql." from HojaDeTiempo.dbo.ActividadesCostosD C, HojaDeTiempo.dbo.Usuarios U ";
$CDsql=$CDsql." where C.unidad = U.unidad ";
$CDsql=$CDsql." and C.id_proyecto =" . $cualProyecto ;
$CDsql=$CDsql." and C.id_actividad =" . $cualActividad ;
$CDcursor = mssql_query($CDsql);

//8Ago2008
//Trae la informaci�n del personal externos 
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
	<form name="form1" id="form1" method="post" action="">
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
		<? $codProyecto = trim($reg[codigo]) ;?>
		<?
		//27Ene2009
		//Traer los cargos adicionales del proyecto
		$sqlCargos="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		$sqlCargos=$sqlCargos." where id_proyecto = " . trim($reg[id_proyecto]) ;
		$cursorCargos = mssql_query($sqlCargos);
		while ($regCargos=mssql_fetch_array($cursorCargos)) {
			echo  "<br>". "." . $regCargos[cargos_adicionales] ;
		}
		
		?>
		</td>
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
        <input name="Submit5" type="button" class="Boton" onclick="MM_openBrWindow('addProgProy.php?kProyecto=<? echo $reg[id_proyecto] ; ?>&kActiv=<? echo $primerActiv; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
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
            <td class="TxtTabla"><a href="htProgProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr >
        <td width="15%" height="20" class="FichaInAct"><a href="htProgProyectos01.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >EDT</a></td>
        <td width="15%" height="20" class="FichaAct">Participantes</td>
        <td width="15%" height="20" class="FichaInAct"><a href="htProgProyectos03.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Programaci&oacute;n</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="htProgProyectos04.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Resumen</a></td>
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
                  <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
      <tr>
        <td class="TituloTabla">Lote de control</td>
        <td align="left" class="TxtTabla">
			<?
                $sqlLc = "select * from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND nivel = 1";
                $qryLc = mssql_query( $sqlLc );			
            ?>
            <select name="lc" class="CajaTexto" id="lc" onchange="document.form1.submit();" >
                <option value="" >:::Todos los Lotes de control:::</option>
                <? while ( $rwLc = mssql_fetch_array( $qryLc )) { 	
                        if ($lc == $rwLc[id_actividad]) {
                            $selLc = "selected";
                        }
                        else {
                            $selLc = "";
                        }
                ?>
                <option value="<?= $rwLc[id_actividad]; ?>" <? echo $selLc; ?> ><?= "[".$rwLc[macroactividad]."] ".ucwords(strtolower($rwLc[nombre])) ; ?></option>
                <? } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="TituloTabla">Lote de trabajo</td>
        <td align="left" class="TxtTabla">
        <?
			$sqlLt = "select * from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND nivel = 2 ";
			if( trim($lc) != "" )
				$sqlLt .= "AND dependeDe = ".$lc;			
			$qryLt = mssql_query( $sqlLt );			
		?>
        <select name="lt" class="CajaTexto" id="lt" onchange="document.form1.submit();" >
          <option value="" >:::Todas los Lotes de trabajo:::</option>
          <? while ($rwLt=mssql_fetch_array($qryLt)) { 	
					if ($lt == $rwLt[id_actividad]) 
						$selLt = "selected";
					else 
						$selLt = "";
			?>
          <option value="<?= $rwLt[id_actividad]; ?>" <? echo $selLt; ?> ><?= "[".$rwLt[macroactividad]."] ".ucwords(strtolower($rwLt[nombre])) ; ?></option>
          <? } ?>
        </select>
        </td>
      </tr>
      <tr>
        <td class="TituloTabla">Divisi&oacute;n</td>
        <td align="left" class="TxtTabla">
        <?
			$sqlDv = "select distinct macroactividad, id_division, nombre from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND nivel = 3";
			if( trim($lt) != "" )
				$sqlDv .= "AND dependeDe = ".$lt;
			if( trim($lc) != "" )
				$sqlDv .= "AND actPrincipal = ".$lc;
			#echo $sqlDv."<br />";		
			$qryDv = mssql_query( $sqlDv );			
		?>
		<select name="pfDivision" class="CajaTexto" id="pfDivision" onChange="document.form1.submit();" >
            <option value="" >:::Todas las Divisiones:::</option>
            <?	while ($fDivReg=mssql_fetch_array($qryDv)) { 	
                	if ($pfDivision == $fDivReg[id_division]) 
						$selDiv = "selected";
					else 
						$selDiv = "";            
            ?>
            <option value="<? echo $fDivReg[id_division]; ?>" <? echo $selDiv; ?> ><?= "[".$fDivReg[macroactividad]."] ".ucwords(strtolower($fDivReg[nombre])) ; ?></option>
    	    <? } ?> 	
	    </select>
    </td>
      </tr>
      <tr>
        <td class="TituloTabla">Perf&iacute;l</td>
        <td align="left" class="TxtTabla"><?
			$perfiles = array( '::: Todos los Pefiles :::', 'Coordinador', 'Director', 'Programador', 'Ordenador de gastos', 'Participante', 'Responsable'  );
			$regPer = 0;
		?>
          <select name="pr" class="CajaTexto" id="pr" onchange="document.form1.submit();" >
            <!--<option value="" ></option>-->
            <? 
			while ( $regPer < 7) { 	
				if ( $pr == $regPer ) 
					$selPer = "selected";
				else 
					$selPer = "";
		  ?>
            <option value="<?= $regPer; ?>" <?= $selPer; ?> >
              <?= ucwords( strtolower( $perfiles[$regPer] ) ) ?>
              </option>
            <? 
		  		$regPer++;
		  	} 
		  ?>
          </select></td>
      </tr>
      <tr class="TituloTabla">
        <td colspan="2" height="2px"></td><!-- &nbsp; -->
      </tr>
      <tr>
        <td class="TituloTabla">Divisi&oacute;n</td>
        <td align="left" class="TxtTabla"><?
			$sqlDv = "SELECT DISTINCT * FROM HojaDeTiempo.dbo.Divisiones WHERE estadoDiv IS NOT NULL";
			#if( trim($lt) != "" )
			#	$sqlDv .= "AND dependeDe = ".$lt;
			#echo $sqlDv."<br />";		
			$qryDv = mssql_query( $sqlDv );			
		?>
          <select name="pfDivisionOrg" class="CajaTexto" id="pfDivisionOrg" onchange="document.form1.submit();" >
            <option value="" >:::Todas las Divisiones:::</option>
            <?	while ($fDivReg=mssql_fetch_array($qryDv)) { 	
                	if ($pfDivisionOrg == $fDivReg[id_division]) 
						$selDiv = "selected";
					else 
						$selDiv = "";            
            ?>
            <option value="<? echo $fDivReg[id_division]; ?>" <? echo $selDiv; ?> >
              <?= ucwords(strtolower($fDivReg[nombre])) ; ?>
              </option>
            <? } ?>
          </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">Departamento</td>
        <td align="left" class="TxtTabla">
	<?
	//Trae los departamentos asociados la divisi�n seleccionada
	if( trim($pfDivision) != "" ){
		$pfDv = "id_division = ".$pfDivision;
		$and = " and ";
	}
	else{
		$pfDv = "";
		$and = " ";
	}
	
	if( trim($pfDivisionOrg) != "" ){
		if( trim($pfDivision) != "" ){
			$or = " or ";
			
		}
		else
			$or = "";
		$and = " and ";
		$pfDv2 = $or."id_division = ".$pfDivisionOrg;
	}
	else{
		$pfDv2 = "";
		$and = " ";
	}
	
	$dTSql="Select * from departamentos where ".$pfDv." ".$pfDv2." ".$and." estadoDpto = 'A' order by nombre" ;
	$dTcursor = mssql_query($dTSql);
	?>
	<select name="miDpto" class="CajaTexto" id="miDpto" onChange="document.form1.submit();" >
	<? 
		if ($miDpto == "")
			$selItem="selected";		
	?>
		<option value="" <? echo $selItem; ?> >:::Todos:::</option>
	<? 
		while ($regdT=mssql_fetch_array($dTcursor)) { 
			if ( $miDpto == $regdT[id_departamento] ) 
				$selIt = "selected";			
			else 
				$selIt = "";
	?>
	  		<option value="<? echo $regdT[id_departamento]; ?>" <? echo $selIt; ?> ><? echo ucwords(strtolower($regdT[nombre])) ; ?></option>
	<? } ?>
    </select>		
    </td>
    </tr>
    <tr>
    	<td width="20%" class="TituloTabla">Categor&iacute;a </td>
		<td align="left" class="TxtTabla">
		<? 
		$fSql="SELECT * FROM categorias";
		$fCursor = mssql_query($fSql);
		if (trim($pFiltro) == "") {
			$selFiltro = "selected";
		}
		?>
        <select name="pFiltro" class="CajaTexto" id="pFiltro" onChange="document.form1.submit();" >
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
        <td align="left" class="TxtTabla">
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
            <td class="TituloUsuario">Participantes asociados a los Lotes de control / Lotes de trabajo / Divisiones / Actividades del proyecto </td>
          </tr>
        </table>
        <?
			$sqlActResponsable = "select distinct pro.nombre Proyecto, usuarios.*
								from
									HojaDeTiempo.dbo.Proyectos pro,
									(   Select
										pro.id_proyecto idProyecto, uCoordinador.nombre uNombre, uCoordinador.apellidos uApellido, uCoordinador.unidad uUnidad,
										cat.nombre nCategoria, uCoordinador.id_categoria Categoria, dep.nombre Departamento, dep.id_departamento idDepartamento, div.nombre 
										Division, div.id_division idDivision
										, 'Coordinador' rol, '1' idRol
										from HojaDeTiempo.dbo.Proyectos pro, HojaDeTiempo.dbo.Usuarios uCoordinador, HojaDeTiempo.dbo.Divisiones div
										, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Categorias cat
										Where pro.id_coordinador = uCoordinador.unidad and uCoordinador.id_departamento = dep.id_departamento and
										dep.id_division = div.id_division AND uCoordinador.id_categoria = cat.id_categoria
								
										union select pro.id_proyecto id2, uDirector.nombre nDirector, uDirector.apellidos aDirector, uDirector.unidad uDirector,
										cat.nombre nCDire, uDirector.id_categoria cDir, dep.nombre nDir, dep.id_departamento idDir, div.nombre ndvDr, div.id_division idDvDir
										, 'Director' rol, '2' idRol
										from HojaDeTiempo.dbo.Proyectos pro, HojaDeTiempo.dbo.Usuarios uDirector, HojaDeTiempo.dbo.Divisiones div
										, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Categorias cat
										Where uDirector.unidad = pro.id_director and uDirector.id_departamento = dep.id_departamento and dep.id_division = div.id_division AND
										uDirector.id_categoria = cat.id_categoria
										
										union select pro.id_proyecto id3, usuarios.nombre nProg, usuarios.apellidos aProg, usuarios.unidad uProg,
										cat.nombre nCPro, usuarios.id_categoria cPro, dep.nombre ndpNm, dep.id_departamento idDp, div.nombre ndvPr, div.id_division dvPr
										, 'Programador' rol, '3' idRol
										from HojaDeTiempo.dbo.Proyectos pro, HojaDeTiempo.dbo.Programadores programadores, HojaDeTiempo.dbo.Usuarios usuarios,
										HojaDeTiempo.dbo.Divisiones div, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Categorias cat
										Where programadores.unidad = usuarios.unidad and programadores.id_proyecto = pro.id_proyecto and
										usuarios.id_departamento = dep.id_departamento and dep.id_division = div.id_division AND usuarios.id_categoria = cat.id_categoria
								
										union select pro.id_proyecto id4, usuarios.nombre nGatos, usuarios.apellidos aGatos, usuarios.unidad uGatos,
										cat.nombre nCOGas, usuarios.id_categoria cOGas, dep.nombre ndeGa, dep.id_departamento deGa, div.nombre ndvGa, div.id_division dvGa
										, 'Ordenador de Gastos' rol, '4' idRol
										from HojaDeTiempo.dbo.Proyectos pro, GestiondeInformacionDigital.dbo.OrdenadorGasto ordGastos, HojaDeTiempo.dbo.Usuarios usuarios,
										HojaDeTiempo.dbo.Divisiones div, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Categorias cat
										Where ordGastos.unidadOrdenador = usuarios.unidad and ordGastos.id_proyecto = pro.id_proyecto 
										and usuarios.id_departamento = dep.id_departamento and dep.id_division = div.id_division AND usuarios.id_categoria = cat.id_categoria
										
										union SELECT pro.id_proyecto id5, usuarios.nombre nAPart, usuarios.apellidos aAPart, usuarios.unidad uAPart, cat.nombre nAPart
										, usuarios.id_categoria cAPart, dep.nombre ndeAPart, dep.id_departamento deAPart, div.nombre ndvAPart, div.id_division dvAPart
										, 'Participante' rol, '5' idRol 
										FROM HojaDeTiempo.dbo.Proyectos pro, ParticipantesActividad pAct, HojaDeTiempo.dbo.Usuarios usuarios, HojaDeTiempo.dbo.Divisiones div
										, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Categorias cat
										Where pAct.unidad = usuarios.unidad and pAct.id_proyecto = pro.id_proyecto and usuarios.id_departamento = dep.id_departamento 
										and dep.id_division = div.id_division AND usuarios.id_categoria = cat.id_categoria
										
										union SELECT pro.id_proyecto id5, usuarios.nombre nAPart, usuarios.apellidos aAPart, usuarios.unidad uAPart, cat.nombre nAPart
										, usuarios.id_categoria cAPart, dep.nombre ndeAPart, dep.id_departamento deAPart, div.nombre ndvAPart, div.id_division dvAPart
										, 'Responsables' rol, '6' idRol 
										FROM HojaDeTiempo.dbo.Proyectos pro, ParticipantesActividad pAct, HojaDeTiempo.dbo.Usuarios usuarios, HojaDeTiempo.dbo.Divisiones div
										, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Categorias cat
										Where pAct.unidad = usuarios.unidad and pAct.id_proyecto = pro.id_proyecto and usuarios.id_departamento = dep.id_departamento 
										and dep.id_division = div.id_division AND usuarios.id_categoria = cat.id_categoria
											
									) usuarios,
									HojaDeTiempo.dbo.Divisiones division,
									HojaDeTiempo.dbo.Departamentos departamento,
									HojaDeTiempo.dbo.Categorias cat
								where
									pro.id_proyecto = usuarios.idProyecto AND
									division.id_division = usuarios.idDivision AND
									departamento.id_departamento = usuarios.idDepartamento AND
									cat.id_categoria = usuarios.Categoria ";
			####
			#	Filtros para las personas que intervienen en el proyecto.			
			if( trim( $pfDivision ) != "" )
				$sqlActResponsable .= " AND division.id_division = ".$pfDivision;
			
			####
			#	Filtros por division de estructura organizativa
			if( trim( $pfDivisionOrg ) != "" )
				$sqlActResponsable .= " AND division.id_division = ".$pfDivisionOrg;
			####	
			if( trim( $miDpto ) != "" )
				$sqlActResponsable .= " AND departamento.id_departamento = ".$miDpto;
				
			if( trim( $pFiltro ) != "" )
				$sqlActResponsable .= " AND Categoria = ".$pFiltro;

			if( trim($pr) != "" ){
				if( $pr == 0 )
					$sqlActResponsable .= " AND idRol = idRol";
				else
					$sqlActResponsable .= " AND idRol = ".$pr;
				
			}
			$sqlActResponsable .= " AND id_proyecto = ".$cualProyecto."
									ORDER BY idRol";
			
			#echo $sqlActResponsable."<br />";
			$qryActResponsable = mssql_query( $sqlActResponsable );			
		?>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="8%">Unidad</td>
            <td width="20%">Nombre</td>
            <td width="5%">Categor&iacute;a</td>
            <td width="10%">Divisi&oacute;n</td>
            <td width="10%">Departamento</td>
            <td width="10%">Rol</td>
            <td colspan="3">Actividades en las que se encuentra </td>
            <!-- <td width="1%">&nbsp;</td> -->
          </tr>
          <?	
		  	while( $row = mssql_fetch_array( $qryActResponsable ) ){	
				#####
				#	Filtro dependiendo el rol para definir por cual tabla se debe buscar 
				if( $row[idRol] != 5 )
					$tabla = "ParticipantesActividad";
				else
					$tabla = "ParticipantesActividad";
					
				
				$sqlAct = " SELECT act.nombre nActividad, act.macroactividad, act.id_actividad, pAct.estado 
							FROM ".$tabla." pAct, Actividades act
							Where  
							pAct.id_proyecto = act.id_proyecto AND act.id_actividad = pAct.id_actividad AND 
							act.id_proyecto = ".$cualProyecto." AND pAct.unidad = ".$row[uUnidad];
				if( trim( $lc ) != "" )
					$sqlAct .= " AND act.id_actividad in ( select id_actividad from HojaDeTiempo.dbo.Actividades 
								 WHERE actPrincipal = ".$lc." AND id_proyecto = ".$cualProyecto." )";
				
				if( trim( $lt ) != "" ){
					if( trim( $lc ) != "" )
						$lote = $lc;
					else
						$lote = "actPrincipal";
					$sqlAct .= " AND act.dependeDe in ( select id_actividad from HojaDeTiempo.dbo.Actividades 
								 WHERE actPrincipal = ".$lote." AND id_proyecto = ".$cualProyecto." AND  dependeDe = ".$lt." )";
				}

				if( trim( $pr ) != "" ){
					if( $pr == 0 )
						$perfil = "idRol";
					else
						$perfil = $pr;#"";
					$sqlActResponsable .= " AND idRol = ".$perfil;
				}
				#	Filtro de actividades
				$qryAct = mssql_query( $sqlAct );
				$numRow = mssql_num_rows( $qryAct );
				#####
				#	Define si se debe mostrar el registro si no tiene ninguna actividad relacionada o es responsable de una actividad
				if( ( 	$numRow != 0 and $row[idRol] == 5 ) or 
					( 	( $row[idRol] == 1 or $row[idRol] == 2 or $row[idRol] == 3 or $row[idRol] == 4 or $row[idRol] == 6 ) and $numRow != 0 )
				  ){
		  ?>
          <tr class="TxtTabla">
          	<td width="8%" valign="top"><?= $row[uUnidad] ?></td>
            <td width="20%" valign="top"><?= $row[uApellido]." ".$row[uNombre] ?></td>
            <td width="5%" valign="top"><?= $row[nCategoria] ?></td>
            <td width="10%" valign="top"><?= strtoupper($row[Division]) ?></td>
            <td width="10%" valign="top"><?= $row[Departamento] ?></td>
             <td valign="top"><?= $row[rol] ?></td>
            <td valign="top">
              <?							  
			  	$sqlActCan = " SELECT COUNT(*) num FROM ".$tabla." pAct, Actividades act
								Where pAct.id_proyecto = act.id_proyecto AND act.id_actividad = pAct.id_actividad AND 
								act.id_proyecto = ".$cualProyecto." AND pAct.unidad = ".$row[uUnidad];
				$qryActCan = mssql_fetch_array( mssql_query( $sqlActCan ) );
				
				if( $qryActCan[num] != 0 ){
			  ?>
           	  <table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF" >
                <tr class="TituloTabla2">
                  <td width="13%">Identificador</td><td align="left">Nombre</td>
                  <td width="1%">&nbsp;</td>
                  <td width="1%">Operacion</td></tr>
				<?

					#*/
					if( $numRow == 0 )
						echo "<tr><td colspan='4'>Este usuario no reporta nada. </td></tr>";
					else{
					#echo "<tr><td>".$sqlAct."</td></tr><br />";
						while( $rw = mssql_fetch_array( $qryAct ) ){			
					?>
                    	<tr class="TxtTabla">
                        <td width="10%">
							<? 	if( $rw[estado]	== 1 ){							
			                       $abre = "<b>";
								   $cierra = "</b>";
								}else {
                                   $abre = "";
								   $cierra = "";
								}
								echo  $abre."[ ".$rw[macroactividad]."	]".$cierra;	
                            ?>
                        </td>
                        <td>
						<?=	$abre.$rw[nActividad].$cierra	?>
                        </td>
                        <td width="1%">
							<? if( $rw[estado]	== 1 ){ ?>
                          <img src="imagenes/flagVerde.gif" title="Activo" style="cursor: pointer" />
							<?	}else{  ?>
                          <img src="imagenes/imgFlag.gif" title="Inactivo" style="cursor: pointer" />
                            <?	}	?>
                        </td>
                        <td width="1%" align="right">
                        <img src="imagenes/actualizar2.gif" alt="" style="cursor:pointer"
                         onclick="MM_openBrWindow('pnfUpdActParticipanteOML2.php?cualProyecto=<? echo $cualProyecto ; ?>&idAct=<?= $row[uUnidad] ?>&idActi=<?= $rw[id_actividad] ?>&tabla=<?= $tabla ?>','addAAT','scrollbars=yes,resizable=yes,width=620,height=400')" />
                         <?	if( $row[idRol] == 5 ){ ?>
                         <img
                          onclick="MM_openBrWindow('pnfDelActParticipanteOML2.php?cualProyecto=<? echo $cualProyecto ; ?>&idAct=<?= $row[uUnidad] ?>&idActi=<?= $rw[id_actividad] ?>&tabla=<?= $tabla ?>','addAAT','scrollbars=yes,resizable=yes,width=620,height=400')" src="imagenes/No.gif" style="cursor:pointer" alt="" />
                          
                         <?	}	?>
                        </td>
                        </tr>
                    <?		
						#}
						}
					}
				?>
                </table>
                <?	}	?>
            </td>
            <td width="1%"><?
			  	$sqlActCan = " SELECT COUNT(*) num FROM ".$tabla." pAct, Actividades act
								Where pAct.id_proyecto = act.id_proyecto AND act.id_actividad = pAct.id_actividad AND 
								act.id_proyecto = ".$cualProyecto." AND pAct.unidad = ".$row[uUnidad];
				$qryActCan = mssql_fetch_array( mssql_query( $sqlActCan ) );
				if( $qryActCan[num] != 0 or (  $row[idRol] != 5  and $qryActCan[num] != 0 ) ){
			  ?>              <img onclick="MM_openBrWindow('pnfUpdAutParticipanteOML2.php?cualProyecto=<? echo $cualProyecto ; ?>&idAct=<?= $row[uUnidad] ?>&tabla=<?= $tabla ?>','addAAT','scrollbars=yes,resizable=yes,width=620,height=400')" src="imagenes/actualizar2.gif" style="cursor:pointer" />
            <?	}	?></td>
            <td width="1%">
            <?
			  	$sqlActCan = " SELECT COUNT(*) num FROM ".$tabla." pAct, Actividades act
								Where pAct.id_proyecto = act.id_proyecto AND act.id_actividad = pAct.id_actividad AND 
								act.id_proyecto = ".$cualProyecto." AND pAct.unidad = ".$row[uUnidad];
				$qryActCan = mssql_fetch_array( mssql_query( $sqlActCan ) );
				if( $qryActCan[num] != 0 and $row[idRol] == 5 ){
			  ?>
            <img onclick="MM_openBrWindow('pnfdelAutParticipanteOML2.php?cualProyecto=<? echo $cualProyecto ; ?>&idAct=<?= $row[uUnidad] ?>','addAAT','scrollbars=yes,resizable=yes,width=620,height=400')" src="imagenes/No.gif" style="cursor:pointer" alt="" />
            <?	}	?></td>
          </tr>
          <?	}	} ?>
		  
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('pnfaddAutParticipanteOML2.php?cualProyecto=<? echo $cualProyecto ; ?>','addAAT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Ingresar participantes al proyecto" /></td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
