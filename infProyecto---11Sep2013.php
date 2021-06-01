<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

if (trim($btnOrdenar) == '') {
	$btnOrdenar = 1;
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
$sql=$sql." 	and P.id_coordinador *= C.unidad ";
$sql=$sql." 	and P.id_director *= eD.unidad  ";
$sql=$sql." 	and P.id_coordinador *= eC.unidad ";
$sql=$sql." 	and P.id_estado = 2 ";
$sql=$sql." 	and (P.codigo <> 'ACC' and P.codigo <> 'AUS' and P.codigo <> 'ENF' and P.codigo <> 'LIC'  ";
$sql=$sql." 	and P.codigo <> 'PER' and P.codigo <> 'SAN' and P.codigo <> 'VAC')   ";
$sql=$sql." 	and P.id_proyecto *= sC.id_proyecto ";
if (trim($cNombre) != "") {
	$sql=$sql." and P.nombre LIKE '%".$cNombre."%'" ;
}
if (trim($pEmp) != "") {
	$sql=$sql." and P.idEmpresa =" . $pEmp ;
}
if (trim($cNombreDir) != "") {
	$sql=$sql." and (D.nombre LIKE '%".$cNombreDir."%' OR D.apellidos LIKE '%".$cNombreDir."%') " ;
}

if (trim($elCodigo) != "") {
	$sql=$sql." and codigo = '" . $elCodigo . "' ";
}

if (trim($elCargo) != "") {
	$sql=$sql." and cargo_defecto = '" . $elCargo . "' ";
}


$sql=$sql." ) X ";
if (trim($pfDivision) != "") {
	$sql=$sql." where id_division = " . $pfDivision;
}


if (trim($btnOrdenar) == '1') {
	$sql=$sql." order by nombre " ;
}

if (trim($btnOrdenar) == '2') {
	$sql=$sql." order by codigo " ;
}

$cursor = mssql_query($sql);

/*
//8Ago2007
//Consulta para traer la información del Proyecto, Director, Coordinador y sus correspondientes extensiones
//Solo para proyectos activos
$sql="select P.* , D.nombre nomDirector, D.apellidos apeDirector, C.nombre nomCoordina, C.apellidos apeCoordina, ";
$sql=$sql." eD.extension extDir, eC.extension extCoordina " ;
$sql=$sql." from HojaDeTiempo.dbo.proyectos P, HojaDeTiempo.dbo.Usuarios D, HojaDeTiempo.dbo.Usuarios C, " ;
$sql=$sql." GestiondeInformacionDigital.dbo.extensiones eD, GestiondeInformacionDigital.dbo.extensiones eC " ;
$sql=$sql." where P.id_director = D.unidad " ;
$sql=$sql." and P.id_coordinador *= C.unidad " ;
$sql=$sql." and P.id_director *= eD.unidad " ;
$sql=$sql." and P.id_coordinador *= eC.unidad " ;
$sql=$sql." and P.id_estado = 2 " ;
$sql=$sql . " and (P.codigo <> 'ACC' and P.codigo <> 'AUS' and P.codigo <> 'ENF' and P.codigo <> 'LIC'  ";
$sql=$sql . " and P.codigo <> 'PER' and P.codigo <> 'SAN' and P.codigo <> 'VAC')   ";
$sql=$sql." and P.nombre LIKE '%".$cNombre."%'" ;
if (trim($pEmp) != "") {
	$sql=$sql." and P.idEmpresa =" . $pEmp ;
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
window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Listado de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> DIRECTORES / COORDINADORES PROYECTOS </div>
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
    <td align="right" class="TituloTabla">Empresa</td>
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
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
        <td align="right" class="TituloTabla">Divisi&oacute;n</td>
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
    </select>		</td>
		 <td class="TxtTabla">&nbsp;</td>
      </tr>
  <tr>
    <td align="right" class="TituloTabla">C&oacute;digo</td>
    <td class="TxtTabla"><input name="elCodigo" type="text" class="CajaTexto" id="elCodigo" value="<? echo $elCodigo; ?>" size="10" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;">
      .
        <input name="elCargo" type="text" class="CajaTexto" id="elCargo" value="<? echo $elCargo; ?>" size="5" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="15%" align="right" class="TituloTabla">Nombre proyecto:&nbsp;</td>
    <td class="TxtTabla">
<input name="cNombre" type="text" class="CajaTexto" id="cNombre" value="<? echo $cNombre; ?>" size="85" />	
&nbsp;      </td>
    <td width="10%" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Nombre Director / Coordinador </td>
    <td class="TxtTabla"><input name="cNombreDir" type="text" class="CajaTexto" id="cNombreDir" value="<? echo $cNombreDir; ?>" size="85" /></td>
    <td class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar" /></td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Ordenar por </td>
    <td class="TxtTabla">
	<?
	if (trim($btnOrdenar) == '1') {
		$selNombre = "checked";
		$selCod = "";
	}
	if (trim($btnOrdenar) == '2') {
		$selNombre = "";
		$selCod = "checked";
	}
	
	?>	<input name="btnOrdenar" type="radio" value="1" <? echo $selNombre; ?> />
      Nombre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="btnOrdenar" type="radio" value="2" <? echo $selCod; ?> />
        C&oacute;digo</td>
    <td class="TxtTabla">&nbsp;</td>
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
  <tr>
    <td align="right">
    <input name="Submit2" type="button" class="Boton" onclick="MM_openBrWindow('reporteListaDirectores.php?cNombre=<? echo $cNombre; ?>&pEmp=<? echo $pEmp; ?>&cNombreDir=<? echo $cNombreDir; ?>&pfDivision=<? echo $pfDivision; ?>','','scrollbars=yes,resizable=yes,width=800,height=600')" value="Generar Archivo en Excel" /></td>
  </tr>
  <? } ?>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="3%">Shared Files </td>
        <td width="5%">C&oacute;digo</td>
        <td>Proyecto</td>
        <td width="15%">Director/Coordinador</td>
        <td width="5%">Extensi&oacute;n</td>
        <td width="12%">Ordenadores del gasto </td>
        <td width="12%">Programadores de proyecto </td>
        <td width="12%">Programadores de actividades </td>
        <td width="7%">Empresa</td>
        <td width="3%">&nbsp;</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <td width="3%">
		<?
		$existeFTP = 0 ;
		$fSql="select count(*) hayFTP from PortalGID.dbo.asignaProyectosExt ";
		$fSql=$fSql." where id_proyecto =" . $reg[id_proyecto];
		$fCursor = mssql_query($fSql);
		if ($fReg=mssql_fetch_array($fCursor)) {
			$existeFTP = $fReg[hayFTP] ;
		}
		?>
		<? if ($existeFTP == 0) {  ?>
				<? 
		$sfSql="SELECT COALESCE(SUM(hayReg), 0) verBoton ";
		$sfSql=$sfSql." from ( ";
		$sfSql=$sfSql." 	SELECT count(*) hayReg ";
		$sfSql=$sfSql." 	FROM proyectos P ";
		$sfSql=$sfSql." 	WHERE (id_director = ".$laUnidad." or id_coordinador = ".$laUnidad.") ";
		$sfSql=$sfSql." 	AND id_proyecto = " . $reg[id_proyecto] ;
		$sfSql=$sfSql." 	UNION ALL ";
		$sfSql=$sfSql." 	SELECT count(*) hayReg ";
		$sfSql=$sfSql." FROM GestiondeInformacionDigital.dbo.OrdenadorGasto ";
		$sfSql=$sfSql." 	where unidadOrdenador = " . $laUnidad ;
		$sfSql=$sfSql." 	and id_proyecto =" . $reg[id_proyecto] ;
		$sfSql=$sfSql." ) A ";
		$sfCursor = mssql_query($sfSql);
	    if ($sfReg=mssql_fetch_array($sfCursor)) {
			  $verBotonSF = $sfReg[verBoton] ;
		}

		//si verBotonSF > 0 quiere decir que es director, coordinador u prdenador del gasto.
		if ($verBotonSF > 0) {
//				if (($laUnidad == $reg[id_director]) OR ($laUnidad == $reg[id_coordinador])) { ?>
					<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addasignaProyExterno.php?cualProyecto=<? echo $reg[id_proyecto]; ?>','vAsPE','scrollbars=yes,resizable=yes,width=400,height=200')" value="Crear" />
				<? } ?>
		<? }
			else { ?>
				<? if (($laUnidad == $reg[id_director]) OR ($laUnidad == $reg[id_coordinador])) { ?>
					<a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" border="0" onclick="MM_openBrWindow('upasignaProyExterno.php?cualReg=<? echo $reg[id_proyecto]; ?>','vupAPE','scrollbars=yes,resizable=yes,width=400,height=200')" /></a>
				<? } 
				   else {
				?>	
					<img src="img/images/Si.gif" alt="Tiene Sistema de Gesti&oacute;n de archivos externos" />
				<? } ?>
		<? } ?>
		
		</td>
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
		  		echo "[" . $cReg[cargos_adicionales] . "] " .  $cReg[descripcion] . "<br>";
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
        <td width="5%" align="center">
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td class="TxtTabla"><? echo $reg[extDir]; ?></td>
            </tr>
            <tr>
              <td class="TxtTabla"><? echo $reg[extCoordina]; ?></td>
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
		$prSql=$prSql." and P.id_proyecto =" . $reg[id_proyecto];
		$prSql=$prSql." and P.progProyecto = 1" ;
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
		$prSql=$prSql." and P.id_proyecto =" . $reg[id_proyecto];
		$prSql=$prSql." and P.progProyecto = 0" ;
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
        <td width="3%" align="center">
		<?
		$existeEstrucNomP=0;
		$enSql="select count(*) hayEstruc ";
		$enSql=$enSql." from GestiondeInformacionDigital.dbo.EstructuraNomProyecto ";
		$enSql=$enSql." where id_proyecto =" . $reg[id_proyecto] ;
//		echo $enSql;
		$enCursor = mssql_query($enSql);
		if ($enReg=mssql_fetch_array($enCursor)) {
			$existeEstrucNomP=$enReg[hayEstruc] ;
		}
//		echo "<br>" 
		if ($existeEstrucNomP == 0) {
		?>
   			<?
			//El botón sólo aparece al perfil de administrador del sistema por ahora,
			//en el futuro cada director/coordinador definirá el nombre.
			if ($_SESSION["sesPerfilUsuario"] == 1 ) {
			?>
		<a href="#"><img src="img/images/imgFiles.gif" alt="Definir Estructura de nombre de archivos para el proyecto" width="14" height="14" border="0" onclick="MM_openBrWindow('addEstrucNombreArchivo.php?cualProyecto=<? echo $reg[id_proyecto]; ?>','adEstNA','scrollbars=yes,resizable=yes,width=500,height=400')" /></a>
		<? } // if session?>
		<? }
		else {
		?>
		<a href="#"><img src="img/images/Si.gif" alt="Ver Estructura definida para el archivo" border="0" onclick="MM_openBrWindow('verEstrucNombreArchivo.php?cualProyecto=<? echo $reg[id_proyecto]; ?>','adEstNA','scrollbars=yes,resizable=yes,width=500,height=400')" /></a>
		<? } // else ?>
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
