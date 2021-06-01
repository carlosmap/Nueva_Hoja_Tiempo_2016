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


//25Jun2009
//Trae la información del usuario seleccionado
$sql0="select * from usuarios where unidad = " . $cualUnidad ;
$cursor0 = mssql_query($sql0);
if ($reg0=mssql_fetch_array($cursor0)) {
	$elnomUsu = $reg0[nombre] . " " . $reg0[apellidos];
}


//Si se presionó el botón Grabar
if ($btnGraba == "Grabar") {
	//Realiza la grabacion en dbo.Autorizaciones
	//undAutorizado, undAutorizar
	@mssql_select_db("HojaDeTiempo");
	$query = "INSERT INTO Autorizaciones(undAutorizado, undAutorizar)  " ;
	$query = $query . " VALUES (" . $usuDelega . ", ";
	$query = $query . $cualUnidad ;
	$query = $query . " ) ";
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

$miBtn= explode("-", $btnDel);

//Si presionó el botón eliminar
if ($miBtn[0] == "Eliminar") {

	//Realiza la eliminación de dbo.Autorizaciones
	//undAutorizado, undAutorizar
	@mssql_select_db("HojaDeTiempo");
	$query = "DELETE FROM Autorizaciones WHERE  " ;
	$query = $query . " undAutorizado = " . $miBtn[1] ;
	$query = $query . " AND undAutorizar = " .  $cualUnidad ;
	$cursor = mssql_query($query) ;	

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La eliminación se realizó con éxito.');</script>");
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
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo - Delegar tr&aacute;mite de Hoja de tiempo por retiro de usuario </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<form action="" method="post" name="Form1" id="Form1">
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla"><? echo $cualUnidad ; ?><input name="cualUnidad" type="hidden" id="cualUnidad" value="<? echo $cualUnidad ; ?>"></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<? echo strtoupper($elnomUsu); ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario a delegar </td>
    <td class="TxtTabla"><select name="usuDelega" class="CajaTexto" id="usuDelega" >
            <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra todos los usuarios. 
		$sql2="Select * from Usuarios where  "  ;
		$sql2=$sql2."  retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" <? echo $selJefe; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="btnGraba" type="submit" class="Boton" value="Grabar"></td>
    </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="TituloUsuario">
        <td>Quienes est&aacute;n autorizados para diligenciar la Hoja de tiempo de <br>
          <? echo strtoupper($elnomUsu); ?></td>
      </tr>
    </table>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloUsuario">
        <td>Nombre</td>
        <td width="5%">&nbsp;</td>
      </tr>
	  <?
	  //25Jun2009
	  //Mostrar el listado de quienes estan asignados para diligenciar la HT del usuario seleccionado
	  $rSql="select A.* , U.nombre, U.apellidos ";
	  $rSql=$rSql." from autorizaciones A, Usuarios U ";
	  $rSql=$rSql." where A.undAutorizado = U.unidad ";
	  $rSql=$rSql." and A.undAutorizar =" . $cualUnidad;
		$rcursor = mssql_query($rSql);
		while ($rreg=mssql_fetch_array($rcursor)) {
	  ?>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($rreg[apellidos])) . " " . ucwords(strtolower($rreg[nombre])) ; ?></td>
        <td width="5%">
          <input name="btnDel" type="submit" class="Boton" id="btnDel" value="Eliminar-<? echo $rreg[undAutorizado]; ?>">
        </td>
      </tr>
	  <? } ?>
    </table></td>
  </tr>
  </form>
</table>
	</td>
  </tr>
</table>

</body>
</html>
