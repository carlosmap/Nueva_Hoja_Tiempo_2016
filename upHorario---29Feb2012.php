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

//17Mar2008
//Trae el horario seleccionado
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo, h.localiza  " ;
$sql3=$sql3." FROM Horarios h " ;
$sql3=$sql3." Where h.IDhorario =" . $cualHorario ;
$cursor3 = mssql_query($sql3);
if ($Reg3=mssql_fetch_array($cursor3)) {
	$pIDhorario = $Reg3[IDhorario];
	$pNomHorario= $Reg3[NomHorario];
	$pLunes= $Reg3[Lunes];
	$pMartes= $Reg3[Martes];
	$pMiercoles= $Reg3[Miercoles];
	$pJueves= $Reg3[Jueves];
	$pViernes= $Reg3[Viernes];
	$pSabado= $Reg3[Sabado];
	$pDomingo= $Reg3[Domingo];
	$pTotal = $Reg3[Lunes] + $Reg3[Martes] + $Reg3[Miercoles] + $Reg3[Jueves] + $Reg3[Viernes] + $Reg3[Sabado] + $Reg3[Domingo];
	$pLocalizacion = $Reg3[localiza];
}

//Si se presionó el botón Grabar
if ($recarga == 2) {
	//Verifica que el horario no exista con valores coincidentes de lunes a domingo en otro horario
	$vSql1="SELECT IDhorario, NomHorario " ;
	$vSql1=$vSql1." From HojaDeTiempo.dbo.Horarios   " ;
	$vSql1=$vSql1." Where Lunes =" . $lLunes ;
	$vSql1=$vSql1." and Martes =" . $lMartes ;
	$vSql1=$vSql1." and Miercoles =" . $lMiercoles ;
	$vSql1=$vSql1." and Jueves =" . $lJueves ;
	$vSql1=$vSql1." and Viernes =" . $lViernes ;
	$vSql1=$vSql1." and Sabado =" . $lSabado ;
	$vSql1=$vSql1." and Domingo =" . $lDomingo;
	$vSql1=$vSql1." and Domingo =" . $lDomingo;
	$vSql1=$vSql1." and IDhorario <>" . $miHorario; 
	$vCursor1 = mssql_query($vSql1);
	if ($vReg1=mssql_fetch_array($vCursor1)) {
		echo ("<script>alert('El Horario ya existe. Corresponde al nombre: " . $vReg1[NomHorario] . " ');</script>");
		echo ("<script>window.close()</script>");	
		exit;
	}

	$vSql1="SELECT count(*) as Cuantos FROM Horarios " ;
	$vSql1=$vSql1." WHERE Upper(NomHorario) = '". strtoupper($lNombre) ."' " ;
	$vSql1=$vSql1." and IDhorario <>" . $miHorario; 
	$vCursor1 = mssql_query($vSql1);
	if ($vReg1=mssql_fetch_array($vCursor1)) {
		if ($vReg1[Cuantos] > 0) {
			echo ("<script>alert('El nombre del Horario YA EXISTE. ');</script>");
			echo ("<script>window.close()</script>");	
			exit;
		}
	}
	
	//Realiza la inserción de la actividad en la tabla Horarios
	//IDhorario, NomHorario, Lunes, Martes, Miercoles, Jueves, Viernes, Sabado, Domingo
	$query = "UPDATE Horarios SET  NomHorario = '" . $lNombre . "' , " ;
	$query = $query." Lunes=" . $lLunes . ", ";
	$query = $query." Martes=" . $lMartes . ", ";
	$query = $query." Miercoles=" . $lMiercoles . ", ";
	$query = $query." Jueves=" . $lJueves . ", ";
	$query = $query." Viernes=" . $lViernes . ", ";
	$query = $query." Sabado=" . $lSabado . ", ";
	$query = $query." Domingo=" . $lDomingo . ", ";
	$query = $query." localiza=" . $lLocaliza . " ";
	$query = $query . " WHERE IDhorario =" . $miHorario;
	$cursor = mssql_query($query);

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosHorarios.php?cualProyecto=$miProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}

