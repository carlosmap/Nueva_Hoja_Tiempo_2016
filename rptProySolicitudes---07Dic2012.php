<?php
session_start();
//include("../verificaRegistro2.php");

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//Establecer la conexión a la base de datos
//$conexion = conectar();

/*
26Ene2011
PBM
Listado de proyectos activos para presentar los reportes
*/

$sqlP="SELECT Proyectos.*, Usuarios_1.nombre AS nomDirector, Usuarios_1.apellidos AS apeDirector, Usuarios_1.email AS mailDirector,  ";
$sqlP=$sqlP." Usuarios.nombre AS nomCoord, Usuarios.apellidos AS apeCoord, Usuarios.email AS mailCoord, Empresas.nombre AS nomEmpresa ";
$sqlP=$sqlP." FROM Proyectos LEFT OUTER JOIN ";
$sqlP=$sqlP." Empresas ON Proyectos.idEmpresa = Empresas.idEmpresa LEFT OUTER JOIN ";
$sqlP=$sqlP." Usuarios ON Proyectos.id_coordinador = Usuarios.unidad LEFT OUTER JOIN ";
$sqlP=$sqlP." Usuarios AS Usuarios_1 ON Proyectos.id_director = Usuarios_1.unidad ";
$sqlP=$sqlP." WHERE Proyectos.id_proyecto =" . $_SESSION["sesProyReportes"] ;
@mssql_select_db("HojaDeTiempo",$conexion);
$cursorP = mssql_query($sqlP);

//@mssql_select_db("GestiondeInformacionDigital",$conexion);
//Seleccionar los registros de SolicitudElementos
$sql="select * from GestiondeInformacionDigital.dbo.SolicitudElementos";
$sql= $sql. " where id_proyecto = " . $_SESSION["sesProyReportes"] ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql= $sql. " and month(fechaSolicitud) = month(getdate()) ";
	$sql= $sql. " and year(fechaSolicitud) = year(getdate()) ";
}
else {
	$sql= $sql. " and month(fechaSolicitud) = " . $pMes;
	$sql= $sql. " and year(fechaSolicitud) =  " . $pAno;
}
$cursor = mssql_query($sql);

//Seleccionar los registros de SolicitudPasajes
$sql2="Select S.*, A.nombre nombreAgencia, E.nombre nombreempresaA, U.nombre, U.apellidos ";
$sql2= $sql2. " from GestiondeInformacionDigital.dbo.SolicitudPasajes S, ";
$sql2= $sql2. " GestiondeInformacionDigital.dbo.Agencias A, GestiondeInformacionDigital.dbo.EmpresaAerea E, ";
$sql2= $sql2. " HojaDeTiempo.dbo.Usuarios U ";
$sql2= $sql2. " where S.codAgencia *= A.codAgencia ";
$sql2= $sql2. " and S.codEmpresa *= E.codEmpresa ";
$sql2= $sql2. " and S.unidad = U.unidad ";
$sql2= $sql2. " AND S.id_proyecto  = " . $_SESSION["sesProyReportes"] ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql2= $sql2. " AND month(S.fechaSolicitud) = month(getdate()) ";
	$sql2= $sql2. " AND year(S.fechaSolicitud) = year(getdate()) ";
}
else {
	$sql2= $sql2. " AND month(S.fechaSolicitud) = " . $pMes;
	$sql2= $sql2. " AND year(S.fechaSolicitud) =  " . $pAno;
}
$cursor2 = mssql_query($sql2);

