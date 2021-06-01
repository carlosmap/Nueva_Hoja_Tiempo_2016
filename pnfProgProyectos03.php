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
            <td class="TxtTabla"><a href="pnfProgProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr >
        <td width="15%" height="20" class="FichaInAct"><a href="pnfProgProyectos01.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >EDT</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="pnfProgProyectos02.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Participantes</a></td>
        <td width="15%" height="20" class="FichaAct">Programaci&oacute;n</td>
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
        </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
                      <td width="5%" class="TxtTabla">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="left" class="TituloTabla">Lote de trabajo </td>
                      <td align="left" class="TxtTabla"><select class="CajaTexto">
					  <option value="0">::: Todos:::</option>
         <option>    ACTIVIDADES DE SOPORTE A LA GERENCIA DEL 
PROYECTO    </option>

         <option>    ADMINISTRACIÓN DE ASESORÍAS, CONSULTORÍAS E 
INTERVENTORÍAS    </option>
         <option>    VÍA DE ACCESO    </option>
         <option>    VÍAS SECUNDARIAS Y PUENTE TEMPORAL, PLAZOLETAS Y 
PORTALES - ADECUACIÓN CANTERAS Y DEPÒSITOS </option>
         <option>    CAMPAMENTOS, BODEGAS Y ALMACÉN </option>
         <option>    ENERGÍA PARA CONSTRUCCIÓN </option>
         <option>    SUBESTACIÓN DE CONSTRUCCIÓN </option>
         <option>    COMUNICACIONES PARA CONSTRUCCIÓN </option>
         <option>    TÚNELES, PORTALES Y TAPÓN </option>
         <option>    PREATAGUÍA, CONTRA-ATAGUÍA Y ATAGUÍA </option>
         <option>    EQUIPOS PARA EL SISTEMA DE DESVIACIÓN </option>
         <option>    PRESA    </option>
         <option>    VERTEDERO    </option>
         <option>    DESCARGA DE FONDO, CÁMARA DE COMPUERTAS Y GALERÍAS 
     </option>
         <option>    DESCARGA PARA EL CAUDAL ECOLÓGICO </option>
         <option>    EMBALSE    </option>
         <option>    EQUIPO PARA LA PRESA Y EL VERTEDERO </option>
         <option>    EQUIPO PARA LA DESCARGA DE FONDO Y CAUDAL 
ECOLÓGICO    </option>
         <option>    TÚNEL DE CARGA (CAPTACIÓN, SUPERIOR, POZO E 
INFERIOR, GALERÍA No. 1)    </option>
         <option>    TÚNEL DISTRIBUIDOR    </option>
         <option>    POZO DE COMPUERTAS Y CABLES </option>
         <option>    EQUIPO PARA LA CONDUCCIÓN  (COMPUERTAS Y REJAS 
COLADERAS)    </option>
         <option>    BLINDAJE TÚNEL Y DISTRIBUIDOR </option>
         <option>    TÚNEL DE ACCESO Y GALERÍAS DE CONSTRUCCIÓN    </option>
         <option>    CAVERNA DE MÁQUINAS Y GALERÍAS DE BARRAS    </option>
         <option>    CAVERNA DE TRANSFORMADORES </option>
         <option>    OBRAS PARA SALIDA DE CABLES </option>
         <option>    OBRAS DESCARGA - ASPIRACIÓN, RESTITUCIÓN, 
OSCILACIÓN, POZOS DE COMPUERTAS DESCARGA    </option>
         <option>    COMPUERTAS PARA LOS POZOS DE COMPUERTAS </option>
         <option>    TURBINAS, REGULADORES, VÁLVULAS Y EQUIPO 
ASOCIADO    </option>
         <option>    PUENTE GRÚAS    </option>
         <option>    EQUIPOS PARA AIRE COMPRIMIDO </option>
         <option>    EQUIPOS PARA REFRIGERACIÓN Y DRENAJE </option>
         <option>    EQUIPOS ANTI-INCENDIO    </option>
         <option>    EQUIPOS DE VENTILACIÓN Y AIRE ACONDICIONADO    
