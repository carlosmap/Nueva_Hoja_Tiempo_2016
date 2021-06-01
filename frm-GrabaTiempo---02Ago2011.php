<?php
	session_start();
	include "funciones.php";
	include "validacion.php";
	
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

//Verifica si el usuario puede ver el link de Reporte de avance de facturación
$rvfSql="select count(*) hayReg ";
$rvfSql=$rvfSql." from GestiondeInformacionDigital.dbo.UsuariosAvanceFact ";
$rvfSql=$rvfSql." where unidad = " . $laUnidad ;
$rvfCursor = mssql_query($rvfSql);
if ($rvfReg=mssql_fetch_array($rvfCursor)) {
	$linkRF = $rvfReg[hayReg];
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

	resultado=acti*nomp*fech*hora*retr
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
              <td class="TituloUsuario"> ::: Men&uacute; de opciones :::</td>
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
              <td class="menu"><a href="lstProyectosHT.php">Aprobaci&Oacute;n facturaci&Oacute;n </a></td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="AutorizaUnidades.php">Cambiar Usuario</a> </td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="CambiodeRol.php">Delegar tr&aacute;mite </a></td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="infProyecto.php">Directores / Coordinadores </a></td>
            </tr>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="EdicionRegistros-mnu.php">Edici&oacute;n</a></td>
            </tr>
            <tr> 
			  <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
			  <td class="menu"><a href="HtiempoElBuey.php" target="_blank">Hoja de tiempo PH El Buey</a></td>
		    </tr>
			<?php
				if(($laUnidad == 14005) OR ($laUnidad == 12974)) {
			?>
			<tr>
			  <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
			  <td class="menu"><a href="HtiempoElBueyEtapaII.php" target="_blank">H.T. PH El Buey Etapa 2</a></td>
		    </tr>
			
			<?php
				}
			?>
			<?php
				if(($laUnidad == 12974) OR ($laUnidad == 15061)) {
			?>
			<tr>
			  <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
			  <td class="menu"><a href="HtiempoDiquis.php" target="_blank">Hoja de tiempo El Diquis</a></td>
		    </tr>
			
			<?PHP
				}
			?>
            <? 
			//Quién puede acceder a la opción
			//Gonzalo Rodriguez, Patricia Barón, Camilo Marulanda, Fernando Manjarrés, Fabio Sánchez, María Isabel Muñoz, Gustavo Gomez
			if (($laUnidad == 12974) OR ($laUnidad == 15712) OR ($laUnidad == 14384) OR ($laUnidad == 11733) OR ($laUnidad == 13829) OR ($laUnidad == 16119) OR ($laUnidad == 4906) ) { ?>
			<tr>
			  <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
			  <td class="menu"><a href="RevisaHTDivision.php">Hojas de tiempo por División</a></td>
		    </tr>
			<tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="ProgDivision.php">Programaci&oacute;n de personal</a></td>
            </tr>
            <? } ?>
			
            <? 
			//Para que el link solo aparezca en este momento a Gonzalo rodríguez, Patricia Barón y Olga Robayo y Camilo M
			if (($laUnidad == 12974) OR ($laUnidad == 15712) OR ($laUnidad == 15320) OR ($laUnidad == 14384)) { ?>
			<tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="ProgProyectos.php">Programaci&oacute;n de proyectos </a></td>
            </tr>
            <? } ?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <a href="ReportesHT.php">Reportes</a></td>
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
              <td class="menu"><a href="ApruebaHT.php">Revisi&Oacute;n H. de Tiempo </a></td>
            </tr>
            <?
			//La opción solo aparece para el perfil de Personal, Perfil = 18
			if ($_SESSION["sesPerfilUsuario"] == 18 ) {
			?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="PersonalHT.php">Revisi&oacute;n Personal</a> </td>
            </tr>
			<?  } ?>
            <?
			//La opción solo aparece para los usuarios indicados
			//Gonzalo rodríguez, Patricia Barón 
//			if (($_SESSION["sesUnidadUsuario"] == 12974 ) OR ($_SESSION["sesUnidadUsuario"] == 15712 ) ) {
			?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="solProgramacion.php">Solicitud Programaci&oacute;n </a></td>
            </tr>
			<? //} ?>			
            <?
			//La opción solo aparece para los usuarios indicados
			//Gonzalo rodríguez, Patricia Barón y Jenny Angulo
			//9Ene2009 --> Cambia a Jenny Angulo (14194) por William Ramirez (16001)
			if (($_SESSION["sesUnidadUsuario"] == 12974 ) OR ($_SESSION["sesUnidadUsuario"] == 15712 ) OR ($_SESSION["sesUnidadUsuario"] == 16001 )) {
			?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="UsuariosHT.php">Usuarios Ingetec S.A. </a></td>
            </tr>
			<?  } ?>
            <? 
			//Si el usuario activo aparece en la tabla UsuariosAvanceFact aparece el link
			if ($linkRF > 0) { ?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="validaHT.php">Ver Avance Hojas de tiempo </a></td>
            </tr>
			<? } ?>			
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="hdetiempo.php"> Ver Hoja de tiempo</a> </td>
            </tr>
			<? 
			//Forzar que no se vea la opción en el menú
			if ($verOpcion == "VER") {
			?>
			<tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"> <A href="HtiempoPorce4.php">Ver Hoja de tiempo Porce IV</A> </td>
            </tr>
			<? } ?>
			<? 
			//13Nov2007
			//Si la unidad activa corresponde a Heidy Zambrano muestra el link para entrar a 
			//las Hojas de tiempo de las personas que facturaron a Porce III
			if ($laUnidad == 13991) {			?>
            <tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="RevisaHTporceIII.php"> Ver Hoja de tiempo Porce III</a> </td>
            </tr>
			<? } ?>
			<? 
			//Las personas autorizadas para ingresar a este módulo son:
			//Gonzalo Rodriguez, Patricia Barón, Camilo Marulanda
			//Enrique piñeros 14888, Wilson martinez 15195, Rafael ariza 14020, Gustavo Carrasco 3212
			//Katerine Olaya 16404, Sonia Hoyos 14499
			if ( ($laUnidad == 12974) OR ($laUnidad == 15712) OR ($laUnidad == 14384) OR ($laUnidad == 14888) OR ($laUnidad == 15195) OR ($laUnidad == 14020) OR ($laUnidad == 3212) OR ($laUnidad == 16404) OR ($laUnidad == 14499) ) { 
			
			?>
			<tr>
              <td class="menu"><img src="img/images/mnuFlecha.gif" width="12" height="13" /></td>
              <td class="menu"><a href="RevisaHTTMP.php" target="_blank"> Ver Hoja de tiempo temporal </a> </td>
            </tr>
			<? } ?>
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
          <td valign="top"><FORM name='formulario' ACTION="frm-GrabaTiempo.php" METHOD="POST">

            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><TABLE width="100%" border="0" cellpadding=0 cellspacing=1 bordercolorlight=#FFFFFF bordercolordark="#DFE8FD">
<TR> 
<TD width="30%" height="26" valign="top" class="TituloTabla">Proyecto</TD>
<TD height="26" valign="top" class="TxtTabla"><select name="nomproyecto" onchange="{formulario.actividad.value='';form.submit()}">
<?
	//Trae las los proyectos en los cuales está programado el personaje
	$sqlp="SELECT DISTINCT Asignaciones.id_proyecto, Proyectos.especial, Proyectos.nombre, Proyectos.codigo
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
				$codigoProyZ =  $filas[codigo] ;
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
	<TD width="30%" valign="top" class="TituloTabla">Fecha</TD>
	<TD valign="top" class="TxtTabla">
	<!--Aqui va la fecha seleccionable-->
	
		<input name="timestamp" size="25" value="<? echo $timestamp;?>"  readonly>
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.formulario.timestamp);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=-130 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
		<input name="Submit" type="submit" class="Boton" value="Consultar Actividades"  onClick="{formulario.actividad.value='';form.submit()}" /></TD>
