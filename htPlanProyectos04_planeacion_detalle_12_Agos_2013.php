<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//SI EL PARAMETRO T (LOTE DE TRABAJO), ESTA VACIO, ES POR QUE SE HA CONSULTADO TODAS LAS ACTIVIDADES  EN LA PAGINA ANTERIOR, Y SE HA SELECCIONADO EL DETALLE DE UNA ACTIVIDAD (NIVEL 4)
//ENTONCES SE CONSULTA EL ID DEL LOTE DE TRABAJO DE LA DIVISON A LA CUAL PERTENECE LA ACTIVIDAD (NIVEL 4)
if(trim($T)=="")
{
	$sql_lt_act="select dependeDe from Actividades where id_actividad=".$DIV."  and id_proyecto=".$cualProyecto."  and actPrincipal=".$LC;
	$cur_lt_act=mssql_query($sql_lt_act);
	if($datos_lt_act=mssql_fetch_array($cur_lt_act))
	{
		$T=$datos_lt_act["dependeDe"];
	}
}


$LT=$T;



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

/*

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


*/





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

/*
//11Sep2008
//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$fDivSql="Select * from divisiones ";
$fDivSql=$fDivSql." where (nombre <> '' and nombre <> 'sd') ";
$fDivSql=$fDivSql."and estadoDiv = 'A' ";
$fDivSql=$fDivSql." order by nombre ";
$fDivCursor = mssql_query($fDivSql);
*/
//22Ene2008
//Trae el nombre de las actividades asociadas al proyecto
//Si el usuario es Director, Coordinador, Ordenador del gasto o Programador del proyecto
//ve todas las actividades del proyecto
$primerActiv = 1;
//if ($verProyecto=="SI") { 
//o cuando es el Administrador del sistema o se trata de Camilo Marulanda
//if (($verProyecto=="SI") OR ($_SESSION["sesPerfilUsuario"] == 1 ) OR ($laUnidad == 14384) ) { 
/*
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
*/
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

	if(trim($ACT==""))
	{
		if(trim($DIV!=""))
		{
			$act=$DIV;
		}
	}
	else
		$act=$ACT;

		//CONSULTA LOS PARTICIPANTES DE LA ACTIVIDAD

//CONSULTA PARA CORREGIR, POR QUE NO SE, NO SE ESTAN ASOCIANDO LOS PARTICIPANTES CON LA PLANEACION
		$sql_parti="
			select distinct(PlaneacionProyectos.unidad),PlaneacionProyectos.id_proyecto ,PlaneacionProyectos.id_actividad,  UPPER(Usuarios.nombre) nombre,UPPER(Usuarios.apellidos) apellidos,fechaRetiro,
			PlaneacionProyectos.unidad,vigencia from PlaneacionProyectos
			 inner join Actividades on PlaneacionProyectos.id_actividad=Actividades.id_actividad and PlaneacionProyectos.id_proyecto=Actividades.id_proyecto 
			inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad 
			where PlaneacionProyectos.id_actividad=".$act." and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.vigencia=".$V;

/* INCLUIR ESTE CODIGO EN LA CONSULTA
inner join ParticipantesActividad on PlaneacionProyectos.unidad=ParticipantesActividad.unidad 
			and 
PlaneacionProyectos.id_proyecto=ParticipantesActividad.id_proyecto and PlaneacionProyectos.id_actividad=ParticipantesActividad.id_actividad	
*/

// O  CAMBIAR CONSULTA POR ESTA
/*			select 
			distinct(PlaneacionProyectos.unidad),PlaneacionProyectos.id_proyecto ,PlaneacionProyectos.id_actividad,  UPPER(Usuarios.nombre) nombre,UPPER(Usuarios.apellidos) apellidos,fechaRetiro,
			PlaneacionProyectos.unidad,vigencia, ParticipantesActividad.estado			
			from ParticipantesActividad			
			 inner join Actividades on ParticipantesActividad.id_actividad=Actividades.id_actividad and ParticipantesActividad.id_proyecto=Actividades.id_proyecto 
			inner join Usuarios on ParticipantesActividad.unidad=Usuarios.unidad 			
inner join PlaneacionProyectos on ParticipantesActividad.unidad=PlaneacionProyectos.unidad and 
PlaneacionProyectos.id_proyecto=ParticipantesActividad.id_proyecto and PlaneacionProyectos.id_actividad=ParticipantesActividad.id_actividad			



*/

			$cur_parti=mssql_query($sql_parti);
