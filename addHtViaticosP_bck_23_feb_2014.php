
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
include "funciones.php";
include "validaUsrBd.php";

//FALTA EL VALOR DE LA VARIABLE $cualCargo , RELACIONADO EN LA TABLA  viaticosproyecto  EN EL CAMPO cargo
//echo $cualCargo . " --------------- <br>";
//echo $cualLocaliza . "<br>";

?>

<?
/*
$cualProyecto=261;
$cualActiv=1;
$cualHorario=7;
//$cualLocaliza=1;
$cualLocaliza=1;
$cualClaseT=1;
$cualCargo=10;
$cualVigencia=2013;
$cualMes=9;
*/
?>

<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="content-type" content="text/html;" >

<script language="JavaScript" src="ts_picker.js"></script>
	<title>Reporte de vi&aacute;ticos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

</script>
<script>
//window.name("RegViatico");
window.name="RegViatico";

var  notificacion="**";    

function activaTV(){ 
//alert ("Entro a envia 1");
//document.RViaticos.recarga.value="1";
document.RViaticos.submit();

}

function valFecha(oTxt){
	var bOk = true;
	if (oTxt.value != ""){
		bOk = bOk && (valAno(oTxt));
		bOk = bOk && (valMes(oTxt));
		bOk = bOk && (valDia(oTxt));
		bOk = bOk && (valSep(oTxt));

		if (!bOk){
			alert("Fecha inválida");
			oTxt.value = "";
			oTxt.focus();
		}
	}
}


function cambiarDisplay(id) {

    var fila=document.getElementById(id);
//alert("Ingresa");

    if(((document.RViaticos.diaCompleto[0].checked || document.RViaticos.diaCompleto[1].checked)) && (document.RViaticos.fechaInicialViatico.value!="") && (document.RViaticos.fechaFinalViatico.value!="")  )
    {

		// Si la fecha inicial es = a la final, y se ha seleccionado como dia de regreso en NO, se muestra el mensaje de notificacion
        if( (document.RViaticos.diaCompleto[1].checked)&&(document.RViaticos.fechaInicialViatico.value==document.RViaticos.fechaFinalViatico.value)) 
            {           
               notificacion="seleccione SI";
                fila.style.display = "block"; //mostrar fila                
            }
        else{

                fila.style.display = "none"; //ocultar fila
            }
    }
}

</script>

<script language="javascript" type="text/javascript">

var nav4 = window.Event ? true : false;
function acceptNum(evt){   
var key = nav4 ? evt.which : evt.keyCode;   
return (key <= 13 || (key>= 48 && key <= 57) || (key==47));
}

