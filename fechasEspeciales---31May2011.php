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
$sql3="SELECT DISTINCT h.IDhorario, h.NomHorario, h.Lunes, h.Martes, h.Miercoles, h.Jueves, h.Viernes, h.Sabado, h.Domingo  " ;
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
}

//17Mar2008
//Trae las fechas especiales del horario seleccionado
$sql4="select * from FechasEspeciales " ;
$sql4=$sql4." where IDhorario = " . $cualHorario ;
$cursor4 = mssql_query($sql4);



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
	}

	$vSql1="SELECT count(*) as Cuantos FROM Horarios " ;
	$vSql1=$vSql1." WHERE Upper(NomHorario) = '". strtoupper($lNombre) ."' " ;
	$vSql1=$vSql1." and IDhorario <>" . $miHorario; 
	$vCursor1 = mssql_query($vSql1);
	if ($vReg1=mssql_fetch_array($vCursor1)) {
		if ($vReg1[Cuantos] > 0) {
			echo ("<script>alert('El nombre del Horario YA EXISTE. ');</script>");
			echo ("<script>window.close()</script>");	
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
	$query = $query." Domingo=" . $lDomingo . " ";
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
<script language="JavaScript">
window.name="winFechas";
</script>

<script language="JavaScript" type="text/JavaScript">

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
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="14%" class="TituloTabla">Nombre de Horario </td>
            <td colspan="7" class="TxtTabla">
			<? echo $pNomHorario; ?>
              <input name="miProyecto" type="hidden" value="<? echo $cualProyecto; ?>">
              <input name="recarga" type="hidden" id="recarga" value="1">
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
            <td width="14%"><? echo $pLunes; ?></td>
            <td width="14%"><? echo $pMartes; ?></td>
            <td width="14%"><? echo $pMiercoles; ?></td>
            <td width="14%"><? echo $pJueves; ?></td>
            <td width="14%"><? echo $pViernes; ?></td>
            <td width="14%"><? echo $pSabado; ?></td>
            <td width="14%"><? echo $pDomingo; ?></td>
            <td width="14%"><? echo $pTotal; ?></td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Fechas especiales </td>
          </tr>
        </table>
	    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Fecha</td>
            <td>Cantidad Horas            </td>
            <td width="1%">&nbsp;</td>
          </tr>
          <? while ($reg4=mssql_fetch_array($cursor4)) {  
		  		$miFecha=date("Y/n/d ", strtotime($reg4[Fecha]));
		  ?>
		  <tr class="TxtTabla">
            <td><? echo date("M d Y ", strtotime($reg4[Fecha])); ?></td>
            <td>
			<? 	echo $reg4[CuantasHoras]; ?>
			</td>
            <td width="1%"><a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onClick="MM_openBrWindow('delHorarioFechasEsp.php?cualHorario=<? echo $pIDhorario; ?>&cualFecha=<? echo $miFecha ?>','vdFE','scrollbars=yes,resizable=yes,width=400,height=250')"></a></td>
          </tr>
		  <? } ?>
        </table>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onClick="MM_openBrWindow('addHorarioFechasEsp.php?cualHorario=<? echo $pIDhorario; ?>','adFE','scrollbars=yes,resizable=yes,width=400,height=300')" value="Ingresar"></td>
          </tr>
        </table></td>
  </tr>
</table>

</body>
</html>
