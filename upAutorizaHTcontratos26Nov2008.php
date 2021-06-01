<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

//-->
</script>
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
	$pvalidaJefe = $reg[validaJefe] ;
	$pcomentaJefe = $reg[comentaJefe] ;
	$punidadJef = $reg[unidadJefe] ;
	$pvalidaContratos = $reg[validaContratos] ;
	$pcomentaContratos = $reg[comentaContratos] ;
	$punidadContratos = $reg[unidadContratos] ;
}


//Si se presionó el botón Grabar
if ($elAno != "") {
	//Diseñar el mensaje de envio
	if ($pAprueba == "1") {
		$mensajeUsu = "La hoja de tiempo del mes " . $elMes . " y año " . $elAno .  " fué tramitada satisfactoriamente en Contratos. ";
		$mensajeUsu = $mensajeUsu .  " El Departamento de Contratos la imprimirá y usted la firmará en el momento en que reciba el comprobante de pago de nómina. Gracias." . " \n \n";
	}
	if ($pAprueba == "0") {
		$mensajeUsu = "La hoja de tiempo del mes " . $elMes . " y año " . $elAno .  " no pudo aprobarse por el siguiente motivo: \n \n ";
		$mensajeUsu = $mensajeUsu .  $pComenta . " \n \n";
	}

	//Encontrar el Mail del usuario en la Hoja de tiempo
	$mailUsu = "";
	$sqlU="select U.* , C.nombre nomCategoria ";
	$sqlU=$sqlU." from usuarios U, categorias C " ;
	$sqlU=$sqlU." where U.id_categoria = C.id_categoria " ;
	$sqlU=$sqlU." and U.Unidad = " . $laUnidadUsu ;
	$cursorU = mssql_query($sqlU) ;	
	if ($regU=mssql_fetch_array($cursorU)) {
		$mailUsu = $regU[email];
		$categoriaUsu = $regU[nomCategoria];
		$nombreUsu = $regU[nombre] . " " . $regU[apellidos] ;
	}	
	
	//Hace el envio de los correos de los Relacionados en Copias
	//***Arma el mensaje
	$msgM="<html>";
	$msgM=$msgM." <head>";
	$msgM=$msgM." <title></title>";
	$msgM=$msgM." <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
	$msgM=$msgM." <style type='text/css'> ";
	$msgM=$msgM." <!-- ";
	$msgM=$msgM." .Estilo1 { ";
	$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
	$msgM=$msgM." 	font-weight: bold; ";
	$msgM=$msgM." 	font-size: 12px; ";
	$msgM=$msgM." 	color: #FFFFFF; ";
	$msgM=$msgM." } ";
	$msgM=$msgM." .Estilo2 { ";
	$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
	$msgM=$msgM." 	color: #666666; ";
	$msgM=$msgM." 	font-size: 12px; ";
	$msgM=$msgM." } ";
	$msgM=$msgM." --> ";
	$msgM=$msgM." </style>";
	$msgM=$msgM." </head>";
	$msgM=$msgM." <body> ";
	$msgM=$msgM." <table width='100%'  border='0' cellspacing='0' cellpadding='0'>";
	$msgM=$msgM."   <tr> ";
	$msgM=$msgM."     <td height='10' bgcolor='#999999'></td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Estimado usuario: </td> \n\n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td><span class='Estilo2'>" . $mensajeUsu . " </span></td> \n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Por favor consultar el Portal <a href='http://www.ingetec.com.co/portal' target='_blank'>www.ingetec.com.co/portal</a></td> \n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Atentamente,</td> \n\n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Departamento Contratos - Sistemas </td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr >";
	$msgM=$msgM."     <td bgcolor='#999999' ><span class='Estilo1'>Ingetec S.A. </span></td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM." </table>";
	$msgM=$msgM." </body>";
	$msgM=$msgM." </html>";
	//***
	
	$Asunto = "Información de la Hoja de tiempo";
	$Asunto2 = "Información de la Hoja de tiempo - " . ucwords(strtolower($nombreUsu)) ;
	
	//25Nov2008
	//Encontrar nombre corto de quien tramita la HT en contratos
	$ncU = "";
	$sqlNC="select NombreCorto  ";
	$sqlNC=$sqlNC." from HojaDeTiempo.dbo.usuarios " ;
	$sqlNC=$sqlNC." and Unidad = " . $_SESSION["sesUnidadUsuario"] ;
	$cursorNC = mssql_query($sqlNC) ;	
	if ($regNC=mssql_fetch_array($cursorNC)) {
		$ncU = $regNC[NombreCorto] ;
	}	
	//25Nov2008
	$Asunto3 = "Hoja de tiempo - " . ucwords(strtolower($nombreUsu)) . " - [" .  ucwords(strtolower($ncU)) . "]";
	
	
	$Descripcion = $msgM;
	//cABECERAS
	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$cabeceras .= "From: Portal Ingetec S.A. <portal@ingetec.com.co>" . "\r\n";

	
	if ($mailUsu != "") {
		$cualMail= trim($mailUsu) . "@ingetec.com.co";
		mail($cualMail,$Asunto,$Descripcion, $cabeceras); 

		//23Nov2007
		//Si el mail se devuelve hay que enviar correo al jefe que firma y a enrique piñeros
		
		if ($pAprueba == "0") {
			//Encontrar el Mail del jefe en la Hoja de tiempo
			$mailMJefe = "";
			$cualMailJefe = "";
			$cualMailContratos = "";
			
			$sqlMJ="select U.* ";
			$sqlMJ=$sqlMJ." from usuarios U " ;
			$sqlMJ=$sqlMJ." where U.Unidad = " . $laUnidadJefe ;
			$cursorMJ = mssql_query($sqlMJ) ;	
			if ($regMJ=mssql_fetch_array($cursorMJ)) {
				$mailMJefe = $regMJ[email];
			}	
			
			//Envia mail a Jefe inmediato
			if ($mailMJefe != "") {
				$cualMailJefe = trim($mailMJefe) . "@ingetec.com.co";
				mail($cualMailJefe,$Asunto2,$Descripcion, $cabeceras); 
			}
			
			//Mail de contratos
			$cualMailContratos = "epineros@ingetec.com.co";
			//mail($cualMailContratos,$Asunto2,$Descripcion, $cabeceras); 
			mail($cualMailContratos,$Asunto3,$Descripcion, $cabeceras); 
		}
		//Cierra23Nov2007
		
		//sI LA CATEGORIA DEL USUARIO ES 60 o 42 se trata de un conductor
		//y se envía copia a jmahecha
		if (($categoriaUsu == 60) OR ($categoriaUsu == 42)) {
//		if ((trim($categoriaUsu) == "60") OR (trim($categoriaUsu) == "05")) {
			mail("jmahecha@ingetec.com.co",$Asunto2,$Descripcion, $cabeceras); 
			//mail("pbaron@ingetec.com.co",$Asunto2,$Descripcion, $cabeceras); 
		}
	}
	

	//Verifica si el registro ya existe en la tabla AutorizacionsHT para 
	//Determinar si se inserta o se modifica.
	$query = "UPDATE  AutorizacionesHT SET "; 
	$query = $query . " unidadContratos = " . $_SESSION["sesUnidadUsuario"] . ",  ";
	$query = $query . " validaContratos = '" . $pAprueba . "',  ";
	//Para abrir la Hoja de tiempo
	if ($pAprueba == "0") {
		$query = $query . " validaJefe = '" . $pAprueba . "',  ";
	}
	$query = $query . " comentaContratos = '" . $pComenta . "',  ";
	$query = $query . " fechaContratos = '" . gmdate ("n/d/y") . "'  ";
	$query = $query . " WHERE vigencia = " . $elAno ;
	$query = $query . " AND mes = " . $elMes ;
	$query = $query . " AND unidad = " . $laUnidadUsu ;
	$cursor = mssql_query($query) ;	

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
		echo ("<script>window.close();</script>");

	
}


