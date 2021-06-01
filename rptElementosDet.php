<?php
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?
//Establecer la conexión a la base de datos
//$conexion = conectar();


//Seleccionar los registros de SolicitudElementos
$sql="select * from GestiondeInformacionDigital.dbo.SolicitudElementos";
$sql= $sql. " WHERE secuencia = " . $cualSec;
//echo $sql;
$cursor = mssql_query($sql);

//Seleccionar los registros del detalle de la solicitud
//registros de DetalleSolicitudElementos
$sql3= " Select D.*, U.nomUnidadMedida, E.nomEstadoElemento ";
$sql3= $sql3. " from GestiondeInformacionDigital.dbo.DetalleSolicitudElementos D, GestiondeInformacionDigital.dbo.UnidadMedida U, GestiondeInformacionDigital.dbo.EstadoElementos E ";
$sql3= $sql3. " where D.codUnidadMedida = U.codUnidadMedida";
$sql3= $sql3. " and D.codEstadoElemento *= E.codEstadoElemento ";
$sql3= $sql3. " and D.secuencia =" . $cualSec;
$cursor3 = mssql_query($sql3);

//Para validar que el detalle de la solicitud tenga por lo menos 1 registro en el detalle de la solicitud
$numFilas = 0;
$numFilas = mssql_num_rows($cursor3);


//Se envia como par{ametro de observaciones para la adición
$cualObserv = "";
?>
<html>
<head>
<script>
var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,"name","height=400,width=650, resizable=yes, scrollbars=yes");
	if (window.focus) {newwindow.focus()}
}
function muestraventana2(url)
{
	newwindow=window.open(url,"name2","height=400,width=650, resizable=0, scrollbars=0");
	if (window.focus) {newwindow.focus()}
}
</script>
<title>Reportes de los Proyectos - Elementos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winSolEle";
</script>
<SCRIPT language=JavaScript>
<!--
function mOvr(src,clrOver) {
    if (!src.contains(event.fromElement)) {
	  src.style.cursor = 'hand';
	  src.bgColor = clrOver;
	}
  }
  function mOut(src,clrIn) {
	if (!src.contains(event.toElement)) {
	  src.style.cursor = 'default';
	  src.bgColor = clrIn;
	}
  }
  function mClk(src) {
    if(event.srcElement.tagName=='TD'){
	  src.children.tags('A')[0].click();
    }
  }

//-->
</SCRIPT>
<script language="JavaScript" type="text/JavaScript">
<!--




