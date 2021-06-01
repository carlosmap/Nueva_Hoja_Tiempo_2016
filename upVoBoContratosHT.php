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
//Funcion para el envio de correos 
include("fncEnviaMailPEAR.php");

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
//vigencia, mes, unidad, fechaEnvio, unidadJefe, validaJefe, comentaJefe, fechaAprueba, unidadContratos, validaContratos, comentaContratos, 
//fechaContratos, seImprimio, usuarioCrea, fechaCrea, usuarioMod, fechaMod
$sql="Select * from VoBoFirmasHT ";
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
	$msgM=$msgM."     <td class='Estilo2'>Por favor consultar la Intranet <a href='http://www.ingetec.com.co/Intranet' target='_blank'>http://www.ingetec.com.co/Intranet</a></td> \n";
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
	$msgM=$msgM."     <td bgcolor='#999999' ><span class='Estilo1'>INGETEC </span></td>";
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
	$sqlNC=$sqlNC." where Unidad = " . $_SESSION["sesUnidadUsuario"] ;
	$cursorNC = mssql_query($sqlNC) ;	
	if ($regNC=mssql_fetch_array($cursorNC)) {
		$ncU = $regNC[NombreCorto] ;
	}	
	//25Nov2008
	$Asunto3 = "Hoja de tiempo - " . ucwords(strtolower($nombreUsu)) . " - [" .  ucwords(strtolower($ncU)) . "]";
	
	
	$Descripcion = $mensajeUsu;
	//cABECERAS
	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$cabeceras .= "From: Intranet INGETEC <portal@ingetec.com.co>" . "\r\n";

	
	if ($mailUsu != "") {
		//$cualMail= trim($mailUsu) . "@ingetec.com.co";
		//mail($cualMail,$Asunto,$Descripcion, $cabeceras); 
		
		//****************ENVIA CORREO************************
		$pPara= trim($mailUsu) . "@ingetec.com.co";
		$pAsunto= $Asunto;
		$pTema = $Descripcion;
		$pFirma = "Departamento de Contratos";
		
		enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		//**********FIN DE LA FUNCION ENVIA CORREO ***********
		
		
		
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
				
				//****************ENVIA CORREO************************
				$pPara= trim($mailMJefe) . "@ingetec.com.co";
				$pAsunto= $Asunto2;
				$pTema = $Descripcion;
				$pFirma = "Departamento de Contratos";
				
				enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
				//**********FIN DE LA FUNCION ENVIA CORREO ***********
				
				//$cualMailJefe = trim($mailMJefe) . "@ingetec.com.co";
				//mail($cualMailJefe,$Asunto2,$Descripcion, $cabeceras); 
				
				//03Sep2009
				//PBM--> Si el Jefe es CME, envía copia del correo a María Isabel Muñoz (16119 - mariamunoz@ingetec.com.co )
				//26Jul2012 Se saca a María Isabel por su retiro y se agrega a Mary Ruth Guevara (18164 - maryguevara@ingetec.com.co) y a Katherine Pinilla (900384 - katherinepinilla@ingetec.com.co)
/*
//
//PBM
//Quité el siguiente if porque las tres personas maria muñoz, mary guevara y katherine pinilla ya se retiraron de la compañía entonces no aplica.
				if ($laUnidadJefe == 14384) {
					//$cualMailJefe = "mariamunoz@ingetec.com.co";
					//mail($cualMailJefe,$Asunto2,$Descripcion, $cabeceras); 
					

					//****************ENVIA CORREO************************
					//$pPara= "mariamunoz@ingetec.com.co";
					$pPara= "maryguevara@ingetec.com.co";
					$pAsunto= $Asunto2;
					$pTema = $Descripcion;
					$pFirma = "Departamento de Contratos";
					
					enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
					
					$pPara= "katherinepinilla@ingetec.com.co";
					enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
					
					//**********FIN DE LA FUNCION ENVIA CORREO ***********
					
				}
*/				

			}
			
			//Mail de contratos
			//$cualMailContratos = "epineros@ingetec.com.co";
			//mail($cualMailContratos,$Asunto2,$Descripcion, $cabeceras); 
			//mail($cualMailContratos,$Asunto3,$Descripcion, $cabeceras); 


			//****************ENVIA CORREO************************
			$pPara= "epineros@ingetec.com.co";
			$pAsunto= $Asunto3;
			$pTema = $Descripcion;
			$pFirma = "Departamento de Contratos";
			
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			//**********FIN DE LA FUNCION ENVIA CORREO ***********
		}
		//Cierra23Nov2007
		
		//sI LA CATEGORIA DEL USUARIO ES 60 o 42 se trata de un conductor
		//y se envía copia a jmahecha
		if (($categoriaUsu == 60) OR ($categoriaUsu == 42)) {
		//if (($categoriaUsu == "D") OR ($categoriaUsu == "O")) {
//		if ((trim($categoriaUsu) == "60") OR (trim($categoriaUsu) == "05")) {
			
			//****************ENVIA CORREO************************
			$pPara= "jmahecha@ingetec.com.co";
			$pAsunto= $Asunto2;
			$pTema = $Descripcion;
			$pFirma = "Departamento de Contratos";
			
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			//**********FIN DE LA FUNCION ENVIA CORREO ***********
					
			//mail("jmahecha@ingetec.com.co",$Asunto2,$Descripcion, $cabeceras); 
			//mail("pbaron@ingetec.com.co",$Asunto2,$Descripcion, $cabeceras); 
		}
	}
	

	//Verifica si el registro ya existe en la tabla VoBoFirmasHT para 
	//Determinar si se inserta o se modifica.
	$query = "UPDATE  VoBoFirmasHT SET "; 
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

		//VERIFICA, SI SE SELECCIONO, EL LEVANTE DE LOS VOBO DE APROBACION EN LA FACTURACION
		$error="no";
		$aprueba="pAprueba";
		//SI LA HOJA DE TIEMPO NO SE APRUEBA, POR PARTE DE CONTRATOS
		//SE VERIFICAN LOS VOBO DE LOS PROYECTOS
		if($$aprueba=="0")
		{	
			$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");		
	
			//--Trae los proyectos en los que una persona tiene facturación agrupada así:
			//--Proyecto, Actividad, Horario, clase de tiempo, localización, cargo
			$sql02="SELECT DISTINCT A.id_proyecto, A.id_actividad,B.nombre ";
			$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C " ;
			$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
			$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
			$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
			$sql02=$sql02." AND A.unidad = " . $cualUnidad ;
			$sql02=$sql02." AND A.mes = " . $mesAut ;
			$sql02=$sql02." AND A.vigencia = " . $anoAut ;
			$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad,B.nombre " ;
			//$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
			$sql02=$sql02." ORDER BY B.nombre,A.id_proyecto, A.id_actividad  " ;
			$cursor02 =	 mssql_query($sql02);
	//echo $sql02." <br>".mssql_get_last_message();

			while ($reg02 = mssql_fetch_array($cursor02)) 
			{
				$radio="o".$reg02["id_proyecto"]."_".$reg02["id_actividad"];
				if($$radio==1)
				{
					$sql_up="update VoBoFactuacionProyHT set validaEncargado=0, usuarioMod=" . $laUnidad." , fechaMod=GETDATE()
	where id_proyecto=".$reg02["id_proyecto"]." and id_actividad=".$reg02["id_actividad"]." and unidad=" . $cualUnidad." and vigencia=" . $anoAut ." and mes=" . $mesAut ." and esInterno='I'";
					$cur_up=mssql_query($sql_up);
					if(trim($cur_up)=="")
						$error="si";
//					echo $sql_up."<BR>";
				}
			}
	
			if($error=="no")
			{
				$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");
//				echo ("<script>alert('Operación realizada exitosamente.');<script>");
			}		
			if($error=="si")
			{
				$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
//				echo ("<script>alert('Error en la operación.');<script>");	
			}

		}

	//Si los cursores no presentaron problema
	if  ((trim($cursor) != "")&&($error=="no"))
	{
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('htContratosHT.php?pMes=".$elMes."&pAno=".$elAno."&pEmpresa=".$pEmpresa."&pDivision=".$pDivision."&pDepto=".$pDepto."&pUnidad=".$pUnidad."&pCategoria=".$pCategoria."&pRetirado=".$pRetirado."&pNombre=".$pNombre."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=*,height=*');</script>");
	
	}
	

