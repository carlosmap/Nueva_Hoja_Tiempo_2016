<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Encontrar el nombre del proyecto seleccionado
$sql="Select * from proyectos where id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elProyecto = $reg[nombre];
	}
else {
	$elProyecto= "";
}

//Encontrar el nombre de la actividad seleccionada
$sql="Select * from Actividades where id_proyecto = " . $cualProyecto ;
$sql=$sql." and id_actividad=" . $cualActividad;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laActividad = $reg[nombre];
}
else {
	$laActividad= "";
}



//Encuentra la información de la persona externa seleccionada
//identificacion, nombre, apellidos, estado
$pidentificacion="";
$pnombre="";
$papellidos="";
$sql3="SELECT * FROM HojaDeTiempo.dbo.PersonalExterno WHERE  identificacion = '" . $pAutoriza . "' ";
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) {
	$pidentificacion= $reg3[identificacion];
	$pnombre=$reg3[nombre];
	$papellidos=$reg3[apellidos];

	$sololeer = "readonly";
	
}

//$recarga = 2 si se presionó el botón Grabar
if ($recarga == "2") {
	//Si $pAutoriza == -999 es porque el personal es nuevo y hay que crearlo antes de hacer la asociación al proyecto.
	if ($pAutoriza != -999) {
		//Realiza la grabación dbo.ActividadesPersonalExt
		//id_proyecto, id_actividad, identificacion, servicio, valor, factor, unidad, fechaCrea
		$query = "INSERT INTO ActividadesPersonalExt(id_proyecto, id_actividad, identificacion, servicio, valor, factor, unidad)" ;
		$query = $query . " VALUES (" ;	
		$query = $query . $cualProyecto . ", " ;
		$query = $query . $cualActividad . ", " ;
		$query = $query . $pAutoriza . ", " ;
		$query = $query . " '" . $servicio . "', " ;
		$query = $query . $valor . ", " ;
		$query = $query . $factor . ", " ;
		$query = $query . $laUnidad  ;
		$query = $query . " ) ";
		$cursor = mssql_query($query) ;
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>"); 
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
	}
	else {
		//Verifica que la cedula no se encuentre previamente registrada
		$existeID = 0;
		$qry="select count(*) existeUsu from PersonalExterno ";
		$qry=$qry." where identificacion =".$lIden;
		$qry=$qry." and estado = 'A' ";
		$curQry = mssql_query($qry);
		if ($regQ=mssql_fetch_array($curQry)) {
			$existeID = $regQ[existeUsu];
			$msgID = "Esta persona ya se encuentra registrada, por favor selecciónela en la lista y realice nuevamente la grabación. " ;
		}
			
		if ($existeID == 0)  {
			//Inserta la persona en la tabla dbo.PersonalExterno
			//identificacion, nombre, apellidos, estado
			$qry0 = "INSERT INTO PersonalExterno (identificacion, nombre, apellidos, estado) " ;
			$qry0 = $qry0 . " VALUES (" ;	
			$qry0 = $qry0 . $lIden . ", " ;	
			$qry0 = $qry0 . " '" .$lNombre. "', " ;	
			$qry0 = $qry0 . " '" .$lApellido. "', " ;	
			$qry0 = $qry0 . " 'A' " ;	
			$qry0 = $qry0 . " ) " ;	
			$cursor0 = mssql_query($qry0) ;
	
			//Realiza la asociación de la persona al proyecto y actividad
			//Realiza la grabación dbo.ActividadesPersonalExt
			//id_proyecto, id_actividad, identificacion, servicio, valor, factor, unidad, fechaCrea
			$query = "INSERT INTO ActividadesPersonalExt(id_proyecto, id_actividad, identificacion, servicio, valor, factor, unidad)" ;
			$query = $query . " VALUES (" ;	
			$query = $query . $cualProyecto . ", " ;
			$query = $query . $cualActividad . ", " ;
			$query = $query . $lIden . ", " ;
			$query = $query . " '" . $servicio . "', " ;
			$query = $query . $valor . ", " ;
			$query = $query . $factor . ", " ;
			$query = $query . $laUnidad  ;
			$query = $query . " ) ";
			$cursor = mssql_query($query) ;
			
	//		echo $query . "<br>";
	//		exit;
			//Si los cursores no presentaron problema
			if  ((trim($cursor0) != "") AND (trim($cursor) != "")) {
				echo ("<script>alert('La Grabación se realizó con éxito.');</script>"); 
			} 
			else {
				echo ("<script>alert('Error durante la grabación');</script>");
			};
		}
		else {
			echo ("<script>alert('". $msgID . "');</script>"); 
		}
	}
	echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$cualProyecto&cualActividad=$cualActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
}


?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--


function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}

