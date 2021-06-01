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

//4Mar2008
//Trae la información de la programación de ls asignación de recursos para el proyecto seleccionado y el usuario activo
//ProgAsignaRecursos
//id_proyecto, unidadProgramador, fechaInicio, plazo
$sql2="SELECT * FROM ProgAsignaRecursos ";
$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
$sql2=$sql2." and unidadProgramador =" . $laUnidad ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {	 
	$pfechaInicio = date("d M Y ", strtotime($reg2[fechaInicio])) ;
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

		/*
		echo ${$elpMes} . "<br>";
		echo ${$elpAno} . "<br>";
		echo ${$elhProg} . "<br>";
		echo ${$elhCosto} . "<br>";
		*/

		//Solo realiza la grabación si horas programadas > 0
		if(${$elhProg} > 0) {		
			//Inserta el registro
			//ProgAsignaRecursosUsu
			//id_proyecto, unidadProgramador, unidad, mes, vigencia, horasProgramadas, valorProgramado, salarioBase
			$query = "INSERT INTO ProgAsignaRecursosUsu(id_proyecto, unidadProgramador, unidad, mes, vigencia,   " ;
			$query = $query . " horasProgramadas, valorProgramado, salarioBase) ";
			$query = $query . " VALUES( " . $cualProyecto . ", " ;
			$query = $query . $laUnidad . ", ";	
			$query = $query . $pUsuario . ", ";	
			$query = $query . ${$elpMes} . ", ";			
			$query = $query . ${$elpAno} . ", ";	
			$query = $query . ${$elhProg}. ", ";	
			$query = $query . ${$elhCosto} . ", ";	
			$query = $query . $pSalario . ") ";	
			$cursor = mssql_query($query);
		}
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
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
var mHP, mHL, mCosto;

	mHP="hProg" + cItem;
	mHL="hLab" + cItem;
	mCosto="hCosto" + cItem;
	if (isNaN(document.Form1.elements[mHP].value)) {
		alert('Horas programadas debe ser numérico');
		document.Form1.elements[mCosto].value =  parseFloat(0);
		document.Form1.elements[mHP].value =  parseFloat(0);
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
    <td class="TxtTabla"><select name="pUsuario" class="CajaTexto" id="pUsuario" onChange="envia1()" >
      <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra los usuarios no retirados que aun no se encuentran en la programación de asignación de recursos para el proyecto y usuario activo
		$sql2="Select * from Usuarios   "  ;
		$sql2=$sql2." where retirado is null ";
		$sql2=$sql2." and not exists ";
		$sql2=$sql2." (select distinct unidad from ProgAsignaRecursosUsu ";
		$sql2=$sql2." where id_proyecto = " . $cualProyecto;
		$sql2=$sql2." and unidadProgramador= " . $laUnidad ;
		$sql2=$sql2." and unidad = Usuarios.unidad ";
		$sql2=$sql2." ) ";
		$sql2=$sql2." order by apellidos ";
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
			$pUsuario = $reg2a[unidad];
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
          <td width="30%">Horas laborales </td>
          <td width="30%">Costo <br>
            (h Prog / h Lab) * salario</td>
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
          <td align="center"><input name="hProg<? echo $e; ?>" type="text" class="CajaTexto" id="hProg<? echo $e; ?>" value="0" size="10" onBlur="calcularCosto(<? echo $e; ?>)"></td>
          <td width="30%" align="center">
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
		  <input name="hLab<? echo $e; ?>" type="text" class="CajaTexto" id="hLab<? echo $e; ?>" value="<? echo $pHorasLab; ?>" size="10" readonly>            
		  
		  </td>
          <td width="30%" align="center"><input name="hCosto<? echo $e; ?>" type="text" class="CajaTexto" value="0" size="25">            </td>
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
          <td align="right" class="TxtTabla">
		  <input name="cantItems" type="hidden" id="cantItems" value="<? echo $pplazo; ?>">
          <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()" >
		  </td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>
</body>
</html>
