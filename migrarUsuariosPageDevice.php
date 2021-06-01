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
$sql1 = " SELECT email, unidad, '1' bilcode
FROM usuarios
where retirado is null
and unidad not in (80, 82, 83, 84, 85) ";
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
    <td class="TituloUsuario">Programación de Proyectos - Autorizados para Impresi&oacute;n (Migraci&oacute;n Usuarios)</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<form name="Form1" action="" method="post">
  <tr>
    <td >	<table width="100%"  cellspacing="1" class="fondo">
      <tr class="TituloTabla2">
        <td width="50%">Usuario</td>
        <td>Usuarios</td>
        </tr>
      <?
			//While de Usuarios de Proyecto
			$i = 1;
			while($reg1 = mssql_fetch_array($cursor1)){
			?>
      <tr class="TxtTabla">
        <?
			  ?>
        <td><? echo $reg1['unidad'] . " - " . $reg1['email'] ; ?></td>
        <td align="center"><?
			  /*************************
			   Transferencia del Usuario a PageDevice
				*************************/
				if($_POST["recarga"] == 2){
					$sqlMy1 = " SELECT user_id FROM pc_user WHERE user_name = '" . $reg1['email'] . "' ";
					$sqlMy1 = $sqlMy1 . " AND descrip = '" . $reg1['unidad'] . "' ";
					$cursorMy1 = mysql_query($sqlMy1);
					if($regMy1 = mysql_fetch_array($cursorMy1)){
						$elIdUsuario = $regMy1['user_id'];
					} else {
						$sqlMy2 = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
						$sqlMy2 = $sqlMy2 . " VALUES ( ";
						$sqlMy2 = $sqlMy2 . " '" . strtolower($reg1['email']) . "', ";
						$sqlMy2 = $sqlMy2 . " '" . $reg1['unidad'] . "', ";
						$sqlMy2 = $sqlMy2 . " 1 ";
						$sqlMy2 = $sqlMy2 . " ) ";
						$cursorMy2 = mysql_query($sqlMy2);
						if(trim($cursorMy2) == ""){
							$okGuardarPD = "No";
						} else {
							echo "PD - OK <br>";
						}
					}
				}
			  ?>
        </td>
      </tr>
      <? }   // Fin While Usuarios ?>
    </table></td>
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