function activa()
{
	//si se selecciona tipo de viatico como ocasional, se activa el radio de "Es dia de regreso"
	if(document.RViaticos.tipoViatico.value==1)
	{

		document.RViaticos.diaCompleto[0].removeAttribute('disabled');
		document.RViaticos.diaCompleto[1].removeAttribute('disabled');
	}
	else //si se selecciona una ocion diferente, se desabilita el radio de "Es dia de regreso"
	{	
		document.RViaticos.diaCompleto[0].disabled="true";
		document.RViaticos.diaCompleto[1].disabled="true";
		document.RViaticos.diaCompleto[0].checked=false;
		document.RViaticos.diaCompleto[1].checked=false;
	}
}

	var mes = new Array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
	function valida()
	{

		var mensaje="",ban=0;


		if(document.RViaticos.sitioTrabajo.value=="")
		{
			mensaje="Seleccione un sitio de trabajo. \n";
			ban=1;
		}

		if(document.RViaticos.tipoViatico.value=="")
		{
			mensaje=mensaje+"Seleccione el tipo de viatico. \n";
			ban=1;
		}

		//si el tipo de viatico es ocasional, se valida el dia de regreso
		if(document.RViaticos.tipoViatico.value==1)
		{	
			if(((!document.RViaticos.diaCompleto[0].checked && !document.RViaticos.diaCompleto[1].checked)))
			{
				mensaje=mensaje+"Especifique si es dia de regreso. \n";
				ban=1;
			}
		}
		if(document.RViaticos.fechaInicialViatico.value=="")
		{
			mensaje=mensaje+"Seleccione la fecha inicial. \n";
			ban=1;
		}

		if(document.RViaticos.fechaFinalViatico.value=="")
		{
			mensaje=mensaje+"Seleccione la fecha final. \n";
			ban=1;
		}

	
		if(document.RViaticos.trayectoViatico.value=="")
		{
			mensaje=mensaje+"Ingrese el trayecto realizado. \n";
			ban=1;
		}


		if(document.RViaticos.trabajoRealizado.value=="")
		{
			mensaje=mensaje+"Ingrese el trabajo realizado. \n";
			ban=1;
		}

		if(ban!=1)
		{

			//si las validaciones anteriores, no tienen inconvenientes, se valida las fechas ingresadas
			var elem = document.RViaticos.fechaFinalViatico.value.split('/');
			var mes_f = parseInt(elem[0]);
			var dia_f = parseInt(elem[1]);
			var ano_f = parseInt(elem[2]);
	
			var elem = document.RViaticos.fechaInicialViatico.value.split('/');
			var mes_i = parseInt(elem[0]);
			var dia_i =parseInt( elem[1]);
			var ano_i = parseInt(elem[2]);

			//valida si las fecha ingresadas, son validas
			if( ( !(parseInt(mes_i)) ) || (!(parseInt(dia_i))) || (!(parseInt(ano_i))) )
			{
				mensaje=mensaje+"Ingrese una fecha inicial valida. \n";
					ban=1;
			}
			else if( ( !(parseInt(mes_f)) ) || (!(parseInt(dia_f))) || (!(parseInt(ano_f))) )
			{
				mensaje=mensaje+"Ingrese una fecha final valida. \n";
					ban=1;
			}

			else if ( ((parseInt(mes_i))>12)||((parseInt(dia_i))>31) )
			{
				mensaje=mensaje+"Ingrese una fecha inicial valida. \n";
					ban=1;
			}
			else if ( ((parseInt(mes_f))>12) || ((parseInt(dia_f))>31) )
			{
				mensaje=mensaje+"Ingrese una fecha final valida. \n";
					ban=1;
			}

			else
			{

				if( (mes_i!=(parseInt(document.RViaticos.cualMes.value)) )||(mes_f!=(parseInt(document.RViaticos.cualMes.value))) )
				{
					mensaje=mensaje+"El mes seleccionado en la fecha inicial y/o final, deben corresponder al mes de "+mes[document.RViaticos.cualMes.value]+" del "+document.RViaticos.cualVigencia.value+".\n";
					ban=1;
				}
				if((parseInt(document.RViaticos.cualVigencia.value)!=ano_f)||(parseInt(document.RViaticos.cualVigencia.value)!=ano_i))
				{
					mensaje=mensaje+"El año seleccionado en la fecha inicial y/o final, deben corresponder  al año "+document.RViaticos.cualVigencia.value+".\n";
					ban=1;
				}
			}


		}

		if(ban==1)
		{
			alert(mensaje);
		}
		else
		{
			document.RViaticos.Enviar.value="Grabar Viatico";
			document.RViaticos.submit();
		}
	
	}
</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="RViaticos" action="" method="post">
<? //include("bannerArriba.php") ; ?>
<!--
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right">
	<?
	//	echo strtoupper($nombreempleado." ".$apellidoempleado);
	?>	</td>
  </tr>
