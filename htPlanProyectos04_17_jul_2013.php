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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--


window.name="winHojaTiempo";
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
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
    	    <td width="15%" height="20" class="FichaAct" >VALORES</td>
	        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos04_planeacion.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n</a>
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
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td  >
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?
	//CONSULTA EL VALOR DEL  PROYECTO
	$sql_proyecto_valor_total=" select valorProyecto from Proyectos where id_proyecto= ".$cualProyecto." ";
//echo $sql_proyecto_valor_total."<br>".mssql_get_last_message();
	$cur_proyecto_valor_total=mssql_query($sql_proyecto_valor_total);
	$z=1;
	if($datos_proyecto_valor_total=mssql_fetch_array($cur_proyecto_valor_total))
	{
		$val_proye=$datos_proyecto_valor_total["valorProyecto"];
	}

	if(trim($val_proye)=="")
		$val_proye=0;

	//CONSULTA EL VALOR FACTURADO
	$datos_facturacion=mssql_fetch_array(mssql_query("select sum(valorFacturado) as val_facturado from HojaDeTiempo.dbo.FacturacionProyectos where id_proyecto =".$cualProyecto));
	if(trim($datos_facturacion["val_facturado"])=="")
		$val_facturado=0;
	else
		$val_facturado=$datos_facturacion["val_facturado"];

	//CONSULTA EL VALOR PLANEADO
	$datos_divs=mssql_fetch_array(mssql_query("select  SUM(valorPlaneado) as valor_planeado_divisiones FROM HojaDeTiempo.dbo.PlaneacionProyectos WHERE id_proyecto = ".$cualProyecto." "));
	if(trim($datos_divs["valor_planeado_divisiones"])=="")
		$val_planea=0;
	else
		$val_planea=$datos_divs["valor_planeado_divisiones"];

			?>
        
          <tr class="TxtTabla" >
			<td valign="top" width="35%" >
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
				    <td class="TituloUsuario">&nbsp;</td>
				    </tr>
				  </table>
				<table width="100%" border="0" align="left" cellpadding="0" cellspacing="1" valign="top" >
                  		<tr class="TxtTabla">
                            <td class="TituloTabla2" width="35%" >Valor del proyecto </td>
                            <td align="right"> $<? echo number_format($val_proye, "2", ",", "." )   ?></td>
                            <td class="TituloTabla2" width="35%" >&nbsp;  </td>
                    </tr>
                
                          <tr class="TxtTabla">
                            <td class="TituloTabla2" width="35%" >Valor  facturado</td>
                            <td align="right"> $<? echo number_format($val_facturado, "2", ",", "." )   ?></td>
                            <td class="TituloTabla2" width="35%" >&nbsp;  </td>
                          </tr>
                          <tr class="TxtTabla">
                            <td class="TituloTabla2" width="35%" >Valor  planeado</td>
                            <td align="right"> $<?  echo number_format($val_planea, "2", ",", "." );   ?></td>
                            <td class="TituloTabla2" width="35%" >&nbsp;  </td>
                          </tr>
				</table>


				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr class="TituloUsuario">
				    <td height="3"> </td>
				    </tr>
			    </table></td>

        	    <td  align="center" colspan="4"><img src='grafico.php?val_proy=<?=$val_proye ?>&val_fact=<?=$val_facturado; ?>&val_planea=<?=$val_planea; ?>&titulo=<?='Vl proyecto vs Vl planeado vs Vl facturado ' ?>' border='0' width="765" height="329" />
			<?php //$url=
//graficos_barra( $val_proye,$val_proye,'Valores del proyecto'); 
#				echo	<img src='<?php graficos_barra( $val_proye,$val_proye,'Valores del proyecto'); ' border='0' width='695' height='289' />
				
// graficos_barra( $val_proye,$val_proye,'Valores del proyecto'); ?>

				</td>

          </tr>

  	      </table>			
		</td>

          </tr>
        </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        </td>
      </tr>
	  </ form >
    </table>

<table  width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td  class="TituloUsuario" colspan="3" >.:Totales por divisi&oacute;n (Planeado vs Facturado)</td>
          </tr>
        </table>
		<table width="100%" border="0" cellpadding="0" cellspacing="1">
          <tr>
            <td align="right" class="TxtTabla"><table width="100%" border="0" cellpadding="0" cellspacing="1">
              <tr>
                <td width="35" colspan="2">

