<?php
	session_id(rand());
	session_start();
	session_register('id_proyecto');
	session_register('id_division');
	session_register('fi');
	session_register('ff');
	session_register('login');
	session_register('clave');
	session_register('usrBD');
	session_register('nombreempleado');
	session_register('apellidoempleado');
	session_register('laUnidad');
	session_register("dirDiv");
	session_register("dirDep");
	session_register("dirProy");
	session_register("pac");
	session_register('MiUnidad');
	session_register('esRevisor');
	session_register('fiAprobacion');
	session_register('ffAprobacion');
	session_register('elQueAutoriza');//Se usa para grabar la unidad de la persona que autorizó a otra a revisar la hdet
	session_register('perfilQueAutoriza');


	//$_SESSION["laUnidad"];
	include "funciones.php";
	//header("Cache-Control: no-cache, must-revalidate");
	//header("Pragma: no-cache");
?>

<HTML>
<HEAD>
<TITLE>
Inicio de session Control de tiempo
</TITLE>
<script>

function validar(){
		var01=document.form2.usuarios.value.length;
		var02=document.form2.password.value.length;
		var03=document.form2.unidVal.value.length;

		estadocampos=var01*var02*var03;

		if(estadocampos){

			document.forminiov.submit();

		}else
				rpta=window.confirm("Error.\nTodos los campos deben tener datos")
					if(rpta){
					rpta=!rpta;
					return rpta;
		}
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="GENERATOR" content="Actual Drawing 5.5 (http://www.ingetec.com.co) [CALIVEA]">

</HEAD>

<BODY bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#800080" alink="#FF0000">
<?php
       $cad=$HTTP_USER_AGENT;
       //alert($cad);
	$esIE=strstr($cad," MSIE");
	if ($esIE!=false) {
		// El navegador es Internet Explorer
		$pos=strpos($esIE,".");
		$ver=substr($esIE,$pos-1,1);
		if ($ver<4) {
				echo "Su explorador es muy antiguo. Debe actualizar a la version 4 o superior";
				exit;
		}
	} else {
		// El navegador no es Internet Explorer
		// puede tener fallas (REVISAR)
		$pos=strpos($cad,".");
		$ver=substr($cad,$pos-1,1);
		if ($ver<4) {
				echo "Su explorador es muy antiguo. Debe actualizar a la version 4 o superior";
				exit;
		}
	}
?>
<div id="Layer6" style="position:absolute; left:223px; top:145px; width:201px; height:19px; z-index:9">
<img src="picsI/Image17900109.gif" width="201" height="19" border="0" name="Image_Layer6"></div>
<div id="Layer5" style="position:absolute; left:13px; top:400px; width:641px; height:64px; z-index:8">
<FONT size=1 color=#000000 face="Arial">&nbsp;</FONT><HR size=2><div align="CENTER"><FONT size=1 color=#000000 face="Arial">Copyright© 2006, Ingetec S.A. All rights reserved.</FONT></div>
<div align="CENTER"><A href="http://www.ingetec.com.co/"><FONT size=1 color=#008000 face="Arial">www.ingetec.com.co</FONT></A></div>
</div>
<div id="Layer4" style="position:absolute; left:187px; top:204px; width:322px; height:151px; background-color:#FFFFFF; z-index:7">
<?php echo "<font face=arial size=2 color=red>El usuario y password corresponden al usado en su cuenta de correo</font>"; ?>
<FORM action=Inicio.php method=post name="form2">
	<TABLE width=290 bgcolor=#FFFFFF cellpadding=1 bordercolorlight=#FFFFFF bordercolordark=#000000 cellspacing=2>
	<TR>
	<TD width="96" valign="top">
	<FONT color=#0000FF><B>Usuario</B></FONT></TD><TD width="184" valign="top">
	<INPUT type= text name="usuarios">
	</TD></TR>
	<TR>
	<TD width="96" valign="top">
	<FONT color=#0000FF><B>Password</B></FONT></TD><TD width="184" valign="top">
	<INPUT name="password" type=password>
	</TD></TR>
	<TR>

	<TR>


	<TD width="96" valign="top">
	 </TD><TD width="184" valign="top">
	</TD></TR>
	<TR>
	<TD width="96" valign="top">
	<INPUT name=Enviar type=submit value=Aceptar onclick= "return validar()";>
	</TD><TD width="184" valign="top">
	</TD></TR>

	<!--<tr><td><h1><font face="Arial, Helvetica, sans-serif"><strong><font color="#FF0000">EN MANTENIMIENTO</font></strong></font></h1></td></tr>-->
	</TABLE>
	</div>

</form>


<div id="Layer1" style="position:absolute; left:188px; top:113px; width:336px; height:33px; z-index:6">
<img src="picsI/Image85999750.gif" width="336" height="33" border="0" name="Image_Layer1"></div>
<div id="Image20783687" style="position:absolute; left:19px; top:9px; width:154px; height:43px; z-index:5">
<img src="picsI/Image20783687.gif" width="154" height="43" border="0" name="Image_Image20783687"></div>

<div id="Layer3" style="position:absolute; left:-39px; top:60px; width:687px; height:22px; z-index:3">
<img src="picsI/GreenRoundedImage3_0.gif" width="687" height="22" border="0" name="Image_Layer3"></div>
<div id="Layer2" style="position:absolute; left:648px; top:60px; width:81px; height:36px; z-index:2">
<img src="picsI/GreenRoundedImage2_0.gif" width="81" height="36" border="0" name="Image_Layer2"></div>
<div id="Layer12" style="position:absolute; left:404px; top:-2px; width:295px; height:62px; z-index:1">
<img src="picsI/GreenRoundedImage12_0.gif" width="295" height="62" border="0" name="Image_Layer12"></div>
</BODY>
</HTML>

<?php
if($Enviar=="Aceptar"){


$ldaphost = "192.168.1.5";
$ldapport = 389;
$login = $usuarios;
$clave = $password;

$ds = ldap_connect($ldaphost, $ldapport) or die("No se pudo conectar al Servidor $ldaphost");
if ($ds) {

	$binddn = "uid=$login,ou=Users,ou=OxObjects,dc=ingetec,dc=com,dc=co";
	$ldapbind = ldap_bind($ds, $binddn, $clave);
	if ($ldapbind) {
		//print "! $uusuario autenticado.";
		$sr=ldap_search($ds, "ou=Users,ou=OxObjects,dc=ingetec,dc=com,dc=co","uid=$login");
		$entry = ldap_first_entry($ds, $sr);
		$values = ldap_get_values($ds, $entry, "oxuserposition");
        $tipousuario=$values[0];

        $sql="select * from usuarios where email='$login' order by unidad desc";

		include "validaUsrBd.php";

		$ap = mssql_query($sql);

		$numReg = mssql_num_rows($ap);

		if($numReg > 0){
			$reg = mssql_fetch_array($ap);
			$laUnidad = $reg[unidad];
			//$login=$undUsr;
		}else{
			//$sql="update usuarios set email = '$login' where unidad=$unidVal";
			//$ap = mssql_query($sql);
			echo "<script>alert('Usuario registrado en la base de datos del sistema');</script>";
			echo "<meta http-equiv='refresh' content='0;url=Inicio.php'>";
		}

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

			echo "<script>location.href=\"frm-GrabaTiempo3.php?laUnidad=$laUnidad\"</script>";
		}else {
			//echo "<script>alert('Ofrecemos disculpas. Hoja de tiempo en mantenimiento');</script>";
			echo "<script>alert('No es posible autenticar, favor revisar usuario y clave !');</script>";
			echo "<SCRIPT LANGUAGE=\"JavaScript\">";
			echo "location.href=\"Inicio.php\"";
			echo "</SCRIPT>";
		}
	}
}


?>