<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//echo "Proy=".  $cualProyecto . "<br>";
//echo "Act=" . $cualActividad . "<br>";
//exit;

//VALIDACION, QUE PERMITE EJECUTAR LA CONSULTA, CUANDO SE RETORNA DE LAS PAGINAS DE DETALLE, Y ASI MOSTRAR LOS REGISTROS CONSULTADOS ANTERIORMENTE
if(($ba!=1))
{
	$recarga=1;
	$ba=1;
}
//echo $ba."<< <br>";

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);


//01Feb2013
//Trae los RESPONSABLES de actividades, ResponsablesActividades (ingresados por los responsables delegando el tema
//y los ParticipantesActividades que se asignan como participantes en el tema 
$sql01="SELECT A.unidad, B.nombre nombreUsu, B.apellidos apellidosUsu, C.nombre nomCategoria, D.nombre nomDepartamento, E.nombre nomDivision ";
$sql01=$sql01." FROM ";
$sql01=$sql01." 	( ";
$sql01=$sql01." 	SELECT DISTINCT id_encargado as unidad ";
$sql01=$sql01." 	FROM Actividades ";
$sql01=$sql01." 	WHERE id_proyecto = " . $cualProyecto ;
$sql01=$sql01." 	and id_encargado is not null ";
$sql01=$sql01." 	UNION ";
$sql01=$sql01." 	SELECT DISTINCT unidad ";
$sql01=$sql01." 	FROM ResponsablesActividad ";
$sql01=$sql01." 	WHERE id_proyecto =  " . $cualProyecto ;
$sql01=$sql01." 	UNION ";
$sql01=$sql01." 	SELECT DISTINCT unidad ";
$sql01=$sql01." 	FROM ParticipantesActividad ";
$sql01=$sql01." 	WHERE id_proyecto =  " . $cualProyecto ;
$sql01=$sql01." 	) A, Usuarios B, Categorias C, Departamentos D, Divisiones E ";
$sql01=$sql01." where A.unidad = B.unidad ";
$sql01=$sql01." AND B.id_categoria = C.id_categoria ";
$sql01=$sql01." AND B.id_departamento = D.id_departamento ";
$sql01=$sql01." AND D.id_division = E.id_division ";
$cursor01 = mssql_query($sql01);








//--------HASTA AQUI

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
$fDivSql=$fDivSql."and estadoDiv = 'A' ";
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

function array_url($arrai)
{
		$tmp=serialize($arrai);  //Serializar el arreglo.
		$url=urlencode($tmp);  //Codificar URL. 
		return($url);
}

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





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--


window.name="winHojaTiempo";


function envia0()
{
	var error = 'n';
	var mensaje="";


	if((document.Form1.Lote_control.value == '')&&(document.Form1.Lote_trabajo.value == '')&&(document.Form1.Division.value == ''))
	{
		document.Form1.recarga.value = 1;
//		document.Form1.ba.value = 0;
		document.Form1.submit();
	}
	else
	{

		if(document.Form1.Lote_control.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione un lote de control. \n';
		}
		if(document.Form1.Lote_trabajo.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione un lote de trabajo. \n';
		}
		if(document.Form1.Division.value == '')
		{
			error = 's';
			mensaje = mensaje + 'Seleccione una división. \n';
		}
		if(error=='s')
		{
			alert(mensaje);
		}
		else
		{
	
			document.Form1.recarga.value = 1;
			document.Form1.submit();
		}
	}
}

//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>


</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;">
Planeaci&oacute;n de proyectos - Participantes</div>
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
	<form name="Form1" id="Form1" method="post" action="">

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
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos03.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n</a></td>
        <td width="15%" height="20" class="FichaAct">Resumen</td>
        <td height="20" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td height="2" colspan="5" class="TituloUsuario"> </td>
        </tr>
    </table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="TxtTabla" height="2" colspan="3"></td>
          </tr>
          <tr>
	        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos04.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Valores</a>
    	    <td width="15%" height="20" class="FichaInAct" > <a href="htPlanProyectos04_planeacion.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n </a></td>
	        <td width="15%" height="20" class="FichaAct">Planeaci&oacute;n vs Facturaci&oacute;n</td>
			 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
          <tr>
            <td class="TxtTabla" height="2" colspan="3"></td>
          </tr>
		
		</table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td  class="TituloUsuario" colspan="3" >.: TOTALES DEL PROYECTO</td>
          </tr>
        </table>

          <tr>
            <td >
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td  align="center">&nbsp;</td>
            <td width="30%"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td rowspan="2"  align="center">
              <table width="100%" border="0" cellpadding="0" cellspacing="1"  bgcolor="#FFFFFF" >
                <tr>
                <td colspan="2" class="TituloUsuario" height="2" align="left" >Criterios de consulta </td>
                </tr>
				<tr>
					<td align="left" class="TituloTabla">Lote de Control
						
					</td>
					<td align="left" class="TxtTabla" >
    <select name="Lote_control" class="CajaTexto" id="Lote_control" onchange="document.Form1.submit();">
                <option value="">::Seleccione un Lote de control::</option>
                <?
						$sql_lote_control="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=1 order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
						$cur_lote_lc=mssql_query($sql_lote_control);
						while($datos_lote_lc=mssql_fetch_array($cur_lote_lc))
						{
							$sel="";
							if($datos_lote_lc["id_actividad"]==$Lote_control)
								$sel="selected";
?>
                <option value="<? echo $datos_lote_lc["id_actividad"]; ?>" <? echo $sel; ?>><? echo strtoupper("[".$datos_lote_lc["macroactividad"]."] ".$datos_lote_lc["nombre"]); ?></option>
                <?
						}

