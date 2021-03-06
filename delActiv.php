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

//Trae la información de la actividad
$qSql="select * from Actividades ";
$qSql=$qSql." where id_proyecto = " . $cualProyecto ;
$qSql=$qSql." and id_actividad = " . $cualActividad;
$qCursor = mssql_query($qSql);
if ($qReg=mssql_fetch_array($qCursor)) {
	$pnombre = $qReg[nombre] ;
	$pFechaInicio = date("n/d/Y", strtotime($qReg[fecha_inicio])); 
	$pFechaFin = date("n/d/Y", strtotime($qReg[fecha_fin]));
	$pmacroactividad = $qReg[macroactividad] ;
	$pid_encargado = $qReg[id_encargado] ;
	$pavance_reportado = $qReg[avance_reportado] ;
	$presumen_avance = $qReg[resumen_avance] ;
}

//Traer la minima fecha inicial en asignaciones para la actividad seleccionada
$qSql2="SELECT MIN(fecha_inicial) minFechaI FROM asignaciones ";
$qSql2=$qSql2." WHERE id_proyecto = " . $cualProyecto ;
$qSql2=$qSql2." AND id_actividad =" . $cualActividad ;
$qCursor2 = mssql_query($qSql2);
if ($qReg2=mssql_fetch_array($qCursor2)) {
	$pMinFechaIni = date("n/d/Y", strtotime($qReg2[minFechaI]));  
}


//Trae la máxima fecha final en asignaciones para la actividad seleccionada
$qSql2="SELECT MAX(fecha_final) maxFechaF FROM asignaciones ";
$qSql2=$qSql2." WHERE id_proyecto = " . $cualProyecto ;
$qSql2=$qSql2." AND id_actividad =" . $cualActividad ;
$qCursor2 = mssql_query($qSql2);
if ($qReg2=mssql_fetch_array($qCursor2)) {
	$pMinFechaFin = date("n/d/Y", strtotime($qReg2[maxFechaF]));  
}

//Si se presionó el botón Grabar
if ($lNombre != "") {
	//Direcciona a la BD a donde va a grabar
	@mssql_select_db("HojaDeTiempo");
	
	//Realiza la eliminacióninserción de la actividad en la tabla Actividades
	$query = "DELETE FROM Actividades " ;
	$query = $query . " WHERE id_proyecto =" . $miProyecto ;
	$query = $query . " AND id_actividad = " . $miActividad ;
	//echo $query . "<br>";
	//exit;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	

}

//rENOMBRA LAS VARIABLES PARA NO PERDER EL VALOR CUANDO RECARGA Y ENTRA A GRABAR
$lFechaInicio = $pFechaInicio; 
$lFechaFin = $pFechaFin ;



?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="ts_picker.js"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function compareFechas() { 
//alert(document.Form1.lFechaInicio.value);
//alert(document.Form1.lFechaFin.value);
	fecha1=new Date(document.Form1.lFechaInicio.value); 
	fecha2=new Date(document.Form1.lFechaFin.value); 

	fIni = new Date(document.Form1.minfechaInicial.value);
	fFin = new Date(document.Form1.maxfechaFinal.value);
//alert(document.Form1.minfechaInicial.value);
//alert(document.Form1.maxfechaFinal.value);
	
	diferencia = fecha1 - fecha2; 
//  	alert(diferencia);
   	if (diferencia > 0) {
   		alert ("La fecha inicial es MAYOR que la fecha de finalización, por favor realice la corrección.");
		document.Form1.lFechaFin.value = "";
	}
		
	diferenciaIni = fecha1 - fIni;
   	if (diferenciaIni > 0) {
   		alert ("No puede poner esta fecha de inicio de actividad, hay facturación antes de esta fecha.");
		document.Form1.lFechaInicio.value = document.Form1.fechaIniActual.value ;
	}

	diferenciaFin = fFin - fecha2 ;
//	alert(diferenciaFin);	
   	if (diferenciaFin > 0) {
   		alert ("No puede poner esta fecha de fin de actividad, hay facturación despues de esta fecha");
		document.Form1.lFechaFin.value = document.Form1.fechaFinActual.value ;
	}
	
//      return 1; 
//   else if (diferencia < 0) 
//   		alert ("La fecha inicial es MENOR que la fecha de finalización ");
//      return -1; 
//   else 
//   	alert ("La fecha inicial es IGUAL que la fecha de finalización ");
//      return 0; 
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Actividades</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Nombre</td>
    <td class="TxtTabla"><input name="lNombre" type="text" class="CajaTexto" id="lNombre" value="<? echo $pnombre; ?>" size="70" readonly></td>
  </tr>
  <tr>
    <td class="TituloTabla">C&oacute;digo Macroactividad (m&aacute;ximo 6 caracteres) </td>
    <td class="TxtTabla"><input name="lMacroactividad" type="text" class="CajaTexto" id="lMacroactividad" value="<? echo $pmacroactividad; ?>" maxlength="6" readonly>
      <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>">
      <input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualActividad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha inicio actividad </td>
    <td class="TxtTabla">
	<input name="lFechaInicio" class="CajaTexto"  value="<? echo $lFechaInicio;?>" size="25"  readonly >
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha fin actividad </td>
    <td class="TxtTabla">
	<input name="lFechaFin" class="CajaTexto" value="<? echo $lFechaFin;?>" size="25"  readonly >
	</td>
  </tr>
  <tr>
    <td colspan="2" class="TituloTabla"><img src="img/images/Pixel.gif" width="4" height="4"></td>
    </tr>
  <tr>
    <td class="TituloTabla">Encargado actividad </td>
    <td class="TxtTabla"><select name="pJefe" class="CajaTexto" id="pJefe" readonly >
	<option value=""  >Sin Encargado</option>
      <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra todos los usuarios. 
//		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
//		$sql2="Select * from Usuarios where id_categoria <= 40 "  ;
		$sql2="Select * from Usuarios  "  ;
		$sql2=$sql2." where retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
			if ($pid_encargado == $reg2[unidad]) {
				$selUsu = "selected";
			}
			else {
				$selUsu = "";
			}
		?>
      <option value="<? echo $reg2[unidad]; ?>" <? echo $selUsu; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . " (".$reg2[unidad].") - ". $reg2[TipoContrato] ;  ?></option>
      <? } ?>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">&Uacute;ltimo Avance reportado </td>
    <td class="TxtTabla"><input name="lAvance" type="text" class="CajaTexto" id="lAvance" value="<? echo $pavance_reportado ; ?>" readonly=""></td>
  </tr>
  <tr>
    <td class="TituloTabla">Descripci&oacute;n del avance </td>
    <td class="TxtTabla"><textarea name="llDescripcion" cols="70" class="CajaTexto" id="llDescripcion" readonly><? echo $presumen_avance; ?></textarea></td>
  </tr>
  <tr align="center">
    <td colspan="2" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar esta actividad?</strong></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	<input name="fechaIniActual" type="hidden" id="fechaIniActual" value="<? echo $pFechaInicio; ?>">
      <input name="fechaFinActual" type="hidden"  value="<? echo $pFechaFin; ?>">      
	  <input name="maxfechaFinal" type="hidden"  value="<? echo $pMinFechaFin; ?>">    
	  <input name="minfechaInicial" type="hidden" id="minfechaInicial" value="<? echo $pMinFechaIni; ?>">
      <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
      <input name="Submit" type="submit" class="Boton" value="Borrar"></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
