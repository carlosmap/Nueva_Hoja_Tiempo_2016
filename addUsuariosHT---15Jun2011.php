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

	//Valida que se haya seleccionado una empresa
	if (trim($pEmpresa) == "") {
		echo "<script>alert('La empresa es obligatoria. Por favor seleccionela. ')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}

	//Valida que se haya seleccionado un tipo de documento
	if (trim($pTipoDocu) == "") {
		echo "<script>alert('El tipo de documento es obligatorio. Por favor seleccionelo. ')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}

//16Jun2010
//Verifica
$elSitioDeTrabajo="";
if (trim($tipoSitio) == "1") {
	$elSitioDeTrabajo=$lstSitioT;
}
else {
	$elSitioDeTrabajo=trim($SitioTrabajo);
	//$elSitioDeTrabajo=trim($SitioContrato);
}
//Cierra 16Jun2010

	//Inserta el usuario en la base de datos de Usuarios de la hoja de tiempo
	//dbo.Usuarios
	//unidad, nombre, apellidos, id_departamento, id_categoria, retirado, 
	//administrador, email, ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, 
	//SitioTrabajo, TipoContrato, Seccion, NombreCorto, unidadJefe, fechaIngreso, fechaRetiro, idEmpresa, lFechaNacimiento
	//codTipoDoc, numDocumento, sexo
	$insertaUsr = "INSERT INTO usuarios (unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, ";
	$insertaUsr = $insertaUsr. " email, ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, ";
//	$insertaUsr = $insertaUsr. " Seccion, NombreCorto, unidadJefe, fechaIngreso, idEmpresa, fechaNacimiento )";
	$insertaUsr = $insertaUsr. " Seccion, NombreCorto, unidadJefe, fechaIngreso, idEmpresa, fechaNacimiento, codTipoDoc, numDocumento, sexo )";
	$insertaUsr = $insertaUsr. " values($Unidad, '$Nombres', '$Apellidos', $Departamento, $Categoria, NULL, NULL, '$Email', NULL, " ;
//	$insertaUsr = $insertaUsr. " NULL, NULL, '$SitioContrato', '$SitioTrabajo', '$TipoContrato', '$Seccion', '$NombreCorto', $pJefe ) ";
//	$insertaUsr = $insertaUsr. " NULL, NULL, '$SitioContrato', '$SitioTrabajo', '$TipoContrato', " ; 
//	$insertaUsr = $insertaUsr. " NULL, NULL, '$elSitioDeTrabajo', '$SitioTrabajo', '$TipoContrato', " ; 
	$insertaUsr = $insertaUsr. " NULL, NULL, '$SitioContrato', '$elSitioDeTrabajo', '$TipoContrato', " ; 
//	$insertaUsr = $insertaUsr. " '$Seccion', '$NombreCorto', $pJefe, '$lFechaInicio', $pEmpresa, '$lFechaNacimiento' ) ";
	$insertaUsr = $insertaUsr. " '$Seccion', '$NombreCorto', $pJefe, '$lFechaInicio', $pEmpresa, '$lFechaNacimiento',   ";
	$insertaUsr = $insertaUsr. " $pTipoDocu, '$Documento', '$lSexo' ) ";
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
   
   //31May2010
   	//Realiza la grabación UsuariosProyectos para el proyecto de GestionCalidad = 646
	/*	id_proyecto, unidad	, verCorreo, verMail */	
	$queryCal = "INSERT INTO GestiondeInformacionDigital.dbo.UsuariosProyectos(id_proyecto, unidad, verCorreo, verMail)" ;
	$queryCal = $queryCal . " VALUES (646 , ";	
	$queryCal = $queryCal . $Unidad . ", " ;
	$queryCal = $queryCal . " '0', " ;	
	$queryCal = $queryCal . " '0' " ;	
	$queryCal = $queryCal . " ) ";
	$cursorCal = mssql_query($queryCal) ;

   //Cierra 31May2010
	
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

//02Nov2010
//PBM - Implementación para PageDevice
//-----Grabar en la tabla pc_user el usuario que acaba de ingresar
//-----Asociar el usuario automáticamente al proyecto Gastos Generales en caso que pertenezca a Administrativa

	$connMySql = conectarMySql();
	//Grabación del usuario en pc_user
	$ySql00="INSERT INTO pc_user (user_name, descrip, bil_codes) ";
	$ySql00=$ySql00 . " VALUES (";
	$ySql00=$ySql00 . " '" . $Email . "', ";
	$ySql00=$ySql00 . " '" . $Unidad . "', " ;
	$ySql00=$ySql00 . " 1 ";
	$ySql00=$ySql00 . " ) ";
	$yCursor00 = mysql_query($ySql00);
//echo " ySql00 =" . $ySql00 . "<br>" ;
	
	$ceIdDiv='';
	//Información Departamento
	$ceSql02="Select id_division from HojaDeTiempo.dbo.Departamentos " ;
	$ceSql02=$ceSql02." where id_departamento = " . $Departamento ;
	$ceCursor02 = mssql_query($ceSql02);
	if ($ceReg02 = mssql_fetch_array($ceCursor02)) {	
		$ceIdDiv = $ceReg02[id_division];
	}
//	echo " ceSql02 =" . $ceSql02 . "<br>" ;
	
	//Si el usuario pertenece a la División Administrativa, automáticamente lo asocia al proyecto Gastos Generales
	//id_division = 11 = Administrativa
	//id_division = 46 = [División GERADM]
	//16Feb2011
	//Se activo la autorización de Gastos Generales para todo el mundo
	//if (($ceIdDiv == 11) OR ($ceIdDiv == 46)) {
		//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
		$yIdUsuarioUltimo='';
		$ySql02 = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
		$yCursor02 = mysql_query($ySql02);
		if($yReg02 = mysql_fetch_array($yCursor02)){
			$yIdUsuarioUltimo = $yReg02['elIdUsuario'];
		}
//		echo " ySql02 =" . $ySql02 . "<br>" ;
		
		//Encuentra el id del proyecto Gastos Generales
		$yIdProyMySql='';
		$ySql03 = " SELECT * FROM pc_bilcode ";
		$ySql03 = $ySql03 . " WHERE ident = '42' "; //42=Gastos Generales
		$yCursor03 = mysql_query($ySql03);
		if($yReg03 = mysql_fetch_array($yCursor03)){
			$yIdProyMySql = $yReg03['bil_id'];
		}
//		echo " ySql03 =" . $ySql03 . "<br>" ;

		/*****************
		MySql:
		Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
		*****************/
		$ySql04 = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
		$ySql04 = $ySql04 . " VALUES ( ";
		$ySql04 = $ySql04 . " " . $yIdUsuarioUltimo . ", ";
		$ySql04 = $ySql04 . " " . $yIdProyMySql . " ";
		$ySql04 = $ySql04 . " ) ";   
		$yCursor04 = mysql_query($ySql04);
//		echo " ySql04 =" . $ySql04 . "<br>" ;
	//}

//-----

//	if ( (trim($cursorIns) != "") AND (trim($cursorPerfil) != "") AND (trim($cursorSalario) != "") )  {
	if ( (trim($cursorIns) != "") AND (trim($cursorPerfil) != "") AND (trim($cursorSalario) != "") AND (trim($cursorCal) != "") )  {
		echo ("<script>alert('La Grabación se realizó con éxito. ');</script>"); 

//************ANTIGUO ENVIO DE MAIL*************
/*		
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
		Tipo=usuario
		SitioTrabajo=$elSitioDeTrabajo
		No responda este correo, se ha enviado automaticamente por motivo de novedades de personal.
		
		Administrador de la hoja de tiempo";
		mail($cualMailProy,$AsuntoProy,$DescripcionProy,"FROM: Sistema Portal Usr <portalingetec@ingetec.com.co>\n"); 
*/
		
		//********ENVIA CORREO CON NUEVA FUNCION **************
		
		//29Oct2010
		//PBM
		//Enviar los campos Categoría, Descripción de la categoría, División y Departamento en el correo para que Guillermo Bazurto  
		//pueda implementar un script para telmex
		
		//Información Categorías
		$ceSql01="Select id_categoria, upper(nombre) nombre, upper(descripcion) descripcion from HojaDeTiempo.dbo.Categorias " ;
		$ceSql01=$ceSql01." where id_categoria = " . $Categoria ;
		$ceCursor01 = mssql_query($ceSql01);
		if ($ceReg01 = mssql_fetch_array($ceCursor01)) {	
			$ceIDcategoria =$ceReg01[nombre] ;
			$ceNomCategoria = $ceReg01[descripcion];
		}
		

		//Información Departamento
		
		
		$ceSql02="Select 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace(
				upper(ltrim(rtrim(nombre)) )
				, 'Á', 'A') 
				, 'É', 'E') 
				, 'Í', 'I') 
				, 'Ó', 'O') 
				, 'Ú', 'U') 
				, 'Ñ', 'N') 
				, ',', ' ') 
				, '-', '_') 
				, ' ', '_') 
				, '[DPTO_', '') 
				, '[DEPARTAMENTO_', '') 
				, ']', '') 
				, '.', '') 
				as nomDepto
				, id_division from HojaDeTiempo.dbo.Departamentos ";
		$ceSql02=$ceSql02." where id_departamento = " . $Departamento ;
		$ceCursor02 = mssql_query($ceSql02);
		if ($ceReg02 = mssql_fetch_array($ceCursor02)) {	
			$ceNomDpto = $ceReg02[nomDepto];
			$ceIdDiv = $ceReg02[id_division];
		}
		
		//Información División
		$ceSql03="Select 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace( 
				replace(
				upper(ltrim(rtrim(nombre)) )
				, 'Á', 'A') 
				, 'É', 'E') 
				, 'Í', 'I') 
				, 'Ó', 'O') 
				, 'Ú', 'U') 
				, 'Ñ', 'N') 
				, ',', '') 
				, '-', '_') 
				, ' ', '_') 
				, '[DIVISION_', '') 
				, ']', '') 
						as nomDivision 
				from HojaDeTiempo.dbo.Divisiones ";
		$ceSql03=$ceSql03." where id_division = " . $ceIdDiv  ;
		$ceCursor03 = mssql_query($ceSql03);
		if ($ceReg03 = mssql_fetch_array($ceCursor03)) {	
			$ceNomDivision = $ceReg03[nomDivision];
		}

		//Fin 29Oct2010
		
		include("fncEnviaMailPEAR.php");
		$pPara= "mhtiempo@ingetec.com.co";
		$pAsunto= "Novedades de personal ";
		$pTema = "Proceso=Agregar
		Unidad=$Unidad
		Nombres=$Nombres
		Apellidos=$Apellidos
		Email=$Email
		Tipo=usuario
		SitioTrabajo=$elSitioDeTrabajo
		IDCategoria=$ceIDcategoria
		NomCategoria=$ceNomCategoria
		Division=$ceNomDivision
		Departamento=$ceNomDpto
		
		No responda este correo, se ha enviado automaticamente por motivo de novedades de personal.";
		$pFirma = "Administrador de la hoja de tiempo";
		
		//enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		
		//21Oct2010
		//Quitar el HTML para la creación del correo
		enviarCorreoSinHtml($pPara, $pAsunto, $pTema, $pFirma);
			
		//********FIN DE ENVIO DE MAIL**************************
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
	<script language="JavaScript" src="calendar.js"></script>
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


