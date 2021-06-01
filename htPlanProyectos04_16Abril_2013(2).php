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
	        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos01.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n</a>
		 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
          <tr>
            <td class="TxtTabla" height="2" colspan="3"></td>
          </tr>
		
		</table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td  class="TituloUsuario" colspan="3" >Totales del proyecto</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td>
			<table width="100%"  border="0" cellspacing="1" cellpadding="0">
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

			?>
          <tr class="TxtTabla">
            <td class="TituloTabla2" width="30%" >&nbsp;  </td>
            <td class="TituloTabla2" width="20%" >Valor del proyecto </td>
			<td> $<? echo number_format($val_proye, "2", ",", "." )   ?></td>
            <td class="TituloTabla2" width="30%" >&nbsp;  </td>
  		 </tr>

          <tr class="TxtTabla">
            <td class="TituloTabla2" width="30%" >&nbsp;  </td>
            <td class="TituloTabla2" width="20%" >Valor  facturado</td>
            <td> $<? echo number_format($val_proye, "2", ",", "." )   ?></td>
            <td class="TituloTabla2" width="30%" >&nbsp;  </td>
          </tr>
          <tr class="TxtTabla">
        	    <td width="15%" align="center" colspan="4"> 
<?
			//	include("grafico.php");
?>
				<img src='grafico.php?val_proy=<?=$val_proye ?>&val_fact=<?=$val_proye; ?>&titulo=<?='Valores del proyecto' ?>' border='0' width="695" height="289" />
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
            <td  class="TituloUsuario" colspan="2" >Totales por divisi&oacute;n (Planeado vs Facturado)</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td  >
			<table width="100%"  border="0" cellspacing="1" cellpadding="1">
          <tr class="TituloTabla2">
			<td width="70%">
				<table width="100%"  cellspacing="1" cellpadding="0" border="0" bgcolor="#EAEAEA" >
					<tr>
			            <td width="15%" class="TituloTabla2">Divisi&oacute;n</td>
            			<td width="15%" class="TituloTabla2">Valor Asignado </td>
			            <td width="15%" class="TituloTabla2">Valor Planeado </td>
            			<td width="15%" class="TituloTabla2">Valor Facturado </td>
					</tr>
				</table>
			</td>
			<td width="30%" colspan="4">
				<table  cellspacing="1" cellpadding="0" border="0"  >
					<tr>
					   <td width="15%">Valor planeado Vs Facturado por Divisi&oacute;n </td>
					</tr>
				</table>
			</td>

          </tr>
<?

	//CONSULTA EL VALOR ASIGNADO A LAS DIVISIONES EN EL PROYECTO
	$sql_div_asignado="select D.nombre, AsignaValorDivision.id_division, valorAsignado from AsignaValorDivision 
						inner join Divisiones D on D.id_division=AsignaValorDivision.id_division where id_proyecto=".$cualProyecto." order by (nombre) ";
	$cur_div_asignado=mssql_query($sql_div_asignado);
	$cant_filas=mssql_num_rows($cur_div_asignado);
	$z=1;
	$total_valor_planeado_division=0;
	$total_valor_asignado_division=0;

	$l=0;
	$nom_div= array();
?>
	<tr >
	<td  width="70%" class="TituloTabla">
		<table width="100%"  cellspacing="1" cellpadding="0" border="0" >

<?PHP
	while($datos_div_asignado=mssql_fetch_array($cur_div_asignado))
	{
		$total_valor_asignado_division+=$datos_div_asignado["valorAsignado"];
?>
				  <tr class="TxtTabla" >
					<td width="15%"><? echo strtoupper( $datos_div_asignado["nombre"]); ?></td>
					<td width="15%" align="right" ><? echo  number_format($datos_div_asignado["valorAsignado"], "2", ",", "." ); ?></td>
<?php
			$nom_div[$l]=$datos_div_asignado["nombre"];

			//CONSULTA QUE CONSOLIDA EL VALOR TOTAL ASIGNADO A CADA DIVISION, EN EL PROYECTO
			$sql_division_valor_total="
			select nombre, SUM(valor) as valor_division FROM  HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nombre in (
			 SELECT distinct(nombre) FROM  HojaDeTiempo.dbo.Actividades WHERE id_proyecto = ".$cualProyecto." and nivel=3 and id_division=".$datos_div_asignado["id_division"]."
			 ) and nivel=3 and id_division=".$datos_div_asignado["id_division"]." group by(nombre) ";
		//echo $sql_division_valor_total."<br>".mssql_get_last_message();
			$cur_division_valor_total=mssql_query($sql_division_valor_total);

			if($datos_division_valor_total=mssql_fetch_array($cur_division_valor_total))
			{
					?>
					<td width="15%" align="right">$<?php 
						if($datos_division_valor_total["valor_division"]!="")
						{ echo number_format($datos_division_valor_total["valor_division"], "2", ",", "." );

							$val_planeado[$l]+=$datos_division_valor_total["valor_division"]; //suma el valor planeado para las divisiones, para graficarlo
						} 
						else { echo "0";  $val_planeado[$l]=0; }  ?>
					</td>
					<?php
						$total_valor_planeado_division+=$datos_division_valor_total["valor_division"];



					?>
		<?
			}
			else //si  no se ha asignado recurso, en las actividades, de la división
				echo '<td width="15%" align="right">$0 </td>';

			?>
			<td width="15%" align="right">$0 <?php 	$val_facturado[$l]=0; ?> </td>		
<?php

			$z++;
			$l++;
?>
			  </tr>
<?php
		}
?>
			</table>
		</td>
		<td colspan="4" align="center">

			<table>
				<tr>

				<td width="15%" align="right" rowspan="<? echo $cant_filas; ?>" >  
<!--						<img src='grafico.jpg' border='0' /> 
-->
<?php
				$tmp=serialize($nom_div);  //Serializar el arreglo.
				$nom_div=urlencode($tmp);  //Codificar URL. 
?>
				<img src='grafico2.php?val_planeado=<?=$val_planeado ?>&val_facturado=<?=$val_facturado; ?>&titulo=<?='Totales divisi&oacute;n' ?>&nom_div=<?=$nom_div; ?>' border='0' width="695" height="289" />
						 </td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table width="100%">
			<tr class="TituloTabla">
				<td align="left" width="15%">TOTAL</td>
				<td  align="right" width="15%">$<? echo number_format($total_valor_asignado_division, "2", ",", "." ) ; ?></td>
				<td  align="right" width="15%">$<? echo number_format($total_valor_planeado_division, "2", ",", "." )  ?></td>
				<td  align="right" width="15%">$0</td>

			</tr>
			</table>
		</td>
		<td>&nbsp;
			
		</td>
	</tr>
	

        </table>			</td>

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
