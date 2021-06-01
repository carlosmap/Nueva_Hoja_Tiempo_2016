<?
session_start();

//Si $cualAno viene vacio es porque no han cambiado las listas en la hoja de tiempo, 
//por lo tanto el mes activo es el actual
if (trim($cualAno) == "") {
	$anoAut=date("Y");
	$mesAut=date("m");
}
else {
	$anoAut=$cualAno;
	$mesAut=$cualMes;
}

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Verificar si el usuario ya existe para mostrar el jefe ya seleccionado
$sql="Select * from AutorizacionesHT ";
$sql=$sql." where vigencia = " . $anoAut;
$sql=$sql." and mes = " . $mesAut ;
//31Oct2007
//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
//se continua trabajando con la Unidad.
if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
	$sql=$sql." and unidad = " . $laUnidad;
}
else {
	$sql=$sql." and unidad = " . $_SESSION["sesUnidadUsuario"];
}
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elUsuarioJefe = $reg[unidadJefe];
}
//echo "La sesion=" . $_SESSION["sesUnidadUsuario"] . "<br>";
//echo "La unidad=" . $laUnidad . "<br>";

//Encontrar la categoria vigente para la selección de usuarios de la base de dato del portal
//@mssql_select_db("GestiondeInformacionDigital",$CONECTADO);
@mssql_select_db("GestiondeInformacionDigital");
$sql="Select * from CategoriaAutoriza";
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laCategoria = $reg[id_categoria];
	}
else {
	$laCategoria= 0;
}

//04Mar2011
//PBM
//Validar que la persona no haya grabado facturación antes de su fecha de ingreso o despues de la fecha de retiro

//Encontrar la fecha de ingreso y retiro del usuario
$vSql01="Select unidad, nombre, apellidos, fechaIngreso, fechaRetiro, ";
$vSql01=$vSql01." DAY(fechaIngreso) diaIngreso, MONTH(fechaIngreso) mesIngreso, YEAR(fechaIngreso) anoIngreso, ";
$vSql01=$vSql01." DAY(fechaRetiro) diaRetiro, MONTH(fechaRetiro) mesRetiro, YEAR(fechaRetiro) anoRetiro ";
$vSql01=$vSql01." from HojaDeTiempo.dbo.Usuarios ";
if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
	$vSql01=$vSql01." where Unidad =" . $laUnidad;
}
else {
	$vSql01=$vSql01." where Unidad =" . $_SESSION["sesUnidadUsuario"];
}
$vCursor01 = mssql_query($vSql01);
if ($vReg01=mssql_fetch_array($vCursor01)) {
	$pdiaIngreso=$vReg01[diaIngreso];
	$pmesIngreso=$vReg01[mesIngreso];
	$panoIngreso=$vReg01[anoIngreso];
	$pdiaRetiro=$vReg01[diaRetiro];
	$pmesRetiro=$vReg01[mesRetiro];
	$panoRetiro=$vReg01[anoRetiro];
	$pfechaIngreso=$vReg01[fechaIngreso];
	$pfechaRetiro=$vReg01[fechaRetiro];
}


//Encontrar la mínima y máxima fecha del mes y año de facturación activo
$vSql02="SELECT minFechaFac, maxFechaFac, ";
$vSql02=$vSql02." DAY(minFechaFac) diaMinFact, MONTH(minFechaFac) mesMinFact, YEAR(minFechaFac) anoMinFact, ";
$vSql02=$vSql02." DAY(maxFechaFac) diaMaxFact, MONTH(maxFechaFac) mesMaxFact, YEAR(maxFechaFac) anoMaxFact ";
$vSql02=$vSql02." FROM ";
$vSql02=$vSql02." ( ";
$vSql02=$vSql02." Select MIN(fecha) minFechaFac, MAX(fecha) maxFechaFac ";
$vSql02=$vSql02." from HojaDeTiempo.dbo.horas ";
if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
	$vSql02=$vSql02." where unidad = " . $laUnidad;
}
else {
	$vSql02=$vSql02." where unidad = " . $_SESSION["sesUnidadUsuario"];
}
$vSql02=$vSql02." and MONTH(fecha)=".$mesAut;
$vSql02=$vSql02." and YEAR(fecha)=".$anoAut;
$vSql02=$vSql02." ) A ";
$vCursor02 = mssql_query($vSql02);
if ($vReg02=mssql_fetch_array($vCursor02)) {
	$pminFechaFac=$vReg02[minFechaFac];
	$pmaxFechaFac=$vReg02[maxFechaFac];
	$pdiaMinFact=$vReg02[diaMinFact];
	$pmesMinFact=$vReg02[mesMinFact];
	$panoMinFact=$vReg02[anoMinFact];
	$pdiaMaxFact=$vReg02[diaMaxFact];
	$pmesMaxFact=$vReg02[mesMaxFact];
	$panoMaxFact=$vReg02[anoMaxFact];
}

//Verifica si el mes actual es igual al de ingreso para comparar primer día de facturación contra ingreso
$validaIngreso="NO";
if (($pmesIngreso == $mesAut) AND ($panoIngreso == $anoAut)) {
	//Valida que la facturación no esté antes del dia de ingreso
	if ($pdiaMinFact < $pdiaIngreso) {
		$validaIngreso="SI";
		$MensajeIngreso="No puede facturar antes de su fecha de ingreso. Por favor corrija la información.";
	}
	else {
		$MensajeIngreso="";
	}
}

