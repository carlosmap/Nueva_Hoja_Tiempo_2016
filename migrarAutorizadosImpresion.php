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
2010-11-03
Daniel Felipe Rentería Martínez
Migracion Proyectos
*/

/*
Proyectos Activos
*/
$sql1 = " SELECT id_proyecto, nombre FROM HojaDeTiempo.dbo.Proyectos
WHERE id_estado = 2
AND id_proyecto not in (
	SELECT id_proyecto FROM Proyectos
	where especial = 1
	and LEN(codigo) > 2
) 
AND ( id_proyecto <> 1270 AND id_proyecto <> 1278)
ORDER BY id_proyecto ";
$cursor1 = mssql_query($sql1);


?>
<html>
<head>
<title>Autorizaci&oacute;n de Impresiones</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript">
<!--

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
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Autorizados para Impresi&oacute;n (Migraci&oacute;n)</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<form name="Form1" action="" method="post">
  <tr>
    <td >
	<table width="100%"  cellspacing="1" class="fondo">
		<?
		//While de Proyectos Activos
		$i = 1;
		$j = 1;
		while($reg1 = mssql_fetch_array($cursor1)){
		?>
		<tr>
        <td width="30%" class="TituloTabla2">Proyecto</td>
        <td class="TxtTabla2"><? echo $reg1['id_proyecto'] . " - " . $reg1['nombre']; ?></td>
        </tr>
		<tr>
		  <td colspan="2" class="fondo">
		  <?
		  //Consulta de Usuarios Dentro del Proyecto
			$sql1a = " SELECT * FROM (
				SELECT DISTINCT A.id_proyecto, A.nombre AS nombreProyecto, B.unidad, B.nombre, B.apellidos, B.email
				FROM HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Usuarios B
				WHERE ( A.id_director = B.unidad OR A.id_coordinador = B.unidad )
				AND A.especial is null
				AND B.retirado is null
				UNION
				SELECT DISTINCT A.id_proyecto, A.nombre AS nombreProyecto, C.unidad, C.nombre, C.apellidos, C.email
				FROM HojaDeTiempo.dbo.Proyectos A, GestiondeInformacionDigital.dbo.OrdenadorGasto B,
				HojaDeTiempo.dbo.Usuarios C
				WHERE A.id_proyecto = B.id_proyecto
				AND B.unidadOrdenador = C.unidad
				AND A.especial is null
				AND C.retirado is null
				UNION
				SELECT DISTINCT A.id_proyecto, A.nombre AS nombreProyecto, C.unidad, C.nombre, C.apellidos, C.email
				FROM HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Programadores B,
				HojaDeTiempo.dbo.Usuarios C
				WHERE A.id_proyecto = B.id_proyecto
				AND B.unidad = C.unidad
				AND A.especial is null
				AND C.retirado is null
			) AS Usu WHERE Usu.id_proyecto = " . $reg1['id_proyecto'];
			$cursor1a = mssql_query($sql1a);
			
			
			
		  ?>
		  <table width="100%"  cellspacing="1" class="fondo">
            <tr class="TituloTabla2">
              <td width="50%">Usuario</td>
              <td width="25%">Usuarios</td>
              <td width="25%">Cargos</td>
              </tr>
			<?
			
			//While de Usuarios de Proyecto
			while($reg1a = mssql_fetch_array($cursor1a)){
			?>
			<tr class="TxtTabla">
              <?
			  /***********
			  Inicio de la transacción de migración para usuarios de Proyecto
			  ***********/
			  $okGuardarHT = "Si";
			  $okGuardarPD = "Si";
			  ?>
			   <td><? echo $reg1a['unidad'] . " - " . ucwords(strtolower($reg1a['nombre'] . " " . $reg1a['apellidos'])); ?></td>
			  <td align="center"><?
			  /*************************
			  Migración del Usuario a Autorizados Impresión
			  *************************/
				if($_POST["recarga"] == 2){
					$sqlIn1 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
					$sqlIn1 = $sqlIn1 . " VALUES ( ";
					$sqlIn1 = $sqlIn1 . " " . $reg1['id_proyecto'] . ", ";
					$sqlIn1 = $sqlIn1 . " " . $reg1a['unidad'] . ", ";
					$sqlIn1 = $sqlIn1 . " '" . date("m/d/Y H:i:s") . "', ";
					$sqlIn1 = $sqlIn1 . " 'A', ";
					$sqlIn1 = $sqlIn1 . " 14888, ";
					$sqlIn1 = $sqlIn1 . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn1 = $sqlIn1 . " ) ";
					$cursorIn1 = mssql_query($sqlIn1);
					if(trim($cursorIn1) == ""){
						$okGuardarHT = "No";
					} else {
						echo "HT - OK <br>";
					}
					/*************************
				   Transferencia del Usuario a PageDevice
					*************************/
					$sqlMy1 = " SELECT user_id FROM pc_user WHERE user_name = '" . $reg1a['email'] . "' ";
					$sqlMy1 = $sqlMy1 . " AND descrip = '" . $reg1a['unidad'] . "' ";
					$cursorMy1 = mysql_query($sqlMy1);
					if($regMy1 = mysql_fetch_array($cursorMy1)){
						$elIdUsuario = $regMy1['user_id'];
					} else {
						$elIdUsuario = 0;
					}
					if($elIdUsuario == 0){
						$sqlMy2 = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
						$sqlMy2 = $sqlMy2 . " VALUES ( ";
						$sqlMy2 = $sqlMy2 . " '" . $reg1a['email'] . "', ";
						$sqlMy2 = $sqlMy2 . " '" . $reg1a['unidad'] . "', ";
						$sqlMy2 = $sqlMy2 . " 1 ";
						$sqlMy2 = $sqlMy2 . " ) ";
						$cursorMy2 = mysql_query($sqlMy2);
						if(trim($cursorMy2) == ""){
							$okGuardarPD = "No";
						} else {
							echo "PD (User) - OK <br>";
						}
						//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
						$sqlMy3 = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
						$cursorMy3 = mysql_query($sqlMy3);
						if($regMy3 = mysql_fetch_array($cursorMy3)){
							$elIdUsuario = $regMy3['elIdUsuario'];
						}
					}
				}
			  ?>			    </td>
              <td align="center"><?
			  //Consulta de Cargos dentro del Proyecto
				$sql1b = " SELECT * FROM (
					SELECT A.id_proyecto, A.nombre, A.codigo, A.cargo_defecto, A.descCargoDefecto, '0' esAdicional
					FROM HojaDeTiempo.dbo.Proyectos A
					UNION
					select A.id_proyecto, A.nombre, A.codigo, B.cargos_adicionales, B.descripcion, '1' esAdicional
					from HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Cargos B
					where A.id_proyecto = B.id_proyecto
				) X WHERE X.id_proyecto = " . $reg1['id_proyecto'];
				$cursor1b = mssql_query($sql1b);
			  //While de los cargos del proyecto
			  if($_POST["recarga"] == 2){
				  while($reg1b = mssql_fetch_array($cursor1b)){
						/*************************
						Migración del Usuario a Autorizados Impresión (Cargos)
						*************************/
						$sqlIn2 = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
						$sqlIn2 = $sqlIn2 . " WHERE id_proyecto = " . $reg1['id_proyecto'] . " ";
						$sqlIn2 = $sqlIn2 . " AND unidad = " . $reg1a['unidad'] . " ";
						$cursorIn2 = mssql_query($sqlIn2);
						if($regIn2 = mssql_fetch_array($cursorIn2)){
							$elConsCargo = $regIn2['elId'] + 1;
						}
						$sqlIn3 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
						cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
						$sqlIn3 = $sqlIn3 . " VALUES ( ";
						$sqlIn3 = $sqlIn3 . " " . $reg1['id_proyecto'] . ", ";
						$sqlIn3 = $sqlIn3 . " " . $reg1a['unidad'] . ", ";
						$sqlIn3 = $sqlIn3 . " " . $elConsCargo . ", ";
						if($reg1b['esAdicional'] == 0){
							$sqlIn3 = $sqlIn3 . " " . $reg1b['cargo_defecto'] . ", ";
							$sqlIn3 = $sqlIn3 . " NULL, ";
						} else if($reg1b['esAdicional'] == 1){
							$sqlIn3 = $sqlIn3 . " NULL, ";
							$sqlIn3 = $sqlIn3 . " " . $reg1b['cargo_defecto'] . ", ";
						}
						$sqlIn3 = $sqlIn3 . " 14888, ";
						$sqlIn3 = $sqlIn3 . " '" . date("m/d/Y H:i:s") . "' ";
						$sqlIn3 = $sqlIn3 . " ) ";
						$cursorIn3 = mssql_query($sqlIn3);
						if(trim($cursorIn3) == ""){
							$okGuardarHT = "No";
						} else {
							echo $reg1['codigo'] . "." . $reg1b['cargo_defecto'] . " HT - OK <br>";
						}
						/*************************
						Migración de los Cargos a Page Device
						*************************/
						$sqlMy4 = " SELECT bil_id FROM pc_bilcode WHERE bil_code = '" . $reg1b['codigo'] . "." . $reg1b['cargo_defecto'] . "' ";
						$sqlMy4 = $sqlMy4 . " AND ident = '" . $reg1['id_proyecto'] . "' ";
						$cursorMy4 = mysql_query($sqlMy4);
						if($regMy4 = mysql_fetch_array($cursorMy4)){
							$elIdCentroCosto = $regMy4['bil_id'];
						}
						$sqlMy5 = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
						$sqlMy5 = $sqlMy5 . " VALUES ( ";
						$sqlMy5 = $sqlMy5 . " " . $elIdUsuario . ", ";
						$sqlMy5 = $sqlMy5 . " " . $elIdCentroCosto . " ";
						$sqlMy5 = $sqlMy5 . " ) ";   
						$cursorMy5 = mysql_query($sqlMy5);
						if(trim($cursorMy5) == ""){
							$okGuardarMySql = "No";
						} else {
							echo "PD (Cargos) - OK <br>";
						}
				  } // Fin While Cargos
			  } 
			  ?></td>
            </tr>
			<? 
			$j++;
			} // Fin While Usuarios ?>
          </table></td>
		  </tr>
		  <? 
		  $i++;
		  } // Fin While Proyectos ?>
	</table>
	</td>
  </tr>
   <tr>
    <td align="right" class="TxtTabla" >Proyectos: <? echo $i; ?> - Usuarios: <? echo $j; ?></td>
  </tr>
  <tr>
    <td align="right" class="TxtTabla" >
		<input name="recarga" type="hidden" id="recarga" value="2">
		<input name="BtnMigrar" type="submit" class="Boton" id="BtnMigrar" value="Migrar">
	</td>
  </tr>
</form>
</table>


<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="TituloTabla2">
  <tr>
    <td class="copyr" >Powered by Ingetec S.A. - 2010 </td>
  </tr>
</table>

<?
//Finaliza las conexiones a MySql y a SQL Server
mssql_close();
mysql_close();
?>
</body>
</html>