</TR>
  <TD width="30%" valign="top" class="TituloTabla">Actividad-Clase Tiempo-Localizaci&oacute;n-Cargo</TD>
    <TD valign="top" class="TxtTabla">
<?
$eFechaSelec = explode("/",$timestamp);
$eAnoSelec = $eFechaSelec[2];
$eMesSelec = $eFechaSelec[0];

//echo "El año=" . $eAnoSelec . "<br>";
//echo "El mes=" . $eMesSelec . "<br>";


?>
<select name='actividad' OnChange="{form.submit();}">
<?
//Verifica si el proyecto es especial
$esProyEsp = "NO";
$sqlPrE="select especial from proyectos where id_proyecto = " . $nomproyecto ;
$curSqlPrE=mssql_query($sqlPrE);
if ($filSqlPrE=mssql_fetch_array($curSqlPrE)){
	if (trim($filSqlPrE[especial]) == '1') {
		$esProyEsp = "SI";
	}
	else {
		$esProyEsp = "NO";
	}
}

//Si es proyecto especial no se programa, se buscan todas las actividades, 
//de lo contrario filtra las actividades por mes y año concidente en fecha_inicial y fecha_final
if ($esProyEsp == "NO") {
	//Trae la actividades del proyecto que ha seleccionado
	$sql="SELECT     Asignaciones.id_actividad, Actividades.nombre, Asignaciones.clase_tiempo,
	Asignaciones.localizacion, Asignaciones.cargo
	FROM Asignaciones INNER JOIN
	Actividades ON Asignaciones.id_proyecto = Actividades.id_proyecto AND
	Asignaciones.id_actividad = Actividades.id_actividad
	WHERE (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.id_proyecto = '$nomproyecto')
	and month(Asignaciones.fecha_inicial) = month(Asignaciones.fecha_final)
	and year(Asignaciones.fecha_inicial) = year(Asignaciones.fecha_final)
	and (month(Asignaciones.fecha_inicial) = $eMesSelec )
	and (year(Asignaciones.fecha_inicial) = $eAnoSelec)
	and Asignaciones.tiempo_asignado > 0
	";

}
else {
	//Trae la actividades del proyecto que ha seleccionado
	$sql="SELECT     Asignaciones.id_actividad, Actividades.nombre, Asignaciones.clase_tiempo, Asignaciones.localizacion,
	Asignaciones.cargo
	FROM Asignaciones INNER JOIN
	Actividades ON Asignaciones.id_proyecto = Actividades.id_proyecto AND
	Asignaciones.id_actividad = Actividades.id_actividad
	WHERE (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.id_proyecto = '$nomproyecto')
	";
}	

	if (trim($eAnoSelec) != "") {
		if($resultado=mssql_query($sql)){
			if ($actividad=="" or $actividad=="Seleccione una actividad"){
			   echo "<OPTION SELECTED>Seleccione una actividad</OPTION>";
			}else{
				//identifica el código del proyecto que trae y lo busca en el resultado
			   while ($filas=mssql_fetch_array($resultado)){
					//if($filas[id_actividad]."-".$filas[clase_tiempo]==$actividad){
					if($filas[id_actividad]."-".$filas[clase_tiempo]."-".$filas[localizacion]."-".$filas[cargo]==$actividad){
						//$elnombredelaactividad = ucwords(strtolower($filas[nombre]))."-".$filas[clase_tiempo];
						$elnombredelaactividad = ucwords(strtolower($filas[nombre]))."-".
						$filas[clase_tiempo]."-".$filas[localizacion]."-".$filas[cargo];
					}
			   }
			   echo "<OPTION VALUE='".$actividad."' SELECTED>".$elnombredelaactividad."</OPTION>";
	
			   //echo "<OPTION VALUE=0>-------</OPTION>";
			   mssql_data_seek($resultado,0);
			}
	

			while ($filas=mssql_fetch_array($resultado)){
				echo "<OPTION VALUE='".$filas[id_actividad]."-".$filas[clase_tiempo]."-".$filas[localizacion]."-".
				$filas[cargo]."'>".
				ucwords(strtolower($filas[nombre])).'-'.$filas[clase_tiempo]."-".$filas[localizacion]."-".
				$filas[cargo].'</OPTION>';
			}   
				mssql_free_result($resultado);
		}else{
				echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las actividades!')</SCRIPT>";
		}
	} //if de validación para el año y mes en blanco
