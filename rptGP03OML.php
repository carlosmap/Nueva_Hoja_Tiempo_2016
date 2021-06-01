<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


if (trim($pOrdenar) == '') {
	$pOrdenar = '1';
}

//08Nov2010
//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$fDivSql="Select * from divisiones ";
$fDivSql=$fDivSql." where estadoDiv = 'A' ";
$fDivSql=$fDivSql." order by nombre ";
$fDivCursor = mssql_query($fDivSql);

//Consulta para traer la información del Proyecto, Director, Coordinador y sus correspondientes extensiones
//Solo para proyectos activos
//--Patricia Barón Manrique
//--05Nov2010	
//Ajuste para Silvia palacio
//Incluye filtro división, nombre del director
/*
$cantProyectos = 0 ;
$sql=" SELECT * FROM ";
$sql=$sql." ( ";
$sql=$sql." 	select P.* , D.nombre nomDirector, D.apellidos apeDirector, C.nombre nomCoordina, C.apellidos apeCoordina, ";
$sql=$sql." 	eD.extension extDir, eC.extension extCoordina, sC.id_division, D.email mailDirector, C.email mailCoordina ";
$sql=$sql." 	from HojaDeTiempo.dbo.proyectos P, HojaDeTiempo.dbo.Usuarios D, HojaDeTiempo.dbo.Usuarios C, ";
$sql=$sql." 		GestiondeInformacionDigital.dbo.extensiones eD, ";
$sql=$sql." 		GestiondeInformacionDigital.dbo.extensiones eC, ";
$sql=$sql." 		(select Distinct A.secuencia, A.id_division, B.id_proyecto ";
$sql=$sql." 		from GestiondeInformacionDigital.dbo.SolicitudCodigo A, GestiondeInformacionDigital.dbo.CargosSolCodigo B ";
$sql=$sql." 		where A.secuencia = B.secuencia ";
$sql=$sql." 		) sC ";
$sql=$sql." 	where P.id_director = D.unidad  ";
$sql=$sql." 	AND P.id_coordinador *= C.unidad ";
$sql=$sql." 	AND P.id_director *= eD.unidad  ";
$sql=$sql." 	AND P.id_coordinador *= eC.unidad ";
$sql=$sql." 	AND P.id_estado = 2 ";
$sql=$sql." 	AND (P.codigo <> 'ACC' AND P.codigo <> 'AUS' AND P.codigo <> 'ENF' AND P.codigo <> 'LIC'  ";
$sql=$sql." 	AND P.codigo <> 'PER' AND P.codigo <> 'SAN' AND P.codigo <> 'VAC')   ";
$sql=$sql." 	AND P.id_proyecto *= sC.id_proyecto ";
if (trim($cNombre) != "") {
	$sql=$sql." AND P.nombre LIKE '%".$cNombre."%'" ;
}
if (trim($pEmp) != "") {
	$sql=$sql." AND P.idEmpresa =" . $pEmp ;
}
if (trim($cNombreDir) != "") {
	$sql=$sql." AND (D.nombre LIKE '%".$cNombreDir."%' OR D.apellidos LIKE '%".$cNombreDir."%') " ;
}

$sql=$sql." ) X ";
if (trim($pfDivision) != "") {
	$sql=$sql." where id_division = " . $pfDivision;
}

$sql=$sql." order by nombre " ;
*/



if ($pMes == "") {
	$pMes=date("m"); //el mes actual
}

if ($pAno == "") {
	$pAno=date("Y"); //el a&ntilde;o actual
}

$cantProyectos = 0 ;
$sql="SELECT P.* , D.nombre nomDirector, D.apellidos apeDirector, C.nombre nomCoordina, C.apellidos apeCoordina,  ";
$sql=$sql." D.email mailDirector, C.email mailCoordina  ";
$sql=$sql." FROM HojaDeTiempo.dbo.proyectos P, HojaDeTiempo.dbo.Usuarios D, HojaDeTiempo.dbo.Usuarios C ";
$sql=$sql." WHERE P.id_director = D.unidad  ";
$sql=$sql." AND P.id_coordinador *= C.unidad ";
if (trim($pEmp) != "") {
	$sql=$sql." AND P.idEmpresa =" . $pEmp ;
}
$sql=$sql." AND P.id_proyecto  in ( ";
$sql=$sql." 	select distinct id_proyecto ";
$sql=$sql." 	from horas ";
$sql=$sql." 	where id_proyecto not in (56, 60, 61, 62, 63, 64, 65, 546, 359) ";
##
if (trim($cNombre) != "") {
	$sql=$sql." and CAST(P.nombre as varchar) LIKE '%".$cNombre."%'" ;
}

