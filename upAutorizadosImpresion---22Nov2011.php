<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

/*****************
Gestion de las conexiones MySQL y SQLServer
*****************/

function conectarMySql(){
	if (!($conexionMySql=@mysql_connect("192.168.30.18:3306","root", "pl42imaf"))) {
		echo "No pudo establecerse conexión con la Base de Datos de Page Device";
		return 0;
		exit();
	}
	if (!@mysql_select_db("pcontrol",$conexionMySql)) {
		echo "No pudo establecerse conexión con la Base de Datos de Page Device";
		exit();
	}
	return $conexionMySql;
}
$connMySql = conectarMySql();

//Establecer la conexión a la base de datos (MS Sql Server)
include "funciones.php";
include "validaUsrBd.php";


/*
2010-10-27
Daniel Felipe Rentería Martínez
Edición Usuarios 
*/

/*
Datos de Proyecto
dbo.Proyectos
id_proyecto, codigo, cargo_defecto, nombre, id_director, 
id_coordinador, id_estado, especial, maxclase, codProyecto, 
idEmpresa, fechaCrea, descCargoDefecto
*/
$sql0 = " SELECT id_proyecto, codigo, cargo_defecto, nombre
FROM HojaDeTiempo.dbo.Proyectos ";
$sql0 = $sql0 . " WHERE id_proyecto = " . $cualProyecto;
$cursor0 = mssql_query($sql0);
if($reg0 = mssql_fetch_array($cursor0)){
	$nombreProyecto = $reg0['nombre'];
	$cargoDefecto = $reg0['codigo'] . "." . $reg0['cargo_defecto'];
	$idProyecto = $reg0['id_proyecto'];
}

/*
Usuarios de la Hoja de Tiempo
dbo.Usuarios
unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, 
email, ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, 
Seccion, NombreCorto, unidadJefe, fechaIngreso, fechaRetiro, idEmpresa, fechaNacimiento, 
verTarjeta, codTipoDoc, numDocumento, sexo, codUbicacion, id_departamentoANT
*/
$sql1 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios
WHERE retirado IS NULL ";
$sql1 = $sql1 . " AND unidad = " . $cualUnidad;
$cursor1 = mssql_query($sql1);

/*
Cargos del Proyecto
*/
$sql2 = " SELECT DISTINCT A.*, B.consecutivo, B.cargo_defecto, B.cargos_adicionales
FROM (
	SELECT id_proyecto, codigo, cargo_defecto AS cargo, descCargoDefecto AS descripcion, '0' AS esAdicional
	FROM Proyectos
	UNION
	SELECT A.id_proyecto, A.codigo, B.cargos_adicionales, B.descripcion, '1' AS esAdicional
	FROM Proyectos A, Cargos B
	WHERE A.id_proyecto = B.id_proyecto
) A, HojaDeTiempo.dbo.AutorizadosImpresionCargos B
WHERE A.id_proyecto *= B.id_proyecto
AND ( A.cargo *= B.cargo_defecto OR A.cargo *= B.cargos_adicionales ) ";
$sql2 = $sql2 . " AND A.id_proyecto = " . $idProyecto . " ";
$sql2 = $sql2 . " AND B.unidad = " . $cualUnidad . " ";
$cursor2 = mssql_query($sql2);

/*
Verificación: Estado Activo o Inactivo
*/
$sql3 = " SELECT estado FROM HojaDeTiempo.dbo.AutorizadosImpresion ";
$sql3 = $sql3 . " WHERE id_proyecto = " . $idProyecto . " ";
$sql3 = $sql3 . " AND unidad = " . $cualUnidad . " ";
$cursor3 = mssql_query($sql3);
if($reg3 = mssql_fetch_array($cursor3)){
	$estadoUsuario = $reg3['estado'];
}