?>
</select>
<?

//echo "<br> el SQL=" . $sql . "<br>";
	//Divide la actividad por que arriba le metimos la clase de tiempo
	$act=explode("-",$actividad);
	//le quito el tipo de tiempo y solo dejo la actividad
	$actividad=$act[0];
	$clasetiempo=$act[1];
	$qlocaliza=$act[2];
	$xcargo = $act[3];
	
	//Visualiza en el formulario la posibilidad de digitar el ADP
	if($nomproyecto=="65" or $nomproyecto=="61" or $nomproyecto=="60" or $nomproyecto=="63" or $nomproyecto=="62" or $nomproyecto=="64" or $nomproyecto=="56" ){
		echo "<tr><td><b>ADP-***/Digitar</b></td><td><input type=text name=adp value=$adp></td></tr>";
	}
	
	//Lee los datos básicos de la actividad seleccionada
	/*
	$sql="SELECT datepart(year,fecha_inicial) as anno,datepart(month,fecha_inicial) as mes,datepart(day,fecha_inicial) as dia,
	 datepart(year,fecha_final) as annof,datepart(month,fecha_final) as mesf,datepart(day,fecha_final) as diaf, tiempo_asignado
	FROM Asignaciones
	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') AND (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.clase_tiempo='$clasetiempo')";
	*/

//Si es proyecto especial no se programa, se buscan todas las actividades, 
//de lo contrario filtra las actividades por mes y año concidente en fecha_inicial y fecha_final
if ($esProyEsp == "NO") {
	$sql="SELECT datepart(year,fecha_inicial) as anno,datepart(month,fecha_inicial) as mes,datepart(day,fecha_inicial) as dia,
	 datepart(year,fecha_final) as annof,datepart(month,fecha_final) as mesf,datepart(day,fecha_final) as diaf, tiempo_asignado, localizacion, cargo
	FROM Asignaciones
	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') AND (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.clase_tiempo='$clasetiempo')
	AND (Asignaciones.localizacion='$qlocaliza') 
	AND (month(Asignaciones.fecha_inicial)=$eMesSelec)
	and (year(Asignaciones.fecha_inicial)=$eAnoSelec) 
	and Asignaciones.cargo = '$xcargo'
	";
}
else {
	$sql="SELECT datepart(year,fecha_inicial) as anno,datepart(month,fecha_inicial) as mes,datepart(day,fecha_inicial) as dia,
	 datepart(year,fecha_final) as annof,datepart(month,fecha_final) as mesf,datepart(day,fecha_final) as diaf, tiempo_asignado, localizacion, cargo
	FROM Asignaciones
	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') AND (Asignaciones.unidad = '$laUnidad') AND (Asignaciones.clase_tiempo='$clasetiempo')
	AND (Asignaciones.localizacion='$qlocaliza') and asignaciones.cargo ='$xcargo'
	";

}
	if (trim($eMesSelec) != "") {
		if($resultado=mssql_query($sql)){
			$filas = mssql_fetch_array($resultado);
			$horasprogramadas=$filas[tiempo_asignado];
			$annoIni=$filas[anno];
			$mesIni=$filas[mes];
			$diaIni=$filas[dia];
			$annoFin=$filas[annof];
			$mesFin=$filas[mesf];
			$diaFin=$filas[diaf];
			$localizacion = $filas[localizacion];
			$cargo = $filas[cargo];
			$ElCargoAdicional = $cargo; //Asignaciones para usar en grabaviaticos
			$laLocalizacion = $localizacion;//Asignaciones para usar en graba viaticos
		}else{
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las fechas inicial y final de la actividad!')</SCRIPT>";
		}
	}