?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">

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

function dis(valor)
{
	var  arr=  document.Form1.radi_id1.value.split(","),valss;
// new Array (document.Form1.radi_id1.value.split(","));
	for(var i=1; i<arr.length ;i++)
	{
		if(valor==1)
		{
			expr1='document.Form1.o'+arr[i]+'[0].disabled=true';
			eval(expr1);
			expr1='document.Form1.o'+arr[i]+'[1].disabled=true';
			eval(expr1);
		}
		if(valor==0)
		{
			expr1='document.Form1.o'+arr[i]+'[0].disabled=false';
			eval(expr1);
			expr1='document.Form1.o'+arr[i]+'[1].disabled=false';
			eval(expr1);
		}
/*
alert(arr[i]+" ---- "+document.getElementById(valss)[0].value);
*/
/*
		document.getElementById(arr[i])[0].disabled=true;
		document.getElementById(arr[i])[1].disabled=true;

		document.getElementById(arr[i])[1].checked=true;
*/
	}
}
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
	<input name="pAprueba" id="pAprueba" type="radio" class="CajaTexto" value="1" <? echo $selSI; ?> onClick="dis(1);" >
      Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      <input name="pAprueba" id="pAprueba" type="radio" class="CajaTexto" onClick="dis(0);" value="0" <? echo $selNo; ?>>
      No</td>
  </tr>
  
  <tr>
    <td class="TituloTabla">Comentarios</td>
    <td class="TxtTabla"><textarea name="pComenta"  cols="50" rows="4" class="CajaTexto" id="pComenta"><? echo $pcomentaContratos; ?></textarea></td>
  </tr>  
