<?php
	session_start();
	include "funciones.php";
	include "validacion.php";
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
/*
	//Le pone valor a la variable id_proyecto. La primera vez sera blanco, despues de un submit tendrá un valor
	$id_proyecto = $nomproyecto;
	$idA = explode("-",$actividad);
	$idActividad = $idA[0];
	$laLocalizacion = $localizacion;
	$ElCargoAdicional = $cargos_adicionales;

	//Busca el nombre del usuario que entró

		$laUnidad = $laUnidaddelUsuario;
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
*/
?>

<HTML>
<HEAD>
<script language="JavaScript" src="ts_picker.js">

</script>
<SCRIPT LANGUAGE="JavaScript">

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

function GViaticos(url){
	var newwindow;
	newwindow=window.open(url,'name','height=450,width=450, resizable=no');
	if (window.focus) {newwindow.focus()}
}
</SCRIPT>

<TITLE>
Control de Tiempo
</TITLE>

<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="GENERATOR" content="Actual Drawing 5.1 (http://www.pysoft.com) [JOSE]">

</HEAD>

<BODY bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#800080" alink="#FF0000">

<div id="Layer7" style="position:absolute; left:586px; top:554px; width:99px; height:62px; z-index:9">
<img src="pics/Image71557437_0.gif" width="99" height="62" border="0" name="Image_Layer7"></div>
<div id="Layer3" style="position:absolute; left:7px; top:156px; width:160px; height:352px; background-color:#FFFFFF; z-index:8">

<!--El menu principal, lo maneja la funcion de java script-->

<div  id="Layer21" style="position:absolute; left:20px; top:467px; width:110px; height:23px; z-index:2; background-color: #E3ECFB; layer-background-color: #E3ECFB; border: 1px none #000000;">
<A href="hdetiempo.php?PHP_SESSID=session_id">Hoja de tiempo</A></div>
<div id="Layer20" style="position:absolute; left:130px; top:467px; width:114px; height:23; z-index:5; background-color: #E3ECFB; layer-background-color: #E3ECFB; border: 1px none #000000;">
<A href="AutorizaUnidades.php">Cambiar Usuario</A></div>
<div id="Layer23" style="position:absolute; left:245px; top:467px; width:104px; height:23px; z-index:4; background-color: #E3ECFB; layer-background-color: #E3ECFB; border: 1px none #000000;">
<A href="CambiodeRol-mnu.php">Autorizaciones</A></div>
<div id="Layer22" style="position:absolute; left:640px; top:467px; width:49px; height:23px; z-index:3; background-color: #E3ECFB; layer-background-color: #E3ECFB; border: 1px none #000000;">
<A href="Inicio.php">Salir</A></div>
<div id="Layer25" style="position:absolute; left:350px; top:467px; width:156px; height:23px; z-index:6; background-color: #E3ECFB; layer-background-color: #E3ECFB; border: 1px none #000000;">
<A href="reportes.php">Reportes/Aprobaciones</A></div>
</div>
<div id="Layer26" style="position:absolute; left:515px; top:623px; width:129px; height:23px; z-index:7; background-color: #E3ECFB; layer-background-color: #E3ECFB; border: 1px none #000000;">
<A href="EdicionRegistros-mnu.php">Edición</A></div>
<?php
	//Ubica esta opción si el tipo tiene personal a cargo para revisar las hojas de tiempo
	$sql = "SELECT * FROM DelegacionFunciones WHERE (autorizado = $laUnidad)";
	$ap = mssql_query($sql);
	if(mssql_num_rows($ap) > 0){
		//Entonces ql personaje que esta en el sistema tiene personas a cargo las cuales les revisará la hoja de tiempo
		echo "<div id='Layer27' style='position:absolute; left:570px; top:623px; width:154px; height:41px; z-index:7'><A href='RevisarHojasdeTiempo.php'>Revisiones</A></div>";
	}else{
		echo "<div id='Layer27' style='position:absolute; left:570px; top:623px; width:154px; height:41px; z-index:7'>Revisiones</div>";
	}
