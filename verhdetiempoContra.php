<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />


<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>

<body  class="TxtTabla" >
<?
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

$cur_usu=mssql_query("select nombre,apellidos,unidad from Usuarios where unidad=".$unidad_u);
$dato_usu=mssql_fetch_array($cur_usu);


?>
<? include("bannerArriba.php") ; ?>
<table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF" >
  <tr>
    <td class="TxtTabla" colspan="2" >&nbsp;  </td>
  </tr>

  <tr>
    <td class="TituloUsuario" colspan="2" > .:: Informaci&oacute;n del usuario ::. </td>
  </tr>
  <tr>
    <td  class="TituloTabla"> Unidad </td>
    <td   class="TxtTabla"><? echo strtoupper($dato_usu["unidad"]); 	?></td>
  </tr>
  <tr>
    <td width="10%"  class="TituloTabla"> Nombre </td>
    <td   class="TxtTabla"><? echo strtoupper($dato_usu["nombre"]." ".$dato_usu["apellidos"]); 	
	$mes = array( 'Seleccione Mes', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?></td>
  </tr>
  <tr>
    <td  class="TituloTabla"> Mes </td>
    <td   class="TxtTabla"><? echo $mes[$pMes]	?></td>
  </tr>
  <tr>
    <td  class="TituloTabla"> A&ntilde;o </td>
    <td   class="TxtTabla"><? echo $pAno;	?></td>
  </tr>
  <tr>
    <td class="TxtTabla" colspan="2" >&nbsp;</td>
  </tr>
  <tr>
    <td class="TxtTabla" colspan="2" ><a href="htContratosHT.php?pMes=<?=$pMes ?>&pAno=<?=$pAno ?>&unidad_u=<?=$unidad_u ?>&pNombre=<?=$pNombre ?>&pRetirado=<?=$pRetirado ?>&pUnidad=<?=$pUnidad ?>&pDepto=<?=$pDepto ?>&pDivision=<?=$pDivision ?>&pEmpresa=<?=$pEmpresa ?>&revision=<?=$revision ?>"  class="menu"> << Regresar al listado de usuarios </a>
	</td>
  </tr>
</table>

<?

include ("htTablaFacturacion.php");
?>
</body>
</html>