<?

	$ban_facturacion=0; //permite verificar, si el proyecto tiene facturacion en alguna de sus divisiones.
	$ban_planeacion=0; //permite verificar, si el proyecto tiene planeacion en alguna de sus divisiones.


	$total_valor_planeado_division=0;
	$total_valor_asignado_division=0;
	$total_valor_facturado_division=0;

	$l=0;
	$nom_div= array();

	//CONSULTA EL VALOR ASIGNADO A LAS DIVISIONES EN EL PROYECTO
	$sql_div_asignado="select D.nombre, AsignaValorDivision.id_division, valorAsignado from AsignaValorDivision 
						inner join Divisiones D on D.id_division=AsignaValorDivision.id_division where id_proyecto=".$cualProyecto."  order by(D.id_division) ";
	$cur_div_asignado=mssql_query($sql_div_asignado);
	$cant_filas=mssql_num_rows($cur_div_asignado);

	while($datos_div_asignado=mssql_fetch_array($cur_div_asignado))
	{

			$total_valor_asignado_division+=$datos_div_asignado["valorAsignado"];
			$nom_div[$l]= strtoupper($datos_div_asignado["nombre"]);
			
			$val_asignado[$l]=$datos_div_asignado["valorAsignado"];

"select  SUM(valorPlaneado) as valor_planeado_divisiones FROM HojaDeTiempo.dbo.PlaneacionProyectos WHERE id_proyecto = ".$cualProyecto;
			$sql_division_valor_total="
			select  SUM(valorPlaneado) as valor_planeado_divisiones FROM HojaDeTiempo.dbo.PlaneacionProyectos WHERE id_proyecto = ".$cualProyecto." 
			AND id_actividad IN( 
				--consulta las actividades (nivel 3) que estan asociadas con la division
				select  id_actividad FROM HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nivel=3 and id_division=".$datos_div_asignado["id_division"]." 
					union
				--consulta las actividades	(nivel 4) asociadas a la division
				select  id_actividad FROM HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and dependeDe in (
					select  id_actividad FROM HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nivel=3 and id_division=".$datos_div_asignado["id_division"]." ))";
			$cur_division_valor_total=mssql_query($sql_division_valor_total);

			if($datos_division_valor_total=mssql_fetch_array($cur_division_valor_total))
			{

						if($datos_division_valor_total["valor_planeado_divisiones"]!="")
						{ 
							$val_planeado[$l]+=$datos_division_valor_total["valor_planeado_divisiones"]; //suma el valor planeado para las divisiones, para graficarlo
							$total_valor_planeado_division+=$datos_division_valor_total["valor_planeado_divisiones"]; 
							$ban_planeacion=1;//el proyecto tiene facturacion
						} 
						else
							$val_planeado[$l]=0;
			}
			else
							$val_planeado[$l]=0;
/*
		//CONSULTA QUE CONSOLIDA EL VALOR TOTAL ASIGNADO A CADA DIVISION, EN EL PROYECTO
			$sql_division_valor_total="
			select  SUM(valor) as valor_division FROM  HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nivel=3 and id_division=".$datos_div_asignado["id_division"]." ";
//echo $sql_division_valor_total."<br>".mssql_get_last_message();
			$cur_division_valor_total=mssql_query($sql_division_valor_total);

			if($datos_division_valor_total=mssql_fetch_array($cur_division_valor_total))
			{

						if($datos_division_valor_total["valor_division"]!="")
						{ 
							$val_planeado[$l]+=$datos_division_valor_total["valor_division"]; //suma el valor planeado para las divisiones, para graficarlo
							$total_valor_planeado_division+=$datos_division_valor_total["valor_division"]; 
							$ban_planeacion=1;//el proyecto tiene facturacion
						} 
						else
							$val_planeado[$l]=0;
			}
			else
							$val_planeado[$l]=0;
*/

			$sql_factu="
			select  SUM(valorFacturado) as valor_facturado_divisiones FROM HojaDeTiempo.dbo.FacturacionProyectos WHERE id_proyecto = ".$cualProyecto." 
			AND id_actividad IN( 
				--consulta las actividades (nivel 3) que estan asociadas con la division
				select  id_actividad FROM HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nivel=3 and id_division=".$datos_div_asignado["id_division"]." 
					union
				--consulta las actividades	(nivel 4) asociadas a la division
				select  id_actividad FROM HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and dependeDe in (
					select  id_actividad FROM HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nivel=3 and id_division=".$datos_div_asignado["id_division"]." 	)) ";

			$cur_factu=mssql_query($sql_factu);
//echo $sql_factu."<br>".mssql_get_last_message();
			if($datos_factu=mssql_fetch_array($cur_factu))
			{

						if($datos_factu["valor_facturado_divisiones"]!="")
						{ 
//echo  "L ".$l." -+- " .$datos_factu["valor_facturado_divisiones"]." -*- ".$val_facturado_div[$l]."<br>";
							$val_facturado_div[$l]+=( (int) $datos_factu["valor_facturado_divisiones"]); //suma el valor facturado en las divisiones, para graficarlo

							$total_val_facturado+=$datos_factu["valor_facturado_divisiones"]; 
							$ban_planeacion=1;//el proyecto tiene facturacion
						} 
						else
							$val_facturado_div[$l]=0;

			}
			else
							$val_facturado_div[$l]=0;


	//		$total_val_facturado[$l]=10000; //temporal para quitar y adacptar, una vez se implemente EL MODULO DE LA  FACTURACION

			$l++;	
	}