</table>
-->
<?
	$cur_proy=(mssql_query("select ('['+ Proyectos.codigo+'.'+Proyectos.cargo_defecto+']') cod_proy ,Proyectos.nombre
									,Actividades.macroactividad, Actividades.nombre as actividad
									 from Actividades
									 inner join Proyectos on Actividades.id_proyecto=Proyectos.id_proyecto
								  where Actividades.id_proyecto = ".$cualProyecto." and Actividades.id_actividad=".$cualActiv));
	$datos_proy=mssql_fetch_array($cur_proy);
?>

<?
$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>

<?

//CONSULTA SI LA ACTIVIDAD SELECCIONADA TIENE PLANEACION ASOCIADA
//SOLO SE PUEDE REGISTRAR VIATICOS, SI LA ACTIVIDAD TIENE PLANEACION
$slq_planeacion="select COUNT(*) planeacion from PlaneacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$cualActiv." and unidad=".$laUnidad." and esInterno='I' and vigencia=".$cualVigencia." and mes=".$cualMes;
$cur_planeacion=mssql_query($slq_planeacion);

///echo "**********". $slq_planeacion;
$dato_planeacion=mssql_fetch_array($cur_planeacion);

if ( ( (int) $dato_planeacion["planeacion"]) ==0)
{
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">

			  <tr>
				<td class="TxtTabla">&nbsp;</td>
			
			  </tr>
			  <tr>
				<td class="TituloUsuario">.:: Atenci&oacute;n</td>
			
			  </tr>

			  <tr>
				<td align="center" class="TxtTabla"  >
				<b>	La actividad 
				'.$datos_proy["macroactividad"].' '.$datos_proy["actividad"].', no tiene planeacion asociada para el mes de
			'.$mes[$cualMes].', en el proyecto
			'.$datos_proy["cod_proy"].' '.$datos_proy["nombre"].', por lo tanto, no puede<br> 
			registrar  informacion de los viaticos para esta actividad.</b>
				</td>
			  </tr>
			  <tr>
				<td align="center" class="TituloTabla2"  >
					<input type="button" value="Cerrar" class="Boton" onClick="window.close()" >
				</td>
			  </tr>
			</table>';
}
else
{
	//echo "Con planeacion";

	//SI TIENE PLANEACION, SE CONSULTA QUE EL PROYECTO TENGA ASOCIADOS TIPOS DE VIATICOS DEFINIDOS
	$sql = "SELECT  TiposViatico.IDTipoViatico AS IDTipoViatico, TiposViatico.NomTipoViatico AS NomTipoViatico,
		TiposViaticoProy.id_proyecto AS id_proyecto, TiposViaticoProy.IDTipoViatico AS IDTipoViatico
		FROM TiposViatico INNER JOIN TiposViaticoProy ON TiposViatico.IDTipoViatico = TiposViaticoProy.IDTipoViatico
		where TiposViaticoProy.id_proyecto = $cualProyecto";
	
	$ap = mssql_query($sql);
	if(mssql_num_rows($ap) == 0){
		echo "<script>alert('No existen tipos de viáticos definidos para el proyecto seleccionado. Por lo tanto no podrá continuar');</script>";
		echo "<script>window.close();</script>";
		exit();
	}

	//CONSULTA LOS SITIOS DE TRABAJO DEL PROYECTO
	$sql_sitios = "select * from sitiostrabajo where id_proyecto = $cualProyecto";
//echo $sql_sitios." *********  <br>";
	$ap_s = mssql_query($sql_sitios);
	if(mssql_num_rows($ap_s) == 0){
		echo "<script>alert('No existen sitios de trabajo definidos para el proyecto seleccionado. Por lo tanto no podrá continuar');</script>";
		echo "<script>window.close();</script>";
		exit();
	}
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">.:: Informaci&oacute;n del usuario</td>

  </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" >

  <tr>
    <td>  
      <table width="100%"  border="0"  bgcolor="#FFFFFF">
        <tr>
<?
	$cur_usu=mssql_query("select unidad,UPPER( nombre) nombre,UPPER( apellidos) apellidos from Usuarios where unidad=".$laUnidad);
	$datos_usu=mssql_fetch_array($cur_usu);
?>
          <td class="TituloTabla" >Unidad</td>
          <td class="TxtTabla" ><?=$datos_usu["unidad"]; ?></td>
        </tr>
        <tr>
          <td class="TituloTabla" width="7%" >Nombre</td>
          <td  class="TxtTabla" ><?=$datos_usu["nombre"]." ".$datos_usu["apellidos"]; ?>
		  </td>
        </tr>
        <tr>
          <td colspan="5" class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
        <td colspan="4" class="TituloUsuario">.:: Informaci&oacute;n del registro</td>

    
      </tr>
        <tr>
          <td colspan="5" class="TxtTabla"><table width="100%" border="0" bgcolor="#FFFFFF" >
            <tr class="">


              <td width="7%" class="TituloTabla2">Proyecto</td>
              <td align="left" class="TxtTabla"><?=$datos_proy["cod_proy"]." ".$datos_proy["nombre"] ?></td>

            <tr >
              <td class="TituloTabla2">Actividad</td>
              <td align="left"  class="TxtTabla"><?=$datos_proy["macroactividad"]." ".$datos_proy["actividad"] ?></td>
            </tr>
            <tr class="">
<?
	$cur_horario=(mssql_query("select NomHorario from Horarios where IDhorario= ".$cualHorario));
	$datos_horario=mssql_fetch_array($cur_horario);
?>

              <td class="TituloTabla2">Horario</td>
              <td align="left"  class="TxtTabla"><?=$datos_horario["NomHorario"] ?></td>
            </tr>
  <tr class="">
              <td class="TituloTabla2">Loc.</td>


              <td align="left" class="TxtTabla"><?
					$sql07="SELECT * FROM TipoLocalizacion where localizacion = ".$cualLocaliza;
				  	$cursor07 =	 mssql_query($sql07);
					if ($reg07 = mssql_fetch_array($cursor07)) 
					{
						echo $reg07["nomLocalizacion"];		
					}
					
				  ?></td>

            </tr>
				<tr>
					<td class="TituloTabla2">CT</td>
					<td class="TxtTabla"><?=$cualClaseT ?></td>
				</tr>	
            <tr class="">
<?
/*
	$cur_horario=($mssql_query("select NomHorario from Horarios where IDhorario= ".$cualHorario));
	$datos_horario=mssql_fetch_array($cur_horario);
*/
?>

              <td class="TituloTabla2">Cargo</td>
              <td align="left" class="TxtTabla"><?=$cualCargo ?></td>
            </tr>

			<tr>
              <td class="TituloTabla2">Vigencia</td>
              <td align="left" class="TxtTabla"><?=$cualVigencia ?></td>
            </tr>

			<tr>
              <td class="TituloTabla2">Mes</td>

              <td align="left" class="TxtTabla"><?=$mes[$cualMes] ?></td>
            </tr>
          
          </table></td>

        </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5"   class="TituloUsuario">.::Registro de vi&aacute;ticos</td> 
        </tr>
      </table>

<table width="100%"  border="0"  bgcolor="#FFFFFF" >
<tr>
  <td width="30%" class="TituloTabla2">Seleccione el sitio de trabajo</td>
  <td width="246" class="TxtTabla">

  <select name="sitioTrabajo" id="sitioTrabajo" class="CajaTexto">
	<option value= "">Seleccione sitio de trabajo</option>
  <?php

	//CONSULTA LOS SITIOS DE TRABAJO

	while($reg = mssql_fetch_array($ap_s)){
		$sel="";
		if($sitioTrabajo==$reg["IDsitio"])
		{
			$sel="selected";
		}
		echo "<option value='$reg[IDsitio]' $sel >".str_replace('ó','&oacute;',str_replace('ñ','&ntilde;',$reg[NomSitio]))." ";
	}

?>
  </select>
    
  </td>
</tr>

<tr>
  <td height="22" class="TituloTabla2">Tipo de Vi&aacute;tico</td>
  <td class="TxtTabla">
  <select name="tipoViatico" id="tipoViatico " class="CajaTexto" onChange="activa()">
	<option value= "">Seleccione tipo de viatico</option>
  <?php

//if(mssql_num_rows($ap) > 0)

//{
	//CONSULTA LOS TIPOS DE VIATICO
	while($reg = mssql_fetch_array($ap)){
		echo "<option value=$reg[IDTipoViatico]>".str_replace('ó','\xf3',str_replace('ñ','&ntilde;',$reg[NomTipoViatico]))."";
	}

	if($tipoViatico != ""){
		//Decide cual opción dejar seleccionada
		$sql="select * from tiposviatico where idtipoviatico=$tipoViatico";
		$ap = mssql_query($sql);
		$reg = mssql_fetch_array($ap);
		echo "<option selected value= $tipoViatico>$reg[NomTipoViatico]";
	}
//}
?>
  </select>
    
  </td>
</tr>



<tr>
<td class="TituloTabla2">Fecha Inicial (mm/dd/aaaa)</td>
<td class="TxtTabla"><input name="fechaInicialViatico" id="fechaInicialViatico" type="text" size= 15 class="CajaTexto" onKeyPress="return acceptNum(event)"  onBlur="cambiarDisplay('row2'); valFecha(this); "   onChange="cambiarDisplay('row2')"  value="<? echo $fechaInicialViatico; ?>"  >
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.RViaticos.fechaInicialViatico);  return false;" onBlur="cambiarDisplay('row2'); "  ><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-30 id="gToday:normal:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
		</iframe>

</td>
</tr>

<tr>
<td class="TituloTabla2">Fecha Final (mm/dd/aaaa)</td>
<td class="TxtTabla"><table width="100%" border="0">
  <tr>
		    <td width="30%"><input name="fechaFinalViatico" id="fechaFinalViatico" type="text" size=15 class="CajaTexto"  onKeyPress="return acceptNum(event)"  onBlur="cambiarDisplay('row2'); valFecha(this);"  onChange="cambiarDisplay('row2')" value="<? echo $fechaFinalViatico; ?>"  >
              <a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.RViaticos.fechaFinalViatico); return false; " onBlur="cambiarDisplay('row2'); "  ><img name="popcal"
		 align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
              <iframe width=174 height=189 name="gToday:normal1:agenda.js" vspace=-60 id="gToday:normal1:agenda.js"
		 src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:998; position:absolute; left:-500px; top:0px;"> </iframe></td>
			 <td width="20%" class="TituloTabla2">Es d&iacute;a de regreso? </td>
		    <td class="TxtTabla">
  <?
	//PBM - 13Sep2007
	//La lista Tipo de viático sólo está disponible para 1=ocasional
  if (trim($tipoViatico) == "" ) {
  	$actTV = "";
  }
  else {
  	if($tipoViatico != 1) {
		$actTV = "disabled";
	}
	else {
		$actTV = "";
	}
  }
  
  ?>Si <input type="radio" name="diaCompleto" id="diaCompleto" value="2"  onClick="cambiarDisplay('row2')" > No <input type="radio" name="diaCompleto" id="diaCompleto" value="1" onClick="cambiarDisplay('row2')" >
			</td>
		    </tr>
		  </table></td>
</tr>

<tr>
<td class="TituloTabla2">Trayecto</td>
<td class="TxtTabla">
<input name="trayectoViatico" id="trayectoViatico" type="text" size="30" class="CajaTexto" value="<?php echo $trayectoViatico;?>"  onChange="cambiarDisplay('row2')"  >

</td>
</tr>

<tr>
<td class="TituloTabla2">Trabajo Realizado</td>
<td class="TxtTabla">
<textarea name="trabajoRealizado" id="trabajoRealizado" rows="4" cols= 30 class="CajaTexto"  onChange="cambiarDisplay('row2')" >
<?php echo $trabajoRealizado;?>
</textarea>
<input name="cualCargo" type="hidden" id="cualCargo" value="<? echo $cualCargo; ?>">

</td>
</tr>

<tr   align="left"  class="TxtTabla" > 
	<td colspan="3" width="100%" >
		<div   style="display:none" id="row2" 	>
<img src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif" title="Atención">
        La Fecha Inicial y  Final son iguales, y ha marcado d&iacute;a de regreso en No. Para este caso, deber&iacute;a marcar d&iacute;a de regreso en Si.
		</div>
	</td>
</tr>
<tr class="TxtTabla"><td></td><td><input name="Enviar1" type="button" class="Boton" value=" Grabar Viatico" onClick="valida()" >
<input type="hidden" name="Enviar" id="Enviar" value="No Grabar Viatico" >
<input type="hidden" name="cualMes" id="cualMes" value="<?=$cualMes ?>" >
<input type="hidden" name="cualVigencia" id="cualVigencia" value="<?=$cualVigencia ?>" >
</td></tr>

</table>


</form>

<?php
if($Enviar=="Grabar Viatico"){

	include "validaUsrBd.php";
	//Verifica que la hoja no esté cerrada
	$fecha = explode("/",$fechaInicialViatico);	
/*PARA TENER EN CUENTA, DE REVISAR, LA APROBACION DE LA HOJA DE TIEMPO	
	$sql = "select * from autorizacionesht where unidad=$laUnidad and vigencia = $fecha[2] and mes = $fecha[0]";
	$ap = mssql_query($sql);
	$regV = mssql_fetch_array($ap);
	$valEncargado = $regV[validaJefe];
	
	if($valEncargado == 1) {
		echo "<script>alert('Su hoja de tiempo ya fué aprobada. No podrá realizar ninguna modificación en este periodo. Su jefe inmediato/Contratos podrá desbloquearla')</script>";	
		exit();
	}
	
*/
	//22Mar2011
	//PBM
/*
	//Verificar si ya existe VoBo para los viáticos del proyecto. si existe no deja grabar.
	$laAprobacionViaticos = 0; 
	$sqlA="SELECT * ";
	$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
	$sqlA=$sqlA." WHERE unidad = " .$laUnidad ;
	$sqlA=$sqlA." and id_proyecto =" . $cualProyecto ;
	$sqlA=$sqlA." and mes = " . $fecha[0] ;
	$sqlA=$sqlA." and vigencia = " . $fecha[2] ;
	$cursorA = mssql_query($sqlA);
//	echo $sqlA;
//	exit;
	
	if ($regA=mssql_fetch_array($cursorA)) {
		$laAprobacionViaticos = $regA[validaEncargado] ; 
	}
	if (trim($laAprobacionViaticos) == "1" ) {
		echo "<script>alert('Los viáticos ya fueron aprobados. No podrá realizar ninguna modificación en este periodo. El director/coordinador o encargado del proyecto podrá levantar el VoBo de los viáticos')</script>";	
		exit();
	}	
*/
	//Cierre 22Mar2011
	
	//verifica que las dos fechas no cambien de mes ni de año
	$fiv = explode("/",$fechaInicialViatico);
	$mesFiv = $fiv[0];
	$anoFiv = $fiv[2];
	$diaFiv = $fiv[1];
	$ffv = explode("/",$fechaFinalViatico);
	$mesFfv = $ffv[0];
	$anoFfv = $ffv[2];
	$diaFfv = $ffv[1];

	if($mesFiv != $mesFfv or $anoFiv != $anoFfv){
		echo "<script>alert('Las fechas inicial y final deben ser del mismo mes y de igual año')</script>";
		exit();
	}

	//Verifica que no se graben viáticos cuando en la tabla horas no haya el tiempo registrado

	$mktFiv = mktime(0,0,0,$mesFiv,$diaFiv,$anoFiv);
	$mktFfv = mktime(0,0,0,$mesFfv,$diaFfv,$anoFfv);

	//Verifica que la fecha final del viatico no sea menor que la fecha inicial
	if ($mktFfv < $mktFiv) {
		echo "<script>alert('La fecha final del viatico debe ser mayor que la fecha inicial')</script>";
		exit();
	}
	
	while($mktFiv <= $mktFfv){
		$laFecha = date("d/M/Y",$mktFiv);
		$fec = explode("/",$laFecha);

		switch ($fec[1]) {
			case "Jan":
			$mes="01";
			break;
			case "Feb":
			$mes="02";
			break;
			case "Mar":
			$mes="03";
			break;
			case "Apr":
			$mes="04";
			break;
			case "May":
			$mes=05;
			break;
			case "Jun":
			$mes="06";
			break;
			case "Jul":
			$mes="07";
			break;
			case "Aug":
			$mes="08";
			break;
			case "Sep":
			$mes="09";
			break;
			case "Oct":
			$mes="10";
			break;
			case "Nov":
			$mes="11";
			break;
			case "Dec":
			$mes="12";
			break;
		}
		$laFecha = $fec[2]."/".$mes."/".$fec[0]	;
		$sql ="select * from horas where unidad=$laUnidad and id_actividad=$cualActiv and localizacion=$cualLocaliza and
			cargo=$ElCargoAdicional and fecha = '$laFecha' and id_proyecto = $cualProyecto";

		//Se elimina la restricción que impedia que al no tener horas registradas en una fecha determinada no puede registrar viaticos
		/*$ap = mssql_query($sql);
		if(mssql_num_rows($ap)==0){
			echo "<script>alert('En la fecha $laFecha no hay tiempo registrado. Usted no puede registrar un viático, si aún no ha registrado las horas laboradas para esa fecha');</script>";
			exit();
		}*/

		
		$mktFiv = $mktFiv+86400;
	}

		//Verifica que en la tabla Viaticosproyecto no se traslapen las fechas
		//Fecha inicio y final del mes actual
		
		$fch = explode("/",$fechaInicialViatico);
		$fch2 = explode("/",$fechaFinalViatico);
		
		$diaIniAGrabar = $fch[1];
		$diaFinAGrabar = $fch2[1];
		
		$MiMes=$fch[0];
		$MiAnno=$fch[2];
		$numDias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));
		$fechaActual="'$MiMes/1/$MiAnno' and '$MiMes/$numDias/$MiAnno'";

		//$sql ="select * from viaticosproyecto where unidad=$laUnidad and fecha between '$fechaActual'";
		$sql="SELECT rtrim(localizacion)+ '-' +rtrim(cargo) AS codigo, DAY(fechaIni) AS diaIni, DAY(fechaFin) AS diaFin, IDTipoViatico, id_proyecto, IDSitio ";
			$sql1="FROM ViaticosProyectosHT WHERE  ((fechaIni BETWEEN $fechaActual) and (fechaFin BETWEEN $fechaActual)) AND(unidad = '$laUnidad') ";
			$sql2 = "order by codigo";
			
		$sql = $sql.$sql1.$sql2;

		$ap = mssql_query($sql);
		
		$salir = 0;
		while($reg = mssql_fetch_array($ap)){
			$diaIniGrabado = $reg[diaIni];
			$diaFinGrabado = $reg[diaFin];
			/*echo "dia inicial ".$diaIniGrabado;
			echo "dia ini a grabar ".$diaIniAGrabar;
			echo "dia fin a grabar ".$diaFinAGrabar;*/
			
			for ($i=$diaIniAGrabar;$i<=$diaFinAGrabar;$i++){
				if($i >= $diaIniGrabado and $i <= $diaFinGrabado){
					$salir=$salir+1;
				}
			}
		}
		if($salir>0){
			echo "<script>alert('No es posible realizar la grabación de los viáticos debido a que se cruzan las fechas. Por favor revise las fechas de los viáticos previamente registrados.');</script>";
			exit();
			$salir = 0;
		}		
		
 	$trabajoRealizado = trim($trabajoRealizado);

	//$sqla = "insert into viaticosproyecto values($id_proyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $laUnidad, ";