//echo $sql;	
?>
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
//	$sql="select sum(horas_registradas) as hr
//	FROM horas
//	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') AND (horas.unidad = '$laUnidad') AND (horas.clase_tiempo='$clasetiempo')";
	$sql="select sum(horas_registradas) as hr
	FROM horas
	WHERE (id_proyecto = '$nomproyecto') AND (id_actividad = '$actividad') 
	AND (horas.unidad = '$laUnidad') AND (horas.clase_tiempo='$clasetiempo') AND (localizacion = $localizacion)
	AND (cargo = ".  "'".$codigoProyZ.$xcargo."'". ")
	AND (month(horas.fecha) = $eMesSelec)
	AND (year(horas.fecha) = $eAnoSelec)
	";

	if (trim($localizacion) != "") {
		if($resultado=mssql_query($sql)){
			$filas = mssql_fetch_array($resultado);
			$horasregistradas=$filas[hr];
		}else{
			
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer las fechas inicial y final  de la actividad!')</SCRIPT>";
		}
	}
?>
	<TR><TD width="30%" class="TituloTabla">Horas reportadas</TD>
	<TD class="TxtTabla"><?echo $horasregistradas."  Horas"?></TD>
	</TR>
	
	<TR>
	<TD width="30%" valign="top" class="TituloTabla">Codigo proyecto</TD>
	<TD valign="top" class="TxtTabla">
<?
	//despliega los datos de código de proyecto y cargo
	$sqlp="SELECT Proyectos.codigo, Proyectos.cargo_defecto
		FROM Proyectos
		WHERE (Proyectos.id_proyecto = '$nomproyecto') and id_estado = 2";

	if($resultado=mssql_query($sqlp)){
		if ($filas = mssql_fetch_array($resultado)) {
			$codigoproyecto=$filas[codigo];
			//Almacena en una variable de session el codigo del proyecto
			//$elCargo = $codigoproyecto.$ElCargoAdicional;
			$elCargo = $codigoproyecto.$cargo;
			//$cargoxdefecto=$filas[cargo_defecto];
		}
	}else{
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Ocurrió un error al leer el código de los proyectos!')</SCRIPT>";
	}
	
	//Despliega la localización
	/*echo "<select name=localizacion>";
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
	*/
	echo $localizacion."-".$elCargo;
	
	/*echo "<select name='cargos_adicionales'>";

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
	*/
	//Compone el cargo
	//$cargo = $codigoproyecto.$cargos_adicionales;
	//$ElCargoAdicional = $cargo;
	$cargo = $elCargo;
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
	<TD height="34" valign="top" class="TxtTabla">
	<? //if (trim($localizacion) != "1") { ?>
	<a href ="javascript:verificaSeleccion();formulario.submit();GViaticos('grabaViaticos.php?miCargoAdicional=<? echo $elCargo; ?>&laLocalizacion=<? echo $laLocalizacion; ?>');"><img src='imagenes/viatico.gif' border="0"width="20" height="20"></a>
	<? // } 
	   //else { 
		//<a href ="javascript:alert('No puede grabar viáticos cuando la localización es oficina (1)')"><img src='imagenes/viatico.gif' border="0"width="20" height="20"></a>
	// } ?>
	<font face="Arial Unicode MS" size="2">Grabar
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
	
	//Verifica que la hoja de tiempo esté sin firmar por el jefe inmediato. Estar firmada significa que esta cerrrada
	$fecha = explode("/",$timestamp);
	$verifCerrada = "select * from autorizacionesHT where unidad= $laUnidad and vigencia=$fecha[2] and mes=$fecha[0] and
	validaJefe = 1";

	$cursor = mssql_query($verifCerrada);
	$numReg = mssql_num_rows($cursor);
	
	if($numReg > 0) {
		echo "<script>alert('Su hoja de tiempo se encuentra cerrada, por lo tanto no podrá modificarla. La persona que le firmó la hoja de tiempo podrá desbloquearla')</script>";
		exit();
	}

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
/*	if ($horasprogramadas > 0){
		if($FechaLaborado<$FechaIni or $FechaLaborado>$FechaFin) {
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('La fecha está fuera del rango del inicio y final de la actividad')</SCRIPT>";
			exit();
		}
	}
	*/
	
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

	//Valida que la cantidad de horas de Lunes a Jueves <= 9 y viernes <= 8 para clase de tiempo = 1 	
