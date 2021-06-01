<?php
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?
//Establecer la conexión a la base de datos
//$conexion = conectar();

//Seleccionar los registros de SolicitudPasajes
$sql="Select S.*, A.nombre nombreAgencia, E.nombre nombreempresaA, U.nombre, U.apellidos ";
$sql= $sql. " from GestiondeInformacionDigital.dbo.SolicitudPasajes S, ";
$sql= $sql. " GestiondeInformacionDigital.dbo.Agencias A, GestiondeInformacionDigital.dbo.EmpresaAerea E, ";
$sql= $sql. " HojaDeTiempo.dbo.Usuarios U ";
$sql= $sql. " where S.codAgencia *= A.codAgencia ";
$sql= $sql. " and S.codEmpresa *= E.codEmpresa ";
$sql= $sql. " and S.unidad = U.unidad ";
$sql= $sql. " AND S.secuencia  = " . $cualSec;
$cursor = mssql_query($sql);



?>
<html>
<head>
<title>Reportes de los Proyectos - Pasajes</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winSolPasaje";
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
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:594px; height:33px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota1">SOLICITUD DE PASAJES A&Eacute;REOS</td>
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
    <td class="TituloUsuario">Solicitud de Pasajes A&eacute;reos </td>
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
        <td class="TituloTabla">Ruta Ida </td>
        <td class="TxtTabla"><? echo $reg[rutaIda]; ?></td>
        <td class="TituloTabla">Fecha de Salida</td>
        <td class="TxtTabla"><? echo date("M d Y ", strtotime($reg[fechaIda])); ?></td>
        <td class="TituloTabla">Hora de Salida </td>
        <td class="TxtTabla">
		<? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg[jornadaIda] == "1") {
			$miJornada = "AM";
		}
		if ($reg[jornadaIda] == "2") {
			$miJornada = "PM";
		}
		
		if (strlen($reg[minutosIda]) == 1) {
			$miMinutos = "0" . $reg[minutosIda];
		}
		else {
			$miMinutos = $reg[minutosIda];
		}
		
		?>
		<? echo $reg[horaIda] . ":" . $miMinutos . " " . $miJornada; ?>
		</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Ruta Regreso </td>
        <td width="22%" class="TxtTabla"><? echo $reg[rutaRegreso]; ?></td>
        <td width="13%" class="TituloTabla">Fecha de Salida </td>
        <td width="22%" class="TxtTabla">
		<? echo date("M d Y ", strtotime($reg[fechaRegreso])); ?></td>
        <td width="12%" class="TituloTabla">Hora de Salida </td>
        <td class="TxtTabla"><? 
		//si Jornaja = 1 es AM, si es 2 es PM
		if ($reg[jornadaRegreso] == "1") {
			$miJornada = "AM";
		}
		if ($reg[jornadaRegreso] == "2") {
			$miJornada = "PM";
		}
		
		if (strlen($reg[minutosRegreso]) == 1) {
			$miMinutos = "0" . $reg[minutosRegreso];
		}
		else {
			$miMinutos = $reg[minutosRegreso];
		}
		
		?>
		<? echo $reg[horaRegreso] . ":" . $miMinutos . " " . $miJornada; ?></td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">C&eacute;dula</td>
        <td class="TxtTabla"><? echo $reg[cedula]; ?>          </td>
        <td class="TituloTabla">Celular</td>
        <td colspan="3" class="TxtTabla"><? echo $reg[celular] ; ?></td>
        </tr>
      <tr>
        <td class="TituloTabla">Observaciones</td>
        <td colspan="5" class="TxtTabla"><? echo $reg[comentaUsuario]; ?>
          <? 
		  
		  //**Para mostrar el botón conirmar servicio
		  $hayAprobacionSG = $reg[validaServiciosGen];
		  $hayAprobacionUs = $reg[confirmaUsuario];
		  //**
		  
		$estadoVU = $reg[validaUsuario];
		//echo $reg[validaUsuario]; ?></td>
        </tr>
    </table>
	      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>
	      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="12%" class="TituloTabla">Agencia proveedora </td>
              <td width="22%" class="TxtTabla"><? echo $reg[nombreAgencia]; ?></td>
              <td width="13%" class="TituloTabla">Tiquete N&ordm; </td>
              <td width="22%" class="TxtTabla"><? echo $reg[tiqueteNumero]; ?></td>
              <td width="12%" class="TituloTabla">Factura N&ordm; </td>
              <td class="TxtTabla"><? echo $reg[facturaNumero]; ?></td>
            </tr>
            <tr>
              <td width="12%" class="TituloTabla">Empresa A&eacute;rea</td>
              <td width="22%" class="TxtTabla"><? echo $reg[nombreempresaA]; ?></td>
              <td width="13%" class="TituloTabla">Fecha Remisi&oacute;n Tiquete </td>
              <td width="22%" class="TxtTabla">
			  <? 
