<?php
	session_start();
	include "funciones.php";
	include "validacion.php";
	
	//$laUnidad = $laUnidaddelUsuario;
	//cambio de tabla horas, cambiar a NULL en tres consultas.
	$ret=include "validaUsrBd.php";

?>

<?php
//if($Consulta == "Consultar") {
if(trim($timestamp) != "") {
	//Verifica el rango de fechas de la actividad
	$arreglo=explode("/",$timestamp);
	$FechaLaborado=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);

	//Trae la información de las actividades con facturación para el día seleccionado
	$sql="select h.*, p.nombre, a.nombre nomActividad, c.descripcion  ";
	$sql=$sql." from horas h, proyectos p, actividades a, clase_tiempo c ";
	$sql=$sql." where h.id_proyecto = p.id_proyecto ";
	$sql=$sql." and h.id_proyecto = a.id_proyecto ";
	$sql=$sql." and h.id_actividad = a.id_actividad ";
	$sql=$sql." and h.clase_tiempo = c.clase_tiempo ";
	$sql=$sql." and h.unidad = " . $laUnidad;
	$sql=$sql." and h.fecha = '".$timestamp."'";
	$cursor = mssql_query($sql);

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hoja de tiempo</title>


<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="ts_picker.js">

</script>
<script>
<!--
function verificaSeleccion(){

	//Set wsh = WScript.CreateObject("WScript.Shell");

	pr = document.formulario.nomproyecto.value;
	ac = document.formulario.actividad.value;
	//lc = document.formulario.localizacion.value;

	if(pr == ""){
		alert('Por favor, seleccione un proyecto');
		exit();
		return 0
	 }

  if(ac == ""){
		alert('Por favor, seleccione una actividad');
		exit();
		return 0
	}else{
		rpta = confirm('Todos los datos son correctos?');
		if (rpta){
			//document.formulario.GrabarTiempo.focus();
			//wsh.SendKeys("{ENTER}");
			return rpta
		}else{
			exit();
			return 0
		}
	}
}

function numCaracteres(){
	if(document.formulario.resumentrabajo.value.length < 20) {
		alert('El resumen de trabajo debe tener mínimo 20 caracteres');
		//document.formulario.resumentrabajo.focus();
	}
}
function validar(){

	var nomp=document.formulario.nomproyecto.options.value.length;
	var acti=document.formulario.actividad.options.value.length;
	var fech=document.formulario.timestamp.value.length;
	//var loca=document.formulario.localizacion.options.value.length;
	//var cadi=document.formulario.cargos_adicionales.options.value.length;
	//var cltp=document.formulario.clasetiempo.options.value.length;
	var hora=document.formulario.horas.options.value.length;
	var retr=document.formulario.resumentrabajo.value.length;

	if(retr>200){
		rpta=window.confirm("Aviso. Usted ha escrito más de 200 caracteres\n en el campo resumen trabajo ")
		if(rpta){
			rpta=!rpta;
			return rpta;
		}else{
			return rpta;
		}
	}

	resultado=acti*nomp*fech*loca*cadi*hora*retr
		if(resultado){
			//Valida el campo horas
			var numhoras=document.formulario.horas.options.value;
			hor=numhoras;
			menj2=" horas, es correcto?";
			var fec=document.formulario.timestamp.value;

			rpta=window.confirm("Se grabará " +hor +menj2);

			if(rpta){
				document.formulario.submit();
			}else{
			//	rpta=!rpta;
				return rpta;
			}

		}else{
				rpta=window.confirm("Error.\nExisten campos en blanco ")
				if(rpta){
					rpta=!rpta;
					return rpta;
				}else{
					return rpta;
				}
		}


}
function GViaticos(url){
	var newwindow;
	newwindow=window.open(url,'name','height=450,width=450, resizable=no');
	if (window.focus) {newwindow.focus()}
}

function ShowButton(objName, ImageName) {
	objName.src=ImageName
}

function PreloadImages() {
  if(document.images)
    { if (!document.tmpImages)
         document.tmpImages=new Array();
      with(document) {
       var
          i,j=tmpImages.length,
          a=PreloadImages.arguments;

       for(i=0; i<a.length; i++)
          if (a[i].indexOf("#")!=0) {
             tmpImages[j]=new Image;
             tmpImages[j++].src=a[i];
          }
      }
    }
}

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

</head>

<body bgcolor="#EAEAEA" leftmargin="5" topmargin="0" marginwidth="0" marginheight="0">
<? include("bannerArriba.php") ; ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right">
	<?
		echo strtoupper($nombreempleado." ".$apellidoempleado);
	?>	</td>
  </tr>
</table>

      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Hoja de tiempo -Actualizaci&oacute;n  resumen de trabajo </td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td><FORM name='formulario' ACTION="" METHOD="POST">

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><TABLE width="100%" border="0" cellpadding=0 cellspacing=1 bordercolorlight=#FFFFFF bordercolordark="#DFE8FD">
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">Fecha</TD>
	
	<TD valign="top" class="TxtTabla">
	<!--Aqui va la fecha seleccionable-->
	
		<input name="timestamp" size="25" value=<?echo $timestamp;?> >
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.formulario.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>		<input name="Consulta" type="submit" class="Boton" value="Consultar" /></TD></TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">			</TD>
	<TD valign="top" class="TxtTabla">	</TD>
	</TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">			</TD>
	<TD valign="top" class="TxtTabla">	</TD>
	</TR>
	</TABLE></td>
  </tr>
</table>

</form></td>
        </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td bgcolor="#FFFFFF">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TituloUsuario">Registro de facturaci&oacute;n para el d&iacute;a seleccionado </td>
              </tr>
            </table>
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr class="TituloTabla2">
    <td>Proyecto</td>
    <td>Actividad</td>
    <td>Fecha</td>
    <td>Localizaci&oacute;n</td>
    <td width="12%">Clase Tiempo </td>
    <td width="5%">Horas registradas </td>
    <td>Resumen de trabajo </td>
    <td width="1%">&nbsp;</td>
    </tr>
   <? while ($reg=mssql_fetch_array($cursor)) {   ?>
  <tr class="TxtTabla">
    <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
    <td><? echo  ucwords(strtolower($reg[nomActividad])) ; ?></td>
    <td><? echo date("M d Y ", strtotime($reg[fecha])); ?></td>
    <td><? echo  strtolower($reg[localizacion]) ; ?></td>
    <td width="12%"><? echo  ucwords(strtolower($reg[descripcion])) ; ?></td>
    <td width="5%"><? echo  ucwords(strtolower($reg[horas_registradas])) ; ?></td>
    <td><? echo $reg[resumen_trabajo] ; ?></td>
    <td width="1%"><a href="#"><img src="img/images/actualizar.jpg" alt="Editar" width="19" height="17" border="0" onclick="MM_openBrWindow('upResumen.php?cualProy=<? echo $reg[id_proyecto]; ?>&cualAct=<? echo $reg[id_actividad]; ?>&cualFecha=<? echo date("Y/n/d", strtotime($reg[fecha])); ?>&cualCargo=<? echo $reg[cargo]; ?>&cualClase=<? echo $reg[clase_tiempo]; ?>','vupRes','scrollbars=yes,resizable=yes,width=500,height=300')" /></a></td>
    </tr>
	<? } ?>
</table>		  </td>
        </tr>
      </table>
      

      <div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
        <div align="center"> EDICI&Oacute;N REGISTROS </div>
</div>
       
      
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="Página principal Hoja Tiempo" /></td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="TituloTabla2">Ingetec S.A &copy; 2007 </td>
  </tr>
</table>
</body>
</html>