//	if ($clasetiempo == 1) {
//		horasPorDia($horas,$timestamp,$clasetiempo, $laUnidad) ;
//	}

	//Valida que la la fecha no sea sábado, domingo o festivo para clase de tiempo = 1
	if (($clasetiempo == 1) OR ($clasetiempo == 2) OR ($clasetiempo == 11)) {
		diasHabiles($timestamp, $nomproyecto, $actividad, $laUnidad, $clasetiempo, $laLocalizacion, $ElCargoAdicional ) ;
	}
	
//Valida que no se pueda ingresar tiempo extra (clase_tiempo <> 1 o 2) si no se ha 
//registrado previamente tiempo normal (clase_tiempo = 1 o 2)
//Algo pasa no funciona con ur or
//if($clasetiempo != "1" and $clasetiempo != "2" and $clasetiempo != 11) {
if($clasetiempo != "1" and $clasetiempo != "2" and $clasetiempo != 11 and $clasetiempo != 3) {
	tiempoExtra($timestamp, $laUnidad, $idHorario ) ;
}

//PBM 13Asep2007
//Valida que la sumatoria de horas en una fecha no supere la cantidad de horas máxima 
//de los horarios en que se encuentra involucrado un usuario en un mes y año dados
if (($clasetiempo == 1) OR ($clasetiempo == 2) OR ($clasetiempo == 11)) {
	totalizarHorasDia($timestamp, $laUnidad, $horas);
}

//PBM06Ene2009
//Verifica que la localización y actividad sean diferente de 0 y vacio
if (($localizacion == 0) OR ($actividad == 0) OR (trim($localizacion) == "") OR (trim($actividad) == "") ) {
	echo "<script>alert('ATENCIÓN. Su sesión se ha vencido. Por favor ingrese de nuevo al Portal.')</script>";
	echo "<script>window.close()</script>";
	exit();
}
//fin 06Ene2009

	//********* INICIA EL PROCESO DE INSERCION DE DATOS************************************
	$sql="INSERT INTO horas VALUES('$nomproyecto','$actividad','$laUnidad','$timestamp',
	'$localizacion','$cargo','$clasetiempo','$horas', '$resumentrabajo',NULL,NULL,NULL,NULL,NULL,NULL)";

//echo $sql;
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
			alert('Error al grabar la información de facturación. Verifique la fecha en la hoja de tiempo');
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
		//Para que no valide festivos normales, esto se validaria por diasHabiles
/*			echo "<script>alert('La fecha digitada es festivo. Únicamente podrá facturar si la clase de tiempo es 10')</script>";
			exit();*/
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

function horasPorDia($hr,$ts,$ct, $uni){
/*
27Ago2007
PBM
Permite controlar que para clase de tiempo = 1 pueda grabarse 
entre 1 y 9 para los dias Lunes, Martes, Miércoles, Jueves
entre 1 y 8 los viernes
*/

	include "validaUsrBd.php";

	//Identifica el dia de la semana
	$arreglo=explode("/",$ts);
	$mk=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);
	$dia = strftime ("%a",$mk);
	$laFecha = $arreglo[2] . "-" . $arreglo[0] . "-" . $arreglo[1];
	
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
	
	$totHorasR = 0;
	//encuentra la sumatoria para clase de tiempo, unidad y fecha de las horas registradas
	$zSql="select sum(horas_registradas) totalHoras from horas ";
	$zSql=$zSql." where unidad = " . $uni ;
//	$zSql=$zSql." and clase_tiempo =" . $ct ;
	$zSql=$zSql." and clase_tiempo = 1 " ;
	$zSql=$zSql." and fecha = '" . $laFecha . "' " ;
	$cursorzSql = mssql_query($zSql);
	if ($regzSql=mssql_fetch_array($cursorzSql)) {
		$CantHorasR= $regzSql[totalHoras];
	}
	else {
		$CantHorasR= 0;
	}
	
	//calcula cantidad de horas con las que van a ingresarse
	$totHorasR = $CantHorasR + $hr ;
	
	//Verifica el dia y la cantidad de horas
	if (($dia == "Lunes") OR ($dia == "Martes") OR ($dia == "Miercoles") OR ($dia == "Jueves")) {
		if ($totHorasR > 9 ) {
			echo "<script>alert('No puede grabar $totHorasR en un día $dia. Se supera el límite establecido. Por favor corrija la información')</script>";
			exit();
		}
	}
	
	if ($dia == "Viernes")  {
		if ($totHorasR > 8 ) {
			echo "<script>alert('No puede grabar $totHorasR en un día $dia. Se supera el límite establecido. Por favor corrija la información')</script>";
			exit();
		}
	}

}

