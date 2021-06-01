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

	$sql2="Select A.* , U.nombre nomUsu, U.apellidos apeUsu ";
	$sql2=$sql2." from Actividades A, Usuarios U" ;
	$sql2=$sql2." where A.id_encargado *= U.unidad " ;
	$sql2=$sql2." and A.id_proyecto = " . $cualProyecto ;
	if($plstActividad<>"")
	{
	$sql2=$sql2." and A.id_actividad=".$plstActividad;
	}
	//para que en Porce muestre ordenado por ID
	if ($cualProyecto == 697) {
		$sql2=$sql2." order by A.id_actividad " ;
	}
	
		//$sql2=$sql2." order by A.nivelesActiv " ;
		
		//11Sep2008
		//Para ordenar las actividades por nombre de la actividad y por macroactividad sin perder la 
		//jerarquia de actividades y subactividades
		
			$sql2=$sql2." order by A.actPrincipal , nivelesActiv " ;
	
	
//Sino, se trata de responsable de actividad o programadores de actividad y ven sus actividades

//echo $sql2 ;
$cursor2 = mssql_query($sql2);



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
//20Nov2013
//PBM
//Si se trata del proyecto Inscripciones, Promociones y Pequeñas propuestas = 48 muestra la gente programada desde 01-Nov-2013
if(trim($cualProyecto)=="48") {
	$sql3u=$sql3u." and A.fecha_inicial > '2011-11-01' " ;
}

//Cierra 20Nov2013

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

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 521px; height: 30px;">
Reporte Programaci&oacute;n de proyectos - Actividades </div>
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
        <td class="TituloUsuario">Asignaciones para las actividades
		</td>
      </tr>
    </table>
	<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
		<form name="form1" method="post" action="">
	  <tr>
		<td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
		<td width="30%" class="TxtTabla">
		<? 
		//Seleccionar el mes cuando se carga la página por primera vez
		//si no cuando se recarga la página
		$selMesTodos= "";
		if ($pMes == "") {
			$mesActual=date("m"); //el mes actual
		}
		else {
			if ($pMes == "TODOS") {
				$selMesTodos= "selected"; //el mes seleccionado
			}
			else {
				$mesActual= $pMes; //el mes seleccionado
			}
		}
	
		$selMes1 = "";
		$selMes2 = "";
		$selMes3 = "";
		$selMes4 = "";
		$selMes5 = "";
		$selMes6 = "";
		$selMes7 = "";
		$selMes8 = "";
		$selMes9 = "";
		$selMes10 = "";
		$selMes11 = "";
		$selMes12 = "";
		for($m=1; $m<=12; $m++) {
			if (($m == $mesActual) AND ($m == 1)) {
				$selMes1 = "selected";
			}
			if (($m == $mesActual) AND ($m == 2)) {
				$selMes2 = "selected";
			}
			if (($m == $mesActual) AND ($m == 3)) {
				$selMes3 = "selected";
			}
			if (($m == $mesActual) AND ($m == 4)) {
				$selMes4 = "selected";
			}
			if (($m == $mesActual) AND ($m == 5)) {
				$selMes5 = "selected";
			}
			if (($m == $mesActual) AND ($m == 6)) {
				$selMes6 = "selected";
			}
			if (($m == $mesActual) AND ($m == 7)) {
				$selMes7 = "selected";
			}
			if (($m == $mesActual) AND ($m == 8)) {
				$selMes8 = "selected";
			}
			if (($m == $mesActual) AND ($m == 9)) {
				$selMes9 = "selected";
			}
			if (($m == $mesActual) AND ($m == 10)) {
				$selMes10 = "selected";
			}
			if (($m == $mesActual) AND ($m == 11)) {
				$selMes11 = "selected";
			}
			if (($m == $mesActual) AND ($m == 12)) {
				$selMes12 = "selected";
			}
	
	
	
		}
		
		?>
		<select name="pMes" class="CajaTexto" id="pMes">
		  <option value="1" <? echo $selMes1; ?> >Enero</option>
		  <option value="2" <? echo $selMes2; ?>>Febrero</option>
		  <option value="3" <? echo $selMes3; ?>>Marzo</option>
		  <option value="4" <? echo $selMes4; ?>>Abril</option>
		  <option value="5" <? echo $selMes5; ?>>Mayo</option>
		  <option value="6" <? echo $selMes6; ?>>Junio</option>
		  <option value="7" <? echo $selMes7; ?>>Julio</option>
		  <option value="8" <? echo $selMes8; ?>>Agosto</option>
		  <option value="9" <? echo $selMes9; ?>>Septiembre</option>
		  <option value="10" <? echo $selMes10; ?>>Octubre</option>
		  <option value="11" <? echo $selMes11; ?>>Noviembre</option>
		  <option value="12" <? echo $selMes12; ?>>Diciembre</option>
		  <option value="TODOS" <? echo $selMesTodos; ?>>::: Todos :::</option>
		</select></td>
		<td width="15%" align="right" class="TituloTabla">A&ntilde;o:&nbsp;</td>
		<td class="TxtTabla">
		&nbsp;
		<select name="pAno" class="CajaTexto" id="pAno">
		 <option value="TODOS" >Todos los años</option>
		<? 
		//Generar los años de 2006 a 2050
		for($i=2006; $i<=2050; $i++) { 
			
			//seleccionar el año cuando se carga la página por primera vez
			if ($pAno == "") {
				$AnoActual=date("Y"); //el año actual
			}
			else {
				$AnoActual= $pAno; //el año seleccionado
			}
			
			if ($i == $AnoActual) {
				$selAno = "selected";
			}
			else {
				$selAno = "";
			}
		?>
		  <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
		 <? 
			
		 } //for 
		 
		 ?>
		</select>	</td>
		<td width="10%" class="TxtTabla">&nbsp;</td>
	  </tr>
	   <?  
	    $sqlActivi="  Select A.* , U.nombre nomUsu, U.apellidos apeUsu 
  from Actividades A, Usuarios U 
  where A.id_encargado *= U.unidad and A.id_proyecto =".$cualProyecto."

  order by A.actPrincipal , nivelesActiv
		";
