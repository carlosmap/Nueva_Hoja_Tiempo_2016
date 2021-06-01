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

//12Feb2008
//Trae los sitios de trabajo asociados al proyecto seleccio
$sql2="select * from SitiosTrabajo where id_proyecto= ". $cualProyecto ;
$cursor2 = mssql_query($sql2);


//26Mar2008
//Trae los tipos de viático del proyecto seleccionado
$sql3="SELECT p.id_proyecto, p.IncluyeFestivos, v.*  " ;
$sql3=$sql3." FROM TiposViaticoProy p, TiposViatico v  " ;
$sql3=$sql3." Where p.IDTipoViatico = v.IDTipoViatico " ;
$sql3=$sql3." and p.id_proyecto = " . $cualProyecto ;
$cursor3 = mssql_query($sql3);
$cantTVProy = mssql_num_rows ($cursor3) ;

//14Mar2008
//Trae los Horarios asociados al proyecto seleccio
$sql4="SELECT p.id_proyecto, p.HorarioDefecto, p.ubicacion, h.*  " ;
$sql4=$sql4." FROM HorariosProy p, Horarios h " ;
$sql4=$sql4." Where p.IDhorario = h.IDhorario " ;
$sql4=$sql4." and id_proyecto =" . $cualProyecto ;
$cursor4 = mssql_query($sql4);
$cantHorProy = mssql_num_rows ($cursor4) ;

//31May2011
//Trae las horas y días laborales del proyecto
$sql05="SELECT * ";
$sql05=$sql05." FROM horasydiasLaboralesProy ";
$sql05=$sql05." WHERE id_proyecto = " . $cualProyecto ;
if ($pAno == "") {
	$sql05=$sql05." and vigencia =  year(getdate())";
}
else {
	$sql05=$sql05." and vigencia = " . $pAno;
}
$cursor05 = mssql_query($sql05);
$cantLaboralesproy = mssql_num_rows($cursor05);

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
<title>.:: Planeaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 560px; height: 30px;">
Configuraci&oacute;n del proyecto</div>
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
    <td class="TituloUsuario">  .: PROYECTO </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
        </table>		</td>
	  </tr>
	  <? } ?>
    </table>
		
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
          <tr>
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Todos los proyectos </a></td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">.: SITIOS DE TRABAJO </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="5%">C&oacute;digo</td>
            <td>Sitio de trabajo</td>
            <td width="1%">&nbsp;</td>
          </tr>
	<? while ($reg2=mssql_fetch_array($cursor2)) {  ?>
          <tr class="TxtTabla">
            <td width="5%"><? echo  $reg2[IDsitio] ; ?></td>
            <td><? echo  ucfirst(strtolower($reg2[NomSitio])) ; ?></td>
            <td width="1%">
			<?
			//    'Consulta que valida que el sitio de trabajo no este asociado a un viático
			$SQLval = "Select count(*) as hayViaticos from ViaticosProyecto ";
			$SQLval = $SQLval . " Where id_proyecto = " . $reg2[id_proyecto];
			$SQLval = $SQLval . " and IDsitio = "  . $reg2[IDsitio] ;
			$cursorVal = mssql_query($SQLval);
			if ($regVal=mssql_fetch_array($cursorVal)) {
				$haySTViatico = $regVal[hayViaticos] ;
			}

			//Muestra el botón si no hay viaticos con el ST
			if ($haySTViatico == 0) {
			?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHTSitioT.php?cualProyecto=<? echo $cualProyecto ; ?>&cualST=<? echo $reg2[IDsitio]; ?>','delST','scrollbars=yes,resizable=yes,width=500,height=150')" /></a>
			<? } ?>
			</td>
          </tr>
	<? } ?>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
            <input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addHTSitioT.php?cualProyecto=<? echo $cualProyecto ; ?>','vAddA','scrollbars=yes,resizable=yes,width=500,height=150')" value="Nuevo Sitio de trabajo" />
			</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">.: TIPOS DE VI&Aacute;TICO </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Codigo</td>
        <td>Tipos de vi&aacute;tico </td>
        <td width="10%">Incluye Festivos </td>
        <td width="1%">&nbsp;</td>
      </tr>
      	<? 
		while ($reg3=mssql_fetch_array($cursor3)) {  
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg3[IDTipoViatico] ; ?></td>
        <td><? echo  ucfirst(strtolower($reg3[NomTipoViatico])) ; ?></td>
        <td width="10%" align="center">
		<? 
		if (trim($reg3[IncluyeFestivos]) == "1" ) {
			echo "SI" ; 
		}
		else {
			echo "NO" ; 
		}
		?>
		</td>
        <td width="1%" align="right">
		<? 
		//18Mar2008
		//Valida que el multiplicador de viático no se encuentre asociado a un viático
		$hayViatico = 0;
		$vSqlHP="select count(*) existeViatico from ViaticosProyecto ";
		$vSqlHP=$vSqlHP." WHERE id_proyecto = " . $reg3[id_proyecto] ;
		$vSqlHP=$vSqlHP." AND IDTipoViatico =" .$reg3[IDTipoViatico] ;
		$vCursorHP = mssql_query($vSqlHP);
		if ($vRegHP=mssql_fetch_array($vCursorHP)) {  
			$hayViatico= $vRegHP[existeViatico];
		}
		
		if  ($hayViatico == 0) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHTTipoViaticoProy.php?cualProyecto=<? echo $cualProyecto; ?>&cualTipoV=<? echo  $reg3[IDTipoViatico] ; ?>','dHP','scrollbars=yes,resizable=yes,width=400,height=200')" /></a>
		<? } ?>
		</td>
	  </tr>
	  <? } ?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TxtTabla"><input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addHTTipoViaticoProy.php?cualProyecto=<? echo $cualProyecto ?>','vHP','scrollbars=yes,resizable=yes,width=400,height=200')" value="Agregar Tipo de vi&aacute;tico al Proyecto" /></td>
      </tr>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">.: HORARIOS DEL PROYECTO </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="5%">Codigo</td>
        <td width="5%">Horario Defecto </td>
        <td>Nombre</td>
        <td width="10%">Tipo de N&oacute;mina <br />
        Localizaci&oacute;n</td>
        <td width="5%">Lunes</td>
        <td width="5%">Martes</td>
        <td width="5%">Mi&eacute;rcoles</td>
        <td width="5%">Jueves</td>
        <td width="5%">Viernes</td>
        <td width="5%">S&aacute;bado</td>
        <td width="5%">Domingo</td>
        <td width="5%">Total</td>
        <td width="5%">&nbsp;</td>
        <td width="1%">&nbsp;</td>
      </tr>
      	<? 
		