function diasHabiles($ts, $pProy, $pActiv, $pUni, $pClase, $pLocaliza, $pCargo ) {
/*
28Ago2007
PBM
Permite controlar que para clase de tiempo = 1 
no se grabe fechas correspondientes a sábados, domingos o festivos
A no ser que las fechas especiales del horario lo habiliten
*/

	include "validaUsrBd.php";

	//Identifica el dia de la semana
	$arreglo=explode("/",$ts);
	$mk=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);
	$dia = strftime ("%a",$mk);
	$laFecha = $arreglo[2] . "-" . $arreglo[0] . "-" . $arreglo[1];
	
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

//04Mar2008
//Para que no tenga en cuenta las fechas en la selección del horario.
//Caso específico. La señoras de servicios Generales no podían grabar horas los sábados.
//El proyecto gastos generales, no se programa por lo tanto fecha_inicial y fecha_final no coinciden con la fecha que se está registrando

//Consultar la categoría del usuario
	$claCatUsuario="";
	$csCat="Select U.id_categoria , C.nombre miCat ";
	$csCat=$csCat." from HojaDeTiempo.dbo.usuarios U, HojaDeTiempo.dbo.categorias C ";
	$csCat=$csCat." where U.id_categoria = C.id_categoria ";
	$csCat=$csCat." and U.unidad = " . $pUni ;
	$ccursCat = mssql_query($csCat);
	if ($cresCat=mssql_fetch_array($ccursCat)) {
		$claCatUsuario= $cresCat[miCat];
	}
	
	//Con esta categoría verifico más abajo si se tienen en cuenta o no las fechas en el Select
	
//cierra 04Mar2004


	//Verifica cuál es el horario de la actividad seleccionada
	$zSql0="select IDhorario from asignaciones  ";
	$zSql0=$zSql0." where id_proyecto = " . $pProy ;
	$zSql0=$zSql0." and id_actividad = " . $pActiv ;
	$zSql0=$zSql0." and unidad = " . $pUni ;
	$zSql0=$zSql0." and clase_tiempo = " . $pClase ;
	$zSql0=$zSql0." and localizacion = " . $pLocaliza ;
	$zSql0=$zSql0." and cargo = " . $pCargo ;
	
	//24Feb2009
	//Incluir la fecha para que traiga adecuadamente el horario correcto
	if (trim($claCatUsuario) != '42') {
		$zSql0=$zSql0." and month(fecha_inicial) = " . $arreglo[0] ;
		$zSql0=$zSql0." and YEAR(fecha_inicial) = " . $arreglo[2] ;
	}
	
	$cursorzSql0 = mssql_query($zSql0);
	if ($regzSql0=mssql_fetch_array($cursorzSql0)) {
		$miHorario= $regzSql0[IDhorario];
	}
	else {
		$miHorario= 0;
	}

//Trae las horas del sábado y domingo del horario seleccionado
	$zSql0="select * from horarios where IDhorario =  " . $miHorario;
	$cursorzSql0 = mssql_query($zSql0);
	if ($regzSql0=mssql_fetch_array($cursorzSql0)) {
		$horasSabado = $regzSql0[Sabado];
		$horasDomingo = $regzSql0[Domingo];
	}
	else {
		$horasSabado = 0;
		$horasDomingo = 0;
	}

	//Busca la fecha en  las fechas especiales del Horario
	//Si es Mayor que 0 puede grabar en la fecha, si no valida dia 
	//$zSql0="select count(*) esDiaEspecial from fechasEspeciales   "; //en comentario 10Jul2008 x sig línea
	$zSql0="select count(*) esDiaEspecial , coalesce(sum(CuantasHoras), 0) cuantasHoras from fechasEspeciales  ";
	$zSql0=$zSql0." where IDhorario =" . $miHorario ;
	$zSql0=$zSql0." and fecha = '" . $laFecha . "' " ;
//	$zSql0=$zSql0." and CuantasHoras > 0 " ;
//	echo $zSql0 ;
	$cursorzSql0 = mssql_query($zSql0);
	if ($regzSql0=mssql_fetch_array($cursorzSql0)) {
		$miDiaEspecial= $regzSql0[esDiaEspecial];
		
		//10Jul2008
		//Verifica el máximo valor en cantidad de horas
		$miHorasDiaEspecial=$regzSql0[cuantasHoras];
		//Cierra 10Jul2005
	}
