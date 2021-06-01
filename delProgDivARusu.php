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

//Trae la información de la programación de ls asiganción de recursos para al proyecto seleccionado y el usuario activo
//id_proyecto, unidadProgramador, fechaInicio, plazo, valorSumaGlobal
$sql2="SELECT * FROM ProgAsignaRecursos ";
$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
$sql2=$sql2." and unidadProgramador =" . $laUnidad ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {	 
	$pfechaInicio = date("M d Y ", strtotime($reg2[fechaInicio])) ;
	$pplazo = $reg2[plazo];
	$pMesInicial = date("n", strtotime($reg2[fechaInicio])) ;
	$pAnoInicial = date("Y", strtotime($reg2[fechaInicio])) ;
}


//$recarga = 2 si se presionó el botón Grabar
if ($recarga == "2") {
	
	$s = 1;
	while ($s <= $cantItems) {
		$elpMes="pMes". $s;
		$elpAno="pAno". $s;
		$elhProg="hProg". $s;
		$elhCosto="hCosto". $s;
		$queHacer="cualOperacion".$s;
		$paraBorrar="pItemElm".$s;

		//Solo realiza la eliminación si horas programadas > 0
		if(${$elhProg} > 0) {		
			//Elimina el registro de acuerdo al valor establecido en $paraBorrar
			//ProgAsignaRecursosUsu
			//id_proyecto, unidadProgramador, unidad, mes, vigencia, horasProgramadas, valorProgramado, salarioBase
			if (${$paraBorrar} == "1") {
				$query = "DELETE FROM ProgAsignaRecursosUsu " ;
				$query = $query . " WHERE id_proyecto =" . $cualProyecto ;
				$query = $query . " AND unidadProgramador=". $laUnidad ;
				$query = $query . " AND unidad=" . $cualUnidad;
				$query = $query . " AND mes=". ${$elpMes} ;
				$query = $query . " AND vigencia=" . ${$elpAno};
				$cursor = mssql_query($query);
//				echo $query . "<br>";
//				exit;
			}
		}
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La operación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgDivisionRec.php?cualProyecto=$cualProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--
function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}

function envia2(){ 
document.Form1.recarga.value="2";
document.Form1.submit();
}

function calcularCosto(cItem) {
var mHP, mHL, mCosto, mHPorig, mCostoOrig;

	mHP="hProg" + cItem;
	mHPorig="hProgOrig" + cItem;
	mHL="hLab" + cItem;
	mCosto="hCosto" + cItem;
	mCostoOrig="hCostoOrig" + cItem;
	
	if (isNaN(document.Form1.elements[mHP].value)) {
		alert('Horas programadas debe ser numérico');
		document.Form1.elements[mCosto].value =  parseFloat(document.Form1.elements[mCostoOrig].value);
		document.Form1.elements[mHP].value =  parseFloat(document.Form1.elements[mHPorig].value);
	}
	else {
		if (parseFloat(document.Form1.elements[mHL].value) == 0) {
			document.Form1.elements[mCosto].value =  parseFloat(0);
			document.Form1.elements[mHP].value =  parseFloat(0);
		}
		else {
			document.Form1.elements[mCosto].value =  Math.round(((parseFloat(document.Form1.elements[mHP].value) / parseFloat(document.Form1.elements[mHL].value)) * parseFloat(document.Form1.pSalario.value)));		
		};
	};
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>

<title>Programaci&oacute;n de Asignaci&oacute;n de recursos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Asignaci&oacute;n de recursos - Empleados que participan </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Personal</td>
    <td class="TxtTabla"><select name="pUsuario" class="CajaTexto" id="pUsuario" disabled >
      <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra los usuarios no retirados que aun no se encuentran en la programación de suma global para el proyecto y usuario activo
		$sql2="Select * from Usuarios   "  ;
		$sql2=$sql2." where retirado is null ";
		$sql2=$sql2." and unidad =" . $cualUnidad;
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
			if ($pUsuario == $reg2[unidad]) {
				$selUsu = "selected";
			}
			else {
				$selUsu = "";
			}
		?>
      <option value="<? echo $reg2[unidad]; ?>" <? echo $selUsu ; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre])) . " (".$reg2[unidad].") - ". $reg2[TipoContrato] ;  ?></option>
      <? } ?>
    </select>
      <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">
	  <input name="recarga" type="hidden" id="recarga" value="2">	  </td>
  </tr>
  <tr>
    <td class="TituloTabla">Salario</td>
    <td class="TxtTabla">
	<?
	if (trim($pUsuario) == "") {
		$cursor2a = mssql_query($sql2);
		if ($reg2a=mssql_fetch_array($cursor2a)) {
			$pUsuario = $cualUnidad;
		}
	}
	$sqlS="select * from usuariosSalario U ";
	$sqlS=$sqlS." where U.unidad = " . $pUsuario ;
	$sqlS=$sqlS." and U.fecha = (select max(fecha) maxFecha from usuariosSalario where unidad = U.unidad) ";
	$cursorS = mssql_query($sqlS);
	if ($regS=mssql_fetch_array($cursorS)) {
		$sSalarioUsu = $regS[salario];
	}
	else {
		$sSalarioUsu = 0;
		echo ("<script>alert('Para poder programar a esta persona se debe defirnir previamente un salario');</script>");
	}
	?>
	$ <? echo number_format($sSalarioUsu, 0, ',', '.');?> 
	<input name="pSalario" type="hidden" id="pSalario" value="<? echo $sSalarioUsu; ?>">	</td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla"><img src="img/images/Pixel.gif" width="4" height="4"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="15%">Mes</td>
          <td>Horas programadas </td>
          <td width="20%">Horas laborales </td>
          <td width="20%">Costo <br>
            (h Prog / h Lab) * salario</td>
          <td width="20%">&iquest;Borrar?</td>
        </tr>
			<? 
			$mesActual = $pMesInicial ;
			$anoActual = $pAnoInicial ;
			for ($e=1; $e<=$pplazo ; $e++) { 
				switch ($mesActual) {
				case 1:
					$nombreMes="Ene";
					break;
				case 2:
					$nombreMes="Feb";
					break;
				case 3:
					$nombreMes="Mar";
					break;
				case 4:
					$nombreMes="Abr";
					break;
				case 5:
					$nombreMes="May";
					break;
				case 6:
					$nombreMes="Jun";
					break;
				case 7:
					$nombreMes="Jul";
					break;
				case 8:
					$nombreMes="Ago";
					break;
				case 9:
					$nombreMes="Sep";
					break;
				case 10:
					$nombreMes="Oct";
					break;
				case 11:
					$nombreMes="Nov";
					break;
				case 12:
					$nombreMes="Dic";
					break;
				}
			?>
        <tr class="TxtTabla">
          <td width="15%"><? echo $nombreMes . "-" . $anoActual;  ?>
            <input name="pMes<? echo $e; ?>" type="hidden" id="pMes<? echo $e; ?>" value="<? echo $mesActual; ?>">
            <input name="pAno<? echo $e; ?>" type="hidden" id="pAno<? echo $e; ?>" value="<? echo $anoActual; ?>"></td>
          <td align="center">
		  <?
			$phorasProg = 0;
			$pvalorProg = 0;
			$miOpera="ADD";
			//Trae la programación almacenada
			$sqlP="select * from ProgAsignaRecursosUsu ";
			$sqlP=$sqlP." where id_proyecto =" . $cualProyecto ;
			$sqlP=$sqlP." and unidadProgramador = " . $laUnidad ;
			$sqlP=$sqlP." and unidad = " . $cualUnidad ;
			$sqlP=$sqlP." and mes =" . $mesActual ;
			$sqlP=$sqlP." and vigencia =" . $anoActual ;
			$cursorP = mssql_query($sqlP);
			if ($regP=mssql_fetch_array($cursorP)) {	 
				$phorasProg = $regP[horasProgramadas];
				$pvalorProg = $regP[valorProgramado];
				$miOpera="UP";
			}			

		  ?>
		  <input name="cualOperacion<? echo $e; ?>" type="hidden" id="cualOperacion<? echo $e; ?>" value="<? echo $miOpera; ?>">
		  <input name="hProg<? echo $e; ?>" type="text" class="CajaTexto" id="hProg<? echo $e; ?>" value="<? echo $phorasProg; ?>" size="10"  readonly>
		  <input name="hProgOrig<? echo $e; ?>" type="hidden" id="hProgOrig<? echo $e; ?>" value="<? echo $phorasProg; ?>"></td>
          <td width="20%" align="center">
		  <?
		  	$pHorasLab=0;
			//Encuentra las horas laborales del mes y año especificado
			$sqlHL="select * from horasydiaslaborales ";
			$sqlHL=$sqlHL." where vigencia = ". $anoActual;
			$sqlHL=$sqlHL." and mes =" . $mesActual ;
			$cursorHL = mssql_query($sqlHL);
			if ($regHL=mssql_fetch_array($cursorHL)) {	 
				$pHorasLab = $regHL[hOficina];
			}			
		  ?>
		  <input name="hLab<? echo $e; ?>" type="text" class="CajaTexto" id="hLab<? echo $e; ?>" value="<? echo $pHorasLab; ?>" size="10" disabled>            
		  
		  </td>
          <td width="20%" align="center"><input name="hCosto<? echo $e; ?>" type="text" class="CajaTexto" value="<? echo $pvalorProg; ?>" size="25" disabled>
            <input name="hCostoOrig<? echo $e; ?>" type="hidden" id="hCostoOrig<? echo $e; ?>" value="<? echo $pvalorProg; ?>"></td>
          <td width="20%" align="center"><input name="pItemElm<? echo $e; ?>" type="radio" value="1">
            Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
              <input name="pItemElm<? echo $e; ?>" type="radio" value="0" checked>
            No</td>
        </tr>
		<? 
			$mesActual = $mesActual + 1;
			if ($mesActual > 12) {
				$mesActual = 1;
				$anoActual = $anoActual + 1;
			}
		} ?>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" class="TxtTabla"><strong>&iquest;Est&aacute; seguro de eliminar la programaci&oacute;n de los registros marcados con Si en la columna Borrar?</strong></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
		    <input name="cualUnidad" type="hidden" id="cualUnidad" value="<? echo $cualUnidad; ?>">
		    <input name="cantItems" type="hidden" id="cantItems" value="<? echo $pplazo; ?>">
            <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
            <input name="Submit" type="submit" class="Boton" value="Eliminar"  >		  </td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>
</body>
</html>
