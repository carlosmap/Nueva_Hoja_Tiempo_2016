<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador

$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador = C.unidad " ;
//Sólo aparece el listado total de proyectos para el administrador del sistema perfil Administrado = 1
if ($_SESSION["sesPerfilUsuario"] != "1") {
	$sql=$sql." AND (P.id_director = " . $laUnidad . " or P.id_coordinador=". $laUnidad .") " ;
}
if (trim($pNombre) != "") {
	$sql=$sql." and P.nombre like '%".trim($pNombre)."%' " ;
}
if (trim($pProyecto) == 2) {
	$sql=$sql." AND especial is not null " ;
}


if ($pOrden == 1) {
	$sql=$sql." ORDER BY P.nombre  " ;
}
else {
	$sql=$sql." ORDER BY P.codigo, P.cargo_defecto " ;
}
$cursor = mssql_query($sql);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempoSHoras";

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
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 448px; height: 30px;"> Solicitud de Programaci&oacute;n
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
        <td class="TituloUsuario">Criterios de consulta </td>
      </tr>
    </table>


    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellpadding="0" cellspacing="1">
    <form name="form1" id="form1" method="post" action="">	
      <tr>
        <td width="20%" class="TituloTabla">Ordenar por </td>
        <td colspan="3" class="TxtTabla">
		<?
		if ($pOrden == 1) {
			$selOrden1 = "checked";
			$selOrden2 = "";
		}
		else {
			$selOrden1 = "";
			$selOrden2 = "checked";
		}
		?>
		<input name="pOrden" type="radio" value="1" <? echo $selOrden1; ?>  onClick="document.form1.submit();" />
          Nombre 
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="pOrden" type="radio" value="2" <? echo $selOrden2; ?> onClick="document.form1.submit();" />
          C&oacute;digo</td>
        <td width="2%" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Proyectos</td>
        <td colspan="3" class="TxtTabla"><?
		if (($pProyecto == 1) or (trim($pProyecto) == "")) {
			$selP1 = "checked";
			$selP2 = "";
		}
		else {
			$selP1 = "";
			$selP2 = "checked";
		}
		?>
          <input name="pProyecto" type="radio" value="1" <? echo $selP1; ?>  onClick="document.form1.submit();" />