//echo mssql_get_last_message()."  ddddd <br>".$sql_parti;

	//CONSULTA LA FECHA DE INICIO Y FINALIZACION DE LA ACTIVIDAD
	$sql_act1="select fecha_inicio,fecha_fin, year(fecha_inicio) as y_i ,month(fecha_inicio) as m_i, day(fecha_inicio) as d_i  ,year(fecha_fin) as y_f ,month(fecha_fin) as m_f,day(fecha_fin) as d_f  from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$act." ";
	$cur_act1=mssql_query($sql_act1);
	$datos_act1=mssql_fetch_array($cur_act1);

	$ano_i=$datos_act1["y_i"];
	$mes_i=$datos_act1["m_i"];
	$dia_i=$datos_act1["d_i"];
	$fecha_i=$datos_act1["fecha_inicio"];

	$ano_f=$datos_act1["y_f"];
	$mes_f=$datos_act1["m_f"];
	$dia_f=$datos_act1["d_f"];
	$fecha_f=$datos_act1["fecha_fin"];

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
    	    <td width="15%" height="20" class="FichaAct" >PLANEACI&Oacute;N</td>

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
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td >
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td width="20%"  align="center">&nbsp;</td>
            <td width="20%"  align="center">&nbsp;</td>
            <td width="30%"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td colspan="2" rowspan="2"  align="center"  bgcolor="#FFFFFF"  ><table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr>
                <td colspan="2" class="TituloUsuario" height="2" align="left" >Criterios de consulta </td>
                </tr>
          <tr class="TxtTabla" >
            
            <td width="20%"  align="center" class="TituloTabla2">Lote de Control</td>
            <td width="10%"  align="left"><label for="LC"></label>
              
              <?
						$sql_lote_control="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=1 and id_actividad=".$LC." order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
						$cur_lote_lc=mssql_query($sql_lote_control);
//echo $sql_lote_control." --- ".mssql_get_last_message();
						while($datos_lote_lc=mssql_fetch_array($cur_lote_lc))
						{

?>
              <input type="text" class="CajaTexto" value="<? echo strtoupper("[".$datos_lote_lc["macroactividad"]."] ".$datos_lote_lc["nombre"]); ?> " size="30"  readonly="readonly" >
              <?
						}
// echo $sql_lote_control." ** ".mssql_get_last_message(); 
?>
              </td>
            
          </tr>
          <tr class="TxtTabla" >
            <td width="20%"  align="center" class="TituloTabla2">Lote de Trabajo</td>
            <td width="10%"  align="left">
              
              <?
						$sql_lote_trabajo="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=2 and dependeDe=".$LC." and id_actividad=".$LT;
						$sql_lote_trabajo=$sql_lote_trabajo." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_lote_lt=mssql_query($sql_lote_trabajo);
//echo $sql_lote_trabajo." --- ".mssql_get_last_message();
						while($datos_lote_lt=mssql_fetch_array($cur_lote_lt))
						{

?>
              <input type="text" class="CajaTexto" value="<? echo strtoupper("[".$datos_lote_lt["macroactividad"]."] ".$datos_lote_lt["nombre"]); ?>" size="30" readonly > </option>
              <?

						}
?>
              </td>
          </tr>
          <tr class="TxtTabla" >
            <td width="20%"  align="center" class="TituloTabla2">Divisi&oacute;n</td>
            <td width="10%"  align="left">
              <?
						$sql_div="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=3 and dependeDe=".$LT ." and actPrincipal=".$LC." and id_actividad=".$DIV;
						$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_div=mssql_query($sql_div);
//echo $sql_div." --- ".mssql_get_last_message();
						while($datos_div=mssql_fetch_array($cur_div))
						{
							//almacena el id de la division, este valor se utiliza para cargar la division seleccionada en (Asociacion de participanes)


?>
              <INPUT type="text" class="CajaTexto" value="<? echo strtoupper("[".$datos_div["macroactividad"]."] ".$datos_div["nombre"]); ?> " size="30"  readonly="readonly" />
              <?
						}
?>
           </td>
            </tr>

          <tr class="TxtTabla" >
            <td width="20%"  align="center" class="TituloTabla2"> Actividad</td>
            <td  align="left">
              <?
						$sql_act="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=4 and dependeDe=".$DIV ."  and id_actividad=".$ACT." and actPrincipal=".$LC;
						$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
						$cur_act=mssql_query($sql_act);