//	echo "miDiaEspecial=".$miDiaEspecial."<br>";
	if ($miDiaEspecial == 0) {
/*		if (($dia == "Sabado") OR ($dia == "Domingo") ) {
			echo "<script>alert('No puede grabar Clase de tiempo 1 en un día $dia. Por favor corrija la información')</script>";
			exit();
		}*/
		
		if ( (($dia == "Sabado") AND ($horasSabado == 0)) OR (($dia == "Domingo") AND ($horasDomingo == 0)) ) {
			echo "<script>alert('No puede grabar Clase de tiempo 1 o 2 en un día $dia. Por favor corrija la información')</script>";
			exit();
		}


		//Encuentra si el día es festivo
		$zSql1="Select count(*) esFestivo from festivos ";
		$zSql1=$zSql1." where fecha = '" . $laFecha . "' " ;
//		echo $zSql1 . "<br>";
		$cursorzSql1 = mssql_query($zSql1);
		if ($regzSql1=mssql_fetch_array($cursorzSql1)) {
			$esDiaFestivo= $regzSql1[esFestivo];
		}
		if ($esDiaFestivo == "1") {
			echo "<script>alert('No puede grabar Clase de tiempo 1 en un día festivo. Por favor corrija la información.')</script>";
			exit();
		}
	}
	else {
		//10Jun2008
		//Sólo deja grabar si la cantidad de horas en ese día es mayor que 0
		if ($miHorasDiaEspecial == 0) {
			echo "<script>alert('No puede grabar Clase de tiempo 1 en un día festivo. El horario asignado no lo permite. Por favor corrija la información-xx')</script>";
			exit();
		}
	}

}


function tiempoExtra($ts, $pUni , $pHorario ) {
/*
31Ago2007
PBM
Valida que no se pueda ingresar tiempo extra (clase_tiempo <> 1 o 2) si no se ha 
registrado previamente tiempo normal (clase_tiempo = 1 o 2)
*/

	include "validaUsrBd.php";

	//Identifica el dia de la semana
	$arreglo=explode("/",$ts);
	$mk=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);
	$dia = strftime ("%a",$mk);
	$laFecha = $arreglo[2] . "-" . $arreglo[0] . "-" . $arreglo[1];
	
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

	//Busca la fecha en los festivos
	//Si la fecha es festivo deja ingresar el dato
	$fSqlF="Select * from HojaDeTiempo.dbo.Festivos ";
	$fSqlF=$fSqlF." where fecha = '" . $laFecha . "' ";
	$fcursorF = mssql_query($fSqlF);
	if ($fregF=mssql_fetch_array($fcursorF)) {
/*		echo "<script>alert('Es un dia festivo')</script>";*/
		return;
	}

	//Busca la fecha en  las fechas especiales del Horario
	//Si es Mayor que 0 puede grabar en la fecha, si no valida dia 
	$zSql0="select count(*) esDiaEspecial from fechasEspeciales   ";
	$zSql0=$zSql0." where IDhorario =" . $pHorario ;
	$zSql0=$zSql0." and fecha = '" . $laFecha . "' " ;
//	$zSql0=$zSql0." and CuantasHoras > 0 " ;
	
	$cursorzSql0 = mssql_query($zSql0);
	if ($regzSql0=mssql_fetch_array($cursorzSql0)) {
		$miDiaEspecial= $regzSql0[esDiaEspecial];
	}

	if ($miDiaEspecial == 0) {
		if (($dia == "Lunes") OR ($dia == "Martes")  OR ($dia == "Miercoles") OR ($dia == "Jueves") OR ($dia == "Viernes")) {
			$zSql0="select sum(horas_registradas) totalHorasR ";
			$zSql0=$zSql0." from horas " ;
			$zSql0=$zSql0." where unidad =" . $pUni;
//			$zSql0=$zSql0." and (clase_tiempo = 1 or clase_tiempo = 2) " ;
			$zSql0=$zSql0." and (clase_tiempo = 1 or clase_tiempo = 2 or clase_tiempo = 3) " ;
			$zSql0=$zSql0." and fecha = '" . $laFecha . "' " ;
			$cursorzSql0 = mssql_query($zSql0);
			if ($regzSql0=mssql_fetch_array($cursorzSql0)) {
				$miTiempoNormal= $regzSql0[totalHorasR];
			}
			else {
				$miTiempoNormal= 0;
			}
			
			if ($miTiempoNormal==0) {
				echo "<script>alert('No puede grabar tiempo extra si previamente no se ha registrado tiempo normal. Por favor corrija la información')</script>";
				exit();
			}
			else {
				/*echo "<script>alert('Sugerencia. Por favor revise que las horas del tiempo normal estén completas. ')</script>";*/
			}
		}
	}
}


