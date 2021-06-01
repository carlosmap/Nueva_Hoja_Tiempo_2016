<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

/*
echo "Dependencia" . $cualDependencia . "<br>"; 
echo "Anterior Dependencia =" . $antDependencia . "<br>";  
echo "Division=" . $cualDivision . "<br>"; 
echo "Anterior Division =" . $antDivision . "<br>";  
*/

//10Abr2008
//Trae el listado de Dependencias
$sql="select * from dependencias ";
$cursor = mssql_query($sql);
if (trim($cualDependencia) == "" ) {
	if ($reg=mssql_fetch_array($cursor)) { 
		$cualDependencia =  $reg[id_dependencia] ;
		//Ejecuta de nuevo el cursor para que muestre toda la lista de dependencias
		$cursor = mssql_query($sql);
	}
}
else {
	//Si cambia la dependencia blanquea los valores de división y departamento
	if ($cualDependencia <> $antDependencia) {
		$cualDivision = "";
		$cualDpto =  "" ;
	}
}

//10Abr2008
//Trae el listado de divisiones
$sql2="Select * from divisiones ";
$sql2=$sql2." where id_dependencia = " . $cualDependencia ;
$sql2=$sql2." and  estadoDiv = 'A' "  ;
$cursor2 = mssql_query($sql2);
if (trim($cualDivision) == "" ) {
	if ($reg2=mssql_fetch_array($cursor2)) { 
		$cualDivision =  $reg2[id_division] ;
		//Ejecuta de nuevo el cursor para que muestre toda la lista de divisiones
		$cursor2 = mssql_query($sql2);
	}
}
else {
	//Si cambia la división blanquea los valores de departamento
	if ($cualDivision <> $antDivision) {
		$cualDpto =  "" ;
	}
}