function envia2(){ 
var v1,v2,v3,v4,v5, v6, i, CantCampos, msg1, msg2, msg3, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
mensaje = '';

//alert (document.Form1.pAcargoDe[0].checked);
//alert (document.Form1.pAcargoDe[1].checked);
//alert (document.Form1.CantidadItem.value);

//validar que el nombre no se encuentre vacio
if (document.Form1.lNombre.value == '') {
	v1='n';
	msg1 = 'El nombre es obligatorio. \n'
}

//validar que el apellido no se encuentre vacio
if (document.Form1.lApellido.value == '') {
	v2='n';
	msg2 = 'El apellido es obligatorio. \n'
}


//validar que el servicio no se encuentre vacio
if (document.Form1.servicio.value == '') {
	v3='n';
	msg3 = 'El servicio es obligatorio. \n'
}

//validar que el valor no se encuentre vacio y sea numérico
if (document.Form1.valor.value == '') {
	v4='n';
	msg4 = 'El valor es obligatorio y numérico. \n'
}

if (isNaN(document.Form1.valor.value)) {
	v4='n';
	msg4 = 'El valor es obligatorio y numérico. \n'
}

//validar que el factor no se encuentre vacio y sea numérico
if (document.Form1.factor.value == '') {
	v5='n';
	msg5 = 'El factor es obligatorio y numérico. \n'
}

if (isNaN(document.Form1.factor.value)) {
	v5='n';
	msg5 = 'El factor es obligatorio y numérico. \n'
}

//validar que la identificación no se encuentre vacio y sea numérico
if (document.Form1.lIden.value == '') {
	v6='n';
	msg6 = 'La identificacion es obligatoria y numérica. \n'
}

if (isNaN(document.Form1.lIden.value)) {
	v6='n';
	msg6 = 'La identificacion es obligatoria y numérica. \n'
}


//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg6 + msg1 + msg2 + msg3 + msg4 + msg5 ;
		alert (mensaje);
	}

}
//-->
</script>
<title>Gesti&oacute;n de Archivos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post" name="Form1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Personal externo  que paricipa en el proyecto </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td>    
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="TituloTabla">Proyecto</td>
          <td class="TxtTabla"><? echo ucwords(strtolower($elProyecto)); ?>
            <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">            </td>
        </tr>
        <tr>
          <td class="TituloTabla">Actividad</td>
          <td class="TxtTabla"><? echo ucwords(strtolower($laActividad)); ?> <input name="cualActividad" type="hidden" id="cualActividad" value="<? echo $cualActividad; ?>"></td>
        </tr>
        <tr>
          <td width="39%" class="TituloTabla">Persona que se asociar&aacute; al proyecto </td>
          <td class="TxtTabla">
		  <select name="pAutoriza" class="CajaTexto" id="pAutoriza" onChange="envia1()" >
  	  <?
		//Traer el listado de personas a las que aun no se han asociado al proyecto y actividad
		$sql2="SELECT P.*  ";
		$sql2=$sql2." FROM HojaDeTiempo.dbo.PersonalExterno P ";
		$sql2=$sql2." WHERE NOT EXISTS ";
		$sql2=$sql2." 	( ";
		$sql2=$sql2." 	SELECT A.identificacion ";
		$sql2=$sql2." 	FROM HojaDeTiempo.dbo.ActividadesPersonalExt A ";
		$sql2=$sql2." 	WHERE A.identificacion = P.identificacion ";
		$sql2=$sql2." 	AND A.id_proyecto = " . $cualProyecto ;
		$sql2=$sql2."   AND A.id_actividad = " . $cualActividad;
		$sql2=$sql2." 	) ";
		$sql2=$sql2." AND P.estado = 'A' ";
		$sql2=$sql2." ORDER BY P.apellidos ";
		$cursor2 = mssql_query($sql2);
	  
	  while ($reg2=mssql_fetch_array($cursor2)) {
	  if ($pAutoriza == $reg2[identificacion]) {
	  		$selItem = "selected";
	  } 
	  else {
	  		$selItem = "";
	  }
	  ?>
            <option value="<? echo $reg2[identificacion]; ?>" <? echo $selItem; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
			<?
			if (($pAutoriza == -999) OR (trim($pAutoriza) == "")) {
				$selItem = "selected";
			}
			else {
				$selItem = "";
			}
			?>
			<option value="-999"  <? echo $selItem; ?> >::: Otro :::</option>
          </select>
		  <input name="recarga" type="hidden"  value="2"></td>
        </tr>
        <tr>
          <td class="TituloTabla">Identificacion</td>
          <td class="TxtTabla"><input name="lIden" type="text" class="CajaTexto" id="lIden" value="<? echo $pidentificacion; ?>" size="50" <? echo $sololeer; ?> ></td>
        </tr>
        <tr>
          <td class="TituloTabla">Nombre</td>
          <td class="TxtTabla"><input name="lNombre" type="text" class="CajaTexto" id="lNombre" value="<? echo $pnombre; ?>" size="50" <? echo $sololeer; ?>></td>
        </tr>
        <tr>
          <td class="TituloTabla">Apellidos</td>
          <td class="TxtTabla"><input name="lApellido" type="text" class="CajaTexto" id="lApellido" value="<? echo $papellidos; ?>" size="50" <? echo $sololeer; ?>></td>
        </tr>
        <tr>
          <td class="TituloTabla">Servicio</td>
          <td class="TxtTabla"><textarea name="servicio" cols="50" class="CajaTexto" id="servicio"><? echo $servicio; ?></textarea></td>
        </tr>
        <tr>
          <td class="TituloTabla">Valor</td>
          <td class="TxtTabla"><input name="valor" type="text" class="CajaTexto" id="valor" value="<? echo $valor; ?>"></td>
        </tr>
        <tr>
          <td class="TituloTabla">Factor</td>
          <td class="TxtTabla"><input name="factor" type="text" class="CajaTexto" id="factor" value="<? echo $factor; ?>"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()"></td>
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