if (trim($pEmp) != "") {
	$sql=$sql." and P.idEmpresa =" . $pEmp ;
}

if (trim($cNombreDir) != "") {
	$sql=$sql." and (D.nombre LIKE '%".$cNombreDir."%' OR D.apellidos LIKE '%".$cNombreDir."%') " ;
}
##
if (trim($pMes) != "") {
	$sql=$sql." AND MONTH(fecha) =" . $pMes ;
}

if (trim($pAno) != "") {
	$sql=$sql." AND YEAR(fecha)=" . $pAno ;
}
$cantDias= 0;
if (trim($pUltSemana) != "") {
	$cantDias= $pUltSemana * 8 ;
	$sql=$sql." AND fecha between DATEADD(day, -".$cantDias.", GETDATE()) AND GETDATE()  " ;
}
$sql=$sql." 	) ";
if (trim($pOrdenar) == "1") {
	$sql=$sql." order by P.nombre ";
}

if (trim($pOrdenar) == "2") {
	$sql=$sql." order by P.codigo ";
}
#echo $sql."<br />";
$cursor = mssql_query($sql);
$cantProyectos = mssql_num_rows($cursor);

#echo $sql . "<br>";
/*
//8Ago2007
//Consulta para traer la información del Proyecto, Director, Coordinador y sus correspondientes extensiones
//Solo para proyectos activos
$sql="select P.* , D.nombre nomDirector, D.apellidos apeDirector, C.nombre nomCoordina, C.apellidos apeCoordina, ";
$sql=$sql." eD.extension extDir, eC.extension extCoordina " ;
$sql=$sql." from HojaDeTiempo.dbo.proyectos P, HojaDeTiempo.dbo.Usuarios D, HojaDeTiempo.dbo.Usuarios C, " ;
$sql=$sql." GestiondeInformacionDigital.dbo.extensiones eD, GestiondeInformacionDigital.dbo.extensiones eC " ;
$sql=$sql." where P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_director *= eD.unidad " ;
$sql=$sql." AND P.id_coordinador *= eC.unidad " ;
$sql=$sql." AND P.id_estado = 2 " ;
$sql=$sql . " AND (P.codigo <> 'ACC' AND P.codigo <> 'AUS' AND P.codigo <> 'ENF' AND P.codigo <> 'LIC'  ";
$sql=$sql . " AND P.codigo <> 'PER' AND P.codigo <> 'SAN' AND P.codigo <> 'VAC')   ";
$sql=$sql." AND P.nombre LIKE '%".$cNombre."%'" ;
if (trim($pEmp) != "") {
	$sql=$sql." AND P.idEmpresa =" . $pEmp ;
}
$sql=$sql." order by P.nombre " ;
$cursor = mssql_query($sql);
*/

$sqlEm="select * from HojaDeTiempo.dbo.empresas ";
$cursorEm = mssql_query($sqlEm);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--

window.name="winReportesGP";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Listado de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 534px;">
		<div > Reportes Para La Gerencia de Proyectos </div>