//Verifica si el mes actual es igual al de retiro para comparar el ultimo día contra el retiro
$validaRetiro="NO";
if (($pmesRetiro == $mesAut) AND ($panoRetiro == $anoAut)) {
	//Valida que la facturación no esté despues del dia de retiro
	if ($pdiaMaxFact > $pdiaRetiro) {
		$validaRetiro="SI";
		$MensajeRetiro="No puede facturar despues de su fecha de retiro. Por favor corrija la información.";
	}
	else {
		$MensajeRetiro="";
	}
}

echo "pmesIngreso" . $pmesIngreso . "<br>";
echo "pmesRetiro" . $pmesRetiro . "<br>";
echo "mesAut" . $mesAut . "<br>";

echo "panoIngreso" . $panoIngreso . "<br>";
echo "panoRetiro" . $panoRetiro . "<br>";
echo "anoAut" . $anoAut . "<br>";

echo "pdiaMinFact" . $pdiaMinFact . "<br>";
echo "pdiaIngreso" . $pdiaIngreso . "<br>";
echo "pdiaMaxFact" . $pdiaMaxFact . "<br>";
echo "pdiaRetiro" . $pdiaRetiro . "<br>";


echo "validaIngreso" . $validaIngreso . "<br>";
echo "validaRetiro" . $validaRetiro . "<br>";

//OJO PENDIENTE VALIDAR Y CERRAR LA VENTANA
// Cierra 04Mar2011


//Si se presionó el botón Grabar
if ($elAno != "") {
	//Verifica si el registro ya existe en la tabla AutorizacionsHT para 
	//Determinar si se inserta o se modifica.
	@mssql_select_db("HojaDeTiempo");
	$cuantosHay = 0;
	$sql1="Select count(*) hayRegistros ";
	$sql1=$sql1." from AutorizacionesHT ";
	$sql1=$sql1." where vigencia = " . $elAno;
	$sql1=$sql1." and mes = " . $elMes ;
	//31Oct2007
	//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
	//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
	//se continua trabajando con la Unidad.
	if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
		$sql1=$sql1." and unidad = " . $laUnidad;
	}
	else {
		$sql1=$sql1." and unidad = " . $_SESSION["sesUnidadUsuario"];
	}

	$cursor1 = mssql_query($sql1);
	if ($reg1=mssql_fetch_array($cursor1)) {
		$cuantosHay = $reg1[hayRegistros];
	}
	
	if ($cuantosHay == 0) {
		$query = "INSERT INTO AutorizacionesHT(vigencia, mes, unidad, unidadJefe, fechaEnvio)  " ;
		$query = $query . " VALUES (" . $elAno . ", ";
		$query = $query . $elMes . " , ";

		//31Oct2007
		//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
		//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
		//se continua trabajando con la Unidad.
		if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
			$query = $query . $laUnidad . " , ";	
		}
		else {
			$query = $query . $_SESSION["sesUnidadUsuario"] . " , ";	
		}
		$query = $query . $pJefe . ", ";		
		$query = $query . " '" . gmdate ("n/d/y") . "' ";		
		$query = $query . " ) ";
	}
	else {
		$query = "UPDATE  AutorizacionesHT SET "; 
		$query = $query . " unidadJefe = " . $pJefe . ",  ";
		$query = $query . " fechaEnvio = '" . gmdate ("n/d/y") . "'  ";
		$query = $query . " WHERE vigencia = " . $elAno ;
		$query = $query . " AND mes = " . $elMes ;
		//31Oct2007
		//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
		//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
		//se continua trabajando con la Unidad.
		if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
			$query = $query . " AND unidad = " .  $laUnidad ;
		}
		else {
			$query = $query . " AND unidad = " . $_SESSION["sesUnidadUsuario"] ;
		}

	}
	$cursor = mssql_query($query) ;	

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close()</script>");
}


?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo - Envio a Autorizaci&oacute;n del Jefe </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<? 
	//Valida si muestra o no el formulario de envia al jefe
	if ( ($validaIngreso=="NO") AND ($validaRetiro=="NO") ) {
	?>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<form action="" method="post" name="Form1" id="Form1">
  <tr>
    <td width="25%" class="TituloTabla">A&ntilde;o</td>
    <td class="TxtTabla">
	<? echo $anoAut ; ?>
	<input name="elAno" type="hidden" id="elAno" value="<? echo $anoAut ; ?>">
	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Mes</td>
    <td class="TxtTabla">
	<? echo $mesAut ; ?>
	<input name="elMes" type="hidden" id="elMes" value="<? echo $mesAut ; ?>">
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla">
	<?
	if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
		echo $laUnidad;
	}
	else {
		echo $_SESSION["sesUnidadUsuario"];
	}
	?>
	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Jefe que autoriza </td>
    <td class="TxtTabla"><select name="pJefe" class="CajaTexto" id="pJefe" >
            <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra todos los usuarios. 
//		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
		$sql2="Select * from Usuarios where id_categoria <= 40 "  ;
		$sql2=$sql2." and retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
			if ($elUsuarioJefe == $reg2[unidad]) {
				$selJefe = "selected";
			}
			else {
				$selJefe = "";
			}
		?>
            <option value="<? echo $reg2[unidad]; ?>" <? echo $selJefe; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
    </tr>
  </form>
</table>
<? }
else { ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TxtTabla"><strong>IMPORTANTE:</strong></td>
  </tr>
  <tr>
    <td class="TxtTabla"><? echo $MensajeIngreso; ?></td>
  </tr>
  <tr>
    <td class="TxtTabla"><? echo $MensajeRetiro; ?></td>
  </tr>
  <tr>
    <td class="TxtTabla"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close();')" value="Cerrar"></td>
  </tr>
</table>
<? } ?>
	</td>
  </tr>
</table>

</body>
</html>