?>
<!--fin de Este es el menu-->



<div id="Layer6" style="position:absolute; left:165px; top:141px; width:800px; height:390px; z-index:7">

<FORM name='formulario' ACTION="frm-GrabaTiempo3.php" METHOD="POST">
<TABLE cellpadding=1 border="1" bordercolorlight=#FFFFFF bordercolordark="#DFE8FD" cellspacing=2>
<TR> <TD width="182" height="26" valign="top">
	<font face="Arial Unicode MS" size="3" color="1E90FF">Proyecto</FONT></TD><TD width="251" height="26" valign="top">
	<select name="nomproyecto" onChange="{formulario.actividad.value='';formulario.cargos_adicionales.value='';formulario.localizacion.value='';form.submit()}">
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
			    $elnombredelproyecto=$filas[nombre];
			  }
			}
			echo "<OPTION VALUE='".$nomproyecto."' SELECTED>".$elnombredelproyecto."</OPTION>";
			mssql_data_seek($resultado,0);
		}
			echo "<OPTION  VALUE=''>-------</OPTION>";
			while ($filas=mssql_fetch_array($resultado)) {
				if ($filas[especial]==1 && $yaempezo==false) {
					if ($cnt>1) {echo "<OPTION  VALUE=''>-------</OPTION>";}
					$yaempezo=true;
				}
				echo "<OPTION VALUE='".$filas[id_proyecto]."'>".$filas[nombre]."</OPTION>";
				$cnt++;
			};
	}else{
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer los proyectos!')</SCRIPT>";
	}
	echo "</select></TD></TR>";
?>

<TR>
<TD width="182" valign="top">
<font face="Arial Unicode MS" size="3" color="1E90FF">Actividad-Clase Tiempo</FONT></TD><TD width="251" valign="top">
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
					$elnombredelaactividad=$filas[nombre]."-".$filas[clase_tiempo];
				}
		   }
		   echo "<OPTION VALUE='".$actividad."' SELECTED>".$elnombredelaactividad."</OPTION>";
		   //echo "<OPTION VALUE=0>-------</OPTION>";
		   mssql_data_seek($resultado,0);
		}

		do {
			echo "<OPTION VALUE='".$filas[id_actividad]."-".$filas[clase_tiempo]."'>".$filas[nombre].'-'.$filas[clase_tiempo].'</OPTION>';
		   } while ($filas=mssql_fetch_array($resultado));
		mssql_free_result($resultado);
	}else{
		echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las actividades!')</SCRIPT>";
	}
	echo "</select></TD></TR>";

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

<TR><TD><font face="Arial Unicode MS" size="3" color="1E90FF">Inicio actividad (aa/mm/dd)</FONT></TD><TD><?echo $filas[anno]."/".$filas[mes]."/".$filas[dia];?></TD></TR>
<TR><TD><font face="Arial Unicode MS" size="3" color="1E90FF">Final actividad (aa/mm/dd)</FONT></TD><TD><?echo $filas[annof]."/".$filas[mesf]."/".$filas[diaf];?></TD></TR>
<TR><TD><font face="Arial Unicode MS" size="3" color="1E90FF">Horas programadas</FONT></TD>
<TD>

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
	<TR><TD><font face="Arial Unicode MS" size="3" color="1E90FF">Horas reportadas</FONT></TD><TD><?echo $horasregistradas."  Horas"?></TD></TR>
	<TR>
	<TD width="182" valign="top">
	<font face="Arial Unicode MS" size="3" color="1E90FF">Fecha</FONT></TD>
	
	<TD width="251" valign="top">
	<!--Aqui va la fecha seleccionable-->
	
	<input name="timestamp" size="25" value=<?echo $timestamp;?> >
	<a href="javascript:void(0)" onClick="gfPop.fPopCalendar(document.formulario.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"></a>
	<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>			</TD></TR>
	<TR>
	<TD width="182" valign="top">
	<font face="Arial Unicode MS" size="3" color="1E90FF">Codigo proyecto</FONT></TD><TD width="251" valign="top">
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
?>

	<!--Despliega el número de horas a facurar-->
	<TR>
	<TD width="182" height="26" valign="top">
	<font face="Arial Unicode MS" size="3" color="1E90FF">Horas laboradas</FONT></TD><TD width="251" height="26" valign="top">
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
	</select>
	</TD></TR>

