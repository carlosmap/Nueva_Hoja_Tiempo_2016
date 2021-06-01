<?php
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?
//Establecer la conexión a la base de datos
//$conexion = conectar();

//Econtrar el ultimo horario establecido para el pico y placa
$mQry = "select * "; 
$mQry = $mQry . " from GestiondeInformacionDigital.dbo.PicoPlaca "; 
$mQry = $mQry . " where secuencia = (select max(secuencia) ultimoPico from PicoPlaca) "; 
$mQry = $mQry . " and getdate() <= fechaFin  "; 
$cursor = mssql_query($mQry);
if ($reg=mssql_fetch_array($cursor)) {
	$zp1a = $reg[p1a];
	$zp2a = $reg[p2a];
	$zp3a = $reg[p3a];
	$zp4a = $reg[p4a];
	$zp5a = $reg[p5a];
	$zp6a = $reg[p6a];
	$zp7a = $reg[p7a];
	$zp8a = $reg[p8a];
	$zp9a = $reg[p9a];
	$zp0a = $reg[p0a];
	$zp1b = $reg[p1b];
	$zp2b = $reg[p2b];
	$zp3b = $reg[p3b];
	$zp4b = $reg[p4b];
	$zp5b = $reg[p5b];
	$zp6b = $reg[p6b];
	$zp7b = $reg[p7b];
	$zp8b = $reg[p8b];
	$zp9b = $reg[p9b];
	$zp0b = $reg[p0b];
}
$diaA = "";
$diaB = "";

//Seleccionar los registros de SolicitudTransporte
$sql="SELECT S.*, T.nomTiempo, V.numVehiculo ";
$sql= $sql. " FROM GestiondeInformacionDigital.dbo.SolicitudTransporte S, GestiondeInformacionDigital.dbo.Tiempos T, GestiondeInformacionDigital.dbo.vehiculos V ";
$sql= $sql. " WHERE S.codTiempo *= t.codTiempo ";
$sql= $sql. " AND S.placa *= V.placa ";
$sql= $sql. " AND S.secuencia  = " . $cualSec;
$cursor = mssql_query($sql);

//Seleccionar los registros del personal que debe recogerse con la solicitud
//registros de PersonalSolTransporte
$sql3= " SELECT * FROM GestiondeInformacionDigital.dbo.PersonalSolTransporte ";
$sql3= $sql3. " WHERE secuencia = " . $cualSec;
$cursor3 = mssql_query($sql3);

//Seleccionar los registros del tiempo trabajado con la solicitud
//registros de TiempoTrabajo
$sql5= " SELECT * FROM TGestiondeInformacionDigital.dbo.iempoTrabajo ";
$sql5= $sql5. " WHERE secuencia = " . $cualSec;
$cursor5 = mssql_query($sql5);
//Para validar que se haya ingresado tiempo de trabajo para mostrar el botón Confirmar Servicio
$numFilasTT = 0;
$numFilasTT = mssql_num_rows($cursor5);


//Para validar que el personal de la solicitud tenga por lo menos 1 registro en el detalle de la solicitud
$numFilas = 0;
$numFilas = mssql_num_rows($cursor3);

//Se envia como par{ametro de observaciones para la adición
$cualObserv = "";


?>
<html>
<head>
<title>Reportes de los Proyectos - Transporte</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winSolTran";
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:594px; height:33px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota3">SOLICITUD DE SERVICIO DE TRANSPORTE</td>
    </tr>
  </table>
</div>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Solicitud de Servicio de Transporte </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
		  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="12%" class="TituloTabla">Solicitud No </td>
        <td width="22%" class="TxtTabla"><? echo $reg[secuencia]; ?></td>
        <td width="13%" class="TituloTabla">Fecha</td>
        <td width="22%" class="TxtTabla"><? echo date("M d Y ", strtotime($reg[fechaSolicitud])); ?></td>
        <td width="12%" class="TituloTabla">
