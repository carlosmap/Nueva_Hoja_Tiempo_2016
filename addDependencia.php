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

if($Nombre != "") {

	//Realiza las validaciones de rigor
	//Valida que el nombre de la dependencia no exista
	$sqlDep = "select * from Dependencias where nombre = '". $Nombre . "' ";
	$cursorDep = mssql_query($sqlDep) ;
	if(mssql_num_rows($cursorDep) > 0) {
		echo "<script>alert('Este nombre de dependencia ya existe. Por favor corríjalo')</script>";
		echo "<script>history.back(-1)</script>";
		exit();
	}

	//Realiza la inserción de de la dependencia
	//Tabla:  Dependencias
	//id_dependencia, nombre, id_director
	$insDep="INSERT INTO Dependencias (nombre, id_director) " ;
	$insDep=$insDep." VALUES ( " ;
	$insDep=$insDep." '" . $Nombre . "', "  ;
	if (trim($pJefe) == "") {
		$insDep=$insDep."NULL" ;
	}
	else {
		$insDep=$insDep. $pJefe ;
	}
	$insDep=$insDep." ) " ;
	$cursorDep = mssql_query($insDep) ;
	

	if (trim($cursorDep) != "")  {
		echo ("<script>alert('La Grabación se realizó con éxito. ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('ProgMantenimiento.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}
?>


<html>
<head>
<title>Estructura Organizacional Ingetec</title>
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

  <form action="" method="post" name = "addUsr" onSubmit="MM_validateForm('Nombre','','R');return document.MM_returnValue">
  
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Ingreso de Dependencias </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="32%" class="TituloTabla">Nombre</td>
    <td width="68%" class="TxtTabla"><label>
      <input name="Nombre" type="text" class="CajaTexto" id="Nombre" size="40">
    </label></td>
  </tr>
  <tr>
    <td class="TituloTabla">Director</td>
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