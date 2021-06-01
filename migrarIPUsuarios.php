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
$sql1 = " SELECT * FROM GestiondeInformacionDigital.dbo.UsuarioIP ";
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
    <td >	<table width="100%"  cellspacing="1" class="fondo">
      <tr class="TituloTabla2">
        <td width="50%">Usuario</td>
        <td width="25%">Direccion IP </td>
        <td width="25%">Cargos</td>
      </tr>
      <? 
	  //While de Usuarios de Proyecto
	  while($reg1 = mssql_fetch_array($cursor1))
	  { 
	  ?>
      <tr class="TxtTabla">
        <td><? echo $reg1['NomUsuario']; ?></td>
        <td align="center"><? echo $reg1['DireccionIP']; ?></td>
        <td align="center"><?
		if($_POST["recarga"] == 2){
			$sqlUpIp = " UPDATE pc_user SET ip_address = '" . $reg1['DireccionIP'] . "' ";
			$sqlUpIp = $sqlUpIp . " WHERE user_name = '" . $reg1['NomUsuario'] . "' ";
			$cursorUpIp = mysql_query($sqlUpIp);
			if(trim($cursorUpIp) != ""){
				echo "OK";
			} else {
				echo "Fallo";
			}
		}
		
		?></td>
      </tr>
	  <? } ?>
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