function totalizarHorasDia($ts, $pUni, $pHoras) {
/*
13Sep2007
PBM
Valida que la sumatoria de horas en una fecha no supere la cantidad de horas máxima 
de los horarios en que se encuentra involucrado un usuario en un mes y año dados

Procedimiento:
1. Totalizar la cantidad de horas para ese día 
2. Buscar para ese día en todos los horarios en que participa, el valor máximo permitido
3. Verificar que el total del punto 1 más el valor recibido no supere el valor máximo de los horarios
*/

	include "validaUsrBd.php";

	//Identifica el dia de la semana
	$arreglo=explode("/",$ts);
	$mk=mktime(0,0,0,$arreglo[0],$arreglo[1],$arreglo[2]);
	$dia = strftime ("%a",$mk);
	$laFecha = $arreglo[2] . "-" . $arreglo[0] . "-" . $arreglo[1];
	
	
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

/*	echo "<script>alert('El dia de la fecha seleccionada es ".$dia."')</script>";*/

	$totalHorasDia = 0;
	//Busca la sumatoria de horas registradas en la fecha indicada
	$tSql="Select sum(horas_registradas) horasReg from horas ";
	$tSql=$tSql." where unidad = " . $pUni ;
	$tSql=$tSql." and fecha = '". $laFecha . "' " ;
	$tSql=$tSql." and (clase_tiempo = 1 or clase_tiempo = 2 or clase_tiempo = 11) " ;	
	$cursortSql = mssql_query($tSql);
	if ($regtSql=mssql_fetch_array($cursortSql)) {
		$totalHorasDia= $regtSql[horasReg];
	}
	else {
		$totalHorasDia= 0;
	}
/*	echo "<script>alert('El total de horas de la fecha seleccionada es ".$totalHorasDia."')</script>";*/
	
	$maxHorasDia=0;
	//Busca los horarios en que está involucrado el usuario para la fecha en asignaciones
	//y trae sus características
	$tSql2="select distinct y.IDhorario, z.NomHorario, z.Lunes, z.Martes, z.Miercoles,  ";
	$tSql2=$tSql2." z.Jueves, z.Viernes, z.Sabado, z.Domingo " ;
	$tSql2=$tSql2." from asignaciones a, horariosProy y, horarios z  " ;
	$tSql2=$tSql2." where a.id_proyecto = y.id_proyecto " ;
//06Feb2009 - La siguiente línea que por una extraña razón desapareció.
	$tSql2=$tSql2." and a.IDhorario = y.IDhorario  " ;
//-----
	$tSql2=$tSql2." and y.IDhorario = z.IDhorario " ;
	$tSql2=$tSql2." and a.unidad = " . $pUni ;
	$tSql2=$tSql2." and (a.fecha_inicial <= '".$laFecha."' and a.fecha_final >= '". $laFecha . "')	" ;
//La siguiente línea podría reemplazar la anterior en caso que no encuentre coincidencia en fechas en algun momento. 06Feb2009
//--and (month(a.fecha_inicial) = 1 and year(a.fecha_inicial) = 2009) 

//	echo $tSql2;
	$cursortSql2 = mssql_query($tSql2);
	while ($regtSql2=mssql_fetch_array($cursortSql2)) {
		if ( $maxHorasDia < $regtSql2[$dia] ) {
			$maxHorasDia = $regtSql2[$dia];
		}
	}
/*	echo "<script>alert('El máximo de horas permitido por los horarios es ".$maxHorasDia."')</script>";*/

	$nuevoTotal = 0;
	//Totaliza las horas registradas + la cantidad de horas que se estan registrando 
	//y las compara con el máximo permitido en los horarios
	$nuevoTotal = $totalHorasDia + $pHoras;
/*	echo "<script>alert('nuevo total de horas en el día ".$nuevoTotal."')</script>";*/

//6Feb2009
//Por instrucción telefónica de Enrique Piñeros, las categorías 53 a 62 pueden facturar más hasta 10 horas

//Consultar la categoría del usuario
	$laCatUsuario="";
	$sCat="Select U.id_categoria , C.nombre miCat ";
	$sCat=$sCat." from HojaDeTiempo.dbo.usuarios U, HojaDeTiempo.dbo.categorias C ";
	$sCat=$sCat." where U.id_categoria = C.id_categoria ";
	$sCat=$sCat." and U.unidad = " . $pUni ;
	$cursCat = mssql_query($sCat);
	if ($resCat=mssql_fetch_array($cursCat)) {
		$laCatUsuario= $resCat[miCat];
	}
//	echo $sCat;
/*echo "<script>alert('La categoria es ".$laCatUsuario."')</script>";*/
	if (($laCatUsuario >=53) AND ($laCatUsuario <=62) ) {
		if ($nuevoTotal > 10) {
			echo "<script>alert('La cantidad de horas máxima que puede registrar para este día es 10. Por favor corrija la información')</script>";
			exit;
		}
	}
	else {
		//	echo $nuevoTotal . "<br>";
		//	echo $maxHorasDia . "<br>";
	
		if ($nuevoTotal > $maxHorasDia) {
			echo "<script>alert('La cantidad de horas máxima que puede registrar para este día es ".$maxHorasDia.". Por favor corrija la información')</script>";
			exit;
		}
	}
	
//Cierra-6Feb2009
/* eSTO LO QUE HABIA ANTES DEL BLOQUE DEL 6Feb2009
	if ($nuevoTotal > $maxHorasDia) {
		echo "<script>alert('La cantidad de horas máxima que puede registrar para este día es ".$maxHorasDia.". Por favor corrija la información')</script>";
		exit;
	}
*/	
	
//exit;
	


}

?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="TituloTabla2">Ingetec S.A &copy; 2007 </td>
  </tr>
</table>
</body>
</html>
