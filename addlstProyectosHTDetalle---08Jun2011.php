<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//10Jul2007
//Traer la información del proyecto seleccionado
$sql="Select * ";
$sql=$sql." from Proyectos " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDProyecto = $reg[id_proyecto];
	$elProyecto = $reg[nombre];
	$elCodigo = $reg[codigo];
	$elCargoDef = $reg[cargo_defecto];
}



//--Traer las personas relacionadas a la facturación de un proyecto en un mes  y año especificos
$sql="Select H.unidad, U.nombre, U.apellidos, sum(horas_registradas) as totalHorasR ";
$sql=$sql." from horas H, usuarios U " ;
$sql=$sql." where H.unidad = U.unidad " ;
$sql=$sql." and H.id_proyecto = " . $cualProyecto ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql=$sql." and month(H.fecha) = month(getdate()) " ;
	$sql=$sql." and year(H.fecha) = year(getdate()) " ;
}
else {
	$sql=$sql." and month(H.fecha) = " . $pMes;
	$sql=$sql." and year(H.fecha) = " . $pAno;
}
//8Nov2007
//Si el proyecto es Gastos generales = 42, debe filtrar por los usuarios que asignaron la unidad activa
//como persona que revisará su proyecto de hoja de tiempo
if ($cualProyecto == 42) {
	$sql=$sql." and EXISTS " ;
	$sql=$sql." 	( " ;
	$sql=$sql." 	SELECT * FROM RevisaGastosGenerales " ;
	$sql=$sql." 	WHERE unidad = H.unidad " ;
	$sql=$sql." 	and  unidadRevisa = " . $laUnidad;
	$sql=$sql." 	) " ;
}
//fin 8NOv2007
$sql=$sql." group by H.unidad, U.nombre, U.apellidos " ;
$sql=$sql." order by U.apellidos " ;
$cursor = mssql_query($sql);
$CantItems = mssql_num_rows($cursor);