//			  echo is_null($reg[fechaRemision]) . "<br>" ;
//			  if ((trim($reg[fechaRemision]) != "") AND !is_null($reg[fechaRemision]))  {
			  if (!is_null($reg[fechaRemision]))  {
				  echo date("M d Y ", strtotime($reg[fechaRemision])); 
			  }
			  ?>
			  
			  </td>
              <td width="12%" class="TituloTabla">Valor del Tiquete </td>
              <td class="TxtTabla"><? echo "$" . $reg[valorTiquete]; ?></td>
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
        <td colspan="5" class="TxtTabla">
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
        <td width="13%" class="TituloTabla">Jefe que autoriza </td>
        <td width="22%" class="TxtTabla"><?
		$miUsuarioJefe = "";
		//Consulta para traer el nombre del jefe que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadJefe]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioJefe = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
          <? echo ucwords(strtolower($miUsuarioJefe)); ?></td>
        <td width="12%" class="TituloTabla">Comentario quien autoriza solicitud </td>
        <td class="TxtTabla"><? echo $reg[comentaJefe]; ?> </td>
      </tr>
      <tr>
        <td class="TituloTabla">&iquest;Requiere segunda firma de autorizaci&oacute;n?</td>
        <td colspan="5" class="TxtTabla"><? 
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
          <input name="radiobutton4f" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si&nbsp;&nbsp;
<input name="radiobutton4f" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No </td>
        </tr>
      <tr>
        <td class="TituloTabla">&iquest;Solicitud autorizada firma 2?</td>
        <td class="TxtTabla"><? 
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
          <input name="radiobutton5f" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si&nbsp;&nbsp;
<input name="radiobutton5f" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No		</td>
        <td class="TituloTabla">Jefe que autoriza firma 2 </td>
        <td class="TxtTabla"><?
		$miUsuarioJefe2 = "";
		//Consulta para traer el nombre del jefe que autoriza firma 2
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadJefe2]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioJefe2 = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioJefe2)); ?></td>
        <td class="TituloTabla">Comentario jefe que autoriza firma 2 </td>
        <td class="TxtTabla"><? echo $reg[comentaJefe2]; ?></td>
      </tr>
      <tr>
        <td class="TituloTabla">&iquest;Solicitud autorizada en Transporte?</td>
        <td width="22%" class="TxtTabla"><? 
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
No 
		</td>
        <td width="13%" class="TituloTabla">Encargado de Transporte que autoriza </td>
        <td width="22%" class="TxtTabla"><?
		$miUsuarioTran = "";
		//Consulta para traer el nombre del encargado de Transporte a cargo
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadServiciosGen]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioTran = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
          <? echo ucwords(strtolower($miUsuarioTran)); ?> </td>
        <td width="12%" class="TituloTabla">Comentario encargado Transporte </td>
        <td class="TxtTabla"><? echo $reg[comentaServiciosGen]; ?></td>
      </tr>
    </table>
	      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>      
	      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="12%" class="TituloTabla">&iquest;Confirma el uso del pasaje? </td>
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
              <td width="13%" class="TituloTabla">Observaciones de la confirmaci&oacute;n del pasaje </td>
              <td class="TxtTabla"><? echo $reg[comentaConfUsu]; ?></td>
            </tr>
          </table>	      
	      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td class="TxtTabla">El valor del tiquete de este pasaje quedar&aacute; a mi cargo hasta el momento en que presente la Cuenta de Gastos debidamente aprobada y acompa&ntilde;ada de la car&aacute;tula. dentro de los cinco (5) d&iacute;as posteriores a su utilizaci&oacute;n. Si no utilizao el pasaje me comprometo a </td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td class="TxtTabla">En caso de no proceder de la forma antes indicada autorizo a INGETEC S.A. para que descuente de mi salario y/o prestaciones sociales el valor del pasaje. </td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table>
  	      <? } ?>
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
    <td align="right" valign="bottom"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close()')" value="Cerrar Solicitud de Pasajes A&eacute;reos"></td>
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
