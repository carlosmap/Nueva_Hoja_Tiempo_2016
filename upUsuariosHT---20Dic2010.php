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

//Trae la información del usuario seleccionado
//unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, email, 
//ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, Seccion, 
//NombreCorto, unidadJefe
$sql="select * from usuarios ";
$sql=$sql." where unidad =" . $cualUnidad ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$punidad = $reg[unidad];
	$pnombre = $reg[nombre];
	$papellidos = $reg[apellidos];
	$pid_departamento = $reg[id_departamento];
	$pid_categoria = $reg[id_categoria];
	$pemail = $reg[email];
	$pSitioContrato = $reg[SitioContrato];
	$pSitioTrabajo = $reg[SitioTrabajo];
	$pTipoContrato = $reg[TipoContrato];
	$pSeccion = $reg[Seccion];
	$pNombreCorto = $reg[NombreCorto];
	$punidadJefe = $reg[unidadJefe];
	$pFechaInicio = date("m/d/Y", strtotime($reg[fechaIngreso])) ;
	$pidEmpresa = $reg[idEmpresa]; 
	$pFechaNacimiento = date("m/d/Y", strtotime($reg[fechaNacimiento])) ;
	
	$pcodTipoDoc = $reg[codTipoDoc]; 
	$pnumDocumento = $reg[numDocumento]; 
	$psexo = $reg[sexo]; 
	
}