//id_proyecto, IDsitio, IDfraccion, IDTipoViatico, unidad, id_actividad, localizacion, cargo, 
//FechaIni, FechaFin, Trayecto, ObjetoComision, viaticoCompleto	
//	$sqla = "insert into viaticosproyecto values($cualProyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $sesUnidadUsuario, ";

	//Si el tipo de viático es diferente de ocasional por defecto se graba es dia de regreso en 1.	
	if ($tipoViatico != 1) {
		$diaCompleto = 1;
	}
	$multiViatico=1;



	mssql_query("BEGIN TRANSACTION");
	//diacompleto=si, Y LAS FECHAS SON DIFERENTES
	if(($diaCompleto==2)&&($fechaInicialViatico!=$fechaFinalViatico))
	{
//		$fecha_inicio=strtotime($fechaInicialViatico);
		$fecha_final=strtotime($fechaFinalViatico);
		$ano=date("Y", $fecha_final);
		$mes=date("m", $fecha_final);
		$dia=( (int) date("d", $fecha_final))-1;

		$fecha_fin=$mes."/".$dia."/".$ano;


		//SI ES DIA DE FINALIZACION 
		//SE GRABAN DOS REGISTROS, EL UNO CON LA FECHA COMPRENDIDA ENTRE EL LA FECHA INICIAL Y FECHA FINAL (RESTANDOLE UN DIA)
		// Y OTRO CON LA FECHA FINAL SELECCIONADA, ALMECNANDOLO ESTE DATO COMO FECHA INICIAL Y FINAL
		$sqla = "insert into ViaticosProyectosHT  (clase_tiempo,IDhorario,vigencia,mes,id_proyecto,IDsitio,IDfraccion,IDTipoViatico,unidad,id_actividad,localizacion,cargo,FechaIni,FechaFin,Trayecto
		 ,ObjetoComision, viaticoCompleto,esInterno,usuarioCrea,fechaCrea) values($cualClaseT,$cualHorario,$cualVigencia,$cualMes,$cualProyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $laUnidad, ";
			$sqlb = "$cualActiv, $cualLocaliza, '$cualCargo', '$fechaFinalViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto','I', $laUnidad,getdate())";
		//	$sqlb = "$cualActiv, $cualLocaliza, $miCargo, '$fechaInicialViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto')";
			$sql = $sqla.$sqlb;
			$ap = mssql_query($sql);

		if  (trim($ap) != "") 
		{
		
			$sqla1 = "insert into ViaticosProyectosHT  (clase_tiempo,IDhorario,vigencia,mes,id_proyecto,IDsitio,IDfraccion,IDTipoViatico,unidad,id_actividad,localizacion,cargo,FechaIni,FechaFin,Trayecto
		 ,ObjetoComision, viaticoCompleto,esInterno,usuarioCrea,fechaCrea) values($cualClaseT,$cualHorario,$cualVigencia,$cualMes,$cualProyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $laUnidad, ";
			$sqlb1 = "$cualActiv, $cualLocaliza, '$cualCargo', '$fechaInicialViatico', '$fecha_fin', '$trayectoViatico','$trabajoRealizado', '1','I', $laUnidad,getdate())";
		//	$sqlb = "$cualActiv, $cualLocaliza, $miCargo, '$fechaInicialViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto')";
			$sql1 = $sqla1.$sqlb1;
			$ap1 = mssql_query($sql1);
		}
	}

