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

//Encontrar el registro seleccionado
$sql= "Select A.* , P.nombre ";
$sql=$sql." from PortalGID.dbo.asignaProyectosExt A, ";
$sql=$sql." HojaDeTiempo.dbo.Proyectos P ";
$sql=$sql." where A.id_proyecto = P.id_proyecto ";
$sql=$sql." and A.id_proyecto = " . $cualReg ;
$cursor = mssql_query($sql);
if ($reg1=mssql_fetch_array($cursor)) {
	$elProyecto = $reg1[id_proyecto];
	$elNombre = $reg1[nombre];
	$elDirector = $reg1[unidadDirector];
	$elEncargado = $reg1[unidadEncargado];
}

//Encontrar la categoria vigente para la selección de usuarios
$sql="Select * from GestiondeInformacionDigital.dbo.CategoriaAutoriza ";
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laCategoria = $reg[id_categoria];
	}
else {
	$laCategoria= 0;
}

//Si se hizo submit
if ($pProyecto != "") {
	//Realiza la grabación de la Asignación del proyecto en AsignaProyectos
	$query = "UPDATE PortalGID.dbo.asignaProyectosExt SET ";
	$query = $query . " unidadDirector =" . $pJefe . ", ";
	if ($pAut2 == '1') {
		$query = $query . " unidadEncargado = " . $pJefeAut2 ;
	}
	else {
		$query = $query . " unidadEncargado = NULL " ;
	}
	$query = $query . " WHERE id_proyecto = " . $pProyecto;
	$cursor = mssql_query($query) ;

	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Actualización se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('infProyecto.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}

?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>


<title>Asignaci&oacute;n de proyectos externos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post" name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Asignaci&oacute;n de Proyectos </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="20%" bgcolor="#FFFFFF">
      
      <tr>
        <td class="TituloTabla">Proyecto</td>
        <td class="TxtTabla">
		<? echo ucwords(strtolower($elNombre)) ; ?>
        <input name="pProyecto" type="hidden" id="pProyecto" value="<? echo $elProyecto; ?>">
</td>
      </tr>
      <tr>
          <td class="TituloTabla">Director para el Proyecto en la Gesti&oacute;n de Archivos</td>
          <td class="TxtTabla">
		  <select name="pJefe" class="CajaTexto" id="pJefe" >
            <?
		@mssql_select_db("HojaDeTiempo",$conexion);
		//Muestra todos los usuarios. 
		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
		$sql2=$sql2." and retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
			if ($elDirector == $reg2[unidad]) {
				$selDir = "selected";
			}
			else {
				$selDir = "";
			}
		
		?>
            <option value="<? echo $reg2[unidad]; ?>" <? echo $selDir ; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		  </td>
        </tr>
	  <tr>
          <td class="TituloTabla">&iquest;Requiere Encargado? </td>
          <td class="TxtTabla">
		  <? 
		  if (trim($elEncargado) != "") {
		  		$selSI = "checked";
				$selNO = "";
		  }
		  else {
		  		$selSI = "";
				$selNO = "checked";
		  }
		  ?>
		  
		  <input name="pAut2" type="radio" value="1" <? echo $selSI; ?> >
            Si
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="pAut2" type="radio" value="0" <? echo $selNO; ?>>
            No&nbsp;&nbsp;&nbsp;		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Encargado</td>
          <td class="TxtTabla">
		  <select name="pJefeAut2" class="CajaTexto" id="pJefeAut2" >
            <?
		@mssql_select_db("HojaDeTiempo",$conexion);
		//Muestra todos los usuarios. 
		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
		$sql2=$sql2." and retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {		
			if ($elEncargado == $reg2[unidad]) {
				$selEnc = "selected";
			}
			else {
				$selEnc = "";
			}
		
		
		?>
            <option value="<? echo $reg2[unidad]; ?>" <? echo $selEnc ; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>
		  </td>
        </tr>
  </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar" ></td>
        </tr>
      </table></td>
  </tr>
</table>

	     </td>
  </tr>
</table>
</form> 

</body>
</html>

<? mssql_close ($conexion); ?>	
