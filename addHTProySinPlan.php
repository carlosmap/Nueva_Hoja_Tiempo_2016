<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//--Trae el listado de proyectos que no tienen planeación
$sql01="SELECT *  ";
$sql01=$sql01." FROM Proyectos ";
$sql01=$sql01." WHERE id_estado = 2 ";
$sql01=$sql01." AND id_proyecto not in ( ";
$sql01=$sql01." 	SELECT DISTINCT id_proyecto ";
$sql01=$sql01." 	FROM PlaneacionProyectos  ";
$sql01=$sql01." 	WHERE unidad = " . $laUnidad ;
$sql01=$sql01." 	AND vigencia = " . $cualVigencia;
$sql01=$sql01." 	AND mes = "  . $cualMes;
//Este UNION es para que los Proyectos que ya tienen facturación no puedan eliminarse de la lista
$sql01=$sql01." 	UNION ";
$sql01=$sql01." 	SELECT DISTINCT id_proyecto ";
$sql01=$sql01." 	FROM FacturacionProyectos ";
$sql01=$sql01." 	WHERE unidad = " . $laUnidad ;
$sql01=$sql01." 	AND vigencia = " . $cualVigencia;
$sql01=$sql01." 	AND mes = "  . $cualMes;
$sql01=$sql01." ) ";
$sql01=$sql01." ORDER BY nombre ";
$cursor01 =	 mssql_query($sql01);

//Realiza la grabación si recarga está en 1
if(trim($recarga) == "1"){
	
	//Variables del proceso
	$msgGraba = "";
	$msgSinGraba = "";
	$cuantasSinGrabar=0;
	$cuantasGrabo=0;


	$s = 1;
	while ($s <= $pCantidadItem) {
		//Recoger las variables
		$elcProyecto = "cProyecto" . $s;
		$elbtnSelecciona = "btnSelecciona" . $s;
		

		//Si seleccionar está en Si elimina el registro y vuelve y lo graba
		//dbo.ProyectosSinPlaneacion
		//id_proyecto, unidad, vigencia, mes, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		if (trim(${$elbtnSelecciona}) == "S") {
			//Elimina el registro
			$qry01="DELETE FROM ProyectosSinPlaneacion ";
			$qry01=$qry01." WHERE unidad = " . $laUnidad ;
			$qry01=$qry01." AND mes = " . $miMes;
			$qry01=$qry01." AND vigencia = " . $miVigencia;
			$qry01=$qry01." AND id_proyecto = " . ${$elcProyecto} ;
			$cursorQry01 = mssql_query($qry01);
			
			//Realiza la inserción
			$qry02 = " INSERT INTO ProyectosSinPlaneacion ";
			$qry02 = $qry02 . " ( id_proyecto, unidad, vigencia, mes, usuarioCrea, fechaCrea) ";
			$qry02 = $qry02 . " VALUES ( ";
			$qry02 = $qry02 . " " . ${$elcProyecto} . ", ";		
			$qry02 = $qry02 . " " . $laUnidad . ", ";
			$qry02 = $qry02 . " " . $miVigencia . ", ";
			$qry02 = $qry02 . " " . $miMes . ", ";
			$qry02 = $qry02 . " " . $laUnidad . ", ";
			$qry02 = $qry02 . " '". gmdate ("n/d/Y") . "' ";
			$qry02 = $qry02 . " ) ";		
			$cursorQry02 = mssql_query($qry02);
			if  (trim($cursorQry02) != "")  {
				$cuantasGrabo=$cuantasGrabo+1;
			}
			
		}
		else {
			//Botta el registro. Por si acaso estaba grabado
			//Elimina el registro
			$qry01="DELETE FROM ProyectosSinPlaneacion ";
			$qry01=$qry01." WHERE unidad = " . $laUnidad ;
			$qry01=$qry01." AND mes = " . $miMes;
			$qry01=$qry01." AND vigencia = " . $miVigencia;
			$qry01=$qry01." AND id_proyecto = " . ${$elcProyecto} ;
			$cursorQry01 = mssql_query($qry01);
		}
		
		$s=$s+1;
	}
	
	//Acorde con las acciones realizadas muestra un mensaje y finaliza el proceso.	
	if ($cuantasGrabo > 0) {
		echo ("<script>alert('Se realizó la grabación de " . $cuantasGrabo . "  proyectos satisfactoriamente.');</script>");
	}
	else{
		echo ("<script>alert('No seleccionó ningún proyecto.');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('htFacturacion.php?pAno=$miVigencia&pMes=$miMes','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}


?>
<html>
<head>
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">


</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">.: PROYECTOS SIN FACTURACI&Oacute;N </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<form name="form1" method="post" action="">
  <tr>
    <td bgcolor="#FFFFFF">
	  
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td rowspan="2">Proyecto</td>
          <td colspan="2">&iquest;Seleccionar?</td>
        </tr>
        <tr class="TituloTabla2">
          <td width="5%">Si</td>
          <td width="5%">No</td>
        </tr>
		<?
		$r=1;
		while ($reg01 = mssql_fetch_array($cursor01)) {
		
			//Verifica si el proyecto ya está en los proyectos sin planeación 
			$hayProy=0;
			$sql02="SELECT COUNT(*) existeProy ";
			$sql02=$sql02." FROM ProyectosSinPlaneacion  ";
			$sql02=$sql02." WHERE id_proyecto = " . $reg01['id_proyecto'];
			$sql02=$sql02." AND unidad = " . $laUnidad ;
			$sql02=$sql02." AND mes = " . $cualMes;
			$sql02=$sql02." AND vigencia = " . $cualVigencia;
			$cursor02 = mssql_query($sql02);
			if ($reg02=mssql_fetch_array($cursor02)) {
				$hayProy = $reg02['existeProy'] ;
			}
			
			if ($hayProy > 0) {
				$chkSI="checked";
				$chkNO="";
			}
			else {
				$chkSI="";
				$chkNO="checked";
			}

		?>			
        <tr class="TxtTabla">
          <td><? echo "[" . $reg01['codigo'] . "." . $reg01['cargo_defecto'] . "] " . $reg01['nombre'];  ?>
          <input name="cProyecto<? echo $r; ?>" type="hidden" id="cProyecto<? echo $r; ?>" value="<? echo $reg01['id_proyecto']; ?>"></td>
          <td width="5%" align="center"><input name="btnSelecciona<? echo $r; ?>" type="radio" value="S" <? echo $chkSI; ?> ></td>
          <td width="5%" align="center"><input name="btnSelecciona<? echo $r; ?>" type="radio" value="N" <? echo $chkNO; ?> ></td>
        </tr>
		<? 
			$r=$r+1;
		} //Cierra while 01 ?>
		<tr align="right" class="TxtTabla">
          <td colspan="3"><input name="Submit2" type="submit" class="Boton" value="Enviar"></td>
          </tr>

      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">&nbsp;</td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">

  <tr>
    <td align="right" class="TxtTabla"><input name="miVigencia" type="hidden" id="miVigencia" value="<? echo $cualVigencia; ?>">
      <input name="miMes" type="hidden" id="miMes" value="<? echo $cualMes;; ?>">      
	  <input name="pCantidadItem" type="hidden" id="pCantidadItem" value="<? echo $r-1; ?>">    
	  <input name="recarga" type="hidden" id="recarga" value="1">
    </td>
  </tr>

</table>  	</td>
  </tr>
  </form>
</table>

</body>
</html>