Usuario		</td>
        <td class="TxtTabla">
          <?

		$miNomUsuario = "";
		$miDepto = "";
		$miDivision = "";
		$miDependencia = "";

		//Consulta para traer Dependencia, división, departamento
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql2="select u.unidad, u.nombre, u.apellidos, d.nombre as departamento, d.id_division,  ";
		$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ";
		$sql2= $sql2. " from usuarios u, departamentos d, divisiones v, dependencias x ";
		$sql2= $sql2. " where u.unidad =" .$reg[unidad];
		$sql2= $sql2. " and u.id_departamento = d.id_departamento";
		$sql2= $sql2. " and d.id_division = v.id_division ";
		$sql2= $sql2. " and v.id_dependencia = x.id_dependencia ";
		$cursor2 = mssql_query($sql2);
		if ($reg2=mssql_fetch_array($cursor2)) {
			$miNomUsuario = $reg2[nombre] . " " .$reg2[apellidos];
			$miDepto = $reg2[departamento];
			$miDivision = $reg2[division];
			$miDependencia = $reg2[dependencia];
		}
		?>
		<? echo ucwords(strtolower($miNomUsuario)); ?>
		</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Dependencia</td>
        <td width="22%" class="TxtTabla">
		
		<? echo ucwords(strtolower($miDependencia)); ?>		</td>
        <td width="13%" class="TituloTabla">Divisi&oacute;n</td>
        <td width="22%" class="TxtTabla"><? echo ucwords(strtolower($miDivision)); ?></td>
        <td width="12%" class="TituloTabla">Departamento</td>
        <td class="TxtTabla"><? echo ucwords(strtolower($miDepto)); ?></td>
      </tr>
      <tr>
        <td class="TituloTabla">Proyecto</td>
        <td class="TxtTabla"><?
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql2="Select * from Proyectos where id_proyecto =" . $reg[id_proyecto];
		$cursor2 = mssql_query($sql2);
		if ($reg2=mssql_fetch_array($cursor2)) {
			echo ucwords(strtolower($reg2[nombre]));
		}
		?>
          <? //echo $reg[id_proyecto]; ?></td>
        <td width="13%" class="TituloTabla">C&oacute;digo</td>
        <td class="TxtTabla"><? echo $reg[codigo]; ?></td>
        <td class="TituloTabla">Cargo</td>
        <td class="TxtTabla"><? echo $reg[cargo]; ?></td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Destino</td>
        <td width="22%" class="TxtTabla"><? echo $reg[destino]; ?></td>
        <td width="13%" class="TituloTabla">Fecha de Inicio del servicio </td>
        <td width="22%" class="TxtTabla">
		<? echo date("M d Y ", strtotime($reg[fechaInicio])); ?></td>
        <td width="12%" class="TituloTabla">Hora de Inicio </td>
        <td class="TxtTabla"><? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg[jornada] == "1") {
			$miJornada = "AM";
		}
		if ($reg[jornada] == "2") {
			$miJornada = "PM";
		}
		
		if (strlen($reg[minutosInicio]) == 1) {
			$miMinutos = "0" . $reg[minutosInicio];
		}
		else {
			$miMinutos = $reg[minutosInicio];
		}
		
		?>
		<? echo $reg[horaInicio] . ":" . $miMinutos . " " . $miJornada; ?></td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Observaciones</td>
        <td colspan="3" class="TxtTabla"><? 
		$cualObserv = $reg[Observaciones];
		echo $reg[Observaciones]; ?>
          <? 
		  //**Para mostrar en la sección Servicio
		  $numeroVehiculo = $reg[numVehiculo];
		  $PlacaVehiculo = $reg[placa];
		  $uniConductor = $reg[unidadConductor];
		  $salFecha = $reg[fechaSalida];
		  $salHora = $reg[horaSalida];
		  $salMinutos = $reg[minutosSalida];
		  $salJornada = $reg[jornadaSalida];
		  //**
		  
		  //**Para mostrar el botón conirmar servicio
		  $hayAprobacionSG = $reg[validaServiciosGen];
		  $hayAprobacionConf = $reg[confirmaUsuario];
		  //**
		  
		$estadoVU = $reg[validaUsuario];
		//echo $reg[validaUsuario]; ?> </td>
        <td width="12%" class="TituloTabla">Tiempo de utilizaci&oacute;n del servicio </td>
        <td class="TxtTabla"><? echo $reg[cantTiempo] . " ". $reg[nomTiempo]; ?></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
      </table>
	  
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloTabla">&iquest;Solicitud enviada a jefe? </td>
        <td class="TxtTabla">
		<? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[enviaAJefe] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[enviaAJefe] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			$pEnviaJefe = $reg[enviaAJefe];
			
		?>
		<input name="radiobutton3" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
          Si&nbsp;&nbsp;
          <input name="radiobutton3" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
          No
		</td>
        <td class="TituloTabla">&iquest;Requiere segunda firma de autorizaci&oacute;n?</td>
        <td colspan="3" class="TxtTabla"><? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[requiereFirma2] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[requiereFirma2] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
		?>
          <input name="radiobutton4" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si&nbsp;&nbsp;
