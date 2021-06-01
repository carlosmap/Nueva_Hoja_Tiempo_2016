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



if(trim($recarga) == "2")
{
	$error="no";
	$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");

	//Elimina los programadores
	$sql_del_prog="delete from Programadores where id_proyecto=".$cualProyecto;
	$cursor_del_prog=mssql_query($sql_del_prog);
	if  (trim($cursor_del_prog) == "")  
	{
		$error="si";
	}
//	echo $sql_del_prog . "<br>";
	//echo mssql_get_last_message(); 

	if($error=="no")
	{
		$sql_del_valor="DELETE FROM HojaDeTiempo.dbo.ActividadesRecursos ";
		$sql_del_valor=$sql_del_valor." WHERE id_proyecto = " . $cualProyecto;
		$cursor_del_valor = mssql_query($sql_del_valor);
	//	echo $sql_del_valor . "<br>";
		if  (trim($cursor_del_valor) == "")  
		{
			$error="si";
		}
	}

	if($error=="no")
	{	
		$sql_del="delete from  HojaDeTiempo.dbo.AsignaValorDivision where id_proyecto=".$cualProyecto;
		$cur_del_val_div=mssql_query($sql_del);
		if  (trim($cur_del_val_div) == "")  
		{
			$error="si";
		}
	}

	if($error=="no")
	{
		$sql_del="delete from  HojaDeTiempo.dbo.Actividades where id_proyecto=".$cualProyecto;
		$cur_del=mssql_query($sql_del);
	//	echo $sql_del . "<br>";
	//echo mssql_get_last_message(); 
		if  (trim($cur_del) == "")  
		{
			$error="si";
		}	
	}

	if  ($error=="no")  
	{
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operación realizada satisfactoriamente.');</script>"); 
	} 
	else 
	{
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la operación.');</script>");
	}
//	exit;
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>

<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Eliminar EDT </td>
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
<?php 
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
?>
	<tr class="TituloTabla2">
        <td width="10%">ID</td>
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="20%">Programadores</td>	
	</tr>
      <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?>
		<? $codProyecto = trim($reg[codigo]) ;?>
		<?
		//27Ene2009
		//Traer los cargos adicionales del proyecto
		$sqlCargos="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		$sqlCargos=$sqlCargos." where id_proyecto = " . trim($reg[id_proyecto]) ;
		$cursorCargos = mssql_query($sqlCargos);
		while ($regCargos=mssql_fetch_array($cursorCargos)) {
			echo  "<br>". "." . $regCargos[cargos_adicionales] ;
		}
		
		?>
		</td>
        <td width="20%">
		<? 
		echo "<B>Director: </B><br>" . ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" ;
		echo "<B>Coordinador: </B><br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])) . "<br>"; 
		$DirectorNombre =  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD]));
		$DirectorUnidad = $reg[id_director];
		?>
		<? 
		$oSql="select O.*, U.nombre, U.apellidos ";
		$oSql=$oSql." from GestiondeInformacionDigital.dbo.OrdenadorGasto O, HojaDeTiempo.dbo.Usuarios U  ";
		$oSql=$oSql." where O.id_proyecto =" . $reg[id_proyecto] ;
		$oSql=$oSql." and O.unidadOrdenador = U.unidad ";
		$oCursor = mssql_query($oSql);
		echo "<br><strong>Ordenadores</strong><br>" ;
		while ($oReg=mssql_fetch_array($oCursor)) {
			echo  ucwords(strtolower($oReg[nombre])) . " " . ucwords(strtolower($oReg[apellidos])) . "<br>";
		}
		?>			</td>
        <td width="20%" align="right" valign="top">
		<?
		//Lista los programadores del proyecto
		$pSql="Select P.* , U.nombre, U.apellidos ";
		$pSql=$pSql." from programadores P, Usuarios U ";
		$pSql=$pSql." where P.unidad = U.unidad ";
		$pSql=$pSql." and P.id_proyecto = " . $reg[id_proyecto] ;
		$pSql=$pSql." and P.progProyecto = 1 ";
		$pCursor = mssql_query($pSql);
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<? while ($pReg=mssql_fetch_array($pCursor)) { ?>
          <tr>
            <td align="left" class="TxtTabla"><? echo ucwords(strtolower($pReg[apellidos])). ", " . ucwords(strtolower($pReg[nombre]))   ; ?></td>
            <td width="1%">
			<? if ($verProyecto=="SI") {   ?>
			<a href="#"><img src="img/images/Del.gif" alt="Eliminar Programador del Proyecto" width="14" height="13" border="0" onclick="MM_openBrWindow('delProgProy.php?kProyecto=<? echo $pReg[id_proyecto] ; ?>&kActiv=<? echo $pReg[id_actividad]; ?>&kUnidad=<? echo $pReg[unidad]; ?>','adPP','scrollbars=yes,resizable=yes,width=500,height=200')" /></a>
			<? } ?>			</td>
          </tr>
		<? } ?>
        </table>
		</td>
	  </tr>
	  <? } ?>
      </table>

<!--
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">LOTES DE TRABAJO </td>
        </tr>
      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Identificador</td>
        <td>Nombre</td>
        <td>Responsable</td>
        </tr>
	  <?
/*
		$sql_LC="SELECT  Actividades.id_actividad,Actividades.nombre as nom_LC,Actividades.macroactividad, usu.nombre FROM HojaDeTiempo.dbo.Actividades 
inner join HojaDeTiempo.dbo.Usuarios as usu on Actividades.id_encargado=usu.unidad
WHERE id_proyecto=".$cualProyecto." and nivel = 1 order by id_actividad";
		$cursor_Lc=mssql_query($sql_LC);
		while($datos_Lc=mssql_fetch_array($cursor_Lc))
		{
*/		
	  ?>
      <tr class="TxtTabla">
        <td width="5%" align="center"><input name="pLC" type="text" class="CajaTexto" id="pLC" size="5" readonly value="<?php //echo $datos_Lc["macroactividad"]; ?>" ></td>
        <td width="5%" align="center"><input name="pLC" type="text" class="CajaTexto" id="pLC" size="20"  readonly value="<?php //echo $datos_Lc["nom_LC"]; ?>" ></td>
        <td width="5%" align="center"><input name="pLC" type="text" class="CajaTexto" id="pLC" size="20"  readonly value="<?php //echo $datos_Lc["nombre"]; ?>" ></td>
 	   </tr>	
<?php	//} ?>

    </table>
-->
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td  class="TxtTabla">&nbsp;</td>
		</tr>
		<tr>
          <td  align="center" class="TxtTabla">NOTA: Al eliminar la EDT, se eliminar&aacute;n  todos sus Lotes de Control, Lotes de Trabajo, Divisiones, y Actividades. Esta operaci&oacute;n es irreversible.<br/>
            <br />
            <strong>¿Esta seguro de eliminar la EDT?</strong></td>

		</tr>
        <tr>
          <td align="right" class="TxtTabla">
			<input type="hidden" name="recarga" id="recarga" value="2">
  		    <input name="Submit" type="button" class="Boton" value="Cancelar"  onClick="window.close()" >
  		    <input name="Submit" type="button" class="Boton" value="Eliminar" onClick="document.Form1.submit();" >
		</td>
        </tr>
		<tr>
			<td  class="TxtTabla">&nbsp;</td>
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