if($Unidad != "") {

//Realiza las validaciones de rigor
	//Valida que se haya seleccionado un jefe
	if (trim($pJefe) == "") {
		echo "<script>alert('El jefe inmediato es obligatorio. Por favor escriba seleccionelo. ')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}
	
//17Jun2010
//Verifica
$elSitioDeTrabajo="";
if (trim($tipoSitio) == "1") {
	$elSitioDeTrabajo=$lstSitioT;
}
else {
	$elSitioDeTrabajo=trim($SitioTrabajo);
}
//Cierra 17Jun2010
	

	//Actualiza el usuario en la base de datos de Usuarios de la hoja de tiempo
	//dbo.Usuarios
	//unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, email, ContadorFallas, 
	//FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, Seccion, NombreCorto, unidadJefe
	$insertaUsr = "UPDATE usuarios SET ";
	$insertaUsr = $insertaUsr. " nombre = '" . $Nombres . "', ";
	$insertaUsr = $insertaUsr. " apellidos = '" . $Apellidos . "', ";
	$insertaUsr = $insertaUsr. " id_categoria = " . $Categoria . ", ";
	$insertaUsr = $insertaUsr. " id_departamento = " . $Departamento . ", ";
	$insertaUsr = $insertaUsr. " Seccion = '" . $Seccion . "', ";
	$insertaUsr = $insertaUsr. " SitioContrato = '" . $SitioContrato . "',  ";
//	$insertaUsr = $insertaUsr. " SitioTrabajo = '" . $SitioTrabajo. "', ";
	$insertaUsr = $insertaUsr. " SitioTrabajo = '" . $elSitioDeTrabajo. "', ";
	$insertaUsr = $insertaUsr. " TipoContrato = '" . $TipoContrato . "', " ;
	$insertaUsr = $insertaUsr. " NombreCorto = '" . $NombreCorto . "', ";
	$insertaUsr = $insertaUsr. " fechaIngreso = '" . $lFechaInicio . "', ";
	$insertaUsr = $insertaUsr. " idEmpresa = " . $pEmpresa . ", ";
	$insertaUsr = $insertaUsr. " unidadJefe = " . $pJefe . ", " ;
	//$insertaUsr = $insertaUsr. " fechaNacimiento = '" . $lFechaNacimiento . "' ";
	$insertaUsr = $insertaUsr. " fechaNacimiento = '" . $lFechaNacimiento . "', ";
	$insertaUsr = $insertaUsr. " codTipoDoc = '" . $pTipoDocu . "', ";
	$insertaUsr = $insertaUsr. " numDocumento = '" . $Documento . "', ";
	$insertaUsr = $insertaUsr. " sexo = '" . $lSexo . "' ";
	$insertaUsr = $insertaUsr. " WHERE unidad = " . $Unidad ;
	$cursorIns = mssql_query($insertaUsr) ;
	if (trim($cursorIns) != "")    {
		echo ("<script>alert('La Grabación se realizó con éxito. ');</script>"); 
		
		//********ENVIA CORREO CON NUEVA FUNCION **************
		
		//18Nov2010
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
		$pAsunto= "Novedades de personal - ACTUALIZACIÓN ";
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
	}
	echo ("<script>window.close();MM_openBrWindow('UsuariosHT.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	

}

?>


<html>
<head>
<title>Actualizar Usuario</title>
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

  <form action="" method="post" name = "addUsr" onSubmit="MM_validateForm('Unidad','','RisNum','Nombres','','R','Apellidos','','R','SitioContrato','','R','SitioTrabajo','','R','Email','','R','NombreCorto','','R','lFechaNacimiento','','R');return document.MM_returnValue">
  
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario"> Usuarios Nuevos INGETEC S.A.</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="32%" class="TituloTabla">Unidad</td>
    <td width="68%" class="TxtTabla">
      <input name="Unidad" type="text" class="CajaTexto" id="Unidad" value="<? echo $punidad; ?>" readonly>
    </td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombres</td>
    <td class="TxtTabla"><label>
      <input name="Nombres" type="text" class="CajaTexto" id="Nombres" value="<? echo $pnombre; ?>" size="40">
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Apellidos</td>
    <td class="TxtTabla"><label>
      <input name="Apellidos" type="text" class="CajaTexto" id="Apellidos" value="<? echo $papellidos; ?>" size="40">
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
		if ($pid_categoria == $reg[id_categoria] ) {
			$selCat = "selected";
		}
		else {
			$selCat = "";
		}
			echo "<option value =". $reg[id_categoria]. " $selCat >". $reg[nombre] ;
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
		$sqlDpto=$sqlDpto." order by D.nombre, A.nombre  ";

		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			if ($pid_departamento == $reg[id_departamento]) {
				$selDepto= "selected";
			}
			else {
				$selDepto= "";
			}
			echo "<option value =". $reg[id_departamento]. " $selDepto >" . "[" . ucwords(strtolower($reg[nomDiv])) . "] - " . ucwords(strtolower($reg[nombre])) ;
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Secci&oacute;n</td>
    <td class="TxtTabla">
	<select name="Seccion" class="CajaTexto" id="Seccion">
	
	<?php
	  	//$sqlDpto = "select distinct seccion from personalACTUALIZADO ";
		$sqlDpto = "select distinct seccion from hojaDeTiempo.dbo.usuarios ";
		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			if (trim(ucwords(strtoupper($pSeccion))) == trim(ucwords(strtoupper($reg[seccion])))) {
				$selSec = "selected";
			}
			else {
				$selSec = "";
			} ?>
			<option value="<? echo ucwords(strtolower($reg[seccion])) ?>" <? echo $selSec ?> ><? echo ucwords(strtolower($reg[seccion])) ?></option>
		<?
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Sitio Contrato</td>
    <td class="TxtTabla"><input name="SitioContrato" type="text" class="CajaTexto" id="SitioContrato" value="<? echo $pSitioContrato; ?>" size="40"></td>
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
	<? while ($regST = mssql_fetch_array($CursorST)){ 
			if (trim($pSitioTrabajo) == trim($regST[SitioTrabajo])) {
				$selST = "selected";
			}
			else {
				$selST = "";
			}
	?>
	 <option value="<? echo $regST[SitioTrabajo]; ?>" <? echo $selST; ?> ><? echo $regST[SitioTrabajo]; ?></option>
	<? } ?>  
    </select>
	<input name="SitioTrabajo" type="text" class="CajaTexto" id="SitioTrabajo" value="<? echo $pSitioTrabajo; ?>" size="40" disabled></td>
  </tr>
  <tr>
    <td class="TituloTabla">Email sin dominio </td>
    <td class="TxtTabla"><input name="Email" type="text" class="CajaTexto" id="Email" value="<? echo $pemail; ?>" readonly></td>
  </tr>
  <tr>
    <td class="TituloTabla">Tipo de contrato</td>
    <td class="TxtTabla"><select name="TipoContrato" class="CajaTexto" id="TipoContrato">
	<?
	if ($pTipoContrato == "TC") {
		$selTC = "selected";
		$selMT = "";
		$selCT = "";
	}
	
	if ($pTipoContrato == "MT") {
		$selTC = "";
		$selMT = "selected";
		$selCT = "";
	}
	
	if ($pTipoContrato == "CT") {
		$selTC = "";
		$selMT = "";
		$selCT = "selected";
	}
	
	?>
    <option value = "TC" <? echo $selTC; ?> >Tiempo Completo
    <option value = "MT" <? echo $selMT; ?> >Medio Tiempo
	<option value = "CT" <? echo $selMT; ?> >Cuarto de Tiempo
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombre corto </td>
    <td class="TxtTabla"><input name="NombreCorto" type="text" class="CajaTexto" id="NombreCorto" value="<? echo $pNombreCorto; ?>" size="40"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha de ingreso </td>
    <td class="TxtTabla">
	<input name="lFechaInicio" class="CajaTexto"  value="<? echo $pFechaInicio;?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.addUsr.lFechaInicio);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
	</td>
  </tr>
  
  <tr>
    <td class="TituloTabla">Jefe inmediato </td>
    <td class="TxtTabla">
	<select name="pJefe" class="CajaTexto" id="pJefe" >
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
			if ($punidadJefe == $reg2[unidad]) {
				$selUsu = "selected";
			}
			else {
				$selUsu = "";
			}
		?>
            <option value="<? echo $reg2[unidad]; ?>"  <? echo $selUsu; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
		<?
			if (trim($punidadJefe) == "") {
				$selUsu = "selected";
			}
		?>
			<option value=""  <? echo $selUsu; ?> ><? echo "  ";  ?></option>
          </select>	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Empresa</td>
    <td class="TxtTabla">
	<select name="pEmpresa" class="CajaTexto" id="pEmpresa" >
            <?
		//Trae la información de la lista empresas
		$sqlE="select * from Empresas " ;
		$cursorE = mssql_query($sqlE);
		while ($regE=mssql_fetch_array($cursorE)) {
		if ($pidEmpresa == $regE[idEmpresa]) {
			$selEmp = "selected";
		}
		else {
			$selEmp = "";
		}
		
		?>
            <option value="<? echo $regE[idEmpresa]; ?>" ><? echo ucwords(strtolower($regE[nombre])) ;  ?></option>
            <? } ?>
          </select>
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha de nacimiento </td>
    <td align="left" class="TxtTabla">
	<input name="lFechaNacimiento" type="text" class="CajaTexto" value="<? echo $pFechaNacimiento; ?>" size="15" readonly >
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
		if ($pcodTipoDoc == $regTD[codTipoDoc]) {
			$selTDoc = "selected";
		}
		else {
			$selTDoc = "";
		}
		
		?>
            <option value="<? echo $regTD[codTipoDoc]; ?>" <? echo $selTDoc; ?> ><? echo $regTD[tipoDoc] ;  ?></option>
            <? } ?>
          </select> 
	- 
	<input name="Documento" type="text" class="CajaTexto" id="Documento" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<? echo $pnumDocumento; ?>" >
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Sexo</td>
    <td class="TxtTabla">
	<?
	if ($psexo == "F") {
		$selF="checked";
		$selM="";
	}
	else {
		$selF="";
		$selM="checked";
	}
	?>
	<input name="lSexo" type="radio" value="F" <? echo $selF; ?> >
      Femenino&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="lSexo" type="radio" value="M" <? echo $selM; ?> >
        Masculino</td>
  </tr>  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td align="right" class="TxtTabla"><label>
      <input  type="submit" class="Boton" id="Grabar" value="Grabar" >
    </label></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla"><strong>Nota: Si requiere cambiar el correo electr&oacute;nico debe dirigirse al &aacute;rea de Sistemas </strong></td>
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