<?

//Permite poner víaticos en porce IV
if($elnombredelproyecto=="porce iv"){
	echo "<TR>";
	echo "<TD width='182'>
	<FONT face='Arial' color='#FF0000' size=2><B>Hora de salida, si tiene Viáticos.</B></FONT></TD>
	<TD width='251'>
	<select name=horasalida>
	<option value='".$horasalida."' selected>".$horasalida."</option>
	<option>1<option>2<option>3<option>4<option>5<option>6<option>7<option>8<option>9<option>10<option>11<option>12</select>
	<select name=ampmsal>
	<option value='".$ampmsal."' selected>".$ampmsal."</option>
	<option>AM
	<option>PM";
	echo "</TD>";
	echo "</TR>";
	echo "<TR>";
	echo "<TD width='182'>
	<FONT face='Arial' color='#FF0000' size=2><B>Hora de llegada, si tiene viáticos</B></FONT></TD>
	<TD width='251'>
	<select name=horallegada>
	<option value='".$horallegada."' selected>".$horallegada."</option>
	<option>1<option>2<option>3<option>4<option>5<option>6<option>7<option>8<option>9<option>10<option>11<option>12</select>
	<select name=ampmlleg>
	<option value='".$ampmlleg."' selected>".$ampmlleg."</option>
	<option>AM
	<option>PM";
	echo "</TD>";
	echo "</TR>";
}

?>
	
	<TR>
	<TD height="68" valign="top"><font face="Arial Unicode MS" size="3" color="1E90FF">Resumen
	Trabajo</font></TD>
	<TD height="68" valign="top"><textarea name="resumentrabajo" cols="29" rows="3" onBlur="numCaracteres();"><?echo $resumentrabajo;?></textarea></TD>
	</TR>
	<TR>
	<TD width="182" height="34" valign="top"><font face="Arial Unicode MS" size="3" color="1E90FF">Viáticos</font></TD>
	<TD width="251" height="34" valign="top"><a href ="javascript:verificaSeleccion();formulario.submit();GViaticos('grabaViaticos.php');"><img src='imagenes/ver.jpg' border="0"width="20" height="20"></a><font face="Arial Unicode MS" size="2">Grabar
	Viáticos</font></TD></TR>
	<TR>
	<TD width="182" valign="top">			</TD><TD width="251" valign="top">
	</TD></TR>
	<TR>
	<TD width="182" valign="top">			</TD><TD width="251" valign="top">
	<input type="submit" name="GrabarTiempo" value="Grabar" onclick= "return validar()";>
	
	</TD></TR>
	<TR>
	<TD width="182" valign="top">			</TD><TD width="251" valign="top">
	</TD></TR>
	</TABLE>
</form>

<FONT size=4 color=#0000FF face="Arial"><B>&nbsp;</B></FONT></div>
<div id="Layer5" style="position:absolute; left:291px; top:63px; width:357px; height:29px; z-index:6">
<img src="pics/Image22977796.gif" width="357" height="29" border="0" name="Image_Layer5"></div>
<div id="logoingetec" style="position:absolute; left:12px; top:7px; width:154px; height:43px; z-index:5">
<img src="pics/Image20783687.gif" width="154" height="43" border="0" name="Image_logoingetec"></div>

<div id="Layer4" style="position:absolute; left:-3px; top:63px; width:650px; height:91px; z-index:3">
<img src="pics/GreenRoundedImage4_0.gif" width="650" height="91" border="0" name="Image_Layer4"></div>
<div id="Layer2" style="position:absolute; left:647px; top:63px; width:81px; height:54px; z-index:2">
<img src="pics/GreenRoundedImage2_0.gif" width="81" height="54" border="0" name="Image_Layer2"></div>
<div id="Layer12" style="position:absolute; left:404px; top:-2px; width:295px; height:62px; z-index:1">
<img src="pics/GreenRoundedImage12_0.gif" width="295" height="62" border="0" name="Image_Layer12">
</div>

