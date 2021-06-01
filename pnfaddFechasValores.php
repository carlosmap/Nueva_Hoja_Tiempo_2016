<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}


if(trim($recarga) == "2"){
	//Realiza la grabación en Muestra
	$msgGraba = "";
	$msgNOGraba = "";
	$s = 1;
	while ($s <= $pCantReg) {
		//Generar la secuencia  del lote de trabajo
		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sigienteSec =0;
		$sqlId = " SELECT COALESCE(MAX(codLoteControl), 0) AS elMax FROM EnsayosProyLC ";
		$sqlId = $sqlId. " WHERE id_proyecto = " . $_SESSION["sesProyLaboratorio"] ;
		$cursorId = mssql_query($sqlId);
		if($regId = mssql_fetch_array($cursorId)){
			$sigienteSec = $regId["elMax"] + 1;
		}

		//Recoger las variables
		$elpNombre = "pNombre" . $s;
		$elpSigla = "pSigla" . $s;

		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sqlIn1 = " INSERT INTO EnsayosProyLC ";
		$sqlIn1 = $sqlIn1 . " (id_proyecto, codLoteControl, nomLoteControl, siglaLC, fechaCrea, usuarioCrea ) ";
		$sqlIn1 = $sqlIn1 . " VALUES ( ";
		$sqlIn1 = $sqlIn1 . " " . $_SESSION["sesProyLaboratorio"] . ", ";
		$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";
		$sqlIn1 = $sqlIn1 . " '" . ${$elpNombre} . "', ";
		$sqlIn1 = $sqlIn1 . " '" . ${$elpSigla} . "', ";
		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";
		$sqlIn1 = $sqlIn1 . " ) ";
//		$cursorIn1 = mssql_query($sqlIn1);

		if  (trim($cursorIn1) != "")  {
			//echo "entro eal if 2" . "<br>";
			$msgGraba=$msgGraba."[".${$elpSigla}."] " ;
		}
		else {
			//echo "entro al else " . "<br>";
			$msgNOGraba=$msgNOGraba."[".${$elpSigla}."] " ; 
		}
				
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	//if  (trim($cursorIn1) != "")  {
	if  (trim($msgNOGraba) != "")  {
		echo ("<script>alert('No se grabaron los siguientes Lotes de control: $msgNOGraba ');</script>"); 
	} 
	
	if  (trim($msgGraba) != "")  {
		echo ("<script>alert('Se grabaron las siguientes Lotes de control: $msgGraba ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('pnfProgProyectos01.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--


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

CantCampos=1+(2*document.Form1.pCantReg.value);

	
//Valida que el campo Nombre no esté vacio
for (i=1;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v2='n';
		msg2 = 'Nombre es obligatorio. \n'
	}
}

//Valida que el campo Sigla no esté vacio
for (i=2;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v3='n';
		msg3 = 'Sigla es obligatorio. \n'
	}
}



//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s') && (v10=='s') && (v11=='s') && (v12=='s') && (v13=='s') && (v14=='s') && (v15=='s')) {
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
<title>Investigaciones Geot&eacute;cnicas</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Divisi&oacute;n / Actividad</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="3%">ID</td>
            <td width="5%">Macroactividad</td>
            <td>Lote de control / Lote de trabajo / Actividad Vs Divisi&oacute;n </td>
            <td width="15%">Responsable</td>
            <td width="5%">Valor Presupuestado </td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%">Fecha  Inicio </td>
            <td width="8%">Fecha  Fin </td>
            <td width="8%">Valor del recurso </td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%"><strong>1</strong></td>
            <td width="5%"><strong>LC1</strong></td>
            <td class="TxtTabla"><strong>GERENCIA DEL PROYECTO</strong></td>
            <td width="15%"><strong>[2964] Alberto Marulanda </strong>            </td>
            <td width="5%"><strong>$ 250.000.000 </strong></td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center" class="TxtTabla"><input name="textfield" type="text" class="CajaTexto"></td>
            <td width="8%" align="center" class="TxtTabla"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td width="8%" class="TxtTabla"><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">2</td>
            <td width="5%">LT1.1</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="3%">&nbsp;</td>
                <td>ACTIVIDADES DE SOPORTE A LA GERENCIA DEL PROYECTO</td>
              </tr>
            </table></td>
            <td width="15%">[2964] Alberto Marulanda            </td>
            <td width="5%">$ 180.000.000 </td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">3</td>
            <td width="5%">&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Calidad</td>
              </tr>
            </table></td>
            <td width="15%">[4417] Hector Alfredo L&oacute;pez            </td>
            <td width="5%">&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">4</td>
            <td width="5%">&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Medio Ambiente </td>
              </tr>
            </table></td>
            <td width="15%">[14469] William L&oacute;pez            </td>
            <td width="5%">&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">5</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Manuales e informes </td>
              </tr>
            </table></td>
            <td width="15%">[12372] Hernando Caicedo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">6</td>
            <td width="5%">&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Documentos obra civil</td>
              </tr>
            </table></td>
            <td width="15%">[12372] Hernando Caicedo            </td>
            <td width="5%">&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">7</td>
            <td width="5%">&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Geolog&iacute;a - Sismolog&iacute;a </td>
              </tr>
            </table></td>
            <td width="15%">[17206] Fernando Garz&oacute;n            </td>
            <td width="5%">&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">8</td>
            <td width="5%">&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Climat. Hidro - Sedim </td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra            </td>
            <td width="5%">&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">9</td>
            <td width="5%">&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Rutas transporte y carga </td>
              </tr>
            </table></td>
            <td width="15%">[11383] Gloria B&aacute;ez            </td>
            <td width="5%"><br />            </td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">10</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td>Centro de CADD </td>
              </tr>
            </table></td>
            <td width="15%">[12974] Gonzalo rodr&iacute;guez            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">11</td>
            <td>LT1.2</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TxtTabla">
                <td width="3%">&nbsp;</td>
                <td>ADMINISTRACI&Oacute;N DE ASESOR&Iacute;AS, CONSULTOR&Iacute;AS E INTERVENTOR&Iacute;AS</td>
              </tr>
            </table></td>
            <td width="15%">[13829] Fabio S&aacute;nchez            </td>
            <td align="right">$ 70.000.000 </td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%"><strong>12</strong></td>
            <td><strong>LC2</strong></td>
            <td class="TxtTabla"><strong>INFRAESTRUCTURA</strong></td>
            <td width="15%"><strong>[15252] Julio Gonz&aacute;lez</strong>            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">13</td>
            <td>LT2.1</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">V&Iacute;A DE ACCESO </td>
              </tr>
            </table></td>
            <td width="15%">[14176] Javier Lizarazo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">14</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Hidrol., hidr&aacute;ulica y socav.</td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">15</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[15415] Thomas Solano            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">16</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Estructuras</td>
              </tr>
            </table></td>
            <td>[5044] Samuel Su&aacute;rez            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">17</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Dise&ntilde;o geom&eacute;trico </td>
              </tr>
            </table></td>
            <td width="15%"> 
            [14176] Javier Lizarazo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">18</td>
            <td>LT2.2</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">V&Iacute;AS SECUNDARIAS Y PUENTE TEMPORAL, PLAZOLETAS Y PORTALES - ADECUACI&Oacute;N CANTERAS Y DEP&Ograve;SITOS </td>
              </tr>
            </table></td>
            <td width="15%">[15252] Julio Gonz&aacute;lez            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">19</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Hidrol., hidr&aacute;ulica y socav.</td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">20</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Geotecnia obras superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">21</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[5044] Samuel Su&aacute;rez            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">22</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Dise&ntilde;o geom&eacute;trico </td>
              </tr>
            </table></td>
            <td width="15%">            [15252] Julio Gonz&aacute;lez            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">23</td>
            <td>LT2.3</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">CAMPAMENTOS, BODEGAS Y ALMAC&Eacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[4618] &Aacute;rvid Bernal            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">24</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Hidr&aacute;ulica y sanitaria </td>
              </tr>
            </table></td>
            <td width="15%">[11973] Jose Luis Sierra            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr><tr class="TxtTabla">
            <td width="3%">25</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr><tr class="TxtTabla">
            <td width="3%">26</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[4618] Arvid Bernal            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr><tr class="TxtTabla">
            <td width="3%">27</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Arquitectura</td>
              </tr>
            </table></td>
            <td width="15%">[15021] Diana Figueredo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr><tr class="TxtTabla">
            <td width="3%">28</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Mec&aacute;nica</td>
              </tr>
            </table></td>
            <td width="15%">[11577] Gabriel Rudas            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr><tr class="TxtTabla">
            <td width="3%">29</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">El&eacute;ctrica</td>
              </tr>
            </table></td>
            <td width="15%">            [10579] Jorge Mart&iacute;nez            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">30</td>
            <td>LT2.4</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">ENERG&Iacute;A PARA CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
		  <tr class="TxtTabla">
            <td width="3%">31</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Geotecnia o. superf </td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr><tr class="TxtTabla">
            <td width="3%">32</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">Estructuras</td>
              </tr>
            </table></td>
            <td width="15%">[15218] Roberto Rojas            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr><tr class="TxtTabla">
            <td width="3%">33</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">El&eacute;ctrica</td>
              </tr>
            </table></td>
            <td width="15%">            [5008] Jorge Correa            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr>
		  <tr class="TxtTabla">
            <td width="3%">34</td>
            <td>LT2.2</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">SUBESTACI&Oacute;N DE CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr>
		  <tr class="TxtTabla">
            <td width="3%">35</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla"><span class="xl65">Geotecnia o. superf </span></td>
              </tr>
            </table></td>
            <td width="15%">[14033] Juan Carlos Caicedo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr><tr class="TxtTabla">
            <td width="3%">36</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla"><span class="xl65">Estructuras</span></td>
              </tr>
            </table></td>
            <td width="15%">[15218] Roberto Rojas            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr><tr class="TxtTabla">
            <td width="3%">37</td>
            <td>&nbsp;</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla"><span class="xl65">El&eacute;ctrica</span></td>
              </tr>
            </table></td>
            <td width="15%">[17080] Mario Giraldo            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr>
		  <tr class="TxtTabla">
            <td width="3%">38</td>
            <td>LT2.2</td>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td width="3%">&nbsp;</td>
                <td class="TxtTabla">COMUNICACIONES PARA CONSTRUCCI&Oacute;N </td>
              </tr>
            </table></td>
            <td width="15%">[14894] Gustavo Suaza            </td>
            <td>&nbsp;</td>
            <td class="TituloTabla2">&nbsp;</td>
            <td align="center"><input name="textfield" type="text" class="CajaTexto"></td>
            <td align="center"><input name="textfield2" type="text" class="CajaTexto"></td>
            <td><input name="textfield3" type="text" class="CajaTexto"></td>
		  </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
            <td width="3%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%" align="center">&nbsp;</td>
            <td width="8%">&nbsp;</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
        </tr>
      </table>
      </td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
</body>
</html>

<? mssql_close ($conexion); ?>	
