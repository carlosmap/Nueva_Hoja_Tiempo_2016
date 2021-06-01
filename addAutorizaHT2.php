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
$sql=$sql." and unidad = " . $cualUnidad;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elUsuarioJefe = $reg[unidadJefe];
}

//eNCUENTRA EL NOMBRE DEL USUARIO

//Verificar si el usuario ya existe para mostrar el jefe ya seleccionado
$sql="Select * from USUARIOS ";
$sql=$sql." where unidad = " . $cualUnidad;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elnombreUsuario = ucwords(strtolower($reg[nombre] . " " . $reg[apellidos])) ;
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
	$sql1=$sql1." and unidad = " . $unidadUsuario;

	$cursor1 = mssql_query($sql1);
	if ($reg1=mssql_fetch_array($cursor1)) {
		$cuantosHay = $reg1[hayRegistros];
	}
	
	if ($cuantosHay == 0) {
		$query = "INSERT INTO AutorizacionesHT(vigencia, mes, unidad, unidadJefe, fechaEnvio)  " ;
		$query = $query . " VALUES (" . $elAno . ", ";
		$query = $query . $elMes . " , ";
		$query = $query . $unidadUsuario . " , ";	
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
		$query = $query . " AND unidad = " .  $unidadUsuario ;
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
	<? 	echo $cualUnidad; 	?>
    <input name="unidadUsuario" type="hidden" id="unidadUsuario" value="<? echo $cualUnidad; ?>">	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<? echo $elnombreUsuario ?></td>
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
	</td>
  </tr>
</table>

</body>
</html>
