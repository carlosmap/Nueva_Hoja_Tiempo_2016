<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//18Feb2013
//PBM
//Trae los sistemas de cobro
$sql01="SELECT * ";
$sql01=$sql01." FROM TipoCobroProy ";
$cursor01 = mssql_query($sql01);

//18Feb2013
//PBM
//Trae los valores indicados para valor y sistema de cobro 
$sql02="SELECT * ";
$sql02=$sql02." FROM GestiondeInformacionDigital.dbo.SolicitudCodigo ";
$sql02=$sql02." WHERE secuencia in ( ";
$sql02=$sql02." SELECT DISTINCT secuencia ";
$sql02=$sql02." FROM GestiondeInformacionDigital.dbo.OrdenadorGasto ";
$sql02=$sql02." WHERE id_proyecto =  " . $kProyecto;
$sql02=$sql02." ) ";
$cursor02 = mssql_query($sql02);
if ($reg02=mssql_fetch_array($cursor02)) {
	$scValor= $reg02[contratoValor];
	$scSistemaCobro= $reg02[sistemaCobro];
}

//18Feb2013
//PBM
//Trae los valores de Fecha inicial, Valor y Sistema de cobro
if (trim($recarga) == "") {
	$sql03="SELECT *  ";
	$sql03=$sql03." FROM Proyectos  ";
	$sql03=$sql03." WHERE id_proyecto =  " . $kProyecto;
	$cursor03 = mssql_query($sql03);
	if ($reg03=mssql_fetch_array($cursor03)) {
		if (trim($reg03[fechaInicio]) != "") {
			$lFecha = date("n/d/Y", strtotime($reg03[fechaInicio]));
		}
		else {
			$lFecha = "";
		}
		$Valor =  $reg03[valorProyecto];
		$lstCobro =  $reg03[idTipoCobro];
		$nombreProyecto = $reg03[nombre];
	}
}


