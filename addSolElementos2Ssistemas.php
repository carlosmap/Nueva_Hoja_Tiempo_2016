<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
include("../verificaRegistro2.php");
include('../conectaBD.php');

?>

<?

//Establecer la conexión a la base de datos
$conexion = conectar();

//Traer el listado de personas que me tienen autorizado a mi
$sql2="SELECT *  FROM [HojaDeTiempo].[dbo].[Usuarios]
where retirado is null
order by apellidos";
$cursorAut = mssql_query($sql2);
//Para verificar si se muestra o no el botón para Grabar, 
//si no hay registros de personas que autorizan que haga solicitud no aparece el botón
$numAutorizar = 0;
$numAutorizar = mssql_num_rows($cursorAut);


//Encontrar la categoria vigente para la selección de usuarios
$sql="Select * from CategoriaAutoriza";
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laCategoria = $reg[id_categoria];
	}
else {
	$laCategoria= 0;
}



//Por defecto  cuando se carga la lista proyecto el item seleccionado es el primero que aparece
if ($recarga == "") {
	@mssql_select_db("HojaDeTiempo",$conexion);
	$sql2="Select * from Proyectos  ";
	$sql2=$sql2 . " where id_estado = 2 ";
	$sql2=$sql2 . " and (codigo <> 'ACC' and codigo <> 'AUS' and codigo <> 'ENF' and codigo <> 'LIC'  ";
	$sql2=$sql2 . " and codigo <> 'PER' and codigo <> 'SAN' and codigo <> 'VAC')   ";
	$sql2=$sql2 . " order by nombre ";
	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) {
		$codigoProy = $reg2[codigo];
		$cargoDefProy = $reg2[cargo_defecto];
		$elDirector = $reg2[id_director];
		$elCoordinador = $reg2[id_coordinador];
		
		$pProyecto = $reg2[id_proyecto] ;
	}
}
/*
$codigoProy = "00|";
$cargoDefProy = "0";	
$elDirector = "12974";
$elCoordinador = "Null";
*/
//
if (trim($CantidadItem) == "" or trim($CantidadItem) <= 0 ) {
	$CantidadItem= 1;}


//$recarga = 1 si se cambió de proyecto
if ($recarga == "1") {
//	echo "Solo recarga página <br> ";
	@mssql_select_db("HojaDeTiempo",$conexion);
	$sql4="Select * from Proyectos where id_proyecto = " . $pProyecto ;
	$cursor4 = mssql_query($sql4);
	if ($reg4=mssql_fetch_array($cursor4)) {
		$codigoProy = $reg4[codigo];
		$cargoDefProy = $reg4[cargo_defecto];
		$elDirector = $reg4[id_director];
		$elCoordinador = $reg4[id_coordinador];
	}

	//Consulta los cargos adicionales del proyecto seleccionado
	$sql4="Select * from cargos where id_proyecto = " . $pProyecto ;
	$cursor4 = mssql_query($sql4);


}