$cursorActi = mssql_query($sqlActivi);
	
	   ?>
	  <tr>
		<td align="right" class="TituloTabla">Actividades</td>
		<td colspan="3" class="TxtTabla"><select name="plstActividad" class="CajaTexto" id="plstActividad">
				<option value="" selected >Todas las actividades </option>
		  <? 
		  while ($RegAct=mssql_fetch_array($cursorActi)) { 
		  if ($RegAct[id_actividad] == $plstActividad) {
				$selActividad= "selected";
		  }
		  else {
				$selActividad = "";
		  }
		  
		  
		  ?>
			<option value="<? echo $RegAct[id_actividad]; ?>" <? echo $selActividad ; ?> ><? echo $RegAct[nombre]; ?></option>
		  <? }?>
		  </select></td>
		<td class="TxtTabla">&nbsp;</td>
	  </tr>
	  <tr>
		<td align="right" class="TituloTabla">Division </td>
		<td colspan="3" class="TxtTabla"><select name="pfDivision" class="CajaTexto" id="pfDivision" onchange="document.form1.submit();" >
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
          <option value="888" <? echo $selDiv; ?> ><? echo ":::Sin Divisi&oacute;n:::" ; ?></option>
        </select></td>
		<td class="TxtTabla">&nbsp;</td>
	  </tr>
	  <tr>
	    <td align="right" class="TituloTabla">
		Departamento
		</td>
		<td colspan="3" class="TxtTabla"><?
	//Trae los departamentos asociados la divisi&oacute;n seleccionada
	$dTSql="Select * from departamentos where id_division = " . $pfDivision ;
	$dTcursor = mssql_query($dTSql);
	
	?>
          <select name="select2" class="CajaTexto" id="select2" onchange="document.form1.submit();" >
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
		  <td class="TxtTabla">&nbsp;</td>
		  </tr>
		  <tr>
		  
	<td width="20%" align="right" class="TituloTabla">Filtro Categor&iacute;a </td>
        <td class="TxtTabla" colspan="3">
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
		<td class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar" /></td>  
	  </tr>
		</form>
	</table>
	</td>
  </tr>
  </table>	
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
            
          </tr>
		<? } ?>
        </table>
	    </td>
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
          <tr  class="TxtTabla">
            <td>
			</td>
            <td width="28%" align="right"><strong>Asg</strong>: Valor Asignado a la actividad </td>
            <td width="28%" align="right"><strong>Prg</strong>: Valor programado en la actividad </td>
            <td width="28%" align="right"><strong>Cdt</strong>: Valor de los costos directos de la actividad </td>
          </tr>
        </table>
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="20%" class="TxtTabla"></td>
            <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="20%" class="TituloTabla">&nbsp;</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table></td>
            <td width="20%" class="TxtTabla" >&nbsp;</td>
          </tr>
        </table>		
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="5" class="TituloUsuario"></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="3%">ID</td>
            <td>Nombre</td>
            <td>MacroActividad</td>
            <td>Responsable</td>
			<td width="50%">Detalle</td>
            <td width="10%">Valor Recurso </td>
          </tr>
          <? while ($reg2=mssql_fetch_array($cursor2)) { ?>
          <tr class="TxtTabla">
            <td width="3%"><? echo  $reg2[id_actividad] ; ?></td>
            <td><? 
			$cualNivel = 0;
			$numEspacios="";
			$cualNivel = substr_count($reg2[nivelesActiv], '-'); 

			for ($i=1; $i<=$cualNivel; $i++) {
				if ($cualNivel > 1) {
					$numEspacios=$numEspacios."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}
			?>              <? echo $numEspacios . ucwords(strtolower($reg2[nombre])) ; ?></td>
            <td><? echo  ucwords(strtolower($reg2[macroactividad])) ; ?></td>
            <td align="right"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left"><? echo  ucwords(strtolower($reg2[nomUsu])) . " " . ucwords(strtolower($reg2[apeUsu])) ; 
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
                  <td width="1%"></td>
                </tr>
                <? } ?>
            </table></td>
            
			  <?
			 
			   $sql3u="select distinct A.unidad, U.nombre, U.apellidos, C.nombre nomCat   ";
$sql3u=$sql3u." from asignaciones A, Usuarios U, Horarios H, Categorias C , Departamentos D ";
$sql3u=$sql3u." where A.unidad = U.unidad  ";
$sql3u=$sql3u." and A.IDhorario = H.IDhorario  ";
$sql3u=$sql3u." And U.id_categoria = C.id_categoria ";
$sql3u=$sql3u." And U.id_departamento = D.id_departamento ";
$sql3u=$sql3u." and A.id_proyecto = " . $cualProyecto ;
$sql3u=$sql3u." and A.id_actividad = " . $reg2[id_actividad] ;
//20Nov2013
//PBM
//Si se trata del proyecto Inscripciones, Promociones y Pequeñas propuestas = 48 muestra la gente programada desde 01-Nov-2013
if(trim($cualProyecto)=="48") {
	$sql3u=$sql3u." and A.fecha_inicial > '2011-11-01' " ;
}

//Cierra 20Nov2013

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
$cursor3u = mssql_query($sql3u);?>
			   <td width="15%" align="right" valign="top">


				  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td bgcolor="#FFFFFF">
					       <table width="100%"  border="0" cellspacing="1" cellpadding="0">
							  <tr class="TxtTabla">
								  <td><b> Unidad</b> </td>
								  <td><b> Categoria</b> </td>
								  <td><b> Nombre</b></td>
								  <td width="70%">
								   <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
									  <tr class="TituloTabla2">
										<td width="11%">Clase Tiempo </td>
										<td width="12%">Localizaci&oacute;n</td>
										<td width="11%">Cargo</td>
										<td width="11%">Horas Programadas </td>
										<td width="11%">Horas reportadas </td>
										<td>Horario</td>
										<td width="12%">Valor Recurso </td>
									  </tr>
									</table>								</td>
							  </tr>
							<? while ($reg3u=mssql_fetch_array($cursor3u)) { ?>
								<tr class="TxtTabla">
									<td width="8%"><? echo  $reg3u[unidad] ; ?></td>
									<td width="5%" align="center"><? echo  $reg3u[nomCat] ; ?></td>
									<td><? echo  ucwords(strtolower($reg3u[apellidos])) . ", " . ucwords(strtolower($reg3u[nombre]))  ; ?></td>
									<td width="70%"><?
	//Trae los recursos asignados para la actividad
//	$sql3a="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, H.NomHorario ";
	$sql3a="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo ";
	$sql3a=$sql3a." from asignaciones A, horarios H ";
	$sql3a=$sql3a." where A.IDhorario = H.IDhorario ";
	$sql3a=$sql3a." and A.id_proyecto = " . $cualProyecto ;
	$sql3a=$sql3a." and A.id_actividad = " . $reg2[id_actividad] ;
	$sql3a=$sql3a." and A.unidad = " . $reg3u[unidad] ;
	if($pAno<>"TODOS"){
	$sql3a=$sql3a." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
	}
	if($pMes<>"TODOS"){
	$sql3a=$sql3a." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	}
//	$sql3a=$sql3a." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, H.NomHorario ";
	$sql3a=$sql3a." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo ";
	//echo $sql3a;
	$cursor3a = mssql_query($sql3a);
	?>
									  <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
	<? while ($reg3a=mssql_fetch_array($cursor3a)) { ?>
      <tr>
        <td width="11%"><? echo $reg3a[clase_tiempo]; ?></td>
        <td width="12%"><? echo $reg3a[localizacion]; ?></td>
        <td width="11%"><? echo $reg3a[cargo]; ?></td>
        <td width="11%">
		<?
		//23Ene2008
		//Traer el total de horas programadas para el usuario y actividad
		$sql4="select coalesce(sum(tiempo_asignado),0) horasProg from asignaciones  ";
		$sql4=$sql4." where id_proyecto = " . $cualProyecto ;
		$sql4=$sql4." and id_actividad =" . $reg2[id_actividad] ;
		$sql4=$sql4." and unidad =" . $reg3a[unidad] ;
		$sql4=$sql4." and clase_tiempo =" . $reg3a[clase_tiempo] ;
		$sql4=$sql4." and localizacion =" . $reg3a[localizacion] ;
		$sql4=$sql4." and cargo =" . $reg3a[cargo] ;
		#$sql4=$sql4." and month(fecha_inicial)=month(fecha_final) ";
		#$sql4=$sql4." and year(fecha_inicial)=year(fecha_final) ";
		if($pAno<>"TODOS"){
		$sql4=$sql4." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
		}
		if($pMes<>"TODOS"){
		$sql4=$sql4." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	     }
		$cursor4 = mssql_query($sql4);
		if ($reg4=mssql_fetch_array($cursor4)) {
			echo  $reg4[horasProg];
		}
		?>		</td>
        <td width="11%">
		<?
		//23Ene2008
		//Traer el total de horas reportadas para el usuario y la actividad
		$sql5="select coalesce(sum(horas_registradas),0) horasEje from horas  ";
		$sql5=$sql5." where id_proyecto = " . $cualProyecto ;
		$sql5=$sql5." and id_actividad = " . $reg2[id_actividad] ;
		$sql5=$sql5." and unidad = " . $reg3a[unidad] ;
		$sql5=$sql5." and clase_tiempo = " . $reg3a[clase_tiempo] ;
		$sql5=$sql5." and localizacion = " . $reg3a[localizacion] ;
		$sql5=$sql5." and cargo =" . $codProyecto . $reg3a[cargo] ;
		$sql5=$sql5." and fecha > CONVERT(DATETIME, '2007-09-30 00:00:00', 102) " ;
		if($pAno<>"TODOS"){
		$sql5=$sql5." and DATEPART(YEAR,fecha)=" . $pAno;
		}
		if($pMes<>"TODOS"){
		$sql5=$sql5." and DATEPART(MONTH,fecha)=" . $pMes;
	     }
		$cursor5 = mssql_query($sql5);
		if ($reg5=mssql_fetch_array($cursor5)) {
			echo  $reg5[horasEje];
		}

		?>		</td>
        <td >
		<? //echo $reg3a[NomHorario]; ?>
		<?

		//5Mar2009
		//Trae el nombre de los horarios implicados en asignaciones para el 
		//proyecto, actividad, unidad, clase de tiempo, localización y cargo correspondientes
		$sql3h="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, H.NomHorario ";
		$sql3h=$sql3h." from asignaciones A, horarios H ";
		$sql3h=$sql3h." where A.IDhorario = H.IDhorario ";
		$sql3h=$sql3h." and A.id_proyecto = " . $cualProyecto ;
		$sql3h=$sql3h." and A.id_actividad = " . $reg2[id_actividad] ;
		$sql3h=$sql3h." and A.unidad = " . $reg3u[unidad] ;
		$sql3h=$sql3h." and A.clase_tiempo = '" .  $reg3a[clase_tiempo] . "' ";
		$sql3h=$sql3h." and A.localizacion = '" .  $reg3a[localizacion] . "' ";
		$sql3h=$sql3h." and A.cargo = '" .  $reg3a[cargo] . "' ";
		if($pAno<>"TODOS"){
		$sql3h=$sql3h." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
		}
		if($pMes<>"TODOS"){
		$sql3h=$sql3h." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	     }
		$sql3h=$sql3h." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, H.NomHorario ";
		$cursor3h = mssql_query($sql3h);
		
		while ($reg3h=mssql_fetch_array($cursor3h)) {
			echo $reg3h[NomHorario] . "<br>" ;
		}
		
		?>
		
          &nbsp;</td>
        <td width="12%" align="right"><?
		$rTPSql="select coalesce(sum(valorProgramado), 0) valorProgramado  ";
		$rTPSql=$rTPSql." from asignaciones ";
		$rTPSql=$rTPSql." where id_proyecto = " . $reg3a[id_proyecto];
		$rTPSql=$rTPSql." and id_actividad =" . $reg3a[id_actividad];
		$rTPSql=$rTPSql." and unidad =" . $reg3a[unidad];
		$rTPSql=$rTPSql." and clase_tiempo = " . $reg3a[clase_tiempo];
		$rTPSql=$rTPSql." and localizacion =" . $reg3a[localizacion];
		$rTPSql=$rTPSql." and cargo = '".$reg3a[cargo]."'";
		if($pAno<>"TODOS"){
		$rTPSql=$rTPSql." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
		}
		if($pMes<>"TODOS"){
		$rTPSql=$rTPSql." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	     }
		 
		$rTPcursor = mssql_query($rTPSql);
		if ($rTPreg=mssql_fetch_array($rTPcursor)) {
			echo "$ " . number_format($rTPreg[valorProgramado], 0, ',','.') ;
		}
		
		?></td>
      </tr>
	  <? } ?>
    </table>	</td>
								</tr>
							<? }?>	
						   </table>				  </td>
				 </tr>
				</table>		  </td>
		  <td width="15%" align="right" valign="top"><?
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
			 if($pAno<>"TODOS"){
			$vrSql=$vrSql." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
			}
			if($pMes<>"TODOS"){
			$vrSql=$vrSql." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	     }
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
			  if($pAno<>"TODOS"){
			$vrSql=$vrSql." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
			}
			if($pMes<>"TODOS"){
			$vrSql=$vrSql." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	     }
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
			 $vrSql;
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
			 if($pAno<>"TODOS"){
			$vrSql=$vrSql." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
			}
			if($pMes<>"TODOS"){
			$vrSql=$vrSql." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
	     }		
			$vrCursor = mssql_query($vrSql);
			if ($vrReg=mssql_fetch_array($vrCursor)) {
				$miTotPrgSubAct = $vrReg[totAsignaSA] ;
			}


			?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
                        <tr class="TxtTabla">
                          <td width="1%">Asg</td>
                          <td align="right"><? echo "$ " . number_format($miValorActiv, 0, ',','.')?></td>
                        </tr>
                        <tr class="TxtTabla">
                          <td>AsgS</td>
                          <td align="right"><? echo "$ " . number_format($miTotalSubAct, 0, ',','.')?> </td>
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
                          <td align="right"><? echo "$ " . number_format($miTotalCostosD, 0, ',','.')?> </td>
                        </tr>
                        <tr class="TxtTabla">
                          <td width="1%">&nbsp;</td>
                          <td align="right"><? echo "$ " . number_format($valorRemanente, 0, ',','.')?></td>
                        </tr>
                    </table>					</td>
                  </tr>
              </table>		    </td>
</tr>
          <? } ?>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        

	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="5" class="TituloUsuario"></td>
      </tr>
    </table>


		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		
<?
	$sql2R="Select A.* , U.nombre nomUsu, U.apellidos apeUsu ";
	$sql2R=$sql2R." from Actividades A, Usuarios U" ;
	$sql2R=$sql2R." where A.id_encargado *= U.unidad " ;
	$sql2R=$sql2R." and A.id_proyecto = " . $cualProyecto ;
	//para que en Porce muestre ordenado por ID
	if ($cualProyecto == 697) {
		$sql2R=$sql2R." order by A.id_actividad " ;
	}
	$sql2R=$sql2R." order by A.actPrincipal , nivelesActiv " ;
	$cursor2R = mssql_query($sql2R);
	
	$excel="";
	
	$excel=" ID \t";
	$excel=$excel." Nombre Actividad \t";
	$excel=$excel." MacroActividad \t";
	$excel=$excel." Unidad \t";
	$excel=$excel." Catregoria \t";
	$excel=$excel." Nombre \t";
	$excel=$excel." Clase Tiempo \t";
	$excel=$excel." Localizacion \t";
	$excel=$excel." Cargo \t";
	$excel=$excel." Horas programadas \t";
	$excel=$excel." Horas reportadas \t";
	$excel=$excel." Horario \t";
	$excel=$excel." Valor Recurso\t";
	$excel=$excel." Asg \t";
	$excel=$excel." AsgS \t";
	$excel=$excel." Prg \t";
    $excel=$excel." PrgS \t";
	$excel=$excel." Cdt \n";
	
	
	while ($reg2R=mssql_fetch_array($cursor2R))
	  {
			 $id_act=$reg2R[id_actividad]  ;
			 $nombreAc=ucwords(strtolower($reg2R[nombre])) ;
			 $macro=ucwords(strtolower($reg2R[macroactividad])) ; 
			 
			 $rSqlR="SELECT R.*, U.nombre, U.apellidos ";
			 $rSqlR=$rSqlR." FROM ResponsablesActividad R, Usuarios U ";
			 $rSqlR=$rSqlR." WHERE R.unidad = U.unidad ";
			 $rSqlR=$rSqlR." AND R.id_proyecto = " . $reg2R[id_proyecto] ;
			 $rSqlR=$rSqlR." AND R.id_actividad = ". $reg2R[id_actividad] ;
			 $rCursorR = mssql_query($rSqlR);
			 
			while ($rRegR=mssql_fetch_array($rCursorR))
			  {
			 
			   $responsa=ucwords(strtolower($rRegR[nombre])) . " " . ucwords(strtolower($rRegR[apellidos])) ;
			 }
			  
			$sql3uR="select distinct A.unidad, U.nombre, U.apellidos, C.nombre nomCat   ";
			$sql3uR=$sql3uR." from asignaciones A, Usuarios U, Horarios H, Categorias C , Departamentos D ";
			$sql3uR=$sql3uR." where A.unidad = U.unidad  ";
			$sql3uR=$sql3uR." and A.IDhorario = H.IDhorario  ";
			$sql3uR=$sql3uR." And U.id_categoria = C.id_categoria ";
			$sql3uR=$sql3uR." And U.id_departamento = D.id_departamento ";
			$sql3uR=$sql3uR." and A.id_proyecto = " . $cualProyecto ;
			$sql3uR=$sql3uR." and A.id_actividad = " . $reg2R[id_actividad] ;
			if(trim($cualProyecto)=="48") {$sql3uR=$sql3uR." and A.fecha_inicial > '2011-11-01' " ;}
		    if ($pOrdena == 1) {$sql3uR=$sql3uR." ORDER BY U.apellidos " ;}
		    if ($pOrdena == 2) {$sql3uR=$sql3uR." ORDER BY C.nombre  " ;}
 			$cursor3uR = mssql_query($sql3uR);  
			
			 while ($reg3uR=mssql_fetch_array($cursor3uR)) 
			 {
			 
			      $uni=$reg3uR[unidad] ;
				  $nomCat=$reg3uR[nomCat] ;
				  $nomUsu=ucwords(strtolower($reg3uR[apellidos])) . ", " . ucwords(strtolower($reg3uR[nombre]))  ;
			        $sql3aR="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo ";
					$sql3aR=$sql3aR." from asignaciones A, horarios H ";
					$sql3aR=$sql3aR." where A.IDhorario = H.IDhorario ";
					$sql3aR=$sql3aR." and A.id_proyecto = " . $cualProyecto ;
					$sql3aR=$sql3aR." and A.id_actividad = " . $reg2R[id_actividad] ;
					$sql3aR=$sql3aR." and A.unidad = " . $reg3uR[unidad] ;
					if($pAno<>"TODOS"){$sql3aR=$sql3aR." and DATEPART(YEAR,fecha_inicial)=" . $pAno;}
					if($pMes<>"TODOS"){$sql3aR=$sql3aR." and DATEPART(MONTH,fecha_inicial)=" . $pMes;}
					$sql3aR=$sql3aR." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo ";
					$cursor3aR = mssql_query($sql3aR);
					while ($reg3aR=mssql_fetch_array($cursor3aR)) 
						 {  
						    $claseTiem=$reg3aR[clase_tiempo];
							$localiza=$reg3aR[localizacion];
							$carg=$reg3aR[cargo];
							$sql4R="select coalesce(sum(tiempo_asignado),0) horasProg from asignaciones  ";
							$sql4R=$sql4R." where id_proyecto = " . $cualProyecto ;
							$sql4R=$sql4R." and id_actividad =" . $reg2R[id_actividad] ;
							$sql4R=$sql4R." and unidad =" . $reg3aR[unidad] ;
							$sql4R=$sql4R." and clase_tiempo =" . $reg3aR[clase_tiempo] ;
							$sql4R=$sql4R." and localizacion =" . $reg3aR[localizacion] ;
							$sql4R=$sql4R." and cargo =" . $reg3aR[cargo] ;
							if($pAno<>"TODOS"){$sql4R=$sql4R." and DATEPART(YEAR,fecha_inicial)=" . $pAno;}
							if($pMes<>"TODOS"){$sql4R=$sql4R." and DATEPART(MONTH,fecha_inicial)=" . $pMes;}
							$cursor4R = mssql_query($sql4R);
							if ($reg4R=mssql_fetch_array($cursor4R)) 
							    {
								    $HorasProg=$reg4R[horasProg];
							    } 
							$sql5R="select coalesce(sum(horas_registradas),0) horasEje from horas  ";
							$sql5R=$sql5R." where id_proyecto = " . $cualProyecto ;
							$sql5R=$sql5R." and id_actividad = " . $reg2R[id_actividad] ;
							$sql5R=$sql5R." and unidad = " . $reg3aR[unidad] ;
							$sql5R=$sql5R." and clase_tiempo = " . $reg3aR[clase_tiempo] ;
							$sql5R=$sql5R." and localizacion = " . $reg3aR[localizacion] ;
							$sql5R=$sql5R." and cargo =" . $codProyecto . $reg3aR[cargo] ;
							$sql5R=$sql5R." and fecha > CONVERT(DATETIME, '2007-09-30 00:00:00', 102) " ;
							if($pAno<>"TODOS"){$sql5R=$sql5R." and DATEPART(YEAR,fecha)=" . $pAno;}
							if($pMes<>"TODOS"){$sql5R=$sql5R." and DATEPART(MONTH,fecha)=" . $pMes;}
							$cursor5R = mssql_query($sql5R);
							if ($reg5R=mssql_fetch_array($cursor5R)) {$HorasRep=$reg5R[horasEje];}	
							
							$sql3hR="select A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, H.NomHorario ";
							$sql3hR=$sql3hR." from asignaciones A, horarios H ";
							$sql3hR=$sql3hR." where A.IDhorario = H.IDhorario ";
							$sql3hR=$sql3hR." and A.id_proyecto = " . $cualProyecto ;
							$sql3hR=$sql3hR." and A.id_actividad = " . $reg2R[id_actividad] ;
							$sql3hR=$sql3hR." and A.unidad = " . $reg3uR[unidad] ;
							$sql3hR=$sql3hR." and A.clase_tiempo = '" .  $reg3aR[clase_tiempo] . "' ";
							$sql3hR=$sql3hR." and A.localizacion = '" .  $reg3aR[localizacion] . "' ";
							$sql3hR=$sql3hR." and A.cargo = '" .  $reg3aR[cargo] . "' ";
							if($pAno<>"TODOS"){$sql3hR=$sql3hR." and DATEPART(YEAR,fecha_inicial)=" . $pAno;}
							if($pMes<>"TODOS"){$sql3hR=$sql3hR." and DATEPART(MONTH,fecha_inicial)=" . $pMes;}
							$sql3hR=$sql3hR." group by A.id_proyecto, A.id_actividad, A.unidad, A.clase_tiempo, A.localizacion, A.cargo, H.NomHorario ";
							$cursor3hR = mssql_query($sql3hR);
							while ($reg3hR=mssql_fetch_array($cursor3hR)) { $Horario=$reg3hR[NomHorario];}
							
				
									$rTPSqlR="select coalesce(sum(valorProgramado), 0) valorProgramado  ";
									$rTPSqlR=$rTPSqlR." from asignaciones ";
									$rTPSqlR=$rTPSqlR." where id_proyecto = " . $reg3aR[id_proyecto];
									$rTPSqlR=$rTPSqlR." and id_actividad =" . $reg3aR[id_actividad];
									$rTPSqlR=$rTPSqlR." and unidad =" . $reg3aR[unidad];
									$rTPSqlR=$rTPSqlR." and clase_tiempo = " . $reg3aR[clase_tiempo];
									$rTPSqlR=$rTPSqlR." and localizacion =" . $reg3aR[localizacion];
									$rTPSqlR=$rTPSqlR." and cargo = '".$reg3aR[cargo]."'";
									if($pAno<>"TODOS"){$rTPSqlR=$rTPSqlR." and DATEPART(YEAR,fecha_inicial)=" . $pAno;}
									if($pMes<>"TODOS"){$rTPSqlR=$rTPSqlR." and DATEPART(MONTH,fecha_inicial)=" . $pMes;}
									$rTPcursorR = mssql_query($rTPSqlR);
									if ($rTPregR=mssql_fetch_array($rTPcursorR)) { $ValorProg="$ " . number_format($rTPregR[valorProgramado], 0, ',','.') ;}
									
									
							$miValorActiv= 0;
							$vrSqlR="SELECT COALESCE(MAX(valorActiv), 0) valorActiv FROM ActividadesRecursos ";
							$vrSqlR=$vrSqlR." where secuencia = (SELECT COALESCE(MAX(secuencia), 0) hayRecurso ";
							$vrSqlR=$vrSqlR." 		FROM ActividadesRecursos ";
							$vrSqlR=$vrSqlR." 		WHERE id_proyecto = " . $cualProyecto ;
							$vrSqlR=$vrSqlR." 		AND id_actividad = " . $reg2R[id_actividad]  . ") ";
							$vrSqlR=$vrSqlR." AND id_proyecto = " . $cualProyecto ;
							$vrSqlR=$vrSqlR." AND id_actividad = " . $reg2R[id_actividad] ;
							if($pAno<>"TODOS"){$vrSqlR=$vrSqlR." and DATEPART(YEAR,fecha_inicial)=" . $pAno;}
							if($pMes<>"TODOS"){$vrSqlR=$vrSqlR." and DATEPART(MONTH,fecha_inicial)=" . $pMes;}
							$vrCursorR = mssql_query($vrSqlR);
							if ($vrRegR=mssql_fetch_array($vrCursorR)) {
								$miValorActiv = $vrRegR[valorActiv] ;}
				
							$miTotalAsigna=0;
							$vrSqlR="select coalesce(sum(valorProgramado), 0) totProgramado ";
							$vrSqlR=$vrSqlR." from HojaDeTiempo.dbo.asignaciones  ";
							$vrSqlR=$vrSqlR." where id_proyecto =" . $cualProyecto ;
							$vrSqlR=$vrSqlR." and id_actividad =" . $reg2R[id_actividad] ;
							if($pAno<>"TODOS"){$vrSqlR=$vrSqlR." and DATEPART(YEAR,fecha_inicial)=" . $pAno;}
							if($pMes<>"TODOS"){
							$vrSqlR=$vrSqlR." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
						    }
							$vrCursorR = mssql_query($vrSqlR);
							if ($vrRegR=mssql_fetch_array($vrCursorR)) {
								$miTotalAsigna =  "$ " . number_format($vrRegR[totProgramado], 0, ',','.');
							}
				
							$miTotalCostosD=0;
							$vrSqlR="SELECT COALESCE(SUM(valorItem), 0) valorItem ";
							$vrSqlR=$vrSqlR." FROM ActividadesCostosD ";
							$vrSqlR=$vrSqlR." WHERE id_proyecto = " . $cualProyecto ;
							$vrSqlR=$vrSqlR." AND id_actividad =" . $reg2R[id_actividad] ;
							 $vrSqlR;
							$vrCursorR = mssql_query($vrSqlR);
							if ($vrRegR=mssql_fetch_array($vrCursorR)) {
								$miTotalCostosD = $vrRegR[valorItem] ;
							}
										
							//ValorRemanente
							$valorRemanente=$miValorActiv-$miTotalAsigna-$miTotalCostosD ;
							
							//25Jul2008
							//Trae la sumatoria del total asignado a las subactividades
							$miTotalSubAct=0;
							$vrSqlR="SELECT COALESCE(SUM(B.valorActiv), 0) totSA ";
							$vrSqlR=$vrSqlR." FROM (select id_actividad, max(secuencia) secuencia  from ActividadesRecursos ";
							$vrSqlR=$vrSqlR." 		where id_proyecto = " . $cualProyecto ;
							$vrSqlR=$vrSqlR." 		and id_actividad in (SELECT id_actividad FROM Actividades ";
							$vrSqlR=$vrSqlR." 				     WHERE id_proyecto = " . $cualProyecto ;
							$vrSqlR=$vrSqlR." 				     and tipoActividad > 1 ";
							$vrSqlR=$vrSqlR." 				     and actPrincipal = ".$reg2R[actPrincipal]."  ";
							if ($reg2R[tipoActividad] > 1) {
								$vrSqlR=$vrSqlR." 				     and nivelesActiv like '%".$reg2R[nivelesActiv]."%'" ;
								$vrSqlR=$vrSqlR." 				     AND id_actividad <>	".$reg2R[id_actividad] ;			
							}
							$vrSqlR=$vrSqlR." 				     ) ";			
							$vrSqlR=$vrSqlR." 		group by id_actividad  ";
							$vrSqlR=$vrSqlR." 		) A, ActividadesRecursos B ";
							$vrSqlR=$vrSqlR." WHERE A.id_actividad = B.id_actividad ";
							$vrSqlR=$vrSqlR." and A.secuencia = B.secuencia ";			
							$vrSqlR=$vrSqlR." and B.id_proyecto = " . $cualProyecto ;	
									
							$vrCursorR = mssql_query($vrSqlR);
							if ($vrRegR=mssql_fetch_array($vrCursorR)) {
								$miTotalSubAct = $vrRegR[totSA] ;
							}
				
							//Trae la sumatoria de la programación asignada a las subactividades de una actividad
							$miTotPrgSubAct=0;
							$vrSqlR="SELECT SUM(valorProgramado) totAsignaSA FROM Asignaciones ";
							$vrSqlR=$vrSqlR." WHERE id_proyecto = " . $cualProyecto ;	
							$vrSqlR=$vrSqlR." and id_actividad in (SELECT id_actividad FROM Actividades ";
							$vrSqlR=$vrSqlR." 		     WHERE id_proyecto = " . $cualProyecto ;	
							$vrSqlR=$vrSqlR." 		     and tipoActividad > 1  ";
							$vrSqlR=$vrSqlR." 		     and actPrincipal = " . $reg2R[actPrincipal] ;
							if ($reg2R[tipoActividad] > 1) {
								$vrSqlR=$vrSqlR." 		 and nivelesActiv like '%".$reg2R[nivelesActiv]."%'" ;
								$vrSqlR=$vrSqlR." 		 AND id_actividad <>	".$reg2R[id_actividad] ;			
							}
							$vrSqlR=$vrSqlR." 				     ) ";	
							 if($pAno<>"TODOS"){
							$vrSqlR=$vrSqlR." and DATEPART(YEAR,fecha_inicial)=" . $pAno;
							}
							if($pMes<>"TODOS"){
							$vrSqlR=$vrSqlR." and DATEPART(MONTH,fecha_inicial)=" . $pMes;
						    }		
							$vrCursorR = mssql_query($vrSqlR);
							if ($vrRegR=mssql_fetch_array($vrCursorR)) {
								$miTotPrgSubAct ="$ " . number_format($vrRegR[totAsignaSA], 0, ',','.') ;
							}
					
							
						    $excel=$excel."$id_act\t";
						    $excel=$excel."$nombreAc\t";
						    $excel=$excel."$macro\t";
							$excel=$excel."$uni\t";
							$excel=$excel."$nomCat\t";
							$excel=$excel."$nomUsu\t";
							$excel=$excel."$claseTiem\t";
							$excel=$excel."$localiza\t";
							$excel=$excel."$carg\t";
							$excel=$excel."$HorasProg\t";
							$excel=$excel."$HorasRep\t";
							$excel=$excel."$Horario\t";
							$excel=$excel."$ValorProg\t";
							$excel=$excel."$miValorActiv\t";
							$excel=$excel."$miTotalSubAct\t";
							$excel=$excel."$miTotalAsigna\t";
							$excel=$excel."$miTotPrgSubAct\t";
							$excel=$excel."$miTotalCostosD\n";
							
							
						    
						 }
						 
						 
						 
			  }
			  
			
		}
	
	


?>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form action="rptXlsFactura01.php" method="post">
      <tr>
        <td align="right" class="TxtTabla">
		<input type="hidden" name="export" value="<? echo $excel; ?>">
		<input name="submit" type="submit" class="Boton" value="Generar XLS" /></td>
      </tr>
	  </form>
    </table>
	
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
	

	

</body>
</html>