//echo $sql_act." --- ".mssql_get_last_message();
						if($datos_act=mssql_fetch_array($cur_act))
						{
?>
              <input type="text" class="CajaTexto" value="<? echo strtoupper("[".$datos_act["macroactividad"]."] ".$datos_act["nombre"]); ?>" size="30" readonly="readonly" >
              <?
						}
						else
			   	    	  echo '   <input type="text" class="CajaTexto" value="" size="30" readonly="readonly" > ';

// echo $sql_act." ** ".mssql_get_last_message(); 
?>
            </td>
            </tr>
          <tr class="TxtTabla" >
            <td width="20%" align="right" class="TituloTabla2">Vigencia</td>
            <td align="left"><label for="vigencia"></label>
              
              
              <input type="text" class="CajaTexto"  value="<? echo $V; ?>" size="7" readonly="readonly" >
              
              </td>
          </tr>
          <tr class="TxtTabla" >
            <td colspan="2" align="right"><table width="100%" border="0">
              <tr>
                <td width="25%" align="center" class="TituloTabla">Fecha I
                  (m/d/a)</td>
<?
/*	if( ((int) $mes_i)<10 )
		$mes_i="0".$mes_i;
	if( ((int) $mes_f)<10 )
		$mes_f="0".$mes_f;

	if( ((int) $dia_f)<10 )
		$dia_f="0".$dia_f;
	if( ((int) $dia_i)<10 )
		$dia_i="0".$dia_i;
*/
?>
                <td width="25%" align="left" class="TxtTabla"><INPUT type="text" class="CajaTexto" name="fecha_i" id="fecha_i" value="<? echo   date("M d Y ", strtotime ( $fecha_i));
//$mes_i."/".$dia_i."/".$ano_i; ?> " size="11"  readonly="readonly" /></td>
                <td width="25%" align="center" class="TituloTabla">Fecha F
                  (m/d/a)</td>
                <td width="25%" align="left" class="TxtTabla"><INPUT type="text" class="CajaTexto" name="fecha_f" id="fecha_f" value="<? echo date("M d Y", strtotime( $fecha_f)); // $mes_f."/".$dia_f."/".$ano_f; ?> " size="11"  readonly="readonly" /></td>
              </tr>
            </table></td>
            </tr>



            </table></td>
            <td width="30%" rowspan="3"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%" rowspan="2"  align="center"></td>
          </tr>
          <tr class="TxtTabla" >
            <td colspan="2"  align="center" class="TxtTabla">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >


        	    <td colspan="4"  align="left"><a href="htPlanProyectos04_planeacion.php?cualProyecto=<?=$cualProyecto; ?>&Lote_control=<?=$LC ?>&Lote_trabajo=<?=$LT ?>&Division=<?=$DIV; ?>&Actividad=<?=$ACT ?>&lstVigencia=<?=$V; ?>" class="menu">&lt;&lt; Regresar a la planeaci&oacute;n</a></td>
        	    </tr>

  	      </table>
			</td>

          </tr>
        </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80%" colspan="4" align="right" class="TxtTabla">&nbsp;</td>
          </tr>
          <tr>
            <td  align="left" class="TituloUsuario">.: Planeaci&oacute;n Actividad</td>
          </tr>
          <tr>
            <td colspan="4" align="right" class="TxtTabla"><table width="100%" border="0">
              <tr class="TituloTabla2">
                <td width="3%" rowspan="2">&nbsp;</td>
                <td width="5%" rowspan="2">Unidad</td>
                <td width="30%" rowspan="2">Participantes</td>
                <td width="5%" rowspan="2">Total</td>
                <td colspan="13"><? echo $V; ?></td>


              </tr>
              <tr class="TituloTabla2">
                <td width="4%">Enero</td>
                <td width="4%"  >Febrero</td>
                <td width="4%"  >Marzo</td>
                <td width="4%" >Abril</td>
                <td width="4%"  >Mayo</td>
                <td width="4%"  >Junio</td>
                <td width="4%"  >Julio</td>
                <td width="4%"  >Agosto</td>
                <td width="4%"  >Septiembre</td>
                <td width="4%"  >Octubre</td>
                <td  >Noviembre</td>
                <td  >Diciembre</td>
                <td ></td>
                </tr>