?>
              </select>
				  </td>

				</tr>
                <tr>
<td width="22%"  align="left" class="TituloTabla">Lote de Trabajo</td>
            <td width="78%"  class="TxtTabla"  align="left"><select name="Lote_trabajo" class="CajaTexto" id="Lote_trabajo" onchange="document.Form1.submit();">
              <option value="">::Seleccione un lote de trabajo::</option>
              <?
						$sql_lote_trabajo="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=2 and dependeDe=".$Lote_control;
						$sql_lote_trabajo=$sql_lote_trabajo." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_lote_lt=mssql_query($sql_lote_trabajo);
						while($datos_lote_lt=mssql_fetch_array($cur_lote_lt))
						{
							$sel="";
							if($datos_lote_lt["id_actividad"]==$Lote_trabajo)
								$sel="selected";
?>
              <option value="<? echo $datos_lote_lt["id_actividad"]; ?>" <? echo $sel; ?>><? echo strtoupper("[".$datos_lote_lt["macroactividad"]."] ".$datos_lote_lt["nombre"]); ?></option>
              <?
						}
?>
            </select></td>
            </tr>
          <tr class="TxtTabla" >
            <td width="22%"  align="left" class="TituloTabla">Divisi&oacute;n</td>
            <td width="78%"  align="left"><select name="Division" class="CajaTexto" id="Division" onchange="document.Form1.submit();" >
              <option value="">::Seleccione una division::</option>
              <?
						$sql_div="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=3 and dependeDe=".$Lote_trabajo ." and actPrincipal=".$Lote_control;
						$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_div=mssql_query($sql_div);
						while($datos_div=mssql_fetch_array($cur_div))
						{
							//almacena el id de la division, este valor se utiliza para cargar la division seleccionada en (Asociacion de participanes)

							$sel="";
							if($datos_div["id_actividad"]==$Division)
							{
								$sel="selected";
								$divis=$datos_div["id_division"]; 
								if($division==0) //si la dvision no esta seleccionada, se carga el select de la division con la division del LT
								{
									$division=$datos_div["id_division"]; 
//									echo"<script type='text/javascript'> document.Form1.submit() <script>";
								}
							}
?>
              <option value="<? echo $datos_div["id_actividad"]; ?>"  <? echo $sel; ?> ><? echo strtoupper("[".$datos_div["macroactividad"]."] ".$datos_div["nombre"]); ?> </option>
              <?
						}
?>
            </select></td>
            </tr>

          <tr class="TxtTabla" >
            <td  align="left" class="TituloTabla"> Actividad</td>
            <td  align="left"><select name="Actividad" class="CajaTexto" id="Actividad">
              <option value="">::Seleccione una actividad::</option>
              <?
						$sql_act="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=4 and dependeDe=".$Division ." and actPrincipal=".$Lote_control;
						$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
						$cur_act=mssql_query($sql_act);
						while($datos_act=mssql_fetch_array($cur_act))
						{
							$sel="";
							if($datos_act["id_actividad"]==$Actividad)
								$sel="selected";
?>
              <option value="<? echo $datos_act["id_actividad"]; ?>"  <? echo $sel; ?>><? echo strtoupper("[".$datos_act["macroactividad"]."] ".$datos_act["nombre"]); ?> </option>
              <?
						}
?>
            </select></td>
            </tr>
          <tr class="TxtTabla" >
            <td align="left" class="TituloTabla">Tipo de Personal</td>
            <td align="left">
				<select name="personal" class="CajaTexto" id="personal">
					<option value="T" <? if($personal=='T'){ echo "selected"; } ?> >Todos</option>
					<option value="C" <? if($personal=='C'){ echo "selected"; } ?> >Con planeación</option>
					<option value="S" <? if($personal=='S'){ echo "selected"; } ?>>Sin planeación</option>
				</select>
			</td>
          </tr>
          <tr class="TxtTabla" >
            <td align="left" class="TituloTabla">Vigencia</td>
            <td align="left"><label for="vigencia2"></label>
              <select name="lstVigencia" class="CajaTexto" id="lstVigencia" onchange="document.form1.submit();">
                <? 
		for ($k=$minVigenciaP; $k<=$maxVigenciaP; $k++) { 
			if ($lstVigencia == $k) {
				$selVig = "selected";
			}
			else {
				$selVig = "";
			}
		?>
                <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
                <? } ?>
              </select></td>
          </tr>

          <tr class="TxtTabla" >
            <td colspan="2" align="right" class="TxtTabla"><input type="hidden" name="recarga" value="0" id="recarga" />     