?>
			<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr valign="top" >
			<td valign="top" class="TxtTabla" width="35%">
				<table width="100%" border="0" cellpadding="0" class="TituloUsuario">
				  <tr>
				    <td>&nbsp;</td>
				    </tr>
				  </table>
				<table width="100%">
                     <tr class="TituloTabla2">	
                        <td  width="5%">No</td>
                        <td  width="10%">Divisi&oacute;n</td>
                        <td width="10%">Valor Asignado </td>
                        <td width="10%">Valor Planeado </td>
                        <td width="10%">Valor Facturado </td>
					</tr>
					<?php
                        $i=0;
						$id_div=1;
                        for($c=0;$c<$cant_filas;$c++)
                        {
                            for($m=0;$m<1;$m++)
                            {
                        ?>
                                 <tr class="TxtTabla">
                                            <td align="center"> <strong><span class="TxtTabla"><?php echo $id_div; ?></span></strong></td>
                                            <td align="center">                                            <strong><span class="TxtTabla">
                                            <?php  echo  $nom_div[$i]; ?>
                                            </span></strong></td>
                                   <td align="right" >$<? echo  number_format($val_asignado[$i], "2", ",", "." ); ?></td>
                        
                                            <td width="10%" align="right" class="TxtTabla">$<?php echo number_format($val_planeado[$i], "2", ",", "." ); ?>                                            </td>
                        
                                        <td width="10%" align="right">$<?php 	echo  number_format($val_facturado_div[$i], "2", ",", "." ); ?>                                        </td>		
                    </tr>
                    <?
                            }
                            $i++;
							$arr_div[$id_div]=$id_div;
                            $id_div++;
                        }
                    ?>
                    <tr class="TituloTabla">
                        <td align="left" colspan="2" >TOTAL</td>
                        <td  width="10%"  align="right" >$<? echo number_format($total_valor_asignado_division, "2", ",", "." ) ; ?></td>
                        <td   width="10%" align="right">$<? echo number_format($total_valor_planeado_division, "2", ",", "." );  ?></td>
                        <td  align="right">$<? echo number_format($total_val_facturado, "2", ",", "." );  ?></td>
                    </tr>
                  </table>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr class="TituloUsuario">
				    <td height="3"> </td>
				    </tr>
			    </table
                ></td>
                <td >
                    <table width="100%" class="TxtTabla">

                        <tr>

                                <?php
											
                                                    $nom_divs=array_url($nom_div);
                                                    $val_planeados=array_url($val_planeado);
                                                    $val_facturados=array_url($val_facturado_div);
                                    ?>               <td  align="center" rowspan="<? echo $cant_filas; ?>">  

                                                        <table>
                                                            <tr>
                                                                <td class="TxtTabla">
<?
		//SI EL PROYECTO TIENE FACTURACION Y/O PLANEACION, SE MUESTRA LA GRAFICA
		if ( (trim($ban_planeacion)==1)||(trim( $ban_facturacion )==1))
		{
?>
                                                    <img src='grafico2.php?val_planeado=<?=$val_planeados; ?>&val_facturado=<?=$val_facturados; ?>&titulo=<?='Valor planeado Vs Facturado por Divisi&oacute;n ' ?>&nom_div=<?=$arr_div[$id_div]  //$nom_divs; ?>' border='0' width="765" height="329" />
<?
		}
		else
			echo "No se ha generado planeaci&oacute;n y/o facturaci&oacute;n en el proyecto.";
