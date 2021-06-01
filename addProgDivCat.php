<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Si se presionó el botón Grabar
if ($lValor != "") {
	//Valida que no ingrese 0 en horas registradas
	if ($lValor == 0) {
		echo ("<script>alert('No puede asignar 0 al valor de la categoría. Por favor corrija la información.');</script>");
	}
	else {
		//Direcciona a la BD a donde va a grabar
		@mssql_select_db("HojaDeTiempo");
		
		//Realiza la inserción de la persona a la tabla ProgAsignaRecursosCat
		//id_proyecto, unidadProgramador, id_categoria, valorItem		
		$query = "INSERT INTO ProgAsignaRecursosCat(id_proyecto, unidadProgramador, id_categoria, valorItem )   " ;
		$query = $query . " VALUES( " . $cualProyecto . ", " ;
		$query = $query . $laUnidad . ", ";	
		$query = $query . $pCategoria . ", ";	
		$query = $query . $lValor ;	
		$query = $query . " ) ";	
		$cursor = mssql_query($query);
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		echo ("<script>window.close();MM_openBrWindow('ProgDivisionRec.php?cualProyecto=$cualProyecto','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");			
	}
}


?>
<html>
<head>
<title>Programaci&oacute;n de Asignaci&oacute;n de recursos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
        if (isNaN(val)) errors+='- '+nm+' debe ser numérico.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
  } if (errors) alert('Validación:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Asignaci&oacute;n por recursos </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('lValor','','RisNum');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><select name="pCategoria" class="CajaTexto" id="pCategoria" >
          <?
			@mssql_select_db("HojaDeTiempo",$conexion);
			$sql2="select *  " ;
			$sql2=$sql2." from Categorias " ;
			$sql2=$sql2." WHERE NOT EXISTS " ;
			$sql2=$sql2." 	(SELECT id_categoria " ;
			$sql2=$sql2." 	FROM ProgAsignaRecursosCat " ;
			$sql2=$sql2." 	WHERE id_categoria = Categorias.id_categoria " ;
			$sql2=$sql2." 	) " ;
			$cursor2 = mssql_query($sql2);
			while ($reg2=mssql_fetch_array($cursor2)) {
			?>
          <option value="<? echo $reg2[id_categoria]; ?>" <? echo $selCat; ?> ><? echo $reg2[nombre]; ?></option>
          <? } ?>
            </select>
      <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Valor</td>
    <td class="TxtTabla"><input name="lValor" type="text" class="CajaTexto" id="lValor"></td>
  </tr>
</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
        </tr>
      </table>
	  </form>
  	</td>
  </tr>
</table>

</body>
</html>