//10Abr2008
//Trae la lista de departamentos de la división seleccionada
//id_departamento, nombre, id_director, id_division
$sql3="select * from departamentos ";
$sql3=$sql3." where id_division = "  . $cualDivision ;
$sql3=$sql3." and estadoDpto = 'A' "  ;
$cursor3 = mssql_query($sql3);
if (trim($cualDpto) == "" ) {
	if ($reg3=mssql_fetch_array($cursor3)) { 
		$cualDpto =  $reg3[id_departamento] ;
		//Ejecuta de nuevo el cursor para que muestre toda la lista de departamentos
		$cursor3 = mssql_query($sql3);
	}
}


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

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Estructura Organizacional Ingetec</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 521px; height: 30px;"> Estructura organizacional de Ingetec </div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <form name="form1" id="form1" method="post" action="">

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Dependencias</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">

          <tr class="TituloTabla2">
            <td width="3%">&nbsp;</td>
            <td width="5%">ID</td>
            <td>Dependencia</td>
            <td width="1%">&nbsp;</td>
            <td width="1%">&nbsp;</td>
          </tr>
		   <? while ($reg=mssql_fetch_array($cursor)) { ?>
          <tr class="TxtTabla">
            <td width="3%" align="center">
			<? 
			//id_dependencia, nombre, id_director
			if ($cualDependencia == $reg[id_dependencia]) {
				$selDep = "checked" ;
			}
			else {
				$selDep = "" ;
			}
			
			?>
			<input name="cualDependencia" type="radio" value="<? echo  $reg[id_dependencia] ; ?>" onClick="document.form1.submit();" <? echo $selDep; ?> /></td>
            <td width="5%"><? echo  $reg[id_dependencia] ; ?></td>
            <td><? echo  strtoupper($reg[nombre]) ; ?></td>
            <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Editar" width="19" height="17" border="0" onclick="MM_openBrWindow('upDependencia.php?cualDependencia=<? echo $reg[id_dependencia]; ?>','vUpDep','scrollbars=yes,resizable=yes,width=500,height=150')" /></a></td>
            <td width="1%">
			<?
			$dSql="SELECT COALESCE(COUNT(*), 0) hayDiv ";
			$dSql=$dSql." FROM divisiones  ";
			$dSql=$dSql." WHERE id_dependencia = " . $reg[id_dependencia] ;
			$dCursor = mssql_query($dSql);
			if ($dReg=mssql_fetch_array($dCursor)) {
				$cuantasDiv= $dReg[hayDiv];
			}
			if ($cuantasDiv == 0) {
			?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delDependencia.php?cualDependencia=<? echo $reg[id_dependencia]; ?>','vdeDep','scrollbars=yes,resizable=yes,width=500,height=150')" /></a>
			<? } ?>
			</td>
          </tr>
		  <? } ?>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
            <input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addDependencia.php','aInDep','scrollbars=yes,resizable=yes,width=500,height=150')" value="Insertar" />
			</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="img/images/Pixel.gif" width="4" height="4" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Divisiones</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">

            <tr class="TituloTabla2">
              <td width="3%">&nbsp;</td>
              <td width="5%">ID</td>
              <td><input name="antDependencia" type="hidden" id="antDependencia" value="<? echo $cualDependencia; ?>" />
              Divisi&oacute;n</td>
              <td width="1%">&nbsp;</td>
              <td width="1%">&nbsp;</td>
            </tr>
		   <? while ($reg2=mssql_fetch_array($cursor2)) { 
		   //id_division, nombre, id_director, id_dependencia, id_subdirector
		   ?>
            <tr class="TxtTabla">
              <td width="3%">
			  			<? 
			//id_division, nombre, id_director, id_dependencia, id_subdirector
			if ($cualDivision == $reg2[id_division]) {
				$selDiv = "checked" ;
			}
			else {
				$selDiv = "" ;
			}
			
			?>

			  <input name="cualDivision" type="radio" value="<? echo  $reg2[id_division] ; ?>"  onClick="document.form1.submit();" <? echo $selDiv; ?>  /></td>
              <td width="5%"><? echo  $reg2[id_division] ; ?></td>
              <td><? echo  strtoupper($reg2[nombre]) ; ?></td>
              <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Editar" width="19" height="17" border="0" onclick="MM_openBrWindow('upDivision.php?cualDivision=<? echo $reg2[id_division] ; ?>','vUpDep','scrollbars=yes,resizable=yes,width=500,height=150')" /></a></td>
              <td width="1%">
			  <?
			$dSql="SELECT COALESCE(COUNT(*), 0) hayDpto ";
			$dSql=$dSql." FROM Departamentos  ";
			$dSql=$dSql." WHERE id_division = " . $reg2[id_division] ;
			$dCursor = mssql_query($dSql);
			if ($dReg=mssql_fetch_array($dCursor)) {
				$cuantosDep= $dReg[hayDpto];
			}
			if ($cuantosDep == 0) {
			?>
			  <a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delDivision.php?cualDivision=<? echo $reg2[id_division] ; ?>','vUpDep','scrollbars=yes,resizable=yes,width=500,height=150')" /></a>
			<? } ?>  
			  </td>
            </tr>
			<? } ?>

          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addDivision.php?cualDependencia=<? echo $cualDependencia; ?>','aInDep','scrollbars=yes,resizable=yes,width=500,height=150')" value="Insertar" /></td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="img/images/Pixel.gif" width="4" height="4" /></td>
          </tr>
        </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Departamentos</td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="3%" >&nbsp;</td>
            <td width="5%" >ID</td>
            <td ><input name="antDivision" type="hidden" id="antDivision" value="<? echo $cualDivision; ?>" />
            Departamento</td>
            <td width="5%" >&nbsp;</td>
            <td width="1%" >&nbsp;</td>
            <td width="1%" >&nbsp;</td>
          </tr>
		   <? while ($reg3=mssql_fetch_array($cursor3)) { 	   ?>
		  <tr>
            <td width="3%" class="TxtTabla">
			<? 
			//id_departamento, nombre, id_director, id_division
			if ($cualDpto == $reg3[id_departamento]) {
				$selDpto = "checked" ;
			}
			else {
				$selDpto = "" ;
			}
			?>
			<input name="cualDpto" type="radio" value="<? echo  $reg3[id_departamento] ; ?>" onClick="document.form1.submit();" <? echo $selDpto; ?>  /></td>
            <td width="5%" class="TxtTabla"><? echo  $reg3[id_departamento] ; ?></td>
            <td class="TxtTabla"><? echo  strtoupper($reg3[nombre]) ; ?></td>
            <td width="5%" class="TxtTabla"><input name="Submit4" type="button" class="Boton" onclick="MM_openBrWindow('verUsuDpto.php?cualDpto=<? echo $reg3[id_departamento] ; ?>','verUDepto','scrollbars=yes,resizable=yes,width=500,height=300')" value="Usuarios" /></td>
            <td width="1%" class="TxtTabla"><a href="#"><img src="img/images/actualizar.jpg" alt="Editar" width="19" height="17" border="0" onclick="MM_openBrWindow('upDpto.php?cualDpto=<? echo $reg3[id_departamento] ; ?>&cualDependencia=<? echo $cualDependencia ; ?>','vUpDpto','scrollbars=yes,resizable=yes,width=500,height=150')" /></a></td>
            <td width="1%" class="TxtTabla">
			<?
			$dSql="SELECT COALESCE(COUNT(*), 0) hayusu ";
			$dSql=$dSql." FROM usuarios ";
			$dSql=$dSql." WHERE id_departamento =" . $reg3[id_departamento] ;
			$dCursor = mssql_query($dSql);
			if ($dReg=mssql_fetch_array($dCursor)) {
				$cuantosUsu= $dReg[hayusu];
			}
			if ($cuantosUsu == 0) {
			?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar" width="14" height="13" border="0" onclick="MM_openBrWindow('delDpto.php?cualDpto=<? echo $reg3[id_departamento] ; ?>&cualDependencia=<? echo $cualDependencia ; ?>','vDelDpto','scrollbars=yes,resizable=yes,width=500,height=150')" /></a>
			<? } ?>
			</td>
          </tr>
		<? } ?>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addDpto.php?cualDependencia=<? echo $cualDependencia; ?>&cualDivision=<? echo $cualDivision; ?>','aInDpto','scrollbars=yes,resizable=yes,width=500,height=150')" value="Insertar" /></td>
            </tr>
          </table>
		</td>
      </tr>
    </table>
	
    </form>	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','UsuariosHT.php');return document.MM_returnValue" value="Lista de Usuarios" />
    <input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