?>
                                                                </td>
                                                            </tr>

                                                        </table>
                    
                          </td>
                                        <?  
                    
                                        ?>
                        </tr>
					</table>
				</td>
              </tr>
            </table></td>
          </tr>

			  <tr>
			    
			  </tr>
            </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">

        </table>
        </td>
      </tr>
	<tr>
	<td>
		<table width="100%" border="0">
          <tr>


			<td colspan="2" align="left" class="TituloUsuario">.: Tiempos estimados en el proyecto</td>
		      </tr>
<?php
				$cant_meses_transcurridos=0;
				$cant_meses=0;
				//CANTIDAD DE MESES ESTIMADOS PARA LA DURACION EL PROYECTO
				$sql_fechas_actividades="select DATEDIFF(MONTH, (select fechaInicio from Proyectos where id_proyecto=".$cualProyecto."), (select MAX(fecha_fin) from  Actividades where id_proyecto=".$cualProyecto.")) cant_meses ,(select fechaInicio from Proyectos where id_proyecto=".$cualProyecto.") fecha_inicio, (select MAX(fecha_fin) from  Actividades where id_proyecto=".$cualProyecto.") fecha_finalizacion";
				$cur_fechas_actividades=mssql_query($sql_fechas_actividades);
				if($datos_fechas_actividades=mssql_fetch_array($cur_fechas_actividades))
				{
					$cant_meses=$datos_fechas_actividades["cant_meses"];
					$fecha_inicio=$datos_fechas_actividades["fecha_inicio"];
					$fecha_finalizacion=$datos_fechas_actividades["fecha_finalizacion"];
				}
				if(trim($cant_meses)=="")
					$cant_meses=0;
				//TIEMPO TRANSCURRIDO (FECHA INICIO PROYECTO, FECHA ACTUAL)
				$sql_fechas_actividades="select DATEDIFF(MONTH,(select fechaInicio from Proyectos where id_proyecto=".$cualProyecto."),getdate()) cant_meses_transcurridos, getdate() fecha_actual";
				$cur_fechas_actividades=mssql_query($sql_fechas_actividades);
				if($datos_fechas_actividades=mssql_fetch_array($cur_fechas_actividades))
				{
					$cant_meses_transcurridos=$datos_fechas_actividades["cant_meses_transcurridos"];
					$fecha_actual=$datos_fechas_actividades["fecha_actual"];

				}
?>
			  <tr valign="top" >
			    <td width="35%" valign="top" >
					<table width="100%" border="0" class="TituloUsuario">
					  <tr>
					    <td>&nbsp;</td>
				      </tr>
			    </table>
				  <table width="100%">
			        <tr >
			        <td class="TituloTabla2" width="35%" >Fecha de Inicio </td>
                            <td width="30%" align="right" class="TxtTabla"><? if(trim($fecha_inicio)!=""){ echo date("M d Y" , strtotime( $fecha_inicio )); }  ?></td>
                            <td class="TituloTabla2" width="35%" >&nbsp;  </td>
                    </tr>
                

                          <tr class="TxtTabla">
                            <td class="TituloTabla2" width="35%" >Fecha Finalizaci&oacute;n <img title="Fecha maxima de finalizaci&oacute;n estipulada, en las actividades del proyecto." src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif"></td>
                            <td width="30%" align="right" class="TxtTabla"><? if($fecha_finalizacion!="") { echo date('M d Y', strtotime( $fecha_finalizacion)); }  ?></td>
                            <td class="TituloTabla2" width="35%" >&nbsp;  </td>
                          </tr>
                          <tr class="TxtTabla">
                            <td class="TituloTabla2" width="35%" >Fecha Actual</td>
                            <td width="30%" align="right" class="TxtTabla"><?  echo date("M d Y" , strtotime( $fecha_actual));   ?></td>
                            <td class="TituloTabla2" width="35%" >&nbsp;  </td>

		          </table>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr class="TituloUsuario">
				    <td height="3"> </td>
			      </tr>
			    </table></td>
			    <td align="center" class="TxtTabla" ><?php

							//CONSULTA LA CANTIDAD DE MESES, EN LAS QUE SE HA GENERADO FACTURACION, EN EL PROYECTO, DURANTE LAS VIGENCIAS
							$cur_ejecutado=mssql_query("select COUNT(*) as T_ejecutado from (select  mes,vigencia from HojaDeTiempo.dbo.FacturacionProyectos  where id_proyecto=".$cualProyecto." group by mes,vigencia) aa");
							if($datos_ejecu=mssql_fetch_array($cur_ejecutado))
								$ejecutado=$datos_ejecu["T_ejecutado"];
							else
								$ejecutado=0;
					

                                                    $nom_divs=array_url($nom_div);
                                                    $val_planeados=array_url($val_planeado);
                                                    $val_facturados=array_url($val_facturado);

                                    ?>
								<img src='grafico3.php?duracion=<?=$cant_meses; ?>&m_transcurridos=<?=$cant_meses_transcurridos; ?>&ejecutado=<?=$ejecutado ?>&amp;titulo=<?='Valor planeado Vs Facturado por 
