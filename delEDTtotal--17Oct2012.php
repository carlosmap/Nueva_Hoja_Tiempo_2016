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

	$sql_del_prog="delete from Programadores where id_proyecto=".$cualProyecto;
	$cursor_del_prog=mssql_query($sql_del_prog);
	if  (trim($cursor_del_prog) == "")  
	{
		$error="si";
	}
//echo mssql_get_last_message(); 

	$sql_del="delete from  HojaDeTiempo.dbo.Actividades where id_proyecto=".$cualProyecto;
	$cur_del=mssql_query($sql_del);
//echo mssql_get_last_message(); 

	if  (trim($cur_del) == "")  
	{
		$error="si";
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
	echo ("<script>window.close();MM_openBrWindow('htProgProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>

<title>Programaci&oacute;n de Proyectos</title>
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
        <tr>
          <td  class="TituloTabla">Nombre del Proyecto</td>
          <td   class="TxtTabla">
<?php 
			$sql_pro="select nombre FROM HojaDeTiempo.dbo.Proyectos  where id_proyecto=".$cualProyecto;
			$cur_sql_pro=mssql_query($sql_pro);
			if($datos_pro=mssql_fetch_array($cur_sql_pro))
			{
				echo $datos_pro["nombre"];
			}

?>	
			 
		  </td>
          <td width="3%" class="TituloTabla">ID</td>
          <td width="37%" class="TxtTabla"><?php echo $cualProyecto; ?></td>
        </tr>

      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
		<tr>
			<td  class="TxtTabla">&nbsp;</td>
		</tr>
		<tr>
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
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td  class="TxtTabla">&nbsp;</td>
		</tr>
		<tr>
			
          <td  align="center" class="TxtTabla">Nota: Al eliminar la EDT, se eliminar&aacute;n  todos sus Lotes de Control, Lotes de Trabajo, Divisiones, y Actividades. Esta operaci&oacute;n es irreversible.<br/><br />
¿Esta seguro de eliminar la EDT?</td>

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