</option>
         <option>    EQUIPOS AUXILIARES MECÁNICOS </option>
         <option>    GENERADORES Y EQUIPO ASOCIADO </option>
     </select>
</td>
                      <td class="TxtTabla"><input name="Submit9" type="submit" class="Boton" value="Consultar" /></td>
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
          <tr  class="TxtTabla">
            <td>&nbsp;</td>
            <td width="15%" align="right"><strong>AsgS</strong>: Valor Asignado los Lotes de trabajo / Subactividades </td>
            <td width="15%" align="right"><strong>Prg</strong>: Valor programado en la actividad </td>
            <td width="15%" align="right"><strong>PrgS</strong>: Valor programado en<br /> 
            los lotes de trabajo / Subactividades</td>
            <td width="15%" align="right"> <strong>Fac</strong>: Valor Facturado </td>
            <td width="15%" align="right"><strong>FactS</strong>: Valor Facturado de las subactividades </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Lotes de control / Lotes de trabajo / Divisiones - Actividades</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="3%">ID</td>
            <td width="1%" align="center"><img src="img/images/imgInfoG.gif" alt="Seleccionar las macroactividades donde se har&aacute; programaci&oacute;n" width="29" height="23" /></td>
            <td width="5%">Macroactividad</td>
            <td>Lote de control / Lote de trabajo / Actividad Vs Divisi&oacute;n </td>
            <td width="15%">Responsable</td>
            <td width="5%">Valor Presupuestado </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%">Fecha  Inicio </td>
            <td width="8%">Fecha  Fin </td>
            <td width="8%">Valor del recurso </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%"><strong>1</strong></td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox" value="checkbox" /></td>
            <td width="5%"><strong>LC1</strong></td>
            <td><strong>GERENCIA DEL PROYECTO</strong></td>
            <td width="15%"><strong>[2964] Alberto Marulanda </strong>
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                    <input name="Submit52" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                    <? } ?></td>
                </tr>
              </table></td>
            <td width="5%"><strong>$ 250.000.000 </strong></td>
            <td width="1%"><a href="#"><img src="img/images/icoAdd.gif" alt="Ingresar Lote de trabajo / Actividad" width="16" height="15" border="0" onclick="MM_openBrWindow('pnfaddLT.php','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" /></a></td>
            <td width="1%"><a href="#"><img src="img/images/imgSeguimiento.gif" alt="Actividad en seguimiento" width="18" height="17" border="0" /></a></td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center" class="TxtTabla">01-Ene-2012</td>
            <td width="8%" align="center" class="TxtTabla">15-Dic-2012</td>
            <td width="8%" class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td>AsgS</td>
                <td align="right">$250.000.000</td>
              </tr>
              <tr class="TxtTabla">
                <td width="1%">Prg</td>
                <td width="1%" align="right">$ 50.000.000</td>
              </tr>
              <tr class="TxtTabla">
                <td>PrgS</td>
                <td align="right">$ 10.000.000 </td>
              </tr>
              <tr class="TxtTabla">
                <td>Fac</td>
                <td align="right">$8.000.000 </td>
              </tr>
              <tr class="TxtTabla">
                <td width="1%">FacS</td>
                <td width="1%" align="right">$3.000.000</td>
              </tr>
            </table></td>
            <td width="1%" class="TxtTabla"><img src="img/images/actualizar.jpg" width="19" height="17" /></td>
            <td width="1%" class="TxtTabla"><img src="img/images/Del.gif" width="14" height="13" /></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">2</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox2" value="checkbox" /></td>
            <td width="5%">LT1.1</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>ACTIVIDADES DE SOPORTE A LA GERENCIA DEL PROYECTO</td>
              </tr>
            </table></td>
            <td width="15%">[2964] Alberto Marulanda 
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit522" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%">$ 180.000.000 </td>
            <td width="1%"><a href="#"><img src="img/images/icoAdd.gif" alt="Ingresar Divisió / Actividad" width="16" height="15" border="0" onclick="MM_openBrWindow('pnfaddAct.php','winHH','scrollbars=yes,resizable=yes,width=900,height=400')" /></a></td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">01-Ene-2012</td>
            <td width="8%" align="center">30-Mar-2012</td>
            <td width="8%"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td>AsgS</td>
                <td align="right">$100.000.000</td>
              </tr>
              <tr class="TxtTabla">
                <td width="1%">Prg</td>
                <td width="1%" align="right">$ 10.000.000</td>
              </tr>
              <tr class="TxtTabla">
                <td>PrgS</td>
                <td align="right">$ 5.000.000 </td>
              </tr>
              <tr class="TxtTabla">
                <td>Fac</td>
                <td align="right">$5.000.000 </td>
              </tr>
              <tr class="TxtTabla">
                <td width="1%">FacS</td>
                <td width="1%" align="right">$1.000.000</td>
              </tr>
            </table></td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">3</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox3" value="checkbox" /></td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Calidad</td>
              </tr>
            </table></td>
            <td width="15%">[4417] Hector Alfredo L&oacute;pez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit523" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">4</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox4" value="checkbox" /></td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Medio Ambiente </td>
              </tr>
            </table></td>
            <td width="15%">[14469] William L&oacute;pez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit524" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">5</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox5" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Manuales e informes </td>
              </tr>
            </table></td>
            <td width="15%">[12372] Hernando Caicedo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit525" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">6</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox6" value="checkbox" /></td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Documentos obra civil</td>
              </tr>
            </table></td>
            <td width="15%">[12372] Hernando Caicedo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit526" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">7</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox8" value="checkbox" /></td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geolog&iacute;a - Sismolog&iacute;a </td>
              </tr>
            </table></td>
            <td width="15%">[17206] Fernando Garz&oacute;n
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit527" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">8</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox7" value="checkbox" /></td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Climat. Hidro - Sedim </td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit528" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">9</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox9" value="checkbox" /></td>
            <td width="5%">&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Rutas transporte y carga </td>
              </tr>
            </table></td>
            <td width="15%">[11383] Gloria B&aacute;ez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit529" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td width="5%"><br />            </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">10</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox10" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Centro de CADD </td>
              </tr>
            </table></td>
            <td width="15%">[12974] Gonzalo rodr&iacute;guez 
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5210" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">11</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox11" value="checkbox" /></td>
            <td>LT1.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>ADMINISTRACI&Oacute;N DE ASESOR&Iacute;AS, CONSULTOR&Iacute;AS E INTERVENTOR&Iacute;AS</td>
              </tr>
            </table></td>
            <td width="15%">[13829] Fabio S&aacute;nchez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5211" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td align="right">$ 70.000.000 </td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%"><strong>12</strong></td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox12" value="checkbox" /></td>
            <td><strong>LC2</strong></td>
            <td><strong>INFRAESTRUCTURA</strong></td>
            <td width="15%"><strong>[15252] Julio Gonz&aacute;lez</strong>
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5212" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%"><a href="#"><img src="img/images/imgSeguimiento.gif" alt="Actividad en seguimiento" width="18" height="17" border="0" /></a></td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">13</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox13" value="checkbox" /></td>
            <td>LT2.1</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>V&Iacute;A DE ACCESO </td>
              </tr>
            </table></td>
            <td width="15%">[14176] Javier Lizarazo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5213" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">14</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox14" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Hidrol., hidr&aacute;ulica y socav.</td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5214" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">15</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox15" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[15415] Thomas Solano
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5215" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">16</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox16" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td>[5044] Samuel Su&aacute;rez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5216" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">17</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox17" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Dise&ntilde;o geom&eacute;trico </td>
              </tr>
            </table></td>
            <td width="15%"> 
            [14176] Javier Lizarazo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5217" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">18</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox18" value="checkbox" /></td>
            <td>LT2.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>V&Iacute;AS SECUNDARIAS Y PUENTE TEMPORAL, PLAZOLETAS Y PORTALES - ADECUACI&Oacute;N CANTERAS Y DEP&Ograve;SITOS </td>
              </tr>
            </table></td>
            <td width="15%">[15252] Julio Gonz&aacute;lez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5218" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">19</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox19" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Hidrol., hidr&aacute;ulica y socav.</td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5219" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">20</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox20" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia obras superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5220" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">21</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox21" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[5044] Samuel Su&aacute;rez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5221" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">22</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox22" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Dise&ntilde;o geom&eacute;trico </td>
              </tr>
            </table></td>
            <td width="15%">            [15252] Julio Gonz&aacute;lez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5222" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">23</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox23" value="checkbox" /></td>
            <td>LT2.3</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>CAMPAMENTOS, BODEGAS Y ALMAC&Eacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[4618] &Aacute;rvid Bernal
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5223" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">24</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox24" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Hidr&aacute;ulica y sanitaria </td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5224" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr><tr class="TxtTabla">
            <td width="3%">25</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox25" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5225" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr><tr class="TxtTabla">
            <td width="3%">26</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox26" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[4618] Arvid Bernal
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5226" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr><tr class="TxtTabla">
            <td width="3%">27</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox27" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Arquitectura</td>
              </tr>
            </table></td>
            <td width="15%">[15021] Diana Figueredo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5227" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr><tr class="TxtTabla">
            <td width="3%">28</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox28" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Mec&aacute;nica</td>
              </tr>
            </table></td>
            <td width="15%">[11577] Gabriel Rudas
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5228" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr><tr class="TxtTabla">
            <td width="3%">29</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox29" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>El&eacute;ctrica</td>
              </tr>
            </table></td>
            <td width="15%">            [10579] Jorge Mart&iacute;nez
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5229" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">30</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox30" value="checkbox" /></td>
            <td>LT2.4</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>ENERG&Iacute;A PARA CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5230" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
		  <tr class="TxtTabla">
            <td width="3%">31</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox31" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5231" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr><tr class="TxtTabla">
            <td width="3%">32</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox32" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[15218] Roberto Rojas
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5232" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr><tr class="TxtTabla">
            <td width="3%">33</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox33" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>El&eacute;ctrica</td>
              </tr>
            </table></td>
            <td width="15%">            [5008] Jorge Correa
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5233" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr>
		  <tr class="TxtTabla">
            <td width="3%">34</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox34" value="checkbox" /></td>
            <td>LT2.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>SUBESTACI&Oacute;N DE CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5234" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr>
		  <tr class="TxtTabla">
            <td width="3%">35</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox35" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td><span class="xl65">Geotecnia o. superf </span></td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5235" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr><tr class="TxtTabla">
            <td width="3%">36</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox36" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td><span class="xl65">Estructuras</span></td>
              </tr>
            </table></td>
            <td width="15%">[15218] Roberto Rojas
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5236" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr><tr class="TxtTabla">
            <td width="3%">37</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox37" value="checkbox" /></td>
            <td>&nbsp;</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td><span class="xl65">El&eacute;ctrica</span></td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5237" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr>
		  <tr class="TxtTabla">
            <td width="3%">38</td>
            <td width="1%" align="center"><input type="checkbox" name="checkbox38" value="checkbox" /></td>
            <td>LT2.2</td>
            <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td>COMUNICACIONES PARA CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[14894] Gustavo Suaza
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right"><? 
			//Solo es visible por los responsables y/p programadores de la actividad
			if ($verProyecto=="SI") {   ?>
                      <input name="Submit5238" type="button" class="Boton" onclick="MM_openBrWindow('addProgResp.php?kProyecto=<? echo $reg2[id_proyecto] ; ?>&kActiv=<? echo $reg2[id_actividad] ; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Insertar" />
                      <? } ?></td>
                </tr>
              </table></td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
		    <td width="1%" class="TituloTabla2">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%" align="center">&nbsp;</td>
		    <td width="8%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		    <td width="1%">&nbsp;</td>
		  </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td width="1%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td width="1%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td width="1%" align="center">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit10" type="submit" class="Boton" onclick="MM_openBrWindow('pnfaddFechasValores.php','wAFV','width=900,height=400')" value="Relacionar Fechas y Valor del recurso" /></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Programaci&oacute;n de recursos </td>
          </tr>
        </table>
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="20%" class="TxtTabla">
		  
	      
  		  </td>
        <td>
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">

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
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
              <tr class="TituloTabla2">
                <td width="10%">Clase Tiempo </td>
                <td width="12%">Localizaci&oacute;n</td>
                <td width="10%">Cargo</td>
                <td width="10%">Hombre/Mes Programado </td>
                <td width="10%">Horas reportadas </td>
                <td>Horario</td>
                <td width="12%">Valor Recurso </td>
                <td width="2%">&nbsp;</td>
                <td width="2%">&nbsp;</td>
                <td width="2%">&nbsp;</td>
                <td width="2%">&nbsp;</td>
              </tr>
            </table></td>
            <td>x</td>
          </tr>
          <tr class="TxtTabla">
            <td width="8%">2964</td>
            <td width="5%">01-A</td>
            <td>Alberto Marulanda </td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">

              <tr>
                <td width="10%">1</td>
                <td width="12%">1</td>
                <td width="10%"><? echo $codProyecto ?></td>
                <td width="10%">
                  0.75 h/m
                </td>
                <td width="10%">
                  12
                </td>
                <td>
                  Horario Base [9 - 9 - 9 - 9 - 8 - 0 - 0]
