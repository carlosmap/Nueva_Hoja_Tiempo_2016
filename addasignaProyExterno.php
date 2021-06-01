<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

?>

<?

//Encontrar la categoria vigente para la selección de usuarios
$sql="Select * from GestiondeInformacionDigital.dbo.CategoriaAutoriza  ";
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laCategoria = $reg[id_categoria];
	}
else {
	$laCategoria= 0;
}

//$recarga = 2 si se presionó el botón Grabar
if ($pProyecto != "") {

	//Realiza la grabación de la Asignación del proyecto en AsignaProyectosExt
	//id_proyecto, unidadDirector, unidadEncargado, 
	$query = "INSERT INTO PortalGID.dbo.AsignaProyectosExt (id_proyecto, unidadDirector, unidadEncargado)";
	$query = $query . " VALUES (" . $pProyecto . ", ";
	$query = $query . $pJefe . ", ";	
	if ($pAut2 == '1') {
		$query = $query . $pJefeAut2 ;
	}
	else {
		$query = $query . " NULL " ;
	}
	$query = $query . " ) ";
//echo $query . "<BR>";
	$cursor = mssql_query($query) ;

	
	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	
	//Crea los directorios en los servidores de ingetec
	//Identifica el código del proyecto y con ese código crea el directorio principal de almacenamiento
	$sql = "select codigo, cargo_defecto from HojaDeTiempo.dbo.proyectos where id_proyecto = '$pProyecto'";
	$cursor = mssql_query($sql);
	$regis  = mssql_fetch_array($cursor);
	$codigo = $regis[codigo];
	$cargod = $regis[cargo_defecto];
	//Conforma el código del proyecto (id_proyecto+codigo+cargo)
	$codProyD = trim($pProyecto).trim($codigo).trim($cargod);
	
	//Ruta donde se crea la carpeta principal del proyecto
	$dirProyecto = $DOCUMENT_ROOT . "/GIDPortal/Datos/" . $codProyD ;
	
	echo $dirProyecto;
	
	//Con el código del proyecto creo el directorio raíz
	if(!mkdir($dirProyecto,0777)){
		echo "<script>alert('Error en la creación del directorio principal. Usuario sin permisos/Directorio existe')</script>";
	}
	
	//Dentro de este directorio se crearán los dos subdirectorios principales
	chdir($dirProyecto);
	$subdirProyecto="InfCliente";
	if(!mkdir($subdirProyecto,0777)){
		echo "<script>alert('Error en la creación del subdirectorio 1. Usuario sin permisos/Directorio existe')</script>";
	}

	$subdirProyecto="InfIngetec";
	if(!mkdir($subdirProyecto,0777)){
		echo "<script>alert('Error en la creación del subdirectorio 2. Usuario sin permisos/Directorio existe')</script>";
	}
echo ("<script>window.close();MM_openBrWindow('infProyecto.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}


?>
<html>
<head>
<title>Asignaci&oacute;n de proyectos externos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post" name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Asignaci&oacute;n de Proyectos Externos </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="20%" bgcolor="#FFFFFF">
		
	  <tr>
        <td class="TituloTabla">Proyecto</td>
        <td class="TxtTabla">
		<?
		//Traer los proyectos que no tienen asignación
		$sql2="SELECT P.* ";
		$sql2=$sql2 . " FROM HojaDeTiempo.dbo.Proyectos P ";
		$sql2=$sql2 . " WHERE P.id_proyecto = " . $cualProyecto ;
		$cursor2 = mssql_query($sql2);
		if ($reg2=mssql_fetch_array($cursor2)) {
			echo ucwords(strtolower($reg2[nombre])); 
		}
		?>
		<input name="pProyecto" type="hidden" id="pProyecto" value="<? echo $cualProyecto ; ?>">		</td>
      </tr>
      <tr>
          <td class="TituloTabla">Director para el Proyecto en la Gesti&oacute;n de Archivos Externos </td>
          <td class="TxtTabla">
		  <select name="pJefe" class="CajaTexto" id="pJefe" >
            <?
		//Muestra todos los usuarios. 
		$sql2="Select * from HojaDeTiempo.dbo.Usuarios where id_categoria <= " . $laCategoria ;
		$sql2=$sql2." and retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		  </td>
        </tr>
	  <tr>
          <td class="TituloTabla">&iquest;Requiere Encargado? </td>
          <td class="TxtTabla">
		  <input name="pAut2" type="radio" value="1">
            Si
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="pAut2" type="radio" value="0" checked>
            No&nbsp;&nbsp;&nbsp;		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Encargado</td>
          <td class="TxtTabla">
		  <select name="pJefeAut2" class="CajaTexto" id="pJefeAut2" >
            <?
		//Muestra todos los usuarios. 
		$sql2="Select * from HojaDeTiempo.dbo.Usuarios where id_categoria <= " . $laCategoria ;
		$sql2=$sql2." and retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		  </td>
        </tr>
  </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar" ></td>
        </tr>
      </table></td>
  </tr>
</table>

	     </td>
  </tr>
</table>
</form> 

</body>
</html>

<? mssql_close ($conexion); ?>	
