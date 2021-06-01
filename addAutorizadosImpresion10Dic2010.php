<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();


//Establecer la conexión a la base de datos (MS Sql Server)
include "funciones.php";
include "validaUsrBd.php";

$connMySql = conectarMySql();


/*
2010-10-25
Daniel Felipe Rentería Martínez
Adición Usuarios 
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
Divisiones de la Hoja de Tiempo
dbo.Divisiones
id_division, nombre, id_director, id_dependencia, id_subdirector, codigoDAF, estadoDiv
*/
$sql1 = " SELECT * FROM HojaDeTiempo.dbo.Divisiones
WHERE estadoDiv = 'A' ";
$cursor1 = mssql_query($sql1);
if($division == 0){
	$dpto = 0;
} else {
	$nombre = "";
	$unidad = "";
}

/*
Departamentos de la Hoja de Tiempo
dbo.Departamentos
id_departamento, nombre, id_director, id_division, codDpto, codigoDAF, estadoDpto, claseDpto
*/
$sql2 = " SELECT * FROM HojaDeTiempo.dbo.Departamentos
WHERE estadoDpto = 'A' ";
$sql2 = $sql2 . " AND id_division = " . $division;
//echo $sql2 . "<br>";
$cursor2 = mssql_query($sql2);

/*
Usuarios de la Hoja de Tiempo
dbo.Usuarios
unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, 
email, ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, 
Seccion, NombreCorto, unidadJefe, fechaIngreso, fechaRetiro, idEmpresa, fechaNacimiento, 
verTarjeta, codTipoDoc, numDocumento, sexo, codUbicacion, id_departamentoANT
*/
$sql3 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios
WHERE retirado IS NULL ";
if($dpto != 0){
	$sql3 = $sql3 . " AND id_departamento = " . $dpto;
}
if(isset($unidad) && trim($unidad) != ""){
	$sql3 = $sql3 . " AND unidad = " . $unidad;
}
if(isset($nombre) && trim($nombre) != ""){
	$sql3 = $sql3 . " AND ( nombre LIKE '%" . $nombre . "%' ";
	$sql3 = $sql3 . " OR apellidos LIKE '%" . $nombre . "%') ";
}

$sql3 = $sql3 . " AND unidad NOT IN ";
$sql3 = $sql3 . " ( SELECT unidad FROM HojaDeTiempo.dbo.AutorizadosImpresion WHERE id_proyecto = " . $idProyecto . ") ";
$sql3 = $sql3 . " ORDER BY apellidos ";
//echo $sql3 . "<br>";
$cursor3 = mssql_query($sql3);

$sql4 = " SELECT *
FROM (
SELECT A.id_proyecto, A.nombre, A.codigo, A.cargo_defecto, A.descCargoDefecto, '0' esAdicional
FROM HojaDeTiempo.dbo.Proyectos A
where A.id_estado = 2
and A.id_proyecto not in (
	SELECT id_proyecto FROM Proyectos
	where especial = 1
	and LEN(codigo) > 2
)
UNION
select A.id_proyecto, A.nombre, A.codigo, B.cargos_adicionales, B.descripcion, '1' esAdicional
from HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Cargos B
where A.id_proyecto = B.id_proyecto
and A.id_estado = 2
and A.id_proyecto not in (
	SELECT id_proyecto FROM Proyectos
	where especial = 1
	and LEN(codigo) > 2
) 
) X ";
$sql4 = $sql4 . " WHERE X.id_proyecto = " . $idProyecto . " ";
$sql4 = $sql4 . " ORDER BY X.id_proyecto ";
$cursor4 = mssql_query($sql4);