//IDhorario, id_proyecto, HorarioDefecto
//IDhorario, NomHorario, Lunes, Martes, Miercoles, Jueves, Viernes, Sabado, Domingo		
		while ($reg4=mssql_fetch_array($cursor4)) {  
		$totHP = 0;
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg4[IDhorario] ; ?></td>
        <td width="5%">
		<? if (trim($reg4[HorarioDefecto]) == "1") { ?>
			<img src="img/images/Si.gif" alt="Horario Defecto" width="16" height="14" />
		<? } ?>
		</td>
        <td><? echo  ucfirst(strtolower($reg4[NomHorario])) ; ?></td>
        <td width="10%"><? 
		$origenLoc = "";
		if (trim($reg4[ubicacion]) != "") {
			$origenLoc = "Proyecto";
			if ($reg4[ubicacion]==1) {
				$pLocaliza=$reg4[ubicacion].". Oficina";
			}
			if ($reg4[ubicacion]==2) {
				$pLocaliza=$reg4[ubicacion].". Campo";
			}
			if ($reg4[ubicacion]==3) {
				$pLocaliza=$reg4[ubicacion].". Planilla";
			}
		}
		else {
			$origenLoc = "Horario";
			if ($reg4[localiza]==1) {
				$pLocaliza=$reg4[localiza].". Oficina";
			}
			if ($reg4[localiza]==2) {
				$pLocaliza=$reg4[localiza].". Campo";
			}
			if ($reg4[localiza]==3) {
				$pLocaliza=$reg4[localiza].". Planilla";
			}
		}
		echo $origenLoc . "<br>" . $pLocaliza;
		?></td>
        <td width="5%" align="right"><? echo  $reg4[Lunes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg4[Martes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg4[Miercoles] ; ?></td>
        <td width="5%" align="right"><? echo  $reg4[Jueves] ; ?></td>
        <td width="5%" align="right"><? echo  $reg4[Viernes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg4[Sabado] ; ?></td>
        <td width="5%" align="right"><? echo  $reg4[Domingo] ; ?></td>
        <td width="5%" align="right">
		<? 
		$totHP = $reg4[Lunes] + $reg4[Martes] + $reg4[Miercoles] + $reg4[Jueves] + $reg4[Viernes] + $reg4[Sabado] +  $reg4[Domingo];
		echo  $totHP ; ?></td>
        <td width="5%" align="right"><input type="button" name="Submit6" onclick="MM_openBrWindow('fechasEspecialesProy.php?cualHorario=<? echo $reg4[IDhorario];?>&cualProy=<? echo $cualProyecto; ?>','winFechas','scrollbars=yes,resizable=yes,width=600,height=400')" value="Fechas Especiales" class="Boton" /></td>
        <td width="1%" align="right">
		<? 
		//18Mar2008
		//Valida que el Horario no se encuentre en asignaciones
		$hayAsignacion= 0;
		$vSqlHP="select count(*) existeHor from asignaciones ";
		$vSqlHP=$vSqlHP." where id_proyecto = " . $reg4[id_proyecto] ;
		$vSqlHP=$vSqlHP." and IDhorario = " .$reg4[IDhorario] ;
		$vCursorHP = mssql_query($vSqlHP);
		if ($vRegHP=mssql_fetch_array($vCursorHP)) {  
			$hayAsignacion= $vRegHP[existeHor];
		}
		
		if (($cantHorProy > 1) AND ($hayAsignacion == 0)) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHTHorarioProy.php?cualProyecto=<? echo $cualProyecto; ?>&cualHorario=<? echo $reg4[IDhorario] ; ?>','dHP','scrollbars=yes,resizable=yes,width=400,height=200')" /></a>
		<? } ?>
		</td>
	  </tr>
	  <? } ?>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TxtTabla"><input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addHTHorarioProy.php?cualProyecto=<? echo $cualProyecto ?>','vHP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Agregar Horario al Proyecto" /></td>
      </tr>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">.: DIAS Y HORAS LABORALES DEL PROYECTO</td>
      </tr>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">
			<table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Filtro de consulta </td>
      </tr>
    </table>
      
      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
	  <form name="form1" id="form1" method="post" action="">
        <tr>
          <td width="25%" class="TituloTabla">Vigencia</td>
          <td class="TxtTabla">
		  <select name="pAno" class="CajaTexto" id="pAno" onchange="document.form1.submit()" >
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

    </select>		  </td>
        </tr>
		</form>
      </table></td>
  </tr>
</table>
			</td>
          </tr>
        </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="20%">Vigencia</td>
        <td width="20%">Mes</td>
        <td width="20%">Oficina</td>
        <td width="20%">Campo</td>
        <td width="20%">D&iacute;as laborales </td>
        <td width="1%">&nbsp;</td>
      </tr>
	  <?
		while ($reg05=mssql_fetch_array($cursor05)) {  
	  ?>
      <tr class="TxtTabla">
        <td width="20%"><? echo  $reg05[vigencia] ; ?></td>
        <td width="20%">
		<? 
		if ($reg05[mes] == 1) {
			echo "Enero"; 		
		}
		if ($reg05[mes] == 2) {
			echo "Febrero"; 		
		}
		if ($reg05[mes] == 3) {
			echo "Marzo"; 		
		}
		if ($reg05[mes] == 4) {
			echo "Abril"; 		
		}
		if ($reg05[mes] == 5) {
			echo "Mayo"; 		
		}
		if ($reg05[mes] == 6) {
			echo "Junio"; 		
		}
		if ($reg05[mes] == 7) {
			echo "Julio"; 		
		}
		if ($reg05[mes] == 8) {
			echo "Agosto"; 		
		}
		if ($reg05[mes] == 9) {
			echo "Septiembre"; 		
		}
		if ($reg05[mes] == 10) {
			echo "Octubre"; 		
		}
		if ($reg05[mes] == 11) {
			echo "Noviembre"; 		
		}
		if ($reg05[mes] == 12) {
			echo "Diciembre"; 		
		}

		
		?></td>
        <td width="20%"><? echo  $reg05[hOficina] ; ?></td>
        <td width="20%"><? echo  $reg05[hCampo] ; ?></td>
        <td width="20%"><? echo  $reg05[diasLaborales] ; ?></td>
        <td width="1%"><?
		$phayHorProy = 0;
		$vhSql="select count(*) hayHorProy ";
		$vhSql=$vhSql." from HojaDeTiempo.dbo.HorariosProy ";
		$vhSql=$vhSql." where IDhorario =" . $reg05[IDhorario]  ;
		$vhCursor = mssql_query($vhSql);
		if ($vhReg=mssql_fetch_array($vhCursor)) {
			$phayHorProy = $vhReg[hayHorProy];
		}
		if ($phayHorProy == 0) {
		?>
          <a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upHTLaboralesProy.php?cualProyecto=<? echo $cualProyecto ?>&cualVigencia=<? echo $AnoActual; ?>&cualMes=<? echo $reg05[mes]; ?>','wUpLD','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
          <? } ?></td>
      </tr>
	  <? } ?>
    </table>
</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right">
		<?  if ($cantLaboralesproy == 0) { ?>
		<input name="Submit7" type="submit" class="Boton" onclick="MM_openBrWindow('addHTLaboralesProy.php?cualProyecto=<? echo $cualProyecto ?>&cualVigencia=<? echo $AnoActual; ?>','eHDl','scrollbars=yes,resizable=yes,width=500,height=300')" value="Ingresar Horas y d&iacute;as laborales del Proyecto" />
		<? } ?>
		</td>
      </tr>
    </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF" class="TxtTabla">&nbsp;		</td>
      </tr>
</table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;      </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
