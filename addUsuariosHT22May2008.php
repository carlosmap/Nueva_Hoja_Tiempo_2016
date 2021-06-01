<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//Actualiza un usuario de la lista de usuarios de INGETEC S.A.
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


if($Unidad != "") {

//Realiza las validaciones de rigor
	//Valida que la unidad no exista en la base de datos
	$sqlUni = "select * from usuarios where unidad = ". $Unidad ;
	$cursorUnidad = mssql_query($sqlUni) ;
	if(mssql_num_rows($cursorUnidad) > 0) {
		echo "<script>alert('La unidad digitada existe en la base de datos. por favor escríbala de nuevo')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}


	//valida que le email no tenga la letra @
	if(ereg("[@]", $Email)) {
		echo "<script>alert('Por favor corrija el email, tiene el caracter @. Por favor no ingrese el dominio')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}

	//Valida la cuenta de mail que no exista en la tabla usuarios
	$sqlMail = "select * from usuarios where email = ". "'". $Email. "'" ;
	$sqlMail = $sqlMail. " and retirado IS NULL";
	$cursorMail = mssql_query($sqlMail) ;
	if(mssql_num_rows($cursorMail) > 0) {
		echo "<script>alert('El Email digitado ya existe en la base de datos. por favor escriba uno nuevo')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}

	//Valida que se haya seleccionado un jefe
	if (trim($pJefe) == "") {
		echo "<script>alert('El jefe inmediato es obligatorio. Por favor seleccionelo. ')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}

	//Inserta el usuario en la base de datos de Usuarios de la hoja de tiempo
	//dbo.Usuarios
	//unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, email, ContadorFallas, 
	//FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, Seccion, NombreCorto, unidadJefe
	$insertaUsr = "INSERT INTO usuarios (unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, ";
	$insertaUsr = $insertaUsr. " email, ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, ";
	$insertaUsr = $insertaUsr. " Seccion, NombreCorto, unidadJefe )";
	$insertaUsr = $insertaUsr. " values($Unidad, '$Nombres', '$Apellidos', $Departamento, $Categoria, NULL, NULL, '$Email', NULL, " ;
	$insertaUsr = $insertaUsr. " NULL, NULL, '$SitioContrato', '$SitioTrabajo', '$TipoContrato', '$Seccion', '$NombreCorto', $pJefe ) ";
	$cursorIns = mssql_query($insertaUsr) ;
	
	//agrega el usuario a la base de datos gestion de informacion digital. tabla perfil usuarios, con perfil normal
	//BD-->GestiondeInformacionDigital
	//PerfilUsuarios
	//codPerfil, unidad
   $sql = "INSERT INTO GestiondeInformacionDigital.dbo.PerfilUsuarios (codPerfil, unidad) VALUES (3, " .  $Unidad . ")";
   $cursorPerfil = mssql_query($sql);

	//Inserta el salario del usuario en
	//BD-->HojaDeTiempo
	//dbo.UsuariosSalario
	//unidad, fecha, salario
   $sqlSalarioUsu="INSERT INTO HojaDeTiempo.dbo.UsuariosSalario (unidad, fecha, salario) ";
   $sqlSalarioUsu=$sqlSalarioUsu." VALUES (". $Unidad . ", '" . gmdate ("n/d/Y") . "', " .  $Salario . " )" ; 
   $cursorSalario = mssql_query($sqlSalarioUsu);
   
	
	function AgregarUsuarioIlimitadoActividad ($IdProy, $IdActiv, $Unidad, $clase){
		//Ejecuta un procedimiento almacenado en la base de datos de SQL sever para agregar un usuario ilimitado en proyectos
		//especiales
		$sql = "EXEC spAgregarUsuarioIlimitado " ;
		$sql = $sql. "'". $IdProy . "'". "," . "'". $IdActiv . "'". ",". "'". $Unidad . "'". ",". "'". $clase . "'" ;
		mssql_query($sql);
	}

	//Si todo está bien, Agrega el usuario a proyectos especiales
	//No se dejó la validación de unidad < UNIDAD_MAXIMA, porque según Gonzalo todos deben ir a proyectos especiales.
	$SQLproy = "select id_proyecto,maxclase from proyectos where especial is not null and maxclase is not null";
	$cursor1 = mssql_query($SQLproy);
	while ($reg = mssql_fetch_array($cursor1)) {	
		  $IdProy = $reg[id_proyecto];
		  $MaxClase = $reg[maxclase] ;
	
		  $SQLActiv = "select id_actividad from actividades where id_proyecto=" . $IdProy ;
		  $cursor2 = mssql_query($SQLActiv);
	
		  while ($reg2 = mssql_fetch_array($cursor2)) {
			 for($clase = 1 ; $clase<=$MaxClase; $clase++) {
				AgregarUsuarioIlimitadoActividad ($IdProy, $reg2[id_actividad], $Unidad, $clase) ;
			 }
		  }
	}

	if ( (trim($cursorIns) != "") AND (trim($cursorPerfil) != "") AND (trim($cursorSalario) != "") )  {
		echo ("<script>alert('La Grabación se realizó con éxito. ');</script>"); 
		
		//si graba todo bien envia la creación del Mail a sistemas
	   	//envia un mail a la cuenta mhtiempo@ingetec.com.co para crear el login y el correo
		//inicio envio mail
			$cualMailProy="mhtiempo@ingetec.com.co";
			//$cualMailProy="pbaron@ingetec.com.co";
			$AsuntoProy="Novedades de personal";
			$DescripcionProy="Proceso=Agregar
Unidad=$Unidad
Nombres=$Nombres
Apellidos=$Apellidos
Email=$Email
No responda este correo, se ha enviado automaticamente por motivo de novedades de personal.
			
Administrador de la hoja de tiempo";
			mail($cualMailProy,$AsuntoProy,$DescripcionProy,"FROM: Sistema Portal Usr <portalingetec@ingetec.com.co>\n"); 
		//Fin envio mail			
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
		//Aqui debe ir un procedimiento de rollback 
	};
	echo ("<script>window.close();MM_openBrWindow('UsuariosHT.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}
?>


<html>
<head>
<title>Ingreso de Usuarios Nuevos</title>
    <LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" src="ts_picker.js"></script>
    <script language="JavaScript" type="text/JavaScript">
<!--
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' debe numérico.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
    } if (errors) alert('Validación:\n'+errors);
    document.MM_returnValue = (errors == '');
} }

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">

  <form action="addUsuariosHT.php" method="post" name = "addUsr" onSubmit="MM_validateForm('Unidad','','RisNum','Nombres','','R','Apellidos','','R','SitioContrato','','R','SitioTrabajo','','R','Email','','R','NombreCorto','','R','Salario','','RisNum');return document.MM_returnValue">
  
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Ingreso de Usuarios Nuevos INGETEC S.A.</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="32%" class="TituloTabla">Unidad</td>
    <td width="68%" class="TxtTabla"><label>
      <input name="Unidad" type="text" class="CajaTexto" id="Unidad">
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombres</td>
    <td class="TxtTabla"><label>
      <input name="Nombres" type="text" class="CajaTexto" id="Nombres" size="40">
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Apellidos</td>
    <td class="TxtTabla"><label>
      <input name="Apellidos" type="text" class="CajaTexto" id="Apellidos" size="40">
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><label>
      <select name="Categoria" class="CajaTexto" id="Categoria">
      <?php
	  	$sqlCat = "select * from categorias ";
		$catCursor = mssql_query($sqlCat);
		while ($reg = mssql_fetch_array($catCursor)){
			echo "<option value =". $reg[id_categoria]. ">". $reg[nombre] ;
		}
	  ?>
      </select>
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Departamento</td>
    <td class="TxtTabla"><select name="Departamento" class="CajaTexto" id="Departamento">
     <?php
	  	$sqlDpto = "select * from departamentos ";
		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			echo "<option value =". $reg[id_departamento]. ">". ucwords(strtolower($reg[nombre])) ;
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Secci&oacute;n</td>
    <td class="TxtTabla"><select name="Seccion" class="CajaTexto" id="Seccion">
	<?php
	  	$sqlDpto = "select distinct seccion from personalACTUALIZADO";
		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			echo "<option>". ucwords(strtolower($reg[seccion])) ;
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Sitio Contrato</td>
    <td class="TxtTabla"><input name="SitioContrato" type="text" class="CajaTexto" id="SitioContrato" size="40"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Sitio Trabajo</td>
    <td class="TxtTabla"><input name="SitioTrabajo" type="text" class="CajaTexto" id="SitioTrabajo" size="40"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Email sin dominio </td>
    <td class="TxtTabla"><input name="Email" type="text" class="CajaTexto" id="Email"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Tipo de contrato</td>
    <td class="TxtTabla"><select name="TipoContrato" class="CajaTexto" id="TipoContrato">
    <option value = "TC">Tiempo Completo
    <option value = "MT">Medio Tiempo
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombre corto </td>
    <td class="TxtTabla"><input name="NombreCorto" type="text" class="CajaTexto" id="NombreCorto" size="40"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Jefe inmediato </td>
    <td class="TxtTabla">
	<select name="pJefe" class="CajaTexto" id="pJefe" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." order by U.apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Salario</td>
    <td class="TxtTabla"><input name="Salario" type="text" class="CajaTexto" id="Salario"></td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td align="right" class="TxtTabla"><label>
      <input  type="submit" class="Boton" id="Grabar" value="Grabar" >
    </label></td>
  </tr>
</table></td>
  </tr>
</table>
</form>

</body>
</html>