/*****************
Grabación del Registro
*****************/
if($recarga == 2){
	
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
	
	for($i = 1; $i <= $cantUsuarios; $i++){
		
		$usuario = "usuario" . $i;
		$mail = "mail" . $i;
		$aplicaUsuario = "aplicaUsuario" . $i;
	
		/*
		Usuarios Autorizados para Imprimir en los Proyectos
		dbo.AutorizadosImpresion
		id_proyecto, unidad, fechaAutoriza, fechaDesautoriza, estado, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		*/
		if($$aplicaUsuario == 1){
			$sqlIn1 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
			$sqlIn1 = $sqlIn1 . " VALUES ( ";
			$sqlIn1 = $sqlIn1 . " " . $idProyecto . ", ";
			$sqlIn1 = $sqlIn1 . " " . $$usuario . ", ";
			$sqlIn1 = $sqlIn1 . " '" . date("m/d/Y H:i:s") . "', ";
			$sqlIn1 = $sqlIn1 . " 'A', ";
			$sqlIn1 = $sqlIn1 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlIn1 = $sqlIn1 . " '" . date("m/d/Y H:i:s") . "' ";
			$sqlIn1 = $sqlIn1 . " ) ";
			$cursorIn1 = mssql_query($sqlIn1);
			//echo $sqlIn1 . "<br>";
			if(trim($cursorIn1) == ""){
				$okGuardar = "No";
			}
			
			//Log de Auditoría
			$queryLog = $queryLog . $sqlIn1 . " *** ";
			
			/********
			MySql:
			1. Consulta en la tabla pc_user si el nombre del usuario ya está establecido como impresor. Si no se encuentra, se almacena la información
			en la tabla con valores por defecto
			2. Obtiene el valor user_id y lo asocia para posteriormente relacionarlo con los cargos autorizados
			********/
			$sqlMy1 = " SELECT user_id FROM pc_user WHERE user_name = '" . $$mail . "' ";
			$sqlMy1 = $sqlMy1 . " AND descrip = '" . $$usuario . "' ";
			$cursorMy1 = mysql_query($sqlMy1);
			if($regMy1 = mysql_fetch_array($cursorMy1)){
				$elIdUsuario = $regMy1['user_id'];
			} else {
				$elIdUsuario = 0;
			}
			if($elIdUsuario == 0){
				$sqlMy2 = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
				$sqlMy2 = $sqlMy2 . " VALUES ( ";
				$sqlMy2 = $sqlMy2 . " '" . $$mail . "', ";
				$sqlMy2 = $sqlMy2 . " '" . $$usuario . "', ";
				$sqlMy2 = $sqlMy2 . " 1 ";
				$sqlMy2 = $sqlMy2 . " ) ";
				//echo $sqlMy2 . "<br>";
				$cursorMy2 = mysql_query($sqlMy2);
				if(trim($cursorMy2) == ""){
					$okGuardarMySql = "No";
				}
				
				//Log de Auditoría
				$queryLogMy = $queryLogMy . $sqlMy2 . " *** ";
				
				//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
				$sqlMy3 = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
				$cursorMy3 = mysql_query($sqlMy3);
				if($regMy3 = mysql_fetch_array($cursorMy3)){
					$elIdUsuario = $regMy3['elIdUsuario'];
				}
			}
			
			
			//Recorrido por el array de cargos para almacenar los cargos para el usuario correspondiente
			for($j = 1; $j <= $cantCargos; $j++){
				
				$codigo = "codigo" . $j;
				$cargo = "cargo" . $j;
				$adicional = "adicional" . $j;
				$aplicaCargo = "aplicaCargo" . $j;
				
				if($$aplicaCargo == 1){
					/*
					Cargos Autorizados por los usuarios 
					dbo.AutorizadosImpresionCargos
					id_proyecto, unidad, consecutivo, cargo_defecto, cargos_adicionales, 
					usuarioCrea, fechaCrea, usuarioMod, fechaMod
					*/
					$sqlIn2 = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM AutorizadosImpresionCargos ";
					$sqlIn2 = $sqlIn2 . " WHERE id_proyecto = " . $idProyecto . " ";
					$sqlIn2 = $sqlIn2 . " AND unidad = " . $$usuario . " ";
					$cursorIn2 = mssql_query($sqlIn2);
					if($regIn2 = mssql_fetch_array($cursorIn2)){
						$elId = $regIn2['elId'] + 1;
					}
					
					$sqlIn3 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn3 = $sqlIn3 . " VALUES ( ";
					$sqlIn3 = $sqlIn3 . " " . $idProyecto . ", ";
					$sqlIn3 = $sqlIn3 . " " . $$usuario . ", ";
					$sqlIn3 = $sqlIn3 . " " . $elId . ", ";
					if($$adicional == 0){
						$sqlIn3 = $sqlIn3 . " " . $$cargo . ", ";
						$sqlIn3 = $sqlIn3 . " NULL, ";
					} else if($$adicional == 1){
						$sqlIn3 = $sqlIn3 . " NULL, ";
						$sqlIn3 = $sqlIn3 . " " . $$cargo . ", ";
					}
					$sqlIn3 = $sqlIn3 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn3 = $sqlIn3 . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn3 = $sqlIn3 . " ) ";
					$cursorIn3 = mssql_query($sqlIn3);
					//echo $sqlIn3 . "<br>";
					if(trim($cursorIn3) == ""){
						$okGuardar = "No";
					}
					
					//Log de Auditoría
					$queryLog = $queryLog . $sqlIn3 . " *** ";
					
					/*****************
					MySql:
					1. Busca el codigo y el cargo en el campo bil_code dentro de la tabla pc_bilcode
					2. Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
					*****************/
					$sqlMy4 = " SELECT bil_id FROM pc_bilcode WHERE bil_code = '" . $$codigo . "." . $$cargo . "' ";
					$sqlMy4 = $sqlMy4 . " AND ident = '" . $idProyecto . "' ";
					//echo $sqlMy4 . "<br>";
					$cursorMy4 = mysql_query($sqlMy4);
					if($regMy4 = mysql_fetch_array($cursorMy4)){
						$elIdCentroCosto = $regMy4['bil_id'];
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
		}
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
	//exit;
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

function seleccionarUsuarios(cantidad, opcion){
	var cantUsu = parseInt(cantidad);
	var expr1 = '';
	var expr2 = '';
	for(var i = 1; i <= cantUsu; i++){
		if(opcion == 1){
			expr1 = 'document.Form1.aplicaUsuario' + i + '[0].checked = true';
			expr2 = 'document.Form1.aplicaUsuario' + i + '[1].disabled = true';
		} else if(opcion == 0){
			expr1 = 'document.Form1.aplicaUsuario' + i + '[1].disabled = false';
			expr2 = 'document.Form1.aplicaUsuario' + i + '[1].checked = true';
		}
		eval(expr1);
		eval(expr2);
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
	
	//Verifica que haya seleccionado una división y un departamento
	if(document.Form1.division.selectedIndex == 0 || document.Form1.dpto.selectedIndex == 0){
		error = 's';
		mensaje = mensaje + 'Debe seleccionar una división y un departamento. \n';
	}
	/* 
	Si no hay usuarios en la lista, muestra la advertencia. Si los hay, verifica que haya al menos un 
	*/
	if(document.Form1.cantUsuarios.value == ''){
		error = 's';
		mensaje = mensaje + 'Debe haber al menos un usuario en el listado de usuarios. \n';
	} else {
		var cantUsu = parseInt(document.Form1.cantUsuarios.value);
		var expr1 = '';
		var cantSeleccionados = 0;
		for(var i = 1; i <= cantUsu; i++){
			expr1 = 'document.Form1.aplicaUsuario' + i + '[0].checked';
			if(eval(expr1) == true){
				cantSeleccionados++;
			}
		}
		if(cantSeleccionados == 0){
			error = 's';
			mensaje = mensaje + 'Debe haber al menos un usuario seleccionado como autorizado para imprimir. \n';
		}
	}
	
	/*
	Validación de la cantidad de cargos
	*/
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
		mensaje = mensaje + 'Debe haber al menos un cargo seleccionado para los usuarios autorizados. \n';
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
    <td class="TituloTabla">Divisi&oacute;n</td>
    <td class="TxtTabla"><select name="division" class="CajaTexto" id="division" onChange="envia1();">
		<option value="0">::: Seleccione una División :::</option>
	<?
	while($reg1 = mssql_fetch_array($cursor1)){ 
		$optDiv = "";
		if($division == $reg1['id_division']){
			$optDiv = "selected";
		}
	?>
		<option value="<? echo $reg1['id_division']; ?>" <? echo $optDiv; ?>><? echo ucwords(strtolower($reg1['nombre'])); ?></option>
	<? } ?>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Departamento</td>
    <td class="TxtTabla"><select name="dpto" class="CajaTexto" id="dpto" onChange="envia1();">
		<option value="0">::: Seleccione un Departamento :::</option>
      <?
		while($reg2 = mssql_fetch_array($cursor2)){ 
			$optDpto = "";
			if($dpto == $reg2['id_departamento']){
				$optDpto = "selected";
			}
		?>
		  <option value="<? echo $reg2['id_departamento']; ?>" <? echo $optDpto; ?>><? echo ucwords(strtolower($reg2['nombre'])); ?></option>
		  <? } ?>
    </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombre</td>
    <td class="TxtTabla"><input name="nombre" type="text" class="CajaTexto" id="nombre" value="<? echo $nombre; ?>" size="50"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla"><input name="unidad" type="text" class="CajaTexto" id="unidad" onKeyPress="return campoNumerico(event);" value="<? echo $unidad; ?>" size="10"></td>
  </tr>
  <tr align="right">
    <td colspan="2" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="envia1();" value="Consultar"></td>
    </tr>
  <tr>
    <td colspan="2"><table width="100%"  cellspacing="1">
      <tr>
        <td colspan="3" class="TituloTabla2">Usuarios</td>
        </tr>
      <tr>
        <td colspan="3" class="TxtTabla"><table width="50%"  cellspacing="1" class="fondo">
          <tr>
            <td class="TituloTabla">Seleccionar todos los usuarios </td>
            <td class="TxtTabla">Si 
              <input name="selUsu" type="radio" onClick="seleccionarUsuarios(<? echo mssql_num_rows($cursor3); ?>, 1)" value="1"></td>
            <td class="TxtTabla">No 
              <input name="selUsu" type="radio" onClick="seleccionarUsuarios(<? echo mssql_num_rows($cursor3); ?>, 0)" value="0" checked></td>
          </tr>
        </table></td>
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
	  if($dpto != 0 || trim($nombre) != "" || trim($unidad) != ""){
		  $i = 1;
		  while($reg3 = mssql_fetch_array($cursor3)){ 
	  ?>
	  <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($reg3['unidad'] . " - " . $reg3['apellidos'] . " " . $reg3['nombre'])); ?>
          <input name="usuario<? echo $i; ?>" type="hidden" id="usuario<? echo $i; ?>" value="<? echo $reg3['unidad']; ?>">
          <input name="mail<? echo $i; ?>" type="hidden" id="mail<? echo $i; ?>" value="<? echo $reg3['email']; ?>"></td>
        <td align="center"><input name="aplicaUsuario<? echo $i; ?>" type="radio" value="1"></td>
        <td align="center"><input name="aplicaUsuario<? echo $i; ?>" type="radio" value="0" checked></td>
      </tr>
	  <? 
		  $i++;
		  } 
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
                  <input name="selCargo" type="radio" onClick="seleccionarCargos(<? echo mssql_num_rows($cursor4); ?>, 1)" value="1"></td>
              <td class="TxtTabla">No
                  <input name="selCargo" type="radio" onClick="seleccionarCargos(<? echo mssql_num_rows($cursor4); ?>, 0)" value="0" checked></td>
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
	  while($reg4 = mssql_fetch_array($cursor4)){ 
	  ?>
      <tr class="TxtTabla">
        <td>
		<? 
		if($reg4['esAdicional'] == 0){
			echo "<b>" . ucwords(strtolower($reg4['codigo'] . "." . $reg4['cargo_defecto'] . " - " . $reg4['descCargoDefecto'])) . "</b>"; 
		} else {
			echo ucwords(strtolower($reg4['codigo'] . "." . $reg4['cargo_defecto'] . " - " . $reg4['descCargoDefecto'])); 
		}
		?>
		<input name="codigo<? echo $j; ?>" type="hidden" id="codigo<? echo $j; ?>" value="<? echo $reg4['codigo']; ?>">
		<input name="cargo<? echo $j; ?>" type="hidden" id="cargo<? echo $j; ?>" value="<? echo $reg4['cargo_defecto']; ?>">
		<input name="adicional<? echo $j; ?>" type="hidden" id="adicional<? echo $j; ?>" value="<? echo $reg4['esAdicional']; ?>">		</td>
        <td align="center"><input name="aplicaCargo<? echo $j; ?>" type="radio" value="1"></td>
        <td align="center"><input name="aplicaCargo<? echo $j; ?>" type="radio" value="0" checked></td>
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
	<input name="cantCargos" type="hidden" id="cantCargos" value="<? echo mssql_num_rows($cursor4); ?>">
	<input name="cantUsuarios" type="hidden" id="cantUsuarios" value="<? echo mssql_num_rows($cursor3); ?>">	<input name="recarga" type="hidden" id="recarga" value="1">
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
