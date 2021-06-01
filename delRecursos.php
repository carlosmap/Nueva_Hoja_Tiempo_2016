<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//ES">	-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<!-- 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
-->



<?php
	session_start();
	//include("../verificaRegistro2.php");
	//include('../conectaBD.php');
	
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";

	$sql="SELECT P.*  ";
	$sql=$sql." FROM proyectos P" ;
	$sql=$sql." WHERE P.id_proyecto = " . $cualProyecto ;
	$cursor = mssql_query($sql);	
	if( $recarga == 2 )
	{
		$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");

		$sql_elimina_recurso="delete from ActividadesRecursos WHERE  id_proyecto=".$cualProyecto ;
		$cur_elimina_recurso=mssql_query($sql_elimina_recurso);

		$sql_actualiza_recurso="update  Actividades set valor=0 WHERE  id_proyecto=".$cualProyecto ;
		$cur_actualiza_recurso=mssql_query($sql_actualiza_recurso);

//echo mssql_get_last_message()."*** ".$sql_actualiza_recurso."<br> <bR>".$sql_elimina_recurso;

		if ( (trim($cur_elimina_recurso) != "") and (trim($cur_actualiza_recurso) != ""))
		{
			$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
			echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
		} 
		else 
		{
//echo "rollback  ";
			$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
			echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
		}
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

	}
				
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}


function envia2(){ 
	
		document.Form1.recarga.value="2";
		document.Form1.submit();

}
//-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<span class="TxtTabla">

</span>
<form action="" method="post" enctype="multipart/form-data"  name="Form1">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="TituloUsuario">Eliminar valores de las actividades </td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
<?
	if($reg=mssql_fetch_array($cursor))
	{
		$nom_proyecto=$reg[nombre] ;
	}
?>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="20%" class="TituloTabla">Proyecto</td>
          <td class="TxtTabla">
			<? echo $nom_proyecto; ?>
		  </td>
        </tr>
	</table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
                <td class="TxtTabla" align="center"><strong>¿Desea eliminar todos los recursos asignados a las actividades de la EDT?</strong></td>
	        </tr>
    	    <tr>
        		<td align="right" class="TxtTabla">
		  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo ucwords(strtolower($cualProyecto)); ?>" />
		  		    <input name="recarga" type="hidden" id="recarga" value="1">
		  		    <input name="Submit" type="button" class="Boton" value="Cancelar"  onClick="window.close()" >
		      <input name="Submit" type="button" class="Boton" value="Eliminar" onClick="envia2()" ></td>
	        </tr>
	</table>
	</td>
  </tr>
</table>
</form>
</body>
</html>

<? mssql_close ($conexion); ?>	