//Seleccionar los registros de SolicitudTransporte
$sql3="SELECT S.*, T.nomTiempo, V.numVehiculo ";
$sql3= $sql3. " FROM GestiondeInformacionDigital.dbo.SolicitudTransporte S, GestiondeInformacionDigital.dbo.Tiempos T, GestiondeInformacionDigital.dbo.vehiculos V ";
$sql3= $sql3. " WHERE S.codTiempo *= t.codTiempo ";
$sql3= $sql3. " AND S.placa *= V.placa ";
$sql3= $sql3. " AND S.id_proyecto  = " . $_SESSION["sesProyReportes"] ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql3= $sql3. " AND month(S.fechaSolicitud) = month(getdate()) ";
	$sql3= $sql3. " AND year(S.fechaSolicitud) = year(getdate()) ";
}
else {
	$sql3= $sql3. " AND month(S.fechaSolicitud) = " . $pMes;
	$sql3= $sql3. " AND year(S.fechaSolicitud) =  " . $pAno;
}
$cursor3 = mssql_query($sql3);

//Traer los viáticos asociados al proyecto tla cual como fueron registrados
$sql4="SELECT ViaticosProyecto.IDsitio, ViaticosProyecto.IDfraccion, ViaticosProyecto.IDTipoViatico, ViaticosProyecto.unidad, ViaticosProyecto.id_actividad, 
	ViaticosProyecto.localizacion, ViaticosProyecto.cargo, ViaticosProyecto.FechaIni, ViaticosProyecto.FechaFin, ViaticosProyecto.Trayecto, 
	ViaticosProyecto.ObjetoComision, ViaticosProyecto.viaticoCompleto, Actividades.nombre AS nomActividad, TiposViatico.NomTipoViatico, 
	SitiosTrabajo.NomSitio, Usuarios.nombre AS nomUsuario, Usuarios.apellidos AS apeUsuario
	FROM ViaticosProyecto INNER JOIN
	Usuarios ON ViaticosProyecto.unidad = Usuarios.unidad INNER JOIN
	SitiosTrabajo ON ViaticosProyecto.IDsitio = SitiosTrabajo.IDsitio AND ViaticosProyecto.id_proyecto = SitiosTrabajo.id_proyecto INNER JOIN
	TiposViatico ON ViaticosProyecto.IDTipoViatico = TiposViatico.IDTipoViatico INNER JOIN
	Actividades ON ViaticosProyecto.id_proyecto = Actividades.id_proyecto AND ViaticosProyecto.id_actividad = Actividades.id_actividad ";
$sql4= $sql4. " WHERE ViaticosProyecto.id_proyecto = " . $_SESSION["sesProyReportes"] ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql4= $sql4. " and MONTH(ViaticosProyecto.FechaIni) = month(getdate()) ";
	$sql4= $sql4. " and YEAR(ViaticosProyecto.FechaIni) = year(getdate()) ";
}
else {
	$sql4= $sql4. " and MONTH(ViaticosProyecto.FechaIni) = " . $pMes;
	$sql4= $sql4. " and YEAR(ViaticosProyecto.FechaIni) = " . $pAno;
}
$sql4= $sql4. " order by ViaticosProyecto.unidad ";
@mssql_select_db("HojaDeTiempo",$conexion);
$cursor4 = mssql_query($sql4);

?>
<html>
<head>
<title>Reportes de los Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winReportes";
</script>
<script language="JavaScript" type="text/JavaScript">
<!--


function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:774px; height:25px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota3">Reportes de solicitudes y vi&aacute;ticos del proyecto </td>
    </tr>
  </table>