<?
//--Trae los proyectos en los que una persona tiene facturación agrupada así:
//--Proyecto, Actividad, Horario, clase de tiempo, localización, cargo
$sql02="SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  ";
//$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion , A.esInterno" ;
$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
$sql02=$sql02." AND A.unidad = " . $cualUnidad ;
$sql02=$sql02." AND A.mes = " . $mesAut ;
$sql02=$sql02." AND A.vigencia = " . $anoAut ;
$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
//$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion, A.esInterno " ;
$sql02=$sql02." ORDER BY B.nombre ,A.id_proyecto, A.id_actividad 
 " ;
$cursor02 =	 mssql_query($sql02);

?>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><table width="100%" border="0" bgcolor="#FFFFFF" >
      <tr class="TituloTabla">
        <td>Proyecto/Actividad</td>
        <td width="10%">Horario</td>
        <td width="10%">Loc.</td>
        <td width="10%">CT</td>
        <td width="10%">Cargo</td>
        <td width="5%">Total Horas</td>
        <td width="5%">&nbsp;</td>
        <td width="5%"><table width="100%" border="0" bgcolor="#FFFFFF" >
  <tr>
    <td colspan="2" class="TituloTabla2">Levantar VoBo</td>
    </tr>
  <tr>
    <td width="50%" class="TituloTabla2">Si</td>
    <td width="50%" class="TituloTabla2">No</td>
  </tr>