Divisi&oacute;n ' ?>' border='0' width="765" height="329" />



<?php
	//CODIGO DE DIAGRAMA DE GANT
    function crea_fecha($vigencia,$mes)
    {
        //CONSULTAMOS EL ULTIMO DIA DEL MES DE LA VIGENCIA FINAL
        $cur_dia_mes=mssql_query("select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$vigencia."' + '".$mes."' + '01')))) AS ultimo_dia");
//echo " eeee ".mssql_get_last_message()." <br> select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$vigencia."' + '".$mes."' + '01')))) AS ultimo_dia";
        if($datos_dia_mes=mssql_fetch_array($cur_dia_mes))
        {
            $ultimo_dia=$datos_dia_mes["ultimo_dia"];
        }
        return($ultimo_dia);
    
    }
    //$meses= array(1,2,3,4,5,6,7,8,9,10,11,12); //meses de un mes
    //$meses2= array(2,3,4,5,7,8,9,10,11);
    $f=0; $c=0; //define los valores iniciales de las filas y las columnas
    
    //$activid__inicio_fin= [][] array();
    //$vigencias= array(2013,2014);  ///select vigencias
    //foreach($vigencias as $val )  //while vigencias
    $cur_vigencia=mssql_query("select distinct vigencia from PlaneacionProyectos where id_proyecto=".$cualProyecto." order by vigencia");
    while($datos_vigencia=mssql_fetch_array($cur_vigencia))
    {
        //echo $val."<br>";   //select meses de la vigencia
        //foreach($meses2 as  $mex)    //while meses de la vigencia
        $cur_mes=mssql_query("select distinct mes from PlaneacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$datos_vigencia["vigencia"]." order by  mes");
        $can_reg=mssql_num_rows($cur_mes); //almacenamos la cantidad de registros extrahidos
//echo "select distinct mes from PlaneacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$datos_vigencia["vigencia"]." order by  mes <br>".$can_reg."<br>";
        $id_sql=0;
//		$mes_hsta="";
        while($datos_mes=mssql_fetch_array($cur_mes))
        {
            $id_sql++; //permite saber la psocicion del identificacor interno de la consulta
            //$ind=$mex-1;  //asigna el valor del mes 
            //echo $meses[$ind]."<br>";
            //$fecha=$mex.' - '.$vigencias;
                $activid__inicio_fin[$f][$c]= $datos_vigencia["vigencia"]."-".$datos_mes["mes"]."-1"; //almacena la primera fecha del mes            
    	        $mes_inicio=$datos_mes["mes"];  //alamcena el valor, para comparalo mas adelante, y encontrar el mes hasta donde va la planeacion


            //echo $activid__inicio_fin[$f][$c]." --<br>";

            while($datos_mes2=mssql_fetch_array($cur_mes)) //se ejecuta la consulta, para buscar hasta que mes exite planeación
            {
                $id_sql++; //permite saber la psocicion del identificacor interno de la consulta
                $mes_inicio++;
//echo $mes_inicio." */* <br>";
//echo "<br>  Ingresa *** ".$datos_mes2["mes"]." **  ".$mes_inicio."--- ".mssql_num_rows($cur_mes)." <br>";

                if($datos_mes2["mes"]!=$mes_inicio) //si el siguiente mes es diferente a la secuencia de meses, SE ALMACENA LA FECHA COMO FECHA FINAL
                {
					// EJ: MESES EN LA BD $datos_mes2["mes"] MESES (8,9,11)
					// CONTADOR DE LA PAGINA $mes_inicio MESES(8,9,10)
					// COMO EN EL EJENPO EL ULTIMO MES DE LA B.D. ES !=  AL DEL CONTADOR INTERNO ENTONCES:
					$mes_inicio=$datos_mes2["mes"]; //se almacena el valor del mes de la BD, para QUE QUEDE CON EL MISMO VALOR, DEL SIGUIENTE REGISTRO DE LA BD (11)
					mssql_data_seek($cur_mes,($id_sql-2)); //SE DEVUELVE EL CURSOR DE LA B.D. 2 VECES PARA POSICIONARLO EN EL PENULTIMO VALOR DE LOS MESES (8)
					$datos_mes2=mssql_fetch_array($cur_mes); //SE CONSULTA EL VALOR DEL MES ANTERIOR DE LA B.D (9) Y SE TOMA ESTE VALOR COMO EL DE FINALIZACIÓN
					$id_sql--; //SE RESTA EN 1, EL VALOR DEL CURSOR, UTILIZADO, PARA QUE QUEDE CON EL MISMO VALOR, QUE EL CURSOR DE LA B.D.

                    //echo "<br>  Ingresa 2222 *** ".$datos_mes2["mes"]." mes I ".$mes_inicio." --vigen ".$datos_vigencia["vigencia"]."<br>";

					if( ( (int) ($datos_mes2["mes"]) )<=9)
						$messs="0".($datos_mes2["mes"]);
                    $c++;
                    $ultimo_dia=crea_fecha($datos_vigencia["vigencia"],$messs);
                    $activid__inicio_fin[$f][$c]=$datos_vigencia["vigencia"]."-".$messs."-".$ultimo_dia; //se almacena el mes, hasta donde exista planeacion
//echo "Ingreso ".$datos_vigencia["vigencia"]."- mes: ".$datos_mes2["mes"]."- ultimo dia: ".$ultimo_dia." 1  <br> ";
                    break;    
                }
                //SI VALIDA SI ES EL ULTIMO REGISTRO DE LA CONSULTA, PARA PONER ESTE VALOR, COMO LA FECHA FINAL
                if($id_sql==$can_reg)
                {
//                    echo "<br>  Ingresa2 *** ".$datos_mes2["mes"]." mes I ".$mes_inicio." --vigen ".$datos_vigencia["vigencia"]."<br>";
                    $c++;

					if( ( (int) ($datos_mes2["mes"]) )<=9)
						$datos_mes2["mes"]="0".$datos_mes2["mes"];

//                    echo " ---- ".$datos_mes2["mes"]."<br>";
                    $ultimo_dia=crea_fecha($datos_vigencia["vigencia"],$datos_mes2["mes"]);
                   $activid__inicio_fin[$f][$c]=$datos_vigencia["vigencia"]."-".$datos_mes2["mes"]."-".$ultimo_dia; //se almacena el mes, hasta donde exista planeacion
//echo "cantidad registros: ".$id_sql." -- ".$can_reg."<br>";
//echo $datos_vigencia["vigencia"]."- mes: ".$datos_mes2["mes"]."- ultimo dia: ".$ultimo_dia." * ";
                }
                
            }            
//            echo "<br><br>".$activid__inicio_fin[$f][0]." ** ".$activid__inicio_fin[$f][1]."<br>";
            $f++; //aumenta la fila, para almacenar la siguiente fecha en la matriz
            $c=0;
           //
        }
    }

    	$tmp=serialize($activid__inicio_fin);  //Serializar el arreglo.
		$url=urlencode($tmp);  //Codificar URL. 
?>
<BR >
<BR >
<?
/*
for($m=0;$m<=10;$m++)
	echo $activid__inicio_fin[$m][0]." -*- ".$activid__inicio_fin[$m][1]."<br>";
*/

//$activid__inicio_fin[0][0]="2013-03-1";
// 2014-10-31 
//$activid__inicio_fin[0][0]="2013-03-01";
//echo " Info ".$f." -- ".$activid__inicio_fin[$f-1][1]." -- ".$activid__inicio_fin[0][0]." fecha fin ".$activid__inicio_fin[$f][0];
	//si se ha generado planeacio, se muestra la grafica
	if(($f!=0) and ($activid__inicio_fin[$f-1][1] !="") and ($activid__inicio_fin[0][0]!=""))
	{
		echo "<img src='gant_chars5.php?act_array=".$url."&F=".$f."&f_i=".$activid__inicio_fin[0][0]."&f_f=".$activid__inicio_fin[$f-1][1]."'  width='765' height='119'>";
?>

<?
	}
?>
	
								</td>
		      </tr>
			 
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>

	</td>
	</tr>
	<tr>
		<td>
        <table width="100%" border="0">

		 <tr>
				<td colspan="2" class="TituloUsuario">.:Otros Valores				</td>
		</tr>
		<tr>
			  <td colspan="2" class="TxtTabla">Sumatoria de facturaci&oacute;n y viaticos por Categoria   </td>            
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
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>	</td>
  </tr>
</table>

</body>
</html>
