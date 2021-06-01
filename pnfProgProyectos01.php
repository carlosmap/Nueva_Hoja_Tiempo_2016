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

if (trim($pOrdenAct)=="") {
	$pOrdenAct=1;
}

//11Sep2008
//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$fDivSql="Select * from divisiones ";
$fDivSql=$fDivSql." where (nombre <> '' and nombre <> 'sd') ";
$fDivSql=$fDivSql." order by nombre ";
$fDivCursor = mssql_query($fDivSql);

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
		//$sql2=$sql2." order by A.nivelesActiv " ;
		
		//11Sep2008
		//Para ordenar las actividades por nombre de la actividad y por macroactividad sin perder la 
		//jerarquia de actividades y subactividades
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
//	$sql2=$sql2." order by A.nivelesActiv " ;
	//11Sep2008
	//Para ordenar las actividades por nombre de la actividad y por macroactividad sin perder la 
	//jerarquia de actividades y subactividades
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

//10Sep2008
//Para incluir el filtro del orden de las registros que aparecen en recursos
if (trim($pOrdena)=="") {
	$pOrdena=1;
}

//28Ago2008
//Para incluir la categoría
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
//Para incluir División y Departamento
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
		<? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); 
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
            <td class="TxtTabla"><a href="pnfProgProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr >
        <td width="15%" height="20" class="FichaAct">EDT</td>
        <td width="15%" height="20" class="FichaInAct"><a href="pnfProgProyectos02.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Participantes</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="pnfProgProyectos03.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Programaci&oacute;n</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="pnfProgProyectos04.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Resumen</a></td>
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
                      <td align="left" class="TxtTabla"><select name="select" class="CajaTexto">
                        <option value="0">::: Todos:::</option>
                        <option value="1">GERENCIA DEL PROYECTO</option>
                        <option value="2">INFRAESTRUCTURA</option>
                        <option value="3">SISTEMA DE DESVIACIÓN DEL RÍO</option>
                        <option value="4">PRESA Y OBRAS ANEXAS</option>
                        <option value="5">CONDUCCIÓN</option>
                        <option value="6">OBRAS DE LA CENTRAL</option>
                        <option value="7">EQUIPOS MECÁNICOS DE LA CENTRAL</option>
                        <option value="8">CONEXIÓN AL S.T.N.</option>
						<option value="9">GESTIÓN AMBIENTAL</option>
						<option value="10">COMPRA DE TIERRAS, SERVIDUMBRES Y ADMINISTRACIÓN DE PREDIOS</option>
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
            <td class="TituloUsuario">Estructura de descomposici&oacute;n de trabajo </td>
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
            <td width="3%">ID</td>
            <td width="5%">Macroactividad</td>
            <td>Lote de control / Lote de trabajo / Actividad Vs Divisi&oacute;n </td>
            <td width="15%">Responsable</td>
            <td width="5%">Valor Presupuestado </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%"><strong>1</strong></td>
            <td width="5%"><strong>LC1</strong></td>
            <td><strong>GERENCIA DEL PROYECTO</strong></td>
            <td width="15%"><strong>[2964] Alberto Marulanda </strong></td>
            <td width="5%"><strong>$ 250.000.000 </strong></td>
            <td width="1%"><a href="#"><img src="img/images/icoAdd.gif" alt="Ingresar Lote de trabajo / Actividad" width="16" height="15" border="0" onclick="MM_openBrWindow('pnfaddLT.php','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" /></a></td>
            <td width="1%"><a href="#"><img src="img/images/imgSeguimiento.gif" alt="Actividad en seguimiento" width="18" height="17" border="0" /></a></td>
            <td width="1%"><img src="img/images/actualizar.jpg" width="19" height="17" /></td>
            <td width="1%"><img src="img/images/Del.gif" width="14" height="13" /></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">2</td>
            <td width="5%">LT1.1</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>ACTIVIDADES DE SOPORTE A LA GERENCIA DEL PROYECTO</td>
              </tr>
            </table></td>
            <td width="15%">[2964] Alberto Marulanda </td>
            <td width="5%">&nbsp;</td>
            <td width="1%"><a href="#"><img src="img/images/icoAdd.gif" alt="Ingresar Divisió / Actividad" width="16" height="15" border="0" onclick="MM_openBrWindow('pnfaddAct.php','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" /></a></td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">3</td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Calidad</td>
              </tr>
            </table></td>
            <td width="15%">[4417] Hector Alfredo L&oacute;pez</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">4</td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Medio Ambiente </td>
              </tr>
            </table></td>
            <td width="15%">[14469] William L&oacute;pez</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">5</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Manuales e informes </td>
              </tr>
            </table></td>
            <td width="15%">[12372] Hernando Caicedo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">6</td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Documentos obra civil</td>
              </tr>
            </table></td>
            <td width="15%">[12372] Hernando Caicedo</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">7</td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geolog&iacute;a - Sismolog&iacute;a </td>
              </tr>
            </table></td>
            <td width="15%">[17206] Fernando Garz&oacute;n</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">8</td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Climat. Hidro - Sedim </td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">9</td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Rutas transporte y carga </td>
              </tr>
            </table></td>
            <td width="15%">[11383] Gloria B&aacute;ez</td>
            <td width="5%"><br />            </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">10</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Centro de CADD </td>
              </tr>
            </table></td>
            <td width="15%">[12974] Gonzalo rodr&iacute;guez </td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">11</td>
            <td>LT1.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>ADMINISTRACI&Oacute;N DE ASESOR&Iacute;AS, CONSULTOR&Iacute;AS E INTERVENTOR&Iacute;AS</td>
              </tr>
            </table></td>
            <td width="15%">[13829] Fabio S&aacute;nchez</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%"><strong>12</strong></td>
            <td><strong>LC2</strong></td>
            <td><strong>INFRAESTRUCTURA</strong></td>
            <td width="15%"><strong>[15252] Julio Gonz&aacute;lez</strong></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%"><a href="#"><img src="img/images/imgSeguimiento.gif" alt="Actividad en seguimiento" width="18" height="17" border="0" /></a></td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">13</td>
            <td>LT2.1</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>V&Iacute;A DE ACCESO </td>
              </tr>
            </table></td>
            <td width="15%">[14176] Javier Lizarazo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">14</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Hidrol., hidr&aacute;ulica y socav.</td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">15</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[15415] Thomas Solano</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">16</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td>[5044] Samuel Su&aacute;rez</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">17</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Dise&ntilde;o geom&eacute;trico </td>
              </tr>
            </table></td>
            <td width="15%"> 
            [14176] Javier Lizarazo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">18</td>
            <td>LT2.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>V&Iacute;AS SECUNDARIAS Y PUENTE TEMPORAL, PLAZOLETAS Y PORTALES - ADECUACI&Oacute;N CANTERAS Y DEP&Ograve;SITOS </td>
              </tr>
            </table></td>
            <td width="15%">[15252] Julio Gonz&aacute;lez</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">19</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Hidrol., hidr&aacute;ulica y socav.</td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">20</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia obras superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">21</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[5044] Samuel Su&aacute;rez</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">22</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Dise&ntilde;o geom&eacute;trico </td>
              </tr>
            </table></td>
            <td width="15%">            [15252] Julio Gonz&aacute;lez</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">23</td>
            <td>LT2.3</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>CAMPAMENTOS, BODEGAS Y ALMAC&Eacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[4618] &Aacute;rvid Bernal</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">24</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Hidr&aacute;ulica y sanitaria </td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr><tr class="TxtTabla">
            <td width="3%">25</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            </tr><tr class="TxtTabla">
            <td width="3%">26</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[4618] Arvid Bernal</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            </tr><tr class="TxtTabla">
            <td width="3%">27</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Arquitectura</td>
              </tr>
            </table></td>
            <td width="15%">[15021] Diana Figueredo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            </tr><tr class="TxtTabla">
            <td width="3%">28</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Mec&aacute;nica</td>
              </tr>
            </table></td>
            <td width="15%">[11577] Gabriel Rudas</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            </tr><tr class="TxtTabla">
            <td width="3%">29</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>El&eacute;ctrica</td>
              </tr>
            </table></td>
            <td width="15%">            [10579] Jorge Mart&iacute;nez</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            </tr>
          <tr class="TxtTabla">
            <td width="3%">30</td>
            <td>LT2.4</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>ENERG&Iacute;A PARA CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
		  <tr class="TxtTabla">
            <td width="3%">31</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
	      </tr><tr class="TxtTabla">
            <td width="3%">32</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[15218] Roberto Rojas</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    </tr><tr class="TxtTabla">
            <td width="3%">33</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>El&eacute;ctrica</td>
              </tr>
            </table></td>
            <td width="15%">            [5008] Jorge Correa</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    </tr>
		  <tr class="TxtTabla">
            <td width="3%">34</td>
            <td>LT2.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>SUBESTACI&Oacute;N DE CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
	      </tr>
		  <tr class="TxtTabla">
            <td width="3%">35</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td><span class="xl65">Geotecnia o. superf </span></td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
	      </tr><tr class="TxtTabla">
            <td width="3%">36</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td><span class="xl65">Estructuras</span></td>
              </tr>
            </table></td>
            <td width="15%">[15218] Roberto Rojas</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    </tr><tr class="TxtTabla">
            <td width="3%">37</td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td><span class="xl65">El&eacute;ctrica</span></td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    </tr>
		  <tr class="TxtTabla">
            <td width="3%">38</td>
            <td>LT2.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>COMUNICACIONES PARA CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[14894] Gustavo Suaza</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
	      </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
        </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('PlantillaXLS.xls','winPlantilla','scrollbars=yes,resizable=yes,width=1000,height=500')" value="Descargar Plantilla .xls" />              <input name="Submit3" type="submit" class="Boton" onclick="MM_openBrWindow('pnfaddImport.php','winImport','width=500,height=250')" value="Importar EDT a partir de XLS" />
              <input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('pnfaddLC.php','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" value="Nuevo Lote de control" /></td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Firmas de aprobaci&oacute;n </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr class="TituloTabla2">
              <td width="33%">Elaboraci&oacute;n</td>
              <td width="33%">Director del proyecto </td>
              <td>Director de la Divisi&oacute;n </td>
            </tr>
            <tr class="TxtTabla">
              <td width="33%">[15712]<br />
                Patricia Bar&oacute;n Manrique<br />
                Fecha de creaci&oacute;n: 01-Sep-2012<br />
                Enviado a Director: <img src="img/images/Si.gif" width="16" height="14" /></td>
              <td width="33%">
			  <?
			  echo "[" . $DirectorUnidad . "]" . "<br>" ;
			  echo $DirectorNombre . "<br>" ;
			  ?>
              <br />
              Fecha de aprobaci&oacute;n: 10-Sep-2012<br />
              VoBo. <img src="img/images/Aprobado.gif" width="21" height="24" /></td>
              <td> [14384]<br />
                Camilo Marulanda Escobar<br />
                Fecha de aprobaci&oacute;n: 11-Sep-2012<br />
                VoBo.<img src="img/images/NoAprobado.gif" width="20" height="22" /> </td>
            </tr>
            <tr class="TxtTabla">
              <td><input name="Submit6" type="submit" class="Boton" value="Enviar a Director" /></td>
              <td><input name="Submit62" type="submit" class="Boton" value="Aprobaci&oacute;n de la EDT Director del proyecto" /></td>
              <td><input name="Submit622" type="submit" class="Boton" value="Aprobaci&oacute;n de la EDT Director de divisi&oacute;n" /></td>
            </tr>
          </table></td>
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