<input type="hidden" name="ba" value="<?=$ba; ?>" id="ba" />     
         <input name="Consultar" onclick="envia0();" type="submit" class="Boton" id="Consultar" value="Consultar" /></td>
            </tr>
            </table></td>

            <td width="30%" rowspan="2"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
          </tr>

  	      </table>
	  </form >
			</td>

          </tr>
        </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80%" colspan="4" align="right" class="TxtTabla">&nbsp;</td>
          </tr>
		  <tr>
				<td>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr class="TxtTabla">
<td align="right"><img src="imagenes/ico11.gif" width="77" height="16">Planeado</td>
    <td align="right">&nbsp;</td>
    <td align="right"><img src="../portal/imagenes/ico1.gif" width="77" height="16">Excede Hombre/Mes la planeaci&oacute;n</td>
    <td align="right">&nbsp;</td>

    <td align="right"><img src="../portal/imagenes/eje100.gif" width="77" height="16">Facturado</td>
    <td align="right">&nbsp;</td>
    <td align="right"><img src="../portal/imagenes/eje1.gif" width="77" height="16">Excede Hombre/Mes la facturaci&oacute;n</td>
            <td>&nbsp;</td>
          </tr>
                    </table>
				</td>
		  </tr>
          <tr>
            <td colspan="4" align="left" class="TituloUsuario">.: Planeaci&oacute;n total del proyecto</td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla"><table width="100%" border="0">
<?php
	//SOLO SE MUESTRA EL BOTON DETALLE, CUANDO SE CONSULTA TODAS LOS PARTICIPANTES DE  LAS ACTIVIDADES
// || ($ba!=1)  --- ||( ($ba!=1) && isset($ba) )

?>