/*****************
Grabación del Registro
*****************/
if($recarga == 2){
	
	//exit;
	
	$okGuardar = "Si";
	$okGuardarMySql = "Si";
	$queryLog = "";
	$queryLogMy = "";
	
	$cursorTran1 = mssql_query("BEGIN TRANSACTION");
	if(trim($cursorTran1) == ""){
		$okGuardar = "No";
	}
	
	$cursorTranMy1 = mysql_query("START TRANSACTION");
	if(trim($cursorTranMy1) == ""){
		$okGuardarMySql = "No";
	}
	
	/*
	0. Verifica que el usuario haya sido desactivado. 
	1. Si fue desactivado
		1.1 Borra los cargos en AutorizadosImpresionCargos (HojaDeTiempo) y en pc_user_bilcode (Page Device)
		1.2 Inactiva al usuario dentro del proyecto en AutorizadosImpresion (HT)
	2. Si no ha sido desactivado:
		2.1 Verifica los Cargos seleccionados en Si
			2.1.1 Verifica si el cargo ya existía anteriormente
				2.1.1.1 Si no existe, lo guarda en AutorizadosImpresionCargos y en pc_user_bilcode
		2.2 Verifica los Cargos seleccionados en No
			2.2.1 Verifica si el cargo ya existía anteriormente
				2.2.1.1 Si existe, lo borra de AutorizadosImpresionCargos y de pc_user_billcode
	*/
	for($i = 1; $i <= $cantCargos; $i++){
		$codigo = "codigo" . $i;
		$cargo = "cargo" . $i;
		$adicional = "adicional" . $i;
		$consecutivo = "consecutivo" . $i;
		$aplicaCargo = "aplicaCargo" . $i;
		
		//Usuario Desactivado
		if($aplicaUsuario == 0){
			//Si el cargo esta relacionado
			if(trim($$consecutivo) != ""){
				/*****
				MySql:
				- Busca el user_id de la persona en la tabla pc_user
				- Busca el bil_id del cargo en la tabla pc_bilcode
				- Elimina la relación Usuario - Cargo en la tabla pc_user_bcode
				*****/
				$sqlMy1 = " SELECT user_id FROM pc_user WHERE user_name = '" . $mail . "' ";
				$sqlMy1 = $sqlMy1 . " AND descrip = '" . $usuario . "' ";
				$cursorMy1 = mysql_query($sqlMy1);
				//echo $sqlMy1 . "<br>";
				if($regMy1 = mysql_fetch_array($cursorMy1)){
					$elIdUsuario = $regMy1['user_id'];
				}
				
				$sqlMy2 = " SELECT bil_id FROM pc_bilcode WHERE bil_code = '" . $$codigo . "." . $$cargo . "' ";
				$sqlMy2 = $sqlMy2 . " AND ident = '" . $idProyecto . "' ";
				//echo $sqlMy2 . "<br>";
				$cursorMy2 = mysql_query($sqlMy2);
				if($regMy2 = mysql_fetch_array($cursorMy2)){
					$elIdCentroCosto = $regMy2['bil_id'];
				}
				
				$sqlMy3 = " DELETE FROM pc_user_bcode ";
				$sqlMy3 = $sqlMy3 . " WHERE user_id =  " . $elIdUsuario . " ";
				$sqlMy3 = $sqlMy3 . " AND bil_id = " . $elIdCentroCosto . " ";
				//echo $sqlMy3 . "<br>";
				$cursorMy3 = mysql_query($sqlMy3);
				if(trim($cursorMy3) == ""){
					$okGuardarMySql = "No";
				}
				
				//Log de Auditoría
				$queryLogMy = $queryLogMy . $sqlMy3 . " *** ";
				
				/****
				Hoja De Tiempo
				- Eliminar al usuario de AutorizadosImpresionCargos
				- Si no hay cargos asociados al proyecto, hacer update al usuario para dejarlo en estado Inactivo
				****/
				
				$sqlIn1 = " DELETE FROM AutorizadosImpresionCargos ";
				$sqlIn1 = $sqlIn1 . " WHERE id_proyecto = " . $idProyecto . " ";
				$sqlIn1 = $sqlIn1 . " AND unidad = " . $usuario . " ";
				$sqlIn1 = $sqlIn1 . " AND consecutivo = " . $$consecutivo . " ";
				//echo $sqlIn1 . "<br>";
				$cursorIn1 = mssql_query($sqlIn1);
				if(trim($cursorIn1) == ""){
					$okGuardar = "No";
				}
				
				//Log de Auditoría
				$queryLog = $queryLog . $sqlIn1 . " *** ";
				
				$sqlIn2 = " SELECT COUNT(*) AS tieneCargos FROM AutorizadosImpresionCargos ";
				$sqlIn2 = $sqlIn2 . " WHERE id_proyecto = " . $idProyecto . " ";
				$sqlIn2 = $sqlIn2 . " AND unidad = " . $usuario . " ";
				//echo $sqlIn2 . "<br>";
				$cursorIn2 = mssql_query($sqlIn2);
				if($regIn2 = mssql_fetch_array($cursorIn2)){
					$tieneCargos = $regIn2['tieneCargos'];
				} else {
					$tieneCargos = -1;
				}
				
				if($tieneCargos == 0){
					$sqlIn3 = " UPDATE AutorizadosImpresion SET estado = 'I', ";
					$sqlIn3 = $sqlIn3 . " fechaDesautoriza = '" . date("m/d/Y H:i:s") . "', ";
					$sqlIn3 = $sqlIn3 . " fechaMod = '" . date("m/d/Y H:i:s") . "', ";
					$sqlIn3 = $sqlIn3 . " usuarioMod = " . $_SESSION["sesUnidadUsuario"] . " ";
					$sqlIn3 = $sqlIn3 . " WHERE id_proyecto = " . $idProyecto . " ";
					$sqlIn3 = $sqlIn3 . " AND unidad = " . $usuario . " ";
					//echo $sqlIn3 . "<br>";
					$cursorIn3 = mssql_query($sqlIn3);
					if(trim($cursorIn3) == ""){
						$okGuardar = "No";
					}
					
					//Log de Auditoría
					$queryLog = $queryLog . $sqlIn3 . " *** ";
					
				}
			}
		}
		// Usuario Habilitado
		else {
			//Cargo Aplica
			if($$aplicaCargo == 1){
				
				//Habilita al usuario, en caso de estar inhabilitado
				$sqlIn3a = " UPDATE AutorizadosImpresion SET estado = 'A', ";
				$sqlIn3a = $sqlIn3a . " fechaMod = '" . date("m/d/Y H:i:s") . "', ";
				$sqlIn3a = $sqlIn3a . " usuarioMod = " . $_SESSION["sesUnidadUsuario"] . " ";
				$sqlIn3a = $sqlIn3a . " WHERE id_proyecto = " . $idProyecto . " ";
				$sqlIn3a = $sqlIn3a . " AND unidad = " . $usuario . " ";
				$sqlIn3a = $sqlIn3a . " AND estado = 'I' ";
				//echo $sqlIn3a . "<br>";
				$cursorIn3a = mssql_query($sqlIn3a);
				if(trim($cursorIn3a) == ""){
					$okGuardar = "No";
				}
				
				//Log de Auditoría
				$queryLog = $queryLog . $sqlIn3a . " *** ";
				
				//Cargo no registrado
				if(trim($$consecutivo) == ""){
					/*****
					HojaDeTiempo
					- Registrar el cargo dentro de AutorizadosImpresionCargos
					*****/
					
					$sqlIn4 = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM AutorizadosImpresionCargos ";
					$sqlIn4 = $sqlIn4 . " WHERE id_proyecto = " . $idProyecto . " ";
					$sqlIn4 = $sqlIn4 . " AND unidad = " . $usuario . " ";
					//echo $sqlIn4 . "<br>";
					$cursorIn4 = mssql_query($sqlIn4);
					if($regIn4 = mssql_fetch_array($cursorIn4)){
						$elId = $regIn4['elId'] + 1;
					}
					
					$sqlIn5 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn5 = $sqlIn5 . " VALUES ( ";
					$sqlIn5 = $sqlIn5 . " " . $idProyecto . ", ";
					$sqlIn5 = $sqlIn5 . " " . $usuario . ", ";
					$sqlIn5 = $sqlIn5 . " " . $elId . ", ";
					if($$adicional == 0){
						$sqlIn5 = $sqlIn5 . " " . $$cargo . ", ";
						$sqlIn5 = $sqlIn5 . " NULL, ";
					} else if($$adicional == 1){
						$sqlIn5 = $sqlIn5 . " NULL, ";
						$sqlIn5 = $sqlIn5 . " " . $$cargo . ", ";
					}
					$sqlIn5 = $sqlIn5 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn5 = $sqlIn5 . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn5 = $sqlIn5 . " ) ";
					//echo $sqlIn5 . "<br>";
					$cursorIn5 = mssql_query($sqlIn5);
					if(trim($cursorIn5) == ""){
						$okGuardar = "No";
					}
					
					//Log de Auditoría
					$queryLog = $queryLog . $sqlIn5 . " *** ";
					
					//Actualiza
					$sqlIn5a = " UPDATE AutorizadosImpresion SET estado = 'A', ";
					$sqlIn5a = $sqlIn5a . " fechaMod = '" . date("m/d/Y H:i:s") . "', ";
					$sqlIn5a = $sqlIn5a . " usuarioMod = " . $_SESSION["sesUnidadUsuario"] . " ";
					$sqlIn5a = $sqlIn5a . " WHERE id_proyecto = " . $idProyecto . " ";
					$sqlIn5a = $sqlIn5a . " AND unidad = " . $usuario . " ";
					//echo $sqlIn5a . "<br>";
					$cursorIn5a = mssql_query($sqlIn5a);
					if(trim($cursorIn5a) == ""){
						$okGuardar = "No";
					}
					
					//Log de Auditoría
					$queryLog = $queryLog . $sqlIn5a . " *** ";
					
					/****
					MySql
					- Buscar el user_code y el bil_code
					- Guarda el cargo nuevo en pc_user_bcode
					****/
					$sqlMy4a = " SELECT user_id FROM pc_user WHERE user_name = '" . $mail . "' ";
					$sqlMy4a = $sqlMy4a . " AND descrip = '" . $usuario . "' ";
					$cursorMy4a = mysql_query($sqlMy4a);
					//echo $sqlMy4a . "<br>";
					if($regMy4a = mysql_fetch_array($cursorMy4a)){
						$elIdUsuario = $regMy4a['user_id'];
					}
					
					$sqlMy4b = " SELECT bil_id FROM pc_bilcode WHERE bil_code = '" . $$codigo . "." . $$cargo . "' ";
					$sqlMy4b = $sqlMy4b . " AND ident = '" . $idProyecto . "' ";
					//echo $sqlMy4b . "<br>";
					$cursorMy4b = mysql_query($sqlMy4b);
					if($regMy4b = mysql_fetch_array($cursorMy4b)){
						$elIdCentroCosto = $regMy4b['bil_id'];
					}
					
					$sqlMy5 = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
					$sqlMy5 = $sqlMy5 . " VALUES ( ";
					$sqlMy5 = $sqlMy5 . " " . $elIdUsuario . ", ";
					$sqlMy5 = $sqlMy5 . " " . $elIdCentroCosto . " ";
					$sqlMy5 = $sqlMy5 . " ) ";   
					//echo $sqlMy5 . "<br>";
					$cursorMy5 = mysql_query($sqlMy5);
					if(trim($cursorMy5) == ""){
						$okGuardarMySql = "No";
					}
					
					//Log de Auditoría
					$queryLogMy = $queryLogMy . $sqlMy5 . " *** ";
					
				}
			} 
			//Cargo no aplica
			else {
				//Si el cargo existe
				if(trim($$consecutivo) != ""){
					$sqlIn6 = " DELETE FROM AutorizadosImpresionCargos ";
					$sqlIn6 = $sqlIn6 . " WHERE id_proyecto = " . $idProyecto . " ";
					$sqlIn6 = $sqlIn6 . " AND unidad = " . $usuario . " ";
					$sqlIn6 = $sqlIn6 . " AND consecutivo = " . $$consecutivo . " ";
					//echo $sqlIn6 . "<br>";
					$cursorIn6 = mssql_query($sqlIn6);
					if(trim($cursorIn6) == ""){
						$okGuardar = "No";
					}
					
					//Log de Auditoría
					$queryLog = $queryLog . $sqlIn6 . " *** ";
					
					//Actualiza
					$sqlIn6a = " UPDATE AutorizadosImpresion SET estado = 'A', ";
					$sqlIn6a = $sqlIn6a . " fechaMod = '" . date("m/d/Y H:i:s") . "', ";
					$sqlIn6a = $sqlIn6a . " usuarioMod = " . $_SESSION["sesUnidadUsuario"] . " ";
					$sqlIn6a = $sqlIn6a . " WHERE id_proyecto = " . $idProyecto . " ";
					$sqlIn6a = $sqlIn6a . " AND unidad = " . $usuario . " ";
					//echo $sqlIn6a . "<br>";
					$cursorIn6a = mssql_query($sqlIn6a);
					if(trim($cursorIn6a) == ""){
						$okGuardar = "No";
					}
					
					//Log de Auditoría
					$queryLog = $queryLog . $sqlIn6a . " *** ";
					
					/*****
					MySql:
					- Busca el user_id de la persona en la tabla pc_user
					- Busca el bil_id del cargo en la tabla pc_bilcode
					- Elimina la relación Usuario - Cargo en la tabla pc_user_bcode
					*****/
					$sqlMy6 = " SELECT user_id FROM pc_user WHERE user_name = '" . $mail . "' ";
					$sqlMy6 = $sqlMy6 . " AND descrip = '" . $usuario . "' ";
					$cursorMy6 = mysql_query($sqlMy6);
					//echo $sqlMy6 . "<br>";
					if($regMy6 = mysql_fetch_array($cursorMy6)){
						$elIdUsuario = $regMy6['user_id'];
					}
					
					$sqlMy7 = " SELECT bil_id FROM pc_bilcode WHERE bil_code = '" . $$codigo . "." . $$cargo . "' ";
					$sqlMy7 = $sqlMy7 . " AND ident = '" . $idProyecto . "' ";
					//echo $sqlMy7 . "<br>";
					$cursorMy7 = mysql_query($sqlMy7);
					if($regMy7 = mysql_fetch_array($cursorMy7)){
						$elIdCentroCosto = $regMy7['bil_id'];
					}
					
					$sqlMy8 = " DELETE FROM pc_user_bcode ";
					$sqlMy8 = $sqlMy8 . " WHERE user_id =  " . $elIdUsuario . " ";
					$sqlMy8 = $sqlMy8 . " AND bil_id = " . $elIdCentroCosto . " ";
					//echo $sqlMy8 . "<br>";
					$cursorMy8 = mysql_query($sqlMy8);
					if(trim($cursorMy8) == ""){
						$okGuardarMySql = "No";
					}
					
					//Log de Auditoría
					$queryLogMy = $queryLogMy . $sqlMy8 . " *** ";
					
				}
			}
		}
		//echo "<br>";
	} 
	
	/*
	Almacenamiento en el Log de Auditoría
	Para facilitar la grabación, dentro del query del Log se suprimen las comillas simples
	*/
	$sqlInLog = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionLog ( id_proyecto, qryLog, qryLogPD, usuarioCrea, fechaCrea ) ";
	$sqlInLog = $sqlInLog . " VALUES ( ";
	$sqlInLog = $sqlInLog . " " . $idProyecto . ", ";
	$sqlInLog = $sqlInLog . "  '" . ereg_replace("'", "", $queryLog) . "', ";
	$sqlInLog = $sqlInLog . "  '" . ereg_replace("'", "", $queryLogMy) . "', ";
	$sqlInLog = $sqlInLog . " " . $_SESSION["sesUnidadUsuario"] . ", ";
	$sqlInLog = $sqlInLog . " '" . date("m/d/Y H:i:s") . "' ";
	$sqlInLog = $sqlInLog . " ) ";
	$cursorInLog = mssql_query($sqlInLog);
	
	if($okGuardar == "Si"){
		$cursorTranMy2 = mysql_query(" COMMIT ");
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");
		echo "<script>alert('La grabación se realizó con éxito')</script>";
	} else {
		$cursorTranMy2 = mysql_query(" ROLLBACK ");
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo "<script>alert('Error en la grabación')</script>";
	}
	echo "<script>window.close();</script>";
	echo ("<script>MM_openBrWindow('ProgProyectosImpresion.php?cualProyecto=$idProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}

?>
<html>
<head>
<title>Autorizaci&oacute;n de Impresiones</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript">

/*
Funcion que verifica que un campo numérico solo permita presionar las teclas numéricas, la tecla punto, la tecla backspace y la tecla tab
Funciona en IE y en Firefox
*/
function campoNumerico(evento){
	var tecla = (document.all)?evento.keyCode:evento.which;
	//alert(tecla);
	if(tecla != 8 && tecla != 0 && tecla != 46 && tecla != 13 && (tecla < 48 || tecla > 57)){
		return false;
	} else {
		return true;
	}
}

function seleccionarCargos(cantidad, opcion){
	var cantCar = parseInt(cantidad);
	var expr1 = '';
	var expr2 = '';
	for(var i = 1; i <= cantCar; i++){
		if(opcion == 1){
			expr1 = 'document.Form1.aplicaCargo' + i + '[0].checked = true';
			expr2 = 'document.Form1.aplicaCargo' + i + '[1].disabled = true';
		} else if(opcion == 0){
			expr1 = 'document.Form1.aplicaCargo' + i + '[1].disabled = false';
			expr2 = 'document.Form1.aplicaCargo' + i + '[1].checked = true';
		}
		eval(expr1);
		eval(expr2);
	}
}

function envia1(){
	document.Form1.recarga.value = 1;
	document.Form1.submit();
}

function envia2(){
	var error = 'n';
	var mensaje = '';
	
	/*
	Validación de la cantidad de cargos
	*/
	if(document.Form1.aplicaUsuario[0].checked == true){
		var cantCar = parseInt(document.Form1.cantCargos.value);
		var expr1 = '';
		var cantSeleccionados = 0;
		for(var i = 1; i <= cantCar; i++){
			expr1 = 'document.Form1.aplicaCargo' + i + '[0].checked';
			if(eval(expr1) == true){
				cantSeleccionados++;
			}
		}
		if(cantSeleccionados == 0){
			error = 's';
			mensaje = mensaje + 'Debe haber al menos un cargo seleccionado para el usuario. \n';
		}
	}
	
	//Finalización de la validación
	if(error == 's'){
		alert(mensaje);
	} else {
		document.Form1.recarga.value = 2;
		document.Form1.submit();
	}
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Autorizados para Impresi&oacute;n</td>
  </tr>
</table>

<form action="" method="post" name="Form1"  >
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Proyecto</td>
    <td class="TxtTabla"><? echo $idProyecto . " - [" . $cargoDefecto . "] - " . ucwords(strtolower($nombreProyecto)); ?></td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%"  cellspacing="1">
      <tr>
        <td colspan="3" class="TituloTabla2">Usuarios</td>
        </tr>
      <tr class="TituloTabla2">
        <td rowspan="2">Nombre</td>
        <td colspan="2">Aplica</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="1%">Si</td>
        <td width="1%">No</td>
      </tr>
      <? 
	  if($reg1 = mssql_fetch_array($cursor1)){ 
	  ?>
	  <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($reg1['unidad'] . " - " . $reg1['apellidos'] . " " . $reg1['nombre'])); ?>
          <input name="usuario" type="hidden" id="usuario" value="<? echo $reg1['unidad']; ?>">
          <input name="mail" type="hidden" id="mail" value="<? echo $reg1['email']; ?>"></td>
        <?
		$optUsuS = "";
		$optUsuN = "";
		if(trim($estadoUsuario) == "A"){
			$optUsuS = "checked";
		} else {
			$optUsuN = "checked";
		}
		?>
		<td align="center"><input name="aplicaUsuario" type="radio" value="1" <? echo $optUsuS; ?>></td>
        <td align="center"><input name="aplicaUsuario" type="radio" value="0" <? echo $optUsuN; ?>></td>
      </tr>
	  <? 
	  } 
	  ?>
      <tr class="TxtTabla">
        <td colspan="3">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%"  cellspacing="1">
      <tr>
        <td colspan="3" class="TituloTabla2">Cargos</td>
      </tr>
      <tr>
        <td colspan="3" class="TxtTabla"><table width="50%"  cellspacing="1" class="fondo">
            <tr>
              <td class="TituloTabla">Seleccionar todos los cargos </td>
              <td class="TxtTabla">Si
                  <input name="selCargo" type="radio" onClick="seleccionarCargos(<? echo mssql_num_rows($cursor2); ?>, 1)" value="1"></td>
              <td class="TxtTabla">No
                  <input name="selCargo" type="radio" onClick="seleccionarCargos(<? echo mssql_num_rows($cursor2); ?>, 0)" value="0" checked></td>
            </tr>
        </table></td>
      </tr>
      <tr class="TituloTabla2">
        <td rowspan="2">Nombre Cargo </td>
        <td colspan="2">Aplica</td>
      </tr>
      <tr class="TituloTabla2">
        <td width="1%">Si</td>
        <td width="1%">No</td>
      </tr>
      <? 
	  $j = 1;
	  while($reg2 = mssql_fetch_array($cursor2)){ 
	  ?>
      <tr class="TxtTabla">
        <td>
		<? 
		if($reg2['esAdicional'] == 0){
			echo "<b>" . ucwords(strtolower($reg2['codigo'] . "." . $reg2['cargo'] . " - " . $reg2['descripcion'])) . "</b>"; 
		} else {
			echo ucwords(strtolower($reg2['codigo'] . "." . $reg2['cargo'] . " - " . $reg2['descripcion'])); 
		}
		?>
		<input name="codigo<? echo $j; ?>" type="hidden" id="codigo<? echo $j; ?>" value="<? echo $reg2['codigo']; ?>">
		<input name="cargo<? echo $j; ?>" type="hidden" id="cargo<? echo $j; ?>" value="<? echo $reg2['cargo']; ?>">
		<input name="adicional<? echo $j; ?>" type="hidden" id="adicional<? echo $j; ?>" value="<? echo $reg2['esAdicional']; ?>">
		<input name="consecutivo<? echo $j; ?>" type="hidden" id="consecutivo<? echo $i; ?>" value="<? echo $reg2['consecutivo']; ?>">	</td>
        <?
		$optS = "";
		$optN = "";
		if(trim($reg2['consecutivo']) != ""){
			$optS = "checked";
		} else {
			$optN = "checked";
		}
		?>
		<td align="center"><input name="aplicaCargo<? echo $j; ?>" type="radio" value="1" <? echo $optS; ?>></td>
        <td align="center"><input name="aplicaCargo<? echo $j; ?>" type="radio" value="0" <? echo $optN; ?>></td>
      </tr>
      <? 
	  $j++;
	  } 
	  ?>
      <tr class="TxtTabla">
        <td colspan="3">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	<input name="cantCargos" type="hidden" id="cantCargos" value="<? echo mssql_num_rows($cursor2); ?>">
		<input name="recarga" type="hidden" id="recarga" value="1">
    <input name="Submit" type="button" class="Boton" onClick="envia2();" value="Grabar"></td>
  </tr>
</table>
  	</td>
  </tr>
</table>
</form>

<?
//Finaliza las conexiones a MySql y a SQL Server
mssql_close();
mysql_close();
?>

</body>
</html>
