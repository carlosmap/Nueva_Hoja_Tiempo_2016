<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador = C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

//14Mar2008
//Trae los Horarios asociados al proyecto seleccio
$sql2="SELECT p.id_proyecto, p.HorarioDefecto, p.ubicacion, h.*  " ;
$sql2=$sql2." FROM HorariosProy p, Horarios h " ;
$sql2=$sql2." Where p.IDhorario = h.IDhorario " ;
$sql2=$sql2." and id_proyecto =" . $cualProyecto ;
$cursor2 = mssql_query($sql2);
$cantHorProy = mssql_num_rows ($cursor2) ;

//14Mar2008
//Trae el listado de horarios disponibles, es decir todos aquellos que uun no han sido asociados al proyecto seleccionado
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo, h.localiza  " ;
$sql3=$sql3." FROM Horarios h " ;
$sql3=$sql3." Where Not Exists  " ;
$sql3=$sql3."   (SELECT * " ;
$sql3=$sql3."   FROM HorariosProy p " ;
$sql3=$sql3."   Where h.IDhorario = p.IDhorario " ;
$sql3=$sql3."   AND id_proyecto = " . $cualProyecto ;
$sql3=$sql3."   ) " ;
$cursor3 = mssql_query($sql3);

//31May2011
//Trae las horas y días laborales del proyecto
$sql04="SELECT * ";
$sql04=$sql04." FROM horasydiasLaboralesProy ";
$sql04=$sql04." WHERE id_proyecto = " . $cualProyecto ;
if ($pAno == "") {
	$sql04=$sql04." and vigencia =  year(getdate())";
}
else {
	$sql04=$sql04." and vigencia = " . $pAno;
}
$cursor04 = mssql_query($sql04);

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
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 560px; height: 30px;">
Programaci&oacute;n de proyectos - Horarios </div>
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
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="10%">ID</td>
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?>
		<? $codProyecto = trim($reg[codigo]) ;?></td>
        <td width="20%"><? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); ?></td>
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
            <td class="TituloUsuario">Horarios del proyecto </td>
          </tr>
        </table>
		</td>
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
		while ($reg2=mssql_fetch_array($cursor2)) {  
		$totHP = 0;
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg2[IDhorario] ; ?></td>
        <td width="5%">
		<? if (trim($reg2[HorarioDefecto]) == "1") { ?>
			<img src="img/images/Si.gif" alt="Horario Defecto" width="16" height="14" />
		<? } ?>
		</td>
        <td><? echo  ucfirst(strtolower($reg2[NomHorario])) ; ?></td>
        <td width="10%"><? 
		$origenLoc = "";
		if (trim($reg2[ubicacion]) != "") {
			$origenLoc = "Proyecto";
			if ($reg2[ubicacion]==1) {
				$pLocaliza=$reg2[ubicacion].". Oficina";
			}
			if ($reg2[ubicacion]==2) {
				$pLocaliza=$reg2[ubicacion].". Campo";
			}
			if ($reg2[ubicacion]==3) {
				$pLocaliza=$reg2[ubicacion].". Planilla";
			}
		}
		else {
			$origenLoc = "Horario";
			if ($reg2[localiza]==1) {
				$pLocaliza=$reg2[localiza].". Oficina";
			}
			if ($reg2[localiza]==2) {
				$pLocaliza=$reg2[localiza].". Campo";
			}
			if ($reg2[localiza]==3) {
				$pLocaliza=$reg2[localiza].". Planilla";
			}
		}
		echo $origenLoc . "<br>" . $pLocaliza;
		?></td>
        <td width="5%" align="right"><? echo  $reg2[Lunes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg2[Martes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg2[Miercoles] ; ?></td>
        <td width="5%" align="right"><? echo  $reg2[Jueves] ; ?></td>
        <td width="5%" align="right"><? echo  $reg2[Viernes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg2[Sabado] ; ?></td>
        <td width="5%" align="right"><? echo  $reg2[Domingo] ; ?></td>
        <td width="5%" align="right">
		<? 
		$totHP = $reg2[Lunes] + $reg2[Martes] + $reg2[Miercoles] + $reg2[Jueves] + $reg2[Viernes] + $reg2[Sabado] +  $reg2[Domingo];
		echo  $totHP ; ?></td>
        <td width="5%" align="right"><input type="button" name="Submit6" onclick="MM_openBrWindow('fechasEspecialesProy.php?cualHorario=<? echo $reg2[IDhorario];?>&cualProy=<? echo $cualProyecto; ?>','winFechas','scrollbars=yes,resizable=yes,width=600,height=400')" value="Fechas Especiales" class="Boton" /></td>
        <td width="1%" align="right">
		<? 
		//18Mar2008
		//Valida que el Horario no se encuentre en asignaciones
		$hayAsignacion= 0;
		$vSqlHP="select count(*) existeHor from asignaciones ";
		$vSqlHP=$vSqlHP." where id_proyecto = " . $reg2[id_proyecto] ;
		$vSqlHP=$vSqlHP." and IDhorario = " .$reg2[IDhorario] ;
		$vCursorHP = mssql_query($vSqlHP);
		if ($vRegHP=mssql_fetch_array($vCursorHP)) {  
			$hayAsignacion= $vRegHP[existeHor];
		}
		
		if (($cantHorProy > 1) AND ($hayAsignacion == 0)) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHorarioProy.php?cualProyecto=<? echo $cualProyecto; ?>&cualHorario=<? echo $reg2[IDhorario] ; ?>','dHP','scrollbars=yes,resizable=yes,width=400,height=200')" /></a>
		<? } ?>
		</td>
	  </tr>
	  <? } ?>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><input name="Submit5" type="submit" class="Boton" onclick="MM_openBrWindow('addHorarioProy.php?cualProyecto=<? echo $cualProyecto ?>','vHP','scrollbars=yes,resizable=yes,width=500,height=200')" value="Agregar Horario al Proyecto" /></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF" class="TxtTabla">&nbsp;		</td>
      </tr>
</table>
	
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">D&iacute;as y horas laborales del proyecto </td>
      </tr>
    </table><table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
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
		while ($reg04=mssql_fetch_array($cursor04)) {  
	  ?>
      <tr class="TxtTabla">
        <td width="20%"><? echo  $reg04[vigencia] ; ?></td>
        <td width="20%">
		<? 
		if ($reg04[mes] == 1) {
			echo "Enero"; 		
		}
		if ($reg04[mes] == 2) {
			echo "Febrero"; 		
		}
		if ($reg04[mes] == 3) {
			echo "Marzo"; 		
		}
		if ($reg04[mes] == 4) {
			echo "Abril"; 		
		}
		if ($reg04[mes] == 5) {
			echo "Mayo"; 		
		}
		if ($reg04[mes] == 6) {
			echo "Junio"; 		
		}
		if ($reg04[mes] == 7) {
			echo "Julio"; 		
		}
		if ($reg04[mes] == 8) {
			echo "Agosto"; 		
		}
		if ($reg04[mes] == 9) {
			echo "Septiembre"; 		
		}
		if ($reg04[mes] == 10) {
			echo "Octubre"; 		
		}
		if ($reg04[mes] == 11) {
			echo "Noviembre"; 		
		}
		if ($reg04[mes] == 12) {
			echo "Diciembre"; 		
		}

		
		?></td>
        <td width="20%"><? echo  $reg04[hOficina] ; ?></td>
        <td width="20%"><? echo  $reg04[hCampo] ; ?></td>
        <td width="20%"><? echo  $reg04[diasLaborales] ; ?></td>
        <td width="1%"><?
		$phayHorProy = 0;
		$vhSql="select count(*) hayHorProy ";
		$vhSql=$vhSql." from HojaDeTiempo.dbo.HorariosProy ";
		$vhSql=$vhSql." where IDhorario =" . $reg3[IDhorario]  ;
		$vhCursor = mssql_query($vhSql);
		if ($vhReg=mssql_fetch_array($vhCursor)) {
			$phayHorProy = $vhReg[hayHorProy];
		}
		if ($phayHorProy == 0) {
		?>
          <a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upLaboralesproy.php?cualProyecto=<? echo $cualProyecto ?>&cualVigencia=<? echo $AnoActual; ?>&cualMes=<? echo $reg04[mes]; ?>','wUpLD','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
          <? } ?></td>
      </tr>
	  <? } ?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><input name="Submit7" type="submit" class="Boton" onclick="MM_openBrWindow('addLaboralesproy.php?cualProyecto=<? echo $cualProyecto ?>&cualVigencia=<? echo $AnoActual; ?>','eHDl','scrollbars=yes,resizable=yes,width=500,height=300')" value="Ingresar Horas y d&iacute;as laborales" /></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Horarios disponibles </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="5%">C&oacute;digo</td>
        <td>Nombre</td>
        <td width="10%">Tipo N&oacute;mina<br />
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
        <td width="1%">&nbsp;</td>
      </tr>
      	<? 
		//SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo 		
		while ($reg3=mssql_fetch_array($cursor3)) {  
			$totHorario = 0 ;
		?>
	  <tr class="TxtTabla">
        <td width="5%"><? echo  $reg3[IDhorario] ; ?></td>
        <td><? echo  ucfirst(strtolower($reg3[NomHorario])) ; ?></td>
        <td width="10%">
		<? 
		if ($reg3[localiza]==1) {
			$pLocalizaH=$reg3[localiza].". Oficina";
		}
		if ($reg3[localiza]==2) {
			$pLocalizaH=$reg3[localiza].". Campo";
		}
		if ($reg3[localiza]==3) {
			$pLocalizaH=$reg3[localiza].". Planilla";
		}
		echo $pLocalizaH; ?>		</td>
        <td width="5%" align="right"><? echo  $reg3[Lunes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Martes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Miercoles] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Jueves] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Viernes] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Sabado] ; ?></td>
        <td width="5%" align="right"><? echo  $reg3[Domingo] ; ?></td>
        <td width="5%" align="right">
		<? 
		$totHorario = $reg3[Lunes] + $reg3[Martes] + $reg3[Miercoles] + $reg3[Jueves] + $reg3[Viernes] + $reg3[Sabado] +  $reg3[Domingo];
		echo  $totHorario ; ?></td>
        <td width="5%" align="right"><input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('fechasEspeciales.php?cualHorario=<? echo $reg3[IDhorario]; ?>','winFechas','scrollbars=yes,resizable=yes,width=600,height=400')" value="Fechas Especiales" /></td>
        <td width="1%" align="right">
		<?
		$phayHorProy = 0;
		$vhSql="select count(*) hayHorProy ";
		$vhSql=$vhSql." from HojaDeTiempo.dbo.HorariosProy ";
		$vhSql=$vhSql." where IDhorario =" . $reg3[IDhorario]  ;
		$vhCursor = mssql_query($vhSql);
		if ($vhReg=mssql_fetch_array($vhCursor)) {
			$phayHorProy = $vhReg[hayHorProy];
		}
		if ($phayHorProy == 0) {
		?>
		<a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upHorario.php?cualProyecto=<? echo $cualProyecto ?>&cualHorario=<? echo $reg3[IDhorario]; ?>','vupHor','scrollbars=yes,resizable=yes,width=640,height=200')" /></a>
		<? } ?>		</td>
	    <td width="1%" align="right">
		<? if ($phayHorProy == 0) { ?>
		<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delHorario.php?cualProyecto=<? echo $cualProyecto ?>&cualHorario=<? echo $reg3[IDhorario]; ?>','vdelHor','scrollbars=yes,resizable=yes,width=640,height=200')" /></a>
		<? } ?>
		</td>
	  </tr>
		<? } ?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addHorario.php?cualProyecto=<? echo $cualProyecto ?>','vadHor','scrollbars=yes,resizable=yes,width=640,height=200')" value="Nuevo Horario" /></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectos.php');return document.MM_returnValue" value="Lista de Proyectos" />
    <input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