Todos &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="pProyecto" type="radio" value="2" <? echo $selP2; ?>   onClick="document.form1.submit();" />
Especial</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Nombre</td>
        <td colspan="3" class="TxtTabla"><input name="pNombre" type="text" class="CajaTexto" id="pNombre" size="70" /> </td>
        <td width="2%" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Mes</td>
        <td class="TxtTabla">
		<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
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
    </select>		</td>
        <td class="TituloTabla">A&ntilde;o</td>
        <td class="TxtTabla">
		<select name="pAno" class="CajaTexto" id="pAno">
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

    </select>
		</td>
        <td class="TxtTabla"><input name="Submit3" type="submit" class="Boton" value="Consultar" /></td>
      </tr>
    </form>	  
    </table></td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">   Revisar solicitud de programaci&oacute;n a cargo de <? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="3" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="5%">C&oacute;digo</td>
        <td>Proyectos</td>
        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td bgcolor="#FFFFFF">
			<table width="100%"  border="1" cellspacing="0" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="7%">Unidad</td>
            <td width="20%">Nombre</td>
            <td width="10%">Cant. Horas </td>
            <td>Comentarios solicitante </td>
            <td width="5%">Aprob</td>
            <td width="20%">Observaciones</td>
            <td width="1%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td width="5%">Para<br />
              Firma</td>
            <td width="7%">VoBo</td>
            <td width="15%">              Comentarios <br />
              VoBo</td>
          </tr>
        </table>			</td>
          </tr>
        </table>          </td>
        <td width="5%">&nbsp;</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
	   <tr class="TxtTabla">
	     <td width="5%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="70%" bgcolor="#FFFFFF" class="TxtTabla">
		<?
		$sSql="select S.* , U.apellidos, U.nombre,  J.apellidos apeJefe, J.nombre nomJefe ";
		$sSql=$sSql." from SolicitudHoras S, Usuarios U, Usuarios J " ;
		$sSql=$sSql." where S.unidad = U.unidad " ;
		$sSql=$sSql." and S.unidadJefe *= J.unidad " ;
		$sSql=$sSql." and S.mes = " . $mesActual;
		$sSql=$sSql." and S.vigencia = " . $AnoActual;
		$sSql=$sSql." and S.id_proyecto =" . $reg[id_proyecto] ;
		$sCursor = mssql_query($sSql);		
		?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td bgcolor="#FFFFFF">
			<table width="100%"  border="1" cellspacing="0" cellpadding="0">
		  <?  while ($sReg=mssql_fetch_array($sCursor)) {	  ?>
          <tr class="TxtTabla">
            <td width="7%"><? echo $sReg[unidad]; ?></td>
            <td width="20%"><? echo ucwords(strtolower($sReg[apellidos])) . ", " . ucwords(strtolower($sReg[nombre])) ; ?></td>
            <td width="10%" align="right"><? echo $sReg[cantidadHoras]; ?></td>
            <td>
			<? echo $sReg[comentario]; ?>&nbsp;
			</td>
            <td width="5%" align="center"><? if ( ($sReg[validaDirector] == "0") AND (trim($sReg[comentaDirector]) == "") ) {  ?>
&nbsp;
<? }  ?>
<? if ( ($sReg[validaDirector] == "0") AND (trim($sReg[comentaDirector]) != "") ) {  ?>
<img src="img/images/No.gif" alt="No aprobado" width="12" height="16" />
<? }  ?>
<? if ($sReg[validaDirector] == "1") {  ?>
<img src="img/images/Si.gif" alt="No aprobado" width="16" height="14" />
<? }  ?></td>
            <td width="20%"><? echo $sReg[comentaDirector]; ?>&nbsp;</td>
            <td width="1%">
			<? if ($sReg[validaDirector] != "1") {  ?>
			<a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upsolProgramacionDir.php?cualMes=<? echo $pMes; ?>&cualAno=<? echo $pAno; ?>&cualSec=<? echo $sReg[secuencia]; ?>','solDir','scrollbars=yes,resizable=yes,width=725,height=350')" /></a>
			<? } ?>
			</td>
			<td width="5%" align="center">
			<? 
			if ($sReg[requiereFirma] == "1")  {  
				echo "Si";
			}
			else {
				echo "No";
			}
			
			?>
			</td>
			<td width="7%" align="center">
			<? if ( ($sReg[validaJefe] == "0") AND (trim($sReg[comentaJefe]) != "") ) {  ?>
            <img src="img/images/NoAprobado.gif" alt="No Autorizado" width="20" height="22" />
<? }  ?>
<? if ($sReg[validaJefe] == "1") {  ?>
<img src="img/images/Aprobado.gif" width="21" height="24" />
<? }  ?>
			&nbsp;</td>
			<td width="15%">
			<? echo ucwords(strtolower($sReg[apeJefe])) . ", " . ucwords(strtolower($sReg[nomJefe])) ; ?>
			<br />
			<? echo $sReg[comentaJefe]; ?>&nbsp;</td>
			<? } ?>
          </tr>
        </table>
			</td>
          </tr>
        </table>		</td>
        <td width="5%">		<input name="Submit4" type="submit" class="Boton" onclick="MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=<? echo $reg[id_proyecto]; ?>','winHojaTiempo','scrollbars=yes,resizable=yes,width=600,height=400')" value="Programar Actividades" />		</td>
        </tr>
	  <? } ?>
	<? 
	//Para que este proyecto siempre le aparezca a Olga Lucia
	if ($laUnidad == 15320) { ?>
	 <? } ?> 
    </table>
		
</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10%"><a href="#"><img src="img/images/flechaAtras1.gif" alt="Regresar a solicitud de Programaci&oacute;n" width="50" height="44" border="0" onclick="MM_goToURL('parent','solProgramacion.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>');return document.MM_returnValue" /></a> </td>
    <td><input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;	</td>
  </tr>
</table>
</body>
</html>
