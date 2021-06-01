
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//include("../verificaRegistro2.php");

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer la información del proyecto seleccionado
$sql0="select P.* , U.nombre nomDirector, U.apellidos apeDirector, C.nombre nomCoo, C.apellidos apeCoo ";
$sql0=$sql0." from proyectos P, Usuarios U, Usuarios C ";
$sql0=$sql0." where P.id_director *= U.unidad ";
$sql0=$sql0." and P.id_coordinador *= C.unidad ";
$sql0=$sql0." and P.id_proyecto = " . $cualProyecto ;
$cursor0 = mssql_query($sql0);
if ($reg0=mssql_fetch_array($cursor0)) {
	$elIDProyecto = $reg0[id_proyecto];
	$elProyecto = $reg0[nombre];
	$elCodigo = $reg0[codigo];
	$elCargoDef = $reg0[cargo_defecto];
	$nombreDirector = $reg0[nomDirector] . " " . $reg0[apeDirector] ;
	$nombreCoordinador = $reg0[nomCoo] . " " . $reg0[apeCoo] ;
}


/*
09MAR2011
PBM
TRAE EL LISTADO DE PERSONAS QUE HAN VIATICADO EN EL PROYECTO
*/
$sql="SELECT DISTINCT ViaticosProyecto.unidad, Usuarios.nombre, Usuarios.apellidos ";
$sql=$sql." FROM ViaticosProyecto, Usuarios ";
$sql=$sql." WHERE ViaticosProyecto.unidad = Usuarios.unidad ";
$sql=$sql." AND ViaticosProyecto.id_proyecto = " . $cualProyecto ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql=$sql." AND MONTH(ViaticosProyecto.FechaIni) = month(getdate()) ";
	$sql=$sql." AND YEAR(ViaticosProyecto.FechaIni) = year(getdate()) ";
}
else {
	$sql=$sql." AND MONTH(ViaticosProyecto.FechaIni) = " . $pMes;
	$sql=$sql." AND YEAR(ViaticosProyecto.FechaIni) =  " . $pAno;
}
$cursor = mssql_query($sql);
$CantItems = mssql_num_rows($cursor);