//Si se presionó el botón Grabar
if ($recarga == "2") {

	//Realiza la actualización de la información en la tabla Proyectos
	$query = "UPDATE Proyectos SET ";
	$query = $query. " fechaInicio = '" . $lFecha . "', ";
	$query = $query. " valorProyecto = " . $Valor .", " ;
	$query = $query. " idTipoCobro = " . $lstCobro . ", ";
	$query = $query . " fechaMod ='". gmdate ("n/d/y") ."', " ;
	$query = $query . " usuarioMod = '". $laUnidad . "' " ;
	$query = $query. " WHERE id_proyecto = " . $miProyecto;
	$cursor = mssql_query($query);
	
	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		//*****Realiza el envío del correo electrónico
		include("fncEnviaMailPEAR.php");
		
		$Asunto = "Especificaciones del proyecto " ;
		
		//Trae la información del sistema de cobro seleccionado
		$listaCobro="";
		$qry="SELECT * FROM TipoCobroProy WHERE idTipoCobro = " . $lstCobro;
		$cursorQry = mssql_query($qry);
		if ($regQry=mssql_fetch_array($cursorQry)) {
			$listaCobro= $regQry[nomTipoCobro];
		}
		
		//Armar el correo
		$pTema = "<table width='100%'  border='0' cellspacing='1' cellpadding='0'>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td width='30%'>Asunto:</td>";
		$pTema = $pTema . "		<td  width='70%'>".$Asunto." </td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td width='30%'>Proyecto:</td>";
		$pTema = $pTema . "		<td width='70%'>".strtoupper($nomProyecto)."</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td width='30%'>Fecha de inicio (mm/dd/YYYY):</td>";
		$pTema = $pTema . "		<td width='70%'>".$lFecha."</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td width='30%'>Valor del proyecto:</td>";
		$pTema = $pTema . "		<td width='70%'>". number_format($Valor, 0, ",", ".") ."</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td>Sistema de cobro:</td>";
		$pTema = $pTema . "		<td>".$listaCobro."</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td>Quién asignó:</td>";
		$pTema = $pTema . "		<td>".$_SESSION["sesNomApeUsuario"]."</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "	</tr>";
		if ($Valor != $vlSC) {
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>Observaciones</td>";
			$pTema = $pTema . "		<td>El valor definido en la solicitud de código ($".number_format($vlSC, 0, ",", ".").") y el valor del proyecto aquí definido no son equivalentes. Por favor verifiuqe la información.</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "	</tr>";
		}
		$pTema = $pTema . "	<tr class='Estilo2'>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "		<td>&nbsp;</td>";
		$pTema = $pTema . "	</tr>";
		$pTema = $pTema . "</table>";
	
		$pFirma = ""; 

		$Descripcion = $mensajeUsu;
		//cABECERAS
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$cabeceras .= "From: Portal Ingetec S.A. <portal@ingetec.com.co>" . "\r\n";

		//Envía correo electrónico a Director, coordinador, Ordenador de gasto
		$sqlMail="SELECT email  ";
		$sqlMail=$sqlMail." FROM usuarios " ;
		$sqlMail=$sqlMail." where retirado is null ";
		$sqlMail=$sqlMail." and unidad in ( ";
		$sqlMail=$sqlMail." 	select id_director unidadMail ";
		$sqlMail=$sqlMail." 	from Proyectos ";
		$sqlMail=$sqlMail." 	where id_proyecto = " . $miProyecto;
		$sqlMail=$sqlMail." 	UNION ";
		$sqlMail=$sqlMail." 	select id_coordinador unidadMail ";
		$sqlMail=$sqlMail." 	from Proyectos ";
		$sqlMail=$sqlMail." 	where id_proyecto = " . $miProyecto;
		$sqlMail=$sqlMail." 	UNION ";
		$sqlMail=$sqlMail." 	select unidadOrdenador unidadMail ";
		$sqlMail=$sqlMail." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto ";
		$sqlMail=$sqlMail." 	where id_proyecto = " . $miProyecto;
		$sqlMail=$sqlMail." ) ";
		$cursorMail = mssql_query($sqlMail) ;	
		$mailEnvio = "";
		while ($regMail=mssql_fetch_array($cursorMail)) {
			$mailEnvio = $regMail[email];
			
			//****************ENVIA CORREO************************
			$pPara= trim($mailEnvio) . "@ingetec.com.co";
			$pAsunto= $Asunto;
			
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			//**********FIN DE LA FUNCION ENVIA CORREO ***********
		}	
		
		//*****
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=600,height=400');</script>");	
}

?>
<html>
<head>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="ts_picker.js"></script>


<script language="JavaScript" type="text/JavaScript">


function envia1(){ 
//alert ("Entro a envia 1");
	document.Form1.recarga.value="1";
	document.Form1.submit();
}


function envia2(){ 
	var v1,v2,v3, v4,v5,v6, v7,v8,v9, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, msg7, msg8, msg9, msg10, msg11, msg12, msg13, msg14, msg15, mensaje;
	v1='s';
	v2='s';
	v3='s';
	v4='s';
	v5='s';
	v6='s';
	v7='s';
	v8='s';
	v9='s';
	v10='s';
	v11='s';
	v12='s';
	v13='s';
	v14='s';
	v15='s';
	msg1 = '';
	msg2 = '';
	msg3 = '';
	msg4 = '';
	msg5 = '';
	msg6 = '';
	msg7 = '';
	msg8 = '';
	msg9 = '';
	msg10 = '';
	msg11 = '';
	msg12 = '';
	msg13 = '';
	msg14 = '';
	msg15 = '';
	mensaje = '';

	if (document.Form1.lFecha.value == '') {
		v1='n';
		msg1 = 'La fecha de inicio es obligatoria. \n'
	}

	if (document.Form1.Valor.value == '') {
		v2='n';
		msg2 = 'El valor es obligatorio y numérico. \n'
	}
	else {
		if (isNaN(document.Form1.Valor.value)) {
			v2='n';
			msg2 = 'El valor es obligatorio y numérico. \n'
		}

/*		
		alert(parseFloat(document.Form1.vlSC.value));
		alert(parseFloat(document.Form1.Valor.value));
		alert(parseFloat(document.Form1.vlSC.value) != parseFloat(document.Form1.Valor.value));
		alert((isNaN(parseFloat(document.Form1.vlSC.value))) && (isNaN(parseFloat(document.Form1.Valor.value))));
*/		
//		if ( (isNaN(document.Form1.vlSC.value)) && (isNaN(document.Form1.Valor.value)) ) {
			if ( parseFloat(document.Form1.vlSC.value) != parseFloat(document.Form1.Valor.value) ) {
				//v4='s'; Esta comentariado porque solo es notificación. No va a cerrar
				msg4 = 'El valor definido en la solicitud de código y el valor del proyecto aquí definido no son equivalentes. \n' ;
			}
//		}
	}

	if (document.Form1.lstCobro.value == '') {
		v3='n';
		msg3 = 'El sistema de cobro es obligatorio. \n'
	}


//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s') && (v10=='s') && (v11=='s') && (v12=='s') && (v13=='s') && (v14=='s') && (v15=='s')) {
		if (msg4 != '') {
			alert (msg4);
		}
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6 + msg7 + msg8 + msg9 + msg10 + msg11 + msg12 + msg13 + msg14 + msg15;
		alert (mensaje);
	}
}
//-->
</script>

