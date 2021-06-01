<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
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
    <td class="TituloUsuario">Programaci&oacute;n de recursos </td>
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
        <tr>
          <td class="TituloTabla">Lotes de control </td>
          <td class="TxtTabla"><strong>[LC1] GERENCIA DEL PROYECTO</strong></td>
        </tr>
        <tr>
          <td class="TituloTabla">Fecha Inicial </td>
          <td class="TxtTabla">1-Ene-2012</td>
        </tr>
        <tr>
          <td class="TituloTabla">Fecha Final </td>
          <td class="TxtTabla">15-Dic-2012</td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Cantidad de personas a programar </td>
          <td class="TxtTabla"><input name="pCantReg" type="text" class="CajaTexto" id="pCantReg" value="<? echo $pCantReg; ?>" size="10" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onChange="envia1()"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">PROGRAMACI&Oacute;N BASE </td>
        </tr>
      </table>      
	<? 
	//echo $brtHM1;
	if (($brtHM1 == "") OR ($brtHM1 == "0")) { 
	?>
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Responsable</td>
        <td width="3%">Hombres<br>
          /Mes</td>
        <td width="10%">Replicar</td>
        <td width="3%"><p>Ene<br>
          2012
        </p>          </td>
        <td width="3%">Feb<br>
          2012</td>
        <td width="3%">Mar<br>
          2012</td>
        <td width="3%">Abr<br>
          2012</td>
        <td width="3%">May<br>
          2012</td>
        <td width="3%">Jun<br>
          2012</td>
        <td width="3%">Jul<br>
          2012</td>
        <td width="3%">Ago<br>
          2012</td>
        <td width="3%">Sep<br>
          2012</td>
        <td width="3%">Oct<br>
          2012</td>
        <td width="3%">Nov<br>
          2012</td>
        <td width="3%">Dic<br>
          2012</td>
      </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  ?>
      <tr class="TxtTabla">
        <td align="center">
		<select name="pJefe" class="CajaTexto" id="pJefe" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
		$sql2=$sql2." order by U.apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		</td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="10" ></td>
        <td width="10%" align="center"><input name="brtHM<? echo $r; ?>" type="radio" value="1"  onClick="envia1()">
          Si 
            &nbsp;
            <input name="brtHM<? echo $r; ?>" type="radio" value="0" checked>
            No</td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>"  size="8" ></td>
      </tr>
		<? 
		$r = $r + 1;
		$nuevoCodigo = $nuevoCodigo + 1;
		} ?>
    </table>
	<? }
	if ($brtHM1 == "1") { 
		
	?>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Responsable</td>
        <td width="3%">Hombres<br>
          /Mes</td>
        <td width="10%">Replicar</td>
        <td width="3%"><p>Ene<br>
          2012
        </p>          </td>
        <td width="3%">Feb<br>
          2012</td>
        <td width="3%">Mar<br>
          2012</td>
        <td width="3%">Abr<br>
          2012</td>
        <td width="3%">May<br>
          2012</td>
        <td width="3%">Jun<br>
          2012</td>
        <td width="3%">Jul<br>
          2012</td>
        <td width="3%">Ago<br>
          2012</td>
        <td width="3%">Sep<br>
          2012</td>
        <td width="3%">Oct<br>
          2012</td>
        <td width="3%">Nov<br>
          2012</td>
        <td width="3%">Dic<br>
          2012</td>
      </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  	if ($r==1) {
			$mostrar=1;
		}
		else {
			$mostrar="";
		}
	  ?>
      <tr class="TxtTabla">
        <td align="center">
		<select name="pJefe" class="CajaTexto" id="pJefe" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
		$sql2=$sql2." order by U.apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		</td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="10" ></td>
        <td width="10%" align="center"><input name="brtHM<? echo $r; ?>" type="radio" value="1" checked>
          Si 
            &nbsp;
            <input name="brtHM<? echo $r; ?>" type="radio" value="0">
            No</td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
        <td width="3%" align="center"><input name="pSigla<? echo $r; ?>" type="text" class="CajaTexto" id="pSigla<? echo $r; ?>" value="<? echo $mostrar; ?>"  size="8" ></td>
      </tr>
		<? 
		$r = $r + 1;
		$nuevoCodigo = $nuevoCodigo + 1;
		} ?>
    </table>
	<? } ?>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">NOTA:</td>
        </tr>
        <tr>
          <td class="TxtTabla">1. Esta ventana captura Hombres / Mes y las convierte en horas acorde con la totalida de horas por mes previamente definidas en la base de datos. </td>
        </tr>
        <tr>
          <td class="TxtTabla">2. </td>
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
