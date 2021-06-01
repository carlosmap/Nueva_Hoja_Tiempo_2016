<script language="JavaScript" type="text/JavaScript">


function cerrar()
{
	window.close();
}


function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}



</script>

<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
	

//CONSULTAR LOS usuarioS QUE  HAN ENVIADO LA  H.T. A REVISION DEL JEFE INMEDIATO
$sql="Select A.*, U.nombre, U.apellidos, U.retirado ";
$sql=$sql." from VoBoFirmasHT A  INNER JOIN usuarios U ON A.unidad=U.unidad " ;
$sql=$sql." where A.unidad = U.unidad " ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql= $sql. " AND A.mes = month(getdate()) ";
	$sql= $sql. " AND A.vigencia = year(getdate()) ";
}
else {
	$sql= $sql. " AND A.mes = " . $pMes;
	$sql= $sql. " AND A.vigencia =  " . $pAno;
}

//USUARIOS ACTIVOS 
if (($pRetirado == "0")) {
	$sql= $sql. " and u.retirado IS NULL ";
}

//SE CONSULTAN LOS USUARIOS RETIRADOS EN EL MES SELECCIONADO Y/O EL MES ACTUAL
if ($pRetirado == "1") {

		//USUARIOS RETIRADOS
		$sql= $sql. " and u.retirado IS NOT NULL ";

		if ($pMes == "") {
			$sql= $sql. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
			$sql= $sql. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
		}
}


if (trim($unidades) != "") {
	$sql= $sql. " AND  A.unidad = " . $unidades;
}

if (trim($nombres) != "") {
	$sql= $sql. " AND  U.nombre like '%" . $nombres."%' OR  U.apellidos like '%" . $nombres."%'" ;
}

$sql=$sql." and A.unidadJefe = " . $laUnidad;

//COMPONE UNA NUEVA CONSULTA, CON EL FIN DE VERIFICAR, SI EXISTEN H.T, SIN APROBAR
$sql_aprobados=$sql." and  validaJefe IS NULL ";

$sql=$sql." order by U.apellidos  ";
$cursor = mssql_query($sql);


//extrahe la cantidad de H.T, SIN APROBAR Y/O NO APROBADAS 
$cant_aproba=mssql_num_rows(mssql_query($sql_aprobados));



//echo $sql." <br> ******** ".mssql_get_last_message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> REVISIÓN HOJAS DE TIEMPO </div>
	</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
<table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  <tr>
    <td width="10%" align="center" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}
	?>

	<select name="pMes" class="CajaTexto" id="pMes">
      <option value="1"  <? if ($mesActual=='1'){ echo "selected"; } ?> >Enero</option>
      <option value="2" <? if ($mesActual=='2'){ echo "selected"; }  ?>>Febrero</option>
      <option value="3" <? if ($mesActual=='3'){ echo "selected"; } ?>>Marzo</option>
      <option value="4" <? if ($mesActual=='4'){ echo "selected"; }  ?>>Abril</option>
      <option value="5" <? if ($mesActual=='5'){ echo "selected"; }  ?>>Mayo</option>
      <option value="6" <? if ($mesActual=='6'){ echo "selected"; }  ?>>Junio</option>
      <option value="7" <? if ($mesActual=='7'){ echo "selected"; }  ?>>Julio</option>
      <option value="8" <? if ($mesActual=='8'){ echo "selected"; }  ?>>Agosto</option>
      <option value="9" <? if ($mesActual=='9'){ echo "selected"; }  ?>>Septiembre</option>
      <option value="10" <? if ($mesActual=='10'){ echo "selected"; }  ?>>Octubre</option>
      <option value="11" <? if ($mesActual=='11'){ echo "selected"; }  ?>>Noviembre</option>
      <option value="12" <? if ($mesActual=='12'){ echo "selected"; }  ?>>Diciembre</option>
    </select>

		</td>
	</tr>
  <tr>
    <td width="10%" align="center" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
      <select name="pAno" class="CajaTexto" id="pAno">
        <? 
	//Generar los a&ntilde;os de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el a&ntilde;o cuando se carga la p&aacute;gina por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el a&ntilde;o actual
		}
		else {
			$AnoActual= $pAno; //el a&ntilde;o seleccionado
		}

	?>
        <option value="<? echo $i; ?>" <? if ($i == $AnoActual) {	echo  "selected"; } ?> ><? echo $i; ?></option>
        <? 
	 	
	 } //for 
	 
	 ?>
      </select></td>
  </tr>
  <tr>
    <td width="12%" align="center" class="TituloTabla">Ver usuarios retirados  ? </td>
    <td class="TxtTabla"><input name="pRetirado" id="pRetirado"  type="radio" value="1" 	<? 	if ($pRetirado == 1) { 	echo "checked"; } ?> />
      Retirados
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="pRetirado" id="pRetirado"  type="radio" value="0"	<? 	if (($pRetirado == "0")) { 	echo "checked"; } ?> />
      Activos 
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="pRetirado" id="pRetirado"  type="radio" value="2"	<? 	if ( ($pRetirado == 2)|| ($pRetirado == "")) { 	echo "checked"; } ?> />
      Todos</td>
  </tr>
	<tr>
	  <td width="10%" align="center" class="TituloTabla">Unidad</td>
	  <td class="TxtTabla">
	    <input name="unidades" type="text" class="CajaTexto" id="unidades" value="<?=$unidades ?>" /></td>
	  </tr>
	<tr>
	  <td width="10%" align="center" class="TituloTabla">Nombre</td>
	  <td class="TxtTabla">
	    <input name="nombres" type="text" class="CajaTexto" id="nombres" value="<?=$nombres ?>" /></td>
	  </tr>
	<tr>
	  <td width="15%" colspan="2" align="right" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
	  </tr>
	</form>