//$recarga = 1 si se presionó el botón Grabar
if ($recarga == "2") {
//	echo "GRABA";
	
/*	echo $pSolicitudNo . "<br>";
	echo $pFecha . "<br>";
	echo $pProyecto . "<br>";		
	echo $pCodigo . "<br>";
	echo $pCargo . "<br>";
	echo $pPiso . "<br>";		
	echo $pAcargoDe . "<br>";
	echo $pCompleto . "<br>";
	echo $pObserva . "<br>";	
	echo $pLugar . "<br>";	*/

	//Encontrar la máxima secuencia para mostrar el siguiente código
	$sqlSec="Select max(secuencia) MaxCodigo from SolicitudElementos";
	$cursorSec = mssql_query($sqlSec);
	if ($regSec=mssql_fetch_array($cursorSec)) {
		$pSolicitudNo = $regSec[MaxCodigo] + 1;
		}
	else {
		$pSolicitudNo = 1;
	}

	
	//Realiza la grabación del encabezado de la solicitud en SolicitudElemento
	/* secuencia, unidad, fechaSolicitud, lugar, piso, extension, id_proyecto, frente, codigo, cargo, aCargoDe, 
	   Observaciones, validaUsuario */

	$query = "INSERT INTO SolicitudElementos(secuencia, unidad, fechaSolicitud, lugar, piso, extension, id_proyecto, frente, codigo, cargo, aCargoDe, " ;
	$query = $query . " Observaciones, celular, enviaAJefe, unidadJefe, unidadSeg, requiereFirma2, unidadJefe2) ";
	$query = $query . " VALUES (" . $pSolicitudNo . ", ";
	$query = $query . $pUnidadSol . " , ";	
	$query = $query . "'". $pFecha . "' , ";
	$query = $query . "'". $pLugar . "' , ";
	$query = $query . "'" . $pPiso . "' , ";
	$query = $query . "'" . $pExtension . "' , ";
	$query = $query . $pProyecto . " , ";
	$query = $query . "'" . $pFrente . "' , ";
	$query = $query . "'" . $pCodigo . "' , ";
	$query = $query . "'" . $pCargo . "' , ";
	$query = $query . "'". $pAcargoDe . "' , ";
	$query = $query . "'". $pObserva . "' , ";
	$query = $query . "'". $pCelular . "' , ";
	$query = $query . "'". $pCompleto . "',  ";		
	$query = $query . $pJefe . ", ";		
	$query = $query . $_SESSION["sesUnidadUsuario"] . ", ";			
	$query = $query . "'". $pAut2 . "',  ";		
	if ($pAut2 == '1') {
		$query = $query . $pJefeAut2 ;
	}
	else {
		$query = $query . " NULL " ;
	}
	$query = $query . " ) ";
//	echo $query ;
	$cursor = mssql_query($query) ;
	
	$RSdetalle = "";
	$s = 1;
//	echo "Elementos>>>>>>" ;

	while ($s <= $CantidadItem) {
		$elNumEle = "pNoElemento" . $s;
		$elDesEle = "pDesElemento" . $s;
		$elUniEle = "pUniElemento" . $s;
		$elCanEle = "pCantElemento" . $s;
		
		/*
		echo ${$elNumEle} . "<br>";
		echo ${$elDesEle} . "<br>";
		echo ${$elUniEle} . "<br>";
		echo ${$elCanEle} . "<br>";
		*/
		//04Ene2011
		//PBM
		//Arma la cadena para el envio de elementos solicitados para Ruta del sol
		if ($pProyecto == '1220') {
			$RSdetalle = $RSdetalle . trim(${$elCanEle}) . " " . trim(${$elDesEle}) . "<br>";
		}
		
		//Realiza la grabación del detalle de la solicitud en DetalleSolicitudElementos
		//secuencia, numElemento, DescElemento, codUnidadMedida, cantSolicitada, cantEnTramite, 
		//cantDespachada, cuentaIndNumero, salidaAlmacenFecha, entradaNum, entradaFecha, codEstadoElemento
		$query2 = "INSERT INTO DetalleSolicitudElementos(secuencia, numElemento, DescElemento, codUnidadMedida, cantSolicitada) ";
		$query2 = $query2 . " VALUES (" . $pSolicitudNo . ", ";
		$query2 = $query2 . ${$elNumEle} . ", " ;	
		$query2 = $query2 . " '" . ${$elDesEle} . "' , ";
		$query2 = $query2 . ${$elUniEle} . " , ";
		$query2 = $query2 . ${$elCanEle} ;
		$query2 = $query2 . " ) ";
		$cursor2 = mssql_query($query2) ;
//		echo $query2; 
		
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	if  ((trim($cursor) != "") AND (trim($cursor2) != "")) {
		//Envia el correo a la persona autorizada
		if (trim($pCompleto == '1')) {
			$miMailUsuarioEM = "";
			$eMsql="select email from HojaDeTiempo.dbo.usuarios where unidad = " . $pJefe ;
			$eCursorMsql = mssql_query($eMsql);		
			if ($eRegMsql=mssql_fetch_array($eCursorMsql)) {
				$miMailUsuarioEM = $eRegMsql[email] ;
			}				
			
			//***EnviarMailPEAR
			include("../fncEnviaMailPEAR.php");
			$pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
			$pAsunto = "Portal Ingetec - Solicitud de elementos ";
			$pTema = "Usted tiene una nueva de solicitud de elementos para aprobar.";
			$pFirma = "Portal Ingetec ";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			//***FIN EnviarMailPEAR
			
			//Mail de la 2da firma si existe
			if ($pAut2 == '1') {
				$miMailUsuarioEM = "";
				$eMsql="select email from HojaDeTiempo.dbo.usuarios where unidad = " . $pJefeAut2 ;
				$eCursorMsql = mssql_query($eMsql);		
				if ($eRegMsql=mssql_fetch_array($eCursorMsql)) {
					$miMailUsuarioEM = $eRegMsql[email] ;
				}	
				
				$pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
				enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
				
				//04Ene2011
				//PBM
				//Para el proyecto Ruta del Sol, si la segunda firma es del ingeniero Camilo Marulanda (14384)
				//Se envía correo electrónico informativo a Juan cespedes de la solicitud
				if ($pProyecto == '1220') {
					$miUsuarioSol = "";
					$sqlSol="select nombre, apellidos from HojaDeTiempo.dbo.usuarios where unidad = " . $_SESSION["sesUnidadUsuario"] ;
					$CursorSqlSol = mssql_query($sqlSol);		
					if ($eRegSqlSol=mssql_fetch_array($CursorSqlSol)) {
						$miUsuarioSol = $eRegSqlSol[nombre] . " " . $eRegSqlSol[apellidos];
					}	

					$pTema = "Se realizó una solicitud de elementos para el Proyecto Ruta del sol III dirigida al Ing. Camilo Marulanda. <br><br>";
					$pTema = $pTema . "Solicitante: " . $miUsuarioSol . "<br>" ;
					$pTema = $pTema . "Solicitud No: " . $pSolicitudNo . "<br>" ;
					$pTema = $pTema . "Fecha Solicitud: " . $pFecha . "<br>" ;
					$pTema = $pTema . "Observaciones: " . $pObserva . "<br>" ;
					$pTema = $pTema . "Elementos solicitados: " . $RSdetalle . "<br>" ;
					
					$pPara= "juancespedes@ingetec.com.co";
					enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
				}
			} 
		}
		//Fin envio mail


		//07-Oct-2010
		//Se inhabilitó por cambio de servidor de correo
		/*

		//Envia el correo a la persona autorizada
		if (trim($pCompleto == '1')) {
			$miMailUsuarioEM = "";
			$eMsql="select email from HojaDeTiempo.dbo.usuarios where unidad = " . $pJefe ;
			$eCursorMsql = mssql_query($eMsql);		
			if ($eRegMsql=mssql_fetch_array($eCursorMsql)) {
				$miMailUsuarioEM = $eRegMsql[email] ;
			}				
			
			$AsuntoEM = "Portal Ingetec S.A - Solicitud Elementos";
			
			$msgEM= "<table width='100%'  border='0' cellspacing='0' cellpadding='0'>" ;
			$msgEM=$msgEM." <tr> " ;
			$msgEM=$msgEM." 	<td>Usted tiene una nueva de solicitud de elementos para aprobar. Por favor consulte el Portal <a href='http://www.ingetec.com.co/portal/'>www.ingetec.com.co/portal</a>. </td> " ;
			$msgEM=$msgEM."   </tr> ";
			$msgEM=$msgEM."   <tr> ";
			$msgEM=$msgEM." 	<td> </td> " ;
			$msgEM=$msgEM."   </tr> ";
			$msgEM=$msgEM."   <tr> ";
			$msgEM=$msgEM." 	<td><br>Portal Ingetec S.A. </td> ";
			$msgEM=$msgEM."   </tr>" ;
			$msgEM=$msgEM." </table> ";
	
			$DescripcionEM = $msgEM;
			//cABECERAS
			$cabecerasEM  = 'MIME-Version: 1.0' . "\r\n";
			$cabecerasEM .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$cabecerasEM .= "From: Portal Ingetec S.A. <grodrig@ingetec.com.co>" . "\r\n";
			$cualMailEM= trim($miMailUsuarioEM) . "@ingetec.com.co";
			@mail($cualMailEM,$AsuntoEM,$DescripcionEM, $cabecerasEM);
//echo $cualMailEM . "<br>";

			//Mail de la 2da firma si existe
			if ($pAut2 == '1') {
				$miMailUsuarioEM = "";
				$eMsql="select email from HojaDeTiempo.dbo.usuarios where unidad = " . $pJefeAut2 ;
				$eCursorMsql = mssql_query($eMsql);		
				if ($eRegMsql=mssql_fetch_array($eCursorMsql)) {
					$miMailUsuarioEM = $eRegMsql[email] ;
				}	
				$cualMailEM= trim($miMailUsuarioEM) . "@ingetec.com.co";							
				@mail($cualMailEM,$AsuntoEM,$DescripcionEM, $cabecerasEM);
			} 
//			echo $cualMailEM . "<br>";
		}
		//Fin envio mail
		//Fin 07-Oct-2010
		*/	

		echo ("<script>alert('La Grabación se realizó con éxito. Su número de solicitud es $pSolicitudNo');</script>"); 

	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('solElementos6.php','winSolEle','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");



}


?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--
function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}
//para refrescar la pagina  
function envia3(){ 
alert ("Entro a envia 3");
document.form1.recarga.value="3";
document.form1.submit();
}

function envia2(){ 
var v1,v2,v3, v4,v5, i, CantCampos, msg1, msg2, msg3, msg4, msg5, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
mensaje = '';

//alert (document.Form1.pAcargoDe[0].checked);
//alert (document.Form1.pAcargoDe[1].checked);
//alert (document.Form1.CantidadItem.value);


//Valida que seleccionen el campo  A cargo De
	if (!document.Form1.pAcargoDe[0].checked && !document.Form1.pAcargoDe[1].checked) {
		v1='n';
		msg1 = 'Es necesario que seleccione el campo A cargo de. \n'
//		alert('Es necesario que seleccione el campo A cargo de.');
	} 

//Valida que el campo de la descripción del elemento no esté vacio
	CantCampos=13+(4*document.Form1.CantidadItem.value);
	for (i=15;i<=CantCampos;i+=4) {
		if (document.Form1.elements[i].value == '') {
			v2='n';
			msg2 = 'La descripción de los elementos es olbigatoria. \n'
		}
		//xx=document.Form1.elements[i].value;
		//alert (xx);
	}
	
//Valida que la cantidad en elementos sea numérica
	//CantCampos=10+(4*document.Form1.CantidadItem.value);
	for (i=17;i<=CantCampos;i+=4) {
		xx=document.Form1.elements[i].value;

		if (document.Form1.elements[i].value == '') {
			alert ('entro if 1');
			v3='n';
			msg3 = 'La cantidad es obligatoria y numérica. \n'
		}
		
		if (isNaN(xx)) {
//					alert ('entro if 2');
//		if ((!isNaN(xx)) || document.Form1.elements[i].value == '') {
			v3='n';
			msg3 = 'La cantidad es obligatoria y numérica. \n'
		}

		if (parseFloat(xx)== 0) {
//					alert ('entro if 2');
//		if ((!isNaN(xx)) || document.Form1.elements[i].value == '') {
			v3='n';
			msg3 = 'La cantidad es obligatoria, numérica y mayor que 0. \n'
		}

//		alert(xx)
//		alert (isNaN(xx));
	}

	if (parseFloat(document.Form1.pProyecto.value) == 1220) {
		if ((parseFloat(document.Form1.pJefe.value) == 2123) || (parseFloat(document.Form1.pJefe.value) == 14234) ) {
			if (parseFloat(document.Form1.pJefeAut2.value) == 14384)  {
				v4='n';
				msg4 = 'La Segunda firma es obligatoria. \n'
				document.Form1.pAut2[0].disabled = false;
				document.Form1.pAut2[1].disabled = true;
				document.Form1.pAut2[0].checked = true;
				document.Form1.pAut2[1].checked = false;
	//			alert (msg4);
			}
			else {
				v4='n';
				msg4 = 'La Segunda firma no es obligatoria. \n'
				document.Form1.pAut2[0].disabled = true;
				document.Form1.pAut2[1].disabled = false;
				document.Form1.pAut2[0].checked = false;
				document.Form1.pAut2[1].checked = true;
	//			alert (msg4);
			}
		}
		
		else {
			v4='n';
			msg4 = 'La Segunda firma es obligatoria. \n'
			document.Form1.pAut2[0].disabled = false;
			document.Form1.pAut2[1].disabled = true;
			document.Form1.pAut2[0].checked = true;
			document.Form1.pAut2[1].checked = false;
		}
	}

	//Valida que el celular sea obligatorio
	if(document.Form1.pCelular.value == ''){
		v5 = 'n';
		msg5 = 'Celular es un campo obligatorio. \n';
	} 


//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v5=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg5;
		alert (mensaje);
	}
	
}


//-->
</script>
<script>
var newwindow;
var nav4 = window.Event ? true : false;
function muestraventana(url)
{
	newwindow=window.open(url,"name","height=400,width=650, resizable=yes, scrollbars=yes");
	if (window.focus) {newwindow.focus()}
}
function acceptNum(evt){   
var key = nav4 ? evt.which : evt.keyCode;   
return (key <= 13 || (key>= 48 && key <= 57));
}

function muestraventana2(url)
{
	newwindow=window.open(url,"name2","height=400,width=650, resizable=0, scrollbars=0");
	if (window.focus) {newwindow.focus()}
}
</script>
<title>Solicitud de Elementos</title>
<LINK REL="stylesheet" HREF="../css/estilo.css" TYPE="text/css">
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
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
  } if (errors) alert('Validación:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post" name="Form1" onSubmit="MM_validateForm('pSolicitudNo','','RisNum','pFecha','','R','pCodigo','','R','pExtension','','RisNum');return document.MM_returnValue">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Solicitud de elementos / Suplantaci&oacute;n de Unidad </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
  <tr>
        <td width="40%" rowspan="3" class="TituloTabla">Por favor indique la cantidad de items de la solicitud </td>
        
      </tr>
      <tr>
        <td class="TxtTabla"><input name="CantidadItem" type="text" class="CajaTexto" id="CantidadItem" value="<? echo $CantidadItem;?>" size="10" onKeyPress="return acceptNum(event)" onChange="envia1()">         </td>
      </tr>
    <td width="20%" bgcolor="#FFFFFF">
      
      <tr>
        <td class="TituloTabla">Persona a la que se realizar&aacute; la solicitud </td>
        <td class="TxtTabla">
		<select name="pUnidadSol" class="CajaTexto" id="pUnidadSol" >
		  <? 
		  
		  while ($regAut=mssql_fetch_array($cursorAut)) { 
		  	if ($pUnidadSol == $regAut[unidad]) {
				$selUni = "selected";
			}
			else {
				$selUni = "";
			}
			
			
		  ?>
		  		<option value="<? echo $regAut[unidad]; ?>" <? echo $selUni; ?> ><? echo ucwords(strtolower($regAut[apellidos])) . " " . ucwords(strtolower($regAut[nombre]))  ; echo"["; echo $regAut[unidad]; echo"]"; ?></option>
		  <? } ?>
        </select>
		<? if ($numAutorizar <= 0) { 
				echo "ATENCIÓN: NO podrá generar solicitudes para otras personas hasta que lo autoricen.";
		} ?>
		</td>
      </tr>
      <tr>
        <td class="TituloTabla">Solicitud No. </td>
        <td class="TxtTabla"><input name="pSolicitudNo" type="hidden" class="CajaTexto" id="pSolicitudNo" value="<? echo $MaxCE; ?>" size="10" readonly></td>
      </tr>
      <tr>
        <td class="TituloTabla">Fecha (mm/dd/aaaa):</td>
        <td class="TxtTabla">
		<?
		$hoy = getdate();
/*
		print_r($hoy["wday"]) . "<br>";
		print_r($hoy["mon"]) . "<br>";
		print_r($hoy["mday"]) . "<br>";
		print_r($hoy["year"]) . "<br>";
*/		
		$fechaHoy = mktime(0,0,0,$hoy['mon'], $hoy['mday'], $hoy['year']); //fecha actual en número de segundos
		
		if ($hoy["wday"] == 6) { //es sábado =6
//		if ($hoy["wday"] == 2) { //es sábado =6
			$laFechaEs = ($fechaHoy + 24*60*60*2); 
//			echo "fecha sabado =" .  gmdate("m/d/Y", $laFechaEs) . "<br>" ;
		}
		if ($hoy["wday"] == 0) { //es domingo = 0
//		if ($hoy["wday"] == 2) { //es sábado =6
			$laFechaEs = ($fechaHoy + 24*60*60*1); 
//			echo "fecha domingo =" .  gmdate("m/d/Y", $laFechaEs) . "<br>" ;
		}

		if (($hoy["wday"] >= 1) AND ($hoy["wday"] <= 5)) { //es LUNES A VIERNES
			//vERIFICA LA hORA
//			echo strftime('%R', strtotime('now')) . "<br>" ;
			$laHora= explode (":", strftime('%R', strtotime('now')));
//			echo $laHora[0] . "<br>";
			
			if ($laHora[0] >= 18) { //después de las 6 pm
				if ($hoy["wday"] == 5) { //Si es viernas pasa al lunes, por lo tanto suma 3
					$laFechaEs = ($fechaHoy + 24*60*60*3); 
				}
				else { // si es lunes a jueves pasa al siguiente día
					$laFechaEs = ($fechaHoy + 24*60*60*1);  //por lo tanto suma 1
				}
			}
			else {
				$laFechaEs = $laFechaEs = ($fechaHoy + 24*60*60*0);  //pasa con la fecha actual
			}
//			echo "fecha L-V =" .  gmdate("n/d/Y", $laFechaEs) . "<br>" ;
		}

		?>
		<input name="pFecha" type="text" class="CajaTexto" id="pFecha" value="<? echo  gmdate("n/d/Y", $laFechaEs) ; ?>" readonly>
		

		</td>
      </tr>
      <tr>
        <td class="TituloTabla">Lugar</td>
        <td class="TxtTabla"><input name="pLugar" type="text" class="CajaTexto" id="pLugar" value="<? echo $pLugar; ?>"></td>
      </tr>
      <tr>
        <td class="TituloTabla">Proyecto</td>
        <td class="TxtTabla">
		<select name="pProyecto" class="CajaTexto" id="pProyecto" onChange="envia1()">
		<?
		@mssql_select_db("HojaDeTiempo",$conexion);
//		$sql2="Select * from Proyectos order by nombre " ;
		$sql2="Select * from Proyectos  ";
		$sql2=$sql2 . " where id_estado = 2 ";
		$sql2=$sql2 . " and (codigo <> 'ACC' and codigo <> 'AUS' and codigo <> 'ENF' and codigo <> 'LIC'  ";
		$sql2=$sql2 . " and codigo <> 'PER' and codigo <> 'SAN' and codigo <> 'VAC')   ";
		$sql2=$sql2 . " order by nombre ";
		
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		if ($pProyecto == $reg2[id_proyecto]) {
			$selProy = "Selected";
			}
		else {
			$selProy = "";
		};
		
		?>
          <option value="<? echo $reg2[id_proyecto]; ?>" <? echo $selProy; ?> ><? echo ucwords(strtolower($reg2[nombre])); ?></option>
		 <? } ?> 
        </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">C&oacute;digo</td>
        <td class="TxtTabla">		<input name="pCodigo" type="text" class="CajaTexto" id="pCodigo" value="<? echo $codigoProy; ?>" size="10" readonly></td>
      </tr>
      <tr>
        <td class="TituloTabla">Cargo:</td>
        <td class="TxtTabla"><select name="pCargo" class="CajaTexto" >
          <option value="<? echo $cargoDefProy; ?>"><? echo $cargoDefProy; ?></option>
		  <? 
		  
		  while ($reg4=mssql_fetch_array($cursor4)) { ?>
		  		<option value="<? echo $reg4[cargos_adicionales]; ?>"><? echo $reg4[cargos_adicionales]; ?></option>
		  <? } ?>
        </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">Piso:</td>
        <td class="TxtTabla"><select name="pPiso" class="CajaTexto" id="pPiso">
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
        </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">Extensi&oacute;n:</td>
        <td class="TxtTabla"><input name="pExtension" type="text" class="CajaTexto" id="pExtension" size="10" maxlength="3"></td>
      </tr>
      <tr>
        <td class="TituloTabla">Frente de trabajo:</td>
        <td class="TxtTabla"><input name="pFrente" type="text" class="CajaTexto" size="50"></td>
      </tr>
      <tr>
        <td class="TituloTabla">A cargo de: </td>
        <td class="TxtTabla"><input name="pAcargoDe" type="radio" value="C">
          Cliente&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="pAcargoDe" type="radio" value="I" >
            Ingetec
            <input name="recarga" type="hidden" id="recarga" value="1">
            
      </tr>
  </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="4"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="8%">No.</td>
          <td>Descripci&oacute;n elemento </td>
          <td width="10%">Unidad</td>
          <td width="10%">Cantidad</td>
        </tr>
	  <?
	  $r = 1;
	  while ($r <= $CantidadItem) {
	  ?>
		
        <tr class="TxtTabla">
          <td width="8%" align="center">
            <input name="pNoElemento<? echo $r; ?>" type="text" class="CajaTexto" id="pNoElemento<? echo $r; ?>" value="<? echo $r; ?>" size="10" readonly></td>
          <td align="center"><input name="pDesElemento<? echo $r; ?>" type="text" class="CajaTexto" id="pDesElemento<? echo $r; ?>" size="60"></td>
          <td width="10%" align="center">            
		  <select name="pUniElemento<? echo $r; ?>" class="CajaTexto" >
			<?
			@mssql_select_db("GestiondeInformacionDigital",$conexion);
			$sql2="Select * from UnidadMedida " ;
			$cursor2 = mssql_query($sql2);
			while ($reg2=mssql_fetch_array($cursor2)) {
			
			?>
              <option value="<? echo $reg2[codUnidadMedida]; ?>"><? echo $reg2[nomUnidadMedida]; ?></option>
			<? } ?>
            </select></td>
          <td width="10%" align="center"><input name="pCantElemento<? echo $r; ?>" type="text" class="CajaTexto" id="pCantElemento<? echo $r; ?>" value="0" size="10"></td>
        </tr>
		
		<? 
		$r = $r + 1;
		} ?>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="TituloTabla">Celular</td>
          <td class="TxtTabla"><input name="pCelular" type="text" class="CajaTexto" id="pCelular" size="30"></td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Observaciones:</td>
          <td class="TxtTabla"><textarea name="pObserva" cols="60" class="CajaTexto" id="pObserva"></textarea></td>
        </tr>
        <tr>
          <td class="TituloTabla">Jefe que revisar&aacute; la solicitud:</td>
          <td class="TxtTabla">
		  <? 
		  //Verifica si el usuario pertenece o no a la División Administrativa
		  $esUsuarioDAdministrativo = 0;
		  
		  $sqlDA = "select count(*) hayReg ";
		  $sqlDA = $sqlDA . " from HojaDeTiempo.dbo.usuarios U, HojaDeTiempo.dbo.departamentos D, HojaDeTiempo.dbo.divisiones V ";
		  $sqlDA = $sqlDA . " where U.id_departamento = D.id_departamento ";
		  $sqlDA = $sqlDA . " and D.id_division = V.id_division ";
		  $sqlDA = $sqlDA . " and D.id_division = 11 "; // 11 corresponde a División administrativa
		  $sqlDA = $sqlDA . " and U.unidad = " . $_SESSION["sesUnidadUsuario"];
		  $cursorDA = mssql_query($sqlDA);
		  if ($regDA=mssql_fetch_array($cursorDA)) {
		  		$esUsuarioDAdministrativo = $regDA[hayReg];
			}
		  
		  if (($pProyecto == '42') AND (trim($esUsuarioDAdministrativo) != '0')) { ?>
		    <select name="pJefe" class="CajaTexto">
			  <option value="3212">Gustavo Carrasco Merino</option>
		      <option value="4841">Ospina Dur&aacute;n Pablo Alberto</option>
		      <option value="13432">Ca&ntilde;on Burgos Jaime</option>
		      <option value="14888">Pi&ntilde;eros Jimenez Armenio Enrique</option>
		      <option value="1782">Arias Montero Jos&eacute; Bernardo</option>
		      <option value="13925">Mahecha Gutierrez Mar&iacute;a Janeth</option>
              <option value="13849">Angulo Guiza Rosa Amelia</option>
              <option value="14306">Hernandez Rojas Carolina</option>
			  <option value="4638">Santos Mora Victor Camilo</option>
			  <option value="14234">Marulanda Escobar Andrés </option>
			  <option value="800921">Angulo Gomez Angela María</option>
              </select>
			<? 
			} 
			else {
			?>
			
		    <select name="pJefe" class="CajaTexto" id="pJefe" >
            <?
		@mssql_select_db("HojaDeTiempo",$conexion);

		//si son gastos generales (id_proyecto = 42), entonces muestra todos los usuarios
		if ($pProyecto == '42') {
			$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
			$sql2=$sql2." and retirado is null ";
			$sql2=$sql2." order by apellidos ";
		}
		//sino, s{olo muestra el director y el coordinador del proyecto
	  else {
	  	//05Nov2010
		//PBM
		//Para Ruta del sol (14.0) 1220 mostrar las personas autorizadas para primera firma a través de FirmasSolicitudesProyectos
		//Solicitado por Juan Cespedes y autorizado por GRM
			if ($pProyecto == '1220') {
				$sql2="SELECT A.* , B.nombre, B.apellidos "  ;
				$sql2=$sql2." FROM HojaDeTiempo.dbo.FirmasSolicitudesProyectos A, HojaDeTiempo.dbo.Usuarios B ";
				$sql2=$sql2." WHERE A.firma1 = 1 ";
				$sql2=$sql2." and A.id_proyecto = 1220 ";
				$sql2=$sql2." and A.unidad = B.unidad ";
			}
		  else {
				$sql2="SELECT U.*  " ;
				$sql2=$sql2." FROM ";
				$sql2=$sql2." 	( ";
				$sql2=$sql2." 	Select unidad from HojaDeTiempo.dbo.Usuarios ";
				if (trim($elCoordinador) == "") {
					$sql2=$sql2." 	where (unidad =" . $elDirector . ") ";
				}
				else {
					$sql2=$sql2." 	where (unidad =". $elDirector ." or unidad = " . $elCoordinador . ") ";
				}
				$sql2=$sql2." and retirado is null  ";
				$sql2=$sql2." 	UNION ";
				$sql2=$sql2." 	select unidadOrdenador ";
				$sql2=$sql2." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto ";
				$sql2=$sql2."   where id_proyecto = " . $pProyecto ;
//07Dic2012
//PBM por instrucción de Silvia Palacio  Sólo deben verse los Directores, Coordinadores y Ordenadores de gasto.
/*
				$sql2=$sql2." 	UNION ";
				$sql2=$sql2." 	select distinct id_encargado  ";
				$sql2=$sql2." 	from HojaDeTiempo.dbo.actividades ";
				$sql2=$sql2." 	where id_proyecto = " . $pProyecto ;
				$sql2=$sql2." 	and id_encargado is not null ";
				$sql2=$sql2." 	UNION ";
				$sql2=$sql2." 	select unidad ";
				$sql2=$sql2." 	from HojaDeTiempo.dbo.responsablesActividad ";
				$sql2=$sql2." 	where id_proyecto =" . $pProyecto ;
*/				
				$sql2=$sql2." 	) A, Usuarios U  ";
				$sql2=$sql2." WHERE A.unidad = U.unidad ";
				$sql2=$sql2." ORDER BY apellidos  ";
			}
		}
		
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		  <? } // cierra else ?>		  </td>
        </tr>
        <tr>
          <td width="20%" rowspan="2" class="TituloTabla">&iquest;Enviar solicitud a autorizaci&oacute;n? </td>
          <td class="TxtTabla"><input name="pCompleto" type="radio" value="1">
            Si
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="pCompleto" type="radio" value="0" checked>
            No&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td class="TxtTabla">NOTA: Si escoge Si la solicitud aparecer&aacute; para aprobaci&oacute;n al jefe que seleccion&oacute;.</td>
        </tr>
        <tr>
          <td class="TituloTabla">Requiere segunda autorizaci&oacute;n? </td>
          <td class="TxtTabla">
		  <?