</div>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Proyecto</td>
      </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="fondo"><table width="100%" cellspacing="1">
      <tr class="TituloTabla2">
        <td>Nombre del Proyecto </td>
        <td>Codigo - Cargo del Proyecto </td>
        <td>Director</td>
        <td>Coordinador</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td width="1%">&nbsp;</td>
        </tr>
      <? 	  while($regP = mssql_fetch_array($cursorP)){ 	  ?>
      <tr class="TxtTabla">
        <td><? echo strtoupper($regP["nombre"]); ?></td>
        <td><? echo $regP["codigo"]. "." .$regP["cargo_defecto"]; ?></td>
        <td><? echo strtoupper($regP["nomDirector"]. " " .$regP["apeDirector"]); ?></td>
        <td><? echo strtoupper($regP["nomCoord"]. " " .$regP["apeCoord"]); ?></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
	  <? } ?>
    </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>
	<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellpadding="0" cellspacing="1">
    <form name="form1" id="form1" method="post" action="">	
      <tr>
        <td colspan="3" class="TituloUsuario">Criterios de consulta </td>
        </tr>
      <? if ($mostrar == "Siiii") { ?>
      <tr>
        <td width="20%" class="TituloTabla">Reporte</td>
        <td class="TxtTabla"><?
		if (($pProyecto == 1) or (trim($pProyecto) == "")) {
			$selP1 = "checked";
			$selP2 = "";
		}
		else {
			$selP1 = "";
			$selP2 = "checked";
		}
		?>
          <select name="select" class="CajaTexto">
            <option value="1">Solicitud de elementos</option>
            <option value="2">Solicitud de pasajes</option>
            <option value="3">Solicitud de pasajes</option>
          </select></td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
	  <? } ?>
      <tr>
        <td class="TituloTabla">Mes</td>
        <td class="TxtTabla">		<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}



	}
	
	?>
	&nbsp;      
	<select name="pMes" class="CajaTexto" id="pMes">
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select>
		</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">A&ntilde;o</td>
        <td class="TxtTabla">
		<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>
    </select>
		</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">&nbsp;</td>
        <td class="TxtTabla">&nbsp; </td>
        <td width="2%" class="TxtTabla"><input name="Submit3" type="submit" class="Boton" value="Consultar" /></td>
      </tr>
    </form>	  
    </table></td>
      </tr>
    </table>
	</td>
  </tr>
  <tr>
    <td class="menu"><a href="ReportesHT2.php" class="menu" >&lt;&lt; Regresar al listado de proyectos </a>
	</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Solicitud de elementos </td>
      </tr>
    </table>
    </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td colspan="2" class="fondo">
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td colspan="2">Solicitud</td>
        <td width="30%" rowspan="2">Usuario que realiz&oacute; la solicitud </td>
        <td width="5%" rowspan="2">C&oacute;digo</td>
        <td width="5%" rowspan="2">Cargo</td>
        <td rowspan="2">Frente de trabajo </td>
        <td colspan="4">Proceso Solicitud</td>
        <td width="5%" rowspan="2">&nbsp;</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="5%">No.</td>
        <td width="10%">Fecha</td>
        <td width="5%">Finalizada Usuario </td>
        <td width="5%">Jefe Inmediato </td>
        <td width="5%">Jefe 2da Aut </td>
        <td width="5%">Almac&eacute;n</td>
        </tr>
	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%"><? echo $reg[secuencia]; ?></td>
        <td width="10%"><? echo date("M d Y ", strtotime($reg[fechaSolicitud])); ?></td>
        <td width="30%">
		<?
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sqlU="Select * from usuarios where unidad =" . $reg[unidad];
		$cursorU = mssql_query($sqlU);
		if ($regU=mssql_fetch_array($cursorU)) {
			echo "[" .$regU[unidad] . "] " . ucwords(strtolower($regU[nombre])) . " " . ucwords(strtolower($regU[apellidos])) ;
		}
		?>		  </td>
        <td width="5%"><? echo $reg[codigo]; ?></td>
        <td width="5%"><? echo $reg[cargo]; ?></td>
        <td><? echo $reg[frente]; ?></td>
        <td width="5%" align="center">
		<? 
		//Si enviaAJefe = 1 entonces la solicitud fué enviada al Jefe inmediato
		if (($reg[enviaAJefe] == "1") AND ($reg[validaUsuario] == "0")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n del jefe inmediato" width="16" height="16">
		<? } ?>	
		<? 
		//Si validaUsuario = 1 entonces la solicitud fué revisada y aprobada por el jefe inmediato
		if ($reg[validaUsuario] == "1") { ?>
		<img src="img/images/Si.gif" alt="Finalizada">
		<? } ?>		</td>
        <td width="5%" align="center">
		<? 
		//Si validaJefe = 1 entonces el jefe aprobó
		if ($reg[validaJefe] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por el jefe inmediato">
		<? } ?>
		<? 
		//Si validaJefe = 0 pero hay comentarios del jefe, es porque la revisó pero no la aprobó
		if (($reg[validaJefe] == "0") AND ($reg[comentaJefe] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por jefe inmediato y sin aprobar" width="12" height="16">
		<? } ?>		</td>
        <td width="5%" align="center">
		<? 
		//Para determinar quien va a efectur la firma unidadJefe o UnidadJefe2
		if ($reg[requiereFirma2] == "1" ) {
				echo "Si requiere";
		} 
		else {
				echo "N/A";
		} 
		
		?>
				<? 
		//Si validaJefe2 = 1 entonces el jefe de segunda firma aprobó
		if ($reg[validaJefe2] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por el jefe firma 2">
		<? } ?>
		<? 
		//Si validaJefe2 = 0 pero hay comentarios del jefe firma 2, es porque la revisó pero no la aprobó
		if (($reg[validaJefe2] == "0") AND ($reg[comentaJefe2] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por jefe firma 2 y No fue aprobada" width="12" height="16">
		<? } ?>		</td>
        <td width="5%" align="center">
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y requiereFirma2 = 0 Y validaAlmacen = 0 la solicitud pasa a almacén para revisión
		if (($reg[validaUsuario] == "1") AND ($reg[validaJefe] == "1") AND ($reg[validaAlmacen] == "0") AND ($reg[requiereFirma2] == "0") AND ($reg[comentaAlmacen] == "")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n en Almac&eacute;n" width="16" height="16"><? } ?>
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y requiereFirma2 = 1 y validaJefe2 = 1 y validaAlmacen = 0 la solicitud pasa a almacén para revisión
		if (($reg[validaUsuario] == "1") AND ($reg[validaJefe] == "1") AND ($reg[validaJefe2] == "1") AND ($reg[requiereFirma2] == "1") AND ($reg[validaAlmacen] == "0")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n en Almac&eacute;n" width="16" height="16"><? } ?>
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y requiereFirma2 = 1 y validaJefe2 = 1 y validaAlmacen = 0 la solicitud pasa a almacén para revisión
		if (($reg[validaAlmacen] == "0") AND ($reg[comentaAlmacen] != "")) { ?>
		<img src="img/images/No.gif" alt="<? echo $reg[comentaAlmacen]; ?>" width="12" height="16">		<? } ?>
		
		
		<? 
		//Si validaAlmacen = 1 y validaEncargado = 0 y comentaEncargado es Null o vacio
		//Almacén la revisó y la envio para aprobación del encargado
		//Pero el encargado no la ha revisado
		if (($reg[validaAlmacen] == "1") AND ($reg[validaEncargado] == "0") AND ($reg[comentaEncargado] == "")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n Encargado Almac&eacute;n" width="16" height="16"><? } ?>
		<? 
		//Si validaAlmacen = 1 y validaEncargado = 1 y validaDirector = 0 y comentaDirector es Null o vacio
		//encargado de almacén la revisó y la envio para aprobación del Director
		//Pero el director no la ha revisado
		if (($reg[validaAlmacen] == "1") AND ($reg[validaEncargado] == "1") AND ($reg[validaDirector] == "0") AND ($reg[comentaDirector] == "")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n Director Almac&eacute;n" width="16" height="16"><? } ?>
		
		<? 
		//Si validaAlmacen = 1 y validaEncargado = 0 y comentaEncargado es diferente de vacio
		//es porque el encargado de almacén revis{o la solicitud pero no la aprobó
		if (($reg[validaAlmacen] == "1") AND ($reg[validaEncargado] == "0") AND ($reg[comentaEncargado] != "")) { ?>
		<img src="img/images/No.gif" alt="En Revisi&oacute;n encargado Almac&eacute;n sin aprobaci&oacute;n" width="12" height="16"><? } ?>		
		<? 
		//Si validaAlmacen = 1 y validaEncargado = 1  y valida Director = 1 y comentaDirector es diferente de vacio
		//es porque el encargado de almacén revis{o la solicitud pero no la aprobó
		if (($reg[validaAlmacen] == "1") AND ($reg[validaEncargado] == "1") AND ($reg[validaDirector] == "0") AND ($reg[comentaDirector] != "")) { ?>
		<img src="img/images/No.gif" alt="En Revisi&oacute;n Director Almac&eacute;n sin aprobaci&oacute;n" width="12" height="16"><? } ?>		
		
		<? 
		//Si validaAlmacen = 1 y validaEncargado = 1 y validaDirector = 1
		//La solicitud se aprobó por parte de almacén
		if (($reg[validaAlmacen] == "1") AND ($reg[validaEncargado] == "1") AND ($reg[validaDirector] == "1")) { ?>
		<img src="img/images/Si.gif" alt="Aprobado por Almac&eacute;n" width="16" height="14"><? } ?>		</td>
        <td width="5%" align="center"><input name="Submit3" type="submit" class="Boton" onClick="MM_goToURL('parent','rptElementosDet.php?cualSec=<? echo $reg[secuencia]; ?>&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>');return document.MM_returnValue" value="Detalle"></td>
        </tr>
	  <?
	  }
	  ?>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Solicitud de pasajes </td>
      </tr>
    </table>
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td colspan="2">Solicitud</td>
        <td width="20%" rowspan="2">Usuario que realiz&oacute; la solicitud </td>
        <td width="5%" rowspan="2">C&oacute;digo</td>
        <td width="5%" rowspan="2">Cargo</td>
        <td rowspan="2">Ruta Ida /<br>
          Regreso </td>
        <td width="10%" rowspan="2">Fecha Salida Ida / Regreso </td>
        <td width="8%" rowspan="2">Hora Salida Ida / Regreso </td>
        <td colspan="4">Proceso Solicitud</td>
        <td width="5%" rowspan="2">&nbsp;</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="5%">No.</td>
        <td width="10%">Fecha</td>
        <td width="5%">Finalizada Usuario </td>
        <td width="5%">Jefe Inmediato </td>
        <td width="5%">Jefe 2da Aut</td>
        <td width="5%">Servicios Generales </td>
        </tr>
	  <?
	  while ($reg2=mssql_fetch_array($cursor2)) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%"><? echo $reg2[secuencia]; ?></td>
        <td width="10%">
		<? echo date("M d Y ", strtotime($reg2[fechaSolicitud])); ?>
		</td>
        <td width="20%">
		  <?
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sqlU="Select * from usuarios where unidad =" . $reg2[unidad];
		$cursorU = mssql_query($sqlU);
		if ($regU=mssql_fetch_array($cursorU)) {
			echo "[" .$regU[unidad] . "] " . ucwords(strtolower($regU[nombre])) . " " . ucwords(strtolower($regU[apellidos])) ;
		}
		?></td>
        <td width="5%"><? echo $reg2[codigo]; ?></td>
        <td width="5%"><? echo $reg2[cargo]; ?></td>
        <td><? echo $reg2[rutaIda] . " /<br> " . $reg2[rutaRegreso]; ?></td>
        <td width="10%">
		<? echo date("M d Y ", strtotime($reg2[fechaIda])) . " /<br> " . date("M d Y ", strtotime($reg2[fechaRegreso])); ?>
		</td>
        <td width="8%">
		<? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg2[jornadaIda] == "1") {
			$miJornadaIda = "AM";
		}
		if ($reg2[jornadaIda] == "2") {
			$miJornadaIda = "PM";
		}
		
		if ($reg2[jornadaRegreso] == "1") {
			$miJornadaReg = "AM";
		}
		if ($reg2[jornadaRegreso] == "2") {
			$miJornadaReg = "PM";
		}
		
		?>
		<? echo $reg2[horaIda] . ":" . $reg2[minutosIda] . " " . $miJornadaIda . " /<br>" . $reg2[horaRegreso] . ":" . $reg2[minutosRegreso] . " " . $miJornadaReg; ?>
		</td>
        <td width="5%" align="center">
		<? 
		//Si enviaAJefe = 1 entonces la solicitud fué enviada al Jefe inmediato
		if (($reg2[enviaAJefe] == "1") AND ($reg2[validaUsuario] == "0")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n del jefe inmediato" width="16" height="16">
		<? } ?>	
		<? 
		//Si validaUsuario = 1 entonces la solicitud fué revisada y aprobada por el jefe inmediato
		if ($reg2[validaUsuario] == "1") { ?>
		<img src="img/images/Si.gif" alt="Finalizada">
		<? } ?>	
		</td>
        <td width="5%" align="center">
		<? 
		//Si validaJefe = 1 entonces el jefe aprobó
		if ($reg2[validaJefe] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por el jefe inmediato">
		<? } ?>
		<? 
		//Si validaJefe = 0 pero hay comentarios del jefe, es porque la revisó pero no la aprobó
		if (($reg2[validaJefe] == "0") AND ($reg2[comentaJefe] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por jefe inmediato y sin aprobar" width="12" height="16">
		<? } ?>
		</td>
        <td width="5%" align="center"><? 
		//Para determinar quien va a efectur la firma unidadJefe o UnidadJefe2
		if ($reg2[requiereFirma2] == "1" ) {
				echo "Si requiere";
		} 
		else {
				echo "N/A";
		} 
		
		?>
				<? 
		//Si validaJefe2 = 1 entonces el jefe de segunda firma aprobó
		if ($reg2[validaJefe2] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por el jefe firma 2">
		<? } ?>
		<? 
		//Si validaJefe2 = 0 pero hay comentarios del jefe firma 2, es porque la revisó pero no la aprobó
		if (($reg2[validaJefe2] == "0") AND ($reg2[comentaJefe2] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por jefe firma 2 y sin aprobar" width="12" height="16">
		<? } ?></td>
        <td width="5%" align="center">
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y validaServiciosGen = 0 y requiereFirma2 = 0 y no hay comentarios de servicios generales
		//la solicitud del pasaje pasa a trasporte para revisión
		if (($reg2[validaUsuario] == "1") AND ($reg2[validaJefe] == "1") AND ($reg2[validaServiciosGen] == "0") AND ($reg2[requiereFirma2] == "0") AND ($reg2[comentaServiciosGen] == "")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n en Servicios Generales" width="16" height="16">
		<? } ?>
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y requiereFirma2 = 1 y validaJefe2 = 1 y validaServiciosGen = 0 la solicitud pasa a servicios generales para revisión
		if (($reg2[validaUsuario] == "1") AND ($reg2[validaJefe] == "1") AND ($reg2[validaJefe2] == "1") AND ($reg2[requiereFirma2] == "1") AND ($reg2[validaServiciosGen] == "0")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n en Servicios Generales" width="16" height="16">
		<? } ?>		
		<? 
		//Si validaServiciosGen = 1 entonces Transporte aprobó
		if ($reg2[validaServiciosGen] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por Servicios Generales">
		<? } ?>
		
		<? 
		//Si validaServiciosGen = 0 pero hay comentarios de transporte, es porque la revisó pero no la aprobó
		if (($reg2[validaServiciosGen] == "0") AND ($reg2[comentaServiciosGen] != "") ) { ?>
		<img src="img/images/No.gif" alt="Revisada por Servicios Generales y sin aprobar" width="12" height="16">
		<? } ?>		
		
		</td>
        <td width="5%" align="center"><input name="Submit3" type="submit" class="Boton" onClick="MM_goToURL('parent','rptPasajesDet.php?cualSec=<? echo $reg2[secuencia]; ?>&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>');return document.MM_returnValue" value="Detalle"></td>
        </tr>
	  <?
	  }
	  ?>
    </table>	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Solicitud de transporte </td>
      </tr>
    </table>
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td colspan="2">Solicitud</td>
        <td width="25%" rowspan="2">Usuario que realiz&oacute; la solicitud </td>
        <td width="5%" rowspan="2">C&oacute;digo</td>
        <td width="5%" rowspan="2">Cargo</td>
        <td rowspan="2">Destino</td>
        <td rowspan="2">Fecha Inicio </td>
        <td rowspan="2">Hora Inicio </td>
        <td colspan="4">Proceso Solicitud</td>
        <td width="5%" rowspan="2">&nbsp;</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="5%">No.</td>
        <td width="10%">Fecha</td>
        <td width="5%">Finalizada Usuario </td>
        <td width="5%">Jefe Inmediato </td>
        <td width="5%">Jefe 2da Aut.</td>
        <td width="5%">Servicios Generales </td>
        </tr>
	  <?
	  while ($reg3=mssql_fetch_array($cursor3)) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%"><? echo $reg3[secuencia]; ?></td>
        <td width="10%"><? echo date("M d Y ", strtotime($reg3[fechaSolicitud])); ?></td>
        <td width="25%">
		  <?
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sqlU="Select * from usuarios where unidad =" . $reg3[unidad];
		$cursorU = mssql_query($sqlU);
		if ($regU=mssql_fetch_array($cursorU)) {
			echo "[" .$regU[unidad] . "] " . ucwords(strtolower($regU[nombre])) . " " . ucwords(strtolower($regU[apellidos])) ;
		}
		?></td>
        <td width="5%"><? echo $reg3[codigo]; ?></td>
        <td width="5%"><? echo $reg3[cargo]; ?></td>
        <td><? echo $reg3[destino]; ?></td>
        <td>
		<? echo date("M d Y ", strtotime($reg3[fechaInicio])); ?>
		</td>
        <td>
		<? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg3[jornada] == "1") {
			$miJornada = "AM";
		}
		if ($reg3[jornada] == "2") {
			$miJornada = "PM";
		}
		
		?>
		<? echo $reg3[horaInicio] . ":" . $reg3[minutosInicio] . " " . $miJornada; ?></td>
        <td width="5%" align="center">
		<? 
		//Si enviaAJefe = 1 entonces la solicitud fué enviada al Jefe inmediato
		if (($reg3[enviaAJefe] == "1") AND ($reg3[validaUsuario] == "0")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n del jefe inmediato" width="16" height="16">
		<? } ?>	
		<? 
		//Si validaUsuario = 1 entonces la solicitud fué revisada y aprobada por el jefe inmediato
		if ($reg3[validaUsuario] == "1") { ?>
		<img src="img/images/Si.gif" alt="Finalizada">
		<? } ?>	
		</td>
        <td width="5%" align="center">
		<? 
		//Si validaJefe = 1 entonces el jefe aprobó
		if ($reg3[validaJefe] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por el jefe inmediato">
		<? } ?>
		<? 
		//Si validaJefe = 0 pero hay comentarios del jefe, es porque la revisó pero no la aprobó
		if (($reg3[validaJefe] == "0") AND ($reg3[comentaJefe] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por jefe inmediato y sin aprobar" width="12" height="16">
		<? } ?>
		</td>
        <td width="5%" align="center">
		<? 
		//Para determinar quien va a efectur la firma unidadJefe o UnidadJefe2
		if ($reg3[requiereFirma2] == "1" ) {
				echo "Si requiere";
		} 
		else {
				echo "N/A";
		} 
		
		?>
				<? 
		//Si validaJefe2 = 1 entonces el jefe de segunda firma aprobó
		if ($reg3[validaJefe2] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por el jefe firma 2">
		<? } ?>
		<? 
		//Si validaJefe2 = 0 pero hay comentarios del jefe firma 2, es porque la revisó pero no la aprobó
		if (($reg3[validaJefe2] == "0") AND ($reg3[comentaJefe2] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por jefe firma 2 y sin aprobar" width="12" height="16">
		<? } ?>
		
		</td>
        <td width="5%" align="center">
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y requiereFirma2 = 0 y validaServiciosGen = 0 y no hay comentarios de servicios generales
		//la solicitud pasa a trasporte para revisión
		if (($reg3[validaUsuario] == "1") AND ($reg3[validaJefe] == "1") AND ($reg3[requiereFirma2] == "0") AND ($reg3[validaServiciosGen] == "0") AND ($reg3[comentaServiciosGen] == "")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n en Transporte" width="16" height="16">
		<? } ?>
		<? 
		//Si validaUsuario = 1 y validaJefe = 1 y requiereFirma2 = 1 y validaJefe = 1 y validaServiciosGen = 0
		//y no hay comentarios de servicios generales, la solicitud pasa a trasporte para revisión
		if (($reg3[validaUsuario] == "1") AND ($reg3[validaJefe] == "1") AND ($reg3[requiereFirma2] == "1") AND ($reg3[validaJefe2] == "1") AND ($reg3[validaServiciosGen] == "0") AND ($reg3[comentaServiciosGen] == "")) { ?>
		<img src="img/images/EnRevision.gif" alt="En revisi&oacute;n en Transporte" width="16" height="16">
		<? } ?>
		<? 
		//Si validaServiciosGen = 1 entonces Transporte aprobó
		if ($reg3[validaServiciosGen] == "1") { ?>
		<img src="img/images/Si.gif" alt="Aprobada por Transporte">
		<? } ?>
		
		<? 
		//Si validaServiciosGen = 0 pero hay comentarios de transporte, es porque la revisó pero no la aprobó
		if (($reg3[validaServiciosGen] == "0") AND ($reg3[comentaServiciosGen] != "")) { ?>
		<img src="img/images/No.gif" alt="Revisada por Transporte y sin aprobar" width="12" height="16">
		<? } ?>		
		
		</td>
        <td width="5%" align="center"><input name="Submit3" type="submit" class="Boton" onClick="MM_goToURL('parent','rptTransporteDet.php?cualSec=<? echo $reg3[secuencia]; ?>&pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>');return document.MM_returnValue" value="Detalle"></td>
        </tr>
	  <?
	  }
	  ?>
    </table>	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Vi&aacute;ticos</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Usuario</td>
        <td>Actividad</td>
        <td width="8%">Fecha Inicio </td>
        <td width="8%">Fecha finalizaci&oacute;n </td>
        <td width="10%">Sitio</td>
        <td width="10%">Tipo de vi&aacute;tico </td>
      </tr>
	  <?
	  while ($reg4=mssql_fetch_array($cursor4)) {
	  ?>
	  
      <tr class="TxtTabla">
        <td><? echo "[" . $reg4[unidad] . "] " . $reg4[nomUsuario] . " " .  $reg4[apeUsuario] ; ?></td>
        <td><? echo $reg4[nomActividad]; ?></td>
        <td width="8%"><? echo date("M d Y ", strtotime($reg4[FechaIni])); ?></td>
        <td width="8%"><? echo date("M d Y ", strtotime($reg4[FechaFin])); ?></td>
        <td width="10%"><? echo $reg4[NomSitio]; ?></td>
        <td width="10%"><? echo $reg4[NomTipoViatico]; ?></td>
      </tr>
	  <? } ?>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" valign="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;	</td>
    <td align="right" valign="bottom"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close();')" value="Cerrar Ventana"></td>
  </tr>
</table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
</table>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