function verificaOpcion(){ 
	//alert (document.addUsr.reportado[1].checked);
	if (document.addUsr.tipoSitio[1].checked) {
		document.addUsr.lstSitioT.disabled = true ;
		document.addUsr.SitioTrabajo.disabled = false ;
	}
	else {
		document.addUsr.lstSitioT.disabled = false ;
		document.addUsr.SitioTrabajo.disabled = true ;	
	}
}



//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">

  <form action="addUsuariosHT.php" method="post" name = "addUsr" onSubmit="MM_validateForm('Unidad','','RisNum','Nombres','','R','Apellidos','','R','SitioContrato','','R','SitioTrabajo','','R','Email','','R','NombreCorto','','R','lFechaInicio','','R','Salario','','RisNum','lFechaNacimiento','','R','Documento','','RisNum');return document.MM_returnValue">
  
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
    <td class="TituloTabla">[Divisi&oacute;n] - Departamento</td>
    <td class="TxtTabla"><select name="Departamento" class="CajaTexto" id="Departamento">
     <?php
//	  	$sqlDpto = "select * from departamentos ";
		$sqlDpto="select A.* , D.nombre nomDiv ";
		$sqlDpto=$sqlDpto." from departamentos A, divisiones D ";
		$sqlDpto=$sqlDpto." Where A.id_division = D.id_division ";
		$sqlDpto=$sqlDpto." AND A.estadoDpto = 'A' ";
		$sqlDpto=$sqlDpto." order by D.nombre, A.nombre  ";
		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			echo "<option value =". $reg[id_departamento]. ">". "[" . ucwords(strtolower($reg[nomDiv])) . "] - " . ucwords(strtolower($reg[nombre])) ;
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Secci&oacute;n</td>
    <td class="TxtTabla"><select name="Seccion" class="CajaTexto" id="Seccion">
	<?php
	  	$sqlDpto = "select distinct seccion from personalACTUALIZADO ";
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
    <td class="TxtTabla">
	<input name="tipoSitio" type="radio" value="1" checked onClick="verificaOpcion()">
	Lista 
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="tipoSitio" type="radio" value="2" onClick="verificaOpcion()" > 
	Otro	<br>
	<? 
	//16Jun2010
	//Registrar los sitios de trabajo 
	//
	$sqlST="SELECT distinct SitioTrabajo FROM  HojaDeTiempo.dbo.Usuarios ";
	$sqlST=$sqlST." WHERE RETIRADO IS NULL ";
	$sqlST=$sqlST." ORDER BY SitioTrabajo ";
	$CursorST = mssql_query($sqlST);

	?>
    <select name="lstSitioT" class="CajaTexto" id="lstSitioT" >
	<? while ($regST = mssql_fetch_array($CursorST)){ ?>
	 <option value="<? echo $regST[SitioTrabajo]; ?>"><? echo $regST[SitioTrabajo]; ?></option>
	<? } ?>  
    </select>	
    <input name="SitioTrabajo" type="text" disabled class="CajaTexto" id="SitioTrabajo" value=" " size="40"></td>
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
	<option value = "CT">Cuarto de Tiempo
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombre corto </td>
    <td class="TxtTabla"><input name="NombreCorto" type="text" class="CajaTexto" id="NombreCorto" size="40"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha de ingreso </td>
    <td class="TxtTabla">
	<input name="lFechaInicio" class="CajaTexto"  value="<? echo $lFechaInicio;?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.addUsr.lFechaInicio);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
	</td>
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
    <td class="TituloTabla">Empresa</td>
    <td class="TxtTabla">
	<select name="pEmpresa" class="CajaTexto" id="pEmpresa" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Trae la información de la lista empresas
		$sqlE="select * from Empresas " ;
		$cursorE = mssql_query($sqlE);
		while ($regE=mssql_fetch_array($cursorE)) {
		?>
            <option value="<? echo $regE[idEmpresa]; ?>" ><? echo ucwords(strtolower($regE[nombre])) ;  ?></option>
            <? } ?>
          </select>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha de nacimiento (mm/dd/yyyy) </td>
    <td align="left" class="TxtTabla">
	<input name="lFechaNacimiento" type="text" class="CajaTexto" value="<? echo $lFechaNacimiento; ?>" size="15"  >