//		  echo $pAut2 . "<br>";
//		  echo $pProyecto . "<br>";
		  if ($pProyecto == 1220 ) {
				$selAut02Si="checked";
				$selAut02No="";
				$selHabititaNo = "disabled";
		  }
		  else {
				$selAut02Si="";
				$selAut02No="checked";
				$selHabititaNo = "";
		  }
		  ?>
		  <input name="pAut2" type="radio" value="1" <? echo $selAut02Si; ?> >
            Si
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="pAut2" type="radio" value="0" <? echo $selAut02No; ?> <? echo $selHabititaNo; ?> >
            No&nbsp;&nbsp;&nbsp;		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Jefe que realizar&aacute; segunda autorizaci&oacute;n </td>
          <td class="TxtTabla">
		  <select name="pJefeAut2" class="CajaTexto" id="pJefeAut2" >
            <?
		//05Nov2010
		//PBM
		//Para Ruta del sol (14.0) 1220 mostrar las personas autorizadas para segunda firma a través de FirmasSolicitudesProyectos
		//Solicitado por Juan Cespedes y autorizado por GRM
			if ($pProyecto == '1220') {
				$sql2="SELECT A.* , B.nombre, B.apellidos "  ;
				$sql2=$sql2." FROM HojaDeTiempo.dbo.FirmasSolicitudesProyectos A, Usuarios B ";
				$sql2=$sql2." WHERE A.firma2 = 1 ";
				$sql2=$sql2." and A.id_proyecto = 1220 ";
				$sql2=$sql2." and A.unidad = B.unidad ";
			}
			else {
				@mssql_select_db("HojaDeTiempo",$conexion);
				//Muestra todos los usuarios. 
				$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
				$sql2=$sql2." and retirado is null ";
				$sql2=$sql2." order by apellidos ";
			}
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		  </td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td align="center"><strong>ATENCION: Por favor verifique que la informaci&oacute;n ingresada en este formulario sea correcta. <br>
            Una vez grabada la informaci&oacute;n NO podr&aacute; modificarla ni eliminarla, s&oacute;lo podr&aacute; consultarla. </strong></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
		  <? if ($numAutorizar > 0) { ?>
		  <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()">
		  <? } ?>
		  </td>
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