//Si se presionó el botón grabar
if ($pProyecto != "") {
	
	$s = 1;
	while ($s <= $pCantidadItem) {
		$laUnidadG= "pUnidad" . $s;
		$elTipoOperacion= "pOperacion" . $s;
		$laAprobacion = "rAP" . $s;
		$elCometario = "comenta" . $s;

		//Verifica si se va a insertar o a modificar y arma la cadena
		//si $elTipoOperacion = 0 Graba, $elTipoOperacion=1 Modifica
		if (${$elTipoOperacion} == 0) {
			//Inserta en AprobacionFacHT
			//id_proyecto, vigencia, mes, unidad, unidadEncargado, validaEncargado, comentaEncargado
			$query2 = "INSERT INTO AprobacionFacHT(id_proyecto, vigencia, mes, unidad, unidadEncargado, validaEncargado, comentaEncargado) ";
			$query2 = $query2 . " VALUES (" . $pProyecto . ", ";
			$query2 = $query2 . $pElAno . ", ";			
			$query2 = $query2 . $pElMes . ", ";
			$query2 = $query2 . ${$laUnidadG} . ", ";
			$query2 = $query2 . $laUnidad . ", ";
			$query2 = $query2 . " '" . ${$laAprobacion} . "', ";
			$query2 = $query2 . " '" . ${$elCometario} . "' ";
			$query2 = $query2 . " ) ";
		}

		if (${$elTipoOperacion} == 1) {
			//Actualiza en AprobacionFacHT
			//id_proyecto, vigencia, mes, unidad, unidadEncargado, validaEncargado, comentaEncargado
			$query2 = "UPDATE  AprobacionFacHT SET "; 
			$query2 = $query2 . " validaEncargado = '" .  ${$laAprobacion} . "',  ";
			$query2 = $query2 . " comentaEncargado = '" . ${$elCometario} . "',  ";
			$query2 = $query2 . " unidadEncargado = " . $laUnidad ;
			$query2 = $query2 . " WHERE id_proyecto =" . $pProyecto . " ";
			$query2 = $query2 . " AND vigencia = " . $pElAno ;
			$query2 = $query2 . " AND mes = " . $pElMes ;
			$query2 = $query2 . " AND unidad = " . ${$laUnidadG} ;		
		}
//echo $query2 . "<br><br>" ;		
		$cursor2 = mssql_query($query2) ;
//		echo $query2 . "<BR>"; 
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	if  (trim($cursor2) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('lstProyectosHTDetalle.php?cualProyecto=$pProyecto&pMes=$pElMes&pAno=$pElAno','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=*,height=*');</script>");



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
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Informaci&oacute;n del proyecto </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Nombre</td>
        <td width="10%">C&oacute;digo</td>
        <td width="10%">Cargo</td>
      </tr>
      <tr class="TxtTabla">
        <td><? echo strtoupper($elProyecto) ; ?></td>
        <td width="10%"><? echo $elCodigo ; ?></td>
        <td width="10%"><? echo $elCargoDef ; ?></td>
      </tr>
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
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo del Director o encargado </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<form name="Form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Unidad</td>
        <td>Usuarios que facturaron al Proyecto </td>
        <td>Horas Registradas </td>
        <td width="17%">Aprobado</td>
        <td>Comentarios</td>
        </tr>
       <?
	   $i= 1;
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?>
          <input name="pUnidad<? echo $i ; ?>" type="hidden" id="pUnidad<? echo $i ; ?>" value="<? echo $reg[unidad]; ?>" /></td>
        <td><? echo ucwords(strtolower($reg[apellidos] . " " . $reg[nombre])) ; ?></td>
        <td width="5%" align="center">
		<? echo $reg[totalHorasR]; ?>
		</td>
        <td width="17%" align="center">
		<?
		$laAprobacion = 0; 
		$elQueAprueba = "" ;
		$comentaAprueba = "" ;

		$sqlA="Select A.*, U.nombre, U.apellidos ";
		$sqlA=$sqlA." from AprobacionFacHT A, Usuarios U " ;
		$sqlA=$sqlA." where A.unidadEncargado *= U.unidad "  ;
		$sqlA=$sqlA." and A.id_proyecto = " . $elIDProyecto ;
		$sqlA=$sqlA." and A.unidad = " . $reg[unidad] ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sqlA=$sqlA." and A.mes = month(getdate()) " ;
			$sqlA=$sqlA." and A.vigencia = year(getdate()) " ;
		}
		else {
			$sqlA=$sqlA." and A.mes = " . $pMes;
			$sqlA=$sqlA." and A.vigencia = " . $pAno;
		}
		$cursorA = mssql_query($sqlA);
		$CantRegistros = mssql_num_rows($cursorA);
		if ($regA=mssql_fetch_array($cursorA)) {
			$laAprobacion = $regA[validaEncargado] ; 
			$elQueAprueba = $regA[nombre] . " " . $regA[apellidos] ;
			$comentaAprueba = $regA[comentaEncargado] ;
		}

		?>

				<? 
		if ($laAprobacion == "1") {
			$selSi = "checked";
			$selNo = "";
		}
		if (($laAprobacion == "0") OR (trim($laAprobacion) == ""))  {
			$selSi = "";
			$selNo = "checked";
		}
		?>		
		<input name="rAP<? echo $i; ?>" type="radio" value="1" <? echo $selSi ; ?> >
		Si &nbsp;&nbsp;&nbsp;
		<input name="rAP<? echo $i; ?>" type="radio" value="0" <? echo $selNo ; ?> >
		No
		<input name="pOperacion<? echo $i ; ?>" type="hidden" id="pOperacion<? echo $i ; ?>" value="<? echo $CantRegistros; ?>" />		
		</td>
        <td width="30%">
          <textarea name="comenta<? echo $i ; ?>" cols="40" class="CajaTexto" id="comenta<? echo $i ; ?>"><? echo $comentaAprueba; ?></textarea></td>
        </tr>
	  <? 
	  $i = $i + 1;
	  } ?>
    </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="pProyecto" type="hidden" id="pProyecto" value="<? echo $cualProyecto ; ?>" />
                <input name="pElMes" type="hidden" id="pElMes" value="<? echo $pMes; ; ?>" />
                <input name="pElAno" type="hidden" id="pElAno" value="<? echo $pAno ; ?>" />
                <input name="pCantidadItem" type="hidden" id="pCantidadItem" value="<? echo $CantItems; ?>" />
              <input name="Submit" type="submit" class="Boton" value="Grabar" /></td>
            </tr>
          </table>
		
		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		
		
		</td>
      </tr>
	  </form>
    </table>
</body>
</html>