</table>
</td>
      </tr>
		  <?
		  $i=0;
		  while ($reg02 = mssql_fetch_array($cursor02)) {
		  ?>

            <tr >
              <td class="TxtTabla" ><? echo "<B>[" . $reg02['codigo'] . "." . $reg02['cargo_defecto'] . "]</B> " . strtoupper($reg02['nombre']) ; ?><br><? echo "<B>[" . $reg02['macroactividad'] . "]</B> " . strtoupper($reg02['nomActividad'])  ; ?></td>
              <td width="10%" class="TxtTabla" >
                <? 
			  //Trae el Horario de lines a domingo
			  $cpHorario="";
			  $sql03="SELECT * FROM Horarios ";
			  $sql03=$sql03." WHERE IDhorario = " .$reg02['IDhorario'];
			  $cursor03 =	 mssql_query($sql03);
			  if ($reg03 = mssql_fetch_array($cursor03)) {
			  	$cpHorario="[". $reg03['Lunes'] . "-" . $reg03['Martes'] . "-" . $reg03['Miercoles'] . "-" . $reg03['Jueves'] . "-" . $reg03['Viernes'] . "-" . $reg03['Sabado'] . "-" . $reg03['Domingo'] . "] " ;
			  }
			  echo $cpHorario; 
			  ?>			  </td>
              <td width="10%" align="center" class="TxtTabla"><? echo $reg02['localizacion']; ?></td>
              <td width="10%" align="center" class="TxtTabla"><? echo trim(substr($reg02['descripcion'], 0, 2));  ?></td>
			  <td width="10%" align="center" class="TxtTabla"><? echo $reg02['cargo']; ?></td>
			  <td width="5%" class="TxtTabla">
<?
				$sql06="SELECT sum(horasMesF) horasMesF2 ";
				$sql06=$sql06." FROM FacturacionProyectos ";
				$sql06=$sql06." WHERE unidad = " . $cualUnidad ;
				$sql06=$sql06." AND mes = " . $mesAut ;
				$sql06=$sql06." AND vigencia = " . $anoAut ;
				$sql06=$sql06." AND id_proyecto = " . $reg02['id_proyecto'] ;
				$sql06=$sql06." AND id_actividad = " . $reg02['id_actividad'] ;
				$sql06=$sql06." AND IDhorario = " . $reg02['IDhorario'] ;
				$sql06=$sql06." AND clase_tiempo = " . $reg02['clase_tiempo'] ;
				$sql06=$sql06." AND localizacion = " . $reg02['localizacion'] ;
				$sql06=$sql06." AND cargo = '" . $reg02['cargo'] . "' ";
				$cursor06 =	 mssql_query($sql06);
				if ($reg06 = mssql_fetch_array($cursor06)) {
					echo $reg06['horasMesF2'];
				}
			  ?>
			  </td>
<td width="5%" class="TxtTabla">
			  <?
			  //Verifica si el proyecto ya tiene VoBo en la facturación
			  //id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, unidadEncargado, validaEncargado, comentaEncargado, fechaAprEnc, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			  $tieneVBproy="";
			  $fechaVBproy="";
			  $encargadoVBproy="";
			  $sql13 = "SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
			  $sql13 = $sql13 . " FROM VoBoFactuacionProyHT A, Usuarios B ";
			  $sql13 = $sql13 . " WHERE A.unidadEncargado = B.unidad ";
			  $sql13 = $sql13 . " AND A.id_proyecto = " . $reg02['id_proyecto'] ;
			  $sql13 = $sql13 . " AND A.id_actividad = " . $reg02['id_actividad'] ;
			  $sql13 = $sql13 . " AND A.unidad = " . $cualUnidad ;
			  $sql13 = $sql13 . " AND A.vigencia = " . $anoAut ;
			  $sql13 = $sql13 . " AND A.mes = " . $mesAut ;
			  $sql13 = $sql13 . " AND A.esInterno = '" . $reg02['esInterno'] . "'";
			  $cursor13 =	 mssql_query($sql13);
//echo mssql_get_last_message()." --*** <br>".$sql13;
		      if ($reg13 = mssql_fetch_array($cursor13)) {
			  		$tieneVBproy = $reg13['validaEncargado'];
	 			  	$fechaVBproy = date("M d Y ", strtotime($reg13['fechaAprEnc'])) ;
			  		//$encargadoVBproy = $reg13['apellidos'] . " " . $reg13['nombre'] ;
					$encargadoVBproy = $reg13['NombreCorto']  ;
			  }
			  
			  ?>
			  <? if ($tieneVBproy == '1') { ?>
              		<img src="img/images/Aprobado.gif" width="21" height="24" /> <br>
			  <? } ?>
			  <? if ($tieneVBproy == '0') { ?>
			  		<img src="img/images/NoAprobado.gif" /> <br>
			  <? } ?>
			  <?
			  		echo $fechaVBproy . "<br>";
					echo $encargadoVBproy . "<br>";
			  ?>
<?
				$can_fil="";
				//SI SE ESTA EXTRYENDO LA INFORMACION DE UNA ACTIVIDAD, O PROYECTO DIFERENTE, SE EJECUTA LA CONSULTA
				if( ($actiss!= $reg02['id_actividad'])||($proyy!= $reg02['id_proyecto']) )
				{
					//CONSULT, QUE PERMITE SABER, CUANTOS REGISTROS ASOCIADOS A UNA SOLA ACTIVIDAD, TIENEN DITERENTE HORARIO, LOCALIZACIO, CLASE t. Y CARGO
					//CON EL FIN DE PODER DETERMINAR EL TAMAÑO DEL ROWSAPN 
					$sql_activs="select COUNT(*) cant_reg  from (
						select   distinct id_proyecto,id_actividad,unidad,vigencia,mes,IDhorario,clase_tiempo,localizacion,cargo 
						from FacturacionProyectos
						where id_proyecto=" . $reg02['id_proyecto']." and id_actividad=" . $reg02['id_actividad'] ." and 
						unidad=".$cualUnidad." and vigencia=".$anoAut." and mes=".$mesAut." and esInterno='I' 
					) T1
					group by id_proyecto,id_actividad";
					$dato_activis=mssql_fetch_array(mssql_query($sql_activs));
					$can_fil=$dato_activis["cant_reg"];
//echo $dato_activis["cant_reg"]." ---- ";
					$actiss=$reg02['id_actividad'];
					$proyy=$reg02['id_proyecto'];
				}