?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo - Revisi&oacute;n Contratos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<form action="" method="post" name="Form1" id="Form1" onSubmit="MM_validateForm('pComenta','','R');return document.MM_returnValue">
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
    <td class="TxtTabla"><? echo $cualUnidad; ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<?
		$miUsuario = "";
		//Consulta para traer el nombre del jefe que autoriza
//		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $cualUnidad ;
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuario = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo strtoupper($miUsuario); ?>
		<input name="laUnidadUsu" type="hidden" id="laUnidadUsu" value="<? echo $cualUnidad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Hoja de tiempo aprobada por Jefe de Departamento? </td>
    <td class="TxtTabla">
	<?
	//Si ya esta o no aprobada la hoja de tiempo
	if ($pvalidaJefe == "1") {
		$selSI = "checked";
		$selNo = "";
	}
	if ($pvalidaJefe == "0") {
		$selSI = "";
		$selNo = "checked";
	}
	?>
	<input name="pApruebaJefe" type="radio" class="CajaTexto" value="1" <? echo $selSI; ?> disabled >
      Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      <input name="pApruebaJefe" type="radio" class="CajaTexto" value="0" <? echo $selNo; ?> disabled>
      No</td>
  </tr>
  <tr>
    <td class="TituloTabla">Comentarios Jefe de Departamento </td>
    <td class="TxtTabla"><? echo $pcomentaJefe; ?></td>
  </tr>
  <tr>
    <td class="TituloTabla">Hoja de tiempo aprobada por Contratos? </td>
    <td class="TxtTabla">
	<?
	//Si ya esta o no aprobada la hoja de tiempo
	if ($pvalidaContratos == "1") {
		$selSI = "checked";
		$selNo = "";
	}
	if ($pvalidaContratos == "0") {
		$selSI = "";
		$selNo = "checked";
	}
	?>
	<input name="pAprueba" type="radio" class="CajaTexto" value="1" <? echo $selSI; ?> >
      Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      <input name="pAprueba" type="radio" class="CajaTexto" value="0" <? echo $selNo; ?>>
      No</td>
  </tr>
  
  <tr>
    <td class="TituloTabla">Comentarios</td>
    <td class="TxtTabla"><textarea name="pComenta" cols="50" rows="4" class="CajaTexto" id="pComenta"><? echo $pcomentaContratos; ?></textarea></td>
  </tr>  
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	<input name="laUnidadJefe" type="hidden" id="laUnidadJefe" value="<? echo $punidadJef; ?>">
	<input name="Submit" type="submit" class="Boton" value="Grabar"></td>
    </tr>
  </form>
</table>

	</td>
  </tr>
</table>

</body>
</html>
