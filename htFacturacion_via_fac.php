<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	

//24Jul2013
//PBM
//Inicializa el valor de las listas Mes y vigencia 
if ( (trim($pMes) == "") AND (trim($recarga)=="") ) {
	$pMes=date("n");
}
if ( (trim($pAno) == "") AND (trim($recarga)=="") ) {
	$pAno=date("Y");
}

/*
//24Jul2013
//PBM
//--Trae la planeación de una persona para un mes y año seleccionados
$sql01="SELECT A.id_proyecto, A.unidad, A.vigencia, A.mes, SUM(A.hombresMes) totHombresMes, SUM(A.horasMes) totHorasMes, B.nombre, B.codigo, B.cargo_defecto ";
$sql01=$sql01." FROM PlaneacionProyectos A, Proyectos B " ;
$sql01=$sql01." WHERE A.id_proyecto = B.id_proyecto " ;
$sql01=$sql01." AND A.unidad = " . $unidad_u ;
$sql01=$sql01." AND A.vigencia = " . $pAno ;
$sql01=$sql01." AND A.mes = " . $pMes ;
$sql01=$sql01." GROUP BY A.id_proyecto, A.unidad, A.vigencia, A.mes, B.nombre, B.codigo, B.cargo_defecto " ;
$sql01=$sql01." ORDER BY B.nombre " ;
$cursor01 =	 mssql_query($sql01);
*/


/*
//--Trae los proyectos seleccionados sin planeación para su respectiva facturación
$sql09="SELECT A.* , B.nombre, B.codigo, B.cargo_defecto ";
$sql09=$sql09." FROM ProyectosSinPlaneacion A, Proyectos B " ;
$sql09=$sql09." WHERE A.id_proyecto = B.id_proyecto  " ;
$sql09=$sql09." AND A.unidad = " . $unidad_u ;
$sql09=$sql09." AND A.mes = " . $pMes ;
$sql09=$sql09." AND A.vigencia = "  . $pAno ;
$cursor09 =	 mssql_query($sql09);
*/

$cur_usu=mssql_query("select nombre,apellidos,unidad from Usuarios where unidad=".$unidad_u);
$dato_usu=mssql_fetch_array($cur_usu);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--


function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>


<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 557px; height: 30px;">
Hoja de tiempo - Facturaci&oacute;n de proyectos</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="Fecha"></td>
      </tr>
</table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<script type="text/javascript" language="javascript">

function envia()
{
	document.form1.ban.value=2;


}

//-->
</script>



<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td align="center" class="TxtTabla">&nbsp;
	</td>
  </tr>
  <tr>
	<td height="1" align="center" class="TituloTabla"> </td>
  </tr>
</table>
<!-- No. de Registros -->
<table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF" >
  <tr>

    <td class="TituloUsuario" colspan="2" > .:: Informaci&oacute;n del usuario ::. </td>
  </tr>
  <tr>
    <td  class="TituloTabla">
		Unidad
	</td>
    <td   class="TxtTabla">
<? echo strtoupper($dato_usu["unidad"]); 	?>
	</td>

  </tr>
  <tr>
    <td width="10%"  class="TituloTabla">
	Nombre 
	</td>
    <td   class="TxtTabla">
<? echo strtoupper($dato_usu["nombre"]." ".$dato_usu["apellidos"]); 	
	$mes = array( 'Seleccione Mes', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
	</td>	
  </tr>
  <tr>
    <td  class="TituloTabla">
		Mes
	</td>
    <td   class="TxtTabla">
<? echo $mes[$pMes]	?>
	</td>
  </tr>
  <tr>
    <td  class="TituloTabla">
		A&ntilde;o
	</td>
    <td   class="TxtTabla">
<? echo $pAno;	?>
	</td>

  </tr>


</table>


    

          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
</table>

          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
          
<?
include ("htTablaFacturacion.php");
?>
		  </td>
    </tr>

          </table></td>
      </tr>


    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><input name="Submit" type="submit" class="Boton" onclick="MM_callJS('window.close();')" value="Cerrar ventana" /></td>
  </tr>
</table>
</body>
</html>