<?php
				while($datos_parti=mssql_fetch_array($cur_parti))
				{
					$ban=1;  //permite saber que mes del año se esta dibujando(1,2,3....,12)
?>
                      <tr class="TxtTabla">
                        <td width="3%" rowspan="2" align="center" ><? if(trim($datos_parti["fechaRetiro"])!="") { ?> <img src="imagenes/Inactivo.gif" title="Retirado" /> <? } ?></td>
                        <td width="5%" rowspan="2" align="left"><?php echo $datos_parti["unidad"]; ?></td>
                        <td width="30%" rowspan="2" align="left"><? echo $datos_parti["apellidos"]." ".$datos_parti["nombre"]; ?></td>
<?


						//CONSULTA LA INFORMACION DE LA PLANEACIÓN PARA LA PERSONA
						$sql_total="select (select  SUM(hombresMes) as total_H_M from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"].") as total_H_M ,* from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." ORDER BY(mes)";
						$cur_total=mssql_query($sql_total);
//echo $sql_total." ... ".mssql_get_last_message()."<br>";
						while($datos_total=mssql_fetch_array($cur_total))
						{
								
								$total=$datos_total["total_H_M"];		
		
								if($ban==1)				
								{
		?>
						<td width="5%" rowspan="2" align="center">
											<?php echo $total; ?>
						</td>
		<?
								}

								//AUMENTA EL VALOR DE $ban PARA IGUALAR ESTA A EL  MES MAS RECIENTE, PLANEADO PARA LA PERSONA
								while($ban<$datos_total["mes"])
								{
										$aplica="";
										if(($V<$ano_i))
										{
												$aplica='class="TituloTabla2"';		
										}
										else if(($ano_f<$V))
										{
												$aplica='class="TituloTabla2"';		
										}
										else
										{
									
											if($V==$ano_i)
											{
									
												if($ban<$mes_i)
												{
													$aplica='class="TituloTabla2"';		
												}
											}
									
											if($V==$ano_f)
											{
									
												if($mes_f<$ban)
												{
													$aplica='class="TituloTabla2"';		
												}
											}
									
										}

									echo "<td ".$aplica."></td>";
									$ban++;
								}

								//VERIFICA QUE EL MES DEL REGISTRO SEA IGUAL A LA VARIABLE QUE SE RRECORE PARA VALIDAR EL HOMBRE MES Y ASI MOSTRAR LA IMAGEN CORRESPONDIENTE
								if($ban==$datos_total["mes"])
								{
								
									$total_perso=$datos_total["hombresMes"];
				
									if($total_perso<=1)		//	SI Z ES MENOR O IGUAL A 1
									{
										if($total_perso==1) //si EL VALOR ES 1
										{
											$total_perso-=1;
											$ima=" imagenes/ico1.gif";
										}
		
										else if ( ($total_perso<1) && (0.75<=$total_perso) ) // si el valor esta entre 0.99 y 0.75
										{
		//									$total_perso-=0.75;
											$total_perso=0;
											$ima="imagenes/ico2.gif";
										}
		
										else if ( ($total_perso<0.75) && (0.5<=$total_perso) ) // si el valor esta entre 0.75 y 0.5
										{
		//									$total_perso-=0.5;
											$total_perso=0;
											$ima="imagenes/ico3.gif";
										}
										else if ( ($total_perso<0.5) && (0.25<=$total_perso) ) // si el valor esta entre 0.5 y 0.25
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="imagenes/ico4.gif";
										}
										else if ( ($total_perso<0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="imagenes/ico6.gif";
										}
										else
										{ $ima="imagenes/ico5.gif"; } 
										
		
									}
									else //si el valor es mayor a 1
									{
										$ima="../portal/imagenes/ico1.gif";
										$total_perso-=1;
									}
		?>
						<td width="4%" align="center" background="<?  echo $ima; ?>" class="TxtTabla" ><?php echo $datos_total["hombresMes"]; ?></td>                        
		<?
								}		
								$ban++;
						}
//echo $ban." ** ";
						while($ban<13)
						{
							echo "<td></td>";
							$ban++;
						}						
?>	
		<!--	PARA EL ESTADO DEL USUARIO, EN LAS ACTIVIDADES -->
                <td >ESTADO </td>
           	    </tr>

                <tr>

                </tr>
                <tr>
                    <td colspan="17">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" class="TituloTabla2">
                      <tr>
                        <td></td>
                        </tr>
                      </table></td>
                </tr>

<?
				}
?>
            </table></td>
          </tr>

          <tr>
            <td colspan="4" align="right" class="TxtTabla">&nbsp; <input type="hidden" name="recarga" value="0" id="recarga" /> </td>
          </tr>
        </table>
        </td>
      </tr>
	  </form >
    </table>
	<table>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
	</table>


</body>
</html>