</table>
	</td>
  </tr>
</table>



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%" rowspan="2">Unidad</td>
        <td rowspan="2">Usuarios que solicita la revisi&oacute;n </td>
        <td width="2%" rowspan="2">&nbsp;</td>
        <td colspan="2">Aprobaci&oacute;n del Jefe </td>
        <td width="1%" rowspan="2">&nbsp;</td>
        <td width="5%" rowspan="2">&nbsp;</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="5%">Aprobado</td>
        <td width="30%">Comentarios</td>
        </tr>
	<?php
        //si s4 ha consultado la facturacion, y no sse han encontrado registros
        if((mssql_num_rows($cursor)==0))
        {
    ?>
                    <tr class="TituloTabla2">
                            <td colspan="21" align="left" class="TxtTabla">&nbsp;</td>
      </tr>
                    <tr class="TituloTabla2">
    
                            <td colspan="21" align="left" class="TituloTabla2">No se encontraron registros. </td>
                  </tr>
    <?
        }

	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>

        <td><? echo ucwords(strtolower($reg[apellidos]  . " " . $reg[nombre])); ?></td>
        <td align="center"><?
			if($reg[retirado]==1)
			{

?>          <img src="imagenes/Inactivo.gif" title="Retirado de la compa&ntilde;ia" />
          <?
			}
?></td>
        <td width="5%" align="center">
		<? 
		if ($reg[validaJefe] == "1") {

?>
			<img src="img/images/Si.gif" width="16" height="14" title="Facturación aprobada" />
<?
		}
		if (($reg[validaJefe] == "0") AND (trim($reg[comentaJefe]) != "")) {

?>
		<img src="img/images/No.gif" width="12" height="16" title="Facturación no aprobada" />
<?
		}
		?>
</td>
        <td width="30%"><? echo $reg[comentaJefe]; ?></td>
        <td width="1%" align="center">
<?
		if (trim($reg[validaJefe]) != "") {

?>
			<img  src="img/images/actualizar.jpg" onClick="MM_openBrWindow('upAprobarJefeHT.php?pMes=<?=$pMes ?>&pAno=<?=$pAno ?>&unidades=<?=$reg[unidad] ?>','winHojaTiempos','scrollbars=yes,resizable=yes,width=1000,height=400')" />
<?
		}
?>
		</td>


        <td width="5%">

		<input name="Submit" type="submit" class="Boton" onClick="MM_goToURL('parent','verhdetiempo_p.php?pMes=<?=$reg[mes]; ?>&pAno=<?=$reg[vigencia]; ?>&unidad_u=<?=$reg[unidad] ?>&nombres=<?=$nombres ?>&pRetirado=<?=$pRetirado ?>&unidades=<?=$unidades ?>');return document.MM_returnValue" value="Ver Hoja" />


		 </td>
        </tr>
	  <? } ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="BotonReg" type="submit" class="Boton" id="BotonReg" value="P&aacute;gina Principal Hoja de tiempo" />
<?
	//SI LA CANTIDAD DE H.T. SIN APROBAR Y/O NO APROBADAS ES MAYOR A 0
	if ( ( (int) $cant_aproba)>0 )
	{
?>

		<input name="BotonReg2"  class="Boton" id="BotonReg2" type="button" value=" Aprobar toda la facturaci&oacute;n "  onClick="MM_openBrWindow('htAprobarJefeHT.php?pMes=<?=$pMes ?>&pAno=<?=$pAno ?>&unidades=<?=$unidades ?>&nombres=<?=$nombres ?>&pRetirado=<?=$pRetirado ?> ','winHojaTiempos','scrollbars=yes,resizable=yes,width=1400,height=400')" />
<?
	}
?>
			</td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