</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<form action="" method="post" name="Form1" >
  <tr>
    <td bgcolor="#FFFFFF">
	  
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="img/images/Pixel.gif" width="4" height="4"></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Especificaciones generales del proyecto </td>
  </tr>
</table>

		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="TituloTabla">Valor definido en la solicitud de c&oacute;digo </td>
            <td class="TxtTabla">
			<?	echo "$ " . number_format($scValor, 2, ",", ".");	?>
            <input name="vlSC" type="hidden" id="vlSC" value="<? echo $scValor; ?>">
</td>
          </tr>
          <tr>
            <td class="TituloTabla">Sistema de cobro solicitud de c&oacute;digo </td>
            <td class="TxtTabla"><? 
			switch ($scSistemaCobro) {
				case 1:
					echo "Sueldos topes";
					break;
				case 2:
					echo "Tarifas";
					break;
				case 3:
					echo "Tarifas con multiplicador";
					break;
				case 4:
					echo "Contrato a precio fijo";
					break;
			}
 
			?></td>
          </tr>
          <tr>
            <td height="5" colspan="2" class="TituloTabla"> </td>
          </tr>
          <tr>
            <td width="25%" class="TituloTabla">Fecha de inicio del proyecto </td>
            <td class="TxtTabla">
			<input name="lFecha" class="CajaTexto" id="lFecha"  value="<? echo $lFecha; ?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.Form1.lFecha);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
			</td>
          </tr>
          <tr>
            <td class="TituloTabla">Valor del proyecto </td>
            <td class="TxtTabla"><input name="Valor" type="text" class="CajaTexto" id="Valor" value="<? echo $Valor;  ?>" size="30" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
          </tr>
          <tr>
            <td width="25%" class="TituloTabla">Sistema de cobro </td>
            <td class="TxtTabla">
			<select name="lstCobro" class="CajaTexto" id="lstCobro">
			<option value="" selected >:: Seleccione Sistema de cobro ::</option>
			<?
			while ($reg01=mssql_fetch_array($cursor01)) {
				if (trim($lstCobro) == $reg01[idTipoCobro]) {
					$selSC = "selected" ;
				}
				else {
					$selSC = "" ;
				}
			?>
              <option value="<? echo $reg01[idTipoCobro]; ?>" <? echo $selSC; ?> ><? echo $reg01[nomTipoCobro]; ?></option>
			  <? } ?>
            </select></td>
          </tr>
		  
        </table>
		
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla">      <input name="nomProyecto" type="hidden" id="nomProyecto" value="<? echo $nombreProyecto; ?>">      <input name="miProyecto" type="hidden" id="miProyecto" value="<? echo $kProyecto; ?>">            <input name="recarga" type="hidden" id="recarga" value="1">      
	<input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()">	</td>
  </tr>
	
</table>
      
  	</td>
  </tr>
  </form>
</table>

</body>
</html>

<? mssql_close ($conexion); ?>	