&nbsp;</td>
                <td width="13%" align="right">$4.158.195</td>
                <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Edici&oacute;n" width="19" height="17" border="0" onclick="MM_openBrWindow('upProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vupP','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delPrograma.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vDelP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/No.gif" alt="Borrar todo" width="12" height="16" border="0" onclick="MM_openBrWindow('delProgramaTodo.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=350')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Reporte Programado/Facturado Mensual" width="16" height="16" border="0" onclick="MM_openBrWindow('verProgFact.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=200')" /></a></td>
              </tr>
            </table></td>
            <td>&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td> 900047 </td>
            <td>02-B</td>
            <td>Claudia Patricia Torres </td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
              <tr>
                <td width="10%">1</td>
                <td width="12%">1</td>
                <td width="10%"><? echo $codProyecto ?></td>
                <td width="10%"> 5 h/m </td>
                <td width="10%"> 125</td>
                <td> Horario Base [9 - 9 - 9 - 9 - 8 - 0 - 0] &nbsp;</td>
                <td width="13%" align="right">$8.158.195</td>
                <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Edici&oacute;n" width="19" height="17" border="0" onclick="MM_openBrWindow('upProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vupP','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delPrograma.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vDelP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/No.gif" alt="Borrar todo" width="12" height="16" border="0" onclick="MM_openBrWindow('delProgramaTodo.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=350')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Reporte Programado/Facturado Mensual" width="16" height="16" border="0" onclick="MM_openBrWindow('verProgFact.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=200')" /></a></td>
              </tr>
              <tr>
                <td>1</td>
                <td>2</td>
                <td><? echo $codProyecto ?></td>
                <td>3</td>
                <td>150</td>
                <td>Horario Campo [10 - 10 - 10 - 10 - 8 - 0 - 0] </td>
                <td align="right">$10.150.126</td>
                <td><a href="#"><img src="img/images/actualizar.jpg" alt="Edici&oacute;n" width="19" height="17" border="0" onclick="MM_openBrWindow('upProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vupP','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
                <td><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delPrograma.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vDelP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a></td>
                <td><a href="#"><img src="img/images/No.gif" alt="Borrar todo" width="12" height="16" border="0" onclick="MM_openBrWindow('delProgramaTodo.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=350')" /></a></td>
                <td><a href="#"><img src="img/images/ver.gif" alt="Reporte Programado/Facturado Mensual" width="16" height="16" border="0" onclick="MM_openBrWindow('verProgFact.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=200')" /></a></td>
              </tr>
            </table></td>
            <td>&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td> 17333 </td>
            <td>35</td>
            <td>Diana Rocio g&oacute;mez </td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
              <tr>
                <td width="10%">1</td>
                <td width="12%">1</td>
                <td width="10%"><? echo $codProyecto ?></td>
                <td width="10%"> 1 </td>
                <td width="10%"> 18 </td>
                <td> Horario Base [9 - 9 - 9 - 9 - 8 - 0 - 0] &nbsp;</td>
                <td width="13%" align="right">$758.195</td>
                <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Edici&oacute;n" width="19" height="17" border="0" onclick="MM_openBrWindow('upProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vupP','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delPrograma.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vDelP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/No.gif" alt="Borrar todo" width="12" height="16" border="0" onclick="MM_openBrWindow('delProgramaTodo.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=350')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Reporte Programado/Facturado Mensual" width="16" height="16" border="0" onclick="MM_openBrWindow('verProgFact.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=200')" /></a></td>
              </tr>
            </table></td>
            <td>&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td>14055</td>
            <td>68</td>
            <td>Norbey Ni&ntilde;o </td>
            <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
              <tr>
                <td width="10%">1</td>
                <td width="12%">3</td>
                <td width="10%"><? echo $codProyecto ?></td>
                <td width="10%"> 3 h/m </td>
                <td width="10%">135</td>
                <td> Horario Campo [10 - 10 - 10 - 10 - 8 - 0 - 0] </td>
                <td width="13%" align="right">$1.258.195</td>
                <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Edici&oacute;n" width="19" height="17" border="0" onclick="MM_openBrWindow('upProgramaR.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vupP','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delPrograma.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>&pfDivision=<? echo $pfDivision; ?>&miDpto=<? echo $miDpto; ?>&pFiltro=<? echo $pFiltro; ?>','vDelP','scrollbars=yes,resizable=yes,width=500,height=280')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/No.gif" alt="Borrar todo" width="12" height="16" border="0" onclick="MM_openBrWindow('delProgramaTodo.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=350')" /></a></td>
                <td width="1%"><a href="#"><img src="img/images/ver.gif" alt="Reporte Programado/Facturado Mensual" width="16" height="16" border="0" onclick="MM_openBrWindow('verProgFact.php?cualProyecto=<? echo $cualProyecto ; ?>&cualActividad=<? echo $cualActividad ; ?>&cualUnidad=<? echo $reg3a[unidad]; ?>&cualClase=<? echo $reg3a[clase_tiempo]; ?>&cualLocaliza=<? echo $reg3a[localizacion]; ?>&cualCargo=<? echo $reg3a[cargo]; ?>&cualCodigo=<? echo $codProyecto; ?>','vPF','scrollbars=yes,resizable=yes,width=450,height=200')" /></a></td>
              </tr>
            </table></td>
            <td>&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('pnfaddProgramacion1.php','wInf','scrollbars=yes,resizable=yes,width=1200,height=500')" value="Ingresar Programaci&oacute;n" />
            <input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('pnfaddProgramacion2.php','wInf','scrollbars=yes,resizable=yes,width=1200,height=500')" value="Editar Programaci&oacute;n" /></td>
          </tr>
        </table></td>
      </tr>
	  </ form >
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">&nbsp;		</td>
      </tr>
    </table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF" class="TituloTabla2">&nbsp;	  </td>
  </tr>
</table>

</body>
</html>