&nbsp;<a href="javascript:cal.popup();"><img src="imagenes/cal.gif" alt="Calendario" width="16" height="16" border="0" ></a>
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Documento (Tipo - N&uacute;mero) </td>
    <td class="TxtTabla">
	<select name="pTipoDocu" class="CajaTexto" id="pTipoDocu" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Trae la información de la lista Tipo documento
		$sqlTD="select * from TipoDocumento " ;
		$cursorTD = mssql_query($sqlTD);
		while ($regTD=mssql_fetch_array($cursorTD)) {
		?>
            <option value="<? echo $regTD[codTipoDoc]; ?>" ><? echo $regTD[tipoDoc] ;  ?></option>
            <? } ?>
          </select> 
	- 
	<input name="Documento" type="text" class="CajaTexto" id="Documento" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" >
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Sexo</td>
    <td class="TxtTabla"><input name="lSexo" type="radio" value="F">
      Femenino&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="lSexo" type="radio" value="M" checked>
        Masculino</td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td align="right" class="TxtTabla"><input  type="submit" class="Boton" id="Grabar" value="Grabar" ></td>
  </tr>
</table></td>
  </tr>
</table>
</form>

<script language="JavaScript">
		 var cal = new calendar2(document.forms['addUsr'].elements['lFechaNacimiento']);
		 cal.year_scroll = true;
		 cal.time_comp = false;
</script>

</body>
</html>