//	if($diaCompleto==1)
	else
	{
		
			$sqla = "insert into ViaticosProyectosHT  (clase_tiempo,IDhorario,vigencia,mes,id_proyecto,IDsitio,IDfraccion,IDTipoViatico,unidad,id_actividad,localizacion,cargo,FechaIni,FechaFin,Trayecto
		 ,ObjetoComision, viaticoCompleto,esInterno,usuarioCrea,fechaCrea) values($cualClaseT,$cualHorario,$cualVigencia,$cualMes,$cualProyecto,$sitioTrabajo,$multiViatico, $tipoViatico, $laUnidad, ";
			$sqlb = "$cualActiv, $cualLocaliza, '$cualCargo', '$fechaInicialViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto','I', $laUnidad,getdate())";
		//	$sqlb = "$cualActiv, $cualLocaliza, $miCargo, '$fechaInicialViatico', '$fechaFinalViatico', '$trayectoViatico','$trabajoRealizado', '$diaCompleto')";
			$sql = $sqla.$sqlb;
			$ap1 = mssql_query($sql);
		//echo $sql."<br> ---- ".mssql_get_last_message();	

	}

	if  (trim($ap1) != "") 
	{
			mssql_query(" COMMIT TRANSACTION");
			echo "<script>alert('Viático grabado');</script>";
	}else
	{
			mssql_query(" ROLLBACK TRANSACTION");
			echo "<script>alert('Error. Información no grabada.');</script>";
	}
		
		echo ("<script>window.close();MM_openBrWindow('htFacturacion.php?pMes=".$cualMes."&pAno=".$cualVigencia."','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

//	echo "La cadena SQL <br>" . $sql."<br> *** ".mssql_get_last_message() ;
}
?>

<?
} //DEL ELSE DE LA VALIDACION DE LA PLANEACION


?>