function MM_preloadImages() { //v3.0
	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

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
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onLoad="MM_preloadImages('../images/b1.gif')" bgcolor="E6E6E6">
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:371px; height:33px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota3">SOLICITUD DE ELEMENTOS </td>
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
    <td class="TituloUsuario">Solicitud de elementos</td>
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
        <td width="12%" class="TituloTabla">Fecha</td>
        <td width="22%" class="TxtTabla"><? echo date("M d Y ", strtotime($reg[fechaSolicitud])); ?></td>
        <td width="12%" class="TxtTabla">&nbsp;</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Usuario</td>
        <td class="TxtTabla">
		<? 
		$UUDusuario = "";
		$UUDunidad = "" ;
		$UUDdocumento = "" ;
		//Trae el nombre, unidad y documento asociado a la unidad que hizo la solicitud
		$sqlUUD="
			Select U.unidad, U.nombre, U.apellidos, U.codTipodoc, U.numDocumento, T.Tipodoc
			from HojaDeTiempo.dbo.usuarios U, HojaDeTiempo.dbo.TipoDocumento T
			where U.codTipodoc *= T.codTipodoc
			and unidad = " . $reg[unidad];
			$cursorUUD = mssql_query($sqlUUD);
			if ($regUUD=mssql_fetch_array($cursorUUD)) {
				$UUDusuario = ucwords(strtolower($regUUD[nombre])) . " " . ucwords(strtolower($regUUD[apellidos]));
				$UUDunidad = $regUUD[unidad] ;
				$UUDdocumento = $regUUD[Tipodoc] . " " . $regUUD[numDocumento] ;
			}
		?>
		<? echo $UUDusuario; ?>
		</td>
        <td class="TituloTabla">Unidad</td>
        <td class="TxtTabla"><? echo $UUDunidad; ?></td>
        <td class="TituloTabla">Documento</td>
        <td class="TxtTabla"><? echo $UUDdocumento; ?></td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Dependencia</td>
        <td width="22%" class="TxtTabla">
		<?

		$miDepto = "";
		$miDivision = "";
		$miDependencia = "";

		//Consulta para traer Dependencia, división, departamento
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql2="select u.unidad, u.nombre, u.apellidos, d.nombre as departamento, d.id_division,  ";
		$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ";
		$sql2= $sql2. " from usuarios u, departamentos d, divisiones v, dependencias x ";
		$sql2= $sql2. " where u.unidad =" . $reg[unidad];
		$sql2= $sql2. " and u.id_departamento = d.id_departamento";
		$sql2= $sql2. " and d.id_division = v.id_division ";
		$sql2= $sql2. " and v.id_dependencia = x.id_dependencia ";
		$cursor2 = mssql_query($sql2);
		if ($reg2=mssql_fetch_array($cursor2)) {
			$miDepto = $reg2[departamento];
			$miDivision = $reg2[division];
			$miDependencia = $reg2[dependencia];
		}
		?>
		<? echo ucwords(strtolower($miDependencia)); ?>		</td>
        <td width="12%" class="TituloTabla">Divisi&oacute;n</td>
        <td width="22%" class="TxtTabla">
		<? echo ucwords(strtolower($miDivision)); ?>
		</td>
        <td width="12%" class="TituloTabla">Departamento</td>
        <td class="TxtTabla">
		<? echo ucwords(strtolower($miDepto)); ?>
		</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Piso</td>
        <td width="22%" class="TxtTabla">
		<? 
		if ((trim($reg[piso]) == "3") OR (trim($reg[piso]) == "4") OR (trim($reg[piso]) == "5") OR (trim($reg[piso]) == "2") OR (trim($reg[piso]) == "9") OR (trim($reg[piso]) == "13")) {
			echo "Piso " . $reg[piso]; 
		}

		if (trim($reg[piso]) == "0")  {
			echo "Sótano" ; 
		}

		if (trim($reg[piso]) == "6") {
			echo "Baños" ; 
		}
		
		?>
		</td>
        <td width="12%" class="TituloTabla">Extensi&oacute;n</td>
        <td width="22%" class="TxtTabla"><? echo $reg[extension]; ?></td>
        <td width="12%" class="TituloTabla">A cargo de </td>
        <td class="TxtTabla">
		<? 
			$selCliente = "";
			$selIngetec = "";
		
			if ($reg[aCargoDe] == "C") {
				$selCliente = "checked";
				$selIngetec = "";
			} 
			if ($reg[aCargoDe] == "I") {
				$selCliente = "";
				$selIngetec = "checked";
			} 
			
		?>
		<input name="radiobutton" type="radio" value="radiobutton" <? echo $selCliente; ?> disabled >
          Cliente 
          &nbsp;&nbsp;
          <input name="radiobutton" type="radio" value="radiobutton" <? echo $selIngetec; ?> disabled>
          Ingetec</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Proyecto</td>
        <td colspan="3" class="TxtTabla"><?
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql2="Select * from Proyectos where id_proyecto =" . $reg[id_proyecto];
		$cursor2 = mssql_query($sql2);
		if ($reg2=mssql_fetch_array($cursor2)) {
			echo ucwords(strtolower($reg2[nombre]));
		}
		?>
		<? //echo $reg[id_proyecto]; ?>		</td>
        <td width="12%" class="TituloTabla">C&oacute;digo</td>
        <td class="TxtTabla"><? echo $reg[codigo]; ?></td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Frente de trabajo </td>
        <td colspan="3" class="TxtTabla"><? echo $reg[frente]; ?></td>
        <td width="12%" class="TituloTabla">Cargo:</td>
        <td class="TxtTabla"><? echo $reg[cargo]; ?></td>
      </tr>
      <tr>
        <td class="TituloTabla">Observaciones</td>
        <td colspan="5" class="TxtTabla">
		<? 
		$cualObserv = $reg[Observaciones];
		echo $reg[Observaciones]; ?>
		<? 
		
		
		$estadoVU = $reg[validaUsuario];
		
		$estadoConfirmaUsu = $reg[confirmaUsuario];
		$estaCerrada = $reg[validaCierre];

		//echo $reg[validaUsuario]; ?>
		</td>
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
          No		</td>
        <td class="TituloTabla">&iquest;Requiere segunda firma de autorizaci&oacute;n?</td>
        <td class="TxtTabla"><? 
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
        <td class="TxtTabla">
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
		<? echo ucwords(strtolower($miUsuarioJefe)); ?>		</td>
        <td width="13%" class="TituloTabla">Jefe que autoriza firma 2 </td>
        <td class="TxtTabla">
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
		<? echo ucwords(strtolower($miUsuarioJefe2)); ?>		</td>
        </tr>
      <tr>
        <td width="12%" class="TituloTabla">Comentario quien autoriza solicitud </td>
        <td width="22%" class="TxtTabla"><? echo $reg[comentaJefe]; ?></td>
        <td width="13%" class="TituloTabla">Comentario jefe que autoriza firma 2 </td>
        <td class="TxtTabla"><? echo $reg[comentaJefe2]; ?></td>
        </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
      </table>		  
		  
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloTabla">Confirmaci&oacute;n de la solicitud de elementos</td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="12%" class="TituloTabla">Quien Confirma </td>
              <td width="22%" class="TxtTabla">
			  <?
		$miUsuarioConfirma = "";
		//Consulta para traer el nombre del encargado de almacén que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadConfUsu]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioConfirma = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioConfirma)); ?>
			  </td>
              <td width="13%" class="TituloTabla">Fecha / Comentarios </td>
              <td class="TxtTabla">
			  <? 
			  if (trim($reg[fechaConfUsu]) != "" ) {
			  	echo date("M d Y ", strtotime($reg[fechaConfUsu])) . "<br>";
			  }
			  echo $reg[comentaConfUsu];
			   ?></td>
            </tr>
          </table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
      </table>
		  <? if (($reg[validaEncargado] == 0) AND (trim($reg[comentaEncargado]) != "")) {?>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloTabla">Observaci&oacute;n primera revisi&oacute;n de alamac&eacute;n. </td>
        <td class="TxtTabla"><? echo $reg[comentaAlmacen]; ?></td>
        </tr>
      <tr>
        <td width="12%" class="TituloTabla">&iquest;Solicitud autorizada por encargado de almac&eacute;n? </td>
        <td class="TxtTabla">
		<? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[validaEncargado] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[validaEncargado] == "0") {
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
        <td class="TituloTabla">Encargado de almac&eacute;n que autoriza </td>
        <td class="TxtTabla">
		<?
		$miUsuarioAlmacen = "";
		//Consulta para traer el nombre del encargado de almacén que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadEncargado]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioAlmacen = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioAlmacen)); ?>		</td>
        </tr>
      <tr>
        <td width="12%" class="TituloTabla">Comentario encargado almac&eacute;n </td>
        <td class="TxtTabla"><? echo $reg[comentaEncargado]; ?></td>
        </tr>
    </table>
	<? } ?>
		  
		  <? } ?>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Detalle de la solicitud de elementos </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="3%" rowspan="3">No.</td>
        <td rowspan="3">Descripci&oacute;n</td>
        <td width="3%" rowspan="3">Unidad</td>
        <td width="5%" rowspan="3">Cantidad solicitada </td>
        <td width="5%" rowspan="3">Cantidad autorizada </td>
        <td colspan="7">Almac&eacute;n</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="8%" rowspan="2">Tr&aacute;mite</td>
        <td width="10%" rowspan="2">Cantidad Despachada </td>
        <td width="8%" rowspan="2">C.I <br>
          N&deg;</td>
        <td width="10%" rowspan="2">S.A <br>
          Fecha</td>
        <td width="8%" rowspan="2">Estado</td>
        <td colspan="2">Entrada de almacen </td>
        </tr>
      <tr class="TituloTabla2">
        <td width="8%">No</td>
        <td width="10%">Fecha</td>
        </tr>
	  <?

	  
	  while ($reg3=mssql_fetch_array($cursor3)) {
	  ?>
		
      <tr class="TxtTabla">
        <td width="3%"><? echo $reg3[numElemento]; ?></td>
        <td><? echo $reg3[DescElemento]; ?></td>
        <td width="3%"><? echo $reg3[nomUnidadMedida]; ?></td>
        <td width="5%" align="right"><? echo $reg3[cantSolicitada]; ?></td>
        <td width="5%" align="right"><? echo $reg3[cantAutorizada]; ?></td>
        <td width="8%" align="right"><? echo $reg3[cantEnTramite]; ?></td>
        <td width="10%" align="right"><? echo $reg3[cantDespachada]; ?></td>
        <td width="8%"><? echo $reg3[cuentaIndNumero]; ?></td>
        <td width="10%">
		<? 
		if ($reg3[salidaAlmacenFecha] != "") {
		echo date("M d Y ", strtotime($reg3[salidaAlmacenFecha])); 
		}
		?>
		</td>
        <td width="8%"><? echo $reg3[nomEstadoElemento]; ?></td>
        <td width="8%"><? echo $reg3[entradaNum]; ?></td>
        <td width="10%"><? echo $reg3[entradaFecha]; ?></td>
        </tr>
	  <? } ?>
    </table></td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TxtTabla">
        <td width="20%">C.I. Cuenta Individual </td>
        <td>S.A Salida de Almac&eacute;n </td>
      </tr>
    </table>
	<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="rptProySolicitudes.php?pMes=<? echo $pMes; ?>&pAno=<? echo $pAno; ?>"><img src="img/images/flechaAtras1.gif" alt="Regresar al listado de solicitudes" width="50" height="44" border="0"></a></td>
    <td align="right" valign="bottom"><input name="Submit2" type="submit" class="Boton" onClick="MM_callJS('window.close()')" value="Cerrar Solicitud de elementos"></td>
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