?>
			  </td>
<?

			if($can_fil!="")
			{
?>
			  <td width="5%" align="center" class="TxtTabla" rowspan="<?=$can_fil ?>" >
<?
				//SI LA ACTIVIDAD, TIENE VOBO EN APROBADO, SE PERMITE LEVANTAR EL VOBO 
				 if ($tieneVBproy == '1') 
				 { 
					$radi_id[$i]=$reg02["id_proyecto"]."_".$reg02["id_actividad"];
					$i++;
?>
				<table width="100%" border="0">
			    <tr>
			      <td width="50%">

					<input type="radio" name="o<?=$reg02["id_proyecto"]."_".$reg02["id_actividad"]; ?>" value="1" id="o<?=$reg02["id_proyecto"]."_".$reg02["id_actividad"];?>" >
			        <label for="radio"></label></td>
			      <td width="50%"><input type="radio" name="o<?=$reg02["id_proyecto"]."_".$reg02["id_actividad"]; ?>" id="o<?=$reg02["id_proyecto"]."_".$reg02["id_actividad"];?>" value="2" checked ></td>
			      </tr>
			    </table>
<?
				}
?>
				</td>
<?
			}
		
?>
		</tr>
<?
/*
			$array_nom[$i]=$reg02["id_proyecto"]."_".$reg02["id_actividad"]."_".$reg02["clase_tiempo"]."_".$reg02["IDhorario"]."_".$reg02["localizacion"]."_".$reg02["cargo"];
			$i++;
*/

		}
?>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla">
	<input name="laUnidadJefe" type="hidden" id="laUnidadJefe" value="<? echo $punidadJef; ?>">
	<input name="Submit" type="submit" class="Boton" value="Grabar">

	<input type="hidden" name="radi_id1" id="radi_id1" value="<? echo "0"; foreach ($radi_id as $uu) { echo ",".$uu; }?>" >
		</td>
    </tr>
  </form>
</table>

	</td>
  </tr>
</table>

</body>
</html>