?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos - Horarios</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
function totalizar(campo){ 
	//Valida que los campos Lunes a domingo sean numéricos 
	if (document.Form1.elements[campo].value == '') {
		document.Form1.elements[campo].value = 0;
		alert('Los campos Lunes a Domingo son obligatorios y numéricos. Por favor verifique la información. \n');
	}
	if (isNaN(document.Form1.elements[campo].value)) {
		document.Form1.elements[campo].value = 0;
		alert('Los campos Lunes a Domingo son obligatorios y numéricos. Por favor verifique la información. \n');
	}
	else {
		//Valida que el valor ingresado de Lunes a domingo no sea mayor que 20
		if (document.Form1.elements[campo].value > 20) {
			document.Form1.elements[campo].value = 0;
			alert('Los campos Lunes a Domingo son obligatorios y numéricos entre el rango 1 - 20. Por favor verifique la información. \n');
		}
	}
	document.Form1.elements['lTotal'].value = parseFloat(document.Form1.elements['lLunes'].value)+parseFloat(document.Form1.elements['lMartes'].value)+parseFloat(document.Form1.elements['lMiercoles'].value)+parseFloat(document.Form1.elements['lJueves'].value)+parseFloat(document.Form1.elements['lViernes'].value)+parseFloat(document.Form1.elements['lSabado'].value)+parseFloat(document.Form1.elements['lDomingo'].value) ;
}

function enviar(){ 
var v1,msg1,mensaje;

//Valida que se haya ingresado el nombre del horario
	if (document.Form1.lNombre.value == '') {
		document.Form1.recarga.value="1";
		alert('El campo Nombre de horario es obligatorio. \n');
	}
	else {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Horarios</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" >
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="TituloTabla">Nombre de Horario </td>
            <td colspan="7" class="TxtTabla"><input name="lNombre" type="text" class="CajaTexto" id="lNombre" value="<? echo $pNomHorario; ?>" size="100">
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
              <input name="recarga" type="hidden" id="recarga" value="1"></td>
          </tr>
          <tr>
            <td width="14%" class="TituloTabla">Tipo de n&oacute;mina<br>
              Localizaci&oacute;n</td>
            <td colspan="7" class="TxtTabla">
			<?
			if (trim($pLocalizacion) == "1") {
				$sel01="selected";
				$sel02="";
				$sel03="";
			}
			if (trim($pLocalizacion) == "2") {
				$sel01="";
				$sel02="selected";
				$sel03="";
			}
			if (trim($pLocalizacion) == "3") {
				$sel01="";
				$sel02="";
				$sel03="selected";
			}
			
			?>
			<select name="lLocaliza" class="CajaTexto" id="lLocaliza">
              <option value="1" <? echo $sel01; ?> >Oficina</option>
              <option value="2" <? echo $sel02; ?> >Campo</option>
              <option value="3" <? echo $sel03; ?> >Planilla</option>
            </select>
			
			</td>
          </tr>
          <tr class="TxtTabla">
            <td colspan="8"><img src="img/images/Pixel.gif" width="4" height="4"></td>
          </tr>
          <tr class="TituloTabla2">
            <td width="14%">Lunes</td>
            <td width="14%">Martes</td>
            <td width="14%">Mi&eacute;rcoles</td>
            <td width="14%">Jueves</td>
            <td width="14%">Viernes</td>
            <td width="14%">S&aacute;bado</td>
            <td width="14%">Domingo</td>
            <td width="14%">Total</td>
          </tr>
          <tr align="center" class="TxtTabla">
            <td width="14%"><input name="lLunes" type="text" class="CajaTexto" id="lLunes" value="<? echo $pLunes; ?>" size="10" onBlur="totalizar('lLunes')" ></td>
            <td width="14%"><input name="lMartes" type="text" class="CajaTexto" id="lMartes" value="<? echo $pMartes; ?>" size="10" onBlur="totalizar('lMartes')" ></td>
            <td width="14%"><input name="lMiercoles" type="text" class="CajaTexto" id="lMiercoles" value="<? echo $pMiercoles; ?>" size="10" onBlur="totalizar('lMiercoles')"></td>
            <td width="14%"><input name="lJueves" type="text" class="CajaTexto" id="lJueves" value="<? echo $pJueves; ?>" size="10" onBlur="totalizar('lJueves')" ></td>
            <td width="14%"><input name="lViernes" type="text" class="CajaTexto" id="lViernes" value="<? echo $pViernes; ?>" size="10" onBlur="totalizar('lViernes')" ></td>
            <td width="14%"><input name="lSabado" type="text" class="CajaTexto" id="lSabado" value="<? echo $pSabado; ?>" size="10" onBlur="totalizar('lSabado')" ></td>
            <td width="14%"><input name="lDomingo" type="text" class="CajaTexto" id="lDomingo" value="<? echo $pDomingo; ?>" size="10" onBlur="totalizar('lDomingo')" ></td>
            <td width="14%"><input name="lTotal" type="text" class="CajaTexto" id="lTotal" value="<? echo $pTotal; ?>" size="10" readonly ></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" align="right" class="TxtTabla"><input name="miHorario" type="hidden" id="miHorario" value="<? echo $pIDhorario; ?>">
    <input name="Submit" type="submit" class="Boton" value="Grabar" onClick="enviar()"></td>
    </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
