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
	$pretirado = $reg[retirado];
}


if($Unidad != "") {

	if ($pRetira == 1) {
		//Actualiza el usuario en la base de datos de Usuarios de la hoja de tiempo
		//dbo.Usuarios
		//unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, email, ContadorFallas, 
		//FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, Seccion, NombreCorto, unidadJefe
		$insertaUsr = "UPDATE usuarios SET ";
		$insertaUsr = $insertaUsr. " retirado = '1', ";
		$insertaUsr = $insertaUsr. " fechaRetiro = '".$lFechaRetiro."', ";
		$insertaUsr = $insertaUsr. " email = '" . $pemail . "9' ";
		$insertaUsr = $insertaUsr. " WHERE unidad = " . $Unidad ;
		$cursorIns = mssql_query($insertaUsr) ;
	
		//Elimina el usuario del perfil
		//agrega el usuario a la base de datos gestion de informacion digital. tabla perfil usuarios, con perfil normal
		//BD-->GestiondeInformacionDigital
		//PerfilUsuarios
		//codPerfil, unidad
	   $sql = "DELETE FROM GestiondeInformacionDigital.dbo.PerfilUsuarios WHERE unidad = " .  $Unidad ;
	   $cursorPerfil = mssql_query($sql);
		
		if ((trim($cursorIns) != "") AND (trim($cursorPerfil) != ""))    {
			echo ("<script>alert('La Operación se realizó con éxito. ');</script>"); 
			//si graba todo bien envia la eliminación del Mail a sistemas
			//envia un mail a la cuenta mhtiempo@ingetec.com.co para crear el login y el correo
			//inicio envio mail
				$cualMailProy="mhtiempo@ingetec.com.co";
				//$cualMailProy="pbaron@ingetec.com.co";
				$AsuntoProy="Novedades de personal";
				$DescripcionProy="Proceso=Quitar
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
			echo ("<script>alert('Error durante la operación');</script>");
		}
		echo ("<script>window.close();MM_openBrWindow('UsuariosHT.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
	}
}

?>


<html>
<head>
<title>Actualizar Usuario</title>
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

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">

  <form action="" method="post" name = "addUsr" onSubmit="MM_validateForm('Unidad','','RisNum','Nombres','','R','Apellidos','','R','SitioContrato','','R','SitioTrabajo','','R','Email','','R','NombreCorto','','R','lFechaRetiro','','R','lFechaRetiro','','R');return document.MM_returnValue">
  
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
      <input name="Nombres" type="text" class="CajaTexto" id="Nombres" value="<? echo $pnombre; ?>" size="40" disabled>
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Apellidos</td>
    <td class="TxtTabla"><label>
      <input name="Apellidos" type="text" class="CajaTexto" id="Apellidos" value="<? echo $papellidos; ?>" size="40" disabled>
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><label>
      <select name="Categoria" class="CajaTexto" id="Categoria" disabled>
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
    <td class="TituloTabla">Departamento</td>
    <td class="TxtTabla"><select name="Departamento" class="CajaTexto" id="Departamento" disabled >
     <?php
	  	$sqlDpto = "select * from departamentos ";
		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			if ($pid_departamento == $reg[id_departamento]) {
				$selDepto= "selected";
			}
			else {
				$selDepto= "";
			}
			echo "<option value =". $reg[id_departamento]. " $selDepto >". ucwords(strtolower($reg[nombre])) ;
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Secci&oacute;n</td>
    <td class="TxtTabla"><select name="Seccion" class="CajaTexto" id="Seccion" disabled >
	<?php
	  	$sqlDpto = "select distinct seccion from personalACTUALIZADO";
		$catCursor = mssql_query($sqlDpto);
		while ($reg = mssql_fetch_array($catCursor)){
			if (ucwords(strtoupper($pSeccion)) == ucwords(strtolower($reg[seccion]))) {
				$selSec = "selected";
			}
			else {
				$selSec = "";
			}
			echo "<option value =". ucwords(strtolower($reg[seccion])). " $selSec >". ucwords(strtolower($reg[seccion])) ;
		}
	  ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Sitio Contrato</td>
    <td class="TxtTabla"><input name="SitioContrato" type="text" class="CajaTexto" id="SitioContrato" value="<? echo $pSitioContrato; ?>" size="40" disabled ></td>
  </tr>
  <tr>
    <td class="TituloTabla">Sitio Trabajo</td>
    <td class="TxtTabla"><input name="SitioTrabajo" type="text" class="CajaTexto" id="SitioTrabajo" value="<? echo $pSitioTrabajo; ?>" size="40" disabled ></td>
  </tr>
  <tr>
    <td class="TituloTabla">Email sin dominio </td>
    <td class="TxtTabla"><input name="Email" type="text" class="CajaTexto" id="Email" value="<? echo $pemail; ?>" readonly></td>
  </tr>
  <tr>
    <td class="TituloTabla">Tipo de contrato</td>
    <td class="TxtTabla"><select name="TipoContrato" class="CajaTexto" id="TipoContrato" disabled >
	<?
	if ($pTipoContrato == "TC") {
		$selTC = "selected";
		$selMT = "";
	}
	else {
		$selTC = "";
		$selMT = "selected";
	}
	?>
    <option value = "TC" <? echo $selTC; ?> >Tiempo Completo
    <option value = "MT" <? echo $selMT; ?> >Medio Tiempo
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombre corto </td>
    <td class="TxtTabla"><input name="NombreCorto" type="text" class="CajaTexto" id="NombreCorto" value="<? echo $pNombreCorto; ?>" size="40" disabled ></td>
  </tr>
  <tr>
    <td class="TituloTabla">Jefe inmediato </td>
    <td class="TxtTabla">
	<select name="pJefe" class="CajaTexto" id="pJefe" disabled >
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
          </select>	
		  </td>
  </tr>
  <tr>
    <td class="TituloTabla">Retirar usuario </td>
    <td class="TxtTabla"><input name="pRetira" type="radio" value="1">
      Si 
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input name="pRetira" type="radio" value="0" checked>
        No</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha de retiro </td>
    <td class="TxtTabla">
	<input name="lFechaRetiro" class="CajaTexto" id="lFechaRetiro"  value="<? echo $lFechaRetiro;?>" size="25"  readonly >
		<a href="javascript:void(0)"  onClick="gfPop.fPopCalendar(document.addUsr.lFechaRetiro);return false;" HIDEFOCUS><img name="popcal" align="absmiddle" src="calbtn.gif" width="34" height="22" border="0" alt="Seleccione una fecha"  ></a>
		<iframe width=174 height=189 name="gToday:normal:agenda.js" vspace=0 id="gToday:normal:agenda.js" src="ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">				</iframe>
	</td>
  </tr>
  <tr align="center">
    <td colspan="2" class="TxtTabla"><strong>Atenci&oacute;n: Esta operaci&oacute;n es irreversible. Est&aacute; seguro de retirar a este usuario del personal de Ingetec S.A.? <br>
      El retiro de un usuario implica la eliminaci&oacute;n del correo electr&oacute;nico corporativo y la desactivaci&oacute;n del sistema Portal</strong></td>
    </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td align="right" class="TxtTabla">
      <input name="Submit" type="button" class="Boton" onClick="MM_callJS('window.close()')" value="Cancelar">
      <input  type="submit" class="Boton" id="Grabar" value="Retirar" >
    </td>
  </tr>
</table>
      </td>
  </tr>
</table>
</form>

</body>
</html>