</div>
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
    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" bgcolor="#FFFFFF" class="FichaInAct"><a href="rptGP01.php" class="FichaInAct1" >Proyectos <br />
      Activos</a> </td>
        <td width="15%" bgcolor="#FFFFFF" class="FichaInAct"><a href="rptGP02.php" class="FichaInAct1">Propuestas <br />
      Activas </a></td>
        <td width="15%"  class="FichaAct"> Proyectos<br />
      con facturaci&oacute;n</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td height="1" colspan="4" class="TituloUsuario"> </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td >&nbsp;</td>
      </tr>
    </table>
    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Criterio de Consulta </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  
  <tr>
    <td width="15%" align="right" class="TituloTabla">Empresa:</td>
    <td class="TxtTabla">	<select name="pEmp" class="CajaTexto" id="pEmp" onChange="document.form1.submit();">
      <? while ($regEm=mssql_fetch_array($cursorEm)) { 
	  		if (trim($pEmp) == trim($regEm[idEmpresa])) { 
				$selIt="selected";
			}
			else {
				$selIt="";
			}
	  ?>
	  		<option value="<? echo $regEm[idEmpresa] ;?>" <? echo $selIt; ?> ><? echo $regEm[nombre] ; ?></option>
	  <? } ?>
	  		<? 
		if (trim($pEmp) == "") { 
			$selItb="selected";
		}
		?>
      <option value="" <? echo $selItb; ?> > </option>

    </select></td>
    <td width="10%" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="15%" align="right" class="TituloTabla">Nombre proyecto:&nbsp;</td>
    <td class="TxtTabla"><input name="cNombre" type="text" class="CajaTexto" id="cNombre" value="<? echo $cNombre; ?>" size="85" />
  &nbsp; </td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Nombre Director / Coordinador: </td>
    <td class="TxtTabla"><input name="cNombreDir" type="text" class="CajaTexto" id="cNombreDir" value="<? echo $cNombreDir; ?>" size="85" /></td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Fecha de creaci&oacute;n: &nbsp;</td>
    <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" class="TituloTabla">Mes</td>
        <td width="20%">
		<? 
	//Seleccionar el mes cuANDo se carga la página por primera vez
	//si no cuANDo se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
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
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes" onChange="document.form1.submit();" >
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
      <option>::: Todos :::</option>
    </select>		</td>
        <td width="5%">&nbsp;</td>
        <td width="15%" class="TituloTabla">A&ntilde;o</td>
        <td>&nbsp;
          <select name="pAno" class="CajaTexto" id="pAno" onChange="document.form1.submit();">
          <? 
	//Generar los a&ntilde;os de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el a&ntilde;o cuANDo se carga la p&aacute;gina por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el a&ntilde;o actual
		}
		else {
			$AnoActual= $pAno; //el a&ntilde;o seleccionado
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
        </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">&Uacute;ltima(s) Semana(s) </td>
        <td>&nbsp;          
		<?
		if (trim($pUltSemana) == "1") {
			$selSem01="selected";
			$selSem02="";
			$selSem03="";
		}
		if (trim($pUltSemana) == "2") {
			$selSem01="";
			$selSem02="selected";
			$selSem03="";
		}
		if (trim($pUltSemana) == "3") {
			$selSem01="";
			$selSem02="";
			$selSem03="selected";
		}

		?>
		<select name="pUltSemana" class="CajaTexto" id="pUltSemana" onChange="document.form1.submit();" >
          <option value="" selected > </option>
		  <option value="1" <? echo $selSem01; ?> >1</option>
          <option value="2" <? echo $selSem02; ?> >2</option>
          <option value="3" <? echo $selSem03; ?> >3</option>
        </select></td>
        <td>&nbsp;</td>
        <td class="TituloTabla">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>      </td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Ordernar por </td>
    <td class="TxtTabla">
	<? 
	if (trim($pOrdenar) == '1') {
		$selBtn01 = 'checked';
		$selBtn02 = '';
	} 
	
	if (trim($pOrdenar) == '2') {
		$selBtn01 = '';
		$selBtn02 = 'checked';
	} 
	
	?>	<input name="pOrdenar" type="radio" value="1" <? echo $selBtn01; ?> />
      Nombre 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="pOrdenar" type="radio" value="2" <? echo $selBtn02; ?> />
        C&oacute;digo </td>
    <td class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar" /></td>
  </tr>
	</form>
</table>
	</td>
  </tr>
</table>



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <? 
  //Aparece para PBM, DR, y Silvia Palacio
  if($_SESSION["sesUnidadUsuario"] == 15712 or $_SESSION["sesUnidadUsuario"] == 16374 or $_SESSION["sesUnidadUsuario"] == 15850 or $_SESSION["sesPerfilUsuario"] == 1){ ?>
  <? } ?>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" class="TxtTabla"><strong>Cantidad de proyectos: <? echo $cantProyectos ; ?></strong></td>
        <td align="right">&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Proyectos y/o propuestas con facturaci&oacute;n </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">C&oacute;digo</td>
        <td>Propuesta</td>
        <td width="15%">Director/Coordinador</td>
        <td width="12%">Ordenadores del gasto </td>
        <td width="12%">Programadores de proyecto </td>
        <td width="12%">Programadores de actividades </td>
        <td width="7%">Empresa</td>
        <td width="7%">Fecha de creaci&oacute;n </td>
        <td width="5%">Personas que facturan</td>
        <td width="5%">Horas registradas</td>
        <td>Facturaci&oacute;n</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <td width="5%"><? echo ucwords(strtolower($reg[codigo])) . "." . ucwords(strtolower($reg[cargo_defecto]))  ; ?><br />
	      
		  <?
		  //Trae los cargos adicionales del proyecto
		  $cSql="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		  $cSql=$cSql." WHERE id_proyecto =" . $reg[id_proyecto];
		  $cCursor = mssql_query($cSql);
		  $y=0;
		  while ($cReg=mssql_fetch_array($cCursor)) {
				if ($y==0)  {
					echo "<br /> Cargos adicionales <br />";
				}
		  		echo $cReg[cargos_adicionales] . "<br>";
				$y=$y+1;
		  }
		 
		  ?>
</td>
        <td><? echo ucwords(strtolower($reg[nombre]))  ; ?></td>
        <td width="15%"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="TxtTabla"><? echo ucwords(strtolower($reg[apeDirector]))  . ", " . ucwords(strtolower($reg[nomDirector])) . "<br>" . trim($reg[mailDirector]) . "@ingetec.com.co" ; ?></td>
          </tr>
          <tr>
            <td class="TxtTabla"><? 
			if (trim($reg[apeCoordina]) != "" ) {
			echo ucwords(strtolower($reg[apeCoordina])) . ", " . ucwords(strtolower($reg[nomCoordina])) . "<br>" . trim($reg[mailCoordina]) . "@ingetec.com.co" ; 
			}
			?></td>
          </tr>
        </table></td>
        <td width="12%">
		<?
		//lista de los ordenadores del gasto
		$ogSql="SELECT O.* , U.nombre, U.apellidos ";	
		$ogSql=$ogSql." FROM GestiondeInformacionDigital.dbo.OrdenadorGasto O,  ";		
		$ogSql=$ogSql." HojaDeTiempo.dbo.Usuarios U ";		
		$ogSql=$ogSql." WHERE O.unidadOrdenador = U.unidad ";		
		$ogSql=$ogSql." AND O.id_proyecto = " . $reg[id_proyecto];
  		$ogCursor = mssql_query($ogSql);

		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<? while ($ogReg=mssql_fetch_array($ogCursor)) { ?>
          <tr>
            <td><? echo ucwords(strtolower($ogReg[apellidos])) . ", " . ucwords(strtolower($ogReg[nombre]))   ; ?></td>
          </tr>
		  <? } ?>
        </table>		</td>
        <td width="12%">
		<?
		//Listado de programadores
		$prSql="select distinct P.unidad, P.progProyecto, U.nombre, U.apellidos  ";
		$prSql=$prSql." from HojaDeTiempo.dbo.Programadores P, HojaDeTiempo.dbo.Usuarios U ";
		$prSql=$prSql." where P.unidad = U.unidad ";
		$prSql=$prSql." AND P.id_proyecto =" . $reg[id_proyecto];
		$prSql=$prSql." AND P.progProyecto = 1" ;
  		$prCursor = mssql_query($prSql);
		?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<? while ($prReg=mssql_fetch_array($prCursor)) { ?>
          <tr>
            <td><? echo ucwords(strtolower($prReg[apellidos])) . ", " . ucwords(strtolower($prReg[nombre]))   ; ?></td>
          </tr>
		  <? } ?>
        </table>
		</td>
        <td width="12%"><?
		//Listado de programadores
		$prSql="select distinct P.unidad, P.progProyecto, U.nombre, U.apellidos  ";
		$prSql=$prSql." from HojaDeTiempo.dbo.Programadores P, HojaDeTiempo.dbo.Usuarios U ";
		$prSql=$prSql." where P.unidad = U.unidad ";
		$prSql=$prSql." AND P.id_proyecto =" . $reg[id_proyecto];
		$prSql=$prSql." AND P.progProyecto = 0" ;
  		$prCursor = mssql_query($prSql);
		?>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <? while ($prReg=mssql_fetch_array($prCursor)) { ?>
            <tr>
              <td><? echo ucwords(strtolower($prReg[apellidos])) . ", " . ucwords(strtolower($prReg[nombre]))   ; ?></td>
            </tr>
            <? } ?>
          </table></td>
        <td width="7%">
		<? 
		//Trae elnombre de la empresa
		$eSql="select * from empresas ";
		$eSql=$eSql." where idEmpresa =" . $reg[idEmpresa]; 
		$eCursor = mssql_query($eSql);
		if ($eReg=mssql_fetch_array($eCursor)) {
			echo $eReg[nombre] ;
		}
		?>
		</td>
        <td width="7%"><? 
		if (trim($reg[fechaCrea]) != "") {
			echo date("M d Y ", strtotime($reg[fechaCrea])); 
		}
		?></td>
        <td width="5%">
		<?
			#	Personas que faturan en un proyecto
			$sqlPersonas = "SELECT COUNT(*) Personas FROM Horas hr, Proyectos Pro WHERE hr.id_proyecto = Pro.id_proyecto AND 
							Pro.id_proyecto = ".$reg[id_proyecto]."  AND MONTH(hr.fecha) = ".$pMes." AND YEAR(hr.fecha)=  ".$pAno;
			#echo $sqlPersonas."<br />"; 
			$qryPersonas = mssql_fetch_array( mssql_query( $sqlPersonas ) );
			if( $qryPersonas[Personas] != "" )
				echo $qryPersonas[Personas];
			else
				echo "0";

		?></td>
        <td width="5%">
        <table width="100%"  border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF" >
        <tr class="TituloTabla2" bgcolor="#FFFFFF"><td>Clase de tiempo</td><td>Hr.</td></tr>
         <?
			#	Numero de horas facturadas
			$sqlHrFacturado = "SELECT distinct hr.clase_tiempo, SUM(hr.horas_registradas) hrReg FROM Horas hr, Proyectos Pro 
							   WHERE hr.id_proyecto = Pro.id_proyecto AND Pro.id_proyecto = ".$reg[id_proyecto]." AND MONTH(hr.fecha) = ".$pMes." AND YEAR(hr.fecha)= ".$pAno."
							   GROUP BY hr.clase_tiempo";		
			$qryHrFacturado = mssql_query( $sqlHrFacturado );
			while( $rowHrFacturado = mssql_fetch_array( $qryHrFacturado ) ){				
				echo "<tr  class='TxtTabla' bgcolor='#FFFFFF'><td>".$rowHrFacturado[clase_tiempo]."</td><td>".$rowHrFacturado[hrReg]."</td></tr>";
			}
		?>
        </table>
        </td>
        <td>
        <?
			$sqlFacturado = "SELECT DISTINCT 
							 	id_proyecto, nomProyecto, SUM(vlrFacturado) vlrTotalFacturado, codigo, cargo_defecto 
							 FROM ( 
							 	Select 
									H.unidad, U.nombre, U.apellidos, 
									H.id_proyecto, P.nombre nomProyecto, H.clase_tiempo, sum(H.horas_registradas) hFacturadas, A.salarioBase,  
									((A.salarioBase/185)*sum(H.horas_registradas)) vlrFacturado, P.codigo, P.cargo_defecto 
								from 
									Horas H, Proyectos P, Asignaciones A, Usuarios U 
								where 
									H.id_proyecto = P.id_proyecto and H.id_proyecto = A.id_proyecto and H.id_actividad = A.id_actividad and H.unidad = A.unidad 
									and H.clase_tiempo = A.clase_tiempo and H.localizacion = A.localizacion and H.cargo = (P.codigo + A.cargo) and 
									MONTH(H.fecha)= MONTH(A.fecha_inicial) and YEAR(H.fecha)= YEAR(A.fecha_inicial) and H.unidad = U.unidad and 
									H.id_proyecto = ".$reg[id_proyecto]." AND MONTH(H.fecha)= ".$pMes." and YEAR(H.fecha)= ".$pAno."
								GROUP BY 
									H.unidad, U.nombre, U.apellidos, H.id_proyecto, P.nombre, H.clase_tiempo, A.salarioBase, P.codigo, P.cargo_defecto 
							) A
							GROUP BY id_proyecto, nomProyecto, codigo, cargo_defecto 
							ORDER BY nomProyecto ";
			#*/
			#echo $sqlFacturado;
			/**/
			$cursor00 = mssql_query( $sqlFacturado );
			
			#	Facturacion por proyecto
			#$qryFacturado = mssql_fetch_array( $cursor00 );
			$vlrFacturado = 0;
			while ( $reg00 = mssql_fetch_array( $cursor00 ) ){
				#$reg00 = mssql_fetch_array( $cursor00 ); 
				if ( trim( $reg00[vlrTotalFacturado] ) != "" ){
					$vlrFacturado = $reg00[vlrTotalFacturado] ;
					$vlrTotalFacturado = $vlrTotalFacturado + $vlrFacturado; 
					echo "$ ".number_format($vlrFacturado, 2, ",", ".");
					#echo "$ ".$vlrFacturado;
				} 
				else 
					echo "$ 000.000.00";
			}
		?>
        
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
            <td class="TxtTabla"><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