<input name="radiobutton4" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No </td>
        </tr>
      <tr>
        <td width="12%" class="TituloTabla">&iquest;Solicitud autorizada? </td>
        <td width="22%" class="TxtTabla"><? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[validaJefe] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[validaJefe] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
		?>
		<input name="radiobutton2" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
          Si&nbsp;&nbsp;
          <input name="radiobutton2" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
          No</td>
        <td width="13%" class="TituloTabla">&iquest;Solicitud autorizada firma 2? </td>
        <td width="22%" class="TxtTabla">
		<? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[validaJefe2] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[validaJefe2] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
		?>
          <input name="radiobutton5" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si&nbsp;&nbsp;
<input name="radiobutton5" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No		</td>
        <td width="12%" class="TituloTabla">&iquest;Solicitud autorizada en Transporte?</td>
        <td class="TxtTabla">
		<? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[validaServiciosGen] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[validaServiciosGen] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
		?>
          <input name="radiobutton6" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si&nbsp;&nbsp;
<input name="radiobutton6" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No		</td>
      </tr>
      <tr>
        <td class="TituloTabla">Jefe que autoriza </td>
        <td width="22%" class="TxtTabla">
		<?
		$miUsuarioJefe = "";
		//Consulta para traer el nombre del jefe que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadJefe]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioJefe = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioJefe)); ?>
		</td>
        <td width="13%" class="TituloTabla">Jefe que autoriza firma 2 </td>
        <td width="22%" class="TxtTabla">
		<?
		$miUsuarioJefe2 = "";
		//Consulta para traer el nombre del jefe que autoriza firma 2
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadJefe2]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioJefe2 = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioJefe2)); ?>
		</td>
        <td width="12%" class="TituloTabla">Encargado de Transporte que autoriza </td>
        <td class="TxtTabla"><?
		$miUsuarioTran = "";
		//Consulta para traer el nombre del encargado de Transporte a cargo
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadServiciosGen]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioTran = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioTran)); ?>
		</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Comentario quien autoriza solicitud </td>
        <td width="22%" class="TxtTabla"><? echo $reg[comentaJefe]; ?></td>
        <td width="13%" class="TituloTabla">Comentario jefe que autoriza firma 2 </td>
        <td width="22%" class="TxtTabla"><? echo $reg[comentaJefe2]; ?></td>
        <td width="12%" class="TituloTabla">Comentario encargado  Transporte </td>
        <td class="TxtTabla2"><? echo $reg[comentaServiciosGen]; ?></td>
      </tr>
    </table>
	      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>      
	      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="12%" class="TituloTabla">&iquest;Confirma el uso del servicio? </td>
              <td width="22%" class="TxtTabla">
			  <? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[confirmaUsuario] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[confirmaUsuario] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
		?>
          <input name="radiobutton7" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si&nbsp;&nbsp;
