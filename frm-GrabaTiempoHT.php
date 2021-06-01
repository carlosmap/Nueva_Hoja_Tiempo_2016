<?php
	session_start();
	include "funciones.php";
	include "validacion.php";
	$laUnidad = $sesUnidadUsuarioHT;
	//$laUnidad = $laUnidaddelUsuario;
	//cambio de tabla horas, cambiar a NULL en tres consultas.
	$ret=include "validaUsrBd.php";

	if ($ret==0) {
		echo "<html><head><title>Usuario no autorizado</title>
			<meta http-equiv='refresh' content='5;url=Inicio.php'></head>
			<body>";
		echo "<br><font color=blue size=5>Usted no esta autorizado para entrar al sistema</font><br><br>
		<hr>
		Si tiene problemas para conectarse o necesita un nombre de acceso por favor comuniquese con el administrador del sitio<br><br>
		Espere 5 segundos y ser&aacute; direccionado a la pagina de inicio, si esto no ocurre haga clic <a href=Inicio.php>aqu&iacute;</a>";
		echo "</body></html>";
		exit();
	}

	//Le pone valor a la variable id_proyecto. La primera vez sera blanco, despues de un submit tendrá un valor
	$id_proyecto = $nomproyecto;
	$idA = explode("-",$actividad);
	$idActividad = $idA[0];
	$laLocalizacion = $localizacion;
	//$ElCargoAdicional = $cargos_adicionales;

	//Busca el nombre del usuario que entró

		
		$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
			FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
			WHERE     (Usuarios.unidad = '$laUnidad')";
		if ($res=mssql_query($sql)) {
			$fil=mssql_fetch_array($res);
			$categoria=$fil[categoria];
			$nombreempleado=$fil[nombre];
			$apellidoempleado=$fil[apelli];
		} else {
			alert("Usuario no registrado");
			echo "<script>location.href=\"Inicio.php\"</script>";
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
function verificaSeleccion(){

	//Set wsh = WScript.CreateObject("WScript.Shell");

	pr = document.formulario.nomproyecto.value;
	ac = document.formulario.actividad.value;
	lc = document.formulario.localizacion.value;

	if(pr == ""){
		alert('Por favor, seleccione un proyecto');
		exit();
		return 0
	}
	 if(ac == ""){
		alert('Por favor, seleccione una actividad');
		exit();
		return 0
	}

	 if(lc == ""){
		alert('Por favor, seleccione la localización');
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
	var loca=document.formulario.localizacion.options.value.length;
	var cadi=document.formulario.cargos_adicionales.options.value.length;
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




</script>

</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
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
          <td valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario"> ::: Men&uacute; de opciones ::: </td>
            </tr>
          </table></td>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Hoja de tiempo - Registro de Horas </td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td width="22%" valign="top"><table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="hdetiempo.php"> Ver Hoja de tiempo</a> </td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <A href="HtiempoPorce4.php">Ver Hoja de tiempo Porce IV</A> </td>
            </tr>
<tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="ApruebaHT.php">Revisi&Oacute;n H. de Tiempo </a></td>
            </tr>			
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="AutorizaUnidades.php">Cambiar Usuario</a> </td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="CambiodeRol.php">Autorizaciones</a></td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="ReportesHT.php">Reportes</a></td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="lstProyectosHT.php">Aprobaci&Oacute;n facturaci&Oacute;n </a></td>
            </tr>
			<?
			//La opción solo aparece para el perfil de contratos, Perfil = 16
			if ($_SESSION["sesPerfilUsuario"] == 16 ) {
			?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="ContratosHT.php" >Revisi&Oacute;n Contratos </a></td>
            </tr>
			<?  } ?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="EdicionRegistros-mnu.php">Edici&oacute;n</a></td>
            </tr>
            
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="Salir.php">Salir</a></td>
            </tr>
            <tr>
              <td class="menu">&nbsp;</td>
              <td class="menu">&nbsp;</td>
            </tr>
            <tr>
              <td class="menu">&nbsp;</td>
              <td class="menu">&nbsp;</td>
            </tr>
          </table></td>
          <td><FORM name='formulario' ACTION="frm-GrabaTiempo.php" METHOD="POST">

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><TABLE width="100%" border="0" cellpadding=0 cellspacing=1 bordercolorlight=#FFFFFF bordercolordark="#DFE8FD">
<TR> 
<TD width="30%" height="26" valign="top" class="TituloTabla">Proyecto</TD>
<TD height="26" valign="top" class="TxtTabla"><select name="nomproyecto" onChange="{formulario.actividad.value='';formulario.cargos_adicionales.value='';formulario.localizacion.value='';form.submit()}">
<?
	//Trae las los proyectos en los cuales está programado el personaje
	$sqlp="SELECT DISTINCT Asignaciones.id_proyecto, Proyectos.especial, Proyectos.nombre
	FROM Proyectos INNER JOIN Asignaciones ON Proyectos.id_proyecto = Asignaciones.id_proyecto
	WHERE (Asignaciones.unidad = '$laUnidad') and (Proyectos.id_estado=2)
	order by Proyectos.especial, Proyectos.nombre";

	$yaempezo=false;
	$cnt=0;
	if ($resultado=mssql_query($sqlp)) {
		if ($nomproyecto=="" or $nomproyecto=="Seleccione un proyecto"){
		   echo "<OPTION SELECTED>Seleccione un proyecto</OPTION>";
		}else{
		   //identifica el código del proyecto que trae y lo busca en el resultado
			while ($filas=mssql_fetch_array($resultado)){
		   	  if($filas[id_proyecto]==$nomproyecto){
			    $elnombredelproyecto=ucwords(strtolower($filas[nombre]));
			  }
			}
			echo "<OPTION VALUE='".$nomproyecto."' SELECTED>".$elnombredelproyecto."</OPTION>";
			mssql_data_seek($resultado,0);
		}
			echo "<OPTION  VALUE=''>--->>> Información Administrativa <<<---</OPTION>";
			while ($filas=mssql_fetch_array($resultado)) {
				if ($filas[especial]==1 && $yaempezo==false) {
					if ($cnt>1) {echo "<OPTION  VALUE=''>    </OPTION>";}
					if ($cnt>1) {echo "<OPTION  VALUE=''>--->>> Información Administrativa <<<---</OPTION>";}
					$yaempezo=true;
				}
				echo "<OPTION VALUE='".$filas[id_proyecto]."'>".ucwords(strtolower($filas[nombre]))."</OPTION>";
				$cnt++;
			};
	}else{
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer los proyectos!')</SCRIPT>";
	}

?>
</select>
<TR>
<TD width="30%" valign="top" class="TituloTabla">Actividad-Clase Tiempo</TD>
<TD valign="top" class="TxtTabla">
<select name='actividad' OnChange="{formulario.localizacion.value='';form.submit();}">
<?
	//Trae la actividades del proyecto que ha seleccionado
	$sql="SELECT     Asignaciones.id_actividad, Actividades.nombre, Asignaciones.clase_tiempo
	FROM Asignaciones INNER JOIN
	Actividades ON Asignaciones.id_proyecto = Actividades.id_proyecto AND
	Asignaciones.id_actividad = Actividades.id_actividad
	WHERE (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.id_proyecto = '$nomproyecto')";

	if($resultado=mssql_query($sql)){
		if ($actividad=="" or $actividad=="Seleccione una actividad"){
		   echo "<OPTION SELECTED>Seleccione una actividad</OPTION>";
		}else{
			//identifica el código del proyecto que trae y lo busca en el resultado
		   while ($filas=mssql_fetch_array($resultado)){
	   			if($filas[id_actividad]."-".$filas[clase_tiempo]==$actividad){
					$elnombredelaactividad = ucwords(strtolower($filas[nombre]))."-".$filas[clase_tiempo];
				}
		   }
		   echo "<OPTION VALUE='".$actividad."' SELECTED>".$elnombredelaactividad."</OPTION>";
		   //echo "<OPTION VALUE=0>-------</OPTION>";
		   mssql_data_seek($resultado,0);
		}

		do {
			echo "<OPTION VALUE='".$filas[id_actividad]."-".$filas[clase_tiempo]."'>".ucwords(strtolower($filas[nombre])).'-'.$filas[clase_tiempo].'</OPTION>';
		   } while ($filas=mssql_fetch_array($resultado));
		mssql_free_result($resultado);
	}else{
		echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las actividades!')</SCRIPT>";
	}
?>
</select>
<?
	//Divide la actividad por que arriba le metimos la clase de tiempo
	$act=explode("-",$actividad);
	//le quito el tipo de tiempo y solo dejo la actividad
	$actividad=$act[0];
	$clasetiempo=$act[1];
	
	//Visualiza en el formulario la posibilidad de digitar el ADP
	if($nomproyecto=="65" or $nomproyecto=="61" or $nomproyecto=="60" or $nomproyecto=="63" or $nomproyecto=="62" or $nomproyecto=="64" or $nomproyecto=="56" ){
		echo "<tr><td><b>ADP-***/Digitar</b></td><td><input type=text name=adp value=$adp></td></tr>";
	}
	
	//Lee los datos básicos de la actividad seleccionada
	$sql="SELECT datepart(year,fecha_inicial) as anno,datepart(month,fecha_inicial) as mes,datepart(day,fecha_inicial) as dia,
	 datepart(year,fecha_final) as annof,datepart(month,fecha_final) as mesf,datepart(day,fecha_final) as diaf, tiempo_asignado
	FROM Asignaciones
	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') AND (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.clase_tiempo='$clasetiempo')";

	if($resultado=mssql_query($sql)){
		$filas = mssql_fetch_array($resultado);
		$horasprogramadas=$filas[tiempo_asignado];
		$annoIni=$filas[anno];
		$mesIni=$filas[mes];
		$diaIni=$filas[dia];
		$annoFin=$filas[annof];
		$mesFin=$filas[mesf];
		$diaFin=$filas[diaf];
	}else{
		echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las fechas inicial y final de la actividad!')</SCRIPT>";
	}
?>
<TR><TD width="30%" class="TituloTabla">Inicio actividad (aa/mm/dd)</TD>
<TD class="TxtTabla"><?echo $filas[anno]."/".$filas[mes]."/".$filas[dia];?></TD>
</TR>
<TR><TD width="30%" class="TituloTabla">Final actividad (aa/mm/dd)</FONT></TD>
<TD class="TxtTabla"><?echo $filas[annof]."/".$filas[mesf]."/".$filas[diaf];?></TD>
</TR>
<TR><TD width="30%" class="TituloTabla">Horas programadas</FONT></TD>
<TD class="TxtTabla">

<?
	if ($horasprogramadas<0) {
	        echo 'Verifique con su superior la facturación disponible';
	} else {
		echo $horasprogramadas."  Horas";
	}
	echo "</TD></TR>";

	//Calcula las horas reportadas
	$sql="select sum(horas_registradas) as hr
	FROM horas
	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') AND (horas.unidad = '$laUnidad') AND (horas.clase_tiempo='$clasetiempo')";
	if($resultado=mssql_query($sql)){
		$filas = mssql_fetch_array($resultado);
		$horasregistradas=$filas[hr];
	}else{
		echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las fechas inicial y final de la actividad!')</SCRIPT>";
	}
?>
	<TR><TD width="30%" class="TituloTabla">Horas reportadas</TD>
	<TD class="TxtTabla"><?echo $horasregistradas."  Horas"?></TD>
	</TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">Fecha</TD>
	
	<TD valign="top" class="TxtTabla">
	<!--Aqui va la fecha seleccionable-->
	
		<input name="timestamp" size="25" value=<?echo $timestamp;?> >
		<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.formulario.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>			</TD></TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">Codigo proyecto</TD>
	<TD valign="top" class="TxtTabla">
<?
	//despliega los datos de código de proyecto y cargo
	$sqlp="SELECT Proyectos.codigo, Proyectos.cargo_defecto
		FROM Proyectos
		WHERE (Proyectos.id_proyecto = '$nomproyecto')";

	if($resultado=mssql_query($sqlp)){
		if ($filas = mssql_fetch_array($resultado)) {
			$codigoproyecto=$filas[codigo];
			//Almacena en una variable de session el codigo del proyecto
			$elCargo = $codigoproyecto.$ElCargoAdicional;
			$cargoxdefecto=$filas[cargo_defecto];
		}
	}else{
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer el código de los proyectos!')</SCRIPT>";
	}
	
	//Despliega la localización
	echo "<select name=localizacion>";
	if ($localizacion=="" or $localizacion=="Seleccione"){
	   echo "<OPTION SELECTED>Seleccione</OPTION>";
	   echo "<option value='1'>1-Oficina";
	   echo "<option value='2'>2-Campo";
	   echo "<option value='3'>3-Personal de planilla";
	}elseif($localizacion=='1'){
			echo "<option selected value='1'>1-Oficina";
		echo "<option value='2'>2-Campo";
	}elseif($localizacion=='2'){
			echo "<option selected value='2'>2-Campo";
		echo "<option value='1'>1-Oficina";
	}
	echo "</select>";

	echo $codigoproyecto.".";
	echo "<select name='cargos_adicionales'>";

	//Obtiene los cargos adicionales
	$sql="SELECT     cargos_adicionales
	FROM Cargos
	WHERE (id_proyecto = '$nomproyecto')";
	if($resultado=mssql_query($sql)){
		if ($cargos_adicionales=="")
			echo "<OPTION VALUE='".$cargoxdefecto."' SELECTED>".$cargoxdefecto."</OPTION>";
		else
		   echo "<OPTION VALUE='".$cargos_adicionales."' SELECTED>".$cargos_adicionales."</OPTION>";
		while ($filas=mssql_fetch_array($resultado)){
			echo '<OPTION VALUE='.$filas[cargos_adicionales].'>'.$filas[cargos_adicionales].'</OPTION>';
		}
		mssql_free_result($resultado);
	}else{
		echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer los cargos adicionales!')</SCRIPT>";
	}
	echo "</select></TD></TR>";
	
	//Compone el cargo
	$cargo = $codigoproyecto.$cargos_adicionales;
	$ElCargoAdicional = $cargo;
?>

	<!--Despliega el número de horas a facurar-->
	<TR>
	<TD width="30%" height="26" valign="top" class="TituloTabla">Horas laboradas</TD>
	<TD height="26" valign="top" class="TxtTabla">
	<select name="horas">
	<?if ($horas=="" or $horas=="Seleccione"){?>
		<option selected>Seleccione</option>
	<?}else{
		echo "<option value='".$horas."' selected>".$horas."</option>";
	}?>
	<option value="0">0</option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option> 
	</select>	</TD></TR>

	<TR>
	<TD width="30%" height="68" valign="top" class="TituloTabla">Resumen Trabajo</TD>
	<TD height="68" valign="top" class="TxtTabla"><textarea name="resumentrabajo" cols="29" rows="3"><?echo $resumentrabajo;?></textarea></TD>
	</TR>
	<TR>
	<TD width="30%" height="34" valign="top" class="TituloTabla">Viáticos</TD>
	<TD height="34" valign="top" class="TxtTabla"><a href ="javascript:verificaSeleccion();formulario.submit();GViaticos('grabaViaticos.php');"><img src='imagenes/viatico.gif' border="0"width="20" height="20"></a><font face="Arial Unicode MS" size="2">Grabar
	Viáticos</font></TD>
	</TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">			</TD>
	<TD valign="top" class="TxtTabla">
	</TD>
	</TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">&nbsp;&nbsp;</TD>
	<TD valign="top" class="TxtTabla">
	<input name="GrabarTiempo" type="submit" class="Boton"  size="20" onclick= "return validar()" value="Grabar";>	</TD></TR>
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">			</TD>
	<TD valign="top" class="TxtTabla">
	</TD>
	</TR>
	</TABLE></td>
  </tr>
</table>

</form></td>
        </tr>
</table>
      <div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
        <div align="center"> HOJA DE TIEMPO ELECTR&Oacute;NICA </div>
</div>
       
      <div class="TxtTabla" style="position:absolute;left:45px;top:108px">

</div>

<?php
if($GrabarTiempo=="Grabar"){
	//verifica que las horas registradas no sean mayores a lo que está programado
	$totalhorasregistradas=$horasregistradas+$horas;
	if ($horasprogramadas>0) {
		if($totalhorasregistradas>$horasprogramadas){
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Usted no puede registrar  $horas horas. Sobrepasa lo programado ')</SCRIPT>";
			exit();
		}
	}
	
	//Verifica el rango de fechas de la actividad
	$arreglo=explode("/",$timestamp);
	$FechaLaborado=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);
	$FechaIni= mktime(0, 0, 0, $mesIni, $diaIni, $annoIni);
	$FechaFin= mktime(0, 0, 0, $mesFin, $diaFin, $annoFin);

	//Permite facturar cuando en una actividad no hay programaciob. p.e Gastos Generales
	if ($horasprogramadas > 0){
		if($FechaLaborado<$FechaIni or $FechaLaborado>$FechaFin) {
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('La fecha está fuera del rango del inicio y final de la actividad')</SCRIPT>";
			exit();
		}
	}
	
	/*HORARIOS DEL PROYECTO*/
	//Identifica el horario al cual pertenece el parroquiano
	/* Bajo el supuesto de que el usuario tiene un horario en cada proyecto
	Si tuviera varios horarios en el mismo proyecto???, preguntarle a manuel o patricia baron si este caso se da.*/
	
	//Verificar por qué sale un nulo en la consulta (sin and IDhorario >=0)
	$sql = "select DISTINCT IDhorario from asignaciones where id_proyecto = $nomproyecto and unidad = $laUnidad and IDhorario >=0";

	if($ap = mssql_query($sql)){
		$reg = mssql_fetch_array($ap);
		$idHorario = $reg[IDhorario];
	}else{
		echo "<script>alert('No fue posible leer el horario para el proyecto')</script>";
		exit();
	}
	
	//si el control regresa de la siguiente función, es por que se puede facturar
	horasPermitidasParaFacturar($horas,$timestamp, $laUnidad, $nomproyecto,$horas,$clasetiempo,$idHorario);
	
	//********* INICIA EL PROCESO DE INSERCION DE DATOS************************************
	$sql="INSERT INTO horas VALUES('$nomproyecto','$actividad','$laUnidad','$timestamp',
	'$localizacion','$cargo','$clasetiempo','$horas', '$resumentrabajo',NULL,NULL,NULL,NULL,NULL,NULL)";
		if($registro=@mssql_query($sql)){
			//Graba la información con el adp
			if($nomproyecto=="65" or $nomproyecto=="61" or $nomproyecto=="60" or $nomproyecto=="63" or $nomproyecto=="62" or $nomproyecto=="64" or $nomproyecto=="56" ){
				//supongo que siempre funciona....
				$sql2 = "insert into adp values('$timestamp','$laUnidad','$cargo','$adp')";
				$sql1a = "select * from adp where and unidad='$laUnidad' and cargo='$cargo' and adp='$adp'";
				$ap5 = mssql_query($sql1a);
				if(mssql_num_rows($ap5) > 0){
					//por el momento no hace nada
				}else {
					mssql_query($sql2);
				}
			}
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Información de facturacion grabada')</SCRIPT>";
		} else {
			alert('Error al grabar la información de facturación. Consulte con el departamento de Sistemas');
		}
	}
	
	
//funciones del sistema
function horasPermitidasParaFacturar($hr,$ts,$lu,$idp,$hr,$ct,$idH){
/*Permite identificar si un usuario puede facturar las horas que está digitando, ya sea por que se pasa por lo facturado en fechas anteriores o por que lo que está facturando
el horario no lo permite*/
	include "validaUsrBd.php";
	//Identifica cuanto puede facturar de acuerdo con el horario del proyecto
	//Identifica el dia de la semana
	$arreglo=explode("/",$ts);
	$mk=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);
	$dia = strftime ("%a",$mk);
	
	switch ($dia){
		case "Sun":
			$dia = "Domingo";
			break;
		case "Mon":
			$dia = "Lunes";
			break;
		case "Tue":
			$dia = "Martes";
			break;
		case "Wed":
			$dia = "Miercoles";
			break;
		case "Thu":
			$dia = "Jueves"; 	
			break;
		case "Fri":
			$dia = "Viernes";
			break;
		case "Sat":
			$dia = "Sabado";
			break;
	}
	

	//Podrá facturar un sábado o un domingo si el horario establecido para el proyecto lo permite
	//Verificamos cuantas horas puede facturar en el dia correspondiente con la fecha que digitó el usuario, si es 0 no podrá facturar
	$sql = "select * from horarios where idhorario=$idH";
	if($ap = mssql_query($sql)){
		$numRegistros = mssql_num_rows($ap);
		if($numRegistros > 0) {
			$reg = mssql_fetch_array($ap);
			$horasPermitidas = $reg[$dia];
			if($horasPermitidas == 0){
				$permisoPorHorario = "no";
				/*echo "<script>alert('El horario del proyecto no permite que usted facture en esta fecha.')</script>";
				//exit();*/	
			}
		}else{
			echo "<script>alert('No hay horario establecido para este proyecto, por lo tanto no podra facturar')</script>";
			exit();
		}
	}else{
		echo "<script>alert('No fue posible leer el horario para el proyecto..')</script>";
		exit();
	}

	//Se comprueba que para ese horario la fecha digitada no sea una fecha especial
	$permisoPorFechaEspecial = "";
	$permisoPorHorario = "";
	
	$sql = "select idhorario, fecha from fechasespeciales where idhorario=$idH and fecha = '$ts'";
	if($ap = mssql_query($sql)){
		$numRegistros = mssql_num_rows($ap);
		if($numRegistros > 0) {
			$permisoPorFechaEspecial = "no";
			/*echo "<script>alert('La fecha digitada corresponde a un festivo, por lo tanto no podra facturar')</script>";
			//exit();*/
		}
	}else{
		echo "<script>alert('No fue posible leer los festivos del proyecto')</script>";
		exit();
	}
	
	//Obtiene las horas que facturó un parroquiano, en una fecha determinada, para un proyecto dado
	$sql = "SELECT  SUM(horas_registradas) AS horasRegistradas, clase_tiempo FROM Horas WHERE (unidad = $lu) AND (id_proyecto = $idp) AND
	(fecha = '$ts') GROUP BY fecha, unidad, id_proyecto";
	
	if($ap = mssql_query($sql)){
		$numRegistros = mssql_num_rows($ap);
		if($numRegistros > 0){
			$reg = mssql_fetch_array($ap);
			$sumHorasDia = $reg[horasRegistradas];
			$totalFacturadoDia = $sumHorasDia+$hr;
			$Ctiempo = trim($reg[clase_tiempo]);
			
			if($Ctiempo <= 2){
				if($totalFacturadoDia > $horasPermitidas){
					echo "<script>alert('El horario del proyecto no permite que usted facture la cantidad de horas que está grabando. Sobrepasa lo permitido')</script>";
					exit();
				}
			}
		}else{		
			//Como no encontró nada para la fecha citada, entonces compara lo permitido contra lo que intenta facturar
			if($Ctiempo <= 2){
				if($hr > $horasPermitidas){		
					echo "<script>alert('El horario del proyecto no permite que usted facture la cantidad de horas que está grabando. Sobrepasa lo permitido')</script>";
					exit();	
				}
			}
		}
	}
	
	if($permisoPorFechaEspecial=="no"){
		if($ct == "10"  or $ct == "6" or $ct == "7" or $ct == "06" or $ct == "07" or $ct == "04" or $ct == "9" or $ct == "09"){
			//Entoces el código continúa y graba en la base de datos, de lo contrario se sale del script
			return 1;
		}else{
			echo "<script>alert('La fecha digitada es festivo. Únicamente podrá facturar si la clase de tiempo es 10')</script>";
			exit();
		}
	}elseif($permisoPorHorario=="no"){
		if($ct == "10"){
			//Entoces el código continúa y graba en la base de datos, de lo contrario se sale del script
			return 1;
		}else{
			echo "<script>alert('El horario establecido para el proyecto no le permite facturar. Unicamente podrá facturar si la clase de tiempo es 10')</script>";
			exit();
		}
	}
}

?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="TituloTabla2">Ingetec S.A &copy; 2007 </td>
  </tr>
</table>
</body>
</html>