//Si se presionó el botón grabar
if ($recarga == "2") {
	


	$s = 1;
	while ($s <= $pCantidadItem) {
		$laUnidadG= "pUnidad" . $s;
		$elTipoOperacion= "pOperacion" . $s;
		$laAprobacion = "aVP" . $s;
		$elCometario = "comenta" . $s;

		//Verifica si se va a insertar o a modificar y arma la cadena
		//si $elTipoOperacion = 0 Graba, $elTipoOperacion=1 Modifica
		if (${$elTipoOperacion} == 0) {
			//Inserta en AprobacionViaticosHT
			//id_proyecto, vigencia, mes, unidad, unidadEncargado, validaEncargado, comentaEncargado, fechaAprueba
			$query2 = "INSERT INTO AprobacionViaticosHT(id_proyecto, vigencia, mes, unidad, unidadEncargado, validaEncargado, comentaEncargado, fechaAprueba) ";
			$query2 = $query2 . " VALUES (" . $cualProyecto . ", ";
			$query2 = $query2 . $pElAno . ", ";			
			$query2 = $query2 . $pElMes . ", ";
			$query2 = $query2 . ${$laUnidadG} . ", ";
			$query2 = $query2 . $laUnidad . ", ";
			$query2 = $query2 . " '" . ${$laAprobacion} . "', ";
			$query2 = $query2 . " '" . ${$elCometario} . "', ";
			$query2 = $query2 . " '" . gmdate ("m/d/Y") . "' ";
			$query2 = $query2 . " ) ";
		}


		if (${$elTipoOperacion} == 1) {
			//Actualiza en AprobacionViaticosHT
			//id_proyecto, vigencia, mes, unidad, unidadEncargado, validaEncargado, comentaEncargado, fechaAprueba
			$query2 = "UPDATE  AprobacionViaticosHT SET "; 
			$query2 = $query2 . " validaEncargado = '" .  ${$laAprobacion} . "',  ";
			$query2 = $query2 . " comentaEncargado = '" . ${$elCometario} . "',  ";
			$query2 = $query2 . " unidadEncargado = " . $laUnidad .  ", ";
			$query2 = $query2 . " fechaAprueba = '" . gmdate ("m/d/Y") . "'  ";
			$query2 = $query2 . " WHERE id_proyecto =" . $cualProyecto . " ";
			$query2 = $query2 . " AND vigencia = " . $pElAno ;
			$query2 = $query2 . " AND mes = " . $pElMes ;
			$query2 = $query2 . " AND unidad = " . ${$laUnidadG} ;		
		}
//echo $query2 . "<br><br>" ;		
		$cursor2 = mssql_query($query2) ;
//		echo $query2 . "<BR>"; 
		$s = $s + 1;
	}
//exit;
	//Si los cursores no presentaron problema
	if  (trim($cursor2) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('lstApruebaViaticosHT.php?cualProyecto=$cualProyecto&pMes=$pElMes&pAno=$pElAno','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=*,height=*');</script>");

}

?>
<html>
<head>
<title>Vi&aacute;ticos de los Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function envia2(){ 
var v='s';
if( document.getElementById('recarga').value == "" ){
v='n';
		msg = 'error. \n';
	}

if((v=='s'))
{
	document.form1.recarga.value="2";		

	
		document.form1.submit();
			}

else{
	alert (msg);
	
	}
}



function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Proyecto</td>
      </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="fondo"><table width="100%" cellspacing="1">
      <tr class="TituloTabla2">
        <td>Nombre del Proyecto </td>
        <td>Codigo - Cargo del Proyecto </td>
        <td>Director</td>
        <td>Coordinador</td>
        </tr>
      
      <tr class="TxtTabla">
        <td><? echo strtoupper($elProyecto); ?>          </td>
        <td><? echo $elCodigo .  "." . $elCargoDef; ?></td>
        <td><? echo strtoupper($nombreDirector); ?></td>
        <td><? echo strtoupper($nombreCoordinador); ?></td>
        </tr>
    </table>
    <br>
    <form name="form1" method="post" action="">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
        <td width="39%" class="TituloUsuario">Atenci&oacute;n: requiere seleccionar con aprobaci&oacute;n en si todos los vi&aacute;ticos? </td>
        
        <?  if($aprobado==1){
			 $sia="checked";}
			
			?>
          <td width="61%" class="TxtTabla"> Si<input name="aprobado" type="radio" class="CajaTexto" value="1" <? echo $sia;?>  onClick="document.form1.submit();"></td>
          
        </tr>
      </table>

</table>


<table width="100%" border="0" cellspacing="0" cellpadding="1">

  <tr>
    <td colspan="2" class="fondo">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>	
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    
    </table>
    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloUsuario">
        <td rowspan="2">Usuario que viatic&oacute; en el proyecto </td>
        <td colspan="2" align="center">Aprobado</td>
        <td width="30%" rowspan="2">Comentarios</td>
        </tr>
      <tr class="TituloUsuario">
        <td width="5%" align="center">Si</td>
        <td width="5%" align="center">No</td>
        </tr>
	  <?
	  $i= 1;
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TituloTabla">
        <td><? echo "[" . $reg[unidad] . "] " . $reg[nombre] . " " .  $reg[apellidos] ; ?>&nbsp;
		
		<input name="pUnidad<? echo $i ; ?>" type="hidden" id="pUnidad<? echo $i ; ?>" value="<? echo $reg[unidad]; ?>" />
		</td>
        <td width="5%" align="center">
		<?
		$laAprobacion = 0; 
		$comentaAprueba = "" ;

		$sqlA="SELECT * ";
		$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
		$sqlA=$sqlA." WHERE unidad = " . $reg[unidad] ;
		$sqlA=$sqlA." and id_proyecto =" . $cualProyecto ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlA=$sqlA." and mes = month(getdate()) " ;
			$sqlA=$sqlA." and vigencia = year(getdate()) " ;
		}
		else {
			$sqlA=$sqlA." and mes = " . $pMes;
			$sqlA=$sqlA." and vigencia = " . $pAno;
		}
		$cursorA = mssql_query($sqlA);
		$CantRegistros = mssql_num_rows($cursorA);
		if ($regA=mssql_fetch_array($cursorA)) {
			$laAprobacion = $regA[validaEncargado] ; 
			$comentaAprueba = $regA[comentaEncargado] ;
		}

		?>
		<? 
		if($aprobado==''){
		if ($laAprobacion == "1") {
			$selSi = "checked";
			$selNo = "";
		}
		if (($laAprobacion == "0") OR (trim($laAprobacion) == ""))  {
			$selSi = "";
			$selNo = "checked";
		}}
		else
		{
		if($aprobado=="1")
		{ $selSi = "checked";}}
		
		
		?>
		<input name="aVP<? echo $i; ?>" type="radio" class="CajaTexto" value="1" <? echo $selSi; ?>  ></td>
        <td width="5%" align="center"><input name="aVP<? echo $i; ?>" type="radio" class="CajaTexto" value="0" <? echo $selNo; ?> >
		<input name="pOperacion<? echo $i ; ?>" type="hidden" id="pOperacion<? echo $i ; ?>"  value="<? echo $CantRegistros; ?>" />
		</td>
        <td width="30%"><textarea name="comenta<? echo $i; ?>" cols="50" rows="4" class="CajaTexto" id="comenta<? echo $i; ?>"><? echo $comentaAprueba; ?></textarea></td>
        </tr>
      <tr>
        <td colspan="4">
		<?
		//Traer los viáticos asociados al proyecto tla cual como fueron registrados
	$sql4="SELECT ViaticosProyecto.IDsitio, ViaticosProyecto.IDfraccion, ViaticosProyecto.IDTipoViatico, ViaticosProyecto.unidad, ViaticosProyecto.id_actividad, 
		ViaticosProyecto.localizacion, ViaticosProyecto.cargo, ViaticosProyecto.FechaIni, ViaticosProyecto.FechaFin, ViaticosProyecto.Trayecto, 
		ViaticosProyecto.ObjetoComision, ViaticosProyecto.viaticoCompleto, Actividades.nombre AS nomActividad, TiposViatico.NomTipoViatico, 
		SitiosTrabajo.NomSitio, Usuarios.nombre AS nomUsuario, Usuarios.apellidos AS apeUsuario
		FROM ViaticosProyecto INNER JOIN
		Usuarios ON ViaticosProyecto.unidad = Usuarios.unidad INNER JOIN
		SitiosTrabajo ON ViaticosProyecto.IDsitio = SitiosTrabajo.IDsitio AND ViaticosProyecto.id_proyecto = SitiosTrabajo.id_proyecto INNER JOIN
		TiposViatico ON ViaticosProyecto.IDTipoViatico = TiposViatico.IDTipoViatico INNER JOIN
		Actividades ON ViaticosProyecto.id_proyecto = Actividades.id_proyecto AND ViaticosProyecto.id_actividad = Actividades.id_actividad ";
	$sql4= $sql4. " WHERE ViaticosProyecto.id_proyecto = " . $cualProyecto ; ;
	$sql4= $sql4. " AND ViaticosProyecto.Unidad = " . $reg[unidad];
	//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
	//sino con lo seleccionado en las listas mes y año
	if ($pMes == "") {
		$sql4= $sql4. " and MONTH(ViaticosProyecto.FechaIni) = month(getdate()) ";
		$sql4= $sql4. " and YEAR(ViaticosProyecto.FechaIni) = year(getdate()) ";
	}
	else {
		$sql4= $sql4. " and MONTH(ViaticosProyecto.FechaIni) = " . $pMes;
		$sql4= $sql4. " and YEAR(ViaticosProyecto.FechaIni) = " . $pAno;
	}
	$sql4= $sql4. " order by ViaticosProyecto.unidad ";
	@mssql_select_db("HojaDeTiempo",$conexion);
	$cursor4 = mssql_query($sql4);

		?>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TxtTabla">
        <td><strong>Actividad</strong></td>
        <td width="8%"><strong>Fecha Inicio </strong></td>
        <td width="8%"><strong>Fecha finalizaci&oacute;n </strong></td>
        <td width="10%"><strong>Sitio</strong></td>
        <td width="10%"><strong>Tipo de vi&aacute;tico </strong></td>
      </tr>
	  <?
	  while ($reg4=mssql_fetch_array($cursor4)) {
	  ?>
	  
      <tr class="TxtTabla">
        <td><? echo $reg4[nomActividad]; ?></td>
        <td width="8%"><? echo date("M d Y ", strtotime($reg4[FechaIni])); ?></td>
        <td width="8%"><? echo date("M d Y ", strtotime($reg4[FechaFin])); ?></td>
        <td width="10%"><? echo $reg4[NomSitio]; ?></td>
        <td width="10%"><? echo $reg4[NomTipoViatico]; ?></td>
      </tr>
	  <? } ?>
    </table>		</td>
        </tr>
	  <? 
	  $i = $i + 1;
	  } ?>
    </table>	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" valign="bottom">

	<input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto ; ?>" />
                <input name="pElMes" type="hidden" id="pElMes" value="<? echo $pMes; ; ?>" />
                <input name="pElAno" type="hidden" id="pElAno" value="<? echo $pAno ; ?>" />
      <input name="pCantidadItem" type="hidden" id="pCantidadItem" value="<? echo $CantItems; ?>" />	
     
           <input name="recarga" type="hidden" id="recarga" value="1">       
          <input name="Submit2" type="button" class="Boton" value="Grabar" onClick="envia2()"></td>
  </tr>
 
</table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
</table>
 </form>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