<div id=layerx" style="position:absolute; left:35px; top:670px; width:650px; height:91px; z-index:7">
	<?
	echo "<b><font face=Arial pointsize=13 color=#FF0000>Usuario actual: ";
	echo strtoupper($nombreempleado." ".$apellidoempleado);
	echo "</b></font>";
	?>

</div>
</BODY>
</HTML>

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

	/*//Verifica que lo programado en el dia no sea mayor a 10 horas
	$sql="SELECT SUM(horas_registradas) AS HR
	FROM Horas
	WHERE (fecha = '$timestamp') AND (unidad = '$laUnidad')";
	
	if($resultado=mssql_query($sql)){
		$filas=mssql_fetch_array($resultado);
		$numHorasEnFechaRegistro=$filas[HR];
		$totHorasDia=$numHorasEnFechaRegistro+$horas;
		if($totHorasDia>21){
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Usted no puede registrar más de 10 horas en esta fecha. Tiene registradas $numHorasEnFechaRegistro' )</SCRIPT>";
			exit();
		}
	}else{
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrio un error al leer las horas del dia.Por favor intente un poco más tarde')</SCRIPT>";
			exit();
	}*/

	//Identifica el horario al cual pertenece el parroquiano
	$sql = "select DISTINCT IDhorario from asignaciones where id_proyecto = $nomproyecto and unidad = $laUnidad";

	if($ap = mssql_query($sql)){
		$reg = mssql_fetch_array($ap);
		$idHorario = $reg[IDhorario];
	}else{
		echo "<script>alert('No fue posible leer el horario para el proyecto')</script>";
		exit();
	}
	
	//si el control regresa de la siguiente función, es por que se puede factura
	horasPermitidasParaFacturar($horas,$timestamp, $laUnidad, $nomproyecto,$horas,$clasetiempo,$idHorario);
	
	//********* INICIA EL PROCESO DE INSERCION DE DATOS************************************
	$sql="INSERT INTO horas VALUES('$nomproyecto','$actividad','$laUnidad','$timestamp',
	'$localizacion','$cargo','$clasetiempo','$horas', '$resumentrabajo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
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
				//echo "<script>alert('El horario del proyecto no permite que usted facture en esta fecha.')</script>";
				//exit();	
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
			//echo "<script>alert('La fecha digitada corresponde a un festivo, por lo tanto no podra facturar')</script>";
			//exit();
		}
	}else{
		echo "<script>alert('No fue posible leer los festivos del proyecto')</script>";
		exit();
	}
	
	//Obtiene las horas que facturó un parroquiano, en una fecha determinada, para un proyecto dado
	$sql = "SELECT  SUM(horas_registradas) AS horasRegistradas FROM Horas WHERE (unidad = $lu) AND (id_proyecto = $idp) AND
	(fecha = '$ts') GROUP BY fecha, unidad, id_proyecto";
	
	if($ap = mssql_query($sql)){
		$numRegistros = mssql_num_rows($ap);
		if($numRegistros > 0){
			$reg = mssql_fetch_array($ap);
			$sumHorasDia = $reg[horasRegistradas];
			$totalFacturadoDia = $sumHorasDia+$hr;
			if($totalFacturadoDia > $horasPermitidas){
				echo "<script>alert('El horario del proyecto no permite que usted facture la cantidad de horas que está grabando. Sobrepasa lo permitido')</script>";
				exit();
			}
		}else{		
			//Como no encontró nada para la fecha citada, entonces compara lo permitido contra lo que intenta facturar
			if($hr > $horasPermitidas){		
				echo "<script>alert('El horario del proyecto no permite que usted facture la cantidad de horas que está grabando. Sobrepasa lo permitido')</script>";
				exit();	
			}
		}
	}
	
	if($permisoPorFechaEspecial=="no"){
		if($ct == "10"){
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