<input name="radiobutton7" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No			  </td>
              <td width="13%" class="TituloTabla">Observaciones de la confirmaci&oacute;n del servicio </td>
              <td class="TxtTabla"><? echo $reg[comentaConfUsu]; ?></td>
            </tr>
          </table>	      <? } ?>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Personal que debe recoger </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">No.</td>
        <td>Nombre</td>
        <td width="20%">Direcci&oacute;n</td>
        <td width="10%">Tel&eacute;fono</td>
        <td width="10%">Hora</td>
        </tr>
	  <?
	  while ($reg3=mssql_fetch_array($cursor3)) {
	  ?>
		
      <tr class="TxtTabla">
        <td width="5%"><? echo $reg3[item]; ?></td>
        <td>
		<? echo ucwords(strtolower($reg3[nombre])); ?>
		</td>
        <td width="20%"><? echo $reg3[direccion]; ?></td>
        <td width="10%" align="right"><? echo $reg3[telefono]; ?></td>
        <td width="10%" align="right">
		<? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg3[jornada] == "1") {
			$miJornada = "AM";
		}
		if ($reg3[jornada] == "2") {
			$miJornada = "PM";
		}
		
		if (strlen($reg3[minutos]) == 1) {
			$miMinutos = "0" . $reg3[minutos];
		}
		else {
			$miMinutos = $reg3[minutos];
		}

		
		?>
		<? echo $reg3[hora] . ":" . $miMinutos . " " . $miJornada; ?>		</td>
        </tr>
	  <? } ?>
    </table></td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td align="right">&nbsp;
		</td>
      </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../images/Pixel.gif" width="4" height="4"></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Servicio</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td colspan="3">Veh&iacute;culo</td>
        <td rowspan="2">Nombre del conductor</td>
        <td colspan="2">Salida del parqueadero </td>
        </tr>
      <tr class="TituloTabla2">
        <td width="8%">N&uacute;mero</td>
        <td width="10%">Placa</td>
        <td width="10%">D&iacute;as de Restricci&oacute;n </td>
        <td width="15%">Fecha</td>
        <td width="10%">Hora</td>
      </tr>
      <tr class="TxtTabla">
        <td width="8%">
		<? echo $numeroVehiculo ;?>		</td>
        <td width="10%"><? echo $PlacaVehiculo; ?></td>
        <td width="10%" align="center">
		<? 
			$ultNum = substr(trim($PlacaVehiculo), -1, 1); 
			$campoA = "zp" . $ultNum . "a" ;
			$campoB = "zp" . $ultNum . "b" ;
			switch (${$campoA}) {
			   case 1:
				   $diaA = "Lunes";
				   break;
			   case 2:
				   $diaA = "Martes";
				   break;
			   case 3:
				   $diaA = "Miércoles";
				   break;
			   case 4:
				   $diaA = "Jueves";
				   break;
			   case 5:
				   $diaA = "Viernes";
				   break;
			}
			switch (${$campoB}) {
			   case 1:
				   $diaB = "Lunes";
				   break;
			   case 2:
				   $diaB = "Martes";
				   break;
			   case 3:
				   $diaB = "Miércoles";
				   break;
			   case 4:
				   $diaB = "Jueves";
				   break;
			   case 5:
				   $diaB = "Viernes";
				   break;
			}
			echo $diaA . "<br>" . $diaB;

		?>		</td>
        <td>
		<?
		$miUsuarioConductor = "";
		//Consulta para traer el nombre del jefe que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $uniConductor; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioConductor = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioConductor)); ?>		</td>
        <td width="15%">
		<? 
		if (trim($salFecha) != '') { 
			echo date("M d Y ", strtotime($salFecha)); 
		}
		?></td>
        <td width="10%" align="center">
		<? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($salJornada == "1") {
			$miJornada = "AM";
		}
		if ($salJornada == "2") {
			$miJornada = "PM";
		}
		
		if (strlen($salMinutos) == 1) {
			$miMinutos = "0" . $salMinutos;
		}
		else {
			$miMinutos = $salMinutos;
		}
		
		?>
		<? echo $salHora . ":" . $miMinutos . " " . $miJornada; ?>		</td>
      </tr>
    </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><img src="../images/Pixel.gif" width="4" height="4"></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Tiempo de trabajo </td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="10%">Item</td>
            <td>Fecha </td>
            <td>Desde</td>
            <td>Hasta</td>
          </tr>
          	  <?

	  while ($reg5=mssql_fetch_array($cursor5)) {
	  ?>

		  <tr class="TxtTabla">
            <td width="10%"><? echo $reg5[dia]; ?></td>
            <td align="center"><? echo date("M d Y ", strtotime($reg5[fechaServicio])); ?></td>
            <td>
			
		<? echo $reg5[horaDesde] ; ?>
			</td>
            <td>
			
		<? echo $reg5[horaHasta] ; ?>
			</td>
          </tr>
		<? } ?>
        </table>
		<table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td align="right" class="TxtTabla">&nbsp;		</td>
      </tr>
</table>

		</td>
      </tr>
    </table>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="rptProySolicitudes.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>"><img src="img/images/flechaAtras1.gif" alt="Regresar al listado de solicitudes" width="50" height="44" border="0"></a></td>
    <td align="right" valign="bottom">&nbsp;</td>
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
