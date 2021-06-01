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

/*
Usuarios Autorizados para Imprimir en los Proyectos
dbo.AutorizadosImpresion
id_proyecto, unidad, fechaAutoriza, fechaDesautoriza, estado, usuarioCrea, fechaCrea, usuarioMod, fechaMod
*/
$sql1 = " SELECT A.unidad, A.nombre, A.apellidos, B.id_proyecto, B.estado, B.fechaAutoriza, B.usuarioCrea, B.fechaMod, B.fechaDesautoriza, B.usuarioMod
FROM HojaDeTiempo.dbo.Usuarios A, HojaDeTiempo.dbo.AutorizadosImpresion B
WHERE A.unidad = B.unidad ";
$sql1 = $sql1 . " AND B.id_proyecto = " . $cualProyecto;
$sql1 = $sql1 . " ORDER BY A.unidad, B.estado ";
$cursor1 = mssql_query($sql1);


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
<title>Proyectos - Autorizaci&oacute;n de Impresiones</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 521px; height: 30px;">
Programaci&oacute;n de proyectos - Autorizados Impresi&oacute;n </div>
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
            <td class="TituloUsuario">Usuarios autorizados para imprimir en este proyecto </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="fondo">
          <tr class="TituloTabla2">
            <td>Usuario</td>
            <td>Cargos Autorizados </td>
            <td>Persona que autoriza </td>
            <td>Fecha de Autorizaci&oacute;n </td>
            <td>Estado</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
          <? 
		  while($reg1 = mssql_fetch_array($cursor1)){ 
		  	/* Cargos Autorizados */
			$sql1a = " SELECT A.*, B.codigo, B.cargo, B.descripcion
			FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos A, (
				SELECT id_proyecto, codigo, cargo_defecto AS cargo, descCargoDefecto AS descripcion
				FROM HojaDeTiempo.dbo.Proyectos
				UNION
				SELECT A.id_proyecto, A.codigo, B.cargos_adicionales, B.descripcion
				FROM HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Cargos B
				WHERE A.id_proyecto = B.id_proyecto
			) B
			WHERE A.id_proyecto = B.id_proyecto
			AND ( A.cargo_defecto = B.cargo
			OR A.cargos_adicionales = B.cargo) ";
			$sql1a = $sql1a . " AND A.id_proyecto = " . $reg1['id_proyecto'];
			$sql1a = $sql1a . " AND A.unidad = " . $reg1['unidad'];
			$cursor1a = mssql_query($sql1a);
			
			/*
			Usuario que autoriza
			*/
			$sql1b = " SELECT unidad, nombre, apellidos FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $reg1['usuarioCrea'];
			$cursor1b = mssql_query($sql1b);
			/*
			Usuario que modifica
			*/
			$sql1c = " SELECT unidad, nombre, apellidos FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $reg1['usuarioMod'];
			$cursor1c = mssql_query($sql1c);
		  ?>
		  <tr class="TxtTabla">
            <td><? echo ucwords(strtolower($reg1['unidad'] . " - " . $reg1['apellidos'] . " " . $reg1['nombre'])); ?></td>
            <td><?
			//echo $sql1a;
			while($reg1a = mssql_fetch_array($cursor1a)){
				echo $reg1a['codigo'] . "." . $reg1a['cargo'] . " - " . $reg1a['descripcion'] . "<br>";
			}
			?></td>
            <td><?
			if($reg1['estado'] == "A"){
				if($reg1b = mssql_fetch_array($cursor1b)){
					echo ucwords(strtolower($reg1b['unidad'] . " - " . $reg1b['apellidos'] . " " . $reg1b['nombre'])) . "<br>";
				}
				if($reg1c = mssql_fetch_array($cursor1c)){
					echo ucwords(strtolower($reg1c['unidad'] . " - " . $reg1c['apellidos'] . " " . $reg1c['nombre'])) . " [Mod]";
				}
			}
			?></td>
            <td><?
			if($reg1['estado'] == "A"){
				if(trim($reg1['fechaAutoriza']) != ""){
					echo date("m/d/Y", strtotime($reg1['fechaAutoriza'])) . "<br>";
				}
				if(trim($reg1['fechaMod']) != ""){
					echo date("m/d/Y", strtotime($reg1['fechaMod'])) . " [Mod.]";
				}
			} else {
				if(trim($reg1['fechaDesautoriza']) != ""){
					echo date("m/d/Y", strtotime($reg1['fechaDesautoriza'])) . " [Desautorizacion] <br>";
				}
			}
			?></td>
            <td><?
			if($reg1['estado'] == "A"){
				echo "Activo";
			} else {
				echo "Inactivo";
			}
			?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="center"><a href="#"><img src="img/images/actualizar.jpg" width="19" height="17" border="0" onClick="MM_openBrWindow('upAutorizadosImpresion.php?cualProyecto=<? echo $reg1['id_proyecto']; ?>&cualUnidad=<? echo $reg1['unidad']; ?>','upAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" /></a></td>
          </tr>
		  <? } ?>
          <tr align="right" class="TxtTabla">
            <td colspan="8"><input name="Submit" type="button" class="Boton" onclick="MM_openBrWindow('addAutorizadosImpresion.php?cualProyecto=<? echo $cualProyecto; ?>','addAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" value="Agregar Usuarios / Cargos" /></td>
          </tr>
        </table></td>
      </tr>
	  </ form >
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectos.php');return document.MM_returnValue" value="Lista de Proyectos" />
    <input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;
	</td>
  </tr>
  <tr>
    <td>
      <? if($_SESSION["sesUnidadUsuario"] == 16374 || $_SESSION["sesUnidadUsuario"] == 15712){ ?>
      <input name="Submit42" type="button" class="Boton" onclick="MM_openBrWindow('migrarUsuariosPageDevice.php','addAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" value="Migraci&oacute;n de Usuarios Page Device" />
      <input name="Submit422" type="button" class="Boton" onclick="MM_openBrWindow('migrarProyectosPageDevice.php','addAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" value="Migraci&oacute;n de Proyectos Page Device" />
      <input name="Submit4" type="button" class="Boton" onclick="MM_openBrWindow('migrarAutorizadosImpresion.php','addAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" value="Migraci&oacute;n de Usuarios Autorizados Page Device" />
      <input name="Submit43" type="button" class="Boton" onclick="MM_openBrWindow('migrarUsuariosAdministrativa.php','addAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" value="Migraci&oacute;n de Div. Administrativa Page Device" />
      <input name="Submit55" type="button" class="Boton" id="Submit55" onclick="MM_openBrWindow('migrarIPUsuarios.php','addAutImp','scrollbars=yes,resizable=yes,width=600,height=300')" value="Direcciones IP" />
      <? } ?></td>
    <td align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