<?php
//echo $recarga." ***** $ba <br><br>";
if($recarga==1)
{
	//PERMITE SABER SI SE HA SELECCIONADO UNA ACTIVIDAD O UNA DIVISION, PARA CONSULTAR LOS PARTICIPANTES
	if(trim($Actividad==""))
	{
		if(trim($Division!=""))
		{
			$act=$Division;
		}
	}
	else
		$act=$Actividad;


		$sql_act_edt="
			SELECT (valMacro *  factor) miOrden, *  FROM ( Select 
			CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro, factor = 
				case nivel 
					when 1 then 100000 		when 2 then 10000 		when 3 then 1000 		when 4 then 100 
				end, A.*, U.nombre nomUsu, U.apellidos apeUsu 	from Actividades A, Usuarios U where A.id_encargado *= U.unidad and A.id_proyecto =".$cualProyecto;

	//FILTRA LA BUSQUEDA POR LA ACTIVIDAD SELECCIONADA
	if((trim($Lote_control)!= '')&&(trim($Lote_trabajo)!= '')&&(trim($Division)!= ''))
	{
 
//***************IMPORTANTE PAGINACION***********************
//PARA ADECUAR LA PAGINACION, SE DEBE REALIZAR UNA CONSULTA SUPERIOR,  CON LAS ACTIVIDADES QUE TIENEN PLANEACION EN LA EDT, TRAYENDO EL ID DE LAS ACTIVIDADES

		$sql_act_edt=$sql_act_edt."	and (id_actividad=".$act." or dependeDe=".$act.") ";

//***************IMPORTANTE PAGINACION***********************
//and A.id_actividad in(14,7,8,17,19,16) //AÑADIR ESTA LINEA A LA CONSULTA, CON EL ID DE LAS ACTIVIDADES, QUE TIENEN PLANEACIÓN



//echo mssql_get_last_message()." **** $ba*** <br>".$sql_act_edt."<br><br>";
	}
/*
	else
	{
		//SI NO SE HA SELECCIONADO  NINGUNA ACTIVIDAD, SE CONSULTA TODA LA INFORMACION DE LAS ACTIVIDADES, QUE TIENEN FACTURACION EN EL PORYECTO
		$sql_act_edt=$sql_act_edt."	and id_actividad in ( select distinct(id_actividad) from FacturacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$lstVigencia." )  ";

// id_actividad=".$act." or dependeDe=".$act.") ";
	}
*/
//	else  //SI SE CONSULTA ALMENOS UNA ACTIVIDAD(DIVISION, ACTIVIDAD), SE DEBE EJECUTAR 1 VEZ EL CURZOR, PARA QUE MUESTRE LA INFO DE LOS PARTICIPANTES
//		$cur_act_edt=mssql_query("select getdate() ");

		$sql_act_edt=$sql_act_edt."		 ) Z
			 order by (valMacro *  factor)  ";	
		$cur_act_edt=mssql_query($sql_act_edt);	
//echo $sql_act_edt."<br> ".mssql_get_last_message();
//echo "CURSOR $cur_act_edt <BR><BR>"	;
}
	$reg_encontrados="No"; //PERMITE DETERMINAR, SI SE ENCONTRARON ALMENOS UN REGISTRO

		//FUNCION, QUE FORMA LA CONSULTA A EJECUTAR, DEPENDIENDO DE LO SELECCIONADO EN LA LISTA  "Tipo de  Personal"
		//SI SON LOS PARTICIPANTES, CON PLANEACION O SIN PLANEACION, ESTO SE DETERMINA, CON EL PARAMETRO $con, QUE PUEDE CONTIENERE EL VALOR ' IN' O 'NOT IN'
		// 'IN' CON PLANEACION Y FACTURACION RELACIONADA
		// 'NOT IN ' CON FACTURACION, PERO SIN PLANEACION 
		function conPlaneacion_sinPlaneacion($cualProyecto,$lstVigencia,$act,$con)
		{
				//CONSULTA DE USUARIOS CON PLANEACION Y FACTURACION RELACIONADA	
				$sql_parti=" select distinct( FacturacionProyectos.unidad),FacturacionProyectos.id_proyecto,FacturacionProyectos.id_actividad, case esInterno when 'E' then (select UPPER(nombre+' '+apellidos)  from TrabajadoresExternos where consecutivo=FacturacionProyectos.unidad )  
				 when 'I' then (select UPPER(nombre+' '+apellidos)  from Usuarios where unidad=FacturacionProyectos.unidad ) end nombre_apellido, 
				 case  esInterno when 'I' then (select fechaRetiro from Usuarios where unidad=FacturacionProyectos.unidad) else  NULL   end  fechaRetiro , vigencia, esInterno
				   from FacturacionProyectos	
				inner join Actividades on FacturacionProyectos.id_actividad=Actividades.id_actividad and FacturacionProyectos.id_proyecto=Actividades.id_proyecto 		
				where FacturacionProyectos.unidad ".$con." (			
				(
				select distinct(PlaneacionProyectos.unidad) from PlaneacionProyectos
							 inner join Actividades on PlaneacionProyectos.id_actividad=Actividades.id_actividad and PlaneacionProyectos.id_proyecto=Actividades.id_proyecto 
							inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
							left join ParticipantesActividad on PlaneacionProyectos.id_proyecto=ParticipantesActividad.id_proyecto and PlaneacionProyectos.id_actividad=ParticipantesActividad.id_actividad
								and ParticipantesActividad.unidad=PlaneacionProyectos.unidad  						
							where  PlaneacionProyectos.esInterno='I' and PlaneacionProyectos.id_actividad=".$act." and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.vigencia=".$lstVigencia."	
				)
				union
				(
				SELECT distinct(PlaneacionProyectos.unidad)
				FROM PlaneacionProyectos 
				inner join ParticipantesExternos on PlaneacionProyectos.id_proyecto=ParticipantesExternos.id_proyecto
					 and PlaneacionProyectos.id_actividad=ParticipantesExternos.id_actividad and PlaneacionProyectos.unidad=ParticipantesExternos.consecutivo
				inner join TrabajadoresExternos on 	 ParticipantesExternos.consecutivo=TrabajadoresExternos.consecutivo
				 where PlaneacionProyectos.esInterno='E' and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.id_actividad=".$act."  and PlaneacionProyectos.vigencia=".$lstVigencia."
				 )
				 ) and  FacturacionProyectos.id_actividad=".$act." and FacturacionProyectos.id_proyecto=".$cualProyecto." and FacturacionProyectos.vigencia=".$lstVigencia." ";

				return $sql_parti;
		}

	while($datos_act_edt=mssql_fetch_array($cur_act_edt))	
	{
//echo "<br><br>--#################### baaaaaaaaaa 1 <br<br>";

		//SI SE CONSULTA TODAS LAS ACTIVIDADES DE LA EDT, SE ALMACENA EL ID DE LA ACTIVIDAD, Y SE GENERA EL ARBOL DE LA MACRO ACTIVIDAD
		if(trim($datos_act_edt["id_actividad"])!="")
		{
			$act=$datos_act_edt["id_actividad"];
			if(( (int) $datos_act_edt["nivel"])==1)
			{
				$macro=""; $macro2=""; $macro3=""; $macro4="";
				$macro=$datos_act_edt["macroactividad"]." ".$datos_act_edt["nombre"];
			}
			if(( (int) $datos_act_edt["nivel"])==2)
			{
				$macro2=""; $macro3=""; $macro4="";
				$macro2=" - ".$datos_act_edt["macroactividad"]." ".$datos_act_edt["nombre"];
			}
			if(( (int) $datos_act_edt["nivel"])==3)
			{
				$macro3=""; $macro4="";
				$macro3=" - ".$datos_act_edt["macroactividad"]." ".$datos_act_edt["nombre"];
			}
			if(( (int) $datos_act_edt["nivel"])==4)
			{
				$macro4="";
				$macro4=" - ".$datos_act_edt["macroactividad"]." ".$datos_act_edt["nombre"];
			}		
			$macros=$macro.$macro2.$macro3.$macro4;
		}
/*
		else
		{

			$macros=$Lote_control."-".$Lote_trabajo."-".$Division."-".$Actividad;
		}

*/
/*

		CONSULTA ANTERIOR
		//USUARIOS CON PLANEACION
		if($personal=='C')
		{
		//CONSULTA LOS USUARIOS CON PLANEACION EN LA ACTIVIDAD
				$sql_parti="
				(
					select distinct(PlaneacionProyectos.unidad),PlaneacionProyectos.id_proyecto ,PlaneacionProyectos.id_actividad,  UPPER(Usuarios.nombre+' '+Usuarios.apellidos)  nombre_apellido ,fechaRetiro,
					PlaneacionProyectos.unidad,vigencia, ParticipantesActividad.estado from PlaneacionProyectos
					 inner join Actividades on PlaneacionProyectos.id_actividad=Actividades.id_actividad and PlaneacionProyectos.id_proyecto=Actividades.id_proyecto 
					inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad 
					left join ParticipantesActividad on PlaneacionProyectos.id_proyecto=ParticipantesActividad.id_proyecto and PlaneacionProyectos.id_actividad=ParticipantesActividad.id_actividad
									and ParticipantesActividad.unidad=PlaneacionProyectos.unidad  
					where PlaneacionProyectos.esInterno='I' and PlaneacionProyectos.id_actividad=".$datos_act_edt["id_actividad"]." and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.vigencia=".$lstVigencia."
				)
				union
				(
					SELECT distinct(PlaneacionProyectos.unidad), PlaneacionProyectos .id_proyecto,PlaneacionProyectos .id_actividad, UPPER(TrabajadoresExternos.nombre+' '+TrabajadoresExternos.apellidos)  nombre_apellido , NULL,PlaneacionProyectos.unidad,vigencia
					,ParticipantesExternos.estado
					FROM PlaneacionProyectos 
					inner join ParticipantesExternos on PlaneacionProyectos.id_proyecto=ParticipantesExternos.id_proyecto
						 and PlaneacionProyectos.id_actividad=ParticipantesExternos.id_actividad and PlaneacionProyectos.unidad=ParticipantesExternos.consecutivo
					inner join TrabajadoresExternos on 	 ParticipantesExternos.consecutivo=TrabajadoresExternos.consecutivo
					 where PlaneacionProyectos.esInterno='E' and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.id_actividad=".$datos_act_edt["id_actividad"]."  and PlaneacionProyectos.vigencia=".$lstVigencia."
				)";
		}
*/



		//USUARIOS CON PLANEACION
		if($personal=='C')
		{
				$sql_parti=conPlaneacion_sinPlaneacion ($cualProyecto,$lstVigencia,$datos_act_edt["id_actividad"],' IN ');			
		}
		
		//USUARIOS SIN PLANEACION, PERO CON FACTURACION 
		if($personal=='S')
		{
				$sql_parti=conPlaneacion_sinPlaneacion ($cualProyecto,$lstVigencia,$datos_act_edt["id_actividad"],' NOT IN ');			
		}	

		//CONSULTA LA PERSONAS QUE TIENESN FACTURACION CON Y SIN PLANEACION
		if($personal=='T')
		{
				$sql_parti=" ( ";
				$sql_parti=$sql_parti.' '.conPlaneacion_sinPlaneacion ($cualProyecto,$lstVigencia,$datos_act_edt["id_actividad"],' NOT IN ');		
				$sql_parti=$sql_parti.') UNION (';
				$sql_parti=$sql_parti.' '.conPlaneacion_sinPlaneacion ($cualProyecto,$lstVigencia,$datos_act_edt["id_actividad"],' IN ');			
				$sql_parti=$sql_parti.' ) ';
		}					
			$cur_parti=mssql_query($sql_parti);
//echo $sql_parti." <br><br>".mssql_get_last_message();
			//PERMITE VALIDAR SI EXISTE ALMENOS UNA ACTIVIDAD CON PLANEACION, Y SI ES LA PRIMERA ACTIVIDAD, QUE SE VA A IMPRIMIR CON LA PLANEACION
			//ESTO PARA QUE MUESTRE EL BOTON DETALLE TOTAL, SOLO UNA VEZ
			if((mssql_num_rows($cur_parti)!=0)&&($ban_reg!=1))
			{
				$ban_reg=1;

			
		//		if( ( (isset ( $Lote_control))&&( isset($Lote_trabajo))&&( isset($Division))&&( isset($Actividad)) )   )
				{
					if((trim($Lote_control)== '')&&(trim($Lote_trabajo)== '')&&(trim($Division)== '')&&(trim($Actividad)== '')&&(trim($lstVigencia)!=""))
					{
			
			?>
						  <tr class="TituloTabla2">
								<td colspan="17" align="right"></td>
				<form name="Form2" id="Form2" method="post" action="http://www.ingetec.com.co/NuevaHojaTiempo/htPlanProyectos04_planeacionvsfact_det_total.php?cualProyecto=<?=$cualProyecto; ?>&V=<?=$lstVigencia; ?>&personal=<?=$personal; ?>">
								<td  align="center" class="TxtTabla"> 
			
								<input name="detalleT" type="submit" class="Boton" id="detalleT" value="Detalle total"  />
			
								</td>
					</form>
						  </tr>
			
			<?php
					}
				}

			}
//echo mssql_get_last_message()."  ddddd <br>".$sql_parti."<br>";


				$ban=0;
				while($datos_parti=mssql_fetch_array($cur_parti))
				{
					$reg_encontrados="Si";
					if($ban==0) //si es la primera fila
					{
//						if((trim($Lote_control)!= '')&&(trim($Lote_trabajo)!= '')&&(trim($Division)!= ''))//&&(trim($Actividad)== ''))
						{
?>


                      <tr class="TituloTabla2">
                        <td colspan="18" align="left" >
							<table width="100%">
								<tr>
									<td width="60%" class="TituloTabla">
								<? $ll=0; while( $ll<((int)$datos_act_edt["nivel"]) ){ echo " &nbsp;"; $ll++; } echo $macros;  echo "(".$datos_act_edt["id_actividad"]." )"; ?>
									</td>
									<td width="10%" class="TituloTabla">Fecha Inicio</td> 
									<td width="10%" class="TxtTabla"><? echo date("M d Y ", strtotime ($datos_act_edt["fecha_inicio"])); ?></td>

									<td width="10%" class="TituloTabla">Fecha Finalización</td> <td width="10%" class="TxtTabla"><? echo date("M d Y ", strtotime ($datos_act_edt["fecha_fin"])); ?></td>
							  </tr>
							</table>
						</td>
                      </tr>
    
                      <tr class="TituloTabla2">
                        <td width="5%" rowspan="2">&nbsp;</td>
                        <td width="5%" rowspan="2">Unidad</td>
                        <td width="30%" rowspan="2">Participantes</td>
                        <td width="5%" rowspan="2">&nbsp;</td>
                        <td width="5%" rowspan="2">Total</td>
                        <td colspan="12"> <? if(trim($lstVigencia)!=""){ echo $lstVigencia;} else {  echo "Meses"; }?> </td>
                        <td width="5%" rowspan="2">&nbsp;</td>
                      </tr>
                      <tr class="TituloTabla2">
                        <td width="4%">1</td>
                        <td width="4%"  >2</td>
                        <td width="4%"  >3</td>
                        <td width="4%" >4</td>
                        <td width="4%"  >5</td>
                        <td width="4%"  >6</td>
                        <td width="4%"  >7</td>
                        <td width="4%"  >8</td>
                        <td width="4%"  >9</td>
                        <td width="4%"  >10</td>
                        <td width="4%"  >11</td>
                        <td width="4%"  >12</td>
                      </tr>
    <?
                        }
                    }
    ?>

                      <tr class="TxtTabla">
                        <td width="3%"  align="center" ><? if(trim($datos_parti["fechaRetiro"])!="") { ?> <img src="imagenes/Inactivo.gif" title="Retirado de la compañia" /> <? } ?></td>
                        <td width="5%"><?php echo $datos_parti["unidad"]; ?></td>
                        <td width="30%"><? echo $datos_parti["nombre_apellido"]; ?></td>
                        <td width="5%" height="100%"><table width="100%" border="0"  height="100%">
                          <tr>
                            <td class="TituloTabla2">Planeaci&oacute;n</td>
                          </tr>

                          <tr>
                            <td class="TituloTabla2">Facturaci&oacute;n</td>
                          </tr>
                        </table></td>
                        <td width="5%" height="100%"><table width="100%" height="100%" border="0">
						  <tr>
						    <td><?
						//SOLO SE EJECUTA LA CONSULTA DE PLANEACION, CUANDO SE HA SELECCIONADO EN "Tipo persona" C='con planeacion' T='Todo'
						if(($personal=='C')	|| ($personal=='T')	)
						{
							//TOTAL PLANEADO 

							$sql_total="select  SUM(hombresMes) as total_H_M_P from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='".$datos_parti["esInterno"]."' ";
							$cur_total=mssql_query($sql_total);
//echo $sql_total." ********** <br>";
							if($datos_total=mssql_fetch_array($cur_total))
								$total=$datos_total["total_H_M_P"];		
	
							echo $total;

						}

?>
&nbsp;
	</td>
					      </tr>

						  <tr>
						    <td>
<?
						//TOTAL FACTURADO
//						$total=0;
						$sql_total="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]. " and esInterno='".$datos_parti["esInterno"]."' ";
						$cur_total=mssql_query($sql_total);
						if($datos_total=mssql_fetch_array($cur_total))
							$total=$datos_total["total_H_M_F"];		

						echo $total;



?>
&nbsp;
							</td>
					      </tr>
					    </table></td>

<?
							$sql_total="select  SUM(hombresMes) as total_H_M_P from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='".$datos_parti["esInterno"]."'  group by mes ";
							$cur_total=mssql_query($sql_total);
						$mes_imP=1;
						$imagene_P=  array();
						while($datos_total=mssql_fetch_array($cur_total))
						{		
							$total_perso=$datos_total["total_H_M_P"];
							//IMAGEN DE LA INFORMACION DE LA PLANEACION		
							if($total_perso<=1)		//	SI Z ES MENOR O IGUAL A 1
							{
								if($total_perso==1) //si EL VALOR ES 1
								{
									$total_perso-=1;
									$ima=" imagenes/ico11.gif";
								}

								else if ( ($total_perso<1) && (0.75<=$total_perso) ) // si el valor esta entre 0.99 y 0.75
								{
//									$total_perso-=0.75;
									$total_perso=0;
									$ima="imagenes/ico22.gif";
								}

								else if ( ($total_perso<0.75) && (0.5<=$total_perso) ) // si el valor esta entre 0.75 y 0.5
								{
//									$total_perso-=0.5;
									$total_perso=0;
									$ima="imagenes/ico33.gif";
								}
								else if ( ($total_perso<0.5) && (0.25<=$total_perso) ) // si el valor esta entre 0.5 y 0.25
								{
//									$total_perso-=0.25;
									$total_perso=0;
									$ima="imagenes/ico44.gif";
								}
								else if ( ($total_perso<0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
								{
//									$total_perso-=0.25;
									$total_perso=0;
									$ima="imagenes/ico66.gif";
								}
								else
								{ $ima="imagenes/ico5.gif"; } 
								

							}
							else //si el valor es mayor a 1
							{
								$ima="../portal/imagenes/ico1.gif";
								$total_perso-=1;
							}
							$imagene_P[$mes_imP]=$ima;
							$mes_imP++;
						}

						$sql_total="select  SUM(hombresMesF) as total_H_M_F, mes from FacturacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]. " and esInterno='".$datos_parti["esInterno"]."' group by mes ";
						$cur_total=mssql_query($sql_total);

						$mes_imF=1;
						while($datos_total=mssql_fetch_array($cur_total))
						{
							$total_perso_fact=$datos_total["total_H_M_F"];
							//IMAGEN DE LA INFROMACION DE LA FACTURACION
							if($total_perso_fact<=1)		//	SI Z ES MENOR O IGUAL A 1
							{
								if($total_perso_fact==1) //si EL VALOR ES 1
								{
									$total_perso_fact-=1;
									$ima2=" imagenes/ico1.gif";
								}

								else if ( ($total_perso_fact<1) && (0.75<=$total_perso_fact) ) // si el valor esta entre 0.99 y 0.75
								{
//									$total_perso-=0.75;
									$total_perso_fact=0;
									$ima2="imagenes/ico2.gif";
								}

								else if ( ($total_perso_fact<0.75) && (0.5<=$total_perso_fact) ) // si el valor esta entre 0.75 y 0.5
								{
//									$total_perso-=0.5;
									$total_perso_fact=0;
									$ima2="imagenes/ico3.gif";
								}
								else if ( ($total_perso_fact<0.5) && (0.25<=$total_perso_fact) ) // si el valor esta entre 0.5 y 0.25
								{
//									$total_perso-=0.25;
									$total_perso_fact=0;
									$ima2="imagenes/ico4.gif";
								}
								else if ( ($total_perso_fact<0.25) && (0<$total_perso_fact) ) // si el valor esta entre 0.25 y 0.01
								{
//									$total_perso-=0.25;
									$total_perso_fact=0;
									$ima2="imagenes/ico6.gif";
								}
								else
								{ $ima2="imagenes/ico5.gif"; } 
								

							}
							else //si el valor es mayor a 1
							{
								$ima2="../portal/imagenes/eje1.gif";
								$total_perso_fact-=1;
							}
							$imagene_F[$mes_imF]=$ima2;
							$mes_imF++;
						}

						for($ind=1; $ind<=12; $ind++)
						{		
?>
    
                            <td width="4%" ><table width="100%" height="100%"  border="0">
                              <tr>
                                <td  height="100%"  background="<?  echo $imagene_P[$ind]; ?>">&nbsp;</td>
                              </tr>
                              <tr>
                                <td  height="100%"  background="<?  echo $imagene_F[$ind]; ?>">&nbsp;</td>
                              </tr>
                            </table></td>     
<?
						}

		
						if($ban==0)
						{
//echo "<br> Ingreso<br> ";
							$ban_todos=0; //BANDERA, PARA IDENTIFICAR SI SE ESTAN CONSULTANDO TODAS LAS ACTIVIDADES
							//SI SE ESTA CONSULTANDO TODAS LAS ACTIVIDADES, SE CARGAR LOS PARAMETROS DE FORMA DIFERENTE
							if((trim($Lote_control)== '')&&(trim($Lote_trabajo)== '')&&(trim($Division)== '')&&(trim($Actividad)== ''))
							{
//echo "Ingresa 1 -- ".$datos_act_edt["id_actividad"]. " <br>";
								if($datos_act_edt["nivel"]==4) //SI ES LA ACTIVIDAD, DE NIVEL 4
								{
									$Actividad=$datos_act_edt["id_actividad"];
									$Lote_control=$datos_act_edt["actPrincipal"];
									$Division=$datos_act_edt["dependeDe"];
								} 
								if($datos_act_edt["nivel"]==3) //SI ES LA ACTIVIDAD, DE NIVEL 3
								{
									$Lote_control=$datos_act_edt["actPrincipal"];
									$Lote_trabajo=$datos_act_edt["dependeDe"];		
									$Division=$datos_act_edt["id_actividad"];
							
								}
//echo " LC ".$Lote_control." LT ".$Lote_trabajo." DIV ".$Division." ACT ".$Actividad." <br>";
								$ban_todos=1;
							}

/*
							//SI SE ESTAN CONSULTANDO TODAS LAS ACTIVIDADES DE UNA DIVISION, SE ALMACENA EL ID DE LAS ACTIVDADES
							if((trim($Lote_control)!= '')&&(trim($Lote_trabajo)!= '')&&(trim($Division)!= ''))
							{
//echo "Ingresa 2 -- ".$datos_act_edt["id_actividad"]. " <br>";
								if($datos_act_edt["nivel"]==4) //SI ES LA ACTIVIDAD, DE NIVEL 4
								{
									$Actividad=$datos_act_edt["id_actividad"];
									$Lote_control=$datos_act_edt["actPrincipal"];
									$Division=$datos_act_edt["dependeDe"];
								} 
							}
*/

?>	
	<form name="Form2" id="Form2" method="post" action="http://www.ingetec.com.co/NuevaHojaTiempo/htPlanProyectos04_planeacionvsfact_det.php?cualProyecto=<?=$cualProyecto; ?>&LC=<?=$Lote_control; ?>&DIV=<?=$Division; ?>&ACT=<?=$Actividad; ?>&V=<?=$lstVigencia; ?>&T=<?=$Lote_trabajo; ?>&personal=<?=$personal; ?>">
            <td width="5%" rowspan="<?=mssql_num_rows($cur_parti)*2 ?>" align="center" valign="middle"><input name="detalle" type="submit" class="Boton" id="detalle" value="Detalle"  /></td>
	</form>

  	            </tr>
<?
							//SI SE ESTAN CONSULTANDO TODAS LAS ACTIVIDADES DEL PROYECTO, SE LIMPIAN LAS VARIABLES, 
							//PARA VOLVER A ACARGRALAS, EN EL MOMENTO DE CONSULTAR, LA SIGUIENTE ACTIVIDAD
							if($ban_todos==1)
							{
									$Actividad='';
									$Lote_control='';
									$Division='';
									$Lote_trabajo='';
							}

							//SI SE TRHAE MAS DE UN PARTICIPANTE, EN LA CONSULTA, SE MUESTRA LA FRANJA GRSI QUE DIVIDE LOS PARTICIPANTES EN LA ACTIVIDAD
							if( ( (int) mssql_num_rows($cur_parti) )>1 )
							{
			?>
								<tr>
										<td colspan="17" class="TituloTabla2"  > </td>
								</tr>
			<?php
							}
							$ban=1;
						}

				}
				if( ( (int) mssql_num_rows($cur_parti) )!=0 )
				{
?>
                      <tr class="TituloUsuario">
                        <td colspan="18" align="left" class="TituloUsuario"> </td>
                      </tr>
<?
				}
	}
	//PERMITE DETERMINAR, SI SE HAN ENCONTRADO REGISTROS
	if(($reg_encontrados=="No")&&($recarga==1)&&(isset ( $Lote_control))&&( isset($Lote_trabajo))&&( isset($Division))&&( isset($Actividad)))
	{
			echo "<tr><td colspan='17' align='center'></td></tr>
					<tr><td colspan='17' align='center'  class='TituloTabla2'>No se han encontrado registros.</td></tr>";
	}


?>

            </table></td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla">&nbsp; </td>
          </tr>
        </table>

	<table width="100%" cellpadding="0" cellspacing="0" >
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
	</table>


</body>
</html>
